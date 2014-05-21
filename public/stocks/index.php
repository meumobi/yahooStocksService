<?php 
require_once '../services/StockService.php';
ini_set('display_errors', 'Off');

$action = isset($_GET['action']) ? $_GET['action'] : null;
$stockService = new StockService();
$stockService->run($action);
