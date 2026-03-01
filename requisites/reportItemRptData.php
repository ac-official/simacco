<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}


require_once($BASEPATH . "preTallyClass/MasterReportClass.php");
$MstrRptObj = new MasterReportClass();
$filterData = $REQUEST['ITId'];
$type_filtr = $REQUEST['type_filtr'];
$item_filtr = $REQUEST['item_filtr'];
$subhead_filter = $REQUEST['subhead_filter'];
$mainhead_filter = $REQUEST['mainhead_filter'];
$amount_filter = $REQUEST['amount_filter'];
$loc_filtr = $REQUEST['LCId'];
$desc_filtr = $REQUEST['DescFilter'];
$trk_filtr = $REQUEST['TrkFilter'];
$usr_filtr = $REQUEST['USId'];
$amt_status = (isset($REQUEST['TrkAmtType'])) ? $REQUEST['TrkAmtType'] : ''; 
$filter = ' LC.OF_Id ='.$preTally_user_ofid;
//if($filterData  && $filterData!='undefined'&& $filterData!='null')     $filter .=' AND IT.IT_Id IN ('.$filterData.')';
if($type_filtr !='null' && $type_filtr !=''){
    if($type_filtr=="InternalTransferReceived")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Transfers =1';
    if($type_filtr=="InternalTransferPaid")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Transfers =1';
    if($type_filtr=="BusinessReceived")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Business =1';
    if($type_filtr=="BusinessReturned")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Business =1';
    if($type_filtr=="Income")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Business =0 AND IT.IT_Transfers =0';
    if($type_filtr=="Expense")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Business =0 AND IT.IT_Transfers =0';    
}   
if($loc_filtr !='null' && $loc_filtr !='' && $loc_filtr !='undefined' && $loc_filtr !='0' ) $filter.=  ' AND  BS.LC_Id = '.$loc_filtr.' ';
if($filterData !='null' && $filterData !='' && $filterData !='undefined')     $filter .=' AND IT.IT_Id ='.$filterData.' ';
if($subhead_filter !='null' && $subhead_filter !='')     $filter .=' AND SH.SH_Name ="'.$subhead_filter.'" ';
if($mainhead_filter !='null' && $mainhead_filter !='')     $filter .=' AND MH.MH_Name ="'.$mainhead_filter.'" ';
if($amount_filter !='null' && $amount_filter !='')     $filter .=' AND BS.BS_Amount ="'.$amount_filter.'" ';
if($trk_filtr !='null' && $trk_filtr !='' && $trk_filtr !='undefined')     $filter .=' AND TR.TR_Track LIKE "%'.$trk_filtr.'%" ';
if($desc_filtr !='null' && $desc_filtr !='' && $desc_filtr !='undefined')     $filter .=' AND DS.DS_Description LIKE "%'.$desc_filtr.'%" ';
if($usr_filtr !='null' && $usr_filtr !='' && $usr_filtr !='0')     $filter .=' AND BS.US_Id ="'.$usr_filtr.'" ';
if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;
if($REQUEST['amtSort']=='des')
    $sort_filter=' ORDER BY CAST(BS.BS_Amount AS DECIMAL) DESC';
else if($REQUEST['amtSort']=='asc')
    $sort_filter=' ORDER BY CAST(BS.BS_Amount AS DECIMAL) ASC';
else if($REQUEST['dtSort']=='des')
    $sort_filter=' ORDER BY DATE(BS.BS_Date) DESC';
else if($REQUEST['dtSort']=='asc')
    $sort_filter=' ORDER BY DATE(BS.BS_Date) ASC';
else
    $sort_filter=' ORDER BY IT.IT_Name ASC';
$unver_sum = $MstrRptObj->reportItemBasedData($REQUEST['f'],$REQUEST['t'],$filter,$sort_filter,$_GET["posStart"],$_GET["count"],$amt_status);
$MR_Obj = $MstrRptObj->MasterReportArray;
$Count=$MstrRptObj->row_count;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_SUM">'.round($MstrRptObj->row_sum).'</userdata>
    <userdata name="TL_SUMUV">'.round($unver_sum).'</userdata>
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
            $bgcolor = ''; //16-12-2025  
            
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
                    <cell>'; if($rw->TR_Track != ''){echo $rw->TR_Track;}else{ echo "--"; } echo '</cell> '; 
                    if ($rw->BS_Status != 1) { //16-12-2025
                        $bgcolor    = 'background-color:#ed9f3c;'; 
                        $color      = 'black'; 
                    }
                    
                    echo ' <cell style="color : '.$color.';'.$bgcolor.' ">'.round($rw->BS_Amount).'</cell>
                    <cell>'.$date->format('d/m/Y').'</cell>    
                    <cell>'.$rw->US_FName.' '.substr($rw->US_LName,0,1).'</cell>    
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>