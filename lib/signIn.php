<?php

header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<signIn>';
require_once 'inc.php';
include 'db_login.php';
if (!$dbc)
    die("<status>" . mysql_error() . "</status></signIn>");
if (@$_POST['signInType'] == 'object') {
    $password = sqlinjection_free($_POST['password']);
    $username = sqlinjection_free($_POST['username']);
    $query = "SELECT * FROM objectTable WHERE `id`='" . $username . "'";
    $result = mysql_db_query('collegedb2admin', $query, $dbc);
    if ($password and $username and mysql_result($result, 0, "passKey") == $password) {
        session_start();
        $_SESSION['authenticated'] = TRUE;
        $_SESSION['signInType'] = 'object';
        echo '<status>success</status>';
        echo '<sessionid>' . session_id() . '</sessionid>';
        $_SESSION['lastSessionStartTime'] = $_SESSION['sessionStartTime'];
        $_SESSION['sessionStartTime'] = time();
        $_SESSION['username'] = $username;
        $oid = mysql_result($result, 0, 'index');
        $_SESSION['uid'] = "o" . $oid;
        $_SESSION['oid'] = $oid;
        $_SESSION['pid'] = NULL;
        $_SESSION['authenticated'] = TRUE;
        $_SESSION['adminLevel'] = mysql_result($result, 0, "adminLevel");
        $_SESSION['userPic'] = NULL;
        $_SESSION['nickName'] = $username;
        $_SESSION['key'] = $key;
        $_SESSION['function'][$oid]['label'] = mysql_result($result, $i, 'type2');
        $_SESSION['function'][$oid]['func'] = mysql_result($result, $i, 'function');
        $_SESSION['function'][$oid]['aL'] = mysql_result($result, $i, 'adminLevel');
        $_SESSION['function'][$oid]['id'] = mysql_result($result, $i, 'id');
    }
} else {
    $username = strtolower(sqlinjection_free($_POST['username']));
    $password = sqlinjection_free($_POST['password']);
    $query = "select * from users where username='" . $username . "'";
    $result = mysql_db_query('collegedb2admin', $query, $dbc);
    if ($password and $username and mysql_result($result, 0, "password") == $password) {
        $uid = mysql_result($result, 0, 'index');
        $slKey = ftok("${_SERVER['DOCUMENT_ROOT']}/lib/sessionLog", 'f');
        $slSemId = sem_get($slKey);
        $slShmId = shm_attach($slKey, 1000000);
        $sa = sem_acquire($slSemId);
        @$sessionLog = shm_get_var($slShmId, $slKey);
        if (@$sessionId = $sessionLog[$uid]['sessionId']) {
            session_id($sessionId);
        }
        session_start();
        $sessionLog[$uid]['sessionId'] = $sessionId = $sessionId ? $sessionId : session_id();
        $spv = shm_put_var($slShmId, $slKey, $sessionLog);
        $sr = sem_release($slSemId);
        echo '<sessionid>' . $sessionId . '</sessionid>';
        @$_SESSION['lastSessionStartTime'] = $_SESSION['sessionStartTime'];
        $_SESSION['sessionStartTime'] = time();
        $_SESSION['username'] = $username;
        $_SESSION['uid'] = $uid;
        $_SESSION['pid'] = mysql_result($result, 0, "PID");
        $_SESSION['authenticated'] = TRUE;
        $_SESSION['signInType'] = 'user';
        $_SESSION['adminLevel'] = mysql_result($result, 0, "adminLevel");
        $mailPass = mysql_result($result, 0, 'emailPass');
        $query = "SELECT * FROM objectTable WHERE uid=" . $_SESSION['uid'] . " AND type1='NORMAL'";
        $result = mysql_db_query('collegedb2admin', $query, $dbc);
        $rowCount = mysql_num_rows($result);
        for ($i = 0; $i < $rowCount; $i++) {
            if (windowedAccess($_SESSION['adminLevel'], mysql_result($result, $i, 'adminLevel'))) {
                $oid = mysql_result($result, $i, 'index');
                $_SESSION['function'][$oid]['label'] = mysql_result($result, $i, 'type2');
                $_SESSION['function'][$oid]['func'] = mysql_result($result, $i, 'function');
                $_SESSION['function'][$oid]['aL'] = mysql_result($result, $i, 'adminLevel');
                $_SESSION['function'][$oid]['id'] = mysql_result($result, $i, 'id');
                echo "<position><oid>" . $oid . "</oid><id>" . $_SESSION['function'][$oid]['id'] . "</id><label>" . $_SESSION['function'][$oid]['label'] . "</label><func>" . $_SESSION['function'][$oid]['func'] . "</func><al>" . $_SESSION['function'][$oid]['aL'] . "</al></position>";
            }
        }
        $_SESSION['oid'] = $oid;
        $query = "SELECT * FROM `user_profiles` WHERE `index`=" . $_SESSION['pid'];
        $result = mysql_db_query('collegedb2admin', $query, $dbc);
        $_SESSION['userPic'] = mysql_result($result, 0, 'photo_id');
        $_SESSION['nickName'] = mysql_result($result, 0, 'nickName');
        echo "<userPic>" . $_SESSION['userPic'] . "</userPic>";
        $shmFName = $_SERVER['DOCUMENT_ROOT'].'/userFiles/' . $username . '/live.shm';
        $key = ftok($shmFName, 'c');
        $semId = sem_get($key);
        sem_acquire($semId);
        $shmId = shm_attach($key);
        $user = shm_get_var($shmId, $key);
        if (!$user) {
            $f = fopen($shmFName, "r");
            $userStr = fread($f, filesize($shmFName));
            fclose($f);
            $user = unserialize($userStr);
        }
        $user['status'] = 1;
        $user['nickName'] = $_SESSION['nickName'];
        $user['username'] = $_SESSION['username'];
        $user['lat'] = time();
        $user['uid'] = $_SESSION['uid'];
        unset($user['olFrns']);
        $spv = shm_put_var($shmId, $key, $user);
        $sd = shm_detach($shmId);
        sem_release($semId);
        echo "<status>success</status>";
    } else {
        session_start();
        $_SESSION['authenticated'] = FALSE;
        echo "<status>failure</status>";
    }
}
echo '</signIn>'
?>