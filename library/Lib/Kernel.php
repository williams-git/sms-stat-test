<?php

spl_autoload_register('__autoload_lib');

function __autoload_lib($class_name)
{
	if (
	strpos($class_name, 'Lib' ) !== false
	|| strpos($class_name, 'PEL' ) !== false
	|| strpos($class_name, 'API' ) !== false
	|| strpos($class_name, 'Zend') !== false
	|| strpos($class_name, 'ZendX') !== false
	|| strpos($class_name, 'Pattern') !== false
	) {

		$file_name = str_replace('_','/',$class_name.'.php');

		require_once($file_name);
		return true;
	}
	return false;
}

function validHTML($string, $attribute = false)
{
	$string = str_replace('&amp;', '&', $string);
	$string = str_replace('&', '&amp;', $string);

	if ($attribute) {
		$string = str_replace('"', '&quot;', $string);
		$string = str_replace('\'', '&#039;', $string);
		$string = str_replace('\n',' ',$string);
	}

	return $string;
}

function cleanHTML($string)
{
	$string = validHTML($string);
	$string = str_replace('><','> <',$string);
	$string = strip_tags($string, '<div><b><i><u><ol><ul><li><br><p>');

	return $string;
}

function noHTML($string)
{
	$string = str_replace('/>','',$string);
	$string = str_replace('>','',$string);
	$string = str_replace('<','',$string);
	return $string;
}

function thousands($num, $float = false, $sym = '\.')
{
	$parts = explode($sym, $num);
	$num = $parts[0];

	$r = '';
	$num = (string)$num;
	$count = strlen($num);


	for ($i=$count-1;$i>=0;$i--) {

		if (($count-$i-1)%3==0 && $i!=$count-1) {
			$r = ' '.$r;
		}

		$r = $num{$i}.$r;
	}

	if ($float) {
		if (!isset($parts[1]) || strlen($parts[1]) ==0) {
			$parts[1] = '00';
		}

		if (isset($parts[1]) && strlen($parts[1]) ==01) {
			$parts[1] .= '0';
		}
	}

	if (!empty($parts[1])) {
		$r.= '.'.$parts[1];
	}

	return $r;
}

function getTranslitUrl($string, $limit=NULL)
{
	$result = html_entity_decode(strip_tags($string));
	$result = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '_', $result);
	$result = preg_replace('/_+/', '_', $result);
	$result = trim(strtolower($result));
	$result = trim($result, '_');

	if (!empty($limit)) {
		$result = substr($result, 0, $limit-1);
	}

	return $result;
}

function html($text, $hard = false)
{
	if ($hard) {
		$text = htmlentities($text);
	}

	$text = strip_tags($text, '<b><i><u><p><strong><br><big><small><sup><sub><em>');

	return $text;
}

function sdir($str, $rootPath = '', $depth = 5)
{
	if (defined('SDIR_DEPT')) {
		$depth = SDIR_DEPT;
	}

	$pattern = '/[^a-z0-9]/';

	$s = preg_replace($pattern, '', strtolower($str));
	if (strlen($s) < $depth) {
		return null;
	}

	$path = '';

	for ($i = 1; $i <= $depth; $i++) {
		$path .= substr($s, 0, $i).DIRECTORY_SEPARATOR;
	}

	if (!empty($rootPath)) {
		$rootPath = rtrim($rootPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		if (!is_dir($rootPath.$path)) {
			mkdir($rootPath.$path, 0775, true);
		}
	}
	return $path;
}

function uf_filesize($B)
{
	$Kb = $B/1024;
	if ($Kb<1) return array('size'=>sprintf("%01.0f", $B),'dimension'=>'B');

	$Mb = $Kb/1024;
	if ($Mb<1) return array('size'=>sprintf("%01.2f", $Kb),'dimension'=>'KB');

	$Gb = $Mb/1024;
	if ($Gb<1) return array('size'=>sprintf("%01.2f", $Mb),'dimension'=>'MB');

	$Tb = $Gb/1024;
	if ($Tb<1) return array('size'=>sprintf("%01.2f", $Gb),'dimension'=>'GB');

	$Pb = $Tb/1024;
	if ($Pb<1) return array('size'=>sprintf("%01.2f", $Tb),'dimension'=>'TB');

	return array('size'=>sprintf("%01.2f", $Pb),'dimension'=>'PB');
}

function uf_filesize_complete($B)
{
	$info = uf_filesize($B);
	return $info['size'].' '.$info['dimension'];
}

function uf_filesize_complete_rounded($B)
{
	$info = uf_filesize($B);
	return round($info['size']).' '.$info['dimension'];
}

function collapseHTML($html)
{
	$html = preg_replace("~\t~", '', $html);
	$html = preg_replace("~(\n)+~", "\n", $html);

	return $html;
}

function purgeEncoding($str)
{
	$encoding = defined('PURGE_ENCODING') ? PURGE_ENCODING : 'UTF-8';

	@iconv_set_encoding('input_encoding', $encoding);
	@iconv_set_encoding('output_encoding', $encoding);
	@iconv_set_encoding('internal_encoding', $encoding);

	$purge = @iconv($encoding, $encoding.'//IGNORE', $str);

	if (empty($purge)) {
		$purge = mb_convert_encoding($str, $encoding, mb_list_encodings());
	}

	if ($purge != $str) {
		return $purge;
	}
	return $str;
}



































