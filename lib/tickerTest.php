<?php
/* Author: Gowtham */
header('Content-Type: application/octet-stream');
$i=0;
while($i<20){
    $i++;
    echo $i.',';
    ob_end_flush(); 
    ob_flush(); 
    flush(); 
    ob_start();
    sleep(2);
}
?>
