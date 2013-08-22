<?php
    $db_host="localhost";
    $db_user="collegedb2Reader";
    $db_password="123BlackBox";
    $db_database="collegeDB2";
    $dbc=mysql_connect($db_host, $db_user, $db_password);
    mysql_select_db($db_database,$dbc);
?>