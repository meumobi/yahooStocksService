<?php
define('APP_ROOT', dirname(__DIR__));
define('APP_ENV', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod');

set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT);

require_once 'vendor/autoload.php';
require_once 'libs/phpfastcache/phpfastcache.php';


switch (APP_ENV) {
	case "dev":
		ini_set('display_errors', 'On');
		break;
	case "int":
		ini_set('display_errors', 'Off');
		break;
	case "prod":
		ini_set('display_errors', 'Off');
	default:
		break;
}

