<?php

require('../../../wp-load.php');

?>

<pre>
<?php

	session_start();
	if(!session_id()){
		session_start();
		if(empty($_SESSION['er_qm_cart'])){
			$_SESSION['er_qm_cart'] = array();
		}
		if(empty($_SESSION['temp'])){
			$_SESSION['temp'] = "";
		}
		if(empty($_SESSION['er_qm_hold_title'])){
			$_SESSION['er_qm_hold_title'] = "";
		}
		if(empty($_SESSION['er_qm_comments'])){
			$_SESSION['er_qm_comments']="";
		}
		if(empty($_SESSION['condition'])){
			$_SESSION['condition']="";
		}
		if(empty($_SESSION['er_qm_order_total'])){
			$_SESSION['er_qm_order_total']="";
		}
	}
	$mobileCart = json_decode($_POST['payload'],true);
	foreach($mobileCart as $item){
		array_push($_SESSION['er_qm_cart'], $item);
	}
?>
</pre>