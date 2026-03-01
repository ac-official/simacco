<?php

require_once("connection.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

class LateEntryClass {

    var $listLateEntryArray;

    var $listAllLateEntryArray;

    function listManageLateEntries($userId, $filterCondition, $sortFlter = 'Att_Lt_Created_At DESC') {
        $count = 0;

        $query = "
            SELECT 
                aa.*, 
                ua.US_FName, 
                ua.US_LName, 
                l.LC_Name,
                cb.US_FName AS createdBy_first,
                cb.US_LName AS createdBy_last,
                vb.US_FName AS verifiedBy_first,
                vb.US_LName AS verifiedBy_last
            FROM 
                attendance_late_entry aa
            INNER JOIN users_auth ua ON ua.US_Id = aa.Att_Lt_US_Id
            INNER JOIN users_auth cb ON cb.US_Id = aa.Att_Lt_Created_By
            LEFT JOIN users_auth vb ON vb.US_Id = aa.Att_Lt_Verified_By
            INNER JOIN locations l ON l.LC_Id = ua.LC_Id
            WHERE aa.Att_Lt_US_Id = '$userId' $filterCondition
            ORDER BY $sortFlter;
        ";

        // echo "<pre>$query</pre>";die; // Debug if needed

        $result = mysqli_query($GLOBALS['con'], $query);

        while ($row = mysqli_fetch_object($result)) {
            $this->listLateEntryArray[$count] = $row;
            $count++;
        }
    }

        function listManageAllLateEntries($pos, $cnt, $filter = '', $sortFlter = 'Att_Lt_Created_At DESC') {
            $count = 0;
            $this->listAllLateEntryArray = []; // Ensure it's initialized

            $countQuery = "
                SELECT COUNT(*) AS totalCount
                FROM attendance_late_entry aa
                INNER JOIN users_auth ua ON ua.US_Id = aa.Att_Lt_US_Id
                INNER JOIN users_auth cb ON cb.US_Id = aa.Att_Lt_Created_By
                LEFT JOIN users_auth vb ON vb.US_Id = aa.Att_Lt_Verified_By
                INNER JOIN locations l ON l.LC_Id = ua.LC_Id
                INNER JOIN departments d ON d.DP_Id = ua.DP_Id
                WHERE 1 $filter
            ";

            $countResult = mysqli_query($GLOBALS['con'], $countQuery);

            if ($countResult && mysqli_num_rows($countResult) > 0) {
                $countRow = mysqli_fetch_assoc($countResult);
                $this->lateEntryCount = $countRow['totalCount'];
            } else {
                $this->lateEntryCount = 0; // If query fails or no records
            }

            $query = "
            SELECT 
                aa.*, 
                ua.US_FName, 
                ua.US_LName, 
                l.LC_Name,
                d.DP_Name,
                cb.US_FName AS createdBy_first,
                cb.US_LName AS createdBy_last,
                vb.US_FName AS verifiedBy_first,
                vb.US_LName AS verifiedBy_last
            FROM 
                attendance_late_entry aa
            INNER JOIN users_auth ua ON ua.US_Id = aa.Att_Lt_US_Id
            INNER JOIN users_auth cb ON cb.US_Id = aa.Att_Lt_Created_By
            LEFT JOIN users_auth vb ON vb.US_Id = aa.Att_Lt_Verified_By
            INNER JOIN locations l ON l.LC_Id = ua.LC_Id
            INNER JOIN departments d ON d.DP_Id = ua.DP_Id
            WHERE 1 $filter
            ORDER BY $sortFlter 
            LIMIT " . $pos . ", " . $cnt;

            // echo $query;die;

            $result = mysqli_query($GLOBALS['con'], $query);

            if (!$result) {
                // Handle query error (optional logging)
                error_log("MySQL Error: " . mysqli_error($GLOBALS['con']));
                return false; // or handle as needed
            }

            while ($row = mysqli_fetch_object($result)) {
                $this->listAllLateEntryArray[$count] = $row;
                $count++;
            }

            return true; // Indicate success
        }

    function applyLateEntry() {
        if ($this->LateEntryData) {

            if (!empty($this->LateEntryData['Att_Lt_Id'])) {
                // Fetch existing entry
                $attendnaceAdjustmentEntry = $this->getLateEntry($this->LateEntryData['Att_Lt_Id']);
                if ($attendnaceAdjustmentEntry && $attendnaceAdjustmentEntry['Att_Lt_Status'] == 'pending') {
                    // Prepare data for update
                    $att_lt_id = $this->LateEntryData['Att_Lt_Id'];
                    unset($this->LateEntryData['Att_Lt_Id']); // ID should not be updated
                    $this->LateEntryData['Att_Lt_Updated_By'] = $this->LateEntryData['Att_Lt_US_Id'];
                    $this->LateEntryData['Att_Lt_Updated_At'] = date('Y-m-d H:i:s');
                    $updateParts = [];
                    foreach ($this->LateEntryData as $key => $value) {
                        $escapedValue = mysqli_real_escape_string($GLOBALS['con'], $value);
                        $updateParts[] = "`$key` = '$escapedValue'";
                    }
                    $updateQuery = implode(', ', $updateParts);

                    $sql = "UPDATE attendance_late_entry SET $updateQuery WHERE Att_Lt_Id = '$att_lt_id'";

                    $result = mysqli_query($GLOBALS['con'], $sql);

                    if (!$result) {
                        error_log("MySQL Update Error: " . mysqli_error($GLOBALS['con']));
                        return false;
                    }
                    return true;
                } else {
                    // If status is not 1, do not update
                    return false;
                }
            } else {
                // Insert new entry
                $this->LateEntryData['Att_Lt_Created_By'] = $this->LateEntryData['Att_Lt_US_Id'];
                $this->LateEntryData['Att_Lt_Created_At'] = date('Y-m-d H:i:s');
                $columns = implode(', ', array_keys($this->LateEntryData));
                $values = implode("','", array_map(function($value) {
                    return mysqli_real_escape_string($GLOBALS['con'], $value);
                }, array_values($this->LateEntryData)));

                $sql = "INSERT INTO attendance_late_entry ($columns) VALUES ('$values')";

                $result = mysqli_query($GLOBALS['con'], $sql);

                if (!$result) {
                    error_log("MySQL Insert Error: " . mysqli_error($GLOBALS['con']));
                    return false;
                }

                return true;
            }
        } else {
            return false;
        }
    }

    function getFieldValue($field, $filter) {
        $query = mysqli_query($GLOBALS['con'],"SELECT $field FROM attendance_late_entry WHERE $filter");
        return mysqli_fetch_array($query,MYSQLI_ASSOC);
    }

    function checkLateEntryExistence($fromDate, $toDate, $loggedUser, $entryId = null) {
        // Format dates to Y-m-d
        $fromDate = date("Y-m-d", strtotime($fromDate));
        $toDate = date("Y-m-d", strtotime($toDate));

        $query = "
            SELECT COUNT(*) as entry_count 
            FROM attendance_late_entry 
            WHERE Att_Lt_From_Date = '$fromDate' 
              AND Att_Lt_To_Date = '$toDate' 
              AND Att_Lt_US_Id = '$loggedUser'
        ";

        // Exclude the current entry if editing
        if (!empty($entryId) && $entryId != 0) {
            $query .= " AND Att_Lt_Id != '$entryId' ";
        }

        $result = mysqli_query($GLOBALS['con'], $query);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            return $row['entry_count'] > 0 ? true : false;
        }

        return false;
    }

    function getLateEntry($Att_Lt_Id) {
        $sql = 'SELECT * FROM attendance_late_entry WHERE Att_Lt_Id="' . $Att_Lt_Id . '"';
        $result = mysqli_query($GLOBALS['con'], $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result); // Return full row
        }

        return false; // No result or query failed
    }


    /**
    * list user based late entry list saved and approved 
    * filter the list based on the from and to date 
    */
    function getLateEntryUser($inputs) 
    {
        extract($inputs);
        $results    = [];
        // process inputs 
        $from_date  = (isset($from_date) && $from_date != "") ? $from_date : date('Y-m-').'01';
        $to_date    = (isset($to_date) && $to_date != "") ? $to_date : date('Y-m-d'); 

        $where      = ' WHERE Att_Lt_US_Id = "'.$user_id.'"';
        if ($from_date != "" && $to_date != "") {

            $where  .= ' AND (Att_Lt_From_Date >= "'.$from_date.'" OR Att_Lt_To_Date >= "'.$from_date.'") AND (Att_Lt_From_Date <= "'.$to_date.'" OR Att_Lt_To_Date <= "'.$to_date.'")';
        } else if ($from_date != "") {

            $where  .= ' AND (Att_Lt_From_Date >= "'.$from_date.'" OR Att_Lt_To_Date >= "'.$from_date.'") ';
        } else if ($to_date != "") {

            $where  .= ' AND (Att_Lt_From_Date <= "'.$to_date.'" OR Att_Lt_To_Date <= "'.$to_date.'")';
        }
        if (isset($status) && $status != "") {

            switch($status) {
                case '1' : $where  .= ' AND Att_Lt_Status ="approved"';
                break;

                case '3' : $where  .= ' AND Att_Lt_Status ="rejected"';
                break;

                case '2' : $where  .= ' AND Att_Lt_Status ="pending"';
                break;
            }
        }
        $sql    = 'SELECT Att_Lt_Id, Att_Lt_US_Id, Att_Lt_From_Date, Att_Lt_To_Date, Att_Lt_NumOFDays, Att_Lt_Duration, Att_Lt_Type FROM attendance_late_entry  '.$where .' ORDER BY Att_Lt_Id ASC ';
        $res    = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($res)) {           

            $type       = ($row->Att_Lt_Type == 'p1' ) ? '1':'2';
            // calculate the minutes
            $timeAry    = explode(":",$row->Att_Lt_Duration);
            $timeminute = ($timeAry[0] > 0) ? $timeAry[0] * 60 : 0;
            $timeminute = ($timeAry[1] > 0) ? $timeminute + $timeAry[1] : $timeminute;
            // date formating anc processing            
            $lt_from_date  = $row->Att_Lt_From_Date;
            $lt_to_date    = $row->Att_Lt_To_Date;            
            while ( $lt_from_date <= $lt_to_date ) {
                if ( !isset($results[$lt_from_date])) {
                    $results[$lt_from_date] = ['1'=>0,'2'=>0];
                }
                if ($timeminute > $results[$lt_from_date][$type]) {
                    $results[$lt_from_date][$type] = $timeminute;
                }
                $lt_from_date = date('Y-m-d', strtotime($lt_from_date . ' +1 day'));
            }
        }



        return $results;
    }
    function getallowedGraseTime($from_date, $todate, $off_id=0, $type=1) 
    {
        $allowmin   = 10;
        $results    = [];
        $sql        = 'SELECT morning_grase_time, evening_grase_time, effective_from, effective_to FROM grase_time_settings WHERE ( (effective_from <= "'.$from_date.'" AND effective_to > "'.$from_date.'") OR (effective_from <= "'.$from_date.'" AND effective_to is NULL) OR (effective_from >= "'.$from_date.'" AND effective_from < "'.$todate.'") )';
        if ($off_id > 0) {
            $sql   .= ' AND (of_id=0 OR of_id = "'.$off_id.'")';
        } else {
            $sql   .= ' AND of_id=0';
        }
        $sql       .= ' ORDER BY effective_from ASC';
        $res        = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($res)) { 
                $allowmin   = ($type == 1) ? $row->morning_grase_time : $row->evening_grase_time;
                $sdate  = ($from_date < $row->effective_from ) ? $row->effective_from : $from_date;
                $edate  = ($row->effective_to != '') ? $row->effective_to:$todate;
                while ( $sdate <= $edate && $sdate <= $todate ) {

                    $results[$sdate] = $allowmin;
                    $sdate = date('Y-m-d', strtotime($sdate . ' +1 day'));            
                }
            }
        if (empty($results)) {
            while ( $from_date <= $todate ) {

                $results[$from_date] = 0;
                $from_date = date('Y-m-d', strtotime($from_date . ' +1 day'));            
            }
        }
        
        return $results;
    }

}

?>