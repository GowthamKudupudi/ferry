var dateGen={
    year: function(){
        var yearArray=new Array();
        for(var i=1900; i<=2011; i++){
            yearArray[yearArray.length]=i;
        }
        return yearArray;
    },
    month: function(){
        function monthObject(val,mn){
            this.value=val;
            this.monthName=mn;
        }
        var monthArray=new Array();
        var monthID=["January","February","March","April","May","June","July","August","September","October","November","December"];
        for(var i=0; i<12;i++){
            monthArray[monthArray.length]=new Object();
            monthArray[i].monthNum=i+1;
            monthArray[i].monthName=monthID[i];
        }
        return monthArray;
    },
    day: function(year,month){
        var dayArray= new Array();
        if(month==1 || month==3 || month==5 || month==7 || month==8 || month==10 || month == 12){
            for(var i=1;i<=31;i++){
                dayArray[dayArray.length]=i;
            }
        }else if(month==2){
            if(year==1900 || year%4!=0){
                for(var i=1;i<=28;i++){
                    dayArray[dayArray.length]=i;
                }
            }else{
                for(var i=1;i<=29;i++){
                    dayArray[dayArray.length]=i;
                }
            }
        }else{
            for(var i=1;i<=30;i++){
                dayArray[dayArray.length]=i;
            }
        }
        return dayArray;
    }
};