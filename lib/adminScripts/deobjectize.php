<?php

/* Author: Gowtham */
require 'authorize.php';
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<?xml version="1.0" encoding="UTF-8"?><deobjectize>';
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/lib/inc.php";
if (authorizeTransit($_SESSION['adminLevel'], 'Zz0')) {
    require 'db_login.php';
    $objectId = sqlinjection_free($_POST['objectId']);
    $query = "SELECT * FROM `objectTable` WHERE `id`='" . $objectId . "'";
    $or = mysql_query($query, $dbc);
    $dberr.=mysql_error($dbc);
    if (!$dberr and $or) {
        $uid = mysql_result($or, 0, 'uid');
        $aL = mysql_result($or, 0, 'adminLevel');
        $oid = mysql_result($or, 0, 'index');
        $query = "SELECT * FROM `users` WHERE `index`=" . $uid;
        $ur = mysql_query($query, $dbc);
        $dberr.=mysql_error($dbc);
        if (!$dberr and $ur) {
            $uAL = mysql_result($ur, 0, 'adminLevel');
            $i = 0;
            while ($uAL[$i] != NULL) {
                if ($uAL[$i] == $aL[0]) {
                    $i++;
                    while (!preg_match("/[A-Z]/", $uAL[$i]) and $uAL[$i] != NULL) {
                        if ($uAL[$i] == $aL[1] and $uAL[$i + 1] == $aL[2]) {
                            $uAL = stringReplace($uAL, '', $i, $i + 1);
                            $ualch = true;
                            break;
                        } else {
                            $i+=2;
                        }
                    }
                } else {
                    $i++;
                }
            }
            $query = "UPDATE `users` SET `adminLevel`='" . $uAL . "' where `index`='" . $uid . "'";
            $uur = mysql_query($query, $dbc);
            $dberr.=mysql_error($dbc);
            $query = "UPDATE `objectTable` SET `passKey`='" . generatePassword(16, 8) . "' WHERE `index`='" . $oid . "'";
            $uopr = mysql_query($query, $dbc);
            $dberr.=mysql_error($dbc);
            if (!$dberr and $uur and $uopr) {
                echo '<status>success</status>';
            } else {
                echo '<status>Failed to deobjectize ~:|~ please inform administrator@'.$_SESSION['orgDomain'].' immediately.</status>';
            }
        } else {
            echo '<status>' . $dberr . '-Failed to retrieve uid ~:|~</status>';
        }
    } else {
        echo '<status>' . $dberr . '-Failed to read object ~:|~</status>';
    }
    if (!$dberr and $uur and $uopr) {
        $queryts = "UPDATE `users` SET `adminLevel`=now() where `index`='" . $uid . "'";
        $resultts = mysql_query($queryts, $timestampLink);
        $errorts = mysql_error($timestampLink);
        $queryu = "UPDATE `users` SET `adminLevel`='" . $_SESSION['oid'] . "' where `index`='" . $uid . "'";
        $resultu = mysql_query($queryu, $uidLink);
        $erroru = mysql_error($uidLink);
        $queryts = "UPDATE `objectTable` SET `passKey`=now() WHERE `index`='" . $oid . "'";
        $resultts = mysql_query($queryts, $timestampLink);
        $errorts = mysql_error($timestampLink);
        $queryts = "UPDATE `objectTable` SET `passKey`='" . $_SESSION['oid'] . " WHERE `index`='" . $oid . "'";
        $resultu = mysql_query($queryu, $uidLink);
        $erroru = mysql_error($uidLink);
    }
    include 'db_logout.php';
} else {
    echo '<status>u r not authorized to use this tool ~X(~</status>';
}
echo '</deobjectize>';
?>
