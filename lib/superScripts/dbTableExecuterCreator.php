<?php
/* Author: Gowtham */
?>
<span id="ttArea"><h2 id="tableName"><?php echo $dbTable; ?></h2></span>
<form id="columnsGenerator" onsubmit="dbTableExecuter.generateColumns();return false;">
    <label for="noOfSubs">No. of columns:</label><input id="noOfSubs" type="text" size="2" maxlength="2"/>
    <input id="generateColumnsBtn" type="submit" value="generateColumns"/>
</form>
<form id="tableBlock">

</form>
<script type="text/javascript" src="/lib/superScripts/dbTableExecuter_1.js" class="gadgetScript" onload="dbTableExecuterTool.dbTableExecuterjs=true;if(dbTableExecuterTool.allLoadScript)dbTableExecuterTool.allLoadScript();return false;"></script>
<script type="text/javascript" class="gadgetScript">
        dbTableExecuterTool.allLoadScript=function(){
            if(dbTableExecuterTool.dbTableExecuterjs && !dbTableExecuterTool.dbTEolSlded){
                dbTableExecuterTool.dbTEolScript();
                dbTableExecuterTool.dbTEolSlded=true;
            }
            return false;
        }
        dbTableExecuterTool.dbTEolScript=function(){
            dbTableExecuter.tableProperties=new Object();
            dbTableExecuter.tableProperties.tableName='<?php echo $dbTable; ?>';
            window.initGadget=function(dispArea){
            }
        }
</script>
<status>createTable</status>
