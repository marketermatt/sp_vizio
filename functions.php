<?php
/**
 * SP FRAMEWORK FILE - DO NOT EDIT!
 * 
 * @package SP FRAMEWORK
 * if you want to add your own functions, create a child theme and add your functions in the functions.php file.
 *
 * include all the functions
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

/////////////////////////////////////////////////////
// load the framework
/////////////////////////////////////////////////////
require_once( get_template_directory() . '/sp-framework/sp-framework.php' );

/////////////////////////////////////////////////////
// load theme specific functions
/////////////////////////////////////////////////////
if ( is_file( get_template_directory() . '/theme-functions/theme-functions.php' ) ) {
	require_once( get_template_directory() . '/theme-functions/theme-functions.php' );
}

/////////////////////////////////////////////////////
// load theme specific woocommerce functions
/////////////////////////////////////////////////////
if ( is_file( get_template_directory() . '/theme-functions/theme-woo-functions.php' ) && sp_woo_exists() ) {
	require_once( get_template_directory() . '/theme-functions/theme-woo-functions.php' );
}



// LOAD PRETTY PHOTO for the whole site

add_action( 'wp_enqueue_scripts', 'frontend_scripts_include_lightbox' );

function frontend_scripts_include_lightbox() {
  global $woocommerce;
  $suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
  $lightbox_en = get_option( 'woocommerce_enable_lightbox' ) == 'yes' ? true : false; 
  
  if ( $lightbox_en ) {
    wp_enqueue_script( 'prettyPhoto', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
    wp_enqueue_script( 'prettyPhoto-init', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
    wp_enqueue_style( 'woocommerce_prettyPhoto_css', $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css' );
  }
}