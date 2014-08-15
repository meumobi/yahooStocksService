<?php
require_once "services/Service.php";
require_once 'services/StockService.php';

define('CACHE_TIME', 60 * 15); //15 minutes
define('CACHE_PATH', APP_ROOT . '/tmp/cache/redirect');
define('REQUEST_TIMEOUT', 10);
