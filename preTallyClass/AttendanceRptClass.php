<?php
/**
 * Created By Bilin @ 27-AUG-2024
 * This class used for attendance related reports
*/
require_once("connection.php");

class AttendanceRptClass {

    var $listAttendance;
    var $total;
    var $selt_users;
    var $sel_qry;
    var $approve_leaves;
    var $rh_batch;
    var $rh_days;
    var $repay_list;
    // list all users selected month attendance daily based
	function listReptAttendance( $params = [] )
	{
		extract($params);
		$start  	= (isset($start)) ? $start:0;
		$limit  	= (isset($limit)) ? $limit:0;
		$month  	= (isset($month)) ? str_pad($month, 2, "0", STR_PAD_LEFT):date('m');
		$year  		= (isset($year)) ? $year:date('Y');
		$lastday  	= (isset($lastday)) ? str_pad($lastday, 2, "0", STR_PAD_LEFT):date('d');
		$office_id 	= (isset($office_id)) ? $office_id:0;
		$user_id  	= (isset($user_id)) ? $user_id:0;
		$branch_id  = (isset($branch_id)) ? $branch_id:0;
        $sorted     = (isset($sorted)) ? $sorted : 0; //25-05-25
		$username  	= (isset($username)) ? trim($username):"";
		$sort  		= (isset($sort)) ? trim($sort):"name";
		$order  	= (isset($order) && $order == "DESC") ? "DESC":"ASC";
		$lastdate 	= $year."-".$month."-".$lastday;
		$newdate 	= "2024-08-21";
		$relaxation = (isset($relaxation)) ? $relaxation:1;
		$comm_gracetime = (isset($comm_gracetime)) ? $comm_gracetime : 0;
		// function and output variables
		$this->total 			= 0;
		$this->listAttendance 	= [];
        $UsersKey               = []; //25-04-25
		$this->sel_qry 			= '';
		$this->selt_users 		= [];
		$fields 	= "";
		$tables 	= " FROM users_auth AS US"
					. " LEFT JOIN locations AS LC ON (LC.LC_Id = US.LC_Id)"
					. " LEFT JOIN departments AS DP ON (DP.DP_Id = US.DP_Id)"
					. " LEFT JOIN designations AS DG ON (DG.DG_Id = US.DG_Id)";
		$where 		= " WHERE (LAST_DAY(US.US_ResignDate) >= '" . $lastdate . "' OR US.US_ResignFlag = 0) AND (LAST_DAY(US.US_BlkdDate) >= '" . $lastdate . "' OR US.US_Status = 1) AND US.US_DOJ <= '" . $lastdate . "' AND US.US_Status != 5";	
		if ( $office_id > 0) {
			$where 	.= " AND US.OF_Id = '".$office_id."'";
		}
		if ( $user_id > 0) {
			$where 	.= " AND US.US_Id = '".$user_id."'";
		}else if (isset($emp_ids) && !empty($emp_ids)) { //09-04-2025
            $where  .= " AND US.US_Id IN (".implode(',',$emp_ids).")";
        }	
		if ( $username != "") {
			$where 	.= " AND CONCAT(US.US_FName, ' ', US.US_LName) LIKE '".mysqli_real_escape_string($GLOBALS['con'],ucfirst($username))."%'";
		}	
		if ( $branch_id > 0 ) {
			$where 	.= " AND LC.LC_Id = '".$branch_id."'";	
		}
		// find the total once the limit greater than 0
		if ( $limit > 0 ) {
			$sql_count 	= "SELECT COUNT(US.US_Id) AS usercount ".$tables.$where;
			$res_count 	= mysqli_query($GLOBALS['con'], $sql_count);
        	$row_count 	= mysqli_fetch_assoc($res_count);
        	$this->total= $row_count["usercount"];
        	$this->sel_qry  = $sql_count;
		} else {
			$this->total= 1;
		}
		// get all the users based on the search once total greater than 0
		if ( $this->total > 0 ) {

			if (isset($get_salary) && $get_salary == 1) {
				$fields .= ", SAL.US_GrossSal, SAL.US_CcaSal, SAL.US_DASal, SAL.US_HRASal, SAL.US_ConveySal, SAL.US_EduSal, SAL.US_MedSal, SAL.US_MiscSal, SAL.US_BasicSal, SAL.US_DedEPF, SAL.US_DedESI, SAL.US_DedLWF, SAL.US_DedSalTDS, SAL.US_DedMealCard, SAL.SS_Id, ST.SS_DedESI_Type, ST.SS_DedESI, ST.SS_DedEPF_Type, ST.SS_DedEPF, ST.SS_EmpConEPF_Type, ST.SS_EmpConEPF, ST.SS_EmpConESI_Type, ST.SS_EmpConESI, ST.SS_Status, ST.SS_EmpConLWF_Type, ST.SS_EmpConLWF, ST.SS_DedProfTDS ";
				$tables .= " LEFT JOIN users_salary AS SAL ON (SAL.US_Id = US.US_Id) "   
                ." LEFT JOIN salary_structures AS ST ON (ST.SS_Id = SAL.SS_Id) ";
			}

			$sql_user 	= "SELECT US.US_Id, US.OF_Id,US.DP_Id, US.US_DOJ, US.US_ResignDate, US.US_EMPID, DP.DP_Name, DG.DG_Name, LC.LC_Name, LC.ST_Id, US.US_FName, US.US_LName, US.US_LoginTime, US.US_LogoutTime, US.US_WrkHours, US.US_AttndFlag, US.US_WrkHrFlag, US.US_AttndDate, US.US_WrkHrDate, US.CS_MnthlyAlwdLateSign ".$fields." ".$tables.$where."  GROUP BY US.US_Id ORDER BY ";
            switch ($sort) { //25-04-25
                case "name"     : $sql_user.= " US.US_FName ".$order.", US.US_Id DESC"; break;
                case "branch"   : $sql_user.= " LC.LC_Name ".$order.", US.US_Id DESC"; break;
                default         : $sql_user.= " US.US_Id DESC "; break;             
            }
			if ( $limit > 0 ) {
				$sql_user 	.= ' Limit '.$start.','.$limit;
			} 
			$res_user 	= mysqli_query($GLOBALS['con'], $sql_user);
			$workflagusr= "";
            $uki        = 0;
	        while ($row_user = mysqli_fetch_assoc($res_user)) {
	        	//get all users from the database used for process attendance;
                if ($sorted == 1) { //25-04-25
                    $UsersKey[$row_user['US_Id']]       = $uki;
                    $this->listAttendance[$uki]         = $row_user;
                    $this->listAttendance[$uki]['list'] = [];
                } else {
    	            $this->listAttendance[$row_user['US_Id']] = $row_user;
    	            $this->listAttendance[$row_user['US_Id']]['list'] = [];
                }
	            array_push($this->selt_users, $row_user['US_Id']);

	            if ($row_user['US_WrkHrFlag'] == 1) {
	            	$workflagusr .= ($workflagusr != "") ? " OR (":"(";
	            	$workflagusr .= " US_ID = ".$row_user['US_Id']." AND AT_Date > '".$row_user['US_WrkHrDate']."'";
	            	$workflagusr .= ")";
	            }
	        }
	        if (!empty($this->selt_users)) { 
	        	$workflagqry     = "";
	        	if ($workflagusr != "") {
	        		$workflagqry = " AND IF( ".$workflagusr.", AT_Hours >= 0, AT_Hours > 120 )";
	        	} else {
	        		$workflagqry     = " AND AT_Hours > 120";
	        	}
	        	$this->total 	= ($limit == 0 && $this->total == 1) ? count($this->selt_users): $this->total;
		        // list of users available in selected month
		        $usids 	= implode(",", $this->selt_users);		        
		        $sql 	= "SELECT US_ID, AT_Date, AT_SignIn, AT_Date, AT_SignOut, AT_Hours,AT_Status, AT_SignInDelay, AT_SignOutEarly, AT_ExtraTime, AT_DutyLeft, AT_AllotTime, IF(AT_Date >='".$newdate."', '1','0') As new_cal, '".$comm_gracetime."' AS grase_time "
	                . " FROM attendance "
	                . " WHERE US_ID IN(".$usids.") AND MONTH(AT_Date) ='".$month."' AND YEAR(AT_Date)='".$year."' ".$workflagqry;

	            $res 	= mysqli_query($GLOBALS['con'], $sql);
		        while ($row = mysqli_fetch_assoc($res)) {

		        	if ($row['new_cal'] == 1) {
		        		$row['relaxation'] = 1;
		        		$row['grase_time'] = 0;
		        	} else if ($row['AT_Date'] > "2023-02-20") {
		        		$row['relaxation'] = 31;
		        	} else {
		        		$row['relaxation'] = $relaxation;
		        	}
                    if ($sorted == 1) { //25-04-25
                        $uki        = $UsersKey[$row['US_ID']];
                        $this->listAttendance[$uki]['list'][$row['AT_Date']] = $row;
                    } else {
		              $this->listAttendance[$row['US_ID']]['list'][$row['AT_Date']] = $row;
                    }
		        } 		         
		        $this->sel_qry  = $sql;   
		    } else {
		    	$this->total 	= 0;
		        $this->sel_qry  = $sql_user;
		    }
		}
	}
	//get all leave list of all user in selected months
	// Created By Bilin  @ 30-aug-2024
	function getApprovedLeaves($month='', $year='', $leavetype=[]) 
	{	
        $this->approve_leaves = array(); // return variables
        $userids  	= implode(",",$this->selt_users);
        $ltypeids 	= implode(",",$leavetype);
        $userleaves = [];
        $sql 		= "SELECT ld.LRD_Id, lt.LT_Name, ld.LRD_Date, ld.LRD_Days, lt.LT_Id, ld.LRD_Session, lr.LR_AppliedFor AS user_id "
        . " FROM leave_reqdays ld"
        . " LEFT JOIN leave_request lr ON (lr.LR_Id = ld.LR_Id)"
        . " LEFT JOIN leave_type lt ON (ld.LT_Id = lt.LT_Id)"
        . " WHERE lr.LR_AppliedFor IN (" . $userids . ")  AND LR_Status=2 AND (MONTH(LRD_Date ) =" . $month . " AND YEAR(LRD_Date) = " . $year . ") AND lt.LT_Id IN (" . $ltypeids . ") "
        . " GROUP BY ld.LRD_Id ORDER BY ld.LRD_Id ASC";
        $res 		= mysqli_query($GLOBALS['con'], $sql);
        $i 			= 0;
        $this->sel_qry  = $sql;
        while ($row = mysqli_fetch_object($res)) {

        	if (!isset($userleaves[$row->user_id])) {
        		$userleaves[$row->user_id] = ['list'=>[], 'count'=>[]];
        	}
        	// set the leave request details 
        	$userleaves[$row->user_id]['list'][] = ['LT_Name'=>$row->LT_Name, 'LRD_Date'=>$row->LRD_Date, 'LT_Id'=>$row->LT_Id, 'LRD_Session'=>$row->LRD_Session];
        	// set the leave type based total 
        	if (!isset($userleaves[$row->user_id]['count'][$row->LT_Id])) {
        		$userleaves[$row->user_id]['count'][$row->LT_Id] = ['total'=>0, 'name'=>$row->LT_Name];
        	}
        	$userleaves[$row->user_id]['count'][$row->LT_Id]['total'] = $userleaves[$row->user_id]['count'][$row->LT_Id]['total']+$row->LRD_Days;
            $i++;
        }

        $this->approve_leaves = $userleaves;
    }
    /**
     * get the last attendance date of previous month 
     * and first attendance of next month
     * get all users list 
    */
    function getAdjMnthAttendance($month, $year) 
    {
    	$adjust_att_usr = array();
    	$userids  		= implode(",",$this->selt_users);
        $firstDate 		= $year . "-" . $month . "-01";
        $lastDate 		= date("Y-m-t", strtotime($firstDate));
        $next_month_ts 	= strtotime($firstDate . ' +1 month');
        $prev_month_ts 	= strtotime($firstDate . ' -1 month');
        $next_Ym 		= date('Y-m', $next_month_ts);
        $prev_Ym 		= date('Y-m', $prev_month_ts);
        $next 			= explode("-", $next_Ym);
        $prev 			= explode("-", $prev_Ym);
        
        $sqllast 		= "SELECT AT.US_Id, MAX(AT.AT_Date) AS AT_Date FROM attendance AS AT WHERE AT.AT_Date < '" . $firstDate . "' AND MONTH(AT.AT_Date)=" . $prev[1] . " AND YEAR(AT.AT_Date)=" . $prev[0] . " AND  AT.US_Id IN (" . $userids . ") GROUP BY AT.US_Id ORDER BY AT.AT_Date DESC";
        $reslast 		= mysqli_query($GLOBALS['con'], $sqllast);
        $this->sel_qry  = $sqllast;
        while($rowlast 	= mysqli_fetch_assoc($reslast)) {
        	$adjust_att_usr[$rowlast['US_Id']]["last"] = $rowlast['AT_Date'];
        }
        if ($lastDate == date('Y-m-d')) {
        	$sqlnxt 	= "SELECT AT.US_Id, AT.AT_Date AS AT_Date FROM attendance AS AT WHERE AT.AT_Date = '" . $lastDate . "' AND  AT.US_Id IN (" . $userids . ")  GROUP BY AT.US_Id ORDER BY AT.AT_Date DESC";
        } else {
        	$sqlnxt 	= "SELECT AT.US_Id, MIN(AT.AT_Date) AS AT_Date FROM attendance AS AT WHERE AT.AT_Date > '" . $lastDate . "' AND MONTH(AT.AT_Date)=" . $next[1] . " AND YEAR(AT.AT_Date) =" . $next[0] . " AND  AT.US_Id IN (" . $userids . ")  GROUP BY AT.US_Id ORDER BY AT.AT_Date DESC";
        }        
        $resnext 	= mysqli_query($GLOBALS['con'], $sqlnxt);
        while($rownxt 	= mysqli_fetch_assoc($resnext)) {
        	$adjust_att_usr[$rownxt['US_Id']]["next"] = $rownxt['AT_Date'];
        }

        return $adjust_att_usr;
    }
    /**
     * Get all users punching delay/early summary
     * sum of all(late sign in, early signout, duty left, extra time spend)
    */
    function getPunchTime($month, $year) 
    { 
    	$punchDet 	= [];    	
    	$userids  	= implode(",",$this->selt_users);
        $firstDate 	= $year . "-" . $month . "-01";
        if (strtotime($firstDate) < strtotime("2024-08-21")) {
            
           if ($year <= 2024 && $month < 8) {
                return $punchDet;
           } else {
                $firstDate = '2024-08-21';
           }
        }
        $lastDate 	= date("Y-m-t", strtotime($firstDate));
        $sql 		= "SELECT US_Id, SUM(AT_SignInDelay) AS late_in,SUM(AT_SignOutEarly) AS early_out,SUM(CASE WHEN AT_ExtraTime>=0 THEN AT_ExtraTime ELSE 0 END) AS extra_time, SUM(CASE WHEN (AT_DutyLeft>=0 AND AT_Hours > 120) THEN AT_DutyLeft ELSE 0 END) AS duty_left from `attendance` where AT_Date between '".$firstDate."' and '".$lastDate."'  AND US_Id IN (" . $userids . ") GROUP BY US_Id ";
        $this->sel_qry  = $sql;
        $result = mysqli_query($GLOBALS['con'], $sql); 
        while($rows = mysqli_fetch_assoc($result)) {

        	$punchDet[$rows['US_Id']] = ['late_in'=>$rows['late_in'], 'early_out'=>$rows['early_out'], 'extra_time'=>$rows['extra_time'], 'duty_left'=>$rows['duty_left']];
        }

        return $punchDet;
    }
    /**
     * Get all users rh leave list of an year
    */
    function getTakenRHBatches($year) 
    {
    	$userids= implode(",",$this->selt_users);
        $sql 	= "SELECT HD_Batch, US_Id, HD_Date FROM rh_leavelogs WHERE YEAR(HD_Date) = " . $year." AND US_Id IN (" . $userids . ") GROUP BY US_Id, HD_Batch";
        $result = mysqli_query($GLOBALS['con'], $sql);        
        $this->rh_batch = [];
        $this->rh_days 	= [];
        while ($arr = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

        	if (!isset($this->rh_batch[$arr["US_Id"]])) {
        		$this->rh_batch[$arr["US_Id"]] 	= [];
        		$this->rh_days[$arr["US_Id"]] 	= [];
        	}
            $this->rh_batch[$arr["US_Id"]][] 	= $arr["HD_Batch"];
            $this->rh_days[$arr["US_Id"]][] 	= $arr["HD_Date"];
        }
    }
    /**
    * Get the information about the salary advance payments
    */
    function getRepaymentAmounts($month, $year) 
    {
        $this->repay_list 	= [];
        $userids  			= implode(",",$this->selt_users);
        $sql 		= "SELECT RS.SRS_Id, RS.SRS_RepaymentAmt, SAP.SA_PaymentTime, RS.US_Id FROM repayment_schedule AS RS LEFT JOIN salary_advance_payment AS SAP ON (SAP.SA_Id = RS.SA_Id) WHERE RS.US_Id IN (" . $userids . ") AND RS.SRS_Status = 1 AND  RS.SRS_Month ='" . $month . "-" . $year . "' ORDER BY RS.US_Id ASC ";
        $result 	= mysqli_query($GLOBALS['con'], $sql);        
        while ($row = mysqli_fetch_assoc($result)) {

        	if (!isset($this->repay_list[$row["US_Id"]])) {
        		$this->repay_list[$row["US_Id"]] 	= ["loan"=>0, 'advance'=>0, 'sr_ids'=>[]];
        	}
            if ($row["SA_PaymentTime"] > 2) {
                $this->repay_list[$row["US_Id"]]["loan"] 	+= round($row['SRS_RepaymentAmt']);
            } else {
            	$this->repay_list[$row["US_Id"]]["advance"] += round($row['SRS_RepaymentAmt']);
            }
            array_push($this->repay_list[$row["US_Id"]]["sr_ids"], $row['SRS_Id']);
        }
    }
	

}
?>