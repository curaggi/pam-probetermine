<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Core {

    public function init() {
        // Reihenfolge: Settings -> Permissions -> Rest
        require_once PAM_PT_PATH . 'includes/class-pam-settings.php';
        require_once PAM_PT_PATH . 'includes/class-pam-permissions.php';
        require_once PAM_PT_PATH . 'includes/helpers.php';
        require_once PAM_PT_PATH . 'includes/class-pam-devices.php';
        require_once PAM_PT_PATH . 'includes/class-pam-staff.php';
        require_once PAM_PT_PATH . 'includes/class-pam-patients.php';
        require_once PAM_PT_PATH . 'includes/class-pam-trials.php';
        require_once PAM_PT_PATH . 'includes/class-pam-duplicate-check.php';
        require_once PAM_PT_PATH . 'includes/class-pam-shortcodes.php';

        if ( is_admin() ) {
            require_once PAM_PT_PATH . 'admin/class-pam-admin.php';
            new PAM_Admin();
        }

        require_once PAM_PT_PATH . 'public/class-pam-public.php';
        new PAM_Public();

        $shortcodes = new PAM_Shortcodes();
        $shortcodes->register();
    }
}
