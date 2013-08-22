f=function(a,self,rmc){debugger;var items=a.toString().split('|');var i=0;var item;var amtObjArr=[];var total=0;var da=[];while(item=items[i]){var j=0;var info="";var cBD=0;var sa=false;var tAmt="";var ae=true;var d=true;while(item[j]){if(item[j]=='('){cBD++;sa=cBD==1?true:false;ae=false;}else if(item[j]==')'){cBD--;sa=cBD==0?false:true;}if(sa){info+=item[j];}if(ae)tAmt+=item[j];j++;}info=info[0]=='('?info.substring(1):"";tAmt=parseInt(tAmt.trim());var cus=info.split(";");j=0;while(cus[j]){var cuss=cus[j].split('-');if(cuss[0]=='s'){if(cuss[1]=='i'){var ia=cuss[2].split(",");for(var k=0;k<ia.length;k++){if(ia[k]==self){amtObjArr[i]={a:Math.ROUND(tAmt/ia.length,2)}}}d=false;}else if(cuss[1]=='e'){var ia=cuss[2].split(",");for(var k=0;k<ia.length;k++){if(ia[k]==self){var imin=true}}amtObjArr[i]={a:!imin?Math.ROUND(tAmt/(rmc-ia.length),2):0};d=false;}}else if(cuss[0]=='r'){amtObjArr[i]=amtObjArr[i]||{};amtObjArr[i]['r']=cuss[1]}j++;}if(!(amtObjArr[i]&&amtObjArr[i].a)&&(tAmt||tAmt===0)&&d){amtObjArr[i]=amtObjArr[i]||{};amtObjArr[i].a=Math.ROUND(tAmt/rmc,2);}if(amtObjArr[i]&&amtObjArr[i].a){total+=amtObjArr[i].a;da.push(amtObjArr[i].a+(amtObjArr[i].r?":"+amtObjArr[i].r:""));}i++;}return total+(da.length?"("+da.join("+")+")":"")}
ff=function(l,r,aa){debugger;var da=day(*);var d=day;var t=0;for(var j=0;j<da.length;j++){if(da[j-1]&&(da[j-1]!=(da[j]-1))){throw "Day "+da[j-1]+" should be followed by "+(parseInt(da[j-1])+1)+".";}if(da[j]==d-1){t=(parseInt(aa[j])?parseInt(aa[j]):0)}}t=t+parseInt(l)-parseInt(r);return t+"|"+l+"-"+r;}
(l=f(venki()(sept2012roomledger),"gowtham",6),r=f(gowtham()(sept2012roomledger),"venki",6))?ff(l,r,venki(*)):"";
(l=f(sandy()(sept2012roomledger),"gowtham",6),r=f(gowtham()(sept2012roomledger),"sandy",6))?ff(l,r,sandy(*))):"";
(l=f(vamsi()(sept2012roomledger),"gowtham",6),r=f(gowtham()(sept2012roomledger),"vamsi",6))?ff(l,r,vamsi(*)):"";
(l=f(meher()(sept2012roomledger),"gowtham",6),r=f(gowtham()(sept2012roomledger),"meher",6))?ff(l,r,meher(*)):"";
(l=f(raja()(sept2012roomledger),"gowtham",6),r=f(gowtham()(sept2012roomledger),"raja",6))?ff(l,r,raja(*)):"";
(l=f(gowtham()(sept2012roomledger),"gowtham",6),r=0)?ff(l,r,me(*)):""
ar=function(){
    return arguments;
},
f=function(a,self,rmc){
    debugger;
    var items=a.toString().split('|');
    var i=0;
    var item;
    var amtObjArr=[];
    var total=0;
    var da=[];
    while(item=items[i]){
        var j=0;
        var info="";
        var cBD=0;
        var sa=false;
        var tAmt="";
        var ae=true;
        var d=true;
        while(item[j]){
            if(item[j]=='('){
                cBD++;
                sa=cBD==1?true:false;
                ae=false;
            }else if(item[j]==')'){
                cBD--;
                sa=cBD==0?false:true;
            }
            if(sa){
                info+=item[j];
            }
            if(ae)tAmt+=item[j];
            j++;
        }
        info=info[0]=='('?info.substring(1):"";
        tAmt=parseInt(tAmt.trim());
        var cus=info.split(";");
        j=0;
        while(cus[j]){
            var cuss=cus[j].split('-');
            if(cuss[0]=='s'){
                if(cuss[1]=='i'){
                    var ia=cuss[2].split(",");
                    for(var k=0;k<ia.length;k++){
                        if(ia[k]==self){
                            amtObjArr[i]={
                                a:Math.ROUND(tAmt/ia.length,2)
                            }
                        }
                    }
                    d=false;
                }else if(cuss[1]=='e'){
                    var ia=cuss[2].split(",");
                    for(var k=0;k<ia.length;k++){
                        if(ia[k]==self){
                            var imin=true
                        }
                    }
                    amtObjArr[i]={
                        a:!imin?Math.ROUND(tAmt/(rmc-ia.length),2):0
                    }
                    d=false;
                }
            }else if(cuss[0]=='r'){
                amtObjArr[i]=amtObjArr[i]||{};
    
                amtObjArr[i]['r']=cuss[1]
            }
            j++;
        }
        if((!amtObjArr[i]||!amtObjArr[i].a)&&(tAmt||tAmt===0)&&d){
            amtObjArr[i]=amtObjArr[i]||{};
    
            amtObjArr[i].a=Math.ROUND(tAmt/rmc,2);
        }
        if(amtObjArr[i].a){
            total+=amtObjArr[i].a;
            da.push(amtObjArr[i].a+(amtObjArr[i].r?":"+amtObjArr[i].r:""));
        }
        i++;
    }
    return total+(da.length?"("+da.join("+")+")":"")
}
,l=f(venki()(sept2012roomledger),"venki",6);
ff=function(l,r,aa){
    debugger;
    var da=day(*);
    var d=day;
    var t=0;
    for(var j=0;j<da.length;j++){
        if(da[j-1]&&(da[j-1]!=(da[j]-1))){
            throw "Day "+da[j-1]+" should be followed by "+da[j-1]+1+".";
        }
        if(da[j]==d-1){
            t=(parseInt(aa[j])?parseInt(aa[j]):0)
        }
    }
    t=t+parseInt(l)-parseInt(r);
    return t+"|"+l+"-"+r;
}
for(var j=0;j<da.length;j++){if(da[j-1]&&(da[j-1]!=(da[j]-1))){throw "Day "+da[j-1]+" should be followed by "+parseInt(da[j-1])+1+".";}if(da[j]==d-1){t=(parseInt(aa[j])?parseInt(aa[j]):0)}}