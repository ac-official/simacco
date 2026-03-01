<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MasterReportClass.php");
$MstrRptObj = new MasterReportClass();

$LCId = $REQUEST['LCId'] ? $REQUEST['LCId'] : $preTally_user_lcid ;
$filter=1;
if($REQUEST['mode']=='3'){
$filter.= ' AND BS.IT_Id = '.$REQUEST['ITId'].' AND BS.US_Id = '.$REQUEST['USId'] .' AND LC.OF_Id ='.$preTally_user_ofid.' AND LC.LC_Id IN ('.$LCId.') ';
}else{
$filter.=' AND LC.OF_Id ='.$preTally_user_ofid.' AND LC.LC_Id IN ('.$LCId.') ';
}
//$filter=" 1";

if($REQUEST['mode']=='2'){ //For footer click     
$type_filtr=$REQUEST['typeFilter'];
$subhead_filter=$REQUEST['SHFilter'];
$mainhead_filter=$REQUEST['MHFilter'];
$user_filter=$REQUEST['USId'];
$desc_filter=$REQUEST['Desc'];
$trk_filter=$REQUEST['Track'];
if($type_filtr !='null' && $type_filtr !=''){
    if($type_filtr=="Internal Transfer Recieved")
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
if($subhead_filter !='null' && $subhead_filter !='' && $subhead_filter !='All')     $filter .=' AND SH.SH_Name ="'.$subhead_filter.'" ';
if($mainhead_filter !='null' && $mainhead_filter !='' && $mainhead_filter !='All')     $filter .=' AND MH.MH_Name ="'.$mainhead_filter.'" ';
if($REQUEST['ItemFilter']!='null' && $REQUEST['ItemFilter']!='' && $REQUEST['ItemFilter']!='All') $filter.= ' AND BS.IT_Id = '.$REQUEST['ItemFilter'];
if($desc_filter !='null' && $desc_filter !='' )     $filter .=' AND DS.DS_Description LIKE "%'.$desc_filter.'%" ';
if($trk_filter !='null' && $trk_filter !='' && $trk_filter !='All' && $trk_filter !='0')     $filter .=' AND TR.TR_Track LIKE "%'.$trk_filter.'%" ';
if($user_filter !='null' && $user_filter !='' && $user_filter !='All' && $user_filter !='0')     $filter .=' AND BS.US_Id ="'.$user_filter.'" ';
//if($REQUEST['ITId']!='null' && $REQUEST['ITId']!='')$filter.= ' AND BS.IT_Id = '.$REQUEST['ITId'];
}
if($REQUEST['ITId']!='null' && $REQUEST['ITId']!='')
$filter.= ' AND BS.IT_Id = '.$REQUEST['ITId'];
/*if($user_filter !='null' && $user_filter !='' && $user_filter !='All' && $user_filter !='0')     $filter .=' AND BS.US_Id ="'.$user_filter.'" ';*/

$MstrRptObj->reportBMRItemBasedData($REQUEST['f'],$REQUEST['t'],$filter);
$MR_Obj = $MstrRptObj->MasterReportArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($MR_Obj) {
        $j=$_GET["posStart"]+1;
        foreach($MR_Obj as $rw) {            
            $date = new DateTime($rw->BS_Date); 
//            $color = 'black';
//            if($rw->BS_MinAmount != '' && $rw->BS_MaxAmount != '') {
//                if($rw->BS_Amount < $rw->BS_MinAmount ) $color = '#4DB84D; font-weight : bold;';
//                else if($rw->BS_Amount > $rw->BS_MaxAmount) $color = 'red';
//            }
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
                     
                    <cell>'.round($rw->BS_Amount).'</cell>
                    <cell>'.$date->format('d/m/Y').'</cell>    
                    <cell>'.$rw->US_FName.' '.substr($rw->US_LName,0,1).'</cell>    
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>