<?php
include 'authorize.php';
include 'db_login.php';
$table = $_POST['tableName'];

//tableProperties generator

$result = mysql_query('describe ' . $table);
$fields_num = mysql_num_fields($result);
$table = null;
$fieldStr = "";
$typeStr = "";
$nullStr = "";
$keyStr = "";
$defaultStr = "";
$extraStr = "";
$rowCount = mysql_num_rows($result);
for ($i = 0; $i < $rowCount; $i++) {
    $fieldStr.="'" . mysql_result($result, $i, 'Field') . "',";
    $typeStr.="'" . mysql_result($result, $i, 'Type') . "',";
    $nullStr.="'" . mysql_result($result, $i, 'Null') . "',";
    $keyStr.="'" . mysql_result($result, $i, 'Key') . "',";
    $defaultStr = "'" . mysql_result($result, $i, 'Default') . "',";
    $extraStr = "'" . mysql_result($result, $i, 'Extra') . "',";
}
$fieldStr[strlen($fieldStr) - 1] = ' ';
$typeStr[strlen($typeStr) - 1] = ' ';
$nullStr[strlen($nullStr) - 1] = ' ';
$keyStr[strlen($keyStr) - 1] = ' ';
$defaultStr[strlen($defaultStr) - 1] = ' ';
$extraStr[strlen($extraStr) - 1] = ' ';
mysql_close();
?>
<html>
    <head>
        <title>MySQL Table Viewer</title>
        <script type="text/javascript" src="sortable.js"></script>
        <link rel="stylesheet" type="text/css" href="sortable.css"/>
        <script type="text/javascript">
            var tableProperties={
                Field:[<?php echo $fieldStr; ?>],
                Type:[<?php echo $typeStr; ?>],
                Null:[<?php echo $nullStr; ?>],
                Key:[<?php echo $keyStr; ?>],
                Default: [<?php echo $defaultStr; ?>],
                Extra: [<?php echo $extraStr; ?>],
                Maxlength:[],
                Size:[]
            };
    
            var maxLength = new Array();
            var field_size = tableProperties.Field.length;
            var field=tableProperties.Type;
            for (i = 0; i < field_size; i++) {
                var siz = field[i];
                var j = 0;
                var k = 0;
                maxLength[i] = "";
                while (siz[j - 1] != ')' && siz[j]!=null) {
                    if (siz[j] == '(') {
                        j++;
                        while (siz[j] != ')') {
                            maxLength[i]+= siz[j];
                            k++;
                            j++;
                        }
                    }
                    j++;
                }
            }
            var size=new Array();
            for(i=0;i<maxLength.length;i++) {
                if (maxLength[i] > 20)
                    size[i] = 20;else
                        size[i] = maxLength[i];
            }
            tableProperties.MaxLength=maxLength;
            tableProperties.Size=size;
            var  tableRowAppender=function(table){
                var tbody=table.children[1];
                var fieldsNum=tableProperties.Field.length;
                var tr=document.createElement('tr');
                for(i=3;i<fieldsNum;i++){
                    td=document.createElement('td');
                    td.ondblclick=editCell;
                    td.innerHTML="<input class= type=\"text\" onchange=\"if(window.changeEntry)changeEntry(this);else window.frames['docLoader'].changeEntry(this); return false;\" maxlength=\""+tableProperties.Maxlength[i]+"\" size=\""+tableProperties.Size[i]+"\"/>";
                    tr.appendChild(td);
                }
                tbody.appendChild(tr);
                tr.className='newRow';
            }
            function editTable(table){
                var maxlength=tableProperties.MaxLength;
                var size=tableProperties.Size;
                var tbody=table.children[1];
                var rowCount=tbody.children.length;
                var columnCount=tableProperties.Field.length;
                var cell=null;
                for(i=0;i<rowCount;i++){
                    var row=tbody.children[i];
                    for(j=3;j<columnCount;j++){
                        cell=row.cells[j-3];
                        if(cell.children.length==0){
                            cellValue=cell.innerHTML;
                            cell.innerHTML="<input type=\"text\" onchange=\"if(window.changeEntry)changeEntry(this);else window.frames['docLoader'].changeEntry(this); return false;\" maxlength=\""+maxlength[j]+"\" size=\""+size[j]+"\" value=\""+cellValue+"\"/>";
                        }
                    }
                }
            }
            function changeEntry(elm){
                var cell=elm.parentElement;
                var cellIndex=cell.cellIndex;
                var table=cell.parentElement.parentElement.parentElement;
                var tableId=table.id;
                var templateTable=table.nextSibling;
                var dbTable=table.previousSibling.innerHTML;
                var uniColumnIndex=null;
                var priColumnIndex=null;
                var value=elm.value;
                var columnCount=tableProperties.Field.length;
                var cellRow=cell.parentElement;
                for(i=0;i<columnCount;i++){
                    if(tableProperties.Key[i]=='UNI'){
                        uniColumnIndex=i;
                    }
                    if(tableProperties.Key[i]=='PRI'){
                        priColumnIndex=i;
                    }
                }
                editColumn=tableProperties.Field[cellIndex+3];
                uniColumn=tableProperties.Field[uniColumnIndex];
                if(cell.parentElement.children[uniColumnIndex].children[0]){
                    rowRefValue=cell.parentElement.children[uniColumnIndex].children[0].value;}
                else{rowRefValue=cell.parentElement.children[uniColumnIndex].innerHTML;}
                var content="dbTable="+dbTable+"&editColumn="+editColumn+"&value="+value+"&rowRefValue="+rowRefValue+"&referenceField="+tableProperties.Field[priColumnIndex];
                if(cellRow.className=='newRow'){
                    content+="&newRow=true";
                }
                window.parent.formAjaxPostMan('lib/cellUpdater.php',content,'updateDBTable',elm);
            }
            function editCell(evt){
                var cell;
                if(evt){cell=evt;}else{
                    evt = (window.parent.event) ? window.parent.event : null;}
                if (evt && !cell) {
                    cell = (evt.target) ? evt.target :
                        ((evt.srcElement) ? evt.srcElement : null);}
                if (cell ) {
                    if(cell.children.length==0){
                        var cellValue=cell.innerHTML;
                        var maxlength=tableProperties.MaxLength;
                        var size=tableProperties.Size;
                        var j=cell.cellIndex;
                        cell.innerHTML="<input type=\"text\" onchange=\"if(window.changeEntry)changeEntry(this);else window.frames['docLoader'].changeEntry(this); return false;\" maxlength=\""+maxlength[j+3]+"\" size=\""+size[j+3]+"\" value=\""+cellValue+"\"/>";
                        cell.children[0].focus();
                    }
                    cellValue=cell.innerHTML;
                }
            }
            
            function totalRow(cell){
                var total=0;
                var row=cell.parentElement;
                var sumLimit=row.cells.length-1;
                for(i=2;i<sumLimit;i++){
                    if(row.cells[i].children.length==0)
                        total+=row.cells[i].innerHTML;
                }
                row.cells[sumLimit].innerHTML=total;
            }
        </script>
    </head>
    <body onload="if(window.parent.iframeLoader)window.parent.iframeLoader('dbTableExecuter');"><div id="dbTableExecuter">
            <label class="userSpecific" id="docName"></label>
            <?php
            //include 'db_login.php';
            $table = '06ece41';
            // sending query
            $result = mysql_query("SELECT * FROM {$table}");
            if (!$result) {
                die("Query to show fields from table failed");
            }
            $fields_num = mysql_num_fields($result);
            $fields[] = NULL;
            echo "<h1 id='tableName' class='userSpecific'>{$table}</h1>";
            echo "<table border='1' class='sortable' id='dbTableViewer'><thead><tr>";

            // printing table headers
            for ($i = 0; $i < $fields_num; $i++) {
                $field = mysql_fetch_field($result);
                $fieldName = $field->name;
                $fields[$i] = $fieldName;
                if ($i > 2) {
                    echo "<th>{$fieldName}</th>";
                }
            }
            echo "</tr></thead>\n";
            echo "<tbody>";
// printing table rows
            while ($row = mysql_fetch_row($result)) {
                echo "<tr>";
                // $row is array... foreach( .. ) puts every element
                // of $row to $cell variable
                for ($i = 3; $i < $fields_num; $i++)
                    echo "<td ondblclick=\"window.frames['docLoader'].editCell();\">$row[$i]</td>";
                echo "</tr>\n";
            }
            echo "</tbody></table>";
            mysql_free_result($result);
            ?>
            <button onclick="if(window.editTable){editTable(document.getElementsByTagName('table')['dbTableViewer']);}else{window.frames['docLoader'].editTable(document.getElementsByTagName('table')['dbTableViewer']);} this.disabled=true; return false;">EditTable</button>
            <button onclick="if(window.tableRowAppender){tableRowAppender(document.getElementsByTagName('table')['dbTableViewer']);}else{window.frames['docLoader'].tableRowAppender(document.getElementsByTagName('table')['dbTableViewer']);} return false;">AppendRow</button>
        </div>
    </body>
</html>