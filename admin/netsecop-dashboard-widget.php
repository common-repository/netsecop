<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

//DASHBOARD WIDGET
add_action('wp_dashboard_setup', 'add_netsecop_dashboard_widget');

function add_netsecop_dashboard_widget() {
  global $wp_meta_boxes;

  wp_add_dashboard_widget('netsecop_dashboard_widget', 'Netsecop', 'netsecop_results');

  //////////START Put widget to the first row  //////////

  $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

  // Backup and delete our new dashboard widget from the end of the array
  $netsecop_widget_backup = array( 'netsecop_dashboard_widget' => $normal_dashboard['netsecop_dashboard_widget'] );
  unset( $normal_dashboard['netsecop_dashboard_widget'] );

  // Merge the two arrays together so our widget is at the beginning
  $sorted_dashboard = array_merge( $netsecop_widget_backup, $normal_dashboard );

  // Save the sorted array back into the original metaboxes
  $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

  ////////// END Put widget to the first row  //////////

}

function netsecop_results() {
  //If token is not present, we can't get the scan results.
  $NETSECOPACCESSTOKEN = get_option('netsecop_token');
  $ISSCANINITIALIZZED = get_option('netsecop_free_scan_initialized');
  if (empty($NETSECOPACCESSTOKEN) || $NETSECOPACCESSTOKEN == false || empty($ISSCANINITIALIZZED) || $ISSCANINITIALIZZED == 0 || $ISSCANINITIALIZZED == false){
    echo '<p>Your Netsecop Security Scan has not been initialized yet.</p><p>Please check your Netsecop scan settings on the left menu.</p>';
  }
  else{
    $resuts = get_option('netsecop_check');
    if (empty($resuts) || $resuts == false ){ // Token is present, but the scan result are not there.
      echo '<p>Your Netsecop Security Scanis in progress. Results will be updated soon.</p>';
    }
    else{ //Everything is good, show last scan results.
      echo'
      <a href="https://www.netsecop.com/signin.html"><center><img src="https://www.netsecop.com/files/images/netsecop-logo_3.png" width="218" height="58" alt="Netsecop Logo"></center>
      <div><p><strong>Blacklist Scan</strong>: lasest: ' . $resuts['bld'] . '<br/>';
      if($resuts['blm'] == 'OK'){
        echo '<span style="color:green;">Not Blacklisted</span>';
      }
      else if ($resuts['blm'] == 'In progress..'){
	      echo '<span style="color:orange;">In progress..</span>';
      }
      else{
        echo '<span style="color:#C70039;">Blacklisted!</span>';
      }
      echo '<p>
      </div>
      <div><p><strong>Malware Scan</strong>: lasest: ' . $resuts['rmd'] . '<br/>';
      if($resuts['rmm'] == 'OK'){
        echo '<span style="color:green;">No Malware Found.</span>';
      }
      else if ($resuts['rmm'] == 'In progress..'){
	      echo '<span style="color:orange;">In progress..</span>';
      }
      else{
        echo '<span style="color:#C70039;">Malware Detected!</span>';
      }
      echo '<p>
      </div>
      <div><p><strong>Vulnerability Scan</strong>: lasest: ' . $resuts['vuld'] . '<br/>';
      if($resuts['vulm'] == 'OK'){
        echo '<span style="color:green;">No Vulnerabilities Found</span>';
      }
      else if ($resuts['vulm'] == 'In progress..'){
	      echo '<span style="color:orange;">In progress..</span>';
      }
      else{
        $array = explode(',', $resuts['vulm']);
        $info_vuln_array = explode(':', $array[0]);
        $low_vuln_array = explode(':', $array[1]);
        $med_vuln_array = explode(':', $array[2]);
        $high_vuln_array = explode(':', $array[3]);
        echo '<span><u>Scan Report:</u></span><br/><span style="color:#C70039;">' . ucfirst(netsecop_string_strip($info_vuln_array[0])) . ' : ' . netsecop_string_strip($info_vuln_array[1]) . '</span><br/>';
        echo '<span style="color:#C70039;">' . ucfirst(netsecop_string_strip($low_vuln_array[0])) . ' : ' . netsecop_string_strip($low_vuln_array[1]) . '</span><br/>';
        echo '<span style="color:#C70039;">' . ucfirst(netsecop_string_strip($med_vuln_array[0])) . ' : ' . netsecop_string_strip($med_vuln_array[1]) . '</span><br/>';
        echo '<span style="color:#C70039;">' . ucfirst(netsecop_string_strip($high_vuln_array[0])) . ' : ' . netsecop_string_strip($high_vuln_array[1]) . '</span>';
      }
      echo '<p>
      </div>';
    }
  }
}

function netsecop_string_strip($string){
  $string = trim($string, "{}\"'");
  return $string;
}
