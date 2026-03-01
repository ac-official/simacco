<?php
//error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/BankBSClass.php");
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/MessageClass.php");
$ACLReq = $ACL_Obj->ACL_BSheet;

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$temp=  explode('-', $REQUEST['r']);
$keyTemp = explode('_', $temp['0']);

if(($keyTemp['0'])== 'BA'){
    $filter = 'BS.BA_Id = '.$keyTemp[1].' ';
}else if($ACLReq == 2){
    $filter = 'BS.BA_Id IN  ( SELECT BA_Id FROM bank_accounts WHERE LC_Id = '.$preTally_user_lcid.' ) ';
}else{
    $filter = 'US.OF_Id = '.$keyTemp[1].' ';
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
    $filter_mask .=' AND LC.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter_mask .=' AND CONCAT(US_FName," ",US_LName) like "'.$filterData[3].'%" ' ;
}if($filterData[4] != ''){
    $filter_mask .=' AND IT.SH_Id = "'.$filterData[4].'" ' ;
}
if($filterData[5] != '') {
    if(!preg_match("/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/", (float)$filterData[5]) )
    $filter_mask .= ' AND BS.BS_Amount LIKE "'.$filterData[5].'%"';            
    else
    $filter_mask .= ' AND (cast(BS.BS_Amount as decimal(11,2))  = "'.$filterData[5].'" OR FLOOR(BS.BS_Amount) = "'.$filterData[5].'")';            
}
$UserObj = new UserClass();
$BankBSObj = new BankBSClass();
$BalSheetUpdateObj = new BalanceSheetClass();
$MessageObj = new MessageClass();
$blockedDate = $BalSheetUpdateObj->bsEntryUpdateDate($preTally_user_ofid); 

$BankBSObj->reportBankBSData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter, $filter_mask,$_GET["posStart"],$_GET["count"]);
$Count = $BankBSObj->reportBankBSDataCount($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter, $filter_mask);
$BOC_Obj = $BankBSObj->BankBSArray;
//print_r($BOC_Obj);

$IE_Type = array('','Income','Expense');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($BOC_Obj) {
        $j=$_GET["posStart"]+1;
        foreach($BOC_Obj as $rw) {            
            $pettyCash = '';
            $UserObj->getReportingPerson($rw->US_Id);
            $US_Obj = $UserObj->UserArray;
            $amount = number_format($rw->BS_Amount,2);   
            if($rw->BS_PettyCashAmt != 0) $pettyCash = '('.number_format($rw->BS_PettyCashAmt,2).')' ;
//            $income  = ($rw->MH_Type == '1') ? $amount : "---";
//            $expense = ($rw->MH_Type == '2') ? $amount : "---";
            echo '
                <row id="'.$rw->BS_Id.'">
                    <userdata name = "US_Id">'.$rw->US_Id.','.$US_Obj['US_Report'].'</userdata>
                    <userdata name="Entry">'.$rw->IT_Name.'-'. $rw->DS_Description.'-'.$rw->TR_Track.' - Amount : '.$amount.' - Date : '.$rw->BS_Date.'</userdata>    
                    <userdata name = "US_Name">'.$rw->US_Name.','.$US_Obj['US_Name'].'</userdata>
                    <userdata name = "CHQ_Number">'.$rw->CHQ_Number.'</userdata>';
            if($rw->US_Id != $preTally_user_id  && ($blockedDate < $rw->BS_Date)) {
                $MsgCount=$MessageObj->getBSMessageCount($rw->BS_Id);
                if($MsgCount==0)$imgIcon='Messages-icon.png';
                else $imgIcon='NotifiAlert.png';
            echo '<cell title = "Click here to send message" ><![CDATA[<img src="images/icon/'.$imgIcon.'" onclick="preTally.BankBalanceSheet.sendCorrectionMsg(this,'.$rw->BS_Id.','.$rw->SH_Id.');"/> ]]></cell>';
                }
            else {
                echo '<cell></cell>';    
            }
                    echo '<cell>'.$j.'</cell>
                    <cell>'.$IE_Type[$rw->MH_Type].'</cell>';
                    echo '<cell>'.$rw->IT_Name;
                    if($rw->DS_Description != ''){echo '-'. $rw->DS_Description;}
                    if($rw->TR_Track != ''){echo '-Track : '.$rw->TR_Track;}
                    echo '</cell>
                    <cell>'.$rw->SH_Name.'</cell>
                    <cell>'.$amount.$pettyCash.'</cell>
                    <cell><![CDATA[<a style="text-decoration:none;" class="targetBank_'.$rw->BS_Id.'" onclick="preTally.BankBalanceSheet.showDetailData(this,'.$rw->BS_Id.','.$rw->SH_Id.');">'.$rw->LC_Name.'</a>]]></cell>';
                    if(($blockedDate < $rw->BS_Date)) {
                        echo '<cell><![CDATA[<a href="#" style="text-decoration:none;" onclick="preTally.BankBalanceSheet.saveBankBookDetails(this,'.$rw->BS_Id.','.$rw->MH_Type.');"><span class="confirmBtn" >Confirm Payment</span></a>]]></cell>';
                    } else {
                        echo '<cell></cell>';    
                    }
                echo '</row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>