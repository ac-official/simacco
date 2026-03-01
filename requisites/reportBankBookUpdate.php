<?php
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/LocationClass.php");
require_once($BASEPATH . "preTallyClass/SubheadPatternClass.php");
$BalSheetObj = new BalanceSheetClass();

$BSID   =   $REQUEST['BSID'];
$MHType =   $REQUEST['MH_Type'];

    echo '[{type: "settings",position: "label-left", labelWidth: "150", inputWidth: "300", offsetLeft:"20", noteWidth : "100"},';  
    echo '{
                type		: "hidden",
                name		: "BS_Id",
                value		: "'.$BSID.'"
            },{
                type		: "hidden",
                name		: "MH_Type",
                value		: "'.$MHType.'"
            },{
                type		: "hidden",
                name		: "CHQ_Number",
                value		: ""
            },';
    
        if($MHType == 1){
            echo '{type: "fieldset", label: " DETAILS ", inputWidth:"auto", list:[ ';
                echo '{ type : "combo",label:"Payment Status", name: "BS_Status",readonly: "true",  options:[
                                                    {value: "1", text: "Credited"},
                                                    {value: "3", text: "Rejected"},';
                                                    echo ']},';//echo '{ type:"newcolumn"},';
                echo '{ type : "calendar", name: "BS_Date", readonly: "true", required : "true", serverDateFormat:"%Y-%m-%d",  dateFormat:"%d.%m.%Y" , label:"Date" },';
                echo '{ type : "input", name: "BS_BReff", label:"Reference Details" },';
                
                echo '{ type : "input", name: "BS_RJReason", label:"Reason for rejection" },';
                echo '{ type : "input", name: "BS_BRemarks", label:"Remarks" },';
            echo ']}, ';
        }elseif($MHType == 2) {
                echo '{type: "fieldset", label: " DETAILS ", inputWidth:"auto", list:[ ';
                echo '{ type : "combo",label:"Payment Status", name: "BS_Status",readonly: "true",  options:[
                                                    {value: "1", text: "Payment Cleared"},
                                                    {value: "3", text: "Payment Rejected"},';
                                                    echo ']},';//echo '{ type:"newcolumn"},';
                echo '{ type : "calendar", name: "BS_Date", readonly: "true",required : "true", serverDateFormat:"%Y-%m-%d",  dateFormat:"%d.%m.%Y" , label:"Date" },';
                echo '{ type : "input", name: "BS_RJReason", label:"Reason for rejection" },';
                echo '{ type : "input", name: "BS_BRemarks", label:"Remarks" },';
            echo ']}, ';
        }
    
    echo '{
        type: "block", inputWidth: 300, labelHeight : 135, 
        list:[{
                type		: "button", 
                value		: "Save", 
                name		: "bnkBookDetailsSave"
            },{
                type:"newcolumn"
            },{
                type		: "button", 
                value		: "Cancel", 
                name		: "bnkBookDetailsCancel",
            }]

    },';
    echo ']';