<?php

/* Author: Gowtham */
include 'authorize.php';
require '../inc.php';
require 'db_login.php';
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<objectize>';
$id = $_POST['id'];
$passKey = $_POST['passKey'];
$query = "SELECT * FROM `objectTable` WHERE `id`='" . $id . "'";
$resul = mysql_query($query, $dbc);
$error = mysql_error($dbc);
$cPassKey = mysql_result($resul, 0, 'passKey');
$newPos = mysql_result($resul, 0, 'type2');
$oid = mysql_result($resul, 0, 'index');
$match = FALSE;
if ($passKey and $passKey == $cPassKey) {
    $userAdminLevel = $_SESSION['adminLevel'];
    $preUserAdminLevel = $userAdminLevel;
    $oALvl = mysql_result($resul, 0, 'adminLevel');
    $i = 0;
    while ($i < strlen($userAdminLevel) and $userAdminLevel) {
        if ($userAdminLevel[$i] == $oALvl[0]) {
            $i++;
            $match = TRUE;
            while ($userAdminLevel[$i] != $oALvl[1] and !preg_match('/[A-Z]/', $userAdminLevel[$i]) and $i < strlen($userAdminLevel)) {
                $i+=2;
            }
            if ($userAdminLevel[$i] != $oALvl[1]) {
                $userAdminLevel = stringInsert($userAdminLevel, $oALvl[1] . $oALvl[2], $i + 2);
                $i+=2;
            } else {
                if ($userAdminLevel[$i + 1] > $oALvl[2]) {
                    $userAdminLevel = stringInsert($userAdminLevel, $oALvl[1] . $oALvl[2], $i + 2);
                }
                $i+=2;
            }
            while (!preg_match('/[A-Z]/', $userAdminLevel[$i]) and $i < strlen($userAdminLevel)) {
                $i++;
            }
        } else {
            $i++;
        }
    }
    if (!$match) {
        $userAdminLevel.=$oALvl;
    }
    $query = "UPDATE `users` SET `adminLevel`='" . $userAdminLevel . "' WHERE `index`='" . $_SESSION['uid'] . "'";
    $result = mysql_query($query, $dbc);
    $error1 = mysql_error($dbc);
    $npk = generatePassword(16, 8);
    $query = "UPDATE `objectTable` SET `uid`='" . $_SESSION['uid'] . "', `passKey`='" . $npk . "' WHERE `index`='" . $oid . "'";
    $result = mysql_query($query, $dbc);
    $error1.=mysql_error($dbc);
    if (!$error1) {
        $_SESSION['adminLevel'] = $userAdminLevel;
        echo '<status>success</status>';
        $_SESSION['function'][$oid]['label'] = mysql_result($resul, 0, 'type2');
        $_SESSION['function'][$oid]['func'] = mysql_result($resul, 0, 'function');
        $_SESSION['function'][$oid]['id'] = $id;
        $_SESSION['function'][$oid]['aL'] = mysql_result($resul, 0, 'adminLevel');
        $objXML = new DOMDocument();
        $ol = $objXML->load($_SERVER['DOCUMENT_ROOT'] . '/objProperty/' . $id . '.xml');
        $una = $objXML->getElementsByTagName('uid');
        $una = $una->item(0);
        $o = $objXML->getElementsByTagName('object');
        $o = $o->item(0);
        $tn = $objXML->createElement('uid', $_SESSION['uid']);
        $replaceChild = $o->replaceChild($tn, $una);
        $save = $objXML->save($_SERVER['DOCUMENT_ROOT'] . '/objProperty/' . $id . '.xml');
        echo '<newpos><label>' . $newPos . '</label><func>' . $_SESSION['function'][$oid]['id'] . '</func><al>' . $_SESSION['function'][$oid]['aL'] . '</al><oid>' . $oid . '</oid><id>' . $id . '</id></newpos>';
    } else {
        echo '<status>' . $error1 . '</status>';
    }
} else {
    echo "<status>Invalid PassKey ~:|~</status>";
}
if ($passKey and $passKey == $cPassKey) {
    $queryts = "UPDATE `users` SET `adminLevel`=now() WHERE `index`='" . $_SESSION['uid'] . "'";
    $resultts = mysql_query($queryts, $timestampLink);
    $errorts.= mysql_error($timestampLink);
    $queryu = "UPDATE `users` SET `adminLevel`='" . $_SESSION['uid'] . "' WHERE `index`='" . $_SESSION['uid'] . "'";
    $resultu = mysql_query($queryu, $uidLink);
    $erroru.= mysql_error($uidLink);
    $queryts = "UPDATE `objectTable` SET `uid`=now(), `passKey`=now() WHERE `index`='" . $oid . "'";
    $resultts = mysql_query($queryts, $timestampLink);
    $errorts.= mysql_error($timestampLink);
    $queryu = "UPDATE `objectTable` SET `uid`='" . $_SESSION['uid'] . "', `passKey`='" . $_SESSION['uid'] . "' WHERE `index`='" . $oid . "'";
    $resultu = mysql_query($queryu, $uidLink);
    $erroru.= mysql_error($uidLink);
}
echo '</objectize>';
mysql_close($dbc);
?>
