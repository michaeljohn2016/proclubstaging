<?php

add_filter( 'body_class', 'pc_iframe_class' );
function pc_iframe_class( $classes ) {
	if ( isset($_GET['vendorwindow'])  ) {
        $classes[] = 'vendor-window';
    }
	return $classes;
}

function proclubs_vendor_window_style() { 
	if ( isset($_GET['vendorwindow'])  ) {
		wp_enqueue_style( 'vendor-window', plugin_dir_url( __FILE__ ) . 'css/vendor-window.css', array(), 1.0);

	}

  }
add_action( 'wp_enqueue_scripts', 'proclubs_vendor_window_style' );


add_action('init','pc_vendor_iframe', 1);
function pc_vendor_iframe() {
    if (isset($_GET['vendorwindow']) && is_user_logged_in()) {
		wp_logout();
    }
}

function pc_get_shaft_friendly_name($slug) {
	$post = get_page_by_path($slug, OBJECT, 'premium-shaft');
	if ($post) {
		return $post->post_title;
	} else {
		return '';
	}
}