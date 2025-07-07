<?php
error_reporting(-1);
ini_set('display_errors', 0);
/*
Plugin Name: Users to Customers 
Description: Alters the default Users admin menu item to Customers
Version: 0.1
Author: ER - Luminary WS
*/

function change_users_menu_label() {
    global $menu;
    global $submenu;
    $menu[70][0] = 'Customers';
    $submenu['users.php'][5][0] = 'Customers';
    $submenu['users.php'][10][0] = 'Add Customers';
    $submenu['users.php'][15][0] = 'Profile'; // Change name for categories
    $submenu['users.php'][16][0] = 'Export Customers to CSV'; // Change name for tags
    echo '';
}


    add_action( 'admin_menu', 'change_users_menu_label' );
	
	// Adds custom column filter to the "Customers" table
	function test_modify_user_table( $column ) {
		$column['latest_transaction'] = 'Latest Transaction';
		$column['number_of_transactions'] = '# of Transactions';
	 
		return $column;
	}
	add_filter( 'manage_users_columns', 'test_modify_user_table' );
	
	// Retrieves info for custom columns
	function test_modify_user_table_row( $val, $column_name, $user_id ) {
		global $wpdb;
		$user = get_userdata( $user_id );
		$userID =  $user->id;
		
		$query_for_number_of_transactions = $wpdb->get_results(
				"
					SELECT COUNT(post_author) AS number_of_transactions FROM wp_posts WHERE post_type='club_orders' AND post_author=$userID;
				"
		);
		
		$query_for_latest_transaction = $wpdb->get_results(
				"
					SELECT DATE_FORMAT((MAX(post_date)), '%M %e %Y') AS latest_transaction FROM wp_posts WHERE post_type='club_orders' AND post_author=$userID;
				"
		);
		
		$er_qm_number_query_result = $query_for_number_of_transactions[0];
		$er_qm_transaction_query_result = $query_for_latest_transaction [0];
		
		switch($column_name){
			case 'latest_transaction':
				return $er_qm_transaction_query_result->latest_transaction;
				break;
			case 'number_of_transactions':
				return $er_qm_number_query_result->number_of_transactions;
				break;
			default:
		}
		return "Error";
		
	}
 
	add_filter( 'manage_users_custom_column', 'test_modify_user_table_row', 10, 3 );
	
	// Remove the "Posts" column on the "Customers" table
	add_action('manage_users_columns','remove_user_posts_column');
	function remove_user_posts_column($column_headers) {
		unset($column_headers['posts']);
		return $column_headers;
	}
	
	// Make custom columns on customers table sortable
	function user_sortable_columns( $columns ) {
		$columns['number_of_transactions'] = '# of Transactions';
		return $columns;
	}
	add_filter( 'manage_users_sortable_columns', 'user_sortable_columns' );
	
	function user_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'number_of_transactions' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'number_of_transactions',
			'orderby' => 'meta_value',
			'order'     => 'asc'
		) );
	}
	return $vars;
	}
	add_filter( 'request', 'user_column_orderby' );
?>