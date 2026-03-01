<?php

require_once("connection.php");

class LeaveClass {

    var $DojArray;
    var $leaveTypeArray;
    var $listLeaveArray;
    var $listmyLeaveArray;
    var $listLeaveNameArray;
    var $listLeaveBranchArray;
    var $listManageLeaveTypeArray;
    var $selectmaxleaveArray;
    var $getEmployeeStatusArray;
    var $selectEmpStatusArray;
    var $getHrMailIdArray;

    function selectdoj($preTally_user_id, $leaveType) {
        $count = 0;
        $this->DojArray = array();
//                $query="SELECT ua.US_DOJ ,
//                                    SUM(lr.LR_NumOFDays)as TotalLeaveTaken
//                                    FROM `users_auth` as ua 
//                                    LEFT JOIN leave_request AS lr  ON ua.Us_Id=lr.US_Id
//                                    WHERE ua.US_Id=".$preTally_user_id." AND lr.LT_Id= ".$leaveType ." AND (lr.LR_Status=1 OR lr.LR_Status=0 OR lr.LR_Status=2) AND (lr.LR_FromDate BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' ) AND DATE_FORMAT( NOW( ) , '%Y-12-31' )) AND (lr.LR_ToDate BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' ) AND DATE_FORMAT( NOW( ) , '%Y-12-31' ))";
        $query = "SELECT ua.US_DOJ ,
                        SUM(lr.LR_NumOFDays)as TotalLeaveTaken 
                        FROM `users_auth` as ua 
                        LEFT JOIN leave_request AS lr ON ua.Us_Id = lr.LR_AppliedFor  
                        WHERE ua.US_Id=" . $preTally_user_id . " AND lr.LT_Id= " . $leaveType . " AND (lr.LR_Status=1 OR lr.LR_Status=0 OR lr.LR_Status=2) AND (lr.LR_FromDate BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' ) AND DATE_FORMAT( NOW( ) , '%Y-12-31' )) AND (lr.LR_ToDate BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' ) AND DATE_FORMAT( NOW( ) , '%Y-12-31' ))";

        $result = mysqli_query($GLOBALS['con'], $query);
        $this->DojArray = mysqli_fetch_row($result);
    }

    function selectmaxleave($lType) {
        $query = "SELECT LT_MaxCount,ES_Status from leave_type WHERE LT_Id=" . $lType . "";
        $result = mysqli_query($GLOBALS['con'], $query);
        $this->selectmaxleaveArray = mysqli_fetch_row($result);
    }

    //---------------------------------------------------Get the status of employee-------------------------------------------------
    function selectEmpStatus($user_id) {
        $query = "SELECT UA.ES_Id,ESH.ESH_Date from users_auth AS UA
                    LEFT JOIN  employee_status_history AS ESH ON UA.US_Id= ESH.US_Id
                    WHERE UA.US_Id=" . $user_id . " ORDER BY ESH.ESH_Date DESC";
        $result = mysqli_query($GLOBALS['con'], $query);
        $this->selectEmpStatusArray = mysqli_fetch_row($result);
    }

    function applyLeave() {
        $sql = "INSERT INTO leave_request ( " . implode(', ', array_keys($this->Leave_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Leave_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
        return mysqli_insert_id($GLOBALS['con']);
    }

    function applyLeavedays() {
        $sql = "INSERT INTO  leave_reqdays ( " . implode(', ', array_keys($this->Leave_Day_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Leave_Day_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
    }

    function updateLeavedays($USId, $AttDate, $updType) {
        $sql = "UPDATE  leave_reqdays SET LT_Id=" . $updType . " WHERE US_Id=" . $USId . " AND LRD_Date='" . $AttDate . "'";
        mysqli_query($GLOBALS['con'], $sql);
    }

    function deleteLeaveRecords($usid, $att_date) {
        $sql_select = "SELECT LR_Id FROM leave_reqdays WHERE US_Id =" . $usid . " AND LRD_Date ='" . $att_date . "'";
        $result = mysqli_query($GLOBALS['con'], $sql_select);
        while ($row = mysqli_fetch_row($result)) {
            $sql_del_day = "DELETE FROM leave_reqdays WHERE LR_Id=" . $row[0];
            mysqli_query($GLOBALS['con'], $sql_del_day);

            $sql_leave_req = "DELETE FROM leave_request WHERE LR_Id=" . $row[0];
            mysqli_query($GLOBALS['con'], $sql_leave_req);
        }
    }

    function getLeaveType($preTally_user_ofid) {
        $count = 0;
        $this->leaveTypeArray = array();
        $query = "SELECT LT_Id,LT_Name,ES_Status FROM leave_type where OF_Id=" . $preTally_user_ofid . " AND LT_Status=1";
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->leaveTypeArray[$count] = $row;
            $count++;
        }
    }

    function changeLeaveStatus($LR_Id) {
        $filter = "";
        $LeaveData = '';
        if ($this->Leave_Data['LR_Status'] == 5 || $this->Leave_Data['LR_Status'] == 1) {
            $filter = " AND(LR_Status=0)";
        } else if ($this->Leave_Data['LR_Status'] == 2 || $this->Leave_Data['LR_Status'] == 3 || $this->Leave_Data['LR_Status'] == 4) {
            $filter = " AND(LR_Status=0 OR LR_Status=1)";
        } else if ($this->Leave_Data['LR_Status'] == 6) {
            $filter = " AND(LR_Status=1 OR LR_Status=2)";
        }
        foreach ($this->Leave_Data as $key => $value) {
            $LeaveData = $LeaveData . $key . "='" . $value . "', ";
        }
        $LeaveData = substr($LeaveData, 0, -2);
        $sql = "UPDATE leave_request SET " . $LeaveData . " WHERE LR_Id=" . $LR_Id . " " . $filter;
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result > 0) {
            return 'Leave Updated Successfully';
        } else {
            return 'updation failed..plz refresh ';
        }
    }

    function leaveApprovalList($stDate = '', $enDate = '', $filter, $order, $pos, $cnt) {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND lr.LR_FromDate >= '" . $stDate . "' AND lr.LR_ToDate <= '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND lr.LR_ToDate <= '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND lr.LR_FromDate >=  '" . $stDate . "'";
        }

        $sql = "SELECT COUNT(lr.LR_Id)
                        FROM leave_request as lr
                            LEFT JOIN leave_type AS lt ON lr.LT_Id = lt.LT_Id
                            LEFT JOIN users_auth AS ua ON ua.US_Id = lr.LR_AppliedFor 
                            LEFT JOIN users_auth AS u_au ON u_au.US_Id = lr.US_Id  
                            LEFT JOIN users_auth AS u_auth ON u_auth.US_Id = lr.LR_FirstApproval   
                            LEFT JOIN users_auth AS u_hr_auth ON u_hr_auth.US_Id = lr.LR_ApprovedHR    
                            LEFT JOIN locations AS l ON ua.LC_Id = l.LC_Id 
                                WHERE " . $filter . " " . $dateFilt;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        $this->LeaveCount = $row[0];

        $query = "SELECT lr.LR_Id,lr.LR_FromDate, lr.US_Id, lr.LR_ToDate, lr.LR_NumOFDays, lr.LR_Reason,lr.LR_EligibleNumOFDays,lr.LR_FirstDt, ua.US_FName,ua.US_LName,u_au.US_FName AS ReportUS_FName,u_au.US_LName AS ReportUS_LName,u_auth.US_FName AS FirstAprvUS_FName,u_auth.US_LName AS FirstAprvUS_LName,u_hr_auth.US_FName AS HRAprvUS_FName,u_hr_auth.US_LName AS HRAprvUS_LName, lt.LT_Name, lr.LR_Status,ua.US_Report,ua.LC_Id,l.LC_Name,lr.LR_Comments,lr.LR_CDate 
                        FROM leave_request as lr
                        LEFT JOIN leave_type AS lt ON lr.LT_Id = lt.LT_Id
                        LEFT JOIN users_auth AS ua ON ua.US_Id = lr.LR_AppliedFor 
                        LEFT JOIN users_auth AS u_au ON u_au.US_Id = lr.US_Id  
                        LEFT JOIN users_auth AS u_auth ON u_auth.US_Id = lr.LR_FirstApproval   
                        LEFT JOIN users_auth AS u_hr_auth ON u_hr_auth.US_Id = lr.LR_ApprovedHR    
                        LEFT JOIN locations AS l ON ua.LC_Id = l.LC_Id  
                            WHERE " . $filter . " " . $dateFilt . $order . " LIMIT " . $pos . "," . $cnt;
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->listLeaveArray[$count] = $row;
            $count++;
        }
    }

    function listmyLeave($userId, $fromDate, $toDate) {
        $filter = " ORDER BY lr.LR_FromDate DESC";
        if ($fromDate != "" && $toDate != "") {
            $filter = "AND lr.LR_FromDate>='" . $fromDate . "' AND lr.LR_ToDate<='" . $toDate . "' ORDER BY lr.LR_FromDate DESC";
        } else if ($fromDate != "" && $toDate == "") {
            $filter = "AND lr.LR_FromDate>='" . $fromDate . "' ORDER BY lr.LR_FromDate DESC";
        } else if ($fromDate == "" && $toDate != "") {
            $filter = "AND  lr.LR_ToDate<='" . $toDate . "' ORDER BY lr.LR_FromDate DESC";
        }
        $query = "SELECT lr.LR_Id,lr.LR_FromDate, lr.LR_ToDate, lr.LR_NumOFDays,lr.LR_EligibleNumOFDays, lr.LR_Reason, ua.US_FName,ua.US_LName, lt.LT_Name, lr.LR_Status,lr.LR_Comments,lr.LR_CDate
                        FROM leave_request AS lr
                        LEFT JOIN users_auth AS ua ON lr.LR_AppliedFor = ua.US_Id
                        LEFT JOIN leave_type AS lt ON lt.LT_Id = lr.LT_Id WHERE lr.LR_AppliedFor=" . $userId . " " . $filter;
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->listmyLeaveArray[$count] = $row;
            $count++;
        }
    }

    function listManageLeaveType($ofid) {
        $count = "";
        $query = "SELECT LT_Id,OF_Id,LT_Name,LT_MaxCount,LT_LOP,LT_Status,ES_Status from leave_type WHERE OF_Id=" . $ofid . "";
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->listManageLeaveTypeArray[$count] = $row;
            $count++;
        }
    }

    function addLeaveType() {

        $sql = "INSERT INTO leave_type ( " . implode(', ', array_keys($this->LeaveTypeData)) . ") VALUES (" . "'" . implode("','", array_values($this->LeaveTypeData)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
        return 'Leave type added successfully';
    }

    function updateLeaveType($LT_Id) {
        $LeaveData = '';
        foreach ($this->LeaveTypeData as $key => $value) {
            $LeaveData = $LeaveData . $key . "='" . $value . "', ";
        }
        $LeaveData = substr($LeaveData, 0, -2);
        $sql = "UPDATE leave_type SET " . $LeaveData . " WHERE LT_Id=" . $LT_Id . "";
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result > 0) {
            return ' LeaveType Updated Successfully';
        }
    }

    function getAppliedLeaves($userid) {
        $this->AppliedDays = array();
        $query = "SELECT lt.LT_Name,ld.LRD_Date,lt.LT_Id
                FROM leave_reqdays ld
                LEFT JOIN leave_request lr on lr.LR_Id = ld.LR_Id
                LEFT JOIN leave_type lt ON ld.LT_Id = lt.LT_Id
                WHERE lr.US_Id=" . $userid;
        $result = mysqli_query($GLOBALS['con'], $query);
        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $this->AppliedDays[$count] = $row['LRD_Date'];
            $count++;
        }
    }

    function verifyLeaveType($lvTypeId) {
        if ($lvTypeId == "") {
            $lvTypeId = 0;
        }
        $sql = 'SELECT COUNT(LT_Id) FROM leave_type WHERE LT_Name = "' . $this->LeaveTypeData['LT_Name'] . '" AND OF_Id="' . $this->LeaveTypeData['OF_Id'] . '"  AND LT_Id!=' . $lvTypeId;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        $count = $row[0];
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    function getEmployeeStatus($ofid) {
        $this->leaveTypeArray = array();
        $count = 0;
        $query = "SELECT ES_Id,ES_Name from employee_status WHERE OF_Id=" . $ofid;
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->getEmployeeStatusArray[$count] = $row;
            $count++;
        }
    }

    function getAllDatesBetweenTwoDates($strDateFrom, $strDateTo) {
        $aryRange = array();
        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));
        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom));
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    function getHrMailId($of_id) {
        $query = "SELECT HR_Mail from company_settings WHERE OF_Id =" . $of_id;
        $result = mysqli_query($GLOBALS['con'], $query);
        $this->getHrMailIdArray = mysqli_fetch_row($result);
    }

    function createLVRptGrp($ofid) {
        $sql = "SELECT  MAX(SUBSTRING(Lrpt_GrpID FROM 10))  FROM leave_reported WHERE OF_Id=" . $ofid;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        if ($row[0] != "")
            $numb = $row[0] + 1;
        else
            $numb = 1;
        $newbatch = "LVRPT_" . str_pad($ofid, 2, "0", STR_PAD_LEFT) . "_" . str_pad($numb, 2, "0", STR_PAD_LEFT);
        return $newbatch;
    }

    function ReportSubLeave() {
        $sql = "INSERT INTO leave_reported( " . implode(', ', array_keys($this->Leave_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Leave_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
    }

    function checkLeaveExistence($userId, $fromDate, $toDate) {
        $sql = " SELECT COUNT(*) AS count FROM leave_reqdays AS LRD
                        LEFT JOIN leave_request AS LR ON LR.LR_Id = LRD.LR_Id
                            WHERE LRD.US_Id = '" . $userId . "'
                                AND LRD.LRD_Date BETWEEN '" . $fromDate . "' AND '" . $toDate . "'
                                AND LR.LR_Status < 3";
        $query = mysqli_query($GLOBALS['con'], $sql);
        $result = mysqli_fetch_array($query, MYSQLI_ASSOC);
        return $result['count'];
    }

    function checktakenLeaves($userId, $fromDate, $toDate, $Ltype) {
        $sql = " SELECT SUM(LRD.LRD_Days) AS sumdays FROM leave_reqdays AS LRD
                        LEFT JOIN leave_request AS LR ON LR.LR_Id = LRD.LR_Id
                            WHERE LRD.US_Id = '" . $userId . "'
                                AND LRD.LRD_Date BETWEEN '" . $fromDate . "' AND '" . $toDate . "'
                                AND LR.LR_Status < 3 AND LR.LT_Id=" . $Ltype;
        $query = mysqli_query($GLOBALS['con'], $sql);
        $result = mysqli_fetch_array($query, MYSQLI_ASSOC);
        if ($result['sumdays'] != NULL)
            return $result['sumdays'];
        else
            return 0;
    }

    function getLeaveTypeParams($usid) {
        $sql = "SELECT DP_Id,ES_Id FROM users_auth WHERE US_Id=" . $usid;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $this->LeaveParams = mysqli_fetch_assoc($result);
    }

    function leaveDetails($filter, $year, $start, $end) {
        $count = 0;
        $query = "SELECT US.US_Id,CONCAT(US.US_FName,' ',US.US_LName) AS Name,LC.LC_Name,US.DP_Id  
                FROM users_auth AS US LEFT JOIN locations AS LC ON US.LC_Id = LC.LC_Id 
                WHERE $filter AND (YEAR(US.US_ResignDate) = '$year' OR US.US_ResignFlag= 0) 
                AND US.US_DOJ <= '$year-12-31' AND US.US_Status != 5 ORDER BY Name LIMIT $start,$end";

        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->leaveDetailsArray[$count] = $row;
            $count++;
        }
    }

    function leaveDetailsCount($filter, $year) {
        $count = 0;
        $query = mysqli_query($GLOBALS['con'], "SELECT US.US_Id,CONCAT(US.US_FName,' ',US.US_LName) AS Name,LC.LC_Name 
                FROM users_auth AS US LEFT JOIN locations AS LC ON US.LC_Id = LC.LC_Id 
                WHERE $filter AND (YEAR(US.US_ResignDate) = '$year' OR US.US_ResignFlag= 0) 
                AND US.US_DOJ <= '$year-12-31' AND US.US_Status != 5 ");

        return mysqli_num_rows($query);
    }

    function userLeaveTypeDays($filter, $year) {

        $this->ApprovedLeaves = array();
        $this->PendingLeaves = array();

        $query = "SELECT LR.LR_AppliedFor,SUM(IF(LR.LR_Status = 2, LR.LR_NumOFDays, 0)) AS ApprovedLeaves,
            SUM(IF(LR.LR_Status = 0 OR LR.LR_Status = 1, LR.LR_NumOFDays, 0)) AS PendingLeaves,LT.LT_Id  
        FROM leave_request AS LR 
	LEFT JOIN users_auth AS US ON LR.LR_AppliedFor = US.US_Id 
        LEFT JOIN locations AS LC ON US.LC_Id = LC.LC_Id 
        LEFT JOIN leave_type AS LT ON LR.LT_Id = LT.LT_Id 
        WHERE  $filter AND (LR.LR_Status = 2 OR LR.LR_Status = 0 OR LR.LR_Status = 1 ) AND LR.LR_FromDate >= '$year-01-01' 
        AND LR.LR_ToDate <= '$year-12-31' GROUP BY LR.LT_Id,US.US_Id";

        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->ApprovedLeaves[$row->LR_AppliedFor][$row->LT_Id] += $row->ApprovedLeaves;
            $this->PendingLeaves[$row->LR_AppliedFor][$row->LT_Id] += $row->PendingLeaves;
        }
    }

    function userEligibleLeaves($filter, $year, $preTally_user_ofid) {
        $this->eligibleLeaveArray = array();
        $this->eligibleStatusArray = array();
        $this->userLeaveArray = array();
        ///// Get employee status for selected year

        $ESHistorySql = "SELECT US.US_Id,US.ES_Id, CONCAT(MONTH(ES.ESH_Date),'-',YEAR(ES.ESH_Date)) AS ES_StatusDate, CONCAT(MONTH(US.`US_DOJ`),'-',YEAR(US.`US_DOJ`)) AS DOJ_Date FROM 
                users_auth AS US LEFT JOIN  
                employee_status_history AS ES ON ( US.US_Id = ES.US_Id AND YEAR(ES.ESH_Date) = '$year') 
                LEFT JOIN locations AS LC ON US.LC_Id = LC.LC_Id 
                WHERE $filter AND US.US_Status != 5  ORDER BY ES.ESH_Date DESC";

        $ESHistoryResult = mysqli_query($GLOBALS['con'], $ESHistorySql);
        while ($row = mysqli_fetch_object($ESHistoryResult)) {
            if (!$this->eligibleLeaveArray[$row->US_Id]['US_Id']) {
                $this->eligibleLeaveArray[$row->US_Id]['US_Id'] = trim($this->eligibleLeaveArray[$row->US_Id]['US_Id'] . ',' . $row->ES_Id, ',');
                $this->eligibleLeaveArray[$row->US_Id]['Date'] = ($row->ES_StatusDate) ? $row->ES_StatusDate : $row->DOJ_Date;
            }
        }
        ///// Get status of each leave types
        $ESStatusSql = "SELECT ES_Status,LT_Id,LT_MaxCount FROM leave_type WHERE OF_Id = $preTally_user_ofid AND LT_Status = 1";
        $ESStatusResult = mysqli_query($GLOBALS['con'], $ESStatusSql);

        while ($row = mysqli_fetch_object($ESStatusResult)) {
            $empStatus = explode(",", $row->ES_Status);
            for ($i = 0; $i < count($empStatus); $i++) {
                $eligibleEmpStatusIndividual = explode(":", $empStatus[$i]);
                $this->eligibleStatusArray[$row->LT_Id][$eligibleEmpStatusIndividual[0]] = $eligibleEmpStatusIndividual[1];
            }

//            foreach ($this->eligibleLeaveArray as $e_row) {

            foreach ($this->eligibleLeaveArray as $key => $value) {
                $ESStatusArray = explode(',', $value['US_Id']);
                foreach ($ESStatusArray as $Status) {
                    $this->userLeaveArray[$key][$row->LT_Id] = $this->userLeaveArray[$key][$row->LT_Id] + ($row->LT_MaxCount * $this->eligibleStatusArray[$row->LT_Id][$Status]);
                }
            }
        }
    }

    function LeaveScheduler($id,$from,$to) {
        $indx = 0;
        $this->LeaveSchArray = array();
        $sql = "SELECT
  LRD.*,
  IF(LRD.LR_Id <= 0 AND LRD.LRD_Days=1,LRD.LRD_Date, LR.LR_FromDate) AS fromDate,
  IF(LRD.LR_Id <= 0 AND LRD.LRD_Days=1,LRD.LRD_Date,LR.LR_ToDate) AS toDate,
  LR.LR_Status AS Status,
  LT.LT_Name AS leaveType
FROM
  leave_reqdays AS LRD
LEFT JOIN
  leave_request AS LR ON LRD.LR_Id = LR.LR_Id
LEFT JOIN
  leave_type AS LT ON LRD.LT_Id = LT.LT_Id
WHERE
        LRD.LRD_Date BETWEEN '" . $from . "' AND '" . $to . "' AND LRD.US_Id=" . $id;
//        echo $sql;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->LeaveSchArray[$indx] = $row;
            $indx++;
        }
    }
    /**
    * User based leave requested in a selected date range
    */
    function getLeaveReqUser($inputs)
    {
        $result     = [];
        extract($inputs);
        // process inputs 
        $from_date  = (isset($from_date) && $from_date != "") ? $from_date : date('Y-m-').'01';
        $to_date    = (isset($to_date) && $to_date != "") ? $to_date : date('Y-m-d'); 

        $where      = ' WHERE US_Id = "'.$user_id.'"';
        if ($from_date != "" && $to_date != "") {

            $where  .= ' AND (LR_FromDate >= "'.$from_date.'" OR LR_ToDate >= "'.$from_date.'") AND (LR_FromDate <= "'.$to_date.'" OR LR_ToDate <= "'.$to_date.'")';
        } else if ($from_date != "") {

            $where  .= ' AND (LR_FromDate >= "'.$from_date.'" OR LR_ToDate >= "'.$from_date.'") ';
        } else if ($to_date != "") {

            $where  .= ' AND (LR_FromDate <= "'.$to_date.'" OR LR_ToDate <= "'.$to_date.'")';
        }
        if (isset($status) && $status != "") {
            $where  .= ' AND LR_Status = "'.$status.'"';
        }

        $sql    = 'SELECT LR_Id, US_Id, LR_Session, LR_FromDate, LR_ToDate FROM leave_request  '.$where .' ORDER BY LR_Id ASC ';
        $res    = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($res)) { 

            $lfrom_date  = $row->LR_FromDate;
            $lto_date    = $row->LR_ToDate;            
            while ( $lfrom_date <= $lto_date ) {

                $result[$lfrom_date] = $row->LR_Session;
                $lfrom_date = date('Y-m-d', strtotime($lfrom_date . ' +1 day'));
            }
        }
        //$this->sqlqry = $sql;
        return $result;
    }

}

?>