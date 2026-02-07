<?php

$run = (isset($_POST['kernel_update']) && $_POST['kernel_update'] == '1');

if ($run) {
  @ini_set('max_execution_time', '0');
  @ini_set('max_input_time', '0');
  @ini_set('default_socket_timeout', '1800');
  @ini_set('output_buffering', 'off');
  @ini_set('zlib.output_compression', '0');

  @set_time_limit(0);
  @ignore_user_abort(true);

  if (function_exists('session_write_close')) {
    @session_write_close();
  }

  while (ob_get_level() > 0) {
    @ob_end_flush();
  }
  @ob_implicit_flush(true);

  header('Content-Type: text/html; charset=utf-8');
  header('X-Accel-Buffering: no');
  header('Cache-Control: no-cache, no-store, must-revalidate');
  header('Pragma: no-cache');
  header('Expires: 0');
}

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>StereoQ Kernel Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<style>
:root{
  --bg0:#2b3137;
  --bg1:#3a424c;
  --card:rgba(255,255,255,.08);
  --line:rgba(255,255,255,.10);
  --text:rgba(255,255,255,.92);
  --muted:rgba(255,255,255,.70);
  --muted2:rgba(255,255,255,.52);
  --accent:#14E681;
  --accent2:#4BBE87;
  --radius:16px;
  --radius2:12px;
  --shadow:0 14px 40px rgba(0,0,0,.40);
}
*{ box-sizing:border-box; }
html,body{
  height:100%;
  margin:0;
  color:var(--text);
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
  background:
    radial-gradient(1200px 700px at 50% 18%, rgba(255,255,255,.11), transparent 60%),
    radial-gradient(1000px 700px at 20% 0%, rgba(20,230,129,.06), transparent 62%),
    radial-gradient(900px 650px at 85% 10%, rgba(255,255,255,.07), transparent 65%),
    linear-gradient(180deg, var(--bg0), var(--bg1));
}
body::before{
  content:"";
  position:fixed;
  inset:0;
  pointer-events:none;
  background:repeating-linear-gradient(135deg, rgba(255,255,255,.02) 0 2px, transparent 2px 10px);
  opacity:.18;
}
.wrap{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:28px 16px 40px;
}
.panel{
  width:100%;
  max-width:920px;
  background:linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.03));
  border:1px solid var(--line);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  overflow:hidden;
}
.header{
  padding:18px 18px 12px;
  border-bottom:1px solid rgba(255,255,255,.08);
  background:rgba(24,28,33,.45);
}
.title{
  margin:0;
  font-size:18px;
  letter-spacing:.2px;
  font-weight:800;
}
.sub{
  margin:8px 0 0;
  color:var(--muted);
  font-size:13px;
  line-height:1.6;
}
.notice{
  padding:18px;
  text-align:center;
  color:var(--muted);
  font-size:14px;
  line-height:1.7;
}
.terminal{
  margin:0 18px 18px;
  background:rgba(17,24,31,.88);
  border:1px solid rgba(255,255,255,.12);
  border-radius:var(--radius2);
  padding:14px 14px;
  height:320px;
  overflow-y:auto;
  overflow-x:hidden;
  white-space:pre-wrap;
  line-height:1.55;
  font-size:13px;
  box-shadow: inset 0 1px 0 rgba(255,255,255,.06);
}
.actions{
  padding:0 18px 18px;
  text-align:center;
}
button,.btn{
  padding:12px 22px;
  font-size:16px;
  font-weight:800;
  cursor:pointer;
  border:1px solid transparent;
  border-radius:14px;
  text-decoration:none;
  display:inline-block;
  transition:transform .08s ease, filter .2s ease, background .2s ease, border-color .2s ease;
  user-select:none;
}
button{
  background:linear-gradient(180deg, rgba(20,230,129,.95), rgba(75,190,135,.92));
  border-color:rgba(20,230,129,.45);
  color:rgba(255,255,255,.92);
  box-shadow:0 10px 22px rgba(20,230,129,.16);
}
button:hover{ filter:brightness(1.03); }
button:active{ transform:translateY(1px); }
.btn{
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.16);
  color:rgba(255,255,255,.92);
  margin-left:10px;
  box-shadow:0 10px 22px rgba(0,0,0,.22);
}
.btn:hover{
  background:rgba(255,255,255,.09);
  border-color:rgba(255,255,255,.22);
}
.btn:active{ transform:translateY(1px); }
.hint{
  margin:10px 0 0;
  font-size:12px;
  color:var(--muted2);
}
@media (max-width: 520px){
  .terminal{ height:300px; font-size:12px; }
  button,.btn{ width:100%; margin:8px 0 0; }
  .btn{ margin-left:0; }
}
</style>
</head>

<body>

<?php if (!$run): ?>

<div class="wrap">
  <div class="panel">
    <div class="header">
      <h1 class="title">StereoQ Kernel Update</h1>
      <div class="sub">
        Internet access is required. Please keep this page open until the update finishes.
      </div>
    </div>

    <div class="notice">
      You are about to start updating the system kernel.<br>
      This may take up to 20 minutes. Please wait until the update finishes.<br>
      If the update is applied, reboot the player from the side menu.
    </div>

    <form method="POST" class="actions">
      <button type="submit" name="kernel_update" value="1">Kernel Update</button>
      <div class="hint">Do not refresh while the update is running.</div>
    </form>
  </div>
</div>

<?php else: ?>

<div class="wrap">
  <div class="panel">
    <div class="header">
      <h1 class="title">StereoQ Kernel Update</h1>
      <div class="sub">
        Kernel update is running. Output will appear below.
      </div>
    </div>

    <div class="terminal"><?php

function out_keepalive($s, $padBytes = 0) {
  echo $s;
  if ($padBytes > 0) {
    echo "<!--keepalive:" . str_repeat("k", $padBytes) . "-->";
  }
  @ob_flush();
  @flush();
}

out_keepalive("[+] Kernel update started: " . date('H:i:s') . "\n\n", 16384);

$cmd = 'sudo -n /bin/bash /sbin/kernel_update 2>&1';
$h = popen($cmd, 'r');
$code = 1;

if ($h) {
  stream_set_blocking($h, false);
  $lastBeat = time();

  while (true) {
    $read = array($h);
    $write = null;
    $except = null;

    $n = @stream_select($read, $write, $except, 1);

    if ($n === false) {
      break;
    }

    if ($n === 0) {
      if (time() - $lastBeat >= 2) {
        out_keepalive(".", 8192);
        $lastBeat = time();
      }
      if (feof($h)) {
        break;
      }
      continue;
    }

    while (($line = fgets($h)) !== false) {
      out_keepalive(htmlspecialchars($line, ENT_QUOTES, 'UTF-8'), 1024);
      $lastBeat = time();
    }

    if (feof($h)) {
      break;
    }
  }

  $code = pclose($h);

  out_keepalive("\n\n[+] Exit code: " . (int)$code . "\n", 4096);
  if ((int)$code === 0) {
    out_keepalive("[i] If the update is applied, reboot the player from the side menu.\n", 4096);
  }
} else {
  out_keepalive("[!] Cannot start kernel update\n", 4096);
}

?></div>

    <div class="actions">
      <a class="btn" href="/">← Back</a>
    </div>
  </div>
</div>

<?php endif; ?>

</body>
</html>
