<?php 
/*

 */
 
// common include
include('inc/connection.php');
playerSession('open',$db,'',''); 
playerSession('unlock',$db,'','');
?>

<?php 
if (isset($_POST['syscmd'])){
	switch ($_POST['syscmd']) {

	case 'reboot':
	
			if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
			// start / respawn session
			if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			$_SESSION['w_queue'] = "reboot";
			$_SESSION['w_active'] = 1;
			// set UI notify
			$_SESSION['notify']['title'] = 'REBOOT';
			$_SESSION['notify']['msg'] = 'reboot player initiated...';
			// unlock session file
			playerSession('unlock');
			} else {
			echo "background worker busy";
			}
		// unlock session file
		playerSession('unlock');
		break;
		
	case 'poweroff':
	
			if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
			// start / respawn session
			if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			$_SESSION['w_queue'] = "poweroff";
			$_SESSION['w_active'] = 1;
			// set UI notify
			$_SESSION['notify']['title'] = 'SHUTDOWN';
			$_SESSION['notify']['msg'] = 'shutdown player initiated...';
			// unlock session file
			playerSession('unlock');
			} else {
			echo "background worker busy";
			}
		break;

	case 'mpdrestart':
	
			if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
			// start / respawn session
			if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			$_SESSION['w_queue'] = "mpdrestart";
			$_SESSION['w_active'] = 1;
			// set UI notify
			$_SESSION['notify']['title'] = 'MPD RESTART';
			$_SESSION['notify']['msg'] = 'restarting MPD daemon...';
			// unlock session file
			playerSession('unlock');
			} else {
			echo "background worker busy";
			}
		break;
	
	case 'backup':
			
			if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
			// start / respawn session
			if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			$_SESSION['w_jobID'] = wrk_jobID();
			$_SESSION['w_queue'] = 'backup';
			$_SESSION['w_active'] = 1;
			playerSession('unlock');
				// wait worker response loop
				while (1) {
				sleep(2);
				if (session_status() !== PHP_SESSION_ACTIVE) session_start();
					if ( isset($_SESSION[$_SESSION['w_jobID']]) ) {
					// set UI notify
					$_SESSION['notify']['title'] = 'BACKUP';
					$_SESSION['notify']['msg'] = 'backup complete.';
					pushFile($_SESSION[$_SESSION['w_jobID']]);
					unset($_SESSION[$_SESSION['w_jobID']]);
					break;
					}
				session_write_close();
				}
			} else {
			if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			$_SESSION['notify']['title'] = 'Job Failed';
			$_SESSION['notify']['msg'] = 'background worker is busy.';
			}
		// unlock session file
		playerSession('unlock');
		break;
	
	case 'updatempdDB':
		
			if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
				if (session_status() !== PHP_SESSION_ACTIVE) session_start();
				sendMpdCommand($mpd,'update');
				// set UI notify
				$_SESSION['notify']['title'] = 'MPD Update';
				$_SESSION['notify']['msg'] = 'database update started...';
				// unlock session file
				playerSession('unlock');
			} else {
				echo "background worker busy";
				playerSession('unlock');
			}
			
	break;
	
	case 'clearqueue':
			
			if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
			if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			sendMpdCommand($mpd,'clear');
			// set UI notify
			$_SESSION['notify']['title'] = 'Clear Queue';
			$_SESSION['notify']['msg'] = 'Play Queue Cleared';
			// unlock session file
			playerSession('unlock');
			} else {
			echo "background worker busy";
			}
			// unlock session file
			playerSession('unlock');
	break;
	
	case 'updateui':
	
			if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
			// start / respawn session
			if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			$_SESSION['w_queue'] = "updateui";
			$_SESSION['w_active'] = 1;
			// set UI notify
			$_SESSION['notify']['title'] = 'Update';
			$_SESSION['notify']['msg'] = 'Retrieving Updates, if available';
			// unlock session file
			playerSession('unlock');
			} else {
			echo "background worker busy";
			}
	break;
		
	case 'totalbackup':
		
		break;
		
	case 'restore':
		
		break;
	
	}

}

// Show i2s selector only on RaspberryPI
$arch = wrk_getHwPlatform();
if ($arch == '01' || $arch == '08') {
  // показываем
} else {
  $_divi2s = 'class="hide"';
}
if (isset($_POST['orionprofile']) && $_POST['orionprofile'] != $_SESSION['orionprofile']){
	// load worker queue 
	if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	$_SESSION['w_queue'] = 'orionprofile';
	$_SESSION['w_queueargs'] = $_POST['orionprofile'];
	// set UI notify
	$_SESSION['notify']['title'] = 'KERNEL PROFILE';
	$_SESSION['notify']['msg'] = 'orionprofile changed <br> current profile:     <strong>'.$_POST['orionprofile']."</strong>";
	// unlock session file
	playerSession('unlock');
	} else {
	echo "background worker busy";
	}
	
	// activate worker job
	if ($_SESSION['w_lock'] != 1) {
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	$_SESSION['w_active'] = 1;
	// save new value on SQLite datastore
	playerSession('write',$db,'orionprofile',$_POST['orionprofile']);
	// unlock session file
	playerSession('unlock');
	} else {
	return "background worker busy";
	}

}

if (isset($_POST['cmediafix']) && $_POST['cmediafix'] != $_SESSION['cmediafix']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	if ($_POST['cmediafix'] == 1 OR $_POST['cmediafix'] == 0) {
	playerSession('write',$db,'cmediafix',$_POST['cmediafix']);
	}
	// set UI notify
	if ($_POST['cmediafix'] == 1) {
	$_SESSION['notify']['title'] = '';
	$_SESSION['notify']['msg'] = 'CMediaFix enabled';
	} else {
	$_SESSION['notify']['title'] = '';
	$_SESSION['notify']['msg'] = 'CMediaFix disabled';
	}
	// unlock session file
	playerSession('unlock');
}

if (isset($_POST['shairport']) && $_POST['shairport'] != $_SESSION['shairport']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	if ($_POST['shairport'] == 1 OR $_POST['shairport'] == 0) {
	playerSession('write',$db,'shairport',$_POST['shairport']);
	}
	// set UI notify
	if ($_POST['shairport'] == 1) {
	$_SESSION['notify']['title'] = 'Airplay capability enabled';
	$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
	} else {
	$_SESSION['notify']['title'] = 'Airplay capability disabled';
	$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
	}
	// unlock session file
	playerSession('unlock');
}

if (isset($_POST['upnpmpdcli']) && $_POST['upnpmpdcli'] != $_SESSION['upnpmpdcli']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	if ($_POST['upnpmpdcli'] == 1 OR $_POST['upnpmpdcli'] == 0) {
	playerSession('write',$db,'upnpmpdcli',$_POST['upnpmpdcli']);
	}
	// set UI notify
	if ($_POST['upnpmpdcli'] == 1) {
	$_SESSION['notify']['title'] = 'UPNP Control enabled';
	$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
	} else {
	$_SESSION['notify']['title'] = 'UPNP Control disabled';
	$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
	}
	// unlock session file
	playerSession('unlock');
}

if (isset($_POST['djmount']) && $_POST['djmount'] != $_SESSION['djmount']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	if ($_POST['djmount'] == 1 OR $_POST['djmount'] == 0) {
	playerSession('write',$db,'djmount',$_POST['djmount']);
	}
	// set UI notify
	if ($_POST['djmount'] == 1) {
	$_SESSION['notify']['title'] = 'UPNP\DLNA Indexing enabled';
	$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
	} else {
	$_SESSION['notify']['title'] = 'UPNP\DLNA Indexing disabled';
	$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
	}
	// unlock session file
	playerSession('unlock');
}

if (isset($_POST['minidlna']) && $_POST['minidlna'] != $_SESSION['minidlna']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	if ($_POST['minidlna'] == 1 OR $_POST['minidlna'] == 0) {
	playerSession('write',$db,'minidlna',$_POST['minidlna']);
	}
	// set UI notify
	if ($_POST['minidlna'] == 1) {
	$_SESSION['notify']['title'] = 'DLNA Library Server enabled';
	$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
	} else {
	$_SESSION['notify']['title'] = 'DLNA Library Server disabled';
	$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
	}
	// unlock session file
	playerSession('unlock');
}

if (isset($_POST['startupsound']) && $_POST['startupsound'] != $_SESSION['startupsound']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	if ($_POST['startupsound'] == 1 OR $_POST['startupsound'] == 0) {
	playerSession('write',$db,'startupsound',$_POST['startupsound']);
	}
	// set UI notify
	if ($_POST['startupsound'] == 1) {
	$_SESSION['notify']['title'] = '';
	$_SESSION['notify']['msg'] = 'Startup Sound enabled';
	} else {
	$_SESSION['notify']['title'] = '';
	$_SESSION['notify']['msg'] = 'Startup Sound disabled';
	}
	// unlock session file
	playerSession('unlock');
}

if (isset($_POST['hostname']) && $_POST['hostname'] != $_SESSION['hostname']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	$new = trim($_POST['hostname']);
	playerSession('write',$db,'hostname',$new);
	$_SESSION['hostname'] = $new;
	file_put_contents('/etc/hostname', $new . "\n");
	$hsfile = '/etc/hosts';
	$hs = "127.0.0.1       localhost        ".$_SESSION['hostname'];
	file_put_contents($hsfile, $hs);
	$_SESSION['w_queue'] = "hostname";
		$_SESSION['w_queueargs'] = $new;
		// set UI notify
		$_SESSION['notify']['title'] = 'Player Name Changed';
		$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect' ;
		// active worker queue
		$_SESSION['w_active'] = 1;
		} else {
		$_SESSION['notify']['title'] = 'Player Name Changed';
		$_SESSION['notify']['msg'] = 'You must reboot for changes to take effect';
		// open to read and modify


	// unlock session file
	playerSession('unlock');
}


//Library Display
if (isset($_POST['displaylib']) && $_POST['displaylib'] != $_SESSION['displaylib']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	if ($_POST['displaylib'] == 1 OR $_POST['displaylib'] == 0) {
	playerSession('write',$db,'displaylib',$_POST['displaylib']);
	}
	// set UI notify
	if ($_POST['displaylib'] == 1) {
	$_SESSION['notify']['title'] = '';
	$_SESSION['notify']['msg'] = 'Library view enabled';
	} else {
	$_SESSION['notify']['title'] = '';
	$_SESSION['notify']['msg'] = 'Library view disabled';
	}
	// unlock session file
	playerSession('unlock');
}

if (isset($_POST['displaylibastab']) && $_POST['displaylibastab'] != $_SESSION['displaylibastab']){
	// load worker queue 
	// start / respawn session
	if (session_status() !== PHP_SESSION_ACTIVE) session_start();
	// save new value on SQLite datastore
	if ($_POST['displaylibastab'] == 1 OR $_POST['displaylibastab'] == 0) {
	playerSession('write',$db,'displaylibastab',$_POST['displaylibastab']);
	}
	// unlock session file
	playerSession('unlock');
}



// configure html select elements
$_system_select['orionprofile'] .= "<option value=\"default\" ".(($_SESSION['orionprofile'] == 'default') ? "selected" : "").">default</option>\n";
$_system_select['orionprofile'] .= "<option value=\"Eco-Mode\" ".(($_SESSION['orionprofile'] == 'Eco-Mode') ? "selected" : "").">Eco-Mode</option>\n";
$_system_select['cmediafix1'] .= "<input type=\"radio\" name=\"cmediafix\" id=\"togglecmedia1\" value=\"1\" ".(($_SESSION['cmediafix'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['cmediafix0'] .= "<input type=\"radio\" name=\"cmediafix\" id=\"togglecmedia2\" value=\"0\" ".(($_SESSION['cmediafix'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['djmount1'] .= "<input type=\"radio\" name=\"djmount\" id=\"toggledjmount1\" value=\"1\" ".(($_SESSION['djmount'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['djmount0'] .= "<input type=\"radio\" name=\"djmount\" id=\"toggledjmount2\" value=\"0\" ".(($_SESSION['djmount'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['shairport1'] .= "<input type=\"radio\" name=\"shairport\" id=\"toggleshairport1\" value=\"1\" ".(($_SESSION['shairport'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['shairport0'] .= "<input type=\"radio\" name=\"shairport\" id=\"toggleshairport2\" value=\"0\" ".(($_SESSION['shairport'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['upnpmpdcli1'] .= "<input type=\"radio\" name=\"upnpmpdcli\" id=\"toggleupnpmpdcli1\" value=\"1\" ".(($_SESSION['upnpmpdcli'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['upnpmpdcli0'] .= "<input type=\"radio\" name=\"upnpmpdcli\" id=\"toggleupnpmpdcli2\" value=\"0\" ".(($_SESSION['upnpmpdcli'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['minidlna1'] .= "<input type=\"radio\" name=\"minidlna\" id=\"toggleminidlna1\" value=\"1\" ".(($_SESSION['minidlna'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['minidlna0'] .= "<input type=\"radio\" name=\"minidlna\" id=\"toggleminidlna2\" value=\"0\" ".(($_SESSION['minidlna'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['startupsound1'] .= "<input type=\"radio\" name=\"startupsound\" id=\"togglestartupsound1\" value=\"1\" ".(($_SESSION['startupsound'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['startupsound0'] .= "<input type=\"radio\" name=\"startupsound\" id=\"togglestartupsound2\" value=\"0\" ".(($_SESSION['startupsound'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['displaylib1'] .= "<input type=\"radio\" name=\"displaylib\" id=\"toggledisplaylib1\" value=\"1\" ".(($_SESSION['displaylib'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['displaylib0'] .= "<input type=\"radio\" name=\"displaylib\" id=\"toggledisplaylib2\" value=\"0\" ".(($_SESSION['displaylib'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['displaylibastab1'] .= "<input type=\"radio\" name=\"displaylibastab\" id=\"toggledisplaylibastab1\" value=\"1\" ".(($_SESSION['displaylibastab'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['displaylibastab0'] .= "<input type=\"radio\" name=\"displaylibastab\" id=\"toggledisplaylibastab2\" value=\"0\" ".(($_SESSION['displaylibastab'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['spotify1'] .= "<input type=\"radio\" name=\"spotify\" id=\"togglespotify1\" value=\"1\" ".(($_SESSION['spotify'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['spotify0'] .= "<input type=\"radio\" name=\"spotify\" id=\"togglespotify2\" value=\"0\" ".(($_SESSION['spotify'] == 0) ? "checked=\"checked\"" : "").">\n";
$_system_select['spotifybitrate1'] .= "<input type=\"radio\" name=\"spotifybitrate\" id=\"togglespotifybitrate1\" value=\"1\" ".(($_SESSION['spotifybitrate'] == 1) ? "checked=\"checked\"" : "").">\n";
$_system_select['spotifybitrate0'] .= "<input type=\"radio\" name=\"spotifybitrate\" id=\"togglespotifybitrate2\" value=\"0\" ".(($_SESSION['spotifybitrate'] == 0) ? "checked=\"checked\"" : "").">\n";
$_hostname = $_SESSION['hostname'];
$_spotusername = $_SESSION['spotusername'];
$_spotpassword = $_SESSION['spotpassword'];
// set template
$tpl = "settings.html";
?>

<?php
$sezione = basename(__FILE__, '.php');
include('_header.php'); 
?>

<!-- content --!>
<?php
// wait for worker output if $_SESSION['w_active'] = 1
waitWorker(1);
eval("echoTemplate(\"".getTemplate("templates/$tpl")."\");");
?>
<!-- content -->

<?php 
debug($_POST);
?>

<?php include('_footer.php'); ?>
