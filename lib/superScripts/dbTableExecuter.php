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

function rowWithPid(&$liveDBTable, $pid) {
    if ($pid == '') {
        return 'tHR';
    } else {
        $priCol = $liveDBTable['priCol'];
        foreach ($liveDBTable['cells'] as $rid => $row) {
            if ($row[$priCol]['innerHTML'] == $pid) {
                return $rid;
            }
        }
    }
}

function computeSourceCells($formula, $dbTable, $ccid, $crid, $cpid, &$aPSCs, $sciri, &$dbtUpdate) {

    function getCells($cid, $ri, $tid, &$liveDBTable, &$sCs, &$tp, &$dbtUpdate, $sciri, $dbTable, $ccid) {
        $cid = $cid == 'index' ? $ccid : $cid;
        if ($cid[0] == '/') {
            foreach ($liveDBTable['cells'][$ri] as $ci => $cell) {
                if (preg_match($cid, $ci)) {
                    array_push($sCs, $ci . "," . ($ri != 'tHR' ? $liveDBTable['cells'][$ri][$liveDBTable['priCol']]['innerHTML'] : "") . ($tid != $dbTable ? "," . $tid : ''));
                    $tp[$ri][$ci]['f']['ee'][] = $tid == $dbTable ? $sciri : $sciri . "," . $dbTable;
                    $dbtUpdate['tables'][$tid]['cells'][$ri][$ci]['f']['ee'] = $tp[$ri][$ci]['f']['ee'] = true_array_unique($tp[$ri][$ci]['f']['ee']);
                }
            }
        } elseif ($cid[0] == '*') {
            array_push($sCs, "index," . ($ri != 'tHR' ? $liveDBTable['cells'][$ri][$liveDBTable['priCol']]['innerHTML'] : "") . "," . ($tid != $dbTable ? "," . $tid : ''));
        } elseif (count($cids = split('~', $cid)) > 1) {
            $rangeE = FALSE;
            foreach ($liveDBTable['cells']['tHR'] as $ci => $cell) {
                if (($ci == $cids[0] or $rangeS) and !$rangeE) {
                    $rangeS = true;
                    if ($ci == $cids[1]) {
                        $rangeE = true;
                    }
                    $rcells[] = $ci;
                }
            }
            if ($rangeE) {
                foreach ($rcells as $rci => $ci) {
                    array_push($sCs, $ci . "," . ($ri != 'tHR' ? $liveDBTable['cells'][$ri][$liveDBTable['priCol']]['innerHTML'] : "") . ($tid != $dbTable ? "," . $tid : ''));
                    $tp[$ri][$ci]['f']['ee'][] = $tid == $dbTable ? $sciri : $sciri . "," . $dbTable;
                    $dbtUpdate['tables'][$tid]['cells'][$ri][$ci]['f']['ee'] = $tp[$ri][$ci]['f']['ee'] = true_array_unique($tp[$ri][$ci]['f']['ee']);
                }
            }
        } elseif ($cid != '') {
            if (isset($liveDBTable['cells'][$ri][$cid])) {
                array_push($sCs, $cid . "," . ($ri != 'tHR' ? $liveDBTable['cells'][$ri][$liveDBTable['priCol']]['innerHTML'] : "") . ($tid != $dbTable ? "," . $tid : ''));
                $tp[$ri][$cid]['f']['ee'][] = $tid == $dbTable ? $sciri : $sciri . "," . $dbTable;
                $dbtUpdate['tables'][$tid]['cells'][$ri][$cid]['f']['ee'] = $tp[$ri][$cid]['f']['ee'] = true_array_unique($tp[$ri][$cid]['f']['ee']);
            }
        }
    }

    $fx = $formula;
    $sCs = array();
    $matches = array();
    preg_match_all('/[0-9a-zA-Z_~]+\([0-9a-zA-Z_~*]*\)\([0-9a-zA-Z_]*\)/i', $formula, $matches);
    foreach ($matches[0] as $key => &$match) {
        $ml = strlen($match);
        $rstart = strpos($match, '(', 0);
        $rend = strpos($match, ')', 0);
        $rid = substr($match, $rstart + 1, $rend - $rstart - 1);
        $tstart = strpos($match, '(', $rstart + 1);
        $tid = substr($match, $tstart + 1, $ml - $tstart - 2);
        if ($_SESSION['tables'][$tid]) {
            $liveDBTable = getLiveTable($tid);
            $tp = getTableFromFile($tid);
            if ($ca = $aPSCs[$tid]) {
                foreach ($ca as $i => $c) {
                    $c[1] = $c[1] ? rowWithPid($liveDBTable, $c[1]) : 'tHR';
                    foreach ($tp[$c[1]][$c[0]]['f']['ee'] as $e => $ee) {
                        if ($ee == $sciri or $ee == $sciri . "," . $dbTable) {
                            unset($tp[$c[1]][$c[0]]['f']['ee'][$e]);
                            $dbtUpdate['tables'][$tid]['cells'][$c[1]][$c[0]]['f']['ee'] = $tp[$c[1]][$c[0]]['f']['ee'] = true_array_unique($tp[$c[1]][$c[0]]['f']['ee']);
                            break;
                        }
                    }
                }
                unset($aPSCs[$tid]);
            }
            $rid = substr($match, $rstart + 1, $rend - $rstart - 1);
            $cid = substr($match, 0, $rstart);
            if ($rid[0] == '/') {
                foreach ($liveDBTable['cells'] as $ri => $row) {
                    if (preg_match($rid, $row[$liveDBTable['priCol']])) {
                        getCells($cid, $ri, $tid, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
                    }
                }
            } elseif ($rid[0] == '*') {
                getCells($cid, 'tHR', $tid, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
            } elseif (count($rids = split('~', $rid)) > 1) {
                $rangeE = FALSE;
                foreach ($liveDBTable['cells'] as $ri => $row) {
                    if (($row[$liveDBTable['priCol']]['innerHTML'] == $rids[0] or $rangeS) and !$rangeE) {
                        $rangeS = true;
                        if ($row[$liveDBTable['priCol']]['innerHTML'] == $rids[1]) {
                            $rangeE = true;
                        }
                        $rrows[] = $ri;
                    }
                }
                if ($rangeE) {
                    foreach ($rrows as $kei => $ri) {
                        getCells($cid, $ri, $tid, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
                    }
                }
            } elseif ($rid != '') {
                getCells($cid, rowWithPid($liveDBTable, $rid), $tid, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
            } elseif ($rid == '') {
                getCells($cid, rowWithPid($liveDBTable, $cpid), $tid, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
            }
            putTableInFile($tp, $tid);
            closeLiveTable($liveDBTable);
        }
        $k = 0;
        $formula = str_replace($match, 'var' . $k++, $formula);
    }
    $liveDBTable = getLiveTable($dbTable);
    $tp = getTableFromFile($dbTable);
    if ($ca = $aPSCs[$dbTable]) {
        foreach ($ca as $i => $c) {
            $c[1] = $c[1] ? rowWithPid($liveDBTable, $c[1]) : 'tHR';
            foreach ($tp[$c[1]][$c[0]]['f']['ee'] as $e => $ee) {
                if ($ee == $sciri or $ee == $sciri . "," . $dbTable) {
                    unset($tp[$c[1]][$c[0]]['f']['ee'][$e]);
                    $dbtUpdate['tables'][$dbTable]['cells'][$c[1]][$c[0]]['f']['ee'] = $tp[$c[1]][$c[0]]['f']['ee'] = true_array_unique($tp[$c[1]][$c[0]]['f']['ee']);
                    break;
                }
            }
        }
        unset($aPSCs[$dbTable]);
    }
    foreach ($liveDBTable['cells']['tHR'] as $colName => $column) {
        $start = 0;
        $start = strpos($formula, $colName, $start);
        while ($start > -1) {
            if (preg_match("/[^a-zA-Z0-9_]/", $formula[$start - 1]) or !$formula[$start - 1]) {
                $pst = $start;
                $start += strlen($colName);
                if ($formula[$pst - 1] != ';' and ($formula[$start] != '=' or $formula[$start + 1] == '=') and (preg_match("/[^a-zA-Z0-9_]/", $formula[$start]) or !$formula[$start])) {
                    $cid = $colName;
                    $row = '';
                    unset($col2);
                    if ($formula[$start++] == '~') {
                        foreach ($liveDBTable['cells']['tHR'] as $col2 => $column2) {
                            if (strpos($formula, $col2, $start) == $start) {
                                $colName2 = $col2;
                                break;
                            }
                        }
                    }
                    if (($col2 and $colName2) or !($col2 or $colName2)) {
                        $cid = $colName2 ? $cid . '~' . $colName2 : $cid;
                        $j = $start;
                        unset($rid);
                        while ($formula[$start - 1] == '(' and $formula[$j] != '' and $formula[$j] != ')') {
                            $rid.=$formula[$j++];
                        }
                        if ($rid) {
                            $start = ++$j;
                            if (count($rids = split('~', $rid)) > 1) {
                                $rangeE = FALSE;
                                $rrows = array();
                                foreach ($liveDBTable['cells'] as $ri => $row) {
                                    if (($row[$liveDBTable['priCol']]['innerHTML'] == $rids[0] or $rangeS) and !$rangeE) {
                                        $rangeS = true;
                                        if ($row[$liveDBTable['priCol']]['innerHTML'] == $rids[1]) {
                                            $rangeE = true;
                                        }
                                        $rrows[] = $ri;
                                    }
                                }
                                if ($rangeE) {
                                    foreach ($rrows as $kei => $ri) {
                                        getCells($cid, $ri, $dbTable, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
                                    }
                                }
                            } elseif ($rid[0] == '/') {
                                foreach ($liveDBTable['cells'] as $ri => $row) {
                                    if (preg_match($rid, $row[$liveDBTable['priCol']])) {
                                        getCells($cid, $ri, $dbTable, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
                                    }
                                }
                            } elseif ($rid == '*') {
                                getCells($cid, 'tHR', $dbTable, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
                            } elseif ($rid != '' and $rid != ' ') {
                                getCells($cid, rowWithPid($liveDBTable, $rid), $dbTable, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
                            }
                        } else {
                            getCells($cid, $crid, $dbTable, $liveDBTable, $sCs, $tp, $dbtUpdate, $sciri, $dbTable, $ccid);
                        }
                    }
                    $start = strpos($formula, $colName, $start);
                }
            } else {
                $start = strpos($formula, $colName, $start);
            }
        }
    }
    putTableInFile($tp, $dbTable);
    closeLiveTable($liveDBTable);
    foreach ($aPSCs as $tid => $ca) {
        $liveDBTable = getLiveTable($tid);
        $tp = getTableFromFile($tid);
        foreach ($ca as $i => $c) {
            $c[1] = $c[1] ? $c[1] : 'tHR';
            foreach ($tp[$c[1]][$c[0]]['f']['ee'] as $e => $ee) {
                if ($ee == $sciri or $ee == $sciri . "," . $dbTable) {
                    unset($tp[$c[1]][$c[0]]['f']['ee'][$e]);
                    $dbtUpdate['tables'][$tid]['cells'][$c[1]][$c[0]]['f']['ee'] = $tp[$c[1]][$c[0]]['f']['ee'] = true_array_unique($tp[$c[1]][$c[0]]['f']['ee']);
                    break;
                }
            }
        }
        putTableInFile($tp, $tid);
        closeLiveTable($liveDBTable);
        unset($aPSCs[$tid]);
    }
    $sCs = true_array_unique($sCs);
    return $sCs;
}

function updateLiveTable(&$liveDBTable, &$dbtUpdate, $updateOp) {
    $dbTable = $liveDBTable['name'];
    if ($dbtUpdate['op'] and $updateOp) {
        foreach ($dbtUpdate['op'] as $opid => $op) {
            if ($opid == 'renametableName') {
                $newName = $dbtUpdate['op']['renametableName']['newName'];
                removeLiveTable($dbTable);
                getLiveTable($newName);
                $liveDBTable['name'] = $newName;
            } elseif ($opid == 'renamecolName') {
                foreach ($dbtUpdate['op']['renamecolName'] as $colName => $newProps) {
                    $newName = $newProps['newName'];
                    foreach ($liveDBTable['cells']['tHR'] as $cid => $col) {
                        if ($cid == $colName) {
                            $tHR[$newName] = $col;
                            $tHR[$newName]['Type'] = $newProps['Type'];
                            $tHR[$newName]['Size'] = $newProps['Size'];
                            $tHR[$newName]['Null'] = $newProps['Null'];
                            $tHR[$newName]['Default'] = $newProps['Default'];
                            //$tHR[$newName]['Key'] = $newProps['Key'];
                            //$tHR[$newName]['Comment'] = $dbtUpdate['cells']['tHR'][$newName]['Comment'];
                            //$tHR[$newName]['Extra'] = $dbtUpdate['cells']['tHR'][$newName]['Extra'];
                        } else {
                            $tHR[$cid] = $col;
                        }
                    }
                    $liveDBTable['cells']['tHR'] = &$tHR;
                    unset($row);
                    foreach ($liveDBTable['cells'] as $rid => &$row) {
                        if ($row[$colName] && $rid != 'tHR') {
                            $row[$newName] = &$row[$colName];
                            unset($row[$colName]);
                        }
                    }
                }
            } elseif ($opid == 'delTable') {
                if ($op) {
                    $liveDBTable = null;
                    removeLiveTable($dbTable);
                }
            } elseif ($opid == 'delRow') {
                foreach ($dbtUpdate['op']['delRow'] as $rid => $info) {
                    unset($liveDBTable['cells'][$rid]);
                }
            } elseif ($opid == 'updateFormula') {
                
            } elseif ($opid == 'delColumn') {
                $columnName = $dbtUpdate['op']['delColumn']['columnName'];
                foreach ($liveDBTable['cells'] as $rid => &$row) {
                    unset($row[$columnName]);
                }
            } elseif ($opid == 'insColumn') {
                foreach ($dbtUpdate['op']['insColumn'] as $columnName => $value) {
                    $cols = $liveDBTable['cells']['tHR'];
                    $liveDBTable['cells']['tHR'] = array();
                    foreach ($cols as $colName => &$col) {
                        $liveDBTable['cells']['tHR'][$colName] = $col;
                        if ($colName == $value['after']) {
                            $liveDBTable['cells']['tHR'][$columnName]['Type'] = $dbtUpdate['cells']['tHR'][$columnName]['Type'];
                            $liveDBTable['cells']['tHR'][$columnName]['Size'] = $dbtUpdate['cells']['tHR'][$columnName]['Size'];
                            $liveDBTable['cells']['tHR'][$columnName]['Null'] = $dbtUpdate['cells']['tHR'][$columnName]['Null'];
                            $liveDBTable['cells']['tHR'][$columnName]['Default'] = $dbtUpdate['cells']['tHR'][$columnName]['Default'];
                            $liveDBTable['cells']['tHR'][$columnName]['Key'] = $dbtUpdate['cells']['tHR'][$columnName]['Key'];
                            //$liveDBTable['cells']['tHR'][$columnName]['Comment'] = $dbtUpdate['cells']['tHR'][$columnName]['Comment'];
                            //$liveDBTable['cells']['tHR'][$columnName]['Extra'] = $dbtUpdate['cells']['tHR'][$columnName]['Extra'];
                        }
                    }
                }
            }
        }
    }
    foreach ($dbtUpdate['cells'] as $rid => &$row) {
        foreach ($row as $cid => &$cell) {
            foreach ($cell as $pid => $prop) {
                $liveDBTable['cells'][$rid][$cid][$pid] = $prop;
            }
        }
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?><dbTableExecuter>';
if (domesticSlave($_SESSION['adminLevel'], 'Zz8')) {
    $dbTable = sqlinjection_free($_POST['dbTable']);
    $dbTable = strtolower($dbTable);
    $tableOps = explode("$,$", $_POST['tableOperation']);

//Get update.
    foreach ($_SESSION['tables'] as $utn => $ut) {
        $liveDBTable = getLiveTable($utn);
        if ($liveDBTable) {
            foreach ($liveDBTable['dbtUpdates'] as $i => &$sdbtu) {
                if ($sdbtu and !$sdbtu['data']['swallowedBy'][$_SESSION['uid']]) {
                    $dbtu = $sdbtu['data'];
                    foreach ($dbtu as $key => &$value) {
                        if ($key == 'cells') {
                            foreach ($value as $rid => &$row) {
                                foreach ($row as $cid => &$cell) {
                                    if ($liveDBTable['usersData'][$_SESSION['uid']]['authorization'] == "*" or ($liveDBTable['usersData'][$_SESSION['uid']]['cells'][$rid][$cid]['sKey'] and $cell['sKey'] == $liveDBTable['usersData'][$_SESSION['uid']]['cells'][$rid][$cid]['sKey'])) {
                                        foreach ($cell as $pid => $prop) {
                                            $idbtUpdate['tables'][$utn]['cells'][$rid][$cid][$pid] = $prop;
                                        }
                                    }
                                }
                            }
                        } else if ($key == 'op') {
                            foreach ($value as $oid => $op) {
                                foreach ($op as $info => $in) {
                                    $idbtUpdate['tables'][$utn][$key][$oid][$info] = $in;
                                }
                            }
                        } else if ($key == 'notByFullAuthority') {
                            $idbtUpdate['tables'][$utn][$key] = true;
                        }
                    }
                    $sdbtu['data']['swallowedBy'][$_SESSION['uid']] = true;
                    if (count($liveDBTable['usersData']) == count($liveDBTable['dbtUpdates'][$i]['data']['swallowedBy'])) {
                        unset($liveDBTable['dbtUpdates'][$i]);
                    }
                }
            }
        }
        closeLiveTable($utn, $liveDBTable);
    }

//Process current operation as per previous operation
    foreach ($idbtUpdate['tables'] as $utid => &$dbtu) {
        if ($utid == $dbTable) {
            foreach ($dbtu as $key => &$value) {
                if ($key == 'op') {
                    foreach ($value as $oid => $op) {
                        if ($oid == 'delRow') {
                            foreach ($tableOps as $oi => $opn) {
                                if ($opn == 'updateCell') {
                                    $sAUVO = json_decode($_POST['sAUVO'], TRUE);
                                    foreach ($sAUVO as $tn => &$table) {
                                        if ($tn == $dbTable) {
                                            foreach ($table as $rid => &$row) {
                                                foreach ($op as $drn => $info) {
                                                    if ($rid == $drn) {
                                                        unset($table[$rid]);
                                                        $modified = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($modified) {
                                        $_POST['sAUVO'] = json_encode($sAUVO);
                                    }
                                } elseif ($opn == 'updateFormula') {
                                    $ri = $_POST['fRowIndex'];
                                    foreach ($op as $drn => $info) {
                                        if ($ri == $drn) {
                                            foreach ($tableOps as $oi2 => &$opn2) {
                                                if ($opn2 == 'updateFormula') {
                                                    unset($tableOps[$oi2]);
                                                    echo "<updateFormula><status>The row has been deleted by table owner ~:|~</status></updateFormula>";
                                                } else if ($opn2 == 'updateCell') {
                                                    unset($tableOps[$oi2]);
                                                    echo "<updateCell><status>The row has been deleted ~:|~</status></updateCell>";
                                                }
                                            }
                                        }
                                    }
                                } elseif ($opn == 'renametableName') {
                                    foreach ($tableOps as $oi2 => &$opn2) {
                                        if ($opn2 == 'renametableName') {
                                            unset($tableOps[$oi2]);
                                            echo "<renametableName><status>The table is currently being edited Please try again later ~:)~</status></renametableName>";
                                        }
                                    }
                                } elseif ($opn == 'delRow') {
                                    foreach ($op as $rid => $info) {
                                        if ($rid == $_POST['rowIndex']) {
                                            foreach ($tableOps as $oi2 => &$opn2) {
                                                if ($opn2 == 'delRow') {
                                                    unset($tableOps[$oi2]);
                                                    echo "<delRow><status>Already deleted ~:)~</status></delRow>";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($oid == 'delColumn') {
                            foreach ($tableOps as $oi => $opn) {
                                echo "<" . $tableOps[$oi] . "><status>Table Structure has been modified by table owner ~:|~</status></" . $tableOps[$oi] . ">";
                                unset($tableOps[$oi]);
                            }
                        } elseif ($oid == 'updateFormula') {
                            foreach ($tableOps as $oi => $opn) {
                                echo "<" . $tableOps[$oi] . "><status>Table Structure has been modified by table owner ~:|~</status></" . $tableOps[$oi] . ">";
                                unset($tableOps[$oi]);
                            }
                        } elseif ($oid == 'renamecolName') {
                            foreach ($tableOps as $oi => $opn) {
                                echo "<" . $tableOps[$oi] . "><status>Table Structure has been modified by table owner ~:|~</status></" . $tableOps[$oi] . ">";
                                unset($tableOps[$oi]);
                            }
                        } elseif ($oid == 'permitColUsers') {
                            foreach ($tableOps as $oi => $opn) {
                                echo "<" . $tableOps[$oi] . "><status>Table Structure has been modified by table owner ~:|~</status></" . $tableOps[$oi] . ">";
                                unset($tableOps[$oi]);
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($tableOps as $oi => $opn) {
                if ($opn == 'updateCell') {
                    foreach ($dbtu as $key => &$value) {
                        if ($key == 'op') {
                            foreach ($value as $oid => $op) {
                                if ($oid == 'delRow') {
                                    $sAUVO = json_decode($_POST['sAUVO'], TRUE);
                                    foreach ($sAUVO as $tn => &$table) {
                                        if ($tn == $dbTable) {
                                            foreach ($table as $rid => &$row) {
                                                foreach ($op as $drn => $info) {
                                                    if ($rid == $drn) {
                                                        unset($table[$rid]);
                                                        $modified = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($modified)
                                        $_POST['sAUVO'] = json_encode($sAUVO);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

//Execute table operation...
    $liveDBTable = getLiveTable($dbTable);
    if ($liveDBTable or $tableOps[0] == 'createTable') {
        $authorizeTable = $liveDBTable['usersData'][$_SESSION['uid']]['fc'] ? TRUE : FALSE;
        if ($authorizeTable) {
            if ($liveDBTable['adminTable']) {
                require "$root/lib/adminScripts/db_login.php";
            }
        }
        for ($toc = 0; $toc < count($tableOps); $toc++) {
            $tableOperation = $tableOps[$toc];
            echo "<" . $tableOperation . ">";
            switch ($tableOperation) {
                case 'copyTable':
                    $nTable = $_POST['name'];
                    $tableAllowed = tableAllowed($nTable);
                    if ($tableAllowed) {
                        $so=$_POST['onlyStructure']=='on'?true:FALSE;
                        foreach ($liveDBTable['cells']['tHR'] as $cid => $col) {
                            if($cid=='index'){
                                $maxRs='3';
                            }else{
                                $columns[]=$cid." ".$col['Type']." ".($col['Null']=="NO"?"NOT NULL":"NULL").($col['Key']=='PRI'?" PRIMARY KEY":($col['Key']=='UNI'?" UNIQUE":"")).($col['Default']?" DEFAULT ".$col['Default']:"").($col['Extra']?" ".$col['Extra']:"");
                            }
                        }
                        $columns=  implode(",", $columns);
                        $clonet = getTableFromFile($dbTable);
                        $tp=  getTableFromFile($nTable);
                        if ($so) {
                            $tp=array();
                            $tp['tHR'] = $clonet['tHR'];
                            putTableInFile($tp, $nTable);
                        } else {
                            putTableInFile($clonet, $nTable);
                        }
                        $tableOps[]='createTable';
                    } else {
                        echo "<status>U r not allowed to create table with this name</status>";
                    }
                    break;
                case 'updateFormula':
                    $ci = $_POST['fColIndex'];
                    $ri = $_POST['fRowIndex'];
                    $fsKey = $_POST['fsKey'];
                    if ($authorizeTable or ($liveDBTable['usersData'][$_SESSION['uid']]['cells'][$ri][$ci]['sKey'] and $liveDBTable['usersData'][$_SESSION['uid']]['cells'][$ri][$ci]['sKey'] == $fsKey)) {
                        $f = $_POST['formula'];
                        if (strlen($f) < 100000) {
                            $priCol = $liveDBTable['priCol'];
                            $tp = getTableFromFile($dbTable);
                            unset($tp[$ri][$ci]['style']['oid']);
                            unset($tp[$ri][$ci]['style']['ts']);
                            foreach ($tp[$ri][$ci]['style'] as $prop => $value) {
                                $sua[$ri][$ci]['style'][$prop] = $value;
                            }
                            $dbtUpdate['tables'][$dbTable]['cells'][$ri][$ci]['style']['oid'] = $tp[$ri][$ci]['style']['oid'] = $_SESSION['oid'];
                            $dbtUpdate['tables'][$dbTable]['cells'][$ri][$ci]['style']['ts'] = $tp[$ri][$ci]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                            $dbtUpdate['tables'][$dbTable]['cells'][$ri][$ci]['f']['f'] = $tp[$ri][$ci]['f']['f'] = $f;
                            $pSCs = $tp[$ri][$ci]['f']['sCs'];
                            $dbtUpdate['tables'][$dbTable]['cells'][$ri][$ci]['f']['ts'] = $tp[$ri][$ci]['f']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                            $dbtUpdate['tables'][$dbTable]['cells'][$ri][$ci]['f']['oid'] = $tp[$ri][$ci]['f']['oid'] = $_SESSION['oid'];
                            $sciri = $ci . "," . ($ri != 'tHR' ? ($cpid = $liveDBTable['cells'][$ri][$liveDBTable['priCol']]['innerHTML']) : "");

                            //Add or remove changed effective element from changed src elements
                            /**/
                            for ($i = 0; $i < count($pSCs); $i++) {
                                $c = explode(",", $pSCs[$i]);
                                if (!$c[2]) {
                                    $aPSCs[$dbTable][] = $c;
                                } else {
                                    $aPSCs[$c[2]][] = $c;
                                }
                            }
                            putTableInFile($tp, $dbTable);
                            closeLiveTable($dbTable, $liveDBTable);
                            $sCs = computeSourceCells($f, $dbTable, $ci, $ri, $cpid, $aPSCs, $sciri, $dbtUpdate);
                            $liveDBTable = getLiveTable($dbTable);
                            $tp = getTableFromFile($dbTable);
                            $dbtUpdate['tables'][$dbTable]['cells'][$ri][$ci]['f']['sCs'] = $tp[$ri][$ci]['f']['sCs'] = $sCs;
                            $sua = array();
                            if ($ri == 'tHR') {
                                foreach ($tp as $rid => $row) {
                                    if (count($tp[$rid][$ci]['style']) > 2) {
                                        unset($tp[$rid][$ci]['style']['ts']);
                                        unset($tp[$rid][$ci]['style']['oid']);
                                        foreach ($tp[$rid][$ci]['style'] as $prop => $value) {
                                            $sua[$rid][$ci]['style'][$prop] = $value;
                                        }
                                        $dbtUpdate['tables'][$dbTable]['cells'][$rid][$ci]['style'] = $tp[$rid][$ci]['style'] = array();
                                        $dbtUpdate['tables'][$dbTable]['cells'][$rid][$ci]['style']['ts'] = $tp[$rid][$ci]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                                        $dbtUpdate['tables'][$dbTable]['cells'][$rid][$ci]['style']['oid'] = $tp[$rid][$ci]['style']['oid'] = $_SESSION['oid'];
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
                                            $sua[$ri][$cid]['style'][$prop] = $value;
                                        }
                                        $dbtUpdate['tables'][$dbTable]['cells'][$ri][$cid]['style'] = $tp[$ri][$cid]['style'] = array();
                                        $dbtUpdate['tables'][$dbTable]['cells'][$ri][$cid]['style']['oid'] = $tp[$ri][$cid]['style']['oid'] = $_SESSION['oid'];
                                        $dbtUpdate['tables'][$dbTable]['cells'][$ri][$cid]['style']['ts'] = $tp[$ri][$cid]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                                    }
                                }
                            }
                            $sua[$ri][$ci]['style'] = array();
                            if ($tp != 'null') {
                                $tpf[$dbTable] = $tp;
                                $dbtUpdate['tables'][$dbTable]['op']['updateFormula'][$ri][$ci]['formula'] = $f;
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
                        if (!($invalidColName = preg_match("/-/", $newName))) {
                            $type = sqlinjection_free($_POST['type']);
                            $size = stripslashes(sqlinjection_free($_POST['size']));
                            $size = $size ? "(" . $size . ")" : "";
                            $notNull = sqlinjection_free($_POST['notNull']);
                            $notNull = ($notNull == 'true') ? ' NOT NULL' : ' NULL';
                            $default = sqlinjection_free($_POST['dfault']);
                            $default = $default ? " DEFAULT '" . $default . "'" : '';
                            $query = "SHOW FULL COLUMNS FROM `" . $dbTable . "` WHERE Field = '" . $colName . "'";
                            $result = mysql_query($query, $dbc);
                            $comment = mysql_result($result, 0, 'Comment');
                            $comment = ($comment) ? " COMMENT '" . $comment . "'" : '';
                            $query = "ALTER TABLE `" . $dbTable . "` CHANGE `" . $colName . "` `" . $newName . "` " . $type . $size . $notNull . $comment . $default;
                            $result = mysql_query($query, $dbc);
                            $error1 = mysql_error($dbc);
                            if (!$error1) {
                                $dbtUpdate['tables'][$dbTable]['op']['renamecolName'][$colName]['newName'] = $newName;
                                $dbtUpdate['tables'][$dbTable]['op']['renamecolName'][$colName]['Type'] = $type;
                                $dbtUpdate['tables'][$dbTable]['op']['renamecolName'][$colName]['Size'] = $_POST['size'];
                                $dbtUpdate['tables'][$dbTable]['op']['renamecolName'][$colName]['Null'] = $notNull == ' NULL' ? 'YES' : 'NO';
                                $dbtUpdate['tables'][$dbTable]['op']['renamecolName'][$colName]['Default'] = $_POST['dfault'];
                                echo '<status>success</status>';
                            } else {
                                echo '<status>' . $error1 . '</status>';
                            }
                        } else {
                            echo '<status>Invalid column name %|</status>';
                        }
                    } else {
                        echo '<status>U r not authorized to edit table.</status>';
                    }
                    break;
                case 'renametableName':
                    if ($authorizeTable) {
                        $newName = $_POST['newName'];
                        $query = "RENAME TABLE `" . $dbTable . "` TO `" . $newName . "` ";
                        $result = mysql_query($query, $dbc);
                        $error1 = mysql_error($dbc);
                        if (!$error1) {
                            $dbtUpdate['tables'][$dbTable]['op']['renametableName']['newName'] = $newName;
                            $exec = exec("mv $root/dbTableData/$dbTable $root/dbTableData/$newName");
                            $_SESSION['tables'][$newName] = &$_SESSION['tables'][$dbTable];
                            unset($_SESSION['tables'][$dbTable]);
                            echo '<status>success</status>';
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
                        $size = $maxSize;
                        $maxSize = $maxSize != null ? "(" . $maxSize . ")" : "";
                        $insertAfter = sqlinjection_free($_POST['insertAfter']);
                        $notNull = sqlinjection_free($_POST['notNull']) == 'YES' ? ' NOT NULL' : ' NULL';
                        $default = sqlinjection_free($_POST['dfault']);
                        $default = $default != NULL ? " DEFAULT '" . $default . "'" : "";
                        $query = "ALTER TABLE `" . $dbTable . "` ADD `" . $columnName . "` " . $type . $maxSize . $notNull . $default . " AFTER `" . $insertAfter . "`";
                        $result = mysql_query($query, $dbc);
                        $error1 = mysql_error($dbc);
                        if (!$error1) {
                            echo "<status>success</status>";
                            $dbtUpdate['tables'][$dbTable]['op']['insColumn'][$columnName]['after'] = $insertAfter;
                            $dbtUpdate['tables'][$dbTable]['cells']['tHR'][$columnName]['Type'] = $type;
                            $dbtUpdate['tables'][$dbTable]['cells']['tHR'][$columnName]['Size'] = $size;
                            $dbtUpdate['tables'][$dbTable]['cells']['tHR'][$columnName]['Null'] = $_POST['nutNull'];
                            $dbtUpdate['tables'][$dbTable]['cells']['tHR'][$columnName]['Default'] = $_POST['dfault'];
                            $dbtUpdate['tables'][$dbTable]['cells']['tHR'][$columnName]['Key'] = '';
                            if (!$liveDBTable['usersData'][$_SESSION['uid']]['fc']) {
                                echo "<sKeys>";
                                foreach ($liveDBTable['cells'] as $i => $row) {
                                    $rand = rand();
                                    $dbtUpdate['tables'][$dbTable]['cells'][$i][$columnName]['sKey'] = $rand;
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
                            $dbtUpdate['tables'][$dbTable]['op']['delColumn']['columnName'] = $columnName;
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
                        $comment = $liveDBTable['cells']['tHR'][$colName]['Comment'];
                        $rq = $liveDBTable['owner'];
                        $type = $liveDBTable['cells']['tHR'][$colName]['Type'];
                        $null = $liveDBTable['cells']['tHR'][$colName]['Null'];
                        $key = $liveDBTable['cells']['tHR'][$colName]['Key'];
                        $default = $liveDBTable['cells']['tHR'][$colName]['Default'];
                        $extra = $liveDBTable['cells']['tHR'][$colName]['Extra'];
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
                                $op = array('CNG' => $dbTable . ':' . $colName . ':CA,NORMAL,' . $nmStr . ',w' . $liveDBTable['owner']);
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
                        $query = "ALTER TABLE `" . $dbTable . "` CHANGE `" . $colName . "` `" . $colName . "` " . $type . $qn . $qd . $qe . $qc;
                        $result = mysql_query($query, $dbc);
                        $error2 = mysql_error($dbc);
                        $suc = true;
                        if (!$error1 and !$error2) {
                            $dbtUpdate['tables'][$dbTable]['cells']['tHR'][$colName]['Comment'] = $comment;
                            $dbtUpdate['tables'][$dbTable]['op']['permitColUsers'][$colName] = $comment;
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
                    $tableAllowed = tableAllowed($nTable | $dbTable);
                    if ($tableAllowed) {
                        $columns = $columns?$columns:stripslashes(sqlinjection_free($_POST['columns']));
                        $maxRs = $maxRs?$maxRs:sqlinjection_free($_POST['maxRs']);
                        $nTable = $nTable?$nTable:sqlinjection_free($_POST['dbTable']);
                        $asObj = sqlinjection_free($_POST['role']);
                        if ($asObj == 'INDIVIDUAL') {
                            $asObj = 'u' . $_SESSION['uid'];
                        } else {
                            $asObj = 'o' . $asObj;
                        }
                        $comment = " COMMENT '{w" . $asObj . ",{*}}'";
                        $query = "CREATE TABLE " . $nTable . "(`index` INT(" . $maxRs . ") UNIQUE AUTO_INCREMENT" . $comment . ", " . $columns . ") COMMENT = 'al:" . sqlinjection_free($_SESSION['function'][$_POST['role']]['aL']) . ",o:" . substr($asObj, 1) . "'";
                        $result = mysql_query($query, $dbc);
                        $error1 = mysql_error($dbc);
                        if (!$error1) {
                            $tp = getTableFromFile($nTable);
                            $tp['tHR']['index']['tableName'] = $nTable;
                            putTableInFile($tp, $nTable);
                            echo '<status>success</status>';
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
                            exec("rm $root/dbTableData/$dbTable");
                            echo '<status>success</status>';
                            $dbtUpdate['tables'][$dbTable]['op']['delTable'] = true;
                        } else {
                            echo '<status>' . $error1 . '</status>';
                        }
                    }
                    break;
                case 'delRow':
                    if ($authorizeTable) {
                        $rowIndex = sqlinjection_free($_POST['rowIndex']);
                        $query = "DELETE FROM `" . $dbTable . "` WHERE `index` = '" . $rowIndex . "'";
                        $result = mysql_query($query, $dbc);
                        $error1 = mysql_error($dbc);
                        if (!$error1) {
                            $dbtUpdate['tables'][$dbTable]['op']['delRow'][$rowIndex]['oid'] = $_SESSION['oid'];
                            $dbtUpdate['tables'][$dbTable]['op']['delRow'][$rowIndex]['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                            $tp = getTableFromFile($dbTable);
                            unset($tp[$rowIndex]);
                            putTableInFile($tp, $dbTable);
                            echo '<status>success</status>';
                        } else {
                            echo '<status>' . $error1 . '</status>';
                        }
                    }
                    break;
                case 'updateCell':
                    closeLiveTable($dbTable, $liveDBTable);
                    $sAUVO = json_decode($_POST['sAUVO'], TRUE);
                    if (count($sAUVO) == 0) {
                        if ($sua and $dbTable) {
                            $tn = $dbTable;
                            unset($row);
                            foreach ($sua as $rid => &$row) {
                                unset($cell);
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
                    }
                    foreach ($sAUVO as $tn => &$table) {
                        $liveDBTable = getLiveTable($tn);
                        $authorizeTable = $liveDBTable['usersData'][$_SESSION['uid']]['fc'] ? TRUE : FALSE;
                        if ($liveDBTable) {
                            $tp = $tpf[$tn];
                            if (!$tp) {
                                $tp = getTableFromFile($tn);
                            }
                            if ($sua and $dbTable == $tn) {
                                unset($row);
                                foreach ($sua as $rid => &$row) {
                                    unset($cell);
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
                            unset($row);
                            foreach ($table as $rid => &$row) {
                                if (strpos($rid, 'newRow') > -1) {
                                    if ($authorizeTable) {
                                        $colIndexStr = array();
                                        $value = array();
                                        unset($cell);
                                        foreach ($row as $cid => &$cell) {
                                            $colIndexStr[] = $cid;
                                            $value[] = $cell['innerHTML'];
                                        }
                                        $colIndexStr = "`" . implode("`,`", $colIndexStr) . "`";
                                        $value = "\"" . implode("\",\"", $value) . "\"";
                                        $query = "INSERT INTO `" . $tn . "`(" . $colIndexStr . ") VALUES(" . $value . ")";
                                        $result = mysql_query($query, $dbc);
                                        $error1[$tn][$rid] = mysql_error($dbc);
                                        if (!$error1[$tn][$rid]) {
                                            $nrIndex[$tn][$rid] = mysql_insert_id($dbc);
                                            $nRowArr[$nrIndex[$tn][$rid]] = $row;
                                            unset($cell);
                                            foreach ($row as $cid => &$cell) {
                                                $modified = false;
                                                foreach ($cell as $pid => $prop) {
                                                    if ($pid != 'innerHTML' && $pid != 'sKey') {
                                                        $tp[$nrIndex[$tn][$rid]][$cid]['style'][$pid] = $prop;
                                                        $modified = true;
                                                    }
                                                }
                                                if ($modified) {
                                                    $tp[$nrIndex[$tn][$rid]][$cid]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                                                    $tp[$nrIndex[$tn][$rid]][$cid]['style']['oid'] = $_SESSION['oid'];
                                                }
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
                                    unset($cell);
                                    foreach ($row as $cid => &$cell) {
                                        if ($authorizeTable or ($liveDBTable['usersData'][$_SESSION['uid']]['cells'][$rid][$cid]['sKey'] and $cell['sKey'] == $liveDBTable['usersData'][$_SESSION['uid']]['cells'][$rid][$cid]['sKey'])) {
                                            if (isset($cell['innerHTML']))
                                                $ups[] = "`" . $cid . "`=" . ($cell['innerHTML'] ? "'" . $cell['innerHTML'] . "'" : 'null');
                                        } else {
                                            $cell['dbTableExecuterError'] = "U r not authorized to edit the cell ~&amp;|~";
                                        }
                                    } $ups = implode(",", $ups);
                                    if ($ups != '') {
                                        $query = "UPDATE " . $tn . " SET " . $ups . " WHERE `index`='" . $rid . "'";
                                        $result = mysql_query($query, $dbc);
                                        $error1[$tn][$rid] = mysql_error($dbc);
                                    }
                                    if (!$error1[$tn][$rid]) {
                                        unset($cell);
                                        foreach ($row as $cid => &$cell) {
                                            if (!$cell['dbTableExecuterError']) {
                                                $modified = false;
                                                foreach ($cell as $pid => $prop) {
                                                    if ($pid != 'innerHTML' && $pid != 'sKey') {
                                                        $tp[$rid][$cid]['style'][$pid] = $prop;
                                                        $modified = true;
                                                    }
                                                }
                                                if ($modified) {
                                                    $tp[$rid][$cid]['style']['ts'] = strftime("%Y-%m-%d %H:%M:%S");
                                                    $tp[$rid][$cid]['style']['oid'] = $_SESSION['oid'];
                                                }
                                            }
                                        }
                                    } else {
                                        $row['dbTableExecuterError'] = $error1[$tn][$rid];
                                    }
                                }
                            }
                            if ($tp) {
                                putTableInFile($tp, $tn);
                            } else {
                                echo '<' . $tn . 'status>a complex error occured, try to figure it out by urself or contact the ferry captain ~:s~</' . $tn . 'status>';
                            }
                            unset($row);
                            foreach ($table as $rid => &$row) {
                                if (!$row['dbTableExecuterError']) {
                                    if ($row['dbTableExecuterNRIndex']) {
                                        $rid = $row['dbTableExecuterNRIndex'];
                                        $dbtUpdate['tables'][$tn]['cells'][$rid]['index']['innerHTML'] = $rid;
                                    }
                                    unset($cell);
                                    foreach ($row as $cid => &$cell) {
                                        if ($cid != 'dbTableExecuterNRIndex') {
                                            if (!$cell['dbTableExecuterError']) {
                                                foreach ($cell as $pid => $prop) {
                                                    if ($pid != 'innerHTML' && $pid != 'sKey' && $pid != 'ts' && $pid != 'oid') {
                                                        $dbtUpdate['tables'][$tn]['cells'][$rid][$cid]['style'][$pid] = $prop;
                                                    } else if ($pid != 'sKey') {
                                                        $dbtUpdate['tables'][$tn]['cells'][$rid][$cid][$pid] = $prop;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            echo '<status>Table does not exist. It may be deleted or renamed by its owner ~:|~</status>';
                        }
                        if ($dbtUpdate['tables'][$tn] && $tn != $dbTable) {
                            $udn = count($liveDBTable['dbtUpdates']);
                            $liveDBTable['dbtUpdates'][$udn]['data'] = $dbtUpdate['tables'][$tn];
                            $liveDBTable['dbtUpdates'][$udn]['updatedBy'] = $_SESSION['uid'];
                            $liveDBTable['dbtUpdates'][$udn]['updatedOn'] = strftime("%Y-%m-%d %H:%M:%S");
                            updateLiveTable($liveDBTable, $dbtUpdate['tables'][$tn], FALSE);
                        }
                        closeLiveTable($tn, $liveDBTable);
                    }
                    echo '<status>success</status>';
                    echo '<sAUVO>' . json_encode($sAUVO, JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) . '</sAUVO>';
                    $liveDBTable = getLiveTable($dbTable);
                    $authorizeTable = $liveDBTable['usersData'][$_SESSION['uid']]['fc'] ? TRUE : FALSE;
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
                    }
                    break;
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
                            $queryts = "CREATE TABLE " . $nTable . "(`index` INT(" . $maxRs . ") UNIQUE," . $tcol . ")" . $tcom;
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                            $queryu = "CREATE TABLE " . $nTable . "(`index` INT(" . $maxRs . ") UNIQUE," . $ucol . ")" . $ucom;
                            $resultu = mysql_query($queryu, $uidLink);
                            $erroru = mysql_error($uidLink);
                            if (!$error2) {
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
                        $queryts = "RENAME TABLE  `" . $dbTable . "` TO `" . $dbTable . "_del`";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        if (preg_match('/already exists$/', $errorts)) {
                            $queryts = "DROP TABLE `" . $dbTable . "_del`";
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                            $queryts = "RENAME TABLE `" . $dbTable . "` TO `" . $dbTable . "_del`";
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                        }
                        if (!$errorts) {
                            $queryts = "ALTER TABLE `" . $dbTable . "_del` COMMENT = '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                        }
                        $queryu = "RENAME TABLE `" . $dbTable . "` TO `" . $dbTable . "_del`";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        if (preg_match('/already exists$/', $erroru)) {
                            $queryu = "DROP TABLE `" . $dbTable . "_del`";
                            $resultu = mysql_query($queryu, $uidLink);
                            $erroru = mysql_error($uidLink);
                            $queryu = "RENAME TABLE  `" . $dbTable . "` TO  `" . $dbTable . "_del`";
                            $resultu = mysql_query($queryu, $uidLink);
                            $erroru = mysql_error($uidLink);
                        }
                        if (!$erroru) {
                            $queryu = "ALTER TABLE  `" . $dbTable . "_del`  COMMENT = '" . $_SESSION['oid'] . "'";
                            $resultu = mysql_query($queryu, $uidLink);
                            $erroru = mysql_error($uidLink);
                        }
                        if (!($error2 and $error3)) {
                            $queryts = "UPDATE  `admintable` SET  `table` = now() WHERE  `index` = '" . $aTIndex . "' LIMIT 1";
                            $resultts = mysql_query($queryts, $timestampLink);
                            $errorts = mysql_error($timestampLink);
                            $queryu = "UPDATE `admintable` SET `table` = '" . $_SESSION['oid'] . "' WHERE `index` = '" . $aTIndex . "' LIMIT 1";
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
                        $queryts = "UPDATE `" . $dbTable . "` SET `" . $_POST['fColumn'] . "` = now() WHERE `index` = '" . $rowIndex . "' LIMIT 1";
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
                                        $queryts = "UPDATE " . $tn . " SET " . $upts . " WHERE `index`='" . $rid . "'";
                                        $resultts = mysql_query($queryts, $timestampLink);
                                        $errorts = mysql_error($timestampLink);
                                        $queryu = "UPDATE " . $tn . " SET " . $upus . " WHERE `index`='" . $rid . "'";
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
                                $queryts = "UPDATE `adminTable` SET `table`=now() WHERE `index`='" . $tIndex . "'";
                                $resultts = mysql_query($queryts, $timestampLink);
                                $errorts = mysql_error($timestampLink);
                                $queryu = "UPDATE `adminTable` SET `table`='" . $_SESSION['oid'] . "' WHERE `index`='" . $tIndex . "'";
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
                    if ($authorizeTable and !$invalidColName) {
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
                        $queryts = "ALTER TABLE `" . $dbTable . "` CHANGE `" . $colName . "` `" . $colName . "` TIMESTAMP NULL COMMENT '" . strftime('%Y-%m-%d %H:%M:%S') . "'";
                        $resultts = mysql_query($queryts, $timestampLink);
                        $errorts = mysql_error($timestampLink);
                        $queryu = "ALTER TABLE  `" . $dbTable . "` CHANGE  `" . $colName . "`  `" . $colName . "` INT(13) NULL COMMENT '" . $_SESSION['oid'] . "'";
                        $resultu = mysql_query($queryu, $uidLink);
                        $erroru = mysql_error($uidLink);
                        echo '<logstatus><errorts>' . $errorts . '</errorts><erroru>' . $erroru . '</erroru></logstatus>';
                    }
                    break;
            }
            echo "</" . $tableOperation . ">";
        }
        closeLiveTable($tn, $liveDBTable);
        unset($tdbtUpdate);
        foreach ($dbtUpdate['tables'] as $tid => &$tdbtUpdate) {
            $liveDBTable = getLiveTable($tid);
            if (count($liveDBTable['usersData']) > 1) {
                $udn = count($liveDBTable['dbtUpdates']);
                if ($liveDBTable['usersData'][$_SESSION['uid']]['authorization'] != "*") {
                    $tdbtUpdate['notByFullAuthority'] = TRUE;
                }
                $liveDBTable['dbtUpdates'][$udn]['data'] = $tdbtUpdate;
                $liveDBTable['dbtUpdates'][$udn]['data']['swallowedBy'][$_SESSION['uid']] = true;
                $liveDBTable['dbtUpdates'][$udn]['updatedBy'] = $_SESSION['uid'];
                $liveDBTable['dbtUpdates'][$udn]['updatedOn'] = strftime("%Y-%m-%d %H:%M:%S");
            }
            updateLiveTable($liveDBTable, $tdbtUpdate, TRUE);
            closeLiveTable($tid, $liveDBTable);
        }
    }
    /* foreach ($idbtUpdate['tables'] as $utn => $dbtu) {
      foreach ($dbtu as $key => &$value) {
      if ($key == 'cells') {
      foreach ($value as $rid => &$row) {
      foreach ($row as $cid => &$cell) {
      foreach ($cell as $pid => $prop) {
      if (!$dbtUpdate['tables'][$utn]['cells'][$rid][$cid][$pid]) {
      $dbtUpdate['tables'][$utn]['cells'][$rid][$cid][$pid] = $prop;
      }
      }
      }
      }
      }
      if ($key == 'op') {
      foreach ($value as $oid => $op) {
      foreach ($op as $info => $in) {
      $dbtUpdate['tables'][$utn][$key][$oid][$info] = $in;
      }
      }
      }
      }
      }
      $dbtUpdate = $dbtUpdate ? $dbtUpdate : array("tables" => array());
      $dbtUpdate = json_encode($dbtUpdate, JSON_HEX_QUOT || JSON_HEX_APOS); */
    $idbtUpdate = $idbtUpdate ? $idbtUpdate : array("tables" => array());
    $idbtUpdate = json_encode($idbtUpdate, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);
    echo "<status>success</status>";
    echo "<dbtUpdate>" . $idbtUpdate . "</dbtUpdate>";
}
echo '</dbTableExecuter>';
?>