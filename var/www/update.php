<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>StereoQ Player Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<style>
:root{
  --bg0:#2b3137;
  --bg1:#3a424c;
  --card:rgba(255,255,255,.08);
  --card2:rgba(255,255,255,.10);
  --line:rgba(255,255,255,.10);
  --text:rgba(255,255,255,.92);
  --muted:rgba(255,255,255,.70);
  --muted2:rgba(255,255,255,.52);
  --accent:#14E681;
  --accent2:#4BBE87;
  --shadow:0 18px 44px rgba(0,0,0,.48);
  --radius:18px;
  --radius2:14px;
}

html,body{ height:100%; }
body{
  margin:0;
  background:
    radial-gradient(1200px 700px at 50% 18%, rgba(255,255,255,.11), transparent 60%),
    radial-gradient(1000px 700px at 20% 0%, rgba(20,230,129,.06), transparent 62%),
    radial-gradient(900px 650px at 85% 10%, rgba(255,255,255,.07), transparent 65%),
    linear-gradient(180deg, var(--bg0), var(--bg1));
  color:var(--text);
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
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
  padding:24px 14px;
}

.card{
  width:min(980px, 100%);
  background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
  border:1px solid var(--line);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  overflow:hidden;
}

.header{
  padding:22px 26px 14px;
  border-bottom:1px solid rgba(255,255,255,.08);
}

.title{
  margin:0;
  font-size:20px;
  letter-spacing:.3px;
}

.sub{
  margin:8px 0 0;
  font-size:13px;
  color:var(--muted);
  line-height:1.6;
}

.body{
  padding:20px 26px 22px;
}

.notice{
  text-align:center;
  color:var(--muted);
  line-height:1.8;
  font-size:14px;
  margin:8px 0 18px;
}

.actions{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:12px;
  flex-wrap:wrap;
  margin-top:10px;
}

button,.btn{
  appearance:none;
  border:1px solid rgba(255,255,255,.14);
  border-radius:var(--radius2);
  padding:12px 22px;
  font-size:16px;
  font-weight:700;
  cursor:pointer;
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  color:rgba(255,255,255,.92);
  background:rgba(255,255,255,.08);
  box-shadow: 0 12px 26px rgba(0,0,0,.32), inset 0 1px 0 rgba(255,255,255,.10);
  transition: transform .08s ease, background .2s ease, border-color .2s ease, filter .2s ease;
}

button:hover,.btn:hover{
  background:rgba(255,255,255,.11);
  border-color:rgba(255,255,255,.20);
}

button:active,.btn:active{
  transform: translateY(1px);
}

button.primary{
  background: linear-gradient(180deg, rgba(20,230,129,.95), rgba(75,190,135,.92));
  border: 1px solid rgba(20,230,129,.45);
  box-shadow: 0 10px 22px rgba(20,230,129,.16);
}

button.primary:hover{
  filter: brightness(1.03);
}

.btn.back{
  color: rgba(20,230,129,.92);
  border-color: rgba(20,230,129,.28);
  background: rgba(255,255,255,.06);
}

.hint{
  width:100%;
  text-align:center;
  margin-top:8px;
  font-size:12px;
  color:var(--muted2);
}

.terminal{
  background: rgba(17,24,31,.92);
  border:1px solid rgba(255,255,255,.12);
  border-radius:var(--radius2);
  padding:16px 16px;
  height:320px;
  overflow-y:auto;
  overflow-x:hidden;
  white-space:pre-wrap;
  line-height:1.45;
  font-size:13px;
  box-shadow: inset 0 1px 0 rgba(255,255,255,.06);
}

.footer{
  padding:14px 26px 20px;
  display:flex;
  justify-content:center;
  border-top:1px solid rgba(255,255,255,.06);
}

@media (max-width: 520px){
  .header,.body,.footer{ padding-left:16px; padding-right:16px; }
  .terminal{ height:300px; }
}
</style>
</head>

<body>
<div class="wrap">
  <div class="card">
    <?php if (!isset($_POST['update'])): ?>

      <div class="header">
        <h1 class="title">StereoQ Player Update</h1>
        <div class="sub">Internet access is required. Please keep this page open until the update finishes.</div>
      </div>

      <div class="body">
        <div class="notice">
          You are about to start updating the player.<br>
          This may take up to 20 minutes. Please wait until the update finishes.<br>
          After the update completes, you may need to reboot the player manually.
        </div>

        <form method="POST" class="actions">
          <a class="btn back" href="/">← Back</a>
          <button class="primary" type="submit" name="update" value="1">Update Player</button>
          <div class="hint">Do not refresh while the update is running.</div>
        </form>
      </div>

    <?php else: ?>

      <div class="header">
        <h1 class="title">StereoQ Player Update</h1>
        <div class="sub">Update is running. Keep this tab open until it completes.</div>
      </div>

      <div class="body">
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
      </div>

      <div class="footer">
        <a class="btn back" href="/">← Back</a>
      </div>

    <?php endif; ?>
  </div>
</div>
</body>
</html>
