<?php //Netsecop Settings

/*
* This file is required to show settings
* */

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


//display the plugin settings page
function netsecop_display_settings_page(){

  //check if user is allowed access

  if (!current_user_can('manage_options')) return;

  ?>

  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">

      <?php

      // output security fields
      settings_fields('netsecop_options');

      // output setting sections. Setting sectionlarinin gosterilecegi sayfanin adi. ( $menu_slug )
      do_settings_sections('netsecop');

      //submit button
      submit_button();

      ?>

    </form>
  </div>
  <?php
}

//display the scan dashboard page
function netsecop_display_scan_dashboard_page(){

  //check if user is allowed access

  if (!current_user_can('manage_options')) return;

  $params = netsecop_results_for_dashboard();

  wp_enqueue_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null, false );
  wp_enqueue_script('googlechartsload', plugin_dir_url( dirname( __FILE__ ) ) . 'charts.js', array(), null, true);
  wp_localize_script( 'my-googlechartsload', 'googleChartsLoadParams', $params );
  //wp_enqueue_style( 'bootstrapcss', 'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.4.1/css/mdb.min.css', array(), null, 'all' );

  ?>

  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
  </div>
  <div id="myPieChart"/>
  <?php
}
