<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$locateid   = $_GET['lcid'];
$userid     = $_GET['user_id'];
require_once($BASEPATH."preTallyClass/UserBranchReportClass.php");
include_once($BASEPATH . "preTallyClass/ZoneClass.php");
$UserObj = new UserBranchReportClass(); 
$ZoneObj = new ZoneClass();
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

if($REQUEST['LCId'])
$filter     = 'LC.OF_Id = '.$preTally_user_ofid.' AND BS.LC_Id='.$REQUEST['LCId'].' AND BS.BS_Status = 1 ';
else
$filter     = 'LC.OF_Id = '.$preTally_user_ofid.' AND BS.BS_Status = 1 ';  
if($REQUEST['znid'] && $REQUEST['znid']!='null' && $REQUEST['znid']!='All' && $REQUEST['znid']!='null')
{
$znlcid=$ZoneObj->getZoneLocations($REQUEST['znid']);
$filter.= " AND BS.BS_IEByLC IN (".$znlcid.") ";
}
if(!$REQUEST['f'] ) {   // no from date
    if($REQUEST['t']){
        $tyear= date("Y",strtotime($REQUEST['t']))-1;
        $REQUEST['f'] = "01-04-".$tyear;  // show yealry data;
    }
    else {              //  no till date
        $REQUEST['f'] = "01-04-".date("Y")-1;
        $REQUEST['t'] = date('d-m-Y', time());
    }    
}
$fromMonth  = ($REQUEST['f']) ? ltrim(date("m",strtotime($REQUEST['f'])),'0') : 1;
$fromYear   = ($REQUEST['f']) ? date("Y",strtotime($REQUEST['f'])) : date('Y');
$toMonth    = ($REQUEST['t']) ? ltrim(date("m",strtotime($REQUEST['t'])),'0') : 1;
$toYear     = ($REQUEST['t']) ? date("Y",strtotime($REQUEST['t'])) : date('Y');
$day = date("t", strtotime('01-'.$toMonth.'-'.$toYear));

    $REQUEST['f']   = '01-'.$fromMonth.'-'.$fromYear;
    $REQUEST['t']   = $day.'-'.$toMonth.'-'.$toYear;   
$typeOfEntryFiter='';
 if($REQUEST['typeOfEntry']){
    if($REQUEST['typeOfEntry']!="All"){
        if($REQUEST['typeOfEntry']=="InternalTransferReceived"){
            $typeOfEntryFiter = " AND IT.IT_Transfers=1 AND  IT.MH_Type = 1 ";//Internal Transfer Received;
        }else if($REQUEST['typeOfEntry']=="InternalTransferPaid"){
            $typeOfEntryFiter = " AND IT.IT_Transfers=1 AND  IT.MH_Type = 2 ";
        }else if($REQUEST['typeOfEntry']=="BusinessReceived"){
            $typeOfEntryFiter = " AND IT.IT_Business=1 AND  IT.MH_Type = 1 ";
        }else if($REQUEST['typeOfEntry']=="BusinessReturned"){
            $typeOfEntryFiter = " AND IT.IT_Business=1 AND  IT.MH_Type = 2 ";
        }else if($REQUEST['typeOfEntry']=="Income"){
            $typeOfEntryFiter = " AND IT.MH_Type = 1 AND IT.IT_Transfers=0 AND IT.IT_Business=0";
        }else if($REQUEST['typeOfEntry']=="Expense"){
            $typeOfEntryFiter = " AND IT.MH_Type = 2 AND IT.IT_Transfers=0 AND IT.IT_Business=0";
        }
    }
 }  
    $filter=$filter.$typeOfEntryFiter;    
    $UserObj->getaddedUserFilterCombo($REQUEST['f'],$REQUEST['t'],$filter);
    $US_Obj = $UserObj->UserFitlerData;

echo '<complete>';
echo '<option value="0" selected="true">Select User</option>';	
if($US_Obj){
    foreach($US_Obj as $rw){
        echo '<option value="'.$rw->US_Id.'">'.$rw->NAME.'</option>';
    }
}
echo '</complete>';
?>