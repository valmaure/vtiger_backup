/*********************************************************************************
 * The content of this file is subject to the EMAIL Maker license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
Vtiger_Edit_Js("ITS4YouStyles_Edit_Js",{
    duplicateCheckCache : {},
    formElement : false,
    myCodeMirror : false,

    getForm : function(){
        if(this.formElement == false){
            this.setForm(jQuery('#EditView'));
        }
        return this.formElement;
    },
    setForm : function(element){
        this.formElement = element;
        return this;
    },


    registerCodeMirror: function(){

        var myTextArea = document.getElementById("ITS4YouStyles_editView_fieldName_stylecontent");
        if (this.myCodeMirror == false) {
            this.myCodeMirror = CodeMirror.fromTextArea(myTextArea,{
                lineNumbers: true,
                styleActiveLine: true,
                matchBrackets: true
            });
        }
    },

    registerEvents: function(){
        var editViewForm = this.getForm();
        var statusToProceed = this.proceedRegisterEvents();
        if(!statusToProceed){
            return;
        }

        this.registerCodeMirror();
    }

});
