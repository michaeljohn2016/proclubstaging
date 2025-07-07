<?php
	require_once("easypost/lib/easypost.php");
	\EasyPost\EasyPost::setApiKey('6sDxqEGTc_1O7L1d2Z-pPA');
	
	$toAddress = \EasyPost\Address::create(array(
	  'name' => 'Dirk Diggler',
	  'street1' => '300 Granelli Ave',
	  'city' => 'Half Moon Bay',
	  'state' => 'CA',
	  'zip' => '94019',
	  'country' => 'US',
	  'email' => 'dirk_d@gmail.com'
	));
	
	$fromAddress = \EasyPost\Address::create(array(
	  'name' => 'ProClubs.com',
	  'street1' => '23335 N. 18th Drive',
	  'street2' => 'Unit-128',
	  'city' => 'Phoenix',
	  'state' => 'AZ',
	  'zip' => '85027',
	  'country' => 'US',
	  'email' => 'dirk_d@gmail.com'
	));
	
	$parcel = \EasyPost\Parcel::create(array(
	  "length" => 6,
	  "width" => 6,
	  "height" => 4,
	  "weight" => 30
	));
	
	$shipment = \EasyPost\Shipment::create(array(
	  "to_address" => $toAddress,
	  "from_address" => $fromAddress,
	  "parcel" => $parcel,
	  "is_return" => true
	));
	
	echo "<form>";
	foreach($shipment->rates as $rate){
		echo "<input type='radio'>".$rate->service." - $".$rate->rate."</input><br>";
	}
	echo "<input type='button' id='buyLabels' value='Buy Label' onClick='buy()'></input>";
	echo "</form>";
	$shipment->buy($shipment->lowest_rate());

	echo $shipment->postage_label->id;

?>