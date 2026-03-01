<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}


require_once($BASEPATH . "preTallyClass/UserBranchReportClass.php");
$MstrRptObj = new UserBranchReportClass();
$filter= 'BS.BS_IEByLC = '.$REQUEST['LCId'] .' AND LC.OF_Id ='.$preTally_user_ofid;
if($REQUEST['ITId']!="" && $REQUEST['ITId']!="null" && $REQUEST['mode']!='2'){
    $filter .= ' AND BS.IT_Id IN ( '.$REQUEST['ITId'].') ';
}
$filterData = $REQUEST['filter'];
$type_filtr = $REQUEST['type_filtr'];
$item_filtr = $REQUEST['item_filtr'];
$subhead_filter = $REQUEST['subhead_filter'];
$mainhead_filter = $REQUEST['mainhead_filter'];
$amount_filter = $REQUEST['amount_filter'];
if($type_filtr !='null' && $type_filtr !=''){
    if($type_filtr=="Internal Transfer Received")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Transfers =1';
    if($type_filtr=="Internal Transfer Paid")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Transfers =1';
    if($type_filtr=="Business Received")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Business =1';
    if($type_filtr=="Business Returned")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Business =1';
    if($type_filtr=="Income")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Business =0 AND IT.IT_Transfers =0';
    if($type_filtr=="Expense")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Business =0 AND IT.IT_Transfers =0';    
}   
if($item_filtr !='null' && $item_filtr !='')     $filter .=' AND IT.IT_Id ='.$item_filtr.' ';
if($subhead_filter !='null' && $subhead_filter !='')     $filter .=' AND SH.SH_Name ="'.$subhead_filter.'" ';
if($mainhead_filter !='null' && $mainhead_filter !='')     $filter .=' AND MH.MH_Name ="'.$mainhead_filter.'" ';

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
$toYear     = ($REQUEST['t']) ? date("Y",strtotime($REQUEST['t'])) : date('Y');

if($REQUEST['t']) {   // if to date
    $toMonth = ltrim(date("m",strtotime($REQUEST['t'])),'0');
} else {  
    $toMonth = ($toYear == date('Y')) ? date('n') : 12;
}
  
if($fromYear == ($toYear-1) && $fromYear == date("Y") ) {   // current financial year
    $toMonth = date('n');   // show upto current month
    $toYear  = date("Y");
} 

if($REQUEST['m']) {   // month is selected
    $toMonth            = ltrim($REQUEST['m'],'0');
     $day            =  cal_days_in_month(CAL_GREGORIAN, $REQUEST['m'], $toYear);
    $fromYear   = date('Y')-1;
    $toYear     = date('Y');
    $REQUEST['f']   = '01-'.$REQUEST['m'].'-'.$fromYear;
    $REQUEST['t']   = $day.'-'.$REQUEST['m'].'-'.$toYear;   
} 

$MstrRptObj->reportItemBasedData($REQUEST['f'],$REQUEST['t'],$filter);
$MR_Obj = $MstrRptObj->MasterReportArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($MR_Obj) {        
        $j=$_GET["posStart"]+1;                    
        foreach($MR_Obj as $rw) {            
            $date = new DateTime($rw->BS_Date); 
            $color = 'black';
            if($rw->BS_MinAmount != '' && $rw->BS_MaxAmount != '') {
                if($rw->BS_Amount < $rw->BS_MinAmount ) $color = '#4DB84D; font-weight : bold;';
                else if($rw->BS_Amount > $rw->BS_MaxAmount) $color = 'red';
            }             
            $footer     = $footerStyle = $cellFormat = '';
            $footer         .= ",{#stat_total}";
            $footerStyle    .= ",text-align:right;";
            $cellFormat     .= ",70";            
           echo '<head>';           
            echo '<afterInit>
                    <call command="setColumnMinWidth"><param>,'.$cellFormat.',,</param></call>
                    <call command="attachFooter"><param>Total,#cspan,#cspan,#cspan,{#stat_total},,,,</param>
                    <param>text-align:left'.$footerStyle.',text-align:right,text-align:right,text-align:right</param></call>
            </afterInit> ';
        echo '</head>';              
            if(!$rw->PaidLC_Name){
            $Lcname="Not Assigned";
            $UserName="Not Assigned";
            }
            else{
                 $Lcname=$rw->PaidLC_Name; 
                 if(!$rw->PaidUS_Name)$UserName="All Staff";
                   else $UserName=$rw->PaidUS_Name;   
            }
            echo '
                <row id="'.$rw->BS_Id.'">
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>    
                    <userdata name="SH_Track">'.$rw->SH_Track.'</userdata>
                    <userdata name="TR_Id">'.$rw->TR_Id.'</userdata>
                    <userdata name="CHQ_Number">'.$rw->CHQ_Number.'</userdata>
                    <cell>'.$j.'</cell>
                    <cell>'.$rw->IT_Name.'</cell>
                    <cell>'; if($rw->DS_Description != ''){echo $rw->DS_Description;}else{ echo "--"; } echo '</cell>                    
                    <cell>'; if($rw->TR_Track != ''){echo $rw->TR_Track;}else{ echo "--"; } echo '</cell>     
                     
                    <cell style="color : '.$color.'">'.round($rw->BS_Amount).'</cell>
                    <cell>'.$date->format('d/m/Y').'</cell>    
                    <cell>'.$rw->US_FName.' '.substr($rw->US_LName,0,1).'</cell>    
                    <cell>'.$Lcname.'</cell>    
                    <cell>'.$UserName.'</cell>        
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>