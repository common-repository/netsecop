<?php
/*
Plugin Name: Netsecop
Description: Netsecop Security Scanner
Plugin URI:  https://www.netsecop.com
Author:      Netsecop Inc
Version:     1.0
License:     GPLv2 or later
*/


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// If admin area

if (is_admin()){
  //include dependencies
  require_once plugin_dir_path(__FILE__) . 'admin/netsecop-admin-menu.php';
  require_once plugin_dir_path(__FILE__) . 'admin/netsecop-settings-page.php';
  require_once plugin_dir_path(__FILE__) . 'admin/netsecop-settings-register.php';
  require_once plugin_dir_path(__FILE__) . 'admin/netsecop-settings-callbacks.php';
  require_once plugin_dir_path(__FILE__) . 'admin/netsecop-settings-validate.php';
  require_once plugin_dir_path(__FILE__) . 'admin/netsecop-dashboard-widget.php';
}

require_once plugin_dir_path(__FILE__) . 'netsecop-scanner.php';


// default plugin options
function netsecop_options_default() {

  return array(
    'email_address'  => wp_get_current_user()->user_email,
    'website_url'  => get_site_url(),
    'show_seal' => false,
    'show_bubble' => false,
  );

}

//Show Netsecop Seal
function netsecop_seal() {

  $display_seal = 0;

  $options = get_option( 'netsecop_options', netsecop_options_default() );

  if ( isset( $options['show_seal'] ) && ! empty( $options['show_seal'] ) ) {
    $display_seal = sanitize_text_field( $options['show_seal'] );
  }

  if ( $display_seal == 1 ) {
    /*
    wp_enqueue_script(
    string           $handle,
    string           $src = '',
    array            $deps = array(),
    string|bool|null $ver = false,
    bool             $in_footer = false
    )
    */
    wp_enqueue_script( 'netsecop', 'https://www.netsecop.com/files/js/netsecop_wordpress_seal.js', array(), null, true );
  }
}
add_action( 'wp_enqueue_scripts', 'netsecop_seal' );


//THis function will be triggered when netsecop options are changed.
function netsecop_initialize_scan_if_not_initialized_yet()
{
  $scan_initialized = get_option('netsecop_free_scan_initialized');

  if ( $scan_initialized == false || $scan_initialized == 0) {
    netsecop_activate();
  }
}
add_action( 'update_option_netsecop_options', 'netsecop_initialize_scan_if_not_initialized_yet' );
