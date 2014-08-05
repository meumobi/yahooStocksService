<?php 
require_once "../libs/phpfastcache/phpfastcache.php";
class Service
{
	protected $cache;
	protected $logger;
	protected $logPath = '/logs/stock.log';
	protected $cacheTime = 900;	//15 minutes

	public function __construct() {
		phpFastCache::setup("storage","files");
		$this->cache = phpFastCache();
		$handler = new \Monolog\Handler\RotatingFileHandler(dirname(__DIR__) . $this->logPath);   
		$this->logger = new \Monolog\Logger(__CLASS__, [$handler]); 
	}

	function slug($string) {
		return preg_replace(array('/[^a-z0-9]/', '/-{2,}/'), '-', strtolower($string));
	}

	function call($url) {
		$urlKey = $this->slug($url);
		if ($response = $this->cache->get($urlKey))
			return $response;
		try {
			$response = $this->request($url);
			$this->cache->set($urlKey, $response, $this->cacheTime);
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
			$this->logger->error('request error', [
				'exception' => get_class($e),
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()]);
			$this->logger->info('using cached response: ' . ($response ? 'yes': 'no')); 
		}
		return $response;
	}

	function request($feed_url) {
		$this->logger->addInfo('making request to: ', ['url' => $feed_url]);
		$opts = array("http" =>
			array(
				"timeout" => 10 // seconds
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
