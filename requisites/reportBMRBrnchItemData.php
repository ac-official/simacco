<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MasterReportClass.php");
$MstrRptObj = new MasterReportClass();

$filterData = $REQUEST['filter'];
$LCId = $REQUEST['LCId'] ? $REQUEST['LCId'] : $preTally_user_lcid ;

$filter = 'LC.OF_Id = '.$preTally_user_ofid.' AND LC.LC_Id = '.$LCId.' AND BS.BS_Status = 1 ';
if($filterData) {
    $filter .=' AND IT.IT_Id = '.$filterData;
}

$MstrRptObj->reportItemBranchData($filter,$REQUEST['f'],$REQUEST['t']);
$MRP_Obj = $MstrRptObj->MasterReportArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
            if($MRP_Obj) {
                    $j = 1;
                    foreach($MRP_Obj as $rw) {
                            echo '<row id="'.$rw->LC_Id.'">
                                    <userdata name = "IT_Id" >'.$rw->IT_Id.'</userdata>
                                    <cell>'.$j.'</cell>                         
                                    <cell name="LC_Name">'.$rw->IT_Name.'</cell>    
                                    <cell name="LC_Name">'.$rw->LC_Name.'</cell>    
                                    <cell name="BSAmount">'.$rw->BS_Amount.'</cell>
                                    <cell name="View" title="Click to view more details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'"  onclick=""/>]]></cell>    
                            </row>';
                            $j++;
                    }
            } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}   
    echo '</rows>';

?>