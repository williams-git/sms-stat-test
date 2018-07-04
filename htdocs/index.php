<?php

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('memory_limit', '128M');

# # # # # # # # # # # # # # # #
# Variables & constantes;
define('ZF_ROOT',         		realpath($_SERVER['DOCUMENT_ROOT'].'/../'));
define('ZF_APPLICATION',  		ZF_ROOT.'/application/');
define('ZF_LIBRARY',  	  		ZF_ROOT.'/library');
define('ZF_ROUTES',       		ZF_APPLICATION.'routes.xml');
define('ZF_CONFIG',       		ZF_APPLICATION.'config.xml');
define('ZF_CONTROLLERS',  		ZF_APPLICATION.'Controller');
define('ZF_VIEWS',        		ZF_APPLICATION.'View');
define('ZF_LOGS',				ZF_ROOT.'/logs/');

set_include_path(ZF_LIBRARY.PATH_SEPARATOR.ZF_APPLICATION);

require_once('Lib/Kernel.php');

$router 	= new Zend_Controller_Router_Rewrite();
$request	= new Zend_Controller_Request_Http();
$path 		= array ('default' => ZF_CONTROLLERS);
$routes 	= new Zend_Config_Xml(ZF_ROUTES, 'list');

$router->addConfig($routes, 'routes');

$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory($path);
$controller->setRequest($request);
$controller->setRouter($router);
$controller->throwExceptions(false);

$view = new Zend_View();
$view->setScriptPath(ZF_VIEWS);

try {
	$controller->dispatch();
} catch (Zend_Controller_Dispatcher_Exception $e) {
	exit();
}