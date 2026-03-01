<?php
/**
* Break time type list, employee break time save update edit and listing 
* Break time related all reports etc entered here
* Created BY Bilin @ 11-06-2025 Last updated @ 01-07-2025
*/
require_once("connection.php");
class BreakTimeClass {

	var $btypeList; // break type list array
	var $btUsrList; // break timelist array
	var $btUTotal; // break time entry total based on filters
	var $btUdayTotal; // break time user day wise total

	// get the break time settings
	function getBreakSettings($office_id = 0)
	{
		$sql 	= 'SELECT total_break, entry_users, view_users FROM break_settings WHERE (office_id = "'.$office_id.'" OR office_id = 0) ORDER BY office_id DESC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
	   	$row 	= mysqli_fetch_row($res);	   
	   	if (!empty($row)) {

	   		return ['time'=>$row[0], 'users'=>json_decode($row[1]), 'view_users'=>json_decode($row[2])];
	   	}
	   	return ['time'=>60, 'users'=>[],'view_users'=>[]];
	}
	
	// list all break time types 
	function getAllBreakTypes($status = 1) 
	{
		$brektypes 		= [];
		$sql 			= 'SELECT id, title, from_time, to_time FROM break_type WHERE 1 ';
		if ($status) {
			$sql 		.= ' AND status = "'.$status.'"';
		}
		$sql 			.= ' ORDER BY id ASC';
		$res 			= mysqli_query($GLOBALS['con'], $sql);
	    while ($row 	= mysqli_fetch_object($res)) {

	    	$brektypes[] = $row;
	    }

	    return $brektypes; 
	}
	// get the break type is official or not
	function getBreakTypeisOfficial($id) 
	{
		$sql 	= 'SELECT not_calculate FROM break_type WHERE id ='.$id;
		$res 	= mysqli_query($GLOBALS['con'], $sql);
		$row 	= mysqli_fetch_row($res);
		return  ($row[0] == 1) ? 1:0;
	}
	// get the break type name based on the id fields
	function getBreakTypeName($id) {
		$sql 	= 'SELECT title FROM break_type WHERE id ='.$id;
		$res 	= mysqli_query($GLOBALS['con'], $sql);
		$row 	= mysqli_fetch_row($res);
		return  (!empty($row)) ? $row[0]:"This";
	}
	// get the break time existing data details based on the bt id
	function getBreakTimeEntry($id = 0)
	{
		$sql 	= 'SELECT * FROM break_time_user WHERE id = "'.$id.'" ORDER BY id DESC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
	   	$row 	= mysqli_fetch_array($res, MYSQLI_ASSOC);

	   	return (!empty($row)) ? $row : [];
	}
	// update the in time of selected entry
	function updateToTime($id, $from_time, $userid=0)
	{
		$to_time= date('H:i');
		$start 	= strtotime($from_time);
		$end 	= strtotime($to_time.':00');
		$mins 	= ($end - $start) / 60;
		$sql 	= 'UPDATE break_time_user SET to_time= "'.$to_time.'", time_taken="'.$mins.'", updated_at="'.date('Y-m-d H:i:s').'", updated_by="'.$userid.'", status="1" ,is_edited="0"  WHERE id='.$id;
		mysqli_query($GLOBALS['con'], $sql);

		return 1;
	}
	// update the full entry details
	function updateBreakTime($inputs, $id=0) 
	{
		$updstring 		= "";
		foreach ($inputs as $key => $value) { 
            $updstring 	= $updstring .$key ."='".$value."', ";
        }
        $updstring = substr($updstring, 0, -2);
        $sql = "UPDATE break_time_user SET $updstring WHERE id=".$id;
        mysqli_query($GLOBALS['con'],$sql);

        return 1;
	}
	// check the user based or break based or date based or status based deails present
	// duplicate checking
	function checkDupBreakTime($inParams= [])
	{
		extract($inParams);
		$where 	= ' WHERE 1 ';
		if (isset($us_id)) {

			$where 	.= ' AND us_id="'.$us_id.'"'; 			
		}
		if (isset($status)) {

			$where 	.= ' AND status="'.$status.'"'; 			
		}
		if (isset($break_date)) {

			$where 	.= ' AND break_date="'.$break_date.'"'; 			
		}
		if (isset($break_id)) {

			$where 	.= ' AND break_id="'.$break_id.'"'; 			
		}		
		if (isset($from_time)) {

			$where 	.= ' AND from_time="'.$from_time.'"'; 			
		}
		if (isset($id)) {

			$where 	.= ' AND id != "'.$id.'"'; 			
		}

		$sql 	= 'SELECT id FROM break_time_user '.$where.' ORDER BY id DESC LIMIT 0, 1';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
	   	$row 	= mysqli_fetch_row($res);

	   	return (!empty($row) && $row[0] > 0) ? 1: 0;
	}
	//save the break time 
	function saveBreakTime($inParams=[])
	{
		$sql = "INSERT INTO break_time_user ( " . implode(', ',array_keys($inParams)) . ") VALUES (" . "'" . implode("','", array_values($inParams)) . "'" . ")";
		$res = mysqli_query($GLOBALS['con'],$sql);
		
		return ($res) ? 1: 0;
	}
	// list all break time entry
	function listBreakTime($inParams=[])
	{
		extract($inParams);
		$this->btUsrList 	= array(); 
		$this->btUTotal 	= 0; 
		$where 		= ' WHERE 1 ';
		$tables 	= ' FROM `break_time_user` AS btu'
					. ' INNER JOIN `users_auth` AS us ON (us.US_Id = btu.us_id)'
					. ' INNER JOIN `break_type` AS bt ON (bt.id = btu.break_id)'
					. ' LEFT JOIN `locations` AS lc ON (lc.LC_Id = us.LC_Id)';		
		$fields 	= '';
		$joins		= '';			
		if (isset($off_id) && $off_id > 0) {
			$where 		.= ' AND us.OF_Id = "'.$off_id.'"';
		}
		if (isset($user_id) && $user_id > 0) {
			$where 		.= ' AND btu.us_id = "'.$user_id.'"';
		}		
		if (isset($user_ids) && !empty($user_ids)) {
			$where 		.= ' AND btu.us_id IN ('.implode(',', $user_ids).')';
		}
		if (isset($break_id) && $break_id > 0) {
			$where 		.= ' AND btu.break_id = "'.$break_id.'"';
		}		
		if (isset($status) && $status != '') {
			$where 		.= ' AND btu.status = "'.$status.'"';
		}
		if (isset($is_edited) && $is_edited != "") {
			$where 		.= ' AND btu.is_edited = "'.$is_edited.'"';
		}
		if (isset($from_date) || isset($to_date)) {
			if ($from_date != '' && $to_date != '') {
				$where 	.= ' AND btu.break_date between "'.$from_date.'" AND "'.$to_date.'"';
			} else if ($from_date != '') {
				$where 	.= ' AND btu.break_date >= "'.$from_date.'"';
			} else if ($to_date != '') {
				$where 	.= ' AND btu.break_date <= "'.$to_date.'"';
			}
		}
		if (isset($search) && $search != '') {
			$where 		.= ' AND (concat(us.US_FName, " ", us.US_LName)  like "%'.$search.'%" OR lc.LC_Name like "%'.$search.'%")';
		}
		// find the count from the query
		$sql_count 		= 'SELECT COUNT(DISTINCT btu.id) '.$tables.$joins.' '.$where;
		$res_count 		= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count		= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->btUTotal = $row_count[0];
        // get the list of data 
        if ($this->btUTotal > 0) {

        	$slno 		= 0;
        	$sql 		= 'SELECT btu.id, btu.us_id, btu.break_date, btu.from_time, btu.to_time, btu.time_taken, btu.created_by, btu.created_at, btu.updated_at, btu.is_edited, btu.status, bt.title, bt.max_time, concat(us.US_FName, " ", us.US_LName, ", ",lc.LC_Name) AS employee_name, btu.remarks, bt.not_calculate '.$fields
        	.' '.$tables.$joins
        	.' '.$where
        	.' GROUP BY btu.id'
        	.' ORDER BY btu.from_time DESC, btu.id DESC';
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $this->btUsrList[] 	= $row;
            }
        }
	}
	// find the date wise total of all employees
	// listing filters are also used in this functions
	function listDailyUserTotal($inParams=[])
	{
		extract($inParams);
		$this->btUdayTotal = [];
		$where 		= ' WHERE btu.status = 1 AND bt.not_calculate = 0';
		$tables 	= ' FROM `break_time_user` AS btu'
					. ' INNER JOIN `break_type` AS bt ON (bt.id = btu.break_id)'
					. ' INNER JOIN `users_auth` AS us ON (us.US_Id = btu.us_id)';
		$fields 	= '';
		$groupby 	= ' btu.us_id, btu.break_date';
		$orderby 	= ' btu.us_id ASC';
		$having 	= '';
		$is_entry 	= (isset($is_entry) && $is_entry== "1") ? 1: 0;
		if (isset($off_id) && $off_id > 0) {
			$where 		.= ' AND us.OF_Id = "'.$off_id.'"';
		}
		if (isset($user_id) && $user_id > 0) {
			$where 		.= ' AND btu.us_id = "'.$user_id.'"';
		}
		if (isset($user_ids) && !empty($user_ids)) {
			$where 		.= ' AND btu.us_id IN ('.implode(',', $user_ids).')';
		}
		if (isset($is_edited) && $is_edited != "") {
			$where 		.= ' AND btu.is_edited = "'.$is_edited.'"';
		}
		if (isset($from_date) || isset($to_date)) {
			if ($from_date != '' && $to_date != '') {
				$where 	.= ' AND btu.break_date between "'.$from_date.'" AND "'.$to_date.'"';
			} else if ($from_date != '') {
				$where 	.= ' AND btu.break_date >= "'.$from_date.'"';
			} else if ($to_date != '') {
				$where 	.= ' AND btu.break_date <= "'.$to_date.'"';
			}
		}
		if ($is_entry == 1) {
			$fields 	= ', btu.id';
			$groupby 	= ' btu.id';
			$orderby 	= ' btu.id ASC';
		} else {			
			if (isset($break_id) && $break_id > 0) {
				$where 		.= ' AND btu.break_id = "'.$break_id.'"';
			}
		}
		if (isset($time_taken) && $time_taken > 0) {
			$having 	= ' Having total_time >= '.$time_taken; 
		}
		$sql 		= 'SELECT btu.us_id, btu.break_date, sum(btu.time_taken) AS total_time '.$fields
        	.' '.$tables
        	.' '.$where
        	.' GROUP BY '.$groupby
        	.' '.$having
        	.' ORDER BY '.$orderby;
        $res 		= mysqli_query($GLOBALS['con'], $sql);
    	while ($row = mysqli_fetch_object($res)) {
    		
            if (!isset($this->btUsrList[$row->break_date])) {
            	$this->btUsrList[$row->break_date] = [];
            } 
            if ($is_entry == 1) {
            	if (!isset($this->btUsrList[$row->break_date][$row->us_id])) {
            		$this->btUsrList[$row->break_date][$row->us_id] = ['total'=>0];
            	}

            	$this->btUsrList[$row->break_date][$row->us_id]['total'] += $row->total_time;
            	$this->btUsrList[$row->break_date][$row->us_id][$row->id] = $this->btUsrList[$row->break_date][$row->us_id]['total'];
            } else {

           	 	$this->btUsrList[$row->break_date][$row->us_id] = $row->total_time;
            }  
        }			
	}
	/**
	* Date Wise total of selected users or all users in a selected period of time
	* Created by Bilin @ 26-05-2025
	*/
	function getDailyUserTime($inParams=[])
	{
		extract($inParams);
		$dailyData 	= [];
		$type 		= (isset($type)) ? (int)$type: 1; // return type format
		$where 		= ' WHERE btu.status = 1';
		$tables 	= ' FROM `break_time_user` AS btu'
					. ' INNER JOIN `break_type` AS bt ON (bt.id = btu.break_id)';
		$groupby 	= ' btu.us_id, btu.break_date, bt.not_calculate';
		$orderby 	= ' btu.us_id ASC, btu.break_date ASC';		
		$fields 	= '';
		$having 	= '';
		if (isset($user_id) && $user_id > 0) {
			$where 	.= ' AND btu.us_id = "'.$user_id.'"';
		}		
		if (isset($from_date) || isset($to_date)) {
			if ($from_date != '' && $to_date != '') {
				$where 	.= ' AND btu.break_date between "'.$from_date.'" AND "'.$to_date.'"';
			} else if ($from_date != '') {
				$where 	.= ' AND btu.break_date >= "'.$from_date.'"';
			} else if ($to_date != '') {
				$where 	.= ' AND btu.break_date <= "'.$to_date.'"';
			}
		}
		$sql 		= 'SELECT btu.us_id, btu.break_date, bt.not_calculate, sum(btu.time_taken) AS total_time '.$fields
        	.' '.$tables
        	.' '.$where
        	.' GROUP BY '.$groupby
        	.' '.$having
        	.' ORDER BY '.$orderby;
        $res 		= mysqli_query($GLOBALS['con'], $sql);
    	while ($row = mysqli_fetch_object($res)) {

    		if ($type == 1) {
    			if (!isset($dailyData[$row->break_date])) {
	    			$dailyData[$row->break_date] = ['0'=>0, '1'=>0, '2'=>0];
	    		}
	    		$dailyData[$row->break_date][$row->not_calculate] = $row->total_time;
    		}   		
    		
    	}

    	return $dailyData;
	}
	// list all break time report entrys
	function listBreakTimeRpt($inParams=[])
	{
		extract($inParams);
		$export 			= (isset($export) && $export == "yes") ? "yes":'';
		$this->btUsrList 	= array(); 
		$this->btUTotal 	= 0; 
		$breaktypes 		=  $this->getAllBreakTypes(1);
		$brektypary 		= [];
		foreach ($breaktypes as $key => $val) {
			$brektypary[$val->id] = 0;
		}
		$where 		= ' WHERE 1 ';
		$where2 	= ' WHERE 1 ';
		$tables 	= ' FROM `break_time_user` AS btu'
					. ' INNER JOIN `users_auth` AS us ON (us.US_Id = btu.us_id)'
					. ' INNER JOIN `break_type` AS bt ON (bt.id = btu.break_id)'
					. ' LEFT JOIN `locations` AS lc ON (lc.LC_Id = us.LC_Id)';
		$joins		= '';	
		$having 	= '';
		if (isset($off_id) && $off_id > 0) {
			$where 		.= ' AND us.OF_Id = "'.$off_id.'"';
		}
		if (isset($user_id) && $user_id > 0) {
			$where 		.= ' AND btu.us_id = "'.$user_id.'"';
		}		
		if (isset($branch_id) && $branch_id > 0) {
			$where 		.= ' AND us.LC_Id = "'.$branch_id.'"';
		}		
		if (isset($from_date) || isset($to_date)) {
			if ($from_date != '' && $to_date != '') {
				$where 	.= ' AND btu.break_date between "'.$from_date.'" AND "'.$to_date.'"';
			} else if ($from_date != '') {
				$where 	.= ' AND btu.break_date >= "'.$from_date.'"';
			} else if ($to_date != '') {
				$where 	.= ' AND btu.break_date <= "'.$to_date.'"';
			}
		}
		if (isset($search) && $search != '') {
			$where 		.= ' AND concat(us.US_FName, " ", us.US_LName)  like "%'.$search.'%"';
		}
		if (isset($bkstatus) && $bkstatus == 1) {
			$where 		.= ' AND btu.status = 0 ';
			$where2 	.= ' AND btu.status = 0 '; 
		}
		$having 		= '';
		$slno 			= 0;		
		if (isset($bkstatus) && $bkstatus == 2 && isset($total_time)) {
			$having 	= ' Having total_time > '.$total_time;
			$totalcalulate 	= 0;
		} else {
			$totalcalulate 	= 1;
			// find the count from the query
			$sql_count 		= 'SELECT COUNT(DISTINCT CONCAT(btu.us_id,btu.break_date)) '.$tables.$joins.' '.$where;
			$res_count 		= mysqli_query($GLOBALS['con'], $sql_count);
			$row_count		= mysqli_fetch_array($res_count,MYSQLI_NUM);
	        $this->btUTotal = $row_count[0];
		}
		//AND bt.not_calculate = 0
		$sql 	= 'SELECT btu.us_id, btu.break_date,  SUM(IF(bt.not_calculate != 1,btu.time_taken, 0)) AS total_time,  SUM(IF(bt.not_calculate = 1,btu.time_taken, 0)) AS othtotal_time, concat(us.US_FName, " ", us.US_LName) AS employee_name, lc.LC_Name AS branch '
		.' '.$tables.$joins
    	.' '.$where.' '
    	.' GROUP BY btu.us_id, btu.break_date'
    	.' '.$having
    	.' ORDER BY btu.break_date ASC, us.US_FName ASC';
    	if (isset($limit) && $limit > 0 && $totalcalulate == 1) {

    		$sql 	.= ' LIMIT '.$start.','.$limit;
    		$slno 	= $start;
    	}
    	$this->sqlqry = $sql;
    	$res 		= mysqli_query($GLOBALS['con'], $sql);
    	$exUserQry  = '';
    	$keyArrys 	= [];
    	$i 			= 0;
    	while ($row = mysqli_fetch_object($res)) {
            
            $slno++;
            if (!isset($keyArrys[$row->break_date])) {
            	$keyArrys[$row->break_date] = [];
            }
            $keyArrys[$row->break_date][$row->us_id] = $i; 
            if ($export== 'yes') {
            	$this->btUsrList[$i] = ['slno'=>$slno, 'name'=> $row->employee_name, 'branch'=> $row->branch, 'break_date'=>date("m-d-Y",strtotime($row->break_date))];
            	foreach ($brektypary as $key => $value) {
            		$this->btUsrList[$i][$key] = $value;
            	}
            	//$this->btUsrList[$i]	 			= $brektypary;
            	//$this->btUsrList[$i]['slno'] 		=  $slno;
            	//$this->btUsrList[$i]['name'] 		=  $row->employee_name;
            	//$this->btUsrList[$i]['branch'] 	=  $row->branch;
            	//$this->btUsrList[$i]['break_date']=  date("m-d-Y",strtotime($row->break_date));
            	$this->btUsrList[$i]['time_total'] 	=  $row->total_time;
            } else {
            	$this->btUsrList[$i] = ['slno'=>$slno, 'types'=>$brektypary, 'name'=> $row->employee_name,'branch'=> $row->branch,'time_total'=> $row->total_time, 'max'=>$brektypary,'break_date'=>$row->break_date, 'user_id'=>$row->us_id];
            }

            $exUserQry  .= ($exUserQry == '') ? ' AND (' : ' OR ';
            $exUserQry  .= '(btu.us_id = "'.$row->us_id.'" AND btu.break_date = "'.$row->break_date.'")';
            $i++;
        }
        if ($totalcalulate == 0) {
        	$this->btUTotal = $slno;
        }
        if ($this->btUTotal > 0) {
        	$exUserQry  .= ($exUserQry != '') ? ') ' : '';
    		$where2 	.= $exUserQry;
    		$sql 		= 'SELECT btu.us_id, btu.break_date, btu.break_id, SUM(btu.time_taken) AS total_time, bt.max_time, bt.not_calculate '
    		.' FROM `break_time_user` AS btu '
    		.' INNER JOIN `break_type` AS bt ON (bt.id = btu.break_id)'
    		.' '.$where2
    		.' GROUP BY btu.us_id, btu.break_date, btu.break_id'
    		.' ORDER BY btu.break_date, btu.us_id DESC';
    		$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {

        		$i 		= $keyArrys[$row->break_date][$row->us_id];
        		if ($export== 'yes') {
        			$this->btUsrList[$i][$row->break_id] 			= $row->total_time;
        		} else {
        			$this->btUsrList[$i]['types'][$row->break_id] 	= $row->total_time;
        			$this->btUsrList[$i]['max'][$row->break_id] 	= $row->max_time; 
        		}        		
        	}
        }

        return ($export== 'yes') ? $breaktypes :$brektypary;
	}



}
?>