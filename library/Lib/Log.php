<?php

class Lib_Log
{
	public static $table = 'logs';
	public static $echo	 = false;

	private static $_buffer_limit	= 0;
	private static $_BUFFFER_		= array();
	private static $_common_log		= '';
	private static $_buffer_size	= 0;

	public static function save($log_priority,  $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL, $log_read = 'false')
	{
		$save = array();
		$save['log_priority']	= $log_priority;
		$save['log_class']		= $log_class;
		$save['log_function']	= $log_function;
		$save['log_message']	= purgeEncoding($log_message);
		$save['log_line']		= $log_line;
		$save['log_more']		= purgeEncoding(substr($log_more,0,2024));

		$debug_output = ' '.implode(" | ",$save);

		$save['log_read'] = $log_read;

		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$save['log_ip'] = $_SERVER['REMOTE_ADDR'];
		}

		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			$save['log_agent'] = purgeEncoding(substr($_SERVER['HTTP_USER_AGENT'], 0, 256));
		}

		if (!empty($_SERVER['HTTP_HOST'])) {
			$log_url = 'http://'.(!empty($_SERVER['HTTP_FRONTEND']) ? $_SERVER['HTTP_FRONTEND']:$_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI'];
			$save['log_url'] = purgeEncoding(substr($log_url, 0, 2048));
		}

		self::output(getmypid().' '.$debug_output."\n");

		if ($log_priority != 'DEBUG') {

			array_push(self::$_BUFFFER_, $save);

			self::$_buffer_size++;

			if (empty(self::$_buffer_limit)) {
				self::flush();
			}

			if (self::$_buffer_size >= self::$_buffer_limit) {
				self::flush();
			}
		}

		return true;
	}

	public static function emerg( $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL, $log_read = 'false')
	{
		return self::save('EMERG',  $log_message, $log_class, $log_function, $log_line, $log_more, $log_read);
	}

	public static function alert( $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL, $log_read = 'false')
	{
		return self::save('ALERT',  $log_message, $log_class, $log_function, $log_line, $log_more, $log_read);
	}

	public static function crit( $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL, $log_read = 'false')
	{
		return self::save('CRIT',  $log_message, $log_class, $log_function, $log_line, $log_more, $log_read);
	}

	public static function err( $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL, $log_read = 'false')
	{
		return self::save('ERR',  $log_message, $log_class, $log_function, $log_line, $log_more, $log_read);
	}

	public static function warn( $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL, $log_read = 'false')
	{
		return self::save('WARN',  $log_message, $log_class, $log_function, $log_line, $log_more, $log_read);
	}

	public static function notice( $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL, $log_read = 'false')
	{
		return self::save('NOTICE',  $log_message, $log_class, $log_function, $log_line, $log_more, $log_read);
	}

	public static function info( $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL, $log_read = 'false')
	{
		return self::save('INFO',  $log_message, $log_class, $log_function, $log_line, $log_more, $log_read);
	}

	public static function debug( $log_message, $log_class = NULL, $log_function = NULL, $log_line = NULL, $log_more = NULL)
	{
		return self::save('DEBUG',  $log_message, $log_class, $log_function, $log_line, $log_more);
	}

	public static function output($str)
	{
		if (self::$echo && !empty($str)) {
			if (empty($_SERVER['HTTP_FRONTEND'])) {
				echo $str;
			} else {
				echo nl2br($str);
			}

		}
	}

	public static function setOutput($value)
	{
		self::$echo = (boolean)$value;
	}

	public static function buffer($limit = 1000, $log_path = '')
	{
		$limit = intval($limit);
		if (!empty($limit)) {
			self::$_buffer_limit = $limit;
		}

		$log_path = trim($log_path);
		if (!empty($log_path)) {
			self::$_common_log = $log_path;
		}

		return true;
	}

	public static function flush()
	{
		if (empty(self::$_BUFFFER_)) {
			return false;
		}
		$log = '';

		foreach (self::$_BUFFFER_ as $key => $save) {
			$log .= self::toString($save);
			self::$_buffer_size--;
			unset(self::$_BUFFFER_[$key]);
		}

		if (!empty(self::$_common_log) && is_writeable(self::$_common_log)) {
			file_put_contents(self::$_common_log, $log, FILE_APPEND);
		}

		return true;
	}

	private static function toString($save)
	{
		if (empty($save)) {
			return false;
		}

		$log = $save['log_priority'].": \t\t ".date('Y-m-d H:i:s').' Pid:'.posix_getpid()."; \n";
		$log .= "Message: \t ".$save['log_message']."\n";
		if (!empty($save['log_more'])) {
			$log .= "Filename: \t ".$save['log_more']."\n";
		}
		$log .= "Class:\t\t ".$save['log_class']." \t Function:\t\t ".$save['log_function']." \t Line: \t ".$save['log_line']." \t File: ".$_SERVER['PHP_SELF']." \n";

		if (!empty($save['log_url'])) {
			$log .= "URL: \t\t {$save['log_url']}\n";
		}

		if (!empty($save['log_ip'])) {
			$log .= "IP: \t\t ".$_SERVER['REMOTE_ADDR'];
		}

		if (!empty($save['log_agent'])) {
			$log .= "\t Agent: ".substr($_SERVER['HTTP_USER_AGENT'],0,256);
		}

		$log .= "\n";

		if (!empty($_SERVER['HTTP_REFERER'])) {
			$log .= "Referer: \t ".$_SERVER['HTTP_REFERER']."\n";
		}

		$log .= "\n\t\t\t ~~~~~~~~~~~~ \t\t\t\t\t ~~~~~~~~~~~~\n\n";

		return $log;
	}

}