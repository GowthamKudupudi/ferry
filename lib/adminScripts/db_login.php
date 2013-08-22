<?php
    $db_host="mysql.newmeksolutions.com";
    $db_user="cdbadmin";
    $ts_user="colldb2tsWriter";
    $u_user="colldb2uWriter";
    $adb_pass="1729TornShoes";
    $db_password="123GreenGuys";
    $db_database="collegedb2admin";
    $ts_database="collegedb2timestamp";
    $u_database="collegedb2uid";
    $dbc=mysql_connect($db_host, $db_user, $adb_pass, TRUE);
    $sd=mysql_select_db($db_database,$dbc);
    $timestampLink=mysql_connect($db_host, $ts_user, $db_password, TRUE);
    $sd=mysql_select_db($ts_database, $timestampLink);
    $uidLink=mysql_connect($db_host, $u_user, $db_password, TRUE);
    $sd = mysql_select_db($u_database, $uidLink);
?>