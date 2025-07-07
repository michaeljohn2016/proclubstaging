<?php
// Include WordPress core
if (!defined('ABSPATH')) {
    require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');
}

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $current_user;
	get_currentuserinfo();

	if ($account_email = $_GET['accountEmail']) {
		if ( $wp_user = get_user_by('email', $_GET['accountEmail']) ) {
			$user_id = $wp_user->ID;
		} else {
			$user_id = wp_create_user($account_email, bin2hex(openssl_random_pseudo_bytes(6)), $account_email );
		}
	} else {
		$user_id = $current_user->ID;
	}
	
	if(!empty($_SESSION['er_qm_cart'])){
		$title = rand(10000, 99999);
		$post_info = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_author' => $user_id,
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
		if (sanitize_text_field($_GET['vendorwindow']) == "golftec") {
			update_post_meta($new_post_id, 'order_source', "golftec");
		}

		
		

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
		$subject = "A New Order Has Been Placed On Sell.ProClubs.com!";
		$headers = 'Content-type: text/html';
		$message = "<div style='width:750px; text-align:center; padding:50px;'>";		
		$message .= "<h3>A new order has been placed on Sell.ProClubs.com:</h3>";
		$message .= "<p>Order #$title</strong></p>";
		// $message .= "<p><strong>Customer Info</strong></p>";
		// $message .= "<p>Name: ".get_user_meta($current_user->ID, 'first_name', true)." ".get_user_meta($current_user->ID, 'last_name', true)."</p>";
		// $message .= "<p>Street: ".get_user_meta($current_user->ID, 'street_address', true)."</p>";
		// $message .= "<p>City, State, Zipcode: ".get_user_meta($current_user->ID, 'city_address', true).", ".get_user_meta($current_user->ID, 'state_address', true)." ".get_user_meta($current_user->ID, 'zip_code_address', true)."</p><br><br>";
		$message .= "<strong><a href='http://sell.ProClubs.com/wp-admin/post.php?post=$new_post_id&action=edit'>Click here to view the rest of the order</a></strong>";
		$message .= "<h5>Copyright 2025 ProClubs.com</h5></div>";
		wp_mail( $to, $subject, $message, $headers);
		
		// End of Email to Admin Code
		
		get_header();?>

	<div id="primary" class="site-content">
		
		<div id="content" role="main">
			<img src="https://sell.proclubs.com/wp-content/themes/diztinct/src/images/proclubs-logo.png" class="img-responsive" style="display: none;" id="printlogo">
<div class="hideprint">
		<h1 id="order-num-display"><?php echo $display_title; ?></h1>
		<h2>Thank you for submitting your order with ProClubs.com.</h2>  
        
		<div style="font-size: 15px; margin-top: 5px; margin-bottom: 10px;" class="hideprint">Please print a copy of this page and include it inside the box with shipment.</div>
		<p>
		<a href="javascript:window.focus();window.print()" class="order-sched-buttons-print hideprint">Print this page</a></p>
		<p><strong>Need a shipping label?</strong> Check out <a href="/pirateship/" target="_blank">our recommendation for discounted rates</a>.</p>
</div>

<div id="shipping-sheet" style="page-break-after:always !important">
<table id="order-top-info">
<tr>
<td class="left-order-cell" style="vertical-align: top; padding-right: 30px;">
<div id="ship-to-address">
		<h2>Ship To:</h2>
		<p>ProClubs.com<br />
		23335 N. 18th. Drive Unit 128<br />
		Phoenix, AZ 85027</p>
</div>
</td>
<td class="right-order-cell">
	<?php if (!isset($_GET['vendorwindow'])) { ?>
	<div id="buyer-info">
			<h2>Account</h2>
			<p><?php echo get_user_meta($current_user->ID, 'first_name', true)." ".get_user_meta($current_user->ID, 'last_name', true);?><br />
			<?php echo get_user_meta($current_user->ID, 'street_address', true);?><br />
			<?php echo get_user_meta($current_user->ID, 'city_address', true);?>, <?php echo get_user_meta($current_user->ID, 'state_address', true);?> <?php echo get_user_meta($current_user->ID, 'zip_code_address', true);?><br />
			<?php echo $current_user->user_email;?><br />
			<?php echo get_user_meta($current_user->ID, 'phone_number', true);?></p>
	</div>
	<?php } ?>
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
			echo "<h3 style='margin-top: 15px;'>Address for Check</h3>";
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
				<th class="er_qm_shopping_cart_columns">Unit Price</th>
				<th class="er_qm_shopping_cart_columns">Total</th>
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
					if ($item['premiumShaft'] !== "false" && $item['premiumShaft'] !== "none") { $item['shaft'] = pc_get_shaft_friendly_name($item['premiumShaft']);}
					echo $item['headOnly'] == "true" ? "<td>Head Only</td>" : "<td>".$item['shaft']."</td>";
					echo "<td>".$item['condition']."</td>";
					echo "<td>".$item['quantity']."</td>";
					echo "<td> $".$item['price']."</td>";
					echo "<td> $".number_format((str_replace( ',', '', $item['price'] ) * $item["quantity"]), 2)."</td>";
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
		
		if ($_GET['accountEmail']) {
			$to = $_GET['accountEmail'];
		} else {
			$to = $current_user->user_email;
		}
		
		$subject = "Thank you for your recent order at ProClubs!";
		$headers = 'Content-type: text/html';
		$message = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
			<title>Order Confirmation - ProClubs</title>
			<!--[if mso]>
			<style type="text/css">
				table { border-collapse: collapse; }
				.container { width: 980px !important; }
			</style>
			<![endif]-->									
			<style>
			body {
			  -webkit-text-size-adjust:none;
			  -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;
			}
			@media only screen and (max-width: 600px) {
				h3 img {
					display:none !important;
				}
			}
				
			</style>
		</head>
		<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

						<!-- Main Container -->
						<table border="0" cellpadding="0" cellspacing="0" width="980" class="container" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
							
							<!-- Header with Logo -->
							<tr>
								<td align="left" style="padding: 40px 30px 50px 30px;">
									<img src="https://sell.proclubs.com/wp-content/themes/diztinct/src/images/proclubs-logo.png" alt="ProClubs" width="350" height="84" style="display: block; border: 0;" />
								</td>
							</tr>
							
							<!-- Title and Message -->
							<tr>
								<td style="padding: 0 30px 30px 30px;">
									<h1 style="margin: 0 0 15px 0; font-size: 32px; font-weight: bold; color: #000000; text-align: left; font-family: Red Hat Display, sans-serif;">Thank you for your order at ProClubs!</h1>
									<p style="margin: 0; font-size: 18px; line-height: 1.6; color: #000000; text-align: left; width: 700px; font-family: Red Hat Display, sans-serif;">Please keep this email for your records and if you didn\'t already print out the confirmation page, please print this email out and include it with your clubs shipment.</p>
								</td>
							</tr>
							
							<!-- Info Cards Section -->
							<tr>
								<td class="blocks-table" style="padding: 0 30px 30px 30px;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<!-- Ship To Card -->
											<td class="stack-column" width="300" height="350" style="vertical-align: top; padding-right: 20px;">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" height="350" style="background-color: #F2F6FB; border-radius: 8px; border: 1px solid #e9ecef;">
													<tr>
														<td style="padding: 20px 15px; vertical-align: top;">
															<h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: bold; font-family: Red Hat Display, sans-serif; color: #000000;display:flex;align-items:center;"> 
																		<img style="display:inline-block;margin: 0 15px 0 0;object-fit: contain;max-width: 23px;" src="https://wordpress-1090382-5542248.cloudwaysapps.com/wp-content/uploads/2025/06/fast-delivery-1-1.png" alt="SHIP TO">
																		<span style="display:inline-block;">SHIP TO</span>
																		</h3>
																		<hr style="border: none; height: 1px; background-color: #dee2e6; margin: 0 0 8px 0;" />
																		<p style="margin: 0; font-size: 18px; line-height: 140%; color: #303133; font-family: Red Hat Display, sans-serif;">
																			ProClubs.com<br/>
																			23335 N. 18th. Drive Unit 128<br/>
																			Phoenix, AZ 85027<br/>
																			Syracuse, New York 13204
																		</p>
														</td>
													</tr>
												</table>
											</td>
											
											<!-- Payment Details Card -->
											<td class="stack-column" width="300" height="350" style="vertical-align: top; padding-right: 20px;">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" height="350" style="background-color: #F2F6FB; border-radius: 8px; border: 1px solid #e9ecef;">
													<tr>
														<td style="padding: 20px 15px; vertical-align: top;">
															<h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: bold; color: #000000; font-family: Red Hat Display, sans-serif;display:flex;align-items:center;">
																		<img style="display:inline-block;margin: 0 15px 0 0;object-fit: contain;max-width: 23px;" src="https://wordpress-1090382-5542248.cloudwaysapps.com/wp-content/uploads/2025/06/card-1-1.png" alt="Payment Method">
																		<span style="display:inline-block;">PAYMENT DETAILS</span>
																		</h3>
																		<hr style="border: none; height: 1px; background-color: #dee2e6; margin: 0 0 8px 0;" />
																		<p style="margin: 0 0 10px 0; font-size: 14px; color: #000000; font-family: Red Hat Display, sans-serif;"><strong>Payment Method:</strong></p>
																		<p style="margin: 0 0 15px 0; font-size: 18px; line-height: 1.4; color: #303133; font-family: Red Hat Display, sans-serif;">';

																		if($_GET['payment']=='paypal'){ 
																			$message .= 'PayPal</p>';
																			$message .= '<hr style="border: none; height: 1px; background-color: #dee2e6; margin: 0 0 8px 0;" />';
																			$message .= '<p style="margin: 0 0 10px 0; font-size: 14px; color: #000000; font-family: Red Hat Display, sans-serif;"><strong>PayPal Email:</strong></p>';
																			$message .= '<p style="margin: 0; font-size: 18px; line-height: 1.4; color: #303133; font-family: Red Hat Display, sans-serif;">'.$_GET['paypal'].'</p>';
																		} else {
																			$message .= 'Check</p>';
																			$message .= '<hr style="border: none; height: 1px; background-color: #dee2e6; margin: 0 0 8px 0;" />';
																			$message .= '<p style="margin: 0 0 10px 0; font-size: 14px; color: #000000; font-family: Red Hat Display, sans-serif;"><strong>Address for Check:</strong></p>';
																			$message .= '<p style="margin: 0 0 8px 0; font-size: 18px; line-height: 1.4; color: #303133; font-family: Red Hat Display, sans-serif;">'.$_GET['nameoncheck'].'</p>';
																			$message .= '<p style="margin: 0 0 8px 0; font-size: 18px; line-height: 1.4; color: #303133; font-family: Red Hat Display, sans-serif;">'.$_GET['street'].'</p>';
																			$message .= '<p style="margin: 0; font-size: 18px; line-height: 1.4; color: #303133; font-family: Red Hat Display, sans-serif;">'.$_GET['city'].', '.$_GET['state'].' '.$_GET['zipcode'].'</p>';
																		}
		
																		$message .= '
														</td>
													</tr>
												</table>
											</td>
											
											<!-- Customer Info Card -->
											<td class="stack-column" width="300" height="350" style="vertical-align: top;">
												<table border="0" cellpadding="0" cellspacing="0" width="300" height="350" style="background-color: #F2F6FB; border-radius: 8px; border: 1px solid #e9ecef;">
													<tr>
														<td style="padding: 20px 15px; vertical-align: top;">
															<h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: bold; font-family: Red Hat Display, sans-serif;color: #000000;display:flex;align-items:center;">
																		<img style="display:inline-block;margin: 0 15px 0 0;object-fit: contain;max-width: 23px;" src="https://wordpress-1090382-5542248.cloudwaysapps.com/wp-content/uploads/2025/06/user-1-1.png" alt="CUSTOMER INFO">
																		<span style="display:inline-block;">CUSTOMER INFO</span>
																		</h3>
																		<hr style="border: none; height: 1px; background-color: #dee2e6; margin: 0 0 8px 0;" />
																		<p style="margin: 0 0 4px 0; font-size: 14px; color: #000000; font-weight: bold; font-family: Red Hat Display, sans-serif;">Order #</p>
																		<p style="margin: 0 0 8px 0; font-size: 18px; color: #303133; font-family: Red Hat Display, sans-serif;">Order '.$title.'</p>
																		<hr style="border: none; height: 1px; background-color: #dee2e6; margin: 0 0 8px 0;" />
																		<p style="margin: 0 0 4px 0; font-size: 14px; color: #000000; font-weight: bold; font-family: Red Hat Display, sans-serif;">Name:</p>
																		<p style="margin: 0 0 8px 0; font-size: 18px; color: #303133; font-family: Red Hat Display, sans-serif;">'.get_user_meta($current_user->ID, 'first_name', true).' '.get_user_meta($current_user->ID, 'last_name', true).'</p>
																		<hr style="border: none; height: 1px; background-color: #dee2e6; margin: 0 0 8px 0;" />
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
							<!-- Order Table -->
							<tr>
								<td style="padding: 0 30px 30px 30px;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-right: 1px solid #dee2e6;border-left: 1px solid #dee2e6; border-radius: 8px; overflow: hidden;">
										<!-- Table Header -->
										<tr style="background-color: #0066cc;">
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; border-right: 0px solid #0052a3; font-family: Red Hat Display, sans-serif;">MANUFACTURER</td>
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; border-right: 0px solid #0052a3; font-family: Red Hat Display, sans-serif;">TYPE</td>
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; border-right: 0px solid #0052a3; font-family: Red Hat Display, sans-serif;">MODEL</td>
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; border-right: 0px solid #0052a3; font-family: Red Hat Display, sans-serif;">SHAFT</td>
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; text-align: center; border-right: 0px solid #0052a3; font-family: Red Hat Display, sans-serif;">IRON SET QTY</td>
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; border-right: 0px solid #0052a3; font-family: Red Hat Display, sans-serif;">CONDITION</td>
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; text-align: center; border-right: 0px solid #0052a3; font-family: Red Hat Display, sans-serif;">QTY</td>
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; text-align: right; border-right: 0px solid #0052a3; font-family: Red Hat Display, sans-serif;">UNIT PRICE</td>
											<td style="padding: 12px 8px; font-size: 11px; font-weight: bold; color: #ffffff; text-transform: uppercase; text-align: right; font-family: Red Hat Display, sans-serif;">TOTAL</td>
										</tr>';
		
										// Generate cart info for email
										$order_total = 0;
										$row_count = 0;
										foreach($_SESSION['er_qm_cart'] as $item){
											$queried_post = get_post_meta($item['id']);
											$image_url = get_the_post_thumbnail_url($item['id'], 'medium');
											$row_bg = ($row_count % 2 == 0) ? '#ffffff' : '#ffffff';
											
											$message .= '<tr style="background-color: '.$row_bg.';">';
											$message .= '<td style="padding: 12px 8px; font-size: 13px; color: #333333; border-right: 0px solid #0052a3; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">';
											$message .= '<img src="'.$image_url.'" alt="'.$queried_post['club_manufacturer'][0].'" style="max-width: 40px; height: auto; margin-right: 6px; vertical-align: middle;border-radius: 6px;">';
											$message .= $queried_post['club_manufacturer'][0];
											$message .= '</td>';
												$message .= '<td style="padding: 12px 8px; font-size: 13px; color: #333333; border-right: 0px solid #0052a3;; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">'.$queried_post['club_type_field'][0].'</td>';
												$message .= '<td style="padding: 12px 8px; font-size: 13px; color: #333333; border-right: 0px solid #0052a3;; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">'.$queried_post['club_model'][0].'</td>';
												
												if ($item['premiumShaft'] !== "false" && $item['premiumShaft'] !== "none") { 
													$item['shaft'] = pc_get_shaft_friendly_name($item['premiumShaft']);
												}
												$message .= $item['headOnly'] == "true" ? '<td style="padding: 12px 8px; font-size: 13px; color: #333333; border-right: 0px solid #0052a3;; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">Head Only</td>' : '<td style="padding: 12px 8px; font-size: 13px; color: #333333; border-right: 0px solid #0052a3;; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">'.$item['shaft'].'</td>';
												
												$iron_qty = (isset($item["ironQuantity"]) && $item["ironQuantity"] > 0) ? $item["ironQuantity"] : '';
												$message .= '<td style="padding: 12px 8px; font-size: 13px; color: #333333; text-align: center; border-right: 0px solid #0052a3;; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">'.$iron_qty.'</td>';
												$message .= '<td style="padding: 12px 8px; font-size: 13px; color: #333333; border-right: 0px solid #0052a3;; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">'.$item['condition'].'</td>';
												$message .= '<td style="padding: 12px 8px; font-size: 13px; color: #333333; text-align: center; border-right: 0px solid #0052a3;; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">'.$item['quantity'].'</td>';
												$message .= '<td style="padding: 12px 8px; font-size: 13px; color: #333333; text-align: right; border-right: 0px solid #0052a3;; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">$'.$item['price'].'</td>';
												$message .= '<td style="padding: 12px 8px; font-size: 13px; font-weight: bold; color: #333333; text-align: right; border-bottom: 1px solid #dee2e6; font-family: Red Hat Display, sans-serif;">$'.number_format((str_replace( ',', '', $item['price'] ) * $item["quantity"]), 2).'</td>';
											$message .= '</tr>';
											$order_total = $order_total+($item['price']*$item['quantity']);
											$_SESSION['ordertotal'] = $order_total;
											$row_count++;
										}
		
										$message .= '
												</table>
											</td>
										</tr>
							
										<!-- Total Section -->
										<tr>
											<td style="padding: 0 30px 30px 30px;">
									
												<table border="0" cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td align="right" style="padding-top: 20px;">
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td style="font-size: 20px; font-weight: bold; color: #000000; padding-right: 15px; font-family: Red Hat Display, sans-serif;">Total:</td>
																	<td style="font-size: 20px; font-weight: bold; color: #000000; font-family: Red Hat Display, sans-serif;">$'.number_format($order_total, 2).'</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>';
		
										if((boolean)$_SESSION['label']){ //only use easy post if user wants label
											$message .= '
															<!-- Shipping Buttons -->
															<tr>
																<td align="center" style="padding: 0 30px 30px 30px;">
																	<table border="0" cellpadding="0" cellspacing="0">
																		<tr>
																			<td align="center" style="padding-bottom: 15px;">
																				<a href="'.$shipment->postage_label->label_url.'" style="display: inline-block; padding: 12px 30px; background-color: #0066cc; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; font-family: Red Hat Display, sans-serif;">Click Here to View/Print Your Shipping Label!</a>
																			</td>
																		</tr>
																		<tr>
																			<td align="center">
																				<a href="https://wwwapps.ups.com/pickup/schedule?loc=en_US&WT.svl=PNRO_L1" style="display: inline-block; padding: 12px 30px; background-color: #8B4513; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; font-family: Red Hat Display, sans-serif;">Schedule Your UPS Pick-Up!</a>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>';
										}
		
								$message .= '
							<!-- Footer -->
							<tr>
								<td align="center" style="padding: 30px 30px 40px 30px; border-top: 1px solid #dee2e6;">
									<p style="margin: 0; font-size: 12px; color: #6c757d; font-family: Red Hat Display, sans-serif;">Copyright 2025 ProClubs.com</p>
								</td>
							</tr>
							
						</table>

		</body>
		</html>';
		
		// Send email to customer
		wp_mail( $to, $subject, $message, $headers);
		update_post_meta($new_post_id, 'club_order_confirmation_message', $message);
		
	?>

	</div>
</div>
</div>
</div>
<Style>

    
@media print {
	table {page-break-inside: avoid;}
	tr.item {
	    display: table-row !important;
	}
	th.er_qm_shopping_cart_columns {
	    display: table-cell !important;
	}
	.hideprint {
		display: none;
	}
    #shipping-sheet p {
		margin-bottom: 0px;
	}
	table#er_qm_order_confirmation_table {
	    text-align: left;
	}
	h1, h2, h3, h4, h5, h6 {
	    font-family: arial !important;
	    font-size: 20px;
	    margin-bottom: 0px;
	}
	.site-header, .site-footer {
		display:none !important;
	}
	#printlogo {
		display: block !important;
	}
	h1#order-detail-bnr { 
		display: none;
	}
}
h1#order-detail-bnr {
	    font-size: 30px;
	}

</Style>
	<?php
	get_footer();
	session_destroy();
	session_start();
	exit;


?>