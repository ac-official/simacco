<?php
require_once($BASEPATH . "preTallyClass/ReportClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "includes/functions.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$filterData = explode(",",$REQUEST['filter']);
$filter_key = 'AND 1 ';
if($filterData[0]!=0) {
    $filter_key .='AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    $filter_key .='AND IT.IT_Name LIKE "%'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    $filter_key .='AND DS.DS_Description like "%'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter_key .='AND TR.TR_Track like "'.$filterData[3].'%"';
}
if($filterData[4]!=0){
    $filter_key .='AND LC.LC_Id = "'.$filterData[4].'"';
}
if($filterData[5] != ''){
    $filter_key .='AND CONCAT(`US_FName`," ",`US_LName`)  like "'.$filterData[5].'%"';
}
if($filterData[6] != '' ) {   
    if(!preg_match("/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/", (float)$filterData[6]) )
    $filter_key .= ' AND BS.BS_Amount LIKE "'.$filterData[6].'%"';            
    else
    $filter_key .= ' AND (cast(BS.BS_Amount as decimal(11,2))  = "'.$filterData[6].'" OR FLOOR(BS.BS_Amount) = "'.$filterData[6].'")';            
}
$ReportObj = new ReportClass();
//if($ACL_Obj->ACL_BSheet != 5) 
//    $newFilt.= filterBS_User($ACL_Obj->ACL_BSheet,'', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
//    $newFilt.='1';
//    $newParm = "SELECT US_Id FROM `users_auth` WHERE ".$newFilt;
    
$UsrObj  = new UserClass();
$BalSheetUpdateObj = new BalanceSheetClass();
$blockedDate = $BalSheetUpdateObj->bsEntryUpdateDate($preTally_user_ofid);

$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
generate_reptIDS($preTally_user_ofid,$preTally_user_id,$UsrObj->UserArray);
$Rprtid=$_SESSION['user_report_id'];                 
 
$Count = $ReportObj->CountEdt_BSData($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$Rprtid,$filter_key);    
$ReportObj->reportEdt_BSData($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$Rprtid,$filter_key,$_GET["posStart"],$_GET["count"]);
$RP_Obj = $ReportObj->ReportArray;

$IE_Type = array('','Income','Expense');
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($RP_Obj) {
         $j=$_GET["posStart"]+1;
        foreach($RP_Obj as $rw) {            
            $date = new DateTime($rw->BS_Date); 
//            if(!$blockedDate) $besDate = new DateTime($blockedDate); 
//            $blockedBESDate = $blockedDate != '' ? $besDate->format('d/m/Y') : '';
            echo '
                <row id="'.$rw->BS_Id.'">
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>    
                    <userdata name="SH_Track">'.$rw->SH_Track.'</userdata>
                    <userdata name="TR_Id">'.$rw->TR_Id.'</userdata>
                    <userdata name="CHQ_Number">'.$rw->CHQ_Number.'</userdata>
                    <userdata name="BES_Date">'.$blockedDate.'</userdata>
                    <userdata name="BS_PettyCashRefId">'.$rw->BS_PettyCashRefId.'</userdata>
                    <userdata name="IT_PettyCash">'.$rw->IT_PettyCash.'</userdata>
                    <userdata name="BS_Date">'.$rw->BS_Date.'</userdata>
                    <userdata name="BS_Amount">'.$rw->BS_Amount.'</userdata>

                    <cell>'.$j.'</cell>
                    <cell>'.$IE_Type[$rw->MH_Type].'</cell> 
                    <cell>'.$rw->IT_Name.'</cell>
                    <cell>'; if($rw->DS_Description != ''){echo $rw->DS_Description;}else{ echo "--"; } echo '</cell>                    
                    <cell>'; if($rw->TR_Track != ''){echo $rw->TR_Track;}else{ echo "--"; } echo '</cell>     
                     
                    <cell>'.number_format($rw->BS_Amount,2).'</cell>
                    <cell>'.$date->format('d/m/Y').'</cell>    
                    <cell>'.$rw->LC_Name.'</cell>
                    <cell>'.$rw->US_FName.' '.  substr($rw->US_LName,0,1).'</cell> ';   
                    if(($blockedDate < $rw->BS_Date)) {
                        echo '<cell title = "Click here to Delete" ><![CDATA[<img src="images/icon/cross.png" onclick="preTally.EntryEdit.DeleteBSItem('.$rw->BS_Id.');" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    } else {
                        echo '<cell title = "restricted to Delete"><![CDATA[<img src="images/icon/warn_16.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';    
                    }
                    echo'<cell></cell>
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>