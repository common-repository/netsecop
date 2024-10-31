<?php //NETSECOP Settings Callbacks


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

require_once plugin_dir_path(__FILE__) . '../netsecop-scanner.php';


//CALLBACK FUNCTIONS FOR SECTIONS

// callback: freescan section
function netsecop_callback_section_freescan() {

    //echo '<p>These settings enable you to customize the FreeScan settings.</p>';

}

// callback: general section
function netsecop_callback_section_general() {

    //echo '<p>These settings enable you to customize the general settings.</p>';
    echo '<p><input id="coupon" type="text" size="40" value=""><button type="button" onclick="alert(&quot;Sorry, coupon code you entered is not valid&quot;)">Apply Coupon</button><br />';
    echo '<label for="netsecop_options_">Please ask your domain/hosting provider for a Netsecop Premium Services coupon code.</label></p>';

}

//CALLBACK FUNCTIONS FOR FIELDS

// callback: text field
function netsecop_callback_field_text( $args ) {

    $options = get_option( 'netsecop_options', netsecop_options_default() );

    $id    = isset( $args['id'] )    ? $args['id']    : '';
    $label = isset( $args['label'] ) ? $args['label'] : '';

    $value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';

    echo '<input id="netsecop_options_'. $id .'" name="netsecop_options['. $id .']" type="text" size="40" value="'. $value .'"><br />';
    echo '<label for="netsecop_options_'. $id .'">'. $label .'</label>';
}

// callback: email text field
function netsecop_callback_email_field_text( $args ) {

    $options = get_option( 'netsecop_options', netsecop_options_default() );

    $id    = isset( $args['id'] )    ? $args['id']    : '';
    $label = isset( $args['label'] ) ? $args['label'] : '';

    $value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';


    echo '<input id="netsecop_options_'. $id .'" name="netsecop_options['. $id .']" type="text" size="40" value="'. $value .'"><br />';
    echo '<label for="netsecop_options_'. $id .'">'. $label .'</label>';
}

// callback: text field disabled
function netsecop_callback_field_label( $args ) {

    $options = get_option( 'netsecop_options', netsecop_options_default() );

    $id    = isset( $args['id'] )    ? $args['id']    : '';
    $label = isset( $args['label'] ) ? $args['label'] : '';

    $value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';

    echo '<label for="netsecop_options_'.$id .'"> '. $value .'</label><br />';
    echo '<label for="netsecop_options_'. $id .'">'. $label .'</label>';
    echo '<input type="hidden" id="netsecop_options_'. $id .'" name="netsecop_options['. $id .']" type="text" size="40" value="'. $value .'"><br />';

}

// callback: checkbox field
function netsecop_callback_field_checkbox( $args ) {

    $options = get_option( 'netsecop_options', netsecop_options_default() );

    $id    = isset( $args['id'] )    ? $args['id']    : '';
    $label = isset( $args['label'] ) ? $args['label'] : '';

    $checked = isset( $options[$id] ) ? checked( $options[$id], 1, false ) : '';

    echo '<input id="netsecop_options_'. $id .'" name="netsecop_options['. $id .']" type="checkbox" value="1"'. $checked .'> ';
    echo '<label for="netsecop_options_'. $id .'">'. $label .'</label>';

}

// callback: checkbox field
function netsecop_callback_field_seal_checkbox( $args ) {

	$options = get_option( 'netsecop_options', netsecop_options_default() );

	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';

	$checked = isset( $options[$id] ) ? checked( $options[$id], 1, false ) : '';

	echo '<input id="netsecop_options_'. $id .'" name="netsecop_options['. $id .']" type="checkbox" value="1"'. $checked .'> ';
	echo '<label for="netsecop_options_'. $id .'">'. $label .'</label>';
	echo '<p>* Netsecop discovers vulnerabilities in your website. In order to start vulnerability scan and access sensitive results, we need you to verify the ownership of the website. For this purpose we created a very tiny, semi-transparent Netsecop seal. Netsecop scanner will check the presence of the Netsecop seal before starting the vulnerability scan. Please click the checkbox to learn your vulnerabilities.</p>';

}
