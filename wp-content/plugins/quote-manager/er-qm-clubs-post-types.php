<?php
	/*
	Plugin Name: Golf Clubs Post Type
	Description: Creates the club_types post type.
	Version: 0.1
	Author: ER - Luminary WS
	*/

	/* Set up the post types. */
	add_action("admin_init", "admin_init");
	add_action( 'init', 'boj_club_types_register_post_types' );
	add_filter("manage_edit-club_types_columns", "clubs_edit_columns");
	add_action("manage_posts_custom_column",  "clubs_custom_columns", 10, 2);
	add_action('save_post', 'save_details');
	add_action('admin_footer-edit.php', 'custom_bulk_action_for_clubs');
	add_action('wp_footer', 'custom_bulk_action_for_clubs' );
	// add_action('wp_footer', 'custom_javascript_for_clubs' );
	add_action('wp_ajax_mass_update_prices', 'mass_update_prices');
	
	
	function custom_bulk_action_for_clubs() {

		global $post_type;

		if($post_type == 'club_types') {

		?>
					<script type="text/javascript">
						jQuery(document).ready(function($) {
							jQuery('<option>').val('savePrice').text('<?php _e('Mass Save Price')?>').appendTo("select[name='action']");
							jQuery('<option>').val('savePrice').text('<?php _e('Mass Save Price')?>').appendTo("select[name='action2']");

							$("#doaction").click(function(){
								if($('[name="action"] option:selected').text()=="Mass Save Price"){
									var pricesToUpdate = new Array();
									var rows = $(".wp-list-table tr:gt(1)"); // skip the header row
									var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
									
									rows.each(function(index) {
										var id =  parseInt($(this).attr("id").substring(5));
										var price =  parseInt($("td:nth-child(7) input", this).val());
										//alert($("td:nth-child(1) input", this).val());
										var rowArray = [id, price];
										pricesToUpdate.push(rowArray);

									});
									var encodedPrices = JSON.stringify(pricesToUpdate)
									var data = {
										action: 'mass_update_prices',
										prices: encodedPrices
									};
									
									$.post(ajaxurl, data, function(response) {
										alert("All Prices Updated!");
									});
									
									//alert(pricesToUpdate);
									
								}
								
							});

					});
					</script>
		<?php
		
		}
	}
	
	function mass_update_prices(){
		$prices_to_save = json_decode($_POST['prices'],true);
		foreach($prices_to_save as $datum){
			update_post_meta($datum[0], "club_price", $datum[1]);
		}
		die($newArray);
	}
	
	function admin_init(){
		//add_meta_box("club_id-meta", "Club ID", "club_id", "club_types", "normal", "low");
		//add_meta_box("club_manufacturer-meta", "Manufacturer", "club_manufacturer", "club_types", "normal", "low");
		//add_meta_box("club_type_field-meta", "Club Type", "club_type_field", "club_types", "normal", "low");
		//add_meta_box("club_model-meta", "Club Model", "club_model", "club_types", "normal", "low");
		//add_meta_box("club_shaft_type-meta", "Club Shaft Type", "club_shaft_type", "club_types", "normal", "low");
		//add_meta_box("club_condition-meta", "Club Condition", "club_condition", "club_types", "normal", "low");
		//add_meta_box("club_price-meta", "Club Price", "club_price", "club_types", "normal", "low");
		
	}
	/* Registers post types. */
	function boj_club_types_register_post_types() {

		/* Set up the arguments for the 'music_album' post type. */
		$club_args = array(
			'public' => true,
			'query_var' => 'club_types',
			'rewrite' => false,
			'supports' => array(
				'title',
				'thumbnail'
			),
			'labels' => array(
				'name' => 'Clubs',
				'singular_name' => 'Club',
				'add_new' => NULL,         // Hides link text to prevent clients from adding new clubs
				'add_new_item' => NULL,    // This way they have to use the excel sheets
				'edit_item' => 'Edit Club',
				'new_item' => 'New Club',
				'view_item' => 'View Club',
				'search_items' => 'Search Clubs',
				'not_found' => 'No Clubs Found',
				'not_found_in_trash' => 'No Clubs Found In Trash'
			),
			/*'capabilities' => array(     // Completely removes links to add new clubs
				'create_posts' => false,
			)*/
		);

		/* Register the music album post type. */
		register_post_type( 'club_types', $club_args );
	}
	
	add_filter('manage_edit-club_types_sortable_columns', 'register_club_types_sortable_columns');
	function register_club_types_sortable_columns( $columns ){
		$columns['club_manufacturer'] = 'club_manufacturer';
		$columns['club_type_field'] = 'club_type_field';
		return $columns;
	}


	function clubs_edit_columns($columns){
	  $columns = array(
		"cb" => "<input type='checkbox'/>",
		"title" => __('Club ID'),
		"club_manufacturer" => __("Manufacturer"),
		"club_type_field" => __("Club Type"),
		"club_model" => __("Model"),
		"club_shaft_type" => __("Shaft Type"),
		"club_price" => __("Average Price")
	  );
	 
	  return $columns;
	}
	


	function clubs_custom_columns($column, $id){
	  global $post;
	 
	  switch ($column) {
	  	case "club_id":
		  $custom = get_post_custom();
		  echo $custom["club_id"][0];
		  echo $movie_director;
		  break;
		case "club_manufacturer":
		  $custom = get_post_custom();
		  echo $custom["club_manufacturer"][0];
		  break;
		case "club_type_field":
		  $custom = get_post_custom();
		  echo $custom["club_type_field"][0];
		  break;
		case "club_model":
		  $custom = get_post_custom();
		  echo $custom["club_model"][0];
		  break;
		case "club_shaft_type":
		  $custom = get_post_custom();
		  echo $custom["club_shaft_type"][0];
		  break;
		case "club_price":
		  $custom = get_post_custom();
		  echo "$<input type='text' id='admin-side-club-price' value='".$custom["club_price"][0]."' size='4' style='text-align:center'></input>";
		  break;
	  }
	  
	}
	
	

if( is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'club_types' ) {
    add_filter('request', 'js_qm_filter_club_type_request');
    add_filter('restrict_manage_posts', 'js_qm_filter_club_type');
}

function js_qm_filter_club_type_request($request) {
    if( isset($_GET['club_type_field']) && !empty($_GET['club_type_field']) ) {
        $request['meta_key'] = 'club_type_field';
        $request['meta_value'] = $_GET['club_type_field'];
    }
    return $request;
}

function js_qm_filter_club_type() {
    global $wpdb;
    $types = $wpdb->get_col("
        SELECT DISTINCT meta_value
        FROM ". $wpdb->postmeta ."
        WHERE meta_key = 'club_type_field'
        ORDER BY meta_value
    ");
    ?>
    <select name="club_type_field" id="club_type_field">
        <option value="">Show all</option>
        <?php foreach ($types as $type) { ?>
        <option value="<?php echo esc_attr( $type ); ?>" <?php if(isset($_GET['club_type_field']) && !empty($_GET['club_type_field']) ) selected($_GET['club_type_field'], $type); ?>><?php echo esc_attr($type); ?></option>
        <?php } ?>
    </select>
    <?php
}

	
	
	
	function save_details(){
	  global $post;
	 
	 // Bail if we're doing an auto save  
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
		
	//   update_post_meta($post->ID, "club_manufacturer", $_POST["club_manufacturer"]);
	//   update_post_meta($post->ID, "club_type_field", $_POST["club_type_field"]);
	//   update_post_meta($post->ID, "club_model", $_POST["club_model"]);
	//   update_post_meta($post->ID, "club_shaft_type", $_POST["club_shaft_type"]);
	//   //update_post_meta($post->ID, "club_condition", $_POST["club_condition"]);
	//   update_post_meta($post->ID, "club_price", $_POST["club_price"]);
	}
?>