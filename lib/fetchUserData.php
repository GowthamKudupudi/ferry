<?php
/* Author: Gowtham */
//2012-05-04 09:48:00
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require 'authorize.php';
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<userdata>';
$qData=  explode(",", $_POST['query']);
$df=$root."/userFiles/".$_SESSION['username']."/data.json";
$fp=  fopen($df, 'a+');
$fd=  fread($fp, filesize($df));
$df=  json_decode($fd);
for($i=0;$i<count($qData);$i++){
    echo "<".strtolower($qData[$i]).">".htmlentities($df->$qData[$i])."</".strtolower($qData[$i]).">";
}
fclose($fp);
echo '<status>success</status></userdata>';
?>
