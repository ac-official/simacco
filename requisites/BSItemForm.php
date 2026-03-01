<?php


if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	//header("Content-type: application/xhtml+xml"); } else {
	//header("Content-type: text/xml");
}

/*echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");*/

include_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
include_once($BASEPATH . "preTallyClass/LocationClass.php");
include_once($BASEPATH . "preTallyClass/SubheadPatternClass.php");


$BalSheetObj = new BalanceSheetClass();
$PatternObj = new SubheadPatternClass();
$LocObj = new LocationClass();
$SHID   =   $REQUEST['SHID'];
$BSID   =   $REQUEST['BSID'];
$MHType =   $REQUEST['MH_Type'];
$Stats  =   $REQUEST['Stats'];

$BalSheetObj->getBSItemValue($BSID);
$BS_Obj = $BalSheetObj->BalanceSheetArray;
if($Stats!=1){
    if($MHType==1)
        $PatternObj->getPatternList(59);
    else
        $PatternObj->getPatternList(58);
}
else $PatternObj->getPatternList($SHID);
$PatternList_Obj = $PatternObj->PatternArray;

$old = array('"', "[", "]");
$new   = array("", "", "");
$PatternList = str_replace($old, $new, $PatternList_Obj[0]->PL_PatternMap);
$listArray = explode(",", $PatternList); 

$PatternObj->listSHPatternArray($PatternList);
$patternArray = $PatternObj->PatternArray;

$PatternObj->listSHItemArray();
$BS_Items = $PatternObj->PatternArray;

foreach ($listArray as $key => $value) { 
    $curField[$key] = $patternArray[$value];
}

$fieldload = 0; //30-5-2025
$encoded   = array("/");
$decoded   = array("\/");

//if( ($SHID != 58 && $SHID != 59 ) && $BS_Obj){ 
?>
<link rel="stylesheet" type="text/css" href="assets/form/codebase/skins/dhtmlxform_dhx_skyblue.css">
<script src="assets/form/codebase/dhtmlxcommon.js"></script>
<script src="assets/form/codebase/dhtmlxform.js"></script>
<script type="text/javascript" src="scripts/jquery.js"></script>
<script language="javascript">
   
    var BS_Form_Data = [{
        type		: "settings",
        position	: "label-left",
        labelWidth	: 120,
        inputWidth	: 200,
        className	: "itemDetailForm"
    },{
        type		: "hidden",
        name		: "BS_Id",
        value		: "<?php echo $BSID; ?>"
    },{
        type		: "hidden",
        name		: "SH_Id",
        value		: "<?php echo $SHID; ?>"
    },{
        type		: "hidden",
        name		: "db_fields",
        value		: "<?php echo htmlspecialchars(serialize($curField))?>"
    }, 
    
    <?php
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

    uksort($sortArray, "arraySort");
    echo '{ type : "hidden", name: "H_Stats",  value:"'.$Stats.'" },';
    echo '{ type : "hidden", name: "updateType",  value:"0" },';
    foreach ($sortArray as $key => $value) {
        $count = 1;
        $fieldCount = ceil(count($value)/2);
        
        echo '{ type : "hidden", name: "PettyCashAmount", value:"0" },';
        echo '{ type : "hidden", name: "PCRefId_Old", value:"'.$BS_Obj[0]->BS_PettyCashRefId.'" },';
        echo '{ type : "hidden", name: "IT_PettyCash", value:"'.$BS_Obj[0]->IT_PettyCash.'" },';
        echo '{ type : "hidden", name: "BS_Date", value:"'.$BS_Obj[0]->BS_Date.'" },';
        
        echo '{type: "fieldset", label: " '.strtoupper ($key) .' DETAILS ", inputWidth: "auto", list:[ ';
        foreach (arrayArrange($value) as $keyParam => $valueParam) {
            $Combo_Name = $BalSheetObj->getColNamebyId($valueParam,$BS_Obj[0]->$valueParam);
            if($valueParam == 'BS_User' && $BS_Obj[0]->$valueParam == '0') $Combo_Name = 'All Branch Staff';
            $itemValue = '';
            if($valueParam == 'BS_Amount') 
               $itemValue=$BS_Obj[0]->BS_Amount;
            
            if($valueParam == 'BNK_Id') 
               $itemValue=$BS_Obj[0]->BNK_Id;
            
            if($valueParam == 'BB_Id') 
               $itemValue=$BS_Obj[0]->BB_Id;
            
            if($valueParam == 'BA_Id') 
               $itemValue=$BS_Obj[0]->BA_Id;
                
            if($valueParam == 'BS_VoucherNo') 
               $itemValue=$BS_Obj[0]->BS_VoucherNo;
            
            if($valueParam == 'BS_PaidDate'){
                if($BS_Obj[0]->BS_PaidDate!="")
                    $itemValue=$BS_Obj[0]->BS_PaidDate;
                else
                    $itemValue=date("Y-m-d");
            }               
                
            if(!$itemValue)
                $itemValue = $BS_Obj[0]->$valueParam;
            
            if($valueParam == 'BS_PaidTo' || $valueParam == 'BS_RcvdFrm') 
                $itemValue=htmlspecialchars_decode($BS_Obj[0]->DS_Description, ENT_QUOTES); 
            
            
            if($BS_Obj[0]->MH_Type == 1 && $valueParam == 'BS_IEByUS')
                $BS_Items[$valueParam]['title'] = "Amount Received through User";
            if($BS_Obj[0]->MH_Type == 1 && $valueParam == 'BS_IEByLC')
                $BS_Items[$valueParam]['title'] = "Amount Received through Branch";
            
            echo ' { type : "'.$BS_Items[$valueParam]['type'].'", name : "'.$valueParam.'", label : "'.$BS_Items[$valueParam]['title'].'", className : "'.$BS_Items[$valueParam]['className'].'",  value : "'.  str_replace($encoded,$decoded,$itemValue).'",userdata : {cId:"'.str_replace($encoded,$decoded,$itemValue).'", cValue:"'.$Combo_Name.'"}';

                if($BS_Items[$valueParam]['type'] == 'combo') {
                    echo ', '.$BS_Items[$valueParam]['filter'];
                    $ComboValues[] = $valueParam;
                }

            echo '}, ';

            if($fieldCount == $count) 
                echo '{ type:"newcolumn", offset:"20" },';    
            $count++;
        }
        // start @ 30-05-2025 //$BS_Obj[0]->IT_OtherUser == 1 && 
        if ($fieldload == 0 && $key=="payment") { // check the item allowed for other branch 
            echo '{ type : "combo", name : "BS_BranchTo", label : "Amount For Other Branch", className : "",  value : "0",userdata : {cId:"'.$BS_Obj[0]->BS_BranchTo.'", cValue:" "}, connector : "requisites/locations.php&ofid=' . $preTally_user_ofid . '&seltdQffz='.$BS_Obj[0]->BS_BranchTo.'", readonly:true,filterCache: true }';
            /*echo ' { type : "combo", name : "BS_BranchTo", label: "Other Branch", className : "branchcombo", value :"'.$BS_Obj[0]->BS_BranchTo.'"   connector: "requisites/locations.php&amp;ofid=' . $preTally_user_ofid . '&amp;seltdQffz='.$BS_Obj[0]->BS_BranchTo.'",readonly: true, userdata: { cId: "'.$BS_Obj[0]->BS_BranchTo.'",cValue: ""}, }, { type:"newcolumn", offset:"20" },';  */
            $fieldload = 1; 
        }
        // end at 02-06-2025
        echo ']}, ';
    }
    if((($SHID==58)||($SHID==59)||($Stats!=1)) && $BS_Obj){         
    ?>   
    {
        type: "block", inputWidth: 200, labelHeight : 135, offsetLeft:200,width:600,
        list:[{type:"template",inputWidth: 300, value:"Item waiting for Admin Approval",labelWidth:"0",className : "alert_message"},
            {
                type:"newcolumn"
            },{
                type		: "button", 
                value		: "Update", 
                name		: "balSheetFormSave"
            },{
                type:"newcolumn"
            },{
                type		: "button", 
                value		: "Cancel", 
                name		: "balSheetFormCancel",
            }]

    }];
    <?php } else { ?>
        //echo '<cell></cell><cell></cell><cell><div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >Waiting for Admin Approval.</div></cell></row>';
    
    {
        type: "block", inputWidth: 200, labelHeight : 135, offsetLeft:550, 
        list:[{
                type:"newcolumn"
            },{
                type		: "button", 
                value		: "Save", 
                name		: "balSheetFormSave"
            },{
                type:"newcolumn"
            },{
                type		: "button", 
                value		: "Cancel", 
                name		: "balSheetFormCancel",
            }]

    }];
    
    
    <?php } ?>
    BSForm[<?php echo $BSID; ?>] = new dhtmlXForm("BS_Form_<?php echo $BSID; ?>", BS_Form_Data);
</script>
<div id="BS_Form_<?php echo $BSID; ?>" ><input type='hidden' value='<?php echo json_encode($ComboValues);?>' id='ComboValues'></div>
<style type="text/css">
#BS_Form_<?php echo $BSID; ?> {
    margin: -2px 0 0 0;
}
#BS_Form_<?php echo $BSID; ?> fieldset {
    padding: 0px 0 3px 0;
    width: 720px;
    /*border-bottom : none;*/
}
#BS_Form_<?php echo $BSID; ?> .fs_dhxform_item_label_left {
    padding: 0;
}
#BS_Form_<?php echo $BSID; ?> .dhxform_item_label_left {
    padding-top: 2px;
}

</style>
