<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$attObj = new AttendanceClass();
$attObj->getGeneratedSalYear($preTally_user_ofid);
$year=$attObj->salYearArray['Year'];
if(!$year){
    $year = date("Y");
}
$nextYear=$year+1;
$attObj->getGeneratedSalMonth($preTally_user_ofid,$year);
$month=$attObj->salMonthArray['Month'];
if(!$month){
   $month = date("m");
   $month = $month-1;
}
$monthArray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
echo '<complete>';
if($attObj){      
    for($i=$month+1;$i<=12;$i++) {
        echo '<option selected = "true" value="'.$monthArray[$i]." -".$year.'" >'.$monthArray[$i]." -".$year.' </option>';
    }
    for($i=1;$i<=12;$i++) {
        echo '<option value="'.$monthArray[$i]." -".$nextYear.'" >'.$monthArray[$i]." -".$nextYear.' </option>';
    }
}
echo '</complete>';
?>