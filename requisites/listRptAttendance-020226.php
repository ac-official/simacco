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
include_once($BASEPATH . "preTallyClass/RuleClass.php");     // 30-05-2025 by Achu
require_once($BASEPATH . "preTallyClass/LateEntryClass.php"); // 09-07-2025 By Achu

// pagination related parameters getting
$posStart   = (!isset($_GET["posStart"])) ? 0: $_GET["posStart"];
$count      = (!isset($_GET["count"])) ? 0: $_GET["count"];
$calltype   = (!isset($_GET["calltype"])) ? '': $_GET["calltype"];
$displayType   = (!isset($_GET["display_type"])) ? 'list': $_GET["display_type"];

// class object creations
$UserObj    = new UserClass();
$AttObj     = new AttendanceClass();
$LeaveObj   = new LeaveClass();
$AttRptObj  = new AttendanceRptClass();
$RuleObj    = new RuleClass();   // 30052025 by Achu
$lateEntryObj = new LateEntryClass(); // 09-07-2025 By Achu

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
$minTimePresent = (20261001 <= date("Ymd", strtotime($lastdate))) ? 2 : 1.5; // this old By Bilin on 2025/06/04
// $newTimeStart   = (20250601 <= date("Ymd", strtotime($lastdate))) ? 1:0; //18-06-2025 
$newTimeStart   = 0;
$newTimeCheckRuleStarts = (20261001 <= date("Ymd", strtotime($lastdate))) ? 1:0;  // 09-07-2025 By Achu
// echo "New time rules flag => ".(20250601 <= date("Ymd", strtotime('2025-04-01')))?0:1;die;
// get the company Office ID & Details based on the login user office id
$UserObj->selectCompanySettings($preTally_user_ofid);
$compSeting     = $UserObj->CompanySettingsArray;
//$compSeting['CS_WrkHrGraceTime']; - company common grase time
$AttObj->getHolidays($lastdate,$preTally_user_ofid); // Company Holidays List
$AttObj->getWeekendOffs($month,$year,$preTally_user_ofid); // Weekend Off List   
$AttObj->getMonthlyWeekendOffs($month,$year,$preTally_user_ofid); // Weekend Off List   
$AttObj->getOfficeRH($preTally_user_ofid); // RH Off List   
$AttObj->getLeaveType($preTally_user_ofid); // Type Of Leaves
$AttObj->getDeptHolidays($lastdate,$preTally_user_ofid,0); // dept holidays 09-09-2025

$stdate = $year."-".$month."-1";
$cudate     = date('Y-m-d');
$lastDateForGraceTime = ($lastdate >= $cudate) ? date('Y-m-d', strtotime($cudate . ' -1 day')) :$lastdate;
// echo $stdate."=>".$lastdateForGraceTime;die;
$lateEntryAllow = $lateEntryObj->getallowedGraseTime($stdate, $lastDateForGraceTime);
// if($usrid=="3732"){
//     echo "actin innnnnn222 => ".print_r($lateEntryAllow);die;
// }
$leaveTypes     = $AttObj->getLeaveTypeArray;
$leave_typ_ary  = [];
$Holidays       = $AttObj->Holidays;    
$WeekendOffs    = $AttObj->WeekOffs; 
$monthlyWeekendOffs = $AttObj->MonthlyWeekOffs;
// echo "<pre>";print_r($monthlyWeekendOffs);die;
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
if($displayType!="summary"){
    $csvFileName = "Monthly attendance report for ".DateTime::createFromFormat('!m', $month)->format('F');
    $exp_filename   = $BASEPATH."uploads/attendance/".$csvFileName." ".$year.".csv";
}else{
    $csvFileName = "Monthly attendance summary report for ".DateTime::createFromFormat('!m', $month)->format('F');
    $exp_filename   = $BASEPATH."uploads/attendance/summary/".$csvFileName." ".$year.".csv";
}
$filedata       = '';
$fileopobj      = fopen ($exp_filename, "w");
if($displayType!="summary"){
    // base initialise started 
    $filedata = ",\"Monthly attendance report for " . DateTime::createFromFormat('!m', $month)->format('F') . " " . $year . "\" \n ";
}else{
    // base initialise started 
    $filedata = ",\"Monthly attendance summary report for " . DateTime::createFromFormat('!m', $month)->format('F') . " " . $year . "\" \n ";
}
$filedata .= " \n ";
if($displayType!="summary"){
    $filedata .= "SlNo,Name,Branch"; 
}else{
    $filedata .= "SlNo, Employee Id, Staff Name, Branch, Department, Designation"; 
}
if($displayType!="summary"){
    for ($j = 1; $j <= $lastday; $j++) {
        $j  = ($j < 10) ? "0" . $j : $j;
        $filedata .= ",".$j;//09-04-2025
    }
}
if($displayType!="summary"){
    $filedata .= ",FD,HD,TL";
}else{
    $filedata .= ",Total Working Days, Total Sundays, Total Holidays, Present Days, Half Days, Absent Days";
}
foreach ($leaveTypes as $rows) {
    $LT_Name = ucwords($rows->LT_Name);   
    $filedata .= ",".$LT_Name[0]."L";
}
if($displayType!="summary"){
    $filedata .= ",CW,CWP,SDL,HI,LS,ES,GS,ADV,NS \n ";
}else{
    $filedata .= ",CW,CWP,SDL,Working Hours Shortfall,GS,ADV,NS \n ";
}

//first time or loaded 0th data then not show the header (heading grid..)
if ($posStart == 0 && $calltype != "upd") {
    if($displayType!="summary"){ 
        echo 'head:[
        {width:47,  type:"ed", align:"center", sort:"na", value:"SlNo"},
        {width:165, type:"ro", align:"left",   sort:"na", value:"Name"},
        {width:150, type:"ro", align:"left",   sort:"na", value:"Branch"}, '; 
      
        for ($j = 1; $j <= $lastday; $j++) {
            $j  = ($j < 10) ? "0" . $j : $j;
            $currentDay = $year.'-'.$month.'-'.$j;
            $daySt = date('D', strtotime($currentDay));
            $daySt = ($daySt == "Sun") ? '<br>[S]' : '';
            echo '{width:30, type:"combo",    align:"center",   sort:"na", value:"' . $j .$daySt. '"},';
        }
    
        echo '{width:40, type:"ro", align:"center", sort:"na", value:"FD"},
          {width:40, type:"ro", align:"center", sort:"na", value:"HD"},
          {width:45, type:"ro", align:"center", sort:"na", value:"TL"},';
    }else{
        $twdLabel = 'TWD <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'twd_Info1\' data-title=\'Total Working Days\' />';
        $tSLabel = 'TS <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info2\' data-title=\'Total Sunday and Weekend off \' />';
        $tHLabel = 'TH <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info3\' data-title=\'Total Holidays\' />';
        $fdDayLabel = 'FD <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info4\' data-title=\'Full Day Present\' />';
        $hdDayLabel = 'HD <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info5\' data-title=\'Half Day Present\' />';
        $abDayLabel = 'TL <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info6\' data-title=\'Absent Days\' />';
        echo 'head:[
        {width:47,  type:"ed", align:"center", sort:"na", value:"SlNo"},
        // {width:"260", type:"ro", align:"left", sort:"na", value:"Employee ID"},
        {width:"*", type:"ro", align:"left", sort:"na", value:"Staff Name"},
        {width:"180", type:"ro", align:"left", sort:"na", value:"Branch"},
        // {width:"180", type:"ro", align:"left", sort:"na", value:"Department"},
        {width:"*", type:"ro", align:"left", sort:"na", value:"Designation"},
        {width:60, type:"ro", align:"center", sort:"na", value:"'.$twdLabel.'"},
        {width:40, type:"ro", align:"center", sort:"na", value:"'.$tSLabel.'"},
        {width:40, type:"ro", align:"center", sort:"na", value:"'.$tHLabel.'"},
        {width:40, type:"ro", align:"center", sort:"na", value:"'.$fdDayLabel.'"},
        {width:40, type:"ro", align:"center", sort:"na", value:"'.$hdDayLabel.'"}, 
        {width:40, type:"ro", align:"center", sort:"na", value:"'.$abDayLabel.'"},
        ';
    }        
    foreach ($leaveTypes as $rows) {

        $LT_Name = ucwords($rows->LT_Name); 
        echo '{width:45,  type:"ro",   align:"center",  sort:"na", value:"'.$LT_Name[0].'L"},';        
        $leave_typ_ary[] = $rows->LT_Id; 
    }
    $gsLabel = 'GS <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info7\' data-title=\'GROSS Salary\' />';
    $advLabel = 'ADV <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info8\' data-title=\'Advance Amount\' />';
    $nsLabel = 'NS <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info9\' data-title=\'NET Salary (Only available on month end day)\' />';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"CW"},';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"CWP"},';
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"SDL"},';
    if($displayType!="summary"){
        $hIDayLabel = 'HI <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info10\' data-title=\'Working Hours Shortfall (In Minutes)\' />';
        $lsLabel = 'LS <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info10\' data-title=\'Total Number of Late Sign in this Month\' />';
        $esLabel = 'ES <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info10\' data-title=\'Total Number of Early Signout in this Month\' />';
        echo '{width:50, type:"ro", align:"center", sort:"na", value:"'.$hIDayLabel.'"},';
        echo '{width:50, type:"ro", align:"center", sort:"na", value:"'.$lsLabel.'"},';
        echo '{width:50, type:"ro", align:"center", sort:"na", value:"'.$esLabel.'"},';
    }else{
        $hIDayLabel = 'HI <img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; width:14px; \' class=\'img_Info tooltipHead\' id=\'img_Info10\' data-title=\'Working Hours Shortfall\' />';
        echo '{width:50, type:"ro", align:"center", sort:"na", value:"'.$hIDayLabel.'"},';
    }
    echo '{width:50, type:"ro", align:"center", sort:"na", value:"'.$gsLabel.'"},';
    echo '{width:60, type:"ro", align:"center", sort:"na", value:"'.$advLabel.'"},';
    echo '{width:55, type:"ro", align:"center", sort:"na", value:"'.$nsLabel.'"},';
    if($displayType!="summary"){
        if ($UserACLObj->view_attendance_detail == 1) {      
            echo '{width:60, type:"ro", align:"center", sort:"na", value:"Details"}';
        } else {
            echo '{width:1, type:"ro", align:"center", sort:"na", value:""}';
        }
    }
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
        $sinInNum       = 0;
        $sinOutNum      = 0;
        $otherMonthFridaySaturday = [];
        $startArray     = $endArray = [];
        $leaveDays      = [];      // 30052025 by Achu
        $leaveDatesWith = [];
        $lcFlag         = false;
        // $sundayCellCount= 0;
        $hasOtherDays = $hasLastWeekDay = $holidaySandwich = $startOfLastWeek = $lastDayOfMonth = $checkOtherDayRuleFile = $previousMonthFirstWeekCheck = false;
        $dep = true;
        $ofc = false;
        // Initialize cell value

        $att_details    = $rw["list"]; // full attendance details 
        $stid           = $rw['ST_Id']; 
        $dpid           = $rw['DP_Id'];
        $ofcid          = $rw['OF_Id'];     // Office ID
        $deptHoliday    = isset($AttObj->Dept_Holidays[$dpid]) ? array_merge((array)$AttObj->Dept_Holidays[$dpid][0]['dates'],(array)$AttObj->Dept_Holidays[$dpid][$stid]['dates']) : []; //09-09-2025

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
        
        if($displayType=="summary"){
            //user based new rows of data..  - '.$rw['US_DOJ'].'
            echo '{ id : 1000' . $usrid . ',
                        data:[
                            "' . $slno . '",
                            // "' . $rw['US_EMPID'] . '",
                            "' . $rw['US_FName'] .' '. $rw['US_LName'].'",
                            "' . $rw['LC_Name'] . '",
                            // "' . $rw['DP_Name'] . '",
                            "' . $rw['DG_Name'] . '",';
$filedata .= ''.$slno.','.$rw['US_EMPID'].','.$rw['US_FName'].' '.$rw['US_LName'].','.$rw['LC_Name'].','.$rw['DP_Name'].','.$rw['DG_Name'].'';//9-4-25                    
        }else{
            echo '{ id : 1000' . $usrid . ',
                        data:[
                            "' . $slno . '",
                            "' . $rw['US_FName'] .' '. $rw['US_LName'].'",
                            "' . $rw['LC_Name'] . '",';
            $filedata .= ''.$slno.','.$rw['US_FName'].' '.$rw['US_LName'].','.$rw['LC_Name'].'';//9-4-25                    
        }
        //serial , name and branch printed above..
        // date wise data grid loading start....
        $notIncrement = false;
        for ($j = 1; $j <= $lastday; $j++) {   
            $earlySoutMinCount = $lateSinMinCount = 0;
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
            $isAfterExit = (!empty($rw['US_ResignDate']) && $cdate >= $rw['US_ResignDate']);
            $isBeforeJoin = (!empty($rw['US_DOJ']) && $cdate < $rw['US_DOJ']);
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
            $minWrkHHrs      = ($minActHrs)/4;
            $new_min_half   = ($newTimeStart == 1) ? ($minWrkHrs-($minTimePresent/2)) : ($minWrkHrs/$minTimePresent); // 18-06-2025
            $new_min_full   = ($newTimeStart == 1) ? ($minWrkHrs-$minTimePresent) : ($minWrkHrs/$minTimePresent); // 18-06-2025
            // echo $minWrkHrs."=>".$new_min_full;die;
            $cellType   = "N"; // NRM -- Normal Holiday
            if (multi_array_search($cdate,$AttObj->RH_Holidays[0]) || multi_array_search($cdate,$AttObj->RH_Holidays[$stid])) {
                $cellType   = "R"; //RH -- Reserved Holiday
                $title      = "Reserved Holiday";
                $cellTypeValue[$j] = $cellType;
                //$holidayList[] = $cdate;
            }
            // echo $hrs .'=>'. $minWrkHrs.'=>'.$new_min_full;die;
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
            // if($usrid=="3685" && $cdate=="2025-07-04"){
            //     echo "leaveTodayTp => ".print_r($approvedLeaveDates);die;
            // }
            $cellValue = '-';
            // check the date in attendance and not in applied leave and minimum hours (half day) completed
            // new section with updated late time calclation by Achu
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
                // $leaveTodayTp   = (array_search($cdate, $approvedLeaveDates)) ? $approvedLeaveDates[$cdate]: ''; // approved leave in this day
                //$sesionAlter = (array_search($cdate, $approvedLeaveDates)) ? $approvedLeaveDates[$cdate]: '';
                $approveLeaves = $LeaveObj->getLeaveReqUser(['user_id'=>$usrid, 'from_date'=>$stdate, 'to_date'=>$lastdate, 'status'=>'2']);
                // if($usrid=="3743" && $cdate=="2025-07-02"){
                //     echo $usrid."=>".$stdate."=>".$lastdate;die;
                // }
                $sesionAlter   = (isset($approveLeaves[$att_details[$cdate]['AT_Date']])) ? $approveLeaves[$att_details[$cdate]['AT_Date']]: '';
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
                                    // if($usrid=="3743" && $cdate=="2025-07-02"){
                                    //     echo $sesionAlter."=>".$session;die;
                                    // }
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
                        $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2,(array)$deptHoliday); //09-09-2025 bilin
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
                        && (array_search($cdate,$rh_holiday)))) { // deptHoliday Added 09-09-2025
                        if ($AttObj->checkRHDate($rh_holiday,$cdate,$usrid) == "RH_Apply") {
                            $LeaveObj->deleteLeaveRecords($usrid, $cdate);
                        }
                    }
                    // check the date is in off day or holiday
                    if ((in_array($cdate, $WeekendOffs[$dpid]) || in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates'])  || in_array($cdate, $deptHoliday) ) && (!in_array($cdate, $AttObj->tmp_hol) && !array_search($cdate, $approvedLeaveDates) && !in_array($cdate,$rh_takendays))) { // 09-09-25 deptHoliday Added
                        //$holiday += 1;
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
                            $holiday += 1; // 11-09-2025
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
                        } else if (in_array($cdate, $deptHoliday)) { //09-09-2025 bilin
                            $holiday += 1; // 11-09-2025
                            // find the date in dept holiday
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
                            if ($att_details[$cdate]['AT_Hours'] >= $minWrkHHrs && $sesionAlter != 'FL')
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
                                        $holiday    += 1; // 11-09-2025
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
                                        $holiday    += 1; // 11-09-2025
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
                                        $holiday    += 1; // 11-09-2025
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
                                        $holiday    += 1; // 11-09-2025
                                    }
                                }
                            } // end 10-09-2025
                            else{
                                if($dpid==66){
                                    $leaveObj = false;
                                    // if($usrid=="2403" && $cdate == "2025-07-06"){
                                    //     echo "<pre>";print_r($att_user_days);die;
                                    // }
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
                                    // if($usrid=="2403" && $cdate=="2025-07-06"){
                                    //     // echo "<pre>";print_r($nextWorkingDay);die;
                                    //     // echo "Leave Obj => ".$prevWorkingDay." => ".$nextWorkingDay;die;
                                    // }
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
            }else{ // old section with normal late time calclation by Achu
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
                        //$m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);                        
                        $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2,(array)$deptHoliday); //09-09-2025 bilin
                        
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
                    if ((in_array($cdate, $WeekendOffs[$dpid]) || in_array($cdate, $Holidays[0]['dates']) || in_array($cdate, $Holidays[$stid]['dates']) || in_array($cdate, $deptHoliday)) && (!in_array($cdate, $AttObj->tmp_hol) && !array_search($cdate, $approvedLeaveDates) && !in_array($cdate,$rh_takendays))) {                        
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
                            $holiday += 1; // 11-09-2025
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
                        } else if (in_array($cdate, $deptHoliday)) { //09-09-2025 bilin
                            $holiday += 1; // 11-09-2025
                            // find the date in dept holiday
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
                        // check the sanwitch dept based 
                        if (in_array($cdate, $AttObj->tmp_hol) && $dpid==66) { // 17-09-2025
                                        $cellValue  = "L";  // count from here
                                        $absent     += 1; 
                                        $absentact  += 1;
                                        $title      = "Leave";
                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
                        } else if (in_array($cdate,$rh_takendays)) {
                            //check the user take rh
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
                                        } else if( $hrs >= $minWrkHrs) {
                                            $half       += 1;
                                            $halfact    += 1;
                                            $absent     += 0.5; 
                                            $absentact  += 0.5; 
                                            $title      = "Half day";
                                            $cellValue  = "P2"; 
                                            $leaveDatesWith[$cdate] = "P2";
                                        } else {
                                            $cellValue  = $hrs;//"L**";
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
                                    /*if($dpid==66){
                                        $cellValue  = "L";   // here is cup
                                        $leaveDays[]   = $cdate;    // 30052025 by Achu
                                        $leaveDatesWith[$cdate] = "L"; // 11062025 by Achu
                                        $absentact  += 1;
                                    }else{*/
                                        $cellValue  = "H";   // here is cup
                                        $holiday    += 1; // 11-09-2025
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
                                        $holiday    += 1; // 11-09-2025
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
                                        $holiday    += 1; // 11-09-2025
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
                                        $holiday    += 1; // 11-09-2025
                                    }
                                }
                            } //10-09-2025
                             else{
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
                        // if($usrid=="3614" && $cdate=="2025-10-05"){
                        //     echo "<pre>";print_r($dayNames);die;
                        // }
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
                            // end duplicate sections... 10-09-2025
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
                        // end duplicate sections... 10-09-2025
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
                                // if($usrid=="3778" && $cdate=="2025-08-03"){
                                //     echo "<pre>";print_r($cmleaveValue);die;
                                // }
                                if($cmleaveValue==0.5){
                                    $sundayCellValue = "C";
                                    $cmpnstry -= 0.5;  
                                }
                                if (!empty($otherMonthFridaySaturday)) {
                                    $prevMonthComSalary = 1;
                                    $cwp +=($cmleaveValue==2)?1:$cmleaveValue;
                                    $cmpnstry -= 1;   
                                }
                            }elseif($sundayCellValue=="P2" || $sundayCellValue=="2HI"){
                                $sundayCellValue = "C";
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
                                // $lcCount += 1;
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
                                    // $lcCount += 1;
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
                                // $lcCount += 1;
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
                if($isBeforeJoin){
                    $cellValue = "L";
                    $AttObj->deleteAttendance($usrid, $cdate);
                    $LeaveObj->deleteLeaveRecords($usrid, $cdate);
                    $AttObj->deleteRHLog($usrid, $cdate);
                }
                if ($isAfterExit || $isBeforeJoin) {
                    if($cellValue=="S"){
                        $cellValue  = "L";
                        $absentact  += 1;
                    }
                }
                $notIncrement = $checkOtherDayRuleFile = false;
                $leaveDays  = $leaveDatesWith = [];
            }
            if ($isAfterExit || $isBeforeJoin) {
                if($cellValue!="L"){
                    $cellValue  = "L";
                    $absentact  += 1;
                }
            }
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
                $lateSinMinCount = $lateSinMin;
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
                $earlySoutMinCount = $earlySoutMin;
                $extrawtime     = ($hrs > $worktime) ? $hrs-$worktime : 0;
                $late_sign      += $lateSinMin; 
                $early_out      += $earlySoutMin;
                $extratime      += $extrawtime;
            }
            if($lateSinMinCount!=0){
                $sinInNum ++;
            }

            if($earlySoutMinCount!=0){
                $sinOutNum ++;
            }
            $absentact = ($isBeforeJoin) ? (int)(date('d', strtotime($cdate))) : $absentact; // 02-02-2026
            // 30052025 by Achu
            if($displayType!="summary"){
                $cellValue = $cellValue;
                echo '"'.$cellValue.'",'; 
                $filedata .= ','.$cellValue.'';//9-4-25  
            }         
        }
        if($lcFlag == true){
            $oldabsentact = $absentact;
            $absentact = $absentact + $cmpnstry;
        }
        if($fullact==0 && $halfact==0){
            $absentact = date('t', strtotime("$year-$month-01")); 
        }
        $absentact = $absentact - $lcCount;
        //echo '"'.$full.'","'.$half.'","'.$absent.'",'; 
        if($displayType!="summary"){
            echo '"'.$fullact.'","'.$halfact.'","'.$absentact.'",'; 
            $filedata .= ','.$fullact.','.$halfact.','.$absentact.'';//9-4-25 
        }else{
            $monthTotalDays = date('t', strtotime("$year-$month-01"));
            $totalHolidaysByMonth = $holiday; // 11-09-2025
            //$totalHolidaysByMonth = (count($Holidays[$stid]['dates']) + count($Holidays[0]['dates']));
            // $twd = implode(',', $monthlyWeekendOffs[$dpid]). ' => '.$totalHolidaysByMonth . ' => ' .($totalHolidaysByMonth - $monthlyWeekendOffs[$dpid]);
            $totalHolidaysIncludeWeekends = $totalHolidaysByMonth + count($monthlyWeekendOffs[$dpid]);
            $twd = $monthTotalDays - $totalHolidaysIncludeWeekends;
            echo '"'.$twd.'","'.count($monthlyWeekendOffs[$dpid]).'","'.$totalHolidaysByMonth.'","'.$fullact.'","'.$halfact.'","'.$absentact.'",'; 
            $filedata .= ','.$twd.','.count($monthlyWeekendOffs[$dpid]).','.$totalHolidaysByMonth.','.$fullact.','.$halfact.','.$absentact.'';//9-4-25  
        }
        // calcualate lop
        //$lop = $lastday-(($half/2)+$full+$holiday);
        // if($cdate=="2025-07-07" && $usrid=="3685"){
        //     echo "<pre> absent Act => ".print_r($leave_typ_ary);die;
        // }
        $lop = $absentact;
        // find the category wise leave list
        foreach ( $leave_typ_ary AS $leaveid ) {

            $leaveCount = (isset($AttRptObj->approve_leaves[$usrid]) && isset($AttRptObj->approve_leaves[$usrid]['count'][$leaveid])) ? $AttRptObj->approve_leaves[$usrid]['count'][$leaveid]['total'] : 0;

            echo '"'.$leaveCount.'",';
            $filedata .= ','.$leaveCount.'';//9-4-25  
            $lop    -= $leaveCount;
        }
        if($dep==true && $ofc==true){
            $finalFlag = ($absentact - $cmpnstry);
            // $finalFlag = $absentact;
            $finalFlag = ($finalFlag>0)?$finalFlag:0;
            $sdl = ($lop > 0) ? ($finalFlag):0; // loss of pay 
        }else{
            $sdl = ($lop > 0) ? ($lop):0; // loss of pay 
        }
        if($lcFlag == true){
            $sdl = $oldabsentact;
        }
        if($newTimeCheckRuleStarts==0){
            if ($hour_incomple > 0 && $worktime > 0 && $lastdate > "2024-12-31") { //15-01-2025 
                $sdl        +=(round($hour_incomple/($worktime/2))/2); 
                // greater than in complete hours (daily work hours /4) consider half day leave that means 
                //2:15 hour to 6:44 = 0.5 leave
                //6:45 - 11:14      = 1 leave 
            }
        }
        $sdl            = round($sdl, 2);
        $late_sign      = round($late_sign);
        $early_out      = round($early_out);
        $hour_incomple  = round($hour_incomple);
        // $sdl = $absentact.'-'.$sdl;
        if($fullact==0 && $halfact==0){
            $sdl = date('t', strtotime("$year-$month-01")); 
        }
        $sdl = $sdl - $lcCount;
        if($displayType!="summary"){
            echo '"'.$cmpnstry.'","'.$cwp.'","'.$sdl.'","'.$hour_incomple.'","'.$sinInNum.'","'.$sinOutNum.'",';
            $filedata .= ','.$cmpnstry.','.$cwp.','.$sdl.','.$hour_incomple.','.$sinInNum.','.$sinOutNum;//9-4-25 
        }else{
            echo '"'.$cmpnstry.'","'.$cwp.'","'.$sdl.'","'.$hour_incomple.'",';
            $filedata .= ','.$cmpnstry.','.$cwp.','.$sdl.','.$hour_incomple;
        }
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
            if($newTimeCheckRuleStarts==1){
                $incompleteMinutes = $hour_incomple;
                $workingMinutesPerDay = 9 * 60;
                $totalWorkingMinutes = $currentMonthTotalDays * $workingMinutesPerDay;
                $perMinuteSalary = $grossSalary / $totalWorkingMinutes;
                $salaryDeduction = $incompleteMinutes * $perMinuteSalary;
                $netSalary = $netSalary - $salaryDeduction;
            }
            $netSalary = ($netSalary>0)?round($netSalary):0;
        }
        if($fullact==0 && $halfact==0){
            $netSalary = 0;
        }
        $attendanceDisplayDate = DateTime::createFromFormat('Y-m', "$year-$month");
        $attendanceDisplayDate->format('Y-m');
        if($rw["US_AttndFlag"] == 1 && date('Y-m', strtotime($rw['US_AttndDate'])) <= $attendanceDisplayDate->format('Y-m')){
            $netSalary = $grossSalary;
        }
        if($displayType!="summary"){
            // detail popup define
            $Infoimg        = '<img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; \' class=\'img_Info\' id=\'img_Info\' title=\'Click here to view more details\'  onclick=\' preTally.UserProfile.reportShowAttendance('.$usrid.');\'  />';
            echo '"'.$grossSalary.'","'.$advanceSalary.'","'.$netSalary.'",';
            if ( $UserACLObj->view_attendance_detail == 1) { // attendance popup restrict
                echo '"'.$Infoimg.'",';
            } else {
                echo '"",';
            }
        }else{
            echo '"'.$grossSalary.'","'.$advanceSalary.'","'.$netSalary.'",';
        }
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

// end the new display sedtion
 /*   
// leave type ids array for old
$LT_Ids = implode(",", $leave_typ_ary);

$currentMonth = date("m");
$mnthyrStr=str_pad($REQUEST['att_month'], 2, "0", STR_PAD_LEFT).'-'.$REQUEST['att_year'];
if($REQUEST['att_year'] && $REQUEST['att_month'] && $mnthyrStr != date('m-Y')){       
    $year = $REQUEST['att_year'];
    $month   = str_pad($REQUEST['att_month'], 2, "0", STR_PAD_LEFT);
    $g_date  = $year.'-'.$month.'-01';
    $lstdate = date("t", strtotime($g_date)); 
} else {    
    $month  = date("m");
    $g_date     =$year.'-'.$month.'-01';
    //$lstdate = date("t", strtotime($g_date));
    $lstdate = date("d")-1;
}
$filter = "";
$orderCol=" LC.LC_Name";
$orderBy=" ASC";
if($_REQUEST["Filters"]){
    $filters = explode(",",$_REQUEST["Filters"]);
    if($filters[0])
        $filter.=" AND CONCAT(US_FName, ' ', US_LName) LIKE '".mysqli_real_escape_string($GLOBALS['con'],ucfirst($filters[0]))."%'";
    if($filters[1]!=0 && $filters[1]!="")
        $filter.= " AND LC.LC_Id=".$filters[1];
    if($filters[2]==1){
        $orderCol=" CONCAT(US_FName, ' ', US_LName)";
    }
    else {
        $orderCol=" LC.LC_Name";
    }
    if($filters[3]=='des')$orderBy=" DESC";
}
    $a_date     = $year."-".$month."-".$lstdate;
    //$UserObj->selectCompanySettings($preTally_user_ofid); // Office ID & Details
    
    $CompSett    = $UserObj->CompanySettingsArray;
    //$AttObj->getHolidays($a_date,$preTally_user_ofid); // Company Holidays List
    //$AttObj->getWeekendOffs($month,$year,$preTally_user_ofid); // Weekend Off List   
    //$AttObj->getOfficeRH($preTally_user_ofid); // RH Off List
       
    //$AttObj->getLeaveType($preTally_user_ofid); // Type Of Leaves
    $AttLv      = $AttObj->getLeaveTypeArray;
    
    // if ( $_GET["count"] > 0) { // if limit passed by user then get find the total
    //     $att_count  = $AttObj->countReptAttendance($month, $year, $preTally_user_ofid, 0, $filter);
    // } else {
        $att_count  = 0;
    //}
    
    //echo $att_count;
    $userFilter = 0;
    if($REQUEST['userFilter']) $userFilter = $REQUEST['userFilter'];
    
    $AttObj->listReptAttendance($month, $year, $preTally_user_ofid, 0, $filter,$_GET["posStart"], $_GET["count"], $userFilter,$CompSett['CS_WrkHrGraceTime'],$orderCol.$orderBy);
    $holiday_count  = 0;
    $WeekendOffs    = array();
    $Att_Obj        = array();
    $Holidays       = array();
    $Holidays       = $AttObj->Holidays;    //var_dump($Holidays[0]['dates']);die();    
    $WeekendOffs    = $AttObj->WeekOffs;    
    $Att_Obj        = $AttObj->RptAttendance;
    $att_count      = ($att_count <= 0 && !empty($Att_Obj)) ? count($Att_Obj):$att_count;
    $daysInMonth    = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    /*if($currentMonth!=$month){*/
    /*$leave_headers="";
    $attendance_headers="";
    foreach($AttLv as $rows){
        $LT_Name = ucwords($rows->LT_Name); 
        //$leave_headers .= '<column width="110" type="ro" align="center" sort="na">Approved'." ".$LT_Name.'</column>';
        $leave_headers .= '{width:45,  type:"ro",   align:"center",  sort:"na", value:"'.$LT_Name[0].'L"},';        
        $LT_Id .= $rows->LT_Id;
        $LT_Id .= ",";
    }
    $LT_Ids = rtrim($LT_Id,","); 
    
    //$leave_headers .= '<column width="75" type="ro" align="center" sort="na">Salary Deductable Leaves</column>';
    $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"SDL"},';
    $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"LS"},';
    $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"ES"},';
    $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"IH"},';
    $attendance_headers .= '{width:60,  type:"ro",   align:"center",  sort:"na", value:"Details"}';   */                       
                /*}*/  
/*echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");*/
//echo '<rows total_count="'.$att_count.'" pos="'.$_GET["posStart"].'">';
    
    /*if($AttObj->verifyMonth("employee_payroll","EP_Month",$month,$preTally_user_ofid,"EP_Year",$year)){ 
        $editable = true;
    }else{
        $editable = false;
    }*/
    
/*echo '{
        "total_count"   :'.$att_count.',
        "pos"           :'.$_GET["posStart"].',';*/

    /*if($_GET["posStart"] == 0 && $_GET["calltype"] != "upd"){
        echo '
            
            head:[
            {width:47,  type:"ed", align:"center", sort:"na", value:"SlNo"},
            {width:165, type:"ro", align:"left",   sort:"na", value:"Name"},
            {width:150, type:"ro", align:"left",   sort:"na", value:"Branch"},
            ';
            for ($j = 1; $j <= $lstdate; $j++) {
                if ($j < 10) {
                    $j = "0" . $j;
                }
                $date =  $j ;
                //echo '<column width="45" type="combo" align="center" sort="na">' . $date . '</column>';
                echo '{width:30, type:"combo",    align:"center",   sort:"na", value:"' . $date . '"}, 
                      ';
            }
       
        echo '{width:40, type:"ro", align:"center", sort:"na", value:"FD"},
              {width:40, type:"ro", align:"center", sort:"na", value:"HD"},
              {width:45, type:"ro", align:"center", sort:"na", value:"TL"},
              ';
              $LT_Id = "";
        echo $leave_headers;  
        echo $attendance_headers;
        echo '],';
    }*/
    
    //echo 'rows:[';
    //echo '<userdata name="leaveTypes">'.json_encode($AttLv).'</userdata>';
    /*$p = $_GET["posStart"]+1;
    if ($Att_Obj){
        $LTId = $LT_Ids;
        $LT_Id = explode(",",$LTId); 
        
        foreach ($Att_Obj as $rw){
            $array_user=array();
            $user_details   = $rw["USR_Details"];
            $att_details    = $rw["ATT_Dates"];  
            $array_user     = array_keys($att_details); 
            $att_attr       = array_values($att_details); 
            $allleaves      = "";
            $sumofAllLeaves = 0;
            $totalLeave     = 0;
            $lopdeduction   = 0;
            $takeHomeSalary = 0;
            $attendanceInfo = '<img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; \' class=\'img_Info\' id=\'img_Info\' title=\'Click here to view more details\'  onclick=\' preTally.UserProfile.reportShowAttendance('.$user_details['US_Id'].');\'  />';
            $lop=0;            
            $AttObj->tmp_rharray=array();                
            //$onedaySal=($user_details['US_GrossSal'])/$daysInMonth;              
            //print_r($att_details);die();

            $AttObj->ApprovedLeaveDays = array();  
            $AttObj->getApprovedLeaveDates($user_details['US_Id'],$month, $year,$LT_Ids);//Leave Type Dates                         
            for($k=0;$k<count($LT_Id);$k++){
                $AttObj->getApprovedLeave($user_details['US_Id'],$month, $year,$LT_Id[$k]);//Sum of Leave Types
                $approvedLeave     = $AttObj->getApprovedLeaveArray[0];
                $approvedLeaveName = $AttObj->getApprovedLeaveArray[1];
                if(($approvedLeave=="null")||($approvedLeave=="")){
                    $approvedLeave=0; 
                }
                $allleaves.=$approvedLeaveName.":".$approvedLeave.",";
            }  
            $half=0;$full=0;$absent=0;
            unset($AttObj->tmp_hol);
            $AttObj->tmp_hol    = array();   
            $allleaves          = rtrim($allleaves,","); 
            $individualLeaves   = explode(",",$allleaves);           
            for($j=0;$j<count($individualLeaves);$j++){  
                $indiLeavesCountAndName = explode(":",$individualLeaves[$j]);
                $sumofAllLeaves=$sumofAllLeaves+$indiLeavesCountAndName[1];
            }   
            $rh_count=0; 
            $stid=$user_details['ST_Id']; 
            $dpid=$user_details['DP_Id'];                            

            /*echo '<row id="' . $user_details['US_Id'] . '">    
                    
                    <cell>' . $p . '</cell>               
                    <cell>' . $user_details['US_FName'] .' '. $user_details['US_LName'].'</cell>
                    <cell>' . $user_details['LC_Name'] . '</cell>';*/
            
            //echo '{ id : 0,
                
//userdata: { "db_table": "balance_sheets", "db_primary": "BS_Id", "db_date": "BS_MDate", "db_status" : "BS_Status" },
//data:[]
 //               },';
           /* echo '{ id : ' . $user_details['US_Id'] . ',
                    data:[
                        "' . $p . '",
                        "' . $user_details['US_FName'] .' '. $user_details['US_LName'].'",
                        "' . $user_details['LC_Name'] . '",';
                    $half           = 0;
                    $full           = 0;
                    $absent         = 0;
                    $holiday_count  = 0;   
                    $late_sign_in=0;     
                    $duty_left=0;                            
                    unset($AttObj->tmp_hol);
                    $AttObj->tmp_hol = array(); 
                    $cellTypeValue = array();
                    $offTypeValue = array();                                        
                    $AttObj->getAdjMnthAttendance($user_details['US_Id'],$month, $year); 
                    $AttObj->getPunchTime($user_details['US_Id'],$month, $year); 
                    if($AttObj->AdjcntAttendance["last"]!=null)                              
                        array_push($array_user,$AttObj->AdjcntAttendance["last"]);
                    if($AttObj->AdjcntAttendance["next"]!=null)
                        array_push($array_user,$AttObj->AdjcntAttendance["next"]);                               
                                 
                    for ($j = 1; $j <= $lstdate; $j++) {                                              
                        $title      = "";
                        $cellValue  = "";
                        $Lbl        = "";
                        //$offTypeValue = array();
                        $AttObj->tmp_rharray = array_merge((array)$AttObj->RH_Holidays[0],(array)$AttObj->RH_Holidays[$stid]);                              
                        $AttObj->getTakenRHBatches($user_details['US_Id']);                            
                        $flp_array  = array_flip($AttObj->RH_Batches);                                        
                        $rh_holiday = array_diff_key($AttObj->tmp_rharray, $flp_array);                                            
                        if ($j < 10) $j = "0" . $j;   
                        
                        $date = $year . "-" . $month . "-" . $j; 
                        if(multi_array_search($date,$AttObj->RH_Holidays[0])||multi_array_search($date,$AttObj->RH_Holidays[$stid])) {
                            $cellType = "R"; //RH -- Reserved Holiday
                        } else {
                            $cellType = "N"; // NRM -- Normal Holiday
                        }
                        $approvedLeaveDates=array();
                            if(!empty($AttObj->ApprovedLeaveDays)) {
                                $approvedLeaveDates=array_column($AttObj->ApprovedLeaveDays, 'LRD_Date');    
                            }
                           $hrs = $att_details[$date]['AT_Hours'];                                                       
                           $array_intr=array();
                           $array_intr=array_intersect($approvedLeaveDates, $array_user);
                           //print_r($array_intr);
                           foreach($array_intr as $days){
                               $chrs = $att_details[$days]['AT_Hours'];
                           $loginTime   =$logoutTime="00:00:01";
                           $loginTime   =$att_details[$days]['AT_SignIn'];
                           $logoutTime  =$att_details[$days]['AT_SignOut'];    
                           
                           $meanDifAM   =abs(strtotime("12:00:00")-strtotime($loginTime));
                           $meanDifPM   =abs(strtotime("12:00:00")-strtotime($logoutTime));
                           $minWrkHrs   =($user_details['US_WrkHours']/2)-$CompSett['CS_WrkHrGraceTime'];
                           $attSession="FL";
                           if($meanDifAM > $meanDifPM)
                               $attSession="FN";
                           else 
                               $attSession="AN";    
                           $key_L      = array_search($days,$approvedLeaveDates);                                    
                           $session    =$AttObj->ApprovedLeaveDays[$key_L]['LRD_Session'];    
                           if (($key = array_search($days, $array_user)) !== false && $session=="FL") {
                                unset($array_user[$key]);
                           }
                           else if (($key = array_search($days, $array_user)) !== false && $chrs < $minWrkHrs && ($session=="FN" || $session=="AN")) {
                               if($attSession==$session){                                         
                                   unset($array_user[$key]);                                   
                               }
                           }
                           }
                        if (in_array($date, $array_user)&& (!multi_array_search($date, $approvedLeaveDates)) && $hrs >= (($user_details['US_WrkHours']/2)-$CompSett['CS_WrkHrGraceTime'])){                                          
                            unset($AttObj->tmp_hol);
                            $AttObj->tmp_hol = array();
                                                            
                            if($hrs >= ($user_details['US_WrkHours']-$CompSett['CS_WrkHrGraceTime'])){
                                $cellValue  = "P";
                                //$title="P";
                                $full       +=1;
                                //echo '<cell title="Present" usid="'.$user_details['US_Id'].'"  cellType="'.$cellType.'"  cellValue="'.$cellValue.'" attdate="'.$date.'" class="green_cell">P</cell>';
                                echo '"P",';
                                if($cellType === "R") $cellTypeValue[$j] = $cellType;
                                //$offTypeValue[$j] = $title;
                            }
                            elseif ($hrs >= (($user_details['US_WrkHours']/2)-$CompSett['CS_WrkHrGraceTime']) && $hrs<($user_details['US_WrkHours']-$CompSett['CS_WrkHrGraceTime'])){
                                $cellValue  = "P2";
                                //$title="H";
                                $half+=1;
                                //echo '<cell  title="Half Day" usid="'.$user_details['US_Id'].'" cellType="'.$cellType.'" cellValue="'.$cellValue.'" attdate="'.$date.'"  class="orange_cell">H</cell>';
                                echo '"P2",';
                                if($cellType === "R") $cellTypeValue[$j] = $cellType;
                                //$offTypeValue[$j] = $title;
                            }else
                            {
                                     $absent+=1;
                                    //$title="Absent";
                                    //$title="A";
                                    $cellValue="L";
                                     echo '"L*",';
                                }
                        } else{ 
                                $AttObj->getRHTakenDays($user_details['US_Id']);
                                $RH_takendays=$AttObj->RH_TaknDays; 
                                $m_Hol = array();                                                               
                                $m_Hol=$WeekendOffs[$dpid];                                                               
                                $m_Hol_2 = array_merge((array)$Holidays[$stid]['dates'],(array)$Holidays[0]['dates']);                                                            
                                $m_Hol_3 = array_merge((array)$WeekendOffs[$dpid],(array)$RH_takendays);                                                          
                                $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);
                                sort($m_Hol);
                                $AttObj->chkadjDays($array_user,$m_Hol,$date);                                   
                            if((!in_array($date, $WeekendOffs[$dpid]))
                                &&(!in_array($date, $Holidays[0]['dates']))
                                &&(!in_array($date, $Holidays[$stid]['dates'])                                
                                &&(!multi_array_search($date, $approvedLeaveDates))
                                &&(!in_array($date, $AttObj->tmp_hol))        
                                &&(multi_array_search($date,$rh_holiday)))){
                                $RH_Status=$AttObj->checkRHDate($rh_holiday,$date,$user_details['US_Id']);
                                if($RH_Status=="RH_Apply"){
                                    $LeaveObj->deleteLeaveRecords($user_details['US_Id'], $date);
                                }
                            }
                            $AttObj->getRHTakenDays($user_details['US_Id']);
                            $RH_takendays=$AttObj->RH_TaknDays; 
                            /*if ($date == "2024-08-25") {
                            echo "--------------------";
                            print_r($WeekendOffs[$dpid]);
                            echo "===================";
                            print_r($AttObj->tmp_hol);
                            echo "++++++++++++++++++++";
                            print_r($array_user);
                            die();
                            }*/
                           /* if ((in_array($date, $WeekendOffs[$dpid])|| in_array($date, $Holidays[0]['dates']) || in_array($date, $Holidays[$stid]['dates']))
                                    &&(!in_array($date, $AttObj->tmp_hol) && !multi_array_search($date, $approvedLeaveDates)&& !in_array($date,$RH_takendays))){                                                                                                    
                                $holiday_count+=1;                                                        
                                
                                if(in_array($date, $WeekendOffs[$dpid])){                                                                                              
                                    $day = strtolower(date("l",strtotime($date)));
                                    if($day=='sunday'){
                                        $cellValue="S";
                                        $Lbl="S";
                                    }else{
                                        $cellValue="W";
                                        $Lbl="W";
                                    }
                                    
                                    
                                }
                                elseif(in_array($date, $Holidays[0]['dates']) || in_array($date, $Holidays[$stid]['dates'])){                                                            
                                    //$title="Holiday";
                                  $Ntitleindx= array_search($date, $Holidays[0]['dates']);
                                  $Stitleindx= array_search($date, $Holidays[$stid]['dates']); 
                                   if($Ntitleindx!==FALSE){$titleindx=$Ntitleindx;$indx=0;}
                                   else{ $titleindx=$Stitleindx; $indx=$stid;}
                                   
                                    $title=$Holidays[$indx]['title'][$titleindx];
                                    $cellValue="H";
                                    $Lbl= "H";
                                }

                                //echo '<cell  title="'.$title.'" usid="'.$user_details['US_Id'].'" cellType="'.$cellType.'" cellValue="'.$cellValue.'" attdate="'.$date.'" class="red_cell">'.$Lbl.'</cell>';
                                echo '"'.$Lbl.'",';
                                if($cellType === "R") $cellTypeValue[$j] = $cellType;
                                if($title) $offTypeValue[$j] = ucfirst($title);

                            } else {  //print_r($AttObj->tmp_hol);                                                                                                          
                                
                                unset($m_Hol);
                                $m_Hol = array();                                                               
                                $m_Hol=$WeekendOffs[$dpid];                                
                               // $m_Hol_1=  array_merge((array)$WeekendOffs[$dpid],(array)$approvedLeaveDates);
                                
                              /*  $m_Hol_2 = array_merge((array)$Holidays[$stid]['dates'],(array)$Holidays[0]['dates']);                                                            
                                $m_Hol_3 = array_merge((array)$WeekendOffs[$dpid],(array)$rhapplicableDays);                                                          
                                $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);*/

                                /*if(in_array($date,$RH_takendays)){
                                    $holiday_count  +=1;  
                                    $rh_count       +=1;
                                    $title          ="Restrited Holiday";
                                    //$title          ="R";
                                    $cellValue      ="H";
                                    $Lbl            ="H";
                                }
                                elseif(is_numeric(array_search($date, $approvedLeaveDates))){
                                    
                                    $Lbl        = "L-";                                                            
                                    $key_H      = array_search($date,$approvedLeaveDates);
                                    $title      = $AttObj->ApprovedLeaveDays[$key_H]['LT_Name'];                                                                    
                                    $cellValue  = $AttObj->ApprovedLeaveDays[$key_H]['LT_Id']; 
                                    $session    =$AttObj->ApprovedLeaveDays[$key_H]['LRD_Session'];
                                    $halftooltip='';$halfTitle='';
                                    if($session!='FL'){
                                        $halftooltip="P2"; 
                                        $halfTitle="Half Day";            
                                        $absent+=0.5;    
                                        if (in_array($date, $array_user)){                                        
                                        $half+=1;
                                        unset($m_Hol);
                                        $m_Hol = array();     
                                        $Lbl="P2";    
                                        }
                                    }else{
                                        $absent+=1;
                                        $Lbl="L";    
                                    }
                                    $words      = explode(' ', $title); // array of word                                    
                                    //$Lbl=strtoupper($words[0][0].$words[1][0].$halftooltip);                                        
                                    $title.=' '.$halfTitle;
                                } else{
                                    $absent+=1;
                                    //$title="Absent";
                                    //$title="A";
                                    $cellValue="L";
                                    $Lbl="L";
                                }  
                                //sort($m_Hol);           
                                //print_r($array_user);
                                //$AttObj->chkNextDay($date,$m_Hol,$array_user);                                
                                //echo '<cell  title="'.$title.'" usid="'.$user_details['US_Id'].'" cellType="'.$cellType.'" cellValue="'.$cellValue.'" attdate="'.$date.'" class="red_cell">'.$Lbl.'</cell>';                                                        
                                echo '"'.$Lbl.'",';
                                if($cellType === "R") $cellTypeValue[$j] = $cellType;
                                
                                if($title) $offTypeValue[$j] = ucfirst($title);
                            }
                        }
                    }
                    
                    
                    $lop = $lstdate-(($half/2)+$full+$holiday_count);
                    $totalLeave = $lop - $sumofAllLeaves;
                    if($totalLeave<0) {
                        $totalLeave=0;
                    }
                    //$lopdeduction=round(($totalLeave*$onedaySal));
                    //$pfesiwfDeduction=$user_details['US_DedEPF']+$user_details['US_DedESI']+$user_details['US_DedLWF'];
                    //$deduction=$lopdeduction+$pfesiwfDeduction;
                    //$takeHomeSalary= round($user_details['US_GrossSal'] -$deduction);

                    /*echo '<cell><![CDATA[<font color="green"> '.$full.'</font>]]></cell>'
                        .'<cell><![CDATA[<font color="orange">'.$half.'</font>]]></cell>'
                        .'<cell><![CDATA[<font color="red">   '.$lop.' </font>]]></cell>';*/
                    
                    /*echo '"'.$full.'","'.$half.'","'.$lop.'",';

                    /*if($currentMonth!=$month){*/
                    /*for($l=0; $l<count($individualLeaves); $l++){  
                        $indiLeavesCountAndName = explode(":",$individualLeaves[$l]);
                        //echo'<cell><![CDATA[<font color="green">'.$indiLeavesCountAndName[1].'</font>]]></cell>';                        
                        echo '"'.$indiLeavesCountAndName[1].'",';                        
                    }
                   /* for($l=0; $l<count($individualLeaves); $l++){  
                        $indiLeavesCountAndName = explode(":",$individualLeaves[$l]);                     
                        echo '"'.$indiLeavesCountAndName[1].'",';                        
                    }*/
                   /* $late_sign_in = (empty($AttObj->lateSignIn)) ? 0: $AttObj->lateSignIn;
                    $early_left = (empty($AttObj->earlySignOut)) ? 0: $AttObj->earlySignOut;
                    $hour_pending = (empty($AttObj->dutyLeft)) ? 0: $AttObj->dutyLeft;
                    //echo '<cell><![CDATA[<font color="red">'.$totalLeave.'</font>]]></cell>';
                    echo '"'.$totalLeave.'",';
                    echo '"'.$late_sign_in.'",';
                    echo '"'.$early_left.'",';
                     echo '"'.$hour_pending.'",';
                    echo '"'.$attendanceInfo.'",';
                    /*}*/
                    //echo'</row>';
                   /* echo '], 
                        userdata:{
                            \'userID\'  :\''.$user_details['US_Id'].'\',
                            \'offType\' :\''.json_encode($offTypeValue).'\',
                            \'cellType\':\''.json_encode($cellTypeValue).'\'     
                        } 
                    },';
                    $p++;
                }
    }else {
/*        echo '{ id : "null_row",
                    data:["","<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>"]';*/
   /* }
//echo '</rows>';
    //warehouse/updateAttReport.php&date=2015-10-05&usid=648&updType=Pre&prevVal=L&cellType=NRM
    //warehouse/updateAttReport.php&date=2015-10-01&usid=138&updType=P&prevVal=L
/*echo '], 
    \'leaveTypes\':\''.json_encode($AttLv).'\',
    \'leaveCount\':\''.count($individualLeaves).'\',
    \'totalDays\':\''.$lstdate.'\',
    \'isEditable\':\''.$editable.'\',    
    \'ALCEdit\':\''.$ACL_Obj->ACL_AttendanceEdt.'\',        
    \'cellDate\':\''.$year.'-'.$month.'\' 
    }';*/
?>