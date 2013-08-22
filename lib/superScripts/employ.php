<?php
/* Author: Gowtham */
//initiates php n mysql sessions
include 'authorize.php';
include 'db_login.php';
//code goes here
$table=$_POST['table'];
$id=$_POST['id'];
$query="INSERT INTO ".$table."(`rootUID`,`id`,`passKey`) VALUES(".$_SESSION['uid'].",'".$id."','".uniqid()."')";
$query2="INSERT INTO ".$table."(`id`,`passKey`) VALUES(now(),now())";
$query3="INSERT INTO ".$table."(`id`,`passKey`) VALUES(".$_SESSION['uid'].",".$_SESSION['uid'].")";
$result=mysql_query($query);
$error1=mysql_error();
$resultts=mysql_query($query2,$timestampLink);
$errorts=mysql_error();
$resultu=mysql_query($query2,$uidLink);
$erroru=mysql_error();
$query="INSERT INTO objectTable(`id`,`refTable`) VALUES('".$id."','".$table."')";
$queryts="INSERT INTO objectTable(`id`,`refTable`) VALUES(now(),now())";
$queryu="INSERT INTO objectTable(`id`,`refTable`) VALUES(".$_SESSION['uid'].",".$_SESSION['uid'].")";
$result=mysql_query($query);
$error2=mysql_error();
$resultts=mysql_query($queryts,$timestampLink);
$errorts=mysql_error();
$resultu=mysql_query($queryu,$uidLink);
$erroru=mysql_error();
if(!$error1 and !$error2){
    echo "success";
}  else {
    echo $error1.$error2;
}
//closes mysql connection
mysql_close();
?>
