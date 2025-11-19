<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$id      = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
$patient = PAM_Patients::get( $id );
if ( ! $patient ) {
    echo '<p>' . esc_html__( 'Patient nicht gefunden.', 'pam-probetermine' ) . '</p>';
    return;
}

$message = '';
if ( isset( $_POST['pam_pt_patient_nonce'] ) && wp_verify_nonce( $_POST['pam_pt_patient_nonce'], 'pam_pt_patient_save' ) ) {
    $name      = pam_pt_sanitize_text( $_POST['name'] ?? '' );
    $birthdate = sanitize_text_field( $_POST['birthdate'] ?? '' );
    $notes     = pam_pt_sanitize_textarea( $_POST['notes'] ?? '' );

    PAM_Patients::update(
        $id,
        [
            'name'      => $name,
            'birthdate' => $birthdate ?: null,
            'notes'     => $notes,
        ]
    );

    $patient  = PAM_Patients::get( $id );
    $message  = __( 'Patientendaten aktualisiert.', 'pam-probetermine' );
}

$trials = PAM_Trials::get_by_patient( $id );
?>
<div class="pam-pt-wrap">
    <h1><?php esc_html_e( 'Patientendetails', 'pam-probetermine' ); ?></h1>

    <?php if ( $message ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
    <?php endif; ?>

    <div class="pam-pt-two-col">
        <div class="pam-pt-col">
            <h2><?php esc_html_e( 'Stammdaten', 'pam-probetermine' ); ?></h2>
            <form method="post">
                <?php wp_nonce_field( 'pam_pt_patient_save', 'pam_pt_patient_nonce' ); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="pam_name"><?php esc_html_e( 'Name', 'pam-probetermine' ); ?></label></th>
                        <td><input type="text" id="pam_name" name="name" class="regular-text" value="<?php echo esc_attr( $patient->name ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="pam_birth"><?php esc_html_e( 'Geburtsdatum', 'pam-probetermine' ); ?></label></th>
                        <td><input type="date" id="pam_birth" name="birthdate" value="<?php echo esc_attr( $patient->birthdate ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="pam_notes"><?php esc_html_e( 'Notizen', 'pam-probetermine' ); ?></label></th>
                        <td><textarea id="pam_notes" name="notes" rows="5" class="large-text"><?php echo esc_textarea( $patient->notes ); ?></textarea></td>
                    </tr>
                </table>
                <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Speichern', 'pam-probetermine' ); ?></button></p>
            </form>
        </div>

        <div class="pam-pt-col">
            <h2><?php esc_html_e( 'Historie Testbehandlungen', 'pam-probetermine' ); ?></h2>

            <?php if ( $trials ) : ?>
                <table class="pam-pt-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Datum', 'pam-probetermine' ); ?></th>
                            <th><?php esc_html_e( 'Gerät', 'pam-probetermine' ); ?></th>
                            <th><?php esc_html_e( 'Mitarbeiter', 'pam-probetermine' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'pam-probetermine' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $trials as $t ) : ?>
                            <tr>
                                <td><?php echo esc_html( pam_pt_format_datetime( $t->date ) ); ?></td>
                                <td><?php echo esc_html( $t->device_name ); ?></td>
                                <td><?php echo esc_html( $t->staff_name ); ?></td>
                                <td><?php echo esc_html( $t->conversion_status ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e( 'Keine Testbehandlungen für diesen Patienten.', 'pam-probetermine' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
