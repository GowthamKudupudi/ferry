<?php

/* Author: Gowtham */
//2012-03-19 11:18:30
require 'authorize.php';
require '../inc.php';
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<?xml version="1.0" encoding="UTF-8"?><objectmgr>';
$oids = explode(',', $_POST['oids']);
$ops = explode(':', $_POST['ops']);
for ($i = 0; $i < count($ops); $i++) {
    $j = 0;
    while ($ops[$i][$j] != '-' and $ops[$i][$j] != null) {
        $op[$i]['op'].=$ops[$i][$j];
        $j++;
    }
    if ($ops[$i][$j] == '-') {
        $j++;
        $op[$i]['params'] = substr($ops[$i], $j);
    }
    if ($ops[$i] == 'CNO') {
        $oop[$op[$i]['op']] = array('id' => $_POST['obns'], 'uid' => $_POST['uid'], 'adminLevel' => $_POST['objal'], 'type1' => $_POST['type1'], 'type2' => $_POST['type2'], 'function' => $_POST['fi'], 'description' => $_POST['description']);
    } else {
        $oop[$op[$i]['op']] = $op[$i]['params'];
    }
}
for ($j = 0; $j < count($oids); $j++) {
    if ($_POST['type2'] == 'STUDENT') {
        if ($_POST['obne'] and substr($_POST['obne'], 0, 8) == substr($_POST['obns'], 0, 8)) {
            $startRegNo = $_POST['obns'];
            $endRegNo = $_POST['obne'];
            $stuList[] = $_POST['obns'];
            $tens[] = null;
            for ($i = 0; $i < 16; $i++) {
                switch ($i) {
                    case 10 : $k = 'A';
                        break;
                    case 11 : $k = 'B';
                        break;
                    case 12 : $k = 'C';
                        break;
                    case 13 : $k = 'D';
                        break;
                    case 14 : $k = 'E';
                        break;
                    case 15 : $k = 'F';
                        break;
                    default : $k = $i;
                        break;
                }
                $tens[$i] = $k;
            }
            $i = 0;
            $k = 0;
            $stuList[0] = $startRegNo;
            while ($stuList[$i] != $endRegNo) {
                $i++;
                $j = ($i + 1) % 10;
                $stuList[$i] = substr($startRegNo, 0, 8) . $tens[$k] . $j;
                if ($j == 9) {
                    $k = (++$k) % 16;
                }
            }
            for ($i = 0; $i < count($stuList); $i++) {
                $oop['CNO']['id'] = $stuList[$i];
                $oE = objectExe($oids[$j], $oop);
                echo '<obj><g>' . $oids[$j] . '</g><mems>' . implode(',', $oE['members']) . '</mems></obj>';
                if ($oE['noid']) {
                    echo "<noid>" . $oE['noid'] . "</noid>";
                }
            }
        } else {
            $oE = objectExe($oids[$j], $oop);
            echo '<obj><g>' . $oids[$j] . '</g><mems>' . implode(',', $oE['members']) . '</mems></obj>';
            if ($oE['noid']) {
                echo "<noid>" . $oE['noid'] . "</noid>";
            }
        }
    } else {
        $oE = objectExe($oids[$j], $oop);
        echo '<obj><g>' . $oids[$j] . '</g><mems>' . implode(',', $oE['members']) . '</mems></obj>';
        if ($oE['noid']) {
            echo "<noid>" . $oE['noid'] . "</noid>";
        }
    }
}
echo '<status>success</status>';
echo '</objectmgr>';
?>
