<?php

if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/LeaveClass.php");
$DispFromDate = $REQUEST['from'];
$DispFromDate = date('Y-m-d', strtotime($DispFromDate . '-10 days'));
$todayDate = date('Y-m-d');
$year = date("Y");
$yearstart = mktime(00, 00, 00, 01, 01, $year);
$yearend = mktime(23, 59, 00, 12, 31, $year);
$yearStartDate = date("Y-m-d", $yearstart);
$yearEndDate = date("Y-m-d", $yearend);

function date_sort($a, $b) {
    return strtotime($a) - strtotime($b);
}

$AttendanceObj = new AttendanceClass();
$AttendanceObj->AttendanceListScheduler($preTally_user_id, $DispFromDate, $todayDate);
$Attendance_Obj = $AttendanceObj->AttendanceSchArray;
$UserObj = new UserClass();
$UserObj->selectCompanySettings($preTally_user_ofid);
$UserObj->selectPunchingTimes($preTally_user_id);
$userDet = $UserObj->viewSingleUser($preTally_user_id);
$CompObj = $UserObj->UserLogArray;
$CompSett = $UserObj->CompanySettingsArray;
$leastMins = $leastMinsOrg = $CompObj['US_WrkHours'] - $CompSett['CS_WrkHrGraceTime']; //monthly allowed grace time [bsd on compny setting]
$empStatus = strtolower($UserObj->getEmployeeStatusName($userDet->ES_Id));
$userDept = $userDet->DP_Id;
$graceTimeDueDate = $UserObj->selectLastGraceTimeUsedDate($preTally_user_id);
//Rule:: Already relaxtion allowed emp doesn't have any additional relaxation
$cmpyDfltWrkHr = (strtotime($CompSett['CS_OfficeEnds']) - strtotime($CompSett['CS_OfficeStart'])) / 60; // default 9 hr
$isRelaxAldStaff = ( $CompObj['US_WrkHours'] < $cmpyDfltWrkHr ) ? true : false;
$rollBackLeastMin= $leastMins + $CompSett['CS_WrkHrGraceTime'];

echo '<data>';
$attendanceDateArr = [];
if ($Attendance_Obj) {
    foreach ($Attendance_Obj as $att) {
        if($isRelaxAldStaff && (date('Y-m-d',strtotime($att->AT_Date)) >= "2023-02-24") ){
            //check company settings, needed any additional. common grace time
            if($CompSett['CS_GraceAlwdSpMbr']!=1)
                $leastMins = $rollBackLeastMin;
        }
        else if($isRelaxAldStaff && (date('Y-m-d',strtotime($att->AT_Date)) < "2023-02-24")){
                $leastMins=$leastMinsOrg;
        }
        array_push($attendanceDateArr, date("m/d/Y", strtotime($att->AT_Date)));
        if ($att->AT_SignOut == "00:00:00") {
            $OutTime = "--:--";
        } else {
            $OutTime = date("H:i", strtotime($att->AT_SignOut));
        }
        $date = $att->AT_Date;
        $mins = ($att->AT_Hours) % 60;
        $hr = ($att->AT_Hours - $mins) / 60;
        if ($hr == 0 && $mins == 0) {
            $time = "--";
        } else {
            $time = (int) $hr . ' Hr ' . (int) $mins . " Min";
        }
        $sign_in_time = "--:--";
        $sign_out_time = "--:--";
        $statLabel = "Unmarked";
        // those forgot to logout && not maintain minimum time for half day
        if (($date != date("Y-m-d")) && ($att->AT_Status == 0 ) || ( ($att->AT_Hours < $CompObj['US_WrkHours']/2 - $CompSett['CS_WrkHrGraceTime']) && ($att->AT_Status==1)) ) {
            $statLabel = '<img src="images/icon/cross_16.png"/> Invalid';
        } else { 
            $relaxation = $CompSett['CS_LoginGraceTime'] + 1;
            // earlier login relaxatioin was 30 min
            if( date('Y-m-d',strtotime($att->AT_Date)) <= "2023-02-20" ){
                $relaxation = 31;
            }
            // Rule:: these dep. will follow monthly 30 min grace time
            if($userDept==66 && date('Y')>=2022){
                // Rule:: IT Dept (66) (Sign in(!b4 7am) & sign out(!b4 4pm) time not considering, maintain hours(9))
                if($empStatus=='confirmed'){
                    //sign in
                    if ( (strtotime($att->AT_SignIn) >= strtotime("+" . $relaxation . " minutes", strtotime($CompObj['US_LoginTime'])))) {
                            $statLabel = '<img src="images/icon/warning_red_16.png"/> Late SignIn';
                    }
                    else if (($att->AT_Hours < $leastMins) && ($att->AT_SignOut != "00:00:00")) {
                            $statLabel = '<img src="images/icon/warning_red_16.png"/>  Early SignOut';
                    } else {
                            if($att->AT_Hours>=$CompObj['US_WrkHours'])
                                $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                            else if( (count($graceTimeDueDate)>0 && ( strtotime($date) < strtotime($graceTimeDueDate['date']) ) ) ) 
                                $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                            else if (count($graceTimeDueDate)<=0 && $att->AT_Hours >= $leastMins)
                                $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                            else if($att->AT_SignIn!=="00-00-00" && $att->AT_SignOut == "00:00:00")
                                $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                            else
                                $statLabel = '<img src="images/icon/warning_red_16.png"/>  Early SignOut';

                    }
                } //Rule::probation period employees sign in & sign out time will consider (they need to meet alloted working timeing)
                // * Rule::Actually grace time not available for probation period employees
                else{
                        if (strtotime($att->AT_SignIn) >= strtotime("+" . $relaxation . " minutes", strtotime($CompObj['US_LoginTime']))) {
                            $statLabel = '<img src="images/icon/warning_red_16.png"/> Late SignIn';
                        } else if (($att->AT_Hours < $leastMins) && ($att->AT_SignOut != "00:00:00")) {
                            $statLabel = '<img src="images/icon/warning_red_16.png"/>  Early SignOut';
                        } else {
                            if($att->AT_Hours>=$CompObj['US_WrkHours'])
                                $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                            else if( (count($graceTimeDueDate)>0 && ( strtotime($date) < strtotime($graceTimeDueDate['date']) ) ) ) 
                                $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                            else if (count($graceTimeDueDate)<=0 && $att->AT_Hours >= $leastMins)
                                $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                            else if($att->AT_SignIn!=="00-00-00" && $att->AT_SignOut == "00:00:00")
                                $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                            else
                                $statLabel = '<img src="images/icon/warning_red_16.png"/>  Early SignOut';
                        }
                }

            }// Rule::daily 30 min grace follow (for the time being. Actual req. is montly 30 min)
            else{
                     if (strtotime($att->AT_SignIn) >= strtotime("+" . $relaxation . " minutes", strtotime($CompObj['US_LoginTime']))) {
                        $statLabel = '<img src="images/icon/warning_red_16.png"/> Late SignIn';
                    } else if (($att->AT_Hours < $leastMins) && ($att->AT_SignOut != "00:00:00")) {
                        $statLabel = '<img src="images/icon/warning_red_16.png"/>  Early SignOut';
                    } else {
                        $statLabel = '<img src="images/icon/tick_16.png"/> Marked';
                    }
            }
        }
        echo'<event id="' . $att->AT_Id . attendance . '">
            <text><![CDATA[<font color=#000000><img src="images/icon/in.png"/> <b>' . date("H:i", strtotime($att->AT_SignIn)) . '<br/><img src="images/icon/out.png"/> ' . $OutTime .'&nbsp;<img src="images/icon/clock4.png"/> '.$time. '<br/>' . $statLabel . '</b>]]></text>
            <start_date>' . date("m/d/Y H:i", strtotime($att->AT_Date . $att->AT_SignIn)) . '</start_date>
            <end_date>' . date("m/d/Y H:i", strtotime($att->AT_Date . $att->AT_SignOut)) . '</end_date>
            <color>#FFFFFF</color>
        </event>';
    }
}
usort($attendanceDateArr, "date_sort");
$AttendanceObj->listWeekends($preTally_user_id);
$WeekendDays_Obj = $AttendanceObj->WeekendDetailsArray;
foreach ($WeekendDays_Obj as $we) {
    $weekendDays = $we->DH_Weekends;
}
$weekendDaysArr = explode(',', $weekendDays);
$weekendDayCount = sizeof($weekendDaysArr);
$startDate = new DateTime($DispFromDate);
$endDate = new DateTime($todayDate);
$weekendDates = array();
for ($i = 0; $i < sizeof($weekendDaysArr); $i++) {
    if ($weekendDaysArr[$i] == 7) {
        $weekendDaysArr[$i] = 0;
    }
}
foreach ($weekendDaysArr as $day) {
    while ($startDate <= $endDate) {
        if ($startDate->format('w') == $day) {
            $weekendDates[] = $startDate->format('m/d/Y');
        }
        $startDate->modify('+1 day');
    }
    $startDate = new DateTime($DispFromDate);
}
usort($weekendDates, "date_sort");
$leaveObj = new LeaveClass();
$leaveObj->LeaveScheduler($preTally_user_id, $DispFromDate, $todayDate);
$leave_Obj = $leaveObj->LeaveSchArray;
$leaveDateArr = array();
$leaveStatus = array("Pending", "First Approved", "HR Approved", "Rejected By Reporting Person", "Rejected By HR", "Cancelled", "Leave Not Taken");
if ($leave_Obj) {
	$icon='';
    foreach ($leave_Obj as $lev) {
        if ($lev->LRD_Session == "FL") {
            array_push($leaveDateArr, date("m/d/Y", strtotime($lev->LRD_Date)));
            $start = date("m/d/Y H:i", strtotime($lev->fromDate . '00:00:00'));
            $end = date("m/d/Y H:i", strtotime($lev->toDate . '23:59:59'));
            if (strtolower($lev->leaveType) == "leave" || strtolower($lev->leaveType) == "lop" || strtolower($lev->leaveType) == "leave without pay") {
                $icon = '<img src="images/icon/LOP.png"/> LOP';
            } elseif (strtolower($lev->leaveType) == "casual") {
                $icon = '<img src="images/icon/casual.png"/> Casual';
            } elseif (strtolower($lev->leaveType) == "sick") {
                $icon = '<img src="images/icon/red_plus.png"/> Sick';
            } elseif (strtolower($lev->leaveType) == "earned leave") {
                $icon = '<img src="images/icon/casual.png"/> Earned Leave';
            }
        } else if ($lev->LRD_Session == "FN") {
            $start = date("m/d/Y H:i", strtotime($lev->fromDate . '00:00:00'));
            $end = date("m/d/Y H:i", strtotime('-5 minutes' . $lev->toDate . $att->AT_SignIn));
            if (strtolower($lev->leaveType) == "leave" || strtolower($lev->leaveType) == "lop" || strtolower($lev->leaveType) == "leave without pay") {
                $icon = '<img src="images/icon/LOP.png"/> Half Day LOP';
            } elseif (strtolower($lev->leaveType) == "casual") {
                $icon = '<img src="images/icon/casual.png"/> Half Day Casual';
            } elseif (strtolower($lev->leaveType) == "sick") {
                $icon = '<img src="images/icon/red_plus.png"/>Half Day Sick';
            } elseif (strtolower($lev->leaveType) == "earned leave") {
                $icon = '<img src="images/icon/casual.png"/>Half Day Earned Leave';
            }
        } else if ($lev->LRD_Session == "AN") {
            $start = date("m/d/Y H:i", strtotime('+5 minutes' . $lev->toDate . $att->AT_SignOut));
            $end = date("m/d/Y H:i", strtotime($lev->toDate . '23:59:59'));
            if (strtolower($lev->leaveType) == "leave" || strtolower($lev->leaveType) == "lop" || strtolower($lev->leaveType) == "leave without pay") {
                $icon = '<img src="images/icon/LOP.png"/> Half Day LOP';
            } elseif (strtolower($lev->leaveType) == "casual") {
                $icon = '<img src="images/icon/casual.png"/> Half Day Casual';
            } elseif (strtolower($lev->leaveType) == "sick") {
                $icon = '<img src="images/icon/red_plus.png"/>Half Day Sick';
            } elseif (strtolower($lev->leaveType) == "earned leave") {
                $icon = '<img src="images/icon/casual.png"/>Half Day Earned Leave';
            }
        }
        echo'<event id="' . $lev->LR_Id . leave . '">
            <text><![CDATA[<font color=#000000><b>' . $icon . '<br/><img src="images/icon/info_18.png"/> ' . $leaveStatus[$lev->Status] . '</b>]]></text>
            <start_date>' . $start . '</start_date>
            <end_date>' . $end . '</end_date>
            <color>#e2efff;</color>
 </event>';
    }
}
usort($leaveDateArr, "date_sort");
$holidayDates = [];
$stateId = $AttendanceObj->getStateId($preTally_user_lcid);
$AttendanceObj->listHolidays($preTally_user_ofid, $DispFromDate, $yearEndDate, $stateId);
$holiday_Obj = $AttendanceObj->HolidayArray; //company holidays
if ($holiday_Obj) {
    foreach ($holiday_Obj as $hd) {
        array_push($holidayDates, date("m/d/Y", strtotime($hd->HD_Date)));
    }
}
usort($holidayDates, "date_sort");
$takenRHDates = [];
$takenRHBatch = [];
$AttendanceObj->listRHTaken($preTally_user_id, $DispFromDate);
$RHolidayTkn_Obj = $AttendanceObj->RH_TaknDays; //taken restricted holidays
if ($RHolidayTkn_Obj) {
    foreach ($RHolidayTkn_Obj as $rhtkn) {
        array_push($takenRHDates, date("m/d/Y", strtotime($rhtkn->Dat)));
        array_push($takenRHBatch, $rhtkn->Batch);
    }
}
usort($takenRHDates, "date_sort");
$allDates = [];
$dateArray = array();
$dateInterval = new DateInterval('P1D');
$format = 'Y-m-d';
$yesterday = date('Y-m-d', time() - 60 * 60 * 24);
$rangeToDate = new DateTime($yesterday);
$rangeToDate->add($dateInterval);
$period = new DatePeriod(new DateTime($DispFromDate), $dateInterval, $rangeToDate);

foreach ($period as $date) {
    $dateArray[] = $date->format($format);
}
foreach ($dateArray as $date) {
    array_push($allDates, date("m/d/Y", strtotime($date)));
}
$allDates = array_unique($allDates);
$newHolidayArr = [];
$newAbsentArr = [];
$newWeekendArr = [];
$newRHArray = [];
foreach ($allDates as $key => $currDate) {
    if (in_array($currDate, $holidayDates)) {
        $rv = $AttendanceObj->checkPrevDate($currDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates, $leaveDateArr);
        if ($rv == "x") {
            $rv = $AttendanceObj->checkNxtDate($currDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates);
            if ($rv == "x") {
                array_push($newAbsentArr, $currDate);
            } else {
                array_push($newHolidayArr, $currDate);
            }
        } elseif ($rv == "y") {
            array_push($newHolidayArr, $currDate);
        }
    } elseif (in_array($currDate, $weekendDates)) {
        $rv = $AttendanceObj->checkPrevDate($currDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates, $leaveDateArr);
        if ($rv == "x") {
            $rv = $AttendanceObj->checkNxtDate($currDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates);
            if ($rv == "x") {
                array_push($newAbsentArr, $currDate);
            } else {
                array_push($newWeekendArr, $currDate);
            }
        } elseif ($rv == "y") {
            array_push($newWeekendArr, $currDate);
        }
    } elseif (in_array($currDate, $takenRHDates)) {
        $rv = $AttendanceObj->checkPrevDate($currDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates, $leaveDateArr);

        if ($rv == "x") {
            $rv = $AttendanceObj->checkNxtDate($currDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates);
            if ($rv == "x") {
                array_push($newAbsentArr, $currDate);
            } else {
                array_push($newRHArray, $currDate);
            }
        } elseif ($rv == "y") {
            array_push($newRHArray, $currDate);
        }
    } elseif ((!in_array($currDate, $attendanceDateArr)) || (!in_array($currDate, $leaveDateArr))) {
        array_push($newAbsentArr, $currDate);
    }
}
foreach ($holiday_Obj as $hd) {
    if (in_array(date("m/d/Y", strtotime($hd->HD_Date)), $newHolidayArr) && !in_array(date("m/d/Y", strtotime($hd->HD_Date)), $attendanceDateArr)) {
        echo'<event id="' . $hd->HD_Id . holiday . '">
            <text><![CDATA[<font color=#FF0000><b>' . $hd->HD_Comments . '</b>]]></text>
            <start_date>' . date("m/d/Y H:i", strtotime($hd->HD_Date . '00:00:00')) . '</start_date>
            <end_date>' . date("m/d/Y H:i", strtotime($hd->HD_Date . '23:59:00')) . '</end_date>
            <color>#EDB6B6;</color>
 </event>';
    }
}
foreach ($RHolidayTkn_Obj as $rhtkn) {
    if (in_array(date("m/d/Y", strtotime($rhtkn->Dat)), $newRHArray) && !in_array(date("m/d/Y", strtotime($rhtkn->Dat)), $attendanceDateArr)) {
        echo'<event id="' . $rhtkn->ID . RHolidayTaken . '">
            <text><![CDATA[<font color=#136800><b>' . $rhtkn->Comments . '</b>]]></text>
            <start_date>' . date("m/d/Y H:i", strtotime($rhtkn->Dat . '00:00:00')) . '</start_date>
            <end_date>' . date("m/d/Y H:i", strtotime($rhtkn->Dat . '23:59:00')) . '</end_date>
            <color>#EDB6B6;</color>
 </event>';
    }
}

$newAbsentArr = array_diff($newAbsentArr, $leaveDateArr);
$newAbsentArr = array_diff($newAbsentArr, $attendanceDateArr);
usort($newAbsentArr, "date_sort");
foreach ($newAbsentArr as $value) {
    if ($value != date("m/d/Y", strtotime($todayDate))) {
        echo'<event id="' . $value . notApplied . '">
            <text><![CDATA[<font color=#000000><b>  <img src="images/icon/LOP.png"/> LOP  <br/><img src="images/icon/info_18.png"/> Leave Not Applied</b>]]></text>
            <start_date>' . date("m/d/Y H:i", strtotime($value . '00:00:00')) . '</start_date>
            <end_date>' . date("m/d/Y H:i", strtotime($value . '23:59:59')) . '</end_date>
            <color>#e2efff;</color>
 </event>';
    }
}
echo '</data>';
?>