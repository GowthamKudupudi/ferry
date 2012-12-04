<?php

/* Author: Gowtham */
//2012-03-19 11:18:30
require 'authorize.php';
require '../inc.php';
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<?xml version="1.0" encoding="UTF-8"?><taskmgr>';
$tids = explode(',', $_POST['tids']);
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
    if ($ops[$i] == 'CNT') {
        $top[$op[$i]['op']] = array('work' => $_POST['work'], 'type' => $_POST['type'], 'target' => $_POST['target'], 'worker' => $_POST['worker'], 'sst' => $_POST['sst'], 'set' => $_POST['set']);
    } else {
        $top[$op[$i]['op']] = $op[$i]['params'];
    }
}
for ($j = 0; $j < count($tids); $j++) {
    $tE = taskExe($tids[$j], $top);
    echo '<tsk><g>' . $tids[$j] . '</g><mems>' . implode(',', $tE['members']) . '</mems></tsk>';
}
if ($tE['ntid']) {
    echo "<ntid>" . $tE['ntid'] . "</ntid>";
}
echo '<status>success</status>';
echo '</taskmgr>';
?>
