<?php
require_once($BASEPATH . "preTallyClass/ExpenseControlClass.php");
include_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
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
    $filter_key .='AND IT.IT_Name LIKE"'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    $filter_key .='AND DS.DS_Description like "%'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter_key .='AND TR.TR_Track like "'.$filterData[3].'%"';
}
if($filterData[4] == 1){
    $filter_key .='AND BS.BS_Amount < BS.BS_MinAmount ';
}else if($filterData[4] == 2){
    $filter_key .='AND BS.BS_MinAmount <= BS.BS_Amount AND BS.BS_Amount <= BS.BS_MaxAmount ';
}else if($filterData[4] == 3){
    $filter_key .='AND BS.BS_Amount > BS.BS_MaxAmount AND BS.BS_MinAmount != 0 AND BS.BS_MaxAmount != 0 ';
}
if($filterData[5]!=0){
    $filter_key .='AND LC.LC_Id = "'.$filterData[5].'"';
}
if($filterData[6] != ''){
    $filter_key .='AND CONCAT(`US_FName`," ",`US_LName`)  like "'.$filterData[6].'%"';
}
$sortFlter  =   " BS.BS_Date DESC ";

if($filterData[8] != '' && $filterData[7] != '') {    
    if($filterData[8] == '10')  {  // date sorting
        if($filterData[7]   ==  0){
           $sortFlter  =   " BS.BS_Date ASC ";
        }  
    } else if($filterData[8] == '7') {   // amount filtering
        
        if($filterData[7]   ==  0){
           $ascFlter    =   "ASC";
        } else if($filterData[7] ==  1){
           $ascFlter    =   "DESC";
        }  
        $sortFlter      =  " CAST(BS.BS_Amount AS SIGNED) $ascFlter , $sortFlter";
    }
}

$ExpCntrlObj = new ExpenseControlClass();
$BalSheetUpdateObj = new BalanceSheetClass();
$blockedDate = $BalSheetUpdateObj->bsEntryUpdateDate($preTally_user_ofid); 
//if($ACL_Obj->ACL_BSheet != 5) 
//    $newFilt.= filterBS_User($ACL_Obj->ACL_BSheet,'', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
//    $newFilt.='1';
//    $newParm = "SELECT US_Id FROM `users_auth` WHERE ".$newFilt;
    
$UsrObj  = new UserClass();
$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
generate_reptIDS($preTally_user_ofid,$preTally_user_id,$UsrObj->UserArray);
$Rprtid=$_SESSION['user_report_id'];
//$UsrObj->userNotificationHierarchy(' WHERE US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
$Count = $ExpCntrlObj->expCntrlDataCount($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$Rprtid,$filter_key);    
$ExpCntrlObj->expCntrlData($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$Rprtid,$filter_key,$_GET["posStart"],$_GET["count"],$sortFlter);
$RP_Obj = $ExpCntrlObj->ExpenseControlArray;

$IE_Type = array('','Income','Expense');
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($RP_Obj) {
         $j=$_GET["posStart"]+1;
        foreach($RP_Obj as $rw) { 
            $pettyCash = '';
            
            $UsrObj->getReportingPerson($rw->US_Id);
            $US_Obj = $UsrObj->UserArray;
            
            $date = new DateTime($rw->BS_Date); 
            
            $color = 'black';
            if( $rw->BS_MinAmount != 0 || $rw->BS_MaxAmount != 0) { 
                if($rw->BS_Amount < $rw->BS_MinAmount ) $color = '#4DB84D; font-weight : bold;';
                else if($rw->BS_Amount > $rw->BS_MaxAmount) $color = 'red';
            }
            
            $amount = number_format($rw->BS_Amount,2); 
            if($rw->BS_PettyCashAmt != 0) $pettyCash = '('.number_format($rw->BS_PettyCashAmt,2).')' ;
            
            echo '
                <row id="'.$rw->BS_Id.'">
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>    
                    <userdata name="TR_Id">'.$rw->TR_Id.'</userdata>
                    <userdata name="TR_Track">'.$rw->TR_Track.'</userdata>
                    <userdata name="DS_Id">'.$rw->DS_Id.'</userdata>    
                    <userdata name="US_Id">'.$rw->US_Id.','.$US_Obj['US_Report'].'</userdata>
                    <userdata name="US_Name">'.$rw->US_FName.' '.$rw->US_LName.','.$US_Obj['US_Name'].'</userdata>
                    <userdata name="Entry">'.$rw->IT_Name.'-'. $rw->DS_Description.'-'.$rw->TR_Track.' - Amount : '.$amount.' - Date : '.$rw->BS_Date.'</userdata>
                    <userdata name="DS_Description">'.$rw->DS_Description.'</userdata>';
                    

                if(($rw->US_Id != $preTally_user_id) && ($blockedDate < $rw->BS_Date) && $ACL_Obj->ACL_BSheet=='4' && $ACL_Obj->ACL_BSheet_VM=='1') {
                    echo '<cell title = "Click here to send message" ><![CDATA[<img src="images/icon/Messages-icon.png" onclick="preTally.ExpenseControl.sendCorrectionMsg(this,'.$rw->BS_Id.','.$rw->SH_Id.');"/> ]]></cell>
                    <cell title = "Click here to edit" ><![CDATA[<img src="images/icon/edit_icon.gif" style="margin:2px 0; cursor:pointer;" onclick="preTally.ExpenseControl.editExpCnrtlEntryDetails(this,'.$rw->BS_Id.','.$rw->SH_Id.');"/>]]></cell>';
                }
                else {
                    echo '<cell></cell><cell></cell>';    
                }
                echo '<cell>'.$j.'</cell>
                    <cell>'.$IE_Type[$rw->MH_Type].'</cell> 
                    <cell>'.$rw->IT_Name.'</cell>
                    <cell>'.$rw->DS_Description.'</cell>                    
                    <cell>'; if($rw->TR_Track != ''){echo $rw->TR_Track;}else{ echo "--"; } echo '</cell>     
                    <cell style="color : '.$color.'; ">'.$amount.$pettyCash.'</cell>
                    <cell>';if($rw->BS_MinAmount != 0 && $rw->BS_MinAmount != 0 ){ echo $rw->BS_MinAmount; } else { echo '--';} echo '</cell>
                    <cell>';if($rw->BS_MaxAmount != 0 && $rw->BS_MaxAmount != 0 ){ echo $rw->BS_MaxAmount; } else { echo '--';} echo '</cell>
                    <cell>'.$date->format('d/m/Y').'</cell>    
                    <cell>'.$rw->LC_Name.'</cell>
                    <cell>'.$rw->US_FName.' '.  substr($rw->US_LName,0,1).'</cell>    
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell colspan = "11"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
     
     }
echo '</rows>';
?>