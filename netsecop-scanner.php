<?php

// exit if file is called directly
if ( !  defined( 'ABSPATH' ) ) {
  exit;
}


/*
* Run Free Scan When Plugin Activated
*/
function netsecop_activate() {

  // If netsecop scan is already been initialzed we don't need to do it again. Let's check it out.
  $is_netsecop_free_scan_initialized = get_option( 'netsecop_free_scan_initialized');

  if ($is_netsecop_free_scan_initialized == 1){
    $warning = 'Netsecop is reactivated now.';
    set_transient( 'activation-admin-warning-notice', $warning , 5 );
  }
  //Otherwise let's send a scan request to Netsecop API
  else{

    $NETSECOPURL = "https://www.netsecop.com/wp_freescanrequest";
    //$WORDPRESS_WEBSITE_URL = preg_replace("(^https?://)", "", get_site_url());

    //If there is no record, create a table record with default options.
    $options = get_option( 'netsecop_options');
    if ($options == false){
      update_option( 'netsecop_options', netsecop_options_default());
      $options = get_option( 'netsecop_options');
    }

    //Netsecop Scan Request API takes two parameters: URL and email
    //Returns a token which will be used to ask about scan resuts.
    $WORDPRESS_USER_EMAIL = sanitize_text_field( $options['email_address'] );
    $WORDPRESS_WEBSITE_URL = esc_url( $options['website_url'] );

    //If the value in the database differs from the site url, use site url.
    if ($WORDPRESS_WEBSITE_URL != get_site_url()){
      $WORDPRESS_WEBSITE_URL = get_site_url();
    }

    //Send the POST request
    $response = wp_remote_post( $NETSECOPURL, array(
      'method' => 'POST',
      'headers' => array(),
      'body' => array('url' => $WORDPRESS_WEBSITE_URL , 'email' => $WORDPRESS_USER_EMAIL)
    )
  );

  if ( is_wp_error( $response ) ) {
    // $error_message = $response->get_error_message();
    update_option( 'netsecop_free_scan_initialized', 0);
    wp_die( '<pre>' . "ERR1 - An Error Occured! Your Netsecop Scan Cannot Be Initialized. If this problem persists, please contact us via info@netsecop.com" . '</pre>' );
  }
  else if (wp_remote_retrieve_response_code($response) == 200){
    $jsonResponse = json_decode(wp_remote_retrieve_body($response), true);
    $result = $jsonResponse['result'];

    if ($result == "Error"){
      $errSrc = $jsonResponse['errSrc'];
      $errMsg = $jsonResponse['errMsg'];
      $errType = $jsonResponse['errType'];
      set_transient( 'activation-admin-error-notice', $errType , 5 );
      update_option( 'netsecop_free_scan_initialized', 0);
    }
    //If everyting works well, netsecop scan is being initialized and the returning token is saved.
    else if ($result == "OK"){
      $url = $jsonResponse['url'];
      $wpToken = $jsonResponse['wpToken'];

      set_transient( 'activation-admin-notice', true, 5 );
      update_option( 'netsecop_free_scan_initialized', 1);
      update_option( 'netsecop_token', $wpToken);
    }
    else{
      update_option( 'netsecop_free_scan_initialized', 0);
      wp_die( '<pre>' . "ERR2 - An Error Occured! Your Netsecop Scan Cannot Be Initialized. Reason:" .$result."If this problem persists, please contact us via info@netsecop.com" . '</pre>' );
      //wp_die( '<pre>' . "No Problem" . '</pre>' );
    }
  }
  else{
    update_option( 'netsecop_free_scan_initialized', 0);
    wp_die( '<pre>' . "ERR3 - An Error Occured! Your Netsecop Scan Cannot Be Initialized. If this problem persists, please contact us via info@netsecop.com" . '</pre>' );
  }
}

}
register_activation_hook( plugin_dir_path(__FILE__).'netsecop.php', 'netsecop_activate' );


/*
* Positive Admin Notice
*/
function netsecop_show_activation_admin_notice(){

  $WORDPRESS_USER_EMAIL = wp_get_current_user()->user_email;
  $options = get_option( 'netsecop_options');
  if ($options != false){
    $WORDPRESS_USER_EMAIL = sanitize_text_field( $options['email_address'] );
  }

  /* Check transient, if available display notice */
  if( get_transient( 'activation-admin-notice' ) ){
    ?>
    <div class="notice notice-info is-dismissible">
      <p>Your free website security scan has been initialized. Further instructions and security report will ben sent to <strong><?php echo $WORDPRESS_USER_EMAIL ?></strong></p>
    </div>
    <?php
    /* Delete transient, only display this notice once. */
    delete_transient( 'activation-admin-notice' );
  }
}
add_action( 'admin_notices', 'netsecop_show_activation_admin_notice' );

/*
* Negative Admin Notice
*/
function netsecop_show_activation_admin_error_notice(){

  /* Check transient, if available display notice */
  if( get_transient( 'activation-admin-error-notice' ) ){
    $message = get_transient( 'activation-admin-error-notice' );
    ?>
    <div class="notice notice-error is-dismissible">
      <p>A problem occurred while initializing your security scan. <strong><?php echo $message ?></strong></p><br/>
      Please upadate your information using Netsecop settings menu on the left panel.
    </div>
    <?php
    /* Delete transient, only display this notice once. */
    delete_transient( 'activation-admin-error-notice' );
  }
}
add_action( 'admin_notices', 'netsecop_show_activation_admin_error_notice' );

/*
* Neutral Admin Notice
*/
function netsecop_show_activation_admin_warning_notice(){

  /* Check transient, if available display notice */
  if( get_transient( 'activation-admin-warning-notice' ) ){
    $message = get_transient( 'activation-admin-warning-notice' );
    ?>
    <div class="notice notice-warning is-dismissible">
      <p><?php echo $message ?></p>
    </div>
    <?php
    /* Delete transient, only display this notice once. */
    delete_transient( 'activation-admin-warning-notice' );
  }
}
add_action( 'admin_notices', 'netsecop_show_activation_admin_warning_notice' );
