<?php
/*
 *
 * beginning of embedded custom page template
 * This creates the Mobile Shipping Page
 *
*/
	get_header();

?>

	<div id="shipping-label-info">
		<h1>Shipping Label Information</h1>
			<h4>Please enter the dimensions of the parcel you plan to ship the clubs in (leave out the units).</h4>
			<label for="shipping_parcel_length">Length: </label>
			<input type="text" id="shipping_parcel_length"> In Inches</input><br>
			<label for="shipping_parcel_width">Width: </label>
			<input type="text" id="shipping_parcel_width"> In Inches</input><br>
			<label for="shipping_parcel_height">Height: </label>
			<input type="text" id="shipping_parcel_height"> In Inches</input><br>
			<label for="shipping_parcel_weight">Weight: </label>
			<input type="text" id="shipping_parcel_weight"></input> In Pounds<br>
	</div>
	<div id="er_qm_checkout_bottom_buttons">
		<input type="checkbox" id="er_qm_terms_agreement" name="agree_to_terms" value="I Agree">I Agree</input>	
		<input type="button" id="er_qm_checkout_finish" value="Proceed to Checkout"></input>
	</div>
	
<?php
			
	get_footer();
	exit;
?>