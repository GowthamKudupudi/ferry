/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var formulaEvaluator={
    init:function(){
        ipPort.addEventListener("DOMSubtreeModified",formulaEvaluator.evaluate,false);
        for(var i=0;i<dTable.rows.length;i++){
            var row=dTable.rows[i];
            for(var j=0;j<row.cells.length;j++){
                var ut={};
                
                ut[row.id]={};
                
                ut[row.id][row.cells[j].id]=row.cells[j].innerHTML;
                ipPort.innerHTML=JSON.stringify({
                    rid:row.id,
                    cid:row.cells[i].id,
                    value:row.cells[i].innerHTML,
                    ut:ut
                });
            }
        }
    }
}

Math.ROUND=function(num, dec) {
    var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
    return result;
}

Math.AVERAGE=function(){
    var sum=0;
    for(var i=0;i<arguments.length;i++){
        sum+=arguments[i];
    }
    return sum/arguments.length;
}

Math.CONAVG=function(){
    var condition=arguments[arguments.length-1];
    var sum=0;
    var count=0;
    for(var i=0;i<arguments.length-1;i++){
        var exp=arguments[i]+condition;
        if(eval(exp)){
            sum+=arguments[i];
            count++;
        }
    }
    return sum/count;
}

Math.COUNTIF=function(){
    var condition=arguments[arguments.length-1];
    var count=0;
    for(var i=0;i<arguments.length-1;i++){
        var exp=arguments[i]+condition;
        if(eval(exp)){
            count++;
        }
    }
    return count;
}

Math.MAX=function(){
    var max=arguments[0]
    for(var i=1;i<arguments.length;i++){
        if(arguments[i]>max){
            max=arguments[i];
        }
    }
    return max;
}

Math.SUM=function(){
    var s=0;
    var a=arguments[0];
    for(var i=0;i<a.length;i++){
        s+=arguments[i];
    }
    return s;
}

self.onmessage=function(event){
    try{
        var fObj=JSON.parse(event.data);
        for(var v in fObj.variables){
            if(fObj.variables[v].__proto__==[].__proto__){
                this[v]=fObj.variables[v];
            }else{
                var k=parseFloat(fObj.variables[v]);
                var kvar=k.toString()!=fObj.variables[v]?"'"+fObj.variables[v]+"'":k;
                eval("var "+v+"="+kvar);
            }
        }
        for(var pi in fObj.oCellProps){
            var p=fObj.oCellProps[pi];
            var cp=fObj.cellProps[p];
            var r=eval(cp);
            k=parseFloat(r);
            k=k.toString()!=r?r:k
            cp=fObj.cellProps[p]=k||k==0?k:r;
            if(p=='innerHTML' && fObj.eVariable){
                var k=parseFloat(cp);
                var kvar=k.toString()!=cp?"'"+cp+"'":k;
                eval("var "+fObj.eVariable+"="+kvar)
            }
        }
        self.postMessage(JSON.stringify(fObj));
    }catch(e){
        fObj.error=e.message||e;
        self.postMessage(JSON.stringify(fObj));
    }
}