<?php
/* Author: Gowtham */
session_start();
include 'db_login.php';
include 'inc.php';
$_SESSION['adminLevel'] = $_GET['adminLevel'];
$_SESSION['uid'] = $_GET['uid'];
$comment = $_GET['comment'];
$rORw = $_GET['rORw'];
if ($rORw and $comment) {
    $mems = authField($comment, $rORw);
    echo '<div>';
    for ($i = 0; $i <= $mems['r']['index']; $i++) {
        echo 'Readers: ';
        echo '<br/><tab/>Users: ' . implode(',', $mems['r']['users'][$i]);
        echo '<br/><tab/>rows: ' . implode(',', $mems['r']['rows'][$i]);
        echo '<br/><tab/>Objects: ' . implode(',', $mems['r']['objects'][$i]);
        echo '<br/><tab/>GID: ' . $mems['r']['gid'][$i];
        echo '<br/><tab/>AL: ' . $mems['r']['aL'][$i];
    }
    for ($i = 0; $i <= $mems['w']['index']; $i++) {
        echo 'Writers: ';
        echo '<br/><tab/>Users: ' . implode(',', $mems['w']['users'][$i]);
        echo '<br/><tab/>rows: ' . implode(',', $mems['w']['rows'][$i]);
        echo '<br/><tab/>Objects: ' . implode(',', $mems['w']['objects'][$i]);
        echo '<br/><tab/>GID: ' . $mems['w']['gid'][$i];
        echo '<br/><tab/>AL: ' . $mems['w']['aL'][$i];
    }
    echo '<br/>authRows: ' . implode(',', $mems[$rORw]['authRows']);
    echo '</div>';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            authRowTester
        </title>
    </head>
    <body>
        <form method="GET" action="authRowTester.php">
            <label>uid:</label><input id="uid" name="uid"/>
            <label>adminLevel:</label><input id="adminLevel" name="adminLevel" type="text"/><br/>
            <label>comment:</label><input id="comment" name="comment" type="text"/><br/>
            <label>rORw:</label><input id='rORw' name="rORw" type="text"/><br/>
            <input type="submit" value="submit"/>
        </form>
    </body>
</html>