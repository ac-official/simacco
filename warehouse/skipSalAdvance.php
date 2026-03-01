<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$salRepObj = new AttendanceClass();
$SRS_Id = $_REQUEST['SRS_Id'];
$salRepObj->updateRepaymentStatus($SRS_Id,2);

$salRepObj->viewDetails("SA_Id,SRS_RepaymentAmt,US_Id","repayment_schedule", " WHERE SRS_Id = ".$SRS_Id);
$monthArray=$salRepObj->DetailsArray;
foreach($monthArray as $rw){
    $SA_Id        = $rw->SA_Id;
    $SA_DeductAmt = $rw->SRS_RepaymentAmt;
    $US_Id        = $rw->US_Id;
}
echo $SA_Id;
if($SA_Id){
    $row=$salRepObj->viewDetailsrow("SRS_Month","repayment_schedule", " WHERE SA_Id = ".$SA_Id ." ORDER BY SRS_Id DESC");
    $yearMonth=explode("-", $row);
    $month = trim($yearMonth[0]);
    $year  = trim($yearMonth[1]);
    $nextmonth  = $salRepObj->getNextMonth($month,$year);
    $nextmonth=explode("-",$nextmonth);
    $year=$nextmonth[0];
    $nextmonth =$nextmonth[1];
    $salRepObj->SRS_data = array();
    $salRepObj->SRS_data = array(
        'SA_Id'                         => $SA_Id,
        'US_Id'                         => $US_Id,
        'SRS_RepaymentAmt'              => $SA_DeductAmt,
        'SRS_Month'                     => $nextmonth."-".$year,
        'SRS_Status' 	                => 1
    );
    $salRepObj->saveRepaymentShedule();
}

?>