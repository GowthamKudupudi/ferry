<?php

/* Author: Gowtham */
$root=realpath($_SERVER['DOCUMENT_ROOT']);
require "authorize.php";
require_once "$root/lib/inc.php";
$uTools = new DOMDocument();
//disables html error logging
libxml_use_internal_errors(FALSE);
@$uTools->loadHTMLFile('userTools.html');
$userAuthTools = new DOMDocument();
$userTools = $userAuthTools->createElement('userTools');
if (authorizeTransit($_SESSION['adminLevel'], 'Zz0')) {
    
}
if (domesticSlave($_SESSION['adminLevel'], 'Zz9')) {
    $dbTableExecuter = $uTools->getElementById('dbTableExecuterTool');
    $dbTableExecuter = $userAuthTools->importNode($dbTableExecuter, TRUE);
    $appendChild = $userTools->appendChild($dbTableExecuter);
    $inboxOpener = $uTools->getElementById('inboxOpener');
    $inboxOpener = $userAuthTools->importNode($inboxOpener, TRUE);
    $appendChild = $userTools->appendChild($inboxOpener);
    $chatTool = $uTools->getElementById('chatTool');
    $chatTool = $userAuthTools->importNode($chatTool, TRUE);
    $appendChild = $userTools->appendChild($chatTool);
    $keelCrafter = $uTools->getElementById('keelCrafter');
    $keelCrafter = $userAuthTools->importNode($keelCrafter, TRUE);
    $appendChild = $userTools->appendChild($keelCrafter);
}
if (domesticSlave($_SESSION['adminLevel'], 'Zz0')) {
    $dGTool = $uTools->getElementById('dataGrabberTool');
    $dGTool = $userAuthTools->importNode($dGTool, TRUE);
    $appendChild = $userTools->appendChild($dGTool);
    $dObTool = $uTools->getElementById('deobjectizeTool');
    $dObTool = $userAuthTools->importNode($dObTool, TRUE);
    $appendChild = $userTools->appendChild($dObTool);
}
$userAuthTools->appendChild($userTools);
$success = $userAuthTools->createElement('status', 'success');
$userTools->appendChild($success);
$userAuthTools->appendChild($userTools);
echo $userAuthTools->saveHTML();
libxml_clear_errors();
?>