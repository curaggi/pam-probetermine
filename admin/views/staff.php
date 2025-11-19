<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$message = '';

if ( isset( $_POST['pam_pt_staff_nonce'] ) && wp_verify_nonce( $_POST['pam_pt_staff_nonce'], 'pam_pt_staff_save' ) ) {
    $id     = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
    $name   = pam_pt_sanitize_text( $_POST['name'] ?? '' );
    $role   = pam_pt_sanitize_text( $_POST['role'] ?? '' );
    $active = isset( $_POST['active'] ) ? 1 : 0;

    if ( ! empty( $name ) && ! empty( $role ) ) {
        $data = compact( 'name', 'role', 'active' );
        if ( $id > 0 ) {
            PAM_Staff::update( $id, $data );
            $message = __( 'Mitarbeiter aktualisiert.', 'pam-probetermine' );
        } else {
            PAM_Staff::insert( $data );
            $message = __( 'Mitarbeiter hinzugefügt.', 'pam-probetermine' );
        }
    }
}

if ( isset( $_GET['deactivate'] ) && ! empty( $_GET['deactivate'] ) && check_admin_referer( 'pam_pt_staff_deactivate_' . (int) $_GET['deactivate'] ) ) {
    PAM_Staff::deactivate( (int) $_GET['deactivate'] );
    $message = __( 'Mitarbeiter deaktiviert.', 'pam-probetermine' );
}

$edit_staff = null;
if ( isset( $_GET['edit'] ) ) {
    $edit_staff = PAM_Staff::get( (int) $_GET['edit'] );
}

$staff = PAM_Staff::get_all();
?>
<div class="pam-pt-wrap">
    <h1><?php esc_html_e( 'Mitarbeiterverwaltung', 'pam-probetermine' ); ?></h1>

    <?php if ( $message ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
    <?php endif; ?>

    <div class="pam-pt-two-col">
        <div class="pam-pt-col">
            <h2><?php echo $edit_staff ? esc_html__( 'Mitarbeiter bearbeiten', 'pam-probetermine' ) : esc_html__( 'Neuer Mitarbeiter', 'pam-probetermine' ); ?></h2>

            <form method="post">
                <?php wp_nonce_field( 'pam_pt_staff_save', 'pam_pt_staff_nonce' ); ?>
                <input type="hidden" name="id" value="<?php echo $edit_staff ? (int) $edit_staff->id : 0; ?>" />

                <table class="form-table">
                    <tr>
                        <th><label for="pam_name"><?php esc_html_e( 'Name', 'pam-probetermine' ); ?></label></th>
                        <td><input type="text" id="pam_name" name="name" class="regular-text" required value="<?php echo $edit_staff ? esc_attr( $edit_staff->name ) : ''; ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="pam_role"><?php esc_html_e( 'Rolle', 'pam-probetermine' ); ?></label></th>
                        <td>
                            <select id="pam_role" name="role">
                                <option value="therapeut" <?php selected( $edit_staff ? $edit_staff->role : '', 'therapeut' ); ?>><?php esc_html_e( 'Therapeut', 'pam-probetermine' ); ?></option>
                                <option value="rezeption" <?php selected( $edit_staff ? $edit_staff->role : '', 'rezeption' ); ?>><?php esc_html_e( 'Rezeption', 'pam-probetermine' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Aktiv', 'pam-probetermine' ); ?></th>
                        <td><label><input type="checkbox" name="active" <?php checked( $edit_staff ? (int) $edit_staff->active : 1, 1 ); ?>> <?php esc_html_e( 'Aktiver Mitarbeiter', 'pam-probetermine' ); ?></label></td>
                    </tr>
                </table>

                <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Speichern', 'pam-probetermine' ); ?></button></p>
            </form>
        </div>

        <div class="pam-pt-col">
            <h2><?php esc_html_e( 'Mitarbeiterliste', 'pam-probetermine' ); ?></h2>

            <table class="pam-pt-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'pam-probetermine' ); ?></th>
                        <th><?php esc_html_e( 'Rolle', 'pam-probetermine' ); ?></th>
                        <th><?php esc_html_e( 'Aktiv', 'pam-probetermine' ); ?></th>
                        <th><?php esc_html_e( 'Aktionen', 'pam-probetermine' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $staff ) : foreach ( $staff as $row ) : ?>
                        <tr>
                            <td><?php echo esc_html( $row->name ); ?></td>
                            <td><?php echo esc_html( $row->role ); ?></td>
                            <td><?php echo $row->active ? esc_html__( 'Ja', 'pam-probetermine' ) : esc_html__( 'Nein', 'pam-probetermine' ); ?></td>
                            <td>
                                <a class="button button-small" href="<?php echo esc_url( add_query_arg( [ 'page' => 'pam_pt_staff', 'edit' => $row->id ], admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Bearbeiten', 'pam-probetermine' ); ?></a>
                                <?php if ( $row->active ) : ?>
                                    <a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'page' => 'pam_pt_staff', 'deactivate' => $row->id ], admin_url( 'admin.php' ) ), 'pam_pt_staff_deactivate_' . $row->id ) ); ?>"><?php esc_html_e( 'Deaktivieren', 'pam-probetermine' ); ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="4"><?php esc_html_e( 'Keine Mitarbeiter gefunden.', 'pam-probetermine' ); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
