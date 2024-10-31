<?php //NETSECOP - Admin Menu

/*
* This file is required to show netsecop menu in settings page.
* */

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// add top-level administrative menu
function netsecop_add_toplevel_menu() {
  $display_bubble = 0;

  // Each time admin page is loaded, I'll ask Netsecop API about the last scan.

  //This request gets 3 parameters: ULR, email and the API token
  $LATESTSCANRESULTURL = "https://www.netsecop.com/getwpscanstat";
  $NETSECOPACCESSTOKEN = get_option('netsecop_token');

  //If there is no token in db, we can't send the request
  if (!empty($NETSECOPACCESSTOKEN) && $NETSECOPACCESSTOKEN != false ){

    //If netsecop scan has not been initialized yet, we don't need to ask about latest scan results.
    $ISSCANINITIALIZZED = get_option('netsecop_free_scan_initialized');
    if ($ISSCANINITIALIZZED == 1){

      $options = get_option( 'netsecop_options');
      $WORDPRESS_USER_EMAIL = sanitize_text_field( $options['email_address'] );
      $WORDPRESS_WEBSITE_URL = esc_url( $options['website_url'] );

      //Send the request.
      $response = wp_remote_post($LATESTSCANRESULTURL, array(
        'method' => 'POST',
        'headers' => array(),
        'body' => array('url' => $WORDPRESS_WEBSITE_URL , 'email' => $WORDPRESS_USER_EMAIL,'wpToken' => $NETSECOPACCESSTOKEN)
      )
    );

    if ( is_wp_error( $response ) ) {
      echo '<p>An Error Occured!</p><p>If this problem persists, please contact us via info@netsecop.com</p>';
    }
    //Everyting seems OK, lets parse the response and save to DB.
    else if (wp_remote_retrieve_response_code($response) == 200){
      $jsonResponse = json_decode(wp_remote_retrieve_body($response), true);

      // Let's parse the result and save to db.
	  if (array_key_exists('bl', $jsonResponse)) {
		  $bl        = $jsonResponse['bl'];
		  $blmessage = $bl['msg'];
		  $bldate    = $bl['date'];
	  }
	  else{
		  $blmessage = "In progress..";
		  $bldate    = "...";
	  }

	  if (array_key_exists('rm', $jsonResponse)) {
		  $rm        = $jsonResponse['rm'];
		  $rmmessage = $rm['msg'];
		  $rmdate    = $rm['date'];
	  }
	  else{
		  $rmmessage = "In progress..";
		  $rmdate    = "...";
	  }
	  if (array_key_exists('vul', $jsonResponse)) {
		  $vul        = $jsonResponse['vul'];
		  $vulmessage = $vul['msg'];
		  $vuldate    = $vul['date'];
	  }
	  else{
		  $vulmessage = "In progress..";
		  $vuldate    = "...";
	  }

      //If there are some findings, show the notification bubble on Netsecop menu.
      if (($blmessage != "OK" && $blmessage != "In progress..") || ($rmmessage != "OK" && $rmmessage != "In progress..") || ($vulmessage != "OK" && $vulmessage != "In progress..")){
        $display_bubble = 1;
        $options = get_option( 'netsecop_options');
        $options['show_bubble'] = 1;
        update_option('netsecop_options', $options);
      }

      $netsecop_scan_results=array(
        'blm'  => $blmessage,
        'bld'  => $bldate,
        'rmm' => $rmmessage,
        'rmd'  => $rmdate,
        'vulm' => $vulmessage,
        'vuld'  => $vuldate,
      );

      update_option('netsecop_check', $netsecop_scan_results);
    }
    else{
      echo '<p>ERR-5 An Error Occured!</p><p>If this problem persists, please contact us via info@netsecop.com</p>';
    }
  }
}


$options = get_option( 'netsecop_options', netsecop_options_default() );
if ( isset( $options['show_bubble'] ) && ! empty( $options['show_bubble'] ) ) {
  $display_bubble = sanitize_text_field( $options['show_bubble'] );
}

if ( $display_bubble == 1 ) {
  $bubble = sprintf(' <span class="update-plugins"><span class="update-count">%d</span></span>',1);
}
else{
  $bubble = sprintf('');
}

/*
add_menu_page(
string   $page_title,
string   $menu_title,
string   $capability,
string   $menu_slug,
callable $function = '',
string   $icon_url = '',
int      $position = null
)
*/

add_menu_page(
  'Netsecop Settings',
  'Netsecop' . $bubble,
  'manage_options',
  'netsecop',
  'netsecop_display_settings_page',
  'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAAUCAYAAABroNZJAAAAAXNSR0IArs4c6QAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACXBIWXMAAAsTAAALEwEAmpwYAAAEF2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS40LjAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgICAgICAgICAgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iCiAgICAgICAgICAgIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICAgICA8eG1wTU06RGVyaXZlZEZyb20gcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICA8c3RSZWY6aW5zdGFuY2VJRD54bXAuaWlkOjVERTA0REY3NTNGQTExRTdCMTExRTk2OUE1NzA0MUE1PC9zdFJlZjppbnN0YW5jZUlEPgogICAgICAgICAgICA8c3RSZWY6ZG9jdW1lbnRJRD54bXAuZGlkOjVERTA0REY4NTNGQTExRTdCMTExRTk2OUE1NzA0MUE1PC9zdFJlZjpkb2N1bWVudElEPgogICAgICAgICA8L3htcE1NOkRlcml2ZWRGcm9tPgogICAgICAgICA8eG1wTU06RG9jdW1lbnRJRD54bXAuZGlkOjVERTA0REZBNTNGQTExRTdCMTExRTk2OUE1NzA0MUE1PC94bXBNTTpEb2N1bWVudElEPgogICAgICAgICA8eG1wTU06SW5zdGFuY2VJRD54bXAuaWlkOjVERTA0REY5NTNGQTExRTdCMTExRTk2OUE1NzA0MUE1PC94bXBNTTpJbnN0YW5jZUlEPgogICAgICAgICA8eG1wOkNyZWF0b3JUb29sPkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE3IChNYWNpbnRvc2gpPC94bXA6Q3JlYXRvclRvb2w+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgq7hOyFAAACZ0lEQVQ4EWWSS0hUYRSA5874mOyNRvZCJDdFEkQbw2CkVbZo5aJFi17goiDbSq0LQ6VVLcKFCNIuchlRIVhCIIYRRDE9DArLVJLKmW7f93tvWB747nn8555z/kcUx/HvTCYzD2XIQgyK/v+SSwIR+gfUw0AFnwU4DpOQB4uapK1W1Bb9CTax2Sz0Qp1FlOewF07Ba/gCVWC3tFAldgkaoBa64Q1st4gjmtgIZ+AV3IHrsAbMsZh5l6EDLHIVwhB+HN8uD2ACnKof5qMo+obOcG457DK6B3c3lPGL+GH7FnGP+SQ4jn0Pe4aEk/Ae+yGxDuwZ7Pvou/gbQLF57AG5lRKLNWi7hBHRL/SJe1abwG0q5jcFK7nBtIh73g87oN2CdH2G7eG692n8t8S3Yh+GQ9jVaP/Lpl2xw6l/Rm+DayTdQE+BZ/YOvxl9Dpx4DnZBGSKLmGRHxzwIPjzfQydcYYLHFLBwF2yBI/Ad1kIQi/jzEuwDu4zBetgIBQrYtQ7qk9g4ug0awf/CJN6OhYrgq/UHC47AefAmPsFmKMBowkd0E5Q8WLeyCMOgeMB28EY8hxbYA07ti22FD2zTidxSjUV+QQ/BBXQ72MlH5A++Uqd0WrHQALkn2OYB7IvwxNfYDFPwFDwHYzfhEcyC4vWOwqVk/TT2InTrB8GphCGYg6MG0W0wDcoYeKXGb4N5x/SD4Ph0U/ssvh36DKCd0qnysA4mYBJ2JusV2B5JSM6lDtofX4Lb811YrABfYVBfwf7bfDmyHIzSBbRFh6EIt8Cz8bEFwV5dIF1Ur0zAvgA++UKylsX2xv6RP/76eyrybJ9dAAAAAElFTkSuQmCC',
  null
);
}
add_action( 'admin_menu', 'netsecop_add_toplevel_menu' );
