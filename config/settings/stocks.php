<?php
require_once "services/Service.php";
require_once 'services/StockService.php';

define('CACHE_TIME', 60 * 5); //5 minutes
define('CACHE_PATH', APP_ROOT . '/tmp/cache/stocks');
define('REQUEST_TIMEOUT', 10);
define('ENFOQUE_URL', 'http://webservice.enfoque.com.br/wsbancosantander/cotacoes.asmx/Tabela');	
define('YAHOO_YQL_API_URL', 'https://query.yahooapis.com/v1/public/yql');
define('YAHOO_CSV_API_URL', 'http://download.finance.yahoo.com/d/quotes.csv');
define('GOOGLESHEET_CSV_URL', 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSySrp5ZSeXHwrvUa9aipLRHiAByPdlqXromTzOjoz0nMKtuu1Yf8bnNNNek_n0757Q7CTJiPosH83n/pub?gid=0&single=true&output=csv');
define('YAHOO_API', 'GOOGLE');

define('PROFILES', '
	{
		"santander": {
			"login": "BancoSantander",
			"password": "cotacoes2013",
			"codes": "SANB11,SANB3,SANB4,BSBR,DOLCOM,IBOV"
		}
	}	
');

