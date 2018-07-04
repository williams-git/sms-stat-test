<?php

class Lib_Sms_Users
{
	const TABLE	= 'public.users';

	public static function getById($usr_id)
	{
		$usr_id = intval($usr_id);
		if (empty($usr_id)) {
			return false;
		}

		$db = Lib_Db::create();

		$select = $db->select()
						->from(self::TABLE)
						->where('usr_id = ?', $usr_id);
		return $db->fetchRow($select);
	}

	public static function getList($active = null)
	{
		$db = Lib_Db::create();

		$select = $db->select()
					->from(self::TABLE)
					->order('usr_name ASC');
		if (!is_null($active)) {
			if ($active) {
				$select->where('usr_active = 1');
			} else {
				$select->where('usr_active = 0');
			}
		}
		return $db->fetchAll($select);
	}

}