<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$message = '';
if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == '1' ) {
    $message = __( 'Testbehandlung gelöscht.', 'pam-probetermine' );
}

if ( isset( $_GET['delete'] ) && ! empty( $_GET['delete'] ) && check_admin_referer( 'pam_pt_trial_delete_' . (int) $_GET['delete'] ) ) {
    PAM_Trials::delete( (int) $_GET['delete'] );
    wp_safe_redirect( add_query_arg( [ 'page' => 'pam_pt_trials', 'deleted' => 1 ], admin_url( 'admin.php' ) ) );
    exit;
}

$date_from = sanitize_text_field( $_GET['date_from'] ?? '' );
$date_to   = sanitize_text_field( $_GET['date_to'] ?? '' );
$device_id = isset( $_GET['device_id'] ) ? (int) $_GET['device_id'] : 0;
$staff_id  = isset( $_GET['staff_id'] ) ? (int) $_GET['staff_id'] : 0;
$status    = sanitize_text_field( $_GET['status'] ?? '' );

$filters = [
    'date_from'         => $date_from,
    'date_to'           => $date_to,
    'device_id'         => $device_id,
    'staff_id'          => $staff_id,
    'conversion_status' => $status,
];

$trials   = PAM_Trials::get_filtered( $filters );
$devices  = PAM_Devices::get_active_for_select();
$staff    = PAM_Staff::get_active_for_select();
?>
<div class="pam-pt-wrap">
    <h1><?php esc_html_e( 'Testbehandlungen', 'pam-probetermine' ); ?></h1>

    <?php if ( $message ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
    <?php endif; ?>

    <p>
        <a class="button button-primary" href="<?php echo esc_url( add_query_arg( [ 'page' => 'pam_pt_trials', 'action' => 'new' ], admin_url( 'admin.php' ) ) ); ?>">
            <?php esc_html_e( 'Neue Testbehandlung anlegen', 'pam-probetermine' ); ?>
        </a>
    </p>

    <form method="get" class="pam-pt-filter-form">
        <input type="hidden" name="page" value="pam_pt_trials" />
        <div class="pam-pt-filter-grid">
            <div>
                <label><?php esc_html_e( 'Von', 'pam-probetermine' ); ?></label>
                <input type="date" name="date_from" value="<?php echo esc_attr( $date_from ); ?>">
            </div>
            <div>
                <label><?php esc_html_e( 'Bis', 'pam-probetermine' ); ?></label>
                <input type="date" name="date_to" value="<?php echo esc_attr( $date_to ); ?>">
            </div>
            <div>
                <label><?php esc_html_e( 'Gerät', 'pam-probetermine' ); ?></label>
                <select name="device_id">
                    <option value="0"><?php esc_html_e( 'Alle', 'pam-probetermine' ); ?></option>
                    <?php foreach ( $devices as $d ) : ?>
                        <option value="<?php echo (int) $d->id; ?>" <?php selected( $device_id, $d->id ); ?>>
                            <?php echo esc_html( $d->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><?php esc_html_e( 'Mitarbeiter', 'pam-probetermine' ); ?></label>
                <select name="staff_id">
                    <option value="0"><?php esc_html_e( 'Alle', 'pam-probetermine' ); ?></option>
                    <?php foreach ( $staff as $s ) : ?>
                        <option value="<?php echo (int) $s->id; ?>" <?php selected( $staff_id, $s->id ); ?>>
                            <?php echo esc_html( $s->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><?php esc_html_e( 'Status', 'pam-probetermine' ); ?></label>
                <select name="status">
                    <option value=""><?php esc_html_e( 'Alle', 'pam-probetermine' ); ?></option>
                    <?php
                    $statuses = [ 'offen', 'interesse', 'gebucht', 'kein_interesse' ];
                    foreach ( $statuses as $st ) :
                    ?>
                        <option value="<?php echo esc_attr( $st ); ?>" <?php selected( $status, $st ); ?>>
                            <?php echo esc_html( ucfirst( $st ) ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="pam-pt-filter-actions">
                <button type="submit" class="button"><?php esc_html_e( 'Filtern', 'pam-probetermine' ); ?></button>
            </div>
        </div>
    </form>

    <table class="pam-pt-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Datum/Uhrzeit', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Patient', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Gerät', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Mitarbeiter', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Quelle', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Status', 'pam-probetermine' ); ?></th>
                <th><?php esc_html_e( 'Aktionen', 'pam-probetermine' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $trials ) : foreach ( $trials as $t ) : ?>
                <tr>
                    <td><?php echo esc_html( pam_pt_format_datetime( $t->date ) ); ?></td>
                    <td><?php echo esc_html( $t->patient_name ); ?></td>
                    <td><?php echo esc_html( $t->device_name ); ?></td>
                    <td><?php echo esc_html( $t->staff_name ); ?></td>
                    <td><?php echo esc_html( $t->source ); ?></td>
                    <td><?php echo esc_html( $t->conversion_status ); ?></td>
                    <td>
                        <a class="button button-small" href="<?php echo esc_url( add_query_arg( [ 'page' => 'pam_pt_trials', 'action' => 'edit', 'id' => $t->id ], admin_url( 'admin.php' ) ) ); ?>">
                            <?php esc_html_e( 'Bearbeiten', 'pam-probetermine' ); ?>
                        </a>
                        <a class="button button-small button-link-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'page' => 'pam_pt_trials', 'delete' => $t->id ], admin_url( 'admin.php' ) ), 'pam_pt_trial_delete_' . $t->id ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Wirklich löschen?', 'pam-probetermine' ) ); ?>');">
                            <?php esc_html_e( 'Löschen', 'pam-probetermine' ); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; else : ?>
                <tr><td colspan="7"><?php esc_html_e( 'Keine Testbehandlungen gefunden.', 'pam-probetermine' ); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
