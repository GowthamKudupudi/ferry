<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$mkdir = mkdir('../userFiles/test');
$fhandler = fopen('../userFiles/test/live.shm', 'w');
fclose($fhandler);
?>
