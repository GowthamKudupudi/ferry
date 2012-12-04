<?php
//Author: satya gowtham kudupudi
//2012-03-07 11:29:30


?>

<div id="header">
    <img id="logo" src="images/logo.png" style="height: 54px"/>
    <div id="topHLine">
        <span id="welcomeUser">
            Hi <span id="user"></span>.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </span>
        <button id="userProfileBtn" onclick="core.genForm('userProfile',this);return false;">ViewProfile</button>
        <form id="signInTool" onsubmit="event.preventDefault();core.signIn(document.getElementById('username').value,document.getElementById('password').value);return false;" target="dummyIf">
            <label for="username">Username: </label><input id="username" type="text"/>
            <label for="password">Password: </label><input id="password" type="password"/>
            <input type="submit" value="SignIn"/>
        </form>
        <button id="register_Btn" onclick="core.genForm('registerationForm',this); return false;" style="display:none">Register</button>
        <span id="signOutTool">
            <button onclick="core.signOut();">SignOut</button>
        </span>
        <span id="roleTool">
            <span>Role: </span>
            <select id="role" onchange="core.userInfo.role=this.value; return false;"></select>
        </span>
    </div>
    <div id="bottomHLine">
        <img id="homeBtn" src="">
        <span id="statusBar"><button id="logBtn" onclick="if(this.logPanel.style.display=='none'){this.logPanel.style.display=null}if(!document.getElementById('logPanel')){core.header.appendChild(this.logPanel);}this.logPanel.reAlign();return false;">Log</button>&nbsp;<span id="statusField">Welcome to <?php echo $orgName;?></span></span>
    </div>
    <form id="searchTool" class="home" target="dummyIf">
        <input id="searchMouth" style="margin-left: 2px; width:207px" onfocus="if(this.style.color!='black'){this.style.color='black';this.value='';return false;}" onblur="if(this.value==''){this.style.color='grey';this.value='Search';}return false;" value="Search"/>
        <input type="submit" id="searchBtn" value="" title="Search"/>
        <button id="objectizeBtn" title="Objectize"></button><br/>
        <input id="passKeyBox" style="margin-left: 2px; width:207px" onfocus="if(this.style.color!='black'){this.style.color='black';this.value='';return false;}" onblur="if(this.value==''){this.style.color='grey';this.value='Type-in PassKey';}return false;" value="Type-in PassKey" />
        <div id="displayArea" class="subWindow">
            <style type="text/css" scoped>

            </style>
        </div>
    </form>
</div>