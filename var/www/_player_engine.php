<?php
include('inc/connection.php');

@ini_set('default_socket_timeout', '30'); // 

playerSession('open', $db, '', '');

if (!$mpd) {
  header('Content-Type: application/json');
  http_response_code(503);
  echo json_encode(array('error' => 'Error Connecting MPD Daemon'));
  exit;
}

// fetch MPD status
$status = _parseStatusResponse(MpdStatus($mpd));

$cmediafixEnabled = (isset($_SESSION['cmediafix']) && (int)$_SESSION['cmediafix'] === 1);
$lastBitdepth = null;

if ($cmediafixEnabled) {
  $lastBitdepth = isset($status['audio']) ? $status['audio'] : null;
  $_SESSION['lastbitdepth'] = $lastBitdepth;
}

// register player STATE in SESSION
$_SESSION['state'] = isset($status['state']) ? $status['state'] : 'unknown';

// 
session_write_close();

// compare GUI state with Backend state
$reqState = isset($_GET['state']) ? (string)$_GET['state'] : '';
if ($reqState !== '' && isset($status['state']) && $reqState === $status['state']) {
  // long-poll до смены состояния
  $newStatus = monitorMpdState($mpd);
  if (is_array($newStatus) && isset($newStatus['state'])) {
    $status = $newStatus;
  }
}

$curTrack = getTrackInfo($mpd, isset($status['song']) ? $status['song'] : null);

$file = isset($curTrack[0]['file']) ? $curTrack[0]['file'] : '';
$title = isset($curTrack[0]['Title']) ? $curTrack[0]['Title'] : '';

if ($title !== '') {
  $status['currentartist'] = isset($curTrack[0]['Artist']) ? $curTrack[0]['Artist'] : '';
  $status['currentsong']   = $title;
  $status['currentalbum']  = isset($curTrack[0]['Album']) ? $curTrack[0]['Album'] : '';
  $status['fileext']       = $file !== '' ? parseFileStr($file, '.') : '';
} else {
  $path = $file !== '' ? parseFileStr($file, '/') : '';
  $status['fileext']       = $file !== '' ? parseFileStr($file, '.') : '';
  $status['currentartist'] = '';
  $status['currentsong']   = $file !== '' ? basename($file) : '';
  $status['currentalbum']  = $path !== '' ? (' ' . $path) : '';
}


// CMediaFix (track change only)
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if ($cmediafixEnabled && isset($status['state']) && $status['state'] === 'play') {

  $cur = null;
  if (isset($status['songid']) && $status['songid'] !== '') {
    $cur = $status['songid'];
  } else if (isset($status['song']) && $status['song'] !== '') {
    $cur = $status['song'];
  } else if (isset($status['file']) && $status['file'] !== '') {
    $cur = $status['file'];
  }

  if ($cur !== null) {
    $last = isset($_SESSION['last_track_id']) ? $_SESSION['last_track_id'] : null;

    if ($last !== null && $last !== $cur) {
      sendMpdCommand($mpd, 'cmediafix');
    }

    $_SESSION['last_track_id'] = $cur;
  }

} else {
  if (isset($_SESSION['last_track_id'])) unset($_SESSION['last_track_id']);
}

session_write_close();


header('Content-Type: application/json');
echo json_encode($status);

closeMpdSocket($mpd);
?>
