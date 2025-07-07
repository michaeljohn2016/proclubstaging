<?php

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $current_user;
	get_currentuserinfo();
	
	if(!empty($_SESSION['er_qm_cart'])){
		$title = rand(10000, 99999);
		$post_info = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_author' => $current_user->ID,
			'post_type' => 'club_orders'
		);
		$payment_type = "Check";
		if($_GET['payment']=="paypal") $payment_type = "PayPal";
		$address = $_GET['nameoncheck']."&#13;&#10;".$_GET['street']."&#13;&#10;".$_GET['city'].", ".$_GET['state']." ".$_GET['zipcode'];
		
		$display_title = "";
		if($_SESSION['er_qm_hold_title']==""){
			$new_post_id = wp_insert_post($post_info, true);
			$display_title = "Order # ".$title;
		} else {
			$new_post_id = $_SESSION['er_qm_hold_title'];
			$display_title = "Order # ".get_the_title($_SESSION['er_qm_hold_title']);
		}
		update_post_meta($new_post_id, 'club_order_total', $_SESSION['er_qm_order_total']);
		update_post_meta($new_post_id, 'club_order_status', 'Awaiting Clubs');
		update_post_meta($new_post_id, 'club_order_payment', $payment_type);
		update_post_meta($new_post_id, 'club_order_clubs', $_SESSION['er_qm_cart']);
		update_post_meta($new_post_id, 'club_order_address', $address);
		update_post_meta($new_post_id, 'club_order_paypal_email', $_GET['paypal']);
		update_post_meta($new_post_id, 'club_order_comments', $_SESSION['er_qm_comments']);
		
		

	}
	if((boolean)$_SESSION['label']){ //only use easy post if user wants label
		require_once("easypost/lib/easypost.php");
		\EasyPost\EasyPost::setApiKey('6sDxqEGTc_1O7L1d2Z-pPA');
		//\EasyPost\EasyPost::setApiKey('U9Q3eumGWmvQMK2VQVoXhw');
		$shipment = \EasyPost\Shipment::retrieve(array('id' => $_SESSION['shipment_id']));
		$shipment->buy($shipment->lowest_rate()); 
		update_post_meta($new_post_id, 'club_order_shipping_label', $shipment->postage_label->label_url);
	}else{
		update_post_meta($new_post_id, 'club_order_shipping_label', 'NULL');
	}
		
		// Beginning of Email to Admin Code
		// Email to the Customer is located lower in the code after the shipping label gets generated
		
		define('SUBSCRIBE_USER_BASE_DIR', dirname(__FILE__));
		$to = get_option('admin_email' );
		$subject = "A New Order Has Been Placed On www.GolfBuyers.com!";
		$headers = 'Content-type: text/html';
		$message = "<div style='width:750px; border-style:solid; border-width:1px; border-color:#0174DF; text-align:center; padding:50px; background:#EFF5FB;'>";
		$message .= "<img src='https://golfbuyers.com/wp-content/uploads/2014/01/Golf-Buyers-logo.png'></img><br><br><br>";
		$message .= "<h3>A new order has been placed on www.golfbuyers.com:</h3>";
		$message .= "<p>Order #$title</strong></p>";
		$message .= "<p><strong>Customer Info</strong></p>";
		$message .= "<p>Name: ".get_user_meta($current_user->ID, 'first_name', true)." ".get_user_meta($current_user->ID, 'last_name', true)."</p>";
		$message .= "<p>Street: ".get_user_meta($current_user->ID, 'street_address', true)."</p>";
		$message .= "<p>City, State, Zipcode: ".get_user_meta($current_user->ID, 'city_address', true).", ".get_user_meta($current_user->ID, 'state_address', true)." ".get_user_meta($current_user->ID, 'zip_code_address', true)."</p><br><br>";
		$message .= "<strong><a href='http://www.golfbuyers.com/wp-admin/post.php?post=$new_post_id&action=edit'>Click here to view the rest of the order</a></strong>";
		$message .= "<h5>Copyright 2014 GolfBuyers.com</h5></div>";
		wp_mail( $to, $subject, $message, $headers);
		
		// End of Email to Admin Code
		
		get_header();?>

<div id="content-container" class="container_24">
<div id="main-content" class="grid_24 <?php echo $content_position; ?>">
<div class="main-content-padding">
<div class="entry">
<div id="dont-print-me">
		<h1 id="order-num-display"><?php echo $display_title; ?></h1>
		<h1>Thank you for submitting your order with GolfBuyers.com.</h1>  
        
		<h2>Please <a href="javascript:window.print()">print a copy</a> of this page and include it inside the box with shipment.</h2>
		<h2>(Shipping Information Below!)</h2>
		<p><a href="https://wwwapps.ups.com/pickup/schedule?loc=en_US&WT.svl=PNRO_L1" target="_blank" class="order-sched-buttons">Schedule your pick-up with UPS</a>
		<a href="javascript:window.print()" target="_blank" class="order-sched-buttons-print">Print this page</a></p>
</div>

<div id="shipping-sheet" style="page-break-after:always !important">
<div id="shipping-banner"><img height="86px" width="712px" src="http://www.golfbuyers.com/golfbuyers-PDF-header.png" alt="GolfBuyer Banner" /></div>

<table id="order-top-info">
<tr>
<td class="left-order-cell">
<div id="ship-to-address">
		<h2>Ship To:</h2>
		<p>GolfBuyers.com<br />
		23335 N. 18th. Drive Unit 128<br />
		Phoenix, AZ 85027</p>
</div>
</td>
<td class="right-order-cell">
<div id="buyer-info">
		<h2>Account</h2>
		<p><?php echo get_user_meta($current_user->ID, 'first_name', true)." ".get_user_meta($current_user->ID, 'last_name', true);?><br />
		<?php echo get_user_meta($current_user->ID, 'street_address', true);?><br />
		<?php echo get_user_meta($current_user->ID, 'city_address', true);?>, <?php echo get_user_meta($current_user->ID, 'state_address', true);?> <?php echo get_user_meta($current_user->ID, 'zip_code_address', true);?><br />
		<?php echo $current_user->user_email;?><br />
		<?php echo get_user_meta($current_user->ID, 'phone_number', true);?></p>
</div>
</td>
</tr>
</table>

<h1 id="order-detail-bnr">Order Details:</h1>
<h2 id="order-num-display"><?php echo $display_title; ?></h2>

<table id="order-bot-info">
<tr>
<td class="left-order-cell">
<div id="payment-type-wrapper">
		<h2>Payment Type</h2>
		<?php 
			if($_GET['payment']=='paypal'){ 
			echo "<p>PayPal</p>"; 
			echo "<h3>PayPal Account</h3>";
			echo "<p>".$_GET['paypal']."</p>";
			}else{
			echo "<p>Check</p>";
			echo "<h3>Address for Check</h3>";
			echo "<p>".$_GET['nameoncheck']."</p>";
			echo "<p>".$_GET['street']."</p>";
			echo "<p>".$_GET['city'].", ".$_GET['state']." ".$_GET['zipcode']."</p>";
			}?>
</div>
</td>
<td class="right-order-cell">

<?php if((boolean)$_SESSION['label']){ //only use easy post if user wants label ?>
    <div id="shipping-info-wrapper">
            <h2>Shipping Information:</h2>
            <p>Shipping Type: Via UPS <?php $_GET['shipmentType'];?></p>
            <p>Shipping Label: Label printed below.</p>
    </div>
<?php }//end if label ?>

</td>
</tr>
</table>

<div id="order-add-info">
		<h2>Additional Comments</h2>
		<p><?php echo $_SESSION['er_qm_comments']; ?></p>
</div>

<div id="order-detail-info">
		<h2>Club Details:</h2>
		<table id="er_qm_order_confirmation_table">
			<tr style="border-bottom:1px; border-style:solid; height:25px;">
				<th class="er_qm_shopping_cart_columns">Manufacturer</th>
				<th class="er_qm_shopping_cart_columns">Type</th>
				<th class="er_qm_shopping_cart_columns">Model</th>
				<th class="er_qm_shopping_cart_columns">Shaft</th>
				<th class="er_qm_shopping_cart_columns">Condition</th>
				<th class="er_qm_shopping_cart_columns">Quantity</th>
				<th class="er_qm_shopping_cart_columns">Quote</th>
			</tr>
		<?php
		if(!empty($_SESSION['er_qm_cart'])){
		$order_total = 0;
		foreach($_SESSION['er_qm_cart'] as $item){
				$queried_post = get_post_meta($item['id']);
				echo "<tr class='item' style='padding-top:10px; height:25px;'>";
					echo "<td>".$queried_post['club_manufacturer'][0]."</td>";
					echo "<td>".$queried_post['club_type_field'][0]."</td>";
					echo "<td>".$queried_post['club_model'][0]."</td>";
					echo "<td>".$item['shaft']."</td>";
					echo "<td>".$item['condition']."</td>";
					echo "<td>".$item['quantity']."</td>";
					echo "<td> $".$item['price']."</td>";
				echo "</tr>";
				$order_total = $order_total+($item['price']*$item['quantity']);
				
				$_SESSION['ordertotal'] = $order_total;
				
				$user_id_value = $current_user->ID;
				$post_id_value = $item['id'];
				$quote_value = $item['price']*$item['quantity'];
				$sql = "INSERT INTO wp_clubs_ordered_stats
						(user_id, post_id, quote)
						VALUES ($user_id_value, $post_id_value, $quote_value);";

				dbDelta($sql);
		}
		echo "</table>";
		echo "<br><h1 id='er_qm_order_total'> Order Total : $".$order_total."</h1></div>";
		echo "<h3 id='thanks-for-biz'>Thank you for your business!</h3></div><div style='clear:both;'>&nbsp;</div><!-- end-shipping sheet -->";	
		
		} else {
			echo "</table>";
			echo "<br><br><p class='generic_center' >There was an error processing your order. We apologize for the inconvenience, please try placing your order again.</p><br>";
		}
	
        if((boolean)$_SESSION['label']){ //only use easy post if user wants label
		?>
   			 <!-- display the shipping label -->
            <h2 id="shipping-label-display">Shipping Label:</h2>
            <img src="<?php $shipping_label = $shipment->postage_label->label_url; echo $shipping_label; ?>" width="500px" />
		<?php		
		}//end if label
			// Email to the customer 
		
		$to = $current_user->user_email;
		$subject = "Thank you for your recent order at www.GolfBuyers.com!";
		$headers = 'Content-type: text/html';
		$message = "<div style='width:750px; border-style:solid; border-width:1px; border-color:#0174DF; text-align:center; background:#FFFFF; padding:50px; background:#EFF5FB;'>";
		$message .= "<h4>This is a copy of your original order confirmation</h4><br>";
		$message .= "<img src='https://golfbuyers.com/wp-content/uploads/2014/01/Golf-Buyers-logo.png'></img><br><br><br>";
		$message .= "Thank you for your order at www.GolfBuyers.com!<br><br>";
		$message .= "Please keep this email for your records and if you didn't already print out the confirmation page, please print this email out and include it with your clubs shipment.<br><br>";
		// Beginning of proclubs shipping info
		$message .= "<div><div style='width:375px;float:left;'><h5>Ship To:</h5><br>";
		$message .= "<p>GolfBuyers.com</p><p>23335 N. 18th. Drive Unit 128</p><p>Phoenix, AZ 85027</p></div>";
		// Beginning of customer info	
		$message .= "<div style='width:375px; float:right;'><h5><strong>Customer Info</strong></h5><br>";
		$message .= "<p><strong>Order #$title</strong></p>";
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
		
		// Beginning of cart info
		$message .= "<div><table id='er_qm_order_confirmation_table'>";
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
					$message .= "<td>".$item['condition']."</td>";
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
		$message .= "<br><h1 id='er_qm_order_total'> Order Total : $".$order_total."</h1><br><br></div>";
		
		if((boolean)$_SESSION['label']){ //only use easy post if user wants label
			$message .= "<a href='".$shipment->postage_label->label_url."'><h5 style='font-size:250%;'>Click Here to View/Print Your Shipping Label!</h5></a>";
			$message .= "<a href='https://wwwapps.ups.com/pickup/schedule?loc=en_US&WT.svl=PNRO_L1'><h5 style='font-size:250%;'>Schedule Your UPS Pick-Up!</h5></a>";
		}//end if label
		
		$message .="<h5>Copyright 2014 GolfBuyers.com</h5></div>";
		wp_mail( $to, $subject, $message, $headers);
		update_post_meta($new_post_id, 'club_order_confirmation_message', $message);
		
	?>
			</div>
		</div>
	</div><!-- #content -->
</div><!-- #primary -->
	<?php
	get_footer();
	session_destroy();
	session_start();
	exit;


?>