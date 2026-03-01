<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MasterReportClass.php");
$MstrRptObj = new MasterReportClass();
$filterData = $REQUEST['ItmFilter'];
//$filter = 'LC.OF_Id = '.$preTally_user_ofid.' AND BS.BS_Status = 1 ';
$filter = 'LC.OF_Id = '.$preTally_user_ofid.' AND (BS.BS_Status = 1 OR BS.BS_Status=2) ';//9-12-24
if($filterData && $filterData!='null') {
    $itmfilter .=' AND IT.IT_Id = '.$filterData;
}
$typeOfEntryFiter='';
 if($REQUEST['typeOfEntry']){
    if($REQUEST['typeOfEntry']!="All"){
        if($REQUEST['typeOfEntry']=="InternalTransferReceived"){
            $typeOfEntryFiter = " AND IT.IT_Transfers=1 AND  MH.MH_Type = 1 ";//Internal Transfer Received;
        }else if($REQUEST['typeOfEntry']=="InternalTransferPaid"){
            $typeOfEntryFiter = " AND IT.IT_Transfers=1 AND  MH.MH_Type = 2 ";
        }else if($REQUEST['typeOfEntry']=="BusinessReceived"){
            $typeOfEntryFiter = " AND IT.IT_Business=1 AND  MH.MH_Type = 1 ";
        }else if($REQUEST['typeOfEntry']=="BusinessReturned"){
            $typeOfEntryFiter = " AND IT.IT_Business=1 AND  MH.MH_Type = 2 ";
        }else if($REQUEST['typeOfEntry']=="Income"){
            $typeOfEntryFiter = " AND IT.MH_Type = 1 AND IT.IT_Transfers=0 AND IT.IT_Business=0";
        }else if($REQUEST['typeOfEntry']=="Expense"){
            $typeOfEntryFiter = " AND IT.MH_Type = 2 AND IT.IT_Transfers=0 AND IT.IT_Business=0";
        }
    }
 }
    // 26-12-2024 Start
    $filterAry      = [];
    $notuse         = ["span","spa","pan","class","cla","clas","lass","las","ass", "bold", "bol","old","words", "wor","word","ord", "ords","dwo","dwor","ldw","rds"];
    if (isset($REQUEST['item_text_filtr']) && $REQUEST['item_text_filtr'] != "") {
        $filterAry      = explode('-$Plus$-',$REQUEST['item_text_filtr']);
        foreach($filterAry As $pname) {  
            $filter     .= (trim($pname) != '') ? " AND IT.IT_Name LIKE '%" . trim($pname) . "%'":"";
        }
    }
    // End 
$filterData=$filter.' '.$itmfilter.' '.$typeOfEntryFiter;
$entryTypeSum = $MstrRptObj->entryTypeSumItemRpt($filter,$REQUEST['f'],$REQUEST['t'],1);
//$MstrRptObj->reportItemData($filterData,$REQUEST['f'],$REQUEST['t']);
$MstrRptObj->reportItemDataBoth($filterData,$REQUEST['f'],$REQUEST['t']); //
$MRP_Obj = $MstrRptObj->MasterReportArray;
//echo $MstrRptObj->sql;
//die();
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>
        <userdata name = "InternalTransferPaid">'; echo ($entryTypeSum['InternalTransferPaid']) ? round($entryTypeSum['InternalTransferPaid']) : 0 ; echo '</userdata>
        <userdata name = "InternalTransferReceived">'; echo ($entryTypeSum['InternalTransferReceived']) ? round($entryTypeSum['InternalTransferReceived']) : 0 ; echo '</userdata>
        <userdata name = "BusinessReceived">'; echo ($entryTypeSum['BusinessReceived']) ? round($entryTypeSum['BusinessReceived']) : 0 ; echo '</userdata>
        <userdata name = "BusinessReturned">'; echo ($entryTypeSum['BusinessReturned']) ? round($entryTypeSum['BusinessReturned']) : 0 ; echo '</userdata>
        <userdata name = "Income">'; echo ($entryTypeSum['Income']) ? round($entryTypeSum['Income']) : 0 ; echo '</userdata>
        <userdata name = "Expense">'; echo ($entryTypeSum['Expense']) ? round($entryTypeSum['Expense']) : 0 ; echo '</userdata>';
    //09-12-2024
    echo '<userdata name = "UvInternalTransferPaid">'; echo ($entryTypeSum['uv_itrans_paid']) ? round($entryTypeSum['uv_itrans_paid']) : 0 ; echo '</userdata>
        <userdata name = "UvInternalTransferReceived">'; echo ($entryTypeSum['uv_itrans_rcvd']) ? round($entryTypeSum['uv_itrans_rcvd']) : 0 ; echo '</userdata>
        <userdata name = "UvBusinessReceived">'; echo ($entryTypeSum['uv_busi_rcvd']) ? round($entryTypeSum['uv_busi_rcvd']) : 0 ; echo '</userdata>
        <userdata name = "UvBusinessReturned">'; echo ($entryTypeSum['uv_busi_retd']) ? round($entryTypeSum['uv_busi_retd']) : 0 ; echo '</userdata>
        <userdata name = "UvIncome">'; echo ($entryTypeSum['uv_income']) ? round($entryTypeSum['uv_income']) : 0 ; echo '</userdata>
        <userdata name = "UvExpense">'; echo ($entryTypeSum['uv_expense']) ? round($entryTypeSum['uv_expense']) : 0 ; echo '</userdata>';
        //09-12-2024 end 
            if($MRP_Obj) {
                $j = 1;
                foreach($MRP_Obj as $rw) {
                    if($rw->IT_Transfers==1 && $rw->MH_Type == 1 )$type="Internal Transfer Received";
                    else if($rw->IT_Transfers==1 && $rw->MH_Type == 2 )$type="Internal Transfer Paid";
                    else if($rw->IT_Business==1 && $rw->MH_Type==1)$type="Business Received";
                    else if($rw->IT_Business==1 && $rw->MH_Type==2)$type="Business Returned";
                    else if($rw->MH_Type==1)$type="Income";
                    else if($rw->MH_Type==2)$type="Expense";

                    $IT_Name      = $rw->IT_Name;
                    // 26-12-2024 Start
                    if(isset($filterAry) && !empty($filterAry)) {
                        $fromary    = [];
                        $toary      = [];
                        foreach($filterAry As $pname) {
                            if (empty($fromary) ||  (strlen($pname) > 2 && !in_array(strtolower($pname), $notuse))) {
                                $fromary[]  = $pname;
                                $toary[]    = '<span class="boldWords">'.$pname.'</span>';
                            }
                        }
                        $IT_Name = str_ireplace($fromary, $toary, $IT_Name);
                    }
                    //26-12-2024 End
                    echo '<row id="'.$rw->IT_Id.'">
                        <userdata name="IT_Name">'.$rw->IT_Name.'</userdata>
                        <cell title=" ">'.$j.'</cell>
                        <cell name="MH_Type" title=" ">'.$type.'</cell>   
                        <cell name="IT_Name" title=" ">'.htmlspecialchars($IT_Name).'</cell>
                        <cell name="SH_Name" title=" ">'.$rw->SH_Name.'</cell>
                        <cell name="MH_Name" title=" ">'.$rw->MH_Name.'</cell> 
                        <cell name="BSAmount" title=" ">'.round($rw->vBs_amount).'</cell>
                        <cell name="BSAmountun" title=" ">'.round($rw->uBs_amount).'</cell>
                        <cell name="View" title="Click to view more details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'"  onclick=""/>]]></cell>
                        <cell name="IT_Name" title=" ">'.$rw->IT_Name.'</cell>    
                    </row>';
                    $j++;
                }
            } else { echo '<row id="0"><cell colspan="8"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}   
    echo '</rows>';

?>