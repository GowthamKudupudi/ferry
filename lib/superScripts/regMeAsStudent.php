<?php
/* Author: Gowtham */
include 'authorize.php';
require_once '../inc.php';
include_once 'db_Login.php';
$regNo=sqlinjection_free($_POST['regNo']);
$passKey=sqlinjection_free($_POST['passKey']);
$tableStr=substr($regNo, 0, 8);
$query="SELECT `adminLevel`,`table` FROM `adminTable` WHERE `table`='".$tableStr."'";
$result=mysql_query($query);
$error=mysql_error();
$tableAdminLevel=mysql_result($result, 0, 'adminLevel');
if($tableAdminLevel){
$table=mysql_result($result, 0, 'table');
$query="select `passKey` from `".$table."` where `id`='".$regNo."'";
$result=mysql_query($query);
$error=mysql_error();
$cPassKey=mysql_result($result, 0, 'passKey');
$match=FALSE;
if($passKey and $passKey==$cPassKey){
    $userAdminLevel=$_SESSION['adminLevel'];
    $preUserAdminLevel=$userAdminLevel;
    $i=0;
    while($i<strlen($userAdminLevel) and $userAdminLevel){
        if($userAdminLevel[$i]=='A'){
            $i++;
            while(!preg_match('/[A-Z]/', $userAdminLevel[$i]) and $i<strlen($userAdminLevel)){
                if($userAdminLevel[$i]=='a'){
                    $i++;
                    if($userAdminLevel[$i]=='7'){
                        $match=true;
                    }
                    $i++;
                }  else {
                    $i++;
                }
            }if((preg_match('/[A-Z]/', $userAdminLevel[$i]) or $i==strlen($userAdminLevel))and !$match){
                $userAdminLevel=stringInsert($userAdminLevel, 'a7', $i);
                $i+=2;
                $match=TRUE;
            }
        }else{
            $i++;
        }
    }
    if(!$match){
        $userAdminLevel.='Aa7';
    }
    $query="UPDATE `users` SET `adminLevel`='$userAdminLevel' WHERE `username`='".$_SESSION['username']."'";
    $result=mysql_query($query);
    $error1=mysql_error();
    if($error1){
        echo $error1;
    }  else {
        $_SESSION['adminLevel']=$userAdminLevel;
        $query="UPDATE `".$table."` SET `passKEY`='REGISTERED', `uid`=".$_SESSION['uid']." WHERE `passKey`='".$passKey."'";
        $resut=mysql_query($query);
        $error2=mysql_error();
        if($error2){
            $query="UPDATE `users` SET `adminLevel`='$preUserAdminLevel' WHERE `username`='".$_SESSION['username']."'";
            $result=mysql_query($query);
            $_SESSION['adminLevel']=$preUserAdminLevel;
            echo $error2;
        }  else {
            echo "success";
        }
    }
}else echo "Invalid PassKey.";
}else {
    echo "insufficient admin level.";
}
mysql_close();
?>
