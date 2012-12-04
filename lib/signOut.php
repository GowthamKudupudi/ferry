<?php

if ($_POST['SESSION_ID']) {
    session_id($_POST['SESSION_ID']);
}
session_start();
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/lib/inc.php";
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
//$uid = $_SESSION['uid'];
if (@$_SESSION['signInType'] != 'object') {
//signOut of chat
    $key = ftok("${_SERVER['DOCUMENT_ROOT']}/lib/sessionLog", 'f');
    $shmId = shm_attach($key);
    $semId = sem_get($key);
    sem_acquire($semId);
    $user = shm_get_var($shmId, $key);
    $user['status'] = 0;
    $user['chtStatus'] = 0;
    $user['lat'] = time();
    if (isset($user['chat'])) {
        foreach ($user['chat'] as $id => $chatBlock) {
            $chatMail = '';
            for ($i = count($chatBlock['msgs']) - 1; $i >= 0; $i--) {
                $chatMail.='<br/>' . $chatBlock['msgs'][$i]['ts'] . ':' . $chatBlock['msgs'][$i]['rs'] . ':' . $chatBlock['msgs'][$i]['msg'];
            }
            $mail = mail($_SESSION['username'] . '@ferryfair.com', 'chat', $chatMail, "From: " . $id . "@ferryfair.com\nContent-type: text/plain; charset='utf-8'\nContent-Transfer-Encoding: 8bit\n");
        }
        unset($user['chat']);
    }
    unset($user['olFrns']);
    $spv = shm_put_var($shmId, $key, $user);
    $sr = sem_release($semId);
    $sd = shm_detach($shmId);
    //$shr = shm_remove($shmId);
    $userStr = serialize($user);
    $f = fopen("${_SERVER['DOCUMENT_ROOT']}/userFiles/" . $_SESSION['username'] . "/live.shm", 'r+');
    $fw = fwrite($f, $userStr);
    $fc = fclose($f);
}

//close live tables
if (isset($_SESSION['tables'])) {
    foreach ($_SESSION['tables'] as $tid => $value) {
        $liveDBTable = getLiveTable($tid);
        foreach ($liveDBTable['dbtUpdates'] as $key => &$update) {
            if ($update['data']['swallowedBy'][$_SESSION['uid']]) {
                unset($update['data']['swallowedBy'][$_SESSION['uid']]);
            }
            if (count($sdbtu['data']['swallowedBy']) == count($liveDBTable['usersData']) - 1) {
                unset($liveDBTable['dbtUpdates'][$key]);
            }
        }
        if ($liveDBTable['usersData'][$_SESSION['uid']] and count($liveDBTable['usersData']) == 1) {
            $liveDBTable = null;
            closeLiveTable($tid, $liveDBTable);
            removeLiveTable($tid);
        } else {
            unset($liveDBTable['usersData'][$_SESSION['uid']]);
            closeLiveTable($tid, $liveDBTable);
        }
    }
}

//Destroy session log
$slKey = $key;
$slSemId = sem_get($slKey);
$slShmId = shm_attach($slKey, 1000000);
$sa = sem_acquire($slSemId);
$sessionLog = shm_get_var($slShmId, $slKey);
unset($sessionLog[$_SESSION['uid']]);
$spv = shm_put_var($slShmId, $slKey, $sessionLog);
$sr = sem_release($slSemId);

//$mysql_close = mysql_close();
$session_destroy = session_destroy();
echo "<status>signedOut</status>";
?>
