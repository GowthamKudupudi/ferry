<?php
session_start();
@$_SESSION['lastSessionStartTime'] = $_SESSION['sessionStartTime'];
$_SESSION['sessionStartTime'] = time();
if (@$_SESSION['authenticate']) {
    if ($_SESSION['lastSessionStartTime'] < $_SERVER['sessionStartTime'] - 3000) {
        include 'signOut.php';
    }
}
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/lib/inc.php";
require "$root/conf.php";

function usernameEchoer() {
    if (isset($_SESSION['username']))
        echo $_SESSION['username'];else
        echo "guest";
}

function positionEchoer() {
    if (isset($_SESSION['function'])) {
        foreach (@$_SESSION['function'] as $oid => $array) {
            echo ",o" . $oid . ":{label:'" . $_SESSION['function'][$oid]['label'] . "',func:'" . $_SESSION['function'][$oid]['func'] . "',aL:'" . $_SESSION['function'][$oid]['aL'] . "',oid:'" . $oid . "',id:'" . $_SESSION['function'][$oid]['id'] . "'}";
        };
    }
}

function sessionIdEchoer() {
    if (@$_SESSION['authenticated']) {
        echo session_id();
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Ferry @ <?php echo $orgName; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
        <link href="lib/coreStyleSheet_1.css" type="text/css" rel="stylesheet"/>
        <script type="text/javascript" src="/lib/core.js"></script>
        <script type="text/javascript" src="/lib/date.js"></script>
        <script type="text/javascript" src="/lib/formValidator.js"></script>
        <script type="text/javascript" src="/lib/blowfish.js"></script>
        <script type="text/javascript" src="/lib/2.5.3-crypto-sha256.js"></script>
        <script type='text/javascript' src='/lib/jQueryUI1.8.2/js/jquery-1.7.2.min.js'></script>
        <script type='text/javascript' src='/lib/jQueryUI1.8.2/js/jquery-ui-1.8.22.custom.min.js'></script>
        <link href="/lib/jQueryUI1.8.2/css/ui-lightness/jquery-ui-1.8.22.custom.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="/lib/caja.js"></script>
        <script type="text/javascript">
            core.authenticated=<?php authenticated(); ?>;
            core.user='<?php usernameEchoer(); ?>';
            core.userPicURL="<?php echo @$_SESSION['userPic'] ?>";
            core.init=function(){
                window.sessionId='<?php sessionIdEchoer(); ?>';
                core.statusField=document.getElementById('statusField');
                core.mbody=document.getElementById('body');
                core.body=document.getElementById('bodySpace');
                core.header=document.getElementById('header');
                clipBoard=core.clipBoard=document.getElementById('clipBoard');
                selectRectangle=core.selectRectangle=document.getElementById('selectRectangle');
                document.body.selectStartFunc=function(){event.preventDefault();return false;}
                document.body.addEventListener('selectstart',document.body.selectStartFunc,false);
                core.body.addEventListener('mousedown',core.selectOnDrag,false);
                core.body.clickFunc=function(){
                    selectRectangle.style.display='none';
                    selectRectangle.selecting=false;
                }
                core.body.addEventListener('click',core.body.clickFunc,false);
                cmenu=document.getElementById('cmenu');
                cmenu.onblurFunc=function(){
                    this.style.display='none';
                    this.innerHTML='';
                    return false;
                }
                cmenu.onblur=cmenu.onblurFunc;
                selectedElements=new core.nodeList();
                core.alignDoc();
                homeBtn=new core.animatedImage(['/images/home.png','/images/home1.png','/images/home2.png'], function(){core.formatPage('home');statusField.innerHTML='Yeah! its the Home page ~:D~';return false;},document.getElementById('homeBtn'));
                homeBtn.trigMouseEvt();
                logBtn=document.getElementById('logBtn');
                logPanel=new core.msgPanel('log', core.header, logBtn, document.createElement('div'), null);
                logPanel.style.display='none';
                logPanel.style.overflow='auto';
                logBtn.logPanel=logPanel;
                core.logStatus.count=0;
                window.onresize=core.alignDoc;
                core.userInfo.positions={
                    INDIVIDUAL:{
                        label:'INDIVIDUAL',
                        func:'',
                        aL:'',
                        oid:'',
                        id:''
                    }<?php positionEchoer(); ?>
                }
                core.userPic.id="userPic";
                core.userPic.exists=true;
                core.userPic.style.display='inline';
                core.userPic.onerror=function(){
                    core.userPic.src='/images/guest.jpg'; 
                    core.userPic.exists=false;
                };
                core.userPic.width=150;
                core.userPic.height=200;
                core.transit();
                //assign key events.
                document.getElementById('searchTool').onsubmit=core.search;
                document.getElementById('objectizeBtn').onclick=core.objectize;
                //declare global variables
                user=core.user;
                statusField=core.statusField;
                userPic=core.userPic;
                statusField.addEventListener("DOMSubtreeModified",core.logStatus,false);
                clipBoard.afterPaste=function(){};
                clipBoard.addEventListener("paste",function(){clipBoard.innerHTML='';clipBoard.opType='paste';window.setTimeout(function(){cbh.style.display='none'}, 90)},false);
                clipBoard.addEventListener("DOMSubtreeModified",core.clipBoardAction,false);
                clipBoard.addEventListener("copy",function(){core.selectElementContents(clipBoard);clipBoard.opType='copyDone';statusField.innerHTML='Copy done ~:)~';window.setTimeout(function(){cbh.style.display='none';}, 90);},false);
                window.assistant=new core.assistant();
                document.body.style.display=null;
                document.body.customCMenuElms=[];
                dcmenu=document.getElementById('dcmenu');
                core.addContextMenu(selectRectangle, document.createElement('div'));
                core.addContextMenu(document.body,dcmenu);
            }
            core.transit=function(){
                if(core.authenticated){
                    var roleTool=document.getElementById('roleTool');
                    var roleS=roleTool.children['role'];
                    roleS.innerHTML='';
                    core.userPic.src=core.userPic.src || "userFiles/"+core.user+"/"+core.userPicURL;
                    for(var i in core.userInfo.positions){
                        var option=document.createElement('option');
                        option.value=core.userInfo.positions[i].oid;
                        option.title=core.userInfo.positions[i].id;
                        option.innerHTML=core.userInfo.positions[i].label;
                        roleS.appendChild(option);
                    }
                    core.loadUserTools();
                }else{
                    core.authenticated=false;
                    core.userPic.src="";
                    core.user="guest";
                }
                core.formatPage('home');
            }
            var statusField=null
            window.onload=core.init;
        </script>
    </head>
    <body style="display:none">
        <?php
        include 'header.php';
        include 'body.php';
        include 'footer.php';
        ?>
        <iframe id="dummyIf" style="display: none"></iframe>
    </body>
</html>