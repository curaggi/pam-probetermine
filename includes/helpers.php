<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pam_pt_sanitize_int( $value ) {
    return (int) $value;
}

function pam_pt_sanitize_text( $value ) {
    return sanitize_text_field( $value );
}

function pam_pt_sanitize_textarea( $value ) {
    return sanitize_textarea_field( $value );
}

function pam_pt_format_datetime( $datetime ) {
    if ( empty( $datetime ) || $datetime === '0000-00-00 00:00:00' ) {
        return '';
    }
    $ts = strtotime( $datetime );
    if ( ! $ts ) {
        return '';
    }
    return date_i18n( 'd.m.Y H:i', $ts );
}

function pam_pt_format_date( $date ) {
    if ( empty( $date ) || $date === '0000-00-00' ) {
        return '';
    }
    $ts = strtotime( $date );
    if ( ! $ts ) {
        return '';
    }
    return date_i18n( 'd.m.Y', $ts );
}
