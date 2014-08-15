<?php
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';
require_once 'config/settings/stocks.php';

$action = @$_GET['action'];
$stockService = new StockService();
$stockService->run($action, $_GET);
