<?php
if(!is_user_logged_in()) {
	header("Location: /wp-login.php?redirect_to=" . rawurlencode('/?accounthistory'));
	die();
}
		/*
		 *
		 * beginning of embedded custom page template
		 * This creates the Review Your Order Page
		 *
		*/
		get_header();
		//do_action('udesign_above_page_content'); 
		//load_template( $_template_file, $require_once )
		

						global $wpdb;
						$querydata = $wpdb->get_results(
				"
					SELECT * FROM wp_posts WHERE post_type='club_orders' AND post_author='".get_current_user_id()."' ORDER BY post_date DESC
				"
			);
						?>
			
		 
			
	<div id="primary" class="site-content">
		<div id="content" role="main">
				<h2>Order History</h2>
					<table style="margin-left: auto; margin-right: auto; margin-top: 10px; width:100%; font-size:12px;">
						<thead>
							<th style="font-weight: bold !important;">Order ID</th>
							<th style="font-weight: bold !important;">Order Date</th>
							<th style="font-weight: bold !important;">Order Amount</th>
							<th style="font-weight: bold !important;">Order Status</th>
							<th style="font-weight: bold !important;">Payment Method</th>
						</thead>
						<tbody>
							<?php
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
				$time = strtotime($order->post_date);
				$time = date("M jS, Y g:i A", $time);
				$items = unserialize($aa["club_order_clubs"]);
							?>
								<tr>
									<td style="padding-top:5px; padding-bottom:5px; width:103px;"><a href="#" onclick="if(jQuery('#<?php echo $order->post_title; ?>').css('display') == 'none'){jQuery('#<?php echo $order->post_title; ?>').show();}else{jQuery('#<?php echo $order->post_title; ?>').hide();}"><?php echo $order->post_title; ?></a></td>
									<td style="padding-top:5px; padding-bottom:5px; width:167px;"><?php echo $time; ?></td>
									<td style="padding-top:5px; padding-bottom:5px; width:148px;">$<?php echo number_format($aa["club_order_total"], 2); ?></td>
									<td style="padding-top:5px; padding-bottom:5px; width:171px;"><?php echo $aa["club_order_status"] == 'On Hold' ? 'On Hold <div><a style="font-size: 10px;" href="#" class="continue-quote" id="'.$order->ID.'" onclick="return false;">(Continue Quote)</a><br><a style="font-size: 10px;" href="#" class="delete-quote" id="'.$order->ID.'" onclick="return false;">(Delete Quote)</a></div>' : $aa["club_order_status"]; ?></td>
									<td style="padding-top:5px; padding-bottom:5px; width:130px;"><?php echo $aa["club_order_payment"]; ?></td>
								</tr>
								</tbody>
								<tbody style="border:1px; border-style:solid; display:none; text-size:12px;" id="<?php echo $order->post_title; ?>">
								<tr style="border-bottom:1px; border-style:solid;" >
									<td style='padding-top:5px; padding-bottom:5px; width:103px;'><b>Manufacturer</b></td>
									<td style='padding-top:5px; padding-bottom:5px; width:167px;'><b>Type</b></td>
									<td style='padding-top:5px; padding-bottom:5px; width:198px;'><b>Condition/Model/Shaft</b></td>
									<td style='padding-top:5px; padding-bottom:5px; width:101px;'><b>Quantity</b></td>
									<td style='padding-top:5px; padding-bottom:5px; width:130px;'><b>Quote</b></td>
								</tr>
									<?php
									if(isset($items)){
										$order_total = 0;
										foreach($items as $item){
											$queried_post = get_post_meta($item['id']);
											echo "<tr class='item'>";
												echo "<td style='padding-top:5px; padding-bottom:5px; width:103px;'>".$queried_post['club_manufacturer'][0]."</td>";
												echo "<td style='padding-top:5px; padding-bottom:5px; width:167px;'>".$queried_post['club_type_field'][0]."</td>";
												echo "<td style='padding-top:5px; padding-bottom:5px; width:198px;'>".$item['condition'].'/'.$queried_post['club_model'][0].'/'.$item['shaft']."</td>";
												echo "<td style='padding-top:5px; padding-bottom:5px; width:101px;'>".$item['quantity']."</td>";
												echo "<td style='padding-top:5px; padding-bottom:5px; width:130px;'> $".$item['price']."</td>";
											echo "</tr>";
											$order_total = $order_total+($item['price']*$item['quantity']);
											
											$_SESSION['ordertotal'] = $order_total;
											
									}
									echo("</tbody>");
									//echo "<br><h1 id='er_qm_order_total'> Order Total $".number_format($order_total, 2)."</h1></div>";
								}
							}
							?>
						
					</table>
					</div>
					</div>

		<?php //get_sidebar(); 
		 get_footer(); 

		exit;

?>