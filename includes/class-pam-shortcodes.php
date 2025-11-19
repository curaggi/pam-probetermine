<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Shortcodes {

    public function register() {
        add_shortcode( 'probe_form', [ $this, 'render_probe_form' ] );
        add_shortcode( 'probe_search', [ $this, 'render_probe_search' ] );
    }

    public function render_probe_form( $atts = [] ) {
        if ( ! PAM_Permissions::user_can_access() ) {
            return '<p>' . esc_html__( 'Nur für Praxis-Mitarbeiter sichtbar.', 'pam-probetermine' ) . '</p>';
        }

        ob_start();
        include PAM_PT_PATH . 'public/views/shortcode-form.php';
        return ob_get_clean();
    }

    public function render_probe_search( $atts = [] ) {
        if ( ! PAM_Permissions::user_can_access() ) {
            return '<p>' . esc_html__( 'Nur für Praxis-Mitarbeiter sichtbar.', 'pam-probetermine' ) . '</p>';
        }

        ob_start();
        include PAM_PT_PATH . 'public/views/shortcode-search.php';
        return ob_get_clean();
    }
}
