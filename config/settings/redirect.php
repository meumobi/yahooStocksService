<?php
require_once 'services/RedirectService.php'; //TODO use autoload

$environment = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod';

switch ($environment) {
	case "dev":
		$sitebuilder = 'http://mobi.com';
	  $cacheTime = 1;	//seconds
		break;
	case "int":
	  $cacheTime = 259200;	//3 days in seconds
		$sitebuilder = 'http://int-meumobi.com';
		break;
	case "prod":
	default:
	  $cacheTime = 259200;	//3 days in seconds
		$sitebuilder = 'http://meumobi.com';
		break;
}

define('SITE_BUILDER', $sitebuilder);
define('CACHE_TIME', $cacheTime);
define('CACHE_PATH', APP_ROOT . '/tmp/cache/redirect');
ini_set('display_errors', 'On');
