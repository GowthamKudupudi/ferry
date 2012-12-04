<?php
require 'authorize.php';
require 'db_login.php';
?>
<head>
    <title>inboxBrowser</title>
    <script type="text/javascript" src="/lib/core.js"></script>
    <script type="text/javascript">
        function init(){
            var statusField=document.createElement('div');
            statusField.id='statusField';
            document.body.insertAdjacentElement('afterBegin',statusField);
        }
        onload=init;
    </script>
</head>
<body>
    <div id="mailBox" class="gdgBody">
        <style type="text/css">
            div.toggler { border:1px solid #ccc; background-color: #eee; cursor:pointer; }
            div.toggler .msgHeader {padding: 5px 10px;}
            div.toggler .subject  { font-weight:bold; }
            div.read { color:#666; }
            div.toggler .from, div.toggler .date { font-style:italic; font-size:11px; }
            div.toggler .msgBody{padding:5px 10px; background-color: white}
        </style>
        <script type="text/javascript">
            var mail={
                composeMail:function(){
                    this.parentElement.children['inbox'].style.display='none';
                    this.parentElement.children['mailComposer'].style.display=null;
                },
                sendMail:function(){
                    var feed={};
                    feed.content={};
                    feed.content["mailOp"]="sendMail"
                    feed.content["to"]=this.children['to'].value;
                    feed.content["sub"]=this.children['sub'].value;
                    feed.content["msg"]=this.children['msg'].value;
                    feed.mc=this;
                    feed.postExpedition=function(feed){
                        if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=="success"){
                            statusField.innerHTML="Mail sent ~:)~";
                            feed.mc.style.display='none';
                            feed.mc.parentElement.children['inbox'].style.display=null;
                        }else{
                            statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                        }
                    }
                    statusField.innerHTML='sending mail...';
                    feed.ferry=new core.shuttle("/lib/mailExecuter.php", null, feed.postExpedition, feed);
                },
                openMsg:function(){
                    var b=this.children['msgBody'];
                    if(b.style.display=='none'){
                        b.style.display=null;
                    }else{
                        b.style.display='none';
                    }
                    event.cancelBubble=true;
                    return false;
                }
            }
            window.initGadget=function(dispArea){
                //dispArea.appendChild(dbTableExecuter.facade);
            }
        </script>
        <button onclick="mail.composeMail.call(this);return false;">Compose Mail</button><br/>
        <form id="mailComposer" onsubmit="mail.sendMail.call(this);return false;" style="display:none">
            <label>TO: </label><input id="to" name="to" type="text" size="30"/><br/>
            <label>SUB: </label><input id="sub" name="sub" type="text" size="60"/><br/>
            <label>Message: </label><br/>
            <textarea id="msg" name="msg" cols="60" rows="20"></textarea><br/>
            <input type="submit" value="send"/>
        </form>
        <div id="inbox">
            <?php
            /* Author: Gowtham */
            $query = "SELECT `emailPass` FROM `users` WHERE `index`=" . $_SESSION['uid'];
            $result = mysql_db_query('collegedb2admin', $query, $dbc);
            $error1 = mysql_error($dbc);
            if (!$error1) {
                $mailPass = mysql_result($result, 0, 'emailPass');
                $inbox = imap_open('{mail.ferryfair.com/imap/norsh}INBOX', $_SESSION['username'] . '@ferryfair.com', $mailPass);
                $error1.= imap_last_error();
                $emails = imap_search($inbox, 'ALL');
                if ($emails) {
                    $output = '';
                    rsort($emails);
                    $i=0;
                    foreach ($emails as $email_number) {
                        $overview = imap_fetch_overview($inbox, $email_number, 0);
                        $message = imap_fetchbody($inbox, $email_number, 1);

                        /* output the email header information */
                        $output.= '<div class="toggler"' . ($overview[0]->seen ? 'read' : 'unread') . '">';
                        $output.= '<div class="msgHeader" onclick="mail.openMsg.call(this.parentElement);return false;"><span class="subject">' . $overview[0]->subject . '</span> ';
                        $output.= '<span class="from"> From: ' . $overview[0]->from . '</span>';
                        $output.= '<span class="date"> on ' . $overview[0]->date . '</span></div>';

                        /* output the email body */
                        $output.= '<div id="msgBody", style="display:none", class="msgBody">' . $message . '</div>';
                        $output.= '</div>';
                        $i++;
                        if ($i > 5) {
                            break;
                        }
                    }

                    echo $output;
                }
            }
            /* close the connection */
            imap_close($inbox);
            ?>
        </div>
        <script type="text/javascript">
            if(!/inboxBrowser.php/.test(document.location.href)){
                statusField.innerHTML="<?php
            if ($error1)
                echo $error1;else
                echo 'Mail box loaded.';
            ?>";
                    }
        </script>
    </div>
</body>