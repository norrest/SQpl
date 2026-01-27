<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>StereoQ Player Update</title>
    <link rel="stylesheet" href="panels.css">
    <style>
        .update-container {
            padding: 40px 0;
            text-align: center;
            background: #2B3137;
            min-height: 100vh;
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
        .red { color: #f00 !important; }
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
        .status {
            font-size: 20px;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .success { background: rgba(75,190,135,0.3); color: #4BBE87; }
        .updating { background: rgba(255,255,0,0.2); color: #ff0; }
        .error { background: rgba(255,0,0,0.15); color: #ff6060; }
    </style>
</head>
<body>
    <div class="container update-container">

        <?php if (!isset($_POST['update'])): ?>
            <h1 style="color: #00FF84; font-size: 36px;">StereoQ Player Update</h1>
            <p style="color: #7AF6BA; font-size: 18px;">Ready to update?</p>

            <form method="POST" style="margin: 30px 0;">
                <button type="submit" name="update" value="1" class="btn-update">
                    🚀 Update StereoQ
                </button>
            </form>

        <?php else: ?>
            <div class="status updating">⏳ Updating StereoQ... Please wait!</div>

            <div id="terminal" class="terminal"><?php
                // "Realtime" output
                ob_implicit_flush(true);
                while (ob_get_level() > 0) { ob_end_flush(); }
                @ini_set('output_buffering', 'off');
                @ini_set('zlib.output_compression', '0');

                echo "[+] Update started at " . date('H:i:s') . "\n";

                $cmd = 'sudo -n /bin/bash /sbin/update';
                $descriptorspec = [
                    0 => ["pipe", "r"], // stdin
                    1 => ["pipe", "w"], // stdout
                    2 => ["pipe", "w"], // stderr
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
                        usleep(100000); // 0.1s
                    }

                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($proc);

                    echo "\n[+] Exit code: {$exitCode}\n";
                } else {
                    echo "[!] Failed to start update process\n";
                }
            ?></div>

            <?php if (isset($exitCode) && (int)$exitCode === 0): ?>
                <div class="status success">✅ Update complete!</div>
            <?php else: ?>
                <div class="status error">❌ Update failed (exit code: <?= htmlspecialchars((string)($exitCode ?? 'unknown')) ?>)</div>
            <?php endif; ?>

            <a href="update.php" class="btn-update">🔄 Update again</a>
            <a href="/" class="btn-update" style="background: #7A848E;">← Back Home</a>
        <?php endif; ?>

    </div>

    <script>
        // Auto-scroll terminal to bottom during output
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
