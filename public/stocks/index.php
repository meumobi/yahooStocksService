<?php 

function call($feed_url) {
	$opts = array("http" =>
		array(
			"timeout" => 3 // seconds
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
try
{
	$feed_url = "http://webservice.enfoque.com.br/wsbancosantander/cotacoes.asmx/Tabela?Ativos=SANB3,DOLCOM,IBOV,SANB4,SANB11,BSBR&Login=BancoSantander&Senha=cotacoes2013";
	//$feed_url = "http://services.int-meumobilesite.com/requests/sleep.php?delay=4"; // if timeout $http_response_header[0]=> HTTP/1.1 200 OK
	//$feed_url = "http://services.int-meumobilesite.com/requests/500.php";

	$xml = call($feed_url);
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

	$results = array (
			"query" => array (
				"results" => array (
					"quotes" => $quotes
				)
			)
		); 

	echo json_encode($results);

}
catch( Exception $e ) 
{ 
	echo $e->getMessage(); 
} 
?>
