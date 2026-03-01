<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH.'includes/functions.php');
require_once($BASEPATH."preTallyClass/ExportExcelClass.php");
$XLExportObj = new ExportExcelClass();

$filter = ' AND BS.BS_Status IN (1,2) AND IT.OF_Id = '.$preTally_user_ofid;

if($REQUEST['report_type'] == "tallyEx_RptBank")   // bank based
    $filter .= ' AND BS.PM_Id = 2 ';
else if($REQUEST['report_type'] == "tallyEx_RptCash")   // cash based 
    $filter .= ' AND BS.PM_Id = 1 ';

$IncomeExpenceExcelResult = $XLExportObj->incomeExportRpt($REQUEST['stDate'],$REQUEST['enDate'],$filter);
$PaymentDate = $XLExportObj->paymentDateArray;
$headerArray = array();
$headerArray['PaymentDate']     = "Payment Date";
$headerArray['TYPE']            = "Payment Type";
$headerArray['Head']            = "Account Heads";
$headerArray['PaymentMode']     = "Payment Mode";
$headerArray['ChequeNo']        = "Cheque Number";
$headerArray['ChequeDate']      = "Cheque Date";
$headerArray['Amount']          = "Amount";
$headerArray['BranchName']      = "Branch Name";
$headerArray['Item']            = "Narration";
foreach($PaymentDate as $PayDate){
    $arrayStr                = array();
    $arrayStr['PaymentDate'] = date('d-m-Y',  strtotime($PayDate->PaymentDate));
    $arrayStr['TYPE']        = $PayDate->TYPE;
    $arrayStr['Head']        = $PayDate->Head;
    if($PayDate->PaymentMode=='Cheque'){
        $arrayStr['PaymentMode'] = 'Bank-'.' '.$PayDate->BankName;
    }
    else{
        $arrayStr['PaymentMode'] = $PayDate->PaymentMode;
    }
    if(($PayDate->ChequeNo   == 'NULL')||($PayDate->ChequeNo == 0)){
       $arrayStr['ChequeNo']   = ' ';  
       $arrayStr['ChequeDate'] = ' ';
    }
    else{
       $arrayStr['ChequeNo']   = $PayDate->ChequeNo;
       $arrayStr['ChequeDate'] = ($PayDate->ChequeDate) ? date('d-m-Y',  strtotime($PayDate->ChequeDate)) : ''; 
    }
    $arrayStr['Amount']      = $PayDate->Amount;
    $arrayStr['BranchName']  = $PayDate->BranchName;
    $trackName = $PayDate->TR_Track != '' ? ','.$PayDate->TR_Track : '' ;
    $arrayStr['Item']        = $PayDate->Item.','.$PayDate->Description.$trackName;
    $data[]                  = array_map('trim',$arrayStr);
}
echo $XLExportObj->createExcel($data, $headerArray);  
?>