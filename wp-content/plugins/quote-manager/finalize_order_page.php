<?php

		/*
		 *
		 * beginning of embedded custom page template
		 * This creates the Checkout Page
		 *
		*/
			// Set up the EasyPost API
		
			
			get_header();
			global $current_user;
			get_currentuserinfo();
			?>
			 
			
	<div id="primary" class="site-content checkout-page">
		
		<div id="content" role="main">
		
				<?php 
				
				
					$_SESSION['label'] = false;
				

				parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $queryArray);
				
				
				if((boolean)$_SESSION['label']){
				
					if (!is_null($queryArray['logindirect'])) {
					echo "<script>window.location = '/index.php?checkout&label=true&length=".$queryArray['length']."&width=".$queryArray['width']."&height=".$queryArray['height']."&weight=".$queryArray['weight']."'</script>";
					}
					
					if (is_null($queryArray['length'])) {
						
						if (is_null($_SESSION['ship_parameters']['length'])){
							  echo "<script>window.location = '/index.php?quotesummary'</script>";
						}
						/*else {
								echo "<script>window.location = 'http://golfbuyers3.com/index.php?checkout&label=true&length=".$_SESSION['ship_parameters']['length']."&width=".$_SESSION['ship_parameters']['width']."&height=".$_SESSION['ship_parameters']['height']."&weight=".$_SESSION['ship_parameters']['weight']."'</script>";
						}*/
	
					}
				}else{
					
					if (!is_null($queryArray['logindirect'])) {
					echo "<script>window.location = '/index.php?checkout&label=false'</script>";
					}
					
				}//end if label

				?>
				<?php if (isset($_GET['vendorwindow'])) { ?>

					<h2> What's your contact info? </h2>

					<div id="" style="width: 350px; margin-bottom: 50px;">
						<div style="margin-left: 10px; margin-top: 10px;">
						<p style="font-size: 10px;">If you have an account on file, please enter that email address below.</p>
						<label for="er_qm_account_email_address">
							<span style="font-size: 10px; font-weight: bold; padding-left: 2px; display: block;">EMAIL ADDRESS</span>
							<input type="text" id="er_qm_account_email_address"  style="display: block;">
						</label>
						</div>
					</div>


				<?php } ?>
				<div class="checkout-container">
					<div class="checkout-content">
						<div class="payment-form-container">
							<h2>How would you like to receive your payment?</h2>
							
							<div class="payment-options">
								<div class="payment-option">
									<input type="radio" name="er_qm_payment_type" value="check" id="er_qm_payment_check" checked>
									<label class="radio-label" for="er_qm_payment_check">
										<span>Check</span>
									</label>
								</div>
								<div class="payment-option">
									<input type="radio" name="er_qm_payment_type" value="paypal" id="er_qm_payment_paypal">
									<label class="radio-label" for="er_qm_payment_paypal">
										<span>PayPal</span>
									</label>
								</div>
							</div>

							<div id="check-payment-form" class="payment-details-form">
								<div class="form-row">
									<label>
										<span class="field-label">NAME ON CHECK</span>
										<input type="text" id="er_qm_name_check" value="<?php 
    $full_name = trim(get_user_meta($current_user->ID, 'first_name', true)." ".get_user_meta($current_user->ID, 'last_name', true));
    echo !empty($full_name) ? $full_name : '';
?>" placeholder="John Smith">
									</label>
								</div>

								<div class="form-row two-columns">
									<label>
										<span class="field-label">STREET ADDRESS</span>
										<input type="text" id="er_qm_street_address" value="<?php echo get_user_meta($current_user->ID, 'street_address', true);?>" placeholder="4827 Maplewood Drive">
									</label>
									<label>
										<span class="field-label">CITY</span>
										<input type="text" id="er_qm_city_address" value="<?php echo get_user_meta($current_user->ID, 'city_address', true);?>" placeholder="Springfield">
									</label>
								</div>

								<div class="form-row two-columns">
									
									<label>
										<span class="field-label">STATE</span>
										<input type="text" id="er_qm_state_address" value="<?php echo get_user_meta($current_user->ID, 'state_address', true);?>" placeholder="IL, Illinois">
									</label>
									<label>
										<span class="field-label">ZIP CODE</span>
										<input type="text" id="er_qm_zip_address" value="<?php echo get_user_meta($current_user->ID, 'zip_code_address', true);?>" placeholder="62704">
									</label>
								</div>

							</div>

							<div id="paypal-payment-form" class="payment-details-form" style="display: none;">
								<div class="form-row">
									<label>
										<span class="field-label">PAYPAL EMAIL ADDRESS</span>
										<input type="email" id="er_qm_paypal_email" value="<?php echo get_user_meta($current_user->ID, 'paypal_email', true);?>" placeholder="youremail@gmail.com">
									</label>
								</div>
							</div>

							<div class="form-submit">
								<button type="submit" id="er_qm_submit_quote" class="submit-button"><span>Submit Quote</span></button>
							</div>
						</div>
					</div>
					<div class="checkout-image">
						<img src="/wp-content/uploads/2025/06/cart-image.png" alt="Golf Clubs Display">
					</div>
				</div>

				<?php quote_manager_get_faq_section(); ?>

				</div>
				</div>
				</div>

			<?php
			get_footer();

			
				
			exit;
?>