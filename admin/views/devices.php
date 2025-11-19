<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$message = '';

if ( isset( $_POST['pam_pt_device_nonce'] ) && wp_verify_nonce( $_POST['pam_pt_device_nonce'], 'pam_pt_device_save' ) ) {
    $id         = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
    $name       = pam_pt_sanitize_text( $_POST['name'] ?? '' );
    $slug       = sanitize_title( $_POST['slug'] ?? $name );
    $icon       = pam_pt_sanitize_text( $_POST['icon'] ?? '' );
    $active     = isset( $_POST['active'] ) ? 1 : 0;
    $sort_order = (int) ( $_POST['sort_order'] ?? 0 );

    if ( ! empty( $name ) ) {
        $data = compact( 'name', 'slug', 'icon', 'active', 'sort_order' );
        if ( $id > 0 ) {
            PAM_Devices::update( $id, $data );
            $message = __( 'Gerät aktualisiert.', 'pam-probetermine' );
        } else {
            PAM_Devices::insert( $data );
            $message = __( 'Gerät hinzugefügt.', 'pam-probetermine' );
        }
    }
}

if ( isset( $_GET['deactivate'] ) && ! empty( $_GET['deactivate'] ) && check_admin_referer( 'pam_pt_device_deactivate_' . (int) $_GET['deactivate'] ) ) {
    PAM_Devices::deactivate( (int) $_GET['deactivate'] );
    $message = __( 'Gerät deaktiviert.', 'pam-probetermine' );
}

$edit_device = null;
if ( isset( $_GET['edit'] ) ) {
    $edit_device = PAM_Devices::get( (int) $_GET['edit'] );
}

$devices = PAM_Devices::get_all();
?>
<div class="pam-pt-wrap">
    <h1><?php esc_html_e( 'Geräteverwaltung', 'pam-probetermine' ); ?></h1>

    <?php if ( $message ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
    <?php endif; ?>

    <div class="pam-pt-two-col">
        <div class="pam-pt-col">
            <h2><?php echo $edit_device ? esc_html__( 'Gerät bearbeiten', 'pam-probetermine' ) : esc_html__( 'Neues Gerät', 'pam-probetermine' ); ?></h2>

            <form method="post">
                <?php wp_nonce_field( 'pam_pt_device_save', 'pam_pt_device_nonce' ); ?>
                <input type="hidden" name="id" value="<?php echo $edit_device ? (int) $edit_device->id : 0; ?>" />

                <table class="form-table">
                    <tr>
                        <th><label for="pam_name"><?php esc_html_e( 'Name', 'pam-probetermine' ); ?></label></th>
                        <td><input type="text" id="pam_name" name="name" class="regular-text" required value="<?php echo $edit_device ? esc_attr( $edit_device->name ) : ''; ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="pam_slug"><?php esc_html_e( 'Slug', 'pam-probetermine' ); ?></label></th>
                        <td><input type="text" id="pam_slug" name="slug" class="regular-text" value="<?php echo $edit_device ? esc_attr( $edit_device->slug ) : ''; ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="pam_icon"><?php esc_html_e( 'Icon-Pfad (optional)', 'pam-probetermine' ); ?></label></th>
                        <td><input type="text" id="pam_icon" name="icon" class="regular-text" value="<?php echo $edit_device ? esc_attr( $edit_device->icon ) : ''; ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="pam_sort"><?php esc_html_e( 'Sortierung', 'pam-probetermine' ); ?></label></th>
                        <td><input type="number" id="pam_sort" name="sort_order" class="small-text" value="<?php echo $edit_device ? (int) $edit_device->sort_order : 0; ?>"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Aktiv', 'pam-probetermine' ); ?></th>
                        <td><label><input type="checkbox" name="active" <?php checked( $edit_device ? (int) $edit_device->active : 1, 1 ); ?>> <?php esc_html_e( 'Aktives Gerät', 'pam-probetermine' ); ?></label></td>
                    </tr>
                </table>

                <p><button type="submit" class="button button-primary"><?php esc_html_e( 'Speichern', 'pam-probetermine' ); ?></button></p>
            </form>
        </div>

        <div class="pam-pt-col">
            <h2><?php esc_html_e( 'Geräteliste', 'pam-probetermine' ); ?></h2>

            <table class="pam-pt-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'pam-probetermine' ); ?></th>
                        <th><?php esc_html_e( 'Slug', 'pam-probetermine' ); ?></th>
                        <th><?php esc_html_e( 'Aktiv', 'pam-probetermine' ); ?></th>
                        <th><?php esc_html_e( 'Sortierung', 'pam-probetermine' ); ?></th>
                        <th><?php esc_html_e( 'Aktionen', 'pam-probetermine' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $devices ) : foreach ( $devices as $device ) : ?>
                        <tr>
                            <td><?php echo esc_html( $device->name ); ?></td>
                            <td><?php echo esc_html( $device->slug ); ?></td>
                            <td><?php echo $device->active ? esc_html__( 'Ja', 'pam-probetermine' ) : esc_html__( 'Nein', 'pam-probetermine' ); ?></td>
                            <td><?php echo (int) $device->sort_order; ?></td>
                            <td>
                                <a class="button button-small" href="<?php echo esc_url( add_query_arg( [ 'page' => 'pam_pt_devices', 'edit' => $device->id ], admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Bearbeiten', 'pam-probetermine' ); ?></a>
                                <?php if ( $device->active ) : ?>
                                    <a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'page' => 'pam_pt_devices', 'deactivate' => $device->id ], admin_url( 'admin.php' ) ), 'pam_pt_device_deactivate_' . $device->id ) ); ?>"><?php esc_html_e( 'Deaktivieren', 'pam-probetermine' ); ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="5"><?php esc_html_e( 'Keine Geräte gefunden.', 'pam-probetermine' ); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
