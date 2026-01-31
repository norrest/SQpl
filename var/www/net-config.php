<?php
/*
 */

// common include
include('inc/connection.php');
playerSession('open', $db, '', '');

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
        if ($_SESSION['w_lock'] != 1 && $_SESSION['w_queue'] == '') {
            session_start();
            $_SESSION['w_queue'] = "netcfgman";
            $_SESSION['w_queueargs'] = $_POST['netconf'];
            $_SESSION['w_active'] = 1;
            $_SESSION['notify']['title'] = 'Network configuration updated';
            $_SESSION['notify']['msg'] = '';
            session_write_close();
        } else {
            session_start();
            $_SESSION['notify']['title'] = 'Job failed';
            $_SESSION['notify']['msg'] = 'Background worker is busy.';
            session_write_close();
        }
    }

    // close DB handle
    $dbh = null;

    // create job for background worker
    if ($_SESSION['w_lock'] != 1 && !isset($_POST['netconf'])) {
        session_start();
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
    } else {
        $_SESSION['notify']['title'] = '';
        $_SESSION['notify']['msg'] = 'Background worker is busy';
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
    $ipeth0 = exec("ip addr list eth0 |grep \"inet \" |cut -d' ' -f6|cut -d/ -f1");
    $speth0 = exec("ethtool eth0 | grep -i speed | tr -d 'Speed:'");

    $cpuload = shell_exec("top -bn 2 -d 0.5 | grep 'Cpu(s)' | tail -n 1 | awk '{print $2 + $4 + $6}'");
    $cpuload = number_format($cpuload, 0, '.', '');
    $cputemp = substr(shell_exec('cat /sys/class/thermal/thermal_zone0/temp'), 0, 2);

    $mpderrors = shell_exec("mpc | grep ERROR");
    $dacinfo = shell_exec("cat /proc/asound/* | grep USB");
    $dacspeed = shell_exec("cat /proc/asound/* | grep -Eo '.{0,6}speed{0,6}'");
    $status = shell_exec("cat /proc/asound/card*/* | grep Status");
    $status_dsd = shell_exec("cat /proc/asound/card*/pcm*p/sub*/* | grep DSD");
    $status_usb = shell_exec("lsusb | grep -v Linux");

    $mpdinfo = shell_exec("service mpd status | grep -Eio '(\S+\s+){,5}Active(\s+\S+){,5}'");
    $rooninfo = shell_exec("service roonbridge status | grep -Eio '(\S+\s+){,5}Active(\s+\S+){,5}'");

    $mpdver = shell_exec("mpd -V | grep Music");
    $webver = shell_exec("cat /etc/VAMP_VER");
    $kernelver = shell_exec("uname -r -m -o");
    $alsalibver = shell_exec("grep -Eio '(\S+\s+){,5}VERSION_STR(\s+\S+){,5}' /usr/include/alsa/version.h");
    $alsa_rate = shell_exec("cat /proc/asound/card*/pcm*p/sub*/* | grep rate");

    $free_space_usb = shell_exec("df -h | grep /mnt/USB");
    $free_space_nas = shell_exec("df -h --output=source | grep // ");

    if (!empty($ipeth0)) {
        $statuset = 'Connected <i class="fa fa-check green sx"></i>';
    } else {
        $statuset = 'Not connected <i class="fa fa-remove red sx"></i>';
    }

    // eth0 block
    if (isset($_SESSION['netconf']['eth0']) && !empty($_SESSION['netconf']['eth0'])) {
        $_eth0  = "<div class=\"alert alert-info\">\n";

        if (!empty($mpderrors)) {
            $_eth0 .= "<div><b><font color=#ff0000 size=3>" . $mpderrors . "</font></b></div>\n";
            $_eth0 .= "<br>\n";
        }

        $_eth0 .= "<div><font size=3 color=#100f40>Web UI version:</font></div>\n";
        $_eth0 .= "<div>" . $webver . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>USB devices:</font></div>\n";
        $_eth0 .= "<div>" . $status_usb . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>USB DAC status:</font></div>\n";
        $_eth0 .= "<div>" . $dacinfo . "</div>\n";
        $_eth0 .= "<div>USB link speed: " . $dacspeed . "</div>\n";
        $_eth0 .= "<div>" . $status . "</div>\n";
        $_eth0 .= "<div>Sample rate: " . $alsa_rate . "</div>\n";
        $_eth0 .= "<div>Native DSD: " . $status_dsd . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>System:</font></div>\n";
        $_eth0 .= "<div>" . $kernelver . "</div>\n";
        $_eth0 .= "<div><font size=2 color=#100f40>ALSA library:</font></div>\n";
        $_eth0 .= "<div>" . $alsalibver . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>Music playback:</font></div>\n";
        $_eth0 .= "<div>MPD status: " . $mpdinfo . "</div>\n";
        $_eth0 .= "<div>" . $mpdver . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>Roon Bridge:</font></div>\n";
        $_eth0 .= "<div>Status: " . $rooninfo . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>Storage:</font></div>\n";
        $_eth0 .= "<div><font size=3>Internal SATA (size, free):</font></div>\n";
        $_eth0 .= "<div>" . $free_space_usb . "</div>\n";
        $_eth0 .= "<div><font size=3>Mounted network shares:</font></div>\n";
        $_eth0 .= "<div>" . $free_space_nas . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>Network (LAN):</font></div>\n";
        $_eth0 .= "<div>Status: " . $statuset . "</div>\n";
        $_eth0 .= "<div>IP address: " . $ipeth0 . "</div>\n";
        $_eth0 .= "<div>Link speed: " . $speth0 . "</div>\n";
        $_eth0 .= "<br>\n";

        $_eth0 .= "<div><font size=3 color=#100f40>CPU:</font></div>\n";
        $_eth0 .= "<div>Load: " . $cpuload . "%</div>\n";
        $_eth0 .= "<div>Temp: " . $cputemp . "°C</div>\n";

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

<?php debug($_POST); ?>

<?php include('_footer.php'); ?>