<!--
 */
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>StereoQ Player</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/flat-ui.css" rel="stylesheet">
    <link href="css/bootstrap-select.css" rel="stylesheet">
    <link href="css/bootstrap-fileupload.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">

    <!--[if lte IE 7]>
        <link href="css/font-awesome-ie7.min.css" rel="stylesheet">
    <![endif]-->

    <?php if ($sezione == 'index') { ?>
    <link href="css/jquery.countdown.css" rel="stylesheet">
    <?php } ?>

    <!--<link rel="stylesheet" href="css/jquery.mobile.custom.structure.min.css">-->
    <link href="css/jquery.pnotify.default.css" rel="stylesheet">
    <link rel="stylesheet" href="css/panels.css">
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->

    <!-- iOS web app mode -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
</head>

<body class="<?php echo $sezione ?>">

<?php
    if (isset($_POST['stop-all']))
    {
         shell_exec('mpc stop;systemctl stop mpd.socket;killall -s 9 mpd;service mpd restart');
    }
?>

<div id="menu-top" class="ui-header ui-bar-f ui-header-fixed slidedown" data-position="fixed" data-role="header" role="banner">
    <div class="dropdown">
        <a class="dropdown-toggle" id="menu-settings" role="button" data-toggle="dropdown" data-target="#"
           href="<?php echo $sezione ?>.php">MENU <i class="fa fa-th-list dx"></i></a>

        <ul class="dropdown-menu" role="menu" aria-labelledby="menu-settings">
            <li class="<?php ami('index'); ?>">
                <a href="index.php"><i class="fa fa-play sx"></i>Player</a>
            </li>

            <li class="<?php ami('sources'); ?>">
                <a href="sources.php"><i class="fa fa-folder-open sx"></i>NAS Library</a>
            </li>

            <li class="<?php ami('mpd-config'); ?>">
                <a href="mpd-config.php"><i class="fa fa-cogs sx"></i>DAC Settings</a>
            </li>

            <li class="<?php ami('settings'); ?>">
                <a href="settings.php"><i class="fa fa-wrench sx"></i>System Settings</a>
            </li>


            <li>
                <a href="#service-menu-modal" data-toggle="modal"><i class="fa fa-cogs sx"></i>Service Menu</a>
            </li>

            <li class="<?php ami('net-config'); ?>">
                <a style="color:#ffff00" href="net-config.php"><i class="fa fa-sitemap sx"></i>Player Status</a>
            </li>

            <br>

            <li>
                <a href="#poweroff-modal" data-toggle="modal"><i class="fa fa-power-off sx"></i>Power Off</a>
            </li>
        </ul>
    </div>

    <div class="playback-controls">
        <button id="previous" class="btn btn-cmd" title="Previous"><i class="fa fa-step-backward"></i></button>
        <button id="stop" class="btn btn-cmd" title="Stop"><i class="fa fa-stop"></i></button>
        <button id="play" class="btn btn-cmd" title="Play/Pause"><i class="fa fa-play"></i></button>
        <button id="next" class="btn btn-cmd" title="Next"><i class="fa fa-step-forward"></i></button>
    </div>

    <a class="home" href="index.php">
        <img src="images/logo.png" class="logo" alt="StereoQ Player">
    </a>
</div>

<div id="menu-bottom" class="ui-footer ui-bar-f ui-footer-fixed slidedown" data-position="fixed" data-role="footer" role="banner">
    <ul>
        <?php if ($sezione == 'index') { ?>
            <li id="open-panel-sx"><a href="#panel-sx" class="open-panel-sx" data-toggle="tab"><i class="fa fa-music sx"></i> Browse</a></li>
            <li id="open-panel-lib"><a href="#panel-lib" class="open-panel-lib" data-toggle="tab"><i class="fa fa-columns sx"></i> Library</a></li>
            <li id="open-playback" class="active"><a href="#playback" class="close-panels" data-toggle="tab"><i class="fa fa-play sx"></i> Playback</a></li>
            <li id="open-panel-dx"><a href="#panel-dx" class="open-panel-dx" data-toggle="tab"><i class="fa fa-list sx"></i> Playlist</a></li>
        <?php } else { ?>
            <li id="open-panel-sx"><a href="index.php#panel-sx" class="open-panel-sx"><i class="fa fa-music sx"></i> Browse</a></li>
            <li id="open-panel-lib"><a href="index.php#panel-lib" class="open-panel-lib"><i class="fa fa-columns sx"></i> Library</a></li>
            <li id="open-playback"><a href="index.php#playback" class="close-panels"><i class="fa fa-play sx"></i> Playback</a></li>
            <li id="open-panel-dx"><a href="index.php#panel-dx" class="open-panel-dx"><i class="fa fa-list sx"></i> Playlist</a></li>
        <?php } ?>
    </ul>
</div>

</body>
</html>
