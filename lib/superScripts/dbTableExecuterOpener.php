<?php

/* Author: Gowtham */
//tableProperties generator
$fieldStr = "\"" . implode("\",\"", $Fiel) . "\"";
$typeStr = "\"" . implode("\",\"", $Typ) . "\"";
$nullStr = "\"" . implode("\",\"", $Nul) . "\"";
$keyStr = "\"" . implode("\",\"", $Ke) . "\"";
$defaultStr = "\"" . implode("\",\"", $Defaul) . "\"";
$extraStr = "\"" . implode("\",\"", $Extr) . "\"";
$commentStr = "\"" . implode("\",\"", $Commen) . "\"";
if (!$liveDBTable['liveD']) {
    $query = "SELECT * FROM `" . $dbTable . "` ORDER BY `index`";
    $result = mysql_query($query, $dbc);
    $error2 = mysql_error($dbc);
}
if ($sm) {
    $cc = count($Field);
    $rc = $rowCount;
}
$rc = $rc ? $rc : 0;
//$rowCount = $rc;
if (!$liveDBTable['liveD']) {
    $tp = getTableFromFile($dbTable);
    foreach ($Field as $i => $colName) {
        for ($j = -1; $j < $rowCount; $j++) {
            if (isset($rInd[$j])) {
                $liveDBTable['cells'][$rInd[$j]][$colName] = $tp[$rInd[$j]][$colName];
                $liveDBTable['cells'][$rInd[$j]][$colName]['innerHTML'] = mysql_result($result, $j, $colName);
            } elseif ($j == -1) {
                $liveDBTable['cells']['tHR'][$colName] = $tp['tHR'][$colName];
                $liveDBTable['cells']['tHR'][$colName]['Type'] = $Type[$i];
                $liveDBTable['cells']['tHR'][$colName]['Null'] = $Null[$i];
                $liveDBTable['cells']['tHR'][$colName]['Key'] = $Key[$i];
                $liveDBTable['cells']['tHR'][$colName]['Default'] = $Default[$i];
                $liveDBTable['cells']['tHR'][$colName]['Extra'] = $Extra[$i];
                $liveDBTable['cells']['tHR'][$colName]['Comment'] = $Comment[$i];
                if ($colName == 'index') {
                    $liveDBTable['cells']['tHR'][$colName]['rowCount'] = $rc;
                    $liveDBTable['cells']['tHR'][$colName]['colCount'] = $cc;
                    $liveDBTable['cells']['tHR'][$colName]['tableName'] = $dbTable;
                }
            }
        }
    }
}

function tableEchoer(&$fc, &$rowCount, &$result, &$Field, &$adminTable, &$dbTable, &$mems, &$sm, &$rInd, &$liveDBTable) {
    if (!$liveDBTable['liveD']) {
        if ($fc or $sm) {
            foreach ($Field as $i => $colName) {
                for ($j = -1; $j <= $rowCount; $j++) {
                    if ($j == -1) {
                        echo $colName . ':[';
                    } elseif ($j == $rowCount) {
                        echo "],";
                    } else {
                        $value = mysql_result($result, $j, $Field[$i]);
                        echo "'" . htmlentities($value) . "',";
                        $liveDBTable['cells'][$rInd[$j]][$colName]['innerHTML'] = $value;
                    }
                }
            }
            if ($adminTable) {
                $liveDBTable['adminTable'] = TRUE;
            }
        } else {
            foreach ($Field as $i => $colName) {
                if (count($mems['r']['ptable'][$colName]) > 1) {
                    for ($j = -1; $j <= $rowCount; $j++) {
                        if ($j == -1) {
                            echo $colName . ':[';
                        } elseif ($j == $rowCount) {
                            echo "],";
                        } else {
                            $value = mysql_result($result, $j, $Field[$i]);
                            if ($mems['r']['ptable'][$colName][$rInd[$j]]) {
                                echo "'" . $value . "',";
                            }/* else {
                              echo "'DOA',";
                              } */
                            $liveDBTable['cells'][$rInd[$j]][$colName]['innerHTML'] = $value;
                        }
                    }
                } else {
                    for ($j = 0; $j < $rowCount; $j++) {
                        $value = mysql_result($result, $j, $Field[$i]);
                        $liveDBTable['cells'][$rInd[$j]][$colName]['innerHTML'] = $value;
                    }
                }
            }
        }
    } else {
        if ($fc or $sm) {
            foreach ($Field as $i => $colName) {
                for ($j = -1; $j <= $rowCount; $j++) {
                    if ($j == -1) {
                        echo $colName . ':[';
                    } elseif ($j == $rowCount) {
                        echo "],";
                    } else {
                        $value = $liveDBTable['cells'][$rInd[$j]][$colName]['innerHTML'];
                        echo "'" . htmlentities($value) . "',";
                    }
                }
            }
            if ($adminTable) {
                $liveDBTable['adminTable'] = TRUE;
            }
        } else {
            foreach ($Field as $i => $colName) {
                if (count($mems['r']['ptable'][$colName]) > 1) {
                    for ($j = -1; $j <= $rowCount; $j++) {
                        if ($j == -1) {
                            echo $colName . ':[';
                        } elseif ($j == $rowCount) {
                            echo "],";
                        } else {
                            $value = $liveDBTable['cells'][$rInd[$j]][$colName]['innerHTML'];
                            if ($mems['r']['ptable'][$colName][$rInd[$j]]) {
                                echo "'" . $value . "',";
                            }/* else {
                              echo "'DOA',";
                              } */
                        }
                    }
                }
            }
        }
    }
}

function tableHashEchoer(&$fc, &$colCount, &$rowCount, &$result, &$Field, &$adminTable, &$dbTable, &$mems, &$rInd, &$liveDBTable) {
    if (!$fc) {
        foreach ($Field as $i => $colName) {
            if ($mems['r']['ptable'][$colName]) {
                for ($j = -1; $j <= $rowCount; $j++) {
                    if ($j == -1) {
                        echo $Field[$i] . ':[';
                    } elseif ($j == $rowCount) {
                        echo "],";
                    } else {
                        if ($mems['r']['ptable'][$colName][$rInd[$j]]) {
                            $rand = rand();
                            $liveDBTable['usersData'][$_SESSION['uid']]['cells'][$rInd[$j]][$colName]['sKey'] = $rand;
                            if ($mems['w']['ptable'][$colName][$rInd[$j]])
                                echo "'" . $rand . "',";
                            else
                                echo "'',";
                        }
                    }
                }
            }
        }
    }
}

function tableAuthorizationEchoer(&$fc) {
    if ($fc)
        echo '*';
}

function tableFormulaEchoer(&$root, &$dbTable, &$mems, &$liveDBTable, $fc, $sm) {
    foreach ($liveDBTable['cells'] as $rid => &$row) {
        foreach ($row as $cid => &$cell) {
            if ((($mems['r']['ptable'][$cid][$rid] and $rid != 'tHR') or ($rid == 'tHR' and count($mems['r']['ptable'][$cid]) > 1)) or $fc or $sm) {
                if (!$fc) {
                    $rand = rand();
                    $liveDBTable['usersData'][$_SESSION['uid']]['cells'][$rid][$cid]['sKey'] = $rand;
                }
                $tp[$rid][$cid] = $cell;
                if ($mems['w']['ptable'][$cid][$rid]) {
                    $tp[$rid][$cid]['sKey'] = $rand;
                }
            }
        }
    }
    $liveDBTable['usersData'][$_SESSION['uid']]['authorization']=$tp['tHR']['index']['authorization'] = ($fc ? "*" : "");
    echo json_encode($tp, JSON_HEX_QUOT | JSON_HEX_APOS);
}

$fn = "$root/userFiles/" . $_SESSION['username'] . "/data.json";
$fp = fopen($fn, 'a+');
if ($fp) {
    $fd = fgets($fp);
    fclose($fp);
    $fp = fopen($fn, 'w');
    $df = json_decode($fd);
    $tql = explode("$,$", $df->tableQueryLogs);
    if (!$tql[0])
        unset($tql[0]);
    for ($i = 0; $i < count($tql); $i++) {
        if ($tql[$i] == $cQuery) {
            unset($tql[$i]);
        }
    }
    $tql[] = $cQuery;
    if (count($tql) > 100) {
        unset($tql[0]);
    }
    $df->tableQueryLogs = implode("$,$", $tql);
    $fd = json_encode($df);
    $fw = fwrite($fp, $fd);
    $fclose = fclose($fp);
    $_SESSION['tables'][$dbTable]['status'] = 'opened';
    $liveDBTable['name'] = $dbTable;
    $liveDBTable['usersData'][$_SESSION['uid']]['status'] = 'opened';
    foreach ($liveDBTable['dbtUpdates'] as $i => &$sdbtu) {
        if ($sdbtu and !$sdbtu['data']['swallowedBy'][$_SESSION['uid']]) {
            $sdbtu['data']['swallowedBy'][$_SESSION['uid']] = true;
            if (count($sdbtu['data']['swallowedBy']) == count($liveDBTable['usersData'])) {
                unset($liveDBTable['dbtUpdates'][$i]);
            }
        }
    }
}
if ($_POST['rawOpen']) {
    include "$root/lib/superScripts/dbTableExecuterRawOpener.php";
} else {
    include "$root/lib/superScripts/dbTableExecuterOpenerFT.php";
}
?>
