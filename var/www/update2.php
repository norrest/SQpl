<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>StereoQ Player Update</title>
<style>
body{
  background:#0e141b;
  color:#fff;
  font-family:monospace;
}
.terminal{
  background:#11181F;
  border:2px solid #34495E;
  padding:20px;
  max-width:900px;
  margin:40px auto;
  height:280px;
  overflow:auto;
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

<?php if (!isset($_POST['update'])): ?>

<form method="POST" class="actions" style="margin-top:100px;">
  <button type="submit" name="update" value="1">Update Player</button>
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

echo "[+] Update started: ".date('H:i:s')."\n\n";
echo str_repeat(" ", 4096)."\n";
flush();

$cmd = 'sudo -n /bin/bash /sbin/update 2>&1';
$h = popen($cmd, 'r');

$code = null;

if ($h) {
  while (!feof($h)) {
    $line = fgets($h);
    if ($line !== false) {
      echo $line;
      flush();
    }
  }
  $code = pclose($h);
  echo "\n[+] Exit code: ".$code."\n";
} else {
  echo "[!] Cannot start update\n";
  $code = 1;
}
?></div>

<div class="actions">
  <a class="btn" href="/">← Back</a>
</div>

<?php endif; ?>

</body>
</html>
