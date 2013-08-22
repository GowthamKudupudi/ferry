/*
Table sorting script  by Joost de Valk, check it out at http://www.joostdevalk.nl/code/sortable-table/.
Based on a script from http://www.kryogenix.org/code/browser/sorttable/.
Distributed under the MIT license: http://www.kryogenix.org/code/browser/licence.html .

Copyright (c) 1997-2007 Stuart Langridge, Joost de Valk.

Version 1.5.7
*/

/* You can change these values */
var sortable={
    image_path:"images/sortable/",
    image_up:"arrow-up.gif",
    image_down:"arrow-down.gif",
    image_none:"arrow-none.gif",
    europeandate:true,
    alternate_row_colors:true,
    SORT_COLUMN_INDEX:null,
    thead: false,
    dummyCell:document.createElement('td'),
    sortables_init:function() {
        // Find all tables with class sortable and make them sortable
        if (!document.getElementsByTagName) return;
        tbls = document.getElementsByTagName("table");
        for (ti=0;ti<tbls.length;ti++) {
            thisTbl = tbls[ti];
            if (thisTbl.classList.contains('sortable') && thisTbl.id) {
                sortable.ts_makeSortable(thisTbl);
            }
        }
    },
    ts_makeSortable:function(t) {
        if (t.rows && t.rows.length > 0) {
            if (t.tHead && t.tHead.rows.length > 0) {
                var firstRow = t.tHead.rows[t.tHead.rows.length-1];
                sortable.thead = true;
            } else {
                var firstRow = t.rows[0];
            }
        }
        if (!firstRow) return;
	
        // We have a first row: assume it's the header, and make its contents clickable links
        for (var i=0;i<firstRow.cells.length;i++) {
            var cell = firstRow.cells[i];
            //var txt = cell.innerHTML;
            if (!cell.classList.contains("unsortable")) {
                var sa=document.createElement('a');
                sa.classList.add('sortheader');
                sa.i=i;
                sa.onclick=function(){
                    sortable.ts_resortTable(this, this.i);
                    if(dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.table.view=='page'){
                        dbTableExecuter.scrollBy(dbTableExecuter.vRowCount,dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.getElementsByClassName('vInd')[0].rowIndex-dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.thead.children.length);
                    }
                    return false;
                }
                sa.innerHTML="<span class='sortarrow'><img src='"+ sortable.image_path + sortable.image_none + "' alt='&darr;'/></span>"
                cell.children['tools'].appendChild(sa);
                //cell.innerHTML = "<span style='font-size: 17px;font-weight:normal;letter-spacing:1px;font-family:\'lucida grande\',tahoma,verdana,arial,sans-serif;'>"+txt+"<a href='#' class='sortheader' onclick=\"sortable.ts_resortTable(this, "+i+");if(dbTableExecuter)dbTableExecuter.scrollBy(dbTableExecuter.vRowCount,dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.getElementsByClassName('vInd')[0].rowIndex-dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.thead.children.length);return false;\"><span class='sortarrow'><img src='"+ sortable.image_path + sortable.image_none + "' alt='&darr;'/></span></a></span>";
            }
        }
        if (sortable.alternate_row_colors) {
            sortable.alternate(t);
        }
    },
    ts_getInnerText:function(el) {
        if (typeof el == "string") return el;
        if (typeof el == "undefined") {
            return el
        };
        if (el.innerText) return el.innerText;	//Not needed but it is faster
        var str = "";
	
        var cs = el.childNodes;
        var l = cs.length;
        for (var i = 0; i < l; i++) {
            switch (cs[i].nodeType) {
                case 1: //ELEMENT_NODE
                    str += sortable.ts_getInnerText(cs[i]);
                    break;
                case 3:	//TEXT_NODE
                    str += cs[i].nodeValue;
                    break;
            }
        }
        return str;
    },
    ts_resortTable:function (lnk, clid) {
        var span;
        for (var ci=0;ci<lnk.childNodes.length;ci++) {
            if (lnk.childNodes[ci].tagName && lnk.childNodes[ci].tagName.toLowerCase() == 'span') span = lnk.childNodes[ci];
        }
        var spantext = sortable.ts_getInnerText(span);
        var td = lnk.parentNode;
        var column = clid || td.cellIndex;
        column=column==undefined?0:column;
        var t = sortable.getParent(td,'TABLE');
        // Work out a type for the column
        if (t.rows.length <= 1) return;
        var itm = "";
        var i = 0;
        while (itm == "" && i < t.tBodies[0].rows.length) {
            var itm = sortable.ts_getInnerText(t.tBodies[0].rows[i].cells[column]);
            itm = sortable.trim(itm);
            if (itm.substr(0,4) == "<!--" || itm.length == 0) {
                itm = "";
            }
            i++;
        }
        if (itm == "") return; 
        sortfn = sortable.ts_sort_caseinsensitive;
        if (itm.match(/^\d\d[\/\.-][a-zA-z][a-zA-Z][a-zA-Z][\/\.-]\d\d\d\d$/)) sortfn = sortable.ts_sort_date;
        if (itm.match(/^\d\d[\/\.-]\d\d[\/\.-]\d\d\d{2}?$/)) sortfn = sortable.ts_sort_date;
        if (itm.match(/^-?[�$�ۢ�]\d/)) sortfn = sortable.ts_sort_numeric;
        if (itm.match(/^-?(\d+[,\.]?)+(E[-+][\d]+)?%?$/)) sortfn = sortable.ts_sort_numeric;
        SORT_COLUMN_INDEX = column;
        var firstRow = new Array();
        var newRows = new Array();
        for (k=0;k<t.tBodies.length;k++) {
            for (i=0;i<t.tBodies[k].rows[0].length;i++) { 
                firstRow[i] = t.tBodies[k].rows[0][i]; 
            }
        }
        for (k=0;k<t.tBodies.length;k++) {
            if (!sortable.thead) {
                // Skip the first row
                for (j=1;j<t.tBodies[k].rows.length;j++) { 
                    newRows[j-1] = t.tBodies[k].rows[j];
                }
            } else {
                // Do NOT skip the first row
                for (j=0;j<t.tBodies[k].rows.length;j++) { 
                    newRows[j] = t.tBodies[k].rows[j];
                }
            }
        }
        newRows.sort(sortfn);
        if (span.getAttribute("sortdir") == 'down') {
            ARROW = "<img src='"+ sortable.image_path + sortable.image_down + "' alt='&darr;'/>";
            newRows.reverse();
            span.setAttribute('sortdir','up');
        } else {
            ARROW = "<img src='"+ sortable.image_path + sortable.image_up + "' alt='&uarr;'/>";
            span.setAttribute('sortdir','down');
        }
        var vInd=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.getElementsByClassName('vInd')[0];
        // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
        // don't do sortbottom rows and dontsort rows
        for (i=0; i<newRows.length; i++) { 
            if (!newRows[i].className || (newRows[i].className && !newRows[i].classList.contains('sortbottom') && !newRows[i].classList.contains('dontsort'))) {
                t.tBodies[0].appendChild(newRows[i]);
            }
        }
        var newVindSet=false;
        //do sortbottom rows only
        for (i=0; i<newRows.length; i++) {
            if (newRows[i].classList.contains('sortbottom')){
                t.tBodies[0].appendChild(newRows[i]);
            }
            if(newRows[i].offsetWidth>0 && !newVindSet){
                vInd.classList.remove('vInd');
                newRows[i].classList.add('vInd');
                newVindSet=true;
            }
        }
        // Delete any other arrows there may be showing
        var allspans = document.getElementsByTagName("span");
        for (var ci=0;ci<allspans.length;ci++) {
            if (allspans[ci].classList.contains('sortarrow')) {
                if (sortable.getParent(allspans[ci],"table") == sortable.getParent(lnk,"table")) { // in the same table as us?
                    allspans[ci].innerHTML = "<img src='"+ sortable.image_path + sortable.image_none + "' alt='&darr;'/>";
                }
            }
        }		
        span.innerHTML = ARROW;
        sortable.alternate(t);
        //realign new row add row
        var nr=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.children['newRow'];
        var nvr=dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.children['newVRow'];
        if(nr){
            nr.parentElement.removeChild(nr);
            nvr.parentElement.removeChild(nvr);
            dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.appendChild(nr);
            dbTableExecuter.tables[dbTableExecuter.frontTable].dTable.tbody.appendChild(nvr);
        }
    },
    getParent:function (el, pTagName) {
        if (el == null) {
            return null;
        } else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase()) {
            return el;
        } else {
            return sortable.getParent(el.parentNode, pTagName);
        }
    },
    sort_date:function (date) {	
        // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
        dt = "00000000";
        if (date.length == 11) {
            mtstr = date.substr(3,3);
            mtstr = mtstr.toLowerCase();
            switch(mtstr) {
                case "jan":
                    var mt = "01";
                    break;
                case "feb":
                    var mt = "02";
                    break;
                case "mar":
                    var mt = "03";
                    break;
                case "apr":
                    var mt = "04";
                    break;
                case "may":
                    var mt = "05";
                    break;
                case "jun":
                    var mt = "06";
                    break;
                case "jul":
                    var mt = "07";
                    break;
                case "aug":
                    var mt = "08";
                    break;
                case "sep":
                    var mt = "09";
                    break;
                case "oct":
                    var mt = "10";
                    break;
                case "nov":
                    var mt = "11";
                    break;
                case "dec":
                    var mt = "12";
                    break;
            // default: var mt = "00";
            }
            dt = date.substr(7,4)+mt+date.substr(0,2);
            return dt;
        } else if (date.length == 10) {
            if (sortable.europeandate == false) {
                dt = date.substr(6,4)+date.substr(0,2)+date.substr(3,2);
                return dt;
            } else {
                dt = date.substr(6,4)+date.substr(3,2)+date.substr(0,2);
                return dt;
            }
        } else if (date.length == 8) {
            yr = date.substr(6,2);
            if (parseInt(yr) < 50) { 
                yr = '20'+yr; 
            } else { 
                yr = '19'+yr; 
            }
            if (sortable.europeandate == true) {
                dt = yr+date.substr(3,2)+date.substr(0,2);
                return dt;
            } else {
                dt = yr+date.substr(0,2)+date.substr(3,2);
                return dt;
            }
        }
        return dt;
    },
    ts_sort_date:function (a,b) {
        dt1 = sortable.sort_date(sortable.ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
        dt2 = sortable.sort_date(sortable.ts_getInnerText(b.cells[SORT_COLUMN_INDEX]));
	
        if (dt1==dt2) {
            return 0;
        }
        if (dt1<dt2) { 
            return -1;
        }
        return 1;
    },
    ts_sort_numeric:function (a,b) {
        var aa = sortable.ts_getInnerText(a.cells[SORT_COLUMN_INDEX] || sortable.dummyCell);
        aa = sortable.clean_num(aa);
        var bb = sortable.ts_getInnerText(b.cells[SORT_COLUMN_INDEX] || sortable.dummyCell);
        bb = sortable.clean_num(bb);
        return sortable.compare_numeric(aa,bb);
    },
    compare_numeric:function (a,b) {
        var a = parseFloat(a);
        a = (isNaN(a) ? 0 : a);
        var b = parseFloat(b);
        b = (isNaN(b) ? 0 : b);
        return a - b;
    },
    ts_sort_caseinsensitive:function (a,b) {
        aa = sortable.ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
        bb = sortable.ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();
        if (aa==bb) {
            return 0;
        }
        if (aa<bb) {
            return -1;
        }
        return 1;
    },
    ts_sort_default:function (a,b) {
        aa = sortable.ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
        bb = sortable.ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
        if (aa==bb) {
            return 0;
        }
        if (aa<bb) {
            return -1;
        }
        return 1;
    },
    addEvent:function (elm, evType, fn, useCapture)
    // addEvent and removeEvent
    // cross-browser event handling for IE5+,	NS6 and Mozilla
    // By Scott Andrew
    {
        if (elm.addEventListener){
            elm.addEventListener(evType, fn, useCapture);
            return true;
        } else if (elm.attachEvent){
            var r = elm.attachEvent("on"+evType, fn);
            return r;
        } else {
            alert("Handler could not be removed");
        }
    },
    clean_num:function (str) {
        str = str.replace(new RegExp(/[^-?0-9.]/g),"");
        return str;
    },
    trim:function (s) {
        return s.replace(/^\s+|\s+$/g, "");
    },
    alternate:function (table) {
        // Take object table and get all it's tbodies.
        var tableBodies = table.getElementsByTagName("tbody");
        // Loop through these tbodies
        for (var i = 0; i < tableBodies.length; i++) {
            // Take the tbody, and get all it's rows
            var tableRows = tableBodies[i].getElementsByTagName("tr");
            // Loop through these rows
            // Start at 1 because we want to leave the heading row untouched
            for (var j = 0; j < tableRows.length; j++) {
                // Check if j is even, and apply classes for both possible results
                if ( (j % 2) == 0  ) {
                    if (tableRows[j].classList.contains('odd')) {
                        tableRows[j].classList.remove('odd');
                        tableRows[j].classList.add('even');
                    } else {
                        if ( !tableRows[j].classList.contains('even') ) {
                            tableRows[j].classList.add("even");
                        }
                    }
                } else {
                    if (tableRows[j].classList.contains('even')) {
                        tableRows[j].classList.remove('even');
                        tableRows[j].classList.add('odd');
                    } else {
                        if (!tableRows[j].classList.contains('odd')) {
                            tableRows[j].classList.add("odd");
                        }
                    }
                } 
            }
        }
    }
}
if(core.pathBuilder){
    sortable.image_path=core.pathBuilder(sortable.image_path, document.baseURI)
}
sortable.addEvent(window, "load", sortable.sortables_init);
window.sortable=sortable;