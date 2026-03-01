<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH.'includes/functions.php');
require_once($BASEPATH."preTallyClass/ExportExcelClass.php");
$XLExportObj = new ExportExcelClass();
if($REQUEST['report_type'] == "stk_Rpt") {
        $filter = 'OF_Id = '.$preTally_user_ofid.' ';
}
if($REQUEST['report_type'] == "stk_Rpt") {
    $ExcelResult = $XLExportObj->exportstkRpt($REQUEST['stDate'],$REQUEST['enDate'],$preTally_user_ofid);
    $TRID = $XLExportObj->trArray;
    $ITName = $XLExportObj->itArray;
    $TrackName = $XLExportObj->trNameArray;
    $ITAmount = $XLExportObj->amntArray;
    $JobAmount = $XLExportObj->jobAmtArray;
}
$data = array();
$headerArray = array("Id","Branch","Job Amount","Job Date");
$tot_Job = 0;
$tot_Amt = array();

foreach ($ITName as $rw_Head){
    $headerArray[] =  $rw_Head;
}

$tempInit = array();
$arrayStr = array();

foreach($TRID as $rw_TR) {
    $arrayStr[$rw_TR]['Id'] = $TrackName[$rw_TR];
    $arrayStr[$rw_TR]['LC_Name'] = $JobAmount[$rw_TR]['LC_Name'];
    $arrayStr[$rw_TR]['JobAmount'] = $JobAmount[$rw_TR]['JobAmount'];
    $arrayStr[$rw_TR]['TR_CDate'] =  date('d-m-Y',  strtotime($JobAmount[$rw_TR]['TR_CDate']));
    $tot_Job += $JobAmount[$rw_TR]['JobAmount']; 
    
    foreach ($ITName as $rw_IT) {
        $arrayStr[$rw_TR][$rw_IT] = $ITAmount[$rw_TR][$rw_IT];
        $tot_Amt[$rw_IT] += $ITAmount[$rw_TR][$rw_IT]; 
//       $tempInit[$rw_IT] = 0; 
    }
}

$totStr=array("TOTAL","",$tot_Job,"");

foreach ($ITName as $rw){
    $totStr[] =  $tot_Amt[$rw];
}

array_push($arrayStr,$totStr);

echo $XLExportObj->createExcel($arrayStr, $headerArray);
