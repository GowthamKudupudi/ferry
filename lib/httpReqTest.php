<?php

//Author: satya gowtham kudupudi
//Time: 2012-03-04 13:08:00

require 'HTTP/Request2.php';
$url = 'http://mail.ferryfair.com/PHPWebAdmin/index.php';
$r = new Http_Request2($url);
$r->setMethod(HTTP_Request2::METHOD_POST);
$r->setHeader(array(
    "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
    "Accept-Charset" => "ISO-8859-1,utf-8;q=0.7,*;q=0.3",
    "Accept-Encoding" => "gzip,deflate,sdch",
    "Accept-Language" => "en-US,en;q=0.8,te;q=0.6",
    "Cache-Control" => "max-age=0",
    "Connection" => "keep-alive",
    "Content-Length" => "355",
    "Content-Type" => "application/x-www-form-urlencoded"
));
$r->addPostParameter(array(
    'page' => 'background_account_save',
    'action' => 'edit',
    'domainid' => '1',
    'accountid' => '12',
    'accountaddress' => 'capt.roja',
    'accountpassword' => 'g2wjzxx1NXDeujPW',
    'accountmaxsize' => '100',
    'accountadminlevel' => '0',
    'accountactive' => '1',
    'vacationsubject' => '',
    'vacationmessage' => '',
    'vacationmessageexpiresdate' => '2012-03-04',
    'forwardaddress' => '',
    'SignaturePlainText' => '',
    'SignatureHTML' => '',
    'addomain' => '',
    'adusername' => '',
    'PersonFirstName' => '',
    'PersonLastName' => '',
));
$r->send();
$page = $r->getBody();
print_r($page);
?>
