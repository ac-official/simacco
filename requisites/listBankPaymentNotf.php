<?php
//error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/NotificationClass.php");
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$filter = $preTally_user_lcid ;

$filterData = explode(",",$REQUEST['filter']);
//print_r($filterData);
$filter_mask = 'AND LC.OF_Id = "'.$preTally_user_ofid.'"  ';
if($filterData[0]) {
    $filter_mask .=' AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    $filter_mask .=' AND IT.IT_Name like "'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    $filter_mask .=' AND LC.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter_mask .=' AND CONCAT(US_FName," ",US_LName) like "'.$filterData[3].'%" ' ;
}if($filterData[4]){
    $filter_mask .=' AND IT.SH_Id = "'.$filterData[4].'" ' ;
}
if($filterData[5] != '') {
    if(!preg_match("/^[0-9.]+$/", $filterData[5]) )
        $filter_mask .= ' AND BS.BS_Amount = "'.$filterData[5].'"';
    else
        $filter_mask .= ' AND BS.BS_Amount = "'.+$filterData[5].'"';
}

$NotificationObj = new NotificationClass();
$BalSheetUpdateObj = new BalanceSheetClass();
$blockedDate = $BalSheetUpdateObj->bsEntryUpdateDate($preTally_user_ofid);

$NotificationObj->viewBankPaymentNoft($filter, $filter_mask,$_GET["posStart"],$_GET["count"]);
$Count = $NotificationObj->viewBankPaymentNoftCount($filter, $filter_mask);
$BOC_Obj = $NotificationObj->NotfArray;
//print_r($BOC_Obj);

$IE_Type = array('','Income','Expense');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($BOC_Obj) {
        $j=$_GET["posStart"]+1;
        foreach($BOC_Obj as $rw) {
            $amount = number_format($rw->BS_Amount,2);            
//            $income  = ($rw->MH_Type == '1') ? $amount : "---";
//            $expense = ($rw->MH_Type == '2') ? $amount : "---";
            echo '
                <row id="'.$rw->BS_Id.'">
                    <userdata name = "DS_Description">'.$rw->DS_Description.'</userdata>
                    <userdata name = "TR_Track">'.$rw->TR_Track.'</userdata>
                    <userdata name = "BS_Amount">'.$rw->BS_Amount.'</userdata>
                    <userdata name = "LC_Id">'.$preTally_user_lcid.'</userdata>
                    <userdata name = "US_Id">'.$preTally_user_id.'</userdata>
                    <userdata name = "PM_Id">'.$rw->PM_Id.'</userdata>
                    <userdata name = "BNK_Id">'.$rw->BNK_Id.'</userdata>
                    <userdata name = "BB_Id">'.$rw->BB_Id.'</userdata>
                    <userdata name = "BA_Id">'.$rw->BA_Id.'</userdata>
                    <userdata name = "LC_Name">'.$rw->LC_Name.'</userdata>
                    <cell>'.$j.'</cell>
                    <cell>'.$IE_Type[$rw->MH_Type].'</cell>';
                    echo '<cell>'.$rw->IT_Name;
                    if($rw->DS_Description != ''){echo '-'. $rw->DS_Description;}
                    if($rw->TR_Track != ''){echo '-Track : '.$rw->TR_Track;}
                    echo '</cell>
                    <cell>'.$rw->SH_Name.'</cell>
                    <cell>'.$amount.'</cell>
                    <cell><![CDATA[<a style="text-decoration:none;" class="targetBank_'.$rw->BS_Id.'" onclick="preTally.BankBalanceSheet.showDetailData(this,'.$rw->BS_Id.','.$rw->SH_Id.');">'.$rw->LC_Name.'</a>]]></cell>
                    <cell>'.date('d/m/Y', strtotime($rw->BS_Date)).'</cell>';
                    if(($blockedDate < $rw->BS_Date)) {
                        echo '<cell><![CDATA[<a href="#" style="text-decoration:none;" onclick="preTally.Notification.confirmBankPaymentNotf(this,'.$rw->BS_Id.','.$rw->IT_DualItem.');"><span class="confirmBtn" >Confirm Payment</span></a>]]></cell>';
                    } else {
                        echo '<cell></cell>';    
                    }
                echo '</row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>