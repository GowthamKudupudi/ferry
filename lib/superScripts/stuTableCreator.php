<?php
include 'authorize.php';
if ($_SESSION['adminLevel'] == 0) {
    include_once 'db_login.php';
    require_once '../inc.php';
    $startRegNo = $_POST['startRegNo'];
    $endRegNo = $_POST['endRegNo'];
    $tableName=substr($startRegNo, 0,8);
    $stuList[] = $startRegNo;
    $tens[]=null;
    for ($i = 0; $i < 16; $i++) {
        switch ($i) {
            case 10 : $k = 'A';
                break;
            case 11 : $k = 'B';
                break;
            case 12 : $k = 'C';
                break;
            case 13 : $k = 'D';
                break;
            case 14 : $k = 'E';
                break;
            case 15 : $k = 'F';
                break;
            default : $k = $i;
                break;
        }
        $tens[$i] = $k;
    }
    $i = 0;
    $k = 0;
    $stuList[0]=$startRegNo;
    while ($stuList[$i] != $endRegNo) {
        $i++;
        $j = ($i+1) % 10;
        $stuList[$i] = substr($startRegNo, 0, 8) . $tens[$k] . $j;
        if ($j == 9)
            $k = (++$k) % 16;
    }
    $query = "create table " . $tableName . "(`index` int(3) unique auto_increment, rootUID int(16) not null, timeStamp timestamp not null default current_timestamp, id varchar(10) not null primary key, uid int(16), passKey varchar(13) null)engine innodb";
    $result = mysql_query($query);
    $error1 = mysql_error();
    if ($error1 and !preg_match("/Table '.{3,16}' already exists/", $error1)) {
        echo $error1;
    } else {
        foreach($stuList as $value){
            $query="INSERT INTO ". $tableName." (`rootUID`,`id`,`passKey`) values(".$_SESSION['uid'].",'$value','".uniqid()."')";
            $result=mysql_query($query);
            $error2=mysql_error();
            $query="INSERT INTO objectTable (`rootUID`,`id`,`refTable`,`type`) values(".$_SESSION['uid'].",'$value','".$tableName."','STUDENT')";
            $result=mysql_query($query);
            $error3=mysql_error();
            if($error2 and !preg_match("/Duplicate entry '.{4,16}' for key '.{2,16}'/", $error3) and !preg_match("/Duplicate entry '.{4,16}' for key '.{2,16}'/", $error2)){
                die($error2);
            }
        }
        $query="INSERT INTO adminTable (`rootUID`,`table`,`adminLevel`) values(".$_SESSION['uid'].",'".$tableName."','".$_SESSION['adminLevel']."')";
        $result=mysql_query($query);
        $error4=mysql_error();
        if(!$error4 or preg_match("/Duplicate entry '.{3,16}' for key 'table'/", $error4))echo 'success';else echo $error4;
    }
}
mysql_close();
?>