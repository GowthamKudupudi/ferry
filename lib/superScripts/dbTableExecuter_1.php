<?php

/* Author: Gowtham */
include 'authorize.php';
include_once 'db_login.php';
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include_once "$root/lib/inc.php";
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);

function updateECells($dbTable, $cid, $rid, $value) {
    $sCs = $_SESSION['tables'][$dbTable][$col][$nrIndex[$i]]['dCs'];
    foreach ($sCs as $dC) {
        $dC = explode(",", $dC);
        $formula = $_SESSION['tables'][$dbTable][$dC[0]]['tHR']['f']['f'];
        if ($formula) {
            foreach ($_SESSION['tables'][$dbTable] as $colName => $c) {
                $ofs = 0;
                $strpos = strpos($formula, $colName, $ofs);
                while ($strpos > -1) {
                    $ofs = $strpos + strlen($colName);
                    if ($formula[$ofs] == '(') {
                        while ($formula[$ofs] != ')' and $formula[$ofs] != null) {
                            $r.=$formula[++$ofs];
                        }
                    }
                }
            }
        }
    }
}

function computeSourceCells($formula, $dbTable, $rid) {
    $fx = $formula;
    $sCs = array();
    foreach ($_SESSION['tables'][$dbTable] as $colName => $column) {
        $start = 0;
        $start = strpos($fx, $colName, $start);
        while ($start > -1 and (preg_match("/[^a-zA-Z0-9_]/", $fx[$start - 1]) or !$fx[$start - 1])) {
            $pst = $start;
            $start += strlen($colName);
            if ($fx[$pst - 1] != ';' and $fx[$start] != '=' and (preg_match("/[^a-zA-Z0-9_]/", $fx[$start]) or !$fx[$start])) {
                $j = $start + 1;
                $row = '';
                while ($fx[$start] == '(' and $fx[$j] != '' and $fx[$j] != ')') {
                    $row.=$fx[$j++];
                }
                if ($row == '')
                    $row = $rid;
                array_push(&$sCs, $colName . "," . $row);
            }
        }
    }
    $sCs = array_unique($sCs);
    return $sCs;
}

echo '<?xml version="1.0" encoding="UTF-8"?><dbTableExecuter>';
if (domesticSlave($_SESSION['adminLevel'], 'Zz8')) {
    $dbTable = sqlinjection_free($_POST['dbTable']);
    $dbTable = strtolower($dbTable);
    $dbtKey = ftok("$root/dbTableData/$dbTable", 'c');
    $dbtSemId = sem_get($dbtKey);
    $dbtShmId = shm_attach($dbtKey);
    $i = 0;
    $sa = sem_acquire($dbtSemId);
    $liveDBTable = shm_get_var($dbtShmId, $dbtKey);
    $authorizeTable = $_SESSION['tables'][$dbTable]['fc'] ? TRUE : FALSE;
    if ($authorizeTable) {
        if ($_SESSION['tables'][$dbTable]['adminTable']) {
            require '../adminScripts/db_login.php';
        }
    }
    $tableOps = explode("$,$", $_POST['tableOperation']);
    for ($toc = 0; $toc < count($tableOps); $toc++) {
        $tableOperation = $tableOps[$toc];
        echo "<" . $tableOperation . ">";
        switch ($tableOperation) {
            case 'updateFormula':
                $ci = $_POST['fColIndex'];
                $ri = $_POST['fRowIndex'];
                $fsKey = $_POST['fsKey'];
                if ($authorizeTable or ($_SESSION['tables'][$dbTable][$ci][$ri]['sKey'] and $_SESSION['tables'][$dbTable][$ci][$ri]['sKey'] == $fsKey)) {
                    $f = $_POST['formula'];
                    if (strlen($f) < 1000) {
                        $sCs = computeSourceCells($f, $dbTable, $ri);
                        $fn = "$root/dbTableData/$dbTable";
                        $fp = fopen($fn, 'a+');
                        $tp = fread($fp, filesize($fn));
                        fclose($fp);
                        $tp = json_decode($tp, TRUE);
                        unset($tp[$ri][$ci]['style']['oid']);
                        unset($tp[$ri][$ci]['style']['ts']);
                        foreach ($tp[$ri][$ci] as $prop => $value) {
                            $sua[$ri][$ci]['style'][$prop] = $value;
                        }
                        $tp[$ri][$ci]['style']['oid'] = $_SESSION['oid'];
                        $tp[$ri][$ci]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                        $tp[$ri][$ci]['f']['f'] = $f;
                        $pSCs = $tp[$ri][$ci]['f']['sCs'];
                        $tp[$ri][$ci]['f']['sCs'] = $sCs;
                        $tp[$ri][$ci]['f']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                        $tp[$ri][$ci]['f']['oid'] = $_SESSION['oid'];
                        $sciri = $ci . "," . $ri;
                        for ($i = 0; $i < count($pSCs); $i++) {
                            $c = explode(",", $pSCs);
                            foreach ($tp[$c[1]][$c[0]]['f']['ee'] as $e => $ee) {
                                if ($ee == $sciri) {
                                    unset($tp[$c[1]][$c[0]]['f']['ee'][$e]);
                                    break;
                                }
                            }
                        }
                        for ($i = 0; $i < count($sCs); $i++) {
                            $c = explode(",", $sCs[$i]);
                            $tp[$c[1]][$c[0]]['f']['ee'][] = $sciri;
                            $tp[$c[1]][$c[0]]['f']['ee'] = array_unique($tp[$c[1]][$c[0]]['f']['ee']);
                        }
                        $sua = array();
                        if ($ri == 'tHR') {
                            foreach ($tp as $rid => $row) {
                                if (count($tp[$rid][$ci]['style']) > 2) {
                                    unset($tp[$rid][$ci]['style']['ts']);
                                    unset($tp[$rid][$ci]['style']['oid']);
                                    foreach ($tp[$rid][$ci]['style'] as $prop => $value) {
                                        $sua[$rid][$ci]['style'][$prop] = $value;
                                    }
                                    $tp[$rid][$ci]['style'] = array();
                                    $tp[$rid][$ci]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                                    $tp[$rid][$ci]['style']['oid'] = $_SESSION['oid'];
                                }
                            }
                        }
                        if ($ci == 'index') {
                            $row = $tp[$ri];
                            foreach ($row as $cid => $cell) {
                                if (count($tp[$ri][$cid]['style']) > 2) {
                                    unset($tp[$ri][$cid]['style']['ts']);
                                    unset($tp[$ri][$cid]['style']['oid']);
                                    foreach ($tp[$ri][$cid]['style'] as $prop => $value) {
                                        $tp[$ri][$cid]['style'][$prop] = $value;
                                    }
                                    $tp[$ri][$cid]['style'] = array();
                                    $tp[$ri][$cid]['style']['oid'] = $_SESSION['oid'];
                                    $tp[$ri][$cid]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                                }
                            }
                        }
                        $sua[$ri][$ci]['style'] = array();
                        if ($tp != 'null') {
                            $tpf[$dbTable] = $tp;
                            echo "<status>success</status>";
                            echo "<sCs>" . implode("@,$", $sCs) . "</sCs>";
                        } else {
                            unset($tp);
                            echo "<status>unknown error ~:|~</status>";
                            for ($i = 0; count($tableOps); $i++) {
                                if ($tableOps[$i] == 'updateCell') {
                                    unset($tableOps[$i]);
                                    break;
                                }
                            }
                        }
                    } else {
                        echo '<status>' . 'Ur formula exceeded 999 characters ~&amp;|~' . '</status>';
                    }
                } else {
                    echo '<status>U r not authorized to update the formula ~:|~</status>';
                }
                break;
            case 'renamecolName':
                if ($authorizeTable) {
                    $colName = sqlinjection_free($_POST['colName']);
                    $newName = sqlinjection_free($_POST['newName']);
                    $type = sqlinjection_free($_POST['type']);
                    $size = stripslashes(sqlinjection_free($_POST['size']));
                    $size = $size ? " (" . $size . ")" : "";
                    $notNull = sqlinjection_free($_POST['notNull']);
                    $notNull = ($notNull == 'true') ? ' NOT NULL' : ' NULL';
                    $default = sqlinjection_free($_POST['dfault']);
                    $default = $default ? " DEFAULT '" . $default . "'" : '';
                    $query = "SHOW FULL COLUMNS FROM `" . $dbTable . "` WHERE Field='" . $colName . "'";
                    $result = mysql_query($query, $dbc);
                    $comment = mysql_result($result, 0, 'Comment');
                    $comment = ($comment) ? " COMMENT '" . $comment . "'" : '';
                    $query = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $newName . "` " . $type . $size . $notNull . $comment . $default;
                    $result = mysql_query($query, $dbc);
                    $error1 = mysql_error($dbc);
                    if (!$error1) {
                        if ($newName != $colName) {
                            $_SESSION['tables'][$dbTable][$newName] = $_SESSION['tables'][$dbTable][$colName];
                            unset($_SESSION['tables'][$dbTable][$colName]);
                        }
                        echo '<status>success</status>';
                    } else {
                        echo '<status>' . $error1 . '</status>';
                    }
                } else {
                    echo '<status>U r not authorized to edit table.</status>';
                }
                break;
            case 'renametableName':
                if ($authorizeTable) {
                    $newName = $_POST['newName'];
                    $query = "RENAME TABLE  `" . $dbTable . "` TO  `" . $newName . "` ";
                    $result = mysql_query($query, $dbc);
                    $error1 = mysql_error($dbc);
                    if (!$error1) {
                        /* $query = "UPDATE `adminTable` set `table`='" . $newName . "' where `table`='" . $dbTable . "'";
                          $result = mysql_query($query, $dbc);
                          $error2 = mysql_error($dbc);
                          $query = "SELECT `index` FROM `adminTable` WHERE `table`='" . $newName . "'";
                          $result = mysql_query($query, $dbc);
                          $tIndex = mysql_result($result, 0, 'index'); */
                        //if (!$error2) {
                        $_SESSION['tables'][$newName] = $_SESSION['tables'][$dbTable];
                        unset($_SESSION['tables'][$dbTable]);
                        $exec = exec("mv $root/dbTableData/$dbTable $root/dbTableData/$newName");
                        echo '<status>success</status>';
                        /* } else {
                          echo '<status>' . $error2 . '</status>';
                          } */
                    } else {
                        echo '<status>' . $error1 . '</status>';
                    }
                } else {
                    echo '<status>u r not authorized to edit table</status>';
                }
                break;
            case 'insColumn':
                if ($authorizeTable) {
                    $columnName = sqlinjection_free($_POST['columnName']);
                    $type = sqlinjection_free($_POST['type']);
                    $maxSize = stripslashes(sqlinjection_free($_POST['maxSize']));
                    $maxSize = $maxSize != null ? "(" . $maxSize . ")" : "";
                    $insertAfter = sqlinjection_free($_POST['insertAfter']);
                    $notNull = sqlinjection_free($_POST['notNull']) == 'YES' ? ' NOT NULL' : ' NULL';
                    $default = sqlinjection_free($_POST['dfault']);
                    $default = $default != NULL ? " DEFAULT '" . $default . "'" : "";
                    $query = "ALTER TABLE  `" . $dbTable . "` ADD  `" . $columnName . "` " . $type . $maxSize . $notNull . $default . " AFTER  `" . $insertAfter . "`";
                    $result = mysql_query($query, $dbc);
                    $error1 = mysql_error($dbc);
                    if (!$error1) {
                        echo "<status>success</status>";
                        if (!$_SESSION['tables'][$dbTable]['fc']) {
                            echo "<sKeys>";
                            foreach ($_SESSION['tables'][$dbTable]['index'] as $i => $row) {
                                $rand = rand();
                                $_SESSION['tables'][$dbTable][$columnName][$i]['sKey'] = $rand;
                                echo '<sKey>' . $rand . '</sKey>';
                            }
                            echo '</sKeys>';
                        }
                    } else {
                        echo '<status>' . $error1 . '</status>';
                    }
                } else {
                    echo '<status>user not authorized to insert column</status>';
                }
                break;
            case 'delColumn':
                if ($authorizeTable) {
                    $columnName = sqlinjection_free($_POST['columnName']);
                    $query = "ALTER TABLE `" . $dbTable . "` DROP `" . $columnName . "`";
                    $result = mysql_query($query, $dbc);
                    $error1 = mysql_error($dbc);
                    if (!$error1) {
                        $fn = "$root/dbTableData/$dbTable";
                        $fp = fopen($fn, 'a+');
                        $tp = fread($fp, filesize($fn));
                        fclose($fp);
                        $tp = json_decode($tp, TRUE);
                        foreach ($tp as $rid => $row) {
                            unset($tp[$rid][$columnName]);
                        }

                        $tp = json_encode($tp);
                        if ($tp) {
                            $fp = fopen($fn, "w");
                            $fw = fwrite($fp, $tp);
                            fclose($fp);
                            echo '<status>success</status>';
                        } else {
                            echo "<status>success</status>";
                        }
                        unset($_SESSION['tables'][$dbTable][$columnName]);
                    } else {
                        echo '<status>' . $error1 . '</status>';
                    }
                } else {
                    echo '<status>user not authorized to delete column</status>';
                }
                break;
            case 'permitColUsers':
                if ($authorizeTable) {
                    $colName = sqlinjection_free($_POST['colName']);
                    $drows = explode(',', sqlinjection_free($_POST['rows']));
                    $rows = dtsRows($drows);
                    $nmStr = sqlinjection_free($_POST['nmembers']);
                    $rORw = sqlinjection_free($_POST['rORw']);
                    $query = "SHOW FULL COLUMNS FROM `" . $dbTable . "` WHERE Field='" . $colName . "'";
                    $result = mysql_query($query, $dbc);
                    $error1 = mysql_error($dbc);
                    $comment = mysql_result($result, 0, 'Comment');
                    $rq = $_SESSION['tables'][$dbTable]['owner'];
                    $type = mysql_result($result, 0, 'Type');
                    $null = mysql_result($result, 0, 'Null');
                    $key = mysql_result($result, 0, 'Key');
                    $default = mysql_result($result, 0, 'Default');
                    $extra = mysql_result($result, 0, 'Extra');
                    $fold = -1;
                    $j = -1;
                    $i = 0;
                    while ($comment[$i] != NULL) {
                        if ($comment[$i] == '{') {
                            $fold++;
                            if ($fold == 0) {
                                $j++;
                            }
                        }
                        if ($fold > -1) {
                            $entry[$j].=$comment[$i];
                        }
                        if ($comment[$i] == '}') {
                            $fold--;
                        }
                        $i++;
                    }
                    $enCount = $j;
                    for ($j = 0; $j <= $enCount; $j++) {
                        $i = 0;
                        $g = false;
                        $k = 0;
                        while ($entry[$j][$i] != NULL) {
                            if (!$g) {
                                if ($i == 2) {
                                    $nentry[$j].='x';
                                } else {
                                    $nentry[$j].= $entry[$j][$i];
                                }
                            }
                            if ($i == 2) {
                                $g = true;
                            }
                            if ($entry[$j][$i] == ',' and $g) {
                                $g = false;
                                $nentry[$j].=',';
                            }
                            if ($g) {
                                $gis[$j].=$entry[$j][$i];
                            }
                            $i++;
                        }
                    }
                    $newPerm = '{' . $rORw . 'x,{' . $rows . '}}';
                    for ($i = 0; $i < count($nentry); $i++) {
                        if ($nentry[$i] == $newPerm) {
                            $matchg = $gis[$i];
                            $matchIndex = $i;
                            break;
                        }
                    }
                    $delGrps = array();
                    for ($i = 0; $i < count($gis); $i++) {
                        if ($entry[$i][1] == $rORw and $matchg != $gis[$i]) {
                            $grexe = groupExe($gis[$i], null, array('del' => $nmStr, 'delEmGrps' => TRUE));
                            $delGrps = array_merge($delGrps, $grexe['delGrps']);
                            $error3.=$grexe['error'];
                            for ($j = 0; $j < count($delGrps); $j++) {
                                if ($delGrps[$j] == $gis[$i]) {
                                    echo '<delGrp><rORw>' . $entry[$i][1] . '</rORw><g>' . $gis[$i] . '</g></delGrp>';
                                    unset($entry[$i]);
                                    unset($gis[$i]);
                                }
                            }
                            if ($gis[$i] and $grexe['grMod'])
                                echo '<modGrp><rORw>' . $entry[$i][1] . '</rORw><g>' . $gis[$i] . '</g><mems>' . implode(',', $grexe['members']) . '</mems></modGrp>';
                        }
                    }
                    if ($matchg && $matchg[0] == 'g') {
                        $grpMems = groupExe($matchg, null, array("add" => $nmStr));
                        $error2 = $grpMems['error'];
                        if (!$error2) {
                            echo '<status>success</status>';
                            if ($grpMems['delGrps']) {
                                $delGrps[] = $matchg;
                                echo '<delGrp><rORw>' . $newPerm[1] . '</rORw><g>' . $matchg . '</g></delGrp>';
                            } else {
                                echo '<modGrp><rORw>' . $newPerm[1] . '</rORw><g>' . $matchg . '</g><mems>' . implode(',', $grpMems['members']) . '</mems></modGrp>';
                            }
                        } else {
                            echo '<status>' . $error2 . '</status>';
                        }
                    } else {
                        if ($matchg && $matchg[0] != 'g') {
                            if ($rows != '') {
                                $nmStr.=',' . $matchg;
                                unset($entry[$matchIndex]);
                                unset($gis[$matchIndex]);
                                echo '<delGrp><rORw>' . $rORw . '</rORw><g>' . $matchg . '</g></delGrp>';
                            }
                        }
                        if ($rows != '') {
                            $op = array('CNG' => $dbTable . ':' . $colName . ':CA,NORMAL,' . $nmStr . ',w' . $_SESSION['tables'][$dbTable]['owner']);
                            $gE = groupExe(NULL, NULL, $op);
                            $ngid = $gE['ngid'];
                            $newPerm = '{' . $rORw . 'g' . $ngid . ',{' . $rows . '}}';
                            $entry[] = $newPerm;
                            echo '<newGrp><rORw>' . $rORw . '</rORw><g>' . 'g' . $ngid . '</g><mems>' . $nmStr . '</mems></newGrp>';
                        }
                    }
                    for ($i = 0; $i < count($delGrps); $i++) {
                        for ($j = 0; $j < count($gis); $j++) {
                            if ($delGrps[$i] == $gis[$j]) {
                                unset($entry[$j]);
                            }
                        }
                    }
                    $comment = implode(',', $entry);
                    $qn = ($null == 'YES') ? ' NULL ' : ' NOT NULL';
                    $qd = ($default != NULL) ? ' DEFAULT ' . $default : '';
                    $qk = ($key != '') ? ' KEY ' . $key : '';
                    $qe = ($extra != '') ? ' ' . $extra : '';
                    $qc = ($comment != '') ? " COMMENT '" . $comment . "'" : '';
                    $query = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $colName . "` " . $type . $qn . $qd . $qe . $qc;
                    $result = mysql_query($query, $dbc);
                    $error2 = mysql_error($dbc);
                    $suc = true;
                    if (!$error1 and !$error2) {
                        if (!$error3)
                            echo '<status>success</status>';
                        else
                            echo '<status>' . $error3 . '</status>';
                        echo '<permissions>' . $qc . '</permissions>';
                        echo '<users>' . $grexe['users'] . '</users>';
                        echo '<comment>' . $comment . '</comment>';
                    }else {
                        echo '<status>' . $error1 . $error2 . '</status>';
                    }
                }
                break;
            case 'createTable':
                $tableAllowed = tableAllowed($dbTable);
                if ($tableAllowed) {
                    $columns = stripslashes(sqlinjection_free($_POST['columns']));
                    $maxRs = sqlinjection_free($_POST['maxRs']);
                    $dbTable = sqlinjection_free($_POST['dbTable']);
                    $asObj = sqlinjection_free($_POST['role']);
                    if ($asObj == 'INDIVIDUAL') {
                        $asObj = 'u' . $_SESSION['uid'];
                    } else {
                        $asObj = 'o' . $asObj;
                    }
                    $comment = " COMMENT '{w" . $asObj . ",{*}}'";
                    $query = "CREATE TABLE " . $dbTable . "(`index` INT(" . $maxRs . ") UNIQUE AUTO_INCREMENT" . $comment . "," . $columns . ") COMMENT='al:" . sqlinjection_free($_SESSION['function'][$_POST['role']]['aL']) . ",o:" . substr($asObj, 1) . "'";
                    $result = mysql_query($query, $dbc);
                    $error1 = mysql_error($dbc);
                    if (!$error1) {
                        /* if ($_POST['writeAdminLevel']) {
                          if ($_POST['readAdminLevel']) {
                          $query = "INSERT INTO `adminTable` (`table`,`writeAdminLevel`,`readAdminLevel`) VALUES('" . $dbTable . "','" . $_POST['writeAdminLevel'] . "','" . $_POST['readAdminLevel'] . "')";
                          } else {
                          $query = "INSERT INTO `adminTable` (`table`,`writeAdminLevel`,`readAdminLevel`) VALUES('" . $dbTable . "','" . $_POST['writeAdminLevel'] . "','" . $_POST['writeAdminLevel'] . "')";
                          }
                          } else {
                          $query = "INSERT INTO `adminTable` (`table`,`writeAdminLevel`,`readAdminLevel`) VALUES('" . $dbTable . "','" . $_SESSION['adminLevel'] . "','" . $_SESSION['adminLevel'] . "')";
                          }
                          $result = mysql_query($query, $dbc);
                          $aTIndex = mysql_insert_id($dbc);
                          $error2 = mysql_error($dbc); */
                        if (!$error2) {
                            echo '<status>success</status>';
                        } else {
                            echo '<status>' . $error2 . '</status>';
                        }
                    } else {
                        echo '<status>' . $error1 . '</status>';
                    }
                }
                break;
            case 'delTable':
                if ($authorizeTable) {
                    $query = "DROP TABLE `" . $dbTable . "`";
                    $result = mysql_query($query, $dbc);
                    $error1 = mysql_error($dbc);
                    if (!$error1) {
                        $query = "UPDATE  `admintable` SET  `table` =  '" . $dbTable . "_del' WHERE  `table` = '" . $dbTable . "' LIMIT 1";
                        $result = mysql_query($query, $dbc);
                        $error2 = mysql_error($dbc);
                        if (!$error2) {
                            $query = "SELECT `index` FROM `adminTable` WHERE `table`='" . $dbTable . "'";
                            $result = mysql_query($query, $dbc);
                            $aTIndex = mysql_result($result, 0, 'index');
                            echo '<status>success</status>';
                        } elseif (preg_match('/Duplicate entry/', $error2)) {
                            $query = "DELETE FROM `adminTable` WHERE `table`='" . $dbTable . "_del'";
                            $result = mysql_query($query, $dbc);
                            $query = "UPDATE  `admintable` SET  `table` =  '" . $dbTable . "_del' WHERE  `table` = '" . $dbTable . "' LIMIT 1";
                            $result = mysql_query($query, $dbc);
                            $query = "SELECT `index` FROM `adminTable` WHERE `table`='" . $dbTable . "_del'";
                            $result = mysql_query($query, $dbc);
                            $aTIndex = mysql_result($result, 0, 'index');
                            $error3 = mysql_error($dbc);
                            if (!$error3) {
                                echo '<status>success</status>';
                            }
                        } elseif ($error3) {
                            echo '<status>' . $error3 . '</status>';
                        }
                    } else {
                        echo '<status>' . $error1 . '</status>';
                    }
                }
                break;
            case 'delRow':
                if ($authorizeTable) {
                    $rowIndex = sqlinjection_free($_POST['rowIndex']);
                    $query = "DELETE FROM `" . $dbTable . "` WHERE `index`='" . $rowIndex . "'";
                    $result = mysql_query($query, $dbc);
                    $error1 = mysql_error($dbc);
                    if (!$error1) {
                        if (!$_SESSION['tables'][$dbTable]['fc']) {
                            foreach ($_SESSION['tables'][$dbTable] as $columnName => $column) {
                                unset($_SESSION['tables'][$dbTable][$columnName][$rowIndex]);
                            }
                        }
                        echo '<status>success</status>';
                    } else {
                        echo '<status>' . $error1 . '</status>';
                    }
                }
                break;
            case 'updateCell':
                $sAUVO = json_decode($_POST['sAUVO'], TRUE);
                foreach ($sAUVO as $tn => &$table) {
                    $tp = $tpf[$tn];
                    if (!$tp) {
                        $fn = "$root/dbTableData/$tn";
                        $fp = fopen($fn, 'a+');
                        $tp = fread($fp, filesize($fn));
                        fclose($fp);
                        $tp = json_decode($tp, TRUE);
                    }
                    if ($sua and $dbTable == $tn) {
                        foreach ($sua as $rid => &$row) {
                            foreach ($row as $cid => &$cell) {
                                if (!$sAUVO[$tn][$rid][$cid] or !$sAUVO[$tn][$rid][$cid]['innerHTML']) {
                                    if (isset($cell['style'])) {
                                        foreach ($cell['style'] as $prop => &$value) {
                                            if (!$sAUVO[$tn][$rid][$cid][$prop]) {
                                                $sAUVO[$tn][$rid][$cid][$prop] = null;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    foreach ($table as $rid => &$row) {
                        if (strpos($rid, 'newRow') > -1) {
                            if ($authorizeTable) {
                                $colIndexStr = array();
                                $value = array();
                                foreach ($row as $cid => &$cell) {
                                    $colIndexStr[] = $cid;
                                    $value[] = $cell['innerHTML'];
                                }
                                $colIndexStr = "`" . implode("`,`", $colIndexStr) . "`";
                                $value = "\"" . implode("\",\"", $value) . "\"";
                                $query = "INSERT INTO `" . $tn . "`(" . $colIndexStr . ") VALUES(" . $value . ")";
                                $result = mysql_query($query, $dbc);
                                $error1[$rid] = mysql_error($dbc);
                                if (!$error1[$rid]) {
                                    $nrIndex[$rid] = mysql_insert_id($dbc);
                                    $nRowArr[$nrIndex[$rid]] = $row;
                                    foreach ($row as $cid => $cell) {
                                        foreach ($cell as $pid => $prop) {
                                            if ($pid != 'innerHTML' && $pid != 'sKey') {
                                                $tp[$nrIndex[$rid]][$cid]['style'][$pid] = $prop;
                                            }
                                        }
                                        $tp[$nrIndex[$rid]][$cid]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                                        $tp[$nrIndex[$rid]][$cid]['style']['oid'] = $_SESSION['oid'];
                                        $rand = rand();
                                        $cell['sKey'] = $rand;
                                        $_SESSION['tables'][$tn][$cid][$nrIndex[$rid]]['sKey'] = $rand;
                                    }
                                    $row['dbTableExecuterNRIndex'] = $nrIndex[$tn][$rid];
                                } else {
                                    $row['dbTableExecuterError'] = $error1[$tn][$rid];
                                }
                            } else {
                                $row['dbTableExecuterError'] = 'U r not authorized to append a row ~:|~';
                            }
                        } else {
                            $ups = array();
                            foreach ($row as $cid => &$cell) {
                                if ($authorizeTable or ($_SESSION['tables'][$tn][$cid][$rid]['sKey'] and $cell['sKey'] == $_SESSION['tables'][$tn][$cid][$rid]['sKey'])) {
                                    if (isset($cell['innerHTML']))
                                        $ups[] = "`" . $cid . "`=" . ($cell['innerHTML'] ? "'" . $cell['innerHTML'] . "'" : 'null');
                                } else {
                                    $cell['dbTableExecuterError'] = "U r not authorized to edit the cell ~&amp;|~";
                                }
                            }
                            $ups = implode(",", $ups);
                            if ($ups != '') {
                                $query = "UPDATE " . $tn . " SET " . $ups . " WHERE `index`='" . $rid . "'";
                                $result = mysql_query($query, $dbc);
                                $error1[$tn][$rid] = mysql_error($dbc);
                            }
                            if (!$error1[$tn][$rid]) {
                                foreach ($row as $cid => &$cell) {
                                    if (!$cell['dbTableExecuterError']) {
                                        foreach ($cell as $pid => &$prop) {
                                            if ($pid != 'innerHTML' && $pid != 'sKey') {
                                                $tp[$rid][$cid]['style'][$pid] = $prop;
                                            }
                                        }
                                        $tp[$rid][$cid]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                                        $tp[$rid][$cid]['style']['oid'] = $_SESSION['oid'];
                                    }
                                }
                            } else {
                                $row['dbTableExecuterError'] = $error1[$tn][$rid];
                            }
                        }
                    }
                    $tp = json_encode($tp);
                    if ($tp) {
                        $fp = fopen($fn, "w");
                        $fw = fwrite($fp, $tp);
                        fclose($fp);
                    } else {
                        echo '<' . $tn . 'status>a complex error occured, try to figure it out by urself ~:s~</' . $tn . 'status>';
                    }
                }
                echo '<status>success</status>';
                echo '<sAUVO>' . json_encode($sAUVO) . '</sAUVO>';
                break;
        }
        switch ($tableOperation) {
            case 'insColumn':
                if ($authorizeTable) {
                    if (!$error1) {
                        $queryts = "ALTER TABLE  `" . $dbTable . "` ADD  `" . $columnName . "` TIMESTAMP NULL COMMENT '" . strftime('%Y-%m-%d %H:%M:%S') . "' AFTER  `" . $insertAfter . "`";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        $queryu = "ALTER TABLE  `" . $dbTable . "` ADD  `" . $columnName . "` INT(13) NULL COMMENT '" . $_SESSION['uid'] . "' AFTER  `" . $insertAfter . "`";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        if (!$errorts and !$erroru) {
                            echo '<logstatus>success</logstatus>';
                        } else {
                            echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                        }
                    }
                }break;
            case 'delColumn':
                if ($authorizeTable) {
                    if (!$error1) {
                        $queryts = "alter table " . $dbTable . " change " . $columnName . " " . $columnName . "_del varchar (1) NULL COMMENT '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        if (preg_match('/Duplicate column name/', $errorts)) {
                            $queryts = "ALTER TABLE `" . $dbTable . "` DROP `" . $columName . "_del`";
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                            $queryts = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $columnName . "`  `" . $columName . "_del` VARCHAR( 1 ) NULL COMMENT '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                        }
                        $queryu = "alter table " . $dbTable . " change " . $columnName . " " . $columnName . "_del varchar (1) NULL COMMENT '" . $_SESSION['oid'] . "'";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        if (preg_match('/Duplicate column name/', $errorts)) {
                            $queryu = "ALTER TABLE `" . $dbTable . "` DROP `" . $columName . "_del`";
                            $resultu = mysql_query($queryu, $uidLink);
                            $erroru = mysql_error($uidLink);
                            $queryu = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $columnName . "`  `" . $columName . "_del` VARCHAR( 1 ) NULL COMMENT '" . $_SESSION['oid'] . "'";
                            $resultu = mysql_query($queryu, $uidLink);
                            $erroru = mysql_error($uidLink);
                        }
                        if (!$errorts && !erroru) {
                            echo '<logstatus>success</logstatus>';
                        } else {
                            echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                        }
                    }
                }break;

            case 'createTable':
                if ($tableAllowed) {
                    if (!$error1) {
                        $columns = explode(',', $columns);
                        for ($i = 0; $i < count($columns); $i++) {
                            $columns[$i] = explode(' ', $columns[$i]);
                            $tcol[$i] = $columns[$i][0] . " TIMESTAMP";
                            $ucol[$i] = $columns[$i][0] . " INT(13)";
                        }
                        $tcol = implode(',', $tcol);
                        $ucol = implode(',', $ucol);
                        $tcom = " COMMENT '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                        $ucom = " COMMENT '" . $_SESSION['oid'] . "'";
                        $queryts = "CREATE TABLE " . $dbTable . "(`index` INT(" . $maxRs . ") UNIQUE," . $tcol . ")" . $tcom;
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        $queryu = "CREATE TABLE " . $dbTable . "(`index` INT(" . $maxRs . ") UNIQUE," . $ucol . ")" . $ucom;
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        if (!$error2) {
                            /* $queryts = "INSERT INTO `adminTable` (`index`,`table`,`writeAdminLevel`,`readAdminLevel`) VALUES('" . $aTIndex . "',now(),now(),now())";
                              $resultts = mysql_query($queryts, $timestampLink);
                              $errorts = mysql_error($timestampLink);
                              $queryu = "INSERT INTO `adminTable` (`index`,`table`,`writeAdminLevel`,`readAdminLevel`) VALUES('" . $aTIndex . "','" . $_SESSION['oid'] . "','" . $_SESSION['oid'] . "','" . $_SESSION['oid'] . "')";
                              $resultu = mysql_query($queryu, $uidLink);
                              $erroru = mysql_error($uidLink); */
                            if (!$errorts && !$erroru) {
                                echo '<logstatus>success</logstatus>';
                            } else {
                                echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                            }
                        }
                    }
                }
                break;
            case 'delTable':
                if (!$error1) {
                    $queryts = "RENAME TABLE  `" . $dbTable . "` TO  `" . $dbTable . "_del` ";
                    $resultts = mysql_query($queryts, $timestampLink);
                    $errorts = mysql_error($timestampLink);
                    if (preg_match('/already exists$/', $errorts)) {
                        $queryts = "DROP TABLE `" . $dbTable . "_del`";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        $queryts = "RENAME TABLE  `" . $dbTable . "` TO  `" . $dbTable . "_del` ;";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                    }
                    if (!$errorts) {
                        $queryts = "ALTER TABLE  `" . $dbTable . "_del` COMMENT = '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                    }
                    $queryu = "RENAME TABLE  `" . $dbTable . "` TO  `" . $dbTable . "_del` ;";
                    $resultu = mysql_query($queryu, $uidLink);
                    $erroru = mysql_error($uidLink);
                    if (preg_match('/already exists$/', $erroru)) {
                        $queryu = "DROP TABLE `" . $dbTable . "_del`";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        $queryu = "RENAME TABLE  `" . $dbTable . "` TO  `" . $dbTable . "_del` ;";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                    }
                    if (!$erroru) {
                        $queryu = "ALTER TABLE  `" . $dbTable . "_del` COMMENT = '" . $_SESSION['oid'] . "'";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                    }
                    if (!($error2 and $error3)) {
                        $queryts = "UPDATE  `admintable` SET  `table` = now() WHERE  `index` = '" . $aTIndex . "' LIMIT 1";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        $queryu = "UPDATE  `admintable` SET  `table` =  '" . $_SESSION['oid'] . "' WHERE  `index` = '" . $aTIndex . "' LIMIT 1";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        if (!$errorts && !$erroru) {
                            echo '<logstatus>success</logstatus>';
                        } else {
                            echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                        }
                    } else {
                        echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                    }
                }
                break;
            case 'delRow':
                if (!$error1) {
                    $queryts = "UPDATE  `" . $dbTable . "` SET  `" . $_POST['fColumn'] . "` = now() WHERE  `index` = '" . $rowIndex . "' LIMIT 1";
                    $resultts = mysql_query($queryts, $timestampLink);
                    $errorts = mysql_error($timestampLink);
                    $queryu = "UPDATE  `" . $dbTable . "` SET  `" . $_POST['fColumn'] . "` = " . $_SESSION['oid'] . " WHERE  `index` = '" . $rowIndex . "' LIMIT 1";
                    $resultu = mysql_query($queryu, $uidLink);
                    $erroru = mysql_error($uidLink);
                }
                break;
            case 'updateCell':
                foreach ($sAUVO as $tn => &$table) {
                    foreach ($table as $rid => &$row) {
                        if (strpos($rid, 'newRow') > -1) {
                            if (!$error1[$tn][$rid]) {
                                $colIndexStr = array();
                                $value = array();
                                $colIndexStr = array();
                                $tss = array();
                                $os = array();
                                foreach ($row as $cid => &$cell) {
                                    if ($cid != 'dbTableExecuterNRIndex') {
                                        $colIndexStr[] = $cid;
                                        $tss[] = strftime("%Y-%m-%d %H:%M:%S");
                                        $os[] = $_SESSION['oid'];
                                    }
                                }
                                $colIndexStr = "`" . implode("`,`", $colIndexStr) . "`";
                                $tss = "\"" . implode("\",\"", $tss) . "\"";
                                $os = "\"" . implode("\",\"", $os) . "\"";
                                $queryts = "INSERT INTO `" . $tn . "`(`index`," . $colIndexStr . ") VALUES(" . $nrIndex[$tn][$rid] . "," . $tss . ")";
                                $resultts = mysql_query($queryts, $timestampLink);
                                $errorts = mysql_error($timestampLink);
                                $queryu = "INSERT INTO `" . $tn . "`(`index`," . $colIndexStr . ") VALUES(" . $nrIndex[$tn][$rid] . "," . $os . ")";
                                $resultu = mysql_query($queryu, $uidLink);
                                $erroru = mysql_error($uidLink);
                            }
                        } else {
                            if (!$error1[$tn][$rid]) {
                                $upts = array();
                                $upus = array();
                                foreach ($row as $cid => $cell) {
                                    if (!$sAUVO[$rid][$cid]['dbTableExecuterError']) {
                                        if ($cell['innerHTML'] and $cell['']) {
                                            $upts[] = "`" . $cid . "`='" . strftime("%Y-%m-%d %H:%M:%S") . "'";
                                            $upus[] = "`" . $cid . "`='" . $_SESSION['oid'] . "'";
                                        }
                                    }
                                }
                                $upts = implode(",", $upts);
                                $upus = implode(",", $upus);
                                if ($upts and $upus) {
                                    $queryts = "update " . $tn . " set " . $upts . " WHERE `index`='" . $rid . "'";
                                    $resultts = mysql_query($queryts, $timestampLink);
                                    $errorts = mysql_error($timestampLink);
                                    $queryu = "update " . $tn . " set " . $upus . " WHERE `index`='" . $rid . "'";
                                    $resultu = mysql_query($queryu, $uidLink);
                                    $erroru = mysql_error($uidLink);
                                }
                            }
                        }
                    }
                }
                break;
            case 'renametableName':
                if ($authorizeTable) {
                    if (!$error1) {
                        $queryts = "RENAME TABLE  `" . $dbTable . "` TO  `" . $newName . "` ";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        $queryu = "RENAME TABLE  `" . $dbTable . "` TO  `" . $newName . "` ";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                        if (!$error2) {
                            $queryts = "UPDATE `adminTable` set `table`=now() where `index`='" . $tIndex . "'";
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                            $queryu = "UPDATE `adminTable` set `table`='" . $_SESSION['oid'] . "' where `index`='" . $tIndex . "'";
                            $resultu = mysql_query($queryu, $uidLink);
                            $erroru = mysql_error($uidLink);
                            echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                            $queryts = "ALTER TABLE  `" . $newName . "` COMMENT = '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                            $queryu = "ALTER TABLE  `" . $newName . "` COMMENT = '" . $_SESSION['oid'] . "'";
                            $resultu = mysql_query($queryu, $uidLink);
                            $erroru = mysql_error($uidLink);
                            echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                        }
                    }
                }
                break;
            case 'renamecolName':
                if ($authorizeTable) {
                    if (!$error1) {
                        $queryts = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $newName . "` TIMESTAMP COMMENT  '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        $queryu = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $newName . "` INT(13) COMMENT  '" . $_SESSION['oid'] . "'";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                    }
                }
                break;

            case 'permitColUsers':
                if ($matchg) {
                    $queryts = "UPDATE groups SET members=now() WHERE `index`=" . $matchg;
                    $resultts = mysql_query($queryts, $timestampLink);
                    $errorts = mysql_error($timestampLink);
                    $queryu = "UPDATE groups SET members=" . $_SESSION['oid'] . " WHERE `index`=" . $matchg;
                    $resultu = mysql_query($queryu, $uidLink);
                    $erroru = mysql_error($uidLink);
                    echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                } else {
                    if ($rows != '') {
                        $queryts = "INSERT INTO groups(`index`,`label`,`members`)values(" . $ngid . ", now(), now())";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        $queryu = "INSERT INTO groups(`index`,`label`,`members`)values(" . $ngid . ", " . $_SESSION['oid'] . ", " . $_SESSION['oid'] . ")";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                    }
                    $queryts = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $colName . "` TIMESTAMP NULL COMMENT  '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                    $resultts = mysql_query($queryts, $timestampLink);
                    $errorts = mysql_error($timestampLink);
                    $queryu = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $colName . "` INT(13) NULL COMMENT  '" . $_SESSION['oid'] . "'";
                    $resultu = mysql_query($queryu, $uidLink);
                    $erroru = mysql_error($uidLink);
                    echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                }
                break;
        }
        echo "</" . $tableOperation . ">";
    }
    shm_put_var($dbtShmId, $dbtKey, $liveDBTable);
    sem_release($dbtSemId);
}
echo '</dbTableExecuter>';
?>