<?php

class Lib_Sms_Log
{
	const TABLE = 'public.send_log_aggregated';

	public static function getData($date_from, $date_to, $cnt_id = null, $usr_id = null, $page = 1, $limit = 10)
	{
		if (empty($page) || $page < 1) {
			$page = 1;
		}
		if (empty($limit) || $limit < 1) {
			$limit = 10;
		}
		if (empty($date_from) || empty($date_to)) {
			return false;
		}
		$cnt_id = (int)$cnt_id;
		$usr_id = (int)$usr_id;

		$date_from = (ctype_digit($date_from) ? intval($date_from) : strtotime($date_from));
		$date_from = @date('Y-m-d', $date_from);

		$date_to = (ctype_digit($date_to) ? intval($date_to) : strtotime($date_to));
		$date_to = @date('Y-m-d', $date_to);

		$db = Lib_Db::create();

		$select = $db->select()
					->from(self::TABLE, array('lga_date', 'SUM(lga_sent) as sent', 'SUM(lga_failed) as failed'))
					->where('lga_date >= ?', $date_from)
					->where('lga_date <= ?', $date_to)
					->group('lga_date')
					->order(array('lga_date ASC'));

		if (!empty($cnt_id)) {
			$select->where('cnt_id = ?', $cnt_id);
		}
		if (!empty($usr_id)) {
			$select->where('usr_id = ?', $usr_id);
		}

		$paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage($limit);
		$paginator->setPageRange(5);
		$paginator->setCurrentPageNumber($page);

		return $paginator;
	}

}