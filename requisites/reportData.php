<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "includes/functions.php");
require_once($BASEPATH . "preTallyClass/ReportClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");

$ReportObj = new ReportClass();
$UserObj = new UserClass();
$BalSheetUpdateObj = new BalanceSheetClass();
$blockedDate = $BalSheetUpdateObj->bsEntryUpdateDate($preTally_user_ofid);

$newParm='';
//$H_ID = base64_decode($_COOKIE['HReprt_ID']);
$H_ID = base64_decode($_REQUEST["ID"]);
if (!isset($_REQUEST["posStart"]))
        $_REQUEST["posStart"] = 0;
if (!isset($_REQUEST["count"]))
        $_REQUEST["count"] = 50;

if($H_ID){
    $newParm = $H_ID;
} /*else {
    $temp=  explode('-', $REQUEST['r']);
    $keyTemp = explode('_', $temp['0']);

    foreach ($temp as $value) {
        $newTemp = explode('_', $value);
        $newFilt.= "US_Id =  '".$newTemp['1']."' AND ";
    }
    if($ACL_Obj->ACL_BSheet != 5) 
    $newFilt.= filterBS_User($ACL_Obj->ACL_BSheet, '', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
    $newFilt.='1';
    $newParm = "SELECT US_Id FROM `users_auth` WHERE ".$newFilt;
}  */

//$mask       = $REQUEST['mask']  !=''  ?  $REQUEST['mask']   : $_REQUEST['mask'];
//$SHFilter   = $REQUEST['SHName']!=''  ?  $REQUEST['SHName'] : $_REQUEST['SHName'];
//$MHType     = $REQUEST['MHType']!=''  ?  $REQUEST['MHType'] : $_REQUEST['MHType'];
//$LCName     = $REQUEST['LC_Name']!='' ?  $REQUEST['LC_Name']: $_REQUEST['LC_Name'];
//$ITPending  = $REQUEST['ITPending']!='' ?  $REQUEST['ITPending']: $_REQUEST['ITPending'];
//
//$filter_mask="";
//if($mask != '') {
//    $filter_mask =$filter_mask. " AND IT.IT_Name like '".$mask."%'";
//} if($LCName != '') {
//    $filter_mask = $filter_mask." AND LC.LC_Name like '".$LCName."%'";
//} if($SHFilter) {
//    $filter_mask =$filter_mask. " AND SH.SH_Name like '".$SHFilter."%'";
//} if($MHType) {
//    $filter_mask = $filter_mask." AND IT.MH_Type =".$MHType."";
//} if($ITPending) {
//    $filter_mask  = $filter_mask." AND IT.MH_Type =".$ITPending." AND IT.SH_Id = 0 ";
//}else{
//    $filter_mask = $filter_mask.' AND 1';
//}

$filterData = explode(",",$REQUEST['filter']);

$filter_mask = 'AND 1 ';
if($filterData[0]) {
    $filter_mask .=' AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    //$filter_mask .=' AND IT.IT_Name like "'.$filterData[1].'%"' ;
    // change the old filter and enter new below 16-05-2025
    $filter_mask .=' AND (IT.IT_Name like "%'.$filterData[1].'%" OR DS.DS_Description like "%'.$filterData[1].'%" OR TR.TR_Track like "%'.$filterData[1].'%" )' ;
}
if($filterData[2] != ''){
    $filter_mask .=' AND LC.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3] != '' ) {
    if(!preg_match("/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/", (float)$filterData[3]) )
    $filter_mask .= ' AND BS.BS_Amount LIKE "'.$filterData[3].'%"';            
    else
    $filter_mask .= ' AND (cast(BS.BS_Amount as decimal(11,2))  = "'.$filterData[3].'" OR FLOOR(BS.BS_Amount) = "'.$filterData[3].'")';            
}
if($filterData[4]){
    $filter_mask .=' AND SH.SH_Name like "'.$filterData[4].'" ' ;
}


$ReportObj->reportBSDataFilter($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$newParm,$filter_mask,$_REQUEST["posStart"],$_REQUEST["count"]);
$Count = $ReportObj->reportBSDataCount($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$newParm,$filter_mask);
$RP_Obj = $ReportObj->ReportArray;
//print_r($RP_Obj);

$IE_Type = array('','Income','Expense');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($RP_Obj) {
        $j=$_GET["posStart"]+1;
        foreach($RP_Obj as $rw) {
            $pettyCash = '';
            $UserObj->getReportingPerson($rw->US_Id);
            $US_Obj = $UserObj->UserArray;
            $amount = number_format($rw->BS_Amount,2);  
            if($rw->BS_PettyCashAmt != 0) $pettyCash = '('.number_format($rw->BS_PettyCashAmt,2).')' ;
            $item_desc=$rw->IT_Name;
                    if($rw->DS_Description != ''){$item_desc .= '-'. $rw->DS_Description;}
                    if($rw->TR_Track != ''){$item_desc .= '-Track : '.$rw->TR_Track;}                   
            
//            $income  = ($rw->MH_Type == '1') ? $amount : "---";
//            $expense = ($rw->MH_Type == '2') ? $amount : "---";
                echo '
                <row id="'.$rw->BS_Id.'">
                <userdata name="US_Id">'.$rw->US_Id.','.$US_Obj['US_Report'].'</userdata>
                <userdata name="US_Name">'.$rw->US_FName.' '.$rw->US_LName.','.$US_Obj['US_Name'].'</userdata>
                <userdata name="Entry">'.$rw->IT_Name.'-'. $rw->DS_Description.'-'.$rw->TR_Track.' - Amount : '.$amount.' - Date : '.$rw->BS_Date.'</userdata>
                <userdata name="DS_Description">'.$rw->DS_Description.'</userdata>
                <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>
                <userdata name="TR_Track">'.$rw->TR_Track.'</userdata>
                <userdata name="DS_Id">'.$rw->DS_Id.'</userdata>
                <userdata name="TR_Id">'.$rw->TR_Id.'</userdata>';
               if(($rw->US_Id != $preTally_user_id) && ($blockedDate < $rw->BS_Date) && $ACL_Obj->ACL_BSheet=='4' && $ACL_Obj->ACL_BSheet_VM=='1') {
                    echo'<cell title = "Click here to send message" ><![CDATA[<img src="images/icon/Messages-icon.png" onclick="preTally.Reports.sendCorrectionMsg(this,'.$rw->BS_Id.','.$rw->SH_Id.');"/> ]]></cell>
                    <cell title = "Click here to edit"><![CDATA[<img src="images/icon/edit_icon.gif" style="margin:2px 0; cursor:pointer;" onclick="preTally.Reports.editBalSheetDetailsFromMsg(this,'.$rw->BS_Id.','.$rw->SH_Id.');"/>]]></cell>';
                }else {
                    echo '<cell></cell><cell></cell>';    
                }
                 echo   '<cell>'.$j.'</cell>
                    <cell>'.$IE_Type[$rw->MH_Type].'</cell>';
                    echo '<cell>'.$item_desc.'</cell>
                    <cell>'.$rw->SH_Name.'</cell>
                    <cell>'.$amount.$pettyCash.'</cell>                     
                    <cell title = "Click here to edit" ><![CDATA[<a style="text-decoration:none;" class="target_'.$rw->BS_Id.'" onclick="preTally.Reports.showDetailData(this,'.$rw->BS_Id.','.$rw->SH_Id.');">'.$rw->LC_Name.'</a>]]></cell>
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>