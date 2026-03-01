<?php
//error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/MyBankBookClass.php");

$ACLReq = $ACL_Obj->ACL_BSheet;

if (!isset($_GET["posStart"]))
    $_GET["posStart"]   = 0;
if (!isset($_GET["count"]))
    $_GET["count"]      = 50;

if($ACLReq == 2){     // Self Office
    $filter = ' BS.LC_Id = '.$preTally_user_lcid.'  ';
} else if($ACLReq == 4) {  // Self Company
    $filter = ' BS_LC.OF_Id = '.$preTally_user_ofid.'  ';
} else {
    $filter = ' BS.US_Id = '.$preTally_user_id;
} 

$filterData = explode(",",$REQUEST['filter']);
$filter_mask = 'AND 1 ';
if($filterData[0]) {
    $filter_mask .=' AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    //$filter_mask .=' AND IT.IT_Name like "'.$filterData[1].'%"' ;
    // change the old filter and enter new below 16-05-2025
    $filter_mask .=' AND (IT.IT_Name like "%'.$filterData[1].'%" OR DS.DS_Description like "%'.$filterData[1].'%" OR TR.TR_Track like "%'.$filterData[1].'%" )' ;
}
if($filterData[2] != ''){
    $filter_mask .=' AND BS_LC.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter_mask .=' AND CONCAT(US.US_FName," ",US.US_LName) like "'.$filterData[3].'%" ' ;
}
if($filterData[4] != '' ) {
    if(!preg_match("/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/", (float)$filterData[4]) )
    $filter_mask .= ' AND BS.BS_Amount LIKE "'.$filterData[4].'%"';            
    else
    $filter_mask .= ' AND (cast(BS.BS_Amount as decimal(11,2))  = "'.$filterData[4].'" OR FLOOR(BS.BS_Amount) = "'.$filterData[4].'")';            
}
if($filterData[5] != ''){
    $filter_mask .=' AND BS.BS_Status = '.$filterData[5] ;
}
//21-10-2025 start
if (isset($filterData[6]) && $filterData[6] != '') {
    $bankary    = explode('_', $filterData[6]);
    if ($bankary[2] > 0) {
        $filter_mask .=' AND BS.BA_Id = '.$bankary[2];
    }
}
//21-10-2025 End


$BankBSObj  = new MyBankBookClass();
$BankBSObj->reportBankBookBSData($REQUEST['f'],$REQUEST['t'],$filter, $filter_mask,$_GET["posStart"],$_GET["count"]);
$Count      = $BankBSObj->reportBankBookDataCount($REQUEST['f'],$REQUEST['t'],$filter, $filter_mask);
$BOC_Obj    = $BankBSObj->BankBSArray;

$IE_Type    = array('','Income','Expense');
$BS_Status  = array('','Approved','Waiting for Approval','Rejected');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count ="'.$Count.'" pos ="'.$_GET["posStart"].'">
    <userdata name ="TL_Count">'.$Count.'</userdata>
    <userdata name="bank_reconciliation">'.$UserACLObj->bank_reconciliation.'</userdata>';
    if($BOC_Obj) {
        $j  =   $_GET["posStart"]+1;
        foreach($BOC_Obj as $rw) {
            $amount = number_format($rw->BS_Amount,2);  
            $checkedyes = ($rw->bank_con_status == 1) ? 'checked="true"':''; 
            echo '
                <row id = "'.$rw->BS_Id.'">
                    <cell>'.$j.'</cell>
                    <cell>'.$IE_Type[$rw->MH_Type].'</cell>';
                    echo '<cell>'.$rw->IT_Name;
                        if($rw->DS_Description != ''){echo '-'. $rw->DS_Description;}
                        if($rw->TR_Track != ''){echo '-Track : '.$rw->TR_Track;}
                    echo '</cell>';
                    echo '<cell>'.$rw->bank_acc_name.'</cell>';
                    echo '<cell>'.$amount.'</cell>
                    <cell><![CDATA[<a style="text-decoration:none;" class="BSTarget_'.$rw->BS_Id.'" onclick="preTally.MyBankBook.showDetailData(this,'.$rw->BS_Id.','.$rw->SH_Id.');">'.$rw->US_FName.' '.$rw->US_LName.'</a>]]></cell>
                    <cell>'.$rw->BS_LCName.'</cell>
                    <cell>'.date('d/m/Y', strtotime($rw->BS_CDate)).'</cell>
                    <cell>';
                    if($rw->BS_Status == 1 || $rw->BS_Status == 3) echo date('d/m/Y', strtotime($rw->BS_Date)); else echo "--";
                    echo '</cell>
                    <cell>'.$BS_Status[$rw->BS_Status].'</cell>';
                    if ($UserACLObj->bank_reconciliation == 1) {
                        echo '<cell><![CDATA[ <input type="checkbox" name="bctransbox" class="bctransbox" value="'.$rw->BS_Id.'" id="chkbctr'.$rw->BS_Id.'" '.$checkedyes.' /> ]]></cell>';
                    } else {
                        echo '<cell>-</cell>';
                    }                    
            echo '</row>';
            $j++;
        }
    }   else  { 
        echo '<row id="0"> <cell colspan = "11" ><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell></row>';
    }
echo '</rows>';