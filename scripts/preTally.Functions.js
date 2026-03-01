;(function($, window, undefined) {
    preTally.Functions = {
        setSens: function (obj, inp, k) {
            if (k == "min") 
                obj.setSensitiveRange(inp.value, null);
            else 
                obj.setSensitiveRange(null, inp.value);
        },
        number2text : function(value) {
            var fraction = Math.round(preTally.Functions.frac(value)*100);
            var f_text  = "";
            if(fraction > 0) 
                f_text = "AND "+preTally.Functions.convert_number(fraction)+" PAISE";
            return preTally.Functions.convert_number(value)+""+f_text+" ONLY";
        },
        frac : function(f) {
            return f % 1;
        },
        convert_number : function(number) {
            if ((number < 0) || (number > 999999999)) 
                return "NUMBER OUT OF RANGE!";

            var Gn = Math.floor(number / 10000000);  /* Crore */ 
            number -= Gn * 10000000; 
            var kn = Math.floor(number / 100000);     /* lakhs */ 
            number -= kn * 100000; 
            var Hn = Math.floor(number / 1000);      /* thousand */ 
            number -= Hn * 1000; 
            var Dn = Math.floor(number / 100);       /* Tens (deca) */ 
            number = number % 100;               /* Ones */ 
            var tn= Math.floor(number / 10); 
            var one=Math.floor(number % 10); 
            var res = ""; 

            if (Gn>0) 
               res += (preTally.Functions.convert_number(Gn) + " CRORE"); 
            if (kn>0)  { 
                res += (((res=="") ? "" : " ") + 
                preTally.Functions.convert_number(kn) + " LAKH"); 
            } 
            if (Hn>0)   { 
                res += (((res=="") ? "" : " ") +
                preTally.Functions.convert_number(Hn) + " THOUSAND"); 
            } 

            if (Dn) { 
                res += (((res=="") ? "" : " ") + 
                preTally.Functions.convert_number(Dn) + " HUNDRED"); 
            } 

            var ones = Array("", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX","SEVEN", "EIGHT", "NINE", "TEN", "ELEVEN", "TWELVE", "THIRTEEN","FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN","NINETEEN"); 
            var tens = Array("", "", "TWENTY", "THIRTY", "FOURTY", "FIFTY", "SIXTY","SEVENTY", "EIGHTY", "NINETY"); 

            if (tn>0 || one>0) { 
                if (!(res=="")) 
                    res += " AND "; 
                if (tn < 2) 
                    res += ones[tn * 10 + one]; 
                else  { 
                    res += tens[tn];
                    if (one>0) 
                        res += ("-" + ones[one]); 
                } 
            }
            if (res=="")
                res = "zero"; 
            return res;
        },
        generateUniqueId : function() {
            var res = Math.random()+new Date().getTime()+ Math.random();
            return Math.floor(res);
        },
        recordNotFound : function(Grid,colNum) {
            if(Grid.getRowsNum() == 0) {
                if(Grid.doesRowExist("msgRow")) 
                    Grid.deleteRow("msgRow");

                Grid.addRow('msgRow',"No records found");
                Grid.setRowTextStyle('msgRow','font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                Grid.setColspan("msgRow",0,colNum);
            }
        }, 
        convArrToObj : function(array){
            var thisEleObj = new Object();
            if(typeof array == "object"){
                for(var i in array){
                    var thisEle = preTally.Functions.convArrToObj(array[i]);
                    thisEleObj[i] = thisEle;
                }
            }else {
                thisEleObj = array;
            }
            return thisEleObj;
        },
        setRequired : function (formName, setReqArray, status) { // To Change The Status Of A Field In Form
            $.each(setReqArray, function( index, value ) {
                formName.setRequired(value,status); 
            });
        },
        showItem: function (formName, setItemArray) { // To Show A Field In Form
            $.each(setItemArray, function( index, value ) {
                formName.showItem(value); 
            });
        },
        hideItem : function (formName, setItemArray) { // To Hide A Field In Form
            $.each(setItemArray, function( index, value ) {
                formName.hideItem(value); 
            });
        },
        enableItem : function(formName, setItemArray){ // To Enable an Item
            $.each(setItemArray, function( index, value ) {
                formName.enableItem(value); 
            });
        },
        disableItem : function(formName, setItemArray){ // To Disable an Item
            $.each(setItemArray, function( index, value ) {
                formName.disableItem(value); 
            });
        },
        clearComboValues : function (comboArray , form) { // To Clear Combo values
            $.each(comboArray, function( index, value ) {
                if(form != 'automateForm') value.clearAll();
                value.setComboText('');
                value.setComboValue('');
            });
        },
        setComboChecked : function(ObjArray,NameArray, name,value){
            var checkedFlag = ObjArray[NameArray.indexOf(name)].isChecked(ObjArray[NameArray.indexOf(name)].getIndexByValue(value)) ? false : true ;
            if(checkedFlag == false) ObjArray[NameArray.indexOf(name)].setComboText("");
            ObjArray[NameArray.indexOf(name)].setChecked(ObjArray[NameArray.indexOf(name)].getIndexByValue(value), checkedFlag);
            ObjArray[NameArray.indexOf(name)].openSelect();
            preTally.Functions.setComboText(ObjArray[NameArray.indexOf(name)]);
        },
        setComboText : function(ObjName){
            ObjName.setComboText(ObjName.getChecked().length + " Items Selected");
            ObjName.openSelect();

//            var comboText = '';
//            var checkedOpnIds = ObjName.getChecked();
//            $.each(checkedOpnIds, function( index, value ) {
//                comboText += ObjName.getOption(value).text+",";
//            });
//            var selectedTextValues = comboText.replace(/,(?=[^,]*$)/, '')
//            ObjName.setComboText(selectedTextValues);
        },
        clearComboCheckedValues : function (comboObj) { // To Clear Combo values
            comboObj.forEachOption(function(optId){
                comboObj.setChecked(optId.index, false);
            });
            setTimeout(function(){ 
                comboObj.setComboText(' ');
                comboObj.setComboValue();
            }, 200);
            
        },
        ValidateCheckboxCombo : function(ObjArray,NameArray,FormObj){
            var validateStatus = true;
            $.each(ObjArray, function( index, value ) {
                if(value.getChecked().length == 0){
                    FormObj.setValidateCss(NameArray[index],false,"validate_red");
                    validateStatus = false;
                } else {
                    FormObj.setValidateCss(NameArray[index],true," ");
                }
            });
            return validateStatus;
        },
        clearGridFilters : function (gridObj) {
            for (var i=0; i < gridObj.getColumnCount(); i++) {
                var filter = gridObj.getFilterElement(i);
                if (filter) filter.value = '';
            }
            gridObj.filterByAll();
        }
    };
})(jQuery, this);