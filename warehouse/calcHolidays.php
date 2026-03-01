<?php

require_once($BASEPATH . "/preTallyClass/AttendanceClass.php");

$AttObj = new AttendanceClass();

while($date < $end)
{    
$timestamp = strtotime($date);
$day = date('D', $timestamp);


if($day=="Sun")
{
    echo "<font color='red'>".$date." Sunday</font><br/>";
    $AttObj->AT_Data = array(    
    'HD_Date' 		=> $date,
    'HD_Type'           => 0,
    'HD_Comments' 	=> 'Sunday',
    'HD_Status' 	=> 1,
    'HD_CDate' 		=> date('Y-m-d H:i:s')
);

$AttObj->AddHolidays();
}

$date = date("Y-m-d", strtotime("+1 day", strtotime($date)));  

}
?>