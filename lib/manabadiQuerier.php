<?php

/* Author: Gowtham */
require 'HTTP/Request2.php';
$url = 'http://www.results.manabadi.co.in/KJNTUBTECH11012012.aspx?htno=06331a0478';
$r = new Http_Request2($url);
$r->setMethod(HTTP_Request2::METHOD_GET);
$r->setHeader(array(
    "Referer" => "http://www.results.manabadi.co.in/KakinadaJNTU-IIIB.Tech-IIsem-results-11012012.htm"
));
$r->addCookie('__utma', '264667532.627952658.1331704468.1331704468.1331704468.1');
$r->addCookie('__utmc', '264667532');
$r->addCookie('__utmz', '264667532.1331704468.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)');
$r->addCookie('__utmb', '264667532');
//$response = $r->send();
//$page = $response->getBody();
$dom = new DOMDocument();
$page="<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN' 'http://www.w3.org/TR/REC-html40/loose.dtd'><html><body><br><table width='80%' cellpadding='0' cellspacing='0'><tr><th colspan='2' align='center'>Personal Information</th></tr><tr><td><b> Hall Ticket No </b></td><td>06331A0478</td></tr></table><br><table width='100%' cellpadding='0' cellspacing='0'><tr><th colspan='5' align='center'>Marks Details</th></tr><tr><td><b>Subject Code</b></td><td><b>Subject Name</b></td><td><b>Internal Marks</b></td><td><b>External Marks</b></td><td><b>Credits</b></td></tr><tr><td>Q0403</td><td>MICROWAVE ENGG.</td><td>9</td><td>14</td><td>0</td></tr></table><br><br></body></html>";
$dom->loadHTML($page);
$xp = new DOMXPath($dom);
///html/body/table/tbody/tr[td='Subject Code']/ancestor::tbody/tr
$result = $xp->query("/html/body/table/tr[td='Subject Code']/ancestor::table/tr");
$i=2;
echo('<subs>');
while($elm=$result->item($i)){
    $r = $xp->query("td[1]/text()",$elm);
    echo('<sub><subcode>'.$r->item(0)->C14N().'</subcode>');
    $r = $xp->query("td[2]/text()",$elm);
    echo('<subname>'.$r->item(0)->C14N().'</subname></sub>');
    $i++;
}
echo('</subs>');
?>
