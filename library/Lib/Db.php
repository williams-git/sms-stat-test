<?php

class Lib_Db
{
	private static $_connection			=	array();
	private	static $OpenTransaction		=	array();
	private static $_node				=	'db';
	private static $_schema				=	array();

	public static function create($current_node = '', $dont_set_node = false)
	{
		if (!empty($current_node) && !$dont_set_node) {
			self::$_node = $current_node;
		}

		if (empty($current_node)) {
			$current_node = self::$_node;
		}

		if (!isset(self::$_connection[$current_node]) || !(self::$_connection[$current_node] instanceof Zend_Db_Adapter_Abstract) || !is_object(self::$_connection[$current_node]->getConnection())) {

			$db_config 	= new Zend_Config_Xml(ZF_CONFIG, $current_node);

			$db_type				= $db_config->type;
			$db_params['host']		= $db_config->host;
			$db_params['username'] 	= $db_config->username;
			$db_params['password'] 	= $db_config->password;
			$db_params['dbname']	= $db_config->dbname;
			$db_params['port']		= @$db_config->port;

			if (!empty($db_config->charset)) {
				$db_params['charset'] = $db_config->charset;
			}

			if (!empty($db_config->persistent)) {
				$db_params['persistent'] = TRUE;
			}

			try {
				self::$_connection[$current_node] = Zend_Db::factory($db_type, $db_params);
			} catch (Exception $e){
				Lib_Log::err('Unable connect DB: '.$e->getMessage(), __CLASS__, __FUNCTION__, __LINE__);
				return FALSE;
			}

			if (!empty($_SERVER['HTTP_HOST']) && (int)$db_config->profiling == 1) {
				self::$_connection[$current_node]->getProfiler()->setEnabled(true);
			}
			self::$_schema[$current_node] = @$db_config->schema;
		}

		return self::$_connection[$current_node];
	}

	public static function selectNode($node)
	{
		$node = trim($node);
		if (empty($node)) {
			throw new Exception('Server', 'Empty config node');
		}
		self::$_node = $node;
	}

	public static function closeConnection()
	{
		if (isset(self::$_connection[self::$_node]) && self::$_connection[self::$_node] instanceof Zend_Db_Adapter_Abstract) {
			try {
				if (self::$_connection[self::$_node]->closeConnection()) {

					unset(self::$_connection[self::$_node]);
					unset(self::$_schema[self::$_node]);

					return true;
				}
			} catch (Exception $e) {
				Lib_Log::err('Disconnect failed: '.$e->getMessage(), __CLASS__, __FUNCTION__, __LINE__);
			}
		}
		return false;
	}

	public static function reconnect()
	{
		self::closeConnection();
		self::create();
	}

	public static function getSchema($current_node = '')
	{
		if (empty($current_node)) {
			$current_node = self::$_node;
		}
		if (array_key_exists($current_node, self::$_schema) && !empty(self::$_schema[$current_node])) {
			return self::$_schema[$current_node];
		}
		return false;
	}

}