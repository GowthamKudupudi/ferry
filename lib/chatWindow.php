<?php
//Author: satya gowtham kudupudi

require 'authorize.php';
$key = $_SESSION['key'];
$shmId = shm_attach($key);
$semId = sem_get($key);
while (!sem_acquire($semId)) {
    sleep(1);
    $i++;
    if ($i++ > 9) {
        die('<status>sem acquire timed out ~:|~</status>');
    }
}
$user = shm_get_var($shmId, $key);
?>
<head>
    <title>chat</title>
    <script type="text/javascript" src="/lib/core.js"></script>
    <script type="text/javascript" src="/lib/formValidator.js"></script>
</head>
<body>
    <div id="chatBoxFrnLst" class="gdgBody">
        <style type="text/css">
            #addFrnNameInp{
                background-color: lightgray;
                color: white;
                border-width: 1px;
                border-color: darkgray;
            }
            .frnDiv{
                width: 155px;
                cursor: pointer;
            }
            .chtMsgsArea::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            .chtMsgsArea::-webkit-scrollbar-button {
                display: none;
            }
            .chtMsgsArea::-webkit-scrollbar-corner {
                background-color: transparent;
            }
            .chtMsgsArea::-webkit-scrollbar-thumb {
                border-radius: 2px;
                background: grey;
                border: 1px solid transparent;
            }
            .chtMsgsArea::-webkit-scrollbar-track-piece {
                background-color: transparent;
            }
            .chtMsg{
                background-color: #DDF;
                margin: 1px 1px 1px 3px;
                border: 1px solid #EEF;
                padding: 0px 1px 0px 1px;
            }
            .chatBoxMouth{
                width: 175px;
                font-family:"lucida grande",tahoma,verdana,arial,sans-serif;
                margin: 2px 2px 0px 2px;
            }
            .chtMsgsArea{
                width: 183px;
                overflow-y: auto;
                margin-top: 2px;
            }
        </style>
        <div id="titleBar"><span id="status"></span> Chat </div>
        <form id="addFrnBox">
            <input id="addFrnNameInp" title="type in ur frn's email id to chat with" type="text" value="Add a friend :)" onclick="if(!this.style.initial){this.style.backgroundColor='white';this.style.color='black';this.value='';this.focus();this.style.initial=true;return false;}" onblur="if(this.value==''){this.style.backgroundColor='lightgray';this.style.color='white';this.value='Add a friend :)';this.style.initial=false;}return false;"/>
        </form>
        <div id="frnsLst"></div>
        <script id="chatBoxFrnLstScript" type="text/javascript" onload="chat.init();return false;">
            window.chat={
                frnsLst:[<?php
foreach ($user['friends'] as $friend => $info) {
    echo "['" . $info['nickName'] . "','" . $friend . "','" . ($info['req'] ? $info['req'] : 0) . "'],";
}
?>],
            toggleStatus:function(){
                this.disabled=true;
                if(chat.status.value==0){
                    var feed=new Object();
                    feed.content={
                        action:'signIn'
                    }
                    feed.elm=this;
                    feed.postExpedition=function(){
                        if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                            var olFrns=feed.responseXML.getElementsByTagName('olfrns')[0].childNodes;
                            if(olFrns){
                                var olFrnsCount=olFrns.length;
                                for(var i=0;i<olFrnsCount;i++){
                                    var frnName=olFrns[i].tagName;
                                    var frndiv=chat.chatBoxFrnLst.frnsLst.children[frnName];
                                    if(!frndiv && olFrns[i].attributes['req'].value=='r'){
                                        frndiv=document.createElement('div');
                                        frndiv.id=frnName;
                                        frndiv.title=frndiv.id;
                                        var status=document.createElement('span');
                                        status.id='status';
                                        frndiv.classList.add('pending');
                                        status.innerHTML="<button id='accept' onclick='chat.acceptFrnReq.call(this);return false;'>Accept</button>";
                                        var statusLED=new Image();
                                        statusLED.src="/images/"+(olFrns[i].attributes['status'].value=='online'?'on':'off')+"Bulb.png";
                                        statusLED.id='statusLED';
                                        statusLED.classList.add(frnName);
                                        statusLED.classList.add('statusLED');
                                        status.appendChild(statusLED);
                                        frndiv.appendChild(status);
                                        var name=document.createElement('span');
                                        name.id='name';
                                        name.innerHTML=olFrns[i].attributes['nickName'].value?olFrns[i].attributes['nickName'].value:frndiv.id;
                                        frndiv.appendChild(name);
                                        frndiv.onclick=chat.openChatBox;
                                        frndiv.classList.add('frnDiv');
                                        chat.chatBoxFrnLst.frnsLst.appendChild(frndiv);
                                    }
                                    var statusLEDs=document.getElementsByClassName(frnName+' statusLEDs');
                                    for(var j=0;j<statusLEDs.length;j++){
                                        statusLEDs[j].src='/images/onBulb.png';
                                    }
                                    frndiv.classList.add('online');
                                }
                            }
                            var pendingFrns=chat.chatBoxFrnLst.frnsLst.getElementsByClassName('pending');
                            var pendFrnsCount=pendingFrns.length;
                            for(var i=0;i<pendFrnsCount;i++){
                                pendingFrns[i].children['status'].children['accept'].disabled=false;
                            }
                            feed.elm.anim(6);
                            feed.elm.mState=2;
                            feed.elm.disabled=false;
                            statusField.innerHTML="U've signed into chat, happy chatting ~:)~";
                            chat.status.value=1;
                            chat.updater=window.setTimeout(chat.update, 1500);
                            chat.updCounter=0;
                        }else{
                            feed.elm.anim(0);
                            feed.elm.mState=0;
                            statusField.innerHTML="Sign into chat failed ~:|~";
                        }
                    }
                    feed.ferry=new core.shuttle('/lib/chat.php', feed.content, feed.postExpedition, feed);
                }else{
                    var feed=new Object();
                    feed.content={
                        action:'signOut'
                    }
                    feed.elm=this;
                    feed.postExpedition=function(){
                        if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                            var olFrns=chat.chatBoxFrnLst.frnsLst.getElementsByClassName('online');
                            var statusLEDs=document.getElementsByClassName('statusLED');
                            while(olFrns.length>0){
                                for(var j=0;j<statusLEDs.length;j++){
                                    statusLEDs[j].src='/images/offBulb.png';
                                }
                                olFrns[0].classList.remove('online');
                            }
                            var pendingFrns=chat.chatBoxFrnLst.frnsLst.getElementsByClassName('pending');
                            var pfc=pendingFrns.length;
                            for(var i=0;i<pfc;i++){
                                pendingFrns[i].children['status'].children['accept'].disabled=true;
                            }
                            feed.elm.anim(0);
                            feed.elm.mState=0;
                            feed.elm.disabled=false;
                            statusField.innerHTML="U've signed out of chat ~:/~";
                            chat.status.value=0
                            window.clearTimeout(chat.updater);
                        }else{
                            feed.elm.anim(6);
                            feed.elm.mState=2;
                            statusField.innerHTML="unable to sign out of chat ~:|~";
                        }
                    }
                    feed.ferry=new core.shuttle('/lib/chat.php', feed.content, feed.postExpedition, feed);
                }
                return false;
            },
            addFrn:function(){
                statusField.innerHTML='Sending friend request...';
                var validate=validator.isEmailID(this.children['addFrnNameInp']);
                if(validate){
                    var friend=this.children['addFrnNameInp'].value;
                    var feed=new Object();
                    feed.content={
                        action:'addFrn',
                        friend:friend
                    }
                    feed.friend=friend;
                    feed.elm=this.children['addFrnNameInp'];
                    feed.postExpedition=function(){
                        if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                            var newFrn=document.createElement('div');
                            var status=document.createElement('span');
                            status.id='status';
                            newFrn.appendChild(status);
                            var frnName=document.createElement('span');
                            frnName.id='name';
                            newFrn.appendChild(frnName);
                            frnName.innerHTML=feed.friend.slice(0,feed.friend.indexOf('@'));
                            status.innerHTML="<img id='statusLED' src='/images/offBulb.png' class='statusLED "+frnName.innerHTML+"'/>";
                            newFrn.id=frnName.innerHTML;
                            newFrn.className='frnDiv';
                            newFrn.onclick=chat.openChatBox;
                            chat.chatBoxFrnLst.frnsLst.appendChild(newFrn);
                            feed.elm.value='';
                            feed.elm.onblur.call(feed.elm);
                            statusField.innerHTML='Friend request sent ~:)~';
                        }else{
                            statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                        }
                    }
                    feed.ferry=new core.shuttle('/lib/chat.php', feed.content, feed.postExpedition, feed);
                }
                return false;
            },
            openChatBox:function(){
                if(!this.chatBox){
                    var name=this.children['name'].innerHTML;
                    var parent=chat.chatBoxFrnLst;
                    var icon=this.children['status'].children['statusLED'].cloneNode(true);
                    var objOwner=this;
                    var body=document.createElement('div');
                    var infoArea=document.createElement('div');
                    infoArea.id='infoArea';
                    body.appendChild(infoArea);
                    var msgsArea=document.createElement('div');
                    msgsArea.id='msgsArea';
                    msgsArea.style.height='50px';
                    msgsArea.classList.add('chtMsgsArea');
                    body.appendChild(msgsArea);
                    var inputArea=document.createElement('textarea');
                    inputArea.id='inputArea';
                    inputArea.onkeydown=chat.cBMouthKeyHandler;
                    inputArea.classList.add('chatBoxMouth');
                    inputArea.to=this.id;
                    body.appendChild(inputArea);
                    this.chatBox=new core.msgPanel(name, parent, objOwner, body, icon)
                    inputArea.focus();
                    this.chatBox.onclick=function(){
                        if(this.newMsg){
                            this.newMsg=false;
                            this.titleBar.style.backgroundColor='#6666C5';
                        }
                        this.activate();
                    }
                    this.chatBox.mouth=inputArea;
                    this.chatBox.style.overflow=null;
                }else{
                    this.chatBox.parent.appendChild(this.chatBox);
                    this.chatBox.style.display=null;
                }
            },
            cBMouthKeyHandler:function(){
                if(event.keyCode==13){
                    event.preventDefault();
                    event.cancelBubble=true;
                    chat.update.call(this);
                    this.value='';
                    return false;
                }
                if(this.parentElement.parentElement.newMsg){
                    this.parentElement.parentElement.newMsg=false;
                    this.parentElement.parentElement.titleBar.style.backgroundColor='#6666C5';
                }
            },
            update:function(){
                window.clearTimeout(chat.updater);
                var feed=new Object();
                feed.content={
                    action:'update'
                }
                if(this!=window && this.classList.contains('chatBoxMouth')){
                    chat.updCounter=0;
                    feed.content.action+=',post';
                    feed.content.msg=this.value;
                    feed.content.to=this.to;
                    feed.elm=this;
                }
                feed.postExpedition=function(feed){
                    if(chat.status.value>0){
                        chat.updCounter++;
                        if(chat.updCounter>59)chat.updCounter=60;
                        chat.updater=window.setTimeout(chat.update, 1500+chat.updCounter*500);
                    }
                    if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                        try{
                            var olFrns=feed.responseXML.getElementsByTagName('olfrns')[0].childNodes;
                        }catch(e){}
                        if(olFrns){
                            var olFrnsCount=olFrns.length;
                            for(var i=0;i<olFrnsCount;i++){
                                var frnName=olFrns[i].tagName;
                                var frndiv=chat.chatBoxFrnLst.frnsLst.children[frnName];
                                if(!frndiv && olFrns[i].attributes['req'].value=='r'){
                                    frndiv=document.createElement('div');
                                    frndiv.id=frnName;
                                    frndiv.title=frndiv.id;
                                    var status=document.createElement('span');
                                    status.id='status';
                                    frndiv.classList.add('pending');
                                    status.innerHTML="<button id='accept' onclick='chat.acceptFrnReq.call(this);return false;'>Accept</button>";
                                    var statusLED=new Image();
                                    statusLED.src="/images/"+(olFrns[i].attributes['status'].value=='online'?'on':'off')+"Bulb.png";
                                    statusLED.id='statusLED';
                                    statusLED.classList.add(frnName);
                                    statusLED.classList.add('statusLED');
                                    status.appendChild(statusLED);
                                    frndiv.appendChild(status);
                                    var name=document.createElement('span');
                                    name.id='name';
                                    name.innerHTML=olFrns[i].attributes['nickName'].value?olFrns[i].attributes['nickName'].value:frndiv.id;
                                    frndiv.appendChild(name);
                                    frndiv.onclick=chat.openChatBox;
                                    frndiv.classList.add('frnDiv');
                                    chat.chatBoxFrnLst.frnsLst.appendChild(frndiv);
                                }
                                var frnNckName=chat.chatBoxFrnLst.frnsLst.children[frnName].children['name'].innerHTML;
                                var statusLEDs=document.getElementsByClassName(frnName+' statusLED');
                                if(olFrns[i].attributes['status'].value=='online'){
                                    for(var j=0;j<statusLEDs.length;j++){
                                        statusLEDs[j].src='/images/onBulb.png';
                                    }
                                    frndiv.classList.add('online');
                                }else{
                                    for(var j=0;j<statusLEDs.length;j++){
                                        statusLEDs[j].src='/images/offBulb.png';
                                    }
                                    frndiv.classList.remove('online');
                                }
                                try{
                                    var msgs=olFrns[i].getElementsByTagName('msgs')[0].childNodes;
                                }catch(e){
                                    var msgs=[];
                                }
                                if(msgs.length>0){
                                    if(!chat.chatBoxFrnLst.frnsLst.children[frnName].chatBox){
                                        chat.openChatBox.call(chat.chatBoxFrnLst.frnsLst.children[frnName]);
                                    }else if(!chat.chatBoxFrnLst.frnsLst.children[frnName].chatBox.parentElement){
                                        chat.chatBoxFrnLst.frnsLst.children[frnName].chatBox.parent.appendChild(chat.chatBoxFrnLst.frnsLst.children[frnName].chatBox);
                                        chat.chatBoxFrnLst.frnsLst.children[frnName].chatBox.style.display=null;
                                    }
                                    var chatBox=chat.chatBoxFrnLst.frnsLst.children[frnName].chatBox;
                                    for(i=0;i<msgs.length;i++){
                                        var msg=document.createElement('div');
                                        msg.className='chtMsg';
                                        msg.innerHTML="<span id='by' style='font-weight:bold'>"+(msgs[i].getElementsByTagName('rs')[0].firstChild.nodeValue=='r'?frnNckName:'me')+"</span>: "+msgs[i].getElementsByTagName('msgbody')[0].firstChild.nodeValue;
                                        chatBox.body.children['msgsArea'].appendChild(msg);
                                        msg.scrollIntoViewIfNeeded();
                                    }
                                    if(document.activeElement!=chatBox.mouth || !document.hasFocus()){
                                        chatBox.titleBar.style.backgroundColor='orange';
                                        chatBox.newMsg=true;
                                    }
                                    var msgsArea=chatBox.body.children['msgsArea'];
                                    if(msgsArea.scrollHeight>50 && msgsArea.scrollHeight<200){
                                        msgsArea.style.height=null;
                                    }else if(msgsArea.scrollHeight>200){
                                        msgsArea.style.height='200px';
                                    }
                                    if(msg)msg.scrollIntoViewIfNeeded();
                                }
                            }
                        }
                    }else{
                        statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                    }
                }
                feed.ferry=new core.shuttle('lib/chat.php', feed.content, feed.postExpedition, feed);
            },
            acceptFrnReq:function(){
                statusField.innerHTML='Accepting Frn request';
                event.cancelBubble=true;
                statusField.innerHTML='Accepting friend request ~@|~';
                var friend=this.parentElement.parentElement.id;
                var feed=new Object();
                feed.content={
                    action:'acceptFrnReq',
                    friend:friend
                }
                feed.friend=friend;
                feed.fN=this.parentElement.parentElement.children['name'].innerHTML;
                feed.ctrlr=this;
                feed.postExpedition=function(feed){
                    if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                        var statusF=feed.ctrlr.parentElement;
                        feed.ctrlr.parentElement.parentElement.classList.remove('pending');
                        statusF.removeChild(feed.ctrlr);
                        statusField.innerHTML=feed.fN+' is ur frn now ~:)~';
                        var statusLEDs=document.getElementsByClassName(feed.content.friend+' statusLED');
                        if(feed.responseXML.getElementsByTagName('frnStatus')[0].firstChild){
                            if(feed.responseXML.getElementsByTagName('frnStatus')[0].firstChild.nodeValue=='1'){
                                for(var j=0;j<statusLEDs.length;j++){
                                    statusLEDs[j].src='/images/onBulb.png';
                                }
                            }else{
                                for(var j=0;j<statusLEDs.length;j++){
                                    statusLEDs[j].src='/images/offBulb.png';
                                }
                            }
                        }else{
                            for(var j=0;j<statusLEDs.length;j++){
                                statusLEDs[j].src='/images/offBulb.png';
                            }
                        }
                    }else{
                        statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                    }
                }
                feed.ferry=new core.shuttle('/lib/chat.php', feed.content, feed.postExpedition, feed);
                return false;
            },
            init:function(){
                chat.chatBoxFrnLst=document.getElementById('chatBoxFrnLst');
                chat.chatBoxFrnLst.titleBar=chat.chatBoxFrnLst.children['titleBar'];
                chat.chatBoxFrnLst.addFrnBox=chat.chatBoxFrnLst.children['addFrnBox'];
                chat.status=chat.chatBoxFrnLst.titleBar.children['status'];
                chat.status.value=<?php echo ($user['chtStatus'] ? $user['chtStatus'] : '0'); ?>;
                chat.status.lED=new core.animatedImage(['/images/offSwitch.png','/images/offSwitch.png','/images/offSwitch.png','/images/onInActSwitch.png','/images/onInActSwitch.png','/images/onInActSwitch.png','/images/onSwitch.png','/images/onSwitch.png','/images/onSwitch.png','/images/offInActSwitch.png','/images/offInActSwitch.png','/images/offInActSwitch.png'], chat.toggleStatus);
                chat.status.lED.trigMouseEvt();
                chat.status.appendChild(chat.status.lED.__proto__);
                chat.chatBoxFrnLst.addFrnBox.onsubmit=chat.addFrn;
                chat.chatBoxFrnLst.frnsLst=chat.chatBoxFrnLst.children['frnsLst'];
                chat.frnsLst.sort();
                for(i=0;i<chat.frnsLst.length;i++){
                    try{
                        var friend=document.createElement('div');
                        friend.id=chat.frnsLst[i][1];
                        friend.title=friend.id;
                        var status=document.createElement('span');
                        status.id='status';
                        if(chat.frnsLst[i][2]=='r'){
                            friend.classList.add('pending');
                            status.innerHTML="<button id='accept' disabled='true' onclick='chat.acceptFrnReq.call(this);return false;'>Accept</button>";
                        }
                        var statusLED=new Image();
                        statusLED.src="/images/offBulb.png";
                        statusLED.id='statusLED';
                        statusLED.classList.add(friend.id);
                        statusLED.classList.add('statusLED');
                        status.appendChild(statusLED);
                        friend.appendChild(status);
                        var name=document.createElement('span');
                        name.id='name';
                        name.innerHTML=chat.frnsLst[i][0]?chat.frnsLst[i][0]:friend.id;
                        friend.appendChild(name);
                        friend.onclick=chat.openChatBox;
                        friend.classList.add('frnDiv');
                        chat.chatBoxFrnLst.frnsLst.appendChild(friend);
                    }catch(e){
                        core.log+='\n'+e;
                    }
                }
                this.removeEventListener('DOMNodeInsertedIntoDocument',arguments.callee,false);
                //event.cancelBubble=true;
                //event.stopPropagation();
                //event.preventDefault();
                if(chat.status.value==1){
                    chat.status.value=0;
                    chat.toggleStatus.call(chat.status.lED.__proto__);
                }
            }
        }
        if(location.href.indexOf('chatWindow.php')<0){
            var dfgs=document.getElementById('chatBoxFrnLstScript');
            dfgs.firstChild.addEventListener('DOMNodeInsertedIntoDocument', chat.init, true);
        }
        </script>
    </div>
</body>
