<?php
/**
 * Modified By ArunDev
 */
require_once("connection.php");

class AttendanceClass {

    var $HolidayArray;
    var $Holidays;
    var $Signed_Dates;
    var $Signed_Hours;
    var $AttendanceArray;
    var $RptAttendance;
    var $userAttendance;
    var $getApprovedLeaveArray;
    var $getApprovedLeaveByDateArray; // by Achu
    var $getEmployeePayrollDataArray;
    var $getPayrollManagementHistoryArray;
    var $getPaymodeWiseEmpSalRptArray;
    var $WeekOffs;
    var $MonthlyWeekOffs;
    var $RH_Batches;
    var $RH_Holidays;
    var $Dept_Holidays;
    var $tmp_hol;
    var $tmp_rharray;
    var $RH_TaknDays;
    var $AdjcntAttendance;
    var $AdjcntWeekends;
    var $AdjcntHolidays;
    var $RH_Days;
    var $BSSalRptArray;
    var $lateSignIn;
    var $earlySignOut;
    var $lateSignOut;
    var $dutyLeft;
    var $sql_qry;

    // -----------------------------------------Check Signed In----------------------------------------//
    function checkAttendance($usid) {
        $date = date('Y-m-d');
        $sql = 'SELECT COUNT(*) AS ATT_CNT FROM attendance WHERE US_Id="' . $usid . '" AND AT_Date="' . $date . '" ';
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_row($result);
        return $row[0];
    }

    function CheckSignOut($usid) {
        $date = date('Y-m-d');
        $sql = 'SELECT COUNT(*) AS ATT_CNT FROM attendance WHERE US_Id="' . $usid . '" AND AT_Hours=0 AND AT_Date="' . $date . '" ';
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_row($result);
        return $row[0];
    }

    //-----------------------------------------Sign In----------------------------------------//
    function MarkEntry() {
        $sql = "INSERT INTO attendance ( " . implode(', ', array_keys($this->US_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->US_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
        session_start();
        $_SESSION['attendance_flag'] = '1';
//        echo $sql;
        return 'SignIn Marked Successfully';
    }

    //-----------------------------------------Sign out----------------------------------------//
    function MarkExit($id, $date, $time, $uuid=NULL, $earlySignOutMin=0, $extraTime=0, $ipOut=NULL, $actualDutyTime=0, $actualLoginLogOutTime=NULL, $deviceOut = 0) {
        $sql = "UPDATE attendance SET AT_SignOut='" . $time . "',AT_Status =1 ,AT_SignOutEarly='".$earlySignOutMin."',AT_ExtraTime=ROUND(time_to_sec(TIMEDIFF('" . $time . "',AT_SignIn))/60,2)-$actualDutyTime,AT_IPAddrOut='".$ipOut."', AT_AllotTime='".$actualLoginLogOutTime."',AT_Hours = ROUND(time_to_sec(TIMEDIFF('" . $time . "',AT_SignIn))/60,2),AT_DutyLeft=$actualDutyTime - ROUND(time_to_sec(TIMEDIFF('" . $time . "',AT_SignIn))/60,2),uuid_out='".$uuid."', deviceOut='".$deviceOut."' WHERE US_Id=" . $id . " AND AT_Date='" . $date . "'";
        mysqli_query($GLOBALS['con'], $sql);
        return 'Signout Marked Successfully';
    }

    //-----------------------------------------View Attendance ----------------------------------------//
    function viewAttendance($filt = '') {
        $count = 0;
        $this->AttendanceArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT * FROM attendance " . $filt);
        while ($row = mysqli_fetch_object($result)) {
            $this->AttendanceArray[$count] = $row;
            $count++;
        }
    }

    //----------------------------------------- Delete Attendance ----------------------------------------//
    function deleteAttendance($usid, $date) {
        $sql = 'DELETE FROM attendance WHERE US_Id="' . $usid . '" AND AT_Date="' . $date . '"';
        mysqli_query($GLOBALS['con'], $sql);
    }

    //--------------------------------------Change attendance for half day leave markings---------------------------------//
    function alterAttendance($usid, $date, $session) {
        $sql_select = "SELECT US_LoginTime,US_LogoutTime,US_WrkHours FROM users_auth WHERE US_Id =".$usid;
        $result     = mysqli_fetch_assoc(mysqli_query($GLOBALS['con'], $sql_select));
        if ($result['US_LoginTime'] != "" && $result['US_LogoutTime'] != "" && $result['US_WrkHours'] != "") {
            $wrkHrs     = $result['US_WrkHours'] / 2;
            $delayin    = 0;
            $delayout   = 0;
            if ($session == "AN") { //Adjusting logout time for halfday forenoon
                $loginTime      = $result['US_LoginTime'];
                $logoutTime     = date("H:i:s", strtotime($result['US_LoginTime']) + ($wrkHrs * 60));
                $delayout       = $wrkHrs;
            } else if ($session == "FN") { //Adjusting login time for halfday after noon
                $loginTime      = date("H:i:s", strtotime($result['US_LogoutTime']) - ($wrkHrs * 60));
                $logoutTime     = $result['US_LogoutTime'];
                $delayin        = $wrkHrs;
            }
            $sqlupdate = 'UPDATE attendance SET AT_SignIn="' . $loginTime . '",AT_SignOut="' . $logoutTime . '",AT_Hours=' . $wrkHrs . ', AT_SignInDelay='.$delayin.', AT_SignOutEarly='.$delayout.', AT_AllotTime= '.json_encode(array("in"=>$result['US_LoginTime'], "out"=>$result['US_LogoutTime'], 'whour'=>$result['US_WrkHours'])).', AT_Status="1" WHERE US_Id="' . $usid . '" AND AT_Date="' . $date . '"';
            mysqli_query($GLOBALS['con'], $sqlupdate);
        }
    }

    //----------------------------------------- Adding Special Holidays ----------------------------------------//
    function AddHolidays() {
        $sql = "INSERT INTO holidays  ( " . implode(', ', array_keys($this->AT_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->AT_Data)) . "'" . ")";
        $result = mysqli_query($GLOBALS['con'], $sql);
        return "Holiday Added";
    }

    //----------------------------------------- Update Special Holidays ----------------------------------------//
    function updateHoliday($hdid) {
        $ATData = '';

        foreach ($this->AT_Data as $key => $value) {
            $ATData = $ATData . $key . "='" . $value . "', ";
        }
        $ATData = substr($ATData, 0, -2);
        $sql = "UPDATE holidays SET $ATData WHERE HD_Id=$hdid";
        mysqli_query($GLOBALS['con'], $sql);
        // return 'Holiday Updated Successfully';
    }

    //----------------------------------------- List Holidays ----------------------------------------//
    function viewHolidays($filt = "") {
        $count = 0;
        $this->HolidayArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT holidays.*, dp.DP_Name FROM holidays LEFT JOIN departments as dp ON (dp.DP_Id=holidays.DP_Id)" . $filt . " GROUP BY HD_Id ORDER BY HD_Date ASC");
        while ($row = mysqli_fetch_object($result)) {
            $this->HolidayArray[$count] = $row;
            $count++;
        }
    }

    //----------------------------------------- Check Sunday ----------------------------------------//
    function checkSunday($date) {
        $timestamp = strtotime($date);
        $day = date('D', $timestamp);
        if ($day == "Sun") {
            return true;
        } else {
            return false;
        }
    }

    //----------------------------------------- Check Holiday ----------------------------------------//
    function checkHoliday($date, $deptid =0) {
        $result = mysqli_query($GLOBALS['con'], "SELECT COUNT(*) FROM holidays WHERE HD_Date=" . $date." AND (DP_Id=" . $deptid . " OR DP_Id = 0) ");
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        if ($row[0] == 0) {
            return true;
        } else {
            return false;
        }
    }

    //----------------------------------------- Fetch all Holidays ----------------------------------------//
    function getHolidays($date, $ofid=0, $deptid=0) {
        $first = date('Y-m-1', strtotime($date));
        $last = date('Y-m-t', strtotime($date));
        $lstdate = date('Y-m-d', strtotime('+5 days', strtotime($last)));
        $frmdate = date("Y-m-d", strtotime('-5 days', strtotime($first)));
        $count = 0;
        $this->Holidays = array();
        $holsql = "SELECT ST_Id,HD_Date,HD_Comments FROM holidays WHERE HD_Date BETWEEN '" . $frmdate . "' AND '" . $lstdate . "' AND OF_Id=" . $ofid . "  AND (DP_Id=" . $deptid . " OR DP_Id = 0) AND HD_Type!=3 AND HD_Status=1 ORDER BY ST_Id"; //echo  $holsql;
        $result = mysqli_query($GLOBALS['con'], $holsql);
        while ($row = mysqli_fetch_assoc($result)) {
            $arr_states = array();
            $arr_states = explode(',', $row['ST_Id']);
            foreach ($arr_states as $state_id) {
                $this->Holidays[$state_id]['dates'][] = $row['HD_Date'];
                $this->Holidays[$state_id]['title'][] = $row['HD_Comments'];
                $count++;
            }
        }
    }
    // -------------------------------fetch all holidays based on the dept ------------------------------//
    // ----------------------------- created By Bilin @ 09-09-2025 --------------------------//
    function getDeptHolidays($date, $ofid=0, $deptid=0) {

        $first = date('Y-m-1', strtotime($date));
        $last = date('Y-m-t', strtotime($date));
        $lstdate = date('Y-m-d', strtotime('+5 days', strtotime($last)));
        $frmdate = date("Y-m-d", strtotime('-5 days', strtotime($first)));
        $this->Dept_Holidays = array();
        $holsql = "SELECT ST_Id,HD_Date, DP_Id ,HD_Comments FROM holidays WHERE HD_Date BETWEEN '" . $frmdate . "' AND '" . $lstdate . "' AND OF_Id=" . $ofid;
        if ($deptid > 0) {

            $holsql .="  AND DP_Id=" . $deptid;
        } else {
            $holsql .="  AND DP_Id > 0 ";
        }
        $holsql     .= " AND HD_Type!=3 AND HD_Status=1 ORDER BY DP_Id ASC, ST_Id ASC";
        $result     = mysqli_query($GLOBALS['con'], $holsql);
        while ($row = mysqli_fetch_assoc($result)) {
            $arr_states = array();
            $arr_states = explode(',', $row['ST_Id']);
            foreach ($arr_states as $state_id) {
                $this->Dept_Holidays[$row['DP_Id']][$state_id]['dates'][] = $row['HD_Date'];
                $this->Dept_Holidays[$row['DP_Id']][$state_id]['title'][] = $row['HD_Comments'];
            }
        }
    }

    //----------------------------------------- Fetch Signed Dates ----------------------------------------//
    function getSignedDates($month, $usid) {
        $lstdate = date("Y-m-t", strtotime("-1 " . $month));
        $frmdate = date("Y-m-1", strtotime("-1 " . $month));
        $count = 0;
        $this->Signed_Dates = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT AT_Date,AT_Hours FROM attendance WHERE AT_DATE BETWEEN '" . $frmdate . "' AND '" . $lstdate . "' AND US_Id=" . $usid);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            $this->Signed_Dates[$count] = $row[0];
            $this->Signed_Hours[$count] = $row[1];
            $count++;
        }
    }

    //----------------------------------------- Calcualte Attendance of a month ----------------------------------------//
    function calcAttendance($month, $curdate, $usid) {
        $leave_days = 0;
        $full_days = 0;
        $half_days = 0;
        $off_days = 0;
        $ot_days = 0;
        $lstdate = date("Y-m-t", strtotime("-1 " . $month));
        $frmdate = date("Y-m-1", strtotime("-1 " . $month));
        if ($curdate != "") {
            $lstdate = $curdate;
        }
        $this->getHolidays($month);
        $this->getSignedDates($month, $usid);
        $date = $frmdate;
        $end = $lstdate;

        while ($date <= $end) {
            if (in_array($date, $this->Holidays, $strict = 'false')) {

                if (in_array($date, $this->Signed_Dates, $strict = 'false')) {
                    //echo $date. " Over Time <br/>";    
                    $ot_days = $ot_days + 1;
                } else {
                    // echo $date. " OFF DAY <br/>";            
                }

                $off_days = $off_days + 1;
            } elseif (in_array($date, $this->Signed_Dates, $strict = 'false')) {

                $indx = array_search($date, $this->Signed_Dates);
                if ($this->Signed_Hours[$indx] >= 540) {
                    //echo $date. " FULL DAY <br/>";    
                    $full_days = $full_days + 1;
                } elseif (($this->Signed_Hours[$indx] == 0) || ($this->Signed_Hours[$indx] < 540)) {
                    //  echo $date. " Half Day<br/>"; 
                    $half_days = $half_days + 1;
                }
            } else {
                $leave_days = $leave_days + 1;
                //echo $date. " LEAVE <br/>";
            }
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        }
        $results = array();
        $results['Full_days'] = $full_days;
        $results['Half_days'] = $half_days;
        $results['Off_days'] = $off_days;
        $results['Leave_days'] = $leave_days;
        $results['Over_time'] = $ot_days;
        return $results;
        // print_r($results);
    }

    function countReptAttendance($month, $year, $ofid, $halfday, $filter) {
        $temp_usid = array();
        $halfday = $halfday * 60;
        $lastday = date("Y-m-t", mktime(0, 0, 0, $month, 1, $year));
        $this->RptAttendance = array();
        $sql_user = "SELECT COUNT(US.US_Id) AS CNT FROM `users_auth` AS US LEFT JOIN locations AS LC ON LC.LC_Id = US.LC_Id WHERE (LAST_DAY(US.US_ResignDate)>='" . $lastday . "' OR US.US_ResignFlag = 0) AND (LAST_DAY(US.US_BlkdDate)>='" . $lastday . "' OR US.US_Status = 1) AND US.US_DOJ<='" . $lastday . "' AND US.OF_Id=" . $ofid . " AND US.US_Status != 5  " . $filter;
        $result_count = mysqli_query($GLOBALS['con'], $sql_user);
        $count = mysqli_fetch_assoc($result_count);
        return $count["CNT"];
    }

    //----------------------------------------------------List attendance ---------------------------------------------------------------
    function listReptAttendance($month, $year, $ofid, $halfday, $filter, $pos, $cnt, $usid, $wrkgraceTime, $orderby) {
        $temp_usid = array();
        $halfday = $halfday * 60;
        $lastday = date("Y-m-t", mktime(0, 0, 0, $month, 1, $year));
        $this->RptAttendance = array();
        $userFilter = '';
        if ($usid != 0)
            $userFilter = 'AND US.US_Id = ' . $usid;
        $sql_user = "SELECT US.US_Id, US.OF_Id,US.DP_Id, LC.LC_Name,LC.ST_Id, US.US_FName, US.US_LName,US.US_LoginTime,US.US_LogoutTime,US.US_WrkHours 
                FROM `users_auth` AS US LEFT JOIN locations AS LC ON LC.LC_Id = US.LC_Id WHERE (LAST_DAY(US.US_ResignDate)>='" . $lastday . "' OR US.US_ResignFlag= 0) AND (LAST_DAY(US.US_BlkdDate)>='" . $lastday . "' OR US.US_Status= 1)  AND US.US_DOJ<='" . $lastday . "' AND US.OF_Id=" . $ofid . " AND US.US_Status != 5 " . $filter . " " . $userFilter . " ORDER BY " . $orderby;
        if ( $cnt > 0 ) {
            $sql_user .= " LIMIT " . $pos . "," . $cnt;
        }          
        $result_user = mysqli_query($GLOBALS['con'], $sql_user);
        while ($row_user = mysqli_fetch_assoc($result_user)) {
            $this->RptAttendance[$row_user['US_Id']]["USR_Details"] = $row_user;
            array_push($temp_usid, $row_user['US_Id']);
        }
        $usids = implode(",", $temp_usid);
        /* print $sql1 = "SELECT US.US_Id,AT.AT_Date,AT.AT_SignIn,AT.AT_SignOut, AT.AT_Hours, AT.AT_Status "
          . "FROM `users_auth` AS US "
          . "RIGHT JOIN attendance AS AT ON US.US_Id = AT.US_ID "
          . "WHERE US.US_Id IN(".$usids.") AND (LAST_DAY(US.US_ResignDate)>='".$lastday."' "
          . "OR US.US_ResignFlag= 0) AND  US.US_DOJ<='".$lastday."' "
          . "AND AT.AT_Hours>=((US.US_WrkHours/2)-$wrkgraceTime) AND US.OF_Id=".$ofid." AND MONTH(AT_Date) =".$month." AND YEAR(AT_Date)=".$year; */
        $sql = "SELECT US.US_Id,AT.AT_Date,AT.AT_SignIn,IF(US_WrkHrFlag=1 AND AT.AT_Date>US.US_WrkHrDate,US.US_LogOutTime,AT.AT_SignOut) AS AT_SignOut,IF(US_WrkHrFlag=1 AND AT.AT_Date>US.US_WrkHrDate,US.US_WrkHours,AT.AT_Hours) AS AT_Hours,IF(US_WrkHrFlag=1 AND AT.AT_Date>US.US_WrkHrDate,1,AT.AT_Status) AS AT_Status "
                . " FROM `users_auth` AS US RIGHT JOIN attendance AS AT ON US.US_Id = AT.US_ID WHERE US.US_Id IN(" . $usids . ") AND (LAST_DAY(US.US_ResignDate)>='" . $lastday . "' ". " OR US.US_ResignFlag= 0)  AND (LAST_DAY(US.US_BlkdDate)>='" . $lastday . "' ". "  OR US.US_Status= 1) AND  US.US_DOJ<='" . $lastday . "' AND US.OF_Id=" . $ofid . " AND MONTH(AT_Date) =" . $month . " AND YEAR(AT_Date)=" . $year
                . " AND US.US_Status != 5 AND IF(US_WrkHrFlag=1 AND AT.AT_Date>US.US_WrkHrDate,AT.AT_Hours>=0,AT.AT_Hours>=((US.US_WrkHours/2)-$wrkgraceTime)) ";
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $this->RptAttendance[$row['US_Id']]["ATT_Dates"][$row['AT_Date']] = $row;
        }
    }

    //----------------------------------------------Get attendance details for generate Payroll of employee-------------------------------
    function listReptAttendancePayroll($month, $year, $ofid, $wrkgraceTime) {
        $lastday = date("Y-m-t", mktime(0, 0, 0, $month, 1, $year));
        $this->RptAttendance = array();
        $temp_usid = array();
        $sql_user = "SELECT US.US_Id, US.US_EMPID,US.OF_Id,US.DP_Id, LC.LC_Name,LC.ST_Id, US.US_FName, US.US_LName,US.US_AttndFlag,US_LoginTime,US.US_LogoutTime,US.US_WrkHours,
                SAL.US_GrossSal,SAL.US_CcaSal,SAL.US_DASal,SAL.US_HRASal, SAL.US_ConveySal, SAL.US_EduSal,
                SAL.US_MedSal,SAL.US_MiscSal,SAL.US_BasicSal,SAL.US_DedEPF,SAL.US_DedESI,SAL.US_DedLWF,SAL.US_DedSalTDS,US_DedMealCard,
                SAL.SS_Id,ST.SS_Status 
                    FROM `users_auth` AS US 
                    LEFT JOIN users_salary AS SAL ON  SAL .US_Id = US.US_Id   
                    LEFT JOIN salary_structures AS ST ON  ST .SS_Id = SAL.SS_Id                    
                    LEFT JOIN locations AS LC ON LC.LC_Id = US.LC_Id 
                    WHERE (LAST_DAY(US.US_ResignDate)>='" . $lastday . "' OR US.US_ResignFlag = 0) 
                     AND US.US_DOJ<='" . $lastday . "' AND US.OF_Id=" . $ofid . "  AND US.US_Status != 5 ORDER BY US.US_Id";


        $result_user = mysqli_query($GLOBALS['con'], $sql_user);
        while ($row_user = mysqli_fetch_assoc($result_user)) {
            $this->RptAttendance[$row_user['US_Id']]["USR_Details"] = $row_user;
            array_push($temp_usid, $row_user['US_Id']);
        }
        $usids = implode(",", $temp_usid);
        /* $sql = "SELECT US.US_Id,AT.AT_Date, AT.AT_SignIn,AT.AT_SignOut,AT.AT_Hours, AT.AT_Status FROM `users_auth` AS US "
          . "RIGHT JOIN attendance AS AT ON US.US_Id = AT.US_ID WHERE US.US_Id IN(".$usids.") "
          . "AND (LAST_DAY(US.US_ResignDate)>='".$lastday."' OR US.US_ResignFlag= 0) AND  US.US_DOJ<='".$lastday."' "
          . "AND US.OF_Id=".$ofid." AND MONTH(AT_Date) =".$month." "
          . "AND YEAR(AT_Date)=".$year  ." AND AT.AT_Hours>=((US.US_WrkHours/2)-".$wrkgraceTime.")"; */
        $sql = "SELECT US.US_Id,AT.AT_Date,AT.AT_SignIn,IF(US_WrkHrFlag=1 AND AT.AT_Date>US.US_WrkHrDate,US.US_LogOutTime,AT.AT_SignOut) AS AT_SignOut,IF(US_WrkHrFlag=1 AND AT.AT_Date>US.US_WrkHrDate,US.US_WrkHours,AT.AT_Hours) AS AT_Hours,IF(US_WrkHrFlag=1 AND AT.AT_Date>US.US_WrkHrDate,1,AT.AT_Status) AS AT_Status "
                . " FROM `users_auth` AS US RIGHT JOIN attendance AS AT ON US.US_Id = AT.US_ID WHERE US.US_Id IN(" . $usids . ") AND (LAST_DAY(US.US_ResignDate)>='" . $lastday . "' "
                . " OR US.US_ResignFlag= 0) AND  US.US_DOJ<='" . $lastday . "' AND US.OF_Id=" . $ofid . " AND MONTH(AT_Date) =" . $month . " AND YEAR(AT_Date)=" . $year
                . " AND US.US_Status != 5 AND IF(US_WrkHrFlag=1 AND AT.AT_Date>US.US_WrkHrDate,AT.AT_Hours>=0,AT.AT_Hours>=((US.US_WrkHours/2)-$wrkgraceTime)) ";
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $this->RptAttendance[$row['US_Id']]["ATT_Dates"][$row['AT_Date']] = $row;
        }
    }

    //-------------------------------Get User details  Who does not have salary structure in attandance list-----------------
    function empSalPayroll($month, $year, $ofid) {

        $lastday = date("Y-m-t", mktime(0, 0, 0, $month, 1, $year));
        $this->userAttendance = array();
        $sql_user = "SELECT US.US_Id, US.US_EMPID,US.US_FName, US.US_LName,SAL.US_GrossSal,ST.SS_Status
                    FROM `users_auth` AS US 
                    LEFT JOIN users_salary AS SAL ON  SAL .US_Id = US.US_Id
                    LEFT JOIN salary_structures AS ST ON  ST .SS_Id = SAL.SS_Id
                    WHERE ((MONTH(US.US_ResignDate)>=MONTH('" . $lastday . "') AND YEAR(US.US_ResignDate)>=YEAR('" . $lastday . "')) OR US.US_ResignFlag= 0) AND                     
                     US.US_DOJ<='" . $lastday . "' AND US.OF_Id=" . $ofid . " AND US.US_Status != 5  ORDER BY US.US_Id";
        $result_user = mysqli_query($GLOBALS['con'], $sql_user);
        while ($row_user = mysqli_fetch_assoc($result_user)) {
            $this->userAttendance[$row_user['US_Id']]["USR_Details"] = $row_user;
        }
    }

//---------------------Select Dates of approved leave of selected month----------------------------
    function getApprovedLeaveDates($user_id, $month, $year, $LTId) {
        $this->ApprovedLeaveDays = array();
        $query = "SELECT lt.LT_Name,ld.LRD_Date,lt.LT_Id,ld.LRD_Session
                FROM leave_reqdays ld
                LEFT JOIN leave_request lr on lr.LR_Id = ld.LR_Id
                LEFT JOIN leave_type lt ON ld.LT_Id = lt.LT_Id
                WHERE lr.LR_AppliedFor=" . $user_id . " AND LR_Status=2 AND (MONTH(LRD_Date ) =" . $month . " AND YEAR(LRD_Date) = " . $year . ") AND lt.LT_Id IN (" . $LTId . ")";
        $result = mysqli_query($GLOBALS['con'], $query);
        $count = 0;
        while ($row = mysqli_fetch_row($result)) {
            $this->ApprovedLeaveDays[$count]["LT_Name"] = $row[0];
            $this->ApprovedLeaveDays[$count]["LRD_Date"] = $row[1];
            $this->ApprovedLeaveDays[$count]["LT_Id"] = $row[2];
            $this->ApprovedLeaveDays[$count]["LRD_Session"] = $row[3];
            $count++;
        }
    }

    //---------------------Select sum of approved leave of selected month----------------------------
    function getApprovedLeave($user_id, $month, $year, $LRId) {
        $this->getApprovedLeaveArray = array();
        $query = "SELECT SUM(LRD_Days),lt.LT_Name
                FROM leave_reqdays ld
                LEFT JOIN leave_request lr on lr.LR_Id = ld.LR_Id
                LEFT JOIN leave_type lt ON ld.LT_Id = lt.LT_Id
                WHERE lr.LR_AppliedFor=" . $user_id . " AND lr.LR_Status=2 AND (MONTH(LRD_Date ) =" . $month . " AND YEAR(LRD_Date) = " . $year . ") AND lt.LT_Id=" . $LRId;
        $result = mysqli_query($GLOBALS['con'], $query);
        $this->getApprovedLeaveArray = mysqli_fetch_row($result);
    }

    function getAdjMnthAttendance($userid, $month, $year) {
        $firstDate = $year . "-" . $month . "-01";
        $lastDate = date("Y-m-t", strtotime($firstDate));
        $next_month_ts = strtotime($firstDate . ' +1 month');
        $prev_month_ts = strtotime($firstDate . ' -1 month');
        $next_Ym = date('Y-m', $next_month_ts);
        $prev_Ym = date('Y-m', $prev_month_ts);
        $next = explode("-", $next_Ym);
        $prev = explode("-", $prev_Ym);
        $this->AdjcntAttendance = array();
        $sqllast = "SELECT AT.AT_Date FROM attendance as AT WHERE AT.AT_Date < '" . $firstDate . "' AND MONTH(AT.AT_Date)=" . $prev[1] . " AND YEAR(AT.AT_Date)=" . $prev[0] . " AND  AT.US_Id=" . $userid . " ORDER BY AT.AT_Date DESC LIMIT 0,1";
        $resultlast = mysqli_query($GLOBALS['con'], $sqllast);
        $rowlast = mysqli_fetch_assoc($resultlast);
        $sqlnxt = "SELECT AT.AT_Date FROM attendance as AT WHERE AT.AT_Date > '" . $lastDate . "' AND MONTH(AT.AT_Date)=" . $next[1] . " AND YEAR(AT.AT_Date)=" . $next[0] . " AND  AT.US_Id=" . $userid . " ORDER BY AT.AT_Date ASC LIMIT 0,1";
        $resultnxt = mysqli_query($GLOBALS['con'], $sqlnxt);
        $rownxt = mysqli_fetch_assoc($resultnxt);
        $this->AdjcntAttendance["last"] = $rowlast['AT_Date'];
        $this->AdjcntAttendance["next"] = $rownxt['AT_Date'];
    }
    /**
     * Created By ArunDev
     */
    function getPunchTime($userid, $month, $year) { 
        $firstDate = $year . "-" . $month . "-01";
        if (strtotime($firstDate) < strtotime("2024-08-21")) {
            
           if ($year <= 2024 && $month < 8) {
                $this->lateSignIn = 0;
                $this->earlySignOut = 0;
                $this->lateSignOut = 0;
                $this->dutyLeft = 0;
                return false;
           } else {
                $firstDate = '2024-08-21';
           }
        }
        $lastDate = date("Y-m-t", strtotime($firstDate));
        $sql = "SELECT SUM(AT_SignInDelay > 0) AS AT_SignInDelay,SUM(AT_SignOutEarly > 0) AS AT_SignOutEarly,SUM(CASE WHEN AT_ExtraTime>=0 THEN AT_ExtraTime ELSE 0 END) AS AT_ExtraTime, SUM(CASE WHEN (AT_DutyLeft>=0 AND AT_Hours > 120) THEN AT_DutyLeft ELSE 0 END) AS AT_DutyLeft from `attendance` where AT_Date between '".$firstDate."' and '".$lastDate."' and US_Id='".$userid."'";
        $result = mysqli_query($GLOBALS['con'], $sql); 
        $rows = mysqli_fetch_assoc($result); 
        $this->lateSignIn = $rows['AT_SignInDelay'];
        $this->earlySignOut = $rows['AT_SignOutEarly'];
        $this->lateSignOut = $rows['AT_ExtraTime'];
        $this->dutyLeft = $rows['AT_DutyLeft'];
    }

    //-------------------------------------------Generates salarySlip related information--------------------------------------------
    /* function generateSalarySlip($data){       
      for($i=0;$i<count($data);$i++){
      $valueElements.="(";
      foreach($data[$i] as $key=>$value){
      $keyelements.=$key;
      $keyelements.=",";
      $valueElements.=$value;
      $valueElements.=",";
      }
      $valueElements=rtrim($valueElements, ",");
      $valueElements.=")".",";
      $key=rtrim($keyelements, ",");
      $keyelements="";
      }
      $valueElements=rtrim($valueElements, ",");
      $keyelements=rtrim($keyelements, ",");
      $sql = "INSERT INTO generate_salary_slip ( " . $key . ") VALUES ".$valueElements ;
      mysqli_query($GLOBALS['con'],$sql);
      echo "Report Generated Successfully";
      } */
    //-----------------------------------------Generate payroll/salaryReport of employee----------------------------------------------------

    function generateEmployeePayroll($table, $data) {
        for ($i = 0; $i < count($data); $i++) {
            $valueElements .= "(";
            foreach ($data[$i] as $key => $value) {
                $keyelements .= $key;
                $keyelements .= ",";
                $valueElements .= $value;
                $valueElements .= ",";
            }
            $valueElements = rtrim($valueElements, ",");
            $valueElements .= ")" . ",";
            $key = rtrim($keyelements, ",");
            $keyelements = "";
        }
        $valueElements = rtrim($valueElements, ",");
        $keyelements = rtrim($keyelements, ",");
        $sql = "INSERT INTO  " . $table . "( " . $key . ") VALUES " . $valueElements;
        mysqli_query($GLOBALS['con'], $sql);
        return 1;
    }

    //----------------------------------------Check employee payroll/employee_salary_report exist or not---------------------------------------------------------    
    function verifyMonth($table, $monthField, $month, $ofId, $yearField, $year) {
        $sql = "SELECT COUNT($monthField) FROM " . $table . " WHERE " . $monthField . " = " . $month . " AND OF_Id=" . $ofId . " AND " . $yearField . "=" . $year;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        $count = $row[0];
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    //---------------------------------------View payroll report of employee-----------------------------------------------------------------------------
    function listEmpPayrollReport($month, $preTallyofid, $year, $filt, $order, $start, $end) {
        $count = 0;
        if (is_numeric($month)) {
            $this->listEmpPayrollReportArray = array();
            $query = "SELECT EP.EP_Id,EP.US_Id,EP.US_FName,EP.US_LName,EP.LC_Name,
                            EP.US_GrossSal,EP.EP_Year,EP.EP_Month,EP.EP_Lop,EP.US_DedEPF,
                            EP.US_DedESI,EP.US_DedLWF,EP.EP_TakehomeSal,EP.US_HRASal,
                            EP.US_ConveySal,EP.US_EduSal,EP.US_CcaSal,EP.US_MedSal,
                            EP.EP_Otherallowance,EP.US_BasicSal,EP.EP_Lop,
                            EP.EP_Proftax,EP.EP_MealCard,EP.EP_SalTds,EP.EP_ProfTds,EP.EP_Salaryadvance,EP.EP_Loan,
                            EP.EP_EmpConEPF,EP.EP_EmpConESI,EP.EP_EmpConLWF,EP.EP_AdjstmntAddition,EP.EP_AdjstmntDeduction,
                            EP.EP_SalDeductableLeave,EP.EP_PFESI_Sal,SS.SS_DedProfTDS,SS.SS_DedProfTDS_Type,SS.SS_DedESI,
                            SS.SS_DedESI_Type,SS_DedEPF,SS_DedEPF_Type,SS_DedLWF_Type,
                            SS.SS_DedLWF,SS.SS_EmpConEPF,SS.SS_EmpConEPF_Type,SS.SS_EmpConESI,
                            SS.SS_EmpConESI_Type,SS.SS_EmpConLWF,SS.SS_EmpConLWF_Type,GROUP_CONCAT(EPH_ColLabel) AS EPH_ColLabel,
                            UA.US_AttndFlag 
                            FROM employee_payroll AS EP 
                                LEFT JOIN users_auth UA ON UA.US_Id = EP.US_Id
                                LEFT JOIN users_salary US ON US.US_Id = EP.US_Id
                                LEFT JOIN salary_structures SS ON SS.SS_Id = US.SS_Id
                                LEFT JOIN payroll_management_history PH ON PH.EP_Id = EP.EP_Id
                                WHERE EP.EP_Month=" . $month . " AND UA.US_Status != 5
                                    AND EP.OF_Id=" . $preTallyofid . " AND EP.EP_Year=" . $year . " " . $filt . " 
                                    GROUP BY EP.EP_Id  " . $order . "  LIMIT " . $start . "," . $end;
            $result = mysqli_query($GLOBALS['con'], $query);
            while ($row = mysqli_fetch_object($result)) {
                $this->listEmpPayrollReportArray[$count] = $row;
                $count++;
            }
        }
    }

    function countEmpPayrollReport($month, $preTallyofid, $year, $filter) {
        $count = 0;
        if (is_numeric($month)) {
            $this->listEmpPayrollReportArray = array();

            $query = "SELECT COUNT(Distinct EP.EP_Id) AS PAY_COUNT 
                            FROM employee_payroll AS EP 
                                LEFT JOIN users_auth UA ON UA.US_Id = EP.US_Id
                                LEFT JOIN users_salary US ON US.US_Id = EP.US_Id
                                LEFT JOIN salary_structures SS ON SS.SS_Id = US.SS_Id
                                LEFT JOIN payroll_management_history PH ON PH.EP_Id = EP.EP_Id
                                WHERE EP.EP_Month=" . $month . " AND UA.US_Status != 5
                                    AND EP.OF_Id=" . $preTallyofid . "
                                    AND EP.EP_Year=" . $year . " " . $filter;
            $result = mysqli_query($GLOBALS['con'], $query);
            $row = mysqli_fetch_assoc($result);
            return $row["PAY_COUNT"];
        }
    }

    function sumPayrollFooter($month, $preTallyofid, $year, $filt) {
        $count = 0;
        if (is_numeric($month)) {
            $this->payrollFooterArray = array();
            $query = "SELECT 
                            SUM(EP.US_BasicSal) AS Basic,SUM(EP.US_HRASal) AS HRA,SUM(EP.US_CcaSal) AS CCA,SUM(EP.US_ConveySal) AS Convey,
                            SUM(EP.US_EduSal) AS Edu,SUM(EP.US_MedSal) AS Medic,SUM(EP.EP_Otherallowance) AS Other,
                            SUM(EP.US_GrossSal) AS Gross,SUM(EP.EP_PFESI_Sal) AS PFESI_Sal,SUM(EP.US_DedEPF) AS DedEPF,SUM(EP.US_DedESI) AS DedESI,
                            SUM(EP.US_DedLWF) AS DedLWF,SUM(EP.EP_Lop) AS EP_Lop,SUM(EP.EP_Proftax) AS EP_Proftax,
                            SUM(EP.EP_SalTds) AS EP_SalTds,SUM(EP.EP_ProfTds) AS EP_ProfTds,SUM(EP.EP_Salaryadvance) AS SalAdv,SUM(EP.EP_MealCard) AS EP_MealCard,SUM(EP.EP_Loan) AS Loan,SUM(EP.EP_AdjstmntAddition) AS Addition,
                            SUM(EP.EP_AdjstmntDeduction) AS Deduct,SUM(EP.EP_TakehomeSal) AS THS 
                            FROM employee_payroll AS EP 
                                LEFT JOIN users_auth UA ON UA.US_Id = EP.US_Id
                                LEFT JOIN users_salary US ON US.US_Id = EP.US_Id
                                LEFT JOIN salary_structures SS ON SS.SS_Id = US.SS_Id                                
                                WHERE EP.EP_Month=" . $month . " AND UA.US_Status != 5
                                    AND EP.OF_Id=" . $preTallyofid . " AND EP.EP_Year=" . $year . " " . $filt;

            $result = mysqli_query($GLOBALS['con'], $query);
            $row = mysqli_fetch_object($result);
            $this->payrollFooterArray[] = $row;
        }
    }

    //-------------------------------------------Update mail status ----------------------------------------------
    function changeMailStatus($filter) {
        $query = "UPDATE employee_salary_report set ESR_Status=1 WHERE ESR_Id=" . $filter;
        mysqli_query($GLOBALS['con'], $query);
    }

    //---------------------------------------------------Get the mail id of the user to sent salary slip------------------------------------
    function getMailId($filter) {
        $count = 0;
        $this->getMailIdArray = array();
        $query = "SELECT UA.US_Email,ESR.ESR_Id,ESR.US_FName,ESR.US_LName
                    FROM employee_salary_report ESR LEFT JOIN users_auth UA
                    ON ESR.US_Id= UA.US_Id WHERE ESR.ESR_Id IN(" . $filter . ") AND UA.US_Status != 5";
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->getMailIdArray[$count] = $row;
            $count++;
        }
    }

    function saveSalaryMailDeatils() {
       $sql = "INSERT INTO salary_slip_tokens( " . implode(', ', array_keys($this->mail_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->mail_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
    }

    //-----------------------------------Select salary related data of employe to produce pdf---------------------------
    function salarySlipPdfData($password) {
        $this->salarySlipPdfDataArray = array();
       $query = "SELECT UA.US_EMPID,ESR.US_FName, ESR.US_LName, DS.DG_Name, DM.DP_Name, ESR.ESR_Month, ESR.ESR_Year, ESR.US_BasicSal, ESR.US_HRASal, ESR.US_ConveySal, ESR.US_MedSal, ESR.US_GrossSal, ESR.ESR_Lop, ESR.ESR_Id, ESR.US_DedEPF, ESR.US_DedESI, ESR.US_DedLWF, AC.US_AccNo, AC.US_Bankname, AC.US_BankBranch,ESR.ESR_Proftax,
                            ESR.ESR_SalTds,ESR.ESR_ProfTds,ESR.ESR_Salaryadvance,ESR.ESR_Loan,ESR.ESR_Otherallowance,ESR.ESR_TakeHomeSalary,ESR.US_CcaSal,ESR.ESR_AdjstmntAddition,ESR.ESR_AdjstmntDeduction,ESR.US_EduSal,
                            ESR.ESR_TotWrkDays,ESR.ESR_PrsntDays,ESR.ESR_HlfDays,ESR.ESR_LOPDays,OF1.OF_Logo,OF1.OF_Name   
                            FROM salary_slip_tokens ST
                            LEFT JOIN employee_salary_report ESR ON ST.ESR_Id = ESR.ESR_Id
                            LEFT JOIN users_auth UA ON ESR.US_Id = UA.US_Id
                            LEFT JOIN users_salary AS US ON US.US_Id = ESR.US_Id
                            LEFT JOIN users_accounts AS AC ON AC.US_Id = ESR.US_Id
                            LEFT JOIN offices AS OF1 ON OF1.OF_Id = UA.OF_Id
                            LEFT JOIN designations DS ON DS.DG_Id = UA.DG_Id
                            LEFT JOIN departments DM ON DM.DP_Id = UA.DP_Id
                            WHERE ST.SSS_Token ='" . $password . "' AND UA.US_Status != 5";
        $result = mysqli_query($GLOBALS['con'], $query);
        $this->salarySlipPdfDataArray = mysqli_fetch_assoc($result);
    }

    //-----------------------------------Check for already created Salary Slip file------------------------------------//
    function checkSalarySlip($password) {
        $query = "SELECT SSS_FileName FROM salary_slip_tokens WHERE SSS_Token='" . $password . "'";
        $result = mysqli_query($GLOBALS['con'], $query);
        $arr = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $arr["SSS_FileName"];
    }

    //--------------------------------update slip name for the token-------------------------------------//
    function updateSlipName($gid, $filename) {
        $query = "UPDATE salary_slip_tokens SET SSS_FileName='" . $filename . "' WHERE SSS_Token='" . $gid . "'";
        mysqli_query($GLOBALS['con'], $query);
    }

    // -------------------------------------Select leave_type baced on office -------------------------------------------------------
    function getLeaveType($ofid) {
        $this->getLeaveTypeArray = array();
        $query = "SELECT LT_Id,LT_Name FROM leave_type WHERE OF_Id=" . $ofid . " AND LT_LOP=0 AND LT_Status=1";
        $result = mysqli_query($GLOBALS['con'], $query);
        $count = 0;
        while ($row = mysqli_fetch_object($result)) {
            $this->getLeaveTypeArray[$count] = $row;
            $count++;
        }
    }

    function getEligibleLeaves($ofid) {
        $this->LeaveArray = array();
        $LeaveTypes = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT LT_Id,LT_Name,ES_Status FROM leave_type WHERE OF_Id=" . $ofid . " AND LT_LOP=0 AND LT_Status=1");
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $empstats = array();
            $esstatusarray = array();
            $esstatusarray = explode(",", $row['ES_Status']);

            for ($k = 0; $k < count($esstatusarray); $k++) {
                $leaveTypearray = explode(":", $esstatusarray);
                if ($leaveTypearray[2] == 1)
                    array_push($empstats, $leaveTypearray);
            }
            $LeaveTypes[$i]['LT_Id'] = $row['LT_Id'];
            $LeaveTypes[$i]['EMP_Stats'] = $empstats;
            $i++;
        }
    }

    function chkNextDay($date, $Holidays, $array_user) {
        $nxtday = date('Y-m-d', strtotime('+1 day', strtotime($date)));
        $prevday = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        $lastholiday = $this->nextHoliday($Holidays, $date);
        $nxtdaywrking = date('Y-m-d', strtotime('+1 day', strtotime($lastholiday)));
        if (in_array($nxtdaywrking, $array_user)) {
            //die($nxtdaywrking);
            unset($this->tmp_hol);
            $this->tmp_hol = array();
            return;
        }
        if (in_array($nxtday, $Holidays)) {
            if (!in_array($nxtday, $this->tmp_hol)) {
                array_push($this->tmp_hol, $nxtday);
            }
            return $this->chkNextDay($nxtday, $Holidays, $array_user);
        } else {
            if (in_array($date, $array_user)) {
                unset($this->tmp_hol);
                $this->tmp_hol = array();
            }
            // print_r($this->tmp_hol);     
            return;
        }
    }

    function nextHoliday($array, $date) {
        foreach ($array as $a) {
            if (strtotime($date) <= strtotime($a))
                return $a;
        }
        //return last($array);
    }

    function chkadjDays($attendance_array, $holiday_list, $date) {//for holidays
        //print_r($holiday_list);
        //find adjacent working days to the holidays (previous and next working days)          
        $nxtwrkingday = $this->findNxtWrkingDay($attendance_array, $holiday_list, $date); //function_call
        $prevwrkingday = $this->findPrevWrkingDay($attendance_array, $holiday_list, $date); //function_call 
        if ((!in_array($nxtwrkingday, $attendance_array) ) && (!in_array($prevwrkingday, $attendance_array))) {
            array_push($this->tmp_hol, $date);
        }
    }

    function findNxtWrkingDay($attendance_array, $holiday_list, $date) {
        $nxtday = date('Y-m-d', strtotime('+1 day', strtotime($date)));
        if (in_array($nxtday, $attendance_array)) {
            return $nxtday;
        } else if (in_array($nxtday, $holiday_list)) {
            return $this->findNxtWrkingDay($attendance_array, $holiday_list, $nxtday);
        } else {
            return $nxtday;
        }
    }

    function findPrevWrkingDay($attendance_array, $holiday_list, $date) {
        $prevday = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        if (in_array($prevday, $attendance_array)) {
            return $prevday;
        } else if (in_array($prevday, $holiday_list)) {
            return $this->findPrevWrkingDay($attendance_array, $holiday_list, $prevday);
        } else
            return $prevday;
    }

    function getStates($ofid) {
        $count = 0;
        $this->StateArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT DISTINCT ST.ST_Id,ST.ST_Name FROM locations as LC "
                . "LEFT JOIN states as ST ON LC.ST_Id = ST.ST_Id "
                . " WHERE LC.LC_Status=1 AND ST.ST_Status=1 AND LC.OF_Id = $ofid ORDER BY ST_Name");
        while ($row = mysqli_fetch_object($result)) {
            $this->StateArray[$count] = $row;
            $count++;
        }
    }

    //-----------------------------------------------Changes in payroll empPayrollGrid are saving-----------------------------------------------
    function createPayrollHistory() {
        $sql = "INSERT INTO payroll_management_history ( " . implode(', ', array_keys($this->payroll_History_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->payroll_History_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
    }

    //---------------------------------------------Update the payroll related data of employee----------------------------------------
    function changeEmployeePayrollData($EP_Id, $month, $year) {
        $payrollData = '';
        foreach ($this->payroll_Data as $key => $value) {
            $payrollData = $payrollData . $key . "=" . $value . ", ";
        }
        $payrollData = substr($payrollData, 0, -2);
        $sql = "UPDATE employee_payroll SET " . $payrollData . " WHERE EP_Id=" . $EP_Id . " AND EP_Month=" . $month . " AND EP_Year=" . $year;
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result > 0) {
            return ' payroll Updated Successfully';
        }
    }

    //-------------------------------------------------Get the updated payroll data of employee to generate Salary Report---------
    function getEmployeePayrollData($month, $year, $ofid) {
        $count = 0;
        $this->getEmployeePayrollDataArray = array();
        $query = "SELECT * FROM employee_payroll WHERE OF_Id= " . $ofid . " AND EP_Month=" . $month . " AND EP_Year=" . $year;
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->getEmployeePayrollDataArray[$count] = $row;
            $count++;
        }
    }

    //-----------------------Count the employee Salary Report----------------------------------------------------------
    function CountEmpSalReport($month, $preTally_user_ofid, $year, $filter) {
        $count = 0;
        $this->listEmpSalReportArray = array();
        if (is_numeric($month)) {
            $query = "SELECT COUNT(ESR_Id) AS SAL_COUNT FROM employee_salary_report WHERE
                ESR_Month=" . $month . " AND OF_Id=" . $preTally_user_ofid . " AND ESR_Year=" . $year . " " . $filter;
            $result = mysqli_query($GLOBALS['con'], $query);
            $row = mysqli_fetch_assoc($result);
            return $row["SAL_COUNT"];
        }
    }

    //-----------------------List the employee Salary Report----------------------------------------------------------
    function listEmpSalReport($month, $preTally_user_ofid, $year, $filter, $order, $posStart, $end) {
        $count = 0;
        $this->listEmpSalReportArray = array();
        if (is_numeric($month)) {
            $query = "SELECT ESR_Id,US_Id,US_FName,US_LName,LC_Name,US_GrossSal,ESR_Year,ESR_Month,ESR_Lop,US_DedEPF,US_DedESI,US_DedLWF,ESR_TakeHomeSalary,US_HRASal,US_ConveySal,US_EduSal,US_CcaSal,US_MedSal,ESR_Otherallowance,US_BasicSal,ESR_Lop,ESR_Proftax,ESR_ProfTds,ESR_SalTds,ESR_MealCard,ESR_Salaryadvance,ESR_Loan,ESR_AdjstmntAddition,ESR_AdjstmntDeduction,ESR_Status,ESR_PFESI_Sal FROM employee_salary_report WHERE
                ESR_Month=" . $month . " AND OF_Id=" . $preTally_user_ofid . " AND ESR_Year=" . $year . " " . $filter . " " . $order . " LIMIT " . $posStart . "," . $end;
            $result = mysqli_query($GLOBALS['con'], $query);
            while ($row = mysqli_fetch_object($result)) {
                $this->listEmpSalReportArray[$count] = $row;
                $count++;
            }
        }
    }

    //-----------------------Count the employee Salary Report----------------------------------------------------------
    function CountEmpSalPFESIReport($month, $preTally_user_ofid, $year, $filter) {
        $count = 0;
        $this->listEmpSalPFReportArray = array();
        if (is_numeric($month)) {
            $query = "SELECT COUNT(ESR.ESR_Id) AS SAL_COUNT 
                            FROM employee_salary_report AS ESR
                                LEFT JOIN users_accounts as UA ON UA.US_Id = ESR.US_Id
                                LEFT JOIN employee_payroll AS EP ON EP.US_Id = ESR.US_Id AND ESR.ESR_Month = EP.EP_Month AND ESR.ESR_Year = EP.EP_Year
                                            WHERE  ESR_Month=" . $month . " 
                                                AND ESR.OF_Id=" . $preTally_user_ofid . " 
                                                AND ESR_Year=" . $year . " 
                                                " . $filter;
            $result = mysqli_query($GLOBALS['con'], $query);
            $row = mysqli_fetch_assoc($result);
            return $row["SAL_COUNT"];
        }
    }

    //-----------------------List the employee Salary Report----------------------------------------------------------
    function listEmpSalPFESIReport($month, $preTally_user_ofid, $year, $filter, $posStart, $end) {
        $count = 0;
        $this->listEmpSalPFReportArray = array();
        if (is_numeric($month)) {

            $query = "SELECT ESR.ESR_Id,ESR.US_Id,ESR.US_FName,ESR.US_LName,ESR.LC_Name,ESR.ESR_PFESI_Sal,
                        UA.US_PFNo,UA.US_ESI,
                        EP.EP_SalDeductableLeave,EP.EP_WorkDays     
                            FROM employee_salary_report AS ESR
                                LEFT JOIN users_accounts as UA ON UA.US_Id = ESR.US_Id
                                LEFT JOIN employee_payroll AS EP ON EP.US_Id = ESR.US_Id AND ESR.ESR_Month = EP.EP_Month AND ESR.ESR_Year = EP.EP_Year
                                    WHERE ESR_Month=" . $month . " 
                                        AND ESR.OF_Id=" . $preTally_user_ofid . " 
                                        AND ESR_Year=" . $year . " 
                                        " . $filter . " LIMIT " . $posStart . "," . $end;


//                $query="SELECT ESR_Id,US_Id,US_FName,US_LName,LC_Name,US_GrossSal,ESR_Year,ESR_Month,ESR_Lop,US_DedEPF,US_DedESI,US_DedLWF,ESR_TakeHomeSalary,US_HRASal,US_ConveySal,US_EduSal,US_CcaSal,US_MedSal,ESR_Otherallowance,US_BasicSal,ESR_Lop,ESR_Proftax,ESR_ProfTds,ESR_SalTds,ESR_MealCard,ESR_Salaryadvance,ESR_Loan,ESR_AdjstmntAddition,ESR_AdjstmntDeduction,ESR_Status,ESR_PFESI_Sal FROM employee_salary_report WHERE
//                ESR_Month=".$month." AND OF_Id=".$preTally_user_ofid." AND ESR_Year=".$year." ".$filter." ". $order ." LIMIT ".$posStart.",".$end;
            $result = mysqli_query($GLOBALS['con'], $query);
            while ($row = mysqli_fetch_object($result)) {
                $this->listEmpSalPFReportArray[$count] = $row;
                $count++;
            }
        }
    }

    //-----------------------Sum the employee Salary Footer----------------------------------------------------------
    function listEmpSalFooter($month, $preTally_user_ofid, $year) {

        $this->salRptFooterArray = array();
        if (is_numeric($month)) {
            $query = "SELECT SUM(US_GrossSal) AS Gross,SUM(US_DedEPF) AS DedEPF,SUM(US_DedESI) AS DedESI,SUM(US_DedLWF) AS DedLWF,SUM(ESR_TakeHomeSalary) AS THS,SUM(US_HRASal) AS HRA,
                  SUM(US_ConveySal) AS Convey,SUM(US_EduSal) AS Edu,SUM(US_CcaSal) AS CCA,SUM(US_MedSal) AS Med,SUM(ESR_Otherallowance) AS Other,
                  SUM(US_BasicSal) AS Basic,SUM(ESR_Lop) AS LOP,SUM(ESR_Proftax) AS Ptax,SUM(ESR_ProfTds) AS PTDS,SUM(ESR_SalTds) AS Saltds,SUM(ESR_MealCard) AS Meal,
                  SUM(ESR_Salaryadvance) AS SalAdv,SUM(ESR_Loan) AS Loan,SUM(ESR_AdjstmntAddition) AS AdjAdd,SUM(ESR_AdjstmntDeduction) AS AdjDeduct,ESR_Status,SUM(ESR_PFESI_Sal) AS PFESI FROM employee_salary_report WHERE
                ESR_Month=" . $month . " AND OF_Id=" . $preTally_user_ofid . " AND ESR_Year=" . $year;
            $result = mysqli_query($GLOBALS['con'], $query);
            $row = mysqli_fetch_assoc($result);
            $this->salRptFooterArray = $row;
        }
    }

    //--------------------Fetch data for Balance Sheet Entries--------------------------------//
    function getBSSalReport($month, $preTally_user_ofid, $year, $salItem, $esrids) {
        $count = 0;
        $this->BSSalReportArray = array();
        if (is_numeric($month)) {
            $query = "SELECT ESR.ESR_Id,ESR.US_Id,ESR.US_FName,ESR.US_LName,
                    ESR.LC_Name,ESR.ESR_TakeHomeSalary,DS.DS_Id,UA.LC_Id  
                    FROM employee_salary_report AS ESR 
                    LEFT JOIN users_auth UA ON UA.US_Id = ESR.US_Id                    
                    LEFT JOIN descriptions DS ON (CONCAT(ESR.US_FName,' ',SUBSTRING(ESR.US_LName,1,1))=DS.DS_Description AND DS.DS_Id IS NOT NULL AND DS.IT_Id=" . $salItem . ")  WHERE ESR_BSStatus=0 
                    AND ESR_Month=" . $month . " AND ESR.OF_Id=" . $preTally_user_ofid . " AND ESR.ESR_Year=" . $year . " AND ESR.ESR_Id IN (" . $esrids . ") AND UA.US_Status != 5 GROUP BY ESR.US_Id";
            $result = mysqli_query($GLOBALS['con'], $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $this->BSSalReportArray[$count] = $row;
                $count++;
            }
        }
    }

    function getDescriptions($Name, $salItem) {
        $this->BSSalAdvArray = array();
        $query = "SELECT DS.DS_Id  FROM descriptions AS DS  WHERE DS_Description='" . $Name . "' AND DS.DS_Id IS NOT NULL AND DS.IT_Id=" . $salItem;
        $result = mysqli_query($GLOBALS['con'], $query);
        $row = mysqli_fetch_row($result);
        return $row[0];
    }

    function getSalItem($preTally_user_ofid) {
        $query = "SELECT CS_SalItemId FROM company_settings WHERE OF_Id=" . $preTally_user_ofid;
        $result = mysqli_query($GLOBALS['con'], $query);
        $row = mysqli_fetch_assoc($result);
        return $row['CS_SalItemId'];
    }

    function getSalAdvItem($preTally_user_ofid) {
        $query = "SELECT CS_SalAdvItemId FROM company_settings WHERE OF_Id=" . $preTally_user_ofid;
        $result = mysqli_query($GLOBALS['con'], $query);
        $row = mysqli_fetch_assoc($result);
        return $row['CS_SalAdvItemId'];
    }

    function updateBSSalaryRptStatus($esrids) {
        $sqlupd = "UPDATE employee_salary_report SET ESR_BSStatus=1 WHERE ESR_Id IN (" . $esrids . ")";
        mysqli_query($GLOBALS['con'], $sqlupd);
    }

    function createSalaryBSEntries() {
        $entryarray = array();
        $sqlpart1 = "INSERT INTO balance_sheets (" . implode(array_keys($this->BSSalRptArray[0]), ",") . ") VALUES ";
        $keyString = "";
        $valueString = "";
        foreach ($this->BSSalRptArray as $entryarray) {
            $valueString .= "(" . implode(array_values($entryarray), ",") . "),";
        }
        $sqlinsert = $sqlpart1 . rtrim($valueString, ",");
        mysqli_query($GLOBALS['con'], $sqlinsert);

        if ($entryarray['CHQ_Number'] != '') {
            $sql = "UPDATE bank_cheque_leafs SET CL_Status = 2 WHERE CL_Id=" . $entryarray['CHQ_Number'];
            mysqli_query($GLOBALS['con'], $sql);
        }
        return "Balance Sheet Entries Created";
    }

    function createSalaryAdvBSEntries() {
        $entryarray = array();
        if ($this->BSSalRptArray['CHQ_Number'] != '') {
            $sql = "UPDATE bank_cheque_leafs SET CL_Status = 2 WHERE CL_Id=" . $this->BSSalRptArray['CHQ_Number'];
            mysqli_query($GLOBALS['con'], $sql);
        }
        $entryarray = $this->BSSalRptArray;
        $sqlinsert = "INSERT INTO balance_sheets (" . implode(array_keys($entryarray), ",") . ") VALUES " . rtrim("(" . implode(array_values($entryarray), ",") . "),", ",");
        mysqli_query($GLOBALS['con'], $sqlinsert);
        return "Balance Sheet Entries Created";
    }

    function countPaymodeWiseEmpSalRpt($month, $year, $sel_modeOfPay, $ofid, $BA_Id, $filters) {
        if ($BA_Id != null) {
            $filter = " AND UA.BA_Id=" . $BA_Id;
        }
        if ($sel_modeOfPay == 0) {
            $sel_modeOfPayment = "(UA.SP_Id=1 OR UA.SP_Id=2 OR UA.SP_Id=3)";
        } else {
            $sel_modeOfPayment = "UA.SP_Id=" . $sel_modeOfPay;
        }
        $count = 0;
        $this->getPaymodeWiseEmpSalRptArray = array();
        if (is_numeric($month) && is_numeric($sel_modeOfPay)) {

            $query = "SELECT COUNT(ESR.ESR_Id) FROM employee_salary_report AS ESR
            LEFT JOIN users_accounts AS UA ON UA.US_Id = ESR.US_Id
            LEFT JOIN users_auth AS UU ON UU.US_Id=ESR.US_Id
            LEFT JOIN users_salary AS USAL ON USAL.US_Id=ESR.US_Id
            WHERE ESR.OF_Id=" . $ofid . " AND UU.US_Status != 5 AND ESR.ESR_Year=" . $year . " AND ESR_Month=" . $month . " AND " . $sel_modeOfPayment . $filter;
            $result = mysqli_query($GLOBALS['con'], $query);
            while ($row = mysqli_fetch_object($result)) {
                $this->getPaymodeWiseEmpSalRptArray[$count] = $row;
                $count++;
            }
        }
    }

    //------------------------------------------------get Paymode wise employee salary report------------------------------------
    function getPaymodeWiseEmpSalRpt($month, $year, $sel_modeOfPay, $ofid, $lcid, $acl, $Sal_BnkId, $filter, $order, $posStart, $end) {
        if ($Sal_BnkId != null) {
            $filter .= " AND USAL.Sal_BnkId=" . $Sal_BnkId;
        }
        if ($sel_modeOfPay == 0 && $acl == 0)
            $sel_modeOfPayment = "(UA.SP_Id=1 OR UA.SP_Id=2 OR UA.SP_Id=3) AND UU.LC_Id=" . $lcid;
        elseif ($sel_modeOfPay == 0 && $acl == 1)
            $sel_modeOfPayment = "(UA.SP_Id=1 OR UA.SP_Id=2 OR UA.SP_Id=3) ";
        else {
            if ($sel_modeOfPay == 2 && $acl == 0) {
                $sel_modeOfPayment = " UA.SP_Id=" . $sel_modeOfPay . " AND UU.LC_Id=" . $lcid;
            } else {
                $sel_modeOfPayment = " UA.SP_Id=" . $sel_modeOfPay;
            }
        }
        $count = 0;
        $this->getPaymodeWiseEmpSalRptArray = array();
        if (is_numeric($month) && is_numeric($sel_modeOfPay)) {

            $query = "SELECT ESR.ESR_Id,UU.US_EMPID,ESR.US_FName,ESR.US_LName,ESR.LC_Name,ESR.ESR_TakeHomeSalary,ESR.ESR_Year,ESR.ESR_Month,ESR.ESR_BSStatus,UA.SP_Id,UA.US_AccNo,UA.US_Bankname FROM employee_salary_report AS ESR
            LEFT JOIN users_accounts AS UA ON UA.US_Id = ESR.US_Id
            LEFT JOIN users_auth AS UU ON UU.US_Id=ESR.US_Id
            LEFT JOIN users_salary AS USAL ON USAL.US_Id=ESR.US_Id
            WHERE ESR.OF_Id=" . $ofid . " AND UU.US_Status != 5 AND ESR.ESR_Year=" . $year . " AND ESR_Month=" . $month . " AND " . $sel_modeOfPayment . $filter;
            $result = mysqli_query($GLOBALS['con'], $query);
            while ($row = mysqli_fetch_object($result)) {
                $this->getPaymodeWiseEmpSalRptArray[$count] = $row;
                $count++;
            }
        }
    }

    //---------------------------------------------------------Get payroll_management_history to set color for edited data at the time of loading payroll grid---------------
    function getPayrollManagementHistorydetails($EP_Id) {
        $count = 0;
        $this->getPayrollManagementHistoryArray = array();
        $query = "SELECT DISTINCT EPH_ColLabel FROM payroll_management_history WHERE EP_Id=" . $EP_Id;
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->getPayrollManagementHistoryArray[$count] = $row;
            $count++;
        }
    }

    function newWeekend() {
        $sql = "INSERT INTO department_holidays( " . implode(', ', array_keys($this->Weekend_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Weekend_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
        return "Weekend Added";
    }

    function verifyWeekend($dhid) {
        $result = mysqli_query($GLOBALS['con'], "SELECT COUNT(DH_Id) AS COUNT FROM department_holidays WHERE DP_Id =" . $this->Weekend_Data["DP_Id"] . " AND DH_Id != " . $dhid);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        if ($row["COUNT"] == 0) {
            return true;
        } else {
            return false;
        }
    }

    function updateWeekend($dpid) {
        $WeekData = '';
        foreach ($this->Weekend_Data as $key => $value) {
            $WeekData = $WeekData . $key . "='" . $value . "', ";
        }
        $WeekData = substr($WeekData, 0, -2);
        $sql = "UPDATE department_holidays SET $WeekData WHERE DP_Id=" . $dpid;
        mysqli_query($GLOBALS['con'], $sql);
        return 'Weekend Updated Successfully';
    }

    function listWeekend($ofid) {
        $count = 0;
        $this->DayArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT DH.DH_Id,DH.DP_Id,DH.DH_Weekends,DH.DH_Status,DP.DP_Name "
                . "FROM department_holidays as DH,departments as DP WHERE DH.DP_Id=DP.DP_Id AND DP.OF_Id= " . $ofid);
        while ($row = mysqli_fetch_object($result)) {
            $this->DayArray[$count] = $row;
            $count++;
        }
    }

    function fetchWeekend($dpid) {
        $result = mysqli_query($GLOBALS['con'], "SELECT DH_Weekends FROM department_holidays WHERE DP_Id=" . $dpid);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row["DH_Weekends"];
    }

    function getWeekendOffs($month, $year, $ofid, $dpid = 0) {
        $this->WeekOffs = array();
        $date = $year . "-" . $month . "-01";
        $last = date('Y-m-t', strtotime($date));
        $lstdate = date('Y-m-d', strtotime('+5 days', strtotime($last)));
        $frmdate = date("Y-m-d", strtotime('-5 days', strtotime($date)));
        $lst = date("t", strtotime($date));
        $count = 0;
        $sql ="SELECT DH.DP_Id,DH.DH_Weekends FROM department_holidays as DH,departments AS DP WHERE DP.DP_Id=DH.DP_Id AND DP.OF_Id=" . $ofid; 
        if ($dpid > 0) {
          $sql .= " AND DP.DP_Id=".$dpid;  
        }
        //echo $sql;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $dept_id = $row["DP_Id"];
            $weekOffsString = explode(",", $row["DH_Weekends"]);
            $counterdate = $frmdate;
            while (strtotime($counterdate) <= strtotime($lstdate)) {
                if (in_array(date("N", strtotime($counterdate)), $weekOffsString)) {
                    $this->WeekOffs[$dept_id][] = $counterdate;
                }
                $counterdate = date("Y-m-d", strtotime('+1 days', strtotime($counterdate)));
            }
        }
    }

    /**** 25-06-2025 by Achu 
     for Finding Total working days column in the attendance field *****/

    function getMonthlyWeekendOffs($month, $year, $ofid, $dpid = 0) {
        $this->MonthlyWeekOffs = array();

        $date = "$year-$month-01";
        $lastDate = date('Y-m-t', strtotime($date)); // last date of the month

        // Prepare SQL using JOIN (better practice)
        $sql = "SELECT DH.DP_Id, DH.DH_Weekends 
                FROM department_holidays AS DH 
                JOIN departments AS DP ON DP.DP_Id = DH.DP_Id 
                WHERE DP.OF_Id = $ofid";

        if ($dpid > 0) {
            $sql .= " AND DP.DP_Id = $dpid";
        }

        $result = mysqli_query($GLOBALS['con'], $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $dept_id = $row["DP_Id"];
            $weekOffs = explode(",", $row["DH_Weekends"]); // example: ['6', '7'] for Saturday, Sunday

            $currentDate = new DateTime($date);
            $endDate = new DateTime($lastDate);

            while ($currentDate <= $endDate) {
                $dayNumber = $currentDate->format('N'); // 1 (Mon) to 7 (Sun)

                if (in_array($dayNumber, $weekOffs)) {
                    $this->MonthlyWeekOffs[$dept_id][] = $currentDate->format('Y-m-d');
                }

                $currentDate->modify('+1 day');
            }
        }
    }

    function getStatesCSV($stateList) {
        $states = explode(",", $stateList);
        $stateNames = "";
        foreach ($states as $state) {
            $query = mysqli_query($GLOBALS['con'], "SELECT ST_Name FROM states WHERE ST_Id = $state");
            $result = mysqli_fetch_array($query, MYSQLI_ASSOC);
            $stateNames .= "," . $result['ST_Name'];
        }
        return trim($stateNames, ',');
    }

    function createRHBatch($ofid,$deptid=0) {
        $sql = "SELECT HD_Batch FROM holidays WHERE OF_Id=" . $ofid . "  AND (DP_Id=" . $deptid . " OR DP_Id = 0) AND HD_Type=3 ORDER BY HD_Id DESC LIMIT 1";
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        if ($row[0] != "") {
            $row_arr = explode("_", $row[0]);
            $numb = $row_arr[2] + 1;
        } else
            $numb = 1;
        $newbatch = "RH_" . $ofid . "_" . str_pad($numb, 2, "0", STR_PAD_LEFT);
        ;
        return $newbatch;
    }

    function getRHDays($batchId, $date, $deptid=0) {
        $count = 0;
        $this->RH_Days = array();
        $sql = "SELECT HD_Id,HD_Date  FROM holidays WHERE HD_Batch ='" . $batchId . "' AND (DP_Id=" . $deptid . " OR DP_Id = 0) AND HD_Date !='" . $date . "'";
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $this->RH_Days[$count] = $row;
            $count++;
        }
    }

    function delRHDays($rhId) {
        $hdId = explode(",", $rhId);
        foreach ($hdId as $Id) {
            $sql = "DELETE FROM holidays WHERE HD_Id =" . $Id;
            mysqli_query($GLOBALS['con'], $sql);
        }
    }

    function getOfficeRH($ofid,$deptid=0) {
        $count = 0;
        $this->RH_Holidays = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT ST_Id,HD_Date,HD_Batch FROM holidays WHERE OF_Id=" . $ofid . "  AND (DP_Id=" . $deptid . " OR DP_Id = 0) AND HD_Type=3 AND HD_Status = 1  ORDER BY ST_Id,HD_Date");
        while ($row = mysqli_fetch_assoc($result)) {
            $arr_states = array();
            $arr_states = explode(',', $row['ST_Id']);
            foreach ($arr_states as $state_id) {
                $this->RH_Holidays[$state_id][$row['HD_Batch']][] = $row['HD_Date'];
                $count++;
            }
        }
    }

    function getTakenRHBatches($usid) {
        $sql = "SELECT DISTINCT(HD_Batch) FROM rh_leavelogs WHERE US_Id=" . $usid;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $count = 0;
        $this->RH_Batches = array();
        while ($arr = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $this->RH_Batches[$count] = $arr["HD_Batch"];
            $count++;
        }
    }

    function checkRHDate($rharray, $date, $usid) {
        $flg = 0;
        foreach ($rharray as $key => $value) {
            $batchId = $key;
            if (in_array($date, $value)) {
                $this->createRHLog($usid, $batchId, $date);
                $flg = 1;
            }
        }
        if ($flg == 0)
            return "NOT_RH";
        else
            return "RH_Apply";
    }

    function getRHTakenDays($usid) {
        $sql = "SELECT HD_Date FROM rh_leavelogs WHERE US_Id=" . $usid;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $count = 0;
        $this->RH_TaknDays = array();
        while ($arr = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $this->RH_TaknDays[$count] = $arr["HD_Date"];
            $count++;
        }
    }

    function getRHStatus($rharray, $date, $usid) {
        $count = $this->checkRHLog($usid, $batchId, $date);
        if ($count == 0) {
            $this->createRHLog($usid, $batchId, $date);
            return "RH_Apply";
        } else {
            return "RH_NA";
        }
    }

    function FlattenRHArray($array) {
        $this->RH_Days = array();
        foreach ($array as $batch) {
            $dates_array = array_values($batch);
            foreach ($dates_array as $dates) {
                array_push($this->RH_Days, $dates);
            }
        }
        sort($this->RH_Days);
    }

    function createRHLog($usid, $batchId, $date) {
        $sqlins = "INSERT INTO rh_leavelogs (US_Id,HD_Date,HD_Batch) VALUES  (" . $usid . ",'" . $date . "','" . $batchId . "')";
        mysqli_query($GLOBALS['con'], $sqlins);
    }

    function deleteRHLog($usid, $date) {
        $sqldel_log = "DELETE FROM rh_leavelogs WHERE US_Id=" . $usid . " AND HD_Date='" . $date . "'";
        mysqli_query($GLOBALS['con'], $sqldel_log);
    }

    function getBatchByDate($date, $ofid, $deptid = 0) {
        $sqlsel = "SELECT HD_Batch FROM holidays WHERE OF_Id=" . $ofid . " AND (DP_Id=" . $deptid . " OR DP_Id = 0)  AND HD_Type=3  AND HD_Date='" . $date . "'";
        $result = mysqli_query($GLOBALS['con'], $sqlsel);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row["HD_Batch"];
    }

    //-----------------------------------------------------get BankNames of office---------------------------------------
    function getBankName($of_id) {
        $count = 0;
        $this->getBankNameArray = array();
        $query = "SELECT Sal_BnkId,Sal_BnkName FROM salary_bank_names WHERE OF_Id=" . $of_id;
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->getBankNameArray[$count] = $row;
            $count++;
        }
    }

    function AddAttendance($usid, $date, $stTime, $endTime, $minHrs) {
        $query = "INSERT INTO attendance (US_Id,AT_Date,AT_SignIn,AT_SignOut,AT_Hours,AT_Status,AT_CDate) VALUES (" . $usid . ",'" . $date . "','" . $stTime . "','" . $endTime . "','" . $minHrs . "',1,'" . $date . "')";
        mysqli_query($GLOBALS['con'], $query);
    }

    function verifyAttendance($usid, $date) {
        $sql = 'SELECT COUNT(AT_Id) FROM attendance WHERE US_Id="' . $usid . '" AND AT_Date="' . $date . '" ';
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_row($result);
        if ($row[0] == 0)
            return true;
        else
            return false;
    }

    function createAttRecord() {
        if ($this->verifyAttendance($this->ATT_Data["US_Id"], $this->ATT_Data["AT_Date"])) {
            $sql = "INSERT INTO attendance ( " . implode(', ', array_keys($this->ATT_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->ATT_Data)) . "'" . ")";
            mysqli_query($GLOBALS['con'], $sql);
        } else {
            $this->updateAttRecord($this->ATT_Data["US_Id"], $this->ATT_Data["AT_Date"]);
        }
    }

    function updateAttRecord($usid, $att_date) {
        $STData = '';
        foreach ($this->ATT_Data as $key => $value) {
            $STData = $STData . $key . "='" . $value . "', ";
        }
        $STData = substr($STData, 0, -2);
        $sql = "UPDATE attendance SET $STData  WHERE US_Id=" . $usid . " AND AT_Date='" . $att_date . "'";
        //$sql = "UPDATE attendance SET AT_SignOut="..",AT_Hours,AT_Status  WHERE US_Id=".$usid." AND AT_Date='".$att_date."'";
        mysqli_query($GLOBALS['con'], $sql);
    }

    function AddLeave($usid, $date, $type) {
        $query = "INSERT INTO leave_requests";
    }

    //Checking calculation of PF/ESI/LWF based on persentage 
    function getDedSalType($ssId) {
        $query = "SELECT SS_DedESI_Type,SS_DedESI,SS_DedEPF_Type,SS_DedEPF,SS_DedProfTDS,SS_EmpConEPF,SS_EmpConEPF_Type,SS_EmpConESI,SS_EmpConESI_Type,SS_EmpConLWF,SS_EmpConLWF_Type FROM salary_structures WHERE SS_Id=" . $ssId;
        $result = mysqli_query($GLOBALS['con'], $query);
        $this->getDedSalTypeArray = mysqli_fetch_assoc($result);
    }

    function getBankDetails($ofid) {
        $this->BankDetails = array();
        $sql = "SELECT BA.BA_Id,BB.BB_Id,BB.BNK_Id FROM bank_accounts AS BA LEFT JOIN bank_branches AS BB ON BA.BB_Id=BB.BB_Id 
                WHERE BA.BA_Status=1 AND BA.OF_Id=" . $ofid;
        $result = mysqli_query($GLOBALS['con'], $sql);
        ;
        while ($row = mysqli_fetch_assoc($result)) {
            $this->BankDetails[$row["BA_Id"]] = $row;
        }
    }

    function changeSalChequeStats($chqids) {

        $sql = "UPDATE bank_cheque_leafs SET CL_Status = 2 WHERE CL_Id IN (" . $chqids . ")";
        mysqli_query($GLOBALS['con'], $sql);
    }

    function listUser($ofId, $mask) {
        $sql = "SELECT CONCAT(US.US_FName,' ', US.US_LName) AS Name,US.US_Id FROM users_auth AS US WHERE US.OF_Id=" . $ofId . " AND US.US_Status!=0 AND US.US_Status != 5 " . $mask . "LIMIT 0,10";
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->listUserArray[$count] = $row;
            $count++;
        }
    }

    function getGeneratedSalYear($ofId) {
        $this->salYearArray = array();
        $sql = "SELECT MAX(EP_Year) AS Year FROM employee_payroll WHERE OF_Id =" . $ofId;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $this->salYearArray = mysqli_fetch_assoc($result);
    }

    function getGeneratedSalMonth($ofId, $year) {
        $this->salMonthArray = array();
        $sql = "SELECT MAX(EP_Month) AS Month FROM employee_payroll WHERE OF_Id =" . $ofId . " AND EP_Year=" . $year;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $this->salMonthArray = mysqli_fetch_assoc($result);
    }

    function saveSalaryAdvance() {
        $sql = "INSERT INTO salary_advance_payment ( " . implode(', ', array_keys($this->SA_data)) . ") VALUES (" . "'" . implode("','", array_values($this->SA_data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
        return mysqli_insert_id($GLOBALS['con']);
    }

    function amt_split($sum, $parts) {
        $this->parts_array = array();
        $diff = $sum % $parts;
        $portion = ($sum - $diff) / $parts;
        for ($i = 0; $i < $parts; $i++) {
            $this->parts_array[$i] = $portion;
        }
        $this->parts_array[0] = $this->parts_array[0] + $diff;
    }

    function saveRepaymentShedule() {
        $sql = "INSERT INTO  repayment_schedule( " . implode(', ', array_keys($this->SRS_data)) . ") VALUES (" . "'" . implode("','", array_values($this->SRS_data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
    }

    function salAdvnceDetails($ofId, $pos, $cnt, $filter) {
        $this->salAdvnceDetailsArray = array();
        $sql = "SELECT SA.*,CONCAT(US.US_FName,' ', US.US_LName) AS Name,CONCAT(UA.US_FName,' ', UA.US_LName) AS CName FROM salary_advance_payment AS SA 
                LEFT JOIN users_auth AS US ON US.US_Id=SA.US_Id 
                LEFT JOIN users_auth AS UA ON UA.US_Id=SA.SA_USId 
                WHERE US.US_Status!=0 AND US.US_Status != 5 AND US.OF_Id=" . $ofId . $filter .
                " ORDER BY SA.SA_Id DESC LIMIT " . $pos . "," . $cnt;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->salAdvnceDetailsArray[$count] = $row;
            $count++;
        }
    }

    function salAdvnceDetailsCount($ofId, $filter) {
        $sql = "SELECT COUNT(SA.SA_Id) FROM salary_advance_payment AS SA 
                LEFT JOIN users_auth AS US ON US.US_Id=SA.US_Id
                LEFT JOIN users_auth AS UA ON UA.US_Id=SA.SA_USId 
                WHERE US.US_Status!=0 AND US.US_Status != 5 AND US.OF_Id=" . $ofId . $filter .
                " ORDER BY SA.SA_Id DESC";
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        return $row[0];
    }

    function repaymentDetails($ofId, $filt, $pos, $cnt) {
        $this->salAdvnceDetailsArray = array();
        $sql = "SELECT RS.* ,CONCAT(US.US_FName,' ', US.US_LName) AS Name,LC.LC_Name FROM repayment_schedule AS RS
                LEFT JOIN salary_advance_payment AS SA ON SA.SA_Id=RS.SA_Id
                LEFT JOIN users_auth AS US ON US.US_Id=SA.US_Id  
                LEFT JOIN locations AS LC ON LC.LC_Id=US.LC_Id  WHERE US.US_Status!=0 AND US.US_Status != 5 AND US.OF_Id=" . $ofId . $filt;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->salAdvnceDetailsArray[$count] = $row;
            $count++;
        }
    }

    function viewDetails($data, $table, $filt) {
        $count = 0;
        $this->DetailsArray = array();
        $sql = " SELECT " . $data . " FROM " . $table . $filt;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DetailsArray[$count] = $row;
            $count++;
        }
    }

    function viewDetailsrow($data, $table, $filt) {
        $sql = " SELECT " . $data . " FROM " . $table . $filt;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_row($result);
        return $row[0];
    }

    function getMonthwiseSalRpt($filt, $stDate = '', $enDate = '', $ESR_Month_From, $ESR_Year_From, $ESR_Month_To, $ESR_Year_To, $ofId) {
        $count = 0;
        $this->getUserArray = array();
        $this->monthSalArray = array();
        $dateFilt = '';
        if ($stDate != '' && $enDate != '') {
            $dateStartFlter = " AND SR.ESR_Month >=" . $ESR_Month_From . " AND SR.ESR_Year=" . $ESR_Year_From;
            $dateEndFlter = " AND SR.ESR_Month <=" . $ESR_Month_To . " AND SR.ESR_Year=" . $ESR_Year_To;
        }
        $sql = "SELECT SR.US_Id ,SR.ESR_TakeHomeSalary, SR.ESR_Year,SR.ESR_Month FROM employee_salary_report AS SR WHERE SR.OF_Id = " . $ofId . $dateStartMonth;
        $sql1 = " UNION SELECT SR.US_Id ,SR.ESR_TakeHomeSalary, SR.ESR_Year,SR.ESR_Month FROM employee_salary_report AS SR WHERE SR.OF_Id = " . $ofId . $dateEndFlter;
        $sqlQuery = $sql . $sql1;
        $result = mysqli_query($GLOBALS['con'], $sqlQuery);
        while ($row = mysqli_fetch_object($result)) {
            $this->monthSalArray[$row->US_Id][$row->ESR_Month][$row->ESR_Year] = $row->ESR_TakeHomeSalary;
        }
        $query = "SELECT US.US_Id,CONCAT(US.US_FName,' ',US.US_LName) AS Name,LC.LC_Name  FROM users_auth AS US
               LEFT JOIN locations AS LC ON LC.LC_Id = US.LC_Id  WHERE US.OF_Id=" . $ofId . " AND US.US_Status != 5 ORDER BY  LC.LC_Name ASC";
        $resultAdded = mysqli_query($GLOBALS['con'], $query);
        while ($row1 = mysqli_fetch_object($resultAdded)) {
            $this->getUserArray[$count] = $row1;
            $count++;
        }
    }

    function getNextMonth($month, $year) {
        //$date = '2008-06-04';

        $date = $year . '-' . $month . '-' . '01';
        list($dateyear, $datemonth, $day) = explode('-', $date);
        $next = date('Y-m-d', mktime(0, 0, 0, $datemonth + 1, $day, $dateyear));
        return $next;
    }

    function updateRepaymentStatus($SRS_Id, $stats) {
        $query = "UPDATE repayment_schedule SET SRS_Status =" . $stats . " WHERE SRS_Id=" . $SRS_Id;
        mysqli_query($GLOBALS['con'], $query);
    }

    function listSalaryHistory($ofid, $filter, $orderBy, $pos, $cnt) {
        $count = 0;
        $this->salHistDetailsArray = array();
        $query = "SELECT UA.US_Id,CONCAT(UA.US_FName,' ',UA.US_LName) AS Name,UA.US_DOJ,LC.LC_Name,US.US_GrossSal,UA.US_EMPID,DG.DG_Name,AL.ALC_Name,DP.DP_Name FROM users_auth AS UA                 
                LEFT JOIN users_salary AS US ON UA.US_Id=US.US_Id 
                LEFT JOIN locations AS LC ON UA.LC_Id=LC.LC_Id 
                LEFT JOIN addr_streets AS AST ON AST.SR_Id=LC.SR_Id 
                LEFT JOIN addr_places AS AP ON AP.PL_Id= AST.PL_Id
                LEFT JOIN addr_locations AS AL ON AL.ALC_Id=AP.ALC_Id
                LEFT JOIN departments AS DP ON DP.DP_Id=UA.DP_Id
                LEFT JOIN designations AS DG ON DG.DG_Id =UA.DG_Id
                WHERE UA.OF_Id=" . $ofid . " AND UA.US_Status != 5 " . $filter . $orderBy . " LIMIT " . $pos . "," . $cnt;
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $row->JoinSal = $row->US_GrossSal;
            $row->Sal_Date = "--";
            $row->Incrmnts = array();
            $row->IncrmntsDates = array();
            $maxsql = "SELECT Sal_Amt,Sal_Date FROM salary_history WHERE US_Id =" . $row->US_Id . " ORDER BY Sal_Date DESC ";
            $resultmax = mysqli_query($GLOBALS['con'], $maxsql);
            //$rowmax=mysqli_fetch_object($resultmax);    
            if (mysqli_num_rows($resultmax) > 0) {
                $indx = 0;
                while ($rowmax = mysqli_fetch_object($resultmax)) {
                    array_push($row->Incrmnts, $rowmax->Sal_Amt);
                    array_push($row->IncrmntsDates, $rowmax->Sal_Date);
                }
            }
            $this->salHistDetailsArray[$count] = $row;
            $count++;
        }
        $query = "SELECT COUNT(*) AS COUNT FROM users_auth AS UA
                LEFT JOIN users_salary AS US ON UA.US_Id=US.US_Id  
                LEFT JOIN locations AS LC ON UA.LC_Id=LC.LC_Id
                LEFT JOIN addr_streets AS AST ON AST.SR_Id=LC.SR_Id 
                LEFT JOIN addr_places AS AP ON AP.PL_Id= AST.PL_Id
                LEFT JOIN addr_locations AS AL ON AL.ALC_Id=AP.ALC_Id
                LEFT JOIN departments AS DP ON DP.DP_Id=UA.DP_Id
                LEFT JOIN designations AS DG ON DG.DG_Id =UA.DG_Id
                WHERE UA.OF_Id=" . $ofid . " " . $filter;
        $result = mysqli_query($GLOBALS['con'], $query);
        $rows = mysqli_fetch_assoc($result);
        return $rows['COUNT'];
    }

    function getCount() {
        $query = "SELECT COUNT(Sal_HistId ) AS COUNT FROM salary_history GROUP BY US_Id ORDER BY COUNT DESC LIMIT 0, 1";
        $res = mysqli_query($GLOBALS['con'], $query);

        $row = mysqli_fetch_array($res, MYSQLI_NUM);
        return $row[0];
    }

    function listSalaryHistoryData($ofid, $usId) {
        $count = 0;
        $this->salHistDetailsArray = array();
        $query = "SELECT UA.US_Id FROM users_auth AS UA                 
                LEFT JOIN users_salary AS US ON UA.US_Id=US.US_Id 
                LEFT JOIN locations AS LC ON UA.LC_Id=LC.LC_Id 
                LEFT JOIN addr_streets AS AST ON AST.SR_Id=LC.SR_Id 
                LEFT JOIN addr_places AS AP ON AP.PL_Id= AST.PL_Id
                LEFT JOIN addr_locations AS AL ON AL.ALC_Id=AP.ALC_Id
                LEFT JOIN departments AS DP ON DP.DP_Id=UA.DP_Id
                LEFT JOIN designations AS DG ON DG.DG_Id =UA.DG_Id
                WHERE UA.OF_Id=" . $ofid . " AND UA.US_Id=" . $usId . "";
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $row->Incrmnts = array();
            $row->IncrmntsDates = array();
            $maxsql = "SELECT Sal_Amt,Sal_Date FROM salary_history WHERE US_Id =" . $usId . " ORDER BY Sal_Date DESC ";
            $resultmax = mysqli_query($GLOBALS['con'], $maxsql);
            //$rowmax=mysqli_fetch_object($resultmax);    
            if (mysqli_num_rows($resultmax) > 0) {
                $indx = 0;
                while ($rowmax = mysqli_fetch_object($resultmax)) {
                    array_push($row->Incrmnts, $rowmax->Sal_Amt);
                    array_push($row->IncrmntsDates, $rowmax->Sal_Date);
                }
            }
            $this->salHistDetailsArray[$count] = $row;
            $count++;
        }
    }

    function getRepaymentAmounts($usid, $month, $year) {
        $this->repaymentArray = array();
        $query = "SELECT RS.SRS_Id,RS.SRS_RepaymentAmt,SAP.SA_PaymentTime FROM repayment_schedule AS RS              
                LEFT JOIN salary_advance_payment AS SAP ON SAP.SA_Id = RS.SA_Id WHERE RS.US_Id=" . $usid . " AND RS.SRS_Status=1 AND  RS.SRS_Month='" . $month . "-" . $year . "'";
        $result = mysqli_query($GLOBALS['con'], $query);
        $loanAmt = 0;
        $AdvAmt = 0;
        $SrIds = array();
        $paymentTimes = array();
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row["SA_PaymentTime"] > 2) {
                $loanAmt += round($row['SRS_RepaymentAmt']);
            } else {
                $AdvAmt += round($row['SRS_RepaymentAmt']);
            }
            array_push($SrIds, $row['SRS_Id']);
            $paymentTimes[] = $row['SA_PaymentTime'];
        }
        $this->repaymentArray["Loan"] = $loanAmt;
        $this->repaymentArray["Advance"] = $AdvAmt;
        $this->repaymentArray["PaymentTime"] = $paymentTimes;
        $this->repaymentArray["SR_Ids"] = $SrIds;
    }

    function AttendanceListScheduler($id, $from, $to) {
        $indx = 0;
        $this->AttendanceSchArray = array();
        $sql = "SELECT * FROM pretally.attendance WHERE  AT_DATE BETWEEN '" . $from . "' AND '" . $to . "' AND US_Id=" . $id;
//        echo $sql;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->AttendanceSchArray[$indx] = $row;
            $indx++;
        }
    }

    function listWeekends($id) {
        $indx = 0;
        $this->WeekendDetailsArray = array();
        $sql = "SELECT
  US.US_Id,
  US.OF_Id,
  US.DP_Id,
  LC.LC_Name,
  LC.ST_Id,
  US.US_FName,
  US.US_LName,
  US.US_LoginTime,
  US.US_LogoutTime,
  US.US_WrkHours,
  DH.DH_Weekends
FROM
  `users_auth` AS US
LEFT JOIN
  locations AS LC ON LC.LC_Id = US.LC_Id
LEFT JOIN
  department_holidays AS DH ON DH.DP_Id = US.DP_Id
WHERE
  US.US_Id ='" . $id . "'";
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->WeekendDetailsArray[$indx] = $row;
            $indx++;
        }
    }

    function getStateId($locId) {
        $sql = "SELECT `ST_Id`  FROM `locations` WHERE `LC_Id` ='" . $locId . "'";
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_assoc($result);
        return $row["ST_Id"];
    }

    function listHolidays($ofid, $fromDate, $toDate, $stateId, $deptid=0) {
        $indx = 0;
        $this->HolidayArray = array();

        $sql = "SELECT
HD_Id,
ST_Id,
HD_Date,
HD_Comments
FROM
holidays
WHERE
OF_Id = '".$ofid."' AND HD_Type != '3' AND HD_Status = '1' AND (ST_Id = '0' OR FIND_IN_SET('".$stateId."',
ST_Id)) AND HD_Date BETWEEN '".$fromDate."' AND '".$toDate."' AND (DP_Id=" . $deptid . " OR DP_Id = 0)";
//        echo $sql;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->HolidayArray[$indx] = $row;
            $indx++;
        }
    }

    function listRHTaken($usid, $fromDate,$deptid=0) {
        $indx = 0;
        $this->RH_TaknDays = array();
        $sql = "SELECT
  RHL.RHL_Id AS ID,
  RHL.HD_Date AS Dat,
  RHL.HD_Batch AS Batch,
  HD.HD_Comments AS Comments
FROM
  rh_leavelogs AS RHL
LEFT JOIN
  holidays AS HD ON RHL.HD_Batch = HD.HD_Batch
WHERE
  US_Id ='".$usid."'  AND (HD.DP_Id=" . $deptid . " OR HD.DP_Id = 0)
  AND RHL.HD_Date BETWEEN '".$fromDate."' AND '" . date('Y-m-d') . "'
GROUP BY
  RHL.RHL_Id";
//        echo $sql;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->RH_TaknDays[$indx] = $row;
            $indx++;
        }
    }

    function listAllRH($ofid, $fromDate, $toDate, $btch, $deptid=0) {
        $indx = 0;
        $this->All_RH = array();
        $sql = "SELECT HD_Id,HD_Date,HD_Comments FROM holidays WHERE OF_Id='" . $ofid . "' AND HD_Type=3 AND HD_Status = 1 AND " . $btch . " AND HD_Date BETWEEN '" . $fromDate . "' AND '" . $toDate . "'  AND (DP_Id=" . $deptid . " OR DP_Id = 0)   ORDER BY ST_Id,HD_Date;";
//        echo $sql;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->All_RH[$indx] = $row;
            $indx++;
        }
    }

    function checkPrevDate($currDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates, $leaveDateArr) {
        $prevDate = date('m/d/Y', strtotime('-1 day', strtotime($currDate)));
        if (in_array($prevDate, $attendanceDateArr)) {
            return "y";
        } elseif (in_array($prevDate, $holidayDates) || in_array($prevDate, $weekendDates) || in_array($prevDate, $takenRHDates)) {
            return $this->checkPrevDate($prevDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates, $leaveDateArr);
        } elseif (in_array($prevDate, $leaveDateArr)) {
            return "x";
        } elseif ((!in_array($prevDate, $attendanceDateArr)) || (!in_array($prevDate, $leaveDateArr))) {
            return "x";
        }
    }

    function checkNxtDate($currDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates) {
        $nxtDate = date('m/d/Y', strtotime('+1 day', strtotime($currDate)));
        if (in_array($nxtDate, $attendanceDateArr)) {
            return "y";
        } elseif (in_array($nxtDate, $holidayDates) || in_array($nxtDate, $weekendDates) || in_array($nxtDate, $takenRHDates)) {
            return $this->checkNxtDate($nxtDate, $attendanceDateArr, $holidayDates, $weekendDates, $takenRHDates, $leaveDateArr);
        } elseif (in_array($nxtDate, $leaveDateArr)) {
            return "x";
        } elseif ((!in_array($nxtDate, $attendanceDateArr)) || (!in_array($nxtDate, $leaveDateArr))) {
            return "x";
        }
    }

    /****** 28/04/2025  By Achu *********/

    function getPreviousWeekDatesFromSunday($sundayDate) {
        $date = new DateTime($sundayDate);
        $date->modify('-6 days');
        return array_map(function($i) use ($date) {
            return $date->modify('+'.($i ? 1 : 0).' day')->format('Y-m-d');
        }, range(0, 5));
    }

    function getApprovedLeaveByDate($user_id, $dates, $LTId) {
        $resultArray = array(); // Prepare the array to return

        // Convert arrays into comma-separated strings with single quotes
        if (is_array($dates)) {
            $dates = "'" . implode("','", $dates) . "'";
        }

        if (is_array($LTId)) {
            $LTId = implode(",", $LTId);
        }

        // Build the query safely
        $query = "SELECT ld.LRD_Date
                  FROM leave_reqdays ld
                  LEFT JOIN leave_request lr ON lr.LR_Id = ld.LR_Id
                  LEFT JOIN leave_type lt ON ld.LT_Id = lt.LT_Id
                  WHERE lr.LR_AppliedFor = " . intval($user_id) . "
                    AND lr.LR_Status = 2
                    AND ld.LRD_Date IN (" . $dates . ")
                    AND lt.LT_Id IN (" . $LTId . ")";

        $result = mysqli_query($GLOBALS['con'], $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $dayName = date('D', strtotime($row['LRD_Date']));
                $resultArray[] = strtolower($dayName);
            }
        }

        return $resultArray;
    }

    function getDaysBetween($startDay, $endDay) {

        // Define the full week
        $weekDays = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];

        // Find the index of start and end
        $startIndex = array_search($startDay, $weekDays);
        $endIndex = array_search($endDay, $weekDays);

        // Get the slice of days
        $totalDaysArray = array_slice($weekDays, $startIndex, $endIndex - $startIndex + 1);

        return $totalDaysArray; 
    }

    /****** 28/04/2025  By Achu *********/

    /**** Achu by 07-05-2025 ******/

        // ------------------------------ Return attendance flag ---------------------- //
        function checkAttendanceByDate($usid, $date) {
            $sql = 'SELECT COUNT(*) AS ATT_CNT FROM attendance WHERE US_Id="' . $usid . '" AND AT_Date="' . $date . '" AND AT_Status=1 ';
            $result = mysqli_query($GLOBALS['con'], $sql);
            $row = mysqli_fetch_row($result);
            return $row[0];
        }

        function checkAttendanceByDateRow($usid, $date) {
            $sql = 'SELECT * FROM attendance WHERE US_Id="' . $usid . '" AND AT_Date="' . $date . '" AND AT_Status=1';
            $result = mysqli_query($GLOBALS['con'], $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result); // Return full row
            }

            return false; // No result or query failed
        }

        function getBetweenDatesAfterDOJ($startDate, $endDate, $DOJ){
            $dates = [];
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $DOJDateTime = new DateTime($DOJ);
            $end->modify('+1 day'); // include end date
            $interval = new DateInterval('P1D'); // 1 day interval
            $dateRange = new DatePeriod($start, $interval, $end);
            foreach ($dateRange as $date) {
                $dates[] = $date->format('Y-m-d');
            }
            // echo $start->format('o-W')."===".$DOJDateTime->format('o-W');die;
            // if ($start->format('o-W') === $DOJDateTime->format('o-W')){
                $remainingDates = array_filter($dates, function($date) use ($DOJDateTime) {
                    return new DateTime($date) >= $DOJDateTime;
                });
                $dates = array_values($remainingDates);
            // }
            return $dates;
        }

        function excludeDatesBeforeJoiningDate($leaveDays, $doj) {
            $leaveDays = array_filter($leaveDays, function($date) use ($doj) {
                return $date >= $doj; // Keep only dates that are $b or later
            });
            return $leaveDays;
        }

    /**** Achu by 07-05-2025 ******/

}

?>