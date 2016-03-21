<?php 
class StockService extends Service
{
	protected $params;
	protected $quote_fields = array('symbol', 'LastTradePriceOnly', 'StockExchange', 'ChangeinPercent', 'Change', 'DaysHigh','DaysLow','DaysRange','Volume', 'Currency','LastTradeDate','LastTradeTime');

	function call($url) {
		$urlKey = $this->slug($url);
		$response = null;
		$errorMessage = '';
		try {
			$response = $this->request($url);
			$this->cache->set($urlKey, $response, CACHE_TIME);//15 minutes
		} catch (Exception $e) {
			$response = $this->cache->get($urlKey);
			$errorMessage = $e->getMessage();
			$this->logger->error('stock error', [
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
		$profiles = json_decode(PROFILES, true);
		$params = [
			'Ativos' => $profiles[$profile]['codes'],
			'Login' => $profiles[$profile]['login'],
			'Senha' => $profiles[$profile]['password']
		];	

		$feed_url = ENFOQUE_URL . '?' . http_build_query($params);
		
		$xml = $this->call($feed_url);
		$json = $this->convertEnfoqueToYahoo($xml);

		echo $json;
	}

	// Old yahoofy function using YQL query
	function yahooYQL() {
		if (isset($this->params['codes'])) {
			// Form YQL query and build URI to YQL Web service
			$codes = $this->params['codes'];
			$yql_query = "select * from yahoo.finance.quotes where symbol in ('$codes')";
			$yql_query_url = YAHOO_YQL_API_URL . "?q=" . urlencode($yql_query) 
				. "&format=json"
				. "&env=http%3A%2F%2Fdatatables.org%2Falltables.env";

			$json = $this->call($yql_query_url);

			echo $json;
		} else {
			throw new Exception("Codes are missing");
		}
	}

	function convertRowToQuote($row) {
		$quote = array();
		foreach ($row as $key=>$value) {
			$quote[$this->quote_fields[$key]] = $value;
		};
		return $quote;
	}

	function convertArrayToYahoo($array) {
		// If only one row don't use an array on response to be compliant with YQL responses
		if (count($array) > 1) {
			$quotes = array();
			foreach ($array as $row) {
				$quotes[] = $this->convertRowToQuote($row);
			}
		} else {
			$quotes = $this->convertRowToQuote($array[0]);
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
	
	function yahoofy() {
		switch (YAHOO_API) {
			case "YQL":
				$this->yahooYQL();
				break;
			case "CSV":
			default:
				$this->yahooCSV();
				break;
		}
	}
	
	function yahooCSV() {
		if (isset($this->params['codes'])) {
			$codes = $this->params['codes'];
			$csv_query = "'$codes'&f=sl1xp2c1hgmvc4d1t1";
			$csv_query_url = YAHOO_CSV_API_URL . "?s='$codes'&f=sl1xp2c1hgmvc4d1t1";

			$csv = $this->call($csv_query_url);
			$this->logger->debug($csv);
			//remove the very last newline to prevent a 0-field array for the last line
			 $str = preg_replace('/\n$/', '', $csv);
			
			$array = array_map("str_getcsv", explode("\n", $str));
			
			$json = $this->convertArrayToYahoo($array);

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
