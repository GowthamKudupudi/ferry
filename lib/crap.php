<?php
$k = 'bye';
echo shell_exec('php -r "echo \'hi\'.$k"');

/*
$rIA = explode('@,$', sqlinjection_free($_POST['rowIndex']));
$cHA = explode('@,$', sqlinjection_free($_POST['cellHash']));
$colIndexStr = sqlinjection_free($_POST['colIndex']);
$vA = explode('@,$', sqlinjection_free($_POST['value']));
$cIA = explode(',', $colIndexStr);
$rc = count($rIA);
for ($i = 0; $i < count($cIA); $i++) {
$ts[] = 'now()';
$us[] = "'" . $_SESSION['oid'] . "'";
}
$ts = implode(",", $ts);
$us = implode(",", $us);
for ($i = 0; $i < $rc; $i++) {
echo "<row>";
    $value = $vA[$i];
    $val = explode(',', $value);
    $rowIndex = $rIA[$i];
    $cellHashOk = FALSE;
    $cHR = explode(',', $cHA[$i]);
    $cc = count($cIA);
    $upv = array();
    $upt = array();
    $upu = array();
    $upcl = array();
    for ($j = 0; $j < $cc; $j++) {
    $chchk = ($_SESSION['tables'][$dbTable][$cIA[$j]][$rowIndex]['hash'] and $cHR[$j] == $_SESSION['tables'][$dbTable][$cIA[$j]][$rowIndex]['hash']);
    if ($cHR[$j] and $_SESSION['tables'][$dbTable][$cIA[$j]][$rowIndex]['hash'] and !$chchk) {
    $_SESSION['tables'][$dbTable][$cIA[$j]][$rowIndex]['hash'] = rand();
    }
    $cellHashOk = ($cellHashOk or $chchk);
    $qval[$j] = $val[$j] ? "'" . $val[$j] . "'" : "null";
    if ($chchk or $authorizeTable) {
    $upv[] = $cIA[$j] . "=" . $qval[$j];
    $upt[] = $cIA[$j] . "=now()";
    $upu[] = $cIA[$j] . "='" . $_SESSION['oid'] . "'";
    $upcl[] = $cIA[$j];
    }
    }
    $ups = implode(',', $upv);
    $upts[$i] = implode(',', $upt);
    $upus[$i] = implode(',', $upu);
    if ($rIA[$i] == 'newRow') {
    if ($authorizeTable) {
    $value = implode(",", $qval);
    $query = "INSERT INTO `" . $dbTable . "`(" . $colIndexStr . ") VALUES(" . $value . ")";
    $result = mysql_query($query, $dbc);
    $error2[$i] = mysql_error($dbc);
    if (!$error2[$i]) {
    $nrIndex[$i] = mysql_insert_id($dbc);
    echo "<status>success</status>";
    echo "<newRowIndex>" . $nrIndex[$i] . "</newRowIndex>";
    echo "<value>" . implode(',', $val) . "</value>";
    echo '<hashes>';
        if (!$_SESSION['tables'][$dbTable]['fc']) {
        foreach ($_SESSION['tables'][$dbTable] as $col => $val) {
        $rand = rand();
        $_SESSION['tables'][$dbTable][$col][$nrIndex[$i]]['hash'] = $rand;
        $_SESSION['tables'][$dbTable][$col][$nrIndex[$i]]['value'] = $qval[$col];
        echo '<' . $col . '>' . $rand . '</' . $col . '>';
        }
        }
        echo '</hashes>';
    } else {
    echo "<status>" . $error2[$i] . "</status>";
    }
    } else {
    echo '<status>u r not authorized to edit the table ~%|~</status>';
    }
    } elseif ($authorizeTable or $cellHashOk) {
    $query = "update " . $dbTable . " set " . $ups . " where `index`='" . $rowIndex . "'";
    $result = mysql_query($query, $dbc);
    $error3[$i] = mysql_error($dbc);
    if (!$error3[$i]) {
    echo "<status>success</status>";
    echo "<newRowIndex>" . $rowIndex . "</newRowIndex>";
    echo "<cols>" . implode(",", $upcl) . "</cols>";
    foreach ($cIA as $j => $val) {
    $_SESSION['tables'][$dbTable][$val][$rowIndex]['value'] = $qval[$j];
    }
    } else {
    echo "<status>" . $error3[$i] . "</status>";
    }
    } else {
    echo '<status>u r not authorized to edit the cell(s) in row ~warn~</status>';
    echo "<newRowIndex>" . $rowIndex . "</newRowIndex>";
    }
    echo "</row>";
}
*/
?>