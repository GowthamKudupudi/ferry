var dbTableExecuterFetchUpdat=window.dbTableExecuterFetchUpdate||function(){
    var feed={
        content:{
            dbTable:dbTableExecuter.frontTable,
            tableOperation:'fetchUpdate'
        },
        postExpedition:function(feed){
            var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
            dbTableExecuter.liveUpdate(dbtUpdate);
            clearTimeout(dbTableExecuter.fetchUpdateTriggerId);
            dbTableExecuter.fetchUpdateTriggerId=setTimeout(dbTableExecuter.fetchUpdate,5000);
        }
    };
    feed.ferry=new core.shuttle("/lib/superScripts/dbTableExecuter.php", feed.content, feed.postExpedition, feed);
    clearTimeout(dbTableExecuter.delayedFetcher);
    dbTableExecuter.delayedFetcher=setTimeout(dbTableExecuter.fetchUpdate,120000);
};
var dbTableExecuter= window.dbTableExecuter||{
    vRowCount:20,
    getNewRows:function(){
        var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rows;
        var rl=rows.length;
        var nrs=[];
        for(var i=0;i<rl;i++){
            if(rows[i].id.indexOf('newRow')==0){
                nrs.push(rows[i]);
            }
        }
        return nrs;
    },
    splitFormula:function(formula){
        var i=0;
        var cBrackDepth=0;
        var fBrackDepth=0;
        var propArray=[];
        var propIndex=0;
        var qset=false,dqset=false;
        while(formula[i]){
            if(formula[i]=='(' && !qset && !dqset){
                cBrackDepth++;
            }else if(formula[i]==')' && !dqset && !qset){
                cBrackDepth--;
            }else if(formula[i]=='{' && !qset && !dqset){
                fBrackDepth++;
            }else if(formula[i]=='}' && !qset && !dqset){
                fBrackDepth--;
            }else if(!fBrackDepth&&!cBrackDepth&& !qset && !dqset&&formula[i]==';'){
                propArray.push(formula.substring(propIndex,i));
                propIndex=i+1;
            }else if(formula[i]=="'" && formula[i-1]!='\\' && !dqset){
                qset=!qset;
            }else if(formula[i]=='"' && formula[i-1]!='\\' && !qset){
                dqset=!dqset;
            }
            i++;
        }
        propArray.push(formula.substring(propIndex,i));
        return propArray;
    },
    appendRow:function(nRow,tid){
        tid=tid||dbTableExecuter.frontTable;
        var table=dbTableExecuter.tables[tid].dTable.table;
        var tHR=table.tHR;
        if(nRow && nRow.tagName=='TR'){
            if(nRow.id && nRow.cells.length==tHR.cells.length-2){
                for(var i=0;i<nRow.cells.length;i++){
                    nRow.cells[i].onfocus=dbTableExecuter.focusCell;
                    nRow.cells[i].className="dc";
                    nRow.cells[i].tabIndex='0';
                    nRow.cells[i].onkeydown=dbTableExecuter.cellNavHandler;
                    nRow.cells[i].ondblclick=dbTableExecuter.editCell;
                    nRow.cells[i].id=tHR.cells[i+1].id
                    nRow.cells[i].ee=[];
                    nRow.cells[i].formula='';
                }
                var nitd=document.createElement('td');
                nitd.id=nRow.id;
                nitd.innerHTML="<button id='delRowBtn' title='Delete row "+nRow.id+"' class='del rowDeleter ibtn' onclick='dbTableExecuter.delRow();return false;'></button>";
                nitd.style.textAlign='center';
                nRow.selector=document.createElement('input');
                nRow.selector.type='checkbox';
                nRow.selector.id='selector';
                nRow.selector.value=nRow.id;
                nRow.selector.className='row selector';
                nRow.selector.style.display='none';
                nitd.appendChild(nRow.selector);
                nRow.title=nRow.id;
                nRow.insertAdjacentElement('afterBegin',nitd);
                if(dbTableExecuter.tables[tid].dTable.tbody.rows['newRow']){
                    dbTableExecuter.tables[tid].dTable.tbody.removeChild(dbTableExecuter.tables[tid].dTable.tbody.rows['newRow']);
                }
                dbTableExecuter.tables[tid].dTable.tbody.rows['newVRow'].insertAdjacentElement('beforeBegin',nRow);
                dbTableExecuter.tables[tid].dTable.table.oRows[nRow.id]=nRow;
                if(nRow.rowIndex%2==1)nRow.classList.add('odd');else nRow.classList.add('even');
                if(dbTableExecuter.tables[tid].dTable.table.view=='page'){
                    dbTableExecuter.scrollBy(null, 'END');
                }
                for(var i=0;i<nRow.cells.length;i++){
                    if(tHR.cells[i].offsetHeight<1){
                        nRow.cells[i].classList.add('hidden');
                    }
                }
                dbTableExecuter.tables[tid].dTable.table.oRows[nRow.id]=nRow;
                dbTableExecuter.tables[tid].rowCount.innerHTML=(parseInt(dbTableExecuter.tables[tid].rowCount.textContent)+1)+" rows,";
            }else{
                statusField.innerHTML="mismatch of row format on appending a new row ~:|~";
            }
        }
        else{
            var iRow=table.rows['newVRow'];
            iRow.cells['appendRowTool'].children['appendRowBtn'].disabled=true;
            var tr=document.createElement('tr');
            var td=null;
            var fInput=null;
            var nrs=dbTableExecuter.getNewRows();
            var ni=1;
            for(var i=0;i<nrs.length;i++){
                var cni=nrs[i].id.substring(6,nrs[i].id.length);
                cni=parseInt(cni);
                if(cni>=ni)ni=cni+1;
            }
            tr.id="newRow"+ni;
            for(var i=0;i<table.tHR.cells.length-1;i++){
                td=document.createElement('td');
                if(tHR.cells[i].id=='index'){
                    td.id='index';
                    td.innerHTML="<button id='delRowBtn' title='Delete row "+tr.id+"' class='del rowDeleter ibtn' onclick='dbTableExecuter.delRow.call(this);return false;' style=\"\"></button>";
                    td.style.textAlign='center';
                    td.formula='';
                    td.ee=[];
                    tr.selector=document.createElement('input');
                    tr.selector.type='checkbox';
                    tr.selector.id='selector';
                    tr.selector.value=td.id;
                    tr.selector.className='newRow selector';
                    if(!dbTableExecuter.tables[tid].dTable.table.classList.contains('rowSelection')){
                        tr.selector.style.display='none';
                    }
                    td.appendChild(tr.selector);
                }else if(dbTableExecuter.tables[tid].dTable.tHR.cells[i].Null=='NO'){
                    var iPE=tHR.cells[i].cellEditTemp.cloneNode(true);
                    iPE.value=tHR.cells[i].cellEditTemp.value?tHR.cells[i].cellEditTemp.value:'';
                    td.innerHTML='';
                    td.appendChild(iPE);
                    td.preValue="";
                    td.onfocus=dbTableExecuter.activateCell;
                    td.className="dc";
                    td.tabIndex='0';
                    td.ee=[];
                    td.id=tHR.cells[i].id;
                    td.formula='';
                    td.onkeydown=dbTableExecuter.cellNavHandler;
                    iPE.onkeydown=dbTableExecuter.newRowInputHandler;
                    td.ondblclick=dbTableExecuter.editCell;
                    if(!fInput)fInput=iPE;
                }else{
                    td.ondblclick=function(){
                        var iPE=tHR.cells[this.cellIndex].cellEditTemp.cloneNode(true);
                        iPE.style.width=this.offsetWidth-3+"px";
                        iPE.style.height=this.offsetHeight-3+"px";
                        dbTableExecuter.activateCell.call(this);
                        this.innerHTML='';
                        this.preValue="";
                        this.appendChild(iPE);
                        iPE.value='';
                        iPE.onkeydown=dbTableExecuter.newRowInputHandler;
                        iPE.onblur=function(){
                            if(this.value=='')this.parentElement.innerHTML='';
                            return false;
                        }
                        iPE.focus();
                        if(iPE.select)iPE.select();
                    }
                    td.onfocus=dbTableExecuter.activateCell;
                    td.className="dc";
                    td.id=tHR.cells[i].id;
                    td.tabIndex='0';
                    td.formula='';
                    td.ee=[];
                    td.onkeydown=dbTableExecuter.cellNavHandler;
                }
                if(tHR.cells[i].offsetHeight<1){
                    td.classList.add('hidden');
                }
                tr.appendChild(td);
            }
            if(iRow.rowIndex%2==0){
                tr.className='odd';
            }else{
                tr.className='even';
            }
            iRow.insertAdjacentElement('beforeBegin',tr);
            table.newRows++;
            tr.classList.add('sortbottom');
            if(dbTableExecuter.tables[tid].dTable.table.view=='page'){
                dbTableExecuter.scrollBy(null, 'END');
            }
            if(!fInput){
                var k=1;
                while(tr.cells[k] && tr.cells[k].offsetWidth<=0){
                    k++;
                }
                if(tr.cells[k]){
                    dbTableExecuter.focusCell.call(tr.cells[k]);
                    tr.cells[k].ondblclick();
                }
            }else{
                dbTableExecuter.focusCell.call(fInput.parentElement);
                fInput.focus();
                if(fInput.select)fInput.select();
            }
            return tr;
        }
    },
    allOrFew:function(){
        var sts=this.parentElement.children;
        var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
        if(dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.view!='full'){
            for(var i=0;i<sts.length;i++){
                sts[i].disabled=true;
            }
            for(i in rows){
                rows[i].style.display=null;
            }
            sts['allOrFew'].disabled=false;
            sts['allOrFew'].style.backgroundPosition="0px -144px";
            dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.view='full';
        }
        else{
            for(i in rows){
                rows[i].style.display='none';
            }
            rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.rows;
            var vInd=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.getElementsByClassName('vInd')[0];
            vInd=vInd?vInd:rows[0];
            var rc=rows.length-1;
            var vStart=vInd.rowIndex-1;
            var vEnd=vStart+dbTableExecuter.vRowCount;
            vEnd=vEnd>rc?rc:vEnd;
            vStart=vEnd-dbTableExecuter.vRowCount;
            vStart=vStart>=0?vStart:0;
            for(i=vStart;i<vEnd;i++){
                rows[i].style.display=null;
            }
            vInd.classList.remove('vInd');
            rows[vStart].classList.add('vInd');
            dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.view='page';
            sts['allOrFew'].style.backgroundPosition="-432px -120px";
            for(i=0;i<sts.length;i++){
                sts[i].disabled=false;
            }
        }
    },
    newRowInputHandler:function (){
        event.cancelBubble=true;
        var cell=null;
        if(event.keyCode==39 && (this.selectionEnd==undefined ||(this.selectionEnd==this.value.length && this.selectionStart==this.selectionEnd))){
            cell=this.parentElement;
            if(cell.nextSibling!=null && cell.nextSibling.offsetHeight>0){
                if(cell.nextSibling.children.length==0)cell.nextSibling.ondblclick();
                else{
                    dbTableExecuter.activateCell.call(cell.nextSibling);
                    cell.nextSibling.children[0].focus();
                    if(cell.nextSibling.children[0].select)cell.nextSibling.children[0].select();
                }
            }
            return false;
        }
        else if(event.keyCode==37 && (this.selectionStart==undefined || (this.selectionStart==0 && this.selectionStart==this.selectionEnd))){
            cell=this.parentElement;
            if(cell.previousSibling.cellIndex!=0 && cell.previousSibling.offsetHeight>0){
                if(cell.previousSibling.children.length==0)cell.previousSibling.ondblclick();
                else {
                    dbTableExecuter.activateCell.call(cell.previousSibling);
                    cell.previousSibling.children[0].focus();
                    if(cell.previousSibling.children[0].select)cell.previousSibling.children[0].select();
                }
            }
            return false;
        }
        else if(event.keyCode==13){
            this.onblur=null;
            event.preventDefault();
            cell=this.parentElement;
            dbTableExecuter.changeEntry.call(this);
            return false;
        }
    },
    fixCell:function (feed){
        event.preventDefault();
        event.cancelBubble=true;
        if(feed.responseXML.getElementsByTagName('updateCell')[0].getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
            var sAUVO=feed.responseXML.getElementsByTagName('sAUVO')[0].firstChild.nodeValue;
            sAUVO=JSON.parse(sAUVO);
            for(var tid in sAUVO){
                var t=dbTableExecuter.tables[tid].dTable.table;
                var vTab=sAUVO[tid];
                for(var rid in vTab){
                    var vRow=vTab[rid];
                    if(!vRow['dbTableExecuterError']){
                        var row=t.rowWithId(rid)||(rid.indexOf('newRow')==0?t.rows[rid]:null);
                        if(row){
                            if(vRow['dbTableExecuterNRIndex']){
                                row.id=vRow['dbTableExecuterNRIndex'];
                                row.cells['index'].children['delRowBtn'].title="Delete row "+row.id;
                                if(!--t.newRows){
                                    t.rowWithId('newVRow').cells[0].children['appendRowBtn'].disabled=false;
                                }
                                row.classList.remove('sortbottom');
                                for(var i=0;i<row.cells.length;i++){
                                    if(row.cells[i].id!='index'){
                                        row.cells[i].onfocus=dbTableExecuter.focusCell;
                                        row.cells[i].ondblclick=dbTableExecuter.editCell;
                                        row.cells[i].onkeydown=dbTableExecuter.cellNavHandler;
                                    }
                                }
                                dbTableExecuter.tables[tid].rowCount.innerHTML=(parseInt(dbTableExecuter.tables[tid].rowCount.textContent)+1)+" rows,";
                                t.oRows[row.id]=row;
                            }
                            for(var cid in vRow){
                                if(cid!='dbTableExecuterNRIndex'){
                                    var cell=row.cells[cid];
                                    var vCell=vRow[cid];
                                    if(!vCell['dbTableExecuterError']){
                                        for(var pid in vCell){
                                            if(pid=='innerHTML'){
                                                cell.innerHTML=core.htmlEncode(vCell.innerHTML);
                                                if(cid==t.priKey){
                                                    cell.parentElement.pid=cell.textContent;
                                                }
                                            }else if(pid=='sKey'){
                                                cell.sKey=vCell['sKey'];
                                            }else if(pid!="display" && pid!="position" && pid!="width" && pid!="height" && pid!="fontSize"){
                                                cell.style[pid]=vCell[pid];
                                            }
                                        }
                                        statusField.innerHTML='Cell '+cell.id+'('+(row.pid?row.pid:row.id)+')('+t.tableName+') updated ~:)~';
                                    }else{
                                        if(cell.children['inputCell']){
                                            cell.innerHTML=cell.preValue?cell.preValue:'';
                                            if(cell.id==t.priKey)cell.pid=cell.preValue?cell.preValue:undefined;
                                        }
                                        statusField.innerHTML=vCell['dbTableExecuterError'];
                                    }
                                }
                            }
                        }else{
                            row=document.createElement('tr');
                            var tHR=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR;
                            if(vRow['dbTableExecuterNRIndex']){
                                row.id=vRow['dbTableExecuterNRIndex'];
                            }else{
                                row.id=rid;
                            }
                            for(var i=1;i<tHR.cells.length-1;i++){
                                var cell=document.createElement('td');
                                row.appendChild(cell);
                                cell.id=tHR.cells[i].id;
                                if(vRow[cell.id]){
                                    var vCell=vRow[cell.id];
                                    for(var pid in vCell){
                                        if(pid=='innerHTML'){
                                            cell.innerHTML=core.htmlEncode(vCell.innerHTML);
                                            if(cell.id==t.priKey)row.pid=cell.textContent;
                                        }else if(pid=='sKey'){
                                            cell.sKey=vCell['sKey'];
                                        }else if(pid!="display" && pid!="position" && pid!="width" && pid!="height" && pid!="fontSize"){
                                            cell.style[pid]=vCell[pid];
                                        }
                                    }
                                }
                            }
                            dbTableExecuter.appendRow(row,tid);
                            statusField.innerHTML='Row '+(row.pid?row.pid:row.id)+'('+t.tableName+') added ~:)~';
                        }
                    }
                    else{
                        var fRow=t.rowWithId(rid);
                        if(fRow){
                            if(fRow.id.indexOf('newRow')>-1){
                                var ips=fRow.getElementsByClassName('inputCell');
                                while(ips.length>0){
                                    var iCell=ips[0].parentElement;
                                    if(iCell.preValue)iCell.innerHTML=iCell.preValue;
                                    else iCell.innerHTML='';
                                    if(iCell.id==t.priKey)iCell.parentElement.pid=undefined;
                                }
                            }
                        }
                        statusField.innerHTML=vRow['dbTableExecuterError'];
                    }
                }
                if(!t.rows['newRow'] && dbTableExecuter.fixCell.appendNewRow){
                    dbTableExecuter.fixCell.appendNewRow=false;
                    dbTableExecuter.appendRow.call(t.rows['newVRow'].cells[0].children['appendRowBtn']);
                }
            }
            var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
            try{
                dbtUpdate['tables'][dbTableExecuter.frontTable]['cells'][feed.content.fRowIndex][feed.content.fColIndex]['f']['f']=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rowWithId(feed.content.fRowIndex).cells[feed.content.fColIndex].formula;
            }catch(e){}
            dbTableExecuter.liveUpdate(dbtUpdate);
        }else{
            statusField.innerHTML=feed.responseXML.getElementsByTagName('updateCell')[0].getElementsByTagName('status')[0].firstChild.nodeValue;
        }
        return false;
    },
    changeEntry:function(){
        var elm=this;
        var cell=elm.parentElement;
        var feed=new Object();
        if(elm.value!=cell.preValue){
            var table=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table;
            var tHR=table.tHR;
            var tHRCells=table.tHR.cells;
            var colIndex=tHRCells[cell.cellIndex].id;
            var cellRow=cell.parentElement;
            var rowIndex=cellRow.id;
            var uCells=[elm];
            if(rowIndex.indexOf('newRow')==0){
                var aCells=cellRow.cells;
                uCells=cellRow.getElementsByClassName('inputCell');
                for(var i=1;i<uCells.length;i++){
                    if(uCells[i].value==''){
                        uCells[i].focus();
                        statusField.innerHTML='No null cells should not be Empty ~:|~';
                        return false;
                    }
                }
                dbTableExecuter.fixCell.appendNewRow=true;
            }
            var eEA=[];
            for(var i=0;i<uCells.length;i++){
                var uCell=uCells[i].parentElement;
                if(uCell.id==table.priKey){
                    cellRow.pid=cellRow.cells[table.priKey]?cellRow.cells[table.priKey].children['inputCell']?cellRow.cells[table.priKey].children['inputCell'].value:cellRow.cells[table.priKey].textContent:undefined;
                }
                dbTableExecuter.updateByFormula(uCell,"='"+core.escapeQuotes(uCells[i].value)+"'",[]);
                eEA.push(uCell);
            }
            if(rowIndex.indexOf('newRow')==0){
                var tr=cellRow;
                for(var j=1;j<cellRow.cells.length;j++){
                    uCell=cellRow.cells[j];
                    var cAE=false;
                    for(var k=0;k<eEA.length;k++){
                        if(cell==eEA[k]){
                            cAE=true;
                            break;
                        }
                    }
                    if(!cAE){
                        var formula='='+tHR.cells['index'].formula+';='+tr.cells['index'].formula+';='+tHR.cells[j].formula;
                        dbTableExecuter.updateByFormula(uCell,formula,[]);
                    }
                }
            }
        }else{
            cell.innerHTML=cell.preValue;
        }
        return false;
    },
    ipKeyHandler:function(){
        var cell=this.parentElement;
        if(event.keyCode=='37'){
            event.cancelBubble=true;
            this.onblur=null
            var nxtCell=cell.previousSibling;
            if(nxtCell && nxtCell.classList.contains('dc') && nxtCell.offsetWidth>0){
                dbTableExecuter.changeEntry.call(this);
                nxtCell.ondblclick.call(nxtCell);
                return false;
            }else{
                return false;
            }
        }
        else if(event.keyCode=='38'){
            event.cancelBubble=true;
            this.onblur=null
            var nxtRow=cell.parentElement.previousSibling;
            if(nxtRow)var nxtCell=nxtRow.cells[cell.id];
            if(nxtCell && nxtCell.classList.contains('dc') && nxtCell.offsetWidth>0){
                dbTableExecuter.changeEntry.call(this);
                nxtCell.ondblclick.call(nxtCell);
                return false;
            }else{
                return false;
            }
        }else if(event.keyCode=='39'){
            event.cancelBubble=true;
            this.onblur=null
            var nxtCell=cell.nextSibling;
            if(nxtCell && nxtCell.classList.contains('dc') && nxtCell.offsetWidth>0){
                dbTableExecuter.changeEntry.call(this);
                nxtCell.ondblclick.call(nxtCell);
                return false;
            }else{
                return false;
            }
        }
        else if(event.keyCode=='40'){
            event.cancelBubble=true;
            this.onblur=null
            var nxtRow=cell.parentElement.nextSibling;
            if(nxtRow.id!='newVRow')var nxtCell=nxtRow.cells[cell.id];
            if(nxtCell && nxtCell.classList.contains('dc') && nxtCell.offsetWidth>0){
                dbTableExecuter.changeEntry.call(this);
                nxtCell.ondblclick.call(nxtCell);
                return false;
            }else{
                return false;
            }
        }
        else if(event.keyCode=='13'){
            event.cancelBubble=true;
            this.onblur.call(this)
            cell.focus();
        }
    },
    editCell:function (){
        var cell=this;
        if (cell) {
            dbTableExecuter.activateCell.call(cell);
            if(cell.children.length==0){
                var cellValue=cell.innerHTML;
                var j=cell.cellIndex;
                cell.preValue=cell.innerHTML;
                var iPE=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[j].cellEditTemp.cloneNode(true);
                iPE.style.width=cell.offsetWidth-3+"px";
                iPE.style.height=cell.offsetHeight-3+"px";
                iPE.value=cellValue;
                iPE.onblur=dbTableExecuter.changeEntry;
                cell.innerHTML='';
                if(iPE.type!='text'){
                    iPE.onkeydown=dbTableExecuter.ipKeyHandler;
                }
                cell.appendChild(iPE);
                iPE.focus();
                if(iPE.select)iPE.select();
            }else{
                cell.children[0].focus();
                if(cell.children[0].select)cell.children[0].select();
            }
        }
    },
    totalRow:function (cell){
        var total=0;
        var row=cell.parentElement;
        var sumLimit=row.cells.length-1;
        for(var i=2;i<sumLimit;i++){
            if(row.cells[i].children.length==0)
                total+=row.cells[i].innerHTML;
        }
        row.cells[sumLimit].innerHTML=total;
    },
    newColumn:function (evt){
        var elm=this;
        if(elm){
            var cell=elm.parentElement.parentElement;
            var thead=cell.parentElement.parentElement;
            var tbody=thead.nextSibling;
            var table=tbody.parentElement;
            var cells=cell.parentElement.cells;
            var nrow=tbody.rows['newRow'];
            if(nrow)nrow.parentElement.removeChild(nrow);
            for(var i=1;i<cells.length;i++){
                try{
                    cells[i].children['tools'].children['newColumnBtn'].disabled=true;
                    cells[i].children['tools'].children['newColumnBtn'].title='finish current column operation';
                    cells[i].children['tools'].children['delColumnBtn'].disabled=true;
                    cells[i].children['tools'].children['delColumnBtn'].title='finish current column operation';
                }
                catch(e){
                    console.log(e.message)
                };
            }
            var rows=table.oRows;
            var columnName=document.createElement('th');
            var aColumnIndex=cell.cellIndex-1;
            td=document.createElement('form');
            td.onsubmit=dbTableExecuter.insColumn;
            var col=document.createElement('input');
            col.type='text';
            col.id='columnNameInput';
            col.value='ColumnName'
            col.title="Enter column name";
            col.size='9';
            col.onclick=function(){
                if(this.select)this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col=document.createElement('select');
            col.innerHTML="<option value=''>SelectType</option><option value='int'>INT</option><option value='float'>FLOAT</option><option value='varchar'>VARCHAR</option><option value='timestamp'>TIMESTAMP</option><option value='enum'>ENUM</option>";
            col.id='type';
            col.title='select data type';
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col=document.createElement('input');
            col.type='text';
            col.id='size';
            col.value='08';
            col.title="Enter size, if enum enter values seperated by ,";
            col.size='20';
            col.onclick=function(){
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col=document.createElement('input');
            col.type='text';
            col.id='default';
            col.title="Enter a default value if any \nelse leave it blank";
            col.size='20';
            col.onclick=function(){
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col=document.createElement('input');
            col.type='checkbox';
            col.id='notNull';
            col.name='notNull';
            col.title='check if column should be given \n a value upon new row.';
            td.appendChild(col);
            col=document.createElement('input');
            col.type='submit';
            col.id='insColumnBtn';
            col.value='InsertColumn';
            td.appendChild(col);
            col=document.createElement('img');
            col.id='cancelInsert';
            col.className='ibtn';
            col.src='images/-.png';
            col.onclick=function(){
                dbTableExecuter.cancelInsertCol();
                return false;
            }
            col.title='cancel column insert';
            td.appendChild(col);
            columnName.appendChild(td);
            dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.insertBefore(columnName,cell);
            for(var i in rows){
                var td=document.createElement('td');
                var rcell=rows[i].cells[aColumnIndex];
                rcell.insertAdjacentElement('afterEnd',td);
            }
        }
    },
    cancelInsertCol:function(evt){
        var elm;
        if(evt && evt!=window.event){
            elm=evt;
        }
        else{
            evt=window.event?window.event:null;
            if(evt){
                elm=evt.targetElement?evt.targetElement:evt.srcElement;
            }
        }
        if(elm){
            var cell=elm.parentElement.parentElement;
            var cells=cell.parentElement.cells;
            var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
            var columnIndex=cell.cellIndex;
            var rcell=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[columnIndex];
            rcell.parentElement.removeChild(rcell);
            for(var i in rows){
                rcell=rows[i].cells[columnIndex];
                rows[i].removeChild(rcell);
            }
            for(var i=1;i<cells.length;i++){
                try{
                    cells[i].children['tools'].children['newColumnBtn'].disabled=false;
                    cells[i].children['tools'].children['newColumnBtn'].title='Insert new column';
                    cells[i].children['tools'].children['delColumnBtn'].disabled=false;
                    cells[i].children['tools'].children['delColumnBtn'].title='Delete column';
                }catch(e){
                    console.log(e.message)
                };
            }
            cells[cells.length-1].children['tools'].children['newColumnBtn'].title='Append a new column';
        }
        return false;
    },
    authorityBlock:function(cell){
        cell=cell||this.parentElement.parentElement;
        if(cell){
            var authBox=document.createElement('form');
            authBox.cell=cell;
            authBox.columnIndex=authBox.cell.cellIndex;
            authBox.table=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table;
            authBox.columnName=authBox.cell.id;
            authBox.dbTable=dbTableExecuter.tables[dbTableExecuter.frontTable].facade.children['ttArea'].children['tableName'].textContent;
            authBox.id='authorityBox';
            dbTableExecuter.colAuthUsers(authBox,dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[authBox.columnIndex].Comment);
            authBox.inputObjectIds=document.createElement('input');
            authBox.inputObjectIds.id='inputObjectId';
            authBox.inputObjectIds.type='text';
            authBox.inputObjectIds.title='add organization\'s object ids seperated by \',\' without any spaces';
            authBox.appendChild(authBox.inputObjectIds);
            authBox.addAuthObjects=document.createElement('input');
            authBox.addAuthObjects.id='addAuthObjects';
            authBox.addAuthObjects.type='submit';
            authBox.addAuthObjects.value='Add';
            authBox.addAuthObjects.title='Add authorized Objects(employee or student or lecturer or any object related to the organization)';
            authBox.appendChild(authBox.addAuthObjects);
            authBox.appendChild(document.createElement('br'));
            authBox.rLabel=document.createElement('label');
            authBox.rLabel.innerHTML='r';
            authBox.appendChild(authBox.rLabel);
            authBox.r=document.createElement('input');
            authBox.r.type='radio';
            authBox.r.id='read';
            authBox.r.value='r';
            authBox.r.name='rORw'
            authBox.r.checked=true;
            authBox.appendChild(authBox.r);
            authBox.wLabel=document.createElement('label');
            authBox.wLabel.innerHTML='/w';
            authBox.appendChild(authBox.wLabel);
            authBox.w=document.createElement('input');
            authBox.w.type='radio';
            authBox.w.id='write';
            authBox.w.value='w'
            authBox.w.name='rORw';
            authBox.appendChild(authBox.w);
            authBox.appendChild(document.createElement('label')).innerHTML='&nbsp;specificRows';
            authBox.specifyRows=document.createElement('input');
            authBox.specifyRows.type='checkbox';
            authBox.specifyRows.className='row specifier';
            authBox.specifyRows.id='specifyRows';
            authBox.specifyRows.onmousedown=function(){
                if(!this.checked){
                    dbTableExecuter.specifyRows();
                }else{
                }
                event.preventDefault();
                return false;
            }
            authBox.appendChild(authBox.specifyRows);
            authBox.onsubmit=function(){
                this.specificRows=[];
                var authRows='';
                if(this.specifyRows.checked){
                    var rows=this.table.getElementsByClassName('row selector');
                    for(i=1;i<rows.length;i++){
                        if(rows[i].checked){
                            if(authRows!='')
                                authRows+=','+rows[i].value;
                            else
                                authRows=rows[i].value;
                        }
                    }
                }else{
                    authRows='*';
                }
                this.feed=new Object();
                if(this.w.checked)this.feed.rORw='w';
                else this.feed.rORw='r';
                this.feed.ab=this;
                this.feed.authRows=authRows;
                this.feed.columnIndex=this.columnIndex;
                this.feed.columnName=this.columnName;
                this.feed.content='dbTable='+this.dbTable+'&tableOperation=permitColUsers&colName='+this.columnName+'&nmembers='+this.inputObjectIds.value+'&rows='+authRows+'&rORw='+this.feed.rORw;
                this.feed.content={
                    dbTable:this.dbTable,
                    tableOperation:'permitColUsers',
                    colName:this.columnName,
                    nmembers:this.inputObjectIds.value,
                    rows:authRows,
                    rORw:this.feed.rORw
                }
                this.feed.postExpedition=function(feed){
                    dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[feed.columnIndex].Comment=feed.responseXML.getElementsByTagName('comment')[0].firstChild.nodeValue;
                    var mg=feed.responseXML.getElementsByTagName('delGrp');
                    var mgl=mg.length;
                    var ngids=[];
                    for(i=0;i<mgl;i++){
                        var ngid=mg[i].getElementsByTagName('g')[0].firstChild.nodeValue;
                        var ogid=feed.ab.children[mg[i].getElementsByTagName('rORw')[0].firstChild.nodeValue].children[mg[i].getElementsByTagName('g')[0].firstChild.nodeValue];
                        ogid.parentElement.removeChild(ogid);
                    }
                    mg=feed.responseXML.getElementsByTagName('modGrp');
                    mgl=mg.length;
                    for(var i=0;i<mgl;i++){
                        var ngid=mg[i].getElementsByTagName('g')[0].firstChild.nodeValue;
                        ngids[ngids.length]=ngid;
                        feed.ab.children[mg[i].getElementsByTagName('rORw')[0].firstChild.nodeValue].children[ngid].children['grpMems'].innerHTML=mg[i].getElementsByTagName('mems')[0].firstChild.nodeValue;
                    }
                    mg=feed.responseXML.getElementsByTagName('newGrp');
                    mgl=mg.length;
                    for(i=0;i<mgl;i++){
                        var ngid=mg[i].getElementsByTagName('g')[0].firstChild.nodeValue;
                        ngids[ngids.length]=ngid;
                        var grp=document.createElement('div');
                        grp.id=ngid
                        grp.className='grp';
                        var grpId=document.createElement('span');
                        grpId.id='grpId';
                        grp.appendChild(grpId);
                        var grpMems=document.createElement('span');
                        grpMems.id='grpMems';
                        grpMems.style.fontWeight='bold';
                        grpMems.innerHTML=mg[i].getElementsByTagName('mems')[0].firstChild.nodeValue;
                        grp.appendChild(grpMems);
                        var grpRows=document.createElement('div');
                        grpRows.id='grpRows';
                        grpRows.innerHTML=feed.authRows;
                        grp.appendChild(grpRows);
                        feed.ab.children[mg[i].getElementsByTagName('rORw')[0].firstChild.nodeValue].appendChild(grp);
                    }
                    feed.ab.parentElement.reAlign();
                    statusField.innerHTML='Column '+feed.columnName+' permissions changed.~B-)~';
                    var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
                    dbTableExecuter.liveUpdate(dbtUpdate);
                }
                this.feed.ferry=new core.shuttle('lib/superScripts/dbTableExecuter.php', this.feed.content, this.feed.postExpedition, this.feed);
                return false;
            }
            var panel=new core.msgPanel(authBox.cell.id+'AuthorizedUsers',cell,this,authBox);
            panel.closeBtn.onclick=function(){
                dbTableExecuter.hideRS();
                delete this.parentElement.objOwner.panel;
                this.parentElement.closePanel();
            }
            return panel;
        }
    },
    hideRS:function(){
        var table=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table;
        if(table.classList.contains('rowSelection')){
            table.classList.remove('rowSelection');
            var rowSelectors=table.getElementsByClassName('row selector');
            for(i=0;i<rowSelectors.length;i++){
                rowSelectors[i].style.display='none';
                rowSelectors[i].checked=false;
                if(rowSelectors[i].anim){
                    rowSelectors[i].mState=0;
                    rowSelectors[i].anim(1+3*rowSelectors[i].mState);
                }
            }
            var rowSs=document.getElementsByClassName('row specifier');
            for(i=0;i<rowSs.length;i++){
                rowSs[i].disabled=false;
            }
        }
    },
    colAuthUsers:function(dArea,comment){
        var i = 0;
        var k = 0;
        var pEnts=[];
        pEnts['r']=[];
        pEnts['w']=[];
        var pT=null;
        var eC=null;
        while (comment[i] != null) {
            if (comment[i] == '{') {
                i++;
                k=0;
                pT=comment[i];
                eC=pEnts[pT].length;
                i++;
                while (comment[i] != '}') {
                    pEnts[pT][eC]=[];
                    pEnts[pT][eC]['gid']='';
                    while (comment[i] != ',') {
                        pEnts[pT][eC]['gid']+=comment[i];
                        i++;
                    }
                    i++;
                    if (comment[i] == '{') {
                        i++;
                        pEnts[pT][eC]['rows']=[];
                        while (comment[i] != '}') {
                            pEnts[pT][eC]['rows'][k]='';
                            while (comment[i] != ',' && comment[i]!='}') {
                                pEnts[pT][eC]['rows'][k]+=comment[i];
                                i++;
                            }
                            /*if(/-/.test(pEnts[pT][eC]['rows'][k])){
                                var jk=0;
                                var ll='';
                                var ul='';
                                while (pEnts[pT][eC]['rows'][k][jk]!='-'){
                                    ll+=pEnts[pT][eC]['rows'][k][jk];
                                    jk++;
                                }
                                jk++;
                                while(pEnts[pT][eC]['rows'][k][jk]!=null){
                                    ul+=pEnts[pT][eC]['rows'][k][jk];
                                    jk++
                                }
                                while(ll!=ul){
                                    pEnts[pT][eC]['rows'][k]=ll;
                                    ll++;
                                    k++;
                                }
                                pEnts[pT][eC]['rows'][k]=ll;
                            }*/
                            i++;
                            k++;
                        }
                    }
                }
                i++;
            }
            else {
                i++;
            }
        }
        if(dArea!=null){
            var rw=['r','w'];
            var gis=[];
            for(var j=0;j<rw.length;j++){
                var rDiv=document.createElement('div');
                rDiv.id=rw[j];
                rDiv.innerHTML=rw[j]+':';
                for(i=0;i<pEnts[rw[j]].length;i++){
                    var grp=document.createElement('div');
                    grp.id=pEnts[rw[j]][i]['gid'];
                    gis[gis.length]=grp.id;
                    grp.className='grp';
                    rDiv.appendChild(grp);
                    var grpId=document.createElement('span');
                    grpId.id='grpId';
                    grp.appendChild(grpId);
                    var grpMems=document.createElement('span');
                    grpMems.id='grpMems';
                    grpMems.style.fontWeight='bold';
                    grp.appendChild(grpMems);
                    var grpRows=document.createElement('div');
                    grpRows.id='grpRows';
                    grpRows.innerHTML=pEnts[rw[j]][i]['rows'].join(',');
                    grp.appendChild(grpRows);
                    rDiv.appendChild(grp);
                }
                dArea.appendChild(rDiv);
            }
            var feed=new Object();
            feed.content={
                gids:gis.join(',')
            };//"gids="+gis.join(',');
            feed.dArea=dArea;
            feed.postExpedition=function(){
                var rgrps= feed.responseXML.getElementsByTagName('grp');
                var ogrps= feed.dArea.getElementsByClassName('grp')
                for(var i=0;i<rgrps.length;i++){
                    try{
                        ogrps[rgrps[i].getElementsByTagName('g')[0].firstChild.nodeValue].children['grpMems'].innerHTML=rgrps[i].getElementsByTagName('mems')[0].firstChild.nodeValue;
                    }
                    catch(e){}    
                }
                statusField.innerHTML='authority block opened successfully...';
            }
            feed.ferry=new core.shuttle('lib/adminScripts/grpManager.php', feed.content, feed.postExpedition, feed);
        }else 
            return pEnts;
    },
    delColumn:function(){
        var elm=this;
        if(elm && confirm('Are u sure in deleting the column? don\'t delete columns more than 10 times for a table. it corrupts table!')){
            var cell=elm.parentElement.parentElement;
            var tableName=dbTableExecuter.tables[dbTableExecuter.frontTable].facade.children['ttArea'].children['tableName'].textContent;
            var columnName=cell.id;
            var feed=new Object();
            feed.colName=columnName
            feed.postExpedition=function(feed){
                if(feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue=='success'){
                    var cell=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[feed.colName];
                    cell.parentElement.removeChild(cell);
                    cell=null;
                    if(dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rows['newRow'])cell=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rows['newRow'].cells[feed.colName];
                    if(cell)cell.parentElement.removeChild(cell);
                    var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
                    for(var i in rows){
                        try{
                            cell=rows[i].cells[feed.colName];
                            cell.parentElement.removeChild(cell);
                        }catch(e){}
                    }
                    dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.colCount-=1;
                    var cNLI=dbTableExecuterTool.tableNameInputField.cNL.children[feed.content.dbTable].children[feed.colName];
                    cNLI.parentElement.removeChild(cNLI);
                    var colgrp=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.getElementsByTagName('colgroup')[0].children[1];
                    colgrp.parentElement.removeChild(colgrp);
                    statusField.innerHTML='Column Deleted ~B-)~';
                }else{
                    statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue+' ~:|~';
                }
                var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
                dbTableExecuter.liveUpdate(dbtUpdate);
            };
            var content="dbTable="+tableName+"&columnName="+columnName+"&tableOperation=delColumn";
            feed.content={
                dbTable:tableName,
                columnName:columnName,
                tableOperation:'delColumn'
            }
            feed.ferry=new core.shuttle('./lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
        }
    },
    insColumn:function (){
        var elm=this;
        if(elm){
            var columnIndex=elm.parentElement.cellIndex;
            var insertAfter=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[columnIndex-1].id;
            var type=elm.children['type'].value;
            var maxSize=elm.children['size'].value;
            var columnName=elm.children['columnNameInput'];
            var notNull=elm.children['notNull'].checked?'&notNull=YES':'';
            var tableName=dbTableExecuter.tables[dbTableExecuter.frontTable].facade.children['ttArea'].children['tableName'].textContent;
            var dflt=elm.children['default'].value;
            if(columnName.value == ''){
                columnName.focus();
            }else if(/-/.test(columnName.value)){
                alert("column name should not contain '-'.");
                columnName.focus();
            }else if(columnName.value.length>16 || columnName.value=='item'){
                alert('column name should not be "item" or column name length should not exceed 16 characters.');
                columnName.focus();
            }else if(type.value==''){
                alert('Select a data type for the column');
                type.focus();
            }else{
                var content='columnName='+columnName.value+'&type='+type+'&maxSize='+maxSize+'&tableOperation=insColumn&dbTable='+tableName+'&insertAfter='+insertAfter+notNull+'&default='+dflt;
                var feed=new Object();
                feed.colName=columnName.value;
                feed.insAftCol=insertAfter;
                feed.type=type;
                feed.maxSize=maxSize;
                feed.nul=notNull?'NO':'YES';
                feed.dflt=dflt;
                feed.key='';
                feed.content={
                    columnName:columnName.value,
                    type:type,
                    maxSize:maxSize,
                    tableOperation:'insColumn',
                    dbTable:tableName,
                    insertAfter:insertAfter,
                    notNull:elm.children['notNull'].checked?'YES':'NO',
                    dfault:dflt
                }
                var columnAppended=function(feed){
                    if(feed.responseXML.getElementsByTagName('status')[0].textContent=='success'){
                        var colName=feed.content.columnName;
                        var insAftCol=feed.content.insertAfter;
                        var inAftColIndex=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[insAftCol].cellIndex;
                        var newColHead=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[insAftCol].nextSibling;
                        dbTableExecuter.preFormatColHeadCell(newColHead,colName,feed.type+'('+feed.maxSize+')',feed.nul,feed.key,feed.dflt,"","",feed.content.columnName,dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table)
                        dbTableExecuter.formatColHeadCell(newColHead,colName,dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table);
                        newColHead.formula='';
                        newColHead.ee=[];
                        var td=null;
                        var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
                        for(var i in rows){
                            td=rows[i].cells[inAftColIndex].nextSibling;
                            td.tabIndex='0';
                            td.ondblclick=dbTableExecuter.editCell;
                            td.onfocus=dbTableExecuter.focusCell;
                            td.classList.add('dc');
                            td.id=colName;
                            td.onkeydown=dbTableExecuter.cellNavHandler;
                            td.ee=[];
                            td.formula=''
                        }
                        if(dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.authorization!='*'){
                            var sKeys=feed.responseXML.getElementsByTagName('sKey');
                            var ki=0;
                            for(var i in rows){
                                td=rows[i].cells[inAftColIndex].nextSibling;
                                td.sKey=sKeys[ki++].firstChild.nodeValue;
                            }
                        }
                        var cells=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells;
                        dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.colCount+=1;
                        for(var i=1;i<cells.length;i++){
                            try{
                                cells[i].children['tools'].children['newColumnBtn'].disabled=false;
                                cells[i].children['tools'].children['newColumnBtn'].title='Insert Column';
                                cells[i].children['tools'].children['delColumnBtn'].disabled=false;
                                cells[i].children['tools'].children['delColumnBtn'].title='Delete Column';
                            }catch(e){
                                console.log(e.message)
                            }
                        }
                        dbTableExecuter.tables[dbTableExecuter.frontTable].colCount.innerHTML=" "+(parseInt(dbTableExecuter.tables[dbTableExecuter.frontTable].colCount.textContent.trim())+1)+" columns";
                        var cNLI=document.createElement('div');
                        cNLI.id=colName;
                        cNLI.innerHTML=colName;
                        dbTableExecuterTool.tableNameInputField.cNL.children[feed.content.dbTable].children[insAftCol].insertAdjacentElement("afterEnd",cNLI);
                        
                        //maintain width of columns
                        var colgrp=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.getElementsByTagName('colgroup')[0].children[1];
                        colgrp.insertAdjacentElement("afterEnd",colgrp.cloneNode(true));
                        
                        statusField.innerHTML='Column Added ~B-)~';
                    }
                    else{
                        statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue+' ~:|~';
                    }
                    var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
                    dbTableExecuter.liveUpdate(dbtUpdate);
                }
                statusField.innerHTML='Creating new column...~:{~';
                feed.ferry=new core.shuttle('./lib/superScripts/dbTableExecuter.php', content, columnAppended, feed)
            }
        }
        return false;
    },
    generateColumns: function (evt){
        var elm;
        if(evt && evt!=window.event){
            elm=evt;
        }else{
            evt = window.event ? window.event : null;
            elm = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        }
        evt.preventDefault();
        var nocs=elm.children['noOfSubs'].value;
        var tableCreatorElm=elm.parentElement;
        var tableName=document.getElementById('dbTableExecuterBdy').children['ttArea'].children['tableName'].textContent;
        var tableBlock=tableCreatorElm.children['tableBlock'];
        if(tableBlock.length!=0){
            for(var i=tableBlock.children.length-1;i>-1;i--){
                tableBlock.children[i].parentElement.removeChild(tableBlock.children[i]);
            }
        }
        var table = document.createElement('table');
        table.id='tableTemplate';
        var tr=document.createElement('tr');
        var td,col;
        for(var i=0;i<nocs;i++){
            td=document.createElement('td');
            td.style.textAlign='center';
            col=document.createElement('input');
            col.type='text';
            col.id='colName';
            col.value='Column Name'
            col.title="Enter a column name";
            col.size='9';
            col.onclick=function(){
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col=document.createElement('select');
            col.innerHTML="<option value=''>SelectType</option><option value='INT'>INT</option><option value='float'>FLOAT</option><option value='VARCHAR'>VARCHAR</option><option value='TIMESTAMP'>TIMESTAMP</option><option value='ENUM'>ENUM</option>";
            col.id='type';
            col.title='select data type';
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col=document.createElement('input');
            col.type='text';
            col.id='size';
            col.value='08';
            col.title="Enter size, if enum enter values seperated by ','";
            col.size='20';
            col.onclick=function(){
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col=document.createElement('input');
            col.type='text';
            col.id='default';
            col.title="Enter a default value if any \nelse leave it blank";
            col.size='20';
            col.onclick=function(){
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col=document.createElement('input');
            col.type='radio';
            col.id='pimaryKey';
            col.name='primaryKey';
            col.ondblclick=function(){
                this.checked=false;
                return false;
            }
            col.title='select a primary key';
            td.appendChild(col);
            col=document.createElement('input');
            col.type='checkbox';
            col.id='notNull';
            col.name='notNull';
            col.title='check if column should be given \n a value upon new row.';
            td.appendChild(col);
            tr.appendChild(td);
        }
        table.appendChild(tr);
        tableBlock.appendChild(table);
        var maxRs=document.createElement('input');
        maxRs.type='text';
        maxRs.size='5';
        maxRs.title='enter max no. of rows in table \n enter only 9\'s';
        maxRs.value='999'
        var cTB=document.createElement('input');
        cTB.type='submit';
        cTB.value='CreateTable';
        tableBlock.appendChild(cTB);
        tableBlock.onsubmit=function(evt){
            var elm;
            if(evt && evt!=window.event){
                elm=evt;
            }
            else{
                evt = (window.event) ? window.event : null;
                elm = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
            }
            statusField.innerHTML='Creating table... ~:{~'
            var dTE=elm.parentElement;
            var tableName=document.getElementById('dbTableExecuterBdy').children['ttArea'].children['tableName'].textContent;
            var tableBlock=dTE.children['tableBlock'];
            var tableTemplate=tableBlock.children['tableTemplate'];
            var rows=tableTemplate.rows;
            var columnCount=rows[0].cells.length;
            var feed=new Object();
            var content="tableOperation=createTable&dbTable="+tableName+"&maxRs="+maxRs.value.length;
            var column='';
            var columns=[];
            for(var i=0;i<columnCount;i++){
                column='';
                if(/-/.test(rows[0].cells[i].children['colName'].value)){
                    alert('column name should not contain \'-\'.');
                    rows[0].cells[i].children['colName'].focus();
                    return false;
                }
                if(rows[0].cells[i].children['colName'].value.length>16 || rows[0].cells[i].children['colName'].value=='item'){
                    alert('column name should not be "item" or column name length should not exceed 16 characters.');
                    rows[0].cells[i].children['colName'].focus();
                    return false;
                }
                column+=rows[0].cells[i].children['colName'].value+' ';
                if(rows[0].cells[i].children['type'].value==''){
                    alert('Select a data type for the column');
                    rows[0].cells[i].children['type'].focus();
                    return false;
                }
                column+=rows[0].cells[i].children['type'].value;
                if(rows[0].cells[i].children['size'].value!='')
                    column+='('+rows[0].cells[i].children['size'].value+')';
                if(rows[0].cells[i].children['primaryKey'].checked){
                    column+=' PRIMARY KEY';
                }
                column+=rows[0].cells[i].children['notNull'].checked?' NOT NULL':" NULL";
                if(rows[0].cells[i].children['default'].value!=''){
                    column+=" DEFAULT '"+rows[0].cells[i].children['default'].value+"'";
                }
                columns[columns.length]=column;
            }
            content+='&columns='+columns.join(',');
            feed.content={
                tableOperation:'createTable',
                dbTable:tableName,
                maxRs:maxRs.value.length,
                columns:columns.join(',')
            }
            feed.dTEF=dTE.parentElement.parentElement.parentElement;
            feed.tableName=tableName;
            feed.postExpedition=function(feed){
                if(feed.responseXML){
                    if(feed.responseXML.getElementsByTagName('status')[0].textContent=='success'){
                        statusField.innerHTML='Table created successfully ~B-)~';
                        dbTableExecuterTool.tableNameInputField.value=feed.tableName;
                        if(dbTableExecuterTool){
                            dbTableExecuterTool.loadGadget.apply(dbTableExecuterTool,[true]);
                        }else{
                            location.href=document.baseURI+'dbTableExcuterForm.php?dbTable='+feed.tableName;
                        }
                    }
                    else{
                        statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].textContent;
                    }
                }
                var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
                dbTableExecuter.liveUpdate(dbtUpdate);
            };
            feed.ferry= new core.shuttle('lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
            return false;
        }
        return false;
    },
    delTable: function(){
        var elm=this;
        if(elm && confirm('Are u sure in deleting the table?')){
            statusField.innerHTML='Deleting table...';
            var dbTable=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.tableName;
            var content='tableOperation=delTable&dbTable='+dbTable;
            var feed=new Object();
            feed.dbTable=dbTable;
            feed.postExpedition=function(feed){
                if(feed.responseXML){
                    if(feed.responseXML.getElementsByTagName('status')[0].textContent=='success'){
                        delete dbTableExecuter.tables[dbTableExecuter.frontTable];
                        delete dbTableExecuter.frontTable;
                        statusField.innerHTML=feed.dbTable+' table deleted successfully ~:)~';
                        dbTableExecuterTool.dispArea.innerHTML='';
                    }
                    else{
                        statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].textContent;
                    }
                }
                var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
                dbTableExecuter.liveUpdate(dbtUpdate);
            }
            feed.content={
                tableOperation:'delTable',
                dbTable:dbTable
            }
            statusField.innerHTML='Deleting table...~:{~';
            feed.ferry=new core.shuttle('lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
        }
    },
    delRow: function(){
        var elm=this;
        if(elm && confirm('Are u sure in deleting the Row?')){
            var row=elm.parentElement.parentElement;
            var rowIndex=row.id;
            var t=row.parentElement.parentElement;
            if(rowIndex.indexOf('newRow')==0){
                row.parentElement.removeChild(row);
                if(!--t.newRows){
                    t.rowWithId("newVRow").cells[0].children['appendRowBtn'].disabled=false;
                }
            }else{
                var feed=new Object();
                feed.content={
                    tableOperation:'delRow',
                    dbTable:t.tableName,
                    rowIndex:rowIndex,
                    fColumn:dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[1].id
                }
                feed.row=row;
                feed.rowIndex=row.rowIndex;
                feed.postExpedition=function(feed){
                    if(feed.responseXML.getElementsByTagName('status')[0].textContent=='success'){
                        dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.removeChild(row);
                        delete dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows[feed.row.id];
                        statusField.innerHTML='Row '+feed.rowIndex+' deleted.';
                    }else{
                        statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].textContent;
                    }
                    var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
                    dbTableExecuter.liveUpdate(dbtUpdate);
                }
                statusField.innerHTML='Deleting row...~:{~';
                feed.ferry=new core.shuttle('lib/superScripts/dbTableExecuter.php', feed.content, feed.postExpedition, feed);
            }
        }
    },
    rename: function(evt){
        var elm=this;
        if(elm){
            var preValue=elm.innerHTML;
            try{
                var cell=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[preValue];
                cell.preValue=preValue;
            }catch(e){}
            var f=document.createElement('form');
            f.init=elm;
            f.id=elm.id;
            elm.parentElement.replaceChild(f,elm);
            if(f.id=='colName'){
                f.cIndex=cell.cellIndex;
                f.colName=document.createElement('input');
                f.colName.type='text';
                f.colName.id='colName';
                f.colName.value=preValue;
                f.colName.init=preValue;
                f.colName.title="Enter column name";
                f.colName.size='9';
                f.colName.onmousedown=function(){
                    if(this!=document.activeElement){
                        this.select();
                        this.focus();
                        return false;
                    }
                }
                f.appendChild(f.colName);
                f.appendChild(document.createElement('br'));
                f.type=document.createElement('select');
                f.type.innerHTML="<option value=''>SelectType</option><option value='int'>INT</option><option value='float'>FLOAT</option><option value='varchar'>VARCHAR</option><option value='timestamp'>TIMESTAMP</option><option value='enum'>ENUM</option>";
                f.type.id='type';
                f.type.title='select data type';
                var type=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[cell.cellIndex].Type;
                var i=0;
                var otype='';
                while(type[i]!='(' && type[i]!=null){
                    otype+=type[i];
                    i++;
                }
                var ol='';
                i++;
                while(type[i]!=')' && type[i]!= null){
                    ol+=type[i];
                    i++;
                }
                f.type.value=otype;
                f.type.init=otype;
                f.appendChild(f.type);
                f.appendChild(document.createElement('br'));
                f.size=document.createElement('input');
                f.size.type='text';
                f.size.id='size';
                f.size.value=ol;
                f.size.init=ol;
                f.size.title="Enter size, if enum enter values in single quotes seperated by ','\n eg.:'ORANGE','WHITE','GREEN','blue'";
                f.size.size='20';
                f.size.onclick=function(){
                    this.select();
                    return false;
                }
                f.appendChild(f.size);
                f.appendChild(document.createElement('br'));
                f.dflt=document.createElement('input');
                f.dflt.type='text';
                f.dflt.value=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[cell.cellIndex].Default;
                f.dflt.init=f.dflt.value;
                f.dflt.title="Enter a default value if any \nelse leave it blank";
                f.dflt.size='20';
                f.dflt.onclick=function(){
                    this.select();
                    return false;
                }
                f.appendChild(f.dflt);
                f.appendChild(document.createElement('br'));
                f.notNull=document.createElement('input');
                f.notNull.type='checkbox';
                f.notNull.id='notNull';
                f.notNull.name='notNull';
                f.notNull.title='check if column should be given \n a value upon new row.';
                if(dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[cell.cellIndex].Null=='NO'){
                    f.notNull.checked=true;
                }
                f.notNull.init=f.notNull.checked;
                f.appendChild(f.notNull);
                var col=document.createElement('input');
                col.type='submit';
                col.id='editColBtn';
                col.value='EditColumn';
                f.appendChild(col);
            }
            else if(f.id=='tableName'){
                f.colName=document.createElement('input');
                f.colName.type='text';
                f.colName.id='tableName';
                f.colName.value=preValue;
                f.colName.init=preValue;
                f.colName.title="Enter column name";
                f.colName.size='9';
                f.colName.onmousedown=function(){
                    if(this!=document.activeElement){
                        this.select();
                        this.focus();
                        return false;
                    }
                }
                f.appendChild(f.colName);
            }
            f.cE=document.createElement('img');
            f.cE.id='cancelInsert';
            f.cE.className='ibtn';
            f.cE.src='images/-.png';
            f.cE.elm=elm;
            f.cE.f=f;
            f.cE.onclick=function(){
                this.f.parentElement.replaceChild(this.elm,this.f);
                return false;
            }
            f.cE.title='cancel edit';
            f.appendChild(f.cE);
            f.firstChild.focus();
            f.firstChild.select();
            f.onsubmit=function(evt){
                var elm;
                if(evt && evt!=window.event){
                    elm=evt;
                }else{
                    evt=window.event?window.event:null;
                    if(evt){
                        elm=evt.targetElement?evt.targetElement:evt.srcElement;
                    }
                }
                evt.preventDefault();
                if((this.id=='colName' && (this.colName.value != this.colName.init || this.type.value !=this.type.init || this.size.value != this.size.init || this.dflt.value != this.dflt.init || this.notNull.checked != this.notNull.init)) || (this.id=='tableName' && this.colName.value!=this.colName.init)){
                    if(/-/.test(this.colName.value)){
                        alert('column name should not contain \'-\'.');
                        this.colName.focus();
                        return false;
                    }
                    if(this.colName.value.length>16 || this.colName.value=='item'){
                        alert('column name should not be "item" or column name length should not exceed 16 characters.');
                        this.colName.focus();
                        return false;
                    }
                    if(this.type.value==''){
                        alert('Select a data type for the column');
                        this.type.focus();
                        return false;
                    }
                    var feed=new Object();
                    var content='tableOperation=rename'+this.id+'&dbTable='+dbTableExecuter.frontTable+'&newName='+this.colName.value;
                    feed.content={
                        tableOperation:'rename'+this.id,
                        dbTable:dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.tableName,
                        newName:this.colName.value
                    }
                    if(elm.id=='colName'){
                        feed.content.colName=this.colName.init;
                        feed.content.type=this.type.value;
                        feed.content.size=this.size.value;
                        feed.content.notNull=this.notNull.checked;
                        feed.content.dfault=this.dflt.value;
                    }
                    feed.elm=this;
                    feed.cell=cell;
                    feed.postExpedition=function(feed){
                        if(feed.responseXML.getElementsByTagName('status')[0].textContent=='success'){
                            if(feed.elm.id=='tableName'){
                                dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.tableName=feed.elm.colName.value;
                                feed.elm.init.innerHTML=feed.elm.colName.value;
                                feed.elm.parentElement.replaceChild(feed.elm.init,feed.elm);
                            }else if(feed.elm.id=='colName'){
                                dbTableExecuter.preFormatColHeadCell(feed.cell,feed.content.newName,feed.content.type+'('+feed.content.size+')',feed.content.notNull?'NO':'YES',feed.cell.Key,feed.content.dfault,feed.cell.Extra,feed.cell.Comment,dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table);
                                dbTableExecuter.formatColHeadCell(feed.cell,feed.content.newName,dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table);
                                var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
                                feed.cell.id=feed.elm.colName.value;
                                for(var i in rows){
                                    rows[i].cells[feed.cell.preValue].id=feed.cell.id;
                                }
                                var preName=feed.elm.init.textContent;
                                var cNLI=dbTableExecuterTool.tableNameInputField.cNL.children[feed.content.dbTable].children[preName];
                                cNLI.id=preName;
                                cNLI.innerHTML=preName;
                                feed.cell.ee=[];
                            /*for(var i=0;i<feed.cell.ee.length;i++){
                                        var sc=feed.cell.ee[i].split(",");
                                        sc=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rowWithPid(sc[1]).cells[sc[0]];
                                        var formula=sc.formula;
                                        if(formula && formula!=''){
                                            var index=formula.indexOf(cell.preValue);
                                            while(index>-1 && (/[^a-zA-Z0-9_]/.test(formula[index-1]) || !formula[index-1])){
                                                var sInd=index;
                                                index=index+cell.preValue.length;
                                                if(/[^a-zA-Z0-9_]/.test(formula[index]) || !formula[index]){
                                                    formula=formula.slice(0,sInd)+cell.id+formula.slice(index);
                                                    index=sInd+cell.id.length;
                                                }
                                                index=formula.indexOf(cell.preValue,index);
                                            }
                                            sc.focus();
                                            dbTableExecuterTool.tableNameInputField.value="="+formula;
                                            dbTableExecuter.updateFormula.call(dbTableExecuterTool.tableNameInputField);
                                        }
                                    }*/
                            }
                            statusField.innerHTML='Edit completed ~e/~.';
                        }else{
                            feed.elm.parentElement.replaceChild(feed.elm.init,feed.elm);
                            statusField.innerHTML=feed.responseXML.getElementsByTagName('status')[0].textContent;
                        }
                        var dbtUpdate=JSON.parse(feed.responseXML.getElementsByTagName('dbtUpdate')[0].textContent);
                        dbTableExecuter.liveUpdate(dbtUpdate);
                    }
                    feed.ferry=new core.shuttle('lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
                }else{
                    elm.parentElement.replaceChild(elm.init,elm);
                }
            }
        }
    },
    scrollBy:function(pitch,goTo){
        pitch=pitch?pitch:dbTableExecuter.vRowCount;
        var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rows;
        var hrc=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.thead.rows.length;
        var tRI=hrc+dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.rows.length-1;
        var vInd=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.getElementsByClassName('vInd')[0];
        if(!vInd){
            vInd=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.rows[0];
            if(vInd)vInd.classList.add('vInd');
        }
        if(vInd){
            var lwr=vInd.rowIndex;
            var nlwr=null;
            if(goTo==undefined){
                nlwr=lwr+pitch;
            }else if(goTo=='START'){
                nlwr=hrc;
            }else if(goTo=='END'){
                nlwr=tRI-pitch;
            }else if(goTo>=0){
                nlwr=goTo+hrc;
            }
            nlwr=nlwr>hrc?nlwr:hrc;
            nlwr=nlwr>tRI-pitch?tRI-pitch:nlwr;
            nlwr=nlwr>hrc?nlwr:hrc;
            if(lwr==nlwr){
                for(var i=0;i<tRI;i++){
                    if(rows[i].offsetWidth>0){
                        lwr=i;
                        break;
                    }
                }
            }
            if(lwr!=nlwr){
                for(var i=hrc;i<tRI;i++){
                    rows[i].style.display='none';
                }
                var upr=nlwr+Math.abs(pitch);
                upr=upr>tRI?tRI:upr;
                var r=null;
                for(i=nlwr;i<upr;i++){
                    r=rows[i];
                    if(!r.hide)r.style.display=null;
                }
                vInd.classList.remove('vInd');
                rows[nlwr].classList.add('vInd');
            }
        }
    },
    specifyRows:function(){
        var rowSelectors=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.getElementsByClassName('row selector');
        if(!dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.classList.contains('rowSelection')){
            dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.classList.add('rowSelection');
            for(var i=0;i<rowSelectors.length;i++){
                rowSelectors[i].style.display=null;
            }
        }
        rowSelectors[0].focus();
        statusField.innerHTML="select rows";
        return false;
    },
    activateCell:function(){
        if(!this.classList.contains('active')){
            var ac=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.getElementsByClassName('active');
            for(var i=0;ac.length!=0;){
                ac[i].classList.remove('active');
            }
            this.classList.add('active');
            if(this.children['inputCell']){
                this.children['inputCell'].focus();
            }else{
                this.focus();
            }
        }
    },
    preFormatColHeadCell: function(th,ci,Type,Null,Key,Default,Extra,Comment,table){
        th.innerHTML="";
        th.id=ci;
        th.tabIndex='0';
        th.onfocus=dbTableExecuter.focusCell;
        th.Type=Type;
        th.Null=Null;
        th.Key=Key;
        th.Default=Default;
        th.Extra=Extra;
        th.Comment=Comment;
        if(Key=='PRI')table.priKey=th.id;
    },
    formatColHeadCell: function(th,ci,table){
        th.innerHTML="<div id='colName' draggable='true' ondrag=\"core.scrollOnDragToEdge.call(this);\" ondragstart=\"event.dataTransfer.setData('text/columnName',event.target.innerHTML);\" ondblclick='dbTableExecuter.rename.call(this); return false;' style=''>"+ci+"</div><div id='tools'><button id=\"newColumnBtn\" class='add ibtn' title=\"Insert new Column\" onclick=\"dbTableExecuter.newColumn.call(this); return false;\" style=\"\"></button><button id=\"delColumnBtn\" class='del ibtn' title=\"Delete column\" onclick=\"dbTableExecuter.delColumn.call(this); return false;\" style=\"\"></button><button id=\"dispAuthorityBtn\" class='auth ibtn' title=\"Execute authority on columns\" onclick=\"if(this.msgPanel){this.msgPanel.activate();}else{this.msgPanel=dbTableExecuter.authorityBlock.call(this);} return false;\" style=\"\"></button></div>";
        th.selector=document.createElement('input');
        th.selector.type='checkbox';
        th.selector.id='selector';
        th.selector.className='col selector';
        th.selector.style.display='none';
        th.appendChild(th.selector);
        th.cellEditTemp=dbTableExecuter.cellEditTempGen(th);
        th.colName=th.children['colName'];
        th.colName.ondrag=core.scrollOnDragToEdge;
        th.addEventListener('mouseover',dbTableExecuter.showColumnTools,false);
        th.addEventListener('mouseout',dbTableExecuter.hideColumnTools,false);
        var tools=th.children['tools'];
        tools.style.display='none';
        tools.style.position='absolute';
        tools.style.backgroundColor="rgba(255,255,255,0.8)";
        tools.style.boxShadow="rgba(0, 0, 0, 0.246094) 2px 2px 5px";
        tools.addEventListener('mouseover',dbTableExecuter.highLight,false);
        tools.addEventListener('mouseout',dbTableExecuter.lowLight,false);
        $(function() {
            var element = $("tbody",table)[0];
            $(th).resizable({
                /*start:function(event, ui){
                    event.stopPropagation();
                },*/
                /*stop: function(event, ui) {
                    var width1 = $(th).width();
                    $('#'+th.id, element).width(width1);
                },*/
                handles: 'e'
            });
            $(th).mousedown(function (evt) {
                evt.stopPropagation();
            });
        });
    },
    openTableOptions:function(){
        var t=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table;
        if(!t.optionsPanel){
            var icon=this.cloneNode(true);
            icon.disabled=true;
            var body=$('<div>')[0];
            var ct=$('<div id="copyTable">')[0];
            $(body).append(ct);
            var cicon=$('<button>')[0];
            $(cicon).addClass("ibtn");
            $(cicon).css("backgroundPosition","-48px -144px");
            cicon.disabled=true;
            $(ct).append(cicon);
            $(ct).append("CopyTable");
            $(ct).bind("click",function(){
                if(event.srcElement==this){
                    if(!this.panel){
                        var body=$("<div>")[0];
                        var nlabel=$('<label>')[0];
                        $(nlabel).appendTo(body);
                        $(nlabel).html("Name ");
                        var name=$('<input/>')[0];
                        $(name).attr({
                            type: 'text', 
                            id: 'name', 
                            name: 'name'
                        }).appendTo(body);
                        $('<br>').appendTo(body);
                        var os=$('<input>')[0];
                        $(os).attr({
                            type:'checkbox',
                            id:'onlyStructure', 
                            name:'onlyStructure'
                        }).appendTo(body);
                        var slabel=$('<label>').appendTo(body);
                        $(slabel).html("Structure only ");
                        var submit=$('<button>')[0];
                        $(submit).html("Copy");
                        $(submit).bind("click", (function(){
                            var feed={};
                            feed.content={
                                dbTable:t.tableName,
                                tableOperation:"copyTable",
                                name:name.value,
                                onlyStructure:os.value
                            }
                            feed.handle=this;
                            feed.postExpedition=function(feed){
                                var status=feed.responseXML.getElementsByTagName('createTable')[0].getElementsByTagName('status')[0].firstChild.nodeValue;
                                if(status=="success"){
                                    feed.handle.npanel.closePanel.call(feed.handle.npanel);
                                    statusField.innerHTML="This table is copied as "+feed.content.name+" ~:)~";
                                }else{
                                    statusField.innerHTML=status;
                                }
                            }
                            feed.ferry=new core.shuttle("/lib/superScripts/dbTableExecuter.php", feed.content, feed.postExpedition, feed);
                        }));
                        $(submit).appendTo(body);
                        var icon=$(cicon).clone()[0];
                        this.panel=new core.msgPanel("CopyTable", this, this, body, icon);
                        submit.npanel=this.panel;
                    }else{
                        this.panel.activate();
                        this.panel.reAlign();
                    }
                }
            });
            var dt=$("<div id='deleteTable'  onclick='dbTableExecuter.delTable();return false;'>")[0];
            $(body).append(dt);
            var dtIcon=$("<button id='delTableBtn' class='del ibtn' style=\"\">")[0];
            $(dt).append(dtIcon);
            $(dt).append('Delete table');
            var tp=$("<div id='deleteTable'>")[0];
            $(body).append(tp);
            var pIcon=$("<button id=\"dispAuthorityBtn\" class='auth ibtn' style=\"\"></button>")[0];
            $(tp).bind("click",function(){
                if(event.srcElement==this){
                    if(!this.panel){
                        this.panel=dbTableExecuter.authorityBlock(dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.tHR.cells['index']);
                    }else{
                        this.panel.activate();
                        this.panel.reAlign();
                    }
                }
            })
            $(tp).append(pIcon);
            $(tp).append('TablePermissions');
            t.optionsPanel=new core.msgPanel(t.tableName+"TableOptions", t, t, body, icon)
        }else{
            t.optionsPanel.activate();
            t.optionsPanel.reAlign();
        }
    },
    genTable: function(dA,aTN){
        var tg=this;
        tg.displayArea=dA;
        tg.table=document.createElement('table');
        tg.table.tableName=aTN;
        tg.table.view='page';
        var tr=null;
        var td=null;
        var th=null;
        var col=null;
        var colgrp=document.createElement('colgroup')
        tg.table.appendChild(colgrp);
        tg.thead=document.createElement('thead');
        tg.tbody=document.createElement('tbody');
        tg.table.appendChild(tg.thead);
        tg.table.appendChild(tg.tbody);
        tg.table.oRows={};
        var tCP=dbTableExecuter.tables[aTN].tableCellProps;
        var i=0;
        var row=tCP['tHR'];
        tr=document.createElement('tr');
        tr.id='tHR';
        tg.table.tHR=tr;
        for(var ci in row){
            var cell=row[ci];
            col=document.createElement('col');
            colgrp.appendChild(col);
            th=document.createElement('th');
            dbTableExecuter.preFormatColHeadCell(th,ci,cell.Type,cell.Null,cell.Key,cell.Default,cell.Extra,cell.Comment,tg.table);
            if(ci=='index'){
                col.width='22'
                th.innerHTML="<div id='colName'></div><button id='optionsBtn' class='ibtn' title='Options' onclick='dbTableExecuter.openTableOptions.call(this);' style=\"background-position:-432px -0px\"></button>";
                th.className='unsortable';
                th.rowSelector=new core.animatedImage(['images/checkbox.png','images/checkboxHover.png','images/checkboxMD.png','images/checkboxC.png','images/checkboxCHover.png','images/checkboxCMD.png'],core.selectAll);
                th.rowSelector.trigMouseEvt();
                th.rowSelector.id='rowSelector';
                th.rowSelector.style.display='none';
                th.rowSelector.className='all row selector';
                th.rowSelector.click=false;
                th.rowSelector.dblclick=false;
                th.rowSelector.title='click to check visible rows\ndoubleClick to check all rows';
                th.rowSelector.__proto__.selected=false;
                th.rowSelector.__proto__.allSelected=false;
                th.rowSelector.style.border='1px solid transparent';
                th.colSelector=new core.animatedImage(['images/checkbox.png','images/checkboxHover.png','images/checkboxMD.png','images/checkboxC.png','images/checkboxCHover.png','images/checkboxCMD.png'],core.selectAll);
                th.colSelector.trigMouseEvt();
                th.colSelector.id='colSelector';
                th.colSelector.style.display='none';
                th.colSelector.className='all col selector';
                th.colSelector.selected=false;
                th.colSelector.allSelected=false;
                th.colSelector.click=false;
                th.colSelector.dblclick=false;
                th.colSelector.title='click to check visible rows\ndoubleClick to check all rows';
                th.appendChild(th.colSelector.__proto__);
                th.appendChild(th.rowSelector.__proto__);
                tg.table.tableName=cell.tableName;
                tg.table.rowCount=cell.rowCount;
                tg.table.colCount=cell.colCount;
                tg.table.authorization=cell.authorization;
                if(tg.table.authorization!='*'){
                    $('#delTableBtn',th)[0].disabled=true;
                    tools.children['dispAuthorityBtn'].disabled=true;
                    tools.children['dispAuthorityBtn'].style.display='none';
                }
            }else{
                col.width='100';
                dbTableExecuter.formatColHeadCell(th,ci,tg.table);
                if(row.index.authorization!='*'){
                    th.children['tools'].children['newColumnBtn'].disabled=true;
                    th.children['tools'].children['delColumnBtn'].disabled=true;
                    th.children['tools'].children['dispAuthorityBtn'].disabled=true;
                }
                for(var pid in cell){
                    if(pid=='f'){
                        
                    }else if(pid=='style'){
                        
                    }else{
                        th[pid]=cell[pid];
                    }
                }
            }
            if(ci==tg.table.priKey)th.style.backgroundColor="rgba(50,200,50,0.8)";
            if(cell['f']){
                if(cell['f']['f'])th.formula=cell['f']['f'].replace(/~~/g,"'").replace(/@@/g,'"');else th.formula='';
                if(cell['f']['ee'])th.ee=cell['f']['ee'];else th.ee=[];
                if(cell['f']['sCs'])th.sCs=cell['f']['sCs'];
            }else{
                th.formula='';
                th.ee=[];
            }
            if(cell['style']){
                for(var pid in cell['style']){
                    if(pid!="display" && pid!="position" && pid!="width" && pid!="height" && pid!="fontSize"){
                        th.style[pid]=cell['style'][pid];
                    }
                }
            }
            tr.appendChild(th);
        }
        th=document.createElement('th');
        th.className='unsortable';
        th.innerHTML='<div id="tools"><button id="newColumnBtn" class="add ibtn" title="Append a new Column" onclick="dbTableExecuter.newColumn.call(this); return false;"></button></div>';
        tr.appendChild(th);
        if(row.index.authorization!='*'){
            th.children['tools'].children['newColumnBtn'].disabled=true;
        }
        tg.tHR=tr;
        tg.thead.appendChild(tr);
        var cNL=document.createElement('div');
        cNL.id=aTN;
        cNL.classList.add('helpList');
        for(var col in row){
            var cn=document.createElement('div');
            cn.id=col;
            cn.innerHTML=col;
            cn.classList.add('fillHelpItem');
            cNL.appendChild(cn);
        }
        var oCNL=dbTableExecuterTool.tableNameInputField.cNL.children[aTN];
        if(oCNL){
            oCNL.parentElement.removeChild(oCNL);
        }
        dbTableExecuterTool.tableNameInputField.cNL.appendChild(cNL);
        var cnll=dbTableExecuterTool.tableNameInputField.cNL.children.length;
        for(var i=0;i<cnll;i++){
            dbTableExecuterTool.tableNameInputField.cNL.children[i].style.display="none";
        }
        dbTableExecuterTool.tableNameInputField.cNL.children[dbTableExecuter.frontTable].style.display=null;
        dbTableExecuterTool.tableNameInputField.cNL.style.display='block';
        if(cNL.offsetHeight>150){
            cNL.offsetHeight="150px";
        }
        dbTableExecuterTool.tableNameInputField.cNL.style.display='none';
        var jTHR=tCP['tHR'];
        delete tCP['tHR'];
        i=0;
        for(var ri in tCP){
            var row=tCP[ri];
            tr=document.createElement('tr');     
            tr.id=ri;
            for(var ci in jTHR){
                cell=row[ci];
                td=document.createElement('td');
                td.tabIndex='0';
                td.onfocus=dbTableExecuter.focusCell;
                td.id=ci;
                if(cell){
                    if(ci=='index'){
                        tr.pid=tg.table.priKey?row[tg.table.priKey]['innerHTML']:undefined;
                        td.innerHTML="<button id='delRowBtn' title='Delete row "+tr.id+"' class='del rowDeleter ibtn' onclick='dbTableExecuter.delRow.call(this);return false;' style=\"\"></button>";
                        td.style.textAlign='center';
                        tr.appendChild(td);
                        tr.selector=document.createElement('input');
                        tr.selector.type='checkbox';
                        tr.selector.id='selector';
                        tr.selector.value=tr.id;
                        tr.selector.className='row selector';
                        tr.selector.style.display='none';
                        td.appendChild(tr.selector);
                        td.title=tr.id;
                        if(tg.table.authorization!='*'){
                            td.children['delRowBtn'].disabled=true;
                        }
                    }else{
                        td.className="dc";
                        td.onkeydown=dbTableExecuter.cellNavHandler;
                        td.ondblclick=dbTableExecuter.editCell;
                    }
                    for(var pid in cell){
                        if(pid=='f'){
                            if(cell['f']['f'])td.formula=cell['f']['f'].replace(/~~/g,"'").replace(/@@/g,'"');else td.formula='';
                            if(cell['f']['ee'])td.ee=cell['f']['ee'];else td.ee=[];
                            if(cell['f']['sCs'])td.sCs=cell['f']['sCs'];
                        }else if(pid=='style'){
                            for(var spid in cell['style']){
                                if(spid!="display" && spid!="position" && spid!="width" && spid!="height" && spid!="fontSize"){
                                    td.style[spid]=cell['style'][spid];
                                }
                            }
                        }else if(ci!='index' || pid!='innerHTML'){
                            td[pid]=cell[pid];
                        }
                    }
                }else{
                    td.className="dc";
                    td.onkeydown=dbTableExecuter.cellNavHandler;
                    td.ondblclick=dbTableExecuter.editCell;
                }
                if(!td.formula)td.formula="";
                if(!td.ee || td.ee.length<1)td.ee=[];
                tr.appendChild(td);
            }
            i++;
            if(i>dbTableExecuter.vRowCount)tr.style.display='none';
            if(i==1)tr.classList.add('vInd');
            tg.tbody.appendChild(tr);
            tg.table.oRows[tr.id]=tr;
        }
        tr=document.createElement('tr');
        tr.id='newVRow';
        td=document.createElement('td');
        td.id='appendRowTool';
        tr.appendChild(td);
        td.style.textAlign='center';
        td.innerHTML="<button id='appendRowBtn' title='Append row' class='add ibtn' onclick='dbTableExecuter.appendRow.call(this);return false;' style=\"\"></button>";
        if(tg.table.authorization!='*'){
            td.children['appendRowBtn'].disabled=true;
        }
        td=document.createElement('td');
        td.innerHTML="<div id='scrollTool' style='width:80px;margin:auto'><button id='top' title='top' class='ibtn' onclick=\"dbTableExecuter.scrollBy(dbTableExecuter.vRowCount,'START');return false;\" style=\"\"></button><button id='up' title='up' class='ibtn' onclick='dbTableExecuter.scrollBy(-dbTableExecuter.vRowCount);return false;' style=\"\"></button><button id='allOrFew' class='ibtn' title='all or few' onclick=\"dbTableExecuter.allOrFew.call(this);return false;\" style=\"\"></button><button id='down' class='ibtn' title='down' onclick='dbTableExecuter.scrollBy(dbTableExecuter.vRowCount);return false;' style=\"\"></button><button id='bottom' class='ibtn' title='bottom' onclick=\"dbTableExecuter.scrollBy(dbTableExecuter.vRowCount,'END');return false;\" style=\"\"></button></div>";
        td.style.textAlign='center';
        td.title="scroll";
        tr.appendChild(td);
        td=document.createElement('td');
        td.id='tableTools';
        td.innerHTML="<button id='print' title='print' class='ibtn' onclick=\"dbTableExecuter.printTable.call(this);return false;\" style=\"\"></button>";
        tr.appendChild(td);
        tr.className='sortbottom';
        tg.tbody.appendChild(tr);
        tg.table.appendChild(tg.tbody);
        tg.table.id='dbTableViewer';
        tg.table.className='sortable';
        tg.displayArea.appendChild(tg.table);
        tg.table.rowWithId=function(id){
            var aTN=arguments.callee.table;
            var rows=aTN.oRows;
            if(id=='tHR'){
                return aTN.rows['tHR'];
            }
            if(id=='newRow'){
                return aTN.rows['newRow'];
            }
            if(id=='newVRow'){
                return aTN.rows['newVRow'];
            }
            return rows[id];
        }
        tg.table.rowWithPid=function(pid){
            //pid=core.htmlEncode(pid);
            var aTN=arguments.callee.table;
            var rows=aTN.oRows;
            if(pid===""){
                return aTN.rows['tHR'];
            }
            for(var rid in rows){
                if(rows[rid].pid==pid){
                    return rows[rid];
                }
            }
        }
        tg.table.rowWithId.table=tg.table;
        tg.table.rowWithPid.table=tg.table;
        tg.table.newRows=0;
    },
    showColumnTools:function(){
        this.children['tools'].style.display=null;
    },
    hideColumnTools:function(){
        this.children['tools'].style.display='none';
    },
    highLight:function(){
        this.style.backgroundColor="rgba(150,50,50,0.9)";
    },
    lowLight:function(){
        this.style.backgroundColor="rgba(255,255,255,0.8)";
    },
    cellNavHandler:function(){
        var cell;
        var row;
        var tbody;
        if(arguments[0]){
            var event=arguments[0];
        }
        var se=event.srcElement;
        event.cancelBubble=true;
        if(event.keyCode==40){
            cell=this;
            row=cell.parentElement;
            tbody=row.parentElement;
            var nxtCell=row.nextSibling.cells[cell.cellIndex];
            if(nxtCell && nxtCell.classList.contains('dc')){
                if(tbody.rows[row.rowIndex].style.display=='none'){
                    tbody.rows[row.rowIndex].style.display=null;
                    tbody.rows[row.rowIndex-dbTableExecuter.vRowCount].style.display='none';
                }
                if(nxtCell.offsetWidth>0){
                    if(se.classList.contains('inputCell')){
                        se.onblur=null;
                        dbTableExecuter.changeEntry.call(se);
                        nxtCell.ondblclick.call(nxtCell)
                        return false;
                    }else{
                        dbTableExecuter.activateCell.call(nxtCell);
                        return false;
                    }
                }
            }
        }else if(event.keyCode==38){
            cell=this;
            row=cell.parentElement;
            tbody=row.parentElement;
            if(row.rowIndex>1)var nxtCell=tbody.rows[row.rowIndex-2].cells[cell.cellIndex];
            if(nxtCell && nxtCell.classList.contains('dc')){
                if(tbody.rows[row.rowIndex-2].style.display=='none'){
                    tbody.rows[row.rowIndex-2].style.display=null;
                    tbody.rows[row.rowIndex-2+dbTableExecuter.vRowCount].style.display='none';
                }
                if(nxtCell.offsetWidth>0){
                    if(se.classList.contains('inputCell')){
                        se.onblur=null;
                        dbTableExecuter.changeEntry.call(se);
                        nxtCell.ondblclick.call(nxtCell)
                        return false;
                    }else{
                        dbTableExecuter.activateCell.call(nxtCell);
                        return false;
                    }
                }
            }
        }
        else if(event.keyCode==39){
            cell=this;
            row=cell.parentElement;
            tbody=row.parentElement;
            var nxtCell=row.cells[this.cellIndex+1];
            if(nxtCell && nxtCell.offsetWidth>0 && nxtCell.classList.contains('dc')){
                if(nxtCell.offsetWidth>0){
                    if(se.classList.contains('inputCell')){
                        if(se.selectionEnd==se.value.length && se.selectionStart==se.selectionEnd){
                            se.onblur=null;
                            dbTableExecuter.changeEntry.call(se);
                            nxtCell.ondblclick.call(nxtCell)
                            return false;
                        }
                    }else{
                        dbTableExecuter.activateCell.call(nxtCell);
                        return false;
                    }
                }
            }
        }
        else if(event.keyCode==37){
            cell=this;
            row=cell.parentElement;
            tbody=row.parentElement;
            var nxtCell=row.cells[this.cellIndex-1];
            if(nxtCell && nxtCell.classList.contains('dc')){
                if(nxtCell.offsetWidth>0){
                    if(se.classList.contains('inputCell')){
                        if(se.selectionStart==0 && se.selectionStart==se.selectionEnd){
                            se.onblur=null;
                            dbTableExecuter.changeEntry.call(se);
                            nxtCell.ondblclick.call(nxtCell)
                            return false;
                        }
                    }
                    else{
                        dbTableExecuter.activateCell.call(nxtCell);
                        return false;
                    }
                };
            }
        }else if(event.keyCode==13){
            event.preventDefault();
            cell=this;
            if(se.classList.contains('inputCell')){
                se.onblur=null;
                dbTableExecuter.changeEntry.call(se);
                cell.focus();
            }
            else{
                dbTableExecuter.editCell.call(this);
            }
        }else if(event.keyCode==27){
            cell=this;
            if(se.classList.contains('inputCell')){
                se.onblur=null;
                cell.innerHTML=cell.preValue;
            }
        }else if(event.keyCode==36){
            cell=this;
            row=cell.parentElement;
            tbody=row.parentElement;
            var dcs=row.getElementsByClassName('dc');
            for(var i=0;i<dcs.length;i++){
                if(dcs[i].offsetWidth>0){
                    var nxtCell=dcs[i];
                    break;
                }
            }
            if(nxtCell && nxtCell!=cell){
                if(se.classList.contains('inputCell')){
                    if(se.selectionStart==0 && se.selectionStart==se.selectionEnd){
                        se.onblur=null;
                        dbTableExecuter.changeEntry.call(se);
                        nxtCell.ondblclick.call(nxtCell)
                        return false;
                    }
                }else{
                    dbTableExecuter.activateCell.call(nxtCell);
                    return false;
                }
            }
        }else if(event.keyCode==35){
            cell=this;
            row=cell.parentElement;
            tbody=row.parentElement;
            var dcs=row.getElementsByClassName('dc');
            for(var i=dcs.length-1;i>-1;i--){
                if(dcs[i].offsetWidth>0){
                    var nxtCell=dcs[i];
                    break;
                }
            }
            if(nxtCell && nxtCell!=cell){
                if(se.classList.contains('inputCell')){
                    if(se.selectionStart==se.value.length && se.selectionStart==se.selectionEnd){
                        se.onblur=null;
                        dbTableExecuter.changeEntry.call(se);
                        nxtCell.ondblclick.call(nxtCell)
                        return false;
                    }
                }
                else{
                    dbTableExecuter.activateCell.call(nxtCell);
                    return false;
                }
            }
        }
    },
    cellEditTempGen:function(th){
        var cellEditTemp;
        var type=th.Type;
        var maxLength = "";
        var j=0;
        var k=0;
        while (type[j - 1] != ')' && type[j]!=null) {
            if (type[j] == '(') {
                j++;
                while (type[j] != ')') {
                    maxLength+= type[j];
                    k++;
                    j++;
                }
            }
            j++;
        }
        if(maxLength=='')maxLength='NA';
        var size=maxLength>20?20:maxLength;
        if(/enum/.test(th.Type)){
            var options=[];
            var i=0;
            options=size.split(',');
            for(i=0;i<options.length;i++){
                options[i]=options[i].slice(1,options[i].length-1);
            }
            cellEditTemp=dbTableExecuter.genSelectBox(options)
        }else{
            cellEditTemp=document.createElement('input')
            cellEditTemp.type='text';
            cellEditTemp.maxlength=maxLength;
            cellEditTemp.size=size;
        }
        cellEditTemp.setAttribute('value',th.Default);
        cellEditTemp.value=th.Default;
        cellEditTemp.classList.add('inputCell');
        cellEditTemp.id='inputCell';
        return cellEditTemp;
    },
    genSelectBox:function(options){
        var sb=document.createElement('select');
        var opt=null;
        options[-1]='';
        for(var i=-1;i<options.length;i++){
            opt=document.createElement('option');
            opt.value=options[i]
            opt.innerHTML=options[i]
            sb.appendChild(opt);
        }
        return sb;
    },
    searchTable:function(searchStr,tbody,options){
        if(!options)options={};
        if(!options.wolS)options.subS=true;
        if(!options.matchCase)options.caseLess=true;
        var sStr=searchStr.toLowerCase();
        var rows=tbody.rows;
        var rowCount=rows.length-1;
        var cellCount=rows[0].cells.length;
        try{
            var searchIndCell=tbody.getElementsByClassName('SI')[0];
            var cellInd=searchIndCell.cellIndex;
            var rowInd=searchIndCell.parentElement.rowIndex-dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.thead.rows.length;
        }
        catch(e){}
        var rowInd=rowInd>=0?rowInd:0;
        var cellInd=cellInd>=0?cellInd+1:0;
        var row=rows[rowInd];
        var cells=row.cells;
        var lastRow=rowCount-1;
        var ri=rowInd;
        var ci=cellInd;
        for(var i=ri;i<rowCount;i++){
            row=rows[i];
            cells=row.cells;
            for(var j=ci;j<cellCount;j++){
                if((!cells[j].classList.contains('hidden') && options.subS && ((options.caseLess && cells[j].innerHTML.toLowerCase().indexOf(sStr)>-1)||(options.matchCase && cells[j].innerHTML.indexOf(searchStr)>-1))) ||(options.wolS && ((options.caseLess && cells[j].innerHTML.toLowerCase()==sStr)||(options.matchCase && cells[j].innerHTML==searchStr)) )){
                    if(cells[j].offsetWidth<1)dbTableExecuter.scrollBy(dbTableExecuter.vRowCount, i);
                    cells[j].classList.add('SI');
                    cells[j].style.backgroundColor='#ffff55';
                    cells[j].scrollIntoViewIfNeeded();
                    if(searchIndCell && searchIndCell!=cells[j]){
                        searchIndCell.classList.remove('SI');
                        searchIndCell.style.backgroundColor=null;
                    }
                    var found=true;
                    break;
                }
            }
            ci=0;
            if(found)break;
            else if(i==lastRow && searchIndCell && !loop){
                i=-1;
                rowCount=ri+1;
                var loop=true
            }
        }
        if(found)statusField.innerHTML='Search completed... Found ~:)~';
        else statusField.innerHTML='Search completed... Not found ~:|~';
    },
    copyCells:function(){
        var h=selectedElements.getElementsByTagName('th');
        var tb=document.createElement('table');
        if(h.length>0){
            var th=document.createElement('tr');
            var tdh;
            for(i=0;i<h.length;i++){
                tdh=h[i].cloneNode();
                tdh.innerHTML=h[i].textContent;
                th.appendChild(tdh);
            }
            tb.appendChild(th);
        }
        var r=selectedElements.getElementsByTagName('td');
        var k=0;
        for(var i=0;i<r.length;i++){
            if(k!=r[i].parentElement.rowIndex){
                k=r[i].parentElement.rowIndex;
                if(tr)tb.appendChild(tr);
                var tr=document.createElement('tr');
                tr.appendChild(r[i].cloneNode(true));
            }
            else{
                tr.appendChild(r[i].cloneNode(true));
            }
            r[i].classList.add('active');
        }
        if(tr)tb.appendChild(tr);
        clipBoard.innerHTML='';
        clipBoard.appendChild(tb);
        core.popClipBoard('copy');
        statusField.innerHTML='Cells copied to clipBoard ~:)~';
    },
    pasteCells:function(){
        clipBoard.opType='clearCB';
        clipBoard.innerHTML='';
        clipBoard.afterPaste=function(){
            if(clipBoard.firstChild.tagName=='TABLE'){
                var cTable=clipBoard.firstChild;
                var trs=clipBoard.getElementsByTagName('tr');
                var td=null;
                if(selectedElements[0].tagName=='TD'){
                    if(selectedElements[0].classList.contains('dc')){
                        td=selectedElements[0];
                    }
                }else if(selectedElements[0].classList.contains('inputCell')){
                    td=selectedElements[0].parentElement;
                }else{
                    statusField.innerHTML='Paste cant be performed here ~:|~';
                    return false;
                }
                if(td.classList.contains('dc') || (td.parentElement.id.index('newRow')==0 && td.id!='index')){
                    var ti=td.cellIndex;
                    var tr=td.parentElement;
                    var rn=trs.length;
                    var cn=trs[0].cells.length;
                    var hk=false;
                    var priKey=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.priKey;
                    var tHR=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR;
                    for(var i=0;i<cn;i++){
                        if(tHR.cells[ti+i].id==priKey){
                            hk=true;
                            break;
                        }
                    }
                    if(tr.parentElement.parentElement.rows.length-tr.rowIndex<rn && !hk){
                        statusField.innerHTML='Insufficient row sapce ~:|~';
                        return false;
                    }
                    if(tr.cells.length-ti<cn){
                        statusField.innerHTML='Insufficient column space ~:|~';
                        return false;
                    }
                    var tHR=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR;
                    dbTableExecuter.vTable={};
                    var eEA=[];
                    for(var i=0;i<rn;i++){
                        var cRow=cTable.rows[i];
                        if(!tr && dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.authorization=="*"){
                            /*dbTableExecuter.vTable['newRow'+i]={
                                cells:{},
                                id:'newRow'+i
                            };
                            for(var j=0;j<tHR.cells.length-1;j++){
                                var cCell=cRow.cells[j-ti];
                                dbTableExecuter.vTable['newRow'+i].cells[tHR.cells[j].id]={
                                    textContent:'',
                                    parentElement:dbTableExecuter.vTable['newRow'+i],
                                    id:tHR.cells[j].id,
                                    children:{
                                        length:0
                                    },
                                    ee:[],
                                    formula:''
                                };
                                if(cCell){
                                    dbTableExecuter.vTable['newRow'+i].cells[tHR.cells[j].id].children={
                                        inputCell:{
                                            value:cCell.textContent
                                        },
                                        length:1
                                    }
                                }
                            }*/
                            tr=dbTableExecuter.appendRow();
                            td=tr.cells[ti];
                        }
                        for(var j=0;j<cn;j++){
                            var cCell=cRow.cells[j];
                            if(tr){
                                var cell=tr.cells[ti+j];
                                var cAE=false;
                                for(var k=0;k<eEA.length;k++){
                                    if(cell==eEA[k]){
                                        cAE=true;
                                        break;
                                    }
                                }
                                if(!cAE){
                                    cell.ondblclick.call(cell);
                                    cell.children['inputCell'].value=cCell.textContent;
                                    var formula='='+tHR.cells['index'].formula+';='+tr.cells['index'].formula+';='+tHR.cells[ti+j].formula+";='"+cCell.textContent+"'";
                                    dbTableExecuter.updateByFormula(cell,formula,[]);
                                }
                            }
                        }
                        if(tr && tr.nextSibling.id!='newVRow')tr=tr.nextSibling;else tr=null;
                        if(tr)td=tr.cells[ti];
                    }
                    statusField.innerHTML='Pasting cells ~@|~';
                }else{
                    statusField.innerHTML="Paste can not be performed here ~:|~";
                }
            }
        }
        core.popClipBoard('paste');
    },
    constrainColumns:function(cString){
        statusField.innerHTML='Constraining columns ~@|~';
        cString="index,"+cString
        var cArray=cString.split(/ *[\,] */);
        var cIndArr=[];
        for(var i=0;i<cArray.length;i++){
            if(dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[cArray[i]])cIndArr.push(dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells[cArray[i]].cellIndex);
        }
        cIndArr.sort().filter(function(el,i,a){
            if(i==a.indexOf(el))return 1;
            return 0
        })
        dbTableExecuterTool.filteredCellsIndexArray=cIndArr;
        var cl=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells.length;
        var rs=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR;
        var rc=rs.length;
        var k=0;
        for(i=0;i<cl;i++){
            rs.cells[i].classList.remove('hidden');
            if(i==cIndArr[k]){
                k++;
            }else if(rs.cells[i] && rs.cells[i].id){
                rs.cells[i].classList.add('hidden');
            }
        }
        rs=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
        for(var j in rs){
            k=0;
            cl=rs[j].cells.length;
            for(i=0;i<cl;i++){
                rs[j].cells[i].classList.remove('hidden');
                if(i==cIndArr[k]){
                    k++;
                }else if(rs[j].cells[i]){
                    rs[j].cells[i].classList.add('hidden');
                }
            }
        }
        statusField.innerHTML='Columns constrained ~:)~';
        dbTableExecuter.tables[dbTableExecuter.frontTable].colCount.innerHTML="&nbsp;"+cIndArr.length+" columns";
    },
    filterRows:function(rf){
        statusField.innerHTML="Filtering rows ~@|~";
        var exps=rf.split(/ *[!]*[()&|=><*/+\-%?:^][|&=]* */);
        var thr=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR;
        var cc=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells.length;
        var colVs=[]
        for(var i=0;i<exps.length;i++){
            exps[i]=exps[i].split(".")[0];
            for(var j=0;j<cc;j++){
                if(exps[i]==thr.cells[j].id){
                    colVs.push(exps[i]);
                }
            }
        }
        var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
        var colvs=colVs;
        colVs=[];
        for(i=0;i<colvs.length;i++){
            if(colvs[i]!=''){
                colVs.push(colvs[i])
                eval("var "+colvs[i]+"=null");
            }
        }
        var rowIndex=0;
        try{
            dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.getElementsByClassName('vInd')[0].classList.remove('vInd');
        }catch(e){}
        try{
            for(var i in rows){
                if(rows[i]){
                    try{
                        if(rows[i].parentElement)rows[i].parentElement.removeChild(rows[i]);
                        dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.rows[dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.rows.length-1].insertAdjacentElement("beforeBegin",rows[i])
                    }catch(e){}
                    for(j=0;j<colVs.length;j++){
                        var iv=rows[i].cells[thr.cells[colVs[j]].cellIndex].textContent;
                        var kiv=parseFloat(kiv)
                        kiv=kiv.toString()!=iv?"'"+iv+"'":(kiv==''?0:kiv);
                        eval(colVs[j]+"="+kiv);
                    }
                    if(eval(rf)){
                        if(rowIndex%2){
                            rows[i].classList.remove('even');
                            rows[i].classList.add('odd');
                            
                        }else{
                            rows[i].classList.remove('odd');
                            rows[i].classList.add('even');
                        }
                        if(rowIndex<dbTableExecuter.vRowCount){
                            if(rowIndex==0)rows[i].classList.add('vInd');
                            rows[i].style.display=null;
                        }else{
                            rows[i].style.display='none';
                        }
                        rowIndex++;
                    }else{
                        rows[i].parentElement.removeChild(rows[i]);
                    }
                }
            }
        }catch(e){
            statusField.innerHTML=e;
            dbTableExecuter.tables[dbTableExecuter.frontTable].rowCount.innerHTML=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.rows.length-1+" rows,";
            return false;
        }
        statusField.innerHTML="Rows filtered ~B)~";
        dbTableExecuter.tables[dbTableExecuter.frontTable].rowCount.innerHTML=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.rows.length-1+" rows,";
    },
    focusCell:function(){
        dbTableExecuter.activateCell.call(this);
        dbTableExecuterTool.tableNameInputField.style.color='black';
        if(this.formula){
            dbTableExecuterTool.tableNameInputField.value="="+this.formula;
        }else{
            dbTableExecuterTool.tableNameInputField.value="="+(parseFloat(this.textContent).toString()==this.textContent?this.textContent:"'"+this.textContent+"'");
        }
    },
    updateFormula:function(){
        var pf=arguments.callee.pf=function(){
            dbTableExecuter.activateCell.call(arguments.callee.srcElm);
            dbTableExecuterTool.tableNameInputField.value=arguments.callee.formula;
            dbTableExecuter.sandbox.updateFormulaOf=null;
            dbTableExecuter.updateFormula.call(dbTableExecuterTool.tableNameInputField);
        }
        var f=this.value.substring(1,this.value.length);
        var srcElm=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.getElementsByClassName('active')[0];
        if(f!=srcElm.formula){
            statusField.innerHTML="Computing values and updating formula ~@|~";
            if(!dbTableExecuter.sandbox.updateFormulaOf){
                dbTableExecuter.sandbox.updateFormulaOf={
                    cell:srcElm,
                    formula:f,
                    srcNotFetchedCells:0
                };
                srcElm.updateFormulaInfo=dbTableExecuter.sandbox.updateFormulaOf;
                var tHR=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR;
                if(srcElm.parentElement.id=='tHR' && srcElm.id!='index'){
                    var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
                    for(var i in rows){
                        var cell=rows[i].cells[srcElm.id];
                        var formula='='+tHR.cells['index'].formula+';='+srcElm.parentElement.cells['index'].formula+";="+f+";="+cell.formula;
                        if(dbTableExecuter.updateByFormula(cell, formula, [])=='breakCellUpdate'){
                            dbTableExecuter.cellsToBeUpdated[dbTableExecuter.frontTable][i][cell.id].postFunction=pf;
                            pf.formula=this.value;
                            pf.srcElm=srcElm;
                            break;
                        }
                    }
                }else if(srcElm.id=='index' && srcElm.parentElement.id!='tHR'){
                    var row=srcElm.parentElement;
                    var cc=row.cells.length;
                    for(var i=0;i<cc;i++){
                        var cell=row.cells[i];
                        if(cell.id!='index'){
                            var formula='='+tHR.cells['index'].formula+';='+f+";="+tHR.cells[cell.id].formula+";="+cell.formula;
                            if(dbTableExecuter.updateByFormula(cell, formula, [])=='breakCellUpdate'){
                                dbTableExecuter.cellsToBeUpdated[dbTableExecuter.frontTable][row.id][cell.id].postFunction=pf
                                pf.formula=this.value;
                                pf.srcElm=srcElm;
                                break;
                            }
                        }
                    }
                }else if(srcElm.id=='index' && srcElm.parentElement.id=='tHR'){
                    var rows=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.oRows;
                    for(var i in rows){
                        var row=rows[i];
                        var cc=row.cells.length;
                        for(var i=0;i<cc;i++){
                            var cell=row.cells[i];
                            if(cell.id!='index'){
                                var formula='='+f+';='+srcElm.parentElement.cells['index'].formula+";="+tHR.cells[cell.id].formula+";="+cell.formula;
                                if(dbTableExecuter.updateByFormula(cell, formula, [])=='breakCellUpdate'){
                                    dbTableExecuter.cellsToBeUpdated[dbTableExecuter.frontTable][row.id][cell.id].postFunction=pf;
                                    pf.formula=this.value;
                                    pf.srcElm=srcElm;
                                    var breakOp=true;
                                    break;
                                }
                            }
                        }
                        if(breakOp){
                            break;
                        }
                    }
                }else{
                    var formula='='+tHR.cells['index'].formula+';='+srcElm.parentElement.cells['index'].formula+";="+tHR.cells[srcElm.id].formula+";="+f;
                    if(dbTableExecuter.updateByFormula(srcElm, formula, [])=='breakCellUpdate'){
                        dbTableExecuter.cellsToBeUpdated[dbTableExecuter.frontTable][srcElm.parentElement.id][srcElm.id].postFunction=pf
                        pf.formula=this.value;
                        pf.srcElm=srcElm;
                    }
                }
            }else{
                statusField.innerHTML="Please wait till the current formula update is complete ~&|~"
            }
        }
        return false;
    },
    escapeFormulaStr:function(formula,sCodeObj){
        var i=0;
        var qSet=false;
        var dqSet=false;
        var strst=0;
        var oformula="";
        var strc="";
        while(formula[i]){
            if(formula[i]=="'" && formula[i-1]!="\\" && !qSet && !dqSet){
                qSet=true;
                strst=i;
            }else if(formula[i]=="'" && formula[i-1]!="\\" && qSet){
                qSet=false;
                oformula+=strc="strgowswa"+strst;
                sCodeObj[strc]=formula.substring(strst,i+1);
            }else if(formula[i]=='"' && formula[i-1]!="\\" && !dqSet && !qSet){
                dqSet=true;
                strst=i;
            }else if(formula[i]=='"' && formula[i-1]!="\\" && dqSet){
                dqSet=false;
                oformula+=strc="strgowswa"+strst;
                sCodeObj[strc]=formula.substring(strst,i+1);
            }else if(!dqSet && !qSet){
                oformula+=formula[i];
            }
            i++;
        }
        return oformula;
    },
    putStringsInFormula:function(formula,sCodeObj){
        for(var sc in sCodeObj){
            formula=formula.replace(RegExp(sc),sCodeObj[sc]);
        }
        return formula;
    },
    updateByFormula:function(cell,formula,sUVA){
        var aP=false;
        var aUVO=dbTableExecuter.sandbox.aUVO;
        var rid=cell.parentElement.id;
        var cid=cell.id;
        var tid=cell.parentElement.parentElement.parentElement.parentElement.children['ttArea'].children['tableName'].textContent;
        var oformula=formula;
        for(var i=0;i<sUVA.length;i++){
            if(sUVA[i]==cell){
                aP=true;
                break;
            }
        }
        if(!aP && (cell.sKey || dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.authorization=="*")){
            var fObj={
                cellProps:{},
                variables:{}
            }
            if(formula && formula!=''){
                var farr=dbTableExecuter.splitFormula(formula);
                var propArr=[];
                var formArr=[];
                for(var fi=0;fi<farr.length;fi++){
                    if(farr[fi].indexOf('=')>-1){
                        var form=farr[fi].substring(farr[fi].indexOf('=')+1).trim();
                        if(form!=''){
                            propArr.push(farr[fi].substring(0,farr[fi].indexOf('=')).trim());
                            formArr.push(form);
                        }
                    }
                }
                formula=formArr.join(";");
                var k=0;
                var dTable=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable;
                var cells=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tHR.cells;
                var sCodeObj={};
                formula=dbTableExecuter.escapeFormulaStr(formula,sCodeObj);
                var cFOTs=formula.match(/[0-9a-zA-Z_~]+\([0-9a-zA-Z_~*]*\)\([0-9a-zA-Z_]*\)/g)||[];
                var breakOnTableFetch=false;
                cFOTs=cFOTs.filter(function(el,i,a){
                    if(i==a.indexOf(el))return 1;
                    return 0
                });
                var tRanged=false;
                var rRanged=true;
                var cRanged=true;
                if(cFOTs.length>0){
                    for(var j=0;j<cFOTs.length;j++){
                        var p=0;
                        var q=0;
                        var r=0;
                        var rindex=cFOTs[j].indexOf("(", 0);
                        var index=cFOTs[j].indexOf("(",rindex+1);
                        var scTid=cFOTs[j].substring(index+1,cFOTs[j].length-1);
                        var scRid=cFOTs[j].substring(rindex+1, index-1);
                        var scCid=cFOTs[j].substring(0,rindex);
                        var rVar=[];
                        if(dbTableExecuter.tables[scTid]){
                            var table=dbTableExecuter.tables[scTid].dTable.table;
                            var rrows={};
                            var vars=[];
                            var rcells={};
                            var oRows=table.oRows;
                            var nRows=dbTableExecuter.getNewRows();
                            var scRidA=scRid.split("~");
                            if(scRidA.length>1 && table.rowWithPid(scRidA[0]) && table.rowWithPid(scRidA[1])){
                                for(var ri in oRows){
                                    if(oRows[ri].pid==scRidA[0]){
                                        var srmatch=true;
                                    }
                                    if(srmatch){
                                        rrows[scTid+oRows[rrid].pid]=oRows[rrid];
                                    }
                                    if(oRows[ri].pid==scRidA[1]){
                                        if(!srmatch){
                                            break;
                                        }else{
                                            srmatch=false;
                                        }
                                    }
                                }
                            }else if(scRid[0]=='/'){
                                var rMRegExp=new RegExp(scRid);
                                for(var ri in oRows){
                                    if(rMRegExp.test(oRows[ri].pid)){
                                        rrows[scTid+ri]=oRows[ri];
                                    }
                                }
                            }else if(scRid=='*'){
                                for(var ri in oRows){
                                    rrows[scTid+ri]=oRows[ri];
                                }
                                for(var ri in nRows){
                                    rrows[scTid+nRows[ri].id]=nRows[ri];
                                }
                            }else if(scRid!=''){
                                var tr=table.rowWithPid(scRid);
                                if(tr){
                                    rrows[scTid+scRidA[0]]=tr;
                                }
                                rRanged=false;
                            }else if(scRid==''){
                                var tr=table.rowWithPid(cell.parentElement.pid);
                                if(tr)rrows[scTid+tr.id]=tr;
                                rRanged=false;
                            }
                            var rcount=0;
                            for(var rrid in rrows){
                                rcount++;
                                var row=rrows[rrid];
                                var scCidA=scCid.split("~");
                                if(scCidA.length>1 && row.cells[scCidA[0]] && row.cells[scCidA[1]]){
                                    var oCells=row.cells;
                                    for(var ci=0;ci<oCells.length;rcid++){
                                        if(oCells[ci].id==scCidA[0]){
                                            var scmatch=true;
                                        }
                                        if(scmatch){
                                            rcells[rrid+oCells[ci].id]=oCells[ci];
                                        }
                                        if(oCells[ci].id==scCidA[1]){
                                            if(!scmatch){
                                                break;
                                            }else{
                                                scmatch=false;
                                            }
                                        }
                                    }
                                }else if(scCid[0]=='/'){
                                    var rMRegExp=new RegExp(scCid);
                                    for(var ci=0;ci<row.cells.length;ci++){
                                        if(rMRegExp.test(row.cells[ci].id)){
                                            rcells[rrid+oRows[ri].pid]=oRows[ri];
                                        }
                                    }
                                }else if(scCid=='*'){
                                    for(var ci=0;ci<row.cells.length;ci++){
                                        rcells[rrid+oRows[ri].pid]=oRows[ri];
                                    }
                                }else if(scCid!=''){
                                    if(scCid=='index'){
                                        var td=row.cells[cell.id]
                                        if(td)rcells[rrid+td.id]=td;
                                    }else{
                                        var td=row.cells[scCid];
                                        if(td)rcells[rrid+scCid]=td;
                                    }
                                    cRanged=false;
                                }
                            }
                            if(rcount==0){
                                statusField.innerHTML="No row specified by "+cFOTs[j]+", skipping update of cell "+cell.id+"("+(cell.parentElement.pid||cell.parentElement.id)+")("+cell.parentElement.parentElement.parentElement.tableName+")";
                                return false;
                            }
                            var ccount=0;
                            for(var rcid in rcells){
                                var sc=rcells[rcid];
                                if(sc){
                                    ccount++;
                                    var val=sc.children['inputCell']?sc.children['inputCell'].value:sc.textContent;
                                    try{
                                        val=aUVO[scTid][sc.parentElement.id][sc.id]['innerHTML']!=undefined?aUVO[scTid][sc.parentElement.id][sc.id]['innerHTML']:val
                                    }catch(e){}
                                    var kval=parseFloat(val);
                                    kval=kval.toString()!=val?val:kval;
                                    val=kval==''?0:kval;
                                    if(tRanged||rRanged||cRanged){
                                        rVar.push(val);
                                    }else if(rRanged){
                                        rVar=rVar||[];
                                        rVar[q][p]=val;
                                    }else if(cRanged){
                                        rVar=rVar||[];
                                        rVar[p]=val;
                                    }else{
                                        rVar=val;
                                    }
                                /*if(sc==cell){
                                        if(fObj.eVariable){
                                            vars.pop();
                                            vars.push(fObj.eVariable);
                                            delete fObj.variables["var"+k];
                                        }else{
                                            fObj.eVariable="var"+k;
                                        }
                                    }*/
                                }
                                p++;
                            }
                            if(ccount==0){
                                statusField.innerHTML="No cell specified by "+cFOTs[j]+", skipping update of "+cell.id+"("+(cell.parentElement.pid||cell.parentElement.id)+")("+cell.parentElement.parentElement.parentElement.tableName+")";
                            }
                            vars.push("var"+k);
                            fObj.variables["var"+k]=rVar;
                            if(vars.length>0){
                                var re=new RegExp(cFOTs[j].replace(/\(/g,'\\(').replace(/\)/g,'\\)').replace(/\*/g,'\\*'),'g');
                                formula=formula.replace(re,vars.join(","));
                            }
                            r++;
                        }else{
                            dbTableExecuter.cellsToBeUpdated[tid]=dbTableExecuter.cellsToBeUpdated[tid]||{};
                            dbTableExecuter.cellsToBeUpdated[tid][rid]=dbTableExecuter.cellsToBeUpdated[tid][rid]||{};
                            if(!dbTableExecuter.cellsToBeUpdated[tid][rid][cid]){
                                
                            }
                            var cRTO=dbTableExecuter.cellsToBeUpdated[tid][rid][cid]=dbTableExecuter.cellsToBeUpdated[tid][rid][cid]||{
                                reqTab:[scTid],
                                formula:oformula
                            };
                            cRTO.reqTab.push(scTid);
                            cRTO.reqTab=cRTO.reqTab.filter(function(el,i,a){
                                if(i==a.indexOf(el))return 1;
                                return 0
                            });
                            dbTableExecuter.fetchTable(scTid);
                            breakOnTableFetch=true;
                        }
                        k++;
                    }
                }
                if(!breakOnTableFetch){
                    dTable=dbTableExecuter.tables[tid].dTable;
                    var table=dTable.table;
                    cells=dTable.tHR.cells;
                    var colCount=cells.length-1;
                    for(var j=0;j<colCount;j++){
                        var cc=cells[j];
                        var re=new RegExp(cc.id,'g');
                        var ck=formula.match(re)||[];
                        for(var u=0;u<ck.length;u++){
                            var index=formula.indexOf(cc.id);
                            while(index>-1 && (/[^a-zA-Z0-9_'"]/.test(formula[index-1])|| formula[index-1]==undefined)){
                                var sInd=index;
                                index=(j==-1)?index+4:index+cc.id.length;
                                if(/[^a-zA-Z0-9_'"]/.test(formula[index])|| formula[index]==undefined){
                                    var rRanged=true;
                                    var cRanged=true;
                                    var scCidA=[formula.substring(sInd,index)];
                                    var rVar=[];
                                    var p=0;
                                    var q=0;
                                    var r=0;
                                    if(formula[index]=='~'){
                                        scCidA[1]='';
                                        while(/[a-zA-Z0-9]/.test(formula[++index])){
                                            scCidA[1]+=formula[index];
                                        }
                                    }
                                    var scRid='';
                                    if(formula[index]=='('){
                                        while(formula[++index]!=')' && formula[index]!=null){
                                            scRid+=formula[index];
                                        }
                                        index++;
                                    }
                                    var cA=formula.substring(sInd,index);
                                    var rrows={};
                                    var rcells={};
                                    if(scRid){
                                        var oRows=table.oRows;
                                        var scRidA=scRid.split("~");
                                        if(scRidA.length>1 && table.rowWithPid(scRidA[0]) && table.rowWithPid(scRidA[1])){
                                            for(var ri in oRows){
                                                if(oRows[ri].pid==scRidA[0]){
                                                    var srmatch=true;
                                                }
                                                if(srmatch){
                                                    rrows[ri]=oRows[ri];
                                                }
                                                if(oRows[ri].pid==scRidA[1]){
                                                    if(!srmatch){
                                                        break;
                                                    }else{
                                                        srmatch=false;
                                                    }
                                                }
                                            }
                                        }else if(scRid[0]=='/'){
                                            var rMRegExp=new RegExp(scRid);
                                            for(var ri in oRows){
                                                if(rMRegExp.test(oRows[ri].pid)){
                                                    rrows[ri]=oRows[ri];
                                                }
                                            }
                                        }else if(scRid=='*'){
                                            for(var ri in oRows){
                                                rrows[ri]=oRows[ri];
                                            }
                                            for(var ri in nRows){
                                                rrows[nRows[ri].id]=nRows[ri];
                                            }
                                        }else if(scRid!=''){
                                            var tr=table.rowWithPid(scRid);
                                            if(tr){
                                                rrows[tr.id]=tr;
                                            }
                                            rRanged=false;
                                        }else if(scRid==''){
                                            var tr=cell.parentElement;
                                            rrows[tr.id]=tr;
                                            rRanged=false;
                                        }
                                    }else{
                                        var rrows=[cell.parentElement];
                                        rRanged=false;
                                    }
                                    var vars=[];
                                    var rcount=0;
                                    for(var rrid in rrows){
                                        rcount++;
                                        var row=rrows[rrid];
                                        if(scCidA.length>1 && row.cells[scCidA[0]] && row.cells[scCidA[1]]){
                                            var oCells=row.cells;
                                            for(var ci=0;ci<oCells.length;ci++){
                                                if(oCells[ci].id==scCidA[0]){
                                                    var scmatch=true;
                                                }
                                                if(scmatch){
                                                    rcells[rrid+oCells[ci].id]=oCells[ci];
                                                }
                                                if(oCells[ci].id==scCidA[1]){
                                                    if(!scmatch){
                                                        break;
                                                    }else{
                                                        scmatch=false;
                                                    }
                                                }
                                            }
                                        }else{
                                            if(scCidA[0]=='index'){
                                                rcells[rrid+cell.id]=cell;
                                            }else{
                                                rcells[rrid+scCidA[0]]=row.cells[scCidA[0]]
                                            }
                                            cRanged=false;
                                        }
                                        q++;
                                    }
                                    if(rcount==0){
                                        statusField.innerHTML="No row specified by "+cA+", skipping update of "+cell.id+"("+(cell.parentElement.pid||cell.parentElement.id)+")("+cell.parentElement.parentElement.parentElement.tableName+")";
                                        return false;
                                    }
                                    var ccount=0;
                                    for(var rcid in rcells){
                                        var sc=rcells[rcid];
                                        if(sc){
                                            ccount++;
                                            var val=sc.children['inputCell']?sc.children['inputCell'].value:sc.textContent;
                                            try{
                                                val=aUVO[tid][sc.parentElement.id][sc.id]['innerHTML']!=undefined?aUVO[tid][sc.parentElement.id][sc.id]['innerHTML']:val
                                            }catch(e){}
                                            var kval=parseFloat(val);
                                            kval=kval.toString()!=val?val:kval;
                                            val=kval==''?0:kval;
                                            if(rRanged||cRanged){
                                                rVar.push(val);
                                            }else if(cRanged){
                                                rVar=rVar||[];
                                                rVar[p]=val;
                                            }else{
                                                rVar=val;
                                            }
                                            if(sc==cell){
                                                if(fObj.eVariable){
                                                    vars.pop();
                                                    vars.push(fObj.eVariable);
                                                    delete fObj.variables["var"+k];
                                                }
                                                else{
                                                    fObj.eVariable="var"+k;
                                                }
                                            }
                                        }
                                        p++;
                                    }
                                    if(ccount==0){
                                        statusField.innerHTML="No cell specified by "+cA+", skipping update of "+cell.id+"("+(cell.parentElement.pid||cell.parentElement.id)+")("+cell.parentElement.parentElement.parentElement.tableName+")";
                                        return false;
                                    }
                                    vars.push("var"+k);
                                    fObj.variables["var"+k]=rVar;
                                    if(vars.length>0){
                                        var varss=vars.join(",");
                                        index=index+(varss.length-cA.length);
                                        var re=new RegExp(cA.replace(/\(/g,'\\(').replace(/\)/g,'\\)').replace(/\*/g,'\\*'),'g');
                                        formula=formula.replace(re,varss);
                                    }
                                    k++;
                                }
                                index=formula.indexOf(sc.id,index);
                            }
                        }
                    }
                    formula=dbTableExecuter.putStringsInFormula(formula,sCodeObj);
                    fObj.oCellProps=fObj.oCellProps||{};
                    if(formula.trim()!=""){
                        formula=dbTableExecuter.splitFormula(formula);
                        for(fi=0;fi<formula.length;fi++){
                            if(propArr[fi]==''){
                                propArr[fi]='innerHTML';
                                if(formula[fi]==''){
                                    formula[fi]=cell.textContent;
                                }
                            }
                            fObj.cellProps[propArr[fi]]=formula[fi];
                            for(var nfi in fObj.oCellProps){
                                if(fObj.oCellProps[nfi]==propArr[fi]){
                                    delete fObj.oCellProps[nfi];
                                }
                            }
                            fObj.oCellProps[fi]=propArr[fi];
                        }
                    }
                    dbTableExecuter.sandbox.f++;
                    dbTableExecuter.sandbox.sUVAs.splice(0,0,sUVA);
                    dbTableExecuter.sandbox.cells.splice(0,0,cell);
                    dbTableExecuter.sandbox.postMessage(JSON.stringify(fObj));
                }else{
                    return "breakCellUpdate";
                }
            }
        }else{
            if(!(cell.sKey || dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.authorization=="*")) {
                statusField.innerHTML="U r not authorized to edit the cell ~%)~";
                if(cell.children['inputCell']){
                    cell.innerHTML=cell.preValue?cell.preValue:'';
                }
            }
        }
    },
    receiveComputedData:function(event){
        var fObj=JSON.parse(event.data);
        var cell=dbTableExecuter.sandbox.cells.pop();
        var rid=cell.parentElement.id;
        var cid=cell.id;
        var aUVO=dbTableExecuter.sandbox.aUVO;
        var sUVA=dbTableExecuter.sandbox.sUVAs.pop();
        var tid=cell.parentElement.parentElement.parentElement.tableName;
        if(!fObj.error){
            sUVA.push(cell);
            var cellValue=cell.children['inputCell']?cell.preValue:cell.textContent;
            cellValue=cellValue==""?0:cellValue;
            cellValue=parseInt(cellValue).toString().length==cellValue.toString().length?parseInt(cellValue):cellValue;
            var cCellValue=fObj.cellProps.innerHTML===undefined?cellValue:fObj.cellProps.innerHTML===null?fObj.cellProps.innerHTML='':fObj.cellProps.innerHTML;
            cCellValue=parseInt(cCellValue).toString().length==cCellValue.toString().length?parseInt(cCellValue):cCellValue;
            if(cellValue!=cCellValue){
                aUVO[tid]=aUVO[tid]||{};
                aUVO[tid][rid]=aUVO[tid][rid]||{};
                aUVO[tid][rid][cid]=aUVO[tid][rid][cid]||{};
                for(var prop in fObj.cellProps){
                    if(prop!='gProp'){
                        aUVO[tid][rid][cid][prop]=fObj.cellProps[prop];
                    }
                }
                aUVO[tid][rid][cid]['sKey']=cell.sKey;
                var dTable=dbTableExecuter.tables[tid].dTable;
            }else{
                for(var pid in fObj.cellProps){
                    if(pid!='innerHTML'&&pid!='gProp'){
                        var cprop=cell.style[pid];
                        cell.style[pid]=fObj.cellProps[pid];
                        if(cell.style[pid]!=cprop){
                            var updprops=true;
                            cell.style[pid]=cprop;
                        }
                    }
                }
                if(updprops){
                    for(pid in fObj.cellProps){
                        aUVO[tid]=aUVO[tid]||{};
                        aUVO[tid][rid]=aUVO[tid][rid]||{};
                        aUVO[tid][rid][cid]=aUVO[tid][rid][cid]||{};
                        aUVO[tid][rid][cid][pid]=fObj.cellProps[pid];
                    }
                }
                cell.innerHTML=cellValue;
            }
            var dc=dbTableExecuter.orderDCs(cell);
            if(dc && dc.tagName=='TD'){
                var tHR=dc.parentElement.parentElement.parentElement.rowWithPid("");
                if(tHR.cells['index'].formula || dc.formula!='' || tHR.cells[dc.id].formula!='' || dc.parentElement.cells['index'].formula!=''){
                    var formula='='+(tHR.cells['index']?tHR.cells['index'].formula:'')+';='+(dc.parentElement.cells['index']?dc.parentElement.cells['index'].formula:'')+";="+tHR.cells[dc.id].formula+";="+dc.formula;
                    dbTableExecuter.updateByFormula(dc,formula,sUVA);
                }else{
                    aUVO[tid]=aUVO[tid]||{}
                    aUVO[tid][rid]=aUVO[tid][rid]||{};
                    aUVO[tid][rid][cid]=aUVO[tid][rid][cid]||{};
                    for(var prop in fObj.cellProps){
                        aUVO[tid][rid][cid][prop]=fObj.cellProps[prop];
                        aUVO[tid][rid][cid]['sKey']=cell.sKey;
                    }
                }
            }
            dbTableExecuter.sandbox.f--;
            if(dbTableExecuter.sandbox.f==0 && core.objPropCount(dbTableExecuter.cellsToBeUpdated)==0){
                dbTableExecuter.sendUpdateReq();
            }
        }
        else{
            dbTableExecuter.sandbox.f--;
            statusField.innerHTML="While updating ("+cell.id+","+cell.parentElement.id+") error:"+fObj.error;
            if(!dbTableExecuter.sandbox.f){
                delete dbTableExecuter.sandbox.updateFormulaOf;
            }
        }
    },
    sendUpdateReq:function(){
        if(!dbTableExecuter.sandbox.updateFormulaOf){
            var sAUVO=JSON.stringify(dbTableExecuter.sandbox.aUVO,core.jsonReplacer);
            dbTableExecuter.sandbox.aUVO={};
            dbTableExecuter.sandbox.dCs=[];
            if(sAUVO!='{}'){
                statusField.innerHTML="Updating table...~:)~";
                var feed={
                    content:{
                        tableOperation:'updateCell',
                        dbTable:dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.tableName,
                        sAUVO:sAUVO
                    }
                };
                feed.ferry=new core.shuttle('lib/superScripts/dbTableExecuter.php',feed.content,dbTableExecuter.fixCell,feed);
            }
        }else{
            if(dbTableExecuter.sandbox.updateFormulaOf.srcNotFetchedCells==0){
                var dbTable=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.tableName;
                dbTableExecuter.sandbox.aUVO[dbTable]=dbTableExecuter.sandbox.aUVO[dbTable]||{};
                var sAUVO=JSON.stringify(dbTableExecuter.sandbox.aUVO,core.jsonReplacer);
                dbTableExecuter.sandbox.aUVO={};
                dbTableExecuter.sandbox.dCs=[];
                var feed={
                    content:{
                        dbTable:dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.tableName,
                        tableOperation:'updateFormula$,$updateCell',
                        sAUVO:sAUVO,
                        formula:dbTableExecuter.sandbox.updateFormulaOf.formula.replace(/'/g,"~~").replace(/"/g,'@@'),
                        fColIndex:dbTableExecuter.sandbox.updateFormulaOf.cell.id,
                        fRowIndex:dbTableExecuter.sandbox.updateFormulaOf.cell.parentElement.id,
                        fsKey:dbTableExecuter.sandbox.updateFormulaOf.cell.sKey
                    },
                    postExpedition:function(feed){
                        if(feed.responseXML.getElementsByTagName('updateFormula')[0].getElementsByTagName('status')[0].textContent=='success'){
                            var cell=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rowWithId(feed.content.fRowIndex).cells[feed.content.fColIndex];
                            var rpid=cell.parentElement.pid;
                            cell.formula=feed.content.formula.replace(/~~/g,"'").replace(/@@/g,'"');
                            var pSCs=cell.sCs;
                            var sciriti=feed.content.fColIndex+","+(feed.content.fRowIndex=='tHR'?"":rpid)+","+dbTableExecuter.frontTable;
                            var sciri=feed.content.fColIndex+","+(feed.content.fRowIndex=='tHR'?"":rpid);
                            if(pSCs){
                                for(var i=0;i<pSCs.length;i++){
                                    var pSC=pSCs[i].split(",");
                                    pSC[2]=pSC[2]?pSC[2]:dbTableExecuter.frontTable;
                                    var prow=dbTableExecuter.tables[pSC[2]].dTable.table.rowWithPid(pSC[1]);
                                    if(prow){
                                        var pcell=prow.cells[pSC[0]];
                                        if(pcell){
                                            for(var j=0;j<pcell.ee.length;j++){
                                                if(pcell.ee[j]==sciriti || pcell.ee[j]==sciri){
                                                    delete pcell.ee[j];
                                                    pcell.ee=pcell.ee.sort().filter(function(el,i,a){
                                                        if(i==a.indexOf(el))return 1;
                                                        return 0
                                                    });
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            var sCs=feed.responseXML.getElementsByTagName('sCs')[0].textContent.split('@,$');
                            cell.sCs=sCs;
                            for (var i = 0; i < sCs.length; i++) {
                                if(sCs[i]!=''){
                                    var c = sCs[i].split(",");
                                    var tn=c[2]?c[2]:dbTableExecuter.frontTable;
                                    var ar=dbTableExecuter.tables[tn].dTable.table.rowWithPid(c[1]);
                                    if(ar){
                                        var a=dbTableExecuter.tables[tn].dTable.table.rowWithPid(c[1]).cells[c[0]]['ee'];
                                        a.push(tn==dbTableExecuter.frontTable?sciri:sciriti);
                                        a = a.sort().filter(function(el,i,a){
                                            if(i==a.indexOf(el))return 1;
                                            return 0
                                        });
                                    }
                                }
                            }
                            statusField.innerHTML='Formula updated ~:)~';
                        }else{
                            statusField.innerHTML=feed.responseXML.getElementsByTagName('updateFormula')[0].getElementsByTagName('status')[0].textContent;
                        }
                        if(feed.responseXML.getElementsByTagName('updateCell')[0].getElementsByTagName('status')[0].textContent=='success'){
                            dbTableExecuter.fixCell(feed);
                        }
                    }
                }
                dbTableExecuter.sandbox.updateFormulaOf=null;
                feed.ferry=new core.shuttle("/lib/superScripts/dbTableExecuter.php", feed.content, feed.postExpedition, feed);
            }
        }
    },
    orderDCs:function(cell){
        var tid=cell.parentElement.parentElement.parentElement.tableName;
        var dTable=dbTableExecuter.tables[tid].dTable;
        var dCa=[];
        var dCs=cell.ee || [];
        var tHR=dTable.tHR;
        dCs=tHR.cells[cell.id].ee?dCs.concat(tHR.cells[cell.id].ee):dCs;
        dCs=cell.parentElement.cells['index'].ee?dCs.concat(cell.parentElement.cells['index'].ee):dCs;
        dCs=tHR.cells['index'].ee?dCs.concat(tHR.cells['index'].ee):dCs;
        dCs=dCs.filter(function(el,i,a){
            if(i==a.indexOf(el))return 1;
            return 0
        });
        var tDCs=[];
        for(var i=0;i<dCs.length;i++){
            var dc=dCs[i].split(',');
            if(!dc[2]){
                if(dc[1]==''){
                    dc=cell.parentElement.cells[dc[0]=='index'?cell.id:dc[0]];
                }else{
                    var dcr=dTable.table.rowWithPid(dc[1]);
                    tHR=dbTableExecuter.tables[tid].dTable.tHR;
                    if(dcr){
                        dc=dc[0]=='index'?dcr.cells[cell.id]:dcr.cells[dc[0]];
                    }
                }
            }else if(dbTableExecuter.tables[dc[2]] || tid==dbTableExecuter.frontTable){
                if(dbTableExecuter.tables[dc[2]]){
                    tHR=dbTableExecuter.tables[dc[2]].dTable.tHR;
                    var dcr=dbTableExecuter.tables[dc[2]].dTable.table.rowWithPid(dc[1]==''?cell.parentElement.pid:dc[1]);
                    if(dcr){
                        dc=dc[0]=='index'?dcr.cells[cell.id]:dcr.cells[dc[0]];
                    }
                }else{
                    dbTableExecuter.cellsToBeUpdated[dc[2]]=dbTableExecuter.cellsToBeUpdated[dc[2]]||{};
                    dbTableExecuter.cellsToBeUpdated[dc[2]][(dc[1]=(dc[1]?dc[1]:cell.parentElement.pid))]=dbTableExecuter.cellsToBeUpdated[dc[2]][dc[1]]||{};
                    var cRTO=dbTableExecuter.cellsToBeUpdated[dc[2]][dc[1]][(dc[0]=(dc[0]=='index'?cell.id:dc[0]))]=dbTableExecuter.cellsToBeUpdated[dc[2]][dc[1]][dc[0]] || {
                        reqTab:[tid]
                    };
                    cRTO.reqTab=cRTO.reqTab.filter(function(el,i,a){
                        if(i==a.indexOf(el))return 1;
                        return 0
                    });
                    dbTableExecuter.fetchTable(dc[2]);
                }
            }
            if(dc && dc.tagName=='TD'){
                var match=false;
                if(dbTableExecuter.sandbox.dCs){
                    for(var j=0;j<dbTableExecuter.sandbox.dCs.length;j++){
                        if(dc==dbTableExecuter.sandbox.dCs[j]){
                            match=true;
                            break;
                        }
                    }
                }
                if(!match)dCa.push(dc);
            }
        }
        dCa=dbTableExecuter.sandbox.dCs?dCa.concat(dbTableExecuter.sandbox.dCs):dCa;
        for(var i=0;i<dCa.length;i++){
            var dc=dCa[i];
            var match=false;
            var tid=dc.parentElement.parentElement.parentElement.tableName;
            var dTable=dbTableExecuter.tables[tid].dTable;
            var tHR=dTable.tHR;
            var dcSCs=dc.sCs||[];
            dcSCs=dc.parentElement.cells['index'].sCs?dcSCs.concat(dc.parentElement.cells['index']):dcSCs;
            dcSCs=tHR.cells[dc.id].sCs?dcSCs.concat(tHR.cells[dc.id].sCs):dcSCs;
            dcSCs=tHR.cells['index'].sCs?dcSCs.concat(tHR.cells['index'].sCs):dcSCs;
            var dct=dc.parentElement.parentElement.parentElement.tableName;
            for(var j=i+1;j<dCa.length;j++){
                var dc2=dCa[j];
                var sciri=dc2.id+","+(dc2.parentElement.pid?dc2.parentElement.pid:'');
                var sciriti=sciri+","+dc2.parentElement.parentElement.parentElement.tableName;
                for(var k=0;k<dcSCs.length;k++){
                    if(dcSCs[k]+","+dct==sciriti || dcSCs[i]==sciriti){
                        var match=true;
                        var apd=false;
                        if(dc2.mSCs){
                            for(var l=0;l<dc2.mSCs.length;l++){
                                if(dc2.mSCs[l]==dc){
                                    apd=true;
                                    break;
                                }
                            }
                        }
                        if(!apd){
                            dCa.splice(j+1, 0, dc);
                            dc2.mSCs=dc2.mSCs||[];
                            dc2.mSCs.push(dc);
                            delete dCa[i];
                        }
                        break;
                    }
                }
                if(match)break;
            }
        }
        var fdCa=[];
        dc=null;
        for(var i=0;i<dCa.length;i++){
            if(dCa[i]){
                //delete dc if already bean listed in update list of source cell
                if(dCa[i]&& !dc){
                    delete dCa[i].mSCs;
                    dc=dCa[i];
                }else if(dCa[i]){
                    delete dCa[i].mSCs;
                    fdCa.push(dCa[i]);
                }
            }
        }
        dbTableExecuter.sandbox.dCs=fdCa;
        return dc;
    },
    cToS:function(sUVA){
        for(var i=0;i<sUVA.length;i++){
            sUVA[i]=sUVA[i].id+","+sUVA[i].parentElement.id
        }
    },
    sToC:function(sUVA){
        for(var i=0;i<sUVA.length;i++){
            var ca=sUVA[i].split(',');
            sUVA[i]=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.rowWithId(ca[1]).cells[ca[0]]
        }
    },
    printTable:function(){
        var newWin=window.open("");
        newWin.document.body.appendChild(dbTableExecuterTool.css.cloneNode(true));
        var table=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.cloneNode(true);
        var tHR=table.getElementsByTagName('thead')[0].children['tHR'];
        var cc=tHR.cells.length;
        for(var i=0;i<cc;i++){
            var cell=tHR.cells[i];
            if(!cell.id){
                cell.parentElement.removeChild(cell);
                i--;
                cc--;
            }else if(cell.id=='index'){
                cell.parentElement.removeChild(cell);
                i--;
                cc--;
            }else{
                cell.innerHTML=cell.textContent;
            }
        }
        var rows=table.getElementsByTagName('tbody')[0].children;
        var nvr=rows['newVRow'];
        if(nvr)nvr.parentElement.removeChild(nvr);
        var nr=rows['newRow'];
        if(nr)nr.parentElement.removeChild(nr);
        for(i=0;i<rows.length;i++){
            var row=rows[i];
            if(row){
                var iCell=row.cells['index'];
                if(iCell)iCell.parentElement.removeChild(iCell);
            }
        }
        newWin.document.body.appendChild(table);
        var printBtn=document.createElement('button');
        printBtn.innerHTML="Print";
        printBtn.window=newWin.window
        printBtn.onclick=function(){
            this.style.display='none';
            this.window.print();
            this.window.close();
        }
        newWin.document.body.appendChild(printBtn);
    },
    fetchTable:function(tn,force){
        var fTQ=dbTableExecuter.fetchTable.fTQ=dbTableExecuter.fetchTable.fTQ||[];
        var fetchInProcess=false;
        for(var i=0; i<fTQ.length; i++){
            if(fTQ[i]==tn){
                fetchInProcess=true;
            }
        }
        if((!fetchInProcess && !dbTableExecuter.tables[tn]) || force){
            if(!force)fTQ.push(tn);
            var feed={
                content:{
                    query:tn,
                    rawOpen:true
                },
                postExpedition:function(feed){
                    dbTableExecuter.fetchTable.updated=false;
                    var fTQ=dbTableExecuter.fetchTable.fTQ;
                    var nFTQ=[];
                    for(var i=0;i<fTQ.length;i++){
                        if(fTQ[i]!=feed.content.query){
                            nFTQ.push(fTQ[i]);
                        }
                    }
                    dbTableExecuter.fetchTable.fTQ=nFTQ;
                    var apHolder=document.createElement('div');
                    apHolder.style.display='none';
                    apHolder.innerHTML=feed.responseText;
                    var gdgBdy=core.cloneNode(apHolder.getElementsByClassName('gdgBody')[0]);
                    var scs=gdgBdy.getElementsByTagName('script');
                    var j=0;
                    var ascs=[];
                    for(var i=0;i<scs.length;i++){
                        ascs.push(scs[i]);
                    }
                    while(j<ascs.length){
                        scs[j].parentElement.replaceChild(core.cloneNode(scs[j]),scs[j]);
                        j++;
                    }
                    document.body.appendChild(gdgBdy);
                    document.body.removeChild(gdgBdy);
                    var eEA=[];
                    if(dbTableExecuter.tables[feed.content.query] && feed.content.query!=dbTableExecuter.frontTable){
                        if(dbTableExecuter.cellsToBeUpdated && dbTableExecuter.cellsToBeUpdated[feed.content.query]){
                            var ctbuott=dbTableExecuter.cellsToBeUpdated[feed.content.query];
                            var table=dbTableExecuter.tables[feed.content.query].dTable.table;
                            for(var rid in ctbuott){
                                var aRow=ctbuott[rid];
                                for(var cid in aRow){
                                    var aCell=aRow[cid];
                                    var tDR=true;
                                    for(var i in aCell.reqTab){
                                        if(!dbTableExecuter.tables[aCell.reqTab[i]]){
                                            tDR=false;
                                            break;
                                        }
                                    }
                                    if(tDR){
                                        if(aCell.postFunction){
                                            aCell.postFunction();
                                        }else if(aCell.formula){
                                            dbTableExecuter.updateByFormula(table.rowWithId(rid).cells[cid], aCell.formula, []);
                                            dbTableExecuter.fetchTable.updated=true;
                                        }else{
                                            var tHR=table.rowWithId('tHR');
                                            var row=table.rowWithPid(rid);
                                            if(row){
                                                var dc=row.cells[cid];
                                                if(dc){
                                                    var cAE=false;
                                                    for(var k=0;k<eEA.length;k++){
                                                        if(dc==eEA[k]){
                                                            cAE=true;
                                                            break;
                                                        }
                                                    }
                                                    if(!cAE){
                                                        dbTableExecuter.fetchAllEffectiveElements(dc,eEA);
                                                        var formula='='+(tHR.cells['index']?tHR.cells['index'].formula:'')+';='+(dc.parentElement.cells['index']?dc.parentElement.cells['index'].formula:'')+";="+tHR.cells[dc.id].formula+";="+dc.formula;
                                                        dbTableExecuter.updateByFormula(dc, formula, []);
                                                        dbTableExecuter.fetchTable.updated=true;
                                                    }
                                                }
                                            }
                                        }
                                        delete aRow[cid];
                                        if(core.objPropCount(aRow)==0){
                                            delete ctbuott[rid];
                                        }
                                    }
                                }
                                if(core.objPropCount(ctbuott)==0){
                                    delete dbTableExecuter.cellsToBeUpdated[feed.content.query];
                                }
                            }
                        }
                    }else{
                        statusField.innerHTML="Unable to open "+feed.content.query+" while resolving table dependency. May be U are not previlaged or table doesn't exist ~:|~";
                    }
                    if(dbTableExecuter.cellsToBeUpdated){
                        var ctbuott=dbTableExecuter.cellsToBeUpdated[feed.content.query];
                        if(ctbuott){
                            for(var rid in ctbuott){
                                var aRow=ctbuott[rid];
                                for(var cid in aRow){
                                    delete aRow[cid];
                                    if(core.objPropCount(aRow)==0){
                                        delete ctbuott[rid];
                                    }
                                }
                            }
                            if(core.objPropCount(ctbuott)==0){
                                delete dbTableExecuter.cellsToBeUpdated[feed.content.query];
                            }
                        }
                        var ctbuott=dbTableExecuter.cellsToBeUpdated[dbTableExecuter.frontTable];
                        var table=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table;
                        for(var rid in ctbuott){
                            var aRow=ctbuott[rid];
                            for(var cid in aRow){
                                var aCell=aRow[cid];
                                var tDR=true;
                                for(var i in aCell.reqTab){
                                    if(!dbTableExecuter.tables[aCell.reqTab[i]]){
                                        tDR=false;
                                        break;
                                    }
                                }
                                if(tDR){
                                    if(aCell.postFunction){
                                        aCell.postFunction();
                                    }
                                    else if(aCell.formula){
                                        dbTableExecuter.updateByFormula(table.rowWithId(rid).cells[cid], aCell.formula, [])
                                        dbTableExecuter.fetchTable.updated=true;
                                    }else{
                                        var tHR=table.rowWithId('tHR');
                                        var dc=table.rowWithId(rid).cells[cid];
                                        var formula='='+(tHR.cells['index']?tHR.cells['index'].formula:'')+';='+(dc.parentElement.cells['index']?dc.parentElement.cells['index'].formula:'')+";="+tHR.cells[dc.id].formula+";="+dc.formula;
                                        dbTableExecuter.updateByFormula(dc, formula, []);
                                        dbTableExecuter.fetchTable.updated=true;
                                    }
                                    delete aRow[cid];
                                    if(core.objPropCount(aRow)==0){
                                        delete ctbuott[rid];
                                    }
                                }
                            }
                            if(core.objPropCount(ctbuott)==0){
                                delete dbTableExecuter.cellsToBeUpdated[dbTableExecuter.frontTable];
                            }
                        }
                    }
                    if(dbTableExecuter.sandbox.f==0 && core.objPropCount(dbTableExecuter.cellsToBeUpdated)==0 && !dbTableExecuter.fetchTable.updated){
                        dbTableExecuter.sendUpdateReq();
                    }
                }
            }
            feed.ferry=new core.shuttle("/lib/superScripts/dbTableExecuterForm.php",null,feed.postExpedition,feed);
            dbTableExecuter.fetchTable.tfc=dbTableExecuter.fetchTable.tfc?dbTableExecuter.fetchTable.tfc+1:1;
        }
    },
    clearTablesOnSignOut:function(){
        delete dbTableExecuter.tables;
        delete dbTableExecuter.frontTable;
    },
    liveUpdate:function(dbtUpdate){
        for(var tid in dbtUpdate['tables']){
            var tbd=dbtUpdate['tables'][tid];
            var t=dbTableExecuter.tables[tid];
            if(t){
                var breakUpd=false;
                var notByFullAuthority=tbd['notByFullAuthority'];
                for(var oid in tbd['op']){
                    if (oid == 'delRow') {
                        for(var rid in tbd['op']['delRow']){
                            var row=t.dTable.table.oRows[rid];
                            if(row) row.parentElement.removeChild(row);
                            delete t.dTable.table.oRows[rid];
                        }
                    }else if (oid == 'updateFormula') {
                        
                    }else{
                        if(tid==dbTableExecuter.frontTable){
                            dbTableExecuterTool.tableNameInputField.value=tid;
                            dbTableExecuterTool.loadGadget.apply(dbTableExecuterTool,[true]);
                        }else{
                            dbTableExecuter.fetchTable(tid,true);
                        }
                        breakUpd=true;
                    }/*if (oid == 'renametableName') {
                var newName = tbd['op']['renametableName']['newName'];
                    dbTableExecuter.tables[newName]=t;
                    dbTableExecuter.tables[newName].facade.children['ttArea'].children['tableName'].innerHTML=newName;
                    delete dbTableExecuter.tables[tid];
                }else if (oid == 'delTable') {
                if (tbd['op']['oid']) {
                        if(t.facade.parentElement){
                            t.facade.parentElement.removeChild(t.facade);
                        }
                        delete dbTableExecuter.table[tid];
                        statusField.innerHTML="Table "+tid+" has been deleted ~:|~";
                    }
                } else if (oid == 'renamecolName') {
                for(var colName in tbd['op']['renamecolName']){
                        var newName = tbd['op']['renamecolName'][colName];
                        var row=t.dTable.table.rowWithId('tHR');
                        row.cells[colName].id=newName;
                        row.cells[newName]['Type'] = tbd['cells']['tHR'][newName]['Type'];
                        var size=row.cells[newName]['Size'] = tbd['cells']['tHR'][newName]['Size'];
                        row.cells[newName]['Null'] = tbd['cells']['tHR'][newName]['Null'];
                        row.cells[newName]['Default'] = tbd['cells']['tHR'][newName]['Default'];
                        row.cells[newName]['MaxLength']=size>20?20:size;
                        for(var rid in t.dTable.table.oRows){
                            row=t.dTable.table.oRows[rid];
                            row.cells[colName].id=newName;
                        }
                    }
                } else if (oid == 'delRow') {
                    for(var rid in tbd['op']['delRow']){
                        var row=t.dTable.table.oRows[rid];
                        if(row) row.parentElement.removeChild(row);
                        delete t.dTable.table.oRows[rid];
                    }
                }else if (oid == 'delColumn') {
                var columnName = tbd['op']['delColumn']['columnName'];
                    var row=t.dTable.tHR;
                    var col=t.dTable.tHR.cells[columnName];
                    col.parentElement.removeChild(col);
                    var rows=t.dTable.table.oRows;
                    for (var rid in rows) {
                        col=rows[rid].cells[columnName];
                        col.parentElement.removeChild(col);
                    }
                } else if (oid=='insColumn'){
                for(var ncid in tbd['op']['insColumn']){
                        var after=tbd['op']['insColumn'][ncid]['after'];
                        after=t.dTable.tHR.cells[after];
                        var th=document.createElement('th');
                        th.id=ncid;
                        for(var pid in tbd['cells']['tHR'][ncid]){
                            th[pid]=tbd['cells']['tHR'][ncid][pid];
                        }
                        after.insertAdjacentElement('afterEnd',th);
                        var rows=t.dTable.table.oRows;
                        for(var rid in rows){
                            after=rows[rid].cells[after.id];
                            var td=document.createElement('td');
                            td.id=ncid;
                            after.insertAdjacentElement('afterEnd',td);
                        }
                    }
                }*/
                }
                if(!breakUpd){
                    for(var rid in tbd['cells']){
                        var urow=tbd['cells'][rid];
                        var row=t.dTable.table.rowWithId(rid);
                        if(row){
                            for(var cid in urow){
                                var ucell=urow[cid];
                                var cell=row.cells[cid];
                                if(cell){
                                    for(var pid in ucell){
                                        try{
                                            if(pid=='innerHTML'){
                                                if(notByFullAuthority){
                                                    dbTableExecuter.updateByFormula(cell,"='"+ucell[pid]+"'",[]);
                                                }else{
                                                    cell.innerHTML=ucell[pid];
                                                }
                                                if(cid==t.dTable.table.priKey){
                                                    cell.parentElement.pid=cell.textContent;
                                                }
                                            }else if(pid=='style'){
                                                for(var sid in ucell[pid]){
                                                    cell[pid][sid]=ucell[pid][sid];
                                                }
                                            }else if(pid=="f"){
                                                if(ucell['f']['f']!=undefined)cell.formula=ucell['f']['f'];
                                                if(ucell['f']['ee']!=undefined)cell.ee=ucell['f']['ee'];
                                                if(ucell['f']['sCs']!=undefined)cell.sCs=ucell['f']['sCs'];
                                            }else{
                                                cell[pid]=ucell[pid]
                                            }
                                        }catch(e){
                                            statusField.innerHTML=e.message;
                                        }
                                    }
                                }else{
                                    statusField.innerHTML="Invalid cell reference in the update ~:s~";
                                }
                            }
                        }else{
                            row=document.createElement('tr');
                            var tHR=dbTableExecuter.tables[tid].dTable.tHR;
                            row.id=rid;
                            for(var i=1;i<tHR.cells.length-1;i++){
                                var cell=document.createElement('td');
                                row.appendChild(cell);
                                cell.id=tHR.cells[i].id;
                                if(urow[cell.id]){
                                    var ucell=urow[cell.id];
                                    for(var pid in ucell){
                                        if(pid=='innerHTML'){
                                            cell.innerHTML=core.htmlEncode(ucell.innerHTML);
                                            if(cell.id==t.dTable.table.priKey)row.pid=cell.innerHTML;
                                        }else if(pid=='sKey'){
                                            cell.sKey=ucell['sKey'];
                                        }else if(pid=="style"){
                                            for(var sid in ucell[pid]){
                                                cell[pid][sid]=ucell[pid][sid];
                                            }
                                        }else if(pid=="f"){
                                            if(ucell['f']['f']!=undefined)cell.formula=ucell['f']['f'];
                                            if(ucell['f']['ee']!=undefined)cell.ee=ucell['f']['ee'];
                                            if(ucell['f']['sCs']!=undefined)cell.sCs=ucell['f']['sCs']
                                        }else{
                                            cell[pid]=ucell[pid]
                                        }
                                    }
                                }
                            }
                            dbTableExecuter.appendRow(row,tid);
                        }
                    }
                }
            }
        }
    },
    fetchUpdate:window.dbTableExecuterFetchUpdate,
    fetchUpdateTriggerId:setTimeout(window.dbTableExecuterFetchUpdate,5000,false),
    shuttle:function(url,postExpedition,feed){
        var ferry=new core.shuttle(url, null, postExpedition, feed);
    },
    updateAllCells:function(ft){
        var tHR=dbTableExecuter.tables[ft].dTable.tHR;
        var rows=dbTableExecuter.tables[ft].dTable.table.oRows;
        var eEA=[];
        for(var rid in rows){
            var row=rows[rid];
            for(var j=0;j<row.cells.length;j++){
                var cell=row.cells[j];
                var cAE=false;
                for(var k=0;k<eEA.length;k++){
                    if(cell==eEA[k]){
                        cAE=true;
                        break;
                    }
                }
                if(!cAE){
                    if(cell.id!='index'){
                        var formula='='+(tHR.cells['index']?tHR.cells['index'].formula:'')+';='+(cell.parentElement.cells['index']?cell.parentElement.cells['index'].formula:'')+';='+tHR.cells[cell.id].formula+';='+cell.formula;
                        if(formula!='=;=;=;='){
                            dbTableExecuter.fetchAllEffectiveElements(cell,eEA);
                            if(dbTableExecuter.updateByFormula(cell,formula,[])=='breakCellUpdate'){
                                var pf=dbTableExecuter.cellsToBeUpdated[ft][rid][cell.id].postFunction=function(){
                                    dbTableExecuter.updateAllCells(arguments.callee.ft);
                                }
                                pf.ft=ft;
                                var breakOp=true;
                            }
                        }
                    }
                    if(breakOp){
                        break;
                    }
                }
            }
            if(breakOp){
                break;
            }
        }
    },
    fetchAllEffectiveElements:function(cell,eEA){
        var tid=cell.parentElement.parentElement.parentElement.tableName;
        var dTable=dbTableExecuter.tables[tid].dTable;
        var dCs=[].concat(cell.ee);
        var tHR=dTable.tHR;
        dCs=tHR.cells[cell.id].ee?dCs.concat(tHR.cells[cell.id].ee):dCs;
        dCs=cell.parentElement.cells['index'].ee?dCs.concat(cell.parentElement.cells['index'].ee):dCs;
        dCs=dCs.concat(tHR.cells['index'].ee);
        dCs=dCs.filter(function(el,i,a){
            if(i==a.indexOf(el))return 1;
            return 0;
        });
        var tDCs=[];
        for(var i=0;i<dCs.length;i++){
            var dc=dCs[i].split(',');
            if(!dc[2]){
                if(dc[1]==''){
                    dc=cell.parentElement.cells[dc[0]=='index'?cell.id:dc[0]];
                }else{
                    var dcr=dTable.table.rowWithPid(dc[1]);
                    if(dcr){
                        tHR=dbTableExecuter.tables[tid].dTable.tHR;
                        dc=dc[0]=='index'?cell:dcr.cells[dc[0]];
                    }
                }
            }else if(dc[2]==dbTableExecuter.frontTable){
                var dcr=dbTableExecuter.tables[dc[2]].dTable.table.rowWithPid(dc[1]==''?cell.parentElement.pid:dc[1]);
                if(dcr){
                    tHR=dbTableExecuter.tables[dc[2]].dTable.tHR;
                    dc=dc[0]=='index'?cell:dcr.cells[dc[0]];
                }
            }
            if(dc && dc.tagName=='TD'){
                var match=false;
                for(var j=0;j<eEA.length;j++){
                    if(dc==eEA[j]){
                        match=true;
                        break;
                    }
                }
                if(!match){
                    eEA.push(dc);
                    dbTableExecuter.fetchAllEffectiveElements(dc,eEA);
                }
            }
        }
    }
}
dbTableExecuter.cellsToBeUpdated=dbTableExecuter.cellsToBeUpdated||{};
core.onSignOutScripts['clearTablesOnSignOut']=dbTableExecuter.clearTablesOnSignOut;
