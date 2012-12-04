<?php
include 'authorize.php';
if ($_SESSION['adminLevel']==0) {
    include_once 'db_login.php';
    require_once 'inc.php';
    $postLength = count($_POST);
    $content_count = $postLength - 1;
    $no_of_subs = ($content_count) / 3;
    $marksTable = $_POST[tableName];
    $sub_string = "";
    for ($i = 0; $i < $no_of_subs; $i++) {
        $sub_string.=sqlinjection_free($_POST[$i]) . '_int int(3), ';
        $sub_string.=sqlinjection_free($_POST[$i]) . '_ext int(3), ';
    }
    $no_of_subjects = count($_POST) / 3;
    $query = "create table " . $marksTable . "(index int(3) unique auto_increment, regd_no varchar(10) not null primary key, " . $sub_string . "total int(4))engine innodb";
    $result = mysql_query($query);
    $maxMarkStrng = "";
    $sub_string = "";
    if ($result) {
        for ($i = 0; $i < $no_of_subs; $i++) {
            $sub_string.="`" . sqlinjection_free($_POST[$i]) . "_int`,";
            $sub_string.="`" . sqlinjection_free($_POST[$i]) . "_ext`,";
        }
        for ($i = $no_of_subs; $i < $content_count; $i++) {
            $maxMarkStrng.="'" . sqlinjection_free($_POST[$i]) . "',";
        }
        $query = "insert into " . $marksTable . "(`id`,`UID`,`timeStamp`,`regd_no`," . $sub_string . "`total`) values(null,'".$_SESSION['uid']."',null,'maxMarks'," . $maxMarkStrng . "null)";
        $result = mysql_query($query);
        if ($result)
            echo "true";
    }
}
mysql_close();
?>