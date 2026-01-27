<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>StereoQ Player Update</title>
    <link rel="stylesheet" href="panels.css">
    <style>
        .update-page {
            padding: 80px 0 80px;
            text-align: center;
        }
        .terminal {
            background: #11181F;
            border: 2px solid #34495E;
            border-radius: 5px;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            max-height: 500px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 14px;
            line-height: 1.4;
            color: #fff;
            white-space: pre-wrap;
            text-align: left;
        }
        .btn-update {
            background: #4BBE87;
            color: #fff !important;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        .btn-update:hover {
            background: #14E681;
            color: #fff !important;
        }
        .btn-grey {
            background: #7A848E;
        }
        .btn-grey:hover {
            background: #525C66;
        }
        .status {
            font-size: 20px;
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            font-weight: bold;
            max-width: 800px;
        }
        .status.updating { background: rgba(255,255,0,0.15); color: #ff0; }
        .status.ok { background: rgba(75,190,135,0.25); color: #4BBE87; }
        .status.bad { background: rgba(255,0,0,0.15); color: #ff6060; }
    </style>
</head>

<body>
<div id="wrap">
    <div id="container">
        <div class="container update-page">

            <?php if (!isset($_POST['update'])): ?>

                <h1 style="color: #00FF84; font-size: 36px; margin: 0 0 10px;">StereoQ Player Update</h1>
                <p style="color: #7AF6BA; font-size: 18px; margin: 0 0 25px;">Ready to update?</p>

                <form method="POST" style="margin: 30px 0;">
                    <button type="submit" name="update" value="1" class="btn-update">🚀 Update StereoQ</button>
                </form>

            <?php else: ?>

                <div class="status updating">⏳ Updating StereoQ... Please wait!</div>

                <div id="terminal" class="terminal"><?php
                    ob_implicit_flush(true);
                    while (ob_get_level() > 0) { ob_end_flush(); }
                    @ini_set('output_buffering', 'off');
                    @ini_set('zlib.output_compression', '0');

                    echo "[+] Update started at " . date('H:i:s') . "\n";

                    $cmd = 'sudo -n /bin/bash /sbin/update';
                    $descriptorspec = [
                        0 => ["pipe", "r"],
                        1 => ["pipe", "w"],
                        2 => ["pipe", "w"],
                    ];

                    $proc = proc_open($cmd, $descriptorspec, $pipes);
                    $exitCode = 1;

                    if (is_resource($proc)) {
                        fclose($pipes[0]);
                        stream_set_blocking($pipes[1], false);
                        stream_set_blocking($pipes[2], false);

                        while (true) {
                            $out = stream_get_contents($pipes[1]);
                            $err = stream_get_contents($pipes[2]);

                            if ($out !== '') { echo $out; }
                            if ($err !== '') { echo $err; }

                            flush();

                            $status = proc_get_status($proc);
                            if (!$status['running']) {
                                $exitCode = $status['exitcode'];
                                break;
                            }
                            usleep(100000);
                        }

                        fclose($pipes[1]);
                        fclose($pipes[2]);
                        proc_close($proc);

                        echo "\n[+] Exit code: {$exitCode}\n";
                    } else {
                        echo "[!] Failed to start update process\n";
                    }
                ?></div>

                <?php if ((int)$exitCode === 0): ?>
                    <div class="status ok">✅ Update complete!</div>
                <?php else: ?>
                    <div class="status bad">❌ Update failed (exit code: <?= htmlspecialchars((string)($exitCode ?? 'unknown')) ?>)</div>
                <?php endif; ?>

                <a href="update.php" class="btn-update">🔄 Update again</a>
                <a href="/" class="btn-update btn-grey">← Back Home</a>

            <?php endif; ?>

        </div>
    </div>

    <div class="push"></div>
</div>

<script>
(function () {
    var t = document.getElementById('terminal');
    if (!t) return;
    var last = 0;
    setInterval(function () {
        if (t.textContent.length !== last) {
            last = t.textContent.length;
            t.scrollTop = t.scrollHeight;
        }
    }, 250);
})();
</script>
</body>
</html>
