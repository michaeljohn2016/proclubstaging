<?php
		/*
		 *
		 * beginning of embedded custom page template
		 * This creates the Review Your Order Page
		 *
		*/
		get_header();
?>

	<main id="primary" class="site-content clubreview">
		<div id="content" role="main">
			<script type="text/javascript" src="/jquery-1.12.4.min.js"></script>
			<?php
		//do_action('udesign_above_page_content');
		//load_template( $_template_file, $require_once )

						$_SESSION['tempCondition'] = $_GET['condition'];
						global $wpdb;
						$manu_order = $_GET['manufacturer'];
						$type_order = $_GET['type'];
						$model_order = $_GET['model'];
						$condition_order = $_GET['condition'];
						$query_data_for_post_id = $wpdb->get_results(
							"
								SELECT c0.post_id
								AS postid
								FROM wp_postmeta AS c0
								JOIN wp_postmeta AS c1 ON c1.post_id = c0.post_id
								JOIN wp_postmeta AS c2 ON c2.post_id = c1.post_id
								WHERE c0.meta_key =  'club_manufacturer'
								AND c0.meta_value =  '$manu_order'
								AND c1.meta_key =  'club_type_field'
								AND c1.meta_value =  '$type_order '
								AND c2.meta_key =  'club_model'
								AND c2.meta_value =  '$model_order'
								LIMIT 0 , 30;
							"
						);

						$order_postid = $query_data_for_post_id[0]->postid;
						$_SESSION['temp'] = $order_postid ;

						$query_data_for_price = $wpdb->get_results(
							"
								SELECT meta_value AS price FROM wp_postmeta WHERE meta_key='club_price' AND post_id=$order_postid;
							"
						);

						$price = ceil($query_data_for_price[0]->price);
						$price = number_format($price, 2);

						$head_only_price = get_post_meta($order_postid, 'head_only_price', true) ? number_format(  get_post_meta($order_postid, 'head_only_price', true) , 2) : false;
			
			
			$head_only_price = ceil($head_only_price);
			$head_only_price = number_format($head_only_price, 2);
			
						$premium_shafts_available = get_post_meta($order_postid, 'premium_shaft_available', true);
						if ($premium_shafts_available) {
							$club_type = preg_replace('/\s+/', '-', strtolower($type_order));
							$args = array(
								'post_type'      => 'premium-shaft', // Replace 'premium-shaft' with the name of your custom post type
								'posts_per_page' => -1, // Retrieve all posts
								'tax_query'      => array(
									array(
										'taxonomy' => 'club-type', // Replace 'club-type' with the name of your taxonomy
										'field'    => 'slug',
										'terms'    => $club_type, // Replace 'fairway-wood' with the term you're filtering by
									),
								),
							);
							
							$query = new WP_Query( $args );
							
							if ( $query->have_posts() ) {
								$posts_array = array();
							
								while ( $query->have_posts() ) {
									$query->the_post();
									$posts_array[] = get_post(); // You can modify this to get specific post data if needed
								}
							
								// Restore original post data
								wp_reset_postdata();
							
								// Now $posts_array contains all the entries in the "premium-shaft" custom post type
								$premium_shafts_list = $posts_array;
							}
						}

						$_SESSION['tempPrice'] = $price;

						$query_data_for_image_id = $wpdb->get_results(
							"
								SELECT meta_value AS image FROM wp_postmeta WHERE meta_key='_thumbnail_id' AND post_id=$order_postid;
							"
						);

						$imageID = intval($query_data_for_image_id[0]->image);

						$query_data_for_image = $wpdb->get_results(
							"
								SELECT guid AS URL from wp_posts WHERE ID =$imageID;
							"
						);
						$imageURL = $query_data_for_image[0]->URL;
						?>

						<?php
						$queried_post = get_post($order_postid);
						?>
			
	<?php
$product_prices_raw = get_post_meta($order_postid, "product_prices", true);
$product_prices = json_decode($product_prices_raw, true);

 if (!empty($product_prices) && is_array($product_prices)) { ?>
			
<script>
$(document).ready(function() {
    // 1. Встановлюємо значення для select за замовчуванням
    document.getElementById("widget_club_condition").value = "Average";
    update_price(); // Оновлюємо ціну при першому завантаженні
});

function update_price() {
    var cond = document.getElementById("widget_club_condition").value;
    
    // 2. Заміна значень для cond
    switch (cond) {
        case "Below Average":
            cond = 0.70;
            break;
        case "Average":
            cond = 1;
            break;
        case "New":
            cond = 1.20;
            break;
    }

    // 3. Отримуємо вибір матеріалу з select
    var selectedMaterial = document.getElementById('er_qm_shaft_type').value;

    // 4. Відповідний об'єкт для вибраного матеріалу з PHP
    var productPrices = <?php echo json_encode($product_prices); ?>;

    var price = 0;
    var headonlyPrice = 0;

    // Шукаємо ціну для вибраного матеріалу
    for (var i = 0; i < productPrices.length; i++) {
        if (productPrices[i].material === selectedMaterial) {
            price = productPrices[i].price;
            headonlyPrice = productPrices[i].headonly;
            break;
        }
    }

    // 5. Перевіряємо додаткові умови
    if (document.getElementById('er_qm_iron_set_quantity') != null) {
        // Якщо є кількість заліза
        price = ((price / 8) * document.getElementById('er_qm_iron_set_quantity').value) * cond;
    } else if (document.body.contains(document.getElementById('er_qm_headonly')) && document.getElementById('er_qm_headonly').checked) {
        // Якщо вибрано лише головку
        price = headonlyPrice * cond;
        document.getElementById('shafttype').style.display = "none";  // Приховуємо вибір стержня
        if (document.body.contains(document.getElementById('premium-shaft'))) {
            document.getElementById('premium-shaft').style.display = "none"; // Приховуємо преміум стержень
        }
    } else {
        // Якщо є стандартний набір
        price = price * cond;
        document.getElementById('shafttype').style.display = "block"; // Показуємо вибір стержня

        // Якщо вибрано преміум стержень
        if (document.body.contains(document.getElementById('er_qm_premium_shaft')) && document.getElementById("er_qm_premium_shaft").value != "none") {
            price = price + Number(document.getElementById('er_qm_premium_shaft').options[document.getElementById('er_qm_premium_shaft').selectedIndex].getAttribute('data-price'));
            document.getElementById('shafttype').style.display = "none";  // Приховуємо вибір стержня, якщо преміум вибрано
        }

        if (document.body.contains(document.getElementById('premium-shaft'))) {
            document.getElementById('premium-shaft').style.display = "block";  // Показуємо преміум стержень
        }
    }
	price = Math.ceil(price);
    // 6. Оновлюємо відображення ціни
    document.getElementById("quoteprice").innerHTML = price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
}
</script>

		<?php } else {?>
			<script>
				
			$( document ).ready(function() {
			   document.getElementById("widget_club_condition").value = "Average";
			});
			function update_price() {
				
				var cond = document.getElementById("widget_club_condition").value;
				switch(cond) {
					case "Below Average":
					cond = 0.70;
					break;

					case "Average":
					cond = 1;
					break;

					case "New":
					cond = 1.20;
					break;
				}
				if(document.getElementById('er_qm_iron_set_quantity') != null) {
					var price = ((<?php echo substr(filter_var($price, FILTER_SANITIZE_NUMBER_INT), 0, -2); ?> / 8) * document.getElementById('er_qm_iron_set_quantity').value) * cond;
				} else if (document.body.contains(document.getElementById('er_qm_headonly')) && document.getElementById('er_qm_headonly').checked) {
					var price = <?php echo $head_only_price ? substr(filter_var($head_only_price, FILTER_SANITIZE_NUMBER_INT), 0, -2) : 0 ; ?> * cond;
					document.getElementById('shafttype').style.display = "none";
					if (document.body.contains(document.getElementById('premium-shaft'))) {
						document.getElementById('premium-shaft').style.display = "none";
					}
				}
				else {
					var price = <?php echo substr(filter_var($price, FILTER_SANITIZE_NUMBER_INT), 0, -2); ?> * cond;
					document.getElementById('shafttype').style.display = "block";
					if (document.body.contains(document.getElementById('er_qm_premium_shaft')) && document.getElementById("er_qm_premium_shaft").value != "none") {
						price = price + Number(document.getElementById('er_qm_premium_shaft').options[document.getElementById('er_qm_premium_shaft').selectedIndex].getAttribute('data-price'));
						document.getElementById('shafttype').style.display = "none";
					}
					if (document.body.contains(document.getElementById('premium-shaft'))) {
						document.getElementById('premium-shaft').style.display = "block";
					}
				}
					
					price = Math.ceil(price);
				document.getElementById("quoteprice").innerHTML = price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');;
			}
			</script>
<?php } ?>
			<div id="proclubsleft">
				<div id="quoteimg">
					<img src="<?php echo $imageURL; ?>" class="img-responsive" style="display: inline-block;">
				</div>
				<div id="conditionguide">
					<div id="conditionhead">
						Condition Guide <span class="plusminus"><i class="fal fa-plus"></i><i class="fal fa-minus"></i></span>
					</div>
					<div id="condition-levels">
						<div class="conditionlevel">
							<strong>New</strong>
							<p>Brand new / never been hit a single time.</p>
						</div>
						<div class="conditionlevel">
							<strong>Average</strong>
							<p>Average play throughout the club head but all original design features still in tact. No skymarks, dents, dings, rattles.</p>
						</div>
						<div class="conditionlevel">
							<strong>Fair</strong>
							<p>Club shows a little more wear then most but still has the overall integrity of the club in tact.</p>
						</div>
					</div>
				</div>
			</div>

			<div id="proclubsright">
				<div id="condition-title"><?php echo $_GET['manufacturer']." ".$_GET['model'];?></div>
				<div id="condition-selects">
					<div class="product-condition-block__badge">
						<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
						<mask id="path-1-outside-1_5448_14744" maskUnits="userSpaceOnUse" x="0" y="0" width="26" height="26" fill="black">
						<rect fill="white" width="26" height="26"/>
						<path d="M13 1C19.633 1 25 6.3677 25 13C25 19.633 19.6324 25 13 25C6.367 25 1 19.6323 1 13C1 6.367 6.36761 1 13 1ZM13 2.6748C7.30647 2.6748 2.6748 7.30642 2.6748 13C2.6748 18.6936 7.30647 23.3252 13 23.3252C18.6935 23.3252 23.3252 18.6936 23.3252 13C23.3252 7.30642 18.6935 2.6748 13 2.6748ZM13 12.25C13.6791 12.25 14.2323 12.5501 14.2324 12.9922V19.0107C14.2322 19.3898 13.6791 19.7686 13 19.7686C12.2895 19.7685 11.7844 19.3898 11.7842 19.0107V12.9922C11.7844 12.5501 12.2895 12.2501 13 12.25ZM12.999 7.29102C13.7098 7.29102 14.2783 7.88668 14.2783 8.57227C14.2781 9.25774 13.7097 9.87109 12.999 9.87109C12.2727 9.87097 11.7043 9.25767 11.7041 8.57227C11.7041 7.88675 12.2725 7.29114 12.999 7.29102Z"/>
						</mask>
						<path d="M13 1C19.633 1 25 6.3677 25 13C25 19.633 19.6324 25 13 25C6.367 25 1 19.6323 1 13C1 6.367 6.36761 1 13 1ZM13 2.6748C7.30647 2.6748 2.6748 7.30642 2.6748 13C2.6748 18.6936 7.30647 23.3252 13 23.3252C18.6935 23.3252 23.3252 18.6936 23.3252 13C23.3252 7.30642 18.6935 2.6748 13 2.6748ZM13 12.25C13.6791 12.25 14.2323 12.5501 14.2324 12.9922V19.0107C14.2322 19.3898 13.6791 19.7686 13 19.7686C12.2895 19.7685 11.7844 19.3898 11.7842 19.0107V12.9922C11.7844 12.5501 12.2895 12.2501 13 12.25ZM12.999 7.29102C13.7098 7.29102 14.2783 7.88668 14.2783 8.57227C14.2781 9.25774 13.7097 9.87109 12.999 9.87109C12.2727 9.87097 11.7043 9.25767 11.7041 8.57227C11.7041 7.88675 12.2725 7.29114 12.999 7.29102Z" fill="#0268C6"/>
						<path d="M13 12.25V11.95H13L13 12.25ZM14.2324 12.9922H14.5324V12.9921L14.2324 12.9922ZM14.2324 19.0107L14.5324 19.0109V19.0107H14.2324ZM13 19.7686L13 20.0686H13V19.7686ZM11.7842 19.0107H11.4842V19.0109L11.7842 19.0107ZM11.7842 12.9922L11.4842 12.9921V12.9922H11.7842ZM12.999 7.29102V6.99102H12.999L12.999 7.29102ZM14.2783 8.57227L14.5783 8.57237V8.57227H14.2783ZM12.999 9.87109L12.999 10.1711H12.999V9.87109ZM11.7041 8.57227H11.4041V8.57237L11.7041 8.57227ZM13 1V1.3C19.4673 1.3 24.7 6.53338 24.7 13H25H25.3C25.3 6.20203 19.7987 0.7 13 0.7V1ZM25 13H24.7C24.7 19.4673 19.4667 24.7 13 24.7V25V25.3C19.7981 25.3 25.3 19.7987 25.3 13H25ZM13 25V24.7C6.5327 24.7 1.3 19.4666 1.3 13H1H0.7C0.7 19.798 6.2013 25.3 13 25.3V25ZM1 13H1.3C1.3 6.5327 6.53329 1.3 13 1.3V1V0.7C6.20193 0.7 0.7 6.2013 0.7 13H1ZM13 2.6748V2.3748C7.14078 2.3748 2.3748 7.14074 2.3748 13H2.6748H2.9748C2.9748 7.47211 7.47215 2.9748 13 2.9748V2.6748ZM2.6748 13H2.3748C2.3748 18.8593 7.14078 23.6252 13 23.6252V23.3252V23.0252C7.47215 23.0252 2.9748 18.5279 2.9748 13H2.6748ZM13 23.3252V23.6252C18.8592 23.6252 23.6252 18.8593 23.6252 13H23.3252H23.0252C23.0252 18.5279 18.5278 23.0252 13 23.0252V23.3252ZM23.3252 13H23.6252C23.6252 7.14074 18.8592 2.3748 13 2.3748V2.6748V2.9748C18.5278 2.9748 23.0252 7.47211 23.0252 13H23.3252ZM13 12.25V12.55C13.2999 12.55 13.5527 12.617 13.7196 12.7144C13.8865 12.8118 13.9324 12.9137 13.9324 12.9923L14.2324 12.9922L14.5324 12.9921C14.5323 12.6286 14.3015 12.3593 14.0221 12.1962C13.7426 12.0331 13.3793 11.95 13 11.95V12.25ZM14.2324 12.9922H13.9324V19.0107H14.2324H14.5324V12.9922H14.2324ZM14.2324 19.0107L13.9324 19.0106C13.9324 19.0522 13.8934 19.1618 13.7097 19.2787C13.5379 19.3881 13.2864 19.4686 13 19.4686V19.7686V20.0686C13.3927 20.0686 13.7573 19.9596 14.0319 19.7849C14.2946 19.6176 14.5323 19.3483 14.5324 19.0109L14.2324 19.0107ZM13 19.7686L13 19.4686C12.696 19.4685 12.4511 19.3871 12.291 19.2814C12.1237 19.1709 12.0842 19.0625 12.0842 19.0106L11.7842 19.0107L11.4842 19.0109C11.4843 19.338 11.6975 19.6085 11.9604 19.7821C12.2305 19.9605 12.5935 20.0685 13 20.0686L13 19.7686ZM11.7842 19.0107H12.0842V12.9922H11.7842H11.4842V19.0107H11.7842ZM11.7842 12.9922L12.0842 12.9923C12.0842 12.9028 12.1303 12.8033 12.2811 12.712C12.4366 12.6178 12.6824 12.55 13 12.55L13 12.25L13 11.95C12.6071 11.95 12.2451 12.0323 11.9703 12.1987C11.6909 12.368 11.4843 12.6395 11.4842 12.9921L11.7842 12.9922ZM12.999 7.29102V7.59102C13.5397 7.59102 13.9783 8.04793 13.9783 8.57227H14.2783H14.5783C14.5783 7.72543 13.8799 6.99102 12.999 6.99102V7.29102ZM14.2783 8.57227L13.9783 8.57217C13.9781 9.10187 13.5343 9.57109 12.999 9.57109V9.87109V10.1711C13.885 10.1711 14.578 9.4136 14.5783 8.57237L14.2783 8.57227ZM12.999 9.87109L12.9991 9.57109C12.4457 9.571 12.0043 9.09935 12.0041 8.57217L11.7041 8.57227L11.4041 8.57237C11.4044 9.41599 12.0997 10.1709 12.999 10.1711L12.999 9.87109ZM11.7041 8.57227H12.0041C12.0041 8.05042 12.4402 7.59111 12.9991 7.59102L12.999 7.29102L12.999 6.99102C12.1048 6.99117 11.4041 7.72309 11.4041 8.57227H11.7041Z" fill="#0268C6" mask="url(#path-1-outside-1_5448_14744)"/>
						</svg>
						<div class="tooltip">
							<div class="tooltip-close">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g opacity="0.5">
								<path d="M16.6663 3.33334L3.33301 16.6667M16.6663 16.6667L3.33301 3.33333" stroke="#191818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
								</svg>
							</div>
							<div class="tooltip-block">
								<div class="tooltip-block__title">
									<span>New</span>
								</div>
								<p class="tooltip-block__text">Brand new. Never been hit a single time</p>
							</div>
							<div class="tooltip-block">
								<div class="tooltip-block__title">
									<span>Average</span>
								</div>
								<p class="tooltip-block__text">Average play throughout the club head but all original design features still in tact. No skymarks, dents, dings, rattles.</p>
							</div>
							<div class="tooltip-block">
								<div class="tooltip-block__title">
									<span>Below Average</span>
								</div>
								<p class="tooltip-block__text">Club shows a little more wear then most but still has the overall integrity of the club in tact.</p>
							</div>
						</div>
					</div>
					<label>
						<strong>Quality (Condition Scale)</strong>
						<select id="widget_club_condition" onchange="update_price();">
							<option value='Below Average'>Below Average</option>
							<option selected value='Average'>Average</option>
							<option value='New'>New</option>
						</select>
					</label>
				<div id="premium-shaft">
			<?php if ($premium_shafts_available) {
					// Sort the premium shafts by post_title
					usort($premium_shafts_list, function($a, $b) {
						return strcmp($a->post_title, $b->post_title);
					});
				?>
				<label>
					<strong>Premium Shaft</strong>
					<select id="er_qm_premium_shaft" onchange="update_price();">
						<option value="none"><?php echo "None"; ?></option>
						<?php foreach ($premium_shafts_list as $shaft) { 
							$premium_shaft_price = get_post_meta($shaft->ID, 'premium-shaft-price', true) ? number_format( intval( get_post_meta($shaft->ID, 'premium-shaft-price', true) ), 2) : false;
							$premium_shaft_price = substr(filter_var($premium_shaft_price, FILTER_SANITIZE_NUMBER_INT), 0, -2)
							?>
							<option value="<?php echo $shaft->post_name; ?>" data-price="<?php echo $premium_shaft_price; ?>"><?php echo $shaft->post_title; ?></option>
						<?php } ?>
					</select>
				</label>
			<?php } ?>
				</div>

					<label>
						<strong>Quantity</strong>
						<input type="text" class="quoteQuantity" id="er_qm_quantity" value="1">
					</label>

					<?php if($_GET['type']=="Iron Set"){ ?>
						<label>
							<strong>Iron Set Quantity</strong>
							<select name='iron_set_quantity' class='iron_set_quantity' id='er_qm_iron_set_quantity' onchange="update_price();">
								<option name='iron_set_quantity' value='5'>5</option>
								<option name='iron_set_quantity' value='6'>6</option>
								<option name='iron_set_quantity' value='7'>7</option>
								<option name="iron_set_quantity" value="8" selected>8</option>
								<option name='iron_set_quantity' value='9'>9</option>
								<option name='iron_set_quantity' value='10'>10</option>
								<option name='iron_set_quantity' value='11'>11</option>
								<option name='iron_set_quantity' value='12'>12</option>
							</select>
						</label>
					<?php }	?>

<?php
$product_prices_raw = get_post_meta($order_postid, "product_prices", true);
$product_prices = json_decode($product_prices_raw, true);

// Перевіряємо тільки перший матеріал, чи є ціна "headonly" більша за 0
$head_only_price_new = false;
if (!empty($product_prices) && is_array($product_prices) && isset($product_prices[0])) {
    $first_material = $product_prices[0];
    if (!empty($first_material['headonly']) && $first_material['price'] > 0) {
        $head_only_price_new = true;
		$head_only_price = $first_material['price'];
    }
}
?>

<?php  if( $head_only_price != '0.00')  if($head_only_price || $head_only_price_new ) { ?>
    <label style="display: flex;">
        <input type="checkbox" class="er_qm_headonly" id="er_qm_headonly" autocomplete="off" onchange="update_price();">
        <strong style="margin: 10px;">Head Only?</strong>
    </label>
<?php } ?>

<?php
$product_prices_raw = get_post_meta($order_postid, "product_prices", true);
$product_prices = json_decode($product_prices_raw, true);
$club_type = get_post_meta($order_postid, 'club_type_field', true); 
$club_type = str_replace(' ', '', $club_type);					
$default_material = get_option("default_shaft_type_{$club_type}");

?>

<?php if (!empty($product_prices) && is_array($product_prices)) : ?>
    <div id="shafttype">
        <strong>Shaft Type:</strong>
        <select id="er_qm_shaft_type" onchange="update_price();" style="font-weight: bold;">
	        <?php foreach ($product_prices as $option) : 
	            // Якщо дефолтний матеріал співпадає з матеріалом в опції, встановлюємо його як вибраний
	            $is_selected = ($option['material'] === $default_material) ? 'selected' : ''; 
	        ?>
	            <option value="<?php echo esc_attr($option['material']); ?>" <?php echo $is_selected; ?>>
	                <?php echo esc_html($option['material']); ?>
	            </option>
	        <?php endforeach; ?>
	    </select>
    </div>
<?php elseif (!empty(get_post_meta($order_postid, "club_shaft_type", true))) : ?>
    <div id="shafttype">
        <strong>Shaft Type:</strong>
        <div style="font-weight: bold;" id="er_qm_shaft_type">
            <?php echo esc_html(get_post_meta($order_postid, "club_shaft_type", true)); ?>
        </div>
    </div>
<?php else : ?>
    <div id="shafttype" style="display: none !important;">
        <strong>Shaft Type:</strong>
        <div style="font-weight: bold;" id="er_qm_shaft_type">
           
        </div>
    </div>
	<style>
		#shafttype {
			display: none !important;
		}
					</style>
<?php endif; ?>

					<?php
$product_prices_raw = get_post_meta($order_postid, "product_prices", true);
$product_prices = json_decode($product_prices_raw, true);

if (!empty($product_prices) && is_array($product_prices)) {
    // Отримуємо ціну першого матеріалу
    $first_material_price = $product_prices[0]['price'];
} else {
    $first_material_price = $price; // Якщо ціна не знайдена або дані некоректні
}
?>

<div class="quotePrice" id="quoted-price">
    <strong>Club Value</strong>
    <div id="clubprice">
        $<span id="quoteprice"><?php echo number_format($first_material_price, 2); ?></span>
    </div>
</div>

					<button type="button" name="Add to Cart" value="Add to Cart" id="add-to-cart" class="quoteSubmit">
						<i class="fas fa-shopping-cart"></i> ADD TO CART
					</button>
				</div>
				<div id="club-promos">
					<div class="club-promo">
						<i class="fas fa-check-circle"></i>
						<div class="cp-text">
							<strong>Quick Club Submitting Process</strong>
							<p>24 Hour Payment Turnaround.</p>
						</div>
					</div>
					<div class="club-promo">
						<i class="fas fa-check-circle"></i>
						<div class="cp-text">
							<strong>Get Paid in Cash, not Store Credit</strong>
							<p>Recieve payment by Check or PayPal.</p>
						</div>
					</div>
					<div class="club-promo">
						<i class="fas fa-check-circle"></i>
						<div class="cp-text">
							<strong>15% Higher Cash Value!</strong>
							<p>Over 100,000 successful online transactions.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php quote_manager_get_faq_section(); ?>
	</main>
</div>

<?php
		get_footer();

		exit;
?>
