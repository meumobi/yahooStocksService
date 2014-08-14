<?php
require_once 'services/RedirectService.php'; //TODO use autoload

$environment = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'prod';

switch ($environment) {
	case "dev":
		$sitebuilder = 'http://mobi.com';
		break;
	case "int":
		$sitebuilder = 'http://int-meumobi.com';
		break;
	case "prod":
	default:
		$sitebuilder = 'http://meumobi.com';
		break;
}

define('SITE_BUILDER', $sitebuilder);
ini_set('display_errors', 'On');
