<?php

/* Author: Gowtham */
//initiates php n mysql sessions
include 'authorize.php';
include 'db_login.php';
//code goes here
$query="SELECT * FROM `users`";
$result=mysql_query($query, $dbc);
$urowCount=mysql_num_rows($result);
for($i=0;$i<$rowCount;$i++){
    $username=mysql_result($result, $i, 'username');
}
//closes mysql connection
mysql_close();
?>
