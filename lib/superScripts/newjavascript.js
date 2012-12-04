var dbTableExecuter = {
    vRowCount: 20,
    inputOnFocusValue: null,
    appendRow: function(evt) {
        if (evt && evt.tagName == 'TR') {
            if (evt.id && evt.cells.length == dbTableExecuter.dTable.tHR.cells.length - 2) {
                for (var i = 0; i < evt.cells.length; i++) {
                    evt.cells[i].onmousedown = dbTableExecuter.activateCell;
                    evt.cells[i].className = "dc";
                    evt.cells[i].tabIndex = '0';
                    evt.cells[i].onkeydown = dbTableExecuter.cellNavHandler;
                    evt.cells[i].ondblclick = dbTableExecuter.editCell;
                }
                var nitd = document.createElement('td');
                nitd.id = evt.id;
                nitd.innerHTML = "<img id='delRowBtn' title='Delete row " + evt.id + "' class='del rowDeleter ibtn' onclick='dbTableExecuter.delRow();return false;' src=\"images/-.png\"/>";
                nitd.style.textAlign = 'center';
                evt.selector = document.createElement('input');
                evt.selector.type = 'checkbox';
                evt.selector.id = 'selector';
                evt.selector.value = evt.id;
                evt.selector.className = 'row selector';
                evt.selector.style.display = 'none';
                nitd.appendChild(evt.selector);
                evt.title = evt.id;
                evt.insertAdjacentElement('afterBegin', nitd);
                if (dbTableExecuter.dTable.tbody.rows['newRow']) {
                    dbTableExecuter.dTable.tbody.removeChild(dbTableExecuter.dTable.tbody.rows['newRow']);
                }
                dbTableExecuter.dTable.tbody.rows['newVRow'].insertAdjacentElement('beforeBegin', evt);
                if (evt.rowIndex % 2 == 1)
                    evt.classList.add('even');
                else
                    evt.classList.add('odd');
                if (dbTableExecuter.dTable.table.view == 'page') {
                    dbTableExecuter.scrollBy(null, 'END')
                }
                var hashCol = null;
                var valCol = null;
                for (var i = 0; i < evt.cells.length; i++) {
                    hashCol = dbTableExecuter.tableHash[dbTableExecuter.dTable.tHR.cells[i].id];
                    valCol = dbTableExecuter.table[dbTableExecuter.dTable.tHR.cells[i].id];
                    if (valCol == dbTableExecuter.table['index']) {
                        if (hashCol)
                            hashCol.push(evt.id);
                        valCol.push(evt.id);
                    }
                    valCol.push(evt.cells[i].innerHTML);
                    if (hashCol)
                        hashCol.push(evt.cells[i].hash);
                }
                dbTableExecuter.tableProperties.rowCount++;
            } else {
                statusField.innerHTML = "mismatch of row format on appending a new row ~:|~";
            }
        } else {
            if (!dbTableExecuter.dTable.tbody.rows['newRow']) {
                var elm;
                if (evt && evt != window.event) {
                    elm = evt;
                } else {
                    evt = evt ? evt : window.event;
                    elm = evt.targetElement ? evt.targetElement : evt.srcElement;
                }
                var iRow = elm.parentElement.parentElement;
                elm.disabled = true;
                var tr = document.createElement('tr');
                var td = null;
                var fInput = null;
                for (var i = 0; i < dbTableExecuter.tableProperties.Field.length; i++) {
                    td = document.createElement('td');
                    if (dbTableExecuter.dTable.tHR.cells[i].id == 'index') {
                        td.id = 'newRow';
                        td.innerHTML = "<img id='delRowBtn' title='Delete row' class='del delRow ibtn' onclick='dbTableExecuter.delRow();return false;' src=\"images/-.png\"/>";
                        td.style.textAlign = 'center';
                        tr.selector = document.createElement('input');
                        tr.selector.type = 'checkbox';
                        tr.selector.id = 'selector';
                        tr.selector.value = td.id;
                        tr.selector.className = 'newRow selector';
                        if (!dbTableExecuter.dTable.table.classList.contains('rowSelection'))
                            tr.selector.style.display = 'none';
                        td.appendChild(tr.selector);
                        tr.id = td.id;
                    } else if (dbTableExecuter.tableProperties.Null[i] == 'NO') {
                        var iPE = dbTableExecuter.dTable.tHR.cells[i].cellEditTemp.cloneNode(true);
                        iPE.value = dbTableExecuter.dTable.tHR.cells[i].cellEditTemp.value;
                        td.innerHTML = '';
                        td.appendChild(iPE);
                        td.onclick = dbTableExecuter.activateCell;
                        td.className = "dc";
                        td.tabIndex = '0';
                        td.onkeydown = dbTableExecuter.cellNavHandler;
                        iPE.onkeydown = dbTableExecuter.newRowInputHandler;
                        if (!fInput)
                            fInput = iPE;
                    } else {
                        td.ondblclick = function() {
                            var iPE = dbTableExecuter.dTable.tHR.cells[this.cellIndex].cellEditTemp.cloneNode(true);
                            dbTableExecuter.activateCell.call(this);
                            this.innerHTML = '';
                            this.appendChild(iPE);
                            iPE.value = '';
                            iPE.onkeydown = dbTableExecuter.newRowInputHandler;
                            iPE.onblur = function() {
                                if (this.value == '')
                                    this.parentElement.innerHTML = '';
                                return false;
                            }
                            iPE.focus();
                            iPE.select();
                        }
                        td.onclick = dbTableExecuter.activateCell;
                        td.className = "dc";
                        td.tabIndex = '0';
                        td.onkeydown = dbTableExecuter.cellNavHandler;
                    }
                    tr.appendChild(td);
                }
                if (iRow.rowIndex % 2 == 1) {
                    tr.className = 'even';
                } else {
                    tr.className = 'odd';
                }
                iRow.insertAdjacentElement('beforeBegin', tr);
                tr.classList.add('sortbottom');
                if (dbTableExecuter.dTable.table.view == 'page') {
                    dbTableExecuter.scrollBy(null, 'END')
                }
                if (!fInput) {
                    dbTableExecuter.activateCell.call(tr.cells[1]);
                    tr.cells[1].ondblclick();
                } else {
                    dbTableExecuter.activateCell.call(fInput.parentElement);
                    fInput.focus();
                    fInput.select();
                }
            }
        }
    },
    newRowInputHandler: function() {
        event.cancelBubble = true;
        var cell = null;
        if (event.keyCode == 39 && (this.selectionEnd == undefined || (this.selectionEnd == this.value.length && this.selectionStart == this.selectionEnd))) {
            cell = this.parentElement;
            if (cell.nextSibling != null) {
                if (cell.nextSibling.children.length == 0)
                    cell.nextSibling.ondblclick();
                else {
                    dbTableExecuter.activateCell.call(cell.nextSibling);
                    cell.nextSibling.children[0].focus();
                    cell.nextSibling.children[0].select();
                }
            }
            return false;
        } else if (event.keyCode == 37 && (this.selectionStart == undefined || (this.selectionStart == 0 && this.selectionStart == this.selectionEnd))) {
            cell = this.parentElement;
            if (cell.previousSibling.cellIndex != 0) {
                if (cell.previousSibling.children.length == 0)
                    cell.previousSibling.ondblclick();
                else {
                    dbTableExecuter.activateCell.call(cell.previousSibling);
                    cell.previousSibling.children[0].focus();
                    cell.previousSibling.children[0].select();
                }
            }
            return false;
        } else if (event.keyCode == 13) {
            this.onblur = null;
            event.preventDefault();
            cell = this.parentElement;
            dbTableExecuter.changeEntry(this);
            return false;
        }
    },
    fixCell: function(feed) {
        event.preventDefault();
        event.cancelBubble = true;
        var cell = feed.cell, preValue = feed.preValue, postValue = feed.postValue, responseText = feed.responseText;
        var cellRow = cell.parentElement;
        if (feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue == 'success') {
            dbTableExecuter.tableOp.updateCell(feed);
            if (cellRow.id == 'newRow') {
                feed.rowIndex = feed.responseXML.getElementsByTagName('newRowIndex')[0].firstChild.nodeValue;
                cellRow.id = feed.rowIndex;
                cellRow.cells[0].id = cellRow.id;
                cellRow.cells[0].children['selector'].value = feed.rowIndex;
                cellRow.cells[0].title = cellRow.id;
                cellRow.cells[0].firstChild.title = "Delete row " + cellRow.id;
                dbTableExecuter.tableProperties.rowCount += 1;
                dbTableExecuter.dTable.tbody.rows['newVRow'].cells[0].childNodes[0].disabled = false;
                for (var i = 1; i < cellRow.cells.length; i++) {
                    try {
                        cellRow.cells[i].innerHTML = core.htmlEncode(cellRow.cells[i].children[0].value);
                    } 
                    catch (e) {
                    }
                    cellRow.cells[i].ondblclick = dbTableExecuter.editCell;
                }
                cellRow.classList.remove('sortbottom');
                dbTableExecuter.appendRow(dbTableExecuter.dTable.table.rows['newVRow'].cells[0].firstChild);
            } else {
                cell.innerHTML = core.htmlEncode(postValue);
                if (cell.classList.contains('active'))
                    cell.focus();
            }
            statusField.innerHTML = 'Cell(' + feed.colIndex + ',' + feed.rowIndex + ') updated ~:)~';
        } else if (feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue == 'CNE') {
            cell.innerHTML = preValue;
            if (cell.classList.contains('active'))
                cell.focus();
        } else {
            statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue + "~:|~";
            if (cellRow.id != 'newRow') {
                cell.innerHTML = preValue;
                if (cell.classList.contains('active'))
                    cell.focus();
            }
        }
        return false;
    },
    inputOnFocus: function(evt) {
        evt = (evt) ? evt : window.event;
        var srcElm = evt.srcElement;
        dbTableExecuter.inputOnFocusValue = srcElm.value;
    },
    cellInputHandler: function() {
        event.cancelBubble = true;
        var cell;
        var row;
        var tbody;
        if (event.keyCode == 40) {
            this.onblur = null;
            cell = this.parentElement;
            row = cell.parentElement;
            tbody = row.parentElement;
            dbTableExecuter.changeEntry(this);
            if (row.nextSibling.id == 'newVRow' && row.id != 'newRow') {
                dbTableExecuter.appendRow(row.nextSibling.firstChild.firstChild);
                dbTableExecuter.activateCell.call(row.nextSibling.cells[cell.cellIndex]);
                dbTableExecuter.editCell(row.nextSibling.cells[cell.cellIndex]);
            } else {
                if (tbody.rows[row.rowIndex].style.display == 'none') {
                    tbody.rows[row.rowIndex].style.display = null;
                    tbody.rows[row.rowIndex - dbTableExecuter.vRowCount].style.display = 'none';
                }
                var ipCell = tbody.rows[row.rowIndex].cells[cell.cellIndex];
                dbTableExecuter.activateCell.call(ipCell);
                if (ipCell.children.length == 0) {
                    if (ipCell.children.length == 0)
                        ipCell.ondblclick(ipCell);
                    else
                        ipCell.firstChild.focus();
                } else {
                    ipCell.children[0].focus();
                }
            }
        } else if (event.keyCode == 38) {
            this.onblur = null;
            cell = this.parentElement;
            row = cell.parentElement;
            tbody = row.parentElement;
            if (row.previousSibling != null) {
                dbTableExecuter.changeEntry(this);
                var ipCell = tbody.rows[row.rowIndex - 2].cells[cell.cellIndex];
                dbTableExecuter.activateCell.call(ipCell);
                if (ipCell.children.length == 0) {
                    dbTableExecuter.activateCell.call(ipCell);
                    if (tbody.rows[row.rowIndex - 2].style.display == 'none') {
                        tbody.rows[row.rowIndex - 2].style.display = null;
                        tbody.rows[row.rowIndex - 2 + dbTableExecuter.vRowCount].style.display = 'none';
                    }
                    if (ipCell.children.length == 0)
                        ipCell.ondblclick(ipCell);
                    else
                        ipCell.firstChild.focus();
                } else {
                    ipCell.children[0].focus();
                }
            }
        } else if (event.keyCode == 39 && (this.selectionEnd == undefined || (this.selectionEnd == this.value.length && this.selectionStart == this.selectionEnd))) {
            this.onblur = null;
            cell = this.parentElement;
            row = cell.parentElement;
            tbody = row.parentElement;
            dbTableExecuter.changeEntry(this);
            if (cell.nextSibling != null) {
                dbTableExecuter.activateCell.call(cell.nextSibling);
                dbTableExecuter.editCell(cell.nextSibling);
            } else if (tbody.rows.length != row.rowIndex + 1) {
                var ipCell = tbody.rows[row.rowIndex].cells[1];
                dbTableExecuter.activateCell.call(ipCell);
                if (ipCell.children.length == 0)
                    ipCell.ondblclick(ipCell);
                else
                    ipCell.firstChild.focus();
            }
        } else if (event.keyCode == 37 && (this.selectionStart == undefined || (this.selectionStart == 0 && this.selectionStart == this.selectionEnd))) {
            this.onblur = null;
            cell = this.parentElement;
            row = cell.parentElement;
            tbody = row.parentElement;
            dbTableExecuter.changeEntry(this);
            if (cell.cellIndex != 1) {
                dbTableExecuter.activateCell.call(cell.previousSibling);
                dbTableExecuter.editCell(cell.previousSibling);
            } else if (row.rowIndex != 1) {
                var ipCell = tbody.rows[row.rowIndex - 2].cells[row.cells.length - 1];
                dbTableExecuter.activateCell.call(ipCell);
                if (ipCell.children.length == 0) {
                    ipCell.ondblclick(ipCell);
                } else {
                    ipCell.firstChild.focus();
                }
            }
        } else if (event.keyCode == 13) {
            event.preventDefault();
            this.onblur = null;
            cell = this.parentElement;
            row = cell.parentElement;
            tbody = row.parentElement;
            dbTableExecuter.changeEntry(this);
            return false;
        } else if (event.keyCode == 27) {
            var parser = new DOMParser();
            this.onblur = null;
            var feed = {
                cell: this.parentElement,
                preValue: dbTableExecuter.inputOnFocusValue,
                postValue: dbTableExecuter.inputOnFocusValue,
                responseText: 'CNE',
                responseXML: parser.parseFromString('<status>CNE</status>', 'text/xml')
            };
            dbTableExecuter.inputOnFocusValue = null;
            dbTableExecuter.fixCell(feed);
        }
    },
    changeEntry: function(elm) {
        if (!elm || elm == event)
            elm = this;
        var cell = elm.parentElement;
        var feed = new Object();
        if (elm.value != dbTableExecuter.inputOnFocusValue) {
            var tHRCells = dbTableExecuter.dTable.tHR.cells;
            var colIndex = tHRCells[cell.cellIndex].id;
            var dbTable = dbTableExecuter.tableProperties.tableName;
            var value = elm.value;
            var cellRow = cell.parentElement;
            var rowIndex = cellRow.id;
            var cellHash = null;
            if (dbTableExecuter.tableProperties.authorization != '*') {
                for (var i in dbTableExecuter.table.index) {
                    if (dbTableExecuter.table.index[i] == rowIndex) {
                        cellHash = dbTableExecuter.tableHash[colIndex][i];
                    }
                }
            }
            var colInd = [colIndex];
            var val = [value];
            if (rowIndex == 'newRow') {
                var uCells = cellRow.getElementsByClassName('inputCell');
                colIndex = "`" + tHRCells[uCells[0].parentElement.cellIndex].id + "`";
                value = uCells[0].value;
                colInd[0] = tHRCells[uCells[0].parentElement.cellIndex].id;
                val[0] = uCells[0].value;
                for (var i = 1; i < uCells.length; i++) {
                    if (uCells[i].value != '') {
                        colIndex += ', `' + tHRCells[uCells[i].parentElement.cellIndex].id + '`';
                        value += "," + uCells[i].value;
                        colInd[colInd.length] = tHRCells[uCells[0].parentElement.cellIndex].id;
                        val[val.length] = uCells[0].value;
                    } else {
                        uCells[i].focus();
                        statusField.innerHTML = 'Not null cells should not be Empty ~:|~';
                        return false;
                    }
                }
            }
            statusField.innerHTML = 'Updating cell(' + colIndex + ',' + rowIndex + ')...';
            var content = "tableOperation=updateCell&cellHash=" + cellHash + "&dbTable=" + dbTable + "&colIndex=" + colIndex + "&value=" + value + "&rowIndex=" + rowIndex;
            feed = {
                cell: cell,
                preValue: dbTableExecuter.inputOnFocusValue,
                postValue: value,
                colIndex: colIndex,
                rowIndex: rowIndex,
                colInd: colInd,
                val: val,
                content: {
                    tableOperation: 'updateCell',
                    cellHash: cellHash,
                    dbTable: dbTable,
                    colIndex: colIndex,
                    value: value,
                    rowIndex: rowIndex
                }
            };
            dbTableExecuter.inputOnFocusValue = null;
            feed.ferry = new core.shuttle('lib/superScripts/dbTableExecuter.php', content, dbTableExecuter.fixCell, feed);
        } else {
            var parser = new DOMParser();
            feed = {
                cell: cell,
                preValue: dbTableExecuter.inputOnFocusValue,
                postValue: dbTableExecuter.inputOnFocusValue,
                responseText: 'CNE',
                responseXML: parser.parseFromString('<status>CNE</status>', 'text/xml')
            };
            dbTableExecuter.inputOnFocusValue = null;
            dbTableExecuter.fixCell(feed);
        }
        return false;
    },
    editCell: function(evt) {
        var cell;
        if (evt != window.event && evt) {
            cell = evt;
        } else {
            evt = window.event;
            cell = evt.targetElement ? evt.targetElement : evt.srcElement;
            cell = cell.tagName == 'TD' ? cell : null;
        }
        if (cell) {
            if (cell.children.length == 0) {
                var cellValue = cell.innerHTML;
                var j = cell.cellIndex;
                dbTableExecuter.inputOnFocusValue = cellValue;
                var iPE = dbTableExecuter.dTable.tHR.cells[j].cellEditTemp.cloneNode(true);
                iPE.style.width = cell.offsetWidth - 3 + "px";
                iPE.style.height = cell.offsetHeight - 2 + "px";
                iPE.value = cellValue;
                iPE.onkeydown = dbTableExecuter.cellInputHandler;
                iPE.onblur = dbTableExecuter.changeEntry;
                cell.innerHTML = '';
                cell.appendChild(iPE);
                iPE.focus();
                if (iPE.select)
                    iPE.select();
            } else {
                cell.children[0].focus();
                if (cell.children[0].select)
                    cell.children[0].select();
            }
        }
        return false;
    },
    totalRow: function(cell) {
        var total = 0;
        var row = cell.parentElement;
        var sumLimit = row.cells.length - 1;
        for (var i = 2; i < sumLimit; i++) {
            if (row.cells[i].children.length == 0)
                total += row.cells[i].innerHTML;
        }
        row.cells[sumLimit].innerHTML = total;
    },
    newColumn: function(evt) {
        var elm;
        if (evt && evt != window.event) {
            elm = evt;
        } else {
            evt = window.event ? window.event : null;
            if (evt) {
                elm = evt.targetElement ? evt.targetElement : evt.srcElement;
            }
        }
        if (elm) {
            var cell = elm.parentElement.parentElement;
            var thead = cell.parentElement.parentElement;
            var tbody = thead.nextSibling;
            var table = tbody.parentElement;
            var cells = cell.parentElement.cells;
            for (var i = 1; i < cells.length; i++) {
                cells[i].children[0].children['newColumnBtn'].disabled = true;
                cells[i].children[0].children['newColumnBtn'].title = 'finish current column operation';
                try {
                    cells[i].children[0].children['delColumnBtn'].disabled = true;
                    cells[i].children[0].children['delColumnBtn'].title = 'finish current column operation';
                } catch (e) {
                }
                ;
            }
            var rows = table.rows;
            var columnName = document.createElement('th');
            var aColumnIndex = cell.cellIndex - 1;
            td = document.createElement('form');
            td.onsubmit = function() {
                dbTableExecuter.insColumn();
                return false;
            }
            var col = document.createElement('input');
            col.type = 'text';
            col.id = 'columnNameInput';
            col.value = 'ColumnName'
            col.title = "Enter column name";
            col.size = '9';
            col.onclick = function() {
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col = document.createElement('select');
            col.innerHTML = "<option value=''>SelectType</option><option value='int'>INT</option><option value='varchar'>VARCHAR</option><option value='timestamp'>TIMESTAMP</option><option value='enum'>ENUM</option>";
            col.id = 'type';
            col.title = 'select data type';
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col = document.createElement('input');
            col.type = 'text';
            col.id = 'size';
            col.value = '08';
            col.title = "Enter size, if enum enter values seperated by ,";
            col.size = '20';
            col.onclick = function() {
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col = document.createElement('input');
            col.type = 'text';
            col.id = 'default';
            col.title = "Enter a default value if any \nelse leave it blank";
            col.size = '20';
            col.onclick = function() {
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col = document.createElement('input');
            col.type = 'checkbox';
            col.id = 'notNull';
            col.name = 'notNull';
            col.title = 'check if column should be given \n a value upon new row.';
            td.appendChild(col);
            col = document.createElement('input');
            col.type = 'submit';
            col.id = 'insColumnBtn';
            col.value = 'InsertColumn';
            td.appendChild(col);
            col = document.createElement('img');
            col.id = 'cancelInsert';
            col.className = 'ibtn';
            col.src = 'images/-.png';
            col.onclick = function() {
                dbTableExecuter.cancelInsertCol();
                return false;
            }
            col.title = 'cancel column insert';
            td.appendChild(col);
            columnName.appendChild(td);
            rows[0].insertBefore(columnName, cell);
            var rowsLen = rows.length - 1;
            for (var i = 1; i <= rowsLen; i++) {
                var td = document.createElement('td');
                var rcell = rows[i].cells[aColumnIndex];
                if (i == rowsLen) {
                    td.style.display = 'none';
                }
                rcell.insertAdjacentElement('afterEnd', td);
            }
        }
    },
    cancelInsertCol: function(evt) {
        var elm;
        if (evt && evt != window.event) {
            elm = evt;
        } 
        else {
            evt = window.event ? window.event : null;
            if (evt) {
                elm = evt.targetElement ? evt.targetElement : evt.srcElement;
            }
        }
        if (elm) {
            var cell = elm.parentElement.parentElement;
            var cells = cell.parentElement.cells;
            var rows = dbTableExecuter.dTable.table.rows;
            var columnIndex = cell.cellIndex;
            for (var i = 0; i < rows.length - 1; i++) {
                var rcell = rows[i].cells[columnIndex];
                rows[i].removeChild(rcell);
            }
            for (var i = 1; i < cells.length; i++) {
                cells[i].children[0].children['newColumnBtn'].disabled = false;
                cells[i].children[0].children['newColumnBtn'].title = 'Insert new column';
                try {
                    cells[i].children[0].children['delColumnBtn'].disabled = false;
                    cells[i].children[0].children['delColumnBtn'].title = 'Delete column';
                } catch (e) {
                }
                ;
            }
            cells[cells.length - 1].children[0].children['newColumnBtn'].title = 'Append a new column';
        }
        return false;
    },
    authorityBlock: function(evt) {
        if (evt && evt != window.event) {
            this.elm = evt;
        } else {
            evt = window.event ? window.event : null;
            if (evt) {
                this.elm = evt.targetElement ? evt.targetElement : evt.srcElement;
            }
        }
        if (this.elm) {
            this.authorityBox = document.createElement('form');
            var authBox = this.authorityBox;
            authBox.cell = this.elm.parentElement.parentElement;
            authBox.columnIndex = authBox.cell.cellIndex;
            authBox.table = dbTableExecuter.dTable.table;
            this.gadget = dbTableExecuterTool.facade;
            authBox.columnName = authBox.cell.id;
            authBox.dbTable = dbTableExecuter.tableProperties.tableName;
            authBox.id = 'authorityBox';
            dbTableExecuter.colAuthUsers(authBox, dbTableExecuter.tableProperties.Comment[authBox.columnIndex]);
            authBox.inputObjectIds = document.createElement('input');
            authBox.inputObjectIds.id = 'inputObjectId';
            authBox.inputObjectIds.type = 'text';
            authBox.inputObjectIds.title = 'add organization\'s object ids seperated by \',\' without any spaces';
            authBox.appendChild(authBox.inputObjectIds);
            authBox.addAuthObjects = document.createElement('input');
            authBox.addAuthObjects.id = 'addAuthObjects';
            authBox.addAuthObjects.type = 'submit';
            authBox.addAuthObjects.value = 'Add';
            authBox.addAuthObjects.title = 'Add authorized Objects(employee or student or lecturer or any object related to the organization)';
            authBox.appendChild(authBox.addAuthObjects);
            authBox.appendChild(document.createElement('br'));
            authBox.rLabel = document.createElement('label');
            authBox.rLabel.innerHTML = 'r';
            authBox.appendChild(authBox.rLabel);
            authBox.r = document.createElement('input');
            authBox.r.type = 'radio';
            authBox.r.id = 'read';
            authBox.r.value = 'r';
            authBox.r.name = 'rORw'
            authBox.r.checked = true;
            authBox.appendChild(authBox.r);
            authBox.wLabel = document.createElement('label');
            authBox.wLabel.innerHTML = '/w';
            authBox.appendChild(authBox.wLabel);
            authBox.w = document.createElement('input');
            authBox.w.type = 'radio';
            authBox.w.id = 'write';
            authBox.w.value = 'w'
            authBox.w.name = 'rORw';
            authBox.appendChild(authBox.w);
            authBox.appendChild(document.createElement('label')).innerHTML = '&nbsp;specificRows';
            authBox.specifyRows = document.createElement('input');
            authBox.specifyRows.type = 'checkbox';
            authBox.specifyRows.className = 'row specifier';
            authBox.specifyRows.id = 'specifyRows';
            authBox.specifyRows.onmousedown = function() {
                if (!this.checked) {
                    dbTableExecuter.specifyRows();
                } else {
                }
                event.preventDefault();
                return false;
            }
            authBox.appendChild(authBox.specifyRows);
            authBox.onsubmit = function() {
                this.specificRows = [];
                var authRows = '';
                if (this.specifyRows.checked) {
                    var rows = this.table.getElementsByClassName('row selector');
                    for (i = 1; i < rows.length; i++) {
                        if (rows[i].checked) {
                            if (authRows != '')
                                authRows += ',' + rows[i].value;
                            else
                                authRows = rows[i].value;
                        }
                    }
                } else {
                    authRows = '*';
                }
                this.feed = new Object();
                if (this.w.checked)
                    this.feed.rORw = 'w';
                else
                    this.feed.rORw = 'r';
                this.feed.ab = this;
                this.feed.authRows = authRows;
                this.feed.columnIndex = this.columnIndex;
                this.feed.columnName = this.columnName;
                this.feed.content = 'dbTable=' + this.dbTable + '&tableOperation=permitColUsers&colName=' + this.columnName + '&nmembers=' + this.inputObjectIds.value + '&rows=' + authRows + '&rORw=' + this.feed.rORw;
                this.feed.content = {
                    dbTable: this.dbTable,
                    tableOperation: 'permitColUsers',
                    colName: this.columnName,
                    nmembers: this.inputObjectIds.value,
                    rows: authRows,
                    rORw: this.feed.rORw
                }
                this.feed.postExpedition = function(feed) {
                    dbTableExecuter.tableProperties.Comment[feed.columnIndex] = feed.responseXML.getElementsByTagName('comment')[0].firstChild.nodeValue;
                    var mg = feed.responseXML.getElementsByTagName('delGrp');
                    var mgl = mg.length;
                    var ngids = [];
                    for (i = 0; i < mgl; i++) {
                        var ngid = mg[i].getElementsByTagName('g')[0].firstChild.nodeValue;
                        var ogid = feed.ab.children[mg[i].getElementsByTagName('rORw')[0].firstChild.nodeValue].children[mg[i].getElementsByTagName('g')[0].firstChild.nodeValue];
                        ogid.parentElement.removeChild(ogid);
                    }
                    mg = feed.responseXML.getElementsByTagName('modGrp');
                    mgl = mg.length;
                    for (var i = 0; i < mgl; i++) {
                        var ngid = mg[i].getElementsByTagName('g')[0].firstChild.nodeValue;
                        ngids[ngids.length] = ngid;
                        feed.ab.children[mg[i].getElementsByTagName('rORw')[0].firstChild.nodeValue].children[ngid].children['grpMems'].innerHTML = mg[i].getElementsByTagName('mems')[0].firstChild.nodeValue;
                    }
                    mg = feed.responseXML.getElementsByTagName('newGrp');
                    mgl = mg.length;
                    for (i = 0; i < mgl; i++) {
                        var ngid = mg[i].getElementsByTagName('g')[0].firstChild.nodeValue;
                        ngids[ngids.length] = ngid;
                        var grp = document.createElement('div');
                        grp.id = ngid
                        grp.className = 'grp';
                        var grpId = document.createElement('span');
                        grpId.id = 'grpId';
                        grp.appendChild(grpId);
                        var grpMems = document.createElement('span');
                        grpMems.id = 'grpMems';
                        grpMems.style.fontWeight = 'bold';
                        grpMems.innerHTML = mg[i].getElementsByTagName('mems')[0].firstChild.nodeValue;
                        grp.appendChild(grpMems);
                        var grpRows = document.createElement('div');
                        grpRows.id = 'grpRows';
                        grpRows.innerHTML = feed.authRows;
                        grp.appendChild(grpRows);
                        feed.ab.children[mg[i].getElementsByTagName('rORw')[0].firstChild.nodeValue].appendChild(grp);
                    }
                    feed.ab.parentElement.reAlign();
                    statusField.innerHTML = 'Column ' + feed.columnName + ' permissions changed.~B-)~';
                }
                this.feed.ferry = new core.shuttle('lib/superScripts/dbTableExecuter.php', this.feed.content, this.feed.postExpedition, this.feed);
                return false;
            }
            this.__proto__ = new core.msgPanel(authBox.cell.id + 'AuthorizedUsers', this.gadget, this.elm, authBox);
            this.closeBtn.onclick = function() {
                dbTableExecuter.hideRS();
                delete this.parentElement.objOwner.msgPanel;
                this.parentElement.closePanel();
            }
        }
        return this.authorityWindow;
    },
    hideRS: function() {
        var table = dbTableExecuter.dTable.table;
        if (table.classList.contains('rowSelection')) {
            table.classList.remove('rowSelection');
            var rowSelectors = table.getElementsByClassName('row selector');
            for (i = 0; i < rowSelectors.length; i++) {
                rowSelectors[i].style.display = 'none';
                rowSelectors[i].checked = false;
                if (rowSelectors[i].anim) {
                    rowSelectors[i].mState = 0;
                    rowSelectors[i].anim(1 + 3 * rowSelectors[i].mState);
                }
            }
            var rowSs = document.getElementsByClassName('row specifier');
            for (i = 0; i < rowSs.length; i++) {
                rowSs[i].disabled = false;
            }
        }
    },
    colAuthUsers: function(dArea, comment) {
        var i = 0;
        var k = 0;
        var pEnts = [];
        pEnts['r'] = [];
        pEnts['w'] = [];
        var pT = null;
        var eC = null;
        while (comment[i] != null) {
            if (comment[i] == '{') {
                i++;
                k = 0;
                pT = comment[i];
                eC = pEnts[pT].length;
                i++;
                while (comment[i] != '}') {
                    pEnts[pT][eC] = [];
                    pEnts[pT][eC]['gid'] = '';
                    while (comment[i] != ',') {
                        pEnts[pT][eC]['gid'] += comment[i];
                        i++;
                    }
                    i++;
                    if (comment[i] == '{') {
                        i++;
                        pEnts[pT][eC]['rows'] = [];
                        while (comment[i] != '}') {
                            pEnts[pT][eC]['rows'][k] = '';
                            while (comment[i] != ',' && comment[i] != '}') {
                                pEnts[pT][eC]['rows'][k] += comment[i];
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
            } else {
                i++;
            }
        }
        if (dArea != null) {
            var rw = ['r', 'w'];
            var gis = [];
            for (var j = 0; j < rw.length; j++) {
                var rDiv = document.createElement('div');
                rDiv.id = rw[j];
                rDiv.innerHTML = rw[j] + ':';
                for (i = 0; i < pEnts[rw[j]].length; i++) {
                    var grp = document.createElement('div');
                    grp.id = pEnts[rw[j]][i]['gid'];
                    gis[gis.length] = grp.id;
                    grp.className = 'grp';
                    rDiv.appendChild(grp);
                    var grpId = document.createElement('span');
                    grpId.id = 'grpId';
                    grp.appendChild(grpId);
                    var grpMems = document.createElement('span');
                    grpMems.id = 'grpMems';
                    grpMems.style.fontWeight = 'bold';
                    grp.appendChild(grpMems);
                    var grpRows = document.createElement('div');
                    grpRows.id = 'grpRows';
                    grpRows.innerHTML = pEnts[rw[j]][i]['rows'].join(',');
                    grp.appendChild(grpRows);
                    rDiv.appendChild(grp);
                }
                dArea.appendChild(rDiv);
            }
            var feed = new Object();
            feed.content = {
                gids: gis.join(',')
            }; //"gids="+gis.join(',');
            feed.dArea = dArea;
            feed.postExpedition = function() {
                var rgrps = feed.responseXML.getElementsByTagName('grp');
                var ogrps = feed.dArea.getElementsByClassName('grp')
                for (var i = 0; i < rgrps.length; i++) {
                    try {
                        ogrps[rgrps[i].getElementsByTagName('g')[0].firstChild.nodeValue].children['grpMems'].innerHTML = rgrps[i].getElementsByTagName('mems')[0].firstChild.nodeValue;
                    } catch (e) {
                    }
                }
                statusField.innerHTML = 'authority block opened successfully...';
            }
            feed.ferry = new core.shuttle('lib/adminScripts/grpManager.php', feed.content, feed.postExpedition, feed);
        } else
            return pEnts;
    },
    delColumn: function(evt) {
        var elm;
        if (evt && evt != window.event) {
            elm = evt;
        } else {
            evt = window.event ? window.event : null;
            if (evt) {
                elm = evt.targetElement ? evt.targetElement : evt.srcElement;
            }
        }
        if (elm && confirm('Are u sure in deleting the column? don\'t delete columns more than 10 times for a table. it corrupts table!')) {
            var cell = elm.parentElement.parentElement;
            var columnIndex = cell.cellIndex;
            var tableName = dbTableExecuter.tableProperties.tableName;
            var columnName = dbTableExecuter.dTable.tHR.cells[columnIndex].id;
            var feed = new Object();
            feed.colName = columnName
            feed.postExpedition = function(feed) {
                if (feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue == 'success') {
                    dbTableExecuter.tableOp.delColumn(feed.colName);
                    statusField.innerHTML = 'Column Deleted ~B-)~';
                } else {
                    statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue + ' ~:|~';
                }
            };
            var content = "dbTable=" + tableName + "&columnName=" + columnName + "&tableOperation=delColumn";
            feed.content = {
                dbTable: tableName,
                columnName: columnName,
                tableOperation: 'delColumn'
            }
            feed.ferry = new core.shuttle('./lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
        }
    },
    insColumn: function(evt) {
        var elm;
        if (evt && evt != window.event) {
            elm = evt;
        } else {
            evt = window.event ? window.event : null;
            if (evt) {
                elm = evt.targetElement ? evt.targetElement : evt.srcElement;
            }
        }
        if (elm) {
            var columnIndex = elm.parentElement.cellIndex;
            var insertAfter = dbTableExecuter.dTable.tHR.cells[columnIndex - 1].id;
            var type = elm.children['type'].value;
            var maxSize = elm.children['size'].value;
            var columnName = elm.children['columnNameInput'];
            var notNull = elm.children['notNull'].checked ? '&notNull=YES' : '';
            var tableName = dbTableExecuter.tableProperties.tableName;
            var dflt = elm.children['default'].value;
            if (columnName.value == '') {
                columnName.focus();
            } else {
                var content = 'columnName=' + columnName.value + '&type=' + type + '&maxSize=' + maxSize + '&tableOperation=insColumn&dbTable=' + tableName + '&insertAfter=' + insertAfter + notNull + '&default=' + dflt;
                var feed = new Object();
                feed.colName = columnName.value;
                feed.insAftCol = insertAfter;
                feed.type = type;
                feed.maxSize = maxSize;
                feed.nul = notNull ? 'NO' : 'YES';
                feed.dflt = dflt;
                feed.key = '';
                feed.content = {
                    columnName: columnName.value,
                    type: type,
                    maxSize: maxSize,
                    tableOperation: 'insColumn',
                    dbTable: tableName,
                    insertAfter: insertAfter,
                    notNull: elm.children['notNull'].checked ? 'YES' : 'NO',
                    dfault: dflt
                }
                var columnAppended = function(feed) {
                    if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'success') {
                        dbTableExecuter.tableOp.insColumn(feed.colName, feed.insAftCol, feed);
                        statusField.innerHTML = 'Column Added ~B-)~';
                    } else {
                        statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].firstChild.nodeValue + ' ~:|~';
                    }
                }
                statusField.innerHTML = 'Creating new column...~:{~';
                feed.ferry = new core.shuttle('./lib/superScripts/dbTableExecuter.php', content, columnAppended, feed)
            }
        }
        return false;
    },
    generateColumns: function(evt) {
        var elm;
        if (evt && evt != window.event) {
            elm = evt;
        } else {
            evt = window.event ? window.event : null;
            elm = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        }
        evt.preventDefault();
        var nocs = elm.children['noOfSubs'].value;
        var tableCreatorElm = elm.parentElement;
        var tableName = dbTableExecuter.tableProperties.tableName;
        var tableBlock = tableCreatorElm.children['tableBlock'];
        if (tableBlock.length != 0) {
            for (var i = tableBlock.children.length - 1; i > -1; i--) {
                tableBlock.children[i].parentElement.removeChild(tableBlock.children[i]);
            }
        }
        var table = document.createElement('table');
        table.id = 'tableTemplate';
        var tr = document.createElement('tr');
        var td, col;
        for (var i = 0; i < nocs; i++) {
            td = document.createElement('td');
            td.style.textAlign = 'center';
            col = document.createElement('input');
            col.type = 'text';
            col.id = 'colName';
            col.value = 'Column Name'
            col.title = "Enter a column name";
            col.size = '9';
            col.onclick = function() {
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col = document.createElement('select');
            col.innerHTML = "<option value=''>SelectType</option><option value='INT'>INT</option><option value='VARCHAR'>VARCHAR</option><option value='TIMESTAMP'>TIMESTAMP</option><option value='ENUM'>ENUM</option>";
            col.id = 'type';
            col.title = 'select data type';
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col = document.createElement('input');
            col.type = 'text';
            col.id = 'size';
            col.value = '08';
            col.title = "Enter size, if enum enter values seperated by ','";
            col.size = '20';
            col.onclick = function() {
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col = document.createElement('input');
            col.type = 'text';
            col.id = 'default';
            col.title = "Enter a default value if any \nelse leave it blank";
            col.size = '20';
            col.onclick = function() {
                this.select();
                return false;
            }
            td.appendChild(col);
            td.appendChild(document.createElement('br'));
            col = document.createElement('input');
            col.type = 'radio';
            col.id = 'pimaryKey';
            col.name = 'primaryKey';
            col.ondblclick = function() {
                this.checked = false;
                return false;
            }
            col.title = 'select a primary key';
            td.appendChild(col);
            col = document.createElement('input');
            col.type = 'checkbox';
            col.id = 'notNull';
            col.name = 'notNull';
            col.title = 'check if column should be given \n a value upon new row.';
            td.appendChild(col);
            tr.appendChild(td);
        }
        table.appendChild(tr);
        tableBlock.appendChild(table);
        var maxRs = document.createElement('input');
        maxRs.type = 'text';
        maxRs.size = '5';
        maxRs.title = 'enter max no. of rows in table \n enter only 9\'s';
        maxRs.value = '999'
        var cTB = document.createElement('input');
        cTB.type = 'submit';
        cTB.value = 'CreateTable';
        tableBlock.appendChild(cTB);
        tableBlock.onsubmit = function(evt) {
            var elm;
            if (evt && evt != window.event) {
                elm = evt;
            } else {
                evt = (window.event) ? window.event : null;
                elm = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
            }
            statusField.innerHTML = 'Creating table... ~:{~'
            var dTE = elm.parentElement;
            var tableName = dbTableExecuter.tableProperties.tableName;
            var tableBlock = dTE.children['tableBlock'];
            var tableTemplate = tableBlock.children['tableTemplate'];
            var rows = tableTemplate.rows;
            var rowCount = rows.length;
            var columnCount = rows[0].cells.length;
            var feed = new Object();
            var content = "tableOperation=createTable&dbTable=" + tableName + "&maxRs=" + maxRs.value.length;
            var column = '';
            var columns = [];
            for (var i = 0; i < columnCount; i++) {
                column = '';
                if (rows[0].cells[i].children['colName'].value.length > 16) {
                    alert('column name length should not exceed 16 characters.');
                    rows[0].cells[i].children['colName'].focus();
                    return false
                }
                column += rows[0].cells[i].children['colName'].value + ' ';
                if (rows[0].cells[i].children['type'].value == '') {
                    alert('Select a data type for the column');
                    rows[0].cells[i].children['type'].focus();
                    return false;
                }
                column += rows[0].cells[i].children['type'].value;
                if (rows[0].cells[i].children['size'].value != '')
                    column += '(' + rows[0].cells[i].children['size'].value + ')';
                if (rows[0].cells[i].children['primaryKey'].checked) {
                    column += ' PRIMARY KEY';
                }
                column += rows[0].cells[i].children['notNull'].checked ? ' NOT NULL' : " NULL";
                if (rows[0].cells[i].children['default'].value != '') {
                    column += " DEFAULT '" + rows[0].cells[i].children['default'].value + "'";
                }
                columns[columns.length] = column;
            }
            content += '&columns=' + columns.join(',');
            feed.content = {
                tableOperation: 'createTable',
                dbTable: tableName,
                maxRs: maxRs.value.length,
                columns: columns.join(',')
            }
            feed.dTEF = dTE.parentElement.parentElement.parentElement;
            feed.tableName = tableName;
            feed.postExpedition = function(feed) {
                if (feed.responseXML) {
                    if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'success') {
                        statusField.innerHTML = 'Table created successfully ~B-)~';
                        feed.dTEF.children['tableName'].value = feed.tableName;
                        if (dbTableExecuterTool) {
                            dbTableExecuterTool.loadGadget(feed.dTEF)
                        } else {
                            location.href = document.baseURI + 'dbTableExcuterForm.php?dbTable=' + feed.tableName;
                        }
                    } else {
                        statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].textContent;
                    }
                }
            };
            feed.ferry = new core.shuttle('lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
            return false;
        }
        return false;
    },
    delTable: function(evt) {
        var elm;
        if (evt && evt != window.event) {
            elm = evt;
        } else {
            evt = window.event ? window.event : null;
            if (evt) {
                elm = evt.targetElement ? evt.targetElement : evt.srcElement;
            }
        }
        if (elm && confirm('Are u sure in deleting the table?')) {
            statusField.innerHTML = 'Deleting table...';
            var dbTable = dbTableExecuter.tableProperties.tableName;
            var content = 'tableOperation=delTable&dbTable=' + dbTable;
            var displayArea = dbTableExecuter.dTable.table.parentElement.parentElement;
            var feed = new Object();
            feed.dbTable = dbTable;
            feed.postExpedition = function(feed) {
                if (feed.responseXML) {
                    if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'success') {
                        dbTableExecuter.tableOp.delTable();
                        statusField.innerHTML = 'Table deleted successfully.';
                        displayArea.innerHTML = '';
                    } else {
                        statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].textContent;
                    }
                }
            }
            feed.content = {
                tableOperation: 'delTable',
                dbTable: dbTable
            }
            statusField.innerHTML = 'Deleting table...~:{~';
            feed.ferry = new core.shuttle('lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
        }
    },
    delRow: function(evt) {
        var elm;
        if (evt && evt != window.event) {
            elm = evt;
        } else {
            evt = window.event ? window.event : null;
            if (evt) {
                elm = evt.targetElement ? evt.targetElement : evt.srcElement;
            }
        }
        if (elm && confirm('Are u sure in deleting the Row?')) {
            var row = elm.parentElement.parentElement;
            var rowIndex = row.id;
            if (rowIndex == 'newRow') {
                row.nextSibling.cells[0].firstChild.disabled = false;
                row.parentElement.removeChild(row);
            } else {
                var content = "tableOperation=delRow&dbTable=" + dbTableExecuter.tableProperties.tableName + "&rowIndex=" + rowIndex + "&fColumn=" + dbTableExecuter.dTable.tHR.cells[1].id;
                var feed = new Object();
                feed.content = {
                    tableOperation: 'delRow',
                    dbTable: dbTableExecuter.tableProperties.tableName,
                    rowIndex: rowIndex,
                    fColumn: dbTableExecuter.dTable.tHR.cells[1].id
                }
                feed.row = row;
                feed.rowIndex = row.rowIndex;
                feed.postExpedition = function(feed) {
                    if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'success') {
                        dbTableExecuter.tableOp.delRow(feed.row);
                        dbTableExecuter.dTable.tbody.removeChild(row);
                        statusField.innerHTML = 'Row ' + feed.rowIndex + ' deleted.';
                    } else {
                        statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].textContent;
                    }
                }
                statusField.innerHTML = 'Deleting row...~:{~';
                feed.ferry = new core.shuttle('lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
            }
        }
    },
    rename: function(evt) {
        var elm;
        if (evt && evt != window.event) {
            elm = evt;
        } else {
            evt = window.event ? window.event : null;
            if (evt) {
                elm = evt.targetElement ? evt.targetElement : evt.srcElement;
            }
        }
        if (elm) {
            var preValue = elm.innerHTML;
            var cell = elm.parentElement.parentElement;
            var f = document.createElement('form');
            f.init = elm;
            f.id = elm.id;
            f.cIndex = cell.cellIndex;
            elm.parentElement.replaceChild(f, elm);
            if (f.id == 'colName') {
                f.colName = document.createElement('input');
                f.colName.type = 'text';
                f.colName.id = 'colName';
                f.colName.value = preValue;
                f.colName.init = preValue;
                f.colName.title = "Enter column name";
                f.colName.size = '9';
                f.colName.onclick = function() {
                    this.select();
                    return false;
                }
                f.appendChild(f.colName);
                f.appendChild(document.createElement('br'));
                f.type = document.createElement('select');
                f.type.innerHTML = "<option value=''>SelectType</option><option value='int'>INT</option><option value='varchar'>VARCHAR</option><option value='timestamp'>TIMESTAMP</option><option value='enum'>ENUM</option>";
                f.type.id = 'type';
                f.type.title = 'select data type';
                var type = dbTableExecuter.tableProperties.Type[cell.cellIndex];
                var i = 0;
                var otype = '';
                while (type[i] != '(' && type[i] != null) {
                    otype += type[i];
                    i++;
                }
                var ol = '';
                i++;
                while (type[i] != ')' && type[i] != null) {
                    ol += type[i];
                    i++;
                }
                f.type.value = otype;
                f.type.init = otype;
                f.appendChild(f.type);
                f.appendChild(document.createElement('br'));
                f.size = document.createElement('input');
                f.size.type = 'text';
                f.size.id = 'size';
                f.size.value = ol;
                f.size.init = ol;
                f.size.title = "Enter size, if enum enter values in single quotes seperated by ','\n eg.:'ORANGE','WHITE','GREEN','blue'";
                f.size.size = '20';
                f.size.onclick = function() {
                    this.select();
                    return false;
                }
                f.appendChild(f.size);
                f.appendChild(document.createElement('br'));
                f.dflt = document.createElement('input');
                f.dflt.type = 'text';
                f.dflt.value = dbTableExecuter.tableProperties.Default[cell.cellIndex];
                f.dflt.init = f.dflt.value;
                f.dflt.title = "Enter a default value if any \nelse leave it blank";
                f.dflt.size = '20';
                f.dflt.onclick = function() {
                    this.select();
                    return false;
                }
                f.appendChild(f.dflt);
                f.appendChild(document.createElement('br'));
                f.notNull = document.createElement('input');
                f.notNull.type = 'checkbox';
                f.notNull.id = 'notNull';
                f.notNull.name = 'notNull';
                f.notNull.title = 'check if column should be given \n a value upon new row.';
                if (dbTableExecuter.tableProperties.Null[cell.cellIndex] == 'NO') {
                    f.notNull.checked = true;
                }
                f.notNull.init = f.notNull.checked;
                f.appendChild(f.notNull);
                var col = document.createElement('input');
                col.type = 'submit';
                col.id = 'editColBtn';
                col.value = 'EditColumn';
                f.appendChild(col);
            
            } else if (f.id == 'tableName') {
                f.colName = document.createElement('input');
                f.colName.type = 'text';
                f.colName.id = 'tableName';
                f.colName.value = preValue;
                f.colName.init = preValue;
                f.colName.title = "Enter column name";
                f.colName.size = '9';
                f.colName.onclick = function() {
                    this.select();
                    return false;
                }
                f.appendChild(f.colName);
            }
            f.cE = document.createElement('img');
            f.cE.id = 'cancelInsert';
            f.cE.className = 'ibtn';
            f.cE.src = 'images/-.png';
            f.cE.elm = elm;
            f.cE.f = f;
            f.cE.onclick = function() {
                this.f.parentElement.replaceChild(this.elm, this.f);
                return false;
            }
            f.cE.title = 'cancel edit';
            f.appendChild(f.cE);
            f.firstChild.focus();
            f.firstChild.select();
            f.onsubmit = function(evt) {
                var elm;
                if (evt && evt != window.event) {
                    elm = evt;
                } else {
                    evt = window.event ? window.event : null;
                    if (evt) {
                        elm = evt.targetElement ? evt.targetElement : evt.srcElement;
                    }
                }
                evt.preventDefault();
                if ((this.id == 'colName' && (this.colName.value != this.colName.init || this.type.value != this.type.init || this.size.value != this.size.init || this.dflt.value != this.dflt.init || this.notNull.checked != this.notNull.init)) || (this.id == 'tableName' && this.colName.value != this.colName.init)) {
                    var feed = new Object();
                    var content = 'tableOperation=rename' + this.id + '&dbTable=' + dbTableExecuter.tableProperties.tableName + '&newName=' + this.colName.value;
                    feed.content = {
                        tableOperation: 'rename' + this.id,
                        dbTable: dbTableExecuter.tableProperties.tableName,
                        newName: this.colName.value
                    }
                    if (elm.id == 'colName') {
                        content += '&colName=' + this.colName.init + '&type=' + this.type.value + '&size=' + this.size.value + '&notNull=' + this.notNull.checked + '&dfault=' + this.dflt.value;
                        feed.content.colName = this.colName.init;
                        feed.content.type = this.type.value;
                        feed.content.size = this.size.value;
                        feed.content.notNull = this.notNull.checked;
                        feed.content.dfault = this.dflt.value;
                    }
                    feed.elm = this;
                    feed.postExpedition = function(feed) {
                        if (feed.responseXML.getElementsByTagName('status')[0].textContent == 'success') {
                            if (feed.elm.id == 'tableName') {
                                dbTableExecuter.tableProperties.tableName = feed.elm.colName.value;
                            } else if (feed.elm.id == 'colName') {
                                if (feed.elm.colName.value != feed.elm.colName.init) {
                                    dbTableExecuter.table[feed.elm.colName.value] = dbTableExecuter.table[feed.elm.colName.init];
                                    delete dbTableExecuter.table[feed.elm.colName.init];
                                    if (dbTableExecuter.tableProperties.authorization != '*') {
                                        dbTableExecuter.tableHash[feed.elm.colName.value] = dbTableExecuter.tableHash[feed.elm.colName.init];
                                        delete dbTableExecuter.tableHash[feed.elm.colName.init];
                                    }
                                    feed.elm.parentElement.parentElement.id = feed.elm.colName.value;
                                }
                                dbTableExecuter.tableProperties.Type[feed.elm.cIndex] = feed.elm.type.value + '(' + feed.elm.size.value + ')';
                                dbTableExecuter.tableProperties.MaxLength[feed.elm.cIndex] = feed.elm.size.value;
                                dbTableExecuter.tableProperties.Size[feed.elm.cIndex] = feed.elm.size.value > 20 ? 20 : feed.elm.size.value;
                                dbTableExecuter.tableProperties.Null[feed.elm.cIndex] = feed.elm.notNull.checked ? 'NO' : 'YES';
                                dbTableExecuter.tableProperties.Default[feed.elm.cIndex] = feed.elm.dflt.value;
                                dbTableExecuter.dTable.tHR.cells[feed.elm.cIndex].cellEditTemp = dbTableExecuter.cellEditTempGen(feed.elm.cIndex);
                            }
                            feed.elm.init.innerHTML = feed.elm.colName.value;
                            feed.elm.parentElement.replaceChild(feed.elm.init, feed.elm);
                            statusField.innerHTML = 'Edit completed ~e/~.';
                        } else {
                            feed.elm.parentElement.replaceChild(feed.elm.init, feed.elm);
                            statusField.innerHTML = feed.responseXML.getElementsByTagName('status')[0].textContent;
                        }
                    }
                    feed.ferry = new core.shuttle('lib/superScripts/dbTableExecuter.php', content, feed.postExpedition, feed);
                } else {
                    elm.parentElement.replaceChild(elm.init, elm);
                }
            }
        }
    },
    scrollBy: function(pitch, goTo) {
        pitch = pitch ? pitch : dbTableExecuter.vRowCount;
        var rows = dbTableExecuter.dTable.table.rows;
        var hrc = dbTableExecuter.dTable.thead.rows.length;
        var tRI = hrc + dbTableExecuter.tableProperties.rowCount;
        var vInd = dbTableExecuter.dTable.tbody.getElementsByClassName('vInd')[0];
        var lwr = vInd.rowIndex;
        var nlwr = null;
        if (goTo == undefined) {
            nlwr = lwr + pitch;
        } else if (goTo == 'START') {
            nlwr = hrc;
        } else if (goTo == 'END') {
            nlwr = tRI - pitch;
        } else if (goTo >= 0) {
            nlwr = goTo + hrc;
        }
        nlwr = nlwr > hrc ? nlwr : hrc;
        nlwr = nlwr > tRI - pitch ? tRI - pitch : nlwr;
        nlwr = nlwr > hrc ? nlwr : hrc;
        if (lwr == nlwr) {
            for (var i = 0; i < tRI; i++) {
                if (rows[i].offsetWidth > 0) {
                    lwr = i;
                    break;
                }
            }
        }
        if (lwr != nlwr) {
            for (var i = hrc; i < tRI; i++) {
                rows[i].style.display = 'none';
            }
            var upr = nlwr + Math.abs(pitch);
            upr = upr > tRI ? tRI : upr;
            var r = null;
            for (i = nlwr; i < upr; i++) {
                r = rows[i];
                if (!r.hide)
                    r.style.display = null
            }
            vInd.classList.remove('vInd');
            rows[nlwr].classList.add('vInd');
        }
    },
    specifyRows: function() {
        var rowSelectors = dbTableExecuter.dTable.table.getElementsByClassName('row selector');
        if (!dbTableExecuter.dTable.table.classList.contains('rowSelection')) {
            dbTableExecuter.dTable.table.classList.add('rowSelection');
            for (var i = 0; i < rowSelectors.length; i++) {
                rowSelectors[i].style.display = null;
            }
        }
        rowSelectors[0].focus();
        statusField.innerHTML = "select rows";
        return false;
    },
    activateCell: function() {
        if (!this.classList.contains('active')) {
            var ac = dbTableExecuter.dTable.tbody.getElementsByClassName('active');
            for (var i = 0; ac.length != 0; ) {
                ac[i].classList.remove('active');
            }
            this.classList.add('active');
            if (this.children.length == 0) {
                this.focus();
            } else {
                this.children[0].focus();
            }
        }
    },
    genTable: function(dA) {
        var tg = this;
        tg.displayArea = dA;
        tg.table = document.createElement('table');
        tg.table.view = 'page';
        var tr = null;
        var td = null;
        var th = null;
        tg.thead = document.createElement('thead');
        tg.tbody = document.createElement('tbody');
        tg.table.appendChild(tg.thead);
        tg.table.appendChild(tg.tbody);
        tr = document.createElement('tr');
        for (var j in dbTableExecuter.table) {
            th = document.createElement('th');
            var k;
            for (k = 0; k < dbTableExecuter.tableProperties.Field.length; k++) {
                if (dbTableExecuter.tableProperties.Field[k] == j) {
                    break;
                }
            }
            if (j == 'index' && dbTableExecuter.tableProperties.authorization == '*') {
                th.id = j;
                th.innerHTML = "<div id='colName'></div><img id='delTableBtn' class='del ibtn' title='Delete table!' onclick='dbTableExecuter.delTable();return false;' src=\"images/-.png\"/><br/><span><img id=\"dispAuthorityBtn\" class='ibtn' title=\"Execute authority on columns\" onclick=\"if(this.msgPanel){this.msgPanel.activate();}else{this.msgPanel=new dbTableExecuter.authorityBlock();} return false;\" src=\"images/$.png\"/></span>";
                th.className = 'unsortable';
                th.rowSelector = new core.animatedImage(['images/checkbox.png', 'images/checkboxHover.png', 'images/checkboxMD.png', 'images/checkboxC.png', 'images/checkboxCHover.png', 'images/checkboxCMD.png'], core.selectAll);
                th.rowSelector.trigMouseEvt();
                th.rowSelector.id = 'rowSelector';
                th.rowSelector.style.display = 'none';
                th.rowSelector.className = 'all row selector';
                th.rowSelector.click = false;
                th.rowSelector.dblclick = false;
                th.rowSelector.title = 'click to check visible rows\ndoubleClick to check all rows';
                th.rowSelector.__proto__.selected = false;
                th.rowSelector.__proto__.allSelected = false;
                th.rowSelector.style.border = '1px solid transparent';
                th.colSelector = new core.animatedImage(['images/checkbox.png', 'images/checkboxHover.png', 'images/checkboxMD.png', 'images/checkboxC.png', 'images/checkboxCHover.png', 'images/checkboxCMD.png'], core.selectAll);
                th.colSelector.trigMouseEvt();
                th.colSelector.id = 'colSelector';
                th.colSelector.style.display = 'none';
                th.colSelector.className = 'all col selector';
                th.colSelector.selected = false;
                th.colSelector.allSelected = false;
                th.colSelector.click = false;
                th.colSelector.dblclick = false;
                th.colSelector.title = 'click to check visible rows\ndoubleClick to check all rows';
                th.appendChild(th.colSelector.__proto__);
                th.appendChild(th.rowSelector.__proto__);
                th.style.textAlign = 'center';
                tr.appendChild(th);
            } else if (j != 'index') {
                th.innerHTML = "<div id='colName' draggable='true' ondrag=\"core.scrollOnDragToEdge.call(this);\" ondragstart=\"event.dataTransfer.setData('text/columnName',event.target.innerHTML);\" ondblclick='dbTableExecuter.rename(); return false;'>" + j + "</div>" + (dbTableExecuter.tableProperties.authorization == '*' ? "<img id=\"newColumnBtn\" class='ibtn' title=\"Insert new Column\" onclick=\"dbTableExecuter.newColumn(); return false;\" src=\"images/+.png\"><img id=\"delColumnBtn\" class='ibtn' title=\"Delete column\" onclick=\"dbTableExecuter.delColumn(); return false;\" src=\"images/-.png\"/><img id=\"dispAuthorityBtn\" class='ibtn' title=\"Execute authority on columns\" onclick=\"if(this.msgPanel){this.msgPanel.activate();}else{this.msgPanel=new dbTableExecuter.authorityBlock();} return false;\" src=\"images/$.png\"/>" : '');
                th.selector = document.createElement('input');
                th.selector.type = 'checkbox';
                th.selector.id = 'selector';
                th.selector.className = 'col selector';
                th.selector.style.display = 'none';
                th.appendChild(th.selector);
                th.cellEditTemp = dbTableExecuter.cellEditTempGen(k);
                th.id = j;
                //th.colName=th.children['colName'];
                //th.colName.ondrag=core.scrollOnDragToEdge;
                tr.appendChild(th);
            }
        }
        if (dbTableExecuter.tableProperties.authorization == '*') {
            th = document.createElement('th');
            th.className = 'unsortable';
            th.innerHTML = '<span><img id="newColumnBtn" class="ibtn" title="Append a new Column" onclick="dbTableExecuter.newColumn(); return false;" src=\"images/+.png\"/></span>';
            tr.appendChild(th);
        }
        tg.tHR = tr;
        tg.thead.appendChild(tr);
        tr = document.createElement('tr');
        tr.classList.add('vInd');
        for (var i = 0; i < dbTableExecuter.tableProperties.rowCount; i++) {
            for (var j in dbTableExecuter.table) {
                td = document.createElement('td');
                if (j == 'index' && dbTableExecuter.tableProperties.authorization == '*') {
                    td.id = dbTableExecuter.table[j][i];
                    td.innerHTML = "<img id='delRowBtn' title='Delete row " + td.id + "' class='del rowDeleter ibtn' onclick='dbTableExecuter.delRow();return false;' src=\"images/-.png\"/>";
                    td.style.textAlign = 'center';
                    tr.appendChild(td);
                    tr.selector = document.createElement('input');
                    tr.selector.type = 'checkbox';
                    tr.selector.id = 'selector';
                    tr.selector.value = td.id;
                    tr.selector.className = 'row selector';
                    tr.selector.style.display = 'none';
                    td.appendChild(tr.selector);
                    tr.id = td.id;
                    td.title = td.id
                } else if (j == 'index') {
                    tr.id = dbTableExecuter.table[j][i];
                } else {
                    td.innerHTML = dbTableExecuter.table[j][i];
                    td.onmousedown = dbTableExecuter.activateCell;
                    td.className = "dc";
                    td.tabIndex = '0';
                    td.onkeydown = dbTableExecuter.cellNavHandler;
                    td.ondblclick = dbTableExecuter.editCell;
                    tr.appendChild(td);
                }
            }
            if (i >= dbTableExecuter.vRowCount)
                tr.style.display = 'none';
            tg.tbody.appendChild(tr);
            tr = document.createElement('tr');
        }
        i = 0;
        for (var j in dbTableExecuter.table) {
            td = document.createElement('td');
            if (j == 'index' && dbTableExecuter.tableProperties.authorization == '*') {
                tr.id = 'newVRow';
                tr.appendChild(td);
                td.style.textAlign = 'center';
                td.innerHTML = "<img id='appendRowBtn' title='Append row' class='add ibtn' onclick='dbTableExecuter.appendRow();return false;' src=\"images/+.png\"/>";
            } else if (j != 'index') {
                if (i == 1) {
                    td.innerHTML = "<img id='left' title='previous rows' class='ibtn' onclick=\"dbTableExecuter.scrollBy(dbTableExecuter.vRowCount,'START');return false;\" src='images/i60.png'/>&nbsp;&nbsp;<img id='left' title='previous rows' class='ibtn' onclick='dbTableExecuter.scrollBy(-dbTableExecuter.vRowCount);return false;' src='images/i60.png'/>&nbsp;<img id='right' class='ibtn' title='next rows' onclick='dbTableExecuter.scrollBy(dbTableExecuter.vRowCount);return false;' src='images/i62.png'/>&nbsp;&nbsp;<img id='right' class='ibtn' title='next rows' onclick=\"dbTableExecuter.scrollBy(dbTableExecuter.vRowCount,'END');return false;\" src='images/i62.png'/>";
                    td.style.textAlign = 'center';
                    tr.appendChild(td);
                } else {
                    td.style.display = 'none';
                    tr.appendChild(td);
                }
            }
            i++;
        }
        tr.className = 'sortbottom';
        tg.tbody.appendChild(tr);
        tg.table.appendChild(tg.tbody);
        tg.table.id = 'dbTableViewer';
        tg.table.className = 'sortable';
        tg.displayArea.appendChild(tg.table);
        sortable.ts_makeSortable(tg.table);
        tg.table.rowWithId = function(id) {
            var rows = dbTableExecuter.dTable.table.rows;
            for (var i = 0; i < rows.length; i++) {
                if (rows[i].id == id) {
                    return rows[i];
                }
            }
            return undefined;
        }
    },
    cellNavHandler: function() {
        var cell;
        var row;
        var tbody;
        event.cancelBubble = true;
        if (event.keyCode == 40) {
            cell = this;
            row = cell.parentElement;
            tbody = row.parentElement;
            var nxtCell = row.nextSibling.cells[cell.cellIndex];
            if (nxtCell && nxtCell.classList.contains('dc')) {
                if (tbody.rows[row.rowIndex].style.display == 'none') {
                    tbody.rows[row.rowIndex].style.display = null;
                    tbody.rows[row.rowIndex - dbTableExecuter.vRowCount].style.display = 'none';
                }
                dbTableExecuter.activateCell.call(nxtCell);
            }
        } else if (event.keyCode == 38) {
            cell = this;
            row = cell.parentElement;
            tbody = row.parentElement;
            if (row.rowIndex > 1)
                var nxtCell = tbody.rows[row.rowIndex - 2].cells[cell.cellIndex];
            if (nxtCell && nxtCell.classList.contains('dc')) {
                if (tbody.rows[row.rowIndex - 2].style.display == 'none') {
                    tbody.rows[row.rowIndex - 2].style.display = null;
                    tbody.rows[row.rowIndex - 2 + dbTableExecuter.vRowCount].style.display = 'none';
                }
                dbTableExecuter.activateCell.call(nxtCell);
            }
        } else if (event.keyCode == 39) {
            cell = this;
            row = cell.parentElement;
            tbody = row.parentElement;
            var nxtCell = row.cells[this.cellIndex + 1];
            if (nxtCell && nxtCell.classList.contains('dc')) {
                dbTableExecuter.activateCell.call(nxtCell);
            }
        } else if (event.keyCode == 37) {
            cell = this;
            row = cell.parentElement;
            tbody = row.parentElement;
            var nxtCell = row.cells[this.cellIndex - 1];
            if (nxtCell && nxtCell.classList.contains('dc')) {
                dbTableExecuter.activateCell.call(nxtCell);
            }
        } else if (event.keyCode == 13) {
            event.preventDefault();
            cell = this;
            row = cell.parentElement;
            tbody = row.parentElement;
            dbTableExecuter.editCell(this);
        }
    },
    cellEditTempGen: function(colIndex) {
        var cellEditTemp;
        if (/enum/.test(dbTableExecuter.tableProperties.Type[colIndex])) {
            var options = [];
            var i = 0;
            var size = dbTableExecuter.tableProperties.Size[colIndex];
            options = size.split(',');
            for (i = 0; i < options.length; i++) {
                options[i] = options[i].slice(1, options[i].length - 1);
            }
            cellEditTemp = dbTableExecuter.genSelectBox(options)
        } else {
            cellEditTemp = document.createElement('input')
            cellEditTemp.type = 'text';
            cellEditTemp.maxlength = dbTableExecuter.tableProperties.MaxLength[colIndex];
            cellEditTemp.size = dbTableExecuter.tableProperties.Size[colIndex];
        }
        cellEditTemp.setAttribute('value', dbTableExecuter.tableProperties.Default[colIndex]);
        cellEditTemp.value = dbTableExecuter.tableProperties.Default[colIndex];
        cellEditTemp.classList.add('inputCell');
        return cellEditTemp;
    },
    tableOp: {
        updateCell: function(feed) {
            var cell = feed.cell;
            var cellRow = cell.parentElement;
            if (cellRow.id == 'newRow') {
                if (dbTableExecuter.tableProperties.authorization != '*') {
                    var hashes = feed.responseXML.getElementsByTagName('hashes')[0].childNodes;
                    var hashCol = null;
                    var tCol = null;
                    feed.rowIndex = feed.responseXML.getElementsByTagName('newRowIndex')[0].firstChild.nodeValue;
                    for (var i = 0; i < hashes.length; i++) {
                        hashCol = dbTableExecuter.tableHash[hashes[i].tagName]
                        hashCol[hashCol.length] = hashes[i].firstChild.nodeValue;
                        tCol = dbTableExecuter.table[hashes[i].tagName];
                        if (hashes[i].tagName == 'index') {
                            tCol[tCol.length] = feed.rowIndex;
                        } else {
                            tCol[tCol.length] = '';
                        }
                    }
                } else {
                    for (i in dbTableExecuter.table) {
                        if (i == 'index') {
                            dbTableExecuter.table[i][dbTableExecuter.table[i].length] = feed.rowIndex;
                        } else {
                            dbTableExecuter.table[i][dbTableExecuter.table[i].length] = '';
                        }
                    }
                }
            }
            for (var i = 0; i < dbTableExecuter.table.index.length; i++) {
                if (dbTableExecuter.table.index[i] == feed.rowIndex) {
                    for (var j = 0; j < feed.colInd.length; j++) {
                        dbTableExecuter.table[feed.colInd[j]][i] = feed.val[j];
                    }
                }
            }
        },
        delColumn: function(colName) {
            var colIndex = dbTableExecuter.dTable.tHR.cells[colName].cellIndex;
            dbTableExecuter.table[colName] = null;
            var cell;
            for (var i = 0; i < dbTableExecuter.dTable.table.rows.length - 1; i++) {
                cell = dbTableExecuter.dTable.table.rows[i].cells[colIndex];
                cell.parentElement.removeChild(cell);
            }
            delete dbTableExecuter.table[colName];
            delete dbTableExecuter.tableHash[colName];
            delete dbTableExecuter.tableProperties.Field[colIndex];
            delete dbTableExecuter.tableProperties.Null[colIndex];
            delete dbTableExecuter.tableProperties.Type[colIndex];
            dbTableExecuter.tableProperties.colCount -= 1;
        },
        insColumn: function(colName, insAftCol, feed) {
            var inAftColIndex = dbTableExecuter.dTable.tHR.cells[insAftCol].cellIndex;
            var newColHead = dbTableExecuter.dTable.tHR.cells[insAftCol].nextSibling;
            newColHead.innerHTML = (insAftCol != 'index' ? dbTableExecuter.dTable.tHR.cells[insAftCol].innerHTML : dbTableExecuter.dTable.tHR.cells[insAftCol].nextSibling.nextSibling.innerHTML);
            newColHead.firstChild.firstChild.innerHTML = colName;
            newColHead.id = colName;
            newColHead.getElementsByTagName('a')[0].onclick = function() {
                window.ts_resortTable(this, newColHead.cellIndex);
                return false;
            }
            var td = null;
            if (dbTableExecuter.tableProperties.authorization != '*')
                var hashes = feed.responseXML.getElementsByTagName('hash');
            dbTableExecuter.table[colName] = [];
            dbTableExecuter.tableHash[colName] = [];
            for (var i = 0; i < dbTableExecuter.tableProperties.rowCount; i++) {
                td = dbTableExecuter.dTable.tbody.rows[i].cells[inAftColIndex].nextSibling;
                td.ondblclick = dbTableExecuter.editCell;
                dbTableExecuter.table[colName][i] = '';
            }
            if (dbTableExecuter.tableProperties.authorization != '*') {
                for (var i = 0; i < dbTableExecuter.tableProperties.rowCount; i++) {
                    dbTableExecuter.tableHash[colName][i] = hashes[i].firstChild.nodeValue;
                }
            }
            dbTableExecuter.tableProperties.Field.splice(inAftColIndex + 1, 0, colName);
            dbTableExecuter.tableProperties.Type.splice(inAftColIndex + 1, 0, feed.type + '(' + feed.maxSize + ')');
            dbTableExecuter.tableProperties.Null.splice(inAftColIndex + 1, 0, feed.nul);
            dbTableExecuter.tableProperties.MaxLength.splice(inAftColIndex + 1, 0, feed.maxSize);
            dbTableExecuter.tableProperties.Key.splice(inAftColIndex + 1, 0, feed.key);
            dbTableExecuter.tableProperties.Size.splice(inAftColIndex + 1, 0, feed.maxSize > 20 ? 20 : feed.maxSize);
            dbTableExecuter.tableProperties.Default.splice(inAftColIndex + 1, 0, feed.dflt);
            var cells = dbTableExecuter.dTable.tHR.cells;
            cells[inAftColIndex + 1].cellEditTemp = dbTableExecuter.cellEditTempGen(inAftColIndex + 1);
            dbTableExecuter.tableProperties.colCount += 1;
            for (var i = 1; i < cells.length; i++) {
                cells[i].children[0].children['newColumnBtn'].disabled = false;
                cells[i].children[0].children['newColumnBtn'].title = 'Insert Column';
                try {
                    cells[i].children[0].children['delColumnBtn'].disabled = false;
                    cells[i].children[0].children['delColumnBtn'].title = 'Delete Column';
                } catch (e) {
                }
                ;
            }
        },
        delRow: function(row) {
            var rIndex = null;
            for (var i in dbTableExecuter.table.index) {
                if (dbTableExecuter.table.index[i] == row.id)
                    rIndex = i;
            }
            var vtable = dbTableExecuter.table;
            for (var i in vtable) {
                delete dbTableExecuter.table[i][rIndex];
                if (dbTableExecuter.tableProperties.authorization != '*')
                    delete dbTableExecuter.tableHash[i][rIndex];
            }
            dbTableExecuter.tableProperties.rowCount -= 1;
            return false;
        },
        delTable: function() {
            dbTableExecuter.table = null;
            dbTableExecuter.tableProperties = null;
            dbTableExecuter.tableHash = null;
            dbTableExecuter.dTable = null;
        }
    },
    genSelectBox: function(options) {
        var sb = document.createElement('select');
        var opt = null;
        options[-1] = '';
        for (var i = -1; i < options.length; i++) {
            opt = document.createElement('option');
            opt.value = options[i]
            opt.innerHTML = options[i]
            sb.appendChild(opt);
        }
        return sb;
    },
    searchTable: function(searchStr, tbody, options) {
        if (!options)
            options = {};
        if (!options.wolS)
            options.subS = true;
        if (!options.matchCase)
            options.caseLess = true;
        var sStr = searchStr.toLowerCase();
        var rows = tbody.rows;
        var rowCount = dbTableExecuter.tableProperties.rowCount;
        var cellCount = rows[0].cells.length;
        try {
            var searchIndCell = tbody.getElementsByClassName('SI')[0];
            var cellInd = searchIndCell.cellIndex;
            var rowInd = searchIndCell.parentElement.rowIndex - dbTableExecuter.dTable.thead.rows.length;
        } catch (e) {
        }
        var rowInd = rowInd >= 0 ? rowInd : 0;
        var cellInd = cellInd >= 0 ? cellInd + 1 : 0;
        var row = rows[rowInd];
        var cells = row.cells;
        var lastRow = rowCount - 1;
        var ri = rowInd;
        var ci = cellInd;
        for (var i = ri; i < rowCount; i++) {
            row = rows[i];
            cells = row.cells;
            for (var j = ci; j < cellCount; j++) {
                if ((options.subS && ((options.caseLess && cells[j].innerHTML.toLowerCase().indexOf(sStr) > -1) || (options.matchCase && cells[j].innerHTML.indexOf(searchStr) > -1))) || (options.wolS && ((options.caseLess && cells[j].innerHTML.toLowerCase() == sStr) || (options.matchCase && cells[j].innerHTML == searchStr)))) {
                    if (!(cells[j].offsetWidth > 0))
                        dbTableExecuter.scrollBy(dbTableExecuter.vRowCount, i);
                    cells[j].classList.add('SI');
                    cells[j].style.backgroundColor = '#ffff55';
                    cells[j].scrollIntoViewIfNeeded();
                    if (searchIndCell && searchIndCell != cells[j]) {
                        searchIndCell.classList.remove('SI');
                        searchIndCell.style.backgroundColor = null;
                    }
                    var found = true;
                    break;
                }
            }
            ci = 0;
            if (found)
                break;
            else if (i == lastRow && searchIndCell && !loop) {
                i = -1;
                rowCount = ri + 1;
                var loop = true
            }
        }
        if (found)
            statusField.innerHTML = 'Search completed... Found ~:)~';
        else
            statusField.innerHTML = 'Search completed... Not found ~:|~';
    },
    copyCells: function() {
        var h = selectedElements.getElementsByTagName('th');
        var tb = document.createElement('table');
        if (h.length > 0) {
            var th = document.createElement('tr');
            var tdh;
            for (i = 0; i < h.length; i++) {
                tdh = h[i].cloneNode();
                tdh.innerHTML = h[i].textContent;
                th.appendChild(tdh);
            }
            tb.appendChild(th);
        }
        var r = selectedElements.getElementsByTagName('td');
        var k = 0;
        for (var i = 0; i < r.length; i++) {
            if (k != r[i].parentElement.rowIndex) {
                k = r[i].parentElement.rowIndex;
                if (tr)
                    tb.appendChild(tr);
                var tr = document.createElement('tr');
                tr.appendChild(r[i].cloneNode(true));
            } else {
                tr.appendChild(r[i].cloneNode(true));
            }
            r[i].classList.add('active');
        }
        if (tr)
            tb.appendChild(tr);
        clipBoard.innerHTML = '';
        clipBoard.appendChild(tb);
        core.popClipBoard('copy');
        statusField.innerHTML = 'Cells copied to clipBoard ~:)~';
    },
    pasteCells: function() {
        clipBoard.opType = 'clearCB';
        clipBoard.innerHTML = '';
        clipBoard.afterPaste = function() {
            if (clipBoard.firstChild.tagName == 'TABLE') {
                var trs = clipBoard.getElementsByTagName('tr');
                if (selectedElements[0].tagName == 'TD') {
                    if (selectedElements[0].classList.contains('dc')) {
                        td = selectedElements[0];
                    }
                } else if (selectedElements[0].classList.contains('inputCell')) {
                    td = selectedElements[0].parentElement;
                } else {
                    statusField.innerHTML = 'Paste cant be performed here ~:|~';
                    return false;
                }
                var ti = td.cellIndex;
                var tr = td.parentElement;
                var rn = trs.length;
                var cn = trs[0].children.length;
                var hk = false;
                for (var i = 0; i < cn; i++) {
                    if (dbTableExecuter.tableProperties.Key[ti + i] == 'PRI') {
                        hk = true;
                        break;
                    }
                }
                if (tr.parentElement.parentElement.rows.length - tr.rowIndex < rn && !hk) {
                    statusField.innerHTML = 'Insufficient row sapce ~:|~';
                    return false;
                }
                if (tr.cells.length - td.cellIndex < cn) {
                    statusField.innerHTML = 'Insufficient column space ~:|~';
                    return false;
                }
                var value = [];
                var rv = [];
                var cH = [];
                var rCH = [];
                var cI = [];
                var rI = [];
                for (var i = 0; i < rn; i++) {
                    rv = [];
                    cH = [];
                    if ((tr && (tr.id == 'newRow' || tr.id == 'newVRow')) || !tr) {
                        rI.push('newRow');
                    } else {
                        rI.push(tr.id)
                    }
                    for (var j = 0; j < cn; j++) {
                        rv.push(trs[i].cells[j].innerHTML);
                        if (!cI.ok)
                            cI.push(dbTableExecuter.dTable.tHR.cells[td.cellIndex].id);
                        if (dbTableExecuter.tableHash[cI[j]]) {
                            cH.push(dbTableExecuter.tableHash[cI[j]][tr.rowIndex - 1]);
                        } else {
                            cH.push(null)
                        }
                        if (tr && tr.id != 'newRow' && tr.id != 'newVRow') {
                            td.newValue = rv[rv.length - 1];
                        }
                        if (tr && tr.id != 'newVRow') {
                            td = td.nextSibling;
                        }
                    }
                    cI.ok = true;
                    value.push(rv.join(','));
                    rCH.push(cH.join(','));
                    if (tr && tr.nextSibling.id != 'newVRow' && tr.nextSibling.id != 'newRow')
                        tr = tr.nextSibling;
                    else
                        tr = null;
                    if (tr)
                        td = tr.cells[ti];
                }
                var feed = {
                    content: {
                        dbTable: dbTableExecuter.tableProperties.tableName,
                        tableOperation: 'updateCell',
                        rowIndex: rI.join('@,$'),
                        cellHash: rCH.join('@,$'),
                        colIndex: "`" + cI.join("`,`") + "`",
                        value: value.join('@,$')
                    },
                    postExpedition: function(feed) {
                        if (feed.responseXML.getElementsByTagName('status').length == feed.rn) {
                            var rows = feed.responseXML.getElementsByTagName('row');
                            var ri = null;
                            for (var i = 0; i < rows.length; i++) {
                                if (rows[i].getElementsByTagName('status')[0].firstChild.nodeValue == 'success') {
                                    var nRI = rows[i].getElementsByTagName('newRowIndex')[0].firstChild.nodeValue;
                                    var r = dbTableExecuter.dTable.table.rowWithId(nRI);
                                    if (r) {
                                        for (var j = 0; j < feed.cn; j++) {
                                            r.cells[feed.ti + j].innerHTML = r.cells[feed.ti + j].newValue;
                                            r.cells[feed.ti + j].classList.add('active');
                                        }
                                    } else {
                                        var tr = document.createElement('tr');
                                        var values = rows[i].getElementsByTagName('value')[0].firstChild.nodeValue.split(',');
                                        var hashes = rows[i].getElementsByTagName('hashes')[0];
                                        var td = null;
                                        var cc = dbTableExecuter.tableProperties.colCount;
                                        var k = 0;
                                        for (var j = 1; j < cc; j++) {
                                            td = document.createElement('td');
                                            if (j >= feed.ti && j < feed.ti + feed.cn) {
                                                td.innerHTML = values[k];
                                                if (hashes)
                                                    td.hash = hashes.getElementsByTagName(dbTableExecuter.dTable.tHR.cells[j].id)[0].textContent;
                                                k++;
                                            }
                                            tr.appendChild(td);
                                            tr.id = nRI;
                                        }
                                        dbTableExecuter.appendRow(tr);
                                    }
                                } else {
                                    statusField.innerHTML = rows[i].getElementsByTagName('status')[0].firstChild.nodeValue;
                                }
                            }
                            statusField.innerHTML = 'pasteDone ~:)~';
                        }
                    }
                }
                feed.rn = rn;
                feed.cn = cn;
                feed.ti = ti;
                feed.trs = trs;
                feed.ferry = new core.shuttle("/lib/superScripts/dbTableExecuter.php", feed.content, feed.postExpedition, feed);
                statusField.innerHTML = 'Pasting cells ~@|~';
            }
        }
        core.popClipBoard('paste');
    }
}
window.dbTableExecuter = dbTableExecuter;
