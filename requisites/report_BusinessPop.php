<?php
//error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
include_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/SubheadPatternClass.php");

if($REQUEST['SHID']!=58 && $REQUEST['SHID']!=59){
    $SHID   = $REQUEST['SHID'];
    $BSID   = $REQUEST['BSID'];
    $BalSheetObj = new BalanceSheetClass();
    $PatternObj = new SubheadPatternClass();
    $UserObj = new UserClass();

    $BalSheetObj->viewBalSheetDetails($BSID);
    $BS_Obj = $BalSheetObj->BalanceSheetArray;//print_r($BS_Obj);

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

    foreach ($listArray as $key => $value) { 
        $curField[$key] = $patternArray[$value];
    }
    //$curField = $updateFields[$SHID];//print_r($BS_Items);
    // start 02-06-2025  add other branch details in to field list 
    $leftextfld     = 2; // static values 
    if ($BS_Obj[0]->BS_BranchTo > 0 && $BS_Obj[0]->LC_Id != $BS_Obj[0]->BS_BranchTo) {
        $curField['custom'] = 'BS_BranchTo'; 
        $BS_Items['BS_BranchTo']['title']='Amount For Other Branch';
        $leftextfld = 3;
    }
    // End 02-06-2025

        $formData = '[{type: "settings",position: "label-left", labelWidth: "130", inputWidth: "auto", offsetLeft:"6"},';   
        $formData .= '{type: "fieldset", name:"fieldsetname", class: "mydata",  label: "<img src=\'images/icon/close_button_icon.png\' style=\'cursor:pointer;\' onclick=\'preTally.BusinessReport.hideDetailData()\' />", width:"auto", list:[';
        
        $trackName = $BalSheetObj->getColNamebyId('TR_Id',$BS_Obj[0]->TR_Id);
        if($trackName)
            $formData .= ' { type : "template", name : "TR_Id", label : "Track ID : ", value : " <b>'.$trackName.'</b>" },';
     
        $i = 1;
        $fieldCount = ceil((count($curField)+$leftextfld)/2);
        foreach ($curField as $value) {            
            $Combo_Name = $BalSheetObj->getColNamebyId($value,$BS_Obj[0]->$value);
            if($value=="BS_Amount"){
                $Combo_Name=number_format($Combo_Name,2);
            }
            if($value == 'BS_User' && $BS_Obj[0]->$value == '0') $Combo_Name = 'All Branch Staff';
            if($value == 'BS_IEByUS' && $BS_Obj[0]->$value == '0') $Combo_Name = 'All';
            
            if($value == 'BS_PettyCashRefId'){ 
                if($BS_Obj[0]->BS_PettyCashRefId != 0) {
                    $filter = ' IT.OF_Id ='.$preTally_user_ofid .' AND BS_Id = '.$BS_Obj[0]->BS_PettyCashRefId;
                    $PCBalDetails = $BalSheetObj->viewPettyCashRefrence($filter);
                    $PCAmount = $BalSheetObj->getAmount($BSID,$BS_Obj[0]->BS_PettyCashRefId);
                    $Combo_Name = $PCAmount.'( Bal : '. $PCBalDetails['BS_Amount'].' ) - '.$PCBalDetails['DS_Description'].' ( '.date('d-M-y', strtotime($PCBalDetails['BS_Date'])).' )';
                }else $Combo_Name = '';
            }
            
            if($BS_Obj[0]->MH_Type == 1 && $valueParam == 'BS_IEByUS')
                $BS_Items[$valueParam]['title'] = "Amount Received through User";
            if($BS_Obj[0]->MH_Type == 1 && $valueParam == 'BS_IEByLC')
                $BS_Items[$valueParam]['title'] = "Amount Received through Branch";
            
            if($value == "BS_PaidDate" && $BS_Obj[0]->BS_PaidDate != '') 
                $Combo_Name = date('d/m/Y',  strtotime($BS_Obj[0]->BS_PaidDate)); 
            if($value == "BS_AprovdDate" && $BS_Obj[0]->BS_AprovdDate != '') 
                $Combo_Name = date('d/m/Y',  strtotime($BS_Obj[0]->BS_AprovdDate)); 
            
            $formData .= ' { type : "template", name : "'.$value.'", label : "'.$BS_Items[$value]['title'].'", value : " <b>'.wordwrap(html_entity_decode($Combo_Name),30,"<br>",TRUE).'</b>" },';
            if($fieldCount == $i) 
               $formData .= '{ type:"newcolumn"},';               
            $i++;
        } 
        $formData .= ' { type : "template", name : "US_Id", label : "Created By : ", value : " <b>'.$BalSheetObj->getColNamebyId('US_Id',$BS_Obj[0]->US_Id).'</b>" },';
        $formData .= ' { type : "template", name : "LC_Id", label : "Created Branch : ", value : "<b>'.$UserObj->getUserLocation($BS_Obj[0]->US_Id).'</b>" },';
        $formData .= ' { type : "template", name : "BS_Date", label : "Created Date : ", value : "<b>'.date('d/m/Y',  strtotime($BS_Obj[0]->BS_Date)).'</b>" },';
    $formData .= ']}]';
    echo $formData;
} else {
    $formData12 = '[{type: "settings",position: "label-left", labelWidth: "190", inputWidth: "auto"},';   
    $formData12 .= '{type: "fieldset", name:"fieldsetname", label: "<img src=\'images/icon/close_button_icon.png\' style=\'cursor:pointer;\' onclick=\'preTally.BusinessReport.hideDetailData()\' />", width:"auto", list:[';
        $formData12 .= ' { type : "template" , label : "<b>Waiting for admin Approval.</b>" , value : "<b> Contact your administrator for further information.</b> " },';
        $formData12 .= '{ type:"newcolumn"},'; 
        //$formData .= ' { type : "template", value : "<b>Waiting for admin Approval.</b> "  },';
    $formData12 .= ']}]';
    echo $formData12;
}
?>
