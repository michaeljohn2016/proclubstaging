<?php
	  	/*
		 *
		 * beginning of embedded custom page template
		 * This creates the Quote Summary Page
		 *
		*/
		
			get_header();?>
			<?php

session_start();
// ...existing code...
if (isset($_POST['clear_cart'])) {
    $_SESSION['er_qm_cart'] = [];
    $_SESSION['er_qm_order_total'] = 0;
    $_SESSION['er_qm_comments'] = '';
    // Можно добавить редирект, чтобы избежать повторной отправки формы:
    echo "<script>window.location.href=window.location.href;</script>";
    exit;
}
// ...existing code...


// ..Show and hide shaft and iron if 0
$show_shaft = false;
$show_iron = false;
if (!empty($_SESSION['er_qm_cart'])) {
    foreach ($_SESSION['er_qm_cart'] as $item) {
        if (!empty($item['shaft']) && $item['shaft'] > 0) {
            $show_shaft = true;
        }
        if (!empty($item['ironQuantity']) && $item['ironQuantity'] > 0) {
            $show_iron = true;
        }
    }
}
// ..Show and hide shaft and iron if 0

?>
	<div>
		<div></div>
		<div id="page-container">
			<div id="container-grad"></div>
			
			<div id="cart-banner">
				<div class="cart-banner-wrapper">
					<h2>Your Cart</h2>
				</div>
			</div>
	<div id="primary" class="site-content shoppingcart">
		
		<div id="content" role="main">
			<div class="cart-heading-wrap">
				<a href="/" class="acart-heading-return">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<path d="M10.6667 5L4 12M4 12L10.6667 19M4 12L20 12" stroke="#111112" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<span>Return to Selection</span>
				</a>
				<h1>Quote Summary</h1>	
				<?php if (!empty($_SESSION['er_qm_cart'])): ?>
						<form method="post" style="display:inline;">
							<button type="submit" name="clear_cart" class="btn-modern btn-modern btn-primary">Clear All</button>
						</form>
					<?php endif; ?>		
			</div>
			<div id="quotesummary">
				<?php if(isset($_GET["heldquote"])) { 
					
					global $wpdb;
		global $current_user;
		if(!empty($_SESSION['er_qm_cart'])){
			get_currentuserinfo();
			$title = rand(10000, 99999); // update after going live
			$post_info = array(
				'post_title' => $title,
				'post_status' => 'publish',
				'post_author' => $current_user->ID,
				'post_type' => 'club_orders'
			);
			$payment_type = "Check"; // as default
			$new_post_id = wp_insert_post($post_info, true);
			update_post_meta($new_post_id, 'club_order_total', $_SESSION['er_qm_order_total']);
			update_post_meta($new_post_id, 'club_order_status', 'On Hold');
			update_post_meta($new_post_id, 'club_order_payment', $payment_type);
			update_post_meta($new_post_id, 'club_order_clubs', $_SESSION['er_qm_cart']);
			update_post_meta($new_post_id, 'club_order_comments', $_SESSION['er_qm_comments']);
			$_SESSION['er_qm_cart'] = "";
			$_SESSION['er_qm_comments'] = "";
		}
				?>
				<div >Your quote has been held! Visit the <strong>My Account</strong> link at the top of the page to continue your quote.</div>
				<?php } ?>	
				
                
                    <?php
					if(!empty($_SESSION['er_qm_cart'])){
						$order_total = 0;
						?>
						<div class="quote-table-container">
							<!-- Table Header -->
							<div class="quote-table-header">
								<div class="header-image"></div>
								<div class="header-content header-quote-summary">
									<div class="header-col header-make">Make</div>
									<div class="header-col header-type">Type</div>
									<div class="header-col header-model">Model</div>
									<?php if ($show_shaft): ?>
										<div class="header-col header-shaft">Shaft</div>
									<?php endif; ?>
    								<?php if ($show_iron): ?>
										<div class="header-col header-iron-qty">Iron Set<br>Qty</div>
									<?php endif; ?>
									<div class="header-col header-condition">Condition</div>
									<div class="header-col header-qty">Qty</div>
									<div class="header-col header-unit-price">Unit Price</div>
									<div class="header-col header-total">Total</div>
									<div class="header-col header-actions"></div>
								</div>
							</div>

							
							<!-- Table Rows -->
							<div class="quote-table-body">
								<?php
								$i = 0;
								foreach($_SESSION['er_qm_cart'] as $item){
									$queried_post = get_post_meta($item['id']);
									$priced = $item['price'];
									$string = trim($item['condition']);

									$unique_id = $item["id"] . '_' . md5($item["condition"] . '_' . $item["headOnly"] . '_' . $item["shaft"]) ;

									// Get club image using the same logic as PDP
									global $wpdb;
									$order_postid = $item['id'];
									
									$query_data_for_image_id = $wpdb->get_results(
										"SELECT meta_value AS image 
										 FROM wp_postmeta 
										 WHERE meta_key='_thumbnail_id' 
										 AND post_id=$order_postid;"
									);
									
									if (!empty($query_data_for_image_id) && !empty($query_data_for_image_id[0]->image)) {
										$imageID = intval($query_data_for_image_id[0]->image);
										
										$query_data_for_image = $wpdb->get_results(
											"SELECT guid AS URL 
											 FROM wp_posts 
											 WHERE ID = $imageID;"
										);
										
										if (!empty($query_data_for_image) && !empty($query_data_for_image[0]->URL)) {
											$club_image = $query_data_for_image[0]->URL;
										} else {
											$club_image = '/wp-content/plugins/quote-manager/default-club-image.php'; // fallback
										}
									} else {
										$club_image = '/wp-content/plugins/quote-manager/default-club-image.php'; // fallback
									}
									?>
									<div class="quote-table-row" id="row-<?php echo $unique_id; ?>">
										<input type="hidden" value="<?php echo $item["condition"]; ?>" class="er_qm_condition" data-id="<?php echo $iunique_id; ?>">
										<?php if ($item["headOnly"] == "true") { ?>
											<input type="hidden" value="Head Only" class="er_qm_shaft" data-id="<?php echo $unique_id; ?>">
										<?php } else { ?>
											<?php if ($item['premiumShaft'] !== "false" && $item['premiumShaft'] !== "none") { $item['shaft'] = pc_get_shaft_friendly_name($item['premiumShaft']);} ?>
											<input type="hidden" value="<?php echo preg_replace("/[^A-Za-z0-9 ]/", '', $item["shaft"]);; ?>" class="er_qm_shaft" data-id="<?php echo $unique_id; ?>">
										<?php } ?>
										<input type="hidden" value="<?php echo $item["price"]; ?>" class="er_qm_price" data-id="<?php echo $priced; ?>">
										
										<!-- Desktop Product Image (hidden on mobile) -->
										<div class="row-image desktop-only">
											<img src="<?php echo $club_image; ?>" alt="<?php echo get_post_meta($item['id'], 'club_manufacturer', true); ?> <?php echo get_post_meta($item['id'], 'club_model', true); ?>" />
										</div>
										
										<!-- Mobile Top Row -->
										<div class="mobile-top-row">
											<div class="mobile-product-info">
												<!-- Product Image -->
												<div class="row-image">
													<img src="<?php echo $club_image; ?>" alt="<?php echo get_post_meta($item['id'], 'club_manufacturer', true); ?> <?php echo get_post_meta($item['id'], 'club_model', true); ?>" />
												</div>
												<!-- Product Name -->
												<div class="row-col row-make"><?php echo get_post_meta($item['id'], 'club_manufacturer', true); ?></div>
											</div>
											<div class="mobile-controls">
												<!-- Quantity Controls -->
												<div class="row-col row-qty">
													<div class="quantity-control">
														<button type="button" class="qty-btn qty-minus" data-id="<?php echo $unique_id; ?>">-</button>
														<input type="text" class="er_qm_quantity" size="3" id="<?php echo $unique_id; ?>" value="<?php echo $item['quantity']; ?>" readonly>
														<button type="button" class="qty-btn qty-plus" data-id="<?php echo $unique_id; ?>">+</button>
													</div>
												</div>
												<!-- Remove Button -->
												<div class="row-col row-actions">
																									<button type="button" class="remove-btn delete_cart_item" id="<?php echo $item['id']; ?>">
													<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" clip-rule="evenodd" d="M3.9375 3.6875V4.3125H1.4375C0.919733 4.3125 0.5 4.73223 0.5 5.25C0.5 5.76777 0.919733 6.1875 1.4375 6.1875H2.39236L2.8688 14.7637C2.9424 16.0885 4.03812 17.125 5.36495 17.125H10.635C11.9619 17.125 13.0576 16.0885 13.1312 14.7637L13.6076 6.1875H14.5625C15.0803 6.1875 15.5 5.76777 15.5 5.25C15.5 4.73223 15.0803 4.3125 14.5625 4.3125H12.0625V3.6875C12.0625 2.1342 10.8033 0.875 9.25 0.875H6.75C5.1967 0.875 3.9375 2.1342 3.9375 3.6875ZM6.75 2.75C6.23223 2.75 5.8125 3.16973 5.8125 3.6875V4.3125H10.1875V3.6875C10.1875 3.16973 9.76777 2.75 9.25 2.75H6.75ZM7.0625 8.6875C7.0625 8.16973 6.64277 7.75 6.125 7.75C5.60723 7.75 5.1875 8.16973 5.1875 8.6875V14.3125C5.1875 14.8303 5.60723 15.25 6.125 15.25C6.64277 15.25 7.0625 14.8303 7.0625 14.3125V8.6875ZM10.8125 8.6875C10.8125 8.16973 10.3928 7.75 9.875 7.75C9.35723 7.75 8.9375 8.16973 8.9375 8.6875V14.3125C8.9375 14.8303 9.35723 15.25 9.875 15.25C10.3928 15.25 10.8125 14.8303 10.8125 14.3125V8.6875Z" fill="#AFB3B6"/>
													</svg>
												</button>
												</div>
											</div>
										</div>

										<!-- Mobile Details Section -->
										<div class="mobile-details">
											<div class="mobile-detail-row">
												<span class="mobile-detail-label">Type:</span>
												<span class="mobile-detail-value"><?php echo $queried_post['club_type_field'][0]; ?></span>
											</div>
											<div class="mobile-detail-row">
												<span class="mobile-detail-label">Model:</span>
												<span class="mobile-detail-value"><?php echo get_post_meta($item['id'], 'club_model', true); ?></span>
											</div>
											<?php if (isset($item["shaft"]) && $item["shaft"] > 0) { ?>
											<div class="mobile-detail-row">
												<span class="mobile-detail-label">Shaft:</span>
												<span class="mobile-detail-value"><?php echo $item['headOnly'] == "true" ? "Head Only" : $item['shaft']; ?></span>
											</div>
											<?php } ?>
											<?php if (isset($item["ironQuantity"]) && $item["ironQuantity"] > 0) { ?>
											<div class="mobile-detail-row">
												<span class="mobile-detail-label">Iron Set:</span>
												<span class="mobile-detail-value"><?php echo $item["ironQuantity"]; ?></span>
											</div>
											<?php } ?>
											<div class="mobile-detail-row">
												<span class="mobile-detail-label">Condition:</span>
												<span class="mobile-detail-value"><?php echo $item['condition']; ?></span>
											</div>
											<div class="mobile-detail-row">
												<span class="mobile-detail-label">Unit Price:</span>
												<span class="mobile-detail-value">$<?php echo number_format((str_replace( ',', '', $priced )), 2); ?></span>
											</div>
											<div class="mobile-detail-row">
												<span class="mobile-detail-label">Total:</span>
												<span class="mobile-detail-value">$<?php echo number_format((str_replace( ',', '', $priced ) * $item["quantity"]), 2); ?></span>
											</div>
										</div>
										
										<!-- Desktop Product Content (hidden on mobile) -->
										<div class="row-content desktop-only row-quote-summary">
											<div class="row-col row-make"><?php echo get_post_meta($item['id'], 'club_manufacturer', true); ?></div>
											<div class="row-col row-type"><?php echo $queried_post['club_type_field'][0]; ?></div>
											<div class="row-col row-model"><?php echo get_post_meta($item['id'], 'club_model', true); ?></div>

											<?php if ($show_shaft): ?>
												<div class="row-col row-shaft"><?php echo $item['headOnly'] == "true" ? "Head Only" : $item['shaft']; ?></div>
											<?php endif; ?>
  											<?php if ($show_iron): ?>
												<div class="row-col row-iron-qty"><?php echo (isset($item["ironQuantity"]) && $item["ironQuantity"] > 0) ? $item["ironQuantity"] : "-"; ?></div>
											<?php endif; ?>

											<div class="row-col row-condition"><?php echo $item['condition']; ?></div>
											<div class="row-col row-qty">
												<div class="quantity-control">
													<button type="button" class="qty-btn qty-minus" data-id="<?php echo $unique_id; ?>">-</button>
													<input type="text" class="er_qm_quantity" size="3" id="<?php echo $unique_id; ?>" value="<?php echo $item['quantity']; ?>" readonly>
													<button type="button" class="qty-btn qty-plus" data-id="<?php echo $unique_id; ?>">+</button>
												</div>
											</div>
											<div class="row-col row-unit-price">$<?php echo number_format((str_replace( ',', '', $priced )), 2); ?></div>
											<div class="row-col row-total">$<?php echo number_format((str_replace( ',', '', $priced ) * $item["quantity"]), 2); ?></div>
											<div class="row-col row-actions">
												
												<button type="button" class="remove-btn delete_cart_item" id="<?php echo $item['id']; ?>">
													<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" clip-rule="evenodd" d="M3.9375 3.6875V4.3125H1.4375C0.919733 4.3125 0.5 4.73223 0.5 5.25C0.5 5.76777 0.919733 6.1875 1.4375 6.1875H2.39236L2.8688 14.7637C2.9424 16.0885 4.03812 17.125 5.36495 17.125H10.635C11.9619 17.125 13.0576 16.0885 13.1312 14.7637L13.6076 6.1875H14.5625C15.0803 6.1875 15.5 5.76777 15.5 5.25C15.5 4.73223 15.0803 4.3125 14.5625 4.3125H12.0625V3.6875C12.0625 2.1342 10.8033 0.875 9.25 0.875H6.75C5.1967 0.875 3.9375 2.1342 3.9375 3.6875ZM6.75 2.75C6.23223 2.75 5.8125 3.16973 5.8125 3.6875V4.3125H10.1875V3.6875C10.1875 3.16973 9.76777 2.75 9.25 2.75H6.75ZM7.0625 8.6875C7.0625 8.16973 6.64277 7.75 6.125 7.75C5.60723 7.75 5.1875 8.16973 5.1875 8.6875V14.3125C5.1875 14.8303 5.60723 15.25 6.125 15.25C6.64277 15.25 7.0625 14.8303 7.0625 14.3125V8.6875ZM10.8125 8.6875C10.8125 8.16973 10.3928 7.75 9.875 7.75C9.35723 7.75 8.9375 8.16973 8.9375 8.6875V14.3125C8.9375 14.8303 9.35723 15.25 9.875 15.25C10.3928 15.25 10.8125 14.8303 10.8125 14.3125V8.6875Z" fill="#AFB3B6"/>
													</svg>
												</button>
											</div>
										</div>
									</div>
									<?php
									$order_total = $order_total+(str_replace( ',', '', $priced )*$item['quantity']);
									$_SESSION['er_qm_order_total'] = $order_total;
									$i++;
								}
								?>
							</div>
						</div>
						<?php
				//echo "<span id='anotherclub' style='text-decoration: underline;'><strong>Add another club</strong></span><br /><br />";
				echo "<div id='newclub_hid' style='display:none;'>";


	
				$id=5491; 
				$post = get_page($id); 
				$content = apply_filters('the_content', $post->post_content); 
				echo $content;  

						echo "</div>";
				?>
				
				<div class="modern-total-container">
					<div class="modern-total-wrapper">
						<span class="total-label-modern">Total:</span>
						<span class="total-amount-modern" id="quote-summary-table-order-total-amount">$<?php echo number_format($order_total,2); ?></span>
					</div>
				</div>
				<div style="clear: both;"></div>
				
				<!-- New Disclaimer and Comments Section -->
				<div class="disclaimer-comments-container">
					<!-- Comments Section -->
					<div class="comments-section">
						<div class="comments-input-container">
							<textarea id="er_qm_checkout_comments" name="checkout_comments" rows="3" placeholder="Your comment here"><?php echo $_SESSION['er_qm_comments'];?></textarea>
						</div>
					</div>
					
					<!-- Disclaimer Section -->
					<div class="disclaimer-section">
						<div class="disclaimer-header">
							<h3>Disclaimer</h3>
							<p>No credit will be given for golf clubs if they have any of the following features:</p>
						</div>
						
						<div class="disclaimer-content">
							<div class="disclaimer-item">
								<div class="check-icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										<circle cx="10" cy="10" r="8.33333" fill="#026BC6" stroke="#026BC6" stroke-width="1.5"/>
										<path d="M6.66667 10L8.88889 12.2222L13.3333 7.77778" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<p>Clubs that are not received in the condition category submitted will be notified before a Check/Paypal is sent. Please view our condition scale to better clarify the condition category of your item(s). Shop wear will not qualify for new pricing. Our new price category is 100% brand new without a single mark on the club head(s).</p>
							</div>
							
							<div class="disclaimer-item">
								<div class="check-icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										<circle cx="10" cy="10" r="8.33333" fill="#026BC6" stroke="#026BC6" stroke-width="1.5"/>
										<path d="M6.66667 10L8.88889 12.2222L13.3333 7.77778" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<p>Wedges with browning on the club face will not be accepted.</p>
							</div>
							
							<div class="disclaimer-item">
								<div class="check-icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										<circle cx="10" cy="10" r="8.33333" fill="#026BC6" stroke="#026BC6" stroke-width="1.5"/>
										<path d="M6.66667 10L8.88889 12.2222L13.3333 7.77778" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<p>Iron Sets that do not have at least 5 consecutive clubs. Iron sets must also have a Pitching Wedge in them and all shafts must match (no non-matching reshafted clubs qualify)</p>
							</div>
							
							<div class="disclaimer-item">
								<div class="check-icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
										<circle cx="10" cy="10" r="8.33333" fill="#026BC6" stroke="#026BC6" stroke-width="1.5"/>
										<path d="M6.66667 10L8.88889 12.2222L13.3333 7.77778" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<p>Clubs with dents, cracks, broken shafts, poor re-shaft work, visible shaft wear, counterfeits or any other features that would directly affect the product's resale value.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		
			
			<div id="er_qm_checkout_bottom_buttons">
				<!-- Wrapper that matches disclaimer-comments layout -->
				<div class="checkout-bottom-wrapper">
					<!-- Spacer to align with comments section -->
					<div class="checkout-bottom-spacer"></div>
					
					<!-- Buttons container aligned with disclaimer section -->
					<div class="checkout-bottom-buttons">
						<!-- Modern button section layout -->
						<div class="checkout-actions-container">
							<!-- Checkbox section -->
							<div class="left-section">
								<div class="checkbox-container">
									<label class="modern-checkbox-label">
										<input type="checkbox" id="er_qm_terms_agreement" name="agree_to_terms" class="modern-checkbox">
										<span class="checkbox-custom"></span>
										<span class="checkbox-text">I agree to the disclaimer above.</span>
									</label>
								</div>
							</div>
							
							<!-- Buttons and Print Quote section -->
							<div class="buttons-section">
								<div class="buttons-row">
									<?php if (!$_GET["vendorwindow"]) {  ?>
										<button type="button" id="er_qm_checkout_hold" class="btn-modern btn-outline">Hold My Quote</button>
									<?php } ?>
									<button type="button" id="er_qm_checkout_finish" class="btn-modern btn-primary">Proceed to Checkout</button>
								</div>
								
								<?php if ( current_user_can('manage_options') ) { ?>
    <div class="print-quote-container">
        <a href="/quote-email-template.php" target="_blank" class="print-quote-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
											<path d="M.075 5.333A3 3 0 0 1 3 3h18a3 3 0 0 1 2.925 2.333L12 12.621zM0 7.046v10.656l8.704-5.337zM10.142 13.245l-9.855 6.04A3 3 0 0 0 3 21h18a3 3 0 0 0 2.712-1.716l-9.855-6.04L12 14.379zm5.154-.879L24 17.701V7.046z"/>
										</svg>
            <span>Email Current Quote</span>
        </a>
        <a href="/print-ShoppingCart.php" target="_blank" class="print-quote-link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M18.375 22.5938C18.375 23.3704 17.7454 24 16.9688 24H6.9375C6.16083 24 5.53125 23.3704 5.53125 22.5938V14.9531H18.375V22.5938ZM10.0781 19.5469C9.68981 19.5469 9.375 19.8617 9.375 20.25C9.375 20.6383 9.68981 20.9531 10.0781 20.9531H13.8281C14.2164 20.9531 14.5312 20.6383 14.5312 20.25C14.5312 19.8617 14.2164 19.5469 13.8281 19.5469H10.0781ZM20.4375 6.04688C22.376 6.04688 23.9531 7.62398 23.9531 9.5625V15.1875C23.9531 17.126 22.376 18.7031 20.4375 18.7031H19.7812V14.9531H20.2031C20.5914 14.9531 20.9062 14.6383 20.9062 14.25C20.9062 13.8617 20.5914 13.5469 20.2031 13.5469H3.70312C3.31481 13.5469 3 13.8617 3 14.25C3 14.6383 3.31481 14.9531 3.70312 14.9531H4.125V18.7031H3.51562C1.57711 18.7031 0 17.126 0 15.1875V9.5625C0 7.62398 1.57711 6.04688 3.51562 6.04688H20.4375ZM10.0781 16.5469C9.68981 16.5469 9.375 16.8617 9.375 17.25C9.375 17.6383 9.68981 17.9531 10.0781 17.9531H13.8281C14.2164 17.9531 14.5312 17.6383 14.5312 17.25C14.5312 16.8617 14.2164 16.5469 13.8281 16.5469H10.0781ZM3.70312 9.04688C3.31481 9.04688 3 9.36169 3 9.75C3 10.1383 3.31481 10.4531 3.70312 10.4531H5.95312C6.34144 10.4531 6.65625 10.1383 6.65625 9.75C6.65625 9.36169 6.34144 9.04688 5.95312 9.04688H3.70312ZM16.2656 0C18.2041 0 19.7812 1.57711 19.7812 3.51562V4.64062H4.125V3.51562C4.125 1.57711 5.70211 0 7.64062 0H16.2656Z" fill="black"/>
										</svg>
            <span>Print Current Quote</span>
        </a>
    </div>
<?php } elseif ( current_user_can('read') ) { ?>
    <div class="print-quote-container">
        <a href="/print-ShoppingCart.php" target="_blank" class="print-quote-link">
           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M18.375 22.5938C18.375 23.3704 17.7454 24 16.9688 24H6.9375C6.16083 24 5.53125 23.3704 5.53125 22.5938V14.9531H18.375V22.5938ZM10.0781 19.5469C9.68981 19.5469 9.375 19.8617 9.375 20.25C9.375 20.6383 9.68981 20.9531 10.0781 20.9531H13.8281C14.2164 20.9531 14.5312 20.6383 14.5312 20.25C14.5312 19.8617 14.2164 19.5469 13.8281 19.5469H10.0781ZM20.4375 6.04688C22.376 6.04688 23.9531 7.62398 23.9531 9.5625V15.1875C23.9531 17.126 22.376 18.7031 20.4375 18.7031H19.7812V14.9531H20.2031C20.5914 14.9531 20.9062 14.6383 20.9062 14.25C20.9062 13.8617 20.5914 13.5469 20.2031 13.5469H3.70312C3.31481 13.5469 3 13.8617 3 14.25C3 14.6383 3.31481 14.9531 3.70312 14.9531H4.125V18.7031H3.51562C1.57711 18.7031 0 17.126 0 15.1875V9.5625C0 7.62398 1.57711 6.04688 3.51562 6.04688H20.4375ZM10.0781 16.5469C9.68981 16.5469 9.375 16.8617 9.375 17.25C9.375 17.6383 9.68981 17.9531 10.0781 17.9531H13.8281C14.2164 17.9531 14.5312 17.6383 14.5312 17.25C14.5312 16.8617 14.2164 16.5469 13.8281 16.5469H10.0781ZM3.70312 9.04688C3.31481 9.04688 3 9.36169 3 9.75C3 10.1383 3.31481 10.4531 3.70312 10.4531H5.95312C6.34144 10.4531 6.65625 10.1383 6.65625 9.75C6.65625 9.36169 6.34144 9.04688 5.95312 9.04688H3.70312ZM16.2656 0C18.2041 0 19.7812 1.57711 19.7812 3.51562V4.64062H4.125V3.51562C4.125 1.57711 5.70211 0 7.64062 0H16.2656Z" fill="black"/>
										</svg>
            <span>Print Current Quote</span>
        </a>
    </div>
<?php } ?>
								
							</div>
						</div>
					</div>
				</div>
			</div>
				<?php

			} else {

				echo "<br><br><p id='quote-summary-table-no-items'>No items have been added to your cart.</p><br>";
			}
			?>
			
			
			
		</div>
	</div>
</div>
</div>

<?php

get_footer();
exit;
?>