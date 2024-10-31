<?php //NETECOP Settings Registration



// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}



// register plugin settings
function netsecop_register_settings() {

    /*

    register_setting(
        string   $option_group,
        string   $option_name,
        callable $sanitize_callback
    );

    */

    register_setting(
        'netsecop_options',
        'netsecop_options',
        'netsecop_callback_validate_options'
    );

    // SECTION'LAR:  SETTINGS SAYFASINDAKI ANA BASLIKLAR
    /*

    add_settings_section(
        string   $id,
        string   $title,
        callable $callback,
        string   $page the section should displayed ( $menu_slug )
    );

    */

    add_settings_section(
        'netsecop_section_freescan',
        'Free Scan Settings',
        'netsecop_callback_section_freescan',
        'netsecop'
    );

    add_settings_section(
        'netsecop_section_general',
        'General Settings',
        'netsecop_callback_section_general',
        'netsecop'
    );
/*
    add_settings_section(
        'netsecop_section_login',
        'Customize Login Page',
        'netsecop_callback_section_login',
        'netsecop'
    );
*/



    // FIELD'LAR:  SECTIONLARIN ALTINDAKI ALT BASLIKLAR
    /*

    add_settings_field(
    string   $id,
    string   $title,
    callable $callback, callback function that will will be called
    string   $page, ( $menu_slug )
    string   $section = 'default',   (section id)
    array    $args = []  arguments that are passed to the callback function.
    );

    */

    add_settings_field(
        'website_url',
        'Website URL',
        'netsecop_callback_field_label',
        'netsecop',
        'netsecop_section_freescan',
        [ 'id' => 'website_url', 'label' => '' ]
    );

    if (get_option( 'netsecop_free_scan_initialized') == true){
      add_settings_field(
          'email_address',
          'Email Address',
          'netsecop_callback_field_label',
          'netsecop',
          'netsecop_section_freescan',
          [ 'id' => 'email_address', 'label' => 'Your scan results will be sent to this email address.' ]
      );
    }
    else{
      add_settings_field(
          'email_address',
          'Email Address',
          'netsecop_callback_email_field_text',
          'netsecop',
          'netsecop_section_freescan',
          [ 'id' => 'email_address', 'label' => 'Please update your e-mail address to start Netsecop security scan!' ]
      );
    }

    add_settings_field(
        'show_seal',
        'Show Netsecop Seal',
        'netsecop_callback_field_seal_checkbox',
        'netsecop',
        'netsecop_section_general',
        [ 'id' => 'show_seal', 'label' => 'Show Netsecop seal in your web pages.' ]
    );

/*
    add_settings_field(
        'custom_url_login',
        'Custom wp-login URL',
        'netsecop_callback_field_text',
        'netsecop',
        'netsecop_section_login',
        [ 'id' => 'custom_url_login', 'label' => 'Custom URL instead of wp-login.php' ]
    );

    add_settings_field(
        'custom_url_admin',
        'Custom wp-admin URL',
        'netsecop_callback_field_text',
        'netsecop',
        'netsecop_section_login',
        [ 'id' => 'custom_url_admin', 'label' => 'Custom URL instead of wp-admin.php' ]
    );
*/

}
add_action( 'admin_init', 'netsecop_register_settings' );
