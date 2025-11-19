<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Permissions {

    public static function required_capability() {
        // Basis-Capability aus Settings
        $settings_cap = 'edit_pages';
        if ( class_exists( 'PAM_Settings' ) ) {
            $settings_cap = PAM_Settings::get( 'capability', 'edit_pages' );
        }

        // Filter bleibt erhalten für Entwickler
        $cap = apply_filters( 'pam_pt_required_capability', $settings_cap );
        return $cap;
    }

    public static function user_can_access() {
        $cap = self::required_capability();
        return is_user_logged_in() && current_user_can( $cap );
    }

    public static function enforce() {
        if ( ! self::user_can_access() ) {
            wp_die( esc_html__( 'Kein Zugriff auf diese Funktion.', 'pam-probetermine' ) );
        }
    }
}
