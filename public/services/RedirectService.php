<?php 
require_once "Service.php";

class RedirectService extends Service
{
	protected $logPath = '/logs/redirect.log';
	protected $cacheTime = 259200;	//3 days in seconds

	public function run($url, $domain) {
		try {
			if($url && $domain) {
				$start_time = microtime(true);
				$stats['parameters'] = compact('url', 'domain');
				$this->redirectToItem($url, $domain);
				$stats['elapsed_time'] = microtime(true) - $start_time;
				$this->logger->info('finished stock action', $stats);
				exit;
			}
		} catch( Exception $e ) { 
			$this->logger->error('service error: ' . $e->getMessage());
		}
		header("Location: $url");
	}

	protected function redirectToItem($url, $domain) {
		$url =  SITE_BUILDER . "/api/$domain/items/search/?link=$url";
		$items = json_decode($this->call($url))->items;
		if ($items) {
			$item = reset(reset($items));
			header("Location: http://$domain#/items/{$item->_id}");
		} else {
			throw new Exception("Can't find item for url: $url");
		}
	}
}