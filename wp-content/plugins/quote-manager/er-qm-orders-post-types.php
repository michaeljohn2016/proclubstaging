<?php
	/*
	Plugin Name: Orders Post Type
	Description: Creates the club_orders post type.
	Version: 0.1
	Author: ER - Luminary WS, modifications by JS - Luminary WS
	*/

	/* Set up the post types. */
	add_action("admin_init", "er_qm_admin_init");
	add_action( 'init', 'boj_club_orders_register_post_types' );
	add_filter("manage_edit-club_orders_columns", "club_orders_edit_columns");
	add_action("manage_posts_custom_column",  "club_orders_custom_columns", 10, 2);
	add_action('save_post', 'er_qm_save_details');
	add_action('admin_footer-edit.php', 'javascript_for_paypal_button' );
	add_action('admin_footer', 'javascript_for_paypal_button' );
	add_action('wp_ajax_send_check_confirmation', 'send_check_confirmation');
	add_action('wp_ajax_send_confirmation_email', 'send_confirmation_email');


	function er_qm_admin_init(){
		add_meta_box("club_order_clubs-meta", "Order", "club_order_clubs", "club_orders", "normal", "low");
		add_meta_box("club_order_total-meta", "Order Total", "club_order_total", "club_orders", "normal", "low");
	//	add_meta_box("club_order_status-meta", "Order Status", "club_order_status", "club_orders", "normal", "low");
		add_meta_box("club_order_payment-meta", "Payment Choice", "club_order_payment", "club_orders", "normal", "low");
		
		add_meta_box("club_order_address-meta", "Address", "club_order_address", "club_orders", "normal", "low");
		add_meta_box("club_order_paypal_email-meta", "PayPal Email", "club_order_paypal_email", "club_orders", "normal", "low");
		add_meta_box("club_order_comments-meta", "Comments", "club_order_comments", "club_orders", "normal", "low");
		add_meta_box("club_order_shipping_label-meta", "Shipping Label", "club_order_shipping_label", "club_orders", "normal", "low");
		add_meta_box("club_order_pay-paypal-meta", "Pay With PayPal", "club_order_pay_paypal", "club_orders", "side", "core");
		add_meta_box("club_order_sent_check_confirmation-meta", "Send Check Confirmation", "club_order_check_confirmation", "club_orders", "side", "core");
		add_meta_box("club_order_resend_confirmation_page-meta", "Resend Check Confirmation", "club_order_resend_confirmation_page", "club_orders", "side", "core");
		add_meta_box("club_order_print-meta", "Print Order Details", "club_order_print", "club_orders", "side", "core");
		
	}

	function javascript_for_paypal_button(){
		
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("#print-order-details").click(function() {
					window.open('/print-OrderDetails.php?order=<?php global $post; echo $post->ID; ?>');
				});
				//alert($("#club_order_paypal_email").val());
				
				$("#pay-with-paypal-button").click(function(){
					var url = 'https://www.paypal.com/sendmoney?email='+$("#club_order_paypal_email").val();
					window.open(url);
					return false;
				});
				
				$("#send-check-confirmation").click(function(){
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					var email = $("#customer-info-email").html();
					var orderNumber = $("#title").val();
					var data = {
						action: 'send_check_confirmation',
						emailAdd: email,
						orderID: orderNumber
					};
					$.post(ajaxurl, data, function(response) {
						alert(response);
						
						
					});
				});
				
				$("#send-confirmation-email").click(function(){
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					var postID = $(this).attr("class");
					var email = $("#customer-info-email").html();
					data = {
						action: 'send_confirmation_email',
						post:  postID,
						email: email
					};
					//alert(postID);
					$.post(ajaxurl, data, function(response){
						alert(response);
					});
				});
				
			});
		</script>
		
		
		<?php
		
	}
	
	function send_confirmation_email(){
		$postID = $_POST['post'];
		$to = $_POST['email'];
		$message_info =  get_post_meta($postID, 'club_order_confirmation_message');
		$message = $message_info[0];
		$subject = "Thank you for your recent order at ProClubs!";
		$headers = 'Content-type: text/html';
		wp_mail($to, $subject, $message, $headers);
		die("Copy of Confirmation Page Sent!");
	}
	
	function send_check_confirmation(){
		$subject = "Your check from ProClubs is in the mail!";
		$headers = 'Content-type: text/html';
		$message = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
			<meta name="x-apple-disable-message-reformatting" />
			<title>Check Confirmation - ProClubs</title>
			<!--[if mso]>
			<style type="text/css">
				table { border-collapse: collapse; }
				.container { width: 980px !important; }
			</style>
			<![endif]-->
			<style>
			@media only screen and (max-width: 600px) {
				h3  {
					font-size: 18px !important;
				}
					p {
					font-size: 14px !important;
					}
			}
				  /* Additional padding for iOS compatibility */
        /*                                                                                    */
        /*                                                                                    */
        /*                                                                                    */
        /*                                                                                    */
        /*                                                                                    */
        /*                                                                                    */
        /*                                                                                    */
        /*                                                                                    */
			</style>
		</head>
		<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f4;">
				<tr>
					<td align="center" style="padding: 20px 0;">
						<!-- Main Container -->
						<table border="0" cellpadding="0" cellspacing="0" width="980" class="container" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
							
							<!-- Header with Logo -->
							<tr>
								<td align="left" style="padding: 40px 30px 50px 30px;">
									<img src="https://sell.proclubs.com/wp-content/themes/diztinct/src/images/proclubs-logo.png" alt="ProClubs" width="350" height="84" style="display: block; border: 0;" />
								</td>
							</tr>
							
							<!-- Centered Cart Image -->
							<tr>
								<td align="center" style="padding: 0 30px 30px 30px;">
									<img src="https://wordpress-1090382-5542248.cloudwaysapps.com/wp-content/uploads/2025/06/Frame-2147223636.png" alt="Cart" style="display: block; border: 0;height: auto;width: 100%;" />
								</td>
							</tr>
							
							<!-- Two Blocks Section -->
							<tr>
								<td style="padding: 0 30px 30px 30px;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<!-- Left Block - 260x150 -->
											<td width="260" height="150" style="vertical-align: top; padding-right: 16px;">
											<div style="background: linear-gradient(81.26deg, #0056A5 22.94%, #006FD6 78.76%); background-color: #0056A5; border-radius: 12px; border: none;text-align: center;text-align: center;width:100%;height:100%;display: table;">
											<div style="margin: 30px 0 18px 0; font-size: 24px !important; font-weight: bold; color: #ffffff;">Order #</div>
																		<div style="margin: 0; font-size: 21px !important; color: #ffffff;">' . $_POST['orderID'] . '</div>
											</div>
																		
												
											</td>
											
											<!-- Right Block - 650x150 -->
											<td width="650" height="150" style="vertical-align: top;">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" height="150" style="background-color: #F2F6FB; border-radius: 8px;">
													<tr>
														<td style="padding: 30px; vertical-align: top;">
														<div style="">
														<div style="margin: 0 0 15px 0; font-size: 24px !important; font-weight: bold; color: #000000; ">Great news!</div>
																		<div style="margin: 0; font-size: 16px !important; line-height: 1.6; color: #000000;">Expect your check in 1-5 business days.</div>
														</div>
															
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
							<!-- Footer -->
							<tr>
								<td align="center" style="padding: 30px 30px 40px 30px; border-top: 1px solid #dee2e6;">
									<p style="margin: 0; font-size: 12px; color: #6c757d; font-family: Red Hat Display, sans-serif;">Copyright 2025 ProClubs.com</p>
								</td>
							</tr>
							
						</table>
					</td>
				</tr>
			</table>
		</body>
		</html>';
		
		$to = $_POST['emailAdd'];
		wp_mail( $to, $subject, $message, $headers);
		die("Email Sent!");
	}
	/* Registers post types. */
	function boj_club_orders_register_post_types() {

		/* Set up the arguments for the 'music_album' post type. */
		$club_order_args = array(
			'public' => true,
			'query_var' => 'club_orders',
			'rewrite' => false,
			'supports' => array(
				'title',
				'thumbnail'
			),
			'labels' => array(
				'name' => 'Orders',
				'singular_name' => 'Order',
				'add_new' => 'Add New Order',
				'add_new_item' => 'Add New Order',
				'edit_item' => 'Edit Order',
				'new_item' => 'New Order',
				'view_item' => 'View Order',
				'search_items' => 'Search Orders',
				'not_found' => 'No Orders Found',
				'not_found_in_trash' => 'No Orders Found In Trash'
			),
		);

		/* Register the music album post type. */
		register_post_type( 'club_orders', $club_order_args );
	}

	function club_orders_edit_columns($columns){
	  $columns = array(
		"cb" => "<input type='checkbox'/>",
		"title" => __('Order ID'),
		"date" => __('Order Date'),
		"email" => __('Customer'),
		"club_order_total" => __("Order Total"),
		"club_order_status" => __("Order Status"),
		"club_order_payment" => __("Payment Choice")
	  );
	 
	  return $columns;
	}
	
	function club_orders_custom_columns($column, $id){
		global $post;
		
		switch ($column) {
			case "club_order_total":
				$custom = get_post_custom();
				echo $custom["club_order_total"][0];
				break;
			case "club_order_status":
				$custom = get_post_custom();
				echo $custom["club_order_status"][0];
				break;
			case "club_order_payment":
				$custom = get_post_custom();
				echo $custom["club_order_payment"][0];
				break;
			case "email":
				the_author_meta("display_name");
				echo "<br>";
				the_author_meta("user_email");
				
		}
	  
	}
	
	function club_order_resend_confirmation_page(){
		global $post;
		$custom = get_post_custom($post->ID);
		$club_order_pay_paypal = $custom["club_order_resend_confirmation_page"][0];
		?>
			<div style="text-align:center;"><p>Resends the confirmation email to the customer that their check has been mailed.</p><input type="button" class="<?php echo $post->ID;?>" id="send-confirmation-email" value="Resend Email"></input></div>
		<?php
	}
	function club_order_check_confirmation(){
		global $post;
		$custom = get_post_custom($post->ID);
		$club_order_pay_paypal = $custom["club_order_check_confirmation"][0];
		?>
			<div style="text-align:center;"><p>Sends an email confirmation to the customer that their check has been mailed.</p><input type="button" id="send-check-confirmation" value="Send Confirmation"></input></div>
		<?php
	}
	
	function club_order_print(){
		
		?>
			<div style="text-align:center;"><p><input type="button" id="print-order-details" value="Print"></input></div>
		<?php
	}
	
	function club_order_pay_paypal(){
		global $post;
		$custom = get_post_custom($post->ID);
		$club_order_pay_paypal = $custom["club_order_pay_paypal"][0];
		?>

			<!-- PayPal Logo --><table border="0" cellpadding="10" cellspacing="0" align="center"><tr><td align="center"></td></tr><tr><td align="center"><a id="pay-with-paypal-button" href="https://www.paypal.com/sendmoney"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" border="0" alt="PayPal Logo"></a></td></tr></table><!-- PayPal Logo -->
		<?php
	}
	
	function club_order_shipping_label(){
		global $post;
		$custom = get_post_custom($post->ID);
		$club_order_shipping_label = $custom["club_order_shipping_label"][0];
		?>
		<div style="vertical-align:center;">
			<p>Shipping Label:</p>
        	<?php 
			if ($club_order_shipping_label != 'NULL'){  
				echo " <img name='club_order_shipping_labels' src='$club_order_shipping_label'></img>";
            }else{
				echo "Customer chose to handle shipping themselves.";		
			}
			?>
		</div>
		<?php
	}
	
	function club_order_comments(){
		global $post;
		$custom = get_post_custom($post->ID);
		$club_order_comments = $custom["club_order_comments"][0];
		?>
		<div style="vertical-align:center;">
			<p>Addtional Comments/Products That Need Quoting:</p>
			<textarea rows="3" name="club_order_comments"><?php echo $club_order_comments; ?></textarea>
		</div>
		<?php
	}
	
	function club_order_paypal_email(){
		global $post;
		$custom = get_post_custom($post->ID);
		$club_order_paypal_email = $custom["club_order_paypal_email"][0];
		?>
			<label>PayPal Email Address:</label>
			<input rows="3" name="club_order_paypal_email"  id="club_order_paypal_email" value="<?php echo $club_order_paypal_email; ?>" />
		<?php
	}
	
	function club_order_address(){
		global $post;
		$custom = get_post_custom($post->ID);
		$club_order_address = $custom["club_order_address"][0];
		?>
			<p>Address for Check</p>
			<textarea rows="3" name="club_order_address"><?php echo $club_order_address; ?></textarea>
		<?php
	}
	
	function club_order_total(){
	
	  global $post;
	  $custom = get_post_custom($post->ID);
	  $club_order_total = $custom["club_order_total"][0];
	  ?>
	  <label>Order Total: $</label>
	  <input name="club_order_total" value="<?php echo $club_order_total; ?>" />
	  <?php
	  
	}
	
	function club_order_status(){
	
		global $post;
		$club_order_status = get_post_meta( $post->ID, 'club_order_status', true );
		$test_array = array(
			'Awaiting Clubs' => 'Awaiting Clubs',
			'Partially Received'   => 'Partially Received',
			'Received' => 'Received',
			'Paid' => 'Paid',
			'On Hold' => 'On Hold',
			'Cancelled' => 'Cancelled'
		);

		echo '<label>Order Status:</label>';
		echo '<select name="club_order_status" >';

		foreach( $test_array as $nick => $name )
		{
			// http://php.net/manual/en/function.printf.php
			printf(
				'<option value="%s" %s> %s</option>',
				$nick,
				selected( $club_order_status, $nick, false ),
				$name
			);
		}

		echo '</select>';

	}
	
	function club_order_payment(){
	
		global $post;
		$club_order_payment = get_post_meta( $post->ID, 'club_order_payment', true );
		$test_array = array(
			'Check' => 'Check',
			'PayPal'   => 'PayPal'
		);

		echo '<label>Payment Choice:</label>';
		echo '<select name="club_order_payment" >';

		foreach( $test_array as $nick => $name )
		{
			// http://php.net/manual/en/function.printf.php
			printf(
				'<option value="%s" %s> %s</option>',
				$nick,
				selected( $club_order_payment, $nick, false ),
				$name
			);
		}

		echo '</select>';
	}
	
	function club_order_clubs(){
		global $post;
		$club_order_clubs = get_post_meta( $post->ID, 'club_order_clubs', true );
		?>
			<div id='customer-info-name' class='<?php echo $post->post_author;?>'<p>Customer: <a href="user-edit.php?user_id=<?php echo $post->post_author;?>&wp_http_referer=%2Fgolfbuyers.com%2Fwp-admin%2Fusers.php"><?php echo get_userdata($post->post_author)->user_login?></a> </p></div>
			<p>Email: <a href="user-edit.php?user_id=<?php echo $post->post_author;?>&wp_http_referer=%2Fgolfbuyers.com%2Fwp-admin%2Fusers.php"><span id='customer-info-email' class='<?php echo $post->post_author;?>'><?php echo get_userdata($post->post_author)->user_email?></span></a> </p>
			<p>Clubs:</p>
			<table style="width:100%; text-align:center;  padding:10px; border-bottom:1px; border-style:solid;" id="er_qm_checkout_table">
						<tr style="border-bottom:1px; border-style:solid; height:25px;">
							<th style="width:50px;">Manufacturer</th>
							<th style="width:50px;">Type</th>
							<th style="width:50px;">Model</th>
							<th style="width:50px;">Shaft</th>
							<th style="width:50px;">Condition</th>
							<th style="width:50px;">Quantity</th>
							<th style="width:50px;">Quote</th>
						</tr>
		<?php
					$order_total = 0;
						foreach($club_order_clubs as $item){
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
								echo "</tr>";
						}
						echo "</table>";
			?>
		<?php
	}

	
	function er_qm_save_details(){
	  global $post;
	  
	  // Bail if we're doing an auto save  
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
			
		// if our current user can't edit this post, bail  
		if( !current_user_can( 'edit_post' ) ) return;    
		
		update_post_meta($post->ID, "club_order_total", $_POST["club_order_total"]);
		update_post_meta($post->ID, "club_order_status", $_POST["club_order_status"]);
		update_post_meta($post->ID, "club_order_payment", $_POST["club_order_payment"]);
		update_post_meta($post->ID, "club_order_address", $_POST["club_order_address"]);
		update_post_meta($post->ID, "club_order_paypal_email", $_POST["club_order_paypal_email"]);
		update_post_meta($post->ID, "club_order_comments", $_POST["club_order_comments"]);
		update_post_meta($post->ID, "club_order_pay_paypal", $_POST["club_order_pay_paypal"]);
		update_post_meta($post->ID, "club_order_shipping_label", $_POST["club_order_shipping_label"]);
		update_post_meta($post->ID, "club_order_check_confirmation", $_POST["club_order_check_confirmation"]);
		update_post_meta($post->ID, "club_order_resend_confirmation_page", $_POST["club_order_resend_confirmation_page"]);
	}


	add_filter('posts_join', 'club_orders_search_join' );
function club_orders_search_join ($join){
    global $pagenow, $wpdb;
    // I want the filter only when performing a search on edit page of Custom Post Type named "club_orders"
    if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='club_orders' && $_GET['s'] != '') {    
        $join .='LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
        $join .='LEFT JOIN '.$wpdb->users. ' ON '. $wpdb->posts . '.post_author = ' . $wpdb->users . '.ID ';
        echo '<script>console.log("'.$join.'");</script>';
    }
    return $join;
}

add_filter( 'posts_where', 'club_orders_search_where' );
function club_orders_search_where( $where ){
    global $pagenow, $wpdb;
    // I want the filter only when performing a search on edit page of Custom Post Type named "club_orders"
    if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='club_orders' && $_GET['s'] != '') {
        $where = preg_replace(
       "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
       "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1) OR (".$wpdb->users.".display_name LIKE $1) OR (".$wpdb->users.".user_email LIKE $1) OR (CONCAT_WS(' ', (SELECT meta_value FROM ".$wpdb->usermeta." WHERE user_id = ".$wpdb->posts.".post_author AND meta_key = 'first_name'), (SELECT meta_value FROM ".$wpdb->usermeta." WHERE user_id = ".$wpdb->posts.".post_author AND meta_key = 'last_name')) LIKE $1)", $where );
    }
    return $where;
}

add_filter( 'posts_groupby', 'club_orders_limits' );
function club_orders_limits($groupby) {
    global $pagenow, $wpdb;
    if ( is_admin() && $pagenow == 'edit.php' && $_GET['post_type']=='club_orders' && $_GET['s'] != '' ) {
        $groupby = "$wpdb->posts.ID";
    }
    return $groupby;
}
	
	
?>