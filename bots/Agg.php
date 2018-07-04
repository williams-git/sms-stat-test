<?php

	error_reporting(0);
	ini_set('display_errors', false);
	ini_set('memory_limit', '128M');

	define('ZF_ROOT',				'/www/smslog.info');
	define('ZF_LIBRARY',  	 	 	ZF_ROOT.'/library');
	define('ZF_LOGS',				ZF_ROOT.'/logs/');
	define('ZF_APPLICATION',		ZF_ROOT.'/application/');
	define('ZF_BOTS',				ZF_ROOT.'/bots/');
	define('ZF_CONFIG',				ZF_APPLICATION.'config.xml');
	define('ZF_VIEWS',        		ZF_APPLICATION.'View');

	set_include_path(ZF_LIBRARY);

	require_once('Lib/Kernel.php');

	Lib_Log::setOutput(true);

	Lib_Log::debug('');

	$db = Lib_Db::create('db');

	# tables names
	$table_countries	= 'countries';
	$table_users		= 'users';
	$table_numbers		= 'numbers';
	$table_log			= 'send_log';
	$table_log_agg		= 'send_log_aggregated';
	$max_days			= 30;

	Lib_Log::debug('Truncate');

	$db->query('TRUNCATE '.$table_countries.' CASCADE;');
	$db->query('TRUNCATE '.$table_users.' CASCADE;');

	$countries_list = array(
				array('cnt_code'=>1, 'cnt_title'=>'USA'),
				array('cnt_code'=>1, 'cnt_title'=>'Canada'),
				array('cnt_code'=>44, 'cnt_title'=>'United Kingdom'),
				array('cnt_code'=>49, 'cnt_title'=>'Germany'),
				array('cnt_code'=>86, 'cnt_title'=>'China'),
	);

	# Countries
	Lib_Log::debug('Init Countries');

	foreach ($countries_list as $k=>$v) {
		$db->insert($table_countries, $v);
	}

	# Numbers
	Lib_Log::debug('Init Numbers');

	$select = $db->select()->from($table_countries, array('cnt_id', 'cnt_code'));
	$countries = $db->fetchPairs($select);

	foreach ($countries as $cnt_id=>$code) {
		$list = array();
		for ($i=1; $i<=100; $i++) {

			$n = rand(1,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

			$list[] = implode(',', array($cnt_id, $n));
		}
		if (!empty($list)) {
			$db->query('INSERT INTO '.$table_numbers.' (cnt_id, num_number) VALUES ('.implode('),(', $list).')');
		}
	}

	# Names
	Lib_Log::debug('Init Names');

	$names = array('Liam', 'Noah', 'Emma', 'Olivia');
	$lnames = array('SMITH', 'JOHNSON');

	$list = array();
	foreach ($lnames as $l) {
		foreach ($names as $n) {
			$list[] = implode(',', array("'".$n.' '.$l."'", rand(0,1) ? 'TRUE' : 'FALSE'));
		}
	}
	if (!empty($list)) {
		$db->query('INSERT INTO '.$table_users.' (usr_name, usr_active) VALUES ('.implode('),(', $list).')');
	}

	# Log
	Lib_Log::debug('Init Log');

	$select = $db->select()->from($table_users, array('usr_id', 'usr_id'));
	$users = $db->fetchPairs($select);

	$select = $db->select()->from($table_numbers, array('num_id', 'num_id'));
	$numbers = $db->fetchPairs($select);

	$list = array();
	for ($i=1; $i<=10000; $i++) {
		$u = array_rand($users);
		$n = array_rand($numbers);
		$s = 'TRUE';
		if (!rand(0,10)) {
			$s = 'FALSE';
		}
		$d = date('Y-m-d H:i:s', rand((time()-(3600*24*$max_days)), time()));

		$list[] = implode(',', array($u, $n, $s, "'".$d."'", "'Message ".$i."'"));

		if (count($list) > 1000) {
			$db->query('INSERT INTO '.$table_log.' (usr_id, num_id, log_success, log_created, log_message) VALUES ('.implode('),(', $list).')');
			$list = array();
		}
	}
	if (!empty($list)) {
		$db->query('INSERT INTO '.$table_log.' (usr_id, num_id, log_success, log_created, log_message) VALUES ('.implode('),(', $list).')');
	}

	Lib_Log::debug('');
	Lib_Log::debug('AGG');
	Lib_Log::debug('');

	# in real world scenario - we just do this one time - for the last full day
	for ($days=1; $days<=$max_days; $days++) {
			$db->query("
				INSERT INTO send_log_aggregated (lga_date, cnt_id, usr_id, lga_sent, lga_failed)
				SELECT DATE(l.log_created), n.cnt_id, l.usr_id, COUNT(NULLIF(log_success,FALSE)) as sent, COUNT(NULLIF(log_success,TRUE)) as failed
				FROM send_log as l
				INNER JOIN numbers as n ON l.num_id=n.num_id
				WHERE DATE(l.log_created) = (CURRENT_DATE - INTERVAL '".$days." days')
				GROUP BY DATE(l.log_created), n.cnt_id, l.usr_id
			");
			$db->delete('send_log', "DATE(log_created) = (CURRENT_DATE - INTERVAL '".$days." days')");
	}

	Lib_Log::debug('OK');
