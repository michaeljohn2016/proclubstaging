<?php

	get_header();?>
		<div id="content-container" class="container_24">
			<div id="main-content" class="grid_24 <?php echo $content_position; ?>">
				<div class="main-content-padding">
					<div class="entry">
						<h1>Your Held Quotes</h1>
						<br>
						 <?php
							global $post;
							global $wpdb;
							require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
							global $current_user;
							get_currentuserinfo();
							$current_ID = $current_user->ID;
							$get_hold_IDs = $wpdb->get_results("SELECT m.post_id AS ID, DATE_FORMAT( p.post_date,  '%M %e, %Y' ) AS date
																FROM wp_posts p, wp_postmeta m
																WHERE p.ID = m.post_id
																AND p.post_author =1
																AND m.meta_value =  'On Hold'
																AND CURRENT_TIMESTAMP( ) - p.post_date <15");
							
							$list_of_IDs = "";
							foreach($get_hold_IDs as $row){
								if($list_of_IDs == ""){
									$list_of_IDs = $row->ID;
								} else {
									$list_of_IDs = $list_of_IDs.", ".$row->ID;
								}
							}
							if($list_of_IDs == ""){
								echo "No held orders were found - remember that orders are only held for two weeks.";
							} else {
								?>

								<?php
								
								
								foreach($get_hold_IDs as $row){
									$club_order_clubs = get_post_meta( $row->ID, 'club_order_clubs', true );
									echo "
									<div class='held-quote-wrapper'>
									<h3>".$row->date."</h3>
									<table id='quote-summary-table'>
										<tr id='quote-summary-table-row'>
											<th class='er_qm_shopping_cart_columns'>Manufacturer</th>
											<th class='er_qm_shopping_cart_columns'>Type</th>
											<th class='er_qm_shopping_cart_columns'>Model</th>
											<th class='er_qm_shopping_cart_columns'>Shaft</th>
											<th class='er_qm_shopping_cart_columns'>Condition</th>
											<th class='er_qm_shopping_cart_columns'>Quantity</th>
											<th class='er_qm_shopping_cart_columns'>Quote</th>
										</tr>";
									foreach($club_order_clubs as $item){
										$queried_post = get_post_meta($item['id']);
										echo "<tr class='item' style='padding-top:10px; height:25px;'>";
										echo "<td>".$queried_post['club_manufacturer'][0]."</td>";
										echo "<td>".$queried_post['club_type_field'][0]."</td>";
										echo "<td>".$queried_post['club_model'][0]."</td>";
										echo "<td>".$queried_post['club_shaft_type'][0]."</td>";
										echo "<td>".$queried_post['club_condition'][0]."</td>";
										echo "<td>".$item['quantity']."</td>";
										echo "<td> $".$queried_post['club_price'][0]."</td>";
										echo "</tr>";
									}
									echo "</table>";
									echo "<input type='button' id='".$row->ID."' class='continue-quote' value='Continue'></input>";
									echo "</div>";
								}
								
							}
						 ?>
					</div>
				</div>
			</div><!-- #content -->
		</div><!-- #primary -->
	<?php
	get_footer();
	exit;
?>