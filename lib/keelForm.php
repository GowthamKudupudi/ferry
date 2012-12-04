<?php
/* Author: Gowtham */
require 'authorize.php';
require 'db_login.php';
$query = "SHOW COLUMNS FROM `objectTable`";
$result = mysql_db_query('collegedb2admin', $query, $dbc);
$error1 = mysql_error($dbc);
$type1Opts = mysql_result($result, 4, 'Type');
$type2Opts = mysql_result($result, 5, 'Type');

function enumExtractor($str) {
    while ($str[$i] and $str[$i] != ')') {
        if ($str[$i] == '(') {
            $i++;
            while ($str[$i] != ')') {
                $to1.=$str[$i];
                $i++;
            }
        }
        $i++;
    }
    $to1 = explode(',', $to1);
    for ($i = 0; $i < count($to1); $i++) {
        $to1[$i] = trim($to1[$i], "'");
    }
    return $to1;
}

$type1Opts = enumExtractor($type1Opts);
$type2Opts = enumExtractor($type2Opts);

$query = "SHOW COLUMNS FROM `groups`";
$result = mysql_db_query('collegedb2admin', $query, $dbc);
$error1 = mysql_error($dbc);
$grpTypes = enumExtractor(mysql_result($result, 2, 'Type'));

function optionEchoer($options) {
    foreach ($options as $value) {
        echo "<option value='" . $value . "'>" . $value . "</option>";
    }
}
?>
<!DOCTYPE html>
<head>
    <title>Object Creator Form</title>
</head>
<body>
    <div id="keelDiv" class="gdgBody">
        <form id="objParamForm" onsubmit="keel.createObject.call(this);return false;">
            <h2>Create Object</h2>
            <label>Object Type 1:</label><select id="type1" onchange="keel.type1Handler.call(this);return false;"><?php optionEchoer($type1Opts) ?></select><br/>
            <label>Object Type 2:</label><select id="type2"><?php optionEchoer($type2Opts) ?></select><br/>
            <label>Object Name:</label><input id="objNameStart" title="Type in Object's name" type ="text" size="10" maxlength="16"/><span>&nbsp;-&nbsp;</span><input id="objNameEnd" title="Enter end name of the objects range.\n if its a single object" type="text" size="10" maxlength="16"/><br/>
            <label>Function Id: </label><input id="functionId" title="Type in thie the id of the object's function" type="text" size="10" maxlength="16"/><span>&nbsp;or&nbsp;</span><button id="newTaskCreBtn" onclick="keel.createTask.call(this); return false;">CreateTask</button><br/>
            <label>Object's adminLevel: </label><input id="objAL" title="U can create only child objects." type="text" size="2" maxlength="3"/><br/>
            <label>Object's description: </label><textarea id="objDescription" title="max 160 character" maxlength="160"></textarea><br/>
            <input type="submit" value="Create"/><br/>
        </form>
        <form id="groupParamForm" onsubmit="keel.createGroup.call(this);return false;">
            <h2>Create Group</h2>
            <label>Label :</label><input type="text" id="label" size="10" maxlength="16"/><br/>
            <label>Type :</label><select id="type"><?php optionEchoer($grpTypes) ?></select><br/>
            <label>Members :</label><input id="members" type="text" size="20"/><br/>
            <label>Authorized Objects:</label><input id="authUnits" type="text" title="type in authorized objs./n Format: wt4.u5,ro6.t5/n if donno leave it blank." size="20" maxlength="16"/><br/>
            <input type="submit" value="Create"/><br/>
        </form>
        <form id="taskParamForm" onsubmit="keel.createTask.call(this);return false;">
            <h2>Create Task</h2>
            <label>Work :</label><input id="work" type="text" size="10" maxlength="20"/><br/>
            <label>Type :</label><input id="type" type="text" size="10" maxlength="12"/><br/>
            <label>Target :</label><input id="target" type="text" size="10" maxlength="16"/><br/>
            <label>Worker :</label><input id="worker" type="text" size="10" maxlength="16"/><br/>
            <label>Scheduled Start Time :</label><input id="sst" type="text" size="10" maxlength="119"/><br/>
            <label>Scheduled End Time :</label><input id="set" type="text" size="10" maxlength="19"/><br/>
            <input type="submit" value="Create"/>
        </form>
        <script id="keelScript" type="text/javascript">
            window.keel={
                oPF:null,
                gPF:null,
                tPF:null,
                init:function(){
                    this.removeEventListener('DOMNodeInsertedIntoDocument',arguments.callee,false);
                    keel.oPF=document.getElementById('keelDiv').children['objParamForm'];
                    keel.gPF=document.getElementById('keelDiv').children['groupParamForm'];
                    keel.tPF=document.getElementById('keelDiv').children['taskParamForm'];
                    keel.oPF.children['newTaskCreBtn'].onclick=function(){
                        keel.tPF.children['work'].focus();
                        keel.createTask.tidCraver=keel.oPF.children['functionId'];
                        statusField.innerHTML='First create a task n grab a task id ~:)~';
                        assistant.setPosition(keel.tPF.children['work']);
                        return false;
                    }
                    return false;
                },
                createTask:function(){
                    var work=this.children['work'];
                    var type=this.children['type'];
                    var target=this.children['target']
                    var worker=this.children['worker'];
                    var sst=this.children['sst'];
                    var set=this.children['set'];
                    if(work.value==''){
                        work.focus();
                        statusField.innerHTML='Invalid work ~&|~';
                        return false;
                    }
                    var feed=new Object();
                    feed.content={
                        ops:'CNT',
                        work:work.value,
                        type:type.value,
                        target:target.value,
                        worker:worker.value,
                        sst:sst.value,
                        set:set.value
                    }
                    feed.postExpedition=function(feed){
                        if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                            if(feed.responseXML.getElementsByTagName('ntid')[0].firstChild){
                                var ntid=feed.responseXML.getElementsByTagName('ntid')[0].firstChild.nodeValue;
                                statusField.innerHTML="New task "+ntid+" has been created ~:)~";
                                if(keel.createTask.tidCraver){
                                    keel.createTask.tidCraver.focus();
                                    assistant.setPosition(keel.createTask.tidCraver);
                                    keel.createTask.tidCraver.style.initial=true;
                                    keel.createTask.tidCraver.value=ntid;
                                    keel.createTask.tidCraver.innerHTML=ntid;
                                    keel.createTask.tidCraver=null;
                                }
                            }
                        }else{
                            statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                        }
                        return false;
                    }
                    feed.ferry=new core.shuttle('/lib/adminScripts/taskMgr.php', feed.content, feed.postExpedition, feed);
                    return false;
                },
                createGroup:function(){
                    var label=this.children['label'];
                    var type=this.children['type'];
                    var members=this.children['members'];
                    var authUnits=this.children['authUnits'];
                    if(label.value==''){
                        label.value.focus();
                        statusField.innerHTML="Invalid label ~&|~";
                        return false;
                    }
                    if(type.value==''){
                        type.focus();
                        statusField.innerHTML="Invalid type ~&|~";
                        return false;
                    }
                    var feed=new Object();
                    feed.content={
                        ops:'CNG-'+label.value+','+type.value+','+members.value+','+authUnits.value
                    }
                    feed.postExpedition=function(feed){
                        if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                            if(feed.responseXML.getElementsByTagName('ngid')[0].firstChild){
                                var ngid=feed.responseXML.getElementsByTagName('ngid')[0].firstChild.nodeValue;
                                statusField.innerHTML="New group "+ngid+" has been created ~:)~"
                                if(keel.createGroup.gidCraver){
                                    keel.createGroup.gidCraver.focus();
                                    assistant.setPosition(keel.createGroup.gidCraver);
                                    keel.createGroup.gidCraver.style.initial=true;
                                    keel.createGroup.gidCraver.value=ngid;
                                    keel.createGroup.gidCraver.innerHTML=ngid;
                                    keel.createGroup.gidCraver=null;
                                }
                            }
                        }else{
                            statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                        }
                        return false;
                    }
                    feed.ferry=new core.shuttle('/lib/adminScripts/grpManager.php', feed.content, feed.postExpedition, feed);
                    return false;
                },
                type1Handler:function(){
                    if(this.value=='GROUP'){
                        var gIB=document.createElement('input');
                        gIB.type='text';
                        gIB.id='grpIpBox';
                        gIB.size='10';
                        gIB.maxLength='16';
                        gIB.value='Group Id';
                        gIB.onclick=function(){
                            if(!this.style.initial){
                                this.style.backgroundColor='white';
                                this.style.color='black';
                                this.value='';
                                this.focus();
                                this.style.initial=true;
                                return false;
                            }
                        }
                        gIB.onblur=function(){
                            if(this.value==''){
                                this.style.backgroundColor='lightgray';
                                this.style.color='white';
                                this.value='Group Id';
                                this.style.initial=false;
                            }
                            return false;
                        }
                        gIB.setAttribute("style","background-color: lightgray;color: white; border-width: 1px; border-color: darkGray;");
                        var gCB=document.createElement('button');
                        gCB.id='grpCreatorBtn';
                        gCB.innerHTML='CreateGroup';
                        gCB.onclick=function(){
                            keel.gPF.children['label'].focus();
                            keel.createGroup.gidCraver=keel.oPF.children['grpIpBox'];
                            statusField.innerHTML='First create a group n grab a group id ~:)~';
                            assistant.setPosition(keel.gPF.children['label']);
                            return false;
                        }
                        this.insertAdjacentElement('afterEnd',gIB);
                        gIB.insertAdjacentElement('afterEnd',gCB);
                    }
                    return false;
                },
                createObject:function(){
                    var t1=this.children['type1'];
                    var t2=this.children['type2'];
                    var obns=this.children['objNameStart'];
                    var obne=this.children['objNameEnd'];
                    var fi=this.children['functionId'];
                    var objal=this.children['objAL'];
                    var od=this.children['objDescription'];
                    var uid=this.children['grpIpBox'];
                    var gIB='';
                    if(t1.value=='GROUP'){
                        gIB=this.children['grpIpBox'];
                        if(gIB.value==''){
                            gIB.focus();
                            statusField.innerHTML='Invalid group id ~&|~';
                            return false;
                        }
                    }
                    if(obns.value==''){
                        obns.focus();
                        statusField.innerHTML='Invalid object start name ~&|~';
                        return false;
                    }
                    if(obne.value!=''){
                        if(t2.value=='STUDENT'){
                            if(obns.value.slice(0,8)!=obne.value.slice(0,8)){
                                obne.focus();
                                statusField.innerHTML='invalid range ~&|~';
                                return false;
                            }
                        }
                    }
                    if(fi.value==''){
                        fi.focus();
                        statusField.innerHTML="Invalid function id ~&|~";
                        return false;
                    }
                    if(objal.value==''){
                        objal.focus();
                        statusField.innerHTML="Invalid adminLevel ~&|~";
                        return false;
                    }
                    var feed=new Object();
                    feed.content={
                        ops:'CNO',
                        uid:(uid?uid.value:''),
                        type1:t1.value,
                        type2:t2.value,
                        obns:obns.value,
                        obne:obne.value,
                        fi:fi.value,
                        description:od.value,
                        objal:objal.value,
                        od:od.value,
                        gIB:gIB.value
                    }
                    feed.postExpedition=function(feed){
                        if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                            if(feed.responseXML.getElementsByTagName('noid')[0].firstChild){
                                var noid=feed.responseXML.getElementsByTagName('noid')[0].firstChild.nodeValue;
                                statusField.innerHTML="New object "+noid+" has been created ~:)~";
                                if(keel.createObject.oidCraver){
                                    keel.createObject.oidCraver.focus();
                                    assistant.setPosition(keel.createObject.oidCraver);
                                    keel.createObject.oidCraver.style.initial=true;
                                    keel.createObject.oidCraver.value=ntid;
                                    keel.createObject.oidCraver.innerHTML=ntid;
                                    keel.createObject.oidCraver=null;
                                }
                            }
                        }else{
                            statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                        }
                        return false;
                    }
                    feed.ferry=new core.shuttle('lib/adminScripts/objectMgr.php', feed.content, feed.postExpedition, feed);
                    return false;
                }
            }
            if(location.href.indexOf('keelForm.php')<0){
                var dfgs=document.getElementById('keelScript');
                dfgs.firstChild.addEventListener('DOMNodeInsertedIntoDocument', keel.init, true);
            }
        </script>
    </div>
</body>