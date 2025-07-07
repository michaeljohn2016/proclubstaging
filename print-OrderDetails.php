<?php
date_default_timezone_set('America/Phoenix');
include("wp-load.php");
$post = get_post($_GET["order"]);
$custom = get_post_custom($_GET["order"]);
$clubs = get_post_meta($_GET["order"], 'club_order_clubs', true);

$custMeta = get_user_meta($post->post_author);
?>
<!DOCTYPE html>
<html>
<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script>
	$(function() {
		window.print();
	});
	</script>
</head>
<body>
	<div class="container" style="max-width: 768px;">
		<div class="col-sm-8 text-left">
			<h1 style="margin-top: 35px; margin-bottom: 0;">Order Details <small>#<?php echo $_GET["order"]; ?></small></h1>
		</div>
		<div class="col-sm-4 text-right">
			<img src="https://sell.proclubs.com/wp-content/themes/diztinct/src/images/proclubs-logo.png" class="img-responsive">
		</div>
		<div class="clearfix"></div>
		<hr>
		<div style="font-size: 13px;" class="col-sm-3">
			<strong>ORDER DATE</strong>
			<div style="color: #999; font-weight: bold;">
				<?php echo date('M jS Y @ g:i A', strtotime($post->post_date) - 25200); ?>
			</div>
		</div>
		<div style="font-size: 13px;" class="col-sm-3">
			<strong>ORDER STATUS</strong>
			<div style="color: #999; font-weight: bold;">
				<?php echo $custom['club_order_status'][0]; ?>
			</div>
		</div>
		<div class="clearfix"></div>
		<hr>
		<div class="col-sm-6">
			<h5>CUSTOMER DETAILS</h5>
			<table class="table table-condesned table-striped">
				<tr>
					<td><strong>Name</strong></td>
					<td><?php echo $custMeta['first_name'][0] . ' ' . $custMeta['last_name'][0]; ?></td>
				</tr>
				<tr>
					<td><strong>Email Address</strong></td>
					<td><?php echo get_the_author_meta('user_email', $post->post_author); ?></td>
				</tr>
				<tr>
					<td><strong>Phone Number</strong></td>
					<td><?php echo $custMeta['phone_number'][0]; ?></td>
				</tr>
				<tr>
					<td><strong>Address</strong></td>
					<td><?php echo $custMeta['street_address'][0]; ?><br><?php echo $custMeta['city_address'][0] . ', ' . $custMeta['state_address'][0] . ' ' . $custMeta['zip_code_address'][0]; ?></td>
				</tr>
			</table>
		</div>
		<div class="col-sm-6">
			<h5>PAYMENT DETAILS</h5>
			<table class="table table-condesned table-striped">
				<tr>
					<td><strong>Payment Method</strong></td>
					<td><?php echo $custom['club_order_payment'][0]; ?></td>
				</tr>
				<tr>
					<td><strong><?php echo $custom['club_order_payment'][0] == 'Check' ? 'Send Check To' : 'PayPal Email'; ?></strong></td>
					<td><?php
						if($custom['club_order_payment'][0] == 'Check') {
							echo implode('<br>', explode('&#13;&#10;', $custom['club_order_address'][0]));
						}
						else {
							echo $custom['club_order_paypal_email'][0];
						}
						?></td>
				</tr>
			</table>
		</div>
		<div class="clearfix"></div>
		<table class="table table-striped">
			<thead>
				<th>Manufacturer</th>
				<th>Type</th>
				<th>Model</th>
				<th>Shaft</th>
				<th>Condition</th>
				<th>Quantity</th>
				<th>Unit Price</th>
				<th>Total</th>
			</thead>
			<tbody>
		<?php
						foreach($clubs as $item){
								$queried_post = get_post_meta($item['id']);
								?>
								<tr>
									<?php
									echo "<td>".$queried_post['club_manufacturer'][0]."</td>";
									echo "<td>".$queried_post['club_type_field'][0]."</td>";
									echo "<td>".$queried_post['club_model'][0]."</td>";
									if ($item['premiumShaft'] !== "false" && $item['premiumShaft'] !== "none") { $item['shaft'] = pc_get_shaft_friendly_name($item['premiumShaft']);}
									echo $item['headOnly'] == "true" ? "<td>Head Only</td>" : "<td>".$item['shaft']."</td>";									echo "<td>".$item['condition']."</td>";
									echo "<td>".$item['quantity']."</td>";
									echo "<td> $".$item['price']."</td>";
									echo "<td> $".number_format((str_replace( ',', '', $item['price'] ) * $item["quantity"]), 2)."</td>";

								?>
								</tr>
								<?php
						}
			?>
			</tbody>
		</table>
		<div class="col-sm-6">
		</div>
		<div class="col-sm-6 text-right">
			<h4>Order Total: $<?php echo number_format($custom['club_order_total'][0], 2); ?></h4>
		</div>
	</div>
</body>
</html>