<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = PAM_Settings::get_settings();
$message  = '';

if ( isset( $_POST['pam_pt_settings_nonce'] ) && wp_verify_nonce( $_POST['pam_pt_settings_nonce'], 'pam_pt_settings_save' ) ) {
    $duplicate_days = isset( $_POST['duplicate_days'] ) ? (int) $_POST['duplicate_days'] : 365;
    $capability     = sanitize_text_field( $_POST['capability'] ?? 'edit_pages' );

    PAM_Settings::update(
        [
            'duplicate_days' => $duplicate_days,
            'capability'     => $capability,
        ]
    );

    $settings = PAM_Settings::get_settings();
    $message  = __( 'Einstellungen gespeichert.', 'pam-probetermine' );
}
?>
<div class="pam-pt-wrap">
    <h1><?php esc_html_e( 'Probe-Termine – Einstellungen', 'pam-probetermine' ); ?></h1>

    <?php if ( $message ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
    <?php endif; ?>

    <form method="post">
        <?php wp_nonce_field( 'pam_pt_settings_save', 'pam_pt_settings_nonce' ); ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pam_pt_duplicate_days"><?php esc_html_e( 'Duplikatsperre (Tage)', 'pam-probetermine' ); ?></label>
                </th>
                <td>
                    <input type="number" min="0" id="pam_pt_duplicate_days" name="duplicate_days" value="<?php echo (int) $settings['duplicate_days']; ?>" class="small-text" />
                    <p class="description">
                        <?php esc_html_e( 'Zeitraum, in dem ein Patient nicht erneut eine kostenlose Testbehandlung für dasselbe Gerät bekommen darf. 0 = keine Sperre.', 'pam-probetermine' ); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="pam_pt_capability"><?php esc_html_e( 'Erforderliche Capability', 'pam-probetermine' ); ?></label>
                </th>
                <td>
                    <input type="text" id="pam_pt_capability" name="capability" value="<?php echo esc_attr( $settings['capability'] ); ?>" class="regular-text" />
                    <p class="description">
                        <?php esc_html_e( 'WordPress-Capability, die Mitarbeitende benötigen, um das Plugin nutzen zu können (z. B. edit_pages, manage_options, manage_woocommerce).', 'pam-probetermine' ); ?>
                    </p>
                </td>
            </tr>
        </table>

        <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Speichern', 'pam-probetermine' ); ?></button></p>
    </form>
</div>
