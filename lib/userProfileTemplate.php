<?php
/* Author: Gowtham */

function photoIdEchoer($username,$photoId) {
    if ($photoId)
        echo "/userFiles/" . $username . "/" . $photoId;
    else
        echo "/images/guest.jpg";
}
?>
<div id="profile" style="position: relative">
    <label>Full name: </label><span class="formField" id="fullName"><?php echo $full_name ?></span><br/>
    <label>Gaurdian ID: </label><span class="formField" id="gaurdianID"><?php echo $gaurdian_id ?></span><br/>
    <label>Sex: </label><span class="formField" id="sex"><?php echo $sex; ?></span><br/>
    <label>DOB: </label><span class="" id="dob"><?php echo $dob; ?></span><br/>
    <label>Permanent Address: </label><span class="formField" id="pAddress"><?php echo $p_address; ?></span><br/>
    <label>Telephone no: </label><span class="formField" id="tel1"><?php echo $tel1 ?></span><br/>
    <label>Telephone no: </label><span class="formField" id="tel2"><?php echo $tel2 ?></span><br/>
    <label>Email ID: </label><span class="formField" id="emailID"><?php echo $email_id ?></span><br/>
    <label>Photo ID: </label>
    <div id="studentPicBox">
        <div id="studentPicInnerBox">
            <div id="studentPicArea"><img id="studentPic" width="150" height="200" src="<?php photoIdEchoer($username,$photoId);?>"/></div>
        </div>
    </div>
    <img id="print" style="position: absolute; bottom: 2px; right: 2px" src="/images/print.png"/>
    <script type="text/javascript" id="onload" src="/lib/dummy.js" onload="var pi=new core.animatedImage(['/images/print.png','/images/print1.png','/images/print2.png'], core.print, this.parentElement.children['print']);pi.trigMouseEvt();return false;"></script>
    <script id="printStyleScript" type="text/javascript" >
        if(window.printWin){
            var p=document.getElementById('print');
            p.parentElement.removeChild(p);
        }
    </script>
</div>