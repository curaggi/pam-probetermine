<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Zentrale Plugin-Einstellungen
 * - Duplikatsperre (Tage)
 * - erforderliche Capability für Zugriff
 */
class PAM_Settings {

    const OPTION_KEY = 'pam_pt_settings';

    /**
     * Liefert alle Settings mit Defaults gemerged
     */
    public static function get_settings() {
        $defaults = [
            'duplicate_days' => 365,
            'capability'     => 'edit_pages',
        ];

        $options = get_option( self::OPTION_KEY, [] );
        if ( ! is_array( $options ) ) {
            $options = [];
        }

        return wp_parse_args( $options, $defaults );
    }

    /**
     * Einzelnen Setting-Wert holen
     */
    public static function get( $key, $default = null ) {
        $settings = self::get_settings();
        return array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
    }

    /**
     * Settings aktualisieren (nur erlaubte Keys)
     */
    public static function update( $data ) {
        $settings = self::get_settings();

        if ( isset( $data['duplicate_days'] ) ) {
            $settings['duplicate_days'] = max( 0, (int) $data['duplicate_days'] );
        }

        if ( isset( $data['capability'] ) ) {
            $settings['capability'] = sanitize_text_field( $data['capability'] );
        }

        update_option( self::OPTION_KEY, $settings );
    }
}
