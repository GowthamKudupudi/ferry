<?php

/* Author: Gowtham */
require 'authorize.php';
require '../inc.php';
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store', false);
echo '<?xml version="1.0" encoding="UTF-8"?><grpManager>';
$gids = explode(',', $_POST['gids']);
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
    $gop[$op[$i]['op']] = $op[$i]['params'];
}
for ($j = 0; $j < count($gids); $j++) {
    $gE = groupExe($gids[$j], NULL, $gop);
    echo '<grp><g>' . $gids[$j] . '</g><mems>' . implode(',', $gE['members']) . '</mems></grp>';
}
if($gE['ngid']){
    echo "<ngid>".$gE['ngid']."</ngid>";
}
echo '<status>success</status>';
echo '</grpManager>';
?>
