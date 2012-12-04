<style type="text/css" scoped>
    root { 
        display: block;
    }

    .inputCell{
        margin: 0px;
    }

    td.dc.active{
        background-color: #88f
    }

    td.dc.selected{
        background-color: #bbf
    }

    td.dc:hover{
        background-color: #bbf
    }

    td.dc.active:hover{
        background-color: #88f
    }

    td.dc.selected:hover{
        background-color: #bbf
    }

    .cmenuItem:hover{
        background-color: #bbf
    }
</style>
<script type="text/javascript" class="gadgetScript">
    dbTableExecuterTool.dbTEolSlded=false;
    dbTableExecuterTool.sortablejs=false;
    dbTableExecuterTool.dbTableExecuterjs=false;
    dbTableExecuterTool.allLoadScript=function(){
        if(dbTableExecuterTool.sortablejs && dbTableExecuterTool.dbTableExecuterjs && !dbTableExecuterTool.dbTEolSlded){
            dbTableExecuterTool.dbTEolScript();
            dbTableExecuterTool.dbTEolSlded=true;
        }
        return false;
    };
    dbTableExecuterTool.dbTEolScript=function(){
        dbTableExecuter.tables=dbTableExecuter.tables||{};
        if(dbTableExecuter.frontTable){
            var df=dbTableExecuter.tables[dbTableExecuter.frontTable].facade;
            df.parentElement.removeChild(df);
        }
        dbTableExecuter.frontTable="<?php echo $dbTable ?>";
        var ft=dbTableExecuter.frontTable;
        dbTableExecuter.tables[ft]={};
        dbTableExecuter.tables[ft]['tableCellProps']=JSON.parse('<?php tableFormulaEchoer($root, $dbTable, $mems, $liveDBTable, $fc, $sm) ?>');
        dbTableExecuter.tables[ft]['facade']=document.createElement('div');
        dbTableExecuter.tables[ft]['facade'].id='facade';
        dbTableExecuter.tables[ft]['facade'].innerHTML="<div id='ttArea' style='position:relative'><span id='tableName' style='font-size:22px; line-height:30px' class='userSpecific' ondblclick='dbTableExecuter.rename.call(this);return false;'>"+ft+"</span>&nbsp;&nbsp;<span id='rowCount'></span><span id='colCount'></span></div>";
        dbTableExecuter.tables[ft]['rowCount']=dbTableExecuter.tables[ft]['facade'].children['ttArea'].children['rowCount'];
        dbTableExecuter.tables[ft]['colCount']=dbTableExecuter.tables[ft]['facade'].children['ttArea'].children['colCount'];
        dbTableExecuter.tables[ft].dTable=new dbTableExecuter.genTable(dbTableExecuter.tables[ft]['facade'],ft);
        sortable.ts_makeSortable(dbTableExecuter.tables[ft].dTable.table);
        dbTableExecuterTool.dispArea.children['dbTableExecuterBdy'].appendChild(dbTableExecuter.tables[ft]['facade']);
        if(!dbTableExecuter.sandbox){
            dbTableExecuter.sandbox=new Worker('/lib/formulaEvaluator.js');
            dbTableExecuter.sandbox.onmessage=dbTableExecuter.receiveComputedData;
            dbTableExecuter.sandbox.aUVO={};
            dbTableExecuter.sandbox.cells=[];
            dbTableExecuter.sandbox.sUVAs=[];
            dbTableExecuter.sandbox.f=0;
        }
        dbTableExecuter.updateAllCells(ft);
        if(dbTableExecuterTool.cmenu && !dbTableExecuter.tables[ft].dTable.table.cmenu)core.addContextMenu(dbTableExecuter.tables[ft].dTable.table,dbTableExecuterTool.cmenu);
        core.mbody.addEventListener("scroll",function(){
            var nvr=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rowWithId('newVRow');
            var ttA=dbTableExecuter.tables[dbTableExecuter.frontTable].facade.children['ttArea'];
            if(core.mbody.scrollLeft!=0){
                nvr.style.position='absolute';
                ttA.style.left=nvr.style.left=core.mbody.scrollLeft+'px';
            }else{
                ttA.style.left=nvr.style.position=null;
            }
        },false)
        statusField.innerHTML='Table opened.';
        if(dbTableExecuterTool.rFilter){
            statusField.innerHTML='Filtering rows ~@|~';
            dbTableExecuter.filterRows(dbTableExecuterTool.rFilter);
        }else{
            dbTableExecuter.tables[ft]['rowCount'].innerHTML=dbTableExecuter.tables[ft].dTable.tbody.rows.length-1+" rows,";
        }
        if(dbTableExecuterTool.cString){
            statusField.innerHTML='Constraining columns ~@|~';
            dbTableExecuter.constrainColumns(dbTableExecuterTool.cString);
        }else{
            dbTableExecuter.tables[ft]['colCount'].innerHTML="&nbsp;"+dbTableExecuter.tables[ft].dTable.table.colCount+" columns";
        }
        if(dbTableExecuterTool.sString){
            statusField.innerHTML='Searching table...';
            dbTableExecuter.searchTable(dbTableExecuterTool.sString, dbTableExecuter.tables[ft].dTable.tbody,dbTableExecuterTool.searchOptions);
        }
    }
</script>
<div id="dTableCmenu" style="display:none">
    <div id="CopyCellsFromDbTable" style="padding:1px;border:1px solid #DDF;border-collapse: collapse" class="cmenuItem" onclick="dbTableExecuter.copyCells();cmenu.blur();return false;">CopyCellsFromDbTable</div>
    <div id="PasteCellsFromDbTable" style="padding: 1px;border:1px solid #DDF;border-collapse: collapse" class="cmenuItem" onclick="dbTableExecuter.pasteCells.call(selectedElements[0]);return false;">PasteCellsToDbTable</div>
</div>
<script type="text/javascript" src="/lib/superScripts/dbTableExecuter.js" class="gadgetScript" onload="dbTableExecuterTool.cmenu=this.parentElement.children['dTableCmenu'];dbTableExecuterTool.dbTableExecuterjs=true;if(dbTableExecuterTool.allLoadScript)dbTableExecuterTool.allLoadScript();return false;"></script>
<script type="text/javascript" src="/lib/sortable.js" class="gadgetScript" onload="dbTableExecuterTool.sortablejs=true;dbTableExecuterTool.allLoadScript();return false;"></script>
<link id="dbTableCSS" type="text/css" rel="stylesheet" href="/lib/sortable.css" class="gadgetCSS" onload="if(dbTableExecuterTool)dbTableExecuterTool.css=this"/>