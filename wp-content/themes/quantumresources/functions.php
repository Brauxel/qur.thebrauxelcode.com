<?php
/*
QUR Theme Functions
By: Stocks Digital <Aakash Bhatia>
http://stocksdigital.com/
*/

function loadMyScripts() {
	if ( !is_admin() ) {
		// Register CSS
		wp_enqueue_style( 'slick', get_bloginfo( 'template_url' ) . '/css/slick.css', null, '1.0.1', 'all' );
		wp_enqueue_style( 'quantum-resources', get_bloginfo( 'template_url' ) . '/css/quantum-resources.css', null, '1.0.3', 'all' );
		wp_enqueue_style( 'stylesheet', get_bloginfo( 'template_url' ) . '/style.css', null, '1.0.3', 'all' );
	
		// Register JavaScript
		//wp_deregister_script( 'jquery' );
		//wp_enqueue_script( 'jquery', get_bloginfo( 'template_url' ) . '/js/jquery.js', null, '3.0.0' );
		wp_enqueue_script( 'jquery' );
		//wp_enqueue_script( 'jquery-ui', get_bloginfo( 'template_url' ) . '/js/jquery-ui.js', array( 'jquery' ), '1.11.4' );
		wp_enqueue_script( 'slick', get_bloginfo( 'template_url' ) . '/js/slick.min.js', array( 'jquery' ), '1.0.0' );
		wp_enqueue_script( 'core', get_bloginfo( 'template_url' ) . '/js/core.js', array( 'jquery' ), '1.0.1', true );
	}
}

add_action( 'wp_enqueue_scripts', 'loadMyScripts' );


// Regsiter Main Menu
register_nav_menus( array(
	'main' => 'Main Menu'
));

// Enable Featured Images in posts (Blog)
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

//gform_confirmation_anchor
add_filter( 'gform_confirmation_anchor', '__return_false' );

//gform_tabindex
add_filter( 'gform_tabindex', '__return_false' );

/** 
  * Send Gravity Forms entry data to WebLink
  */
add_action( 'gform_after_submission', 'post_to_third_party', 10, 2 );
function post_to_third_party( $entry, $form ) {
    
    // Form 1 - Receive Latest Updates
    if( $form['id'] == '1' ) {
    $post_url = 'http://clients3.weblink.com.au/Clients/quantumresources/addEmailDetails.aspx';
    $body = array(
        'first_name' => rgar( $entry, '1.3' ), 
        'last_name' => rgar( $entry, '1.6' ), 
        'email' => rgar( $entry, '2' ),
        );
    GFCommon::log_debug( 'gform_after_submission: body => ' . print_r( $body, true ) );

    $request = new WP_Http();
    $response = $request->post( $post_url, array( 'body' => $body ) );
    GFCommon::log_debug( 'gform_after_submission: response => ' . print_r( $response, true ) );
    }
}
?>