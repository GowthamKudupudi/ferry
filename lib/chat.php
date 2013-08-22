<?php

/* Author: Gowtham */
require 'authorize.php';
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<chat>';
$action = $_POST['action'];
$key = $_SESSION['key'];
$shmId = shm_attach($key);
$semId = sem_get($key);
//post a chat
if (strpos($action, 'post') > -1) {
    $to = $_POST['to'];
    $msg = $_POST['msg'];
    $uid = $_SESSION['username'];
    $instTime = time();
    if ($to != null) {
        $toKey = ftok('../userFiles/' . $to . '/live.shm', 'c');
        $toSemId = sem_get($toKey);
        $toShmId = shm_attach($toKey);
        $i = 0;
        $sa = sem_acquire($toSemId);
        $toUser = shm_get_var($toShmId, $toKey);
        if (!$toUser['chtStatus']) {
            $mail = mail($to . '@ferryfair.com', 'chat', $msg, "From: " . $_SESSION['username'] . "@ferryfair.com\nContent-type: text/plain; charset='utf-8'\nContent-Transfer-Encoding: 8bit\n");
            echo "<olfrns>";
            echo "<" . $to . " status='offline'><lat></lat><msgs>";
            echo '<msg>';
            echo '<ts>' . $instTime . '</ts>';
            echo '<msgbody>' . $msg . '</msgbody>';
            echo '<rs>s</rs>';
            echo '</msg>';
            echo "</msgs></" . $to . ">";
            echo "</olfrns>";
            echo "<info>chat was mailed to ur frn ~:)~, bcoz he is off line.</info>";
        } else {
            $toUser['chat'][$uid]['msgs'][]['ts'] = $instTime;
            $chatIndex = count($toUser['chat'][$uid]['msgs']) - 1;
            $toUser['chat'][$uid]['msgs'][$chatIndex]['msg'] = $msg;
            $toUser['chat'][$uid]['msgs'][$chatIndex]['rs'] = 'r';
            $toUser['chat'][$uid]['nmsgs'][]['ts'] = $instTime;
            $nmi = count($toUser['chat'][$uid]['nmsgs']) - 1;
            $toUser['chat'][$uid]['nmsgs'][$nmi]['msg'] = $msg;
            $toUser['chat'][$uid]['nmsgs'][$nmi]['rs'] = 'r';
            shm_put_var($toShmId, $toKey, $toUser);
            sem_release($toSemId);
        }
        $sa = sem_acquire($semId);
        $user = shm_get_var($shmId, $key);
        $user['lat'] = $instTime;
        $user['chat'][$to]['msgs'][]['ts'] = $instTime;
        $ci = count($user['chat'][$to]['msgs']) - 1;
        $user['chat'][$to]['msgs'][$ci]['msg'] = $msg;
        $user['chat'][$to]['msgs'][$ci]['rs'] = 's';
        $user['chat'][$to]['nmsgs'][]['ts'] = $instTime;
        $ci = count($user['chat'][$to]['nmsgs']) - 1;
        $user['chat'][$to]['nmsgs'][$ci]['msg'] = $msg;
        $user['chat'][$to]['nmsgs'][$ci]['rs'] = 's';
        shm_put_var($shmId, $key, $user);
        sem_release($semId);
        echo "<status>success</status>";
    }
}

//update
if (strpos($action, 'update') > -1) {
    $sa = sem_acquire($semId);
    $user = shm_get_var($shmId, $key);
    $user['lat'] = time();
    echo "<olfrns>";
    foreach ($user['olFrns'] as $friend => $info) {
        if ($info['req'] != 's') {
            $fkey = $user['friends'][$friend]['key'];
            $fshmId = shm_attach($fkey);
            $fuser = shm_get_var($fshmId, $fkey);
            if ($fuser['chtStatus'] == 1) {
                echo "<" . $friend . " status='online' " . ($info['req'] == 'r' ? "req='r' nickName='" . $info['nickName'] . "'" : "") . "><lat>" . $fuser['lat'] . "</lat><msgs>";
                foreach ($user['chat'][$friend]['nmsgs'] as $msg => $msgAr) {
                    echo '<msg>';
                    echo '<ts>' . $msgAr['ts'] . '</ts>';
                    echo '<msgbody>' . $msgAr['msg'] . '</msgbody>';
                    echo '<rs>' . $msgAr['rs'] . '</rs>';
                    echo '</msg>';
                }
                unset($user['chat'][$friend]['nmsgs']);
                echo "</msgs></" . $friend . ">";
            } else {
                echo "<" . $friend . " status='offline' " . ($info['req'] == 'r' ? "req='r' nickName='" . $info['nickName'] . "'" : "") . "><lat>" . $fuser['lat'] . "</lat><msgs>";
                foreach ($user['chat'][$friend]['nmsgs'] as $msg => $msgAr) {
                    echo '<msg>';
                    echo '<ts>' . $msgAr['ts'] . '</ts>';
                    echo '<msgbody>' . $msgAr['msg'] . '</msgbody>';
                    echo '<rs>' . $msgAr['rs'] . '</rs>';
                    echo '</msg>';
                }
                unset($user['chat'][$friend]['nmsgs']);
                echo "</msgs></" . $friend . ">";
                unset($user['olFrns'][$friend]);
            }
        }
    }
    /* if (count($user['olFrns']) == 0 and $to) {
      echo "<" . $to . " status='offline'><lat></lat><msgs>";
      foreach ($user['chat'][$to]['nmsgs'] as $msg => $msgAr) {
      echo '<msg>';
      echo '<ts>' . $msgAr['ts'] . '</ts>';
      echo '<msgbody>' . $msgAr['msg'] . '</msgbody>';
      echo '<rs>' . $msgAr['rs'] . '</rs>';
      echo '</msg>';
      }
      unset($user['chat'][$to]['nmsgs']);
      echo "</msgs></" . $to . ">";
      unset($user['olFrns'][$to]);
      } */
    echo "</olfrns>";
    $spv = shm_put_var($shmId, $key, $user);
    $sr = sem_release($semId);
    if ($action == 'update')
        echo "<status>success</status>";
}


//sign into chat
if (strpos($action, 'signIn') > -1) {
    $semId = sem_get($key);
    $sa = sem_acquire($semId);
    $user = shm_get_var($shmId, $key);
    $user['chtStatus'] = 1;
    echo "<olfrns>";
    foreach ($user['friends'] as $friend => $info) {
        $fkey = $user['friends'][$friend]['key'];
        $fshmId = shm_attach($fkey);
        $fuser = shm_get_var($fshmId, $fkey);
        if ($fuser['chtStatus'] != 0) {
            $fsemId = sem_get($fkey);
            sem_acquire($fsemId);
            $fuser = shm_get_var($fshmId, $fkey);
            $fuser['olFrns'][$_SESSION['username']]['chtStatus'] = 1;
            $spv = shm_put_var($fshmId, $fkey, $fuser);
            $sr = sem_release($fsemId);
            $user['olFrns'][$fuser['username']]['chtStatus'] = $fuser['chtStatus'];
            echo "<" . $friend . " status='online' " . ($info['req'] == 'r' ? "req='r' nickName='" . $info['nickName'] . "'" : "") . "><lat>" . $fuser['lat'] . "</lat></" . $friend . ">";
        }
    }
    echo "</olfrns>";
    $spv = shm_put_var($shmId, $key, $user);
    $sr = sem_release($semId);
    if ($spv) {
        echo '<status>success</status>';
        if (!$sr) {
            echo '<error>sem release failed ~:|~ plzz inform administrator.</error>';
        }
    }
}

//signOut of chat
if (strpos($action, 'signOut') > -1) {
    $semId = sem_get($key);
    $sa = sem_acquire($semId);
    $user = shm_get_var($shmId, $key);
    $user['chtStatus'] = 0;
    $user['lat'] = time();
    foreach ($user['chat'] as $id => $chatBlock) {
        $chatMail = '';
        for ($i = count($chatBlock['msgs']) - 1; $i >= 0; $i--) {
            $chatMail.='<br/>' . $chatBlock['msgs'][$i]['ts'] . ':' . $chatBlock['msgs'][$i]['rs'] . ':' . $chatBlock['msgs'][$i]['msg'];
        }
        $mail = mail($_SESSION['username'] . '@ferryfair.com', 'chat', $chatMail, "From: " . $id . "@ferryfair.com\nContent-type: text/plain; charset='utf-8'\nContent-Transfer-Encoding: 8bit\n");
    }
    unset($user['chat']);
    unset($user['olFrns']);
    $spv = shm_put_var($shmId, $key, $user);
    $sr = sem_release($semId);
    if ($spv) {
        echo '<status>success</status>';
    }
}


//add a friend
if (strpos($action, 'addFrn') > -1) {
    $friend = substr($_POST['friend'], 0, strpos($_POST['friend'], '@'));
    $ffn = '../userFiles/' . $friend . '/live.shm';
    $toKey = ftok($ffn, 'c');
    if ($toKey > -1) {
        $toSemId = sem_get($toKey);
        $toShmId = shm_attach($toKey);
        $sa = sem_acquire($toSemId);
        $toUser = shm_get_var($toShmId, $toKey);
        if (!$toUser) {
            $f = fopen($ffn, 'r+');
            $userStr = fread($f, filesize($ffn));
            $toUser = unserialize($userStr);
            $fo = true;
        }
        if (!$toUser['friends'][$friend]) {
            $toUser['friends'][$_SESSION['username']]['req'] = 'r';
            $toUser['friends'][$_SESSION['username']]['nickName'] = $_SESSION['nickName'];
            $toUser['friends'][$_SESSION['username']]['key'] = $_SESSION['key'];
            $toUser['olFrns'][$_SESSION['username']]['req'] = 'r';
            $toUser['olFrns'][$_SESSION['username']]['nickName'] = $_SESSION['nickName'];
            $toUser['olFrns'][$_SESSION['username']]['key'] = $_SESSION['key'];
            if (!$fo) {
                $spv = shm_put_var($toShmId, $toKey, $toUser);
                $sd = shm_detach($toShmId);
                $sr = sem_release($toSemId);
            } else {
                $userStr = serialize($toUser);
                $fw = fwrite($f, $userStr);
                $fc = fclose($f);
            }
            $sa = sem_acquire($semId);
            $user = shm_get_var($shmId, $key);
            if (!$user['friends'][$friend]) {
                $user['friends'][$friend]['req'] = 's';
                $user['friends'][$friend]['nickName'] = $friend;
                $spv = shm_put_var($shmId, $key, $user);
                $sd = shm_detach($shmId);
                $sr = sem_release($semId);
                $fn = '../userFiles/' . $_SESSION['username'] . '/live.shm';
                $f = fopen($fn, 'r+');
                $userStr = serialize($user);
                $fw = fwrite($f, $userStr);
                $fc = fclose($f);
                echo '<status>success</status>';
            } else {
                echo "<status>ur request was resent to ur friend</status>";
            }
        } else {
            echo "<status>u already requested " . $friend . " ~:|~</status>";
        }
    } else {
        echo "<status>" . $friend . " don Exist ~:|~</status>";
    }
}

//accept friend req
if (strpos($action, 'acceptFrnReq') > -1) {
    $friend = $_POST['friend'];
    $i = 0;
    sem_acquire($semId);
    $user = shm_get_var($shmId, $key);
    if ($user['friends'][$friend]['req']) {
        unset($user['friends'][$friend]['req']);
        $spv = shm_put_var($shmId, $key, $user);
        $sd = shm_detach($shmId);
        $sr = sem_release($semId);
        if ($spv) {
            $ffn = '../userFiles/' . $friend . '/live.shm';
            $toKey = ftok($ffn, 'c');
            $toSemId = sem_get($toKey);
            $toShmId = shm_attach($toKey);
            $sa = sem_acquire($toSemId);
            $toUser = shm_get_var($toShmId, $toKey);
            if (!$toUser) {
                $f = fopen($ffn, 'r+');
                $userStr = fread($f, filesize($ffn));
                $toUser = unserialize($userStr);
                $fo = true;
            }
            unset($toUser['friends'][$_SESSION['username']]['req']);
            $toUser['friends'][$_SESSION['username']]['nickName'] = $_SESSION['nickName'];
            $toUser['friends'][$_SESSION['username']]['key'] = $_SESSION['key'];
            unset($toUser['olFrns'][$_SESSION['username']]['req']);
            $toUser['olFrns'][$_SESSION['username']]['nickName'] = $_SESSION['nickName'];
            $toUser['olFrns'][$_SESSION['username']]['key'] = $_SESSION['key'];
            if (!$fo) {
                $spv = shm_put_var($toShmId, $toKey, $toUser);
                $sd = shm_detach($toShmId);
                $sr = sem_release($toSemId);
            } else {
                $userStr = serialize($toUser);
                $fw = fwrite($f, $userStr);
                $fc = fclose($f);
            }
            if ($spv or $fw) {
                $fn = '../userFiles/' . $_SESSION['username'] . '/live.shm';
                $f = fopen($fn, 'r+');
                $userStr = serialize($user);
                $fw = fwrite($f, $userStr);
                $fc = fclose($f);
                echo '<status>success</status><frnStatus>' . $toUser['chtStatus'] . '</frnStatus>';
            } else {
                echo '<status>failed</status>';
            }
        } else {
            echo '<status>failed</status>';
        }
    } else {
        echo '<status>' . $friend . ' didnt send u any friend request ~%|~</status>';
    }
}
echo '</chat>';
?>
