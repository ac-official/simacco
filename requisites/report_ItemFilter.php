<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/MasterReportClass.php");
include_once($BASEPATH . "preTallyClass/ZoneClass.php");
require_once($BASEPATH . "includes/functions.php");
$MstrRptObj = new MasterReportClass();
$ZoneObj = new ZoneClass();
$filterData = $REQUEST['filter'];
$filter='';
if($REQUEST['LCId'])
$filter     = 'LC.OF_Id = '.$preTally_user_ofid.' AND LC.LC_Id='.$REQUEST['LCId'].' AND BS.BS_Status = 1 ';
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
$ITId = $REQUEST['ITId'];
    $REQUEST['f']   = '01-'.$fromMonth.'-'.$fromYear;
    $REQUEST['t']   = $day.'-'.$toMonth.'-'.$toYear;   
$typeOfEntryFiter='';
 if($REQUEST['typeOfEntry']){
     $REQUEST['typeOfEntry']=str_replace(' ','',$REQUEST['typeOfEntry']);
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
$MstrRptObj->getReportFilters($filter,$REQUEST['f'],$REQUEST['t']);
$IT_NotfObj = $MstrRptObj->RptFilterItemArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
$default_select='';
if($ITId==''|| $ITId=='null'){
    $default_select= 'selected = "'. 1 .'" ' ;     
}
 else { 
     $default_select = 'selected = "'. 0 .'" ' ;      
 }    
echo '<option '.$default_select.'>Select Item</option>';
if($IT_NotfObj){
    $count = 1;
    if ($REQUEST["type"]=="filt") echo '<option value="All" selected="true">All</option>';
    foreach($IT_NotfObj as $rw){ 
        if($ITId && $rw->IT_Id == $ITId) { $selected = 'selected = "'. 1 .'" ' ; } 
        //else if($count == 1 && $REQUEST['filter'] == "Reports") { $selected = 'selected = "'. 1 .'" ' ; } 
        else { $selected = 'selected = "'. 0 .'" ' ; }        
        echo '<option value="'.$rw->IT_Id.'"  '. $selected .'>'.str_replace("&","&amp;",$rw->IT_Name).'</option>';
        $count++;
    }
}
echo '</complete>';
?>