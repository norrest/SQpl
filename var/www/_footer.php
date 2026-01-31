<?php
/*
 * StereoQ Player UI footer
 * Based on Volumio / TsunAMP PlayerUI
 */
?>

<style>
/* Service Menu modal styling */
#service-menu-modal .modal-body.service-menu { padding: 18px 22px; }
#service-menu-modal .section { margin-bottom: 10px; }
#service-menu-modal .section-title {
  font-weight: 700;
  font-size: 14px;
  color: #2c3e50;
  margin: 0 0 10px;
  letter-spacing: .2px;
}
#service-menu-modal .btn-service { margin: 0 0 6px; text-align: center; }
#service-menu-modal .section-hint {
  font-size: 12px;
  color: #7f8c8d;
  margin: 0 0 12px;
  line-height: 1.25;
}
#service-menu-modal .soft-hr {
  margin: 12px 0 14px;
  border: 0;
  border-top: 1px solid rgba(0,0,0,.08);
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
        <div class="section-title">Update</div>

        <button id="update-git-hub"
                name="update_ui"
                formtarget="_blank"
                formaction="update.php"
                class="btn btn-primary btn-large btn-block btn-service">
          <i class="fa fa-cloud-download sx"></i>
          Update web interface
        </button>
        <div class="section-hint">Downloads and installs the latest UI version</div>
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
