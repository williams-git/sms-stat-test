<?php

class Pattern_Main extends Zend_Controller_Action
{
	public $null = false;

	public $content;
	public $title;
	public $description;

	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
	{
		global $view;

		parent::__construct($request, $response, $invokeArgs);

		$this->_helper->viewRenderer->setNoRender();
		$this->view = $view;
	}

	public function param($key)
	{
		return Zend_Controller_Action::_getParam($key);
	}

	public function display()
	{
		if (!empty($this->null)) {
			echo $this->content;
			return true;
		}
		$this->view->title		= $this->title;
		$this->view->description	= $this->description;

		$this->view->content		= $this->content;

		$content = $this->view->render('Pattern/Main.tpl');

		echo $content;
	}

}
