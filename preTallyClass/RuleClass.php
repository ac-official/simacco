<?php

require_once("connection.php");

class RuleClass {

    var $ruleArray;
    var $listRuleArray;
    var $listManageRuleTypeArray;
    var $daysArray;

    function getRuleType() {
        $count = 0;
        $this->ruleArray = array();
        $query = "SELECT RL_Id,RL_Name FROM rules where RL_Status=1 AND RL_End_Date IS NULL";
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->leaveTypeArray[$count] = $row;
            $count++;
        }
    }

    function getRuleEffectiveDate($ruleId) {
        $sql = 'SELECT RL_Effective_Date FROM rules WHERE RL_Id=' . intval($ruleId);
        $result = mysqli_query($GLOBALS['con'], $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result, MYSQLI_NUM);
            return $row[0]; // Return the RL_Effective_Date
        } else {
            return false;
        }
    }

    function getSpecificRule($ruleCode) {
        $sql = 'SELECT * FROM rules WHERE RL_Code="' . $ruleCode .'" and RL_Status=1';
        // echo $sql;die;
        $result = mysqli_query($GLOBALS['con'], $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result, MYSQLI_NUM);
            return $row; // Return the RL_Effective_Date
        } else {
            return false;
        }
    }


    function getRules($lastdate) {
        $count = 0;
        $this->listRuleArray = [];
        // Ensure the date is safely quoted
        $lastdate = mysqli_real_escape_string($GLOBALS['con'], $lastdate);

        $query = "
            SELECT r.*
            FROM rules r
            JOIN (
                SELECT RL_Name, MAX(RL_Effective_Date) AS Max_Effective
                FROM rules
                WHERE RL_Status = 1
                  AND RL_Effective_Date < '$lastdate' -- STRICTLY LESS THAN
                  AND (RL_End_Date IS NULL OR RL_End_Date >= '$lastdate')
                GROUP BY RL_Name
            ) filtered
            ON r.RL_Name = filtered.RL_Name AND r.RL_Effective_Date = filtered.Max_Effective
            WHERE r.RL_Status = 1
        ";
        $result = mysqli_query($GLOBALS['con'], $query);

        while ($row = mysqli_fetch_object($result)) {
            $this->listRuleArray[$count] = $row;
            $count++;
        }
    }

    function listManageRule($type) {
        $count = "";
        $query = "SELECT RL_Id, RL_Name, RL_Description, RL_Start_Day, RL_End_Day, RL_Is_LOP, RL_Status, RL_Sandwich_Type, RL_Leave_Duration, RL_Except_Dept_office, RL_Effective_Date from rules where RL_Is_Sandwich = $type AND RL_End_Date IS NULL";
        $result = mysqli_query($GLOBALS['con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $this->listManageRuleTypeArray[$count] = $row;
            $count++;
        }
    }

    function verifyRule($ruleId) {
        if ($ruleId == "") {
            $ruleId = 0;
        }
        if($this->RuleData['RL_Is_Sandwich']==0){
            $sql = 'SELECT COUNT(RL_Id) FROM rules WHERE RL_Start_Day = "' . $this->RuleData['RL_Start_Day'] . '" AND RL_End_Day = "'.$this->RuleData['RL_End_Day'].'" AND RL_Id!=' . $ruleId .'" AND RL_End_Date IS NULL';
            $result = mysqli_query($GLOBALS['con'], $sql);
            $row = mysqli_fetch_array($result, MYSQLI_NUM);
            $count = $row[0];
            if ($count == 0) {
                $start_day = $this->RuleData['RL_Start_Day'];
                $end_day = $this->RuleData['RL_End_Day'];

                $week_days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

                $start_index = array_search($start_day, $week_days);
                $end_index = array_search($end_day, $week_days);

                $new_selected_days = [];

                if ($start_index !== false && $end_index !== false) {
                    if ($start_index <= $end_index) {
                        $new_selected_days = array_slice($week_days, $start_index, $end_index - $start_index + 1);
                    } else {
                        $new_selected_days = array_merge(
                            array_slice($week_days, $start_index),
                            array_slice($week_days, 0, $end_index + 1)
                        );
                    }
                }

                // Fetch existing rules
                $existing_rules = [];
                $query = "SELECT RL_Start_Day, RL_End_Day, RL_Id FROM rules WHERE RL_Is_Sandwich = 0  AND RL_End_Date IS NULL"; // your condition
                $result = mysqli_query($GLOBALS['con'], $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $existing_rules[] = [
                        'RL_Id' => intval($row['RL_Id']),
                        'start' => strtolower($row['RL_Start_Day']),
                        'end'   => strtolower($row['RL_End_Day'])
                    ];
                }
                $overlap = [];
                if (!empty($existing_rules)) {
                    foreach ($existing_rules as $rule) {
                        $existing_start_index = array_search($rule['start'], $week_days);
                        $existing_end_index = array_search($rule['end'], $week_days);

                        $existing_days = [];

                        if ($existing_start_index !== false && $existing_end_index !== false) {
                            if ($existing_start_index <= $existing_end_index) {
                                $existing_days = array_slice($week_days, $existing_start_index, $existing_end_index - $existing_start_index + 1);
                            } else {
                                $existing_days = array_merge(
                                    array_slice($week_days, $existing_start_index),
                                    array_slice($week_days, 0, $existing_end_index + 1)
                                );
                            }
                        }
                        $overlap = array_intersect($new_selected_days, $existing_days);
                        if (!empty($overlap)) {
                            if ($ruleId > 0 && $ruleId == $rule['RL_Id']) {
                                continue;
                            } else {
                                return false;
                            }
                            // echo $ruleId."=>".$rule['RL_Id']."<br/>";
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        }else{
            $sql = 'SELECT COUNT(RL_Id) FROM rules WHERE RL_Name = "' . $this->RuleData['RL_Name'] . '" AND RL_Sandwich_Type = "'.$this->RuleData['RL_Sandwich_Type'].'" AND RL_Id!=' . $ruleId .'" AND RL_End_Date IS NULL"';
            $result = mysqli_query($GLOBALS['con'], $sql);
            $row = mysqli_fetch_array($result, MYSQLI_NUM);
            $count = $row[0];
            if ($count == 0) {
                return true;
            }else{
                return false;
            }
        }
    }

    function daysArrayFun($day){
        switch($day){
            case "sun":
                $day = "Sunday";
                break;
            case "tue":
                $day = "Tuesday";
                break;
            case "wed":
                $day = "Wednesday";
                break;
            case "thu":
                $day = "Thursday";
                break;
            case "fri":
                $day = "Friday";
                break;
            case "sat":
                $day = "Saturday";
                break;
            default:
                $day = "Monday";
        }
        return $this->daysArray = $day;
    }

    function addRule() {
        if ($this->RuleData) {
            $columns = implode(', ', array_keys($this->RuleData));
            $values  = implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($this->RuleData), $GLOBALS['con']), array_values($this->RuleData)));
            $sql = "INSERT INTO rules ($columns) VALUES ('$values')";

            $result = mysqli_query($GLOBALS['con'], $sql);

            if (!$result) {
                // Log or display the error
                error_log("MySQL Error: " . mysqli_error($GLOBALS['con']));
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    function updateRule($RL_Id) {
        if ($this->UpdateRuleData) {
            $RuleDataArr = [];
            foreach ($this->UpdateRuleData as $key => $value) {
                $escapedValue = mysqli_real_escape_string($GLOBALS['con'], $value);
                $RuleDataArr[] = "$key = '$escapedValue'";
            }

            $RuleData = implode(', ', $RuleDataArr);

            // Escape or cast RL_Id
            $RL_Id = (int)$RL_Id; // If it's an integer. If string, use: mysqli_real_escape_string

            $sql = "UPDATE rules SET $RuleData WHERE RL_Id = $RL_Id";

            $result = mysqli_query($GLOBALS['con'], $sql);
            // echo "<pre>";print_r($result);die;
            if (!$result) {
                error_log("MySQL Error: " . mysqli_error($GLOBALS['con']));
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    function checkValidate($RL_Name, $RL_Description){
        $nameValid = $descValid = true;
        if($RL_Name){
            if(!preg_match("/^[a-zA-Z0-9\s,.'-]+$/", $RL_Name)){
                $nameValid = false;
            }
        }
        if($RL_Description){
            if(!preg_match("/^[a-zA-Z0-9\s,.'-]+$/", $RL_Description)){
                $descValid = false;
            }
        }
        if($nameValid==true && $descValid==true){
            return true;
        }else{
            return $responseArray = [
                'nameValid' => ($nameValid)?'':'Name field contain invalid charecters ',
                'descValid' => ($descValid)?'':', Description field contain invalid charecters'
            ];
        }
    }

}

?>