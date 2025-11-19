<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Trials {

    public static function table() {
        global $wpdb;
        return $wpdb->prefix . 'pam_trials';
    }

    public static function insert( $data ) {
        global $wpdb;
        $table = self::table();

        $wpdb->insert(
            $table,
            [
                'patient_id'        => (int) $data['patient_id'],
                'device_id'         => (int) $data['device_id'],
                'staff_id'          => (int) $data['staff_id'],
                'date'              => $data['date'],
                'source'            => $data['source'],
                'conversion_status' => $data['conversion_status'] ?? 'offen',
                'notes'             => $data['notes'],
                'created_at'        => current_time( 'mysql' ),
            ],
            [ '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s' ]
        );

        return $wpdb->insert_id;
    }

    public static function update( $id, $data ) {
        global $wpdb;
        $table = self::table();

        return $wpdb->update(
            $table,
            [
                'device_id'         => (int) $data['device_id'],
                'staff_id'          => (int) $data['staff_id'],
                'date'              => $data['date'],
                'source'            => $data['source'],
                'conversion_status' => $data['conversion_status'],
                'notes'             => $data['notes'],
            ],
            [ 'id' => (int) $id ],
            [ '%d', '%d', '%s', '%s', '%s', '%s' ],
            [ '%d' ]
        );
    }

    public static function delete( $id ) {
        global $wpdb;
        $table = self::table();
        return $wpdb->delete( $table, [ 'id' => (int) $id ], [ '%d' ] );
    }

    public static function get( $id ) {
        global $wpdb;
        $table       = self::table();
        $patients    = $wpdb->prefix . 'pam_patients';
        $devices     = $wpdb->prefix . 'pam_devices';
        $staff_table = $wpdb->prefix . 'pam_staff';

        $sql = $wpdb->prepare(
            "SELECT t.*, 
                    p.name AS patient_name,
                    d.name AS device_name,
                    s.name AS staff_name
             FROM $table t
             LEFT JOIN $patients p ON p.id = t.patient_id
             LEFT JOIN $devices d ON d.id = t.device_id
             LEFT JOIN $staff_table s ON s.id = t.staff_id
             WHERE t.id = %d",
            (int) $id
        );

        return $wpdb->get_row( $sql );
    }

    public static function get_latest( $limit = 20 ) {
        global $wpdb;
        $table       = self::table();
        $patients    = $wpdb->prefix . 'pam_patients';
        $devices     = $wpdb->prefix . 'pam_devices';
        $staff_table = $wpdb->prefix . 'pam_staff';

        $sql = $wpdb->prepare(
            "SELECT t.*, 
                    p.name AS patient_name,
                    d.name AS device_name,
                    s.name AS staff_name
             FROM $table t
             LEFT JOIN $patients p ON p.id = t.patient_id
             LEFT JOIN $devices d ON d.id = t.device_id
             LEFT JOIN $staff_table s ON s.id = t.staff_id
             ORDER BY t.date DESC
             LIMIT %d",
            (int) $limit
        );

        return $wpdb->get_results( $sql );
    }

    public static function count_today() {
        global $wpdb;
        $table = self::table();
        $today = current_time( 'Y-m-d' );
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE DATE(date) = %s",
                $today
            )
        );
    }

    public static function count_total() {
        global $wpdb;
        $table = self::table();
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
    }

    public static function conversion_rate_total() {
        global $wpdb;
        $table = self::table();

        $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
        if ( $total === 0 ) {
            return 0;
        }

        $booked = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM $table WHERE conversion_status = 'gebucht'"
        );

        return round( ( $booked / $total ) * 100, 1 );
    }

    public static function top_device() {
        global $wpdb;
        $table   = self::table();
        $devices = $wpdb->prefix . 'pam_devices';

        $sql = "SELECT d.name, COUNT(t.id) AS cnt
                FROM $table t
                INNER JOIN $devices d ON d.id = t.device_id
                GROUP BY d.id
                ORDER BY cnt DESC
                LIMIT 1";

        return $wpdb->get_row( $sql );
    }

    public static function top_staff() {
        global $wpdb;
        $table = self::table();
        $staff = $wpdb->prefix . 'pam_staff';

        $sql = "SELECT s.name, COUNT(t.id) AS cnt
                FROM $table t
                INNER JOIN $staff s ON s.id = t.staff_id
                GROUP BY s.id
                ORDER BY cnt DESC
                LIMIT 1";

        return $wpdb->get_row( $sql );
    }

    public static function get_filtered( $args = [] ) {
        global $wpdb;

        $table       = self::table();
        $patients    = $wpdb->prefix . 'pam_patients';
        $devices     = $wpdb->prefix . 'pam_devices';
        $staff_table = $wpdb->prefix . 'pam_staff';

        $where = [];
        $params = [];

        if ( ! empty( $args['date_from'] ) ) {
            $where[]  = "date >= %s";
            $params[] = $args['date_from'] . ' 00:00:00';
        }
        if ( ! empty( $args['date_to'] ) ) {
            $where[]  = "date <= %s";
            $params[] = $args['date_to'] . ' 23:59:59';
        }
        if ( ! empty( $args['device_id'] ) ) {
            $where[]  = "device_id = %d";
            $params[] = (int) $args['device_id'];
        }
        if ( ! empty( $args['staff_id'] ) ) {
            $where[]  = "staff_id = %d";
            $params[] = (int) $args['staff_id'];
        }
        if ( ! empty( $args['conversion_status'] ) ) {
            $where[]  = "conversion_status = %s";
            $params[] = $args['conversion_status'];
        }

        $where_sql = '';
        if ( ! empty( $where ) ) {
            $where_sql = 'WHERE ' . join( ' AND ', $where );
        }

        $sql = "SELECT t.*,
                       p.name AS patient_name,
                       d.name AS device_name,
                       s.name AS staff_name
                FROM $table t
                LEFT JOIN $patients p ON p.id = t.patient_id
                LEFT JOIN $devices d ON d.id = t.device_id
                LEFT JOIN $staff_table s ON s.id = t.staff_id
                $where_sql
                ORDER BY t.date DESC";

        if ( ! empty( $params ) ) {
            $sql = $wpdb->prepare( $sql, $params );
        }

        return $wpdb->get_results( $sql );
    }

    public static function get_by_patient( $patient_id ) {
        global $wpdb;
        $table       = self::table();
        $devices     = $wpdb->prefix . 'pam_devices';
        $staff_table = $wpdb->prefix . 'pam_staff';

        $sql = $wpdb->prepare(
            "SELECT t.*,
                    d.name AS device_name,
                    s.name AS staff_name
             FROM $table t
             LEFT JOIN $devices d ON d.id = t.device_id
             LEFT JOIN $staff_table s ON s.id = t.staff_id
             WHERE t.patient_id = %d
             ORDER BY t.date DESC",
            (int) $patient_id
        );

        return $wpdb->get_results( $sql );
    }

    public static function get_daily_counts_last_days( $days = 14 ) {
        global $wpdb;
        $table = self::table();

        $sql = $wpdb->prepare(
            "SELECT DATE(date) AS day, COUNT(*) AS cnt
             FROM $table
             WHERE date >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
             GROUP BY DATE(date)
             ORDER BY day ASC",
            (int) $days
        );

        return $wpdb->get_results( $sql );
    }
}
