<?php
require_once '../vendor/autoload.php';
require_once '../config/settings.php';
require_once "../libs/phpfastcache/phpfastcache.php";
require_once '../services/RedirectService.php'; //TODO use autoload

$url = @$_GET['url'];
$domain = @$_GET['domain'];
$stockService = new RedirectService();
$stockService->run($url, $domain);
