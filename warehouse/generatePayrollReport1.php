<?php
/**
 * Generate all staff salary processing based on the attendance of the month
 * Created By Bilin @ OCT 8, 2024
*/
require_once($BASEPATH . 'includes/functions.php');
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
include_once($BASEPATH . "preTallyClass/AttendanceRptClass.php");
include_once($BASEPATH . "preTallyClass/LeaveClass.php");     // 27-06-2025 by Achu
include_once($BASEPATH . "preTallyClass/RuleClass.php");     // 27-06-2025 by Achu
require_once($BASEPATH . "preTallyClass/LateEntryClass.php"); // 09-07-2025 By Achu
//date create based on the user request
$year       		= date("Y");
if($REQUEST['att_year'] && $REQUEST['att_month'] && str_pad($REQUEST['att_month'], 2,"0",STR_PAD_LEFT).'-'.$REQUEST['att_year'] != date('m-Y')){       
    $year 		= $REQUEST['att_year']; 
    $month  	= str_pad($REQUEST['att_month'], 2,"0",STR_PAD_LEFT);
  	$g_date     = $year.'-'.$month;
	//$daysInMonth= cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$lastday    = ($g_date == date('Y-m')) ? date("d")-1 : date("t", strtotime($g_date.'-01'));
} else { // invalid no needed to create the reports   
     echo 3;
     return;
}
// create objects for the class
$AttObj 	= new AttendanceClass();
$AttRptObj  = new AttendanceRptClass();
$UserObj    = new UserClass();
$LeaveObj   = new LeaveClass();		// 27-06-2025 by Achu
$RuleObj    = new RuleClass();   // 27-06-2025 by Achu
$lateEntryObj = new LateEntryClass(); // 09-07-2025 By Achu
$lstdate  	= $year."-".$month."-".$lastday; // last date of the month
$stdate = $year."-".$month."-1";
$cudate     = date('Y-m-d'); // 09-07-2025 By Achu
$lastDateForGraceTime = ($lstdate >= $cudate) ? date('Y-m-d', strtotime($cudate . ' -1 day')) :$lstdate; // 09-07-2025 By Achu
// echo $stdate."=>".$lastdateForGraceTime;die;
$lateEntryAllow = $lateEntryObj->getallowedGraseTime($stdate, $lastDateForGraceTime); // 09-07-2025 By Achu
//check the payroll is already generated or not
if ($AttObj->verifyMonth("employee_payroll","EP_Month",$month,$preTally_user_ofid,"EP_Year",$year)) { 

	// user not set to check the salary structure then not execurte the below code
	$empSalStruct = array();
	$userSalCount = 0;
    if (!isset($REQUEST['datas'])) {
    	// check the salary structure enabled users salary and structure status
        $AttObj->empSalPayroll($month, $year, $preTally_user_ofid);
        $Att_Objuser    = $AttObj->userAttendance;
        foreach ($Att_Objuser as $rws) {
            $user_saldetails=$rws["USR_Details"];  
            if (($user_saldetails['US_GrossSal']==0)||($user_saldetails['SS_Status']==0)||($user_saldetails['SS_Status']==3) ) {
                $userSalCount++;
                $empSalStructNames=$user_saldetails['US_EMPID']." ".$user_saldetails['US_FName']." ".$user_saldetails['US_LName'];
                array_push($empSalStruct,$empSalStructNames);
            } 
        }        
    }
    if ($userSalCount != 0) { // one of the users salary is 0 or salary structure is not active return the count and flag
        $empSalStruct["status"]="salary_struct_err";
        $empSalStruct["count"]=$userSalCount;
        echo json_encode($empSalStruct);
        return;
    }	

    $minTimePresent = (20261001 <= date("Ymd", strtotime($lstdate))) ? 30 : 1.5; // this old By Bilin on 2025/06/04
	$newTimeStart   = (20261001 <= date("Ymd", strtotime($lstdate))) ? 1:0; //18-06-2025 
	$newTimeCheckRuleStarts = (20261001 <= date("Ymd", strtotime($lstdate))) ? 1:0;  // 09-07-2025 By Achu
	// start to find the employees/staff salary details and generate the payroll...
    // get the company Office ID & Details based on the login user office id
	$UserObj->selectCompanySettings($preTally_user_ofid);
	$compSeting     = $UserObj->CompanySettingsArray;
	$AttObj->getHolidays($lstdate,$preTally_user_ofid); // Company Holidays List
	$AttObj->getWeekendOffs($month,$year,$preTally_user_ofid); // Weekend Off List   
	$AttObj->getOfficeRH($preTally_user_ofid); // RH Off List   
	$AttObj->getLeaveType($preTally_user_ofid); // Type Of Leaves
	$AttObj->getDeptHolidays($lastdate,$preTally_user_ofid,0); // dept holidays 10-09-2025
	$leaveTypes     = $AttObj->getLeaveTypeArray;
	$leave_typ_ary  = [];
	$Holidays       = $AttObj->Holidays;    
	$WeekendOffs    = $AttObj->WeekOffs; 
	$monthlyWeekendOffs = $AttObj->MonthlyWeekOffs;
	foreach ($leaveTypes as $rows) {
        $leave_typ_ary[] = $rows->LT_Id; 
    }
	// get the users attendance (active users or salary need to paid users)
	$filterary  = ['month'=>$month, 'year'=>$year, 'office_id'=>$preTally_user_ofid, 'lastday'=>$lastday, 'comm_gracetime'=>$compSeting['CS_WrkHrGraceTime'], 'relaxation'=>$compSeting['CS_LoginGraceTime'], 'get_salary'=>1];
	// call the list of users and their daily attendance brief......
	$AttRptObj->listReptAttendance($filterary);
	//echo $AttRptObj->sel_qry;
	if ($AttRptObj->total > 0) { // user list present the process
		$count 			= 0; 
		$err_Flag 		= 0;
		$daysInMonth 	= cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$dataPayroll 	= [];
		$repayment_ids 	= [];
		$errorPayroll 	= [];
		if ( !empty($AttRptObj->selt_users) ) {
	        //echo "last work day of previous & next month first work day of selected users";
	        $adjAttendance  = $AttRptObj->getAdjMnthAttendance($month, $year); 
	        //echo $AttRptObj->sel_qry;
	        //print_r($adjAttendance);
	        //echo "sum of late and early sing in/out time of selected users - total only";
	        $punchDet       = $AttRptObj->getPunchTime($month, $year); 
	        //print_r($punchDet);
	        //echo "list all approved and leave type based counts of all users";
	        $AttRptObj->getApprovedLeaves($month, $year, $leave_typ_ary);
	        //print_r($AttRptObj->approve_leaves);  

	        //echo "get all users RH logs based on the current year";
	        $AttRptObj->getTakenRHBatches($year);
	        //print_r($AttRptObj->rh_batch);       
	        //print_r($AttRptObj->rh_days);  
	        // echo "get all users loan advance payment details";
	        $AttRptObj->getRepaymentAmounts($month, $year);   
	        //print_r($AttRptObj->repay_list);   
	    }
	    // user based list for processing
	    foreach ($AttRptObj->listAttendance as $usrid => $rw) {

	    	if ($rw['US_GrossSal']<=0 || $rw['SS_Status']==0 || $rw['SS_Status']==3) { 
	    		continue;
	    	}
	    	$half           = 0;
	        $halfact        = 0;
	        $advanceSalary  = 0;
        	$cmpnstry       = 0;
        	$lcCount        = 0;    
	        $full           = 0;
	        $fullact        = 0;
	        $absent         = 0;
	        $absentact      = 0;
	        $holiday        = 0;   
	        $late_sign      = 0; 
	        $early_out      = 0;
	        $hour_incomple  = 0;
	        $sdl            = 0; // salary deductable leave
	        $extratime      = 0;
	        $prevMonthComSalary = 0;
	        $cwp            = 0;
	        $otherMonthFridaySaturday = [];
	        $startArray     = $endArray = [];
	        $leaveDays      = [];      // 30052025 by Achu
	        $leaveDatesWith = [];
	        $lcFlag         = false;
	        // $sundayCellCount= 0;
	        $hasOtherDays = $hasLastWeekDay = $holidaySandwich = $startOfLastWeek = $lastDayOfMonth = $checkOtherDayRuleFile = $previousMonthFirstWeekCheck = false;
	        $dep = true;
	        $ofc = false;
	        $att_details    = $rw["list"]; // full attendance details 
	        $stid           = $rw['ST_Id']; 
	        $dpid           = $rw['DP_Id'];
	        $ofcid          = $rw['OF_Id'];     // Office ID
	        $deptHoliday    = isset($AttObj->Dept_Holidays[$dpid]) ? array_merge((array)$AttObj->Dept_Holidays[$dpid][0]['dates'],(array)$AttObj->Dept_Holidays[$dpid][$stid]['dates']) : []; //10-09-2025
	        $att_user_days  = array_keys($att_details); // all attendance days
	        if (isset($adjAttendance[$usrid]) ) {
	            // add attendance days with last month last day and next month first
	            if ($adjAttendance[$usrid]["last"] != null) {
	                array_push($att_user_days,$adjAttendance[$usrid]["last"]);
	            }
	            if ($adjAttendance[$usrid]["next"] != null) {
	                array_push($att_user_days,$adjAttendance[$usrid]["next"]);
	            }
	        }
	        // all rh days stored for temporary;
	        $tmp_rharray    = (!empty($AttObj->RH_Holidays)) ? array_merge((array)$AttObj->RH_Holidays[0],(array)$AttObj->RH_Holidays[$stid]) : array(); 
	        // set HD_Batch As key
	        $flp_array      = (!empty($AttRptObj->rh_batch)) ? array_flip($AttRptObj->rh_batch[$usrid]) : array(); 
	        // unique in the first array (HD_Batch is key)
	        $rh_holiday     = array_diff_key($tmp_rharray, $flp_array); 
	        // clear some variables
	        if (isset($m_Hol)) {
	            unset($m_Hol);
	        }
	        if (isset($AttObj->tmp_hol)) {
	            unset($AttObj->tmp_hol);
	        } 
	        $AttObj->tmp_hol    = array();
	        $m_Hol              = array();
	        
	        // set the leave list into the array
	        $approvedLeaveDates = (!empty($AttRptObj->approve_leaves[$usrid]['list'])) ? array_column($AttRptObj->approve_leaves[$usrid]['list'], 'LRD_Date') : array(); 
	        //find the common in two array and assign that based first array key
	        $array_intr     = array_intersect($approvedLeaveDates, $att_user_days);

	        // date wise data grid loading start....
	        $notIncrement = false;
	        for ($j = 1; $j <= $lastday; $j++) {   
	        
	            $d          = ($j < 10) ? "0".$j : $j; 
	            $cdate      = $year . "-" . $month . "-" . $d;
	            $AttObj->getRepaymentAmounts($usrid,$month, $year);
	            $RuleObj->getRules($cdate);
	            $RulesList  = $RuleObj->listRuleArray;
	            $DeptArray[] = 66; // dept
	            $OfficeArray[] = 5; // office
	            if(!empty($RulesList)){
	                $holidayRules = $weekendRules = false ;
	                $processedRulesDays = $otherRules = [];
	                $DeptArray = [];
	                $OfficeArray = [];
	                foreach ($RulesList as $rule) {
	                    if ($rule->RL_Sandwich_Type == 'none' && $rule->RL_Is_LOP == 1){
	                        $otherRules[] = $rule;
	                    } elseif ($rule->RL_Sandwich_Type == 'holiday' && $rule->RL_Is_Sandwich == 1){
	                        $holidayRules = $rule;
	                    } elseif ($rule->RL_Sandwich_Type == 'weekend' && $rule->RL_Is_Sandwich == 1){
	                        $weekendRules = $rule;
	                    };
	                }
	                // get the 0th index array element
	                $firstRule = reset($RulesList);
	                $decodedDeptOficeIds = json_decode($firstRule->RL_Except_Dept_office);
	                foreach ($decodedDeptOficeIds as $item) {
	                    list($dept, $office) = explode('-', $item);
	                    $DeptArray[] = $dept; // dept
	                    $OfficeArray[] = $office; // office
	                }
	            }
	            $hrs        = (isset($att_details[$cdate])) ? $att_details[$cdate]['AT_Hours'] : 0; // total hours
	            // company defined times
	            $intime         = $rw['US_LoginTime'];
	            $outtime        = $rw['US_LogoutTime'];
	            $worktime       = $rw['US_WrkHours'];
	            $gracetime      = $att_details[$cdate]['grase_time'];
	            $relaxation     = $att_details[$cdate]['relaxation'];
	            if(!empty($att_details[$cdate]['AT_AllotTime'])) {            
	                $assignedTime   = json_decode($att_details[$cdate]['AT_AllotTime']);
	                $intime         = $assignedTime->in; 
	                $outtime        = $assignedTime->out; 
	                $worktime       = $assignedTime->whour; 
	            }
	            $loginTime      = $att_details[$cdate]['AT_SignIn'];
	            $logoutTime     = $att_details[$cdate]['AT_SignOut']; 
	            // reduce the grase time from the total hours working
	            $minActHrs      = ($worktime-$gracetime);
	            $minWrkHrs      = ($minActHrs)/2;
	            $minWrkHHrs     = ($minActHrs)/4;  // 10-07-2025 by Achu
	            // leave days checking - approved leave date have any attendance marked
	            $new_min_half   = ($newTimeStart == 1) ? ($minWrkHrs-($minTimePresent/2)) : ($minWrkHrs/$minTimePresent); // 18-06-2025
	            $new_min_full   = ($newTimeStart == 1) ? ($minWrkHrs-$minTimePresent) : ($minWrkHrs/$minTimePresent); // 18-06-2025
	            $cellType   = "N"; // NRM -- Normal Holiday
	            if (multi_array_search($cdate,$AttObj->RH_Holidays[0]) || multi_array_search($cdate,$AttObj->RH_Holidays[$stid])) {
	                $cellType   = "R"; //RH -- Reserved Holiday
	                $title      = "Reserved Holiday";
	                $cellTypeValue[$j] = $cellType;
	                //$holidayList[] = $cdate;
	            }
	            if (!empty($array_intr) && in_array($cdate, $array_intr)) {

	                $meanDifAM   = abs(strtotime("12:00:00")-strtotime($loginTime));
	                $meanDifPM   = abs(strtotime("12:00:00")-strtotime($logoutTime));
	                $attSession  = ($meanDifAM > $meanDifPM) ? "FN" : "AN"; 
	                // find key of the array stored the leave date
	                $key_L      = array_search($cdate, $approvedLeaveDates);
	                $session    = $AttRptObj->approve_leaves[$usrid]['list'][$key_L]['LRD_Session'];
	                // remove atendance deails 
	                if (($key = array_search($cdate, $att_user_days)) !== false && $session=="FL") {
	                    unset($att_user_days[$key]);
	                } else if (($key = array_search($cdate, $att_user_days)) !== false && $hrs < $minWrkHrs && ($session=="FN" || $session=="AN") && $attSession==$session) {
	                   unset($att_user_days[$key]);
	                }
	            }
	            $cellValue = '-';
	            if($newTimeCheckRuleStarts == 1){
	            	$latetime   = $att_details[$cdate]['AT_SignInDelay']; 
	                $earlytime  = $att_details[$cdate]['AT_SignOutEarly'];  

	                $strtimeIn      = strtotime($loginTime);
	                $strtimeOut     = strtotime($logoutTime);
	                $allowTime      = $lateEntryAllow[$att_details[$cdate]['AT_Date']]; // company allowed for morning time
	                // if($usrid=="3732" && $cdate=="2025-07-03"){
	                //     echo "actin innnnnn222 => ".print_r($lateEntryAllow);die;
	                // }
	                $apprMorgTime   = (isset($listlateEntry[$att_details[$cdate]['AT_Date']]) && $listlateEntry[$att_details[$cdate]['AT_Date'][1]] > $allowTime) ? $listlateEntry[$att_details[$cdate]['AT_Date']][1]: $allowTime; // morning approved
                	$apprEvngTime   = (isset($listlateEntry[$att_details[$cdate]['AT_Date']])) ? $listlateEntry[$att_details[$cdate]['AT_Date']][2]: 0; // evening time approved
                	$approveLeaves = $LeaveObj->getLeaveReqUser(['user_id'=>$usrid, 'from_date'=>$stdate, 'to_date'=>$lstdate, 'status'=>'2']);
                	$sesionAlter   = (isset($approveLeaves[$att_details[$cdate]['AT_Date']])) ? $approveLeaves[$att_details[$cdate]['AT_Date']]: '';
	                // $leaveTodayTp   = (array_search($cdate, $approvedLeaveDates)) ? $approvedLeaveDates[$cdate]: ''; // approved leave in this day
	                $HITime = $att_details[$cdate]['AT_SignInDelay'] + $att_details[$cdate]['AT_SignOutEarly'];
	                $afternoonsignhours = $apprMorgTime+$minWrkHrs;
	                $afterNoonSignin    = strtotime("+" . $afternoonsignhours . " minutes", strtotime($intime));
	                $forenoonsignhours  = $minWrkHrs + $apprEvngTime;
	                $foreNoonSignout    = strtotime("-" . $forenoonsignhours . " minutes", strtotime($outtime));
	                $actInTime          = strtotime("+" . $apprMorgTime . " minutes",strtotime($intime));
	                $actOutTime          = strtotime("-" . $apprEvngTime . " minutes", strtotime($outtime));
	                
	                if (in_array($cdate, $att_user_days) && (!array_search($cdate, $approvedLeaveDates)) && $hrs > 0) {
	                    unset($AttObj->tmp_hol); 
	                    $AttObj->tmp_hol = array();
	                    if ( ($cdate != date("Y-m-d") && $att_details[$cdate]['AT_Status'] == 0) || ($att_details[$cdate]['AT_Hours'] <= $minWrkHHrs && $att_details[$cdate]['AT_Status'] == 1) ) {
	                        $cellValue  = "L";
	                        $absent     += 1; 
	                        $title      = "Leave";
	                        $absentact  += 1;
	                        $leaveDays[] = $cdate;
	                        $leaveDatesWith[$cdate] = "L";
	                    }else{
	                        if ($logoutTime != "00:00:00" && $att_details[$cdate]['AT_Date'] != date("Y-m-d")) {
	                            if ($worktime <= $att_details[$cdate]['AT_Hours']) {
	                                if ( ($strtimeIn <= strtotime($intime) && $strtimeOut >= strtotime($outtime)) OR ($strtimeIn <= $actInTime && $strtimeOut >= strtotime($outtime)) OR ($strtimeOut >= $actOutTime && $strtimeIn >= strtotime("-" . $apprEvngTime . " minutes",strtotime($intime)) && $apprEvngTime > 0) OR ($att_details[$cdate]['AT_Hours'] >= ($HITime + $worktime))  ) {
	                                    $cellValue  = 'P';
	                                    $title      = "Present";
	                                    $full       += 1;
	                                    $fullact    += 1;
	                                    $extratime  += ($worktime < $hrs) ? ($hrs-$worktime): 0;
	                                }else{
	                                    // late sig in and early sign out time with out approval consider Hours INcomplete
	                                    $hour_incomple +=  ($att_details[$cdate]['AT_SignOutEarly'] > 0) ? $att_details[$cdate]['AT_SignOutEarly']: $att_details[$cdate]['AT_SignInDelay']; 
	                                    $title      = "Full day (Incomplete Hours)";
	                                    $half       += 1;
	                                    $absent     += 0.5;
	                                    $fullact    += 1;
	                                    $cellValue = "HI";
	                                }
	                            }// marked but not complete 9 hours (above halfday) 
	                            else if ($att_details[$cdate]['AT_Hours'] >= ($minWrkHrs)) {
	                                // morning time proper and apply leave on afternoon
	                                // Morning time with appove late entry and apply afternoon leave
	                                // Evening halfday sigin or appvove laterentry & Leave on morning
	                                // work time above half day (halfday+hour incomplete time) + apply half day leave too
	                                if ( ($strtimeIn <= $actInTime && $sesionAlter == 'AN') || ($strtimeIn <= $afterNoonSignin && $sesionAlter == 'FN') || ( $HITime < $minWrkHrs &&  $att_details[$cdate]['AT_Hours'] >= ($minWrkHrs+$HITime) && ($sesionAlter == 'FN' || $sesionAlter == 'AN') )     ) {  
	                                    $half       += 1;
	                                    $absent     += 0.5;
	                                    $absentact  += 0.5;
	                                    $halfact    += 1;
	                                    $leaveDays[] = $cdate;       // 30052025 by Achu
	                                    $title  = "Half Day";
	                                    $cellValue  = "P2"; 
	                                    $leaveDatesWith[$cdate] = "P2"; // 11062025 by Achu   
	                                    if ($strtimeIn > $actInTime && $sesionAlter == 'AN') {
                                        	$hour_incomple = round(abs($strtimeIn - $actInTime) / 60,2);
	                                    } else if ($strtimeIn > $afterNoonSignin && $sesionAlter == 'FN') {
	                                        $hour_incomple = round(abs($strtimeIn - $afterNoonSignin) / 60,2);
	                                    }
	                                    if ($strtimeOut < $actOutTime && $sesionAlter == 'FN') {
	                                        $hour_incomple += round(abs($actOutTime - $strtimeOut) / 60,2);
	                                    } else if ($strtimeOut < $foreNoonSignout && $sesionAlter == 'AN') {
	                                        $hour_incomple += round(abs($foreNoonSignout - $strtimeOut) / 60,2);
	                                    }
	                                    // chcek the user take extra time 
	                                    if (($hour_incomple+$minWrkHrs) <= $att_details[$cdate]['AT_Hours']) {
	                                        $hour_incomple = 0;
	                                    }    
	                                }// above 3/4 of work time completed and leave not approved
	                                // early and late minutes consider as hourly incomplete
	                                else if ( ($minWrkHHrs+$minWrkHrs) <= $att_details[$cdate]['AT_Hours'])  {
	                                    // if the user apply half day leave
	                                    if ($sesionAlter == 'FN' || $sesionAlter == 'AN') { //09-07-25
	                                        $half       += 1;
	                                        $absent     += 0.5;
	                                        $absentact  += 0.5;
	                                        $halfact    += 1;
	                                        $leaveDays[] = $cdate;       // 30052025 by Achu
	                                        $title  = "Half Day";
	                                        $cellValue  = "P2"; 
	                                        $leaveDatesWith[$cdate] = "P2"; // 11062025 by Achu
	                                        $hour_incomple = 0;
	                                    } else {
	                                        // early or late mark permission taken 
	                                        if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {

	                                            $hour_incomple  += $worktime-$att_details[$cdate]['AT_Hours'];
	                                        } else {
	                                            $hour_incomple  += $HITime;
	                                        }                            
	                                        $title  = "Hours Incomplete";
	                                        $cellValue   = "HI";
	                                    }
	                                } // work above halfday but not approve half day leave
	                                // work time between 1/2 to below 3/4 of time
	                                else {
	                                    // if the user apply half day leave 09-07-25
	                                    if ($sesionAlter == 'FN' || $sesionAlter == 'AN') {
	                                        $half       += 1;
	                                        $absent     += 0.5;
	                                        $absentact  += 0.5;
	                                        $halfact    += 1;
	                                        $leaveDays[] = $cdate;       // 30052025 by Achu
	                                        $title  = "Half Day";
	                                        $cellValue  = "P2"; 
	                                        $leaveDatesWith[$cdate] = "P2";
	                                        // $hour_incomple  = 0; 
	                                        //check the in time and late signin hour incomplete
	                                        if ($strtimeIn > $actInTime && $sesionAlter == 'AN') {
                                           		$hour_incomple += round(abs($strtimeIn - $actInTime) / 60,2);
	                                        } else if ($strtimeIn > $afterNoonSignin && $sesionAlter == 'FN') {
	                                            $hour_incomple += round(abs($strtimeIn - $afterNoonSignin) / 60,2);
	                                        }
	                                        if ($strtimeOut < $actOutTime && $session == 'FN') {
	                                            $hour_incomple += round(abs($actOutTime - $strtimeOut) / 60,2);
	                                        } else if ($strtimeOut < $foreNoonSignout && $sesionAlter == 'AN') {
	                                            $hour_incomple += round(abs($foreNoonSignout - $strtimeOut) / 60,2);
	                                        }
	                                        // chcek the user take extra time 
	                                        // if (($hour_incomple+$minWrkHrs) <= $att_details[$cdate]['AT_Hours']) {
	                                        //     $hour_incomple = 0;
	                                        // }
	                                        // leave not approved                                
	                                    } else {
	                                        $title  = "Hours Incomplete";
	                                        // early or late mark permission taken 
	                                        if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {
	                                            $hour_incomple  += $worktime-$att_details[$cdate]['AT_Hours'];
	                                        } else {
	                                            $hour_incomple  += $HITime;
	                                        } 
	                                        $cellValue = "HI";
	                                    }
	                                }
	                            }//work above the 1/4 of work time or below half day
	                            else if ($att_details[$cdate]['AT_Hours'] >= $minWrkHHrs && $sesionAlter != 'FL') {
	                                // apply and got approve half day leave then consider 
	                                if ($sesionAlter == 'FN' || $sesionAlter == 'AN') {
	                                    // early or late mark permission taken 
	                                    if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {

	                                        $hour_incomple  += $minWrkHrs-$att_details[$cdate]['AT_Hours'];
	                                    } else {
	                                        $hour_incomple  += $HITime;
	                                    } 
	                                    $half       += 1;
	                                    $absent     += 0.5;
	                                    $absentact  += 0.5;
	                                    $halfact    += 1;
	                                    $leaveDays[] = $cdate;
	                                    $title  = "Half Day";
	                                    $cellValue  = "P2"; 
	                                    $leaveDatesWith[$cdate] = "P2";
	                                }// same as invalid consider the whole day as hourly incomplete
	                                else {
	                                    // early or late mark permission taken 
	                                    if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {

	                                        $hour_incomple  +=  $worktime-$att_details[$cdate]['AT_Hours'];
	                                    } else {
	                                        $hour_incomple  += $HITime;
	                                    }                             
	                                    $title = "Hours Incomplete";
	                                    $cellValue = "HI";
	                                }
	                            } else {
	                                $cellValue  = "L";
	                                $absent     += 1; 
	                                $absentact  += 1;
	                                $title      = "Leave";
	                                $latetime   = 0; 
	                                $earlytime  = 0;
	                                $leaveDays[] = $cdate;
	                                $leaveDatesWith[$cdate] = "L";
	                            } 
	                        } // marked in and out if
	                    }// marked section else
	                } else {
	                    $rh_takendays = (!empty($AttRptObj->rh_days)) ? array_flip($AttRptObj->rh_days[$usrid]) : array(); 
	                    // check the user apply rh leave other wise added and delete the leave list. 
	                    //Sub: set all weekend off, holyday, rh day into one array
	                    // Check the date not in the attendance and available in the Rh 
	                    if (empty($m_Hol)) {

	                        $m_Hol_2 = array_merge((array)$Holidays[$stid]['dates'],(array)$Holidays[0]['dates']);                                   
	                        $m_Hol_3 = array_merge((array)$WeekendOffs[$dpid],(array)$rh_takendays);                                        
	                        //$m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);
	                        $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2,(array)$deptHoliday); //10-09-2025 bilin
	                        sort($m_Hol);
	                    } 
	                    //CHECK THE GRID DATE IN THE ATTENDANCE AND HOLY DAY LIST
	                    $AttObj->chkadjDays($att_user_days,$m_Hol,$cdate);
	                    //insert the rh leave log and delete from the leave request
	                    if((!in_array($cdate, $WeekendOffs[$dpid])) 
	                        && (!in_array($cdate, $Holidays[0]['dates'])) 
	                        && (!in_array($cdate, $Holidays[$stid]['dates'])  
                        	&& (!in_array($cdate, $deptHoliday))   
	                        && (!array_search($cdate, $approvedLeaveDates)) 
	                        && (!in_array($cdate, $AttObj->tmp_hol))         
	                        && (array_search($cdate,$rh_holiday)))) {
	                        if ($AttObj->checkRHDate($rh_holiday,$cdate,$usrid) == "RH_Apply") {
	                            $LeaveObj->deleteLeaveRecords($usrid, $cdate);
	                        }
	                    }
	                    // check the date is in off day or holiday
	                    if ((in_array($cdate, $WeekendOffs[$dpid]) || in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates']) || in_array($cdate, $deptHoliday) ) && (!in_array($cdate, $AttObj->tmp_hol) && !array_search($cdate, $approvedLeaveDates) && !in_array($cdate,$rh_takendays))) {
	                        $holiday += 1;
	                        // chcek the date is weekend /sunday
	                        if (in_array($cdate, $WeekendOffs[$dpid])) { 
	                            $day = strtolower(date("l",strtotime($cdate)));
	                            if ($day=='sunday') {
	                                $cellValue ="S";
	                                if($cdate < $rw['US_DOJ']){
	                                    $absentact  += 1;
	                                }
	                                $title     = $day; 
	                                // $absentact -= 1;
	                            } else {
	                                $cellValue ="W";
	                                $title     = "Week End";
	                            }    
	                            $notIncrement = true;
	                        } else if (in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates'])) {
	                            // find the date in holiday
	                            $Ntitleindx = array_search($cdate, $Holidays[0]['dates']);
	                            $Stitleindx = array_search($cdate, $Holidays[$stid]['dates']); 
	                            if ($Ntitleindx !== FALSE) {
	                                $titleindx  = $Ntitleindx;
	                                $indx   = 0;
	                            } else { 
	                                $titleindx  = $Stitleindx; 
	                                $indx       = $stid;
	                            }
	                            $title      = $Holidays[$indx]['title'][$titleindx];
	                            $cellValue  = "H";   // here is cup
	                        } else if (in_array($cdate, $deptHoliday)) { //10-09-2025 bilin
	                            // find the date in holiday
	                            $Ntitleindx = array_search($cdate, $AttObj->Dept_Holidays[$dpid][0]['dates']);
                            	$Stitleindx = array_search($cdate, $AttObj->Dept_Holidays[$dpid][$stid]['dates']); 
	                            if ($Ntitleindx !== FALSE) {
	                                $titleindx  = $Ntitleindx;
	                                $indx   = 0;
	                            } else { 
	                                $titleindx  = $Stitleindx; 
	                                $indx       = $stid;
	                            }
	                            $title      = $AttObj->Dept_Holidays[$dpid][$indx]['title'][$titleindx];
	                            $cellValue  = "H";   // here is cup
	                        }
	                        $offTypeValue[$j] = ucfirst($title);
	                    } else {
	                         // check the sanwitch deptbased 
	                        if (in_array($cdate, $AttObj->tmp_hol) && $dpid==66) { // 17-09-2025
	                                        $cellValue  = "L";  // count from here
	                                        $absent     += 1; 
	                                        $absentact  += 1;
	                                        $title      = "Leave";
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                        }else if (in_array($cdate,$rh_takendays)) {//check the user take rh
	                            $holiday        += 1; 
	                            $title          ="Restrited Holiday";
	                            $cellValue      ="H";
	                            // $holidayList[] = $cdate;   // 30052025 by Achu
	                        } else if (($key_L = array_search($cdate, $approvedLeaveDates))) {
	                            // leave applied/approved
	                            $title      = $AttRptObj->approve_leaves[$usrid]['list'][$key_L]['LT_Name'];
	                            $cellValue  = $AttRptObj->approve_leaves[$usrid]['list'][$key_L]['LT_Id'];
	                            $session    = $AttRptObj->approve_leaves[$usrid]['list'][$key_L]['LRD_Session'];
	                            if ($att_details[$cdate]['AT_Hours'] >= $minWrkHHrs && $session != 'FL')
	                            { // half day leave
	                                if (in_array($cdate, $att_user_days)){    
	                                    $leaveDays[] = $cdate;   // 30052025 by Achu
	                                    if ($sesionAlter == 'FN' || $sesionAlter == 'AN') {
	                                        // early or late mark permission taken 
	                                        // if($usrid=="3684" && $cdate=="2025-06-16"){
	                                        //     echo $strtimeIn."<br/>".$actInTime."<br/>".$strtimeOut."<br/>".$foreNoonSignout."<br/>".$strtimeOut."<br/>".$actOutTime."<br/>".$strtimeIn."<br/>".$afterNoonSignin."<br/>";die;
	                                        // }
	                                        if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {
	                                            $hour_incomple  += $minWrkHrs-$att_details[$cdate]['AT_Hours'];
	                                        } else {
	                                            $hour_incomple  += $HITime;
	                                        } 
	                                        $half       += 1;
	                                        $absent     += 0.5;
	                                        $absentact  += 0.5;
	                                        $halfact    += 1;
	                                        $leaveDays[] = $cdate;
	                                        $title  = "Half Day";
	                                        $cellValue  = "P2";
	                                        $leaveDatesWith[$cdate] = "P2";
	                                    }// same as invalid consider the whole day as hourly incomplete
	                                    else {
	                                        // early or late mark permission taken 
	                                        if ( ($strtimeIn <= $actInTime && $strtimeOut >= $foreNoonSignout ) || ($strtimeOut >= $actOutTime && $strtimeIn <= $afterNoonSignin ) )  {

	                                            $hour_incomple  +=  $worktime-$att_details[$cdate]['AT_Hours'];
	                                        } else {
	                                            $hour_incomple  += $HITime;
	                                        }                             
	                                        $title = "Hours Incomplete";
	                                        $cellValue = "HI";
	                                    }
	                                } else {
	                                    $cellValue  = "L";
	                                    $absent     += 1; 
	                                    $absentact  += 1;
	                                    $title      = "Leave";
	                                    $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                    $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                } 
	                            } else { // Full day leave
	                                $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                if ($att_details[$cdate]['new_cal'] == 1 && $new_min_half < $hrs) {
	                                    // new calcualtaion no needed becus the user apply leave
	                                    $cellValue  = "L";
	                                    $absent     += 1; 
	                                    $absentact  += 1;
	                                    $title      = "Leave";            
	                                } else { // old - set as leave- if below half day
	                                    $cellValue  = "L";
	                                    $absent     += 1; 
	                                    $absentact  += 1;
	                                    $title      = "Leave";
	                                }
	                            }
	                        }else { // not marked - not apply leave
	                            if(in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates'])){
	                                // find the date in holiday
	                                $Ntitleindx = array_search($cdate, $Holidays[0]['dates']);
	                                $Stitleindx = array_search($cdate, $Holidays[$stid]['dates']); 
	                                if ($Ntitleindx !== FALSE) {
	                                    $titleindx  = $Ntitleindx;
	                                    $indx   = 0;
	                                } else { 
	                                    $titleindx  = $Stitleindx; 
	                                    $indx       = $stid;
	                                }
	                                $title      = $Holidays[$indx]['title'][$titleindx];
	                                if(!$holidayRules){
	                                    /*if($dpid==66){
	                                        $cellValue  = "L";   // here is cup
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                        $absentact  += 1;
	                                    }else{*/
	                                        $cellValue  = "H";   // here is cup
	                                    //}
	                                    // $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                    /*$AttObj->deleteAttendance($usrid, $cdate);
	                                    $LeaveObj->deleteLeaveRecords($usrid, $cdate);
	                                    $AttObj->deleteRHLog($usrid, $cdate);*/
	                                    $notIncrement = true;
	                                }else{
	                                    if($holidayRules->RL_Is_LOP==1){
	                                        $cellValue  = "L";   // here is cup
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                        /*$AttObj->deleteAttendance($usrid, $cdate);
	                                        $LeaveObj->deleteLeaveRecords($usrid, $cdate);
	                                        $AttObj->deleteRHLog($usrid, $cdate);*/
	                                        $absentact  += 1;
	                                        $notIncrement = true;
	                                    }else{
	                                        $cellValue  = "H";   // here is cup
	                                    }
	                                }
	                            }else if(in_array($cdate, $deptHoliday)){ // 10-09-2025   
	                                // find the date in holiday
	                                $Ntitleindx = array_search($cdate, $AttObj->Dept_Holidays[$dpid][0]['dates']);
                                	$Stitleindx = array_search($cdate, $AttObj->Dept_Holidays[$dpid][$stid]['dates']);  
	                                if ($Ntitleindx !== FALSE) {
	                                    $titleindx  = $Ntitleindx;
	                                    $indx   = 0;
	                                } else { 
	                                    $titleindx  = $Stitleindx; 
	                                    $indx       = $stid;
	                                }
	                                $title      = $AttObj->Dept_Holidays[$dpid][$indx]['title'][$titleindx];
	                                if(!$holidayRules){
	                                    /*if($dpid==66){
	                                        $cellValue  = "L";   // here is cup
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                        $absentact  += 1;
	                                    }else{*/
	                                        $cellValue  = "H";   // here is cup
	                                    //}
	                                    // $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                    /*$AttObj->deleteAttendance($usrid, $cdate);
	                                    $LeaveObj->deleteLeaveRecords($usrid, $cdate);
	                                    $AttObj->deleteRHLog($usrid, $cdate);*/
	                                    $notIncrement = true;
	                                }else{
	                                    if($holidayRules->RL_Is_LOP==1){
	                                        $cellValue  = "L";   // here is cup
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                        /*$AttObj->deleteAttendance($usrid, $cdate);
	                                        $LeaveObj->deleteLeaveRecords($usrid, $cdate);
	                                        $AttObj->deleteRHLog($usrid, $cdate);*/
	                                        $absentact  += 1;
	                                        $notIncrement = true;
	                                    }else{
	                                        $cellValue  = "H";   // here is cup
	                                    }
	                                }
	                            }else{
	                                if($dpid==66){
	                                    $leaveObj = false;
	                                    $prevWorkingDay = $AttObj->findPrevWrkingDay($att_user_days,$m_Hol,$cdate);
	                                    $nextWorkingDay = $AttObj->findNxtWrkingDay($att_user_days,$m_Hol,$cdate);
	                                    if(strtolower(date("l",strtotime($cdate)))=="saturday"){
	                                        $cellValue = "W";
	                                        if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
	                                            $leaveObj = true;
	                                        }
	                                    }elseif(strtolower(date("l",strtotime($cdate)))=="sunday"){
	                                        $cellValue = "S";
	                                        if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
	                                            $leaveObj = true;
	                                        }
	                                    }else{
	                                        $leaveObj = true;
	                                    }
	                                    if($leaveObj==true){
	                                        $cellValue  = "L";  // count from here
	                                        $absent     += 1; 
	                                        $absentact  += 1;
	                                        $title      = "Leave";
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                    }
	                                }else{
	                                    if(strtolower(date("l",strtotime($cdate)))!="sunday"){
	                                        $cellValue  = "L";  // count from here
	                                        $absent     += 1; 
	                                        $absentact  += 1;
	                                        $title      = "Leave";
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                    }else{
	                                        if (in_array($cdate, $AttObj->tmp_hol)) {
	                                            if($weekendRules && $weekendRules->RL_Is_LOP==1){
	                                                $cellValue  = "L";  // count from here
	                                                $absentact  += 1;
	                                            }else{
	                                                if($cdate < $rw['US_DOJ']){
	                                                    $cellValue  = "L";  // count from here
	                                                    $absentact  += 1;
	                                                }else{
	                                                    $cellValue  = "S";  // count from here
	                                                }
	                                            }
	                                        }else{
	                                            if($cdate < $rw['US_DOJ']){
	                                                $cellValue  = "L";  // count from here
	                                                $absentact  += 1;
	                                            }else{
	                                                $cellValue  = "S";  // count from here
	                                            }
	                                        }

	                                    }
	                                }
	                            }
	                        }         
	                    }
	                }
	            }else{
		            if (in_array($cdate, $att_user_days) && (!array_search($cdate, $approvedLeaveDates)) && $hrs > 0) {

		                unset($AttObj->tmp_hol); 
		                $AttObj->tmp_hol = array();

		                if ( $hrs >= $minActHrs ) { // full day attendance time completed.
		                	$cellValue  = 'P';
		                    $full       += 1;
		                    $fullact    += 1;
		                    $extratime  += ($worktime < $hrs) ? ($hrs-$worktime): 0;
		                } else if ( $hrs >= $minWrkHrs ) { // half day
		                	$cellValue  = "P2";
		                    // not apply leave (salary not cutting leave not apply)
		                    if ($att_details[$cdate]['new_cal'] == 1) { // new calculation
		                         
		                         if ($hrs >= ($minWrkHrs+$new_min_full)) { //half +half/2  
		                          	// calculate the time and added into HI
		                            $cellValue  = "HI"; 
	                            	// calculate the time and added into HI
	                            	$hour_incomple +=  $minActHrs-$hrs; 
		                            $half       += 1;
		                            $absent     += 0.5;
		                            $fullact    += 1;
		                         } else {
		                            $half       += 1;
		                            $absent     += 0.5;
		                            $absentact  += 0.5;
		                            $halfact    += 1;
		                            if(strtolower(date('D', strtotime($cdate))) != 'sun'){
	                                    $leaveDays[] = $cdate;       // 30052025 by Achu     
	                                    $leaveDatesWith[$cdate] = "P2"; // 11062025 by Achu
	                                }
		                         }
		                    } else { // old 
		                        $half       += 1;
		                        $absent     += 0.5;
		                        $absentact  += 0.5;
		                        $halfact    += 1;
		                        $leaveDays[] = $cdate;       // 30052025 by Achu
	                        	$leaveDatesWith[$cdate] = "P2"; // 11062025 by Achu     
		                    }
		                } else { 
		                    if ($att_details[$cdate]['new_cal'] == 1 && $new_min_half < $hrs) {
		                        // new calculation below 3 hours set as leave other wise hour incomplete + half day
		                        $cellValue      = "2HI";
		                        $halfact        += 1;
		                        $absent         += 1;
		                        $absentact      += 0.5;
		                        $hour_incomple  +=  $minWrkHrs-$hrs;
		                        $leaveDays[]    = $cdate;       // 30052025 by Achu
	                        	$leaveDatesWith[$cdate] = "P2"; // 11062025 by Achu
		                    } else { // old - set as leave- if below half day
		                        $cellValue  = "L";
		                        $absent     += 1; 
		                        $title      = "Leave";
		                        $absentact  += 1;
		                        $leaveDays[] = $cdate;      // 30052025 by Achu
		                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
		                    }
		                }   
		            } else {
		                $rh_takendays       = (!empty($AttRptObj->rh_days)) ? array_flip($AttRptObj->rh_days[$usrid]) : array(); 
		                // check the user apply rh leave other wise added and delete the leave list. 
		                //Sub: set all weekend off, holyday, rh day into one array
		                // Check the date not in the attendance and available in the Rh 
		                if (empty($m_Hol)) {

		                    $m_Hol_2 = array_merge((array)$Holidays[$stid]['dates'],(array)$Holidays[0]['dates']);                                   
		                    $m_Hol_3 = array_merge((array)$WeekendOffs[$dpid],(array)$rh_takendays);                                        
		                    //$m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);
                        	$m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2,(array)$deptHoliday); //10-09-2025 bilin
		                    sort($m_Hol);
		                } 
		                //CHECK THE GRID DATE IN THE ATTENDANCE AND HOLY DAY LIST
		                $AttObj->chkadjDays($att_user_days,$m_Hol,$cdate);
		                // check the date is in off day or holiday
		                if ((in_array($cdate, $WeekendOffs[$dpid]) || in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates']) || in_array($cdate, $deptHoliday)) && (!in_array($cdate, $AttObj->tmp_hol) && !array_search($cdate, $approvedLeaveDates) && !in_array($cdate,$rh_takendays))) {
	                    	$holiday += 1;
	                    	if (in_array($cdate, $WeekendOffs[$dpid])) { 
		                        $day = strtolower(date("l",strtotime($cdate)));
		                        if ($day=='sunday') {
		                            $cellValue ="S";
		                            if($cdate < $rw['US_DOJ']){
		                                $absentact  += 1;
		                            }
		                            $title     = $day; 
		                            // $absentact -= 1;
		                        } else {
		                            $cellValue ="W";
		                            // $title     = "Week End";
		                        }    
		                        // $notIncrement = true;
		                    } else if (in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates'])) {
		                        // find the date in holiday
		                        $Ntitleindx = array_search($cdate, $Holidays[0]['dates']);
		                        $Stitleindx = array_search($cdate, $Holidays[$stid]['dates']); 
		                        if ($Ntitleindx !== FALSE) {
		                            $titleindx  = $Ntitleindx;
		                            $indx   = 0;
		                        } else { 
		                            $titleindx  = $Stitleindx; 
		                            $indx       = $stid;
		                        }
		                        $title      = $Holidays[$indx]['title'][$titleindx];
		                        $cellValue  = "H";   // here is cup
		                        //$holidayList[] = $cdate;   // 30052025 by Achu
		                    } else if (in_array($cdate, $deptHoliday)) { //10-09-2025 bilin
		                        // find the date in holiday
		                        $Ntitleindx = array_search($cdate, $AttObj->Dept_Holidays[$dpid][0]['dates']);
                            	$Stitleindx = array_search($cdate, $AttObj->Dept_Holidays[$dpid][$stid]['dates']); 
		                        if ($Ntitleindx !== FALSE) {
		                            $titleindx  = $Ntitleindx;
		                            $indx   = 0;
		                        } else { 
		                            $titleindx  = $Stitleindx; 
		                            $indx       = $stid;
		                        }
		                        $title      = $AttObj->Dept_Holidays[$dpid][$indx]['title'][$titleindx];
		                        $cellValue  = "H";   // here is cup
		                        //$holidayList[] = $cdate;   // 30052025 by Achu
		                    }
		                } else {
		                    // check the sanwitch deptbased 
	                        if (in_array($cdate, $AttObj->tmp_hol) && $dpid==66) { // 17-09-2025
	                                        $cellValue  = "L";  // count from here
	                                        $absent     += 1; 
	                                        $absentact  += 1;
	                                        $title      = "Leave";
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                        }else if (in_array($cdate,$rh_takendays)) {//check the user take rh
		                        $holiday        += 1; 
		                        $cellValue      ="H";
		                    } else if (($key_L = array_search($cdate, $approvedLeaveDates))) {
		                        // leave applied/approved
		                        $title      = $AttRptObj->approve_leaves[$usrid]['list'][$key_L]['LT_Name'];
		                        $cellValue  = $AttRptObj->approve_leaves[$usrid]['list'][$key_L]['LT_Id'];
		                        $session    = $AttRptObj->approve_leaves[$usrid]['list'][$key_L]['LRD_Session'];
		                        if ($session != 'FL') { // half day leave
		                            
		                            if (in_array($cdate, $att_user_days)){
		                            	$leaveDays[] = $cdate;   // 30052025 by Achu
		                                if ($att_details[$cdate]['new_cal'] == 1) { // new alculation
		                                    if( $new_min_half < $hrs && $hrs < $minWrkHrs) {
		                                    //if ($hrs < $minWrkHrs) {
		                                        $half           += 1;
		                                        $halfact        += 1;
		                                        $absent         += 0.5;
		                                        $absentact      += 0.5; 
		                                        $cellValue      = "2HI";
		                                        $hour_incomple +=  $minWrkHrs-$hrs;
		                                    } else {
		                                        $cellValue  = "L";
		                                        $absent     += 1; 
		                                        $absentact  += 1;
		                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
		                                    }
		                                } else {
		                                    $half       += 1;
		                                    $halfact    += 1;
		                                    $absent     += 0.5; 
		                                    $absentact  += 0.5; 
		                                    $cellValue  = "P2"; 
		                                    $leaveDatesWith[$cdate] = "P2"; // 11062025 by Achu
		                                }
		                            } else {
		                                $cellValue  = "L";
		                                $absent     += 1; 
		                                $absentact  += 1;
		                                $leaveDays[]   = $cdate;    // 30052025 by Achu
		                                $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
		                            } 
		                        } else { // Full day leave
		                        	$leaveDays[]   = $cdate;    // 30052025 by Achu
	                            	$leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
		                            if ($att_details[$cdate]['new_cal'] == 1 && $new_min_half < $hrs) {
		                                // new calcualtaion no needed becus the user apply leave
		                                $cellValue  = "L";
		                                $absent     += 1; 
		                                $absentact  += 1;
		                            } else { // old - set as leave- if below half day
		                                $cellValue  = "L";
		                                $absent     += 1; 
		                                $absentact  += 1;
		                            }
		                        }
		                    }else { // not marked - not apply leave
		                        if(in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates'])){
	                            	// find the date in holiday
		                            $Ntitleindx = array_search($cdate, $Holidays[0]['dates']);
		                            $Stitleindx = array_search($cdate, $Holidays[$stid]['dates']); 
		                            if ($Ntitleindx !== FALSE) {
		                                $titleindx  = $Ntitleindx;
		                                $indx   = 0;
		                            } else { 
		                                $titleindx  = $Stitleindx; 
		                                $indx       = $stid;
		                            }
		                            $title      = $Holidays[$indx]['title'][$titleindx];
		                            if(!$holidayRules){
		                                // $cellValue  = "H";   // here is cup
		                                $cellValue  = "L";   // here is cup
		                                $leaveDays[]   = $cdate;    // 30052025 by Achu
		                                // $notIncrement = true;
		                            }else{
		                                if($holidayRules->RL_Is_LOP==1){
		                                    $cellValue  = "L";   // here is cup
		                                    $leaveDays[]   = $cdate;    // 30052025 by Achu
		                                    $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
		                                    $absentact  += 1;
		                                    // $notIncrement = true;
		                                }else{
		                                    $cellValue  = "H";   // here is cup
		                                }
		                            }
		                        }else if(in_array($cdate, $deptHoliday)){ // 10-09-2025
	                            	// find the date in holiday
		                            $Ntitleindx = array_search($cdate, $AttObj->Dept_Holidays[$dpid][0]['dates']);
                                	$Stitleindx = array_search($cdate, $AttObj->Dept_Holidays[$dpid][$stid]['dates']);
		                            if ($Ntitleindx !== FALSE) {
		                                $titleindx  = $Ntitleindx;
		                                $indx   = 0;
		                            } else { 
		                                $titleindx  = $Stitleindx; 
		                                $indx       = $stid;
		                            }
		                            $title      = $AttObj->Dept_Holidays[$dpid][$indx]['title'][$titleindx];
		                            if(!$holidayRules){
		                                // $cellValue  = "H";   // here is cup
		                                $cellValue  = "L";   // here is cup
		                                $leaveDays[]   = $cdate;    // 30052025 by Achu
		                                // $notIncrement = true;
		                            }else{
		                                if($holidayRules->RL_Is_LOP==1){
		                                    $cellValue  = "L";   // here is cup
		                                    $leaveDays[]   = $cdate;    // 30052025 by Achu
		                                    $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
		                                    $absentact  += 1;
		                                    // $notIncrement = true;
		                                }else{
		                                    $cellValue  = "H";   // here is cup
		                                }
		                            }
		                        }else{
		                            if($dpid==66){
	                                    $leaveObj = false;
	                                    $prevWorkingDay = $AttObj->findPrevWrkingDay($att_user_days,$m_Hol,$cdate);
	                                    $nextWorkingDay = $AttObj->findNxtWrkingDay($att_user_days,$m_Hol,$cdate);
	                                    if(strtolower(date("l",strtotime($cdate)))=="saturday"){
	                                        $cellValue = "W";
	                                        if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
	                                            $leaveObj = true;
	                                        }
	                                    }elseif(strtolower(date("l",strtotime($cdate)))=="sunday"){
	                                        $cellValue = "S";
	                                        if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
	                                            $leaveObj = true;
	                                        }
	                                    }else{
	                                        $leaveObj = true;
	                                    }
	                                    if($leaveObj==true){
	                                        $cellValue  = "L";  // count from here
	                                        $absent     += 1; 
	                                        $absentact  += 1;
	                                        $title      = "Leave";
	                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
	                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
	                                    }
	                                }else{
	                                    if($dpid==66){
		                                    $leaveObj = false;
		                                    $prevWorkingDay = $AttObj->findPrevWrkingDay($att_user_days,$m_Hol,$cdate);
		                                    $nextWorkingDay = $AttObj->findNxtWrkingDay($att_user_days,$m_Hol,$cdate);
		                                    if(strtolower(date("l",strtotime($cdate)))=="saturday"){
		                                        $cellValue = "W";
		                                        if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
		                                            $leaveObj = true;
		                                        }
		                                    }elseif(strtolower(date("l",strtotime($cdate)))=="sunday"){
		                                        $cellValue = "S";
		                                        if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
		                                            $leaveObj = true;
		                                        }
		                                    }else{
		                                        $leaveObj = true;
		                                    }
		                                    if($leaveObj==true){
		                                        $cellValue  = "L";  // count from here
		                                        $absent     += 1; 
		                                        $absentact  += 1;
		                                        $title      = "Leave";
		                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
		                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
		                                    }
		                                }else{
		                                    if(strtolower(date("l",strtotime($cdate)))!="sunday"){
		                                        $cellValue  = "L";  // count from here
		                                        $absent     += 1; 
		                                        $absentact  += 1;
		                                        $title      = "Leave";
		                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
		                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
		                                    }else{
		                                        // if($usrid=="3685" && $cdate=="2025-07-06"){
		                                        //     echo "<pre>";print_r($AttObj->tmp_hol);die;
		                                        // }
		                                        if (in_array($cdate, $AttObj->tmp_hol)) {
		                                            if($weekendRules && $weekendRules->RL_Is_LOP==1){
		                                                $cellValue  = "L";  // count from here
		                                                $absentact  += 1;
		                                            }else{
		                                                if($cdate < $rw['US_DOJ']){
		                                                    $cellValue  = "L";  // count from here
		                                                    $absentact  += 1;
		                                                }else{
		                                                    $cellValue  = "S";  // count from here
		                                                }
		                                            }
		                                        }else{
		                                            if($cdate < $rw['US_DOJ']){
		                                                $cellValue  = "L";  // count from here
		                                                $absentact  += 1;
		                                            }else{
		                                                $cellValue  = "S";  // count from here
		                                            }
		                                        }

		                                    }
		                                }
	                                }
		                        }
		                    }         
		                }
		            }
		        }
	            $day = strtolower(date("l",strtotime($cdate)));
	            if(in_array($dpid, $DeptArray)){
	                $dep = false;
	            }
	            if(in_array($ofcid, $OfficeArray)){
	                $ofc = true;
	            }
	            // echo $usrid." => ".$cdate."<br/>";
	            // echo "just abive the sunday flag => ".$usrid;die;
	            // if($usrid=='3264' && $cdate=="2025-04-25"){
	            // 	echo $day."=>".$cellValue;die;
	            // }
	            // echo "not coming to condition => ".$day."=>".$cellValue."=>".$cdate;die;
	            if ($day=='sunday' && $dep==true && $ofc==true) {
	            	// if($usrid=="3744" && $cdate=="2025-05-25"){
		            // 	echo "date and cell value1111111 => ".$dep." => ".$ofc."=>".$day."<br/>";
		            // }
	                $sundayCellValue = $cellValue;
	                $previousMonthFirstWeekCheck = $isFirstSunday = false;
	                $previousMonthFirstWeekFriSat = [];
	                $checkSandwhich=false;
	                $dayNames = $dayNamesForCompensatory = $dayDatesForCompensatory = $otherDayDatesForCompensatory = [];
	                $leaveDays = $AttObj->excludeDatesBeforeJoiningDate($leaveDays, $rw['US_DOJ']);
	                if(!empty($RulesList)){
	                    if(!empty($leaveDays)){
	                        foreach ($leaveDays as $dateObj) {
	                            $dayOfWeek = date("w",strtotime($dateObj));
	                            $dayNames[] = strtolower(date('D', strtotime($dateObj)));
	                            $dayNamesForCompensatory[] = strtolower(date('D', strtotime($dateObj)));
	                            if(strtolower(date('D', strtotime($dateObj))) == 'fri' || strtolower(date('D', strtotime($dateObj))) == 'sat'){
	                                $dayDatesForCompensatory[strtolower(date('D', strtotime($dateObj)))] = $leaveDatesWith[$dateObj];
	                            }else{
	                                if(strtolower(date('D', strtotime($dateObj))) != 'sun'){
	                                    $otherDayDatesForCompensatory[strtolower(date('D', strtotime($dateObj)))] = $leaveDatesWith[$dateObj];
	                                }
	                            }
	                            if ($dayOfWeek != 0) {
	                                $hasOtherDays = true;
	                            }
	                        }
	                        if ($hasOtherDays) {
	                            $givenDate = new DateTime($cdate);
	                            $firstDayOfMonth = (clone $givenDate)->modify('first day of this month');
	                            $firstSunday = (clone $firstDayOfMonth);
	                            if ($firstSunday->format('w') != 0) {
	                                $firstSunday->modify('next sunday');
	                            }
	                            if ($cdate === $firstSunday->format('Y-m-d')) {
	                                $isFirstSunday = true;
	                                $previousMonthFirstWeekCheck = true;
	                            }else{
	                                $checkOtherDayRuleFile = true;
	                            }
	                        }
	                    }else{
	                        $givenDate = new DateTime($cdate);
	                        $firstDayOfMonth = (clone $givenDate)->modify('first day of this month');
	                        $firstSunday = (clone $firstDayOfMonth);
	                        if ($firstSunday->format('w') != 0) {
	                            $firstSunday->modify('next sunday');
	                        }
	                        if ($cdate === $firstSunday->format('Y-m-d')) {
	                            $isFirstSunday = true;
	                            $previousMonthFirstWeekCheck = true;
	                        }
	                    }
	                    if($previousMonthFirstWeekCheck == true){
	                        $givenDate = new DateTime($cdate);

	                        // Find the Monday of this week
	                        $monday = (clone $givenDate)->modify('monday this week');

	                        // Find the Saturday of this week
	                        $saturday = (clone $givenDate)->modify('saturday this week');

	                        // Format for your function
	                        $startDay = $monday->format('Y-m-d');
	                        $endDay   = $saturday->format('Y-m-d');

	                        $betweenDates = $AttObj->getBetweenDatesAfterDOJ($startDay, $endDay, $rw['US_DOJ']);
	                        if(!empty($betweenDates)){
	                        	$betweenDateSingle  = $betweenDates[0]; // 2026-01-31

								$pyear  = (int) date('Y', strtotime($betweenDateSingle));
								$pmonth = (int) date('m', strtotime($betweenDateSingle));
	                            $AttObj->getHolidays($betweenDateSingle, $preTally_user_ofid);
	                            $AttObj->getDeptHolidays($betweenDateSingle,$preTally_user_ofid,0);
								$AttRptObj->getTakenRHBatches($pyear);
	                            $AttObj->getWeekendOffs($pmonth,$pyear,$preTally_user_ofid); // Weekend Off List
	                            $rh_takendays       = (!empty($AttRptObj->rh_days)) ? array_flip($AttRptObj->rh_days[$usrid]) : array();    
	                            $prevHolidays    = $AttObj->Holidays ?? [];
	                            $pWeekendOffs    = $AttObj->WeekOffs ?? []; 
	                            $prevDeptHoliday = $AttObj->Dept_Holidays[$dpid]
	                            ? array_merge(
	                                (array)($AttObj->Dept_Holidays[$dpid][0]['dates'] ?? []),
	                                (array)($AttObj->Dept_Holidays[$dpid][$stid]['dates'] ?? [])
	                            )
	                            : [];
	                            $pm_Hol_2 = array_merge(
	                                (array)($prevHolidays[$stid]['dates'] ?? []),
	                                (array)($prevHolidays[0]['dates'] ?? [])
	                            );                          
	                            $pm_Hol_3 = array_merge(
	                                (array)($pWeekendOffs[$dpid] ?? []),
	                                (array)$rh_takendays
	                            );                                        
	                            //$m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);
	                            $pm_Hol = array_merge($pm_Hol_3, $pm_Hol_2, (array)$prevDeptHoliday);
	                            sort($pm_Hol);
	                        }
	                        if(!empty($pm_Hol)){
	                            $betweenDates = array_diff($betweenDates, $pm_Hol);
	                        }
	                        if (!empty($m_Hol)) {
	                            $betweenDates = array_diff($betweenDates, $m_Hol);
	                        }
	                        if(!empty($betweenDates)){
	                            $dayNames = [];
	                            foreach($betweenDates as $betDate){
	                                $leaveDatesMonthYear = new DateTime($betDate);
	                                $getAttendance = $AttObj->checkAttendanceByDate($usrid, $betDate);
	                                if(!$getAttendance){
	                                    $dayNames[] = strtolower(date('D', strtotime($betDate)));
	                                    $dayNamesForCompensatory[] = strtolower(date('D', strtotime($betDate)));
	                                    $leaveDatesWith[$betDate] = "L";
	                                    if(strtolower(date('D', strtotime($betDate))) == 'fri' || strtolower(date('D', strtotime($betDate))) == 'sat'){
	                                        $dayDatesForCompensatory[strtolower(date('D', strtotime($betDate)))] = $leaveDatesWith[$betDate];
	                                        $previousMonthFirstWeekFriSat[strtolower(date('D', strtotime($betDate)))] = 'L';
	                                        if($firstSunday->format('Y-m') !== $leaveDatesMonthYear->format('Y-m')){
	                                            $otherMonthFridaySaturday[] = $betDate;
	                                        }
	                                    }else{
	                                        if(strtolower(date('D', strtotime($betDate))) != 'sun'){
	                                            $otherDayDatesForCompensatory[strtolower(date('D', strtotime($betDate)))] = $leaveDatesWith[$betDate];
	                                        }
	                                    }
	                                }else{
	                                    $getAttendanceDetail = $AttObj->checkAttendanceByDateRow($usrid, $betDate);
	                                    $hrs = ($getAttendanceDetail) ? $getAttendanceDetail['AT_Hours'] : 0;
	                                    $gracetime = isset($att_details[$betDate]['grase_time'])?$att_details[$betDate]['grase_time']:0;
	                                    if($getAttendanceDetail['AT_AllotTime']) {            
	                                        $assignedTime   = json_decode($getAttendanceDetail['AT_AllotTime']);
	                                        $worktime       = $assignedTime->whour; 
	                                    }
	                                    $minActHrs      = ($worktime-$gracetime);
	                                    $minWrkHrs      = ($minActHrs)/2;
	                                    if ( $hrs >= $minActHrs ) {  // Full day (p)
	                                        // Nothing to do
	                                    }else if ( $hrs >= ($minWrkHrs+$new_min_full) ){ // Full day with (HI)
	                                        // Nothing to do
	                                    }else if ( $hrs >= $minWrkHrs ){ // Hafl day (P2)
	                                        $dayNames[] = strtolower(date('D', strtotime($betDate)));
	                                        $dayNamesForCompensatory[] = strtolower(date('D', strtotime($betDate)));
	                                        $leaveDatesWith[$betDate] = "P2";
	                                        $previousMonthFirstWeekFriSat[strtolower(date('D', strtotime($betDate)))] = 'P2';
	                                        if(strtolower(date('D', strtotime($betDate))) == 'fri' || strtolower(date('D', strtotime($betDate))) == 'sat'){
	                                            $dayDatesForCompensatory[strtolower(date('D', strtotime($betDate)))] = $leaveDatesWith[$betDate];
	                                            if($firstSunday->format('Y-m') !== $leaveDatesMonthYear->format('Y-m')){
	                                                $otherMonthFridaySaturday[] = $betDate;
	                                            }
	                                        }else{
	                                            if(strtolower(date('D', strtotime($betDate))) != 'sun'){
	                                                $otherDayDatesForCompensatory[strtolower(date('D', strtotime($betDate)))] = $leaveDatesWith[$betDate];
	                                            }
	                                        }
	                                    }else if($new_min_half < $hrs){ // Below P2 with HI (2HI)
	                                        $dayNames[] = strtolower(date('D', strtotime($betDate)));
	                                        $dayNamesForCompensatory[] = strtolower(date('D', strtotime($betDate)));
	                                        $leaveDatesWith[$betDate] = "2HI";
	                                        $previousMonthFirstWeekFriSat[strtolower(date('D', strtotime($betDate)))] = '2HI';
	                                        if(strtolower(date('D', strtotime($betDate))) == 'fri' || strtolower(date('D', strtotime($betDate))) == 'sat'){
	                                            $dayDatesForCompensatory[strtolower(date('D', strtotime($betDate)))] = $leaveDatesWith[$betDate];
	                                            if($firstSunday->format('Y-m') !== $leaveDatesMonthYear->format('Y-m')){
	                                                $otherMonthFridaySaturday[] = $betDate;
	                                            }
	                                        }else{
	                                            if(strtolower(date('D', strtotime($betDate))) != 'sun'){
	                                                $otherDayDatesForCompensatory[strtolower(date('D', strtotime($betDate)))] = $leaveDatesWith[$betDate];
	                                            }
	                                        }
	                                    }else{ // Invalid
	                                        $dayNames[] = strtolower(date('D', strtotime($betDate)));
	                                        $dayNamesForCompensatory[] = strtolower(date('D', strtotime($betDate)));
	                                        $leaveDatesWith[$betDate] = "L";
	                                        $previousMonthFirstWeekFriSat[strtolower(date('D', strtotime($betDate)))] = 'L';
	                                        if(strtolower(date('D', strtotime($betDate))) == 'fri' || strtolower(date('D', strtotime($betDate))) == 'sat'){
	                                            $dayDatesForCompensatory[strtolower(date('D', strtotime($betDate)))] = $leaveDatesWith[$betDate];
	                                            if($firstSunday->format('Y-m') !== $leaveDatesMonthYear->format('Y-m')){
	                                                $otherMonthFridaySaturday[] = $betDate;
	                                            }
	                                        }else{
	                                            if(strtolower(date('D', strtotime($betDate))) != 'sun'){
	                                                $otherDayDatesForCompensatory[strtolower(date('D', strtotime($betDate)))] = $leaveDatesWith[$betDate];
	                                            }
	                                        }
	                                    }
	                                }
	                            }
	                        }
	                        if(!empty($dayNames)){
	                            $checkOtherDayRuleFile = true;
	                        }
	                    }
	                    if($checkOtherDayRuleFile==true){

	                        if(!empty($otherRules)){
	                            foreach($otherRules as $oRule){
	                                $commonItemsArray = [];
	                                $commonItems = false;
	                                $otherRuleDays = $AttObj->getDaysBetween($oRule->RL_Start_Day, $oRule->RL_End_Day);
	                                for($i=0; $i<count($otherRuleDays); $i++) {
	                                    if($oRule->RL_Leave_Duration=="single"){
	                                        if(in_array($otherRuleDays[$i], $dayNames)){
	                                            $commonItems = true;
	                                            break 2;
	                                        }
	                                    }else{
	                                        if(in_array($otherRuleDays[$i], $dayNames)){
	                                            $commonItemsArray[] = true;
	                                        }
	                                        if(count($otherRuleDays)==count($commonItemsArray)){
	                                            $commonItems = true;
	                                            break 2;
	                                        }
	                                    }
	                                }
	                            }
	                            /* Old code for before making no leave on sunday if p2 for both friday and saturday */
	                            /*if($commonItems==true){
	                                if(!$AttObj->checkAttendanceByDate($usrid, $cdate)){
	                                    if($sundayCellValue=="S"){
	                                        $sundayCellValue = 'L';
	                                        $absentact  += 1;
	                                    }
	                                }else{
	                                    $sundayCellValue = $cellValue;
	                                }
	                            }*/

	                            /* Updated code for no leave on sunday if p2 for both friday and saturday */
	                            if ($commonItems) {
	                                if (!$AttObj->checkAttendanceByDate($usrid, $cdate)) {

	                                    $makeLeave = false;

	                                    if ($sundayCellValue == "S" && $previousMonthFirstWeekCheck && !empty($otherDayDatesForCompensatory)) {
	                                        $makeLeave = true;
	                                    } else {
	                                        // Get previous Friday and Saturday
	                                        $friday   = date("Y-m-d", strtotime("last friday", strtotime($cdate)));
	                                        $saturday = date("Y-m-d", strtotime("last saturday", strtotime($cdate)));

	                                        // If not P2 on both days → mark leave
	                                        if (($leaveDatesWith[$friday] ?? null) !== "P2" || ($leaveDatesWith[$saturday] ?? null) !== "P2" || $otherDayDatesForCompensatory) {
	                                            $makeLeave = true;
	                                        }
	                                    }

	                                    if ($makeLeave) {
	                                        $sundayCellValue = 'L';
	                                        $absentact++;
	                                    } else {
	                                        $sundayCellValue = 'S';
	                                    }

	                                } else {
	                                    $sundayCellValue = $cellValue;
	                                }
	                            }

	                            if(!$commonItems){
	                                $checkSandwhich=true;
	                            }
	                        }
	                    }else{
	                        $checkSandwhich=true;
	                    }
	                    if($checkSandwhich==true){
	                        if (in_array($cdate, $AttObj->tmp_hol)) {
	                            $firstDayOfMonth = (clone $givenDate)->modify('first day of this month');
	                            $firstSunday = (clone $firstDayOfMonth);
	                            if ($firstSunday->format('w') != 0) {
	                                if($weekendRules){
	                                    
	                                    if($weekendRules->RL_Is_LOP==1){
	                                        $sundayCellValue = "L";
	                                    }else{
	                                        $sundayCellValue = "S";
	                                        $absentact -= 1;      
	                                    }
	                                }else{
	                                    if($cellValue=="L" && $cdate > $rw['US_DOJ']){
	                                        $absentact -= 1;  
	                                        $sundayCellValue = "S";
	                                    }
	                                }
	                            }else{
	                                if($weekendRules){
	                                    
	                                    if($weekendRules->RL_Is_LOP==1){
	                                        $sundayCellValue = "L";
	                                    }else{
	                                        $sundayCellValue = "S";
	                                        // $absentact -= 1;      
	                                    }
	                                }else{
	                                    if($cellValue=="L"){
	                                        // $absentact -= 1;  
	                                        $sundayCellValue = "S";
	                                    }
	                                }
	                            }
	                        }
	                        if($sundayCellValue!="L"){
	                            // Checking the holiday sandwich leave exists in rule table
	                            if(!empty($Holidays[0]['dates']) || !empty($Holidays[$stid]['dates']))
	                            {
	                                foreach($Holidays[0]['dates'] as $hDate) {
	                                    if(!$AttObj->checkAttendanceByDate($usrid, $hDate)){
	                                        $prevWorkingDay = $AttObj->findPrevWrkingDay($att_user_days,$m_Hol,$hDate);
	                                        $nextWorkingDay = $AttObj->findNxtWrkingDay($att_user_days,$m_Hol,$hDate);
	                                        if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
	                                            $start = new DateTime($prevWorkingDay);
	                                            $end = new DateTime($nextWorkingDay);
	                                            $end->modify('+1 day'); // Include end date in the loop

	                                            $interval = new DateInterval('P1D');
	                                            $dateRange = new DatePeriod($start, $interval, $end);
	                                            $sundaysDatas = [];
	                                            foreach ($dateRange as $date) {
	                                                if ($date->format('w') == 0) { // 0 = Sunday
	                                                    $sundaysDatas[] = $date->format('Y-m-d');
	                                                }
	                                            }
	                                            if(!empty($sundaysDatas)){
	                                                if ($holidayRules && $holidayRules->RL_Is_LOP==1) {
	                                                    if($cdate==$sundaysDatas[0]){
	                                                        $sundayCellValue = "L";
	                                                        // $notIncrement = true;
	                                                        break;
	                                                    }
	                                                }else{
	                                                    $sundayCellValue = "S";
	                                                    // $absentact -= 1; 
	                                                    break;
	                                                }
	                                            }
	                                        }
	                                    }else{
	                                        $sundayCellValue = "S";
	                                        // $absentact -= 1; 
	                                        break;
	                                    }
	                                }
	                            }
	                            // dept holi day checking based on achu code 10-09-2025
	                            if(!empty($deptHoliday))
	                            {
	                                foreach($deptHoliday as $hDate) {
	                                    if(!$AttObj->checkAttendanceByDate($usrid, $hDate)){
	                                        $prevWorkingDay = $AttObj->findPrevWrkingDay($att_user_days,$m_Hol,$hDate);
	                                        $nextWorkingDay = $AttObj->findNxtWrkingDay($att_user_days,$m_Hol,$hDate);
	                                        if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
	                                            $start = new DateTime($prevWorkingDay);
	                                            $end = new DateTime($nextWorkingDay);
	                                            $end->modify('+1 day'); // Include end date in the loop

	                                            $interval = new DateInterval('P1D');
	                                            $dateRange = new DatePeriod($start, $interval, $end);
	                                            $sundaysDatas = [];
	                                            foreach ($dateRange as $date) {
	                                                if ($date->format('w') == 0) { // 0 = Sunday
	                                                    $sundaysDatas[] = $date->format('Y-m-d');
	                                                }
	                                            }
	                                            if(!empty($sundaysDatas)){
	                                                if ($holidayRules && $holidayRules->RL_Is_LOP==1) {
	                                                    if($cdate==$sundaysDatas[0]){
	                                                        $sundayCellValue = "L";
	                                                        // $notIncrement = true;
	                                                        break;
	                                                    }
	                                                }else{
	                                                    $sundayCellValue = "S";
	                                                    // $absentact -= 1; 
	                                                    break;
	                                                }
	                                            }
	                                        }
	                                    }else{
	                                        $sundayCellValue = "S";
	                                        // $absentact -= 1; 
	                                        break;
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }else{
	                    if (in_array($cdate, $AttObj->tmp_hol)) {
	                        $sundayCellValue = "S";
	                        // $absentact -= 1; 
	                    }
	                    if($sundayCellValue!="L"){
	                        if(!empty($Holidays[0]['dates']) || !empty($Holidays[$stid]['dates']))
	                        {
	                            foreach($Holidays[0]['dates'] as $hDate) {
	                                if(!$AttObj->checkAttendanceByDate($usrid, $hDate)){
	                                    $prevWorkingDay = $AttObj->findPrevWrkingDay($att_user_days,$m_Hol,$hDate);
	                                    $nextWorkingDay = $AttObj->findNxtWrkingDay($att_user_days,$m_Hol,$hDate);
	                                    if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
	                                        $start = new DateTime($prevWorkingDay);
	                                        $end = new DateTime($nextWorkingDay);
	                                        $end->modify('+1 day'); // Include end date in the loop

	                                        $interval = new DateInterval('P1D');
	                                        $dateRange = new DatePeriod($start, $interval, $end);
	                                        $sundaysDatas = [];
	                                        foreach ($dateRange as $date) {
	                                            if ($date->format('w') == 0) { // 0 = Sunday
	                                                $sundaysDatas[] = $date->format('Y-m-d');
	                                            }
	                                        }
	                                        if(!empty($sundaysDatas)){
	                                            if(in_array($cdate, $sundaysDatas)){
	                                                $sundayCellValue = "S";
	                                                // $absentact -= 1; 
	                                                break;
	                                            }
	                                        }
	                                    }
	                                }else{
	                                    if($hDate==$cdate){
	                                        $sundayCellValue = "S";
	                                        // $absentact -= 1; 
	                                        break;
	                                    }
	                                }
	                            }
	                        }
	                        // dept holi day checking based on achu code 10-09-2025
                        	if(!empty($deptHoliday))
                        	{
	                            foreach($deptHoliday as $hDate) {
	                                if(!$AttObj->checkAttendanceByDate($usrid, $hDate)){
	                                    $prevWorkingDay = $AttObj->findPrevWrkingDay($att_user_days,$m_Hol,$hDate);
	                                    $nextWorkingDay = $AttObj->findNxtWrkingDay($att_user_days,$m_Hol,$hDate);
	                                    if(!$AttObj->checkAttendanceByDate($usrid, $prevWorkingDay) && !$AttObj->checkAttendanceByDate($usrid, $nextWorkingDay)){
	                                        $start = new DateTime($prevWorkingDay);
	                                        $end = new DateTime($nextWorkingDay);
	                                        $end->modify('+1 day'); // Include end date in the loop

	                                        $interval = new DateInterval('P1D');
	                                        $dateRange = new DatePeriod($start, $interval, $end);
	                                        $sundaysDatas = [];
	                                        foreach ($dateRange as $date) {
	                                            if ($date->format('w') == 0) { // 0 = Sunday
	                                                $sundaysDatas[] = $date->format('Y-m-d');
	                                            }
	                                        }
	                                        if(!empty($sundaysDatas)){
	                                            if(in_array($cdate, $sundaysDatas)){
	                                                $sundayCellValue = "S";
	                                                // $absentact -= 1; 
	                                                break;
	                                            }
	                                        }
	                                    }
	                                }else{
	                                    if($hDate==$cdate){
	                                        $sundayCellValue = "S";
	                                        // $absentact -= 1; 
	                                        break;
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }

	                if($sundayCellValue!="S" && $sundayCellValue!="L"){
	                    $weekendLeavesAvailable = array_intersect($dayNamesForCompensatory, ['fri', 'sat']);
	                    $cmpnstryForWeek = 0;
	                    if(!empty($weekendLeavesAvailable)){
	                        $leaveValue = 0;
	                        $fridaySatRule = $RuleObj->getSpecificRule('friday');
	                        $cmpnstryForWeek = ($sundayCellValue=="P" || $sundayCellValue=="HI")?1:0.5;
	                        $cmpnstry += $cmpnstryForWeek;
	                        $leaveValue = 0;
	                        foreach($weekendLeavesAvailable as $weekendLeaveAv){
	                            if($dayDatesForCompensatory[$weekendLeaveAv]=="L"){
	                                $leaveValue += 1;
	                            }elseif($dayDatesForCompensatory[$weekendLeaveAv]=="P2"){
	                                $leaveValue += 0.5;
	                            }elseif($dayDatesForCompensatory[$weekendLeaveAv]=="2HI"){
	                                $leaveValue += 0.5;
	                            }  
	                        }

	                        if($fridaySatRule[8]=="all"){

	                            if($leaveValue==2){
	                                $absentact += 1;
	                                if($cmpnstryForWeek==1){
	                                    // $absentact += 1;
	                                    if(empty($otherDayDatesForCompensatory)){
	                                        $absentact -= 1;
	                                    }
	                                }else{
	                                    $absentact -= 0.5;
	                                }
	                            }elseif($leaveValue==1.5){
	                                if($cmpnstryForWeek==1){
	                                    // Nothing to do
	                                }else{
	                                    $absentact -= 0.5;
	                                }
	                            }elseif($leaveValue==1){
	                                if($cmpnstryForWeek==1){
	                                    if(!empty($otherDayDatesForCompensatory)){
	                                        $absentact += 1;
	                                    }
	                                }else{
	                                    if(!empty($otherDayDatesForCompensatory)){
	                                        $absentact += 0.5;
	                                    }else{
	                                        $absentact -= 0.5;
	                                    }
	                                }
	                            }else{
	                                if($cmpnstryForWeek==1){
	                                    if(!empty($dayDatesForCompensatory) && !empty($otherDayDatesForCompensatory)) 
	                                    {
	                                        $absentact += 1;
	                                    }
	                                    $cmpnstry -= 0.5;
	                                    // if($usrid=="3820" && $cdate=="2025-05-18"){
	                                    //     echo "here111111 => ".$cmpnstryForWeek;die;
	                                    // }
	                                }else{
	                                    $absentact -= 0.5;
	                                }
	                            } 
	                        }
	                    }else{
	                        if(empty($dayDatesForCompensatory) && empty($otherDayDatesForCompensatory)) {
	                            if($sundayCellValue=="P" || $sundayCellValue=="HI"){
	                                // $absentact -= 1;
	                            }elseif($sundayCellValue=="P2" || $sundayCellValue=="2HI"){
	                                $absentact -= 0.5;
	                            }
	                        }else{
	                            if(empty($dayDatesForCompensatory) && !empty($otherDayDatesForCompensatory)) {
	                                // $absentact += 1;     // notify this area
	                                // $absentact -= 0.5;
	                            }else{
	                                $absentact -= 0.5;
	                            }
	                        }
	                    }
	                    if($isFirstSunday==true){
	                        $fridaySatRule = $RuleObj->getSpecificRule('friday');
	                        if(!empty($previousMonthFirstWeekFriSat)){
	                            $cmleaveValue = 0;
	                            $cmpnstryForWeekFirst = ($sundayCellValue=="P" || $sundayCellValue=="HI")?1:0.5;
	                            $friSatDays = ['fri', 'sat'];
	                            foreach($friSatDays as $weekendLeaveAv){
	                                if($previousMonthFirstWeekFriSat[$weekendLeaveAv]=="L"){
	                                    $cmleaveValue += 1;
	                                }elseif($previousMonthFirstWeekFriSat[$weekendLeaveAv]=="P2"){
	                                    $cmleaveValue += 0.5;
	                                }elseif($previousMonthFirstWeekFriSat[$weekendLeaveAv]=="2HI"){
	                                    $cmleaveValue += 0.5;
	                                }  
	                            }
	                            if($sundayCellValue=="P" || $sundayCellValue=="HI"){
	                                $sundayCellValue = "C";
	                                if($cmleaveValue==0.5){
	                                    $sundayCellValue = "C2";
	                                    $cmpnstry -= 0.5;  
	                                }
	                                if (!empty($otherMonthFridaySaturday)) {
	                                    $prevMonthComSalary = 1;
	                                    $cwp +=($cmleaveValue==2)?1:$cmleaveValue;
	                                    $cmpnstry -= 1;   
	                                }
	                            }elseif($sundayCellValue=="P2" || $sundayCellValue=="2HI"){
	                                $sundayCellValue = "C2";
	                                if (!empty($otherMonthFridaySaturday)) {
	                                    $prevMonthComSalary = 0.5;
	                                    $cwp +=($cmleaveValue==2)?1:$cmleaveValue;
	                                    $cmpnstry -= 0.5;
	                                }
	                            }
	                            if(!empty($otherDayDatesForCompensatory) && !empty($dayDatesForCompensatory))
	                            {
	                                $sundayCellValue = "LC";
	                                $lcFlag = true;
	                                // $absentact -= 1;
	                            }
	                        }

	                    }else{
	                        if(($sundayCellValue=="P" || $sundayCellValue=="HI") && (!empty($dayDatesForCompensatory))){
	                            $sundayCellValue = "C";
	                            if(!empty($otherDayDatesForCompensatory)){
	                                $sundayCellValue = "LC";
	                                $lcCount += 1;
	                                $lcFlag = true;
	                            }
	                        }elseif(($sundayCellValue=="P2" || $sundayCellValue=="2HI") && (!empty($dayDatesForCompensatory))) {
	                            $sundayCellValue = "C";
	                        }
	                        if(!empty($otherDayDatesForCompensatory) && !empty($dayDatesForCompensatory)) {
	                            // if($sundayCellValue=="S"){
	                            //     $absentact -= 1;
	                            // }
	                            if($sundayCellValue=="P" || $sundayCellValue=="HI"){
	                                $sundayCellValue = "C";
	                                if(!empty($otherDayDatesForCompensatory)){
	                                    $sundayCellValue = "LC";
	                                    $lcFlag = true;
	                                }
	                            }elseif($sundayCellValue=="P2" || $sundayCellValue=="2HI"){
	                                $sundayCellValue = "C";
	                            }
	                            // $absentact -= 1;
	                        }else{
	                            if(!empty($otherDayDatesForCompensatory) && empty($dayDatesForCompensatory)){
	                                if($sundayCellValue=="P" || $sundayCellValue=="HI"){
	                                    $absentact += 1;
	                                }
	                                $sundayCellValue = "LC";
	                                $lcFlag = true;
	                            }
	                        }
	                    }
	                }else{ 
	                    if(empty($dayDatesForCompensatory) && !empty($otherDayDatesForCompensatory)) 
	                    {
	                        if($sundayCellValue=="S"){
	                            $absentact -= 1;
	                        }
	                    }
	                }

	                $cellValue = $sundayCellValue;
	                if($AttObj->repaymentArray){
	                    if($AttObj->repaymentArray['PaymentTime'][0] == 1){
	                        $advanceSalary = ($AttObj->repaymentArray)?$AttObj->repaymentArray['Advance']:0;
	                    }else{
	                        $advanceSalary = ($AttObj->repaymentArray)?$AttObj->repaymentArray['Loan']:0;
	                    }
	                }
	                $checkOtherDayRuleFile = false;
	                $leaveDays  = $leaveDatesWith = [];
	            }
	            // if($usrid=="3264" && "2025-04"==$year.'-'.$month){
	            // 	echo "date and cell valueeeeeeesssssss => ".$cdate." => ".$cellValue." => ".$absentact."=> SDL =>".$sdl."<br/>";
	            // }
	            // echo "helloooo";die;
	            // late sign in  and early sign out total calcualtaions
	            $reqdsin    = strtotime($intime); 
	            $reqdsout   = strtotime($outtime); 
	            $actsin     = strtotime($loginTime); 
	            $actsout    = strtotime($logoutTime);
	            if ( $new_min_half < $hrs && $actsout > 0) {

	                $lateSinMin     = ( $reqdsin < $actsin  ) ? ( ($actsin - $reqdsin) / 60):0;
	                if ($relaxation > 1 && $lateSinMin > 0) { // time relax reduce
	                    if ($lateSinMin > $relaxation) {
	                        $lateSinMin -= $relaxation;
	                        $relaxation = 0;
	                    } else {
	                        $relaxation -= $lateSinMin;
	                        $lateSinMin = 0;
	                    }
	                }
	                if ($att_details[$cdate]['new_cal'] == 1) {
	                
	                    $earlySoutMin   = ( $actsout < $reqdsout  ) ? ( ($reqdsout - $actsout) / 60) : 0; 
	                } else if ($hrs < $minActHrs) {
	                    $earlySoutMin   = ( $actsout < $reqdsout  ) ? ( ($reqdsout - $actsout) / 60) : 0;
	                }else {
	                    $earlySoutMin   = 0;
	                }
	                if ($relaxation > 1 && $earlySoutMin > 0) { // time relax reduce
	                    if ($earlySoutMin > $relaxation) {
	                        $earlySoutMin -= $relaxation;
	                        $relaxation = 0;
	                    } else {
	                        $relaxation -= $earlySoutMin;
	                        $earlySoutMin = 0;
	                    }
	                }
	                $extrawtime     = ($hrs > $worktime) ? $hrs-$worktime : 0;
	                $late_sign      += $lateSinMin; 
	                $early_out      += $earlySoutMin;
	                $extratime      += $extrawtime;
	            }   
	        }
	        if($lcFlag == true){
	            $absentact = $absentact + $lcCount;
	        }else{
	            $absentact = $absentact;
	        }
	        if($fullact==0 && $halfact==0){
	            $absentact = (strtotime(date('Y-m-').'01') == strtotime("$year-$month-01")) ? (int)date('d') :date('t', strtotime("$year-$month-01")); //09-02-2026
	        }
	        // if($usrid=="3744" && "2025-05"==$year.'-'.$month){
	        // 	die;
	    	// }
	        $monthTotalDays = date('t', strtotime("$year-$month-01"));
	        $totalHolidaysByMonth = (count($Holidays[$stid]['dates']) + count($Holidays[0]['dates']));
            // $twd = implode(',', $monthlyWeekendOffs[$dpid]). ' => '.$totalHolidaysByMonth . ' => ' .($totalHolidaysByMonth - $monthlyWeekendOffs[$dpid]);
            $totalHolidaysIncludeWeekends = $totalHolidaysByMonth + count($monthlyWeekendOffs[$dpid]);
            $twd = $monthTotalDays - $totalHolidaysIncludeWeekends;
	        // calcualate lop
	        $lop = $absentact;
	        // find the category wise leave list
	        foreach ( $leave_typ_ary AS $leaveid ) {

	            $leaveCount = (isset($AttRptObj->approve_leaves[$usrid]) && isset($AttRptObj->approve_leaves[$usrid]['count'][$leaveid])) ? $AttRptObj->approve_leaves[$usrid]['count'][$leaveid]['total'] : 0;
	            $lop    	-= $leaveCount;
	        }
	        if($dep==true && $ofc==true){
	            $finalFlag = ($absentact - $cmpnstry);
	            // $finalFlag = $absentact;
	            $finalFlag = ($finalFlag>0)?$finalFlag:0;
	            $sdl = ($lop > 0) ? ($finalFlag):0; // loss of pay 
	        }else{
	            $sdl = ($lop > 0) ? ($lop):0; // loss of pay 
	        }
	        if($lcFlag == true && $cmpnstry != 0){
	            $sdl = $absentact - $cmpnstry;
	        }
	        // if($usrid=="3264" && "2025-04"==$year.'-'.$month){
	        //     echo "first => ".$sdl."=>".$hour_incomple.' => '.$lstdate."=>";
	        // }
	        
	        if($newTimeCheckRuleStarts==0){
		        if ($hour_incomple > 0 && $worktime > 0 && $lstdate > "2024-12-31") { //15-01-2025 
		            $sdl        +=(round($hour_incomple/($worktime/2))/2); 
		            // greater than in complete hours (daily work hours /4) consider half day leave that means 
		            //2:15 hour to 6:44 = 0.5 leave
		            //6:45 - 11:14      = 1 leave 
		            // echo "inside => ".$sdl."=>".$hour_incomple.' => '.$worktime."=>";
		        }
		    }
	        // if($usrid=="3264" && "2025-04"==$year.'-'.$month){
            // 	echo "second => ".$sdl;die;
            // }
	        $sdl            = round($sdl, 2);
	        $late_sign      = round($late_sign);
	        $early_out      = round($early_out);
	        $hour_incomple  = round($hour_incomple);

	        // $sdl 			= $sdl - $lcCount;
	        if($fullact==0 && $halfact==0 && $rw["US_AttndFlag"] != 1){
	            $sdl = date('t', strtotime("$year-$month-01")); 
	        } else if ($rw["US_AttndFlag"] == 1) { //09-02-2026
	            $sdl = 0;
	        }
	        $netSalary = 0;
	        $grossSalary = $rw['US_GrossSal'];
	        $userSalary = $UserObj->getRunningSalaryByMonth("$year-$month", $usrid);
	        if($userSalary && $userSalary['Sal_Amt']){
	            $grossSalary = $userSalary['Sal_Amt'];
	            // $grossSalaryD = $userSalary['Sal_Amt'].' - verie';
	        }
	        if($cdate==date("Y-m-t", strtotime("$year-$month-01"))){
	            $currentMonthTotalDays = date('t', strtotime($cdate));
	            $onedaySal = $grossSalary/$currentMonthTotalDays;

	            $lopDeduction = ($sdl)*$onedaySal;
	            $compensatoryAmount = 0;
	            if($prevMonthComSalary!=0){

	                $dateObj = DateTime::createFromFormat('Y-m', "$year-$month");
	                $dateObj->modify('-1 month');

	                $prevMonth = $dateObj->format('m'); // '05'
	                $prevYear  = $dateObj->format('Y'); // '2025'
	                $previousMonthTotalDays = $dateObj->format('t');
	                $compensatoryMonthSalary = $UserObj->getRunningSalaryByMonth("$prevYear-$prevMonth", $usrid);
	                $comPerDaySalary = $compensatoryMonthSalary['Sal_Amt']/$previousMonthTotalDays;
	                $compensatoryAmount = ($cwp==1)?$comPerDaySalary:($comPerDaySalary/2);
	            }
	            // $netSalary = $compensatoryAmount + ($grossSalary - $lopDeduction - $advanceSalary);
	            // $netSalary = ($netSalary>0)?round($netSalary):0;
	        }
	        
            // echo "whats happending here 2025-04 =>".$year."-".$month;die;
	        $attendanceDisplayDate = DateTime::createFromFormat('Y-m', "$year-$month");
	        $attendanceDisplayDate->format('Y-m');
	        // if($rw["US_AttndFlag"] == 1 && date('Y-m', strtotime($rw['US_AttndDate'])) <= $attendanceDisplayDate->format('Y-m')){
	        //     $lopDeduction = round($lopDeduction);
	        // }


	        // total working days 
	        $totworking 	= ($halfact/2)+$fullact+$holiday;
	        // $onedaySal 		= ($rw['US_GrossSal'])/$daysInMonth;
	        // lop amount calculation.
	        $lopdeduct 		= ($rw["US_AttndFlag"] == 1 && date('Y-m', strtotime($rw['US_AttndDate'])) <= $attendanceDisplayDate->format('Y-m')) ? 0 : round($lopDeduction);
	        
	        // after the gross salary 
	        // $grosslop 		= round($rw['US_GrossSal']-$lopdeduct);
	        $grosslop = round($grossSalary-$lopdeduct);
	        //checking ESI is in persentage or amount
	        $ESI 			= ($rw['SS_DedESI_Type'] == 0) ? round(($grosslop*$rw['SS_DedESI'])/100) : round($rw['US_DedESI']);
	        //checking EPF is in percentage or amount
	        $EPF 			= ($rw['SS_DedEPF_Type'] == 0) ? round(($grosslop*$rw['SS_DedEPF'])/100) : round($rw['US_DedEPF']);
	        // employeer contribution EPF amount
	        $EmpConEPF 		= ($rw['SS_EmpConEPF_Type'] == 0) ? round(($grosslop*$rw['SS_EmpConEPF'])/100) : round($rw['SS_EmpConEPF']);
	        // employer contribution of ESI
	        $EmpConESI  	= ($rw['SS_EmpConESI_Type'] == 0) ? round(($grosslop*$rw['SS_EmpConESI'])/100) : round($rw['SS_EmpConESI']);
	        // other salary deduction amount calculation
	        $EmpConLWF 		= ($rw['SS_EmpConLWF_Type'] == 0) ? round(($rw['US_GrossSal']*$rw['SS_EmpConLWF'])/100) : round($rw['SS_EmpConLWF']);
	        // professional tax tds
	        $ProfTds 		= round(($grosslop*$rw['SS_DedProfTDS'])/100);

            $pfesiwfDed  	= $ESI+$EPF+$rw['US_DedLWF']+$ProfTds+round($rw['US_DedSalTDS']); //pfesiwfDeduction
            $loan 			= (isset($AttRptObj->repay_list[$usrid])) ? round($AttRptObj->repay_list[$usrid]['loan']) : 0;
            $advance 		= (isset($AttRptObj->repay_list[$usrid])) ? round($AttRptObj->repay_list[$usrid]['advance']) : 0;
            //$AttRptObj->repay_list[$usrid]['sr_ids']
            // total deduction
            $deduction 		= $lopdeduct+$pfesiwfDed+$advance+$loan+round($rw['US_DedMealCard']);
            // take home salary calcualte
	        // $takeHomeSalary = round(($grossSalary-$deduction));
            $takeHomeSalary = $compensatoryAmount + ($grossSalary - $deduction);
            if($newTimeCheckRuleStarts==1){
                $incompleteMinutes = $hour_incomple;
                $workingMinutesPerDay = 9 * 60;
                $totalWorkingMinutes = $currentMonthTotalDays * $workingMinutesPerDay;
                $perMinuteSalary = $grossSalary / $totalWorkingMinutes;
                $salaryDeduction = $incompleteMinutes * $perMinuteSalary;
                $takeHomeSalary = $takeHomeSalary - $salaryDeduction;
            }
            // if($usrid=="3744" && "2025-05"==$year.'-'.$month){
            // 	echo "date and cell value => ".$sdl."<br/>";
            // }

	        // ESi and EPF zero so no need to set the base salary
	        $grosslop 		= ($ESI==0 && $EPF==0) ? 0 : $grosslop; 

	        $final_arrayPayroll= array(
	            'US_Id'=> $usrid,
	            'OF_Id'=>$rw['OF_Id'],
	            'LC_Name'=>"'".$rw['LC_Name']."'",
	            'US_FName'=>"'".$rw['US_FName']."'",
	            'US_LName'=> "'".$rw['US_LName']."'",
	            'EP_Month'=>$month,
	            'EP_Year'=>$year,
	            'US_GrossSal'=>round($grossSalary),
	            'US_BasicSal'=>round($rw['US_BasicSal']),
	            'US_HRASal'=>round($rw['US_HRASal']),
	            'US_CcaSal'=>round($rw['US_CcaSal']),
	            'US_ConveySal'=>round($rw['US_ConveySal']),
	            'US_EduSal'=>round($rw['US_EduSal']),
	            'US_MedSal'=>round($rw['US_MedSal']),
	            'EP_Otherallowance'=>round($rw['US_MiscSal']),
	            'US_DedEPF'=>$EPF,
	            'US_DedESI'=>$ESI,
	            'US_DedLWF'=>round($rw['US_DedLWF']),
	            'EP_EmpConEPF'=>$EmpConEPF,
	            'EP_EmpConESI'=>$EmpConESI,
	            'EP_EmpConLWF'=>$EmpConLWF,
	            'EP_Lop'=>$lopdeduct,
	            'EP_TakehomeSal'=>round($takeHomeSalary),
	            'EP_Proftax'=>0,
	            'EP_ProfTds'=>$ProfTds,
	            'EP_SalTds'=>round($rw['US_DedSalTDS']),
	            'EP_MealCard'=>round($rw['US_DedMealCard']),
	            'EP_Salaryadvance'=>$advance,
	            'EP_Loan'=>$loan,
	            'EP_AdjstmntAddition'=>0,
	            'EP_AdjstmntDeduction'=>0,
	            'EP_SalDeductableLeave'=>$sdl,
	            'EP_PFESI_sal'=>$grosslop,
	            'EP_WorkDays'=>$totworking,
	            'EP_TotWrkDays'=>$lastday,
	            'EP_PrsntDays'=>$fullact+($halfact/2),
	            'EP_HlfDays'=>$halfact,
	            'EP_LOPDays'=>$sdl,
	            'EP_GeneratedBy'=>$preTally_user_id,
	            'EP_CDate'=>"'".date("Y-m-d H:i:s")."'",
	            'EP_HoursIncomplete'=>$hour_incomple
        	);
	        $totalLoan 	= $advance+$loan;

	        if ($totalLoan > 0 && $takeHomeSalary < $totalLoan) { // COPY OLD CODE 

                $errorPayroll["Status"] = "salary_negative";
                $names 		= $rw['US_FName']." ".$rw['US_LName'];
                array_push($errorPayroll,$names);
                $err_Flag 	= 1;                                    
            } else {

            	$dataPayroll[$count] 	= $final_arrayPayroll;
            	if(!empty($AttRptObj->repay_list[$usrid]['sr_ids'])){
                    $repayment_ids 		= array_merge((array)$repayment_ids,(array)$AttRptObj->repay_list[$usrid]['sr_ids']); 
                }
                $count++;
            }
	    }
	    if ($err_Flag == 0) {
	    	// success and generate the invoice...
	    	$result = $AttObj->generateEmployeePayroll("employee_payroll",$dataPayroll);
	    	if ($result==1) {
	          foreach ($repayment_ids as $ids) {   
	          	$AttObj->updateRepaymentStatus($ids,3);
	          }
	        }
	        echo $result; 
	    }else{              
            echo json_encode($errorPayroll);
            return 4;          
        }
        /*echo "<pre>";
        print_r($dataPayroll);
        return 4; */    
	} else {
		echo 3;
     	return;
	}
} else { // already exisit in the same month
	echo 2;
    return;
}
// end the payroll generation section.
?>