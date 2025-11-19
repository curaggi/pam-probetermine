<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Patients {

    public static function table() {
        global $wpdb;
        return $wpdb->prefix . 'pam_patients';
    }

    public static function get_all() {
        global $wpdb;
        $pat_table   = self::table();
        $trials_table = $wpdb->prefix . 'pam_trials';

        $sql = "SELECT p.*,
                COUNT(t.id) AS trials_count,
                MAX(t.date) AS last_trial_date
            FROM $pat_table p
            LEFT JOIN $trials_table t ON t.patient_id = p.id
            GROUP BY p.id
            ORDER BY p.name ASC";

        return $wpdb->get_results( $sql );
    }

    public static function get( $id ) {
        global $wpdb;
        $table = self::table();
        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", (int) $id )
        );
    }

    public static function insert( $data ) {
        global $wpdb;
        $table = self::table();

        $wpdb->insert(
            $table,
            [
                'name'       => $data['name'],
                'birthdate'  => $data['birthdate'] ?: null,
                'notes'      => $data['notes'],
                'created_at' => current_time( 'mysql' ),
            ],
            [ '%s', '%s', '%s', '%s' ]
        );

        return $wpdb->insert_id;
    }

    public static function update( $id, $data ) {
        global $wpdb;
        $table = self::table();

        return $wpdb->update(
            $table,
            [
                'name'      => $data['name'],
                'birthdate' => $data['birthdate'] ?: null,
                'notes'     => $data['notes'],
            ],
            [ 'id' => (int) $id ],
            [ '%s', '%s', '%s' ],
            [ '%d' ]
        );
    }

    public static function search_by_name( $name, $limit = 20 ) {
        global $wpdb;
        $table = self::table();
        $like  = '%' . $wpdb->esc_like( $name ) . '%';

        $sql = $wpdb->prepare(
            "SELECT * FROM $table WHERE name LIKE %s ORDER BY name ASC LIMIT %d",
            $like,
            (int) $limit
        );

        return $wpdb->get_results( $sql );
    }
}
