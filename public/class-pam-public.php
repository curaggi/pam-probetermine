<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Öffentliche Frontend-Assets für Probe-Termine
 */
class PAM_Public {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Enqueue Medical-UI Styles & JS nur für eingeloggte Nutzer
     */
    public function enqueue_assets() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        // Basis-Frontend-Styles (Medical UI)
        wp_enqueue_style(
            'pam_pt_medical_ui',
            PAM_PT_URL . 'assets/css/medical-ui.css',
            [],
            defined( 'PAM_PT_VERSION' ) ? PAM_PT_VERSION : '1.0.0'
        );

        // Frontend-Enhancements (Device-List etc.)
        wp_enqueue_script(
            'pam_pt_frontend_ui',
            PAM_PT_URL . 'assets/js/frontend-ui.js',
            [ 'jquery' ],
            defined( 'PAM_PT_VERSION' ) ? PAM_PT_VERSION : '1.0.0',
            true
        );
    }
}
