<?php
/*
 * Network config page + system status block
 * Only USB devices and Internal SATA are rendered in dark boxes
 * USB DAC section is rendered in light mode (no <pre> tag to avoid theme CSS)
 */

// common include
include('inc/connection.php');
playerSession('open', $db, '', '');

// Helpers
function sh($cmd) {
    return trim(shell_exec($cmd . " 2>/dev/null"));
}
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/*
 * Light output block
 * Important: do NOT use <pre> here because the theme may style <pre> as black
 */
function light_block($s) {
    $s = trim((string)$s);
    if ($s === '') return '<div>-</div>';
    return '<div style="white-space:pre-wrap;margin:0;font-family:monospace;">' . h($s) . '</div>';
}

/*
 * Dark output block
 */
function dark_box($s) {
    $s = trim((string)$s);
    if ($s === '') $s = '-';
    return '<pre style="background:#0e0f12;color:#e9eef5;padding:12px 14px;border-radius:10px;margin:6px 0 0;white-space:pre-wrap;">'
        . h($s) .
        '</pre>';
}

// handle POST (reset)
if (isset($_POST['reset']) && $_POST['reset'] == 1) {
    // reset to DHCP on eth0
    $_POST['eth0']['dhcp'] = 'true';
    $_POST['eth0']['ip'] = '';
    $_POST['eth0']['netmask'] = '';
    $_POST['eth0']['gw'] = '';
    $_POST['eth0']['dns1'] = '';
    $_POST['eth0']['dns2'] = '';
}

// handle POST
if (isset($_POST) && !empty($_POST)) {
    $dbh = cfgdb_connect($db);

    // eth0
    if (isset($_POST['eth0']['dhcp']) && isset($_POST['eth0']['ip'])) {
        if ($_POST['eth0']['dhcp'] == 'true') {
            $_POST['eth0']['dhcp'] = 'true';
            $_POST['eth0']['ip'] = '';
            $_POST['eth0']['netmask'] = '';
            $_POST['eth0']['gw'] = '';
            $_POST['eth0']['dns1'] = '';
            $_POST['eth0']['dns2'] = '';
        } else {
            $_POST['eth0']['dhcp'] = 'false';
        }

        $value = array(
            'name'    => 'eth0',
            'dhcp'    => $_POST['eth0']['dhcp'],
            'ip'      => $_POST['eth0']['ip'],
            'netmask' => $_POST['eth0']['netmask'],
            'gw'      => $_POST['eth0']['gw'],
            'dns1'    => $_POST['eth0']['dns1'],
            'dns2'    => $_POST['eth0']['dns2']
        );

        cfgdb_update('cfg_lan', $dbh, '', $value);
        $net = cfgdb_read('cfg_lan', $dbh);

        // format new config string for eth0
        if ($_POST['eth0']['dhcp'] == 'true') {
            $eth0 = "\nauto eth0\niface eth0 inet dhcp\n";
        } else {
            $eth0  = "\nauto eth0\niface eth0 inet static\n";
            $eth0 .= "address " . $_POST['eth0']['ip'] . "\n";
            $eth0 .= "netmask " . $_POST['eth0']['netmask'] . "\n";
            $eth0 .= "gateway " . $_POST['eth0']['gw'] . "\n";
            if (!empty($_POST['eth0']['dns1'])) {
                $eth0 .= "nameserver " . $_POST['eth0']['dns1'] . "\n";
            }
            if (!empty($_POST['eth0']['dns2'])) {
                $eth0 .= "nameserver " . $_POST['eth0']['dns2'] . "\n";
            }
        }
    }

    // handle manual config
    if (isset($_POST['netconf']) && !empty($_POST['netconf'])) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
            $_SESSION['w_queue'] = "netcfgman";
            $_SESSION['w_queueargs'] = $_POST['netconf'];
            $_SESSION['w_active'] = 1;
            $_SESSION['notify']['title'] = 'Network configuration updated';
            $_SESSION['notify']['msg'] = '';
            session_write_close();
        } else {
            $_SESSION['notify']['title'] = 'Job failed';
            $_SESSION['notify']['msg'] = 'Background worker is busy.';
            session_write_close();
        }
    }

    // close DB handle
    $dbh = null;

    // create job for background worker
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    if ($_SESSION['w_lock'] != 1 && !isset($_POST['netconf'])) {
        $_SESSION['w_queue'] = 'netcfg';
        // Wi-Fi removed, write only eth0 section
        $_SESSION['w_queueargs'] = (isset($eth0) ? $eth0 : "\nauto eth0\niface eth0 inet dhcp\n");
        $_SESSION['w_active'] = 1;

        $_SESSION['notify']['title'] = '';
        if (isset($_GET['reset']) && $_GET['reset'] == 1) {
            $_SESSION['notify']['msg'] = 'Network settings restored to defaults';
        } else {
            $_SESSION['notify']['msg'] = 'Network settings updated';
        }
        session_write_close();
    } else {
        $_SESSION['notify']['title'] = '';
        $_SESSION['notify']['msg'] = 'Background worker is busy';
        session_write_close();
    }

    playerSession('unlock');
}

// wait for worker output if $_SESSION['w_active'] = 1
waitWorker(1);

// check integrity of /etc/network/interfaces
if (!hashCFG('check_net', $db)) {
    $_netconf = file_get_contents('/etc/network/interfaces');
    // manual config template
    $tpl = "net-config-manual.html";
} else {
    $dbh = cfgdb_connect($db);
    $net = cfgdb_read('cfg_lan', $dbh);
    $dbh = null;

    // live values
    $ipeth0 = sh("ip -o -4 addr show dev eth0 | awk '{print \$4}' | cut -d/ -f1");
    $speth0 = sh("ethtool eth0 | awk -F': ' '/Speed:/ {print \$2; exit}'");

    $cpuload_raw = sh("top -bn 2 -d 0.5 | grep 'Cpu(s)' | tail -n 1 | awk '{print \$2 + \$4 + \$6}'");
    $cpuload = ($cpuload_raw !== '') ? number_format((float)$cpuload_raw, 0, '.', '') : '0';

    $tmp = sh("cat /sys/class/thermal/thermal_zone0/temp");
    $cputemp = ($tmp !== '') ? (int)($tmp / 1000) : '';

    // USB DAC info (less noise, more structured)
    $dac_card   = sh("awk '/\\[.*\\]:/ && /USB/ {print; getline; print}' /proc/asound/cards");
    $dac_speed  = sh("grep -hs -i ' speed' /proc/asound/card*/stream0 | head -n 1");
    if ($dac_speed === '') {
        $dac_speed = sh("grep -hs -i ' speed' /proc/asound/cards | head -n 1");
    }
    $dac_usbid  = sh("grep -hs 'USB Mixer' /proc/asound/card*/stream0 | head -n 1");

    $pcm_status = sh("grep -hs '^Status:' /proc/asound/card*/pcm*p/status");
    $alsa_rate  = sh("grep -hs '^rate:' /proc/asound/card*/pcm*p/sub*/hw_params | head -n 1");
    $alsa_state = sh("grep -hs '^state:' /proc/asound/card*/pcm*p/sub*/status | head -n 1");
    $status_dsd = sh("grep -hs 'DSD' /proc/asound/card*/pcm*p/sub*/hw_params | head -n 1");

    $mpderrors = sh("mpc 2>/dev/null | grep -i ERROR || true");
    $status_usb = sh("lsusb 2>/dev/null | grep -v -i 'Linux Foundation\\|root hub' || true");

    $mpdinfo = sh("service mpd status | grep -i 'Active:' | head -n 1");
    if ($mpdinfo === '') {
        $mpdinfo = sh("pgrep -x mpd >/dev/null && echo 'Active: running (pgrep)' || echo 'Active: not running'");
    }

    $mpdver = sh("mpd -V | head -n 1");
    $webver = sh("cat /etc/VAMP_VER");
    $kernelver = sh("uname -r -m -o");
    $alsalibver = sh("grep -Eio 'VERSION_STR[^\\n]*' /usr/include/alsa/version.h | head -n 1");

    // Only show /dev/sda and /dev/sdb (+ header)
    $free_space_sata = sh("df -h | awk 'NR==1 || \$1 ~ /^\\/dev\\/sd[ab]/ {print}'");
    $free_lines = preg_split("/\\r?\\n/", trim($free_space_sata));
    if (count($free_lines) < 2) {
        $free_space_sata = "No /dev/sda or /dev/sdb mounts found";
    }

    $free_space_nas = sh("mount 2>/dev/null | awk '/ type cifs / {print \$3\" -> \"\$1}'");
    if ($free_space_nas === '') {
        $free_space_nas = sh("df -h 2>/dev/null | awk '\$1 ~ /^\\/\\// {print}'");
    }

    if (!empty($ipeth0)) {
        $statuset = 'Connected <i class="fa fa-check green sx"></i>';
    } else {
        $statuset = 'Not connected <i class="fa fa-remove red sx"></i>';
    }

    // eth0 block
    if (isset($_SESSION['netconf']['eth0']) && !empty($_SESSION['netconf']['eth0'])) {
        $_eth0  = "<div class=\"alert alert-info\">\n";

        if (!empty($mpderrors)) {
            $_eth0 .= "<div><b><font color=#ff0000 size=3>" . h($mpderrors) . "</font></b></div>\n";
            $_eth0 .= "<br>\n";
        }

        $_eth0 .= "<div><font size=3 color=#100f40>Web UI version:</font></div>\n";
        $_eth0 .= "<div>" . h($webver) . "</div>\n";
        $_eth0 .= "<br>\n";

        // Dark only here
        $_eth0 .= "<div><font size=3 color=#100f40>USB devices:</font></div>\n";
        $_eth0 .= dark_box($status_usb) . "\n";
        $_eth0 .= "<br>\n";

        // Light blocks below
        $_eth0 .= "<div><font size=3 color=#100f40>USB DAC:</font></div>\n";
        $_eth0 .= light_block($dac_card) . "\n";

        $_eth0 .= "<div><font size=3 color=#100f40>DAC status:</font></div>\n";
        if (trim($dac_speed) !== '') $_eth0 .= light_block($dac_speed) . "\n";
        if (trim($dac_usbid) !== '') $_eth0 .= light_block($dac_usbid) . "\n";
        if (trim($pcm_status) !== '') $_eth0 .= light_block($pcm_status) . "\n";
        if (trim($alsa_rate) !== '')  $_eth0 .= "<div>" . h($alsa_rate) . "</div>\n";
        if (trim($alsa_state) !== '') $_eth0 .= "<div>" . h($alsa_state) . "</div>\n";
        if (trim($status_dsd) !== '') $_eth0 .= "<div>" . h($status_dsd) . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>System:</font></div>\n";
        $_eth0 .= "<div>" . h($kernelver) . "</div>\n";
        $_eth0 .= "<div><font size=2 color=#100f40>ALSA library:</font></div>\n";
        $_eth0 .= "<div>" . h($alsalibver) . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>Music playback:</font></div>\n";
        $_eth0 .= "<div>MPD status: " . h($mpdinfo) . "</div>\n";
        $_eth0 .= "<div>" . h($mpdver) . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>Storage:</font></div>\n";
        $_eth0 .= "<div><font size=3>Internal SATA (size, free):</font></div>\n";
        $_eth0 .= dark_box($free_space_sata) . "\n";

        $_eth0 .= "<div><font size=3>Mounted network shares:</font></div>\n";
        $_eth0 .= light_block($free_space_nas) . "\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>Network (LAN):</font></div>\n";
        $_eth0 .= "<div>Status: " . $statuset . "</div>\n";
        $_eth0 .= "<div>IP address: " . h($ipeth0) . "</div>\n";
        $_eth0 .= "<div>Link speed: " . h($speth0) . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>CPU:</font></div>\n";
        $_eth0 .= "<div>Load: " . h($cpuload) . "%</div>\n";
        $_eth0 .= "<div>Temp: " . h($cputemp) . "°C</div>\n";

        $_eth0 .= "</div>\n";
    }

    $tpl = "net-config.html";
}

// unlock session files
playerSession('unlock', $db, '', '');

$sezione = basename(__FILE__, '.php');
include('_header.php');
?>

<!-- content -->
<?php
eval("echoTemplate(\"" . getTemplate("templates/$tpl") . "\");");
?>
<!-- content -->

<?php
// Optional POST debug, enable with ?debug=1
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    debug($_POST);
}
?>

<?php include('_footer.php'); ?>
