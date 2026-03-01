<?php

require_once("connection.php");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
require_once($BASEPATH . "preTallyClass/LeaveClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

class AttendanceAdjustmentClass {

    var $adjustmentArray;
    var $listAdjustmentArray;
    var $listManageAdjustmentArray;
    var $daysArray;

    function listManageAdjustments($sortFlter, $filter_key = '') {
        $count = 0; // changed from "" to 0 for indexing
        /*$query = "SELECT 
            attendance_adjustment.*, 
            users_auth.US_FName, 
            users_auth.US_LName, 
            locations.LC_Name,
            created_by.US_FName AS createdBy_first,
            created_by.US_LName AS createdBy_last
        FROM 
            attendance_adjustment
        INNER JOIN 
            users_auth 
            ON users_auth.US_Id = attendance_adjustment.Att_Adj_US_Id
        INNER JOIN 
            users_auth AS created_by
            ON created_by.US_Id = attendance_adjustment.Att_Adj_Created_By    
        INNER JOIN 
            locations 
            ON locations.LC_Id = users_auth.LC_Id
        WHERE 1 $filter_key
        ORDER BY $sortFlter";*/

        $query = "
        SELECT 
            aa.*, 
            ua.US_FName, 
            ua.US_LName, 
            l.LC_Name,
            cb.US_FName AS createdBy_first,
            cb.US_LName AS createdBy_last
        FROM 
            attendance_adjustment aa
        JOIN (
            SELECT 
                MAX(Att_Adj_Id) AS max_id
            FROM 
                attendance_adjustment
            GROUP BY 
                Att_Adj_US_Id, Att_Adj_Date
        ) AS latest
            ON aa.Att_Adj_Id = latest.max_id
        INNER JOIN users_auth ua ON ua.US_Id = aa.Att_Adj_US_Id
        INNER JOIN users_auth cb ON cb.US_Id = aa.Att_Adj_Created_By
        INNER JOIN locations l ON l.LC_Id = ua.LC_Id
        WHERE 1=1 AND aa.Att_Adj_Status = 1 $filter_key
        ORDER BY $sortFlter;";

        // echo $query;die;

        // Debug (optional)
        // echo "<pre>$query</pre>";

        $result = mysqli_query($GLOBALS['con'], $query);

        while ($row = mysqli_fetch_object($result)) {
            $this->listManageAdjustmentArray[$count] = $row;
            $count++;
        }
    }

    function addAdjustment() {
        if ($this->AdjustmentData) {
            // echo "<pre>";print_r($this->AdjustmentData);die;
            $this->AdjustmentData['Att_Adj_Created_At'] = date('Y-m-d H:i:s');
            if($this->AdjustmentData['Att_Adj_Id']){
                $attendnaceAdjustmentEntry = $this->getAttendanceAdjustmentEntry($this->AdjustmentData['Att_Adj_Id']);
                $this->AdjustmentData['Att_Adj_Created_At'] = $attendnaceAdjustmentEntry['Att_Adj_Created_At'];
                $this->AdjustmentData['Att_Adj_Parent'] = $this->AdjustmentData['Att_Adj_Id'];
            }
            unset($this->AdjustmentData['Att_Adj_Id']);
            $columns = implode(', ', array_keys($this->AdjustmentData));
            $values  = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($this->AdjustmentData), $GLOBALS['con']), array_values($this->AdjustmentData)));
            $sql = "INSERT INTO attendance_adjustment ($columns) VALUES ('$values')";

            $result = mysqli_query($GLOBALS['con'], $sql);

            if (!$result) {
                // Log or display the error
                error_log("MySQL Error: " . mysqli_error($GLOBALS['con']));
                return false;
            }else{
                $LeaveObj = new LeaveClass();
                $AttUpdObj = new AttendanceClass();
                $UserObj = new UserClass();
                $UserObj->selectPunchingTimes($this->AdjustmentData['Att_Adj_US_Id']);
                $CompObj = $UserObj->UserLogArray;
                if($this->AdjustmentData['Att_Adj_Type']=="p"){
                    $AttUpdObj->deleteRHLog($this->AdjustmentData['Att_Adj_US_Id'], $this->AdjustmentData['Att_Adj_Date']);
                    $LeaveObj->deleteLeaveRecords($this->AdjustmentData['Att_Adj_US_Id'], $this->AdjustmentData['Att_Adj_Date']);
                    $at_SignIn  = $CompObj['US_LoginTime'];
                    $at_SignOut = $CompObj['US_LogoutTime'];
                    $workHours  = $CompObj['US_WrkHours'];
                    $AT_SignInDelay = $AT_SignOutEarly = 0;
                }else{
                    $AttUpdObj->deleteAttendance($this->AdjustmentData['Att_Adj_US_Id'], $this->AdjustmentData['Att_Adj_Date']); 
                    $AttUpdObj->deleteRHLog($this->AdjustmentData['Att_Adj_US_Id'], $this->AdjustmentData['Att_Adj_Date']);
                    $LeaveObj->deleteLeaveRecords($this->AdjustmentData['Att_Adj_US_Id'], $this->AdjustmentData['Att_Adj_Date']);
                    $hlfdaytime=date("H:i:s",strtotime($CompObj['US_LoginTime'])+($CompObj['US_WrkHours']/2*60));
                    $workHours = $CompObj['US_WrkHours']/2;
                    if($this->AdjustmentData['Att_Adj_Type']=="p1"){
                        $at_SignIn  = date("H:i:s", strtotime($CompObj['US_LoginTime']));
                        $at_SignOut = $hlfdaytime;
                        $AT_SignInDelay = 0;
                        $AT_SignOutEarly = $CompObj['US_WrkHours']/2;
                    }else{
                        $at_SignIn  = $hlfdaytime;
                        $at_SignOut = date("H:i:s", strtotime($CompObj['US_LogoutTime']));
                        $AT_SignInDelay = $CompObj['US_WrkHours']/2;
                        $AT_SignOutEarly = 0;
                    }
                }
                $AttUpdObj->ATT_Data = array(
                    'US_Id'         => $this->AdjustmentData['Att_Adj_US_Id'],
                    'AT_Date'       => $this->AdjustmentData['Att_Adj_Date'],
                    'AT_SignIn'     => $at_SignIn,
                    'AT_SignOut'    => $at_SignOut,
                    'AT_Hours'      => $workHours,
                    'AT_Status'     => 1,
                    'AT_IPAddr'     => gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
                    'AT_CDate'      => date("Y-m-d"),
                    'AT_AllotTime'  => json_encode(array ("in"=>$CompObj['US_LoginTime'], "out"=>$CompObj['US_LogoutTime'], 'whour'=>$CompObj['US_WrkHours'])),
                    'AT_SignInDelay' => $AT_SignInDelay,
                    'AT_SignOutEarly' => $AT_SignOutEarly,
                );  
                if($AttUpdObj->checkAttendanceByDate($this->AdjustmentData['Att_Adj_US_Id'], $this->AdjustmentData['Att_Adj_Date'])){
                    $AttUpdObj->updateAttRecord($this->AdjustmentData['Att_Adj_US_Id'], $this->AdjustmentData['Att_Adj_Date']);
                }else{
                    $AttUpdObj->createAttRecord();
                }
                return true;
            }
            
        } else {
            return false;
        }
    }

    function checkAttendanceExistance($usid, $data) {
        $AttAdjId = '';
        if(htmlspecialchars($data['Att_Adj_Id'], ENT_QUOTES)){
            $AttAdjId = htmlspecialchars($data['Att_Adj_Id'], ENT_QUOTES);
            $sql = 'SELECT Att_Adj_Id FROM attendance_adjustment WHERE Att_Adj_US_Id="' . $usid . '" AND Att_Adj_Date="' . DateTime::createFromFormat('d/m/Y', $data["Att_Adj_Date"])->format('Y-m-d') . '" AND Att_Adj_Type = "'.$data['Att_Adj_Type'].'" AND Att_Adj_Status = 1';
            $result = mysqli_query($GLOBALS['con'], $sql);
            $row = mysqli_fetch_row($result);
            if($row[0]!=$AttAdjId){
                return $row[0];
            }
        }else{
            $sql = 'SELECT COUNT(*) AS ATT_CNT FROM attendance_adjustment WHERE Att_Adj_US_Id="' . $usid . '" AND Att_Adj_Date="' . DateTime::createFromFormat('d/m/Y', $data["Att_Adj_Date"])->format('Y-m-d') . '" AND Att_Adj_Type = "'.$data['Att_Adj_Type'].'" AND Att_Adj_Status = 1';
            $result = mysqli_query($GLOBALS['con'], $sql);
            $row = mysqli_fetch_row($result);
            return $row[0];
        }
        
    }

    function updateAttendanceAdjustment($usid, $date, $Att_Adj_Parent=0) {
        if ($usid && $date) {
            $date = DateTime::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            $AttUpdObj = new AttendanceClass();
            $attReference = $AttUpdObj->checkAttendanceByDateRow($usid, $date);
            $attRow = '';
            if($attReference){
                $attRow = json_encode($attReference, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);
            }
            $Att_Adj_Reference = $attRow;
            $Att_Adj_Updated_At = date('Y-m-d h:i:s');
            $sql = "UPDATE attendance_adjustment SET Att_Adj_Status = '0', Att_Adj_Reference = '$attRow', Att_Adj_Updated_At = '$Att_Adj_Updated_At' WHERE Att_Adj_US_Id = $usid AND Att_Adj_Date = '$date'";
            $result = mysqli_query($GLOBALS['con'], $sql);
            return true;
        } else {
            return false;
        }
    }

    function getAttendanceAdjustmentEntry($Att_Adj_Id) {
        $sql = 'SELECT * FROM attendance_adjustment WHERE Att_Adj_Id="' . $Att_Adj_Id . '"';
        $result = mysqli_query($GLOBALS['con'], $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result); // Return full row
        }

        return false; // No result or query failed
    }

}

?>