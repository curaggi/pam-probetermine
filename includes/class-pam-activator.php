<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Activator {

    public static function activate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $devices_table   = $wpdb->prefix . 'pam_devices';
        $staff_table     = $wpdb->prefix . 'pam_staff';
        $patients_table  = $wpdb->prefix . 'pam_patients';
        $trials_table    = $wpdb->prefix . 'pam_trials';

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql_devices = "CREATE TABLE $devices_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            icon VARCHAR(255) NULL,
            active TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT NOT NULL DEFAULT 0,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql_staff = "CREATE TABLE $staff_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            active TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql_patients = "CREATE TABLE $patients_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            birthdate DATE NULL,
            notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql_trials = "CREATE TABLE $trials_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            patient_id BIGINT UNSIGNED NOT NULL,
            device_id BIGINT UNSIGNED NOT NULL,
            staff_id BIGINT UNSIGNED NOT NULL,
            date DATETIME NOT NULL,
            source VARCHAR(50) NULL,
            conversion_status VARCHAR(50) NOT NULL DEFAULT 'offen',
            notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY patient_id (patient_id),
            KEY device_id (device_id),
            KEY staff_id (staff_id),
            KEY date (date)
        ) $charset_collate;";

        dbDelta( $sql_devices );
        dbDelta( $sql_staff );
        dbDelta( $sql_patients );
        dbDelta( $sql_trials );
    }
}
