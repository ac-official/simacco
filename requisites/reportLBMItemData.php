<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MasterReportsLocationBasedClass.php");
$MstrRptObj = new MasterReportsLocationBasedClass();
$filterData = $REQUEST['filter'];
$filter = 'LC.OF_Id = '.$preTally_user_ofid.' AND BS.BS_Status = 1 AND IT.IT_Status != 4';
if($filterData) {
    $filter .=' AND IT.IT_Id = '.$filterData;
}

$entryTypeSum = $MstrRptObj->entryTypeSumItemRpt($filter,$REQUEST['f'],$REQUEST['t']);
$MstrRptObj->reportItemData($filter,$REQUEST['f'],$REQUEST['t']);
$MRP_Obj = $MstrRptObj->MasterReportArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>
        <userdata name = "InternalTransferPaid">'; echo ($entryTypeSum['InternalTransferPaid']) ? round($entryTypeSum['InternalTransferPaid']) : 0 ; echo '</userdata>
        <userdata name = "InternalTransferReceived">'; echo ($entryTypeSum['InternalTransferReceived']) ? round($entryTypeSum['InternalTransferReceived']) : 0 ; echo '</userdata>
        <userdata name = "BusinessReceived">'; echo ($entryTypeSum['BusinessReceived']) ? round($entryTypeSum['BusinessReceived']) : 0 ; echo '</userdata>
        <userdata name = "BusinessReturned">'; echo ($entryTypeSum['BusinessReturned']) ? round($entryTypeSum['BusinessReturned']) : 0 ; echo '</userdata>
        <userdata name = "Income">'; echo ($entryTypeSum['Income']) ? round($entryTypeSum['Income']) : 0 ; echo '</userdata>
        <userdata name = "Expense">'; echo ($entryTypeSum['Expense']) ? round($entryTypeSum['Expense']) : 0 ; echo '</userdata>';
            if($MRP_Obj) {
                    $j = 1;
                    foreach($MRP_Obj as $rw) {
                        if($rw->IT_Transfers==1 && $rw->MH_Type == 1 )$type="Internal Transfer Received";
                        else if($rw->IT_Transfers==1 && $rw->MH_Type == 2 )$type="Internal Transfer Paid";
                        else if($rw->IT_Business==1 && $rw->MH_Type==1)$type="Business Received";
                        else if($rw->IT_Business==1 && $rw->MH_Type==2)$type="Business Returned";
                        else if($rw->MH_Type==1)$type="Income";
                        else if($rw->MH_Type==2)$type="Expense";
                            echo '<row id="'.$rw->IT_Id.'">
                                    <userdata name="IT_Name">'.$rw->IT_Name.'</userdata>
                                    <cell title=" ">'.$j.'</cell>
                                    <cell name="MH_Type" title=" ">'.$type.'</cell>   
                                    <cell name="IT_Name" title=" ">'.$rw->IT_Name.'</cell>
                                    <cell name="SH_Name" title=" ">'.$rw->SH_Name.'</cell>
                                    <cell name="MH_Name" title=" ">'.$rw->MH_Name.'</cell> 
                                    <cell name="BSAmount" title=" ">'.round($rw->BS_Amount).'</cell>
                                    <cell name="View" title="Click to view more details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'"  onclick=""/>]]></cell>
                            </row>';
                            $j++;
                    }
            } else { echo '<row id="0"><cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}   
    echo '</rows>';

?>