<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Duplicate_Check {

    public static function exists_recent( $patient_id, $device_id ) {
        global $wpdb;

        // Tage aus Settings holen, Default 365
        $settings_days = 365;
        if ( class_exists( 'PAM_Settings' ) ) {
            $settings_days = (int) PAM_Settings::get( 'duplicate_days', 365 );
        }

        // Filter bleibt erhalten, falls jemand programmatisch überschreiben will
        $days = (int) apply_filters( 'pam_pt_duplicate_days', $settings_days );

        $table = PAM_Trials::table();

        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table
             WHERE patient_id = %d
               AND device_id = %d
               AND date >= DATE_SUB(NOW(), INTERVAL %d DAY)",
            (int) $patient_id,
            (int) $device_id,
            $days
        );

        return (int) $wpdb->get_var( $sql ) > 0;
    }
}
