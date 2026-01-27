<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>StereoQ Player Update</title>
<style>
body {
    background:#0e141b;
    color:#fff;
    font-family: monospace;
}
.terminal {
    background:#11181F;
    border:2px solid #34495E;
    padding:20px;
    max-width:900px;
    margin:40px auto;
    height:500px;
    overflow:auto;
    white-space:pre-wrap;
}
button {
    padding:15px 40px;
    font-size:18px;
    font-weight:bold;
    cursor:pointer;
}
</style>
</head>

<body>

<?php if (!isset($_POST['update'])): ?>

<form method="POST" style="text-align:center;margin-top:100px;">
    <button type="submit" name="update" value="1">UPDATE STEREOQ</button>
</form>

<?php else: ?>

<div class="terminal"><?php
set_time_limit(3600);
ignore_user_abort(true);

/* КЛЮЧЕВОЕ */
header('X-Accel-Buffering: no');
header('Cache-Control: no-cache');

while (ob_get_level()) ob_end_flush();
ob_implicit_flush(true);

@ini_set('output_buffering','off');
@ini_set('zlib.output_compression',0);

echo "[+] Update started: ".date('H:i:s')."\n\n";
echo str_repeat(" ", 4096)."\n"; // пинок браузеру
flush();

/* САМЫЙ НАДЕЖНЫЙ СПОСОБ */
$cmd = 'sudo -n /bin/bash /sbin/update 2>&1';
$h = popen($cmd, 'r');

if ($h) {
    while (!feof($h)) {
        echo fgets($h);
        flush();
    }
    $code = pclose($h);
    echo "\n[+] Exit code: $code\n";
} else {
    echo "[!] Cannot start update\n";
}
?></div>

<?php endif; ?>

</body>
</html>
