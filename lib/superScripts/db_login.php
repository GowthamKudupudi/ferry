<?php
    $db_host="db.ferryfair.com";
    $db_user="collegedb2Writer";
    $ts_user="colldb2tsWriter";
    $u_user="colldb2uWriter";
    $db_password="123GreenGuys";
    $db_database="collegedb2";
    $ts_database="collegedb2timestamp";
    $u_database="collegedb2uid";
    $dbc=mysql_connect($db_host, $db_user, $db_password, TRUE);
    $sd=mysql_select_db($db_database,$dbc);
    $timestampLink=mysql_connect($db_host, $ts_user, $db_password, TRUE);
    $sd=mysql_select_db($ts_database, $timestampLink);
    $uidLink=mysql_connect($db_host, $u_user, $db_password, TRUE);
    $sd = mysql_select_db($u_database, $uidLink);
?>