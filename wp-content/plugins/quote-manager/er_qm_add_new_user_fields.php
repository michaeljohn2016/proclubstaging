<?php
/*
Plugin Name: Add New User Fields
Description: Adds the user fields required for the quote manager to function
Version: 0.1
Author: ER - Luminary WS
*/

add_action ( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action ( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields ( $user )
{
?>
	<h3>Extra Account Information</h3>
	<table class="form-table">
		<tr>
			<th><label for="first_name">First Name</label></th>
			<td>
				<input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( get_the_author_meta( 'first_name', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please Enter Your First Name</span>
			</td>
		</tr>
		<tr>
			<th><label for="last_name">Last Name</label></th>
			<td>
				<input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( get_the_author_meta( 'last_name', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please Enter Your Last Name</span>
			</td>
		</tr>
		<tr>
			<th><label for="street_address">Street Address</label></th>
			<td>
				<input type="text" name="street_address" id="street_address" value="<?php echo esc_attr( get_the_author_meta( 'street_address', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please Enter Your Street Address</span>
			</td>
		</tr>
		<tr>
			<th><label for="city_address">City</label></th>
			<td>
				<input type="text" name="city_address" id="city_address" value="<?php echo esc_attr( get_the_author_meta( 'city_address', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please Enter Your City Address</span>
			</td>
		</tr>
		<tr>
			<th><label for="state_address">State</label></th>
			<td>
				<input type="text" name="state_address" id="state_address" value="<?php echo esc_attr( get_the_author_meta( 'state_address', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please Enter Your State Address</span>
			</td>
		</tr>
		<tr>
			<th><label for="zip_code_address">Zip Code</label></th>
			<td>
				<input type="text" name="zip_code_address" id="zip_code_address" value="<?php echo esc_attr( get_the_author_meta( 'zip_code_address', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please Enter Your Zip Code</span>
			</td>
		</tr>
		<tr>
			<th><label for="phone_number">Phone Number</label></th>
			<td>
				<input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr( get_the_author_meta( 'phone_number', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please Enter Your Phone Number</span>
			</td>
		</tr>
		<tr>
			<th><label for="zip_code_address">PayPal Email Address</label></th>
			<td>
				<input type="text" name="paypal_email" id="paypal_email" value="<?php echo esc_attr( get_the_author_meta( 'paypal_email', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Enter your PayPal Email Address If You'd Like To Receive Payment Via PayPal</span>
			</td>
		</tr>
	</table>
<?php
}

add_action ( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action ( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id )
{
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'first_name', $_POST['first_name'] );
	update_usermeta( $user_id, 'last_name', $_POST['last_name'] );
	update_usermeta( $user_id, 'street_address', $_POST['street_address'] );
	update_usermeta( $user_id, 'city_address', $_POST['city_address'] );
	update_usermeta( $user_id, 'state_address', $_POST['state_address'] );
	update_usermeta( $user_id, 'zip_code_address', $_POST['zip_code_address'] );
	update_usermeta( $user_id, 'paypal_email', $_POST['paypal_email'] );
	update_usermeta( $user_id, 'phone_number', $_POST['phone_number'] );
}

/**
 * Add cutom field to registration form
 */

add_action('register_form','show_first_name_field');
add_action('register_post','check_fields',10,3);
add_action('user_register', 'register_extra_fields');

function show_first_name_field()
{
?>
	<p>
	<label>First Name<br/>
	<input id="first_name" type="text" size="25" value="<?php echo $_POST['first_name']; ?>" name="first_name" />
	</label>
	</p>
	<p>
	<label>Last Name<br/>
	<input id="last_name" type="text" size="25" value="<?php echo $_POST['last_name']; ?>" name="last_name" />
	</label>
	</p>
	<p>
	<label>Street Address<br/>
	<input id="street_address" type="text" size="25" value="<?php echo $_POST['street_address']; ?>" name="street_address" />
	</label>
	</p>
	<p>
	<label>City<br/>
	<input id="city_address" type="text" size="25" value="<?php echo $_POST['city_address']; ?>" name="city_address" />
	</label>
	</p>
	<p>
	<label>State<br/>
	<input id="state_address" type="text" size="25" value="<?php echo $_POST['state_address']; ?>" name="state_address" />
	</label>
	</p>
	<p>
	<label>Zip Code<br/>
	<input id="zip_code_address" type="text" size="25" value="<?php echo $_POST['zip_code_address']; ?>" name="zip_code_address" />
	</label>
	</p>
	<p>
	<label>Phone Number<br/>
	<input id="phone_number" type="text" size="25" value="<?php echo $_POST['phone_number']; ?>" name="phone_number" />
	</label>
	</p>
	<p>
	<label>PayPal Email<br/>
	<input id="paypal_email" type="text" size="25" value="<?php echo $_POST['paypal_email']; ?>" name="paypal_email" />
	</label>
	</p>
<?php
}

function check_fields ( $login, $email, $errors )
{
	global $first_name;
	global $last_name;
	global $street_address;
	global $city_address;
	global $state_address;
	global $zip_code_address;
	global $paypal_email;
	global $phone_number;
	if ( $_POST['first_name'] == '' )
	{
		$errors->add( 'empty_realname', "<strong>ERROR</strong>: Please Enter Your First Name" );
	}
	if ( $_POST['last_name'] == '' )
	{
		$errors->add( 'empty_realname', "<strong>ERROR</strong>: Please Enter Your Last Name" );
	}
	if ( $_POST['street_address'] == '' )
	{
		$errors->add( 'empty_realname', "<strong>ERROR</strong>: Please Enter Your Street Address" );
	}
	else if($_POST['city_address'] == '')
	{
		$errors->add( 'empty_realname', "<strong>ERROR</strong>: Please Enter Your City Address" );
	}
	else if($_POST['state_address'] == '')
	{
		$errors->add( 'empty_realname', "<strong>ERROR</strong>: Please Enter Your State Address" );
	}
	else if($_POST['zip_code_address'] == '')
	{
		$errors->add( 'empty_realname', "<strong>ERROR</strong>: Please Enter Your Zip Code" );
	}
	else if($_POST['phone_number'] == '')
	{
		$errors->add( 'empty_realname', "<strong>ERROR</strong>: Please Enter Your Phone Number" );
	}
	else
	{
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$street_address = $_POST['street_address'];
		$city_address = $_POST['city_address'];
		$state_address = $_POST['state_address'];
		$zip_code_address = $_POST['zip_code_address'];
		$paypal_email = $_POST['paypal_email'];
		$phone_number = $_POST['phone_number'];

	}
}

function register_extra_fields ( $user_id, $password = "", $meta = array() )
{
	update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
	update_user_meta( $user_id, 'last_name', $_POST['last_name'] );
	update_user_meta( $user_id, 'street_address', $_POST['street_address'] );
	update_user_meta( $user_id, 'city_address', $_POST['city_address'] );
	update_user_meta( $user_id, 'state_address', $_POST['state_address'] );
	update_user_meta( $user_id, 'zip_code_address', $_POST['zip_code_address'] );
	update_user_meta( $user_id, 'paypal_email', $_POST['paypal_email'] );
	update_user_meta( $user_id, 'phone_number', $_POST['phone_number'] );
}

$result = add_role('er_qm_customer', 'Customer', array(
    'read' => true, // True allows that capability
    'edit_posts' => false,
    'delete_posts' => false, // Use false to explicitly deny
));


?>