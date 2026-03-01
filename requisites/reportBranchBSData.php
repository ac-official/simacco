<?php
if (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . 'includes/functions.php');
include_once($BASEPATH."preTallyClass/BranchBSClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;
$ACLReq = $ACL_Obj->ACL_BSheet;
$temp       =  explode('-', $REQUEST['r']);
$OFkeyTemp  = explode('_', $temp['0']);
$LCkeyTemp  = explode('_', $temp['1']);
$BAkeyTemp  = explode('_', $temp['2']);

if( $temp['1']  && $temp['2'] ){
    $filter = ' BA.LC_Id = '.$LCkeyTemp[1].' AND BA.BA_Id = '.$BAkeyTemp[1].' ';
}else if($temp['1'] && !$temp['2']){
    $filter = ' BA.LC_Id = '.$LCkeyTemp[1].' ';
}else if($ACLReq == 2){
    $filter = 'BA.LC_Id = '.$preTally_user_lcid.' ';
}else{
    $filter = 'LC.OF_Id = '.$OFkeyTemp[1].' ';
}
/*
$mask       = $REQUEST['mask']  !=''  ?  $REQUEST['mask']   : $_REQUEST['mask'];
$SHFilter   = $REQUEST['SHName']!=''  ?  $REQUEST['SHName'] : $_REQUEST['SHName'];
$MHType     = $REQUEST['MHType']!=''  ?  $REQUEST['MHType'] : $_REQUEST['MHType'];
$LCName     = $REQUEST['LC_Name']!='' ?  $REQUEST['LC_Name']: $_REQUEST['LC_Name'];

$filterData = explode(",",$REQUEST['filter']);
if($filterData[0]) {
    $filter_mask .=' AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    $filter_mask .=' AND IT.IT_Name like "'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    $filter_mask .=' AND LC.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3]){
    $filter_mask .=' AND SH.SH_Name like "'.$filterData[3].'" ' ;
}
if($filterData[4] != '') {
    if(!preg_match("/^[0-9.]+$/", $filterData[4]) )
        $filter_mask .= ' AND BS.BS_Amount = "'.$filterData[4].'"';
    else
        $filter_mask .= ' AND BS.BS_Amount = "'.+$filterData[4].'"';
}
*/
$filterData = explode(",",$REQUEST['filter']);

$filter_mask = 'AND 1 ';
if($filterData[0]) {    
    //$filter_mask .=' AND IT.MH_Type = "'.$filterData[0].'" ';
    //change the old fileter and new added below 19-05-2025
    $mytypary = explode('-',$filterData[0]);
    if (isset($mytypary[1]) && $mytypary[0] != 0) {
        $filter_mask .= ' AND IT.MH_Type = "'.$mytypary[0].'" ';
        if ($mytypary[1] == 1) { // expecet internal 
            $filter_mask .= ' AND IT.IT_Transfers = "0"';
        } else if ($mytypary[1] == 2) { // internal only
            $filter_mask .= ' AND IT.IT_Transfers = "1"';
        } 
    } else if($mytypary[0] != 0) {
        $filter_mask .= ' AND IT.MH_Type = "'.$mytypary[0].'" ';
    }    
}
if($filterData[1] != ''){
    //$filter_mask .=' AND IT.IT_Name like "'.$filterData[1].'%"' ;
    // change the old filter and enter new below 16-05-2025
    $filter_mask .=' AND (IT.IT_Name like "%'.$filterData[1].'%" OR DS.DS_Description like "%'.$filterData[1].'%" OR TR.TR_Track like "%'.$filterData[1].'%" )' ;
}
if($filterData[2] != ''){
    $filter_mask .=' AND LC.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3]){
    $filter_mask .=' AND IT.SH_Id = "'.$filterData[3].'" ' ;
}
if($filterData[4] != '' ) {
    if(!preg_match("/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/", (float)$filterData[4]) )
    $filter_mask .= ' AND BS.BS_Amount LIKE "'.$filterData[4].'%"';            
    else
    $filter_mask .= ' AND (cast(BS.BS_Amount as decimal(11,2))  = "'.$filterData[4].'" OR FLOOR(BS.BS_Amount) = "'.$filterData[4].'")';            
}
/*echo $filter;
echo '<br/>';
echo $filter_mask*/

$BranchBSObj = new BranchBSClass();
$UserObj = new UserClass();
$BalSheetUpdateObj = new BalanceSheetClass();
$blockedDate = $BalSheetUpdateObj->bsEntryUpdateDate($preTally_user_ofid); 

$BranchBSObj->reportBranchBSData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter, $filter_mask,$_GET["posStart"],$_GET["count"]);
$Count = $BranchBSObj->reportBranchBSDataCount($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter, $filter_mask);
$BOC_Obj = $BranchBSObj->BankBSArray;
$IE_Type = array('','Income','Expense');
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($BOC_Obj) {
        $j=$_GET["posStart"]+1;
        foreach($BOC_Obj as $rw) {
            $pettyCash = '';
            $UserObj->getReportingPerson($rw->US_Id);
            $US_Obj = $UserObj->UserArray;
            $amount = number_format($rw->BS_Amount,2); 
            if($rw->BS_PettyCashAmt != 0) $pettyCash = '('.number_format($rw->BS_PettyCashAmt,2).')' ;
            
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
                if($rw->US_Id != $preTally_user_id  && ($blockedDate < $rw->BS_Date) && $ACL_Obj->ACL_BSheet=='4' && $ACL_Obj->ACL_BSheet_VM=='1') {
                    echo '<cell title = "Click here to send message" ><![CDATA[<img src="images/icon/Messages-icon.png" onclick="preTally.BranchBSReports.sendCorrectionMsg(this,'.$rw->BS_Id.','.$rw->SH_Id.');"/> ]]></cell>
                    <cell title = "Click here to edit" ><![CDATA[<img src="images/icon/edit_icon.gif" style="margin:2px 0; cursor:pointer;" onclick="preTally.BranchBSReports.editBalSheetDetailsFromMsg(this,'.$rw->BS_Id.','.$rw->SH_Id.');"/>]]></cell>';
                }
                else {
                    echo '<cell></cell><cell></cell>';    
                }
                echo ' <cell>'.$j.'</cell>
                   <cell>'.$IE_Type[$rw->MH_Type].'</cell>';
                    echo '<cell>'.$rw->IT_Name;
                    if($rw->DS_Description != ''){echo '-'. $rw->DS_Description;}
                    if($rw->TR_Track != ''){echo '-Track : '.$rw->TR_Track;}
                    echo '</cell>
                    <cell>'.$rw->SH_Name.'</cell>
                    <cell>'.$amount.$pettyCash.'</cell>
                    <cell><![CDATA[<a style="text-decoration:none;" class="targetBranch_'.$rw->BS_Id.'" onclick="preTally.BranchBSReports.showDetailData(this,'.$rw->BS_Id.','.$rw->SH_Id.');">'.$rw->LC_Name.'</a>]]></cell>
                </row>';
            $j++;
        }
    } 
    else { 
        echo '<row id="0">  <cell colspan="8"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
    }
echo '</rows>';
?>