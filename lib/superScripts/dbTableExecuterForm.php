<?php
require 'authorize.php';
require 'db_login.php';
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once "$root/lib/inc.php";

//check table Authority
$userAdminLevel = $_SESSION['adminLevel'];
if (domesticSlave($userAdminLevel, 'Zz9')) {
    $cQuery = sqlinjection_free($_GET['query']);
    if ($_POST['query']) {
        $cQuery = $_POST['query'];
    }
    $filters = split("[?:@$][?:@$]", $cQuery);
    if (count($filters) > 4) {
        echo 'bad query. Duplicate Operators ~&|~';
        die();
    }
    $dbTable = sqlinjection_free(trim($filters[0]));
    $dbTable = strtolower($dbTable);
    $cQuery = $dbTable . substr($cQuery, strlen($filters[0]));
    $cQuery = str_replace($filters[0], $dbTable, $cQuery);
    $rFilter = null;
    $cString = null;
    $sString = null;
    $filterCount = 0;
    $start = strlen($filters[0]);
    for ($i = 1; $i < count($filters); $i++) {
        if ($cQuery[strpos($cQuery, $filters[$i], $start) - 1] == '?' and $cQuery[strpos($cQuery, $filters[$i], $start) - 2] == '?' and !$rFilter) {
            $rFilter = $filters[$i];
        } elseif ($cQuery[strpos($cQuery, $filters[$i], $start) - 1] == ':' and $cQuery[strpos($cQuery, $filters[$i], $start) - 2] == ':' and !$cString) {
            $cString = $filters[$i];
        } elseif ($cQuery[strpos($cQuery, $filters[$i], $start) - 1] == '@' and $cQuery[strpos($cQuery, $filters[$i], $start) - 2] == '@' and !$sString) {
            $sString = $filters[$i];
            $matchCase = true;
            $wolS = true;
        } elseif ($cQuery[strpos($cQuery, $filters[$i], $start) - 1] == '$' and $cQuery[strpos($cQuery, $filters[$i], $start) - 2] == '$' and !$sString) {
            $sString = $filters[$i];
        } elseif ($cQuery[strpos($cQuery, $filters[$i], $start) - 1] == '@' and $cQuery[strpos($cQuery, $filters[$i], $start) - 2] == '$' and !$sString) {
            $sString = $filters[$i];
            $wolS = true;
        } elseif ($cQuery[strpos($cQuery, $filters[$i], $start) - 1] == '$' and $cQuery[strpos($cQuery, $filters[$i], $start) - 2] == '@' and !$sString) {
            $sString = $filters[$i];
            $matchCase = true;
        } else {
            echo 'More than one filter of single type. ~&|~';
            die();
        }
        $filterCount++;
        $start+=strlen($filters[$i]);
    }
    /* $dbtKey = ftok("$root/dbTableData/$dbTable", 'c');
      $dbtSemId = sem_get($dbtKey);
      $dbtShmId = shm_attach($dbtKey,1000000);
      $sa = sem_acquire($dbtSemId);
      $liveDBTable = shm_get_var($dbtShmId, $dbtKey); */
    $liveDBTable = getLiveTable($dbTable);
    $cQuery = str_replace('CCZZCC', ':', str_replace('ZZCCZZ', '?', $cQuery));
    if (!$liveDBTable) {
        $liveDBTable['liveD'] = FALSE;
        $query = "SHOW FULL COLUMNS FROM `" . $dbTable . "`";
        $result = mysql_query($query, $dbc);
        $error1 = mysql_error($dbc);
        if ($error1 and authorizeTransit($_SESSION['adminLevel'], 'Zz0')) {
            require '../adminScripts/db_login.php';
            $query = "SHOW FULL COLUMNS FROM `" . $dbTable . "`";
            $result = mysql_query($query, $dbc);
            $error1 = mysql_error($dbc) ? TRUE : FALSE;
            if (!$error1) {
                $liveDBTable['adminTable']=$adminTable = true;
            }
        }
        if (!$error1) {
            $fields_num = mysql_num_fields($result);
            $colCount = mysql_num_rows($result);
            $cc = $colCount;
            for ($i = 0; $i < $colCount; $i++) {
                $liveDBTable['cells']['tHR'][$f = $Field[$i] = mysql_result($result, $i, 'Field')]['Type'] = $Type[$i] = mysql_result($result, $i, 'Type');
                $liveDBTable['cells']['tHR'][$f]['Null'] = $Null[$i] = mysql_result($result, $i, 'Null');
                $liveDBTable['cells']['tHR'][$f]['Key'] = $Key[$i] = mysql_result($result, $i, 'Key');
                if ($Key[$i] == 'PRI') {
                    $priCol = $i;
                    $liveDBTable['priCol'] = $Field[$i];
                }
                $liveDBTable['cells']['tHR'][$f]['Default'] = $Default[$i] = mysql_result($result, $i, 'Default');
                $liveDBTable['cells']['tHR'][$f]['Extra'] = $Extra[$i] = mysql_result($result, $i, 'Extra');
                $liveDBTable['cells']['tHR'][$f]['Comment'] = $Comment[$i] = mysql_result($result, $i, 'Comment');
            }
            $Fiel = $Field;
            $Typ = $Type;
            $Nul = $Null;
            $Ke = $Key;
            $Defaul = $Default;
            $Extr = $Extra;
            $Commen = $Comment;
            $query = "SELECT `index` FROM `" . $dbTable . "` ORDER BY `index`";
            $result = mysql_query($query, $dbc);
            $error2 = mysql_error($dbc);
            $liveDBTable['tPs']['rowCount'] = $rowCount = mysql_num_rows($result);
            $tableAdminLevel = $Comment[0];
            for ($j = 0; $j < $rowCount; $j++) {
                $liveDBTable['cells'][$rInd[$j] = mysql_result($result, $j, 'index')]['index']['innerHTML'] = $rInd[$j];
            }
            $mems = authField($tableAdminLevel);
            foreach ($Field as $i => $col) {
                $mems['r']['ptable'][$col]['tHR'] = true;
            }
            $ms[0] = $mems;
            $query = "SHOW TABLE STATUS LIKE  '" . $dbTable . "'";
            $result = mysql_query($query, $dbc);
            $liveDBTable['com'] = $com = mysql_result($result, '0', 'Comment');
            $com = explode(",", $com);
            foreach ($com as $key => $value) {
                $al = explode(":", $value);
                if ($al[0] == 'al') {
                    $adl = trim($al[1]);
                    $sm = superMaster($_SESSION['adminLevel'], $adl);
                } else if ($al[0] == 'o') {
                    $o = $al[1];
                    $own = $adl != "" ? authObject($o) : $_SESSION['uid'] == $o ? true : false;
                    $liveDBTable['owner'] = $adl != "" ? "o" . $o : "u" . $_SESSION['uid'];
                }
            }
            if (authorizeTransit($_SESSION['adminLevel'], 'Zz0') or authorizeTransit($_SESSION['adminLevel'], 'Zs0') or $own) {
                $authorizeTransit = TRUE;
                $liveDBTable['usersData'][$_SESSION['uid']]['fc'] = TRUE;
                $fc = TRUE;
                $rc = $rowCount;
            } elseif ($tableAdminLevel) {
                $mems['r']['authRows'] = array_unique(array_merge($mems['r']['authRows'], $mems['w']['authRows']));
                if ($mems['w']['authRows'][0] == '*') {
                    $authorizeTransit = TRUE;
                    $liveDBTable['usersData'][$_SESSION['uid']]['fc'] = TRUE;
                    $fc = TRUE;
                    $rc = $rowCount;
                } elseif ($mems['r']['authRows'] != NULL) {
                    for ($i = 1; $i < $colCount; $i++) {
                        $k = 0;
                        for ($j = 0; $j < $rowCount; $j++) {
                            if ($mems[$i]['r']['authRows'][$k]) {
                                //collect deleted rows
                                if ($mems['r']['authRows'][$k] < $rInd[$j]) {
                                    $dr[] = $ms[$i]['r']['authRows'][$k];
                                    $k++;
                                }
                                if ($rInd[$j] == $mems['r']['authRows'][$k]) {
                                    $mems['r']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                    $mems['r']['ptable']['index'][$rInd[$j]] = TRUE;
                                    $mems['r']['ptable'][$Field[$priCol]][$rInd[$j]] = TRUE;
                                    $k++;
                                    if ($k > $rc) {
                                        $rc = $k;
                                    }
                                }
                                if ($rInd[$j] == $mems['w']['authRows'][$l]) {
                                    $mems['w']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                    $mems['w']['ptable']['index'][$rInd[$j]] = TRUE;
                                    $l++;
                                }
                            }
                        }
                    }
                    $authorizeTransit = TRUE;
                }
                if (!$fc) {
                    for ($i = 1; $i < $colCount; $i++) {
                        if ($Comment[$i] != '') {
                            $ms[$i] = authField($Comment[$i]);
                            $sar = $ms[$i]['w']['authRows'][0] == "*" ? array("*") : array_unique(array_merge($ms[$i]['r']['authRows'], $ms[$i]['w']['authRows']));
                            sort($sar);
                            $ms[$i]['r']['authRows'] = $sar;
                            $k = 0;
                            $l = 0;
                            if (count($ms[$i]['r']['authRows']) > 0) {
                                if ($ms[$i]['r']['authRows'][0] == '*') {
                                    for ($j = 0; $j < $rowCount; $j++) {
                                        $mems['r']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                        $mems['r']['ptable']['index'][$rInd[$j]] = TRUE;
                                        $mems['r']['ptable'][$Field[$priCol]][$rInd[$j]] = TRUE;
                                        if ($k > $rc) {
                                            $rc = $k;
                                        }
                                        $k++;
                                    }
                                    if ($ms[$i]['w']['authRows'][0] == '*') {
                                        for ($j = 0; $j < $rowCount; $j++) {
                                            $mems['w']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                            $mems['w']['ptable']['index'][$rInd[$j]] = TRUE;
                                            $l++;
                                        }
                                    } else {
                                        for ($j = 0; $j < $rowCount; $j++) {
                                            if ($rInd[$j] == $ms[$i]['w']['authRows'][$l] or $ms[$i]['w']['authRows'][0] == '*') {
                                                $mems['w']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                                $mems['w']['ptable']['index'][$rInd[$j]] = TRUE;
                                                $l++;
                                            }
                                        }
                                    }
                                } else {
                                    for ($j = 0; $j < $rowCount; $j++) {
                                        if ($ms[$i]['r']['authRows'][$k]) {
                                            //collect deleted rows
                                            while (isset($ms[$i]['r']['authRows'][$k]) and $ms[$i]['r']['authRows'][$k] < $rInd[$j]) {
                                                $dr[] = $ms[$i]['r']['authRows'][$k];
                                                $k++;
                                            }
                                            while (isset($ms[$ms[$i]['w']['authRows'][$l]]) and $ms[$i]['w']['authRows'][$l] < $rInd[$j]) {
                                                $l++;
                                            }
                                            if ($rInd[$j] == $ms[$i]['r']['authRows'][$k] or $ms[$i]['r']['authRows'][0] == '*') {
                                                $mems['r']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                                $mems['r']['ptable']['index'][$rInd[$j]] = TRUE;
                                                $mems['r']['ptable'][$Field[$priCol]][$rInd[$j]] = TRUE;
                                                if ($k > $rc) {
                                                    $rc = $k;
                                                }
                                                $k++;
                                            }
                                            if ($rInd[$j] == $ms[$i]['w']['authRows'][$l] or $ms[$i]['w']['authRows'][0] == '*') {
                                                $mems['w']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                                $mems['w']['ptable']['index'][$rInd[$j]] = TRUE;
                                                $l++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (count($mems['r']['ptable'][$Field[$i]]) == 0 and $i != $priCol and !$sm) {
                            unset($Fiel[$i]);
                            unset($Typ[$i]);
                            unset($Nul[$i]);
                            unset($Ke[$i]);
                            unset($Defaul[$i]);
                            unset($Extr[$i]);
                            unset($Commen[$i]);
                            unset($ms[$i]);
                            unset($mems['r']['ptable'][$Field[$i]]);
                        }
                    }
                    if ($dr) {
                        $dr = array_unique($dr);
                        $Comment = prunePermissions(array('delRows' => array('dbTable' => $dbTable, 'columns' => array('field' => $Field, 'type' => $Type, 'key' => $Key, 'null' => $Null, 'default' => $Default, 'extra' => $Extra, 'comments' => $Comment), 'rows' => $dr, 'ms' => $ms)), $liveDBTable);
                    }
                    $cc = count($Field);
                    if ($mems['r']['ptable'] != NULL) {
                        $authorizeTransit = TRUE;
                    }
                }
            }
        }
    } else {
        $liveDBTable['liveD'] = TRUE;
        $fields_num = mysql_num_fields($result);
        $colCount = count($liveDBTable['cells']['tHR']);
        $cc = $colCount;
        $i = 0;
        foreach ($liveDBTable['cells']['tHR'] as $cid => $column) {
            $Field[$i] = $cid;
            $Type[$i] = $column['Type'];
            $Null[$i] = $column['Null'];
            $Key[$i] = $column['Key'];
            if ($Key[$i] == 'PRI')
                $priCol = $i;
            $Default[$i] = $column['Default'];
            $Extra[$i] = $column['Extra'];
            $Comment[$i] = $column['Comment'];
            $i++;
        }
        $Fiel = $Field;
        $Typ = $Type;
        $Nul = $Null;
        $Ke = $Key;
        $Defaul = $Default;
        $Extr = $Extra;
        $Commen = $Comment;
        $rowCount = count($liveDBTable['cells']) - 1;
        $tableAdminLevel = $Comment[0];
        $j = 0;
        foreach ($liveDBTable['cells'] as $rid => &$row) {
            if ($rid != 'tHR') {
                $rInd[$j++] = $rid;
            }
        }
        $mems = authField($tableAdminLevel);
        foreach ($Field as $i => $col) {
            $mems['r']['ptable'][$col]['tHR'] = true;
        }
        $ms[0] = $mems;
        $com = $liveDBTable['com'];
        $com = explode(",", $com);
        foreach ($com as $key => $value) {
            $al = explode(":", $value);
            if ($al[0] == 'al') {
                $adl = trim($al[1]);
                $sm = superMaster($_SESSION['adminLevel'], $adl);
            } else if ($al[0] == 'o') {
                $o = $al[1];
                $own = $adl != "" ? authObject($o) : $_SESSION['uid'] == $o ? true : false;
            }
        }
        if (authorizeTransit($_SESSION['adminLevel'], 'Zz0') or authorizeTransit($_SESSION['adminLevel'], 'Zs0') or $own) {
            $authorizeTransit = TRUE;
            $liveDBTable['usersData'][$_SESSION['uid']]['fc'] = TRUE;
            $fc = TRUE;
            $rc = $rowCount;
        } elseif ($tableAdminLevel) {
            $mems['r']['authRows'] = array_unique(array_merge($mems['r']['authRows'], $mems['w']['authRows']));
            if ($mems['w']['authRows'][0] == '*') {
                $authorizeTransit = TRUE;
                $liveDBTable['usersData'][$_SESSION['uid']]['fc'] = TRUE;
                $fc = TRUE;
                $rc = $rowCount;
            } elseif ($mems['r']['authRows'] != NULL) {
                for ($i = 1; $i < $colCount; $i++) {
                    $k = 0;
                    for ($j = 0; $j < $rowCount; $j++) {
                        if ($ms[$i]['r']['authRows'][$k]) {
                            //collect deleted rows
                            if ($mems['r']['authRows'][$k] < $rInd[$j]) {
                                $dr[] = $ms[$i]['r']['authRows'][$k];
                                $k++;
                            }
                            if ($rInd[$j] == $mems['r']['authRows'][$k]) {
                                $mems['r']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                $mems['r']['ptable']['index'][$rInd[$j]] = TRUE;
                                $mems['r']['ptable'][$Field[$priCol]][$rInd[$j]] = TRUE;
                                $k++;
                                if ($k > $rc) {
                                    $rc = $k;
                                }
                            }
                            if ($rInd[$j] == $mems['w']['authRows'][$l]) {
                                $mems['w']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                $mems['w']['ptable']['index'][$rInd[$j]] = TRUE;
                                $l++;
                            }
                        }
                    }
                }
                $authorizeTransit = TRUE;
            }
            if (!$fc) {
                for ($i = 1; $i < $colCount; $i++) {
                    if ($Comment[$i] != '') {
                        $ms[$i] = authField($Comment[$i]);
                        $sar = $ms[$i]['w']['authRows'][0] == "*" ? array("*") : array_unique(array_merge($ms[$i]['r']['authRows'], $ms[$i]['w']['authRows']));
                        sort($sar);
                        $ms[$i]['r']['authRows'] = $sar;
                        $k = 0;
                        $l = 0;
                        if (count($ms[$i]['r']['authRows']) > 0) {
                            if ($ms[$i]['r']['authRows'][0] == '*') {
                                for ($j = 0; $j < $rowCount; $j++) {
                                    $mems['r']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                    $mems['r']['ptable']['index'][$rInd[$j]] = TRUE;
                                    $mems['r']['ptable'][$Field[$priCol]][$rInd[$j]] = TRUE;
                                    if ($k > $rc) {
                                        $rc = $k;
                                    }
                                    $k++;
                                }
                                if ($ms[$i]['w']['authRows'][0] == '*') {
                                    for ($j = 0; $j < $rowCount; $j++) {
                                        $mems['w']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                        $mems['w']['ptable']['index'][$rInd[$j]] = TRUE;
                                        $l++;
                                    }
                                } else {
                                    for ($j = 0; $j < $rowCount; $j++) {
                                        if ($rInd[$j] == $ms[$i]['w']['authRows'][$l] or $ms[$i]['w']['authRows'][0] == '*') {
                                            $mems['w']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                            $mems['w']['ptable']['index'][$rInd[$j]] = TRUE;
                                            $l++;
                                        }
                                    }
                                }
                            } else {
                                for ($j = 0; $j < $rowCount; $j++) {
                                    if ($ms[$i]['r']['authRows'][$k]) {
                                        //collect deleted rows
                                        while (isset($ms[$i]['r']['authRows'][$k]) and $ms[$i]['r']['authRows'][$k] < $rInd[$j]) {
                                            $dr[] = $ms[$i]['r']['authRows'][$k];
                                            $k++;
                                        }
                                        while (isset($ms[$i]['w']['authRows'][$l]) and $ms[$i]['w']['authRows'][$l] < $rInd[$j]) {
                                            $l++;
                                        }
                                        if ($rInd[$j] == $ms[$i]['r']['authRows'][$k] or $ms[$i]['r']['authRows'][0] == '*') {
                                            $mems['r']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                            $mems['r']['ptable']['index'][$rInd[$j]] = TRUE;
                                            $mems['r']['ptable'][$Field[$priCol]][$rInd[$j]] = TRUE;
                                            if ($k > $rc) {
                                                $rc = $k;
                                            }
                                            $k++;
                                        }
                                        if ($rInd[$j] == $ms[$i]['w']['authRows'][$l] or $ms[$i]['w']['authRows'][0] == '*') {
                                            $mems['w']['ptable'][$Field[$i]][$rInd[$j]] = TRUE;
                                            $mems['w']['ptable']['index'][$rInd[$j]] = TRUE;
                                            $l++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (count($mems['r']['ptable'][$Field[$i]]) == 1 and $i != $priCol and !$sm) {
                        unset($Fiel[$i]);
                        unset($Typ[$i]);
                        unset($Nul[$i]);
                        unset($Ke[$i]);
                        unset($Defaul[$i]);
                        unset($Extr[$i]);
                        unset($Commen[$i]);
                        unset($ms[$i]);
                        unset($mems['r']['ptable'][$Field[$i]]);
                    }
                }
                if ($dr) {
                    $dr = array_unique($dr);
                    $Comment = prunePermissions(array('delRows' => array('dbTable' => $dbTable, 'columns' => array('field' => $Field, 'type' => $Type, 'key' => $Key, 'null' => $Null, 'default' => $Default, 'extra' => $Extra, 'comments' => $Comment), 'rows' => $dr, 'ms' => $ms)), $liveDBTable);
                }
                $cc = count($Field);
                if (count($mems['r']['ptable']['index']) > 1) {
                    $authorizeTransit = TRUE;
                }
            }
        }
    }
} else {
    $userAuthorizationInfo = '<authorization>User not authorized for this tool.</authorization>';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>MySQL Table Viewer</title>
        <script type="text/javascript">
            dbTableExecuter.init=function(){
                dbTableExecuter.window=window;
                dbTableExecuter.dispArea=document.body;
            }
            window.initGadget=function(dispArea){
                
            }
            onload=dbTableExecuter.init;
        </script>
    </head>
    <body>
        <div id="dbTableExecuterBdy" class="gdgBody">
            <?php
            if (!$userAuthorizationInfo) {
                if ($result || $liveDBTable['liveD']) {
                    if (!$authorizeTransit and !$sm) {
                        echo "<span>U r not authorized to view the table ~:|~</span>";
                    } else {
                        include 'dbTableExecuterOpener.php';
                    }
                } else {
                    if (tableAllowed($dbTable)) {
                        include 'dbTableExecuterCreator.php';
                    } else {
                        echo "<span class='display' id='dbTableExecuter'>Table don exist n u r not authorized to create table with this name.</span></body></html>";
                    }
                }
            } else {
                echo $userAuthorizationInfo;
            }
            ?>
        </div>
    </body>
</html>

<?php
/* $spv=shm_put_var($dbtShmId, $dbtKey, $liveDBTable);
  $sr=sem_release($dbtSemId); */
closeLiveTable($dbTable, &$liveDBTable);
include db_logout . php
?>