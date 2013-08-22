<script type="text/javascript" class="gadgetScript">
    dbTableExecuter.tables=dbTableExecuter.tables||{};
    var ft="<?php echo $dbTable ?>";
    dbTableExecuter.tables[ft]={};
    dbTableExecuter.tables[ft]['tableCellProps']=JSON.parse('<?php tableFormulaEchoer($root, $dbTable, $mems, $liveDBTable, $fc, $sm) ?>');
    dbTableExecuter.tables[ft]['facade']=document.createElement('div');
    dbTableExecuter.tables[ft]['facade'].id='facade';
    dbTableExecuter.tables[ft]['facade'].innerHTML="<div id='ttArea' style='position:relative'><span id='tableName' style='font-size:22px; line-height:30px' class='userSpecific' ondblclick='dbTableExecuter.rename.call(this);return false;'>"+ft+"</span>&nbsp;&nbsp;<span id='rowCount'></span><span id='colCount'></span></div>";
    dbTableExecuter.tables[ft]['rowCount']=dbTableExecuter.tables[ft]['facade'].children['ttArea'].children['rowCount'];
    dbTableExecuter.tables[ft]['colCount']=dbTableExecuter.tables[ft]['facade'].children['ttArea'].children['colCount'];
    dbTableExecuter.tables[ft].dTable=new dbTableExecuter.genTable(dbTableExecuter.tables[ft]['facade'],ft);
    sortable.ts_makeSortable(dbTableExecuter.tables[ft].dTable.table);
    if(!dbTableExecuter.sandbox){
        dbTableExecuter.sandbox=new Worker('/lib/formulaEvaluator.js');
        dbTableExecuter.sandbox.onmessage=dbTableExecuter.receiveComputedData;
        dbTableExecuter.sandbox.aUVO={};
        dbTableExecuter.sandbox.cells=[];
        dbTableExecuter.sandbox.sUVAs=[];
        dbTableExecuter.sandbox.f=0;
    }
    if(dbTableExecuterTool.cmenu)core.addContextMenu(dbTableExecuter.tables[ft].dTable.table,dbTableExecuterTool.cmenu);
    statusField.innerHTML=ft+' opened.';
</script>