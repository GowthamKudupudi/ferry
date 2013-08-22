<?php
/* Author: Gowtham */
//2012-03-21 10:14:30
?>
<!DOCTYPE html>
<head>
    <title>Data Grabber</title>
</head>
<body>
    <div id="DataGrabberDiv" class="gdgBody">
        <h2 id="title">Data Grabber</h2>
        <form id="mapper" onsubmit="dataGrabber.genStructMapper.call(this);return false;">
            <label>Group: </label><input title="type in the group id of student group" id="group" size="15" /><br/>
            <label>Table Name: </label><input title="type in the table name to which u download the data" id="tableName"/><select id="tableType" title="Select type of table"><option value="STUMARKS">StudentsMarks</option></select><br/>
            <label>Source URL: </label><input title="enter the url from which u want to download data" id="srcURL"/><br/>
            <input id="srcStructMapBtn" type="submit" value="GenerateSourceStructureMapper"/>
        </form>
        <script id="dGScript" type="text/javascript">
            window.dataGrabber={
                dge:'DataGrabberDiv element',
                init:function(){
                    this.removeEventListener('DOMNodeInsertedIntoDocument',arguments.callee,false);
                    dataGrabber.dge=document.getElementById('DataGrabberDiv');
                    return false;
                },
                genStructMapper:function(){
                    if(this.children['srcStructMapBtn'].trueChild)this.removeChild(this.children['srcStructMapBtn'].trueChild);
                    statusField.innerHTML='generating table structure mapper...';
                    var feed=new Object();
                    feed.content={
                        dbTable:this.children['tableName'].value,
                        grpId:this.children['group'].value,
                        srcurl:this.children['srcURL'].value,
                        tableType:this.children['tableType'].value,
                        opType:'GENSTRUCT'
                    }
                    feed.elm=this;
                    feed.postExpedition=function(feed){
                        if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                            var mapper=document.createElement('div');
                            mapper.innerHTML=feed.responseXML.getElementsByTagName('mapper')[0].textContent;
                            mapper=mapper.cloneNode(true);
                            feed.elm.appendChild(mapper);
                            feed.elm.children['srcStructMapBtn'].trueChild=mapper;
                        }else{
                            statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue;
                        }
                        return false;
                    }
                    feed.ferry=new core.shuttle('/lib/dataGrabber.php', feed.content, feed.postExpedition, feed);
                    return false;
                }
            }
            if(location.href.indexOf('dataGrabberForm.php')<0){
                var dfgs=document.getElementById('dGScript');
                dfgs.firstChild.addEventListener('DOMNodeInsertedIntoDocument', dataGrabber.init, true);
            }
        </script>
    </div>
</body>