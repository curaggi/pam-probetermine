<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$message = '';
$error   = '';

$devices = PAM_Devices::get_active_for_select();
$staff   = PAM_Staff::get_active_for_select();

if ( isset( $_POST['pam_pt_trial_nonce'] ) && wp_verify_nonce( $_POST['pam_pt_trial_nonce'], 'pam_pt_trial_save' ) ) {

    $patient_id   = isset( $_POST['patient_id'] ) ? (int) $_POST['patient_id'] : 0;
    $patient_name = pam_pt_sanitize_text( $_POST['patient_name'] ?? '' );
    $birthdate    = sanitize_text_field( $_POST['birthdate'] ?? '' );
    $device_id    = isset( $_POST['device_id'] ) ? (int) $_POST['device_id'] : 0;
    $staff_id     = isset( $_POST['staff_id'] ) ? (int) $_POST['staff_id'] : 0;
    $source       = pam_pt_sanitize_text( $_POST['source'] ?? '' );
    $notes        = pam_pt_sanitize_textarea( $_POST['notes'] ?? '' );
    $date_input   = sanitize_text_field( $_POST['date'] ?? '' );

    if ( empty( $date_input ) ) {
        $date = current_time( 'mysql' );
    } else {
        $date = date( 'Y-m-d H:i:s', strtotime( $date_input ) );
    }

    if ( ! $patient_id && ! empty( $patient_name ) ) {
        $patient_id = PAM_Patients::insert(
            [
                'name'      => $patient_name,
                'birthdate' => $birthdate ?: null,
                'notes'     => '',
            ]
        );
    }

    if ( ! $patient_id || ! $device_id || ! $staff_id ) {
        $error = __( 'Bitte Patient, Gerät und Mitarbeiter auswählen.', 'pam-probetermine' );
    } else {
        if ( PAM_Duplicate_Check::exists_recent( $patient_id, $device_id ) ) {
            $error = __( 'Dieser Patient hatte bereits eine kostenlose Testbehandlung mit diesem Gerät (innerhalb 365 Tage).', 'pam-probetermine' );
        } else {
            PAM_Trials::insert(
                [
                    'patient_id'        => $patient_id,
                    'device_id'         => $device_id,
                    'staff_id'          => $staff_id,
                    'date'              => $date,
                    'source'            => $source,
                    'conversion_status' => 'offen',
                    'notes'             => $notes,
                ]
            );
            $message = __( 'Testbehandlung angelegt.', 'pam-probetermine' );
        }
    }
}
?>
<div class="pam-pt-wrap">
    <h1><?php esc_html_e( 'Neue Testbehandlung', 'pam-probetermine' ); ?></h1>

    <?php if ( $message ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
    <?php endif; ?>

    <?php if ( $error ) : ?>
        <div class="notice notice-error is-dismissible"><p><?php echo esc_html( $error ); ?></p></div>
    <?php endif; ?>

    <form method="post" class="pam-pt-form">
        <?php wp_nonce_field( 'pam_pt_trial_save', 'pam_pt_trial_nonce' ); ?>

        <div class="pam-pt-form-grid">
            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Patient', 'pam-probetermine' ); ?></label>
                <input type="hidden" name="patient_id" id="pam_patient_id" value="">
                <input type="text" name="patient_name" id="pam_patient_name" class="regular-text" placeholder="<?php esc_attr_e( 'Name des Patienten', 'pam-probetermine' ); ?>">
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Geburtsdatum (optional)', 'pam-probetermine' ); ?></label>
                <input type="date" name="birthdate" value="">
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Gerät', 'pam-probetermine' ); ?></label>
                <select name="device_id" required>
                    <option value=""><?php esc_html_e( 'Bitte wählen', 'pam-probetermine' ); ?></option>
                    <?php foreach ( $devices as $d ) : ?>
                        <option value="<?php echo (int) $d->id; ?>"><?php echo esc_html( $d->name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Mitarbeiter', 'pam-probetermine' ); ?></label>
                <select name="staff_id" required>
                    <option value=""><?php esc_html_e( 'Bitte wählen', 'pam-probetermine' ); ?></option>
                    <?php foreach ( $staff as $s ) : ?>
                        <option value="<?php echo (int) $s->id; ?>"><?php echo esc_html( $s->name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Quelle', 'pam-probetermine' ); ?></label>
                <select name="source">
                    <option value="therapeut"><?php esc_html_e( 'Therapeut', 'pam-probetermine' ); ?></option>
                    <option value="rezeption"><?php esc_html_e( 'Rezeption', 'pam-probetermine' ); ?></option>
                </select>
            </div>

            <div class="pam-pt-field-group">
                <label><?php esc_html_e( 'Datum/Uhrzeit', 'pam-probetermine' ); ?></label>
                <input type="datetime-local" name="date" value="">
            </div>

            <div class="pam-pt-field-group pam-pt-field-full">
                <label><?php esc_html_e( 'Notizen', 'pam-probetermine' ); ?></label>
                <textarea name="notes" rows="4"></textarea>
            </div>
        </div>

        <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Speichern', 'pam-probetermine' ); ?></button></p>
    </form>
</div>
