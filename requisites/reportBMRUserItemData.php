<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MasterReportClass.php");
$MstrRptObj = new MasterReportClass();

$filterData = $REQUEST['filter'];
$LCId = $REQUEST['LCId'] ? $REQUEST['LCId'] : $preTally_user_lcid ;
$filter = 'US.OF_Id = '.$preTally_user_ofid.' AND BS.LC_Id IN ('.$LCId.') AND BS.BS_Status = 1 ';
$user_filter=$REQUEST['USId'];
/*if($REQUEST['mode']==2){ //For footer click    */
    $type_filtr=$REQUEST['typeFilter'];
$subhead_filter=$REQUEST['SHFilter'];
$mainhead_filter=$REQUEST['MHFilter'];
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
if($REQUEST['ItemFilter']!='null' && $REQUEST['ItemFilter']!='' && $REQUEST['ItemFilter']!='All')
$filter.= ' AND BS.IT_Id = '.$REQUEST['ItemFilter'];
/*}*/
if($filterData!='null' && $filterData!='' && $filterData!='All') {
    $filter .=' AND IT.IT_Id = '.$filterData;
}
if($user_filter !='null' && $user_filter !='' && $user_filter !='All' && $user_filter !='0')     $filter .=' AND BS.US_Id ="'.$user_filter.'" ';
$MstrRptObj->reportItemUserData($filter,$REQUEST['f'],$REQUEST['t']);
$MRP_Obj = $MstrRptObj->MasterReportArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
            if($MRP_Obj) {
                    $j = 1;
                    foreach($MRP_Obj as $rw) {
                            echo '<row id="'.$j.'">
                                    <userdata name = "IT_Id" >'.$rw->IT_Id.'</userdata>
                                    <userdata name = "US_Id" >'.$rw->US_Id.'</userdata>    
                                    <cell>'.$j.'</cell>                         
                                    <cell name="IT_Name">'.$rw->IT_Name.'</cell>    
                                    <cell name="US_Name">'.$rw->US_Name.'</cell>    
                                    <cell name="BSAmount">'.round($rw->BS_Amount).'</cell>
                                    <cell name="View" title="Click to view more details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'"  onclick=""/>]]></cell>                                             
                            </row>';
                            $j++;
                    }
            } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}   
    echo '</rows>';

?>