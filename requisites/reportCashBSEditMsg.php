<?php
include_once($BASEPATH."preTallyClass/BalanceSheetClass.php");
include_once($BASEPATH."preTallyClass/LocationClass.php");
include_once($BASEPATH."preTallyClass/SubheadPatternClass.php");

$BalSheetObj = new BalanceSheetClass();
$PatternObj = new SubheadPatternClass();

$SHID   =   $REQUEST['SHID'];
$BSID   =   $REQUEST['BSID'];

$BSDetails_Obj = $BalSheetObj->getSubheadDetails( " AND BS.BS_ID=".$BSID );
$BalSheetObj->viewBalSheetDetails($BSID);
$BS_Obj = $BalSheetObj->BalanceSheetArray;
$PatternObj->getPatternList($SHID);
$PatternList_Obj = $PatternObj->PatternArray;

$old = array('"', "[", "]");
$new   = array("", "", "");
$PatternList = str_replace($old, $new, $PatternList_Obj[0]->PL_PatternMap);
$listArray = explode(",", $PatternList); 

$PatternObj->listSHPatternArray($PatternList);
$patternArray = $PatternObj->PatternArray;

$key = array_search('BS_Amount', $patternArray); 
unset($patternArray[$key]);

$key1 = array_search($key, $listArray); 
unset($listArray[$key1]);

$PatternObj->listSHItemArray();
$BS_Items = $PatternObj->PatternArray;

$curField = array();
        
foreach ($listArray as $key => $value) { 
    $curField[$key] = $patternArray[$value];
}

$ComboValues = array();
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
echo '[{type: "settings",position: "label-left", labelWidth: "130", inputWidth: "200", offsetLeft:"20", noteWidth : "100"},';  
echo '{
        type		: "hidden",
        name		: "BS_Id",
        value		: "'.$BSID.'"
    },{
        type		: "hidden",
        name		: "db_fields",
        value		: "'. htmlspecialchars(serialize($curField)).'"
    },{
        type		: "hidden",
        name		: "updateType",
        value		: "rpt"
    }, ';

    echo '{type: "fieldset", label: " DETAILS ", inputWidth: "auto", width : "800", list:[ ';
        echo '{ type : "combo", name: "MH_Type",readonly: "true", value:"'.$BS_Obj[0]->MH_Type.'", inputWidth: "100", options:[
                {value: "1", text: "Income"},
                {value: "2", text: "Expense"},';
                echo ']},';echo '{ type:"newcolumn"},';   
        echo '{ type : "combo", name: "IT_Id", inputWidth: "250",  value:"'.$BS_Obj[0]->IT_Id.'" },';echo '{ type:"newcolumn"},';    
        echo '{ type : "combo", name: "BS_Description", value:"'.$BS_Obj[0]->BS_Description.'" },';echo '{ type:"newcolumn"},';    
        echo '{ type : "combo", name: "TR_Id", value:"'.$BS_Obj[0]->TR_Id.'", inputWidth: "120", className : "TRCombo" },';echo '{ type:"newcolumn"},';    
        echo '{ type : "calendar", name: "BS_Date", label:"Created Date", value:"'.$BS_Obj[0]->BS_Date.'" },';echo '{ type:"newcolumn"},';
        /*if($BS_Obj[0]->IT_PettyCash == 1)$amount = $BS_Obj[0]->BS_PettyCashAmt;else */$amount= $BS_Obj[0]->BS_Amount ; 
        echo '{ type : "input", name: "BS_Amount", label:"Amount", value:"'.$amount.'" },';
        echo '{ type : "hidden", name: "IT_Flag", value:"0" },';
        echo '{ type : "hidden", name: "IT_Name", value:"'.$BS_Obj[0]->IT_Name.'" },';
        echo '{ type : "hidden", name: "IT_Status", value:"'.$BS_Obj[0]->IT_Status.'" },';
//            echo '{ type : "input", name: "BS_Amount", label:"Amount", value:"'.$BS_Obj[0]->BS_Amount.'" },';
        echo '{ type : "hidden", name: "LC_Id_Old", label:"Amount", value:"'.$BS_Obj[0]->LC_Id.'" },';
        echo '{ type : "hidden", name: "IT_Name", value:"'.$BS_Obj[0]->IT_Name.'" },';
        echo '{ type : "hidden", name: "TR_Track", label:"", value:"'.$BSDetails_Obj['TR_Track'].'" },';
        echo '{ type : "hidden", name: "HiddenTR_Id", label:"", value:"'.$BSDetails_Obj['TR_Id'].'" },';
        echo '{ type : "hidden", name: "DS_Id", label:"", value:"'.$BSDetails_Obj['DS_Id'].'" },';
        echo '{ type : "hidden", name: "DS_Description", label:"", value:"'.$BSDetails_Obj['DS_Description'].'" },';
        echo '{ type : "hidden", name: "BS_PayTime", label:"", value:"'.$BSDetails_Obj['BS_PayTime'].'" },';
        echo '{ type : "hidden", name: "HiddenIT_Id", label:"", value:"'.$BSDetails_Obj['IT_Id'].'" },';
        echo '{ type : "hidden", name: "HiddenMH_Type", label:"", value:"'.$BSDetails_Obj['MH_Type'].'" },';
        echo '{ type : "hidden", name: "H_Stats", label:"", value:"'.$BSDetails_Obj['IT_Status'].'" },';
    echo ']}, ';

foreach ($sortArray as $key => $value) {        
    $count = 1;
    $fieldCount = ceil(count($value)/2);
    echo '{type: "fieldset", label: " '.strtoupper ($key) .' DETAILS ", inputWidth: "auto", list:[ ';
    foreach (arrayArrange($value) as $keyParam => $valueParam) {
        
        echo '{ type : "hidden", name: "PettyCashAmount", value:"0" },';
        echo '{ type : "hidden", name: "PCRefId_Old", value:"'.$BS_Obj[0]->BS_PettyCashRefId.'" },';
        echo '{ type : "hidden", name: "IT_PettyCash", value:"'.$BS_Obj[0]->IT_PettyCash.'" },';

        $Combo_Name = $BalSheetObj->getColNamebyId($valueParam,$BS_Obj[0]->$valueParam);
        if($valueParam == 'BS_User' && $BS_Obj[0]->$valueParam == '0') $Combo_Name = 'All Branch Staff';

        $itemValue = '';
        if($valueParam == 'BS_Amount') 
            $itemValue=$BS_Obj[0]->BS_Amount;

        if($valueParam == 'BS_PaidDate' && $BS_Obj[0]->BS_PaidDate == '') 
            $itemValue=date('Y-m-d H:i:s');    

        if($valueParam == 'BS_PaidTo' || $valueParam == 'BS_RcvdFrm') 
            $itemValue=$BS_Obj[0]->DS_Description;

        if($valueParam == 'BS_RcvdBy') {
            $itemValue=$BS_Obj[0]->BS_PaidBy;
            $BS_Obj[0]->$valueParam = $itemValue;
        }

        if(!$itemValue)
            $itemValue = $BS_Obj[0]->$valueParam;
        
        if($BSDetails_Obj['MH_Type'] == 1 && $valueParam == 'BS_IEByUS')
            $BS_Items[$valueParam]['title'] = "Amount Received through User";
        if($BSDetails_Obj['MH_Type'] == 1 && $valueParam == 'BS_IEByLC')
            $BS_Items[$valueParam]['title'] = "Amount Received through Branch";

        echo ' { type : "'.$BS_Items[$valueParam]['type'].'", name : "'.$valueParam.'", label : "'.$BS_Items[$valueParam]['title'].'", className : "'.$BS_Items[$valueParam]['className'].'",  value : "'.$itemValue.'", userdata : {cId:"'.$itemValue=$BS_Obj[0]->$valueParam.'", cValue:"'.$Combo_Name.'"}';
        if($BS_Items[$valueParam]['type'] == 'combo') {
            echo ', '.$BS_Items[$valueParam]['filter'];
            $ComboValues[] = $valueParam;
        }
        echo '}, ';
        if($fieldCount == $count) 
            echo '{ type:"newcolumn", offset:"20" },';    
        $count++;
    }
    // start @ 03-06-2025 set the other branch cbo
    if ( $key=="payment" ) {
        $othbranchid = ($BS_Obj[0]->BS_BranchTo > 0) ? $BS_Obj[0]->BS_BranchTo:$BS_Obj[0]->LC_Id;
        echo '{ type : "combo", name : "BS_BranchTo", label : "Amount For Other Branch", className : "",  value : "0",userdata : {cId:"'.$othbranchid.'", cValue:" "}, connector : "requisites/locations.php&ofid=' . $preTally_user_ofid . '&seltdQffz='.$othbranchid.'", readonly:true,filterCache: true }';
    }
    // end @ 03-06-2025
    echo ']}, ';
} 
echo '{
    type: "block", inputWidth: 300, labelHeight : 135, 
    list:[{
        type		: "button", 
        value		: "Save", 
        name		: "balSheetDetailsSave"
    },{
        type:"newcolumn"
    },{
        type		: "button", 
        value		: "Cancel", 
        name		: "balSheetDetailsCancel",
    }]
},';
if( $SHID == 58 || $SHID == 59 ){
    echo '{ type : "template", name: "Message", label:"Limited Details,Item not approved.", offsetLeft:"200" ,labelWidth: "300", className : "alert_message" },'; 
} 
echo ']';