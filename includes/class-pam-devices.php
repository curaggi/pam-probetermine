<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PAM_Devices {

    public static function table() {
        global $wpdb;
        return $wpdb->prefix . 'pam_devices';
    }

    public static function get_all( $only_active = false ) {
        global $wpdb;
        $table = self::table();
        $where = $only_active ? 'WHERE active = 1' : '';
        $sql   = "SELECT * FROM $table $where ORDER BY sort_order ASC, name ASC";
        return $wpdb->get_results( $sql );
    }

    public static function get_active_for_select() {
        return self::get_all( true );
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
                'slug'       => $data['slug'],
                'icon'       => $data['icon'],
                'active'     => (int) $data['active'],
                'sort_order' => (int) $data['sort_order'],
            ],
            [ '%s', '%s', '%s', '%d', '%d' ]
        );

        return $wpdb->insert_id;
    }

    public static function update( $id, $data ) {
        global $wpdb;
        $table = self::table();

        return $wpdb->update(
            $table,
            [
                'name'       => $data['name'],
                'slug'       => $data['slug'],
                'icon'       => $data['icon'],
                'active'     => (int) $data['active'],
                'sort_order' => (int) $data['sort_order'],
            ],
            [ 'id' => (int) $id ],
            [ '%s', '%s', '%s', '%d', '%d' ],
            [ '%d' ]
        );
    }

    public static function deactivate( $id ) {
        global $wpdb;
        $table = self::table();
        return $wpdb->update(
            $table,
            [ 'active' => 0 ],
            [ 'id' => (int) $id ],
            [ '%d' ],
            [ '%d' ]
        );
    }
}
