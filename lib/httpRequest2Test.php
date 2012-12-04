<?php

require 'HTTP/Request2.php';
$url = 'https://collegedb2.ferryfair.com/lib/crap.html';
$r = new Http_Request2($url);
$r->setConfig(array("ssl_verify_peer"=>FALSE,"ssl_local_cert"=>"$root/ssl/collegedb2.ferryfair.com.cert"));
/* $r->setHeader(array(
  "Content-Type" => "application/x-www-form-urlencoded"
  ));
  $r->addPostParameter(array(
  'username' => $usrname,
  'ePass' => $ePass,
  'fullName' => $_POST['fullName']
  )); */
//$r->addCookie('PHPSESSID', 'bkq0t2vf2n9898t4q83jje7bp7');
//$r->addCookie('XDEBUG_SESSION', 'netbeans-xdebug');
try {
    $response = $r->send();
} catch (Exception $exc) {
    $es = $exc->getTraceAsString();
    $ets=$exc->__toString();
    $egc=$exc->getCode();
    $egl=$exc->getLine();
    $egm=$exc->getMessage();
    $egt=$exc->getTrace();
    $response = null;
    echo $es.$ets.$egc.$egl.$egm.$egt.$response;
}
$page = $response->getBody();
echo $page;
?>
