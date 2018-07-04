<?php

class IndexController extends Pattern_Main
{
	public function indexAction()
	{
		$page = (int)$this->param('page');
		$cnt_id = (int)$this->param('cnt_id');
		$usr_id = (int)$this->param('usr_id');
		$date_from = (string)$this->param('date_from');
		$date_to = (string)$this->param('date_to');

		if ($page < 1) {
			$page = 1;
		}
		if (empty($date_from)) {
			$date_from = date('Y-m-d', strtotime('-30 days'));
		}
		if (empty($date_to)) {
			$date_to = date('Y-m-d', strtotime('-1 day'));
		}
		$f = strtotime($date_from);
		$t = strtotime($date_to);
		if (empty($t) || $t > time()) {
			$t = strtotime('-1 day');
			$date_to = date('Y-m-d', $t);
		}
		if (empty($f) || $f > time() || $f > $t) {
			if ($f > $t) {
				$date_from = $date_to;
			} else {
				$date_from = date('Y-m-d', strtotime('-30 days'));
			}
		}

		$this->view->countries = Lib_Sms_Countries::getList();

		$this->view->users = Lib_Sms_Users::getList();

		$this->view->data = Lib_Sms_Log::getData($date_from, $date_to, $cnt_id, $usr_id, $page, 8);
		$this->view->paginator = $paginator = null;
		if (!empty($this->view->data)) {
			$paginator = $this->view->data->getPages();
			$this->view->paginator = $paginator;
		}

		$params = array('date_from'=>$date_from, 'date_to'=>$date_to);
		if (!empty($cnt_id)) {
			$params['cnt_id'] = $cnt_id;
		}
		if (!empty($usr_id)) {
			$params['usr_id'] = $usr_id;
		}

		$this->view->prev = $this->view->next = null;
		if (!empty($paginator) && $paginator->pageCount > 1) {
			if ($paginator->current > 1) {
				$params['page'] = ($paginator->current-1);
				$this->view->prev = '/?'.http_build_query($params);
			}
			if ($paginator->current < $paginator->pageCount) {
				$params['page'] = ($paginator->current+1);
				$this->view->next = '/?'.http_build_query($params);
			}
		}

		$this->view->params	= $params;

		$this->content = $this->view->render('Index.tpl');

		$this->title = 'SMS LOG STATISTICS';
		$this->description = 'SMS LOG STATISTICS with FILTERs';

		$this->display();
	}

}
