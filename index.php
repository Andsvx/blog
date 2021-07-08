<?php

include_once('config.php');
spl_autoload_register('autoload');

session_start();

$info = isset($_GET['x']) ? explode('/', $_GET['x']) : $_GET['x'] = array();
$params = array();

foreach ($info as $v)
{
	if ($v != '')
		$params[] = $v;
}

switch ($params[0] = isset($params[0]) ? $params[0] : $params[0] = null)
{
	case 'users':
		$controller = new c_users();
		break;

	case 'articles':
		$controller = new c_articles();
		break;

	default:
		$controller = new c_articles();
}

$action = 'action_' . ((isset($params[1])) ? $params[1] : 'list');

$controller->request($action, $params);

?>