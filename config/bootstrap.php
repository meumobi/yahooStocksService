<?php
use Monolog\Logger; 
define('APP_ROOT', dirname(__DIR__));
define('APP_ENV', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod');

set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT);

require_once 'vendor/autoload.php';
require_once 'lib/phpfastcache/phpfastcache.php';


switch (APP_ENV) {
	case "dev":
		ini_set('display_errors', 'On');
		$logLevel = Logger::DEBUG; 
		break;
	case "int":
		ini_set('display_errors', 'Off');
		$logLevel = Logger::INFO; 
		break;
	case "rimobi":
		ini_set('display_errors', 'Off');
		$logLevel = Logger::ERROR; 
		break;
	case "prod":
	default:
		ini_set('display_errors', 'Off');
		$logLevel = Logger::ERROR; 
		break;
}

define('LOG_LEVEL', $logLevel);
