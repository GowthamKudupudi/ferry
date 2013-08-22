<?php

include 'authorize.php';
require 'db_login.php';
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/lib/inc.php";
require "$root/lib/formValidator.php";
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<editprofile>';
$username = $_SESSION['username'];
$password = "1Aa1Aa1Aa";
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
$error_form = validate_form($username, $password, $full_name, $nickName, $gaurdian_id, $dob, $p_address, $tel1, $tel2, $email_id);
if ($error_form == "") {
    $query = "UPDATE  `user_profiles` SET  `full_name`=" . $full_name . ",`nickName`=" . $nickName . ",`gaurdian_id`=" . $gaurdian_id . ",`sex`=" . $sex . ",`DOB`=" . $dob . ",`permenent_address` = " . $p_address . ",`telephone_no1`=" . $tel1 . ",`telephone_no2` =" . $tel2 . ",`email_id`=" . $email_id . ",`photo_id`=" . $photo_id . " WHERE `index` =" . $_SESSION['pid'] . ";";
    $query2 = "UPDATE  `user_profiles` SET `full_name`=now(),`nickName`=now(),`gaurdian_id`=now(),`sex`=now(),`DOB`=now(),`permenent_address` = now(),`telephone_no1`=now(),`telephone_no2` =now(),`email_id`=now() WHERE `index` =" . $_SESSION['pid'];
    $query3 = "UPDATE  `user_profiles` SET `full_name`=" . $_SESSION['uid'] . ",`nickName`=" . $_SESSION['uid'] . ",`gaurdian_id`=" . $_SESSION['uid'] . ",`sex`=" . $_SESSION['uid'] . ",`DOB`=" . $_SESSION['uid'] . ",`permenent_address` = " . $_SESSION['uid'] . ",`telephone_no1`=" . $_SESSION['uid'] . ",`telephone_no2` =" . $_SESSION['uid'] . ",`email_id`=" . $_SESSION['uid'] . " WHERE `index` =" . $_SESSION['pid'];
    $result = mysql_query($query, $dbc);
    $error1 = mysql_error();
    $result2 = mysql_query($query2, $timestampLink);
    $error2 = mysql_error();
    $result3 = mysql_query($query3, $uidLink);
    $error3 = mysql_error();
    if ($error1 == null) {
        $photo_id = $_POST['photoID'];
        if (copy('../../tmp/' . $photo_id, '../../userFiles/' . $_SESSION['username'] . '/' . $photo_id)) {
            $_SESSION['userPic'] = $photo_id;
            unlink('../../tmp/' . $photo_id);
        }
        echo "<status>success</status>";
    } else {
        echo "<status>" . $error1 . "</status>";
    }
} else {
    echo "<status>" . $error_form . "</status>";
}
echo '</editprofile>';
mysql_close();
?>