<?php
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';
require_once 'services/StockService.php';

ini_set('display_errors', 'Off');

$action = @$_GET['action'];
$stockService = new StockService();
$stockService->run($action, $_GET);
