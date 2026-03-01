<?php

error_reporting(E_ALL ^ E_NOTICE);
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/HistoryClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "includes/functions.php");

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$filterData = explode(",",$REQUEST['filter']);
$filter_key = 'AND 1 ';
if($filterData[0]!=0) {
    $filter_key .='AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1]){
    $filter_key .='AND IT.IT_Name LIKE "'.$filterData[1].'%"' ;
}
if($filterData[2]){
    $filter_key .='AND DS.DS_Description like "'.$filterData[2].'%"';
}
if($filterData[3]){
    $filter_key .='AND TR.TR_Track like "'.$filterData[3].'%"';
}
if(is_numeric($filterData[4])){
    $filter_key .='AND LC.LC_Id = "'.$filterData[4].'"';
}
if($filterData[5]){
    $filter_key .='AND CONCAT(`US_FName`," ",`US_LName`)  like "'.$filterData[5].'%"';
}
if($filterData[6] != ''){
    $filter_key .='AND BS.BS_Status = '.$filterData[6].' ';
}

$HI_BSObj = new HistoryClass();
//if($ACL_Obj->ACL_BSheet != 5) 
//    $newFilt.= filterBS_User($ACL_Obj->ACL_BSheet,'', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
//    $newFilt.='1';
//    $newParm = "SELECT US_Id FROM `users_auth` WHERE ".$newFilt;
    
$UsrObj  = new UserClass();
$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
generate_reptIDS($preTally_user_ofid,$preTally_user_id,$UsrObj->UserArray);
$Rprtid=$_SESSION['user_report_id']; 
$Count = $HI_BSObj->countBSEntries($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$Rprtid,$filter_key);    
$HI_BSObj->viewAllBSEntries($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$Rprtid,$filter_key,$_GET["posStart"],$_GET["count"]);
$HIObj = $HI_BSObj->HistoryArray;

$BS_Status = array('Deleted','Published','Waiting in Bank Book','Rejected Bank Entry');
$IE_Type = array('','Income','Expense');
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($HIObj) {
         $j=$_GET["posStart"]+1;
        foreach($HIObj as $rw) {            
            $date = new DateTime($rw->BS_Date); 
            echo '
                <row id="'.$rw->BS_Id.'">
                    
                    <cell>'.$j.'</cell>
                    <cell>'.$IE_Type[$rw->MH_Type].'</cell> 
                    <cell>'.$rw->IT_Name.'</cell>
                    <cell>'; if($rw->DS_Description != ''){echo $rw->DS_Description;}else{ echo "--"; } echo '</cell>                    
                    <cell>'; if($rw->TR_Track != ''){echo $rw->TR_Track;}else{ echo "--"; } echo '</cell>     
                     
                    <cell>'.$rw->BS_Amount.'</cell>
                    <cell>'.$date->format('d/m/Y').'</cell>    
                    <cell>'.$rw->LC_Name.'</cell>
                    <cell>'.$rw->US_FName.' '.  substr($rw->US_LName,0,1).'</cell>  
                    <cell>'.$BS_Status[$rw->BS_Status].'</cell>
                    <cell></cell>
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>