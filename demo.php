<?php
define('DOCROOT', realpath('./').'/');
require_once DOCROOT . 'libs/template.class.php';

$applist = array(
	't1' => 'php', 
	't2' => 'css',
	't3' => array('help', 'over'),
);

$view = new template();
$view->assign('status', 105);
$view->assign('choose', 1);
$view->assign('applist', $applist);
$view->display('demo');
?>
