<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<head>
    <title>Chat</title>
    <script type="text/javascript" src="/lib/core.js"></script>
    <script type="text/javascript">
        var statusField=document.getElementById('statusField');
    </script>
</head>
<body>
    <div id="statusField"></div><button id="register_Btn" style="display: none"></button>
    <form id="registerationForm" class="gdgBody" onsubmit="if(registeration.register()){formatPage('reg_success');return false;}return false;">
        <h1>Fill in the details and submit:</h1><br/>
        <label>Username: </label><input class="formField" id="username" type="text" size="20" maxlength="20"/><br/>
        <label>Password: </label><input class="formField" id="password" type="password" size="20" maxlength="20" value="123BlackBox"/><br/>
        <label>Retype password: </label><input class="formField" id="rpassword" type="password" size="20" maxlength="20" value="123BlackBox"/><br/>
        <label>Full name: </label><input class="formField" id="fullName" type="text" size="32" maxlength="32"/><br/>
        <label>Nick name: </label><input class="formField" id="nickName" type="text" size="32" maxlength="16"/><br/>
        <label>Gaurdian ID: </label><input class="formField" id="gaurdianID" type="text" size="32" maxlength="32"/><br/>
        <label>Sex: </label><select class="formField" id="sex"><option value="MALE">Male</option><option value="FEMALE">Female</option><option value="INTERSEX">Intersex</option></select><br/>
        <label>DOB: </label>
        <span id="date">
            <select class="formField" id="year" onchange="core.setDay(this.parentElement);return false;">
                <option value="0">Year:</option>
            </select>
            <select class="formField" id="month" onchange="core.setDay(this.parentElement);return false;">
                <option value="0">Month:</option>
            </select>
            <select class="formField" id="day">
                <option value="0">Day:</option>
            </select>
        </span><br/>
        <label>Permanent Address: </label><input class="formField" id="pAddress" type="text" size="32" maxlength="100"/><br/>
        <label>Telephone no: </label><input class="formField" id="tel1" type="text" size="32" maxlength="14" onchange=""/><br/>
        <label>Telephone no: </label><input class="formField" id="tel2" type="text" size="32" maxlength="14" onchange=""/><br/>
        <label>Email ID: </label><input class="formField" id="emailID" type="text" size="32" maxlength="32" onchange=""/><br/>
        <div id="userPicBox">
            <div id="userPicArea">
                <img id="userPic" style="width: 150px;height: 200px" src="/images/guest.jpg"/>
            </div>
        </div>
        <?php
        $root = realpath($_SERVER["DOCUMENT_ROOT"]);
        require "$root/lib/recaptchalib.php";
        $publickey = "6Lf7sdASAAAAAJGJrI4mKwMiSEr_5eYsVVhHpenV"; // you got this from the signup page
        echo recaptcha_get_html($publickey,null,true);
        ?>
        <input type="submit"/><br/>
        <script type="text/javascript" id="registerationScript" onload="registeration.init();return false;">
            var registeration={
                init:function(){
                    //this.removeEventListener('DOMNodeInsertedIntoDocument',arguments.callee,false);
                    statusField.innerHTML="Registeration form loaded successfully ~:)~";
                    var rf=registeration.form;
                    core.dateFieldFill(rf.children['date']);
                    core.formatPage('registerationForm',document.getElementById('register_Btn'));
                    var upb=rf.children['userPicBox'];
                    upb.innerHTML=upb.innerHTML+"<div class='qq-upload-button' id='uploadBtn' style='position: relative; overflow-x: hidden; overflow-y: hidden; direction: ltr; '><span id='uploadBtnTxt'>Add pic</span><input id=\"realBtn\" onchange=\"this.parentElement.children['uploadBtnTxt'].innerHTML='Change Pic';core.uploadFile.apply(this,[this.parentElement.parentElement.children['upldInfo']]); return false;\" multiple='multiple' type='file' name='file' style='position: absolute; right: 0px; top: 0px; cursor: pointer; opacity: 0; '></div><div id='upldInfo'></div>";
                    upb.value=upb.children['userPicArea'].children['userPic'].src;
                    upb.children['uploadBtn'].children['realBtn'].postUpload=function(feed){
                        var usrPicBox=feed.elm.parentElement.parentElement;
                        usrPicBox.value=feed.response.fileName
                        usrPicBox.children['userPicArea'].children['userPic'].src=feed.response.fileName;
                        feed.elm.parentElement.children['uploadBtnTxt'].innerHTML='Change pic';
                    }
                },
                register:function (){
                    var rfc=registeration.form.children;
                    var dateDiv=rfc['date'];
                    var DOB=dateDiv.children['year'].value+'-'+dateDiv.children['month'].value+'-'+dateDiv.children['day'].value;
                    if(validateForm()){
                        var username=rfc['username'].value;
                        var password=core.MD5(rfc['password'].value);
                        var fullName=rfc['fullName'].value;
                        var nickName=rfc['nickName'].value;
                        var gaurdianID=rfc['gaurdianID'].value;
                        var sex=rfc['sex'].value;
                        var pAddress=rfc['pAddress'].value;
                        var tel1=rfc['tel1'].value;
                        var tel2=rfc['tel2'].value;
                        var emailID=rfc['emailID'].value;
                        var photoID=rfc['userPicBox'].value.split('/');
                        photoID=photoID[photoID.length-1];
                        var feed=new Object();
                        feed.postExpedition=function(feed){
                            if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                                statusField.innerHTML='successfully registered';
                                var sm=document.createElement('p');
                                sm.innerHTML='now u can login ~:D~';
                                var rf=registeration.form;
                                rf.parentElement.replaceChild(sm,rf);
                                core.formatPage('home');
                            }else if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue.match(/Duplicate entry .* 'username'/)){
                                core.statusField.innerHTML="username already exists! choose a different username.";
                            }else{
                                statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                            }
                        }
                        feed.content={
                            username:username,
                            password:password,
                            fullName:fullName,
                            nickName:nickName,
                            gaurdianID:gaurdianID,
                            sex:sex,
                            DOB:DOB,
                            pAddress:pAddress,
                            tel1:tel1,
                            tel2:tel2,
                            emailID:emailID,
                            photoID:photoID,
                            recaptcha_challenge_field:rfc['recaptcha_challenge_field'].value,
                            recaptcha_response_field:'manual_challenge'
                        }
                        feed.ferry=new core.shuttle("/lib/adminScripts/registerUsers.php", null, feed.postExpedition, feed)
                    }
                    function validateForm() {
                        if (validator.isUsername(rfc['username'])) {
                            if (validator.isPassword(rfc['password'],rfc['rpassword'])) {
                                if (validator.isFullName(rfc['fullName'])) {
                                    if (validator.isEmailID(rfc['emailID'])) {
                                        if (validator.isGaurdianID(rfc['gaurdianID'])) {
                                            if (validator.isDOB(DOB)) {
                                                if(validator.isPAdress(rfc['pAddress'])){
                                                    if(validator.isTel(rfc['tel1'])){
                                                        if(validator.isTel(rfc['tel2'])){
                                                            return true;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        return false;
                    }
                }
            }
            /*if(location.href.indexOf('registerationForm.php')<0){
                var dfgs=document.getElementById('registerationScript');
                dfgs.firstChild.addEventListener('DOMNodeInsertedIntoDocument', registeration.init, true);
            }*/
        </script>
        <script id="scopeSpecifier" type="text/javascript" src="/lib/dummy.js" onload="registeration.form=this.parentElement;registeration.init()"></script>
    </form>
</body>

