<?php
include 'authorize.php';
if ($_SESSION['adminLevel']==0) {
    include_once 'db_login.php';
    require_once 'inc.php';
    $postLength = count($_POST);
    $content_count = $postLength - 1;
    $no_of_subs = ($content_count) / 3;
    $table = $_POST[tableName];
    $sub_string = "";
    $no_of_subjects = count($_POST) / 3;
    $query = "create table " . $marksTable . "(index int(3) unique auto_increment, regd_no varchar(10) not null primary key, " . $sub_string . "total int(4))engine innodb";
    $result = mysql_query($query,$dbc);
    $error1=mysql_error($dbc);
    if (!$error1) {
        
        $result = mysql_query($query);
        if ($result)
            echo "true";
    }
}
mysql_close();
?>