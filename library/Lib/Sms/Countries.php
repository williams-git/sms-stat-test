<?php

class Lib_Sms_Countries
{
	const TABLE	= 'public.countries';

	public static function getById($cnt_id)
	{
		$cnt_id = intval($cnt_id);
		if (empty($cnt_id)) {
			return false;
		}

		$db = Lib_Db::create();

		$select = $db->select()
						->from(self::TABLE)
						->where('cnt_id = ?', $cnt_id);
		return $db->fetchRow($select);
	}

	public static function getList()
	{
		$db = Lib_Db::create();

		$select = $db->select()
					->from(self::TABLE)
					->order('cnt_title ASC');
		return $db->fetchAll($select);
	}

}