<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$search_name = pam_pt_sanitize_text($_GET['pam_pt_search_name']??'');
$matches=[];$selected_patient=null;$trials=[];
if($search_name){
  $matches = PAM_Patients::search_by_name($search_name,20);
  if(count($matches)===1){
    $selected_patient = $matches[0];
    $trials = PAM_Trials::get_by_patient($selected_patient->id);
  }
}
?>
<div class="pam-pt-frontend-wrap">
  <div class="pam-pt-card">
    <h2><?php esc_html_e('Probe-Termine prüfen','pam-probetermine'); ?></h2>
    <form method="get" class="pam-pt-form">
      <div class="pam-pt-form-grid">
        <div class="pam-pt-field-group pam-pt-field-full">
          <label><?php esc_html_e('Patientenname','pam-probetermine'); ?></label>
          <input type="text" name="pam_pt_search_name" class="pam-pt-input" value="<?php echo esc_attr($search_name); ?>">
        </div>
      </div>
      <div class="pam-pt-actions">
        <button type="submit" class="pam-pt-btn-primary"><?php esc_html_e('Suchen','pam-probetermine'); ?></button>
      </div>
    </form>
    <?php if($selected_patient): ?>
      <h3><?php echo esc_html($selected_patient->name); ?></h3>
      <?php if($trials): ?>
        <table class="pam-pt-table">
          <thead><tr><th><?php esc_html_e('Datum','pam-probetermine'); ?></th><th><?php esc_html_e('Gerät','pam-probetermine'); ?></th><th><?php esc_html_e('Mitarbeiter','pam-probetermine'); ?></th><th><?php esc_html_e('Status','pam-probetermine'); ?></th></tr></thead>
          <tbody>
            <?php foreach($trials as $t): ?>
            <tr>
              <td><?php echo esc_html(pam_pt_format_datetime($t->date)); ?></td>
              <td><?php echo esc_html($t->device_name); ?></td>
              <td><?php echo esc_html($t->staff_name); ?></td>
              <td><?php echo esc_html($t->conversion_status); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?><p><?php esc_html_e('Keine Testbehandlungen gefunden.','pam-probetermine'); ?></p><?php endif; ?>
    <?php elseif($search_name && !$matches): ?>
      <p><?php esc_html_e('Keine Patienten gefunden.','pam-probetermine'); ?></p>
    <?php endif; ?>
  </div>
</div>
