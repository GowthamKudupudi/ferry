<?php
include 'authorize.php';
require_once 'inc.php';
include 'db_login.php';
$query = "select * from user_profiles where `index`='" . $_SESSION['pid'] . "'";
$result = mysql_db_query('collegedb2admin', $query);
$error1 = mysql_error();
$full_name = mysql_result($result, 0, "full_name");
$nickName = mysql_result($result, 0, "nickName");
$gaurdian_id = mysql_result($result, 0, "gaurdian_id");
$sex = mysql_result($result, 0, 'sex');
$dob = mysql_result($result, 0, 'DOB');
$p_address = mysql_result($result, 0, 'permenent_address');
$tel1 = mysql_result($result, 0, 'telephone_no1');
$tel2 = mysql_result($result, 0, 'telephone_no2');
$email_id = mysql_result($result, 0, 'email_id');
$year = $dob[0] . $dob[1] . $dob[2] . $dob[3];
$month = $dob[5] . $dob[6];
$day = $dob[8] . $dob[9];
?>
<head>
    <title>User profile</title>
    <script type="text/javascript" src="/lib/core.js"></script>
    <script type="text/javascript">
        var statusField=document.getElementById('statusField');
    </script>
</head>
<body>
    <div id="statusField"></div><button id="userProfileBtn" style="display: none"></button>
    <div id="userProfile" class="gdgBody">
        <span id="heading" style="font-size:18px">Ur Profile: </span><br/><br/>
        <label>Full name: </label><span class="formField" id="fullName"><?php echo $full_name ?></span><br/>
        <label>Nick name: </label><span class="formField" id="nickName"><?php echo $nickName ?></span><br/>
        <label>Gaurdian ID: </label><span class="formField" id="gaurdianId"><?php echo $gaurdian_id ?></span><br/>
        <label>Sex: </label><span class="formField" id="sex"><?php echo $sex; ?></span><br/>
        <label>DOB: </label><span class="" id="dob"><?php echo $dob; ?></span><br/>
        <label>Permanent Address: </label><span class="formField" id="pAddress"><?php echo $p_address; ?></span><br/>
        <label>Telephone no: </label><span class="formField" id="tel1"><?php echo $tel1 ?></span><br/>
        <label>Telephone no: </label><span class="formField" id="tel2"><?php echo $tel2 ?></span><br/>
        <label>Email ID: </label><span class="formField" id="emailId"><?php echo $email_id ?></span><br/>
        <label>Photo ID: </label>
        <div id="userPicBox">
            <div id="userPicArea"></div>
        </div>
        <button id="editProfileBtn" onclick="userProfileView.editProfile.call(this);">Edit</button>
        <script type="text/javascript" id="userProfileViewScript" onload="userProfileView.init();return false;">
            var userProfileView={
                init:function(){
                    this.removeEventListener('DOMNodeInsertedIntoDocument',arguments.callee,false);
                    statusField.innerHTML="User profile loaded successfully ~:)~";
                    core.formatPage('userProfile',document.getElementById('userProfileBtn'));
                    var up=document.getElementById('userProfile').children['userPicBox'].children['userPicArea'].appendChild(core.userPic.cloneNode(true));
                    up.style.display=null;
                    document.getElementById('userProfile').children['userPicBox'].value=core.userPic.src;
                },
                save:function (){
                    statusField.innerHTML="Saving profile changes... ~@|~"
                    var u=userProfileView;
                    var DOB=u.db.children['year'].value+'-'+u.db.children['month'].value+'-'+u.db.children['day'].value;
                    if(validateForm()){
                        var fullName=u.nfn.value;
                        var gaurdianID=u.gid.value;
                        var sex=u.sx.value;
                        var pAddress=u.npa.value;
                        var tel1=u.nt1.value;
                        var tel2=u.nt2.value;
                        var emailID=u.neid.value;
                        var photoID=u.upb.value.split('/');
                        photoID=photoID[photoID.length-1];
                        var feed=new Object();
                        feed.photoId=photoID;
                        feed.postExpediton=function(feed){
                            if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                                statusField.innerHTML='profile updated..';
                                var u=userProfileView;
                                u.fullName.innerHTML=u.nfn.value;
                                u.nickName.innerHTML=u.nn.value;
                                u.gaurdianId.innerHTML=u.gid.value;
                                u.sex.innerHTML=u.sx.value;
                                u.dob.innerHTML=u.db.children['year'].value+'-'+u.db.children['month'].value+'-'+u.db.children['day'].value;
                                u.pa.innerHTML=u.npa.value;
                                u.t1.innerHTML=u.nt1.value;
                                u.t2.innerHTML=u.nt2.value;
                                u.emId.innerHTML=u.neid.value;
                                u.upb.children['userPicArea'].src='/userFiles/'+core.user+'/'+feed.photoId;
                                core.userPic.src='/userFiles/'+core.user+'/'+feed.photoId;
                                var up=u.nfn.parentElement;
                                up.replaceChild(u.fullName,u.nfn);
                                up.replaceChild(u.nickName,u.nn);
                                up.replaceChild(u.gaurdianId,u.gid)
                                up.replaceChild(u.sex,u.sx);
                                up.replaceChild(u.dob,u.db);
                                up.replaceChild(u.pa,u.npa);
                                up.replaceChild(u.t1,u.nt1);
                                up.replaceChild(u.t2,u.nt2);
                                up.replaceChild(u.emId,u.neid);
                                up.replaceChild(u.epb,u.sb);
                                u.upb.removeChild(u.upb.children['uploadBtn']);
                                u.upb.removeChild(u.upb.children['upldInfo']);
                            } else{
                                core.statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                            }
                        }
                        feed.ferry=new core.shuttle('/lib/adminScripts/editProfile.php', 'fullName='+fullName+'&nickName='+u.nn.value+'&gaurdianID='+gaurdianID+'&sex='+sex+'&DOB='+DOB+'&pAddress='+pAddress+'&tel1='+tel1+'&tel2='+tel2+'&emailID='+emailID+'&photoID='+photoID, feed.postExpediton, feed)
                    }
                    function validateForm() {
                        if (validator.isFullName(u.nfn)) {
                            if (validator.isEmailID(u.neid)) {
                                if (validator.isGaurdianID(u.gid)) {
                                    if (validator.isDOB(DOB)) {
                                        if(validator.isPAdress(u.npa)){
                                            if(validator.isTel(u.nt1)){
                                                if(validator.isTel(u.nt2)){
                                                    return true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        return false;
                    }
                    return false;
                },
                editProfile:function(){
                    var up=this.parentElement;
                    var fullName=up.children['fullName'];
                    var nickName=up.children['nickName'];
                    var gaurdianId=up.children['gaurdianId'];
                    var sex=up.children['sex'];
                    var dob=up.children['dob'];
                    var pa=up.children['pAddress'];
                    var t1=up.children['tel1'];
                    var t2=up.children['tel2'];
                    var emId=up.children['emailId'];
                    var upb=up.children['userPicBox'];
                    var epb=up.children['editProfileBtn'];
                    var nfn=document.createElement('input');
                    nfn.id='fullName';
                    nfn.type='text';
                    nfn.value=fullName.innerHTML;
                    var nn=document.createElement('input');
                    nn.id='nickName';
                    nn.type='text';
                    nn.value=nickName.innerHTML;
                    var gid=document.createElement('input');
                    gid.id='gaurdianID';
                    gid.type='text';
                    gid.value=gaurdianId.innerHTML;
                    var sx=document.createElement('select');
                    sx.id='sex';
                    sx.innerHTML="<option value='MALE'>Male</option><option value='FEMALE'>Female</option><option value='INTERSEX'>Intersex</option>";
                    sx.value=sex.innerHTML;
                    var db=document.createElement('span');
                    db.id='date';
                    var yr=document.createElement('select');
                    yr.id="year";
                    var mnth=document.createElement('select');
                    mnth.id="month";
                    var day=document.createElement('select');
                    day.id="day";
                    db.appendChild(yr);
                    db.appendChild(mnth);
                    db.appendChild(day);
                    core.dateFieldFill(db);
                    var dobMat=dob.innerHTML.split('-');
                    yr.value=dobMat[0];
                    if(dobMat[1][0]=='0'){
                        mnth.value=dobMat[1][1];
                    }else{
                        mnth.value=dobMat[1];
                    }
                    core.setDay(db);
                    if(dobMat[2][0]=='0'){
                        day.value=dobMat[2][1];
                    }else{
                        day.value=dobMat[2];
                    }
                    var npa=document.createElement('input');
                    npa.id='pAddress';
                    npa.type='text';
                    npa.value=pa.innerHTML;
                    var nt1=document.createElement('input');
                    nt1.id='tel1';
                    nt1.type='text';
                    nt1.value=t1.innerHTML;
                    var nt2=document.createElement('input');
                    nt2.id='tel2';
                    nt2.type='text';
                    nt2.value=t2.innerHTML;
                    var neid=document.createElement('input');
                    neid.id='emailId';
                    neid.type='text';
                    neid.value=emId.innerHTML;
                    var sb=document.createElement('button');
                    sb.id='submitBtn';
                    sb.innerHTML='submit';
                    sb.onclick=userProfileView.save;
                    up.replaceChild(nfn,fullName);
                    up.replaceChild(nn,nickName);
                    up.replaceChild(gid,gaurdianId)
                    up.replaceChild(sx,sex);
                    up.replaceChild(db,dob);
                    up.replaceChild(npa,pa);
                    up.replaceChild(nt1,t1);
                    up.replaceChild(nt2,t2);
                    up.replaceChild(neid,emId);
                    upb.innerHTML=upb.innerHTML+"<div class='qq-upload-button' id='uploadBtn' style='position: relative; overflow-x: hidden; overflow-y: hidden; direction: ltr; '><span id='uploadBtnTxt'>Change pic</span><input id=\"realBtn\" onchange=\"core.uploadFile.apply(this,[this.parentElement.parentElement.children['upldInfo']]); return false;\" multiple='multiple' type='file' name='file' style='position: absolute; right: 0px; top: 0px; cursor: pointer; opacity: 0; '></div><div id='upldInfo'></div>";
                    if(core.userPic.src.indexOf('/guest.jpg')>-1){
                        document.getElementById('uploadBtnTxt').innerHTML='Add a pic';
                    }
                    upb.children['uploadBtn'].children['realBtn'].postUpload=function(feed){
                        var usrPicBox=feed.elm.parentElement.parentElement;
                        usrPicBox.value=feed.response.fileName
                        usrPicBox.children['userPicArea'].children['userPic'].src=feed.response.fileName;
                        feed.elm.parentElement.children['uploadBtnTxt'].innerHTML='Change pic';
                    }
                    up.replaceChild(sb,epb);
                    var u=userProfileView;
                    u.nfn=nfn;
                    u.fullName=fullName;
                    u.nn=nn;
                    u.nickName=nickName;
                    u.gid=gid;
                    u.gaurdianId=gaurdianId;
                    u.sx=sx;
                    u.sex=sex;
                    u.db=db;
                    u.dob=dob;
                    u.npa=npa;
                    u.pa=pa;
                    u.nt1=nt1;
                    u.t1=t1;
                    u.nt2=nt2;
                    u.t2=t2;
                    u.neid=neid;
                    u.emId=emId;
                    u.sb=sb;
                    u.epb=epb;
                    u.upb=upb;
                }
            }
            if(location.href.indexOf('userProfile.php')<0){
                var dfgs=document.getElementById('userProfileViewScript');
                dfgs.firstChild.addEventListener('DOMNodeInsertedIntoDocument', userProfileView.init, true);
            }
        </script>
    </div>
</body>