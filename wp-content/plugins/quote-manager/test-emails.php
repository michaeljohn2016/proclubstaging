<?php
	require('../../../wp-load.php');
	/*
	$to = "erosas@email.arizona.edu";
	$subject = "Your check from www.GolfBuyers.com is in the mail!";
	$headers = 'Content-type: text/html';
	$message = "<div style='width:750px; border-style:solid; border-width:1px; border-color:#0174DF; text-align:center; background:#FFFFF; padding:50px; background:#EFF5FB;'>";
	$message .= "<img src='http://dev.luminary.ws/golfbuyers.com/wp-content/plugins/quote-manager/images/golfbuyerslogo.png'></img><br><br><br>";
	$message .= "Expect your check in the mail soon, thanks again for using www.GolfBuyers.com!<br><br></div>";
	//wp_mail( "erosas@email.arizona.edu", $subject, $message, $headers);
	echo wp_mail( "eric@luminary.ws", $subject, $message, $headers);
	*/
			// Email to the customer 
		
		$to = get_option('admin_email' );
		$subject = "Thank you for your recent order at www.ProClubs.com!";
		$headers = 'Content-type: text/html';
		$message = "<div style='width:750px; border-style:solid; border-width:1px; border-color:#0174DF; text-align:center; background:#FFFFF; padding:50px; background:#EFF5FB;'>";
		$message .= "<img src='https://sell.proclubs.com/wp-content/themes/twentytwelve/images/logo.gif'></img><br><br><br>";
		$message .= "Thank you for your order at www.ProClubs.com!<br><br>";
		$message .= "Please keep this email for your records and if you didn't already print out the confirmation page, please print this email out and include it with your clubs shipment.<br><br>";
		// Beginning of proclubs shipping info
		$message .= "<div><div style='width:375px;float:left;'><h5>Ship To:</h5><br>";
		$message .= "<p>ProClubs.com</p><p>23335 N. 18th. Drive Unit 128</p><p>Phoenix, AZ 85027</p></div>";
		// Beginning of customer info	
		$message .= "<div style='width:375px; float:right;'><h5><strong>Customer Info</strong></h5><br>";
		$message .= "<p><strong>Name: </strong>".get_user_meta($current_user->ID, 'first_name', true)." ".get_user_meta($current_user->ID, 'last_name', true)."</p>";
		$message .= "<p><strong>Street: </strong>".get_user_meta($current_user->ID, 'street_address', true)."</p>";
		$message .= "<p><strong>City, State, Zipcode: </strong>".get_user_meta($current_user->ID, 'city_address', true).", ".get_user_meta($current_user->ID, 'state_address', true)." ".get_user_meta($current_user->ID, 'zip_code_address', true)."</p><br><br></div></div>";
		// Beginning of payment info
		$message .= "<div style='width:750px; padding-right:465px;'><h5>Payment Details:</h5><br>";
		if($_GET['payment']=='paypal'){ 
			$message .= "<p><strong>Payment Method:</strong>PayPal</p><p><strong>PayPal Email:</strong>".$_GET['paypal']."</p>";
		}else{
			$message .= "<p><strong>Payment Method:</strong>Check</p>"; 
			$message .= "<p><strong>Address for Check</strong></p>";
			$message .= "<p>".$_GET['nameoncheck']."</p>";
			$message .= "<p>".$_GET['street']."</p>";
			$message .= "<p>".$_GET['city'].", ".$_GET['state']." ".$_GET['zipcode']."</p></div>";
		}

		// Beginning of block for filler
		//$message .= "<div style='width:375px; float:right;'><h5><strong></strong></h5><br><br><br></div></div><br><br><br>";
		
		// Beginning of cart info
		$message .= "<div><table style='float:left;' id='er_qm_order_confirmation_table'>";
		//$message .= "<table id='er_qm_order_confirmation_table'>";
		$message .= "<tr style='border-bottom:1px; border-style:solid; height:25px;'>";
		$message .= "<th class='er_qm_shopping_cart_columns' style='width:107px;'>Manufacturer</th>";
		$message .= "<th class='er_qm_shopping_cart_columns' style='width:107px;'>Type</th>";
		$message .= "<th class='er_qm_shopping_cart_columns' style='width:107px;'>Model</th>";
		$message .= "<th class='er_qm_shopping_cart_columns' style='width:107px;'>Shaft</th>";
		$message .= "<th class='er_qm_shopping_cart_columns' style='width:107px;'>Condition</th>";
		$message .= "<th class='er_qm_shopping_cart_columns' style='width:107px;'>Quantity</th>";
		$message .= "<th class='er_qm_shopping_cart_columns' style='width:107px;'>Quote</th>";
		$message .= "</tr>";
		
		// Generated cart info
		$order_total = 0;
		foreach($_SESSION['er_qm_cart'] as $item){
				$queried_post = get_post_meta($item['id']);
				$message .= "<tr class='item' style='padding-top:10px; height:25px;'>";
					$message .= "<td>".$queried_post['club_manufacturer'][0]."</td>";
					$message .= "<td>".$queried_post['club_type_field'][0]."</td>";
					$message .= "<td>".$queried_post['club_model'][0]."</td>";
					$message .= "<td>".$item['shaft']."</td>";
					$message .= "<td>".$queried_post['club_condition'][0]."</td>";
					$message .= "<td>".$item['quantity']."</td>";
					$message .= "<td> $".$item['price']."</td>";
				$message .= "</tr>";
				$order_total = $order_total+($item['price']*$item['quantity']);
				$_SESSION['ordertotal'] = $order_total;
				
				$user_id_value = $current_user->ID;
				$post_id_value = $item['id'];
				$quote_value = $item['price']*$item['quantity'];
		}
		$message .= "</table>";
		$message .= "<br><h1 id='er_qm_order_total'> Order Total : $".$order_total."</h1><br><br></div></div>";
		$new_message = $message;
		wp_mail( "eric@luminary.ws", $subject, $new_message, $headers);
		echo $message;

?>