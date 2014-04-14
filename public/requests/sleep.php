<?php
  $delay = isset($_GET["delay"])?$_GET["delay"]:0;
  sleep($delay);
	
	echo "Hello my friend, sorry for the delay: ".$delay."s<br/>";
	echo "Change the delay using param 'delay', ie sleep.php?delay=5";

	unset ($_GET);
?>
