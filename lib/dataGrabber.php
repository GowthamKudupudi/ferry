<?php

/* Author: Gowtham */
//2012-03-23 04:06:00
require 'authorize.php';
require 'inc.php';
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/lib/db_login.php";
if (anyDeptSlave($_SESSION['adminLevel'], 'Zc4')) {
    $opType = $_POST['opType'];
    if ($opType == 'GENSTRUCT') {
        header('Content-Type: text/xml');
        header('Cache-Control: no-cache');
        header('Cache-Control: no-store', false);
        echo '<datagrabber>';
        $tableType = $_POST['tableType'];
        if ($tableType == 'STUMARKS') {
            $srcurl = $_POST['srcurl'];
            $grpId = $_POST['grpId'];
            $dbTable = $_POST['dbTable'];
            if (strpos($srcurl, 'manabadi.co.in')) {
                $ge = groupExe('g' . $grpId, NULL, NULL);
                $objects = $ge['objects'];
                $_SESSION['grbr'][$dbTable]['objs'] = $objects;
                $_SESSION['grbr'][$dbTable]['srcurl'] = $srcurl;
                $modReg = trim($objects[0], 'o');
                $query = "SELECT * FROM `objectTable` WHERE `index`=" . $modReg;
                $result = mysql_db_query('collegedb2admin', $query, $dbc);
                $id = mysql_result($result, 0, 'id');
                if ($id) {
                    require 'HTTP/Request2.php';
                    $url = str_replace(".htm", '', $srcurl) . '.aspx?htno=' . $id;
                    $_SESSION['grbr'][$dbTable]['url'] = $url;
                    $r = new Http_Request2($url);
                    $r->setMethod(HTTP_Request2::METHOD_GET);
                    $r->setHeader(array(
                        "Referer" => $srcurl,
                        "User-Agent" => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.4 (KHTML, like Gecko) Chrome/19.0.1077.3 Safari/536.4"
                    ));
                    $r->addCookie('__utma', '264667532.627952658.1331704468.1331704468.1331704468.1');
                    $r->addCookie('__utmc', '264667532');
                    $r->addCookie('__utmz', '264667532.1331704468.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)');
                    $r->addCookie('__utmb', '264667532');
                    try {
                        $response = $r->send();
                    } catch (Exception $exc) {
                        $es = $exc->getTraceAsString();
                        $response = NULL;
                    }
                    $page = $response->getBody();
                    //$page = "<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN' 'http://www.w3.org/TR/REC-html40/loose.dtd'><html><body><br><table width='80%' cellpadding='0' cellspacing='0'><tr><th colspan='2' align='center'>Personal Information</th></tr><tr><td><b> Hall Ticket No </b></td><td>06331A0478</td></tr></table><br><table width='100%' cellpadding='0' cellspacing='0'><tr><th colspan='5' align='center'>Marks Details</th></tr><tr><td><b>Subject Code</b></td><td><b>Subject Name</b></td><td><b>Internal Marks</b></td><td><b>External Marks</b></td><td><b>Credits</b></td></tr><tr><td>Q0403</td><td>MICROWAVE ENGG.</td><td>9</td><td>14</td><td>0</td></tr></table><br><br></body></html>";
                    if ($page) {
                        echo '<status>success</status>';
                        $dom = new DOMDocument();
                        $dom->loadHTML($page);
                        $xp = new DOMXPath($dom);
                        $result = $xp->query("/html/body/table/tr[td='Subject Code']/ancestor::table/tr");
                        $i = 2;
                        echo("<mapper><![CDATA[<form id='mapper' onsubmit='dataGrabber.manabadiGrbr.call(this);return false;'><table><tr><th>Subject Code</th><th>Subject Name</th><th>Internal marks<br/>Column name</th><th>External marks<br/>Column name</th></th></tr>");
                        while ($elm = $result->item($i)) {
                            $r = $xp->query("td[1]/text()", $elm);
                            echo("<tr><td class='subcod grbr'>" . $r->item(0)->C14N() . '</td>');
                            $r = $xp->query("td[2]/text()", $elm);
                            echo('<td>' . $r->item(0)->C14N() . "</td><td><input type='text' ondragover=\"event.preventDefault();\" ondragenter=\"event.preventDefault();\" ondrop=\"event.target.value=event.dataTransfer.getData('text/columnName');return false;\"/></td><td><input type='text' ondragover=\"event.preventDefault();\" ondragenter=\"event.preventDefault();\" ondrop=\"event.target.value=event.dataTransfer.getData('text/columnName');return false;\"/></td></tr>");
                            $i++;
                        }
                        echo("</table><input type='submit'/></form><script type='text/javascript'>
    dataGrabber.manabadiGrbr=function(){
    var feed=new Object();
    var subcodCells=this.getElementsByClassName('subcod grbr');
    var subcolStr={};
    for(var i=0;i<subcodCells.length;i++){
        var sccr=subcodCells[i].parentElement;
        subcolStr[subcodCells[i].innerHTML]=[sccr.cells[2].firstChild.value,sccr.cells[3].firstChild.value];
    }
    feed.content={};
    feed.content.subcolStr=JSON.stringify(subcolStr);
    feed.content.opType='GRABnSTORE';
    feed.dtp=document.createElement('tr');
    feed.onreceiving=function(feed){
        if(dbTableExecuter && dbTableExecuter.tableProperties.tableName==dataGrabber.dbTable){
            if(feed.ferry.responseText!=''){
                var rows=feed.ferry.responseText.split('<rs/>');
                var tr=rows[rows.length-2];
                tr=JSON.parse(tr).row;
                var dumy=document.createElement('tbody');
                dumy.innerHTML=tr;
                tr=dumy.firstChild.cloneNode(true);
                if(tr.__proto__==feed.dtp.__proto__){
                var trc=tr.childNodes;
                var trcc=trc.length-1;
                dbTableExecuter.table['index'][dbTableExecuter.table['index'].length]=trc[0].id;
                for(var i=1;i<trcc;i++){
                    trc[i].ondblclick=dbTableExecuter.editCell;
                    var l=dbTableExecuter.table[dbTableExecuter.dTable.tHR.cells[i].id].lenght;
                    dbTableExecuter.table[dbTableExecuter.dTable.tHR.cells[i].id][l]=trc[i].textContent;
                }
                dbTableExecuter.tableProperties.rowCount++;
                var rl=dbTableExecuter.dTable.tbody.rows.length;
                var lr=dbTableExecuter.dTable.tbody.rows[rl-1];
                if(rl%2) tr.classList.add('odd');else tr.classList.add('even');
                lr.insertAdjacentElement('beforeBegin',tr);
                statusField.innerHTML='marks of '+trc[1].textContent+' are stored in '+dataGrabber.dbTable+' ~:)~';                
            }else{
                statusField.innerHTML=tr.nodeValue;
            }
        }
    }
        return false;
    };
    feed.postExpedition=function(feed){
        var rows=feed.ferry.responseText.split('<rs/>');
        var response=rows[rows.length-1];
        response=JSON.parse(response);
        if(response.status=='success'){
            statusField.innerHTML='all marks are downloaded ~:)~';
        }else{
            statusField.innerHTML=response.status;
        }  
    }
    dataGrabber.dbTable='" . $dbTable . "';
    feed.content.dbTable=dataGrabber.dbTable;
    feed.ferry=new core.shuttle('/lib/dataGrabber.php', null, feed.postExpedition, feed);
    return false;
}
dbTableExecuterTool.tableNameInputField.value='" . $dbTable . "';
dbTableExecuterTool.loadGadget.apply(dbTableExecuterTool,[false]);
</script>]]></mapper>");
                    } else {
                        echo '<status>url not reachable ~:|~</status>';
                    }
                } else {
                    echo '<status>error traversing group ~:|~</status>';
                }
            }elseif(preg_match("/\.xls$/", $srcurl) or preg_match("/\.xlsx$/", $srcurl)){
                
            } else {
                echo '<status>Sry ~:|~ no grabber available for this site.</status>';
            }
        }
        echo '</datagrabber>';
    }
    if ($opType == 'GRABnSTORE') {
        $dbTable = $_POST['dbTable'];
        $srcurl = $_SESSION['grbr'][$dbTable]['srcurl'];
        $qurl = str_replace(".htm", '', $srcurl) . '.aspx?htno=';
        $subjects = json_decode($_POST['subcolStr']);
        //sign into ferry
        require 'HTTP/Request2.php';
        $r = new Http_Request2('https://collegedb2.ferryfair.com/lib/signIn.php');
        $r->setConfig(array("ssl_verify_peer"=>FALSE,"ssl_local_cert"=>"$root/ssl/collegedb2.ferryfair.com.cert"));
        $r->setMethod(HTTP_Request2::METHOD_POST);
        $r->addPostParameter(array(
            'signInType' => 'object',
            'username' => '12COLSO02',
            'password' => 'tyqejavagetutyty'
        ));
        try {
            $response = $r->send();
        } catch (Exception $exc) {
            $es = $exc->getTraceAsString();
        }
        $body = $response->getBody();
        $dom = new DOMDocument();
        $dom->loadXML($body);
        $xp = new DOMXPath($dom);
        $sessionId = $xp->query("sessionid/text()")->item(0)->C14N();
        if (strpos($body, '<status>success</status>')) {
            $r = new Http_Request2('https://collegedb2.ferryfair.com/lib/superScripts/dbTableExecuterForm.php');
            $r->setConfig(array("ssl_verify_peer"=>FALSE,"ssl_local_cert"=>"$root/ssl/collegedb2.ferryfair.com.cert"));
            $r->setMethod(HTTP_Request2::METHOD_POST);
            $r->addPostParameter(array(
                'dbTable' => $dbTable,
                'SESSION_ID' => $sessionId
            ));
            try {
                $response = $r->send();
            } catch (Exception $exc) {
                $es = $exc->getTraceAsString();
                $response = null;
            }
            $body = $response->getBody();
            if (strpos($body, "authorization:'*'")) {
                header('Content-Type: application/octet-stream');
                $fetchErrCount = 0;
                foreach ($_SESSION['grbr'][$dbTable]['objs'] as $key => $value) {
                    $oid = trim($value, 'o');
                    $query = "SELECT * FROM `objectTable` WHERE `index`=" . $oid;
                    $result = mysql_db_query('collegedb2admin', $query, $dbc);
                    $id = mysql_result($result, 0, 'id');
                    $url = $qurl . $id;
                    $r = new Http_Request2($url);
                    $r->setMethod(HTTP_Request2::METHOD_GET);
                    $r->setHeader(array(
                        "Referer" => $srcurl,
                        "User-Agent" => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.4 (KHTML, like Gecko) Chrome/19.0.1077.3 Safari/536.4"
                    ));
                    $r->addCookie('__utma', '264667532.627952658.1331704468.1331704468.1331704468.1');
                    $r->addCookie('__utmc', '264667532');
                    $r->addCookie('__utmz', '264667532.1331704468.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)');
                    $r->addCookie('__utmb', '264667532');
                    try {
                        $response = $r->send();
                    } catch (Exception $exc) {
                        $es = $exc->getTraceAsString();
                        $response = null;
                    }
                    if ($response) {
                        $page = $response->getBody();
                        $dom = new DOMDocument();
                        $dom->loadHTML($page);
                        $xp = new DOMXPath($dom);
                        $result = $xp->query("/html/body/table/tr[td='Subject Code']/ancestor::table/tr");
                        $i = 2;
                        while ($elm = $result->item($i)) {
                            $rc = $xp->query("td[1]/text()", $elm);
                            $r = $xp->query("td[5]/text()", $elm);
                            $subs[$rc->item(0)->C14N()]['int'] = $r->item(0)->C14N();
                            $r = $xp->query("td[4]/text()", $elm);
                            $subs[$rc->item(0)->C14N()]['ext'] = $r->item(0)->C14N();
                            $i++;
                        }
                        $subcols = array();
                        $cVs = array();
                        foreach ($subjects as $scode => $intext) {
                            $subcols[] = $intext[0];
                            $subcols[] = $intext[1];
                            $cVs[] = $subs[$scode]['int'];
                            $cVs[] = $subs[$scode]['ext'];
                        }
                        $colIndex = "`id`,`" . implode("`,`", $subcols) . "`";
                        $cellValues = "'" . $id . "', '" . implode("', '", $cVs) . "'";
                        $r = new Http_Request2('https://collegedb2.ferryfair.com/lib/superScripts/dbTableExecuter.php');
                        $r->setConfig(array("ssl_verify_peer"=>FALSE,"ssl_local_cert"=>"$root/ssl/collegedb2.ferryfair.com.cert"));
                        $r->setMethod(HTTP_Request2::METHOD_POST);
                        $r->addPostParameter(array(
                            'tableOperation' => 'updateCell',
                            'cellHash' => '',
                            'colIndex' => $colIndex,
                            'dbTable' => $dbTable,
                            'rowIndex' => "newRow",
                            'value' => $cellValues,
                            'SESSION_ID' => $sessionId
                        ));
                        //$r->addCookie('PHPSESSID', 'bkq0t2vf2n9898t4q83jje7bp7');
                        //$r->addCookie('XDEBUG_SESSION', 'netbeans-xdebug');
                        /* cellHash: null
                          colIndex: "`id`, `ENG_I_int`, `ENG_II_ext`"
                          dbTable: "11marks11_ECE_HEAD"
                          rowIndex: "newRow"
                          tableOperation: "updateCell"
                          value: "'11331A0401', '15', '20'" */
                        try {
                            $response = $r->send();
                        } catch (Exception $exc) {
                            $es = $exc->getTraceAsString();
                            $response = NULL;
                        }
                        $body = $response->getBody();
                        if (strpos($body, "<status>success</status>")) {
                            $dom = new DOMDocument();
                            $dom->loadXML($body);
                            $xp = new DOMXPath($dom);
                            $result = $xp->query("/dbTableExecuter/newRowIndex/text()");
                            $nRI = $result->item(0)->C14N();
                            $cellStr = "<td>" . $id . "</td><td>" . implode("</td><td>", $cVs) . "</td>";
                            $rowStr = "<tr class='vInd' id='" . $nRI . "'><td id='" . $nRI . "' style='text-align: center; ' title='" . $nRI . "'><img id='delRowBtn' title='Delete row " . $nRI . "' class='del rowDeleter ibtn' onclick='dbTableExecuter.delRow();return false;' src='images/-.png'><input type='checkbox' id='selector' value='" . $nRI . "' class='row selector' style='display: none; '></td>" . $cellStr . "</tr>";
                            //<tr class="vInd even" id="1"><td id="1" style="text-align: center; " title="1"><img id="delRowBtn" title="Delete row 1" class="del rowDeleter ibtn" onclick="dbTableExecuter.delRow();return false;" src="images/-.png"><input type="checkbox" id="selector" value="1" class="row selector" style="display: none; "></td><td>maxMarks</td><td>20</td><td>80</td><td>20</td><td>80</td><td>20</td><td>80</td><td>20</td><td>80</td><td>20</td><td>80</td><td>20</td><td>80</td><td>25</td><td>75</td><td>25</td><td>75</td></tr>
                            $fetchErrCount = 0;
                            echo json_encode(array('row' => $rowStr));
                        } else {
                            echo json_encode(array('row' => 'unable to insert row with id ' . $id));
                            echo '<rs/>';
                            ob_end_flush();
                            ob_flush();
                            flush();
                            ob_start();
                            break;
                        }
                    } else {
                        echo json_encode(array('row' => 'unable to fetch marks of ' . $id));
                        if ($fetchErrCount > 10) {
                            echo '<rs/>';
                            ob_end_flush();
                            ob_flush();
                            flush();
                            ob_start();
                            break;
                        }
                    }
                    echo '<rs/>';
                    ob_end_flush();
                    ob_flush();
                    flush();
                    ob_start();
                }
                $r = new Http_Request2('https://collegedb2.ferryfair.com/lib/signOut.php');
                $r->setConfig(array("ssl_verify_peer"=>FALSE,"ssl_local_cert"=>"$root/ssl/collegedb2.ferryfair.com.cert"));
                $r->setMethod(HTTP_Request2::METHOD_POST);
                $r->addPostParameter(array(
                    'SESSION_ID' => $sessionId
                ));
                //$r->addCookie('PHPSESSID', 'bkq0t2vf2n9898t4q83jje7bp7');
                //$r->addCookie('XDEBUG_SESSION', 'netbeans-xdebug');
                try {
                    $response = $r->send();
                } catch (Exception $exc) {
                    $es = $exc->getTraceAsString();
                    $response = NULL;
                }
                $body = $response->getBody();
                if (strpos($body, "<status>signedOut</status>")>=0) {
                    echo json_encode(array('status' => 'I, dataGrabber.php finished my job ~:)~'));
                } else {
                    echo json_encode(array('status' => 'I, dataGrabber.php unable to signOut from the ferry ~:|~'));
                }
            } else {
                echo json_encode(array('status' => 'I, dataGrabber.php unable to open the table may be i am not authorized ~:(~'));
            }
        } else {
            echo json_encode(array('status' => 'I, dataGrabber.php might got deobjectized. access denied ~:(~'));
        }
    }
}
?>
