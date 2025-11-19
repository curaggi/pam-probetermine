<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function register_menu() {
        if ( ! PAM_Permissions::user_can_access() ) {
            return;
        }

        $cap = PAM_Permissions::required_capability();

        add_menu_page(
            __( 'Probe-Termine', 'pam-probetermine' ),
            __( 'Probe-Termine', 'pam-probetermine' ),
            $cap,
            'pam_pt_dashboard',
            [ $this, 'render_dashboard' ],
            'dashicons-heart',
            26
        );

        add_submenu_page(
            'pam_pt_dashboard',
            __( 'Dashboard', 'pam-probetermine' ),
            __( 'Dashboard', 'pam-probetermine' ),
            $cap,
            'pam_pt_dashboard',
            [ $this, 'render_dashboard' ]
        );

        add_submenu_page(
            'pam_pt_dashboard',
            __( 'Geräte', 'pam-probetermine' ),
            __( 'Geräte', 'pam-probetermine' ),
            $cap,
            'pam_pt_devices',
            [ $this, 'render_devices' ]
        );

        add_submenu_page(
            'pam_pt_dashboard',
            __( 'Mitarbeiter', 'pam-probetermine' ),
            __( 'Mitarbeiter', 'pam-probetermine' ),
            $cap,
            'pam_pt_staff',
            [ $this, 'render_staff' ]
        );

        add_submenu_page(
            'pam_pt_dashboard',
            __( 'Patienten', 'pam-probetermine' ),
            __( 'Patienten', 'pam-probetermine' ),
            $cap,
            'pam_pt_patients',
            [ $this, 'render_patients' ]
        );

        add_submenu_page(
            'pam_pt_dashboard',
            __( 'Testbehandlungen', 'pam-probetermine' ),
            __( 'Testbehandlungen', 'pam-probetermine' ),
            $cap,
            'pam_pt_trials',
            [ $this, 'render_trials' ]
        );

        add_submenu_page(
            'pam_pt_dashboard',
            __( 'Einstellungen', 'pam-probetermine' ),
            __( 'Einstellungen', 'pam-probetermine' ),
            $cap,
            'pam_pt_settings',
            [ $this, 'render_settings' ]
        );
    }

    public function enqueue_assets( $hook_suffix ) {
        if ( strpos( $hook_suffix, 'pam_pt_' ) === false ) {
            return;
        }

        // Admin-CSS
        wp_enqueue_style(
            'pam_pt_admin',
            PAM_PT_URL . 'assets/css/admin.css',
            [],
            PAM_PT_VERSION
        );

        // Chart.js nur für unsere Admin-Pages
        wp_enqueue_script(
            'pam_pt_chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js',
            [],
            '4.4.0',
            true
        );

        // Admin-JS
        wp_enqueue_script(
            'pam_pt_admin',
            PAM_PT_URL . 'assets/js/admin.js',
            [ 'jquery', 'pam_pt_chartjs' ],
            PAM_PT_VERSION,
            true
        );
    }

    public function render_dashboard() {
        PAM_Permissions::enforce();
        include PAM_PT_PATH . 'admin/views/dashboard.php';
    }

    public function render_devices() {
        PAM_Permissions::enforce();
        include PAM_PT_PATH . 'admin/views/devices.php';
    }

    public function render_staff() {
        PAM_Permissions::enforce();
        include PAM_PT_PATH . 'admin/views/staff.php';
    }

    public function render_patients() {
        PAM_Permissions::enforce();

        if ( isset( $_GET['view'] ) && $_GET['view'] === 'detail' && ! empty( $_GET['id'] ) ) {
            include PAM_PT_PATH . 'admin/views/patient-detail.php';
        } else {
            include PAM_PT_PATH . 'admin/views/patients.php';
        }
    }

    public function render_trials() {
        PAM_Permissions::enforce();

        if ( isset( $_GET['action'] ) && $_GET['action'] === 'new' ) {
            include PAM_PT_PATH . 'admin/views/trial-new.php';
        } elseif ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && ! empty( $_GET['id'] ) ) {
            include PAM_PT_PATH . 'admin/views/trial-edit.php';
        } else {
            include PAM_PT_PATH . 'admin/views/trials.php';
        }
    }

    public function render_settings() {
        PAM_Permissions::enforce();
        include PAM_PT_PATH . 'admin/views/settings.php';
    }
}
