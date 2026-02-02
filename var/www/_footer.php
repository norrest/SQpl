<?php
/*
 * StereoQ Player UI footer
 * Based on Volumio / TsunAMP PlayerUI
 */
?>

<style>
:root{
  --bg0:#2b3137;
  --bg1:#3a424c;
  --panel:rgba(255,255,255,.06);
  --panel2:rgba(17,24,31,.90);
  --line:rgba(255,255,255,.12);
  --text:rgba(255,255,255,.92);
  --muted:rgba(255,255,255,.70);
  --muted2:rgba(255,255,255,.52);
  --accent:#14E681;
  --accent2:#4BBE87;
  --radius:16px;
  --radius2:12px;
  --shadow:0 14px 40px rgba(0,0,0,.45);
}

/* Apply dark style only to these modals */
#service-menu-modal,
#poweroff-modal,
#webradio-modal{
  background: linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.03));
  border: 1px solid var(--line);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  color: var(--text);
}

/* Header */
#service-menu-modal .modal-header,
#poweroff-modal .modal-header,
#webradio-modal .modal-header{
  background: rgba(24,28,33,.55);
  border-bottom: 1px solid rgba(255,255,255,.08);
  border-radius: var(--radius) var(--radius) 0 0;
}

#service-menu-modal .modal-header h3,
#poweroff-modal .modal-header h3,
#webradio-modal .modal-header h3{
  color: var(--text);
  font-weight: 800;
  letter-spacing: .2px;
}

/* Close button */
#service-menu-modal .close,
#poweroff-modal .close,
#webradio-modal .close{
  color: rgba(255,255,255,.85);
  opacity: .75;
  text-shadow: none;
}

#service-menu-modal .close:hover,
#poweroff-modal .close:hover,
#webradio-modal .close:hover{
  opacity: 1;
}

/* Body */
#service-menu-modal .modal-body,
#poweroff-modal .modal-body,
#webradio-modal .modal-body{
  background: rgba(17,24,31,.55);
}

#service-menu-modal .modal-body.service-menu{
  padding: 18px 22px;
}

/* Section styling */
#service-menu-modal .section{ margin-bottom: 10px; }

#service-menu-modal .section-title{
  font-weight: 800;
  font-size: 14px;
  color: var(--text);
  margin: 0 0 10px;
  letter-spacing: .2px;
}

#service-menu-modal .section-hint{
  font-size: 12px;
  color: var(--muted);
  margin: 0 0 12px;
  line-height: 1.25;
}

/* Divider */
#service-menu-modal .soft-hr{
  margin: 12px 0 14px;
  border: 0;
  border-top: 1px solid rgba(255,255,255,.10);
}

/* Buttons inside service menu */
#service-menu-modal .btn-service{
  margin: 0 0 6px;
  text-align: center;
  border-radius: 14px;
  font-weight: 800;
}

/* Green primary like previous page */
#service-menu-modal .btn.btn-primary{
  background: linear-gradient(180deg, rgba(20,230,129,.95), rgba(75,190,135,.92));
  border-color: rgba(20,230,129,.45);
  color: rgba(255,255,255,.92);
  box-shadow: 0 10px 22px rgba(20,230,129,.16);
}

#service-menu-modal .btn.btn-primary:hover{ filter: brightness(1.03); }
#service-menu-modal .btn.btn-primary:active{ transform: translateY(1px); }

/* Default button tuned for dark UI */
#service-menu-modal .btn.btn-default{
  background: rgba(255,255,255,.06);
  border-color: rgba(255,255,255,.16);
  color: rgba(255,255,255,.92);
  box-shadow: 0 10px 22px rgba(0,0,0,.22);
}

#service-menu-modal .btn.btn-default:hover{
  background: rgba(255,255,255,.09);
  border-color: rgba(255,255,255,.22);
}

/* Warning button still warning but readable on dark */
#service-menu-modal .btn.btn-warning{
  color: rgba(255,255,255,.92);
  box-shadow: 0 10px 22px rgba(0,0,0,.22);
}

/* Footer */
#service-menu-modal .modal-footer,
#poweroff-modal .modal-footer,
#webradio-modal .modal-footer{
  background: rgba(24,28,33,.45);
  border-top: 1px solid rgba(255,255,255,.08);
  border-radius: 0 0 var(--radius) var(--radius);
}

/* Footer cancel buttons */
#service-menu-modal .modal-footer .btn,
#poweroff-modal .modal-footer .btn,
#webradio-modal .modal-footer .btn{
  background: rgba(255,255,255,.06);
  border-color: rgba(255,255,255,.16);
  color: rgba(255,255,255,.92);
  border-radius: 14px;
  font-weight: 800;
}
</style>


<form class="form-horizontal" action="settings.php" method="post">
  <div id="poweroff-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="poweroff-modal-label" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h3 id="poweroff-modal-label">Power</h3>
    </div>
    <div class="modal-body">
      <button id="syscmd-poweroff" name="syscmd" value="poweroff" class="btn btn-primary btn-large btn-block">
        <i class="fa fa-power-off sx"></i> Power off
      </button>
      <button id="syscmd-reboot" name="syscmd" value="reboot" class="btn btn-primary btn-large btn-block">
        <i class="fa fa-refresh sx"></i> Reboot
      </button>
    </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </div>
  </div>
</form>

<form class="form-horizontal" action="" method="post">
  <div id="service-menu-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="service-menu-modal-label" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h3 id="service-menu-modal-label">Service Menu</h3>
    </div>

    <div class="modal-body service-menu">
<div class="section">
  <div class="section-title">Update Player</div>

  <button id="update-ui"
          type="submit"
          name="update_ui"
          formtarget="_blank"
          formaction="update.php"
          class="btn btn-primary btn-large btn-block btn-service">
    <i class="fa fa-cloud-download sx"></i>
    Update web interface
  </button>
  <div class="section-hint">Downloads and installs the latest web-interface version</div>

  <button id="kernel-update"
          type="submit"
          name="kernel_update"
          formtarget="_blank"
          formaction="kernel-update.php"
          class="btn btn-primary btn-large btn-block btn-service">
    <i class="fa fa-cloud-download sx"></i>
    Update player system kernel
  </button>
  <div class="section-hint">Downloads and installs the latest system kernel version</div>
</div>


      <hr class="soft-hr">

      <div class="section">
        <div class="section-title">Service</div>

        <button id="check-and-space"
                name="check_disk"
                formtarget="_blank"
                formaction="chec.php"
                class="btn btn-default btn-large btn-block btn-service">
          <i class="fa fa-hdd-o sx"></i>
          Check internal disk
        </button>
        <div class="section-hint">Runs a filesystem and SMART check</div>

        <button id="force-remove-samba"
                name="force_remove_mounts"
                formtarget="_blank"
                formaction="del.php"
                class="btn btn-warning btn-large btn-block btn-service">
          <i class="fa fa-chain-broken sx"></i>
          Force remove network mounts
        </button>
        <div class="section-hint">Use only if mounts are stuck or the UI freezes</div>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </div>
  </div>
</form>

<form class="form-horizontal" action="" method="post">
  <div id="webradio-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="webradio-modal-label" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h3 id="webradio-modal-label">Add New WebRadio</h3>
    </div>

    <div class="modal-body">
      <div class="control-group">
        <label class="control-label" for="radio-name">Name</label>
        <div class="controls">
          <input id="radio-name" name="radio-name" type="text" placeholder="WebRadio Name" />
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="radio-url">URL</label>
        <div class="controls">
          <input id="radio-url" name="radio-url" type="text" placeholder="WebRadio URL" />
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <div class="form-actions" style="margin:0;">
        <button class="btn btn-large" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button type="submit" class="btn btn-primary btn-large" name="save" value="save">Add</button>
      </div>
    </div>
  </div>
</form>

<!-- loader -->
<div id="loader">
  <div id="loaderbg"></div>
  <div id="loadercontent"><i class="fa fa-refresh fa-spin"></i>Connecting...</div>
</div>

<script src="js/jquery-1.8.2.min.js"></script>
<script src="js/jquery-ui-1.11.1.custom.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/notify.js"></script>
<script src="js/jquery.countdown.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/volumio.api.js"></script>
<script src="js/volumio.lazyloader.js"></script>
<script src="js/volumio.library.js"></script>

<?php if ($sezione == 'index') { ?>
  <script src="js/jquery.knob.js"></script>
  <script src="js/bootstrap-contextmenu.js"></script>
  <script src="js/jquery.pnotify.min.js"></script>
  <script src="js/volumio.playback.js"></script>
<?php } else { ?>
  <script src="js/custom_checkbox_and_radio.js"></script>
  <script src="js/custom_radio.js"></script>
  <script src="js/jquery.tagsinput.js"></script>
  <script src="js/jquery.placeholder.js"></script>
  <script src="js/parsley.min.js"></script>
  <script src="js/i18n/_messages.en.js" type="text/javascript"></script>
  <script src="js/application.js"></script>
  <script src="js/volumio.settings.js"></script>
  <script src="js/jquery.pnotify.min.js"></script>
  <script src="js/bootstrap-fileupload.js"></script>
<?php } ?>

<?php
// WebRadio Add Dialog
if (isset($_POST['radio-name']) && isset($_POST['radio-url']) && $_POST['radio-name'] !== '' && $_POST['radio-url'] !== '') {
  $url = $_POST['radio-url'];
  $name = $_POST['radio-name'];

  @file_put_contents('/var/lib/mpd/music/WEBRADIO/'.$name.'.pls', $url);

  session_start();
  sendMpdCommand($mpd, 'update WEBRADIO');
  $_SESSION['notify']['msg'] = 'New WebRadio added';
  playerSession('unlock');
}
?>

<script type="text/javascript">
  setLibOptions(
    <? echo isset($_SESSION['displaylib']) && $_SESSION['displaylib'] == 1 ? 1 : 0; ?>/*is enabled?*/,
    <? echo isset($_SESSION['displaylibastab']) && $_SESSION['displaylibastab'] == 1 ? 1 : 0; ?>/*display as tab or in browse view?*/,
    <? echo $sezione == 'index' ? 1 : 0; ?>/*should load it?*/
  );
  loadLibraryIfNeeded();
</script>

<!--[if lt IE 8]>
<script src="js/icon-font-ie7.js"></script>
<script src="js/icon-font-ie7-24.js"></script>
<![endif]-->

<?php
// write backend response on UI Notify popup
if (isset($_SESSION['notify']) && $_SESSION['notify'] != '') {
  sleep(1);
  ui_notify($_SESSION['notify']);
  session_start();
  $_SESSION['notify'] = '';
  session_write_close();
}
?>

<div id="debug" <?php if ($_SESSION['hiddendebug'] == 1 OR $_SESSION['debug'] == 0) { echo "class=\"hide\""; } ?>>
  <pre>
<?php debug_footer($db); ?>
  </pre>
</div>

</body>
</html>
