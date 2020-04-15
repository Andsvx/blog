<?php

function autoload($file_name)
{
	switch ($file_name[0])
	{
		case 'm':
			include_once("model/$file_name.php");
			break;

		case 'c':
			include_once("controller/$file_name.php");
			break;
	}
}

define('MYSQL_HOSTNAME', 'localhost');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', 'root');
define('MYSQL_DATABASE', 'blog');

?>