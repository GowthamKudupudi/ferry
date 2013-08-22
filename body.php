<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div id="body">    
    <div id="headerSpace" style="height: 70px" class="home"></div>
    <div id="bodySpace">
        <div id="userTools" class="home"></div>
        <!--object class="home" width="100%" height="232" data="/images/bannerfile.swf" type="application/x-shockwave-flash">
            <param name="src" value="/multi/bannerfile.swf">
            <param name="wmode" value="transparent">
        </object-->
        <div id="cbh" style="display:none;background-color:white;position:fixed;z-index:101;width:200px;height:50px;border:1px solid blue;box-shadow:1px 1px 0 rgba(0, 0, 0, 0.45) inset, -1px -1px 0 rgba(0, 0, 0, 0.45) inset, 0 2px 5px rgba(0,0,0,0.25);font-size: 16px;text-align: center;color: darkred;">Choose paste in context menu of this box or Ctrl+v.<div id="clipBoard" class="home" tabindex="0" onmousedown="event.cancelBubble = true;" oncontextmenu="event.cancelBubble=true;" onblur="this.focus();" style="color:black;background-color:white;width:200px;height:50px;position:absolute;top:0px;left:0px;opacity:0" contenteditable></div></div>
        <div id="selectRectangle" onmousedown="event.cancelBubble = true;" style="display:none;position:absolute;z-index:100;opacity:0.2;background-color:#00f;border:1px solid #008"></div>
    </div>
    <div id="cmenu" style="cursor:default; display: none; position: absolute; background-color: lightgrey; z-index: 101;padding:1px;border: 1px solid #bbf" tabindex="0" onmousedown="event.cancelBubble = true;" oncontextmenu="event.cancelBubble=true;event.preventDefault();"></div>
    <div id="dcmenu" style="display:none">
        <div id="allowDefaultContextMenuForNsec" style="padding:1px;border:1px solid #DDF;border-collapse: collapse" class="cmenuItem" onmousedown="cmenu.onblur = cmenu.onblurFunc;
                core.defaultCMenu.call(this);
                cmenu.onblurFunc.call(cmenu);">AllowDefaultContextMenuFor<input id="time" style="width:15px" value="10" onmousedown="cmenu.onblur = null;
                event.cancelBubble = true;" onclick="event.cancelBubble = true;" onkeydown="event.cancelBubble = true;
                if (event.keyCode == 13) {
                    this.parentElement.onmousedown.call(this.parentElement)
                }
                ;" onblur="cmenu.onblur = cmenu.onblurFunc;
                cmenu.onblur.call(cmenu)"/>sec</div>
    </div>
</div>