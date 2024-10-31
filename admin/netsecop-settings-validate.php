<?php


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}


// callback: validate options
function netsecop_callback_validate_options( $input ) {

    // website_url
    if ( isset( $input['website_url'] ) ) {

        $input['website_url'] = esc_url( $input['website_url'] );
    }

    // coupon_code
    if ( isset( $input['coupon_code'] ) ) {

        $input['coupon_code'] = sanitize_text_field( $input['coupon_code'] );
    }

    // email_address
    if ( isset( $input['email_address'] ) ) {

        $input['email_address'] = sanitize_text_field( $input['email_address'] );
    }

    // show_seal
    if ( ! isset( $input['show_seal'] ) ) {

        $input['show_seal'] = null;
    }

    $input['show_seal'] = ($input['show_seal'] == 1 ? 1 : 0);

    // show_bubble
    if ( ! isset( $input['show_bubble'] ) ) {

        $input['show_bubble'] = null;
    }

    $input['show_bubble'] = ($input['show_bubble'] == 1 ? 1 : 0);


    return $input;

}
