<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$id    = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
$trial = PAM_Trials::get( $id );
if ( ! $trial ) {
    echo '<p>' . esc_html__( 'Testbehandlung nicht gefunden.', 'pam-probetermine' ) . '</p>';
    return;
}

$message = '';
$error   = '';

$devices = PAM_Devices::get_active_for_select();
$staff   = PAM_Staff::get_active_for_select();

if ( isset( $_POST['pam_pt_trial_nonce'] ) && wp_verify_nonce( $_POST['pam_pt_trial_nonce'], 'pam_pt_trial_save' ) ) {
    $device_id  = (int) ( $_POST['device_id'] ?? 0 );
    $staff_id   = (int) ( $_POST['staff_id'] ?? 0 );
    $source     = pam_pt_sanitize_text( $_POST['source'] ?? '' );
    $status     = pam_pt_sanitize_text( $_POST['conversion_status'] ?? 'offen' );
    $notes      = pam_pt_sanitize_textarea( $_POST['notes'] ?? '' );
    $date_input = sanitize_text_field( $_POST['date'] ?? '' );
    $date       = $date_input ? date( 'Y-m-d H:i:s', strtotime( $date_input ) ) : $trial->date;

    if ( ! $device_id || ! $staff_id ) {
        $error = __( 'Bitte Gerät und Mitarbeiter auswählen.', 'pam-probetermine' );
    } else {
        PAM_Trials::update(
            $id,
            [
                'device_id'         => $device_id,
                'staff_id'          => $staff_id,
                'date'              => $date,
                'source'            => $source,
                'conversion_status' => $status,
                'notes'             => $notes,
            ]
        );
        $trial   = PAM_Trials::get( $id );
        $message = __( 'Testbehandlung aktualisiert.', 'pam-probetermine' );
    }
}
?>
<div class="pam-pt-wrap">
    <h1><?php esc_html_e( 'Testbehandlung bearbeiten', 'pam-probetermine' ); ?></h1>

    <?php if ( $message ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
    <?php endif; ?>

    <?php if ( $error ) : ?>
        <div class="notice notice-error is-dismissible"><p><?php echo esc_html( $error ); ?></p></div>
    <?php endif; ?>

    <form method="post" class="pam-pt-form">
        <?php wp_nonce_field( 'pam_pt_trial_save', 'pam_pt_trial_nonce' ); ?>

        <p><strong><?php esc_html_e( 'Patient:', 'pam-probetermine' ); ?></strong> <?php echo esc_html( $trial->patient_name ); ?></p>

        <div class="pam-pt-form-grid">
            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Gerät', 'pam-probetermine' ); ?></label>
                <select name="device_id" required>
                    <?php foreach ( $devices as $d ) : ?>
                        <option value="<?php echo (int) $d->id; ?>" <?php selected( $trial->device_id, $d->id ); ?>>
                            <?php echo esc_html( $d->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Mitarbeiter', 'pam-probetermine' ); ?></label>
                <select name="staff_id" required>
                    <?php foreach ( $staff as $s ) : ?>
                        <option value="<?php echo (int) $s->id; ?>" <?php selected( $trial->staff_id, $s->id ); ?>>
                            <?php echo esc_html( $s->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Quelle', 'pam-probetermine' ); ?></label>
                <select name="source">
                    <option value="therapeut" <?php selected( $trial->source, 'therapeut' ); ?>><?php esc_html_e( 'Therapeut', 'pam-probetermine' ); ?></option>
                    <option value="rezeption" <?php selected( $trial->source, 'rezeption' ); ?>><?php esc_html_e( 'Rezeption', 'pam-probetermine' ); ?></option>
                </select>
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Datum/Uhrzeit', 'pam-probetermine' ); ?></label>
                <input type="datetime-local" name="date" value="<?php echo esc_attr( date( 'Y-m-d\TH:i', strtotime( $trial->date ) ) ); ?>">
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Conversion-Status', 'pam-probetermine' ); ?></label>
                <select name="conversion_status">
                    <?php
                    $statuses = [ 'offen', 'interesse', 'gebucht', 'kein_interesse' ];
                    foreach ( $statuses as $st ) :
                    ?>
                        <option value="<?php echo esc_attr( $st ); ?>" <?php selected( $trial->conversion_status, $st ); ?>>
                            <?php echo esc_html( ucfirst( $st ) ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pam-pt-field-group pam-pt-field-full">
                <label><?php esc_html_e( 'Notizen', 'pam-probetermine' ); ?></label>
                <textarea name="notes" rows="4"><?php echo esc_textarea( $trial->notes ); ?></textarea>
            </div>
        </div>

        <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Speichern', 'pam-probetermine' ); ?></button></p>
    </form>
</div>
