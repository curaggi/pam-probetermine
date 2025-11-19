<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$message='';$error='';
$devices = PAM_Devices::get_active_for_select();
$staff   = PAM_Staff::get_active_for_select();
if(isset($_POST['pam_pt_public_trial_nonce']) && wp_verify_nonce($_POST['pam_pt_public_trial_nonce'],'pam_pt_public_trial_save')){
  $patient_name = pam_pt_sanitize_text($_POST['patient_name']??'');
  $birthdate    = sanitize_text_field($_POST['birthdate']??'');
  $device_id    = (int)($_POST['device_id']??0);
  $staff_id     = (int)($_POST['staff_id']??0);
  $source       = pam_pt_sanitize_text($_POST['source']??'');
  $notes        = pam_pt_sanitize_textarea($_POST['notes']??'');
  $date_input   = sanitize_text_field($_POST['date']??'');
  $date         = $date_input?date('Y-m-d H:i:s',strtotime($date_input)):current_time('mysql');
  $patient_id   = 0;
  if($patient_name){
    $patient_id = PAM_Patients::insert(['name'=>$patient_name,'birthdate'=>$birthdate?:null,'notes'=>'']);
  }
  if(!$patient_id || !$device_id || !$staff_id){
    $error = __('Bitte Patient, Gerät und Mitarbeiter auswählen.','pam-probetermine');
  }else{
    if(PAM_Duplicate_Check::exists_recent($patient_id,$device_id)){
      $error = __('Dieser Patient hatte bereits eine kostenlose Testbehandlung mit diesem Gerät (innerhalb 365 Tage).','pam-probetermine');
    }else{
      PAM_Trials::insert(['patient_id'=>$patient_id,'device_id'=>$device_id,'staff_id'=>$staff_id,'date'=>$date,'source'=>$source,'conversion_status'=>'offen','notes'=>$notes]);
      $message = __('Testbehandlung angelegt.','pam-probetermine');
    }
  }
}
?>
<div class="pam-pt-frontend-wrap">
  <div class="pam-pt-card">
    <h2><?php esc_html_e('Neuen Probe-Termin erfassen','pam-probetermine'); ?></h2>
    <?php if($message): ?><div class="pam-pt-alert pam-pt-alert-success"><?php echo esc_html($message); ?></div><?php endif; ?>
    <?php if($error): ?><div class="pam-pt-alert pam-pt-alert-error"><?php echo esc_html($error); ?></div><?php endif; ?>
    <form method="post" class="pam-pt-form">
      <?php wp_nonce_field('pam_pt_public_trial_save','pam_pt_public_trial_nonce'); ?>
      <div class="pam-pt-form-grid">
        <div class="pam-pt-field-group">
          <label><?php esc_html_e('Patient','pam-probetermine'); ?></label>
          <input type="text" name="patient_name" class="pam-pt-input" required>
        </div>
        <div class="pam-pt-field-group">
          <label><?php esc_html_e('Geburtsdatum','pam-probetermine'); ?></label>
          <input type="date" name="birthdate" class="pam-pt-input">
        </div>
        <div class="pam-pt-field-group">
          <label><?php esc_html_e('Gerät','pam-probetermine'); ?></label>
          <select name="device_id" class="pam-pt-input" required>
            <option value=""><?php esc_html_e('Bitte wählen','pam-probetermine'); ?></option>
            <?php foreach($devices as $d): ?><option value="<?php echo (int)$d->id; ?>"><?php echo esc_html($d->name); ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="pam-pt-field-group">
          <label><?php esc_html_e('Mitarbeiter','pam-probetermine'); ?></label>
          <select name="staff_id" class="pam-pt-input" required>
            <option value=""><?php esc_html_e('Bitte wählen','pam-probetermine'); ?></option>
            <?php foreach($staff as $s): ?><option value="<?php echo (int)$s->id; ?>"><?php echo esc_html($s->name); ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="pam-pt-field-group">
          <label><?php esc_html_e('Quelle','pam-probetermine'); ?></label>
          <select name="source" class="pam-pt-input">
            <option value="therapeut"><?php esc_html_e('Therapeut','pam-probetermine'); ?></option>
            <option value="rezeption"><?php esc_html_e('Rezeption','pam-probetermine'); ?></option>
          </select>
        </div>
        <div class="pam-pt-field-group">
          <label><?php esc_html_e('Datum','pam-probetermine'); ?></label>
          <input type="datetime-local" name="date" class="pam-pt-input">
        </div>
        <div class="pam-pt-field-group pam-pt-field-full">
          <label><?php esc_html_e('Notizen','pam-probetermine'); ?></label>
          <textarea name="notes" rows="4" class="pam-pt-input"></textarea>
        </div>
      </div>
      <div class="pam-pt-actions">
        <button type="submit" class="pam-pt-btn-primary"><?php esc_html_e('Speichern','pam-probetermine'); ?></button>
      </div>
    </form>
  </div>
</div>
