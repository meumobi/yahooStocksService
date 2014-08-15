<?php 
class StockService
{
	protected $cache;
	protected $logger;
	protected $params;

	public function __construct() {
		phpFastCache::setup("storage","files");
		$this->cache = phpFastCache();
		$handler = new \Monolog\Handler\RotatingFileHandler(dirname(__DIR__).'/logs/stock.log');   
		$this->logger = new \Monolog\Logger('services.stock', [$handler]); 
	}

	function slug($string) {
		return preg_replace(array('/[^a-z0-9]/', '/-{2,}/'), '-', strtolower($string));
	}

	function call($url) {
		$urlKey = $this->slug($url);
		$response = null;
		$errorMessage = '';
		try {
			$response = $this->request($url);
			$this->cache->set($urlKey, $response, 60 * 15);//15 minutes
		} catch (Exception $e) {
			$response = $this->cache->get($urlKey);
			$errorMessage = $e->getMessage();
			$this->logger->error('events update error', [
				'exception' => get_class($e),
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()]);
			$this->logger->info('using cached response: ' . ($response ? 'yes': 'no')); 
		}
		if ($response == null) {
			throw new Exception($errorMessage);
		}
		return $response;
	}

	function request($feed_url) {
		$this->logger->addInfo('making request to: ', ['url' => $feed_url]);
		$opts = array("http" =>
			array(
				"timeout" => 1 // seconds
			)
		);
		$context  = stream_context_create($opts);
		if (!$xml = file_get_contents($feed_url, false, $context)) {
			$error = error_get_last();
			throw new Exception("Cannot access external stock service.</br>Error was: ".$error["message"]
				."</br>Response Header[0]: ".$http_response_header[0]);
		} else {
			return $xml;
		}
	}

	function help() {
		echo 'Following Actions are available:</br><ul>'
			. '<li>enfoquify, using params:profile</li>'
			. '<li>yahoofy, using params:codes</li>'
			. '</ul>'
			. 'Examples: </br>'
			. '<a href="/stocks?action=yahoofy&codes=SANB11.SA,YHOO">/stocks?action=yahoofy&codes=SANB11.SA,YHOO</a></br>'
			. '<a href="/stocks?action=enfoquify&profile=santander">/stocks?action=enfoquify&profile=santander</a>'
			. '<br/></br>For a symbol quote Look Up check this link:</br>'
			. '<a href="https://finance.yahoo.com/lookup">Symbol Look Up</a>';
	}

	function convertEnfoqueToYahoo($xml) {
		$feed = simplexml_load_string($xml);
		foreach( $feed->Info as $quote ) {
			$quotes[] =  array (
				"symbol" => (String)$quote->attributes()->CODE,
				"LastTradePriceOnly" => (String)$quote->attributes()->SHARE_PRICE,
				"ChangeinPercent" => (String)$quote->attributes()->CHANGE,
				"Open" => (String)$quote->attributes()->DAY_OPEN,
				"DaysHigh" => (String)$quote->attributes()->DAY_HIGH,
				"DaysLow" => (String)$quote->attributes()->DAY_LOW,
				"Volume" => (String)$quote->attributes()->DAY_VOLUME,
			);
		}
		if (!empty($quotes)) {
			$results = array (
				"query" => array (
					"results" => array (
						"quote" => $quotes
					)
				)
			); 
		} else {
			$results = "Sorry, no quotes matching";
		}
		return json_encode($results);

	}

	function enfoquify($profile="santander") {

		$BASE_URL = 'http://webservice.enfoque.com.br/wsbancosantander/cotacoes.asmx/Tabela';	

		$profiles['santander'] = array (
			'login' => 'BancoSantander', 
			'password' => 'cotacoes2013',
			'codes' => 'SANB11,SANB3,SANB4,BSBR,DOLCOM,IBOV'
		);

		$feed_url = $BASE_URL . '?'
			. 'Ativos=' . urlencode($profiles['santander']['codes'])
			. '&Login=' . urlencode($profiles['santander']['login'])
			. '&Senha=' . urlencode($profiles['santander']['password']);	

		$xml = $this->call($feed_url);
		$json = $this->convertEnfoqueToYahoo($xml);

		echo $json;
	}

	function yahoofy() {
		$BASE_URL = 'https://query.yahooapis.com/v1/public/yql';

		if (isset($this->params['codes'])) {
			// Form YQL query and build URI to YQL Web service
			$codes = $this->params['codes'];
			$yql_query = "select * from yahoo.finance.quotes where symbol in ('$codes')";
			$yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) 
				. "&format=json"
				. "&env=http%3A%2F%2Fdatatables.org%2Falltables.env";

			$json = $this->call($yql_query_url);

			echo $json;
		} else {
			throw new Exception("Codes are missing");
		}
	}

	function run($action, $params) {
		try {
			if($action && method_exists($this, $action)) {
				$this->params = $params;
				$start_time = microtime(true);
				$stats['action'] = $action;
				$stats['parameters'] = $params;
				$this->$action();
				$stats['elapsed_time'] = microtime(true) - $start_time;
				$this->logger->info('finished stock action', $stats);
			}	else {
				$this->help(); 
			}
		} catch( Exception $e ) { 
			header('HTTP/1.1 500 Internal Server Error');
			$this->logger->error('service error: ' . $e->getMessage());
			echo $e->getMessage(); 
		}
	}
}
