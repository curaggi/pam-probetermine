<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$patients = PAM_Patients::get_all();
?>
<div class="pam-pt-wrap">
    <h1><?php esc_html_e( 'Patienten', 'pam-probetermine' ); ?></h1>

    <table class="pam-pt-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Name', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Geburtsdatum', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Anzahl Tests', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Letzter Test', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Aktionen', 'pam-probetermine' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $patients ) : foreach ( $patients as $p ) : ?>
                <tr>
                    <td><?php echo esc_html( $p->name ); ?></td>
                    <td><?php echo esc_html( pam_pt_format_date( $p->birthdate ) ); ?></td>
                    <td><?php echo (int) $p->trials_count; ?></td>
                    <td><?php echo esc_html( pam_pt_format_datetime( $p->last_trial_date ) ); ?></td>
                    <td>
                        <a class="button button-small" href="<?php echo esc_url( add_query_arg( [ 'page' => 'pam_pt_patients', 'view' => 'detail', 'id' => $p->id ], admin_url( 'admin.php' ) ) ); ?>">
                            <?php esc_html_e( 'Details', 'pam-probetermine' ); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; else : ?>
                <tr><td colspan="5"><?php esc_html_e( 'Keine Patienten gefunden.', 'pam-probetermine' ); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
