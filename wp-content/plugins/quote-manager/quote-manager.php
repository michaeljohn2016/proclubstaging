<?php
	/*
	Plugin Name: Quote Manager
	Description: An easy-to-manage system for giving quotes, checkouts, shipping labels, and PayPal transactions to customers
	Version: 0.1
	Author: Eric Rosas - Luminary WS
	*/


	register_activation_hook(__FILE__, 'er_qm_install');
	register_deactivation_hook(__FILE__, 'er_qm_deactivate');
	add_action('admin_menu', 'er_qm_add_admin_views');
	add_action('init', 'er_qm_clubs_test_post_type');
//	add_action( 'admin_footer', 'er_qm_admin_chart_js' );
//	add_action('wp_ajax_populate_admin_line_chart_dates', 'populate_admin_line_chart_dates');
//	add_action('wp_ajax_populate_admin_line_chart_transactions', 'populate_admin_line_chart_transactions');
//	add_action('wp_ajax_populate_admin_pie_chart', 'populate_admin_pie_chart');
	add_action( 'admin_init', 'er_qm_chart_admin_init' );
	


		function er_qm_chart_admin_init() {
        /* Register our script. */
        wp_register_script( 'chart-js', plugins_url( 'chart-js/Chart.min.js', __FILE__ ) );
		 wp_enqueue_script( 'chart-js' );
		 
		 wp_register_script( 'print-order-js', plugins_url( 'print-order.js', __FILE__ ) );
		 wp_enqueue_script( 'print-order-js' );
		 
		 
    }
	/*
	SELECT "Awaiting Clubs", SUM(CASE WHEN meta_value = "Awaiting Clubs" THEN 1 ELSE 0 END) Awaiting_Clubs
	FROM wp_postmeta m, wp_posts p
	WHERE MONTH( p.post_date ) = MONTH( NOW( ) ) 
	AND p.ID = m.post_id
	UNION
	SELECT "Recieved", SUM(CASE WHEN meta_value= "Recieved" THEN 1 ELSE 0 END) Recieved
	FROM wp_postmeta m, wp_posts p
	WHERE MONTH( p.post_date ) = MONTH( NOW( ) ) 
	AND p.ID = m.post_id
	UNION
	SELECT "Partially Received", SUM(CASE WHEN meta_value= "Partially Received" THEN 1 ELSE 0 END) Partially_Received
	FROM wp_postmeta m, wp_posts p
	WHERE MONTH( p.post_date ) = MONTH( NOW( ) ) 
	AND p.ID = m.post_id
	UNION
	SELECT "Paid", SUM(CASE WHEN meta_value = "Paid" THEN 1 ELSE 0 END) Paid
	FROM wp_postmeta m, wp_posts p
	WHERE MONTH( p.post_date ) = MONTH( NOW( ) ) 
	AND p.ID = m.post_id
	UNION
	SELECT "Cancelled", SUM(CASE WHEN meta_value= "Cancelled" THEN 1 ELSE 0 END) Cancelled
	FROM wp_postmeta m, wp_posts p
	WHERE MONTH( p.post_date ) = MONTH( NOW( ) ) 
	AND p.ID = m.post_id
	*/
	function populate_admin_pie_chart(){
	
		global $wpdb;
		$pieData= array();
		
		$querydata = $wpdb->get_results(
					"
						SELECT COUNT( m.post_id ) AS amount,
						m.meta_value AS STATUS 
						FROM wp_posts p, wp_postmeta m
						WHERE p.post_type =  'club_orders'
						AND p.ID = m.post_id
						AND p.post_status !=  'trash'
						AND m.meta_key =  'club_order_status'
						AND m.meta_value !=  ''
						AND MONTH( p.post_date ) = MONTH( NOW( ) ) 
						GROUP BY STATUS 
						ORDER BY STATUS ASC
					"
		);
		
		foreach($querydata as $datum){
			array_push($pieData, $datum->amount);
		}
		
		die(json_encode($pieData));
	}
	
	function populate_admin_line_chart_dates(){
		global $wpdb;
		$transactions = array();
		$dates = array();
		$querydata = $wpdb->get_results(
					"
						SELECT COUNT( post_author ) AS transactions, 
						DATE_FORMAT( post_date,  '%D' ) AS days, 
						ID
						FROM wp_posts
						WHERE MONTH( post_date ) = MONTH( NOW( ) ) 
						AND post_type =  'club_orders'
						AND post_status !=  'trash'
						GROUP BY days
						ORDER BY ID ASC 
					"
		);
		
		foreach($querydata as $datum){
			array_push($transactions, $datum->transactions);
			array_push($dates, $datum->days);
		}
		
		die(json_encode($dates));
		
	}

	function populate_admin_line_chart_transactions(){
		global $wpdb;
		$transactions = array();
		$dates = array();
		$querydata = $wpdb->get_results(
					"
						SELECT COUNT( post_author ) AS transactions, DATE_FORMAT( post_date,  '%D' ) AS days, ID
						FROM wp_posts
						WHERE MONTH( post_date ) = MONTH( NOW( ) ) 
						AND post_type =  'club_orders'
						AND post_status !=  'trash'
						GROUP BY days
						ORDER BY ID ASC;
					"
		);
		
		foreach($querydata as $datum){
			array_push($transactions, $datum->transactions);
			array_push($dates, $datum->days);
		}
		
		die(json_encode($transactions));
		
	}

	function er_qm_admin_chart_js(){
		?>
			<script type="text/javascript" >
				jQuery(document).ready(function($) {
					
					//Get context with jQuery - using jQuery's .get() method.
					if($("#transactions_per_month_chart").length){
						var ctx = $("#transactions_per_month_chart").get(0).getContext("2d");
						//This will get the first returned node in the jQuery collection.
						var myNewChart = new Chart(ctx);
						
						// AJAX request to get the number of transactions for this month
						var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
						var lineChartDates = [];
						var lineChartTransactions = [];
						var data = {
								action: 'populate_admin_line_chart_dates'
						};
						$.post(ajaxurl, data, function(response) {
							//alert(response);
							$.each(JSON.parse(response), function(i, obj){
								lineChartDates[i] = String(obj);
							});
							getLineTransactionalData()
						});
						//alert(lineChartDates);
						
						var dataTwo = {
								action: 'populate_admin_line_chart_transactions'
						};
						
						function getLineTransactionalData(){
							$.post(ajaxurl, dataTwo, function(response) {
								//alert(response);
								$.each(JSON.parse(response), function(i, obj){
									lineChartTransactions[i] = parseInt(obj);
								});
								// Create the line chart object
								var dataThree = {
									labels : lineChartDates,
									datasets : [
										{
											fillColor : "rgba(220,220,220,0.5)",
											strokeColor : "rgba(220,220,220,1)",
											pointColor : "rgba(220,220,220,1)",
											pointStrokeColor : "#fff",
											data : lineChartTransactions
										}
									]
								}
								//alert(lineChartTransactions);
								var adminLineChart = new Chart(ctx).Line(dataThree);
								//alert(lineChartDates);
							});
						}

						
						
						// beginning of pie chart
						var context = $("#transactions_per_month_statuses").get(0).getContext("2d");
						//This will get the first returned node in the jQuery collection.
						var myNewPieChart = new Chart(context);
						
						// AJAX request to get the number of transactions for this month
						var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
						var pieChartValues = [];
						var dataPie = {
								action: 'populate_admin_pie_chart'
						};
						$.post(ajaxurl, dataPie, function(response) {
							//alert(response);
							$.each(JSON.parse(response), function(i, obj){
								pieChartValues[i] = parseInt(obj);
							});
							var dataFour = [
							{
								value: pieChartValues[0],
								color:"#F38630"
							},
							{
								value : pieChartValues[1],
								color : "#E0E4CC"
							},
							{
								value : pieChartValues[2],
								color : "#69D2E7"
							},
							{
								value : pieChartValues[3],
								color : "#E883E1"
							},
							{
								value : pieChartValues[4],
								color : "#54BC57"
							}								
						];
						var piechart = new Chart(context).Pie(dataFour);
						});
						

					}
				});
			</script>
		<?php
	}
	
	
	
	if(!class_exists('WP_List_Table')){
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	function er_qm_install(){
		// add default values(currently not needed)
		/*
		add_action('plugins_loaded', 'er_qm_plugin_setup');
		add_action('init', 'er_qm_init');
		
		
		// create database tables
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$tablename = $wpdb->prefix . "order_line_items";
		
		$sql = "CREATE TABLE $tablename (
				order_id int(8) NOT NULL PRIMARY KEY,
				product_id int(8) NOT NULL,
				quantity int(2) NOT NULL
		);";
	
		dbDelta($sql);
		
		$tablename = $wpdb->prefix . "addresses";
		
		$sql = "CREATE TABLE $tablename (
				address_id int(8) NOT NULL PRIMARY KEY,
				user_id int(8) NOT NULL,
				street_one varchar(25) NOT NULL,
				street_two varchar(25),
				city varchar(25) NOT NULL,
				state varchar(25) NOT NULL,
				zipcode varchar(25) NOT NULL
		);";
		
		dbDelta($sql);
		
		$tablename = $wpdb->prefix . "products";
		
		$sql = "CREATE TABLE $tablename(
				product_id int(8) NOT NULL PRIMARY KEY,
				club_type varchar(20) NOT NULL,
				model varchar(20) NOT NULL,
				material varchar(20) NOT NULL,
				manufacturer varchar(40) NOT NULL
		);";
		
		dbDelta($sql);
		
		$tablename = $wpdb->prefix . "product_prices";
		
		$sql = "CREATE TABLE $tablename(
			product_id int(8) NOT NULL PRIMARY KEY,
			cond varchar(20) NOT NULL,
			price int(3) NOT NULL
		);";
		
		dbDelta($sql);
		
		$tablename = $wpdb->prefix . "orders";
		
		$sql = "CREATE TABLE $tablename (
				order_ID int(8) NOT NULL PRIMARY KEY,
				user_id int(8) NOT NULL,
				order_date datetime,
				order_total varchar(4) NOT NULL,
				status int(1) NOT NULL,
				payment_choice int(1) NOT NULL,
				paypal_email varchar(25), 
				payment_id int(8) NOT NULL
		);";
		
		dbDelta($sql);
		
		$tablename = $wpdb->prefix . "order_shipments";
		
		$sql = "CREATE TABLE $tablename (
				order_id int(8) NOT NULL PRIMARY KEY,
				shipment_id int(8) NOT NULL,
				courier int(1) NOT NULL,
				status int(1) NOT NULL
		);";
		
		dbDelta($sql);
		
		//$wpdb->print_error();
		*/
		
	}
	
	function er_qm_clubs_test_post_type(){
		/*
		$club_args = array(
			'public' => false,
			'query_var' => 'club_types',
			'rewrite' => false,
			'supports' => array(
				'title',
				'editor'
			),
			'labels' => array(
				'name' => 'Clubs'
				'singular_name' => 'Club',
				'add_new' => 'Add New Club',
				'add_new_item' => 'Add New Club',
				'edit_item' => 'Edit Club',
				'new_item' => 'New Club',
				'view_item' => 'View Club',
				'search_items' => 'Search Clubs',
				'not_found' => 'No Clubs Found',
				'not_found_in_trash' => 'No Clubs Found In Trash'
			)
		);
		
		register_post_type('club_types', $club_args);
		*/
	}

	function er_qm_deactivate(){
		// hide admin views

		
	}
	
	function er_qm_plugin_setup(){
		
	}
	
	function er_qm_init(){
		// final set up
		
	}
	
	function er_qm_add_admin_views(){
		// create the views for admin
		add_menu_page('Quote Manager', 'Quote Manager', 'manage_options', __FILE__, 'er_qm_dashboard');
	}

	add_action('admin_init', 'register_qmsettings');

	function register_qmsettings(){
		register_setting('qm-settings-group', 'qm-good-through');
	}
	
	function er_qm_dashboard(){

		// Count the Number of Golftec Orders

		$args = array(
			'post_type' => 'club_orders',
			'meta_key' => 'order_source',
			'meta_value'   => 'golftec'
		 );
		 $golftec_query = new WP_Query($args);

		 $golftec_orders = $golftec_query->found_posts;

		?>
		
	
		<div class="wrap">
			<!-- Chart.js Dashboard -->
			<div id="icon-options-general" class="icon32"><br/></div>
			<h1>ProClubs Statistics</h1>

			<p><strong>Golftec Orders to date:</strong> <?php echo $golftec_orders; ?> </p>
			

			<h2>Options</h2>
			<form action="options.php" method="post">
				<?php settings_fields('qm-settings-group'); ?>
				<?php do_settings_sections('qm-settings-group'); ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
		    				<th scope="row">
		    					<label for="qm-good-through">Good through date</label>
		    				</th>
		    				<td>
		    					<input type="text" name="qm-good-through" id="qm-good-through" value="<?php echo get_option('qm-good-through'); ?>">
		    				</td>
		    			</tr>
					</tbody>
				</table>
				<?php submit_button();?>
			</form>
		</div>
        
        <div>
        
     
        </div>
        
		<?php
		
	}


	/*
	 *
	 * Beginning of Admin Dashboard View Processing For Latest Orders
	 *
	 *
	*/
	class er_qm_orders_view extends WP_List_Table {
		
		/** ************************************************************************
		 * REQUIRED. Set up a constructor that references the parent constructor. We 
		 * use the parent reference to set some default configs.
		 ***************************************************************************/
		function __construct(){
			global $status, $page;
					
			//Set parent defaults
			parent::__construct( array(
				'singular'  => 'order',     //singular name of the listed records
				'plural'    => 'orders',    //plural name of the listed records
				'ajax'      => false        //does this table support ajax?
			) );
			
		}
		
		
		/** ************************************************************************
		 * Recommended. This method is called when the parent class can't find a method
		 * specifically build for a given column. Generally, it's recommended to include
		 * one method for each column you want to render, keeping your package class
		 * neat and organized. For example, if the class needs to process a column
		 * named 'title', it would first see if a method named $this->column_title() 
		 * exists - if it does, that method will be used. If it doesn't, this one will
		 * be used. Generally, you should try to use custom column methods as much as 
		 * possible. 
		 * 
		 * Since we have defined a column_title() method later on, this method doesn't
		 * need to concern itself with any column with a name of 'title'. Instead, it
		 * needs to handle everything else.
		 * 
		 * For more detailed insight into how columns are handled, take a look at 
		 * WP_List_Table::single_row_columns()
		 * 
		 * @param array $item A singular item (one full row's worth of data)
		 * @param array $column_name The name/slug of the column to be processed
		 * @return string Text or HTML to be placed inside the column <td>
		 **************************************************************************/
		 
		function column_default($item, $column_name){
			switch($column_name){
				case 'ID':
				case 'post_date':
				case 'club_order_total':
				case 'club_order_status':
				case 'club_order_payment':
					return $item[$column_name];
				default:
					return print_r($item,true); //Show the whole array for troubleshooting purposes
			}
		}
		
			
		/** ************************************************************************
		 * Recommended. This is a custom column method and is responsible for what
		 * is rendered in any column with a name/slug of 'title'. Every time the class
		 * needs to render a column, it first looks for a method named 
		 * column_{$column_title} - if it exists, that method is run. If it doesn't
		 * exist, column_default() is called instead.
		 * 
		 * This example also illustrates how to implement rollover actions. Actions
		 * should be an associative array formatted as 'slug'=>'link html' - and you
		 * will need to generate the URLs yourself. You could even ensure the links
		 * 
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @param array $item A singular item (one full row's worth of data)
		 * @return string Text to be placed inside the column <td> (movie title only)
		 **************************************************************************/
		 
		function column_order_ID($item){
			
			//Build row actions
			$actions = array(
				'view_more'      => sprintf('<a href="?page=%s&action=%s&order_ID=%s">View More</a>',$_REQUEST['page'],'edit',$item['ID']),
				'delete'    => sprintf('<a href="?page=%s&action=%s&order=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
			);
			
			//Return the title contents
			return sprintf('%1$s <span style="color:silver"></span>%3$s',
				/*$1%s*/ $item['ID'],
				/*$2%s*/ $item['ID'],
				/*$3%s*/ $this->row_actions($actions)
			);
		}
		
		
		/** ************************************************************************
		 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
		 * is given special treatment when columns are processed. It ALWAYS needs to
		 * have it's own method.
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @param array $item A singular item (one full row's worth of data)
		 * @return string Text to be placed inside the column <td> (movie title only)
		 **************************************************************************/
		function column_cb($item){
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
				/*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
			);
		}
		
		
		/** ************************************************************************
		 * REQUIRED! This method dictates the table's columns and titles. This should
		 * return an array where the key is the column slug (and class) and the value 
		 * is the column's title text. If you need a checkbox for bulk actions, refer
		 * to the $columns array below.
		 * 
		 * The 'cb' column is treated differently than the rest. If including a checkbox
		 * column in your table you must create a column_cb() method. If you don't need
		 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
		 **************************************************************************/
		function get_columns(){
			$columns = array(
				//'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
				'ID' => 'Order ID',
				'post_date' => 'Post Date',
				'club_order_total'  => 'Order Total',
				'club_order_status'  => 'Order Status',
				'club_order_payment'  => 'Payment Choice'
			);
			return $columns;
		}
		
		/** ************************************************************************
		 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
		 * you will need to register it here. This should return an array where the 
		 * key is the column that needs to be sortable, and the value is db column to 
		 * sort by. Often, the key and value will be the same, but this is not always
		 * the case (as the value is a column name from the database, not the list table).
		 * 
		 * This method merely defines which columns should be sortable and makes them
		 * clickable - it does not handle the actual sorting. You still need to detect
		 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
		 * your data accordingly (usually by modifying your query).
		 * 
		 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
		 **************************************************************************/
		 
		function get_sortable_columns() {
			$sortable_columns = array(
				'order_date'     => array('order_date',false),     //true means it's already sorted
				'club_order_status'    => array('club_order_status',false),
				'club_payment_choice'  => array('club_order_payment',false)
			);
			return $sortable_columns;
		}
		
		
		/** ************************************************************************
		 * Optional. If you need to include bulk actions in your list table, this is
		 * the place to define them. Bulk actions are an associative array in the format
		 * 'slug'=>'Visible Title'
		 * 
		 * If this method returns an empty value, no bulk action will be rendered. If
		 * you specify any bulk actions, the bulk actions box will be rendered with
		 * the table automatically on display().
		 * 
		 * Also note that list tables are not automatically wrapped in <form> elements,
		 * so you will need to create those manually in order for bulk actions to function.
		 * 
		 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
		 **************************************************************************/
		function get_bulk_actions() {
			$actions = array(
				//'delete'    => 'Delete'
			);
			return $actions;
		}
		
		
		/** ************************************************************************
		 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
		 * For this example package, we will handle it in the class to keep things
		 * clean and organized.
		 * 
		 * @see $this->prepare_items()
		 **************************************************************************/
		function process_bulk_action() {
			
			//Detect when a bulk action is being triggered...
			if( 'delete'===$this->current_action() ) {
				wp_die('Items deleted (or they would be if we had items to delete)!');
			}
			
		}
		
		
		/** ************************************************************************
		 * REQUIRED! This is where you prepare your data for display. This method will
		 * usually be used to query the database, sort and filter the data, and generally
		 * get it ready to be displayed. At a minimum, we should set $this->items and
		 * $this->set_pagination_args(), although the following properties and methods
		 * are frequently interacted with here...
		 * 
		 * @global WPDB $wpdb
		 * @uses $this->_column_headers
		 * @uses $this->items
		 * @uses $this->get_columns()
		 * @uses $this->get_sortable_columns()
		 * @uses $this->get_pagenum()
		 * @uses $this->set_pagination_args()
		 **************************************************************************/
		function prepare_items() {
			global $wpdb; //This is used only if making any database queries

			/**
			 * First, lets decide how many records per page to show
			 */
			$per_page = 5;
			
			
			/**
			 * REQUIRED. Now we need to define our column headers. This includes a complete
			 * array of columns to be displayed (slugs & titles), a list of columns
			 * to keep hidden, and a list of columns that are sortable. Each of these
			 * can be defined in another method (as we've done here) before being
			 * used to build the value for our _column_headers property.
			 */
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			
			
			/**
			 * REQUIRED. Finally, we build an array to be used by the class for column 
			 * headers. The $this->_column_headers property takes an array which contains
			 * 3 other arrays. One for all columns, one for hidden columns, and one
			 * for sortable columns.
			 */
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			
			/**
			 * Optional. You can handle your bulk actions however you see fit. In this
			 * case, we'll handle them within our package just to keep things clean.
			 */
			$this->process_bulk_action();
			
			
			/**
			 * Instead of querying a database, we're going to fetch the example data
			 * property we created for use in this plugin. This makes this example 
			 * package slightly different than one you might build on your own. In 
			 * this example, we'll be using array manipulation to sort and paginate 
			 * our data. In a real-world implementation, you will probably want to 
			 * use sort and pagination data to build a custom query instead, as you'll
			 * be able to use your precisely-queried data immediately.
			 */
			//$data = $this->example_data;
			
			$querydata = $wpdb->get_results(
				"
					SELECT ID, DATE_FORMAT(post_date, '%M %e %Y') AS post_date FROM wp_posts WHERE post_type='club_orders' AND post_status!='trash' ORDER BY ID DESC LIMIT 3;
				"
			);
			
			$data=array();
			foreach($querydata as $querydatum){
				$row = get_post_meta($querydatum->ID);

				$temp = array(
					'ID' => "<a href='post.php?post=".$querydatum->ID."&action=edit'>".get_the_title($querydatum->ID)."</a>",
					'post_date' => $querydatum->post_date,
					'club_order_total'=>$row['club_order_total'][0],
					'club_order_status'=>$row['club_order_status'][0],
					'club_order_payment'=>$row['club_order_payment'][0]
					);
				array_push($data, $temp);
			}
			
			
				
			/**
			 * This checks for sorting input and sorts the data in our array accordingly.
			 * 
			 * In a real-world situation involving a database, you would probably want 
			 * to handle sorting by passing the 'orderby' and 'order' values directly 
			 * to a custom query. The returned data will be pre-sorted, and this array
			 * sorting technique would be unnecessary.
			 */
			 
			function usort_reorder($a,$b){
				$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order_id'; //If no sort, default to title
				$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
				$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
				return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
			}
			usort($data, 'usort_reorder');
			
			
			/***********************************************************************
			 * ---------------------------------------------------------------------
			 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
			 * 
			 * In a real-world situation, this is where you would place your query.
			 * 
			 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			 * ---------------------------------------------------------------------
			 **********************************************************************/
			
					
			/**
			 * REQUIRED for pagination. Let's figure out what page the user is currently 
			 * looking at. We'll need this later, so you should always include it in 
			 * your own package classes.
			 */
			$current_page = $this->get_pagenum();
			
			/**
			 * REQUIRED for pagination. Let's check how many items are in our data array. 
			 * In real-world use, this would be the total number of items in your database, 
			 * without filtering. We'll need this later, so you should always include it 
			 * in your own package classes.
			 */
			$total_items = count($data);
			
			
			/**
			 * The WP_List_Table class does not handle pagination for us, so we need
			 * to ensure that the data is trimmed to only the current page. We can use
			 * array_slice() to 
			 */
			$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
			
			
			
			/**
			 * REQUIRED. Now we can add our *sorted* data to the items property, where 
			 * it can be used by the rest of the class.
			 */
			$this->items = $data;
			
			
			/**
			 * REQUIRED. We also have to register our pagination options & calculations.
			 */
			$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
			) );
		}
		
	}
	
	/****************************************************************************************************************************/
	
	/****************************************************************************************************************************/
	
	/*
	 *
	 * Beginning of Admin Dashboard View Processing For the Top Clubs Being Sold
	 *
	 *
	*/
	class er_qm_top_clubs_view extends WP_List_Table {
		
		/** ************************************************************************
		 * REQUIRED. Set up a constructor that references the parent constructor. We 
		 * use the parent reference to set some default configs.
		 ***************************************************************************/
		function __construct(){
			global $status, $page;
					
			//Set parent defaults
			parent::__construct( array(
				'singular'  => 'club',     //singular name of the listed records
				'plural'    => 'clubs',    //plural name of the listed records
				'ajax'      => false        //does this table support ajax?
			) );
			
		}
		
		
		/** ************************************************************************
		 * Recommended. This method is called when the parent class can't find a method
		 * specifically build for a given column. Generally, it's recommended to include
		 * one method for each column you want to render, keeping your package class
		 * neat and organized. For example, if the class needs to process a column
		 * named 'title', it would first see if a method named $this->column_title() 
		 * exists - if it does, that method will be used. If it doesn't, this one will
		 * be used. Generally, you should try to use custom column methods as much as 
		 * possible. 
		 * 
		 * Since we have defined a column_title() method later on, this method doesn't
		 * need to concern itself with any column with a name of 'title'. Instead, it
		 * needs to handle everything else.
		 * 
		 * For more detailed insight into how columns are handled, take a look at 
		 * WP_List_Table::single_row_columns()
		 * 
		 * @param array $item A singular item (one full row's worth of data)
		 * @param array $column_name The name/slug of the column to be processed
		 * @return string Text or HTML to be placed inside the column <td>
		 **************************************************************************/
		 
		function column_default($item, $column_name){
			switch($column_name){
				case 'ID':
				case 'club_manufacturer':
				case 'club_type_field':
				case 'club_shaft_type':
					return $item[$column_name];
				default:
					return print_r($item,true); //Show the whole array for troubleshooting purposes
			}
		}
		
			
		/** ************************************************************************
		 * Recommended. This is a custom column method and is responsible for what
		 * is rendered in any column with a name/slug of 'title'. Every time the class
		 * needs to render a column, it first looks for a method named 
		 * column_{$column_title} - if it exists, that method is run. If it doesn't
		 * exist, column_default() is called instead.
		 * 
		 * This example also illustrates how to implement rollover actions. Actions
		 * should be an associative array formatted as 'slug'=>'link html' - and you
		 * will need to generate the URLs yourself. You could even ensure the links
		 * 
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @param array $item A singular item (one full row's worth of data)
		 * @return string Text to be placed inside the column <td> (movie title only)
		 **************************************************************************/
		 
		function column_order_ID($item){
			
			//Build row actions
			$actions = array(
				'view_more'      => sprintf('<a href="?page=%s&action=%s&order_ID=%s">View More</a>',$_REQUEST['page'],'edit',$item['ID']),
				'delete'    => sprintf('<a href="?page=%s&action=%s&order=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
			);
			
			//Return the title contents
			return sprintf('%1$s <span style="color:silver"></span>%3$s',
				/*$1%s*/ $item['ID'],
				/*$2%s*/ $item['ID'],
				/*$3%s*/ $this->row_actions($actions)
			);
		}
		
		
		/** ************************************************************************
		 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
		 * is given special treatment when columns are processed. It ALWAYS needs to
		 * have it's own method.
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @param array $item A singular item (one full row's worth of data)
		 * @return string Text to be placed inside the column <td> (movie title only)
		 **************************************************************************/
		function column_cb($item){
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
				/*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
			);
		}
		
		
		/** ************************************************************************
		 * REQUIRED! This method dictates the table's columns and titles. This should
		 * return an array where the key is the column slug (and class) and the value 
		 * is the column's title text. If you need a checkbox for bulk actions, refer
		 * to the $columns array below.
		 * 
		 * The 'cb' column is treated differently than the rest. If including a checkbox
		 * column in your table you must create a column_cb() method. If you don't need
		 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
		 **************************************************************************/
		function get_columns(){
			$columns = array(
				 //Render a checkbox instead of text
				'ID' => 'Club ID',
				'club_manufacturer' => 'Manufacturer',
				'club_type_field'  => 'Type',
				'club_shaft_type'  => 'Shaft'
			);
			return $columns;
		}
		
		/** ************************************************************************
		 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
		 * you will need to register it here. This should return an array where the 
		 * key is the column that needs to be sortable, and the value is db column to 
		 * sort by. Often, the key and value will be the same, but this is not always
		 * the case (as the value is a column name from the database, not the list table).
		 * 
		 * This method merely defines which columns should be sortable and makes them
		 * clickable - it does not handle the actual sorting. You still need to detect
		 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
		 * your data accordingly (usually by modifying your query).
		 * 
		 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
		 **************************************************************************/
		 
		function get_sortable_columns() {
			$sortable_columns = array(
				'order_date'     => array('order_date',false),     //true means it's already sorted
				'club_order_status'    => array('club_order_status',false),
				'club_payment_choice'  => array('club_order_payment',false)
			);
			return $sortable_columns;
		}
		
		
		/** ************************************************************************
		 * Optional. If you need to include bulk actions in your list table, this is
		 * the place to define them. Bulk actions are an associative array in the format
		 * 'slug'=>'Visible Title'
		 * 
		 * If this method returns an empty value, no bulk action will be rendered. If
		 * you specify any bulk actions, the bulk actions box will be rendered with
		 * the table automatically on display().
		 * 
		 * Also note that list tables are not automatically wrapped in <form> elements,
		 * so you will need to create those manually in order for bulk actions to function.
		 * 
		 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
		 **************************************************************************/
		function get_bulk_actions() {
			$actions = array(
				//'delete'    => 'Delete'
			);
			return $actions;
		}
		
		
		/** ************************************************************************
		 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
		 * For this example package, we will handle it in the class to keep things
		 * clean and organized.
		 * 
		 * @see $this->prepare_items()
		 **************************************************************************/
		function process_bulk_action() {
			
			//Detect when a bulk action is being triggered...
			if( 'delete'===$this->current_action() ) {
				wp_die('Items deleted (or they would be if we had items to delete)!');
			}
			
		}
		
		
		/** ************************************************************************
		 * REQUIRED! This is where you prepare your data for display. This method will
		 * usually be used to query the database, sort and filter the data, and generally
		 * get it ready to be displayed. At a minimum, we should set $this->items and
		 * $this->set_pagination_args(), although the following properties and methods
		 * are frequently interacted with here...
		 * 
		 * @global WPDB $wpdb
		 * @uses $this->_column_headers
		 * @uses $this->items
		 * @uses $this->get_columns()
		 * @uses $this->get_sortable_columns()
		 * @uses $this->get_pagenum()
		 * @uses $this->set_pagination_args()
		 **************************************************************************/
		function prepare_items() {
			global $wpdb; //This is used only if making any database queries

			/**
			 * First, lets decide how many records per page to show
			 */
			$per_page = 5;
			
			
			/**
			 * REQUIRED. Now we need to define our column headers. This includes a complete
			 * array of columns to be displayed (slugs & titles), a list of columns
			 * to keep hidden, and a list of columns that are sortable. Each of these
			 * can be defined in another method (as we've done here) before being
			 * used to build the value for our _column_headers property.
			 */
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			
			
			/**
			 * REQUIRED. Finally, we build an array to be used by the class for column 
			 * headers. The $this->_column_headers property takes an array which contains
			 * 3 other arrays. One for all columns, one for hidden columns, and one
			 * for sortable columns.
			 */
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			
			/**
			 * Optional. You can handle your bulk actions however you see fit. In this
			 * case, we'll handle them within our package just to keep things clean.
			 */
			$this->process_bulk_action();
			
			
			/**
			 * Instead of querying a database, we're going to fetch the example data
			 * property we created for use in this plugin. This makes this example 
			 * package slightly different than one you might build on your own. In 
			 * this example, we'll be using array manipulation to sort and paginate 
			 * our data. In a real-world implementation, you will probably want to 
			 * use sort and pagination data to build a custom query instead, as you'll
			 * be able to use your precisely-queried data immediately.
			 */
			//$data = $this->example_data;
			
			$querydata = $wpdb->get_results(
				"
					SELECT post_id,
					COUNT(post_id) AS ordered_amount
					FROM wp_clubs_ordered_stats
					GROUP BY post_id
					ORDER BY ordered_amount DESC
					LIMIT 3;
				"
			);
			
			$data=array();
			foreach($querydata as $querydatum){
				$row = get_post_meta($querydatum->post_id);
				
				$temp = array(
					'ID' => "<a href='post.php?post=".$querydatum->post_id."&action=edit'>".get_the_title($querydatum->post_id)."</a>",
					'club_manufacturer' => $row['club_manufacturer'][0],
					'club_type_field'=>$row['club_type_field'][0],
					'club_shaft_type'=>$row['club_type_field'][0],
					);
				array_push($data, $temp);
				
			}
			
			
				
			/**
			 * This checks for sorting input and sorts the data in our array accordingly.
			 * 
			 * In a real-world situation involving a database, you would probably want 
			 * to handle sorting by passing the 'orderby' and 'order' values directly 
			 * to a custom query. The returned data will be pre-sorted, and this array
			 * sorting technique would be unnecessary.
			 */
			 
			function usort_reorder_clubs($a,$b){
				$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order_id'; //If no sort, default to title
				$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
				$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
				return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
			}
			usort($data, 'usort_reorder_clubs');
			
			
			/***********************************************************************
			 * ---------------------------------------------------------------------
			 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
			 * 
			 * In a real-world situation, this is where you would place your query.
			 * 
			 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			 * ---------------------------------------------------------------------
			 **********************************************************************/
			
					
			/**
			 * REQUIRED for pagination. Let's figure out what page the user is currently 
			 * looking at. We'll need this later, so you should always include it in 
			 * your own package classes.
			 */
			$current_page = $this->get_pagenum();
			
			/**
			 * REQUIRED for pagination. Let's check how many items are in our data array. 
			 * In real-world use, this would be the total number of items in your database, 
			 * without filtering. We'll need this later, so you should always include it 
			 * in your own package classes.
			 */
			$total_items = count($data);
			
			
			/**
			 * The WP_List_Table class does not handle pagination for us, so we need
			 * to ensure that the data is trimmed to only the current page. We can use
			 * array_slice() to 
			 */
			$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
			
			
			
			/**
			 * REQUIRED. Now we can add our *sorted* data to the items property, where 
			 * it can be used by the rest of the class.
			 */
			$this->items = $data;
			
			
			/**
			 * REQUIRED. We also have to register our pagination options & calculations.
			 */
			$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
			) );
		}
		
	}
	
	
	/****************************************************************************************************************************/
	
	/****************************************************************************************************************************/
	
	/*
	 *
	 * Beginning of Admin Dashboard View Processing For the Top Clubs Being Sold
	 *
	 *
	*/
	class er_qm_top_customers_view extends WP_List_Table {
		
		/** ************************************************************************
		 * REQUIRED. Set up a constructor that references the parent constructor. We 
		 * use the parent reference to set some default configs.
		 ***************************************************************************/
		function __construct(){
			global $status, $page;
					
			//Set parent defaults
			parent::__construct( array(
				'singular'  => 'customer',     //singular name of the listed records
				'plural'    => 'customers',    //plural name of the listed records
				'ajax'      => false        //does this table support ajax?
			) );
			
		}
		
		
		/** ************************************************************************
		 * Recommended. This method is called when the parent class can't find a method
		 * specifically build for a given column. Generally, it's recommended to include
		 * one method for each column you want to render, keeping your package class
		 * neat and organized. For example, if the class needs to process a column
		 * named 'title', it would first see if a method named $this->column_title() 
		 * exists - if it does, that method will be used. If it doesn't, this one will
		 * be used. Generally, you should try to use custom column methods as much as 
		 * possible. 
		 * 
		 * Since we have defined a column_title() method later on, this method doesn't
		 * need to concern itself with any column with a name of 'title'. Instead, it
		 * needs to handle everything else.
		 * 
		 * For more detailed insight into how columns are handled, take a look at 
		 * WP_List_Table::single_row_columns()
		 * 
		 * @param array $item A singular item (one full row's worth of data)
		 * @param array $column_name The name/slug of the column to be processed
		 * @return string Text or HTML to be placed inside the column <td>
		 **************************************************************************/
		 
		function column_default($item, $column_name){
			switch($column_name){
				case 'ID':
				case 'user_nicename':
				case 'user_email':
				case 'display_name':
				case 'transactions':
				case 'amount':
					return $item[$column_name];
				default:
					return print_r($item,true); //Show the whole array for troubleshooting purposes
			}
		}
		
			
		/** ************************************************************************
		 * Recommended. This is a custom column method and is responsible for what
		 * is rendered in any column with a name/slug of 'title'. Every time the class
		 * needs to render a column, it first looks for a method named 
		 * column_{$column_title} - if it exists, that method is run. If it doesn't
		 * exist, column_default() is called instead.
		 * 
		 * This example also illustrates how to implement rollover actions. Actions
		 * should be an associative array formatted as 'slug'=>'link html' - and you
		 * will need to generate the URLs yourself. You could even ensure the links
		 * 
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @param array $item A singular item (one full row's worth of data)
		 * @return string Text to be placed inside the column <td> (movie title only)
		 **************************************************************************/
		 
		function column_order_ID($item){
			
			//Build row actions
			$actions = array(
				'view_more'      => sprintf('<a href="?page=%s&action=%s&order_ID=%s">View More</a>',$_REQUEST['page'],'edit',$item['ID']),
				'delete'    => sprintf('<a href="?page=%s&action=%s&order=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
			);
			
			//Return the title contents
			return sprintf('%1$s <span style="color:silver"></span>%3$s',
				/*$1%s*/ $item['ID'],
				/*$2%s*/ $item['ID'],
				/*$3%s*/ $this->row_actions($actions)
			);
		}
		
		
		/** ************************************************************************
		 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
		 * is given special treatment when columns are processed. It ALWAYS needs to
		 * have it's own method.
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @param array $item A singular item (one full row's worth of data)
		 * @return string Text to be placed inside the column <td> (movie title only)
		 **************************************************************************/
		function column_cb($item){
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
				/*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
			);
		}
		
		
		/** ************************************************************************
		 * REQUIRED! This method dictates the table's columns and titles. This should
		 * return an array where the key is the column slug (and class) and the value 
		 * is the column's title text. If you need a checkbox for bulk actions, refer
		 * to the $columns array below.
		 * 
		 * The 'cb' column is treated differently than the rest. If including a checkbox
		 * column in your table you must create a column_cb() method. If you don't need
		 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
		 **************************************************************************/
		function get_columns(){
			$columns = array(
				//'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
				'ID' => 'User ID',
				'user_nicename' => 'Username',
				'user_email'  => 'Email',
				'display_name'  => 'Display Name',
				'transactions' => '# of Transactions',
				'amount' => 'Amount Ordered'
			);
			return $columns;
		}
		
		/** ************************************************************************
		 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
		 * you will need to register it here. This should return an array where the 
		 * key is the column that needs to be sortable, and the value is db column to 
		 * sort by. Often, the key and value will be the same, but this is not always
		 * the case (as the value is a column name from the database, not the list table).
		 * 
		 * This method merely defines which columns should be sortable and makes them
		 * clickable - it does not handle the actual sorting. You still need to detect
		 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
		 * your data accordingly (usually by modifying your query).
		 * 
		 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
		 **************************************************************************/
		 
		function get_sortable_columns() {
			$sortable_columns = array(
				'order_date'     => array('order_date',false),     //true means it's already sorted
				'club_order_status'    => array('club_order_status',false),
				'club_payment_choice'  => array('club_order_payment',false)
			);
			return $sortable_columns;
		}
		
		
		/** ************************************************************************
		 * Optional. If you need to include bulk actions in your list table, this is
		 * the place to define them. Bulk actions are an associative array in the format
		 * 'slug'=>'Visible Title'
		 * 
		 * If this method returns an empty value, no bulk action will be rendered. If
		 * you specify any bulk actions, the bulk actions box will be rendered with
		 * the table automatically on display().
		 * 
		 * Also note that list tables are not automatically wrapped in <form> elements,
		 * so you will need to create those manually in order for bulk actions to function.
		 * 
		 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
		 **************************************************************************/
		function get_bulk_actions() {
			$actions = array(
				//'delete'    => 'Delete'
			);
			return $actions;
		}
		
		
		/** ************************************************************************
		 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
		 * For this example package, we will handle it in the class to keep things
		 * clean and organized.
		 * 
		 * @see $this->prepare_items()
		 **************************************************************************/
		function process_bulk_action() {
			
			//Detect when a bulk action is being triggered...
			if( 'delete'===$this->current_action() ) {
				wp_die('Items deleted (or they would be if we had items to delete)!');
			}
			
		}
		
		
		/** ************************************************************************
		 * REQUIRED! This is where you prepare your data for display. This method will
		 * usually be used to query the database, sort and filter the data, and generally
		 * get it ready to be displayed. At a minimum, we should set $this->items and
		 * $this->set_pagination_args(), although the following properties and methods
		 * are frequently interacted with here...
		 * 
		 * @global WPDB $wpdb
		 * @uses $this->_column_headers
		 * @uses $this->items
		 * @uses $this->get_columns()
		 * @uses $this->get_sortable_columns()
		 * @uses $this->get_pagenum()
		 * @uses $this->set_pagination_args()
		 **************************************************************************/
		function prepare_items() {
			global $wpdb; //This is used only if making any database queries

			/**
			 * First, lets decide how many records per page to show
			 */
			$per_page = 5;
			
			
			/**
			 * REQUIRED. Now we need to define our column headers. This includes a complete
			 * array of columns to be displayed (slugs & titles), a list of columns
			 * to keep hidden, and a list of columns that are sortable. Each of these
			 * can be defined in another method (as we've done here) before being
			 * used to build the value for our _column_headers property.
			 */
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			
			
			/**
			 * REQUIRED. Finally, we build an array to be used by the class for column 
			 * headers. The $this->_column_headers property takes an array which contains
			 * 3 other arrays. One for all columns, one for hidden columns, and one
			 * for sortable columns.
			 */
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			
			/**
			 * Optional. You can handle your bulk actions however you see fit. In this
			 * case, we'll handle them within our package just to keep things clean.
			 */
			$this->process_bulk_action();
			
			
			/**
			 * Instead of querying a database, we're going to fetch the example data
			 * property we created for use in this plugin. This makes this example 
			 * package slightly different than one you might build on your own. In 
			 * this example, we'll be using array manipulation to sort and paginate 
			 * our data. In a real-world implementation, you will probably want to 
			 * use sort and pagination data to build a custom query instead, as you'll
			 * be able to use your precisely-queried data immediately.
			 */
			//$data = $this->example_data;
			
			$querydata = $wpdb->get_results(
				"
					SELECT user_id, 
					SUM( quote ) AS amount, 
					COUNT( user_id ) AS transactions
					FROM wp_clubs_ordered_stats
					GROUP BY user_id
					ORDER BY amount DESC;
				"
			);
			
			$data=array();
			foreach($querydata as $querydatum){
				$row = get_userdata($querydatum->user_id);
				
				$temp = array(
					'ID' => "<a href='user-edit.php?user_id=".$querydatum->user_id."&wp_http_referer=%2Fgolfbuyers.com%2Fwp-admin%2Fusers.php'>".$querydatum->user_id."</a>",
					'user_nicename' => $row->user_nicename,
					'user_email'=>$row->user_email,
					'display_name'=>$row->display_name,
					'transactions'=>$querydatum->transactions,
					'amount'=>$querydatum->amount
					);
				array_push($data, $temp);
				
			}
			
			
				
			/**
			 * This checks for sorting input and sorts the data in our array accordingly.
			 * 
			 * In a real-world situation involving a database, you would probably want 
			 * to handle sorting by passing the 'orderby' and 'order' values directly 
			 * to a custom query. The returned data will be pre-sorted, and this array
			 * sorting technique would be unnecessary.
			 */
			 
			function usort_reorder_customers($a,$b){
				$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order_id'; //If no sort, default to title
				$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
				$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
				return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
			}
			usort($data, 'usort_reorder_customers');
			
			
			/***********************************************************************
			 * ---------------------------------------------------------------------
			 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
			 * 
			 * In a real-world situation, this is where you would place your query.
			 * 
			 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			 * ---------------------------------------------------------------------
			 **********************************************************************/
			
					
			/**
			 * REQUIRED for pagination. Let's figure out what page the user is currently 
			 * looking at. We'll need this later, so you should always include it in 
			 * your own package classes.
			 */
			$current_page = $this->get_pagenum();
			
			/**
			 * REQUIRED for pagination. Let's check how many items are in our data array. 
			 * In real-world use, this would be the total number of items in your database, 
			 * without filtering. We'll need this later, so you should always include it 
			 * in your own package classes.
			 */
			$total_items = count($data);
			
			
			/**
			 * The WP_List_Table class does not handle pagination for us, so we need
			 * to ensure that the data is trimmed to only the current page. We can use
			 * array_slice() to 
			 */
			$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
			
			
			
			/**
			 * REQUIRED. Now we can add our *sorted* data to the items property, where 
			 * it can be used by the rest of the class.
			 */
			$this->items = $data;
			
			
			/**
			 * REQUIRED. We also have to register our pagination options & calculations.
			 */
			$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
			) );
		}
		
	}
	
	// Include template functions
	require_once plugin_dir_path(__FILE__) . 'includes/template-functions.php';
	
?>