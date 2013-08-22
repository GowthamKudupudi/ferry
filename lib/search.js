/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


window.search={
    init:function(){
        statusField.innerHTML="Search completed ~:)~";
        var da=searchTool.children['displayArea'];
        da.style.position="relative";
        da.style.overflow='auto';
        var sdata=search.body.children['dbSearchContent'].children['sdata'];
        var sobj=JSON.parse(sdata.textContent);
        sdata.innerHTML='';
        searchTool.addEventListener("DOMSubtreeModified",search.reAlign,false);
        for(var t in sobj){
            if(sobj[t]=='END'){
                
            }else if(sobj[t]=='NEXT'){
                var nxtlnk=document.createElement('a');
                nxtlnk.innerHTML='More results';
                nxtlnk.onclick=search.fetchMoreResutls;
            }else{
                var tobj=sobj[t];
                var tdiv=document.createElement('div');
                tdiv.id=t;
                tdiv.innerHTML="<u>"+t+"</u>";
                var tbl=document.createElement('table');
                var tHR=document.createElement('tr');
                var r=tobj[0];
                for(var c in r){
                    var th=document.createElement('th');
                    th.id=c;
                    th.innerHTML=c;
                    tHR.appendChild(th);
                }
                tbl.appendChild(tHR);
                for(var r in tobj){
                    var robj=tobj[r];
                    var tr=document.createElement('tr');
                    for(var c in robj){
                        var td=document.createElement('td');
                        td.id=c;
                        td.innerHTML=robj[c]
                        tr.appendChild(td);
                    }
                    tbl.appendChild(tr);
                }
                tdiv.appendChild(tbl)
                sdata.appendChild(tdiv);
            }
        }
    },
    fetchMoreResults:function(){
        return false;
    },
    reAlign:function(){
        var da=searchTool.children['displayArea'];
        if(da.scrollWidth>window.innerWidth-30){
            da.style.width=window.innerWidth-30+"px";
        }else{
            da.style.width=null;
        }
        if(da.scrollHeight>window.innerHeight-40){
            da.style.height=window.innerHeight-40+"px";
        }else{
            da.style.height=null;
        }
    }
}