<?php

//Author: Gowtham
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
session_id($_POST['SESSION_ID']);
session_start();
require "$root/lib/inc.php";
require "$root/lib/formValidator.php";
include 'db_login.php';
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<register>';
require "$root/lib/recaptchalib.php";
$privatekey = "6Lf7sdASAAAAAKXALMdLPQMowDFkQhppTdj9Dufe";
$resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
if ((!$resp->is_valid) and FALSE) {
    // What happens when the CAPTCHA was entered incorrectly
    echo ("<status>The reCAPTCHA wasn't entered correctly. Go back and try it again.(reCAPTCHA said: " . $resp->error . ")</status>");
} else {
    // Your code here to handle a successful verification
    $usrname = sqlinjection_free($_POST['username']);
    $username = strtolower(valid_mysql_query_data($_POST['username']));
    $password = valid_mysql_query_data($_POST['password']);
    $full_name = valid_mysql_query_data($_POST['fullName']);
    $nickName = valid_mysql_query_data($_POST['nickName']);
    $gaurdian_id = valid_mysql_query_data($_POST['gaurdianID']);
    $sex = valid_mysql_query_data($_POST['sex']);
    $dob = valid_mysql_query_data($_POST['DOB']);
    $p_address = valid_mysql_query_data($_POST['pAddress']);
    $tel1 = valid_mysql_query_data($_POST['tel1']);
    $tel2 = valid_mysql_query_data($_POST['tel2']);
    $email_id = valid_mysql_query_data($_POST['emailID']);
    $photo_id = valid_mysql_query_data($_POST['photoID']);
    $ePass = generatePassword(16, 8);
    $domain = 'ferryfair.com';
    $error_form = validate_form($username, $password, $full_name, $nickName, $gaurdian_id, $dob, $p_address, $tel1, $tel2, $email_id);
    if ($error_form == "") {
        $query = "INSERT INTO `users` (`username`, `password`,`emailPass`) VALUES (" . $username . ", " . $password . ",'" . $ePass . "')";
        $result = mysql_query($query, $dbc);
        $error1 = mysql_error($dbc);
        $lastUIndex = mysql_insert_id($dbc);
        if (!$error1) {
            $query = "INSERT INTO `user_profiles` (`full_name`,`nickName`, `gaurdian_id`, `sex`, `DOB`, `permenent_address`, `telephone_no1`, `telephone_no2`, `email_id`, `photo_id`) VALUES (" . $full_name . ", " . $nickName . ", " . $gaurdian_id . ", " . $sex . ", " . $dob . ", " . $p_address . ", " . $tel1 . ", " . $tel2 . ", " . $email_id . ", " . $photo_id . ")";
            $result = mysql_query($query, $dbc);
            $error2 = mysql_error($dbc);
            $lastPIndex = mysql_insert_id($dbc);
            if (!$error2) {
                $us = TRUE;
                $query = "UPDATE users SET users.PID=" . $lastPIndex . " WHERE users.index=" . $lastUIndex;
                $result = mysql_query($query, $dbc);
                $error3 = mysql_error($dbc);
                if (!$error3) {
                    $mkdir = mkdir('../../userFiles/' . $usrname);
                    $fhandler = fopen('../../userFiles/' . $usrname . '/live.shm', 'w');
                    fclose($fhandler);
                    $photo_id = $_POST['photoID'];
                    if (copy('../../tmp/' . $photo_id, '../../userFiles/' . $usrname . '/' . $photo_id)) {
                        unlink('../../tmp/' . $photo_id);
                    }
// Connect to hMailServer
                    require 'HTTP/Request2.php';
                    $url = 'http://mail.ferryfair.com/PHPWebAdmin/standardAccountCreator.php';
                    $r = new Http_Request2($url);
                    $r->setMethod(HTTP_Request2::METHOD_POST);
                    $r->setHeader(array(
                        "Content-Type" => "application/x-www-form-urlencoded"
                    ));
                    $r->addPostParameter(array(
                        'username' => $usrname,
                        'ePass' => $ePass,
                        'fullName' => $_POST['fullName']
                    ));
                    $r->addCookie('PHPSESSID', 'bkq0t2vf2n9898t4q83jje7bp7');
                    $r->addCookie('XDEBUG_SESSION', 'netbeans-xdebug');
                    $response = $r->send();
                    $page = $response->getBody();
                    $dd = new DOMDocument();
                    $dd->loadXML($page);
                    $st = innerHTML($dd->getElementsByTagName('status')->item(0));
                    $accountId = innerHTML($dd->getElementsByTagName('accountId')->item(0));
                    if ($mkdir && $accountId) {
                        $key = ftok('../../userFiles/' . $usrname . '/live.shm', 'c');
                        $shmId = shm_attach($key);
                        $user['status'] = 0;
                        $user['nickName'] = trim($nickName, "'");
                        $user['username'] = $usrname;
                        $user['lat'] = time();
                        $user['uid'] = $lastUIndex;
                        $spv = shm_put_var($shmId, $key, $user);
                        $sd = shm_detach($shmId);
                        echo "<status>success</status>";
                    } else {
                        echo "<status>registeration successful but unable to create user directory. or update objectTable ~:|~ Contact administrator.</status>";
                    }
                } else {
                    $query = "DELETE FROM user_profiles where index=" . $lastPIndex;
                    $result = mysql_query($query, $dbc);
                    echo "<status>failure, " . $error3 . "</status>";
                }
            } else {
                $query = "DELETE FROM users where users.index=" . $lastUIndex;
                $result = mysql_query($query, $dbc);
                echo "<status>" . $error2 . "</status>";
            }
        } else {
            echo "<status>" . $error1 . "</status>";
        }
    } else {
        echo "<status>" . $error_form . "</status>";
    }

//logging changes..
    if ($error_form == '') {
        if (!$error1) {
            $queryts = "INSERT INTO `users` (`index`,`username`, `password`,`emailPass`) VALUES (" . $lastUIndex . ",now(),now(),now())";
            $resultts = mysql_query($queryts, $timestampLink);
            $errorts = mysql_error($timestampLink);
            $queryu = "INSERT INTO `users` (`index`,`username`, `password`) VALUES (" . $lastUIndex . ",0,0,0)";
            $resultu = mysql_query($queryu, $uidLink);
            $erroru = mysql_error($uidLink);
            if (!$error2) {
                $queryts = "INSERT INTO `user_profiles` (`index`,`full_name`,`nickName`, `gaurdian_id`, `sex`, `DOB`, `permenent_address`, `telephone_no1`, `telephone_no2`, `email_id`, `photo_id`) VALUES (" . $lastPIndex . ",now(),now(),now(),now(),now(),now(),now(),now(),now(),now())";
                $resultts = mysql_query($queryts, $timestampLink);
                $errorts = mysql_error($timestampLink);
                $queryu = "INSERT INTO `user_profiles` (`index`,`full_name`,`nickName`, `gaurdian_id`, `sex`, `DOB`, `permenent_address`, `telephone_no1`, `telephone_no2`, `email_id`, `photo_id`) VALUES (" . $lastPIndex . ",0,0,0,0,0,0,0,0,0,0)";
                $resultu = mysql_query($queryu, $uidLink);
                $erroru = mysql_error($uidLink);
                if (!$error3) {
                    $queryts = "UPDATE users SET users.PID=now() WHERE users.index=" . $lastUIndex;
                    $resultts = mysql_query($queryts, $timestampLink);
                    $errorts = mysql_error($timestampLink);
                    $queryu = "UPDATE users SET users.PID='0' WHERE users.index=" . $lastUIndex;
                    $resultu = mysql_query($queryu, $uidLink);
                    $erroru = mysql_error($uidLink);
                }
            } else {
                $queryts = "UPDATE users set `username`=now(), `password`=now(), `PID`=now(), `adminLevel`=now(), `emailPass`=now() WHERE users.index=" . $lastUIndex;
                $resultts = mysql_query($queryts, $timestampLink);
                $errorts = mysql_error($timestampLink);
                $queryu = "UPDATE users set `username`=0, `password`=0, `PID`=0, `adminLevel`=0, `emailPass`=0 WHERE users.index=" . $lastUIndex;
                $resultu = mysql_query($queryu, $uidLink);
                $erroru = mysql_error($uidLink);
            }
        }
    }
}
echo '</register>';
mysql_close($dbc);
mysql_close($timestampLink);
mysql_close($uidLink);
?>
