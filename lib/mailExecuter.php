<?php
/* Author: Gowtham */
require 'authorize.php';
$mailOp=$_POST['mailOp'];
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo "<mailExecuter>";
switch($mailOp){
    case "sendMail":
        $to=$_POST['to'];
        $sub=htmlentities($_POST['sub']);
        $msg=htmlentities($_POST['msg']);
        $mail=mail($to, $sub, $msg,"From: ".$_SESSION['username']."@ferryfair.com\r\nContent-type: text/plain; charset='utf-8'\r\nContent-Transfer-Encoding: 8bit\r\nX-Mailer: PHP/".  phpversion(),'-f'.$_SESSION['username']."@ferryfair.com");
        if($mail){
            echo "<status>success</status>";
        }else{
            echo "<status>failed</status>";
        }
        break;
    default :
        echo "<status>No service for this mail operation</status>";
        break;
}
echo "</mailExecuter>";
?>
