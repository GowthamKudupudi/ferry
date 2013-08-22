// global validation object encapsulates validation routines
var validator = {
    // validates that the field value string has one or more characters in it
    isNotEmpty : function(elem) {
        var str = elem.value;
        var re = /.+/;
        if(!str.match(re)) {
            statusField.innerHTML="Please fill in the required field.";
            setTimeout("validator.focusElement('"+ elem.form.name +"', '" + elem.name + "')", 0);
            return false;
        } else {
            return true;
        }
    },

    isUsername : function(elm) {
        var str=elm.value;
        var re = /[^a-zA-Z0-9_\-.]/;
        str = str.toString( );
        if(str==""){
            statusField.innerHTML="No Username was entered.";
            elm.focus();
            return false;
        }else if(str.toLowerCase()=='guest'){
            statusField.innerHTML="username should not be guest :|"
            elm.focus();
            return false;
        }else if(str.length<8){
            statusField.innerHTML="username should b of atleast 8 chars."
            elm.focus();
            return false;
        }else if (str.match(re)) {
            statusField.innerHTML="Only letters, numbers, - and _ in usernames.";
            elm.focus();
            return false;
        }
        return true;
    },

    isPassword : function(elm,relm) {
        var str=elm.value;
        var rstr=relm.value.toString();
        str = str.toString( );
        if(str==""){
            statusField.innerHTML="password can't b empty";
            elm.focus();
        }else if(str!=rstr){
            statusField.innerHTML="passwords do not match.";
            elm.focus();
            return false;
        }else if (!(str.match(/[a-z]/)&&str.match(/[A-Z]/)&&str.match(/[0-9]/))) {
            statusField.innerHTML="password should b of small letters, capitals, numerals n 8-16 characters.";
            elm.focus();
            return false;
        }
        return true;
    },

    isEmailID : function(elm) {
        var str=elm.value;
        var re = /[a-zA-Z0-9_.]+@[a-zA-Z0-9.]+.com/;
        str = str.toString( );
        if(str==""){
            return true;
        }
        else if (!str.match(re)) {
            statusField.innerHTML="Only letters, numbers, - and _ in usernames.";
            elm.focus();
            return false;
        }
        return true;
    },

    isFullName : function(elm) {
        var str=elm.value;
        var re = /[^a-zA-Z ]/;
        str = str.toString( );
        if (str.match(re)) {
            statusField.innerHTML="Only letters allowed in full name.";
            elm.focus();
            return false;
        }
        return true;
    },

    isDOB : function(str) {
        var re = /[\d]{4}-[\d]{1,2}-[\d]{1,2}/;
        str = str.toString( );
        if (!str.match(re)) {
            statusField.innerHTML="Enter date of bidrth.";
            return false;
        }
        return true;
    },

    isGaurdianID : function(elm) {
        var str=elm.value;
        var re = /[\d]{1,32}/;
        str = str.toString();
        if (!str.match(re)) {
            statusField.innerHTML="Gaurdian ID should b of 1 to 32 numerals.";
            elm.focus();
            return false;
        }
        return true;
    },

    isPAdress : function(elm) {
        var str=elm.value;
        str = str.toString( );
        if (str.length>200) {
            statusField.innerHTML="address Should not exceed 200 chars";
            elm.focus();
            return false;
        }
        return true;
    },

    isTel : function(elm) {
        var str=elm.value;
        var re = /[\d]{10,14}/;
        str = str.toString( );
        if(str==""){
            return true;
        }
        else if (!str.match(re)) {
            statusField.innerHTML="Only letters, numbers, - and _ in usernames.";
            elm.focus();
            return false;
        }
        return true;
    },

    // validate that the user made a selection other than default
    isChosen : function(select) {
        if (select.selectedIndex == 0) {
            statusField.innerHTML="Please make a choice from the list.";
            return false;
        } else {
            return true;
        }
    },
    // validate that the user has checked one of the radio buttons
    isValidRadio : function(radio) {
        var valid = false;
        for (var i = 0; i < radio.length; i++) {
            if (radio[i].checked) {
                return true;
            }
        }
        statusField.innerHTML="Make a choice from the radio buttons.";
        return false;
    }
}// batch validation router tailored for "sampleForm"
window.validator=validator;