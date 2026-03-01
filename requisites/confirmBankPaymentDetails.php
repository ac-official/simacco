<?php

require_once($BASEPATH . "preTallyClass/NotificationClass.php");
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/LocationClass.php");
require_once($BASEPATH . "preTallyClass/SubheadPatternClass.php");

$NotificationObj = new NotificationClass();
$BalSheetObj = new BalanceSheetClass();
$PatternObj = new SubheadPatternClass();

$ITID   =   $REQUEST['ITID'];
$BSId_Old = $REQUEST['BSID']; 

$NotificationObj->getDualEntryItem($ITID);
$ntfObj = $NotificationObj->NotfArray;

$SHID = $ntfObj['SH_Id'];
if($ntfObj['MH_Type'] == 1){
    $IT_Note = 'NAME OF THE INCOME';
    $DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
    $MH_Type = "Income";
}else{
    $IT_Note = 'NAME OF THE EXPENSE';
    $DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
    $MH_Type = "Expense";
}

$BalSheetObj->getBSItemValue($BSID);
$BS_Obj = $BalSheetObj->BalanceSheetArray;

$PatternObj->getPatternList($SHID);
$PatternList_Obj = $PatternObj->PatternArray;

$old = array('"', "[", "]");
$new   = array("", "", "");
$PatternList = str_replace($old, $new, $PatternList_Obj[0]->PL_PatternMap);
$listArray = explode(",", $PatternList); 

$PatternObj->listSHPatternArray($PatternList);
$patternArray = $PatternObj->PatternArray;

$PatternObj->listSHItemArray();
$BS_Items = $PatternObj->PatternArray;

$key = array_search('BS_Amount', $patternArray); 
unset($patternArray[$key]);

$key1 = array_search($key, $listArray); 
unset($listArray[$key1]);

foreach ($listArray as $key => $value) { 
    $curField[$key] = $patternArray[$value];
}

//if( ($SHID != 58 && $SHID != 59 ) && $BS_Obj){
    $sortArray = array();
    foreach ($curField as $key => $value) {
        $sortArray[$BS_Items[$value]['block']][$key] = $value;
    }
    
    function arraySort($a, $b){
        $priority = array('item','payment','general','user','approvals','');
        return array_search($a, $priority) - array_search($b, $priority);
    } 
    
    function arrayArrange($value) { 
        $OItm = $EItm = array();
        $ic = 0;
        foreach ($value as $arraySort) {
            if($ic % 2 ==0) {
                $OItm[$ic] = $arraySort;
            } else {
                $EItm[$ic] = $arraySort;
            }
            $ic ++;
        }
        return array_merge((array)$OItm, (array)$EItm);   
    }
    array_push($curField,"BS_Amount", "BS_Date","IT_Id","BS_Description","TR_Id");//print_r($curField);
    uksort($sortArray, "arraySort");
    echo '[{type: "settings",position: "label-left", labelWidth: "130", inputWidth: "200", offsetLeft:"20", noteWidth : "auto"},';  
    echo '{
                type		: "hidden",
                name		: "BS_Id",
                value		: "'.$BSId_Old.'"
            },{
                type		: "hidden",
                name		: "db_fields",
                value		: "'. htmlspecialchars(serialize($curField)).'"
            },{
                type		: "hidden",
                name		: "IT_Id",
                value		: "'.$ntfObj["IT_Id"].'"
            },{
                type		: "hidden",
                name		: "DS_Description",
                value		: ""
            },';
    
        echo '{type: "fieldset", label: " DETAILS ", inputWidth: "auto", list:[ ';
            echo '{ type : "input", name: "MH_Type",readonly: "true", value:"'.$MH_Type.'", inputWidth: "70" ,note: { text: "ENTRY TYPE" },},';echo '{ type:"newcolumn"},';   
            echo '{ type : "input", name: "IT_Name", readonly: "true", inputWidth: "250",  value:"'.$ntfObj["IT_Name"].'",note: { text: "'.$IT_Note.'" },},';echo '{ type:"newcolumn"},';    
            echo '{ type : "combo", name: "BS_Description", required:"true", value:"", serverFiltering : "requisites/descriptions.php&IT_Id='.$ntfObj["IT_Id"].'&filter=1" , note: { text: "'.$DS_Note.'" }, },';echo '{ type:"newcolumn"},';    
            echo '{ type : "calendar", name: "BS_Date", label:"Credited Date", value:"'.date("Y-m-d").'" },';echo '{ type:"newcolumn"},';
//            echo '{ type : "input", name: "TR_Id", readonly: "true", value:"", inputWidth: "120", className : "TRCombo",note: { text: "TRACK" }, },';echo '{ type:"newcolumn"},';    
            echo '{ type : "input", name: "Amount",readonly: "true", label:"", value:"" ,note: { text: "AMOUNT" },},';
        echo ']}, ';
        
        

        
         
    foreach ($sortArray as $key => $value) {        
        $count = 1;
        $fieldCount = ceil(count($value)/2);
        
//        if($key == 'general') $Date =  '{ type : "calendar", name: "BS_Date", label:"Created Date", value:"'.$BS_Obj[0]->BS_Date.'" },';
    
        echo '{type: "fieldset", label: " '.strtoupper ($key) .' DETAILS ", inputWidth: "auto", list:[ ';
        foreach (arrayArrange($value) as $keyParam => $valueParam) {
            
//            $Combo_Name = $BalSheetObj->getColNamebyId($valueParam,$BS_Obj[0]->$valueParam);
//            if($valueParam == 'BS_User' && $BS_Obj[0]->$valueParam == '0') $Combo_Name = 'All Branch Staff';
//
            $itemValue = '';
//            if($valueParam == 'BS_Amount') 
//                $itemValue=$BS_Obj[0]->BS_Amount;
//               
            if($valueParam == 'BS_PaidDate') 
                $itemValue=date('Y-m-d H:i:s');    
//                
//            if($valueParam == 'BS_PaidTo' || $valueParam == 'BS_RcvdFrm') 
//                $itemValue=htmlspecialchars_decode($BS_Obj[0]->DS_Description);
//            
//            if($valueParam == 'BS_RcvdBy') {
//                $itemValue=htmlspecialchars_decode($BS_Obj[0]->BS_PaidBy);
//                $BS_Obj[0]->$valueParam = $itemValue;
//            }
//                
//            if(!$itemValue)
//                $itemValue = $BS_Obj[0]->$valueParam;
            
            echo ' { type : "'.$BS_Items[$valueParam]['type'].'", name : "'.$valueParam.'", value: "'.$itemValue.'", label : "'.$BS_Items[$valueParam]['title'].'", className : "'.$BS_Items[$valueParam]['className'].'"';

                if($BS_Items[$valueParam]['type'] == 'combo') {
                    echo ', '.$BS_Items[$valueParam]['filter'];
                    $ComboValues[] = $valueParam;
                }

            echo '}, '.$Date;

            if($fieldCount == $count) 
                echo '{ type:"newcolumn", offset:"20" },';    
            $count++;
        }
        echo ']}, ';
        
    } 
    echo '{
        type: "block", inputWidth: 300, labelHeight : 135, 
        list:[{
                type		: "button", 
                value		: "Save", 
                name		: "confirmBankPayment"
            },{
                type:"newcolumn"
            },{
                type		: "button", 
                value		: "Cancel", 
                name		: "rejectBankPayment",
            }]

    },';
 echo']';