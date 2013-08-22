/* Author: satya gowtham kudupudi
 * Date: 2012-06-07 14:51:00
 */

dbTableExecuterTool.keyPressHandler=function(){
    if(window.event.charCode && this.selectionEnd==this.value.length){
        if(!this.activeHelper){
            if(window.dbTableExecuter && this.value.indexOf("??")>-1 || this.value[0]=='=' || this.value.indexOf('::')>-1){
                if(this.value.substring(this.value.length-5,this.value.length)=='Math.'){
                    this.activeHelper=this.mFHL;
                }else{
                    this.activeHelper=this.cNL;
                }
            }else{
                this.activeHelper=this.qlas;
            }
        }
        if(this.activeHelper){
            this.activeHelper.style.display='none';
            if(window.dbTableExecuter && this.value.indexOf("??")>-1 || this.value[0]=='=' || this.value.indexOf('::')>-1){
                if(this.value.substring(this.value.length-5,this.value.length)=='Math.' || (this.activeHelper==this.mFHL && this.matchIndex!=undefined)){
                    this.activeHelper=this.mFHL;
                }else{
                    this.activeHelper=this.cNL;
                }
            }else{
                this.activeHelper=this.qlas;
                this.matchIndex=this.matchIndex==undefined?undefined:0;
            }
            if(this.activeHelper==this.cNL){
                var lis=this.cNL.children[dbTableExecuter.frontTable];
            }else{
                var lis=this.activeHelper;
            }
            if(this.matchIndex==undefined){
                var cc=lis.children.length;
                for(var i=0;i<cc;i++){
                    if(String.fromCharCode(window.event.charCode)== lis.children[i].firstChild.nodeValue[0]){
                        this.matchIndex=this.selectionStart;
                        var aElms=lis.getElementsByClassName('active');
                        while(aElms.length>0){
                            aElms[0].classList.remove('active');
                        }
                        lis.children[i].classList.add('active');
                        this.value=this.value.substring(0,this.matchIndex)+lis.children[i].firstChild.nodeValue
                        this.selectionStart=this.value.length-lis.children[i].firstChild.nodeValue.length+1;
                        this.selectionEnd=this.value.length;
                        this.blur();
                        this.focus();
                        this.activeHelper.style.display=null;
                        var aLI=lis.children[i];
                        lis.scrollTop=aLI.offsetTop+lis.scrollTop>64?aLI.offsetTop-64:lis.scrollTop;
                        return false;
                    }
                }
            }else{
                if(this.matchIndex>=this.value.length){
                    this.matchIndex=undefined;
                    dbTableExecuterTool.keyPressHandler.call(this);
                    return false;
                }else{
                    var cnam=this.value.substring(this.matchIndex,this.selectionStart)+String.fromCharCode(window.event.charCode);
                    var cc=lis.children.length;
                    var cnaml=cnam.length;
                    var match=false;
                    for(var i=0;i<cc;i++){
                        if(cnam== lis.children[i].firstChild.nodeValue.substring(0,cnaml)){
                            var aElms=lis.getElementsByClassName('active');
                            while(aElms.length>0){
                                aElms[0].classList.remove('active');
                            }
                            lis.children[i].classList.add('active');
                            var vl=this.selectionStart;
                            this.value=this.value.substring(0,this.matchIndex)+lis.children[i].firstChild.nodeValue;
                            this.selectionStart=vl+1;
                            this.selectionEnd=this.value.length;
                            this.blur();
                            this.focus();
                            this.activeHelper.style.display=null;
                            var aLI=lis.children[i];
                            lis.scrollTop=aLI.offsetTop+lis.scrollTop>64?aLI.offsetTop-64:lis.scrollTop;
                            match=true;
                            return false;
                        }
                    }
                    if(!match){
                        this.matchIndex=undefined;
                        if(dbTableExecuterTool.keyPressHandler.call(this)===false)return false;
                    }
                }
            }
        }
    }
}
dbTableExecuterTool.keyDownHandler=function(){
    if(window.event.keyCode==38){
        if((!this.activeHelper) && (this.value=='' || (this.selectionStart==0 && this.selectionEnd==this.value.length))){
            this.activeHelper=this.qlts;
        }else if(this.value.substring(this.value.length-5,this.value.length)=='Math.'){
            this.activeHelper=this.mFHL;
        }
        if(this.activeHelper){
            if(this.activeHelper==this.cNL){
                var lis=this.cNL.children[dbTableExecuter.frontTable];
            }else{
                var lis=this.activeHelper;
            }
            if(this.matchIndex!=undefined && this.selectionStart!=this.selectionEnd){
                var aLIS=lis.getElementsByClassName('active');
                var aLI=aLIS[0].previousSibling;
                if(aLI){
                    while(aLIS.length>0){
                        aLIS[0].classList.remove('active');
                    }
                    aLI.classList.add('active');
                    this.value=this.value.substring(0,this.matchIndex)+aLI.firstChild.nodeValue;
                    this.selectionStart=this.matchIndex;
                    this.selectionEnd=this.value.length;
                    this.blur();
                    this.focus();
                    this.activeHelper.style.display=null;
                    lis.scrollTop=aLI.offsetTop+lis.scrollTop>64?aLI.offsetTop-64:lis.scrollTop;
                }
            }else{
                this.activeHelper.style.display=null;
                var aLIS=lis.getElementsByClassName('active');
                while(aLIS.length>0){
                    aLIS[0].classList.remove('active');
                }
                var aLI=lis.children[0];
                if(aLI){
                    aLI.classList.add('active');
                    var ss=this.value.length
                    this.value=this.value+lis.children[0].firstChild.nodeValue
                    this.selectionStart=ss;
                    this.selectionEnd=this.value.length;
                    this.matchIndex=ss;
                    this.blur();
                    this.focus();
                    lis.scrollTop=aLI.offsetTop+lis.scrollTop>64?aLI.offsetTop-64:lis.scrollTop;
                }
            }
        }
        return false;
    }else if(window.event.keyCode==40){
        if((!this.activeHelper) && (this.value=='' || (this.selectionStart==0 && this.selectionEnd==this.value.length))){
            this.activeHelper=this.qlts;
        }else if(this.value.substring(this.value.length-5,this.value.length)=='Math.'){
            this.activeHelper=this.mFHL;
        }
        if(this.activeHelper){
            if(this.activeHelper==this.cNL){
                var lis=this.cNL.children[dbTableExecuter.frontTable];
            }else{
                var lis=this.activeHelper;
            }
            if(this.matchIndex!=undefined && this.selectionStart!=this.selectionEnd){
                var aLIS=lis.getElementsByClassName('active');
                var aLI=aLIS[0].nextSibling;
                if(aLI){
                    while(aLIS.length>0){
                        aLIS[0].classList.remove('active');
                    }
                    aLI.classList.add('active');
                    this.value=this.value.substring(0,this.matchIndex)+aLI.firstChild.nodeValue;
                    this.selectionStart=this.matchIndex;
                    this.selectionEnd=this.value.length;
                    this.blur();
                    this.focus();
                    this.activeHelper.style.display=null;
                    lis.scrollTop=aLI.offsetTop+lis.scrollTop>64?aLI.offsetTop-64:lis.scrollTop;
                }
            }else{
                this.activeHelper.style.display=null;
                var aLIS=lis.getElementsByClassName('active');
                while(aLIS.length>0){
                    aLIS[0].classList.remove('active');
                }
                var aLI=lis.children[0];
                if(aLI){
                    aLI.classList.add('active');
                    var ss=this.value.length
                    this.value=this.value+lis.children[0].firstChild.nodeValue
                    this.selectionStart=ss;
                    this.selectionEnd=this.value.length;
                    this.matchIndex=ss;
                    this.blur();
                    this.focus();
                    this.activeHelper.style.display=null;
                    lis.scrollTop=aLI.offsetTop+lis.scrollTop>64?aLI.offsetTop-64:lis.scrollTop;
                }
            }
        }
        return false;
    }else if(window.event.keyCode==27){
        if(this.activeHelper && this.activeHelper.offsetWidth>0){
            this.value=this.value.substr(0,this.selectionStart);
            this.activeHelper.style.display='none';
        }else{
            this.value='';
            this.activeHelper=undefined;
        }
        this.matchIndex=undefined;
    }else if(window.event.keyCode==39){
        if(this.selectionEnd==this.value.length && this.selectionStart!=this.value.length && this.activeHelper.offsetWidth>0){
            this.selectionStart++;
            return false;
        }
    }else if(window.event.keyCode==37){
        if(this.selectionEnd==this.value.length && this.selectionStart!=this.value.length && this.activeHelper.offsetWidth>0){
            this.selectionStart--;
            return false;
        }
    }else if(window.event.keyCode==13){
        this.matchIndex=undefined;
        if(this.activeHelper){
            this.activeHelper.style.display='none';
            this.activeHelper=undefined;
        }
        if(this.selectionStart != this.selectionEnd && this.selectionEnd!=0){
            this.selectionStart=this.value.length;
            this.selectionEnd=this.value.length;
            this.focus();
            return false;
        }
    }
};
dbTableExecuterTool.loadGadget=function(refresh){
    var dA=this.dispArea;
    var tn=this.tableNameInputField;
    if(tn.style.color=='black' && tn.value!=null && tn.value[0]!='='){
        var match=false;
        var qlts=dbTableExecuterTool.tableNameInputField.qlts;
        var qlas=dbTableExecuterTool.tableNameInputField.qlas;
        var l=qlts.children.length;
        for(var i=0;i<l;i++){
            if(qlts.children[i].textContent==tn.value){
                match=true;
            }
        }
        if(!match){
            var qli=document.createElement('div');
            qli.classList.add('logItem');
            qli.innerHTML=tn.value;
            qlts.insertAdjacentElement('afterBegin',qli);
            var i=0;
            var qii=false;
            while(i<l && !qii){
                if(dbTableExecuterTool.queryStack[i]>tn.value){
                    qii=true;
                    dbTableExecuterTool.queryStack.splice(i,0,tn.value);
                    qlas.children[i].insertAdjacentElement('beforeBegin',qli.cloneNode(true));
                }
                i++;
            }
            if(!qii){
                dbTableExecuterTool.queryStack.push(tn.value);
                qlas.appendChild(qli.cloneNode(true));
            }
        }
        qlas.style.display=null;
        qlts.style.display=null;
        if(qlas.offsetHeight>150){
            qlas.style.height='150px'
        }
        if(qlts.offsetHeight>150){
            qlts.style.height='150px'
        }
        qlas.style.display='none';
        qlts.style.display='none';
        tn.selectionStart=tn.value.length;
        if(dA && (dA.childNodes.length<1 || dA.offsetHeight>0) || refresh){
            dA.parentElement.removeChild(dA);
            this.appendChild(dA);
            var options={};
            var query=tn.value;
            var filters=query.split(/[?:@$][?:@$]/);
            if(filters.length>4){
                statusField.innerHTML='bad query. Duplicate Operators ~&|~';
                return false;
            }
            var tableName=filters[0].toLowerCase();
            this.rFilter=null;
            this.cString=null;
            this.sString=null;
            this.filterCount=0;
            var start=tableName.length;
            for(var i=1;i<filters.length;i++){
                if(query[query.indexOf(filters[i],start)-1]=='?' && !this.rFilter){
                    this.rFilter=filters[i];
                }else if(query[query.indexOf(filters[i],start)-1]==':' && !this.cString){
                    this.cString=filters[i];
                }else if(query[query.indexOf(filters[i],start)-1]=='@' && query[query.indexOf(filters[i],start)-2]=='@' && !this.sString){
                    this.sString=filters[i];
                    options.matchCase=true;
                    options.wolS=true;
                }else if(query[query.indexOf(filters[i],start)-1]=='$' && query[query.indexOf(filters[i],start)-2]=='$' && !this.sString){
                    this.sString=filters[i];
                }else if(query[query.indexOf(filters[i],start)-1]=='@' && query[query.indexOf(filters[i],start)-2]=='$' && !this.sString){
                    this.sString=filters[i];
                    options.wolS=true;
                }else if(query[query.indexOf(filters[i],start)-1]=='$' && query[query.indexOf(filters[i],start)-2]=='@' && !this.sString){
                    this.sString=filters[i];
                    options.matchCase=true;
                }else{
                    statusField.innerHTML='More than one filter of single type. ~&|~';
                    return false;
                }
                this.filterCount++;
                start+=filters[i].length;
            }
            this.searchOptions=options;
            if(window.dbTableExecuter && dbTableExecuter.tables){
                for(var tbln in dbTableExecuter.tables){
                    if(tbln==tableName){
                        var tAE=true;
                        break;
                    }
                }
            }
            if(dA.children.length<1 || window.dbTableExecuter==undefined || !tAE || refresh){
                statusField.innerHTML='openingTable...';
                this.facade=dA;
                this.gadgetLoader=new core.loadAsGadget2('lib/superScripts/dbTableExecuterForm.php',{
                    query:query
                },dA);
            }else{
                if(tAE && tableName!=dbTableExecuter.frontTable){
                    var cf=dbTableExecuterBdy.children['facade'];
                    dbTableExecuterTool.tableNameInputField.cNL.children[dbTableExecuter.frontTable].style.display='none';
                    dbTableExecuter.frontTable=tableName;
                    dbTableExecuterBdy.replaceChild(dbTableExecuter.tables[tableName].facade,cf);
                    dbTableExecuter.updateAllCells(tableName);
                    dbTableExecuterTool.tableNameInputField.cNL.children[tableName].style.display=null;
                }
                if(dbTableExecuterTool.rFilter){
                    dbTableExecuter.filterRows(dbTableExecuterTool.rFilter);
                }else{
                    dbTableExecuter.filterRows('true');
                }
                if(dbTableExecuterTool.cString){
                    dbTableExecuter.constrainColumns(dbTableExecuterTool.cString);
                }else{
                    var hes=dbTableExecuter.tables[tableName].dTable.table.getElementsByClassName('hidden');
                    for(var i=0;hes.length>0;i++){
                        hes[0].classList.remove('hidden');
                    }
                    dbTableExecuter.tables[tableName].colCount.innerHTML="&nbsp;"+(dbTableExecuter.tables[tableName].dTable.tHR.cells.length-1)+' columns';
                }
                if(dbTableExecuterTool.sString){
                    statusField.innerHTML='Searching table...';
                    dbTableExecuter.searchTable(dbTableExecuterTool.sString, dbTableExecuter.tables[tableName].dTable.tbody,dbTableExecuterTool.searchOptions);
                }
                if(this.filterCount<1){
                                    
                }
            }
        }else{
            if(this.children['displayArea']){
                dA.style.display=null;
            }else{
                dA.mrc.panelBtn.panel.dePanelizeBtn.onclick.call(dA.mrc.panelBtn.panel.dePanelizeBtn)
            }
        }
    }else if(tn.style.color=='black' && tn.value[0]=='='){
        if(window.dbTableExecuter){
            dbTableExecuter.updateFormula.call(this.tableNameInputField);
        }
    }else{
        statusField.innerHTML='Enter a table name ~:o~';
    }
    return false;
}