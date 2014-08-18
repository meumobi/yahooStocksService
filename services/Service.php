<?php 
class Service
{
	protected $cache;
	protected $logger;
	protected $logPath = '/logs/stock.log';
	protected $cacheTime = CACHE_TIME;

	public function __construct() {
		phpFastCache::setup([
			"storage" => "files",
			"path" => CACHE_PATH
		]);
		$this->cache = phpFastCache();
		$handler = new \Monolog\Handler\RotatingFileHandler(dirname(__DIR__) . $this->logPath, null, LOG_LEVEL);   
		$this->logger = new \Monolog\Logger(__CLASS__, [$handler]); 
	}

	function slug($string) {
		return preg_replace(array('/[^a-z0-9]/', '/-{2,}/'), '-', strtolower($string));
	}

	function call($url) {
		$urlKey = $this->slug($url);
		$cached = true;
		if (!$response = $this->cache->get($urlKey)) {
			try {
				$response = $this->request($url);
				$cached = false;
				$this->cache->set($urlKey, $response, $this->cacheTime);
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
				$this->logger->error('request error', [
					'exception' => get_class($e),
					'message' => $e->getMessage(),
					'trace' => $e->getTraceAsString()]);
			}
		}

		$this->logger->info('using cached response: ' . ($cached ? 'yes': 'no')); 
		return $response;
	}

	function request($feed_url) {
		$this->logger->addInfo('making request to: ', ['url' => $feed_url]);
		$opts = array("http" =>
			array(
				"timeout" => REQUEST_TIMEOUT // seconds
			)
		);
		$context  = stream_context_create($opts);
		if (!$response = file_get_contents($feed_url, false, $context)) {
			$error = error_get_last();
			throw new Exception("Cannot make request.\n Error was: ".$error["message"]
				."\n Response Header[0]: ".$http_response_header[0]);
		} else {
			return $response;
		}
	}
}
