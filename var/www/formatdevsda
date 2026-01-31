<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>StereoQ Player Format /dev/sda</title>
<style>
body{
  background:#0e141b;
  color:#fff;
  font-family:monospace;
}
.notice{
  max-width:900px;
  margin:100px auto 10px;
  padding:0 20px;
  color:#cfe7ff;
  line-height:1.6;
  text-align:center;
  font-size:14px;
  opacity:.95;
}
.terminal{
  background:#11181F;
  border:2px solid #34495E;
  padding:20px;
  max-width:900px;
  margin:40px auto;
  height:280px;
  overflow-y:auto;
  overflow-x:hidden;
  white-space:pre-wrap;
}
.actions{
  text-align:center;
  margin:16px auto 40px;
}
button,.btn{
  padding:15px 40px;
  font-size:18px;
  font-weight:bold;
  cursor:pointer;
  border:0;
  border-radius:6px;
  text-decoration:none;
  display:inline-block;
}
button{
  background:#4BBE87;
  color:#fff;
}
button:hover{
  background:#14E681;
}
.btn{
  background:#7A848E;
  color:#fff;
  margin-left:10px;
}
.btn:hover{
  background:#525C66;
}
</style>
</head>

<body>

<?php if (!isset($_POST['format'])): ?>

<div class="notice">
  You are about to format <b>/dev/sda</b> and create a single <b>ext4</b> partition spanning the whole disk.<br>
  On this player, <b>/dev/sda</b> is the default attached storage used for your music library.<br>
  <b>All data on /dev/sda will be permanently destroyed.</b><br>
  The process may take a while. Do not close this page until it finishes.
</div>

<form method="POST" class="actions">
  <button type="submit" name="format" value="1">FORMAT /dev/sda</button>
</form>

<?php else: ?>

<div class="terminal"><?php
set_time_limit(3600);
ignore_user_abort(true);

header('X-Accel-Buffering: no');
header('Cache-Control: no-cache');

while (ob_get_level()) { ob_end_flush(); }
ob_implicit_flush(true);

@ini_set('output_buffering','off');
@ini_set('zlib.output_compression',0);

echo "[+] Format started: ".date('H:i:s')."\n\n";
echo str_repeat(" ", 4096)."\n";
flush();

/*
  Requirements:
    chmod +x /sbin/formatdevsda

  sudoers (for sudo -n):
    www-data ALL=(root) NOPASSWD: /sbin/formatdevsda
*/

$cmd = 'sudo -n /sbin/formatdevsda 2>&1';
$h = popen($cmd, 'r');

$raw = null;

if ($h) {
  while (!feof($h)) {
    $line = fgets($h);
    if ($line !== false) {
      echo $line;
      flush();
    }
  }
  $raw = pclose($h);

  // pclose() returns a status value, not always the plain exit code
  $exitCode = $raw;
  if (function_exists('pcntl_wexitstatus')) {
    $exitCode = pcntl_wexitstatus($raw);
  } else {
    // fallback: common case where exit code is shifted
    if (is_int($raw) && $raw > 255) $exitCode = $raw >> 8;
  }

  echo "\n[+] Exit code: ".$exitCode."\n";
} else {
  echo "[!] Cannot start /sbin/formatdevsda\n";
}
?></div>

<div class="actions">
  <a class="btn" href="/">← Back</a>
</div>

<?php endif; ?>

</body>
</html>
