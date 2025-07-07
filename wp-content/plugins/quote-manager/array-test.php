<?php

	$json = "[['34','33'],['33','11']]";
	echo $json;
	$sba = json_decode($json, true);
	print_r($sba);

?>