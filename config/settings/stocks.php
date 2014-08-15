<?php
require_once "services/Service.php";
require_once 'services/StockService.php';

define('CACHE_TIME', 60 * 15); //15 minutes
define('CACHE_PATH', APP_ROOT . '/tmp/cache/stocks');
define('REQUEST_TIMEOUT', 10);
define('ENFOQUE_URL', 'http://webservice.enfoque.com.br/wsbancosantander/cotacoes.asmx/Tabela');	
define('YAHOO_URL', 'https://query.yahooapis.com/v1/public/yql');	


define('PROFILES', '
	{
		"santander": {
			"login": "BancoSantander",
			"password": "cotacoes2013",
			"codes": "SANB11,SANB3,SANB4,BSBR,DOLCOM,IBOV"
		}
	}	
');

