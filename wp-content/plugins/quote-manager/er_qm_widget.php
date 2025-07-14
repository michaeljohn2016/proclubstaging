<?php
	/*
	Plugin Name: Quote Manager Widget
	Description: Displays the Quote Manager Widget
	Version: 0.2
	Author: Lumen Foundry
	*/
	
	add_action('widgets_init', 'er_qm_register_widget');
	add_action('wp_footer', 'er_qm_ajax_handler' );
	add_action('wp_ajax_repopulate_widget_club_type', 'repopulate_widget_club_type');
	add_action('wp_ajax_repopulate_widget_club_model', 'repopulate_widget_club_model');
	//add_action('wp_ajax_repopulate_widget_club_condition', 'repopulate_widget_club_condition');
	add_action('wp_ajax_add_clubs_to_cart', 'add_clubs_to_cart');
	add_action('wp_ajax_clear_all_from_cart', 'clear_all_from_cart');
	add_action('wp_ajax_update_checkout_cart', 'update_checkout_cart');
	add_action('wp_ajax_save_shipment_parameters', 'save_shipment_parameters');
	add_action('wp_ajax_delete_cart_item', 'delete_cart_item');
	add_action('wp_ajax_place_order_on_hold', 'place_order_on_hold');
	add_action('wp_ajax_delete_held_quote', 'delete_held_quote');
	add_action('wp_ajax_continue_held_order', 'continue_held_order');
	add_action('wp_ajax_nopriv_repopulate_widget_club_type', 'repopulate_widget_club_type');
	add_action('wp_ajax_nopriv_repopulate_widget_club_model', 'repopulate_widget_club_model');
	//add_action('wp_ajax_nopriv_repopulate_widget_club_condition', 'repopulate_widget_club_condition');
	add_action('wp_ajax_nopriv_add_clubs_to_cart', 'add_clubs_to_cart');
	add_action('wp_ajax_nopriv_clear_all_from_cart', 'clear_all_from_cart');
	add_action('wp_ajax_nopriv_update_checkout_cart', 'update_checkout_cart');
	add_action('wp_ajax_nopriv_save_shipment_parameters', 'save_shipment_parameters');
	add_action('wp_ajax_nopriv_delete_cart_item', 'delete_cart_item');
	add_action('wp_ajax_nopriv_place_order_on_hold', 'place_order_on_hold');
	add_action('wp_ajax_nopriv_continue_held_order', 'continue_held_order');
	add_action( 'after_setup_theme', 'set_up_thumbnails' ); 
	add_action('init', 'er_qm_init_sessions');
	add_shortcode('quote-manager-widget', 'er_qm_shortcode_handler');

	require 'proclubs-functions.php';
	
	
	function er_qm_styling(){
		wp_register_style( 'er_qm_custom_styling_file', plugins_url( 'quote-manager-style.css', __FILE__ ) );  
		//wp_enqueue_style( 'css', get_stylesheet_uri() );
		wp_enqueue_style( 'er_qm_custom_styling_file');
	}
	
	function er_qm_shortcode_handler(){
		include(__FILE__);
	}
	
	function er_qm_init_sessions(){
		if(!session_id()){
			session_start();
			
			if(empty($_SESSION['er_qm_cart'])){
				$_SESSION['er_qm_cart'] = array();
			}
			if (!is_array($_SESSION['er_qm_cart'])) {
				$_SESSION['er_qm_cart'] = array();
			}
			if(empty($_SESSION['temp'])){
				$_SESSION['temp'] = "";
			}
			if(empty($_SESSION['tempPrice'])){
				$_SESSION['tempPrice'] = 0;
			}
			if(empty($_SESSION['tempCondition'])){
				$_SESSION['tempCondition'] = "";
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
		
		if(is_user_logged_in() && $_SERVER["REQUEST_METHOD"] == "GET") {
			global $wpdb;
				$querydata = $wpdb->get_results(
				"
					SELECT * FROM wp_posts WHERE post_type='club_orders' AND post_author='".get_current_user_id()."' ORDER BY post_date DESC
				"
			);
			
				foreach($querydata as $order) {
					$a = $wpdb->get_results(
						"
							SELECT * FROM wp_postmeta WHERE post_id='".$order->ID."'
						"
					);
					$aa = array();
					foreach($a as $k) {
						$key = $k->meta_key;
						$value = $k->meta_value;
						$aa[$key] = $value;
					}
					$showFooterBtn = false;
					if($aa["club_order_status"] == 'On Hold') {
						$showFooterBtn = true;
					}
			}
			if($showFooterBtn) {
					?>
					<a href="/?accounthistory" style="position: fixed; z-index: 909999999999; text-decoration: none; font-weight: bold; vertical-align: middle; padding-top: 10px; bottom: 0; right: 100px; height: 25px; width: 200px; font-size: 14px; background-color: #3c3c3c; border-top-left-radius: 4px; border-top-right-radius: 4px; border: 2px solid #fff; text-align: center; border-bottom: 0; color: #fff;">View Held Quote</a>
					<?php
				}
		}
		add_action( 'wp_enqueue_scripts', 'er_qm_styling' );
	}
	
	function set_up_thumbnails(){
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 200, 200, true ); // Normal post thumbnails
		add_image_size( 'single-post-thumbnail', 400, 9999 ); // Permalink thumbnail size
	}
	
	// Set up the rewrite rules for the order page
	//---------------------------------------------------------------------------------------------------------------------------------------------
	add_action( 'init', 'action_init_redirect' );
	function action_init_redirect() {
	  add_rewrite_rule( 'orders/?', 'index.php?orders=new', 'top' );
	  add_rewrite_rule( 'quotesummary/?', 'index.php?quotesummary=new', 'top' );
	  add_rewrite_rule( 'checkout/?', 'index.php?checkout=new', 'top' );
	  add_rewrite_rule( 'orderconfirmation/?', 'index.php?orderconfirmation=new', 'top' );
	  add_rewrite_rule( 'findheldquote/?', 'index.php?findheldquote=new', 'top' );
	  add_rewrite_rule( 'mobileshipping/?', 'index.php?mobileshipping=new', 'top' );
	}
	
	add_filter( 'query_vars', 'filter_query_vars' );
	function filter_query_vars( $query_vars ) {
	  $query_vars[] = 'orders';
	  $query_vars[] = 'quotesummary';
	  $query_vars[] = 'checkout';
	  $query_vars[] = 'orderconfirmation';
	  $query_vars[] = 'accounthistory';
	  $query_vars[] = 'findheldquote';
	  $query_vars[] = 'mobileshipping';
	  return $query_vars;
	}
	
	add_action( 'parse_request', 'action_parse_request');
	function action_parse_request( &$wp ) {
	  if ( array_key_exists( 'orders', $wp->query_vars ) ) {
	
			// Imports the Club Review Page
			include "club_review_page.php";
	
		} else if ( array_key_exists( 'quotesummary', $wp->query_vars ) ) {
	  
			// Imports the Shopping Cart Page
			include "shopping_cart_page.php";
		
		}
		else if ( array_key_exists( 'accounthistory', $wp->query_vars ) ) {
	  
			// Imports the Shopping Cart Page
			include "account-history.php";
		
		} else if ( array_key_exists( 'checkout', $wp->query_vars ) ) {
			
			// Imports the Finalize Order Page
			include "finalize_order_page.php";
			
		} else if ( array_key_exists( 'orderconfirmation', $wp->query_vars ) ) {
	
			// Imports the Order Confirmation Page
			include "order_confirmation_page.php";
	
		} else if ( array_key_exists( 'findheldquote', $wp->query_vars ) ) {
	
			// Imports the Order Confirmation Page
			include "find_held_quote_page.php";
	
		}  else if( array_key_exists( 'mobileshipping', $wp->query_vars ) ) {
	
			// Imports the Order Confirmation Page
			include "mobile-shipping-page.php";
	
		}
	}
	
	//---------------------------------------------------------------------------------------------------------------------------------------------

	add_action( 'init', 'wpse9870_init_internal' );
	function wpse9870_init_internal()
	{
		add_rewrite_rule( 'er_qm_order_page.php$', 'index.php?wpse9870_api=1', 'top' );
	}

	add_filter( 'query_vars', 'wpse9870_query_vars' );
	function wpse9870_query_vars( $query_vars )
	{
		$query_vars[] = 'wpse9870_api';
		return $query_vars;
	}

	add_action( 'parse_request', 'wpse9870_parse_request' );
	function wpse9870_parse_request( &$wp )
	{
		if ( array_key_exists( 'wpse9870_api', $wp->query_vars ) ) {
			include 'er_qm_order_page.php';
			exit();
		}
		return;
	}

	
	function er_qm_register_widget(){
		register_widget('quote_manager_widget');
	}
	
	function er_qm_ajax_handler(){
		?>
			<script type="text/javascript" >	
			jQuery(document).ready(function($) {

				// Custom dropdown functionality
				function initCustomDropdowns() {
					$('.custom-dropdown').each(function() {
						var $dropdown = $(this);
						var $button = $dropdown.find('.custom-dropdown-button');
						var $options = $dropdown.find('.custom-dropdown-options');
						var $hiddenInput = $dropdown.siblings('input[type="hidden"]');
						
						// Handle dropdown button click
						$button.off('click').on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();
							
							if ($dropdown.hasClass('disabled')) {
								return;
							}
							
							// Close other dropdowns
							$('.custom-dropdown').not($dropdown).removeClass('open');
							
							// Toggle current dropdown
							$dropdown.toggleClass('open');
						});
						
						// Handle option selection
						$dropdown.find('.custom-dropdown-option').off('click').on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();
							
							var value = $(this).data('value');
							var text = $(this).text();
							
							// Update button text
							$button.find('.dropdown-text').text(text);
							$button.attr('data-value', value);
							
							// Update hidden input
							if ($hiddenInput.length) {
								$hiddenInput.val(value).trigger('change');
							}
							
							// Mark selected option
							$dropdown.find('.custom-dropdown-option').removeClass('selected');
							$(this).addClass('selected');
							
							// Close dropdown
							$dropdown.removeClass('open');
						});
					});
					
					// Close dropdowns when clicking outside
					$(document).off('click.customDropdown').on('click.customDropdown', function(e) {
						if (!$(e.target).closest('.custom-dropdown').length) {
							$('.custom-dropdown').removeClass('open');
						}
					});
				}
				
				// Enable/disable dropdown
				function setDropdownState(dropdownId, enabled) {
					var $dropdown = $('#' + dropdownId + '_dropdown');
					var $button = $dropdown.find('.custom-dropdown-button');
					
					if (enabled) {
						$dropdown.removeClass('disabled');
						$button.removeClass('disabled');
					} else {
						$dropdown.addClass('disabled');
						$button.addClass('disabled');
						$dropdown.removeClass('open');
					}
				}
				
				// Clear dropdown options and add new ones
				function updateDropdownOptions(dropdownId, options, defaultText) {
					var $dropdown = $('#' + dropdownId + '_dropdown');
					var $optionsContainer = $dropdown.find('.custom-dropdown-options');
					var $button = $dropdown.find('.custom-dropdown-button');
					var $hiddenInput = $('#' + dropdownId);
					
					// Clear existing options
					$optionsContainer.empty();
					
					// Add default option
					$optionsContainer.append('<div class="custom-dropdown-option" data-value="' + defaultText + '">' + defaultText + '</div>');
					
					// Add new options
					options.forEach(function(option) {
						$optionsContainer.append('<div class="custom-dropdown-option" data-value="' + option + '">' + option + '</div>');
					});
					
					// Reset button to default
					$button.find('.dropdown-text').text(defaultText);
					$button.attr('data-value', defaultText);
					$hiddenInput.val(defaultText);
					
					// Re-initialize event handlers for new options
					initCustomDropdowns();
				}
				
				// Initialize dropdowns
				initCustomDropdowns();

				$('#anotherclub').click(function() {
					$('#newclub_hid').toggle("slow");
				});

				/*
				 * The following code is triggered once the user selects a manufacturer
				 *
				*/
				$('#widget_manufacturer').val('Select Manufacturer').change(function(){
					$("#widget_club_type_field").show();
				
					var manufacturer = $("#widget_manufacturer").val();
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					var data = {
						action: 'repopulate_widget_club_type',
						selected_manufacturer: manufacturer
					};
					
					// Show loading state
					updateDropdownOptions('widget_club_type', [], 'Loading...');
					setDropdownState('widget_club_type', false);
					updateDropdownOptions('widget_club_model', [], 'Loading...');
					setDropdownState('widget_club_model', false);
					
					$.post(ajaxurl, data, function(response) {
						var convertedData = JSON.parse(response);
						updateDropdownOptions('widget_club_type', convertedData, 'Select Club Type');
						setDropdownState('widget_club_type', true);
						updateDropdownOptions('widget_club_model', [], 'Select Model');
						setDropdownState('widget_club_model', false);
					});
				});
				
				/*
				 * The following code is triggered once the user selects a club type
				 *
				*/
				
				$('#widget_club_type').val('Select Club Type').change(function(){
					$("#widget_model_field").show();
					var manufacturer = $("#widget_manufacturer").val();
					var clubType = $("#widget_club_type").val();
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					
					var data = {
						action: 'repopulate_widget_club_model',
						selected_manufacturer: manufacturer,
						selected_club_type: clubType
					};
					
					// Show loading state
					updateDropdownOptions('widget_club_model', [], 'Loading...');
					setDropdownState('widget_club_model', false);
					
					$.post(ajaxurl, data, function(response) {
						var convertedData = JSON.parse(response);
						updateDropdownOptions('widget_club_model', convertedData, 'Select Model');
						setDropdownState('widget_club_model', true);
					});
				});
				
				
				/*
				 * The following code is triggered once the user selects a club model
				 *
				*/
				
				
				/*  Commented out because these fields do not differ based on club
				$('#widget_club_model').val('Select Model').change(function(){
					var manufacturer = $("#widget_manufacturer").val();
					var clubType = $("#widget_club_type").val();
					var clubModel = $("#widget_club_model").val();
					
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					
					var data = {
						action: 'repopulate_widget_club_condition',
						selected_manufacturer: manufacturer,
						selected_club_type: clubType,
						selected_club_model: clubModel
					};
					$("#widget_club_condition").find('option').remove().end();
					$("#widget_club_condition").append("<option value='Select Condition'>Select Condition</option>");
					$.post(ajaxurl, data, function(response) {
						
						var convertedData = JSON.parse(response);
						for(var i = 0; i<convertedData.length;i++){						
							$("#widget_club_condition").append("<option value='" + convertedData[i] + "'>" + convertedData[i] + "</option>");
						}
						
					});
				});
				*/
				
				/*
				 * The following code is triggered once the user clicks the "Iron Set Quantity" Input on the the Club Review page
				 *
				*/
				
				
				
				/*
				 * The following code is triggered once the user clicks the "Place Order" Button on the Widget
				 *
				*/
				
				$("#widget-search-button").click(function(){
					if($("#widget_manufacturer").val()=="Select Manufacturer"){
						alert("Please Select a Manufacturer");
						return false;
					} else if ($("#widget_club_type").val()=="Select Club Type"){
						alert("Please Select a Club Type");
						return false;
					} else if ($("#widget_club_model").val()=="Select Model"){
						alert("Please Select a Club Model");
						return false;
					} else {
						var manufacturer = "&manufacturer=" + $("#widget_manufacturer").val();
						var clubType = "&type=" + $("#widget_club_type").val();
						var clubModel = "&model=" + $("#widget_club_model").val();
						var clubCondition = "&condition=Average"; // Default condition
						var isSet = "&set=True";
						var vendorWindow = "<?php echo ($_GET['vendorwindow'] ? "&vendorwindow=" . sanitize_text_field($_GET['vendorwindow']) : '' ); ?>";
						if((clubType.indexOf("Set"))>1){
							window.location = "/?orders=new" + vendorWindow + manufacturer + clubType + clubModel + clubCondition + isSet;
						} else {
							window.location = "/?orders=new" + vendorWindow + manufacturer + clubType + clubModel + clubCondition;
						}
					}
					
				});
				
				/*
				 * The following code is triggered once the user clicks the "Retrieve Held Quote" Button on the Widget
				 *
				*/
				$("#widget-find-held-quote").click(function(){
					if(Boolean(<?php echo is_user_logged_in();?>)){
						window.location = "/?findheldquote=new";
					} else {
						window.location = "wp-login.php";
					}
				});
				
				/*
				 *
				 * The following code gets triggered on the order preview page when "Add to Cart" is clicked
				 *
				*/
				$("#add-to-cart").click(function(){
console.log('hhhh')
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					var quantity = $("#er_qm_quantity").val();
					var condition = $("#widget_club_condition").val();
					var shaftType = "NA";
					if($("#er_qm_shaft_type").val()){
						shaftType = $("#er_qm_shaft_type").val();
					} else if($("#er_qm_shaft_type").text()) {
						shaftType = $("#er_qm_shaft_type").text();
					}
					if($("#er_qm_headonly").length){
						var headOnly = $("#er_qm_headonly").is(":checked");
					}else {
						var headOnly = false;
					}
					if($("#er_qm_premium_shaft").val()){
						var premiumShaft = $("#er_qm_premium_shaft").val();
					}else {
						var premiumShaft = false;
					}
					var tempPrice = $("#quoteprice").html();
					var ironq = 0;
					if($("#er_qm_iron_set_quantity")) {
						ironq = $("#er_qm_iron_set_quantity").val();
					}
					var tempPrice = $("#quoteprice").html();
					
			
					var data = {
						action: 'add_clubs_to_cart',
						quantity: quantity,
						condition: condition,
						headOnly: headOnly,
						shaft: shaftType,
						premiumShaft: premiumShaft,
						price: tempPrice,
						ironQuantity: ironq
					};
					var vendorWindow = "<?php echo ($_GET['vendorwindow'] ? "&vendorwindow=" . sanitize_text_field($_GET['vendorwindow']) : '' ); ?>";
					$.post(ajaxurl, data, function(response) {
						window.location = "/?quotesummary" + vendorWindow;
					});
				});
				
				/*
				 *
				 * The following code gets triggered on the checkout page when "Clear Cart" is clicked
				 *
				*/
				$("#er_qm_clear_cart").click(function(){
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					var data = {
						action: 'clear_all_from_cart',
					};
					$.post(ajaxurl, data, function(response) {
						alert(response);
						window.location = "/";
					});
				});

				/*
				 *
				 * The following code gets triggered on the checkout page when "Update Cart" is clicked
				 *
				*/
				$("#er_qm_update_cart").click(function(){
					var data = new Array();
					$(".quote-table-row").each(function() {
						var quantity = ($(this).find(".er_qm_quantity").val());
						var id = ($(this).find(".er_qm_quantity").attr('id'));
						var price = $(this).find(".er_qm_price").val();
						var shaft = $(this).find(".er_qm_shaft").val();
						var premiumShaft = "none";
						var cond = $(this).find(".er_qm_condition").val();
						
						// Get iron quantity - try mobile first, then desktop
						var ironQuantity = 0;
						// Check mobile details first
						$(this).find('.mobile-detail-row').each(function() {
							var label = $(this).find('.mobile-detail-label').text();
							if (label === 'Iron Set Qty:') {
								var ironQtyText = $(this).find('.mobile-detail-value').text();
								ironQuantity = parseInt(ironQtyText) || 0;
							}
						});
						
						// If not found in mobile, check desktop version
						if (ironQuantity === 0) {
							var desktopIronQty = $(this).find('.row-iron-qty').text().trim();
							if (desktopIronQty && desktopIronQty !== '-') {
								ironQuantity = parseInt(desktopIronQty) || 0;
							}
						}
						
						var tempArray = {"id": id, "quantity": quantity, "condition": cond, "price": price, "shaft": shaft, "premiumShaft": "none", "ironQuantity": ironQuantity};
						data.push(tempArray);
					});
					var comments = $("#er_qm_checkout_comments").val();
					var json = JSON.stringify(data);
				//	alert(json);
					var toSend = {
						action: 'update_checkout_cart',
						newSessionInfo: json,
						comments: comments
					};
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					$.post(ajaxurl, toSend, function(response) {
						//alert(response);
						location.reload();
					});
					
				});
				
				/*
				 *
				 * The following code gets used once the "Hold Quote" button is pressed
				 *
				*/			
				
				$("#er_qm_checkout_hold").click(function(){
				
					var onHold = confirm("Are you sure you'd like to hold this quote?\nIf so, you can resume it by visiting your account for the next two weeks.");
					if(onHold==true){
						if(Boolean(<?php echo is_user_logged_in();?>)){
							var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
							var data = {
								action: 'place_order_on_hold',
							};
							$.post(ajaxurl, data, function(response) {
								window.location  = "<?php echo get_home_url(); ?>";
							});
							
						} else {
							window.location = "wp-login.php?redirect_to="+encodeURIComponent('/index.php?quotesummary&heldquote');
						}
					} 
				});
				
				/*
				 *
				 * The following code gets used once the "Continue" button is pressed on the Find Held Quote page
				 *
				*/		
				$(".continue-quote").click(function(){
					var continueQuote = confirm("Continue this quote?");
						if(continueQuote==true){
							var orderToContinue = $(this).attr("id");
							var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
							var data = {
								action: 'continue_held_order',
								orderID: orderToContinue
							};
							$.post(ajaxurl, data, function(response) {
								window.location = "/?quotesummary";
							});
						}

				});
				
				
				$(".delete-quote").click(function(){
					var continueQuote = confirm("Delete this held quote?");
						if(continueQuote==true){
							var orderToContinue = $(this).attr("id");
							var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
							var data = {
								action: 'delete_held_quote',
								order: orderToContinue
							};
							$.post(ajaxurl, data, function(response) {
								window.location = "/?accounthistory";
							});
						}

				});
				
				/*
				 *
				 * The following code gets used on the checkout to toggle the functionality of the "Proceed to Checkout" button
				 *
				*/				
				$("#er_qm_terms_agreement").click(function() {
					//alert($("#er_qm_terms_agreement").attr('checked'));
					if ($(this).is(":checked")) {
						$("#er_qm_checkout_finish").removeAttr("disabled");
					} else {
						$("#er_qm_checkout_finish").attr("disabled", "disabled");
					}

				});
				
				/*
				 *
				 * The following code gets triggered on the checkout page if the user deletes a cart item
				 *
				*/		
				$(".delete_cart_item").click(function(){
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					var theID = $(this).attr('id');
					var data = {
						action: 'delete_cart_item',
						theID: theID
					};
					$.post(ajaxurl, data, function(response) {
						//alert(response);
						location.reload();
					});
				});
				
				/*
				 *
				 * The following code gets triggered for the new quantity control buttons (+/-)
				 *
				*/
				$(".qty-plus").click(function(){
					var input = $(this).siblings('.er_qm_quantity');
					var currentVal = parseInt(input.val());
					input.val(currentVal + 1);
					syncQuantity(input);
					updateCartTotals();
					updateCartViaAjax();
				});
				
				$(".qty-minus").click(function(){
					var input = $(this).siblings('.er_qm_quantity');
					var currentVal = parseInt(input.val());
					if(currentVal > 1) {
						input.val(currentVal - 1);
						syncQuantity(input);
						updateCartTotals();
						updateCartViaAjax();
					}
				});
				
				// Auto-save comments when they change
				$("#er_qm_checkout_comments").on('input', function(){
					clearTimeout(this.saveTimeout);
					this.saveTimeout = setTimeout(function() {
						updateCartViaAjax();
					}, 1000); // Save after 1 second of no typing
				});
				
				// Function to update cart totals in real-time
				function updateCartTotals() {
					var orderTotal = 0;
					$('.quote-table-row').each(function() {
						var quantity = parseInt($(this).find('.er_qm_quantity').first().val());
						var price = parseFloat($(this).find('.er_qm_price').val().replace(/,/g, ''));
						var lineTotal = quantity * price;
						
						// Update desktop line total
						$(this).find('.row-total').text('$' + lineTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
						
						// Update mobile line total
						var mobileRow = $(this);
						mobileRow.find('.mobile-detail-row').each(function() {
							var label = $(this).find('.mobile-detail-label').text();
							if (label === 'Total:') {
								$(this).find('.mobile-detail-value').text('$' + lineTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
							}
						});
						
						orderTotal += lineTotal;
					});
					
					// Update the order total
					$('#quote-summary-table-order-total-amount').text('$' + orderTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				}
				
				// Function to update cart via AJAX
				function updateCartViaAjax() {
					var data = new Array();
					$(".quote-table-row").each(function() {
						// Find the first quantity input (works for both mobile and desktop)
						var quantityInput = $(this).find(".er_qm_quantity").first();
						var quantity = quantityInput.val();
						var id = quantityInput.attr('id');
						var price = $(this).find(".er_qm_price").val();
						var shaft = $(this).find(".er_qm_shaft").val();
						var premiumShaft = "none";
						var cond = $(this).find(".er_qm_condition").val();
						
						// Get iron quantity - try mobile first, then desktop
						var ironQuantity = 0;
						// Check mobile details first
						$(this).find('.mobile-detail-row').each(function() {
							var label = $(this).find('.mobile-detail-label').text();
							if (label === 'Iron Set Qty:') {
								var ironQtyText = $(this).find('.mobile-detail-value').text();
								ironQuantity = parseInt(ironQtyText) || 0;
							}
						});
						
						// If not found in mobile, check desktop version
						if (ironQuantity === 0) {
							var desktopIronQty = $(this).find('.row-iron-qty').text().trim();
							if (desktopIronQty && desktopIronQty !== '-') {
								ironQuantity = parseInt(desktopIronQty) || 0;
							}
						}
						
						var tempArray = {"id": id, "quantity": quantity, "condition": cond, "price": price, "shaft": shaft, "premiumShaft": "none", "ironQuantity": ironQuantity};
						data.push(tempArray);
					});
					var comments = $("#er_qm_checkout_comments").val();
					var json = JSON.stringify(data);
					var toSend = {
						action: 'update_checkout_cart',
						newSessionInfo: json,
						comments: comments
					};
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					$.post(ajaxurl, toSend, function(response) {
						// Cart updated silently, no page reload needed
					});
				}
				
				// Function to sync quantity between mobile and desktop
				function syncQuantity(changedInput) {
					var id = changedInput.attr('id');
					var newValue = changedInput.val();
					
					// Update all quantity inputs with the same ID
					$('.er_qm_quantity[id="' + id + '"]').val(newValue);
					
					// Update mobile detail values
					var row = changedInput.closest('.quote-table-row');
					var price = parseFloat(row.find('.er_qm_price').val().replace(/,/g, ''));
					var quantity = parseInt(newValue);
					var lineTotal = quantity * price;
					
					// Update mobile detail total
					row.find('.mobile-detail-value:contains("$")').each(function() {
						var label = $(this).siblings('.mobile-detail-label').text();
						if (label === 'Total:') {
							$(this).text('$' + lineTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
						}
					});
				}
				
				/*
				 *
				 * The following code gets triggered on the checkout page if the user clicks ""Receive shipping label and schedule pick-up""
				 *
				*/		

				$("#shipping_option_receive").click(function(){
					 if($('#shipping_option_receive').is(':checked')){
					 	$("#shipping-label-info").show(400);
					 }
					 
				});
				

				/*
				 *
				 * The following code gets triggered on the checkout page if the user clicks ""Receive shipping label and schedule pick-up""
				 *
				*/		

				$("#shipping_option_handle").click(function(){
					 if($('#shipping_option_handle').is(':checked')){
					 	$("#shipping-label-info").hide(400);
					 }
				});
				
				
				/*
				 *
				 * The following code gets triggered on the checkout page if the user clicks "Proceed to Checkout"
				 *
				*/					

				$("#er_qm_checkout_finish").click(function(){
			
					if($("#shipping_option_receive").is(':checked')){

						var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
						var height = $("#shipping_parcel_height").val();
						var width = $("#shipping_parcel_width").val();
						var length = $("#shipping_parcel_length").val();
						var weight = $("#shipping_parcel_weight").val()*16;
					

					
						if($("#shipping_parcel_length").val()=="" || isNaN($("#shipping_parcel_length").val())){
							alert("Please enter a length for your shipping parcel - numbers only please!");
							$("#shipping_parcel_length").focus();
						} else if($("#shipping_parcel_width").val()=="" || isNaN($("#shipping_parcel_width").val())) {
							alert("Please enter a width for your shipping parcel - numbers only please!");
							$("#shipping_parcel_width").focus();
						} else if($("#shipping_parcel_height").val()=="" || isNaN($("#shipping_parcel_height").val())) {
							alert("Please enter a height for your shipping parcel - numbers only please!");
							$("#shipping_parcel_height").focus();
						} else if($("#shipping_parcel_weight").val()=="" || isNaN($("#shipping_parcel_weight").val())) {
							alert("Please enter a weight for your shipping parcel - numbers only please!");
							$("#shipping_parcel_weight").focus();
						}else if(!$("#er_qm_terms_agreement").is(':checked')) {
							alert("Please agree to the terms");
							$("#er_qm_terms_agreements").focus();
						} else {
							var data = {
							action: 'save_shipment_parameters',
							height: height,
							width: width,
							length: length,
							weight: weight
							};
							$.post(ajaxurl, data, function(response) {
								
							});

						
							$(document).ajaxStop(function() {
								var requireLogin = ""
								var vendorWindow = "<?php echo ($_GET['vendorwindow'] ? "&vendorwindow=" . sanitize_text_field($_GET['vendorwindow']) : '' ); ?>";
								alert('hello');
								if(Boolean(<?php echo is_user_logged_in();?>)){
									window.location = "/?checkout&label=true&length=" + length + "&width=" + width + "&height=" + height + "&weight=" + weight;
								} else {
								
									window.location = "wp-login.php?redirect_to=index.php?checkout&label=true&length=" + length + "&width=" + width + "&height=" + height + "&weight=" + weight + "&logindirect=1&reauth=1";
								}
							});
							
						}
					}else{
						if(!$("#er_qm_terms_agreement").is(':checked')) {
							alert("Please agree to the terms");
							$("#er_qm_terms_agreements").focus();
						} else {
							if(Boolean(<?php echo is_user_logged_in();?>)){
								window.location = "/?checkout&label=false";
							} else if ("<?php echo $_GET['vendorwindow']; ?>") {
									window.location = "/?checkout&label=false&vendorwindow=golftec";
							} else {
								window.location = "wp-login.php?redirect_to=index.php?checkout&label=false&logindirect=1&reauth=1";
							}
						}

					}
				});
				
				/*
				 *
				 * The following code gets triggered on the quote summary page if the user clicks "Submit Quote"
				 *
				*/	
				var shipmentID = "";
				var shipmentType = "";

				
				$("input[name=shipment_options]").click(function(){

					if($("input[name=shipment_options]").val() == "on"){
						shipmentID = $(this).attr("class");
						shipmentType = $(this).attr("id");
					}
				});
				
				// Payment method toggle
				$('input[name="er_qm_payment_type"]').on('change', function() {
					if($(this).val() === 'check') {
						$('#check-payment-form').show();
						$('#paypal-payment-form').hide();
					} else {
						$('#check-payment-form').hide();
						$('#paypal-payment-form').show();
					}
				});

				// Form submission
				$("#er_qm_submit_quote").click(function(){
					
					function validateEmail(email) {
						return email.match(
							/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						);
					}

					var vendorWindow = "<?php echo isset($_GET['vendorwindow']) ? '&vendorwindow=true' : ''; ?>";
					var shipmentType = "<?php echo isset($_GET['shipmentType']) ? $_GET['shipmentType'] : ''; ?>";
					var shipmentID = "<?php echo isset($_GET['shipmentID']) ? $_GET['shipmentID'] : ''; ?>";

					if (vendorWindow) {
						// const validateEmail = (email) => {
						// 	return email.match(
						// 		/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						// 	);
						// };
						if (!validateEmail($("#er_qm_account_email_address").val())) {
							alert("Please enter a valid email address");
							return;
						}
						var accountEmailAddress = $("#er_qm_account_email_address").val();
					}

					if($("#er_qm_payment_check").is(":checked")){
						if($("#er_qm_name_check").val() == ""){
							alert("Please enter a name/company for the check's address");
							return;
						}

						var nameOnCheck = $("#er_qm_name_check").val();
						var street = $("#er_qm_street_address").val();
						var state = $("#er_qm_state_address").val();
						var city = $("#er_qm_city_address").val();
						var zipcode = $("#er_qm_zip_address").val();

						if(!street || !state || !city || !zipcode) {
							alert("Please fill in all address fields");
							return;
						}

						$("#er_qm_submit_quote").html('Processing...').prop('disabled', true);
						
						setTimeout(function() {
							var redirectURL = "/?orderconfirmation&nameoncheck=" + encodeURIComponent(nameOnCheck) + 
								vendorWindow + "&street=" + encodeURIComponent(street) + 
								"&city=" + encodeURIComponent(city) + 
								"&state=" + encodeURIComponent(state) + 
								"&zipcode=" + encodeURIComponent(zipcode) + 
								"&shipmentType=" + shipmentType + 
								"&shipmentID=" + shipmentID + 
								"&payment=check";
							
							if (vendorWindow) {
								redirectURL += "&accountEmail=" + encodeURIComponent(accountEmailAddress);
							}
							window.location = redirectURL;
						}, 500);
					} else if($("#er_qm_payment_paypal").is(":checked")) {
						if($("#er_qm_paypal_email").val() == ""){
							alert("Please enter the email address associated with your PayPal account");
							return;
						}
						
						if(!validateEmail($("#er_qm_paypal_email").val())) {
							alert("Please enter a valid PayPal email address");
							return;
						}

						var paypal = $("#er_qm_paypal_email").val();
						$("#er_qm_submit_quote").html('Processing...').prop('disabled', true);
						
						setTimeout(function() {
							var redirectURL = "/?orderconfirmation&paypal=" + encodeURIComponent(paypal) + 
								vendorWindow + 
								"&shipmentType=" + shipmentType + 
								"&shipmentID=" + shipmentID + 
								"&payment=paypal";
							
							if (vendorWindow) {
								redirectURL += "&accountEmail=" + encodeURIComponent(accountEmailAddress);
							}
							window.location = redirectURL;
						}, 500);
					} else {
						alert("Please select a payment type");
					}
				});
				
				/*
				 * The Following is only for debugging purposes, to be deleted after launch
				*/
				/***********************************************************************************************************/
				function print_r(arr,level) {
				var dumped_text = "";
				if(!level) level = 0;

				//The padding given at the beginning of the line.
				var level_padding = "";
				for(var j=0;j<level+1;j++) level_padding += "    ";

				if(typeof(arr) == 'object') { //Array/Hashes/Objects 
					for(var item in arr) {
						var value = arr[item];

						if(typeof(value) == 'object') { //If it is an array,
							dumped_text += level_padding + "'" + item + "' ...\n";
							dumped_text += print_r(value,level+1);
						} else {
							dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
						}
					}
				} else { //Stings/Chars/Numbers etc.
					dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
				}
				return dumped_text;
				}
				
				function toObject(arr) {
				  var rv = {};
				  for (var i = 0; i < arr.length; ++i)
					if (arr[i] !== undefined) rv[i] = arr[i];
				  return rv;
				}
				
				// FAQ Accordion functionality
				$('.faq-box').on('click', function() {
					const $faqBox = $(this);
					
					if ($faqBox.hasClass('active')) {
						// Close this FAQ with fast animation
						$faqBox.addClass('closing');
						$faqBox.removeClass('active');
						setTimeout(() => {
							$faqBox.removeClass('closing');
						}, 150);
					} else {
						// Close all other FAQs with fast animation
						$('.faq-box.active').each(function() {
							const $otherFaq = $(this);
							$otherFaq.addClass('closing');
							$otherFaq.removeClass('active');
							setTimeout(() => {
								$otherFaq.removeClass('closing');
							}, 150);
						});
						
						// Open this FAQ with slow animation (0.6s)
						$faqBox.addClass('active');
					}
				});
				//tooltip badge
				
			});
	
			</script>
			<script>
				document.addEventListener('DOMContentLoaded', function () {
    var badge = document.querySelector('.product-condition-block__badge');
    var tooltip = document.querySelector('.product-condition-block__badge .tooltip');
    var tooltipClose = document.querySelector('.product-condition-block__badge .tooltip-close');

    if (badge && tooltip) {
        badge.addEventListener('click', function (e) {
            if (!tooltip.contains(e.target)) {
                e.stopPropagation();
                tooltip.classList.toggle('active');
            }
        });

        document.addEventListener('click', function (e) {
            if (!badge.contains(e.target) && !tooltip.contains(e.target)) {
                tooltip.classList.remove('active');
            }
        });

        if (tooltipClose) {
            tooltipClose.addEventListener('click', function (e) {
                e.stopPropagation();		
                tooltip.classList.remove('active');
            });
        }
    }
});
			</script>
		<?php
	}
	
	function continue_held_order(){
		$orderToContinue = $_POST['orderID'];
		global $post;
		global $current_user;
		get_currentuserinfo();
		$club_order_clubs = get_post_meta( $orderToContinue, 'club_order_clubs', true );
		$_SESSION['er_qm_cart'] = $club_order_clubs;
		$_SESSION['er_qm_hold_title'] = $orderToContinue;
		die();
	}
	
	
	function place_order_on_hold(){
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
			die();
		} else {
			die("There was an error processing your request, please try again.");
		}
	}
	
	function delete_held_quote() {
		global $wpdb;
		global $current_user;
		
		wp_delete_post($_POST['order'], true);
		
		$_SESSION['er_qm_cart'] = "";
		$_SESSION['er_qm_comments'] = "";
		die();
	}
	
	function delete_cart_item(){
		$theID = $_POST['theID'];
		foreach($_SESSION['er_qm_cart'] as $items => $item){
			if($item["id"] == $theID){
				unset($_SESSION['er_qm_cart'][$items]);
				die("Found");
			}
		}
		die("Not Found");
	}
	
	function update_checkout_cart(){
		$data = stripslashes($_POST['newSessionInfo']);
		//echo $data;
		$comments = $_POST['comments'];
		$_SESSION['er_qm_comments'] = $comments;
		//echo $data;
		$newData = json_decode($data, true);
		//if(!isset($_SESSION['er_qm_cart']) ){
		$cart = array();
		//}
		//print_r($newData);
		foreach($newData as $datum){
			$datum["headOnly"] = ($datum["shaft"] == "Head Only") ? "true" : "false";
			$temp_array = array(
				'id' => intval($datum["id"]),
				'quantity' => intval($datum["quantity"]),
				'condition' => $datum["condition"],
				'price' => $datum["price"],
				'shaft' => $datum["shaft"],
				'premiumShaft' => $datum["premiumShaft"],
				'headOnly' => $datum["headOnly"],
				'ironQuantity' => isset($datum["ironQuantity"]) ? intval($datum["ironQuantity"]) : 0
			);
			array_push($cart, $temp_array);
		}
		//print_r($cart);
		$_SESSION["er_qm_cart"] = $cart;
		die(print_r($_SESSION['er_qm_cart']));
	}

	function save_shipment_parameters(){
		$_SESSION['ship_parameters']['length'] = $_POST['length'];	
		$_SESSION['ship_parameters']['width'] = $_POST['width'];		
		$_SESSION['ship_parameters']['height'] = $_POST['height'];		
		$_SESSION['ship_parameters']['weight'] = $_POST['weight'];		
	
		die("Carry on!");
	}	
	
	function clear_all_from_cart(){
		session_destroy();
		session_start();
		die("Your cart has been emptied");
	}
	
	function add_clubs_to_cart(){
	
		$new_order = array(
				'id' => $_SESSION['temp'],
				'quantity' => $_POST['quantity'],
				'condition' => $_POST['condition'],
				'price' => $_POST['price'],
				'headOnly' => $_POST['headOnly'],
				'shaft' => $_POST['shaft'],
				'premiumShaft' => $_POST['premiumShaft'],
				'ironQuantity' => $_POST["ironQuantity"]
			);
		
		 if(!isset($_SESSION['er_qm_cart']) || $_SESSION['er_qm_cart'] == "") {
        $_SESSION["er_qm_cart"] = array();
    }

    $found = false;
    foreach ($_SESSION['er_qm_cart'] as &$item) {
        if (
            $item['id'] == $new_order['id'] &&
            $item['condition'] == $new_order['condition'] &&
            $item['headOnly'] == $new_order['headOnly'] &&
            $item['shaft'] == $new_order['shaft'] &&
            $item['premiumShaft'] == $new_order['premiumShaft'] &&
            $item['ironQuantity'] == $new_order['ironQuantity']
        ) {
            $item['quantity'] += $new_order['quantity'];
            $found = true;
            break;
        }
    }
    unset($item); // break reference

    if (!$found) {
        $_SESSION['er_qm_cart'][] = $new_order;
    }
    die("Your cart has been updated!");
	}

		

	function repopulate_widget_club_model(){
    global $wpdb; 
    $selected_manu = $_POST['selected_manufacturer'];
    $selected_type = $_POST['selected_club_type'];

    // Get club IDs associated with manufacturer
    $query_data_for_club_type = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT p.ID 
             FROM wp_posts p 
             INNER JOIN wp_postmeta m ON p.ID = m.post_id 
             WHERE p.post_status = 'publish' 
             AND m.meta_value = %s",
            $selected_manu
        )
    );

    if (empty($query_data_for_club_type)) {
        die(json_encode([]));
    }

    $club_ids = implode(', ', wp_list_pluck($query_data_for_club_type, 'ID'));

    // Get club IDs matching selected type
    $query_data_for_club_type_field = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT DISTINCT p.ID 
             FROM wp_posts p 
             INNER JOIN wp_postmeta m ON p.ID = m.post_id 
             WHERE m.meta_value = %s 
             AND p.post_status = 'publish' 
             AND p.ID IN ($club_ids)",
            $selected_type
        )
    );

    if (empty($query_data_for_club_type_field)) {
        die(json_encode([]));
    }

    $final_club_ids = implode(', ', wp_list_pluck($query_data_for_club_type_field, 'ID'));

    // Get all club models and order alphabetically
    $query_data_for_club_model = $wpdb->get_results(
        "SELECT DISTINCT m.meta_value AS club_model 
         FROM wp_posts p 
         INNER JOIN wp_postmeta m ON p.ID = m.post_id 
         WHERE p.post_status = 'publish' 
         AND m.meta_key = 'club_model' 
         AND p.ID IN ($final_club_ids) 
         GROUP BY club_model 
         ORDER BY club_model ASC"
    );

    $club_models_response = wp_list_pluck($query_data_for_club_model, 'club_model');

    die(json_encode($club_models_response));
}
	
	function repopulate_widget_club_type(){
		global $wpdb; 
		
		// Get IDs of clubs associated with selected manufacturer
		$selected_manu = $_POST['selected_manufacturer'];
 
		$query_data_for_club_type = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT p.ID 
        FROM wp_posts p 
        INNER JOIN wp_postmeta m ON p.ID = m.post_id 
        WHERE p.post_status = 'publish' 
        AND m.meta_value = %s",
        $selected_manu
    )
);
		
		// setting the global variable to speed up the proceeding ajax actions
		$_SESSION['club_manufacturers'] = $query_data_for_club_type;
		
		$er_qm_response_ajax = implode(', ', wp_list_pluck($query_data_for_club_type, 'ID'));
		
		// Get club types and order alphabetically
		$query_data_for_club_type_field = $wpdb->get_results(
			"SELECT DISTINCT meta_value AS club_type 
			 FROM wp_postmeta 
			 WHERE meta_key = 'club_type_field' 
			 AND post_id IN ($er_qm_response_ajax) 
			 GROUP BY club_type 
			 ORDER BY club_type ASC"
		);
		
		/* OLD CUSTOM PRIORITY ORDERING - COMMENTED OUT
		$query_data_for_club_type_field = $wpdb->get_results(
			"SELECT DISTINCT meta_value AS club_type 
			 FROM wp_postmeta 
			 WHERE meta_key = 'club_type_field' 
			 AND post_id IN ($er_qm_response_ajax) 
			 GROUP BY club_type 
			 ORDER BY 
				CASE club_type
					WHEN 'Driver' THEN 1
					WHEN 'Fairway Wood' THEN 2
					WHEN 'Hybrid' THEN 3
					WHEN 'Iron Set' THEN 4
					WHEN 'Wedge' THEN 5
					WHEN 'Putter' THEN 6
					ELSE 7
				END ASC"
		);
		*/
		
		$_SESSIONS['club_types'] = $query_data_for_club_type_field;
		
		$er_qm_club_type_response = array();
		
		foreach($query_data_for_club_type_field as $querydatum){
			array_push($er_qm_club_type_response, $querydatum->club_type);
		}
		
		//print_r($er_qm_club_type_response);
		
		die(json_encode($er_qm_club_type_response));
	}
	
	class quote_manager_widget extends WP_Widget{

		function __construct() {
			// Instantiate the parent object.
			parent::__construct( false, __( 'Quote Manager Widget', 'quotemanager' ) );
		}
		
		function quote_manager_widget(){
			wp_enqueue_script('jquery');
			// process the widget
			$widget_ops = array(
				'classname' => 'er_qm_widget_class',
				'description' => 'Display the Quote Manager widget'
			);
			
			$this->WP_Widget('er_qm_widget_info_id', 'Quote Manager Widget', $widget_ops);
		}
		
		function form($instance){
			// display the admin dash
			// this can be used to create admin settings on the widget screen
		}
		
		function update($new_instance, $old_instance){
			// processes widget options to save
		}
		
		function widget($args, $instance){
			global $wpdb;
			

			// displays the widget
			extract($args);
			
			// Get manufacturers and order alphabetically
			$query_data_for_manufacturer = $wpdb->get_results(
				"SELECT DISTINCT m.meta_value AS club_manufacturer 
				 FROM wp_postmeta m 
				 INNER JOIN wp_posts p ON m.post_id = p.ID 
				 WHERE m.meta_key = 'club_manufacturer' 
				 AND m.meta_value != '' 
				 AND p.post_status = 'publish' 
				 ORDER BY club_manufacturer ASC"
			);
			
		
			?>
            
                        
			<div id="clubselector">
				<div id="flexwrap">
					<h6 class="cf-title">Club Finder</h6>
					<div class="clubselect">
						<label style="color: #fff;">Manufacturer
							<div class="custom-dropdown" id="widget_manufacturer_dropdown">
								<div class="custom-dropdown-button" data-value="Select Manufacturer">
									<span class="dropdown-text">Select Manufacturer</span>
									<span class="custom-dropdown-arrow">▼</span>
								</div>
								<div class="custom-dropdown-options">
									<div class="custom-dropdown-option" data-value="Select Manufacturer">Select Manufacturer</div>
									<?php
									foreach($query_data_for_manufacturer as $querydatum){
										$select_manu = $querydatum->club_manufacturer;
										echo "<div class='custom-dropdown-option' data-value='".$select_manu."'>".$select_manu."</div>";
									}
									?>
								</div>
							</div>
							<input type="hidden" id="widget_manufacturer" value="Select Manufacturer" />
						</label>
					</div>
					<div class="clubselect" style="margin-top: 5px; display: none;" id="widget_club_type_field">
						<label style="color: #fff;">Club Type
							<div class="custom-dropdown disabled" id="widget_club_type_dropdown">
								<div class="custom-dropdown-button disabled" data-value="Select Club Type">
									<span class="dropdown-text">Select Club Type</span>
									<span class="custom-dropdown-arrow">▼</span>
								</div>
								<div class="custom-dropdown-options">
									<div class="custom-dropdown-option" data-value="Select Club Type">Select Club Type</div>
								</div>
							</div>
							<input type="hidden" id="widget_club_type" value="Select Club Type" />
						</label>
					</div>
					
					<div class="clubselect" style="margin-top: 5px; display: none;" id="widget_model_field">
						<label style="color: #fff;">Model
							<div class="custom-dropdown disabled" id="widget_club_model_dropdown">
								<div class="custom-dropdown-button disabled" data-value="Select Model">
									<span class="dropdown-text">Select Model</span>
									<span class="custom-dropdown-arrow">▼</span>
								</div>
								<div class="custom-dropdown-options">
									<div class="custom-dropdown-option" data-value="Select Model">Select Model</div>
								</div>
							</div>
							<input type="hidden" id="widget_club_model" value="Select Model" />
						</label>
					</div>
					
					<!--<div style="margin-top: 5px;">
					<label style="color: #fff;">Condition
						<select id="widget_club_condition" style="width: 170px;">
							
							<option value='Below Average'>Below Average</option>
							<option selected value='Average'>Average</option>
							<option value='New'>New</option>
						</select>
					</label>
			</div>-->
	                
	                
					
					<button value="Search" id="widget-search-button">Search</button>
				</div>
               <div class="noclubs">Don't see your equipment listed? Please email us at <a href="mailto:info@proclubs.com">info@proclubs.com</a> for a price quote.</div>
                
			</div>
			<?php
			//echo $after_widget;
		}
	}
?>