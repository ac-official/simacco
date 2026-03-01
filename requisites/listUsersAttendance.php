<?php
/*
* Modified By ArunDev
*/
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
require_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
require_once($BASEPATH . "preTallyClass/LeaveClass.php"); //01-07-2025
require_once($BASEPATH . "preTallyClass/LateEntryClass.php"); //01-07-2025
$BreakObj   = new BreakTimeClass(); //26-06-2025
$UserObj    = new UserClass();
$userId     = $REQUEST['US_Id'];
//$UserObj->selectCompanySettings($preTally_user_ofid);
//$UserObj->selectPunchingTimes($userId);
$userOffice = $UserObj->getOfficeIdByUserId($userId); // added by Bilin
//$CompObj    = $userOffice; //$UserObj->UserLogArray;
$UserObj->selectCompanySettings($userOffice['OF_Id']);
$CompSett   = $UserObj->CompanySettingsArray;
//$leastMins=$CompObj['US_WrkHours']-$CompSett['CS_WrkHrGraceTime'];
$stdate     = $REQUEST['ST_Date'];
$lastdate   = $REQUEST['LST_Date'];
$cudate     = date('Y-m-d');
$nwruledate = "2024-08-21";
//$month = date('M');
if ($stdate != "" && $lastdate != "") {    
    //$month = date("M", strtotime($stdate));
} else {
    $cellDate=$REQUEST['celldate'];
    $yminfo = explode("-", $cellDate);
    $minfo = $yminfo[1];
    $monthInfo = ltrim($minfo, '0');
    $yearInfo = $yminfo[0];
    $startDate = 1;
    $endDate = cal_days_in_month(CAL_GREGORIAN, $monthInfo, $yearInfo);
    $stdate = $cellDate. '-'. '1';
    $lastdate = $cellDate. '-'. $endDate;
}
$lastdate = ($lastdate >= $cudate) ? date('Y-m-d', strtotime($cudate . ' -1 day')) :$lastdate;
// fetch the break time of user //26-06-2025
$listBreak = $BreakObj->getDailyUserTime(['user_id'=>$userId, 'from_date'=>$stdate, 'to_date'=>$lastdate, 'type' =>1]);
$settingbt  = $BreakObj->getBreakSettings($userOffice['OF_Id']); //total allowed break time

$minTimePresent = (20261001 <= date("Ymd", strtotime($stdate))) ? 2 : 1.5; // this old By Bilin on 2025/06/04
$newTimeStart   = (20261001 <= date("Ymd", strtotime($stdate))) ? 1:0;

// get the monthly user late entry permissions 01-07-2025
$lateEntryObj   = new LateEntryClass();
$listlateEntry  = $lateEntryObj->getLateEntryUser(['user_id'=>$userId, 'from_date'=>$stdate, 'to_date'=>$lastdate, 'status'=>'1']);
$lateEntryAllow = $lateEntryObj->getallowedGraseTime($stdate, $lastdate);
// get the approved leaves from database  01-07-2025
$leaveObj       = new LeaveClass(); 
$approveLeaves  = $leaveObj->getLeaveReqUser(['user_id'=>$userId, 'from_date'=>$stdate, 'to_date'=>$lastdate, 'status'=>'2']);

$filter = " WHERE AT_Date BETWEEN '" . $stdate . "' AND '" . $lastdate . "' AND US_Id=" . $userId ." ORDER BY AT_Date DESC";
/*function getOffDay($date, $offDays) {
    $leave          = 0;
    $color          = "black";
    $day            = strtolower(date("l",strtotime($date)));
    if ($day == 'sunday') {
        $statLabel  = "SUNDAY";
    } else if (in_array($date, $offDays['offday'])) {
        $statLabel  = "WEEKEND OFF";
    } else if (in_array($date, $offDays['holyday'])) {
        $statLabel  = "HOLIDAY";
    } else {
        $statLabel  = "Unmarked / Leave";
        $leave      = 1;
        $color      = "red";
    }
    return [ 'label'=>$statLabel, 'leave'=>$leave, 'color'=>$color];
}*/
//-- updated by arun for getting leave info also in user attendance---//
// Changed @ 23-08-2024
$date       = explode("-", $stdate);
$userOffice = $UserObj->getOfficeIdByUserId($userId); //retreive holidays
$AttObj     = new AttendanceClass();
$AttObj->getWeekendOffs($date[1],$date[0],$userOffice['OF_Id'],$userOffice['DP_Id']);
$WeekendOff         = $AttObj->WeekOffs; 
$offDays['offday']  = (!empty($WeekendOff) && isset($WeekendOff[$userOffice['DP_Id']])) ? $WeekendOff[$userOffice['DP_Id']]: [];
//print_r($userOffice);
//echo $preTally_user_ofid."---".$userOffice['OF_Id']."----";
$AttObj->getHolidays(implode('-',$date),$userOffice['OF_Id'],$userOffice['DP_Id']);
$offDays['holyday'] = (!empty($AttObj->Holidays) && isset($AttObj->Holidays[0])) ? $AttObj->Holidays[0]['dates']: [];
if (!empty($AttObj->Holidays) && isset($AttObj->Holidays[$userOffice['ST_Id']])) {
    foreach($AttObj->Holidays[$userOffice['ST_Id']]['dates'] As $datt) {
        $offDays['holyday'][] = $datt;
    }
}
// list data 
//$att_res = array();
// if ($month == date("M")) {
//     $curDate = date("Y-m-d");
// }
//$att_res = $AttObj->calcAttendance($month, $curDate, $userId);
$AttObj->viewAttendance($filter);
$Att_Obj = $AttObj->AttendanceArray;

// adjust the last month and next mon attendance (sandwich leave calculation for month end and start based or other months)
$leavestart     = 0;
$leaveend       = 0;
$chkfirstdate   = $date[0].'-'.$date[1].'-01';
$AttObj->getAdjMnthAttendance($userId,$date[1],$date[0]); 
if ($AttObj->AdjcntAttendance["last"]!=null) {

    $prev_month_ts  = strtotime($chkfirstdate . ' -1 month');
    $ld_adj         = $AttObj->AdjcntAttendance["last"];
    $date_adj       = date('Y-m-t',strtotime($ld_adj));
    if ($ld_adj != $date_adj) {
        $ld_adj     = date('Y-m-d', strtotime($ld_adj . ' +1 day'));
        while ($ld_adj <= $date_adj) {
            if (!(strtolower(date("l",strtotime($ld_adj))) == "sunday" || in_array($ld_adj, $offDays['offday']) || in_array($ld_adj, $offDays['holyday']) )) {
                $leavestart     = 1;
            }
            $ld_adj     = date('Y-m-d', strtotime($ld_adj . ' +1 day'));
        }
    }
} else {
   $leavestart     = 1; 
}  
if (!(date('Y') == $date[0] && date('m') == $date[1])) {
    if ($AttObj->AdjcntAttendance["next"]!=null) {

        $next_month_ts  = strtotime($chkfirstdate . ' +1 month');
        $ld_adj         = $AttObj->AdjcntAttendance["next"];
        $date_adj       = date('Y-m',strtotime($ld_adj)).'-01';
        if ($ld_adj != $date_adj) {
            $ld_adj     = date('Y-m-d', strtotime($ld_adj . ' -1 day'));
            while ($ld_adj >= $date_adj) {
                if (!(strtolower(date("l",strtotime($ld_adj))) == "sunday" || in_array($ld_adj, $offDays['offday']) || in_array($ld_adj, $offDays['holyday']) )) {
                    $leaveend     = 1;
                }
                $ld_adj     = date('Y-m-d', strtotime($ld_adj . ' -1 day'));
            }
        }
    } else {
        $leaveend     = 1;
    }
}

$latesign = 0;
$earlyout = 0;
$extratime= 0;
$lateDays = 0;
$earlyDays= 0;
// create month based date list By Bilin 23-08-2024
$dailyDates = [];
$stdate     = date('Y-m-d', strtotime($stdate));
while ($stdate <= $lastdate && $stdate <= date('Y-m-d')) {
    $day            = strtolower(date("l",strtotime($stdate)));
    $type           = 'H';
    $leave          = 0;
    $color          = "black";
    if ($day == 'sunday') {
        $statLabel  = "SUNDAY";
    } else if (in_array($stdate, $offDays['offday'])) {
        $statLabel  = "WEEKEND OFF";
    } else if (in_array($stdate, $offDays['holyday'])) {
        $statLabel  = "HOLIDAY";
    } else {
        $statLabel  = "Unmarked / Leave";
        $type       = 'L';
        $leave      = 1;
        $color      = "red";
    }
    // date checking no grace time @ 21-aug-2024
    $newdatechk = 0;
    if ($stdate >= $nwruledate) {
        $newdatechk = 1;
    }
    $allotInTime    = strtotime($userOffice['US_LoginTime']);
    $allotoutTime   = strtotime($userOffice['US_LogoutTime']);
    $workMins       = $userOffice['US_WrkHours'];
    $leastMins      = ($newdatechk == 1) ? $workMins:$workMins-$CompSett['CS_WrkHrGraceTime'];

    $dailyDates[$stdate] = ['rid'=>$userId.'_'.date('d', strtotime($stdate)), 'label'=>$statLabel, 'type'=>$type, 'leave'=>$leave, 'new'=>$newdatechk, 'login'=>$allotInTime, 'logout'=>$allotoutTime, 'wtime'=>$workMins, 'htime'=>($workMins/2), 'hhtime'=>($workMins/4), 'leastmin'=>$leastMins, 'color'=>$color, 'mincolor'=>$color, 'in'=>"--:--", 'out'=>"--:--", 'time'=>"--",'btime'=>0, 'stime'=>'', 'hlabel'=>'', 'latetime'=>0, 'earlytime'=>0];
    $stdate         = date('Y-m-d', strtotime($stdate . ' +1 day')); 
}
if ($Att_Obj) {
    foreach ($Att_Obj as $rw) {

        $rw->AT_Date    = trim($rw->AT_Date);
        $newdatechk     = 0;        
        if(!empty($rw->AT_AllotTime) && $rw->AT_Date >= $nwruledate) {   
            $newdatechk     = 1;                 
            $assignedTime   = json_decode($rw->AT_AllotTime);
            $dailyDates[$rw->AT_Date]['login']      = strtotime($assignedTime->in);
            $dailyDates[$rw->AT_Date]['logout']     = strtotime($assignedTime->out);
            $dailyDates[$rw->AT_Date]['wtime']      = $assignedTime->whour; 
            $dailyDates[$rw->AT_Date]['htime']      = ($assignedTime->whour/2); //02-07-2025 
            $dailyDates[$rw->AT_Date]['hhtime']     = ($assignedTime->whour/4); //02-07-2025 
            $dailyDates[$rw->AT_Date]['leastmin']   = $assignedTime->whour;
        }
        if ($rw->AT_SignIn != "" && $rw->AT_SignIn != null) {
            $dailyDates[$rw->AT_Date]['in']    = date('g:i a', strtotime($rw->AT_SignIn));
        }
        if ($rw->AT_SignOut != "" && $rw->AT_SignOut != null && $rw->AT_SignOut != "00:00:00" && $rw->AT_Status == 1) {
            $dailyDates[$rw->AT_Date]['out']   = date('g:i a', strtotime($rw->AT_SignOut));
            $mins   = ($rw->AT_Hours) % 60;
            $hr     = ($rw->AT_Hours - $mins) / 60; 
            $dailyDates[$rw->AT_Date]['time']   = (int) $hr . ':' . ($mins < 10 ? '0'.(int)$mins:(int)$mins);
        }
        $dailyDates[$rw->AT_Date]['rid']    = $rw->AT_Id;
        $datebase       = $dailyDates[$rw->AT_Date];
        $halfdyMin      = (int)($datebase['leastmin']/2);            
            
        if ( $datebase['new'] == 1 ) { // no relaxatioin time
            $relaxation = 1;
        } else if ( date('Y-m-d',strtotime($rw->AT_Date)) <= "2023-02-20" ) {
            // earlier login relaxatioin was 30 min
            $relaxation = 31;
        } else {
            $relaxation = $CompSett['CS_LoginGraceTime']+1;
        }
        //01-07-2025
        $new_min_half   = ($halfdyMin/$minTimePresent);
        $new_min_full   = ($halfdyMin/$minTimePresent);
        //$new_min_half   = ($newTimeStart == 1) ? ($halfdyMin-($minTimePresent/2)) : ($halfdyMin/$minTimePresent); // 18-06-2025
        //$new_min_full   = ($newTimeStart == 1) ? ($halfdyMin-$minTimePresent) : ($halfdyMin/$minTimePresent); // 18-06-2025

        if ($newTimeStart == 1) { // 01-07-2025
            
            if(isset($approveLeaves[$rw->AT_Date]) && $approveLeaves[$rw->AT_Date] == 'FL' ) {
                $dailyDates[$rw->AT_Date]['label']   = "Leave"; 
                $dailyDates[$rw->AT_Date]['color']   = "red"; 
                $dailyDates[$rw->AT_Date]['leave']   = 1; 
                $dailyDates[$rw->AT_Date]['type']    = 'L';
            } else if ( ($rw->AT_Date != date("Y-m-d") && $rw->AT_Status == 0) || ($rw->AT_Hours <= $datebase['hhtime'] && $rw->AT_Status == 1) ) {

                if($dailyDates[$rw->AT_Date]['type'] != 'H') { // invalid on holiday
                    $dailyDates[$rw->AT_Date]['leave']   = 1; 
                    $dailyDates[$rw->AT_Date]['label']   = "Invalid"; 
                    $dailyDates[$rw->AT_Date]['color']   = "red"; 
                    $dailyDates[$rw->AT_Date]['type']    = "L"; 
                }
            } else {
                $dailyDates[$rw->AT_Date]['latetime']   = $rw->AT_SignInDelay; 
                $dailyDates[$rw->AT_Date]['earlytime']  = $rw->AT_SignOutEarly;  
                $dailyDates[$rw->AT_Date]['label']   = "Marked"; 
                $dailyDates[$rw->AT_Date]['color']   = "green"; 
                $dailyDates[$rw->AT_Date]['leave']   = 0; 
                $dailyDates[$rw->AT_Date]['type']    = 'P';

                $strtimeIn      = strtotime($rw->AT_SignIn);
                $strtimeOut     = strtotime($rw->AT_SignOut);
                $allowTime      = $lateEntryAllow[$rw->AT_Date]; // company allowed for morning time

                $apprMorgTime   = (isset($listlateEntry[$rw->AT_Date]) && $listlateEntry[$rw->AT_Date][1] > $allowTime) ? $listlateEntry[$rw->AT_Date][1]: $allowTime; // morning approved
                $apprEvngTime   = (isset($listlateEntry[$rw->AT_Date])) ? $listlateEntry[$rw->AT_Date][2]: 0; // evening time approved
                $leaveTodayTp   = (isset($approveLeaves[$rw->AT_Date])) ? $approveLeaves[$rw->AT_Date]: ''; // approved leave in this day

                // clicked on mark out not Today
                if ($rw->AT_SignOut != "00:00:00" && $rw->AT_Date != date("Y-m-d")) {
                    $HITime             = $rw->AT_SignInDelay + $rw->AT_SignOutEarly;
                    $afternoonsignhours = $apprMorgTime+$datebase['htime'];
                    $afterNoonSignin    = strtotime("+" . $afternoonsignhours . " minutes", $datebase['login']);
                    $forenoonsignhours  = $datebase['htime'] + $apprEvngTime;
                    $foreNoonSignout    = strtotime("-" . $forenoonsignhours . " minutes", $datebase['logout']);
                    $actInTime          = strtotime("+" . $apprMorgTime . " minutes", $datebase['login']);
                    $actOutTime          = strtotime("-" . $apprEvngTime . " minutes", $datebase['logout']);
                    //$actInTime           = $allowTime;
                    //echo "----".$actOutTime."---".$strtimeOut;
                    //die();
                    //9 hours working time completed proper mark in and out  
                    if ($datebase['wtime'] <= $rw->AT_Hours) {                        

                        // check the sign in and sign out at allowed time
                        // sign in with the allowed time or take late sign in approval
                        // sign out time proper with in the approved early signin
                        // work extra time in same day (15 min delay person work 15 extra)
                        if ( ($strtimeIn <= $datebase['login'] && $strtimeOut >= $datebase['logout']) OR ($strtimeIn <= $actInTime && $strtimeOut >= $datebase['logout']) OR ($strtimeOut >= $actOutTime && $strtimeIn >= strtotime("-" . $apprEvngTime . " minutes",$datebase['login']) && $apprEvngTime > 0) OR ($rw->AT_Hours >= ($HITime + $datebase['wtime']))  ) {
                            // marked case
                        } else {
                            // late sig in and early sign out time with out approval consider Hours INcomplete
                            $dailyDates[$rw->AT_Date]['label']  = "Hours Incomplete";
                            $dailyDates[$rw->AT_Date]['type']   = "IH";
                            $dailyDates[$rw->AT_Date]['color']  = "red"; 
                            $dailyDates[$rw->AT_Date]['btime']  = ($rw->AT_SignOutEarly > 0) ? $rw->AT_SignOutEarly: $rw->AT_SignInDelay;
                        }
                    }// marked but not complete 9 hours (above halfday) 
                    else if ($rw->AT_Hours >= ($datebase['htime'])) {

                        $dailyDates[$rw->AT_Date]['color']  = "red";                       
                        
                        // morning time proper and apply leave on afternoon
                        // Morning time with appove late entry and apply afternoon leave
                        // Evening halfday sigin or appvove laterentry & Leave on morning
                        // work time above half day (halfday+hour incomplete time) + apply half day leave too
                        if ( ($strtimeIn <= $actInTime && $leaveTodayTp == 'AN') || ($strtimeIn <= $afterNoonSignin && $leaveTodayTp == 'FN') || ( $HITime < $datebase['htime'] &&  $att->AT_Hours >= ($datebase['htime']+$HITime) && ($leaveTodayTp == 'FN' || $leaveTodayTp == 'AN') )     ) {  

                            $dailyDates[$rw->AT_Date]['leave']   = (0.5);
                            $dailyDates[$rw->AT_Date]['btime']   = 0;
                            $dailyDates[$rw->AT_Date]['label']   = "Half Day";
                            $dailyDates[$rw->AT_Date]['type']    = "P2";  

                            if ($strtimeIn > $actInTime && $leaveTodayTp == 'AN') {
                                $dailyDates[$rw->AT_Date]['btime'] = round(abs($strtimeIn - $actInTime) / 60,2);
                            } else if ($strtimeIn > $afterNoonSignin && $leaveTodayTp == 'FN') {
                                $dailyDates[$rw->AT_Date]['btime'] = round(abs($strtimeIn - $afterNoonSignin) / 60,2);
                            }
                            if ($strtimeOut < $actOutTime && $leaveTodayTp == 'FN') {
                                $dailyDates[$rw->AT_Date]['btime'] += round(abs($actOutTime - $strtimeOut) / 60,2);
                            } else if ($strtimeOut < $foreNoonSignout && $leaveTodayTp == 'AN') {
                                $dailyDates[$rw->AT_Date]['btime'] += round(abs($foreNoonSignout - $strtimeOut) / 60,2);
                            }
                            // chcek the user take extra time 
                            if (($dailyDates[$rw->AT_Date]['btime']+$datebase['htime']) <= $rw->AT_Hours) {
                                $dailyDates[$rw->AT_Date]['btime'] = 0;
                            }
                        }// above 3/4 of work time completed and leave not approved
                        // early and late minutes consider as hourly incomplete
                        else if ( ($datebase['hhtime']+$datebase['htime']) <= $rw->AT_Hours     )  {
                            // if the user apply half day leave
                            if ($leaveTodayTp == 'FN' || $leaveTodayTp == 'AN') { //09-07-25
                                $dailyDates[$rw->AT_Date]['leave']  = (0.5);
                                $dailyDates[$rw->AT_Date]['label']  = "Half Day";
                                $dailyDates[$rw->AT_Date]['type']   = "P2";
                                $dailyDates[$rw->AT_Date]['btime']  = 0;
                            } else {
                                // early or late mark permission taken 
                                if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {

                                    $dailyDates[$rw->AT_Date]['btime']  = $datebase['wtime']-$rw->AT_Hours;
                                } else {
                                    $dailyDates[$rw->AT_Date]['btime']  = $HITime;
                                }                            
                                $dailyDates[$rw->AT_Date]['label']  = "Hours Incomplete";
                                $dailyDates[$rw->AT_Date]['type']   = "IH";
                            }
                        } // work above halfday but not approve half day leave
                        // work time between 1/2 to below 3/4 of time
                        else {
                            // if the user apply half day leave 09-07-25
                            if ($leaveTodayTp == 'FN' || $leaveTodayTp == 'AN') { 
                                $dailyDates[$rw->AT_Date]['leave']  = (0.5);
                                $dailyDates[$rw->AT_Date]['label']  = "Half Day";
                                $dailyDates[$rw->AT_Date]['type']   = "P2";
                                $dailyDates[$rw->AT_Date]['btime']  = 0; 
                                //check the in time and late signin hour incomplete                                 
                                if ($strtimeIn > $actInTime && $leaveTodayTp == 'AN') {
                                    $dailyDates[$rw->AT_Date]['btime'] = round(abs($strtimeIn - $actInTime) / 60,2);
                                } else if ($strtimeIn > $afterNoonSignin && $leaveTodayTp == 'FN') {
                                    $dailyDates[$rw->AT_Date]['btime'] = round(abs($strtimeIn - $afterNoonSignin) / 60,2);
                                }
                                if ($strtimeOut < $actOutTime && $leaveTodayTp == 'FN') {
                                    $dailyDates[$rw->AT_Date]['btime'] += round(abs($actOutTime - $strtimeOut) / 60,2);
                                } else if ($strtimeOut < $foreNoonSignout && $leaveTodayTp == 'AN') {
                                    $dailyDates[$rw->AT_Date]['btime'] += round(abs($foreNoonSignout - $strtimeOut) / 60,2);
                                }
                                // chcek the user take extra time 
                                if (($dailyDates[$rw->AT_Date]['btime']+$datebase['htime']) <= $rw->AT_Hours) {
                                    $dailyDates[$rw->AT_Date]['btime'] = 0;
                                }
                                // leave not approved                                
                            } else {
                                $dailyDates[$rw->AT_Date]['label']  = "Hours Incomplete";
                                $dailyDates[$rw->AT_Date]['type']   = "IH";
                                // early or late mark permission taken 
                                if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {

                                    $dailyDates[$rw->AT_Date]['btime']  = $datebase['wtime']-$rw->AT_Hours;
                                } else {
                                    $dailyDates[$rw->AT_Date]['btime']  = $HITime;
                                }  
                            }
                        }
                    }//work above the 1/4 of work time or below half day
                    else if ($rw->AT_Hours >= $datebase['hhtime'] && $leaveTodayTp != 'FL') {
                        $dailyDates[$rw->AT_Date]['color']      = "red";
                        // apply and got approve half day leave then consider 
                        if ($leaveTodayTp == 'FN' || $leaveTodayTp == 'AN') {
                            $dailyDates[$rw->AT_Date]['leave']  = (0.5);
                            $dailyDates[$rw->AT_Date]['label']  = "Half Day";
                            $dailyDates[$rw->AT_Date]['type']   = "P2";
                            // early or late mark permission taken 
                            if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {

                                $dailyDates[$rw->AT_Date]['btime']  = $datebase['htime']-$rw->AT_Hours;
                            } else {
                                $dailyDates[$rw->AT_Date]['btime']  = $HITime;
                            } 
                        }// same as invalid consider the whole day as hourly incomplete
                        else {
                            // early or late mark permission taken 
                            if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {

                                $dailyDates[$rw->AT_Date]['btime']  =  $datebase['wtime']-$rw->AT_Hours;
                            } else {
                                $dailyDates[$rw->AT_Date]['btime']  = $HITime;
                            }                            
                            $dailyDates[$rw->AT_Date]['label']  = "Hours Incomplete";
                            $dailyDates[$rw->AT_Date]['type']   = "IH";
                        }
                    } else {
                        $dailyDates[$rw->AT_Date]['color']      = "red";
                        $dailyDates[$rw->AT_Date]['leave']      = 1; 
                        $dailyDates[$rw->AT_Date]['label']      = "Invalid"; 
                        $dailyDates[$rw->AT_Date]['color']      = "red"; 
                        $dailyDates[$rw->AT_Date]['type']       = "L";
                        $dailyDates[$rw->AT_Date]['latetime']   = 0; 
                        $dailyDates[$rw->AT_Date]['earlytime']  = 0;
                    } 
                } // marked in and out if  
            } // marked section else 

            $dailyDates[$rw->AT_Date]['mincolor']   = $dailyDates[$rw->AT_Date]['color'];
            $stime          = "";
            // calcualte the sum of time 
            $latesign   += $dailyDates[$rw->AT_Date]['latetime'];
            $earlyout   += $dailyDates[$rw->AT_Date]['earlytime'];
            $lateDays   += ($dailyDates[$rw->AT_Date]['latetime'] > 0) ? 1 : 0;
            $earlyDays  += ($dailyDates[$rw->AT_Date]['earlytime'] > 0) ? 1 : 0;
            $extratime  += ($rw->AT_Hours > $datebase['wtime']) ? $rw->AT_Hours-$datebase['wtime'] : 0;
            $dailyDates[$rw->AT_Date]['btime'];
            // create html hover tooltip
            if ($dailyDates[$rw->AT_Date]['btime'] > 0) {
                $stime  .= " Hours Incomplete : ".$dailyDates[$rw->AT_Date]['btime']." Min <br>";
                $dailyDates[$rw->AT_Date]['hlabel'] .= " IH"; 
            }
            if ($dailyDates[$rw->AT_Date]['latetime'] > 0) {
                $stime  .= " Late Sign In : ".$dailyDates[$rw->AT_Date]['latetime']." Min <br>";
                $dailyDates[$rw->AT_Date]['hlabel'] .= " LS";
            }
            if ($dailyDates[$rw->AT_Date]['earlytime'] > 0) {
                $stime  .= " Early Sign Out : ".$dailyDates[$rw->AT_Date]['earlytime']." Min <br>";
                $dailyDates[$rw->AT_Date]['hlabel'] .= " ES";
            }
            $dailyDates[$rw->AT_Date]['stime']  = ($stime != "") ? "<div>".$stime."</div>":"";
        // end 02-07-2025            
        } else {
            // before 


        if (($rw->AT_Status == 0 ) || ( ( ($rw->AT_Hours < $new_min_half && $datebase['new'] == 1 ) || ($rw->AT_Hours < $halfdyMin && $datebase['new'] == 0) ) && ($rw->AT_Status==1)) ) {
            if($dailyDates[$rw->AT_Date]['type'] != 'H') { // invalid on holiday
                $dailyDates[$rw->AT_Date]['leave']   = 1; 
                $dailyDates[$rw->AT_Date]['label']   = "Invalid"; 
                $dailyDates[$rw->AT_Date]['color']   = "red"; 
                $dailyDates[$rw->AT_Date]['type']    = "L"; 
            }
        } else {                         
            $dailyDates[$rw->AT_Date]['label']   = "Marked"; 
            $dailyDates[$rw->AT_Date]['color']   = "green"; 
            $dailyDates[$rw->AT_Date]['leave']   = 0; 
            $dailyDates[$rw->AT_Date]['type']    = 'P'; 
            
            // check half or full day or working time keeping or not
            if ($rw->AT_Hours < $datebase['leastmin'] && ($rw->AT_SignOut != "00:00:00")) { // not keep time 
                if ($datebase['new'] == 1) {
                    if ($rw->AT_Hours >= ($halfdyMin+$new_min_full)) {
                        $dailyDates[$rw->AT_Date]['btime']  =  $datebase['leastmin']-$rw->AT_Hours;
                        $dailyDates[$rw->AT_Date]['label']  = "Hours Incomplete";
                        $dailyDates[$rw->AT_Date]['type']   = "IH";
                    } else if ($rw->AT_Hours > $halfdyMin) {
                       $dailyDates[$rw->AT_Date]['leave']   = (0.5);
                       $dailyDates[$rw->AT_Date]['btime']   = 0;
                       $dailyDates[$rw->AT_Date]['label']   = "Half Day";
                       $dailyDates[$rw->AT_Date]['type']    = "P2";
                    } else if($rw->AT_Hours >= $new_min_half) {
                       $dailyDates[$rw->AT_Date]['leave']   = (0.5);
                       $dailyDates[$rw->AT_Date]['btime']   = $halfdyMin-$rw->AT_Hours;
                       $dailyDates[$rw->AT_Date]['label']   = "Half Day";
                       $dailyDates[$rw->AT_Date]['type']    = "IH";
                    } else {
                        $dailyDates[$rw->AT_Date]['leave']   = 1;
                        $dailyDates[$rw->AT_Date]['label']   = "Invalid";
                    }
                } else if ($rw->AT_Hours <= ($datebase['wtime']/2)) {
                    $dailyDates[$rw->AT_Date]['leave']   = (0.5); 
                    $dailyDates[$rw->AT_Date]['btime']   = ($datebase['new'] == 1) ? ($halfdyMin-$rw->AT_Hours): 0;
                    $dailyDates[$rw->AT_Date]['label']   = "Half Day";
                    $dailyDates[$rw->AT_Date]['type']    = "P2";
                } /*else if ($datebase['new'] == 1) {

                    $dailyDates[$rw->AT_Date]['btime'] =  $datebase['leastmin']-$rw->AT_Hours;
                    $dailyDates[$rw->AT_Date]['label']   = "Hours Incomplete";
                    $dailyDates[$rw->AT_Date]['type']    = "IH";
                }*/ else {
                    $dailyDates[$rw->AT_Date]['btime']   =  0;
                    $dailyDates[$rw->AT_Date]['leave']   = (0.5);
                    if (strtotime($rw->AT_SignIn) >= strtotime("+".$relaxation." minutes",$datebase['login'])) {  
                        $dailyDates[$rw->AT_Date]['label']   = "Late Sign In";
                        $dailyDates[$rw->AT_Date]['type']    = "LS";
                    } else {                    
                        $dailyDates[$rw->AT_Date]['label']   = "Early Sign Out";
                        $dailyDates[$rw->AT_Date]['type']    = "ES";
                    }
                }
                $dailyDates[$rw->AT_Date]['color']      = "red"; 
            } else if ($datebase['new'] == 1 && ($rw->AT_SignOutEarly > 0 || $rw->AT_SignInDelay > 0)) {

                $dailyDates[$rw->AT_Date]['color']      = '#ff4d01';
                if ($rw->AT_SignOutEarly > 0) {
                    $dailyDates[$rw->AT_Date]['label']   = "Early Sign Out";
                    $dailyDates[$rw->AT_Date]['type']    = "ES";
                    $dailyDates[$rw->AT_Date]['btime']   =  $rw->AT_SignOutEarly;
                } else if ($rw->AT_SignInDelay > 0) {
                    $dailyDates[$rw->AT_Date]['label']   = "Late Sign In";
                    $dailyDates[$rw->AT_Date]['type']    = "LS";
                    $dailyDates[$rw->AT_Date]['btime']   =  $rw->AT_SignInDelay;
                }
            } else if (strtotime($rw->AT_SignIn) >= strtotime("+".$relaxation." minutes",$datebase['login'])) {
                    $dailyDates[$rw->AT_Date]['label']   = "Late Sign In";
                    $dailyDates[$rw->AT_Date]['type']    = "LS";
                    $dailyDates[$rw->AT_Date]['color']   = '#ff4d01';
            }
        } 
        /*$dateary    = explode('-',$rw->AT_Date);
        $firdate    = $dateary[0].'-'.$dateary[1].'-1';
        $chkdate    = $rw->AT_Date;
        while ($firdate != $chkdate) {
            $chkdate    = date('Y-m-d', strtotime($chkdate . ' -1 day')); 
        }*/
        $dailyDates[$rw->AT_Date]['mincolor']   = $dailyDates[$rw->AT_Date]['color'];
        $stime      = "";
        //20-sep-2024 late and early time count total finding
        if ($rw->AT_Hours > 0 && $rw->AT_SignOut != "00:00:00" && $rw->AT_Status==1 && $rw->AT_Hours > $new_min_half) {
            
            $actsin     = strtotime($rw->AT_SignIn);
            $actsout    = strtotime($rw->AT_SignOut);
            $reqdsin    = $datebase['login'];
            $reqdsout   = $datebase['logout'];
            $latesignday= ( $reqdsin < $actsin ) ? (($actsin - $reqdsin) / 60):0;
            if ($relaxation > 1 && $latesignday > 0) { 
                if ($latesignday > $relaxation) {
                    $latesignday -= $relaxation;
                    $relaxation = 0;
                } else {
                    $relaxation -= $latesignday;
                    $latesignday = 0;
                }
            }
            $earlyoutday = 0;
            if ($newdatechk == 1) {                
                $earlyoutday   = ( $actsout < $reqdsout  ) ? ( ($reqdsout - $actsout) / 60) : 0; 
            } else if ($rw->AT_Hours < $datebase['leastmin']) {
                $earlyoutday   = ( $actsout < $reqdsout  ) ? ( ($reqdsout - $actsout) / 60) : 0;
            }
            if ($relaxation > 1 && $earlyoutday > 0) { // time relax reduce
                if ($earlyoutday > $relaxation) {
                    $earlyoutday -= $relaxation;
                    $relaxation = 0;
                } else {
                    $relaxation -= $earlyoutday;
                    $earlyoutday = 0;
                }
            }
            $extrawtime = ($rw->AT_Hours > $datebase['wtime']) ? $rw->AT_Hours-$datebase['wtime'] : 0;
            // calcualte the sum of time 
            $latesign   += $latesignday;
            $earlyout   += $earlyoutday;
            $extratime  += $extrawtime;
            $lateDays   += ($latesignday > 0) ? 1 : 0;
            $earlyDays  += ($earlyoutday > 0) ? 1 : 0;

            // create html hover tooltip
            if (($dailyDates[$rw->AT_Date]['type'] == "IH" || $dailyDates[$rw->AT_Date]['type'] == "P2") && $dailyDates[$rw->AT_Date]['btime'] > 0) {
                $stime  .= " Hours Incomplete : ".$dailyDates[$rw->AT_Date]['btime']." Min <br>";
                $dailyDates[$rw->AT_Date]['hlabel'] .= " IH"; 
            }
            if ($latesignday > 0) {
                $stime  .= " Late Sign In : ".$latesignday." Min <br>";
                $dailyDates[$rw->AT_Date]['hlabel'] .= " LS";
            }
            if ($earlyoutday > 0) {
                $stime  .= " Early Sign Out : ".$earlyoutday." Min <br>";
                $dailyDates[$rw->AT_Date]['hlabel'] .= " ES";
            }
            $stime      = ($stime != "") ? "<div>".$stime."</div>":"";
        }
        $dailyDates[$rw->AT_Date]['stime']  = $stime;


        } // else part end ($newTimeStart == 1) // 01-07-2025
    }
    $latesign   = round($latesign);
    $earlyout   = round($earlyout);
    $leave      = $leavestart;
    $sandate    = [];
    foreach ($dailyDates as $date => $rw) {
        if($rw['leave'] == 1 && !empty($sandate)) {
            foreach ($sandate AS $sdate) {

                $dailyDates[$sdate]['leave']   = 1; 
                $dailyDates[$sdate]['label']   = "Unmarked / Leave"; 
                $dailyDates[$sdate]['color']   = "red"; 
                $dailyDates[$sdate]['type']    = "L"; 
            }
            $leave      = 0;
            $sandate    = [];
        }
        if ($rw['type'] == "H" && $leave > 0) {
            $leave++;
            $sandate[] = $date;
        } else if($rw['leave'] == 1) {
            $leave++;
        } else {
            $leave      = 0;
            $sandate    = [];
        }
    } // end foreach
    // sandwich setup month end
    if ($leaveend == 1 && !empty($sandate)) { 
        foreach ($sandate AS $sdate) {
            $dailyDates[$sdate]['leave']   = 1; 
            $dailyDates[$sdate]['label']   = "Unmarked / Leave"; 
            $dailyDates[$sdate]['color']   = "red"; 
            $dailyDates[$sdate]['type']    = "L"; 
        }
    } // end if
}
//print_r($dailyDates);
//die();
//$AttObj->Holidays[0]['dates']
//echo "Get Holiday list ....";
//print_r($offDays);
//var_dump($AttObj->Holidays[0]['dates']);
//die();
//-----------------------------------------------------------------------------//

//$Holidays       = $AttObj->Holidays; 

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';        
// <userdata name="full_days">' . $att_res['Full_days'] . '</userdata>
// <userdata name="half_days">' . $att_res['Half_days'] . '</userdata>
// <userdata name="off_days">' . $att_res['Off_days'] . '</userdata>
// <userdata name="leave_days">' . $att_res['Leave_days'] . '</userdata>  
//<column width="143" type="ro" align="left" > Date </column>  
echo '<head>
    <column width="63" type="ro" align="center" > SlNo </column>
    <column width="123" type="ro" align="left" > Date </column>
    <column width="80" type="ro" align="left" > Day </column>
    <column width="*" type="ro" align="left" > Sign In </column>
    <column width="*" type="ro" align="left" > Sign Out </column>
    <column width="88" type="ro" align="left" > Hours &amp; Minutes </column> 
    <column width="125" type="ro" align="left" >Status</column>    
    <column width="60" type="ro" align="center" >Time</column>
    <column width="70" type="ro" align="center">Break Time Taken</column> 
    <column width="70" type="ro" align="center">#cspan</column>
    <settings>
        <colwidth>px</colwidth>
    </settings>
    <beforeInit> 
        <call command="setSkin">
            <param>dhx_skyblue</param>
        </call> 
        <call command="setImagePath">
            <param>assets/grid/codebase/imgs/</param>
        </call> 
        <call command="enableSmartRendering">
            <param>false</param>
        </call> 
    </beforeInit>';
// all date will displayed @ 20-aug-2024
$viewHtml   = ''; 
$lastdate   = '';
$lday       = 0;
//$totlate    = 0;  
//$totearly   = 0;
$totincom   = 0; 
$totleave   = 0;  
$totBrek    = 0;   
if ($Att_Obj) {
    $j      = 1;
    krsort($dailyDates);
    foreach ($dailyDates as $date => $rw) {
        if ($date == date('Y-m-d') && $rw['time'] <= 0) {
            continue;
        }
        $rowCOlor = '';
        if ($userOffice['US_ResignFlag'] == 1 && $userOffice['US_ResignDate'] != "" && $userOffice['US_ResignDate'] < $date ) {
            $rowCOlor = ' style="color:red;"';
        }
        $viewHtml .= '<row id="' . $rw['rid'] . '-1" '.$rowCOlor.'>  
        <userdata name="hourlabel">'.$rw['hlabel'].'</userdata>                    
        <cell>' . $j . '</cell>
        <cell name="AT_Date">'.date('d/m/Y', strtotime($date)).'</cell>
        <cell name="AT_Day">'.date("D", strtotime($date)).'</cell>
        <cell name="AT_SignIn">'. $rw['in'].'</cell>
        <cell name="AT_SignOut">'.$rw['out'].'</cell>
        <cell name="AT_Hours">'.$rw['time'].'</cell>
        <cell name="AT_label" style="color:'.$rw['color'].';">'.$rw['label'].'</cell>';

        if ($rw['stime'] != "") {
            $viewHtml .= '<cell name="AT_Min"><![CDATA[<img src="images/icon/info_18.png" id="tp'. $rw['rid'] . '-1" onmouseover="preTally.Settings.showLabel(this,\''.$rw['stime'].'\');"  onmouseout="preTally.Settings.hideLabel(this);" />]]></cell>';
        } else {
            $viewHtml .= '<cell name="AT_Min" style="color:'.$rw['mincolor'].';">'.$rw['stime'].'</cell>';
        }
        // break time shown added 26-06-2025
        if (isset($listBreak[$date])) {
            $fontcolor = ($settingbt['time'] < $listBreak[$date][0]) ? 'style="color:red;"' : '';
            $viewHtml .= '<cell name="AT_Break" '.$fontcolor.'>'.$listBreak[$date][0].' Min</cell>';
            $viewHtml .= '<cell name="AT_Office">'.$listBreak[$date][1].' Min</cell>';
            $totBrek  += $listBreak[$date][0];
        } else {
            $viewHtml .= '<cell name="AT_Break">-</cell>';
            $viewHtml .= '<cell name="AT_Office">-</cell>';
        }
        // end break time        

        $viewHtml .= '</row>';
        $totleave   += $rw['leave'];  
        //$totearly   += ($rw['type'] == "ES") ? $rw['btime']:0;
        $totincom   += ($rw['type'] == "IH" || $rw['type'] == "P2") ? $rw['btime']:0;
        //$totlate    += ($rw['type'] == "LS") ? $rw['btime']:0;
        $j++;
    }
    /*$j      = 1;
    $totlate    = 0;  
    $totearly   = 0;
    $totincom   = 0; 
    $totleave   = 0;
    foreach ($Att_Obj as $rw) {

        $date           = $rw->AT_Date;
        $sign_in_time   = "--:--";
        $sign_out_time  = "--:--";
        $time           = "--";
        // all date will shown in list 21-aug-2024
        if ( $lastdate == '' && $date != '') {
            $lastdate       = date("Y-m-t", strtotime($date));
            if(date('Y-m-d') < $lastdate) {
                $lastdate   = date('Y-m-d');
            }
        } else if ($lday > 1) {
            $lastdate       = date('Y-m-d', strtotime($lastdate . ' -1 day'));            
        }
        $lday               = date('d', strtotime($lastdate));
        while ($lastdate != $date) {

            $detfull        = getOffDay($lastdate, $offDays);
            $totleave       += $detfull['leave'];
            $rowid          = $userId.'_'.$lday;
            $viewHtml .= '<row id="' . $rowid . '">                     
            <cell>' . $j . '</cell>
            <cell name="AT_Date">'.date('d/m/Y', strtotime($lastdate)).'</cell>
            <cell name="AT_Day">'.date("D", strtotime($lastdate)).'</cell>
            <cell name="AT_SignIn">'. $sign_in_time.'</cell>
            <cell name="AT_SignOut">'.$sign_out_time.'</cell>
            <cell name="AT_Hours">'.$time.'</cell>
            <cell name="AT_Hours" style="color:'.$detfull['color'].';">'.$detfull['label'].'</cell>
            <cell name="AT_Min">-</cell>
                </row>';
            if ($lday > 1) {
                $lastdate   = date('Y-m-d', strtotime($lastdate . ' -1 day'));
                $lday       = date('d', strtotime($lastdate));
            }
            $j++;
        }
        // end the new sections
        $lstcolr        = 'black';        
        $detfull        = getOffDay($date, $offDays);
        $statLabel      = $detfull['label'];

        // date checking no grace time @ 21-aug-2024
        $newdatechk = 0;
        if (date('Y-m-d',strtotime($rw->AT_Date)) >= "2024-08-13") {
            $newdatechk = 1;
        }
        // check the users time based on that date
        if(!empty($rw->AT_AllotTime)) {            
            $assignedTime   = json_decode($rw->AT_AllotTime);
            $allotInTime    =  strtotime($assignedTime->in);
            //$allotOutTime   = $assignedTime->out;
            $leastMins      = $assignedTime->whour;
            $workMins       = $assignedTime->whour;
        } else {
            $allotInTime    = strtotime($CompObj['US_LoginTime']);
            $workMins       = $CompObj['US_WrkHours'];
            $leastMins      = ($newdatechk == 1) ? $workMins:$workMins-$CompSett['CS_WrkHrGraceTime'];
        }
        $halfdyMin          = (int)($leastMins/2);
        // old list calculation shown here
        $mins   = ($rw->AT_Hours) % 60;
        $hr     = ($rw->AT_Hours - $mins) / 60;  
        if (($date != date("Y-m-d")) && ($rw->AT_Status == 0 ) || ( ($rw->AT_Hours < $halfdyMin) && ($rw->AT_Status==1)) ) {
            $statLabel  = "Invalid";
            $totleave   += 1;
            $lstcolr    = 'red';
        } else {            

            $relaxation=$CompSett['CS_LoginGraceTime']+1;
            // earlier login relaxatioin was 30 min
            if ( date('Y-m-d',strtotime($rw->AT_Date)) <= "2023-02-20" ) {
                $relaxation = 31;
            }
            //No grace time for login & logout after 19 Aug 2024
            if ( $newdatechk == 1 ) {
                $relaxation = 1;
            }
            // early and late time labels @ 22-aug-2024
            $caltime    = 0;
            $minLabel   = '';
            // check half or full day or working time keeping or not
            if ($rw->AT_Hours < $leastMins && ($rw->AT_SignOut != "00:00:00")) { // not keep time                
                if ($rw->AT_Hours <= ($workMins/2)) {
                    $caltime    = ($newdatechk == 1) ? ($halfdyMin-$rw->AT_Hours): 0;
                    $totleave   += (0.5);                    
                } else {
                    $caltime    = ($newdatechk == 1) ? ($leastMins-$rw->AT_Hours): 0;
                }
                $totincom       += $caltime;
            } else if ($newdatechk == 1 && ($rw->AT_SignInDelay > 0 || $rw->AT_SignOutEarly > 0)) {
                if ($rw->AT_SignInDelay > 0) {
                    $caltime    = $rw->AT_SignInDelay;
                    $totlate    += $rw->AT_SignInDelay;
                } else {
                    $caltime    = $rw->AT_SignOutEarly;
                    $totearly   += $rw->AT_SignOutEarly;
                }
            }
            if ( $caltime > 0 ){
                $minLabel       = $caltime." Min";
            }
            // $minLabel = '';
            // if ($newdatechk == 1 && ($rw->AT_SignInDelay > 0 || $rw->AT_SignOutEarly > 0)) {
            //     if ($rw->AT_SignInDelay > 0 && $rw->AT_SignOutEarly > 0) {
            //         $minLabel   = ($rw->AT_SignInDelay+$rw->AT_SignOutEarly);
            //         $totincom   += ($rw->AT_SignInDelay+$rw->AT_SignOutEarly);  
            //     } else if ($rw->AT_SignInDelay > 0) {
            //         $minLabel   = $rw->AT_SignInDelay;
            //         $totlate    += $rw->AT_SignInDelay;
            //     } else {
            //         if ($rw->AT_Hours < $leastMins) {
            //             $totincom   += $rw->AT_SignOutEarly;
            //         } else {
            //             $totearly   += $rw->AT_SignOutEarly; 
            //         }
            //         $minLabel   = $rw->AT_SignOutEarly;
            //     }
            //     $minLabel       .= " Min";
            // }

            if (($rw->AT_Hours < $leastMins) && ($rw->AT_SignOut != "00:00:00") && $newdatechk == 1) {
                $statLabel = ($caltime != ($leastMins-$rw->AT_Hours)) ? "Half Day":"Hours Incomplete";
            } else if (strtotime($rw->AT_SignIn) >= strtotime("+".$relaxation." minutes",$allotInTime)) {  
                $statLabel = ($rw->AT_Hours <= ($workMins/2)) ? "Half Day": "Late Sign In";
                $lstcolr   = '#ff4d01';
            } else if ($rw->AT_SignOutEarly > 0 && $newdatechk == 1) {
                $lstcolr   = '#ff4d01';
                $statLabel = "Early Sign Out";
            }else if (($rw->AT_Hours < $leastMins) && ($rw->AT_SignOut != "00:00:00") && $newdatechk == 0) {
                $lstcolr   = '#ff4d01';
                $statLabel = "Early Sign Out";
            } else {              
                $statLabel = "Marked";
                $lstcolr   = 'green';
            }
            if ($rw->AT_Hours < $leastMins && $rw->AT_Status == 1) {
                $lstcolr   = 'red';
            }
        }
        if ($rw->AT_SignIn != "" || $rw->AT_SignIn != null) {
            $sign_in_time = date('g:i a', strtotime($rw->AT_SignIn));
        }
        if ($rw->AT_SignOut != "" || $rw->AT_SignOut != null) {
            $sign_out_time = date('g:i a', strtotime($rw->AT_SignOut));
        }
        if (($Att_arr['AT_Status'] == 0) && ($rw->AT_SignOut == "00:00:00")) {
            $sign_out_time = "--:--";
        }
        if ($hr == 0 && $mins == 0) {
            $time = "--";
        } else {
            $time = (int) $hr . ':' . ($mins < 10 ? '0'.(int)$mins:(int)$mins);
        }
        // calculations ends
        $viewHtml .= '<row id="' . $rw->AT_Id . '">                     
        <cell>' . $j . '</cell>
        <cell name="AT_Date">'.date('d/m/Y', strtotime($date)).'</cell>
        <cell name="AT_Day">'.date("D", strtotime($date)).'</cell>
        <cell name="AT_SignIn">'. $sign_in_time.'</cell>
        <cell name="AT_SignOut">'.$sign_out_time.'</cell>
        <cell name="AT_Hours">'.$time.'</cell>
        <cell name="AT_Hours" style="color:'.$lstcolr.';">'.$statLabel.'</cell>
        <cell name="AT_Min" style="color:'.$lstcolr.';">'.$minLabel.'</cell>
            </row>';
        $j++;
    }
    while ($lday > 1) { 
        $lastdate       = date('Y-m-d', strtotime($lastdate . ' -1 day')); 
        $lday           = date('d', strtotime($lastdate));          
        $detfull        = getOffDay($lastdate, $offDays);
        $totleave       += $detfull['leave'];    
        $sign_in_time   = "--:--";
        $sign_out_time  = "--:--";
        $statLabel      = "Unmarked";
        $time           = "--";
        $rowid          = $userId.'_'.$lday;
        $viewHtml .='<row id="' . $userId.'_'.$lday . '">                     
        <cell>' . $j . '</cell>
        <cell name="AT_Date">'.date('d/m/Y', strtotime($lastdate)).'</cell>
        <cell name="AT_Day">'.date("D", strtotime($lastdate)).'</cell>
        <cell name="AT_SignIn">'. $sign_in_time.'</cell>
        <cell name="AT_SignOut">'.$sign_out_time.'</cell>
        <cell name="AT_Hours">'.$time.'</cell>
        <cell name="AT_Hours" style="color:'.$detfull['color'].';">'.$detfull['label'].'</cell>
        <cell name="AT_Min">-</cell>
            </row>';
        $j++;
    }*/
}else{
    $viewHtml .= '<row id="0"> 
        <cell colspan="10"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
}
//Total:,Unmarked/Leave: '.$totleave.',
//font-weight:bold;color:red;padding: 1px;background-color:white;font-style:normal;text-align:center;,
echo '<afterInit>       
    <call command="attachHeader">
        <param><![CDATA[#rspan,#rspan,#rspan,#rspan,#rspan,#select_filter,#select_filter,#rspan,Personal,Official]]></param>

    </call>
    <call command="attachFooter"><param><![CDATA[Total:,Late Sign In Days: '.$lateDays.' <br> TIme: '.$latesign.' Min,#cspan,Early Sign Out Days : '.$earlyDays.' <br> Time: '.$earlyout.' Min,#cspan,Hours Incomplete: '.$totincom.' Min,#cspan,Break: '.$totBrek.' Min,#cspan,#cspan]]></param>
        <param>font-weight:bold;background-color:white;font-style:normal;,font-weight:bold;color:#ff4d01;background-color:white;font-style:normal;text-align:center;padding: 1px;,,font-weight:bold;color:#ff4d01;background-color:white;font-style:normal;text-align:center;padding: 1px;,,font-weight:bold;color:red;background-color:white;font-style:normal;text-align:center;,,font-weight:bold;background-color:white;,,</param>
    </call>
</afterInit> 
</head>'; 
echo $viewHtml;
echo '</rows>';
?>