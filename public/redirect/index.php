<?php
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';
require_once 'config/settings/redirect.php';

$url = @$_GET['url'];
$domain = @$_GET['domain'];
$stockService = new RedirectService();
$stockService->run($url, $domain);
