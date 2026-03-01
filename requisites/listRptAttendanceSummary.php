<?php
/**
 * New monthly attendance report of all staffs Created By Bilin @ 27-aug-2024
 * old files moved into  listAttendanceMonth.php
*/
require_once($BASEPATH . 'includes/functions.php');
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
include_once($BASEPATH . "preTallyClass/AttendanceRptClass.php");
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
include_once($BASEPATH . "preTallyClass/RuleClass.php");     // 30052025 by Achu

// pagination related parameters getting
$posStart   = (!isset($_GET["posStart"])) ? 0: $_GET["posStart"];
$count      = (!isset($_GET["count"])) ? 0: $_GET["count"];
$calltype   = (!isset($_GET["calltype"])) ? '': $_GET["calltype"];

// class object creations
$UserObj    = new UserClass();
$AttObj     = new AttendanceClass();
$LeaveObj   = new LeaveClass();
$AttRptObj  = new AttendanceRptClass();
$RuleObj    = new RuleClass();   // 30052025 by Achu

// $fridaySatRule = $RuleObj->getSpecificRule('friday');
// echo "<pre>";print_r($fridaySatRule[8]);die;
// get multiple employees ids
$emp_ids    = (isset($REQUEST['emp_ids']) && $REQUEST['emp_ids'] != '') ? array_filter(explode(',',$REQUEST['emp_ids'])):[];//09-04-2025
// get selected user data
$user_id    = (isset($REQUEST['userFilter'])) ? (int)$REQUEST['userFilter']: 0;
// find the data fetching and displaying month and year new section.
$year       = (isset($REQUEST['att_year'])) ? $REQUEST['att_year']: date("Y");
$month      = (isset($REQUEST['att_month'])) ? str_pad($REQUEST['att_month'], 2, "0", STR_PAD_LEFT):date("m");
$g_date     = $year.'-'.$month;
//$daysInMonth= cal_days_in_month(CAL_GREGORIAN, $month, $year);
$lastday    = ($g_date == date('Y-m')) ? date("d")-1 : date("t", strtotime($g_date.'-01'));
$lastdate   = $year."-".$month."-".$lastday;
$minTimePresent = (20250601 <= date("Ymd", strtotime($stdate))) ? 30 : 1.5; // this old By Bilin on 2025/06/04
$newTimeStart   = (20250601 <= date("Ymd", strtotime($stdate))) ? 1:0; //18-06-2025 
// get the company Office ID & Details based on the login user office id
$UserObj->selectCompanySettings($preTally_user_ofid);
$compSeting     = $UserObj->CompanySettingsArray;
//$compSeting['CS_WrkHrGraceTime']; - company common grase time
$AttObj->getHolidays($lastdate,$preTally_user_ofid); // Company Holidays List
$AttObj->getWeekendOffs($month,$year,$preTally_user_ofid); // Weekend Off List   
$AttObj->getOfficeRH($preTally_user_ofid); // RH Off List   
$AttObj->getLeaveType($preTally_user_ofid); // Type Of Leaves
$leaveTypes     = $AttObj->getLeaveTypeArray;
$leave_typ_ary  = [];
$Holidays       = $AttObj->Holidays;    
$WeekendOffs    = $AttObj->WeekOffs; 
$editable       = false; // edit mode off

// other filters basaed on once the page enable pagination
$filterary  = ['limit'=>$count, 'month'=>$month, 'year'=>$year, 'start'=>$posStart, 'office_id'=>$preTally_user_ofid, 'user_id'=>$user_id, 'lastday'=>$lastday,  'comm_gracetime'=>$compSeting['CS_WrkHrGraceTime'], 'relaxation'=>$compSeting['CS_LoginGraceTime']];
if (isset($_REQUEST["Filters"]) ) {
    $filters    = explode(",",$_REQUEST["Filters"]);
    $filterary['username']  = ($filters[0] != '') ? trim($filters[0]):""; 
    $filterary['branch_id'] = ($filters[1]!=0 && $filters[1]!="") ?$filters[1]:0;
    $filterary['sort']      = ($filters[2]==1) ? "name":"branch";
    $filterary['order']     = ($filters[3]=='des')? "DESC":"ASC";
}
$filterary['emp_ids']       = $emp_ids; //09-04-2025
// call the list of users and their daily attendance brief......
$AttRptObj->listReptAttendance($filterary);
//echo $AttRptObj->sel_qry;
//echo "Total records : ".$AttRptObj->total; // get the total row countr
//echo "List all users:-";
//print_r($AttRptObj->selt_users);
//print_r($AttRptObj->listAttendance); // list all users in the selected
echo '{';
if ($count > 0 ) { // pagination is present...
    echo '"total_count":'.$AttRptObj->total.', "pos":'.$posStart.',';
}
//09-04-2025 start to save csv files with listed attendance
$csvFileName = "Monthly attendance report for ".DateTime::createFromFormat('!m', $month)->format('F');
$exp_filename   = $BASEPATH."uploads/attendance/".$csvFileName." ".$year.".csv";
$filedata       = '';
$fileopobj      = fopen ($exp_filename, "w");
// base initialise started 
$filedata = ",\"Monthly attendance report for " . DateTime::createFromFormat('!m', $month)->format('F') . " " . $year . "\" \n ";
$filedata .= " \n ";
$filedata .= "SlNo,Name,Branch"; 
for ($j = 1; $j <= $lastday; $j++) {
    $j  = ($j < 10) ? "0" . $j : $j;
    // $filedata .= ",".$j;//09-04-2025
}
$filedata .= ",FD,HD,TL";
foreach ($leaveTypes as $rows) {
    $LT_Name = ucwords($rows->LT_Name);   
    $filedata .= ",".$LT_Name[0]."L";
}
$filedata .= ",CW,CWP,SDL,LS,ES,HI,GS,ADV,NS \n ";

//first time or loaded 0th data then not show the header (heading grid..)
if ($posStart == 0 && $calltype != "upd") {
    echo 'head:[
    {width:47,  type:"ed", align:"center", sort:"na", value:"SlNo"},
    {width:165, type:"ro", align:"left",   sort:"na", value:"Name"},
    {width:150, type:"ro", align:"left",   sort:"na", value:"Branch"}, ';    
    for ($j = 1; $j <= $lastday; $j++) {
        $j  = ($j < 10) ? "0" . $j : $j;
        $currentDay = $year.'-'.$month.'-'.$j;
        $daySt = date('D', strtotime($currentDay));
        $daySt = ($daySt == "Sun") ? '<br>[S]' : '';
        // echo '{width:30, type:"combo",    align:"center",   sort:"na", value:"' . $j .$daySt. '"},';
    }
    echo '{width:40, type:"ro", align:"center", sort:"na", value:"FD"},
          {width:40, type:"ro", align:"center", sort:"na", value:"HD"},
          {width:45, type:"ro", align:"center", sort:"na", value:"TL"},';        
    foreach ($leaveTypes as $rows) {

        $LT_Name = ucwords($rows->LT_Name); 
        echo '{width:45,  type:"ro",   align:"center",  sort:"na", value:"'.$LT_Name[0].'L"},';        
        $leave_typ_ary[] = $rows->LT_Id; 
    }
    $gsLabel = 'GS <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info\' id=\'img_Info\' title=\'GROSS Salary\' />';
    $advLabel = 'ADV <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info\' id=\'img_Info\' title=\'Advance Amount\' />';
    $nsLabel = 'NS <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info\' id=\'img_Info\' title=\'NET Salary (Only available on month end day)\' />';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"CW"},';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"CWP"},';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"SDL"},';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"LS"},';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"ES"},';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"HI"},';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"'.$gsLabel.'"},';
    echo '{width:60, type:"ro", align:"center", sort:"na", value:"'.$advLabel.'"},';
    echo '{width:55, type:"ro", align:"center", sort:"na", value:"'.$nsLabel.'"},';
    echo '{width:60, type:"ro", align:"center", sort:"na", value:"Details"}';
    echo '],';  
} else { // no need headers
    // echo "<pre>";print_r($leaveTypes);die;
    foreach ($leaveTypes as $rows) {
        $leave_typ_ary[] = $rows->LT_Id; 
    }
}
// start to list the grid data...
echo 'rows:[';
if ($AttRptObj->total > 0) { // checking data present or not

    if ( !empty($AttRptObj->selt_users) ) {
        //echo "last work day of previous & next month first work day of selected users";
        $adjAttendance  = $AttRptObj->getAdjMnthAttendance($month, $year); 
        // echo "<pre>";print_r($adjAttendance);die;
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
    }
    //echo "check the display attendance month data have edit permission";
    if ( $AttObj->verifyMonth("employee_payroll","EP_Month",$month,$preTally_user_ofid,"EP_Year",$year) ) { 
        $editable   = true;
    }
    $slno  = $posStart+1; 
    // $dept=false;$ofc=false;
    // echo "<pre>";print_r($AttRptObj->listAttendance);die;
    foreach ($AttRptObj->listAttendance as $usrid => $rw) {

        $half           = 0;
        $halfact        = 0;
        $advanceSalary  = 0;
        $cmpnstry       = 0;        
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
        // $sundayCellCount= 0;
        $hasOtherDays = $hasLastWeekDay = $holidaySandwich = $startOfLastWeek = $lastDayOfMonth = $checkOtherDayRuleFile = $previousMonthFirstWeekCheck = false;
        $dep = true;
        $ofc = false;
        // Initialize cell value

        $att_details    = $rw["list"]; // full attendance details 
        $stid           = $rw['ST_Id']; 
        $dpid           = $rw['DP_Id'];
        $ofcid          = $rw['OF_Id'];     // Office ID
        $att_user_days  = array_keys($att_details); // all attendance days
        $UserDetails    = $UserObj->viewSingleUser($usrid);
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
        // try to check the below 2 variable usage still no idea
        $cellTypeValue  = array();
        $offTypeValue   = array();
        
        //$AttRptObj->approve_leaves[$usrid]['count'][$row->LT_Id]['total']
        // set the leave list into the array
        $approvedLeaveDates = (!empty($AttRptObj->approve_leaves[$usrid]['list'])) ? array_column($AttRptObj->approve_leaves[$usrid]['list'], 'LRD_Date') : array(); 
        //find the common in two array and assign that based first array key
        $array_intr     = array_intersect($approvedLeaveDates, $att_user_days); 
        
        //user based new rows of data..  - '.$rw['US_DOJ'].'
        echo '{ id : 1000' . $usrid . ',
                    data:[
                        "' . $slno . '",
                        "' . $rw['US_FName'] .' '. $rw['US_LName'].'",
                        "' . $rw['LC_Name'] . '",';
        $filedata .= ''.$slno.','.$rw['US_FName'].' '.$rw['US_LName'].','.$rw['LC_Name'].'';//9-4-25                    
        //serial , name and branch printed above..
        // date wise data grid loading start....
        $notIncrement = false;
        for ($j = 1; $j <= $lastday; $j++) {   
        
            $d          = ($j < 10) ? "0".$j : $j; 
            $cdate      = $year . "-" . $month . "-" . $d;
            $AttObj->getRepaymentAmounts($usrid,$month, $year);
            // if($usrid=="3793"){
            //     echo "<pre>";print_r($AttObj->repaymentArray);die;
            // }
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
            $new_min_half   = ($newTimeStart == 1) ? ($minWrkHrs-($minTimePresent/2)) : ($minWrkHrs/$minTimePresent); // 18-06-2025
            $new_min_full   = ($newTimeStart == 1) ? ($minWrkHrs-$minTimePresent) : ($minWrkHrs/$minTimePresent); // 18-06-2025
            $cellType   = "N"; // NRM -- Normal Holiday
            if (multi_array_search($cdate,$AttObj->RH_Holidays[0]) || multi_array_search($cdate,$AttObj->RH_Holidays[$stid])) {
                $cellType   = "R"; //RH -- Reserved Holiday
                $title      = "Reserved Holiday";
                $cellTypeValue[$j] = $cellType;
                //$holidayList[] = $cdate;
            }
           
            // leave days checking - approved leave date have any attendance marked
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
            // check the date in attendance and not in applied leave and minimum hours (half day) completed
            if (in_array($cdate, $att_user_days) && (!array_search($cdate, $approvedLeaveDates)) && $hrs > 0) {
                unset($AttObj->tmp_hol); 
                $AttObj->tmp_hol = array();

                if ( $hrs >= $minActHrs ) { // full day attendance time completed.
                    $cellValue  = 'P';
                    $title      = "Present";
                    $full       += 1;
                    $fullact    += 1;
                    $extratime  += ($worktime < $hrs) ? ($hrs-$worktime): 0;
                } else if ( $hrs >= $minWrkHrs ) { // half day
                    // not apply leave (salary not cutting leave not apply)
                    $cellValue  = "P2";  
                    $title      = "Half day";  
                    if ($att_details[$cdate]['new_cal'] == 1) { // new calculation
                         
                         if ($hrs >= ($minWrkHrs+$new_min_full)) { //half +half/2
                            $cellValue  = "HI"; 
                            // calculate the time and added into HI
                            $hour_incomple +=  $minActHrs-$hrs; 
                            //$full       += 1; 
                            $title      = "Full day (Incomplete Hours)";
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
                        // apply above after confirmation & hide the below
                        /*if ($hrs >= ($worktime-$compSeting['CS_WrkHrGraceTime'] )) {
                            $cellValue  = 'P';
                            $title      = "Present";
                            $full       += 1;
                            $hour_incomple +=  $worktime-$hrs;
                        } else {
                            $half       += 1;
                            $absent     += 0.5;
                        }*/
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
                        $title          = "Half day + Incomplete Hours";
                        $leaveDays[]    = $cdate;       // 30052025 by Achu
                        $leaveDatesWith[$cdate] = "P2"; // 11062025 by Achu
                        // apply above after confirmation & hide the below
                        /*if ($hrs >= (($worktime/2)-$compSeting['CS_WrkHrGraceTime'] )) {
                            $cellValue  = "P2";  
                            $title      = "Half day";
                            $half       += 1;
                            $absent     += 0.5;  
                            $hour_incomple +=  ($worktime/2)-$hrs;   
                        } else {
                            $cellValue  = "L";
                            $absent     += 1; 
                            $title      = "Leave";
                        }*/
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
                    $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);
                    sort($m_Hol);
                } 
                //CHECK THE GRID DATE IN THE ATTENDANCE AND HOLY DAY LIST
                $AttObj->chkadjDays($att_user_days,$m_Hol,$cdate);
                //insert the rh leave log and delete from the leave request
                if((!in_array($cdate, $WeekendOffs[$dpid])) 
                    && (!in_array($cdate, $Holidays[0]['dates'])) 
                    && (!in_array($cdate, $Holidays[$stid]['dates'])  
                    && (!array_search($cdate, $approvedLeaveDates)) 
                    && (!in_array($cdate, $AttObj->tmp_hol))         
                    && (array_search($cdate,$rh_holiday)))) {
                    if ($AttObj->checkRHDate($rh_holiday,$cdate,$usrid) == "RH_Apply") {
                        $LeaveObj->deleteLeaveRecords($usrid, $cdate);
                    }
                }
                // check the date is in off day or holiday
                if ((in_array($cdate, $WeekendOffs[$dpid]) || in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates'])) && (!in_array($cdate, $AttObj->tmp_hol) && !array_search($cdate, $approvedLeaveDates) && !in_array($cdate,$rh_takendays))) {
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
                        //$holidayList[] = $cdate;   // 30052025 by Achu
                    }
                    $offTypeValue[$j] = ucfirst($title);
                } else {
                    //check the user take rh
                    if (in_array($cdate,$rh_takendays)) {
                        $holiday        += 1; 
                        $title          ="Restrited Holiday";
                        $cellValue      ="H";
                        // $holidayList[] = $cdate;   // 30052025 by Achu
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
                                        $title          = "Half day + Incomplete Hours";
                                    } else {
                                        $cellValue  = "L";
                                        $absent     += 1; 
                                        $absentact  += 1;
                                        $title      = "Leave";
                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
                                    }
                                } else {
                                    $half       += 1;
                                    $halfact    += 1;
                                    $absent     += 0.5; 
                                    $absentact  += 0.5; 
                                    $title      = "Half day";
                                    $cellValue  = "P2"; 
                                    $leaveDatesWith[$cdate] = "P2"; // 11062025 by Achu
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
                                // new calculation below 2 hours set as leave other wise hour incomplete + half day
                                /*$cellValue      = "P0";
                                $half           += 1;
                                $absent         += 0.5; 
                                $hour_incomple  +=  $minActHrs-$hrs;
                                $title          = "Half day + Incomplete Hours";*/

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
                                // $cellValue  = "H";   // here is cup
                                $cellValue  = "L";   // here is cup
                                $leaveDays[]   = $cdate;    // 30052025 by Achu
                                $AttObj->deleteAttendance($usrid, $cdate);
                                $LeaveObj->deleteLeaveRecords($usrid, $cdate);
                                $AttObj->deleteRHLog($usrid, $cdate);
                                $notIncrement = true;
                            }else{
                                if($holidayRules->RL_Is_LOP==1){
                                    $cellValue  = "L";   // here is cup
                                    $leaveDays[]   = $cdate;    // 30052025 by Achu
                                    $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
                                    $AttObj->deleteAttendance($usrid, $cdate);
                                    $LeaveObj->deleteLeaveRecords($usrid, $cdate);
                                    $AttObj->deleteRHLog($usrid, $cdate);
                                    $absentact  += 1;
                                    $notIncrement = true;
                                }else{
                                    $cellValue  = "H";   // here is cup
                                }
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
                                // if($usrid=="3685" && $cdate=="2025-04-13"){
                                //     echo "<pre>";print_r($AttObj->tmp_hol);die;
                                // }
                                if (in_array($cdate, $AttObj->tmp_hol)) {
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
            // 30052025 by Achu
            $day = strtolower(date("l",strtotime($cdate)));
            if(in_array($dpid, $DeptArray)){
                $dep = false;
            }
            if(in_array($ofcid, $OfficeArray)){
                $ofc = true;
            }
            if ($day=='sunday' && $dep==true && $ofc==true) {
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
                    // if($usrid=="93" && $cdate=="2025-06-01"){
                    //     echo "<pre> => ".print_r($RulesList);die;
                    // }
                    if($previousMonthFirstWeekCheck == true){
                        $givenDate = new DateTime($cdate); // any given date
                        // First day of the month
                        $firstDayOfMonth = (clone $givenDate)->modify('first day of this month');

                        // First Sunday of the month
                        $firstSunday = (clone $firstDayOfMonth)->modify('first sunday');

                        // Determine the Monday–Saturday week
                        if ((int)$firstSunday->format('d') <= 7) {
                            // First week of the current month
                            $monday = (clone $firstDayOfMonth);
                            if ($monday->format('N') != 1) {
                                $monday->modify('next monday');
                            }
                        } else {
                            // Last week of previous month
                            $lastDayPrevMonth = (clone $firstSunday)->modify('last day of previous month');
                            $monday = (clone $lastDayPrevMonth);
                            if ($monday->format('N') != 1) {
                                $monday->modify('last monday');
                            }
                        }
                        // Saturday of that week
                        $saturday = (clone $monday)->modify('saturday this week');
                        // Format for your function
                        $startDay = $monday->format('Y-m-d');
                        $endDay = $saturday->format('Y-m-d');
                        $betweenDates = $AttObj->getBetweenDatesAfterDOJ($startDay, $endDay, $rw['US_DOJ']);
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
                                    } else{ // Invalid
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
                            if($commonItems==true){
                                if(!$AttObj->checkAttendanceByDate($usrid, $cdate)){
                                    if($sundayCellValue=="S"){
                                        $sundayCellValue = 'L';
                                        $absentact  += 1;
                                    }
                                }else{
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
                            }
                            else{
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
                            // {
                            //     if($cmpnstryForWeek==0.5){
                            //         // if($usrid=="2657" && $cdate=="2025-06-08"){
                            //         //     echo "here111111 => ".$cmpnstry;die;
                            //         // }
                            //         // $absentact -= $cmpnstryForWeek;
                            //     }
                            // }
                            // elseif($leaveValue==1.5){
                            //     if($cmpnstryForWeek==1){
                            //         // nothing to do
                            //     }else{
                            //         // $absentact  += 0.5;
                            //     }
                            // }
                        }
                        // else{
                        //     $absentact  -= 1;
                        // }
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
                                // $absentact -= 1;
                            }
                        }

                    }else{
                        if(($sundayCellValue=="P" || $sundayCellValue=="HI") && (!empty($dayDatesForCompensatory))){
                            $sundayCellValue = "C";
                            if(!empty($otherDayDatesForCompensatory)){
                                $sundayCellValue = "LC";
                            }
                        }elseif(($sundayCellValue=="P2" || $sundayCellValue=="2HI") && (!empty($dayDatesForCompensatory))) {
                            $sundayCellValue = "C2";
                        }
                        if(!empty($otherDayDatesForCompensatory) && !empty($dayDatesForCompensatory)) {
                            // if($sundayCellValue=="S"){
                            //     $absentact -= 1;
                            // }
                            if($sundayCellValue=="P" || $sundayCellValue=="HI"){
                                $sundayCellValue = "C";
                                if(!empty($otherDayDatesForCompensatory)){
                                    $sundayCellValue = "LC";
                                }
                            }elseif($sundayCellValue=="P2" || $sundayCellValue=="2HI"){
                                $sundayCellValue = "C2";
                            }
                            // $absentact -= 1;
                        }else{
                            if(!empty($otherDayDatesForCompensatory) && empty($dayDatesForCompensatory)){
                                if($sundayCellValue=="P" || $sundayCellValue=="HI"){
                                    $absentact += 1;
                                }
                                $sundayCellValue = "LC";
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
                $title     = $day;  
                if($AttObj->repaymentArray){
                    if($AttObj->repaymentArray['PaymentTime'][0] == 1){
                        $advanceSalary = ($AttObj->repaymentArray)?$AttObj->repaymentArray['Advance']:0;
                    }else{
                        $advanceSalary = ($AttObj->repaymentArray)?$AttObj->repaymentArray['Loan']:0;
                    }
                    // if($usrid==3793){
                    //     echo 'advance Salary is => '.$advanceSalary;die;
                    // }
                }
                // if($cellValue=="L" && $notIncrement==true){
                //     $absentact  += 1;
                // } 
                if($cdate < $rw['US_DOJ']){
                    $cellValue = "L";
                    $AttObj->deleteAttendance($usrid, $cdate);
                    $LeaveObj->deleteLeaveRecords($usrid, $cdate);
                    $AttObj->deleteRHLog($usrid, $cdate);
                }
                // else{
                //     if($cellValue=="L"){
                //         // if($notIncrement==true){
                //         //     $absentact  += 1;
                //         // }
                //         $AttObj->deleteAttendance($usrid, $cdate);
                //         $LeaveObj->deleteLeaveRecords($usrid, $cdate);
                //         $AttObj->deleteRHLog($usrid, $cdate);
                //     }
                // }
                $notIncrement = $checkOtherDayRuleFile = false;
                $leaveDays  = $leaveDatesWith = [];
            }
            // if($cellValue=="L"){
            //     $cellValue = $cellValue.' - '.$absentact;
            // }
            // 30052025 by Achu
            // echo '"'.$cellValue.'",'; 
            $filedata .= ','.$cellValue.'';//9-4-25  
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
        //echo '"'.$full.'","'.$half.'","'.$absent.'",'; 
        echo '"'.$fullact.'","'.$halfact.'","'.$absentact.'",'; 
        $filedata .= ','.$fullact.','.$halfact.','.$absentact.'';//9-4-25  
        // calcualate lop
        //$lop = $lastday-(($half/2)+$full+$holiday);
        $lop = $absentact;
        // find the category wise leave list
        foreach ( $leave_typ_ary AS $leaveid ) {

            $leaveCount = (isset($AttRptObj->approve_leaves[$usrid]) && isset($AttRptObj->approve_leaves[$usrid]['count'][$leaveid])) ? $AttRptObj->approve_leaves[$usrid]['count'][$leaveid]['total'] : 0;

            echo '"'.$leaveCount.'",';
            $filedata .= ','.$leaveCount.'';//9-4-25  
            $lop    -= $leaveCount;
        }
        // if($cmpnstry=="0.5"){
        //     if (is_numeric($cmpnstry) && floor($cmpnstry) != $cmpnstry) {
        //         $calculatedCmplmntry = $cmpnstry;
        //     }
        // }else{
        //     if (is_numeric($cmpnstry) && floor($cmpnstry) != $cmpnstry) {
        //         $calculatedCmplmntry = $cmpnstry + 1;
        //     }
        // }
        // if($usrid=="3790"){
        //     echo "calculatedCmplmntry => ".$leaveValue;die;
        // }
        // $finalFlag = ($absentact - $cmpnstry);
        // $finalFlag = $absentact;
        // $finalFlag = ($finalFlag>0)?$finalFlag:0;
        // $sdl = ($lop > 0) ? ($finalFlag):0; // loss of pay 
        //$sdl += ($hour_incomple > 0 && $worktime > 0) ? ($hour_incomple/$worktime):0;
        if($dep==true && $ofc==true){
            $finalFlag = ($absentact - $cmpnstry);
            // $finalFlag = $absentact;
            $finalFlag = ($finalFlag>0)?$finalFlag:0;
            $sdl = ($lop > 0) ? ($finalFlag):0; // loss of pay 
        }else{
            $sdl = ($lop > 0) ? ($lop):0; // loss of pay 
        }
        if ($hour_incomple > 0 && $worktime > 0 && $lastdate > "2024-12-31") { //15-01-2025 
            $sdl        +=(round($hour_incomple/($worktime/2))/2); 
            // greater than in complete hours (daily work hours /4) consider half day leave that means 
            //2:15 hour to 6:44 = 0.5 leave
            //6:45 - 11:14      = 1 leave 
        }
        $sdl            = round($sdl, 2);
        $late_sign      = round($late_sign);
        $early_out      = round($early_out);
        $hour_incomple  = round($hour_incomple);
        // $sdl = $absentact.'-'.$sdl;
        echo '"'.$cmpnstry.'","'.$cwp.'","'.$sdl.'","'.$late_sign.'","'.$early_out.'","'.$hour_incomple.'",';
        $filedata .= ','.$cmpnstry.','.$cwp.','.$sdl.','.$late_sign.','.$early_out.','.$hour_incomple;//9-4-25 
        $netSalary = 0;
        $grossSalary = $UserDetails->US_GrossSal;
        $userSalary = $UserObj->getRunningSalaryByMonth("$year-$month", $usrid);
        if($userSalary && $userSalary['Sal_Amt']){
            $grossSalary = $userSalary['Sal_Amt'];
            // $grossSalaryD = $userSalary['Sal_Amt'].' - verie';
        }
        if($cdate==date("Y-m-t", strtotime("$year-$month-01"))){
            $currentMonthTotalDays = date('t', strtotime($cdate));
            $perDaySalary = $grossSalary/$currentMonthTotalDays;

            $lopDeduction = ($sdl)*$perDaySalary;
            $compensatoryAmount = 0;
            if($prevMonthComSalary!=0){

                $dateObj = DateTime::createFromFormat('Y-m', "$year-$month");
                $dateObj->modify('-1 month');

                $prevMonth = $dateObj->format('m'); // '05'
                $prevYear  = $dateObj->format('Y'); // '2025'
                $previousMonthTotalDays = $dateObj->format('t');
                $compensatoryMonthSalary = $UserObj->getRunningSalaryByMonth("$prevYear-$prevMonth", $usrid);
                $comPerDaySalary = $compensatoryMonthSalary['Sal_Amt']/$previousMonthTotalDays;
                // if($prevMonthComSalary==0.5){
                //     $comPerDaySalary = $comPerDaySalary/2;
                // }
                $compensatoryAmount = ($cwp==1)?$comPerDaySalary:($comPerDaySalary/2);
            }
            // if($usrid=="3828"){
            //     echo $prevMonthComSalary.' => '.$compensatoryAmount.' + '.$grossSalary.' - '.$lopDeduction. ' - '.$advanceSalary;die;
            // }
            $netSalary = $compensatoryAmount + ($grossSalary - $lopDeduction - $advanceSalary);
            $netSalary = ($netSalary>0)?round($netSalary):0;
        }
        $attendanceDisplayDate = DateTime::createFromFormat('Y-m', "$year-$month");
        $attendanceDisplayDate->format('Y-m');
        if($rw["US_AttndFlag"] == 1 && date('Y-m', strtotime($rw['US_AttndDate'])) <= $attendanceDisplayDate->format('Y-m')){
            $netSalary = $grossSalary;
        }
        // detail popup define
        $Infoimg        = '<img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; \' class=\'img_Info\' id=\'img_Info\' title=\'Click here to view more details\'  onclick=\' preTally.UserProfile.reportShowAttendance('.$usrid.');\'  />';
        echo '"'.$grossSalary.'","'.$advanceSalary.'","'.$netSalary.'","'.$Infoimg.'",';
        $filedata .= ','.$grossSalary.','.$advanceSalary.','.$netSalary." \n "; // 23-06-2025
        // define grid row based user data defining
        echo '], 
            userdata:{
                \'userID\'  :\''.$usrid.'\',
                \'offType\' :\''.json_encode($offTypeValue).'\',
                \'cellType\':\''.json_encode($cellTypeValue).'\'     
            } 
        },';
        $slno++;
    }
}
echo '], 
    \'leaveTypes\':\''.json_encode($leaveTypes).'\',
    \'leaveCount\':\''.count($leave_typ_ary).'\',
    \'totalDays\':\''.$lastday.'\',
    \'isEditable\':\''.$editable.'\',    
    \'ALCEdit\':\''.$ACL_Obj->ACL_AttendanceEdt.'\',        
    \'cellDate\':\''.$year.'-'.$month.'\' 
    }';

// ------------ new on progress --------------- //
// 09-04-2025 save contents into excel files    
fputs($fileopobj, $filedata);
fclose($fileopobj);
// end the file saving 09-04-2025
?>