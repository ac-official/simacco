<?php
/**
	* Accounts Related Class - accounts settings managements (group, ledger, and other  configurations etc)
	* Created By Bilin @ 09-07-2025
*/
require_once("connection.php");
class AccountsClass {
	// common varibles for return data 
	var $data_list;
	var $data_total;
	var $sql_query;
	var $msg;
	// static status name array
	function listStatus($flag=0)
	{
		if ($flag == 0) {
			return ["0"=>"Blocked","1"=>"Approved","2"=>"Pending","3"=>"Deleted","4"=>"Rejected"];
		} else if ($flag == 1) { 
			return ["0"=>"<![CDATA[<div style='color:red;'>Blocked</div>]]>","1"=>"Approved","2"=>"Pending","3"=>"Deleted","4"=>"Rejected"];
		}else if ($flag == 2) {
			return ["0"=>"Blocked","1"=>"Active"];
		}		
	}

	// combo box or listing data array based on the filters 
	// Group Types Listing - static list
	function ListGroupTypes($flag = 0)
	{
		if ($flag == 1) {
			return ['Capital'=>'Capital', 'Income'=>'Income', 'Expense'=>'Expense', 'IExpense'=>'Indirect Expense', 'Asset'=>'Asset', 'Liability'=>'Liability'];
		} else {
			return ['Capital'=>'Capital', 'Income'=>'Income', 'Expense'=>'Expense', 'Asset'=>'Asset', 'Liability'=>'Liability'];
		}
	} 
	// Group Or sub groups - basic
	function ListGroups($inputs)
	{
		extract($inputs);
		$group_list = [];
		$parent_list= [];
		$flag 		= (isset($flag)) ? (int)$flag:0;
		$tables 	= ' FROM acc_groups AS gp ';
		$join 		= '';
		$fields 	= '';
		$where 		= ' WHERE gp.status != 3 ';// deleted no need to list
		if (isset($status) && $status != '') {
			$where 	.= ' AND gp.status ="'.$status.'"';
		}
		if (isset($parent_id) && $parent_id != '') {
			$where 	.= ' AND gp.parent_id ="'.$parent_id.'"';
		}
		if (isset($type) && $type != '') {
			$where 	.= ' AND gp.type ="'.$type.'"';
		}	
		if (isset($search) && $search != '') {
			$where 	.= ' AND gp.title LIKE "%'.$search.'%"';
		}
		if ($flag == 1) {
			$join 	.= ' LEFT JOIN users_auth AS ua ON (ua.US_Id = gp.created_by)'
					. ' LEFT JOIN users_auth AS ua1 ON (ua1.US_Id = gp.verified_by)';
			$fields .= ', gp.created_at, gp.verified_at, gp.description, concat(ua.US_FName, " ", ua.US_LName) As created_user, concat(ua1.US_FName, " ", ua1.US_LName) AS verified_user ';
		}
		$sql 		= 'SELECT gp.id, gp.type, gp.parent_id, gp.title, gp.status '.$fields 
					. $tables 
					. $join
					. $where 
					. ' GROUP BY gp.id '
					. ' ORDER BY ';
		if ($flag != 1) {
			$sql 		.= ' gp.title ASC';
		} else {
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "group": $sql 	.= 'gp.title';	
				break;
				case "type": $sql 	.= 'gp.type';	
				break;
				default 	: $sql 	.= 'gp.id';	
				break;
			}
			$sql 		.= ' '.$orderby;
		}			
					
		$res 		= mysqli_query($GLOBALS['con'], $sql);
	    while ($row = mysqli_fetch_object($res)) {

	    	$group_list[] 			= $row;
	    	$parent_list[$row->id] 	= ['title'=>$row->title, 'status'=>$row->status]; 

	    }
	    // serial number for listin page
	    if ($flag == 1 && !empty($group_list)) { 
	    	$slno 					= 0;
	    	$statuslist 			= $this->listStatus(1);
	    	foreach ($group_list as $key => $val) {
	    		$slno++;
	    		$val->slno 			= $slno;
	    		$val->status_val 	= $statuslist[$val->status];
	    		$val->edit_status 	= ($val->parent_id == 0 || (isset($parent_list[$val->parent_id]) && $parent_list[$val->parent_id]['status'] != 0)) ? 1: 0;
	    		$val->parent_name 	= (isset($parent_list[$val->parent_id])) ? $parent_list[$val->parent_id]['title'] :'';
	    	}
	    }

		return $group_list;
	} 
	// get the group details based on the group id provided
	function getGroup($id = 0)
	{
		$sql 	= 'SELECT * FROM acc_groups WHERE id = "'.$id.'" ORDER BY id DESC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
	   	$row 	= mysqli_fetch_array($res, MYSQLI_ASSOC);

	   	return (!empty($row)) ? $row : [];
	}
	// save or update Group Details
	function saveGroup($inputs=[], $id=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';
		// check the duplicate if any
		// AND parent_id = "'.$inputs['parent_id'].'"
		$chksql 	= 'SELECT id FROM acc_groups WHERE id != "'.$id.'" AND type = "'.$inputs['type'].'" AND title = "'.$inputs['title'].'" ORDER BY id DESC LIMIT 0, 1';
		$chkres 	= mysqli_query($GLOBALS['con'], $chksql);
	   	$chkrow 	= mysqli_fetch_row($chkres);
	   	if (!empty($chkrow) && $chkrow[0] > 0) {
	   		$this->msg 		=  'Failed. Duplicate Entry!';
	   	} else if ($id > 0) { // update 
	   		// back up entry
	   		$this->backupTables($id, 1);

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_groups SET $updstring WHERE id=".$id;
	        mysqli_query($GLOBALS['con'],$sql);

	        $ret_status = 1;
	        $this->msg 	= 'Group Details Updated Successfully';
	   	}else { // save entry;
	   		$sql 	= "INSERT INTO acc_groups ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query($GLOBALS['con'],$sql);
			
			$ret_status = ($res) ? 1: 0;
			$this->msg 	= ($ret_status == 1) ? 'Group Details Saved Successfully':'Failed to Save Group Details';
	   	}

	   	return $ret_status;
	}
	// status updation of groups
	function updateGroupStatus($status, $id, $user_id, $oldStatus=2) 
	{
		if ($oldStatus != 2) {
			// back up entry
	   		$this->backupTables($id, 1);
		}
		$sql = "UPDATE acc_groups SET status='".$status."', verified_by='".$user_id."', verified_at='".date('Y-m-d H:i:s')."' WHERE id=".$id;
		mysqli_query($GLOBALS['con'],$sql);
	}
	// List all Sub Group with full parent names 
	function listGroupsCbo($inputs)
	{
		extract($inputs);
		$group_list = [];
		$table 		 = ' FROM acc_groups AS gp '
			. ' LEFT JOIN acc_groups AS gp1 ON (gp1.id=gp.parent_id)';
		$where 		 = ' WHERE gp.status != 3 AND (gp.parent_id = 0 OR gp1.status=1)';
		if (isset($status) && $status != '') {
			$where 	.= ' AND gp.status ="'.$status.'"';
		}
		if (isset($parent_id) && $parent_id == '-3') {
			$where 	.= ' AND (gp.parent_id > 0 OR gp.parent_id = 0)';
		}else if (isset($parent_id) && $parent_id == '-2') {
			$where 	.= ' AND (gp.parent_id > 0 OR gp.id NOT IN (SELECT DISTINCT gp2.parent_id FROM acc_groups AS gp2 WHERE gp2.status=1 AND gp2.parent_id > 0))';
		}else if (isset($parent_id) && $parent_id != '') {
			$where 	.= ($parent_id == '-1') ? ' AND gp.parent_id > 0' :' AND gp.parent_id ="'.$parent_id.'"';
		}
		if (isset($type) && $type != '') {
			$where 	.= ' AND gp.type ="'.$type.'"';
		}
		$sql 		= 'SELECT gp.id, gp.parent_id, gp.title as subname, gp1.title as parentname, gp.type, gp.status '
				. $table
				. $where
				. ' GROUP BY gp.id'
				. ' ORDER BY gp.title ASC';
		$res 		= mysqli_query($GLOBALS['con'], $sql);
	    while ($row = mysqli_fetch_object($res)) {
	    	
	    	$title 			= $row->subname;
	    	$title 		   .= ($row->parent_id > 0) ? " &lt;- ".$row->parentname." &lt;- ".$row->type:" &lt;- ".$row->type;
	    	$row->title 	= $title;
	    	unset($row->parentname);
	    	unset($row->subname);
	    	unset($row->type);
	    	$group_list[] 	= $row;
	    } 
				
	    return $group_list;
	}
	// list all ledger and sub ledger for combo box
	function listLedger($inputs)
	{
		extract($inputs);
		$ledger_list = [];
		$where 		 = ' WHERE ld.status != 3 ';// deleted no need to list
		if (isset($status) && $status != '') {
			if (isset($ledger_id) && $ledger_id > 0) {
				$where 	.= ' AND (ld.status ="'.$status.'" OR ld.id ="'.$ledger_id.'")';
			} else {
				$where 	.= ' AND ld.status ="'.$status.'"';
			}
		}
		if (isset($parent_id) && $parent_id != '') {
			$where 	.= ' AND ld.parent_id ="'.$parent_id.'"';
		}
		if (isset($group_id) && $group_id > 0) {
			$where 	.= ' AND ld.group_id ="'.$group_id.'"';
		}
		if (isset($office_id) && $office_id > 0) {
			$where 	.= ' AND (ld.office_id ="'.$office_id.'" OR ld.office_id = "0")';
		}
		$sql 	= 'SELECT ld.id, ld.group_id, ld.parent_id, ld.title, ld.status '
				. ' FROM acc_ledger AS ld'
				. $where
				. ' GROUP BY ld.id'
				. ' ORDER BY ld.title ASC';
		$res 		= mysqli_query($GLOBALS['con'], $sql);
	    while ($row = mysqli_fetch_object($res)) {

	    	$ledger_list[] 	= $row;
	    } 
					
	    return $ledger_list;
	}
	// get the Ledger details based on the ledger id provided
	function getLedger($id = 0)
	{
		$sql 	= 'SELECT * FROM acc_ledger WHERE id = "'.$id.'" ORDER BY id DESC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
	   	$row 	= mysqli_fetch_array($res, MYSQLI_ASSOC);

	   	return (!empty($row)) ? $row : [];
	}
	// list all ledger with full details used for grid listing
	function listAllLedger($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$tables 			= ' FROM acc_ledger AS ld '
				. ' INNER JOIN acc_groups AS gp ON (gp.id = ld.group_id)'
				. ' LEFT JOIN acc_ledger AS ldp ON (ldp.id = ld.parent_id)';
		$where 		 		= ' WHERE ld.status != 3 ';// deleted no need to list
		$join 				= '';
		$fields 			= '';
		$join 		.= ' LEFT JOIN users_auth AS ua ON (ua.US_Id = ld.created_by)'
					. '  LEFT JOIN users_auth AS ua1 ON (ua1.US_Id = ld.verified_by)'
					. ' LEFT JOIN acc_vendors as vdr ON (vdr.id =ld.vendor_id)';
		$fields 	.= ', ld.created_at, ld.verified_at, ld.description, concat(ua.US_FName, " ", ua.US_LName) As created_user, concat(ua1.US_FName, " ", ua1.US_LName) AS verified_user,
		IF(gp.status != "0" AND (ld.parent_id = "0" OR ldp.status != "0"), 1, 0) AS edit_status';
		if (isset($status) && $status != '') {
			$where 	.= ' AND ld.status ="'.$status.'"';
		}
		if (isset($parent_id) && $parent_id != '') {
			$where 	.= ' AND ld.parent_id ="'.$parent_id.'"';
		}
		if (isset($type) && $type != '') {
			$where 	.= ' AND gp.type ="'.$type.'"';
		}
		if (isset($group_id) && $group_id > 0) {
			$where 	.= ' AND (ld.group_id ="'.$group_id.'" OR gp.parent_id="'.$group_id.'")';
		}
		if (isset($office_id) && $office_id > 0) {
			$where 	.= ' AND (ld.office_id ="'.$office_id.'" OR ld.office_id = "0")';
		}	
		if (isset($search) && $search != '') {
			$where 	.= ' AND ld.title LIKE "%'.$search.'%" ';
		}
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT ld.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;

        // get the list of data 
        if ($this->data_total > 0) {
        	$slno 	= 0;
        	$sql  	= 'SELECT ld.id, ld.title, ld.status, ldp.title AS parent_name, gp.type, gp.title AS group_name, ld.group_id, gp.parent_id AS mgroup_id, ld.office_id, ld.parent_id, ld.vendor_id, vdr.name as vendor_name, ld.is_return, ld.trans_type, ld.is_contra, ld.less_id, ld.plus_id, ld.is_internal, ld.is_same_side, ld.is_job_type '.$fields
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY ld.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "group"	: $sql 	.= 'gp.title';	
				break;
				case "type"		: $sql 	.= 'gp.type';	
				break;
				case "ledger"	: $sql 	.= 'ld.title';	
				break;
				case "parent"	: $sql 	.= 'ldp.title';	
				break;
				default 		: $sql 	.= 'ld.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(1);	
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $row->status_val 	= $statuslist[$row->status];
                $this->data_list[] 	= $row;
            }
        }
		
		return $this->sql_query;
	}
	// status updation of Ledger
	function updateLedgerStatus($status, $id, $user_id, $oldStatus=2) 
	{
		if ($oldStatus != 2) {
			// back up entry
	   		$this->backupTables($id, 2);
		}
		$sql = "UPDATE acc_ledger SET status='".$status."', verified_by='".$user_id."', verified_at='".date('Y-m-d H:i:s')."' WHERE id=".$id;
		mysqli_query($GLOBALS['con'],$sql);
	}
	// save or update Ledger Details
	function saveLedger($inputs=[], $id=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';
		$extrawhere = ($inputs['office_id'] > 0) ? ' AND office_id = "'.$inputs['office_id'].'"':'';
		// find the group based code bassed on the inputed group id @ 27-01-2026		
		$chkgroup 	= mysqli_query($GLOBALS['con'], 'SELECT type FROM acc_groups WHERE id ="'.$inputs['group_id'].'"');
		$chkgrow 	= mysqli_fetch_row($chkgroup);
		$grouptype  = $chkgrow[0];
		$inputs['ie_type'] 	= (in_array($grouptype,['Liability','Income'])) ? 1: 2;
		// check the duplicate if any
		// AND parent_id = "'.$inputs['parent_id'].'"
		$chksql 	= 'SELECT id FROM acc_ledger WHERE id != "'.$id.'" '.$extrawhere.' AND group_id = "'.$inputs['group_id'].'" AND title = "'.$inputs['title'].'" ORDER BY id DESC LIMIT 0, 1';
		$chkres 	= mysqli_query($GLOBALS['con'], $chksql);
	   	$chkrow 	= mysqli_fetch_row($chkres);
	   	if (!empty($chkrow) && $chkrow[0] > 0) {
	   		$this->msg 		=  'Failed. Duplicate Entry!';
	   	} else if ($id > 0) { // update 
	   		// back up entry
	   		$this->backupTables($id, 2);

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_ledger SET $updstring WHERE id=".$id;
	        mysqli_query($GLOBALS['con'],$sql);

	        $ret_status = 1;
	        $this->msg 	= 'Ledger Details Updated Successfully';
	   	}else { // save entry;
	   		$sql 	= "INSERT INTO acc_ledger ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query($GLOBALS['con'],$sql);
			
			$ret_status = ($res) ? 1: 0;
			$this->msg 	= ($ret_status == 1) ? 'Ledger Details Saved Successfully':'Failed to Save Ledger Details';
	   	}

	   	return $ret_status;
	}
	// List all ledger with full parent names 
	function listLedgerCbo($inputs)
	{
		extract($inputs);
		$ledger_list = [];
		$flag 		 = (isset($flag)) ? (int)$flag:0;
		$table 		 = ' FROM acc_ledger AS ld '
			. ' INNER JOIN acc_groups AS gp ON (gp.id=ld.group_id)';
		$fields  	 = ', ld.group_id, ld.parent_id, concat(ld.title," &lt;- ", gp.title, " &lt;- ", gp.type ) as title, ld.status';
		if ($flag == 1) {
			$fields  = ', ld.title AS ledger, gp.title AS group_name, gp.type, ldp.title AS parent';
			$table  .= ' LEFT JOIN acc_ledger AS ldp ON (ldp.id = ld.parent_id)';
		}
		$where 		 = ' WHERE ld.status != 3 AND gp.status=1 ';// deleted no need to list
		if (isset($status) && $status != '') {
			$where 	.= ' AND ld.status ="'.$status.'"';
		}
		if (isset($parent_id) && $parent_id != '') {
			$where 	.= ' AND ld.parent_id ="'.$parent_id.'"';
		}
		if (isset($group_id) && $group_id > 0) {
			$where 	.= ' AND ld.group_id ="'.$group_id.'"';
		}
		if (isset($office_id) && $office_id > 0) {
			$where 	.= ' AND (ld.office_id ="'.$office_id.'" OR ld.office_id = "0")';
		}
		if (isset($search) && $search != '') {
			if ($flag == 1) {
				$where 	.= ' AND (ld.title LIKE "%'.$search.'%" OR ldp.title LIKE "%'.$search.'%")';
			} else {
				$where 	.= ' AND ld.title LIKE "%'.$search.'%" ';
			}			
		}
		$sql 		= 'SELECT ld.id, ld.ie_type '.$fields
				. $table
				. $where
				. ' GROUP BY ld.id'
				. ' ORDER BY ld.title ASC';
		$res 		= mysqli_query($GLOBALS['con'], $sql);
	    while ($row = mysqli_fetch_object($res)) {

	    	$ledger_list[] 	= $row;
	    } 
					
	    return $ledger_list;
	}
	// list all mapped ledger and item details used for grid listing
	function listMapLedger($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$tables 			= ' FROM acc_map_ledger AS ml '
				. ' INNER JOIN acc_ledger AS ld ON (ld.id = ml.ledger_id)'
				. ' INNER JOIN acc_groups AS gp ON (gp.id = ld.group_id)'
				. ' INNER JOIN items AS itm ON (itm.IT_Id = ml.item_id)'
				. ' INNER JOIN sub_heads AS sh ON (sh.SH_Id = itm.SH_Id)';
		$where 		 		= ' WHERE ml.status != 3 ';// deleted no need to list
		$join 				= '';
		$fields 			= '';
		$join 		.= ' LEFT JOIN users_auth AS ua ON (ua.US_Id = ml.created_by)'
					. '  LEFT JOIN users_auth AS ua1 ON (ua1.US_Id = ml.verified_by)';
		$fields 	.= ', ml.created_at, ml.verified_at, concat(ua.US_FName, " ", ua.US_LName) As created_user, concat(ua1.US_FName, " ", ua1.US_LName) AS verified_user, IF (ld.status = "0", 0, 1) AS edit_status ';
		if (isset($status) && $status != '') {
			$where 	.= ' AND ml.status ="'.$status.'"';
		}
		if (isset($parent_id) && $parent_id != '') {
			$where 	.= ' AND ld.parent_id ="'.$parent_id.'"';
		}
		if (isset($type) && $type != '') {
			$where 	.= ' AND gp.type ="'.$type.'"';
		}
		if (isset($group_id) && $group_id > 0) {
			$where 	.= ' AND (ld.group_id ="'.$group_id.'" OR gp.parent_id="'.$group_id.'")';
		}
		if (isset($office_id) && $office_id > 0) {
			$where 	.= ' AND ml.office_id ="'.$office_id.'"';
		}	
		if (isset($search) && $search != '') {
			$where 	.= ' AND ld.title LIKE "%'.$search.'%" ';
		}
		if (isset($search_item) && $search_item != '') {
			$where 	.= ' AND itm.IT_Name LIKE "%'.$search_item.'%" ';
		}
		if (isset($item_shead) && $item_shead > 0) {
			$where 	.= ' AND itm.SH_Id ="'.$item_shead.'"';
		}
		if (isset($limit) && $limit > 0) { // limit not provided
			// find the count from the query
			$sql_count 			= 'SELECT COUNT(DISTINCT ml.id) '.$tables.' '.$where;
			$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
			$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
	        $this->data_total 	= $row_count[0];
	        $this->sql_query 	= $sql_count;
	    } else {
	    	$this->data_total 	= 1;
	    }
        // get the list of data 
        if ($this->data_total > 0) {
        	$slno 	= 0;
        	$sql  	= 'SELECT ml.id, ld.title AS ledger_name, itm.IT_Name AS item_name, ml.status, gp.type, gp.title AS group_name, sh.SH_Name AS subhead_name, ld.group_id, gp.parent_id AS mgroup_id, ml.office_id, ld.parent_id, itm.SH_Id, ml.ledger_id, ml.item_id '.$fields
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY ml.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "group"	: $sql 	.= 'gp.title';	
				break;
				case "type"		: $sql 	.= 'gp.type';	
				break;
				case "ledger"	: $sql 	.= 'ld.title';	
				break;
				case "item"		: $sql 	.= 'itm.IT_Name';	
				break;
				case "subhead"	: $sql 	.= 'sh.SH_Name';	
				break;
				default 		: $sql 	.= 'ml.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(1);	
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	$isexcel 	= (isset($isexcel)) ? $isexcel:0; //26-11-2025
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                if ($row->parent_id <= 0) {
                	$row->parent_id = $row->ledger_id;
                	$row->ledger_id = 0;
                }
                $row->status_val 	= $statuslist[$row->status];
                if ($isexcel == 1) {
                	$this->data_list[] 	= [ 'slno'=>$slno, 'type'=>$row->type, 'group'=>$row->group_name, 'ledger'=>$row->ledger_name, 'item'=>$row->item_name, 'headitem'=>$row->subhead_name, 'status'=>$row->status_val ];
                } else {
                	$this->data_list[] 	= $row;
                }                
            }            
            if (!(isset($limit) && $limit > 0)) { //full data fetching time find the total count
            	$this->data_total 	= count($this->data_list);
            }
        }
		
		return $this->sql_query;
	}
	// get the Ledger details based on the ledger id provided
	function getMapLedger($id = 0)
	{
		$sql 	= 'SELECT * FROM acc_map_ledger WHERE id = "'.$id.'" ORDER BY id DESC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
	   	$row 	= mysqli_fetch_array($res, MYSQLI_ASSOC);

	   	return (!empty($row)) ? $row : [];
	}
	// status updation of mapped Ledger
	function updateMapLedgerStatus($status, $id, $user_id, $oldStatus=2) 
	{
		if ($oldStatus != 2) {
			// back up entry
	   		$this->backupTables($id, 3);
		}
		$sql = "UPDATE acc_map_ledger SET status='".$status."', verified_by='".$user_id."', verified_at='".date('Y-m-d H:i:s')."' WHERE id=".$id;
		mysqli_query($GLOBALS['con'],$sql);
	}
	// map Ledger and Items
	function saveMapLedger($inputs=[], $id=0) 
	{	
		// get ledger type 
		$findsql 	= 'SELECT id,trans_type FROM acc_ledger WHERE id = "'.$inputs['ledger_id'].'" ORDER BY id DESC LIMIT 0, 1';
		$findres 	= mysqli_query($GLOBALS['con'], $findsql);
	   	$findrow	= mysqli_fetch_row($findres);
	   	$trans_type = $findrow[1];
	   	$inputs['trans_type'] = $trans_type;
	   	$wheretran  = ($trans_type == 0) ? '': ' AND (trans_type = 0 OR trans_type = "'.$trans_type.'")';

		$ret_status = 0;
		$this->msg 	= '';
		// check the duplicate if any
		$chksql 	= 'SELECT id FROM acc_map_ledger WHERE id != "'.$id.'" AND item_id = "'.$inputs['item_id'].'" '.$wheretran.' ORDER BY id DESC LIMIT 0, 1';
		$chkres 	= mysqli_query($GLOBALS['con'], $chksql);
	   	$chkrow 	= mysqli_fetch_row($chkres);
	   	if (!empty($chkrow) && $chkrow[0] > 0) {
	   		$this->msg 		=  'Failed. Duplicate Entry!';
	   	} else if ($id > 0) { // update 
	   		// back up entry
	   		$this->backupTables($id, 3);

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_map_ledger SET $updstring WHERE id=".$id;
	        mysqli_query($GLOBALS['con'],$sql);

	        $ret_status = 1;
	        $this->msg 	= 'Ledger And Item Mapped Successfully';
	   	}else { // save entry;
	   		$sql 	= "INSERT INTO acc_map_ledger ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query($GLOBALS['con'],$sql);
			
			$ret_status = ($res) ? 1: 0;
			$this->msg 	= ($ret_status == 1) ? 'Ledger And Item Mapped Successfully':'Failed to Map Ledger';
	   	}

	   	return $ret_status;
	}
	/**
	* Get single balance sheet entry based on the id
	* Created By Bilin @ 07-08-2025
	*/
	function getItemEntry($bsid)
	{
		$sql 	= 'SELECT bl.BS_Id, it.IT_Name, tr.TR_Track, bl.TR_Id, bl.LC_Id, bl.BS_BranchTo, lc.OF_Id, bh.OF_Id AS company_id, lc.LC_Name, bh.LC_Name AS branch_name, bl.BS_Date, bl.BS_Amount, IF (bl.BNK_Id > 0, 1, 2) AS amt_type, l.title AS ledger, ld.ledger_id, ds.DS_Description, l.vendor_id, bl.BA_Id, it.MH_Type, l.ie_type '
			. ' FROM balance_sheets AS bl '
			. ' INNER JOIN items AS it ON (it.IT_Id = bl.IT_Id)'
			. ' INNER JOIN locations AS lc ON (lc.LC_Id = bl.LC_Id)'
			. ' LEFT JOIN descriptions AS ds On (ds.DS_Id = bl.BS_Description)'
			. ' LEFT JOIN locations AS bh ON (bh.LC_Id = bl.BS_BranchTo)'
			. ' LEFT JOIN tracks AS tr ON (tr.TR_Id = bl.TR_Id) '
			. ' LEFT JOIN acc_map_ledger AS ld ON (ld.item_id = bl.IT_Id AND IF(bl.BNK_Id > 0, ld.trans_type != 1, ld.trans_type != 2))'
			. ' LEFT JOIN acc_ledger AS l ON (l.id= ld.ledger_id)'
			. ' WHERE bl.BS_Id = "'.$bsid.'"'
			. ' GROUP BY bl.BS_Id '
			. ' ORDER BY bl.BS_Id LIMIT 0,1 ';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
    	$row 	= mysqli_fetch_object($res);

    	return $row;
	}
	/**
	* list all not transfered item data based on the company/user
	* Created By Bilin @ 05-08-2025
	*/
	function listItemJournal($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$tables 	= ' FROM balance_sheets AS bl '
				. ' INNER JOIN items AS it ON (it.IT_Id = bl.IT_Id)'
				. ' INNER JOIN locations AS lc ON (lc.LC_Id = bl.LC_Id)'
				. ' LEFT JOIN locations AS bh ON (bh.LC_Id = bl.BS_BranchTo)'
				. ' LEFT JOIN tracks AS tr ON (tr.TR_Id = bl.TR_Id) ';
		//$where 		= ' WHERE bl.BS_Moved = 0 AND bl.BS_Status != 3 AND bl.BS_Status != 0 AND (bl.TR_Id <= 0 OR tr.is_closed = 1)'; // not moved list + Not delete or rejected + closed track or not track entry
		$where 		= ' WHERE bl.BS_Moved = 0 AND bl.BS_Status != 3 AND bl.BS_Status != 0'; // not moved list + Not delete or rejected 
		$join 		= '';
		$fields 	= '';
		$join 		.= ' LEFT JOIN acc_map_ledger AS ld ON (ld.item_id = bl.IT_Id AND IF(bl.BNK_Id > 0, ld.trans_type != 1, ld.trans_type != 2))'
				. ' LEFT JOIN acc_ledger AS l ON (l.id= ld.ledger_id)';
		$fields 	.= ', bl.IT_Id, l.title AS ledger, ld.ledger_id';
		if (isset($office_id) && $office_id > 0) {
			$where 	.= ' AND lc.OF_Id = "'.$office_id.'"';
		}				
		if (isset($search) && $search != '') {
			$where 	.= ' AND (it.IT_Name LIKE "%'.$search.'%" OR tr.TR_Track LIKE "'.$search.'%" OR bh.LC_Name LIKE "'.$search.'%" OR ds.DS_Description LIKE "%'.$search.'%")';
			$tables .= ' LEFT JOIN descriptions AS ds On (ds.DS_Id = bl.BS_Description)';
			$fields .= ', ds.DS_Description AS itemdesc'; 
		} else {
			$fields .= ', "" AS itemdesc'; 
		}						
		if (isset($ledgers) && $ledgers != '') {
			$where 	.= ' AND l.title LIKE "%'.$ledgers.'%" ';
		}			
		if (isset($date) && $date != '') {
			$where 	.= ' AND bl.BS_Date = "'.$date.'" ';
		}						
		if (isset($amt) && $amt > 0) {
			$where 	.= ' AND bl.BS_Amount = "'.$amt.'"';
		}	
		if (isset($limit) && $limit > 0) { // limit not provided
			// find the count from the query
			$sql_count 			= 'SELECT COUNT(DISTINCT bl.BS_Id) '.$tables.$join.' '.$where;
			$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
			$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
	        $this->data_total 	= $row_count[0];
	        $this->sql_query 	= $sql_count;
	    } else {
	    	$this->data_total 	= 1;
	    }

        // get the list of data 
        if ($this->data_total > 0) {

        	$sql  	= 'SELECT bl.BS_Id, it.IT_Name, it.MH_Type, tr.TR_Track, bl.TR_Id, bl.LC_Id, bl.BS_BranchTo, lc.OF_Id, bh.OF_Id AS company_id, lc.LC_Name, bh.LC_Name AS branch_name, bl.BS_Date, bl.BS_Amount, IF (bl.BNK_Id > 0, 1, 2) AS amt_type '.$fields
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY bl.BS_Id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"date";
			$orderby	= (isset($orderby) && trim($orderby) == "DESC") ? "DESC" :"ASC";
			switch ($sortby) {
				case "date"		: $sql 	.= 'bl.BS_Date';	
				break;
				case "item"		: $sql 	.= 'it.IT_Name';	
				break;
				case "ledger"	: $sql 	.= 'l.title';	
				break;
				case "amt"		: $sql 	.= 'CAST(bl.BS_Amount AS DECIMAL) ';	
				break;
				default 		: $sql 	.= 'bl.BS_Date';	
				break;
			}
			$sql 	.= ' '.$orderby.', bl.BS_Id ASC';


        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	$isexcel 	= (isset($isexcel)) ? $isexcel:0;
        	$slno 		= 0;
        	while ($row = mysqli_fetch_object($res)) {
               	
               	if ($isexcel == 1) {
               		$slno++;
                	$this->data_list[] 	= ['slno'=>$slno,'type'=>($row->MH_Type == 1 ? 'Income' : 'Expense'), 'item'=>$row->IT_Name, 'track'=>($row->TR_Id > 0 ? $row->TR_Track:'-'), 'amount'=>number_format($row->BS_Amount,2), 'amttype'=>($row->amt_type == 1 ? 'Bank' : 'Cash'), 'date'=>date('d/m/Y', strtotime($row->BS_Date)), 'branchfrom'=>$row->LC_Name, 'branchto'=>($row->BS_BranchTo > 0 ? $row->branch_name: $row->LC_Name), 'ledger'=>$row->ledger];
                } else {
                	$this->data_list[] 	= $row;
                }
            }                       
            if (!(isset($limit) && $limit > 0)) { //full data fetching time find the total count
            	$this->data_total 	= count($this->data_list);
            }
        }
	}	
	function saveMapItem($inputs)
	{
		extract($inputs);
		$retstatus  = 0;
		$msg 		= 'Failed to save!';
		$inData 	= [];
		$insertSql  = 'INSERT INTO acc_journal (office_id, location_id, company_id, branch_id, balance_sheet_id, track_id, ledger_id, amount, amt_type, ba_id, date_entry, status, is_edited, created_by, created_at, vendor_id, ie_type, trackno) VALUES ';
		$sql 		= 'SELECT bl.BS_Id, bl.LC_Id, bl.BS_BranchTo, bl.TR_Id, bl.BS_Amount , bl.BS_Date, ld.ledger_id, IF(bl.BNK_Id > 0, 1, 2) AS amt_type, bl.BA_Id, ldr.vendor_id, ldr.ie_type, trk.TR_Track '
			. ' FROM balance_sheets AS bl '
			. ' INNER JOIN acc_map_ledger AS ld ON (ld.item_id = bl.IT_Id AND IF(bl.BNK_Id > 0, ld.trans_type != 1, ld.trans_type != 2))'
			. ' INNER JOIN acc_ledger AS ldr ON (ldr.id = ld.ledger_id)'
			. ' INNER JOIN items AS itm ON (itm.IT_Id = bl.IT_Id) '
			. ' LEFT JOIN tracks AS trk ON (trk.TR_Id = bl.TR_Id) '
			. ' WHERE bl.BS_Id IN ('.implode(",",$bs_ids).') and bl.BS_Moved = 0 AND bl.BS_Status != 3 AND bl.BS_Status != 0'
			. ' GROUP BY bl.BS_Id ORDER BY bl.BS_Id ASC';
		$res 		= mysqli_query($GLOBALS['con'], $sql);
    	while ($row = mysqli_fetch_object($res)) {
           	
            $singData 	= '('.$office_id.', '.$row->LC_Id.', '.$office_id.', ';
            $singData  .= ($row->BS_BranchTo > 0) ? $row->BS_BranchTo :$row->LC_Id;
            $BS_Date 	= (isset($date_entry) && $date_entry != '') ? $date_entry:$row->BS_Date;
            $singData  .= ', '.$row->BS_Id.', '.$row->TR_Id.', '.$row->ledger_id.', '.$row->BS_Amount.', '.$row->amt_type.', '.$row->BA_Id.', "'.$BS_Date.'", "1", "0", '.$user_id.', "'.date('Y-m-d H:i:s').'", '.$row->vendor_id.', '.$row->ie_type.', "'.$row->TR_Track.'")';

            $insertSql .= (!empty($inData)) ? ', '.$singData:$singData;
            $inData[]  = $row->BS_Id;
        }
        if (!empty( $inData )) {
        	// insert into the journal
        	mysqli_query($GLOBALS['con'], $insertSql);

        	// Update the balance sheet moved to status
        	$updSql 	= 'UPDATE balance_sheets SET BS_Moved = 1 WHERE BS_Id IN ('.implode(",",$inData).')';
        	mysqli_query($GLOBALS['con'], $updSql);
        	
        	$updSql1 	= 'UPDATE acc_journal SET parent_id = id WHERE balance_sheet_id IN ('.implode(",",$inData).') AND status != "3"';
        	mysqli_query($GLOBALS['con'], $updSql1);

        	$msg 		= 'Save data successfully';
        	$retstatus 	= 1;
        }

		return [$retstatus, $msg, $updSql];
	}
	/**
	* Get journal entry based on the parent id
	* base details only not the balance sheet entry details
	*/
	function getJournals($parent_id=0)
	{
		$rData 	= [];
		$sql 	= 'SELECT jl.id, jl.parent_id, jl.company_id, jl.branch_id, jl.balance_sheet_id, jl.track_id, jl.ledger_id, jl.amount, jl.converted, jl.amt_type, jl.ba_id, jl.date_entry, cm.OF_Name AS company, lc.LC_Name AS branch, ld.title AS ledger, jl.remarks, jl.vendor_id, jl.ie_type, jl.trackno, ld.ie_type as ie_typel '
			. ' FROM acc_journal AS jl'
			. ' INNER JOIN acc_ledger AS ld ON (ld.id = jl.ledger_id)'
			. ' INNER JOIN locations AS lc ON (lc.LC_Id = jl.branch_id)'
			. ' INNER JOIN offices AS cm ON (cm.OF_Id = jl.company_id)'
			. ' WHERE jl.status != 3 AND jl.parent_id = "'.$parent_id.'"'
			. ' ORDER BY jl.id ASC';
		$res 		= mysqli_query($GLOBALS['con'], $sql);
		while ($row = mysqli_fetch_object($res)) {
               	
            $rData[] 	= $row;
        }

        return $rData;
    }
	/**
	* list all Journal (mapped data of item and new ledger)
	* Created By Bilin @ 05-08-2025
	*/
	function listJournal($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$parent_ids 		= [];
		$office_id 	= (isset($office_id)) ? (int)$office_id:0;
		$tables 	= ' FROM acc_journal AS jl '
			. ' INNER JOIN acc_ledger AS ld ON (ld.id = jl.ledger_id)'
			. ' LEFT JOIN locations AS br ON (br.LC_Id=jl.branch_id)'
			. ' LEFT JOIN users_auth AS ua ON (ua.US_Id=jl.created_by)';			
		$where 		= ' WHERE jl.status != 3 AND jl.add_type = 0 ';
		$join 		= '';
		$fields 	= '';
		if ($office_id > 0) {
			$where 	.= ' AND (jl.office_id ="'.$office_id.'" OR jl.company_id ="'.$office_id.'")';
		}	
		if (isset($branch_id) && $branch_id > 0) {
			$where 	.= ' AND jl.branch_id ="'.$branch_id.'"';
		}		
		if (isset($search) && $search != '') {
			$where 	.= ' AND ld.title LIKE "%'.$search.'%" ';
		}		
		if (isset($date) && $date != '') {
			$where 	.= ' AND jl.date_entry = "'.$date.'" ';
		}		
		if (isset($date_from) && $date_from != '') {
			$where 	.= ' AND jl.date_entry >= "'.$date_from.'" ';
		}		
		if (isset($date_to) && $date_to != '') {
			$where 	.= ' AND jl.date_entry <= "'.$date_to.'" ';
		}
		if (isset($track_no) && $track_no != '') {
			$where 	.= ' AND (trk.TR_Track LIKE "%'.$track_no.'%" OR jl.trackno LIKE "%'.$track_no.'%") ';
			$tables .= ' LEFT JOIN tracks AS trk ON (trk.TR_Id = jl.track_id )';
		}				
		if (isset($amt) && $amt > 0) {
			$where 	.= ' AND (jl.amount = "'.$amt.'"  OR jl.converted = "'.$amt.'" )';
		}	
		if (isset($limit) && $limit > 0) { // limit not provided
			// find the count from the query
			$sql_count 			= 'SELECT COUNT(DISTINCT jl.id) '.$tables.' '.$where;
			$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
			$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
	        $this->data_total 	= $row_count[0];
	        $this->sql_query 	= $sql_count;
	    } else {
	    	$this->data_total 	= 1; 
	    }
        // get the list of data 
        if ($this->data_total > 0) {
        	if (!(isset($track_no) && $track_no != '')) {
        		$tables .= ' LEFT JOIN tracks AS trk ON (trk.TR_Id = jl.track_id )';
        	}
        	$fields .= ' , trk.TR_Track'; 
        	$slno 	= 0;
        	$sql  	= 'SELECT jl.id, jl.parent_id, ld.title AS ledger_name, jl.date_entry, jl.amount, jl.balance_sheet_id, jl.created_at, CONCAT(ua.US_FName," ",ua.US_LName) AS name, br.LC_Name, jl.converted, "0" AS amount_ratio, jl.company_id, jl.trackno  '.$fields
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY jl.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "date"		: $sql 	.= 'jl.date_entry';	
				break;
				case "updated"	: $sql 	.= 'jl.created_at';	
				break;
				case "amount"	: $sql 	.= 'jl.amount';	
				break;
				case "ledger"	: $sql 	.= 'ld.title';	
				break;
				case "branch"	: $sql 	.= 'br.LC_Name';	
				break;
				default 		: $sql 	.= 'jl.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(1);	
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	$isexcel 	= (isset($isexcel)) ? $isexcel:0;
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $row->status_val 	= $statuslist[$row->status];
                //12-01-2026
                //$row->amount_ratio 	= ($row->converted > 0) ? $row->converted/$row->amount:0; //12-01-2026
                if ($office_id == $row->company_id && $row->converted > 0) {
                	$row->amount 	= $row->converted;
                }
                // end 12-01-2026
                if ($isexcel == 1) {
                	$this->data_list[] 	= ['slno'=>$slno, 'date'=>date('d/m/Y', strtotime($row->date_entry)), 'trackno'=>($row->trackno != '' ? $row->trackno : '-'), 'ledger'=>$row->ledger_name, 'branch'=>$row->LC_Name, 'amount'=>number_format($row->amount,2)];
                } else {
                	$this->data_list[] 	= $row;
                } 
                $parent_ids[] = $row->parent_id;               
            }
            if (!(isset($limit) && $limit > 0)) { //full data fetching time find the total count
            	$this->data_total 	= count($this->data_list);
            }
        }
        $parentSUm 		= [];
        if (!empty($parent_ids)) {
        	$parent_ids = array_unique($parent_ids);
        	$sql 		= "SELECT parent_id, SUM(amount) AS totalamt, SUM(converted) AS convertamt FROM acc_journal WHERE parent_id IN (".implode(',',$parent_ids).") GROUP BY parent_id ORDER BY parent_id";
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
        		$parentSUm[$row->parent_id] = ['base'=>$row->totalamt, 'convrt'=>$row->convertamt ];
        	}
        }
        return $parentSUm;
	}
	/**
	* delete the journal entry based on the id or parent id
	* in the case of id after update check any active journal entry from the same parent id
	* then update the move flag of balance sheet based on the balance sheet id
	* Created By Bilin @ 12-08-2025
	*/
	function deleteJournal($id=0, $parentid=0, $bs_id=0)
	{
		$rStatus  	= 0;
		$rMsg 		= 'Failed to delete!';
		$sql 		= '';
		$this->backJournal($id, $parentid);

		$resetBs 	= 1; // reset the balance sheet
		// find the deleted entry ahave any sliblings
		if ($id > 0) {
			$findSql 	= 'SELECT count(DISTINCT id) AS countid, parent_id, balance_sheet_id FROM acc_journal WHERE ';
			if ($parentid > 0) {
				$findSql 	.= 'parent_id='.$parentid;
			} else if ($bs_id > 0) {
				$findSql 	.= 'balance_sheet_id='.$bs_id;
			} 
			$findSql 	.= ' GROUP BY parent_id';
			$findres 	= mysqli_query($GLOBALS['con'], $findSql);
			$findrow 	= mysqli_fetch_object($findres);
			$resetBs 	= ($findrow->countid > 1) ? 0 : 1;
			$sql 		= $findSql;
		}
		// delete record
		$delSql 	= 'DELETE FROM acc_journal WHERE ';
		if ($id > 0) {
			$delSql 	.= 'id='.$id;
		} else if ($parentid > 0) {
			$delSql 	.= 'parent_id='.$parentid;
		}
		if (mysqli_query($GLOBALS['con'], $delSql)) {
			$rStatus 	= 1;
			$rMsg 		= "Entry deleted successfully";
		}
		$sql 		= $delSql;
		// update the balance sheet entry
		if ($resetBs == 1) {

			$updSql 	= 'UPDATE balance_sheets SET BS_Moved = 0 WHERE BS_Id ='.$bs_id;
        	mysqli_query($GLOBALS['con'], $updSql);
        	$sql 		= $updSql;
		}

		return [$rStatus, $rMsg, $sql];
	}
	/**
	* Save or update the one balance sheet entry to one or more journal entry 
	* insert time check the balance sheet entry present or not in active state (deleted not check)
	* existing active entry of selected parent deleted first 
	* insert all new entry
	* if only one entry for selected balance sheet entry then update not delete
	* Created By bilin @ 12-08-2025
	*/
	function updateJournal($inputs)
	{
		extract($inputs);
		$rStatus  	= 0;
		$rMsg 		= 'Failed to delete!';
		$sql 		= '';
		$is_edited 	= 0;
		$inData 	= [];
		$amount_sum = 0;
		// back up data if edit case
		if (isset($parent_id) && $parent_id > 0) {			
			$is_edited 	= 1;	
		}		
		$insertSql  = 'INSERT INTO acc_journal (office_id, location_id, company_id, branch_id, balance_sheet_id, track_id, ledger_id, amount, converted, amt_type, ba_id, date_entry, status, is_edited, remarks, created_by, created_at, vendor_id, ie_type, trackno) VALUES ';
		foreach ($details as $sl => $rv) {
			$remarks 			= preg_replace('/[^A-Za-z0-9_. -]/', '', $rv["remarks"]);
			$rv["amount"] 		= ($rv["amount"] != '') ? (float)$rv["amount"]:0;
			$rv["converted"] 	= ($rv["converted"] != '') ? (float)$rv["converted"]:0;
			$trackno 			= (isset($rv["trackno"]) && $rv["trackno"] != "") ? $rv["trackno"] : "";		
			$singData 	= '("'.$office_id.'", "'.$location_id.'", "'.$rv["tocompany_id"].'", "'.$rv["tobranch_id"].'","'.$bs_id.'","'.$track_id.'","'.$rv["ledger_id"].'","'.$rv["amount"].'","'.$rv["converted"].'","'.$amt_type.'", "'.$ba_id.'","'.$date_entry.'","1","'.$is_edited.'","'.$remarks.'",'.$user_id.',"'.date('Y-m-d H:i:s').'", "'.(int)$rv["vendor_id"].'","'.$ie_type.'", "'.$trackno.'")';

            $insertSql .= (!empty($inData)) ? ', '.$singData:$singData;
            $inData[]  = $row->BS_Id;		
            $amount_sum += $rv["amount"];	
		}
		/* amount checking stoped by the request from the account team
		if ((int)$total_amount != (int)$amount_sum || $total_amount == 0) {
			$rMsg 		= "Amount mismatch, please check the amount total";
			return [$rStatus, $rMsg, ''];
		}*/
		// back up data if edit case
		if (isset($parent_id) && $parent_id > 0) {
			$this->backJournal(0, $parent_id);
			// delete record
			$delSql 	= 'DELETE FROM acc_journal WHERE parent_id='.$parent_id;
			mysqli_query($GLOBALS['con'], $delSql);	
		} 
		if (isset($bs_id) && $bs_id > 0) {
			// delete record
			$delSql 	= 'DELETE FROM acc_journal WHERE balance_sheet_id='.$bs_id;
			mysqli_query($GLOBALS['con'], $delSql);
		}
		if (!empty( $inData )) {
        	// insert into the journal
        	$sql 		= $insertSql;
        	mysqli_query($GLOBALS['con'], $insertSql);
        	$rMsg 		= ($parent_id > 0) ? "Entry updated successfully":"Entry saved successfully";
        	$rStatus 	= 1;
        	// update the balance sheet..
        	if ($parent_id <= 0) {
        		$updSql 	= 'UPDATE balance_sheets SET BS_Moved = 1 WHERE BS_Id ='.$bs_id;
        		mysqli_query($GLOBALS['con'], $updSql);       		        	
	        }
	        //find the inserted id 
        	$findSql 	= "SELECT id FROM acc_journal WHERE balance_sheet_id ='".$bs_id."' ORDER BY id ASC LIMIT 0,1";
        	$findres 	= mysqli_query($GLOBALS['con'], $findSql);	
        	$findrow 	= mysqli_fetch_row($findres);
        	$parent_id 	= $findrow[0];
        	// updated the parent id
        	$updSql 	= 'UPDATE acc_journal SET parent_id = '.$parent_id.' WHERE balance_sheet_id ='.$bs_id;
        	mysqli_query($GLOBALS['con'], $updSql);
        	$sql 		= $updSql; 
        }
		return [$rStatus, $rMsg, $sql];
	}
	/**
	* backup journal entry
	*/
	function backJournal($id=0, $parentid=0)
	{
		$backup 	= 'INSERT INTO acc_back_journal SELECT NULL, acc_journal.* FROM acc_journal WHERE ';
		if ($id > 0) {
			$backup .= 'id='.$id;
		} else if ($parentid > 0) {
			$backup .= 'parent_id='.$parentid;
		}
		mysqli_query($GLOBALS['con'], $backup);
	}
	/**
	* mark Not moved items in the balance sheet. these are not add into journals
	* created By Bilin @ 13-08-2025 updated @ 07-11-2025
	*/
	function notMovedItem($inputs, $status=2)
	{
		extract($inputs);
		$retstatus  = 0;
		$msg 		= 'Failed to Update!';
		$updSql 	= '';

		if (!empty($bs_ids)) {
        	$updSql 	= 'UPDATE balance_sheets SET BS_Moved = '.$status.', BS_M_USID='.$user_id.' WHERE BS_Id IN ('.implode(",",$bs_ids).')';
        	if (mysqli_query($GLOBALS['con'], $updSql)) {

        		$msg 		= ($status==0 )? 'Item Un Blocked Successfully ':'Block / Marked as Not Moved Successfully';
        		$retstatus 	= 1;
        	}
        }

		return [$retstatus, $msg, $updSql];
	}
	/**
	 * list of journal items - old system data update after mapping
	 * Created BY Bilin 05-01-2025
	*/
	function listUpmJournal($inputs)
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$tables 	= ' FROM acc_journal AS jl '
			. ' INNER JOIN acc_ledger AS ld ON (ld.id = jl.ledger_id)'
			. ' INNER JOIN balance_sheets AS bs ON (bs.BS_Id=jl.balance_sheet_id)';
						
		$where 		= ' WHERE jl.status != 3 AND jl.add_type = 0 AND bs.BS_MDate > jl.created_at ';
		$join 		= '';
		$fields 	= '';
		if (isset($office_id) && $office_id > 0) {
			$where 	.= ' AND (jl.office_id ="'.$office_id.'" OR jl.company_id ="'.$office_id.'")';
		}	
		if (isset($branch_id) && $branch_id > 0) {
			$where 	.= ' AND jl.branch_id ="'.$branch_id.'"';
		}		
		if (isset($ledger) && $ledger != '') {
			$where 	.= ' AND ld.title LIKE "%'.$ledger.'%" ';
		}		
		if (isset($date_from) && $date_from != '') {
			$where 	.= ' AND jl.date_entry >= "'.$date_from.'" ';
		}		
		if (isset($date_to) && $date_to != '') {
			$where 	.= ' AND jl.date_entry <= "'.$date_to.'" ';
		}
		if (isset($track_no) && $track_no != '') {
			$where 	.= ' AND trk.TR_Track LIKE "%'.$track_no.'%" ';
			$tables .= ' LEFT JOIN tracks AS trk ON (trk.TR_Id = jl.track_id )';
		}
		if (isset($limit) && $limit > 0) { // limit not provided
			// find the count from the query
			$sql_count 			= 'SELECT COUNT(DISTINCT jl.id) '.$tables.' '.$where;
			$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
			$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
	        $this->data_total 	= $row_count[0];
	        $this->sql_query 	= $sql_count;
	    } else {
	    	$this->data_total 	= 1; 
	    }
        // get the list of data 
        if ($this->data_total > 0) {
        	if (!(isset($track_no) && $track_no != '')) {
        		$tables .= ' LEFT JOIN tracks AS trk ON (trk.TR_Id = jl.track_id )';
        	}
        	$fields .= ' , trk.TR_Track'; 
        	$slno 	= 0;
        	$sql  	= 'SELECT jl.id, jl.parent_id, ld.title AS ledger_name, jl.date_entry, jl.amount, jl.balance_sheet_id, jl.created_at, bs.BS_MDate '.$fields
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY jl.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "date"		: $sql 	.= 'jl.date_entry';	
				break;
				case "created"	: $sql 	.= 'jl.created_at';	
				break;
				case "updated"	: $sql 	.= 'bs.BS_MDate';	
				break;
				case "amount"	: $sql 	.= 'jl.amount';	
				break;
				case "ledger"	: $sql 	.= 'ld.title';	
				break;
				default 		: $sql 	.= 'jl.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(1);	
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	$isexcel 	= (isset($isexcel)) ? $isexcel:0;
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $row->status_val 	= $statuslist[$row->status];
                if ($isexcel == 1) {
                	$this->data_list[] 	= ['slno'=>$slno, 'date'=>date('d/m/Y', strtotime($row->date_entry)), 'trackno'=>($row->TR_Track != '' ? $row->TR_Track : '-'), 'ledger'=>$row->ledger_name, 'created'=>date('d/m/Y', strtotime($row->created_at)), 'updated'=>date('d/m/Y', strtotime($row->BS_MDate)), 'amount'=>number_format($row->amount,2)];
                } else {
                	$this->data_list[] 	= $row;
                }                
            }
            if (!(isset($limit) && $limit > 0)) { //full data fetching time find the total count
            	$this->data_total 	= count($this->data_list);
            }
        }
	}
	// List all extra data uploaded and manual adding/updating
	// Created By Bilin 09-01-2026
	function listExtraData($inputs)
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$tables 	= ' FROM acc_extra_journal AS ej '
			. ' LEFT JOIN acc_ledger AS ld ON (ld.id = ej.ledger_id)'
			. ' LEFT JOIN locations AS l ON (l.LC_Id = ej.branch_id)';
		$where 		= ' WHERE ej.company_id > 0 ';
		if (isset($company_id) && $company_id > 0) {
			$where 	.= ' AND ej.company_id ="'.$company_id.'"';
		}	
		if (isset($branch_id) && $branch_id > 0) {
			$where 	.= ' AND ej.branch_id ="'.$branch_id.'"';
		}		
		if (isset($title) && $title != '') {
			$where 	.= ' AND (ld.title LIKE "%'.$title.'%" OR ej.title LIKE "%'.$title.'%")';
		}		
		if (isset($branch) && $branch != '') {
			$where 	.= ' AND (l.LC_Name LIKE "%'.$branch.'%" OR ej.branch_name LIKE "%'.$branch.'%")';
		}			
		if (isset($date_from) && $date_from != '') {
			$where 	.= ' AND ej.job_date >= "'.$date_from.'" ';
		}		
		if (isset($date_to) && $date_to != '') {
			$where 	.= ' AND ej.job_date <= "'.$date_to.'" ';
		}
		$sql_count 			= 'SELECT COUNT(DISTINCT ej.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;
         // get the list of data 
        if ($this->data_total > 0) {
        	$slno 	= 0;
        	$sql  	= 'SELECT ej.id, ej.type, ej.job_date, ej.title, ld.title AS ledger_name, ej.amount, ej.company_id, ej.branch_id, ej.addtype, ej.branch_name, l.LC_Name, ej.created_at '
        			.' '.$tables
        			.' '.$where
        			.' GROUP BY ej.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "date"		: $sql 	.= 'ej.job_date';	
				break;
				case "created"	: $sql 	.= 'ej.created_at';	
				break;
				case "amount"	: $sql 	.= 'ej.amount';	
				break;
				case "title"	: $sql 	.= 'ej.title';	
				break;
				case "branch"	: $sql 	.= 'ej.branch_name';	
				break;
				default 		: $sql 	.= 'ej.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 		.= ' LIMIT '.$start.','.$limit;
        		$slno 		 = $start;
        	}
        	$this->sql_query = $sql;
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $this->data_list[] 	= $row;                               
            }
        }
	}
	/**
	 * Extra data save / update Created By Bilin @ 12-01-2026
	*/
	function saveExtraData($inputs=[], $id=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';
		if ($id > 0) { // update 
	   		// back up entry
	   		$this->backupTables( $id, 11 );

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_extra_journal SET $updstring WHERE id=".$id;
	        mysqli_query( $GLOBALS['con'], $sql );
	        $ret_status = 1;
	        $this->msg 	= 'Extra Report Data Updated Successfully';
	   	} else { // save entry;
	   		$sql 	= "INSERT INTO acc_extra_journal ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query( $GLOBALS['con'], $sql );
			if ( mysqli_affected_rows( $GLOBALS['con']) > 0 ) {
                $id 		= mysqli_insert_id( $GLOBALS['con']);
                $ret_status = 1;
            } else {
            	$ret_status = 0;
            }
			
			$this->msg 	= ($ret_status == 1) ? 'Extra Report Data Saved Successfully':'Failed to Save Extra Report Data';
	   	}
	   	//$this->msg 	= $sql;
	   	return $ret_status;
	}
	function deleteExtraData($id=0)
	{
		$rStatus  	= 0;
		$rMsg 		= 'Failed to delete!';
		$sql 		= '';
		$this->backupTables( $id, 11 );		
		// delete record
		$delSql 	= 'DELETE FROM acc_extra_journal WHERE id='.$id;
		if (mysqli_query($GLOBALS['con'], $delSql)) {
			$rStatus 	= 1;
			$rMsg 		= "Entry deleted successfully";
		}
		$sql 		= $delSql;

		return [$rStatus, $rMsg, $sql];
	}

	//list the blocked items (not moved items list)
	// created BY Bilin @ 07-11-2025
	function listItemBlocked($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$tables 	= ' FROM balance_sheets AS bl '
				. ' INNER JOIN items AS it ON (it.IT_Id = bl.IT_Id)'
				. ' INNER JOIN locations AS lc ON (lc.LC_Id = bl.LC_Id)'
				. ' LEFT JOIN locations AS bh ON (bh.LC_Id = bl.BS_BranchTo)'
				. ' LEFT JOIN tracks AS tr ON (tr.TR_Id = bl.TR_Id) ';
		$where 		= ' WHERE bl.BS_Moved = 2 AND bl.BS_Status != 3 AND bl.BS_Status != 0'; 
		if (isset($office_id) && $office_id > 0) {
			$where 	.= ' AND lc.OF_Id = "'.$office_id.'"';
		}
		if (isset($search) && $search != '') {
			$where 	.= ' AND (it.IT_Name LIKE "%'.$search.'%" OR tr.TR_Track LIKE "'.$search.'%" OR bh.LC_Name LIKE "'.$search.'%")';
		}				
		if (isset($date) && $date != '') {
			$where 	.= ' AND bl.BS_Date = "'.$date.'" ';
		}
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT bl.BS_Id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;
        // get the list of data 
        if ($this->data_total > 0) {
        	$sql  	= 'SELECT bl.BS_Id, it.IT_Name, tr.TR_Track, bl.TR_Id, bl.LC_Id, bl.BS_BranchTo, lc.OF_Id, bh.OF_Id AS company_id, lc.LC_Name, bh.LC_Name AS branch_name, bl.BS_Date, bl.BS_Amount '
        			.' '.$tables
        			.' '.$where
        			.' GROUP BY bl.BS_Id'
        			.' ORDER BY ';
        	$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"date";
			$orderby	= (isset($orderby) && trim($orderby) == "DESC") ? "DESC" :"ASC";
			switch ($sortby) {
				case "date"		: $sql 	.= 'bl.BS_Date';	
				break;
				case "item"		: $sql 	.= 'it.IT_Name';	
				break;
				case "amt"		: $sql 	.= 'CAST(bl.BS_Amount AS DECIMAL) ';	
				break;
				default 		: $sql 	.= 'bl.BS_Date';	
				break;
			}
			$sql 		.= ' '.$orderby.', bl.BS_Id ASC';
			if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {

               	$slno++;
                $row->slno 			= $slno;
                $this->data_list[] 	= $row;
            }
        }
	}
	/**
	 * Tds Rules defines section start. (Listing , save, update, view )
	 * Listing TDS with filter and paginations 
	 * created by Bilin @ 02-09-2025
	*/
	function listTds($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';

		$tables 	= ' FROM acc_tds_rules AS atr ';
		$where 		= ' WHERE atr.status != 3';
		$join 		= ' LEFT JOIN users_auth AS uac ON (uac.US_Id=atr.created_by)'
			. ' LEFT JOIN users_auth AS uau ON (uau.US_Id=atr.updated_by)';
		$fields 	= '';
		if (isset($name) && $name != '') {
			$where 	.= ' AND atr.rule_name LIKE "%'.$name.'%" ';
		}
		if (isset($status) && $status != '') {
			$where 	.= ' AND atr.status ="'.$status.'"';
		}
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT atr.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;

        // get the list of data 
        if ($this->data_total > 0) {

        	$slno 	= 0;
        	$sql  	= 'SELECT atr.id, atr.rule_name, atr.percentage, atr.section, atr.code, atr.status, atr.start_date, atr.end_date, CONCAT(uac.US_FName," ",uac.US_LName) AS created, CONCAT(uau.US_FName," ",uau.US_LName) AS updated, atr.created_at, atr.updated_at '
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY atr.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "created"	: $sql 	.= 'atr.created_at';	
				break;
				case "percent"	: $sql 	.= 'atr.percentage';	
				break;
				case "name"		: $sql 	.= 'atr.rule_name';	
				break;
				case "time"		: $sql 	.= 'atr.start_date';	
				break;
				default 		: $sql 	.= 'atr.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(2);	
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $row->status_val 	= $statuslist[$row->status];
                $this->data_list[] 	= $row;
            }
        }
	}
	// save or update TDS Rules @ 04-09-2025
	function saveTds($inputs=[], $id=0,$insert=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';
		// check the duplicate if any
		$chksql 	= 'SELECT id FROM acc_tds_rules WHERE id != "'.$id.'" AND status != "3"  AND rule_name = "'.$inputs['rule_name'].'" AND start_date = "'.$inputs['start_date'].'" AND percentage = "'.$inputs['percentage'].'" ORDER BY id DESC LIMIT 0, 1';
		$chkres 	= mysqli_query($GLOBALS['con'], $chksql);
	   	$chkrow 	= mysqli_fetch_row($chkres);
	   	if (!empty($chkrow) && $chkrow[0] > 0) {
	   		$this->msg 		=  'Failed. Duplicate Entry!';
	   	} else {
	   		if ($insert == 1) { // insert

	   			$sql 		= "INSERT INTO acc_tds_rules ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
				$res 		= mysqli_query($GLOBALS['con'],$sql);				
				$ret_status = ($res) ? 1: 0;
				$this->msg 	= ($ret_status == 1) ? 'TDS Rule Saved Successfully':'Failed to Save TDS Rule';

				$end_date 	= $inputs['start_date'];
				unset($inputs);
				$inputs 	= ['status'=>3, 'end_date'=>$end_date]; 
	   		}
	   		if ($id > 0) { // update 

	   			$updstring 		= "";
				foreach ($inputs as $key => $value) { 
		            $updstring 	= $updstring .$key ."='".$value."', ";
		        }
		        $updstring = substr($updstring, 0, -2);
		        $sql = "UPDATE acc_tds_rules SET $updstring WHERE id=".$id;
		        mysqli_query($GLOBALS['con'],$sql);

		        $ret_status = 1;
		        $this->msg 	= 'TDS Rule Updated Successfully';
	   		}
	   	}

	   	return $ret_status;
	}
	/**
	 * Vendors defines section start. (Listing , save, update, view )
	 * Listing Vendors with filter and paginations 
	 * created by Bilin @ 02-09-2025
	*/
	function listVendor($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$tables 	= ' FROM acc_vendors AS av ';
		$where 		= ' WHERE av.status != 3';
		$join 		= ' LEFT JOIN countries AS cry ON (cry.CN_Id=av.country_id)'
			.' LEFT JOIN users_auth AS uac ON (uac.US_Id=av.created_by)'
			. ' LEFT JOIN users_auth AS uau ON (uau.US_Id=av.updated_by)';
		$fields 	= '';
		if (isset($name) && $name != '') {
			$where 	.= ' AND av.name LIKE "%'.$name.'%" ';
		}
		if (isset($contacts) && $contacts != '') {
			$where 	.= ' AND (av.email LIKE "%'.$contacts.'%" OR av.phone LIKE "%'.$contacts.'%" )';
		}
		if (isset($gstno) && $gstno != '') {
			$where 	.= ' AND av.gst_no LIKE "'.$gstno.'%" ';
		}
		if (isset($pan_no) && $pan_no != '') {
			$where 	.= ' AND av.pan_no LIKE "'.$pan_no.'%" ';
		}
		if (isset($status) && $status != '') {
			$where 	.= ' AND av.status ="'.$status.'"';
		}
		if (isset($type) && $type > 0) {
			$where 	.= ' AND av.type ="'.$type.'"';
		}
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT av.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;

        // get the list of data 
        if ($this->data_total > 0) {

        	$slno 	= 0;
        	$sql  	= 'SELECT av.id, av.name, av.gst_no, av.address, av.email, av.status, av.phone, av.pan_no, av.country_id, CONCAT(uac.US_FName," ",uac.US_LName) AS created, CONCAT(uau.US_FName," ",uau.US_LName) AS updated, cry.CN_Name, av.created_at, av.updated_at, av.type '
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY av.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "created"	: $sql 	.= 'av.created_at';	
				break;
				case "gst"		: $sql 	.= 'av.gst_no';	
				break;
				case "name"		: $sql 	.= 'av.name';	
				break;
				case "country"	: $sql 	.= 'cry.CN_Name';	
				break;
				default 		: $sql 	.= 'av.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(2);	
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $row->status_val 	= $statuslist[$row->status];
                $this->data_list[] 	= $row;
            }
        }
	}
	// save or update Vendor Details @ 04-09-2025
	function saveVendor($inputs=[], $id=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';	
		$typeary  	= ['1'=>'Vendor', '2'=>'Debtor', '3'=>'Creditor'];	
		// check the duplicate if any
		$unqwhere  	= ''; // 27-10-2025
		if (isset($inputs['gst_no']) && $inputs['gst_no'] != '') { // gst or pan number was not mandatory for debtors /creditors
			$unqwhere  	= ' AND gst_no = "'.$inputs['gst_no'].'"';
		} else if (isset($inputs['name']) && $inputs['name'] != '') {
			$unqwhere  	= ' AND name = "'.$inputs['name'].'"';
		}		
		$chksql 	= 'SELECT id FROM acc_vendors WHERE id != "'.$id.'" '.$unqwhere.' ORDER BY id DESC LIMIT 0, 1';
		$chkres 	= mysqli_query($GLOBALS['con'], $chksql);
	   	$chkrow 	= mysqli_fetch_row($chkres);
	   	if (!empty($chkrow) && $chkrow[0] > 0) {
	   		$this->msg 		=  'Failed. Duplicate Entry!';
	   	} else if ($id > 0) { // update 
	   		// back up entry
	   		$this->backupTables($id, 5);

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_vendors SET $updstring WHERE id=".$id;
	        mysqli_query($GLOBALS['con'],$sql);

	        $ret_status = 1;
	        $this->msg 	= (isset($inputs['type'])) ? $typeary[$inputs['type']].' Details Updated Successfully' : 'Vendor Details Updated Successfully';
	   	}else { // save entry;
	   		$sql 	= "INSERT INTO acc_vendors ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query($GLOBALS['con'],$sql);
			
			$ret_status = ($res) ? 1: 0;
			$typename 	= (isset($inputs['type'])) ? $typeary[$inputs['type']] :'Vendor';
			$this->msg 	= ($ret_status == 1) ? $typename.' Details Saved Successfully':'Failed to Save '.$typename.' Details';
	   	}
	   	//$this->msg 	= $sql;
	   	return $ret_status;
	}
	/**
	 * Bills defines section start. (Listing , save, update, view )
	 * Listing Bills with filter and paginations 
	 * created by Bilin @ 10-09-2025
	*/
	function listBills($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$tables 	= ' FROM acc_bills AS bls ';
		$where 		= ' WHERE bls.id > 0';
		$join 		= ' INNER JOIN acc_vendors AS vnd ON (vnd.id = bls.vendor_id)'
			. ' INNER JOIN offices AS cpy ON (cpy.OF_Id = bls.company_id)'
			. ' INNER JOIN users_auth AS uu ON (uu.US_Id = bls.updated_by)'
			. ' LEFT JOIN acc_tds_rules AS tds ON (tds.id = bls.tds_id)'
			. ' LEFT JOIN locations AS bch ON (bch.LC_Id = bls.branch_id)';
		$fields 	= '';
		if (isset($name) && $name != '') {
			$where 	.= ' AND bls.bill_name LIKE "%'.$name.'%" ';
		}
		if (isset($vendor) && $vendor != '') {
			$where 	.= ' AND vnd.name LIKE "%'.$vendor.'%" ';
		}
		if (isset($amount) && $amount != '') {
			$where 	.= ' AND bls.total_amount LIKE "'.$amount.'%"';
		}
		if (isset($billamount) && $billamount != '') {
			$where 	.= ' AND bls.bill_amount LIKE "'.$billamount.'%"';
		}
		if (isset($tds_id) && $tds_id != '') {
			$where 	.= ' AND bls.tds_id ="'.$tds_id.'"';
		}
		if (isset($type) && $type > 0) {
			$where 	.= ' AND bls.bill_type ="'.$type.'"';
		}
		if (isset($company_id) && $company_id > 0) {
			$where 	.= ' AND bls.company_id ="'.$company_id.'"';
		}
		if (isset($branch_id) && $branch_id > 0) {
			$where 	.= ' AND bls.branch_id ="'.$branch_id.'"';
		}		
		if (isset($from_date) || isset($to_date)) {
			if ($from_date != '' && $to_date != '') {
				$where 	.= ' AND bls.bill_date between "'.$from_date.'" AND "'.$to_date.'"';
			} else if ($from_date != '') {
				$where 	.= ' AND bls.bill_date >= "'.$from_date.'"';
			} else if ($to_date != '') {
				$where 	.= ' AND bls.bill_date <= "'.$to_date.'"';
			}
		}
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT bls.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;

        // get the list of data 
        if ($this->data_total > 0) {

        	$slno 	= 0;
        	$sql  	= 'SELECT bls.id, bls.bill_name, bls.bill_date, vnd.name as vendor_name, tds.rule_name, cpy.OF_Name, bch.LC_Name, CONCAT(uu.US_FName," ",uu.US_LName) AS updated_user, bls.updated_at, bls.tds_percent, bls.tds_id, bls.vendor_id, bls.total_amount, bls.bill_type, bls.bill_amount, bls.tds_amount, bls.company_id, bls.branch_id, bls.due_date, bls.created_at, bls.paid_status, bls.tax_id, bls.tax_percent, bls.tax_amount, bls.tax_amount2 '
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY bls.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "date"		: $sql 	.= 'bls.bill_date';	
				break;
				case "name"		: $sql 	.= 'bls.bill_name';	
				break;
				case "vendor"	: $sql 	.= 'vnd.name';	
				break;
				case "amount"	: $sql 	.= 'bls.total_amount';	
				break;
				default 		: $sql 	.= 'bls.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(2);	
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $row->type_val 		= ($row->bill_type == 1) ? "Credit":"Debit";
                $this->data_list[] 	= $row;
            }
        }
	}
	// save or update the bill details 
	// Created BY Bilin 12-09-2025
	function saveBills($inputs=[], $id=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';
		if ($id > 0) { // update 
	   		// back up entry
	   		$this->backupTables($id, 7);

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_bills SET $updstring WHERE id=".$id;
	        mysqli_query($GLOBALS['con'],$sql);
	        //if (isset($inputs['bill_date'])) { // check the recurring bills present - yes then update the next bill date	        	
	        //}
	        $ret_status = 1;
	        $this->msg 	= 'Bill Details Updated Successfully';
	   	}else { // save entry;
	   		$sql 	= "INSERT INTO acc_bills ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query($GLOBALS['con'],$sql);
			
			$ret_status = ($res) ? 1: 0;
			$this->msg 	= ($ret_status == 1) ? 'Bill Details Saved Successfully':'Failed to Save Bill Details';
	   	}
	   	//$this->msg 	= $sql;
	   	return $ret_status;
	}
	//recurring Bills based on the filter and pagination // SEP 23-2025 BY Bilin
	function listRcBills($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$tables 	= ' FROM acc_recurring_bills AS rbl '
			. ' INNER JOIN acc_bills AS bls ON (bls.id = rbl.last_bill_id)';
		$where 		= ' WHERE rbl.status != 3';
		$join 		= ' LEFT JOIN locations AS l ON (l.LC_Id = bls.branch_id)';
		$fields 	= ', l.LC_Name, bls.branch_id';
		if (isset($name) && $name != '') {
			$where 	.= ' AND bls.bill_name LIKE "%'.$name.'%" ';
		}
		if (isset($amount) && $amount != '') {
			$where 	.= ' AND bls.total_amount LIKE "'.$amount.'%"';
		}
		if (isset($status) && $status != '') {
			$where 	.= ' AND rbl.status ="'.$status.'"';
		}
		if (isset($company_id) && $company_id > 0) {
			$where 	.= ' AND bls.company_id ="'.$company_id.'"';
		}			
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT rbl.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;	
        // get the list of data 
        if ($this->data_total > 0) {

        	$slno 	= 0;
        	$sql  	= 'SELECT rbl.id, rbl.bill_id, rbl.day_id, rbl.last_bill_id, rbl.next_date, rbl.status, bls.bill_name, bls.bill_date AS last_date, bls.total_amount '.$fields
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY rbl.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "ndate"	: $sql 	.= 'rbl.next_date';	
				break;
				case "ldate"	: $sql 	.= 'bls.bill_date';	
				break;
				case "name"		: $sql 	.= 'bls.bill_name';	
				break;
				case "amount"	: $sql 	.= 'bls.total_amount';	
				break;
				default 		: $sql 	.= 'rbl.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(2);	
        	$res 					= mysqli_query($GLOBALS['con'], $sql);
        	$statuslist 			= $this->listStatus(2);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $row->status_val 	= $statuslist[$row->status];
                $this->data_list[] 	= $row;
            }
        } 
	}
	//save or update the recurring bills settings
	// @ 23-09-2025
	function saveRcBills($inputs=[], $id=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';
		// check dupliate bill id
		if ($inputs['bill_id'] > 0) {
			$dupsql = "SELECT COUNT(id) FROM acc_recurring_bills WHERE id !=".$id." AND bill_id=".$inputs['bill_id']." AND status!= 3";
	        $dupres = mysqli_query($GLOBALS['con'],$dupsql);
	        $duprow = mysqli_fetch_row($dupres);

	   		if (!empty($duprow) && $duprow[0] > 0) {
	   			$this->msg 	= 'Duplicate Entry';
	   			return $ret_status;
	   		}
		}
		if ($id > 0) { // update 
	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_recurring_bills SET $updstring WHERE id=".$id;
	        mysqli_query($GLOBALS['con'],$sql);

	        $ret_status = 1;
	        $this->msg 	= 'Recurring Bills Updated Successfully';
	   	}else { // save entry;
	   		$sql 	= "INSERT INTO acc_recurring_bills ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query($GLOBALS['con'],$sql);
			
			$ret_status = ($res) ? 1: 0;
			$this->msg 	= ($ret_status == 1) ? 'Recurring Bills Saved Successfully':'Failed to Set Recurring Bills';
	   	}
	   	//$this->msg 	= $sql;
	   	return $ret_status;
	}
	function listJournalEntry($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$tables 	= ' FROM acc_journal_entry AS je ';
		$where 		= ' WHERE je.id > 0';
		$tables    .= ' INNER JOIN acc_ledger AS crl ON (crl.id = je.ledger_cr)'
			. ' INNER JOIN acc_ledger AS drl ON (drl.id = je.ledger_dr)'
			. ' INNER JOIN offices AS cpy ON (cpy.OF_Id = je.company_id)';
		$join 		= ' INNER JOIN users_auth AS uu ON (uu.US_Id = je.updated_by)'
			. ' LEFT JOIN locations AS bch ON (bch.LC_Id = je.branch_id)';
		//$join 		.= ' LEFT JOIN bank_accounts AS badr ON (badr.BA_Id = je.bank_dr)'
		//	. ' LEFT JOIN bank_accounts AS bacr ON (bacr.BA_Id = je.bank_cr)';
		$fields 	= '';
		if (isset($dr_ledger) && $dr_ledger != '') {
			$where 	.= ' AND drl.title LIKE "%'.$dr_ledger.'%" ';
		}
		if (isset($cr_ledger) && $cr_ledger != '') {
			$where 	.= ' AND crl.title LIKE "%'.$cr_ledger.'%" ';
		}
		if (isset($amount) && $amount != '') {
			$where 	.= ' AND je.amount LIKE "'.$amount.'%"';
		}
		if (isset($company_id) && $company_id > 0) {
			$where 	.= ' AND je.company_id ="'.$company_id.'"';
		}
		if (isset($branch_id) && $branch_id > 0) {
			$where 	.= ' AND je.branch_id ="'.$branch_id.'"';
		}	
		if (isset($date) && $date != '') {
			$where 	.= ' AND je.date ="'.$date.'"';
		}
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT je.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;

        // get the list of data 
        if ($this->data_total > 0) {
        	//, badr.BA_DispName AS debit_bank, bacr.BA_DispName AS credit_bank
        	$slno 	= 0;
        	$sql  	= 'SELECT je.id, je.date, je.amount, je.ledger_cr, je.ledger_dr, drl.title as dr_name, crl.title AS cr_name, cpy.OF_Name, bch.LC_Name, je.company_id, je.branch_id, je.created_at, je.bank_dr, je.bank_cr, CONCAT(uu.US_FName," ",uu.US_LName) AS updated_user, je.updated_at '
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY je.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "date"		: $sql 	.= 'je.date';	
				break;
				case "drledger"	: $sql 	.= 'drl.title';	
				break;
				case "crledger"	: $sql 	.= 'crl.title';	
				break;
				case "amt"		: $sql 	.= 'je.amount';	
				break;
				default 		: $sql 	.= 'je.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$statuslist 			= $this->listStatus(2);	
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $this->data_list[] 	= $row;
            }
        }
	}
	// save or update the Journal Entry
	function saveJournalEntry($inputs=[], $id=0)
	{
		$ret_status = 0;
		$this->msg 	= '';
		if ($id > 0) { // update 
	   		// back up entry
	   		$this->backupTables($id, 9);

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_journal_entry SET $updstring WHERE id=".$id;
	        mysqli_query($GLOBALS['con'],$sql);
	        $ret_status = 1;
	        $this->msg 	= 'Journal Entry Updated Successfully';
	   	}else { // save entry;
	   		$sql 	= "INSERT INTO acc_journal_entry ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query($GLOBALS['con'],$sql);
			
			$ret_status = ($res) ? 1: 0;
			$this->msg 	= ($ret_status == 1) ? 'Journal Entry Saved Successfully':'Failed to Save Journal Entry';
	   	}
	   	//$this->msg 	= $sql;
	   	return $ret_status;
	}
	// save or update the Ledger Data 
	// Created BY Bilin 18-11-2025
	function saveLedgerData($inputs=[], $id=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';
		// find the ie type from the ledger table  based on the inputed ledger id @ 27-01-2026		
		$chkietsql 	= mysqli_query($GLOBALS['con'], 'SELECT ie_type FROM acc_ledger WHERE id ="'.$inputs['ledger_id'].'"');
		$chkietrow 	= mysqli_fetch_row($chkietsql);
		if ($inputs['ie_type'] == "add") {
			$inputs['ie_type']  = $chkietrow[0];
		} else {
			$inputs['ie_type']  = ($chkietrow[0] == 1) ? 2 :1;
		}
		if ($id > 0) { // update 
	   		// back up entry
	   		$this->backJournal( $id, 0 );

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_journal SET $updstring WHERE id=".$id;
	        mysqli_query( $GLOBALS['con'], $sql );
	        $ret_status = 1;
	        $this->msg 	= 'Ledger Data Updated Successfully';
	   	} else { // save entry;
	   		$sql 	= "INSERT INTO acc_journal ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query( $GLOBALS['con'], $sql );
			if ( mysqli_affected_rows( $GLOBALS['con']) > 0 ) {
                $id 		= mysqli_insert_id( $GLOBALS['con']);
                $ret_status = 1;
                $updSql 	= 'UPDATE acc_journal SET parent_id = '.$id.' WHERE id ='.$id;
        		mysqli_query( $GLOBALS['con'], $updSql );
            } else {
            	$ret_status = 0;
            }
			
			$this->msg 	= ($ret_status == 1) ? 'Ledger Data Saved Successfully':'Failed to Save Ledger Data';
	   	}
	   	//$this->msg 	= $sql;
	   	return $ret_status;
	}
	/**
	 *  list all direct entered ledger data with filter and paginations
	 *  Created By Bilin @ 18-11-2025
	*/
	function listLedgerData( $inputs ) 
	{	
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$tables 	= ' FROM acc_journal AS jd '
				. ' INNER JOIN acc_ledger AS ld ON (ld.id = jd.ledger_id)'
				. ' LEFT JOIN locations AS bch ON (bch.LC_Id = jd.branch_id)';
		$where 		= ' WHERE jd.add_type = 1';
		$join 		= '';
		$fields 	= '';
		if (isset($office_id) && $office_id > 0) {
			$where 	.= ' AND (jd.office_id ="'.$office_id.'" OR jd.company_id ="'.$office_id.'")';
		}
		if (isset($ledger) && $ledger != '') {
			$where 	.= ' AND ld.title LIKE "%'.$ledger.'%" ';
		}
		if (isset($branch) && $branch != '') {
			$where 	.= ' AND bch.LC_Name LIKE "%'.$branch.'%" ';
		}	
		if (isset($date) && $date != '') {
			$where 	.= ' AND jd.date_entry ="'.$date.'"';
		}
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT jd.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;

        // get the list of data 
        if ($this->data_total > 0) {
        	$amt_types 		= ['0'=>'Cash Or Bank', '2'=>'Cash', '1'=>'Bank'];
        	$slno 			= 0;
			$data_list 		= [];
        	$sql 			= 'SELECT jd.id, jd.office_id, jd.location_id, jd.company_id, jd.branch_id, jd.ledger_id, jd.vendor_id, jd.amount, jd.converted, "0" AS amount_ratio, jd.amt_type, jd.ba_id, jd.ba_id_debit, jd.date_entry, jd.parent_id, jd.remarks, jd.created_at, jd.updated_at, ld.title AS ledger_name, bch.LC_Name AS branch_Name, jd.trackno, jd.ie_type, ld.ie_type AS ietp_ledger'
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY jd.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "date"		: $sql 	.= 'jd.date_entry';	
				break;
				case "ledger"	: $sql 	.= 'ld.title';	
				break;
				case "branch"	: $sql 	.= 'bch.LC_Name';	
				break;
				case "amt"		: $sql 	.= 'jd.amount';	
				break;
				default 		: $sql 	.= 'jd.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	$venderids  = [];
        	$bankids 	= [];
        	$locationids= [];
        	$officeids 	= [];
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $data_list[]  		= $row;
                $venderids[] 		= $row->vendor_id;
	        	$bankids[] 			= $row->ba_id;
	        	$bankids[] 			= $row->ba_id_debit;
	        	$locationids[] 		= $row->location_id;
	        	$officeids[] 		= $row->office_id;
	        	$officeids[] 		= $row->company_id;
            }
            $venders 			= $this->getIdNameAry($venderids, 1); //vender
            $bank_accounts 		= $this->getIdNameAry($bankids, 2); //bank
            $locations 			= $this->getIdNameAry($locationids, 3); //location
            $offices 			= $this->getIdNameAry($officeids, 4); // office
            foreach ($data_list AS $rw) {

                $rw->amount_ratio 	= ($rw->converted > 0) ? $rw->converted/$rw->amount:0; //12-01-2026
            	$rw->type_val 		= $amt_types[$rw->amt_type];
                $rw->OF_Name 		= $offices[$rw->office_id];
                $rw->LC_Name 		= ($rw->location_id > 0) ? $locations[$rw->location_id]:'';
                $rw->company_Name 	= $offices[$rw->company_id];
                $rw->vendor_Name 	= ($rw->vendor_id > 0) ? $venders[$rw->vendor_id]:'';
                $rw->bank_Name 		= ($rw->ba_id > 0) ? $bank_accounts[$rw->ba_id]:'';
                $rw->bank_Dr_Name 	= ($rw->ba_id_debit > 0) ? $bank_accounts[$rw->ba_id_debit]:'';

            	$this->data_list[] = $rw;
            }
        }
	}
	// reports created by Bilin started @ 30-09-2025
	// list ledger based amount for p and L report
	function listProfitLossold($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->sql_query 	= '';
		$groupcodes 		= ["purchase"=>"l0", "direct_expense"=>"l1", "sales"=>"r0", "direct_income"=>"r1", "indirect_expense"=>"l2", "indirect_income"=>"r2"];
		$resData 			= [];
		$ledger_ids 		= [];

		$tables 		= ' FROM acc_journal AS j '
			. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id) '
			. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id) '
			. ' LEFT JOIN acc_groups AS pgr ON (pgr.id = grp.parent_id) '
			. ' LEFT JOIN acc_ledger AS pldr ON (pldr.id = ledr.parent_id) ';
		$where 			= ' WHERE j.status != 3 AND grp.type IN ("Income","Expense") AND grp.status != 3 AND ledr.status != 3 AND (grp.parent_id = 0 OR pgr.status != 3)';
		if ( $company_id > 0 ) {
			$where  	.= ' AND j.company_id = "'.$company_id.'"';
		}
        if (isset($from_date) && $from_date != "") {
        	$where 		.= ' AND j.date_entry >= "'.$from_date.'"';
        }
        if (isset($to_date) && $to_date != "") {
        	$where 		.= ' AND j.date_entry <= "'.$to_date.'"';
        }
        switch($trans_type) {        	
        	case '1' : // external
        	$where 		.= ' AND j.branch_id = j.location_id';
        	if (isset($branch_id) && $branch_id > 0) {
        		$where 	.= ' AND j.branch_id = "'.$branch_id.'"';
        	}
        	break;
        	case '2' : // internal
        	$where 		.= ' AND j.branch_id != j.location_id';        	
        	if (isset($branch_id) && $branch_id > 0) {
        		$where 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
        	}
        	break;
        	// both
        	default  : if (isset($branch_id) && $branch_id > 0) {
        		$where 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
        	}
        	break;
        }
        $sql 			= 'SELECT j.ledger_id, SUM(j.amount) AS ledger_amt, SUM(IF(j.converted > 0,j.converted,j.amount)) As ledger_convert, grp.type, ledr.is_return, IF (ledr.parent_id > 0, pldr.title, ledr.title) AS ledger_name, ledr.parent_id, ledr.group_id, IF (grp.parent_id > 0, pgr.code, grp.code) As group_code, IF (grp.parent_id > 0, pgr.title, grp.title) As group_name, j.company_id, j.ie_type, ledr.ie_type AS ietp_ledger '
        .' '.$tables
        .' '.$where
        .' GROUP BY j.ledger_id, j.company_id, j.ie_type '
        .' ORDER BY ledr.is_return ASC, ledr.title ASC';
        $this->sql_query= $sql;
		$res 			= mysqli_query($GLOBALS['con'],$sql);
		while ($row = mysqli_fetch_object($res)) {
			$group_code = trim($row->group_code);	
			$group_cid = (isset($groupcodes[$group_code])) ? $groupcodes[$group_code] : '0';
			// converted amount shows in p and l 13-01-2026
			if ( $company_id > 0 && $row->company_id == $company_id &&  $row->ledger_convert > 0 ) {
				$row->ledger_amt = $row->ledger_convert;
			}
			
			if ($group_cid == '0') {
				//profit and loss account only show the defined groups data no need other data	
			} else {
				if (!isset($resData[$group_cid])) {
					$resData[$group_cid] = ['total'=>0, 'title'=>$row->group_name, 'list'=>[]];
				} 
				$i 		= count($resData[$group_cid]['list']); // find the number of ledger present in the groups
				if ($row->ietp_ledger == $row->ie_type) { // 28-01-2026
					$amt 	= ($row->is_return == 1) ? (0-$row->ledger_amt):$row->ledger_amt; // check the amount was +ve / -ve
					$ledamt = $row->ledger_amt;
				} else {
					$amt 	= ($row->is_return != 1) ? (0-$row->ledger_amt):$row->ledger_amt; // check the amount was +ve / -ve
					$ledamt = (0-$row->ledger_amt);
				}

				$ledid  = ($row->parent_id > 0) ? $row->parent_id : $row->ledger_id; // find the ledger id
				
				// find the ledger id already in the list then update the amount
				if (isset($ledger_ids[$ledid])) { 
					$i 	= $ledger_ids[$ledid];					
				} else { 
					// new then add into array
					$resData[$group_cid]['list'][$i] = ['title'=>($row->is_return == 1 ? "Less: ".$row->ledger_name:$row->ledger_name), 'amt'=>0];
				}
				$resData[$group_cid]['list'][$i]['amt'] += $ledamt;
				
				$resData[$group_cid]['total'] += $amt; // add the ledger amount into the group total
				$ledger_ids[$ledid] = $i;
			}	
			$this->data_list 	= $resData;
		}
	}
	// profit and loss account generated array based on the group codes 
	// find all ledger name, parent ledger name, group name, parent group name, sum amount ,return type
	// group code check in group list and parent group
	// reports created by Bilin started @ 29-10-2025 
	function listProfitLoss($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->sql_query 	= '';
		$groupcodes 		= ["purchase"=>"l0", "direct_expense"=>"l1", "sales"=>"r0", "direct_income"=>"r1", "indirect_expense"=>"l2", "indirect_income"=>"r2"];
		$groupcoderev 		= ["purchase"=>"r", "direct_expense"=>"r", "sales"=>"l", "direct_income"=>"l", "indirect_expense"=>"r", "indirect_income"=>"l"];
		$internalCmp 		= [];
		$internalBranch		= [];
		$branch_ids 		= [];
		$company_ids		= [];
		$resData 			= [];
		$ledger_ids 		= [];

		$tables 		= ' FROM acc_journal AS j '
			. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id) '
			. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id) '
			. ' LEFT JOIN acc_groups AS pgr ON (pgr.id = grp.parent_id) '
			. ' LEFT JOIN acc_ledger AS pldr ON (pldr.id = ledr.parent_id) ';
		$where 			= ' WHERE j.status != 3 AND grp.type IN ("Income","Expense") AND grp.status != 3 AND ledr.status != 3 AND (grp.parent_id = 0 OR pgr.status != 3)';
		if ( $company_id > 0 ) {
			$where  	.= ' AND (j.company_id = "'.$company_id.'" OR j.office_id = "'.$company_id.'")';
		}
        if (isset($from_date) && $from_date != "") {
        	$where 		.= ' AND j.date_entry >= "'.$from_date.'"';
        }
        if (isset($to_date) && $to_date != "") {
        	$where 		.= ' AND j.date_entry <= "'.$to_date.'"';
        }
        switch($trans_type) {        	
        	case '1' : // external        	
        	if (isset($branch_id) && $branch_id > 0) {
        		$where 	.= ' AND j.branch_id = "'.$branch_id.'"';
        		$where 		.= ' AND j.branch_id = j.location_id';
        	}
        	if ( $company_id > 0 ) {
				$where 		.= ' AND j.company_id = j.office_id';  
			}
        	break;
        	case '2' : // internal        	      	
        	if (isset($branch_id) && $branch_id > 0) {
        		$where 		.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
        		$where 		.= ' AND j.branch_id != j.location_id';  
        	} else {
        		$where 		.= ' AND j.company_id != j.office_id';  
        	}
        	break;
        	// both
        	default  : if (isset($branch_id) && $branch_id > 0) {
        		$where 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
        	}
        	break;
        }
        $sql 			= 'SELECT j.ledger_id, SUM(j.amount) AS ledger_amt, SUM(IF(j.converted > 0,j.converted,j.amount)) As ledger_convert, grp.type, ledr.is_return, IF (ledr.parent_id > 0, pldr.title, ledr.title) AS ledger_name, ledr.parent_id, ledr.group_id, IF (grp.parent_id > 0, pgr.code, grp.code) As group_code, IF (grp.parent_id > 0, pgr.title, grp.title) As group_name, j.company_id, j.office_id, j.ie_type, ledr.ie_type AS ietp_ledger, ledr.is_internal, j.location_id, j.branch_id '
        .' '.$tables
        .' '.$where
        .' GROUP BY j.ledger_id, j.company_id, j.office_id, j.location_id, j.branch_id, j.ie_type '
        .' ORDER BY ledr.is_return ASC, ledr.title ASC';
        $this->sql_query= $sql;
		$res 			= mysqli_query($GLOBALS['con'],$sql);
		while ($row = mysqli_fetch_object($res)) {
			$group_code = trim($row->group_code);	
			$group_cid 	= (isset($groupcodes[$group_code])) ? $groupcodes[$group_code] : '0';
			$group_rid 	= (isset($groupcoderev[$group_code])) ? $groupcoderev[$group_code] : '0';
			// converted amount shows in p and l 13-01-2026
			if ( $company_id > 0 && $row->company_id == $company_id &&  $row->ledger_convert > 0 ) {
				$row->ledger_amt = $row->ledger_convert;
			}
			
			if ($group_cid == '0') {
				//profit and loss account only show the defined groups data no need other data	
			} else {
				if (!isset($resData[$group_cid])) {
					$resData[$group_cid] = ['total'=>0, 'title'=>$row->group_name, 'list'=>[]];
				} 
				$i 		= count($resData[$group_cid]['list']); // find the number of ledger present in the groups
				if ($row->ietp_ledger == $row->ie_type) { // 28-01-2026
					$amt 	= ($row->is_return == 1) ? (0-$row->ledger_amt):$row->ledger_amt; // check the amount was +ve / -ve
					$ledamt = $row->ledger_amt;
				} else {
					$amt 	= ($row->is_return != 1) ? (0-$row->ledger_amt):$row->ledger_amt; // check the amount was +ve / -ve
					$ledamt = (0-$row->ledger_amt);
				}

				$ledid  = ($row->parent_id > 0) ? $row->parent_id : $row->ledger_id; // find the ledger id
				
				// find the ledger id already in the list then update the amount
				if (isset($ledger_ids[$ledid])) { 
					$i 	= $ledger_ids[$ledid];					
				} else { 
					// new then add into array
					$resData[$group_cid]['list'][$i] = ['title'=>($row->is_return == 1 ? "Less: ".$row->ledger_name:$row->ledger_name), 'amt'=>0];
				}
				$resData[$group_cid]['list'][$i]['amt'] += $ledamt;
				
				$resData[$group_cid]['total'] += ($row->is_return == 1) ? $amt : $ledamt; // add the ledger amount into the group total
				$ledger_ids[$ledid] = $i;

				// pending amount of branch wise and company wise calcualtion start 02-02-2026
				$selbcid 	= 0;
				$typesid 	= 0;
				$mtype 		= 0;
				if (isset($branch_id) && $branch_id > 0 && $row->branch_id != $row->location_id && $row->branch_id > 0 && $row->location_id > 0) {
					if ($row->branch_id != $branch_id) {
						if (!isset($internalBranch[$row->branch_id])) {
							$internalBranch[$row->branch_id] = [0=>['title'=>'Pending - from ', 'amt'=>0], 1=>['title'=>'Pending - To ', 'amt'=>0]];
							$branch_ids[] 	= $row->branch_id;
						}						
						$selbcid 		= $row->branch_id;
						$typesid 		= ($group_rid == 'r') ? 0 :1;
					}
					if ($row->location_id != $branch_id) {
						if (!isset($internalBranch[$row->location_id]) && $row->location_id != $branch_id) {
							$internalBranch[$row->location_id] = [0=>['title'=>'Pending - from ', 'amt'=>0], 1=>['title'=>'Pending - To ', 'amt'=>0]];
							$branch_ids[] 	= $row->location_id;
						}						
						$selbcid 		= $row->location_id;
						$typesid 		= ($group_rid == 'r') ? 1 :0;
					}					 
					$mtype 				= 1;					
				} else if ($company_id > 0 && $row->company_id != $row->office_id) {					
					if ($row->company_id != $company_id) {
						if (!isset($internalCmp[$row->company_id])) {
							$internalCmp[$row->company_id] = [0=>['title'=>'Pending - from ', 'amt'=>0], 1=>['title'=>'Pending - To ', 'amt'=>0]];
							$company_ids[] 	= $row->company_id;
						} 						
						$selbcid 		= $row->company_id;
						$typesid 		= ($group_rid == 'r') ? 0 :1;
					}
					if ($row->office_id != $company_id) {

						if (!isset($internalCmp[$row->office_id])) {
							$internalCmp[$row->office_id] = [0=>['title'=>'Pending - from ', 'amt'=>0], 1=>['title'=>'Pending - To ', 'amt'=>0]];
							$company_ids[] 	= $row->office_id;
						}						
						$selbcid 		= $row->office_id;
						$typesid 		= ($group_rid == 'r') ? 1 :0;
					}
					$mtype = 2;					
				}
				if ($mtype == 1) {	
					if ($row->is_internal == 1) {
						$internalBranch[$selbcid][$typesid]['amt'] -= ($row->is_return == 1) ? $amt : $ledamt;	
					} else {
						$internalBranch[$selbcid][$typesid]['amt'] += ($row->is_return == 1) ? $amt : $ledamt;	
					}
				} else if ($mtype == 2) {
					if ($row->is_internal == 1) {
						$internalCmp[$selbcid][$typesid]['amt'] -= ($row->is_return == 1) ? $amt : $ledamt;
					} else {
						$internalCmp[$selbcid][$typesid]['amt'] += ($row->is_return == 1) ? $amt : $ledamt;
					}					
				}
				// internal pending amount total (from and to) 02-02-2026
			}
		}

		// find the total and find the branch and contry 02-02-2026
		if (!empty($branch_ids) || !empty($company_ids)) {
			
			$locList 	= $this->getIdNameAry($branch_ids,3);
			$compList 	= $this->getIdNameAry($company_ids,4);

			if (!isset($resData['r2'])) {
				$resData['r2'] = ['total'=>0, 'title'=>'Indirect Income', 'list'=>[]];
			}
			if (!isset($resData['l2'])) {
				$resData['l2'] = ['total'=>0, 'title'=>'Indirect Expenses', 'list'=>[]];
			}
			foreach ($internalBranch AS $bid=>$bdata) {
				$amt = ($bdata[0]['amt'] >= $bdata[1]['amt']) ?  $bdata[0]['amt']-$bdata[1]['amt']:0;
				$amt1 = ($bdata[0]['amt'] <= $bdata[1]['amt']) ?  $bdata[1]['amt']-$bdata[0]['amt']:0;

				if($amt > 0) {
					$resData['r2']['list'][] = ['title'=>$bdata[0]['title'].$locList[$bid], 'amt'=>$amt];
					$resData['r2']['total'] += $amt;
				}
				if($amt1 > 0) {
					$resData['l2']['list'][] = ['title'=>$bdata[1]['title'].$locList[$bid], 'amt'=>$amt1];
					$resData['l2']['total'] += $amt1;
				}
			}

			foreach ($internalCmp AS $bid=>$bdata) {
				$amt = ($bdata[0]['amt'] >= $bdata[1]['amt']) ?  $bdata[0]['amt']-$bdata[1]['amt']:0;
				$amt1 = ($bdata[0]['amt'] <= $bdata[1]['amt']) ?  $bdata[1]['amt']-$bdata[0]['amt']:0;
				if($amt > 0) {
					$resData['r2']['list'][] = ['title'=>$bdata[0]['title'].$compList[$bid], 'amt'=>$amt];
					$resData['r2']['total'] += $amt;
				}
				if($amt1 > 0) {
					$resData['l2']['list'][] = ['title'=>$bdata[1]['title'].$compList[$bid], 'amt'=>$amt1];
					$resData['l2']['total'] += $amt1;
				}
				unlink($internalCmp[$bid]);
			}
		}
		// static entry related section finished in p and l 02-2-2026
				

		$this->data_list 	= $resData;
	}
	// list vendor and vendor based ledger amount 
	function listRptVendor($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->sql_query 	= '';
		$sql 				= 'SELECT id, type, name, gst_no FROM acc_vendors WHERE status != 3 ORDER BY name';
		$this->sql_query 	= $sql;
		$res 		= mysqli_query($GLOBALS['con'],$sql);
		$vendortype = ['1'=>'Vendor', '2'=>'Debtor'];
		while ($row = mysqli_fetch_object($res)) {
			$this->data_list[$row->id] 	= ['id'=>$row->id, 'title'=>$row->name.' - '.$row->gst_no.'  ('.$vendortype[$row->type].')', 'income'=>0, 'expense'=>0];
		}
		$sql  		= 'SELECT j.vendor_id, SUM(j.amount) AS vend_amt, grp.type, j.ie_type, ledr.ie_type AS ietp_ledger '
			. ' FROM acc_journal AS j '
			. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id)'
			. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id)'
			. ' WHERE  grp.status != 3 AND ledr.status != 3 AND j.vendor_id > 0';
			//grp.type IN ("Income","Expense") AND					
		if (isset($company_id) && $company_id > 0) {
    		$sql 	.= ' AND j.company_id = "'.$company_id.'"';
    	}
    	if (isset($branch_id) && $branch_id > 0) {
    		$sql 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
    	}
        if (isset($from_date) && $from_date != "") {
        	$sql 	.= ' AND j.date_entry >= "'.$from_date.'"';
        }
        if (isset($to_date) && $to_date != "") {
        	$sql 	.= ' AND j.date_entry <= "'.$to_date.'"';
        }
        $sql 		.= ' GROUP BY j.vendor_id, j.ie_type, grp.type'
        			. ' ORDER BY j.vendor_id ASC';
        $this->sql_query 	= $sql;
		$res 		= mysqli_query($GLOBALS['con'],$sql);
		while ($rw 	= mysqli_fetch_object($res)) {

			if ($rw->ietp_ledger == $rw->ie_type) { // 28-01-2026
				$type 	= ($rw->type == 'Income' || $rw->type == 'Asset') ? 1 :2;
			} else {
				$type 	= ($rw->type == 'Income' || $rw->type == 'Asset') ? 2 :1;
			}			
			if(!isset($this->data_list[$rw->vendor_id])) {
				$this->data_list[$rw->vendor_id] 	= ['id'=>$rw->vendor_id, 'title'=>'deleted', 'income'=>0, 'expense'=>0];
			}
			$this->data_list[$rw->vendor_id]['income'] 	+= ($type == 1) ? $rw->vend_amt:0;
			$this->data_list[$rw->vendor_id]['expense'] += ($type == 2) ? $rw->vend_amt:0;
		}
	}
	// list ledger and amount based on the selected vendor
	function listRptVendorDet($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->sql_query 	= '';
		// get the opening balance of the vendor 03-02-2026
		$resOpen 	= mysqli_query($GLOBALS['con'],'SELECT open_amt, open_type, open_date, country_id FROM acc_vendors WHERE id="'.$vendor_id.'" ORDER BY id LIMIT 0, 1');
		$rwopen 	= mysqli_fetch_object($resOpen);
		$this->data_list[0] = ['id'=>0, 'title'=>date("M d, Y", strtotime($rwopen->open_date)).' - Opening Balance', 'income'=>(($rwopen->open_type == 0) ? $rwopen->open_amt:0), 'expense'=>(($rwopen->open_type == 1) ? $rwopen->open_amt:0) ];
		// get the sum of all ledger and bills in between vender date and searched from date
		if (isset($from_date) && $from_date != "" && $from_date > $rwopen->open_date) {
			$intot 		= $this->data_list[0]['income'];
			$extot 		= $this->data_list[0]['expense'];
			$sqltol  	= 'SELECT sum(b.total_amount) as totalamt, sum(b.tds_amount) as totaltds, b.bill_type FROM acc_bills AS b WHERE b.vendor_id = "'.$vendor_id.'" AND b.bill_date < "'.$from_date.'" GROUP BY b.bill_type ORDER BY b.bill_type';
			$ret 		= mysqli_query($GLOBALS['con'],$sqltol);
			while ($rt 	= mysqli_fetch_object($ret)) {
				if ($rt->bill_type ==0) {
					$intot += $rt->totalamt - $rt->totaltds;
				} else {
					$extot += $rt->totalamt - $rt->totaltds;					
				}
			}
			$sql  		= 'SELECT j.ledger_id, SUM(j.amount) AS vend_amt, SUM(j.converted) AS vend_cvrt, grp.type, j.ie_type, ledr.ie_type AS ietp_ledger, j.company_id'
			. ' FROM acc_journal AS j '
			. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id)'
			. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id)'
			. ' WHERE grp.status != 3 AND ledr.status != 3 AND j.vendor_id = "'.$vendor_id.'"  AND j.date_entry < "'.$from_date.'"'
			. ' GROUP BY j.ledger_id, grp.type, j.ie_type ORDER BY j.ledger_id ASC';
			$res 		= mysqli_query($GLOBALS['con'],$sql);
			while ($rw 	= mysqli_fetch_object($res)) {
				$income 	= ($rw->type == 'Income' || $rw->type == 'Liability') ? $rw->vend_amt : 0;			
				$expense 	= ($rw->type != 'Income' && $rw->type != 'Liability') ? $rw->vend_amt : 0;	
				$intot 		+= ($rw->ietp_ledger == $rw->ie_type) ? $income:$expense;
				$extot 		+= ($rw->ietp_ledger == $rw->ie_type) ? $expense:$income;
			}			
			if ($intot > $extot) {
				$this->data_list[0]['income'] 	= $intot-$extot;
				$this->data_list[0]['expense']	= 0;
			} else {
				$this->data_list[0]['expense'] 	= $extot-$intot;
				$this->data_list[0]['income']	= 0;
			}
			$this->sql_query 	= $sql;
		}
		// end 03-02-2026

		// get all entry based on the peroid selected by the user
		$sql  		= 'SELECT j.id, j.ledger_id, ledr.title, SUM(j.amount) AS vend_amt, SUM(j.converted) AS vend_cvrt, grp.type, j.ie_type, ledr.ie_type AS ietp_ledger, j.date_entry, j.company_id'
			. ' FROM acc_journal AS j '
			. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id)'
			. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id)'
			. ' WHERE grp.status != 3 AND ledr.status != 3 AND j.vendor_id = "'.$vendor_id.'"';
			// grp.type IN ("Income","Expense") AND
		$sqlbill  	= 'SELECT b.id, b.bill_name, b.bill_date, b.total_amount, b.tds_amount, b.bill_type FROM acc_bills AS b WHERE b.total_amount > 0  AND b.vendor_id = "'.$vendor_id.'"';
		if (isset($company_id) && $company_id > 0) {
    		$sql 	.= ' AND j.company_id = "'.$company_id.'"';
    		$sqlbill.= ' AND b.company_id = "'.$company_id.'"';
    	}
    	if (isset($branch_id) && $branch_id > 0) {
    		$sql 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
    		$sqlbill.= ' AND b.branch_id = "'.$branch_id.'"';
    	}
        if (isset($from_date) && $from_date != "") {
        	$sql 	.= ' AND j.date_entry >= "'.$from_date.'"';
        	$sqlbill.= ' AND b.bill_date >= "'.$from_date.'"';
        }
        if (isset($to_date) && $to_date != "") {
        	$sql 	.= ' AND j.date_entry <= "'.$to_date.'"';
        	$sqlbill.= ' AND b.bill_date <= "'.$to_date.'"';
        }
        $sql 		.= ' GROUP BY j.id ORDER BY j.ledger_id ASC';
        $sqlbill 	.= ' GROUP BY b.id ORDER BY b.id ASC';
        //$this->sql_query 	= $sql;
        $res 		= mysqli_query($GLOBALS['con'],$sql);
		while ($rw 	= mysqli_fetch_object($res)) {

			if (isset($company_id) && $company_id == $rw->company_id && $rw->vend_cvrt > 0) { 
				// company changes then show the converted amt
				$rw->vend_amt  = $rw->vend_cvrt;
			}
			$income 	= ($rw->type == 'Income' || $rw->type == 'Liability') ? $rw->vend_amt : 0;			
			$expense 	= ($rw->type != 'Income' && $rw->type != 'Liability') ? $rw->vend_amt : 0;		
			if (!isset($this->data_list[$rw->id])) {	
				$this->data_list[$rw->id] 	= ['id'=>$rw->id, 'title'=>date("M d, Y", strtotime($rw->date_entry)).' - '.$rw->title, 'income'=>0, 'expense'=>0];
			} 
			// multiple values in same ledger (after the ie type section added so changed) @ 28-01-2026
			$this->data_list[$rw->id]['income'] 	+= ($rw->ietp_ledger == $rw->ie_type) ? $income:$expense;
			$this->data_list[$rw->id]['expense'] 	+= ($rw->ietp_ledger == $rw->ie_type) ? $expense:$income;
		}
		// process bills
		$resb 		= mysqli_query($GLOBALS['con'],$sqlbill);
		while ($rw 	= mysqli_fetch_object($resb)) {

			$this->data_list['b'.$rw->id] 	= ['id'=>'b'.$rw->id, 'title'=>date("M d, Y", strtotime($rw->bill_date)).' - '.$rw->bill_name.' - (Total:'.$rw->total_amount.', TDS :'.$rw->tds_amount.')', 'income'=>(($rw->bill_type ==0) ? ($rw->total_amount-$rw->tds_amount) : 0), 'expense'=>(($rw->bill_type ==1) ? ($rw->total_amount-$rw->tds_amount) : 0)];
		}
	}
	// list all ledger based transaction list based on the company and time period
	function listRptLedgerDet($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$retTotals 			= ['total_income'=>0, 'total_expense'=> 0];
		$this->sql_query 	= '';
		$type 				= '';
		$company_id 		= (isset($company_id)) ? $company_id :0;
		$branch_id 			= (isset($branch_id)) ? $branch_id :0;
		$ledger_id 			= (isset($ledger_id)) ? $ledger_id :0;
        $trans_type 		= (isset($trans_type)) ? (int)$trans_type:0;
        // any contra ledger related with this selected ledger 17-12-2025
        $less_ledgers 		= [];
        $plus_ledgers 		= [];
        $ledger_ids 		= [];
        if ($ledger_id > 0) {
        	$sqlcontra 	= 'SELECT id, less_id, plus_id FROM acc_ledger WHERE is_contra = 1 AND (less_id = '.$ledger_id.' OR plus_id = '.$ledger_id.') AND id != '.$ledger_id.' ORDER BY id ASC';
        	$rescontra 	= mysqli_query($GLOBALS['con'],$sqlcontra);
			while ($rwcontra = mysqli_fetch_object($rescontra)) {
				$ledger_ids[] 		= $rwcontra->id;
				if ($rwcontra->less_id == $ledger_id) {
					$less_ledgers[] = $rwcontra->id;
				}
				if ($rwcontra->plus_id == $ledger_id) {
					$plus_ledgers[] = $rwcontra->id;
				}
			}
        }
        // end the contra related checking

		$where 				= ' WHERE ledr.status != 3 AND j.amount > 0 ';
		// AND grp.type IN ("Income","Expense")
		//'Capital','Income','Expense','Asset','Liability'
		if ($company_id > 0) {
        	$where 	.= ' AND (j.company_id = "'.$company_id.'" OR j.office_id = "'.$company_id.'")';
        }
        if ($branch_id > 0) {
        	$where 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
        }
        if (!empty($ledger_ids)) { //17-12-2025
        	$where 	.= ' AND (j.ledger_id IN ('.implode(",",$ledger_ids).') OR j.ledger_id = "'.$ledger_id.'")';
        } else if ($ledger_id > 0) {
        	$where 	.= ' AND j.ledger_id = "'.$ledger_id.'"';
        }
        if (isset($from_date) && $from_date != "") {
        	$where 	.= ' AND j.date_entry >= "'.$from_date.'"';
        }
        if (isset($to_date) && $to_date != "") {
        	$where 	.= ' AND j.date_entry <= "'.$to_date.'"';
        }
		$tables 			= ' FROM acc_journal AS j '
				. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id)'
				. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id)'
				. ' LEFT JOIN tracks AS t ON (t.TR_Id = j.track_id)';
		switch($trans_type) {
        	// external
        	case '1' : $where 	.= ' AND j.branch_id = j.location_id';
        	break;
        	// internal
        	case '2' : $where 	.= ' AND j.branch_id != j.location_id';  
        	break;
        }
        // find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT j.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;	
         // get the list of data 
        if ($this->data_total > 0) {

        	$slno 	= 0;
        	$sql  	= 'SELECT j.id, j.amount, j.converted, j.office_id, j.company_id, j.location_id, j.branch_id, ledr.title, grp.type, j.date_entry, t.TR_Track, j.ledger_id, des.DS_Description, j.ie_type, j.trackno, ledr.ie_type AS ietp_ledger, ledr.is_same_side '
        			.' '.$tables
        			.' LEFT JOIN balance_sheets AS bs On (bs.BS_Id = j.balance_sheet_id)'
        			.' LEFT JOIN descriptions AS des On (des.DS_Id = bs.BS_Description)'
        			.' '.$where
        			.' GROUP BY j.id'
        			.' ORDER BY j.date_entry ASC, j.amount ASC';
        	if (isset($limit) && $limit > 0) {
        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$res 					= mysqli_query($GLOBALS['con'], $sql);
        	$statuslist 			= $this->listStatus(2);
        	while ($row = mysqli_fetch_object($res)) {
                //13-01-2026 currency converted amount fetch
                if (($company_id == $row->company_id && $row->converted > 0) || ($branch_id == $row->branch_id && $row->converted > 0 && $branch_id > 0) ) {
                	$row->amount 	= $row->converted;
                }
                $slno++;
                $row->slno 			= $slno;
                $row->income 		= 0;
                $row->expense 		= 0;
                $incomeexpense 		= 0; //28-01-2026
                $type 				= strtolower($row->type);
                if (($type == "income" || $type == "liability") && ($row->ledger_id == $ledger_id || $ledger_id == 0 || in_array($row->ledger_id, $plus_ledgers)) ) {
                	if ($row->location_id != $row->branch_id && $branch_id > 0) {
                		if ($row->is_same_side == 1) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		}else if($row->location_id == $branch_id) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		} else {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		}
                	} else if ($row->office_id != $row->company_id && $company_id > 0) {
                		if ($row->is_same_side == 1) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		}else if($row->office_id == $company_id) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		} else {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		}
                	} else {
                		$incomeexpense 			= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                	}
                } else if ($row->ledger_id == $ledger_id || $ledger_id == 0 || in_array($row->ledger_id, $less_ledgers)){
                	if ($row->location_id != $row->branch_id && $branch_id > 0) {
                		if ($row->is_same_side == 1) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		}else if($row->location_id == $branch_id) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		} else {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		}
                	} else if ($row->office_id != $row->company_id && $company_id > 0) {
                		if ($row->is_same_side == 1) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		}else if($row->office_id == $company_id) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		} else {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		}
                	} else {
                		$incomeexpense 			= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                	}
                } 
                if ($incomeexpense == 1) { //28-01-2026
                	$row->income 		= $row->amount;
                } else {
                	$row->expense 		= $row->amount;
                }
                $this->data_list[] 	= $row;
            }
            // find the total income and expense list based on the above filter 13-01-2026
            $sqlie 		= 'SELECT j.ledger_id, SUM(IF(j.converted > 0,j.converted,j.amount)) As totals, SUM(j.converted) AS convertedamt, SUM(j.amount) AS totalamt, j.office_id, j.company_id, j.location_id, j.branch_id, ledr.title, grp.type, j.ie_type, ledr.ie_type AS ietp_ledger, ledr.is_same_side '
            	.' FROM acc_journal AS j '
            	.' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id) '
            	.' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id) '
            	.' '.$where
            	.' GROUP BY j.ledger_id, j.office_id, j.company_id, j.location_id, j.branch_id, j.ie_type '
            	.' ORDER BY j.ledger_id ASC, j.office_id ASC';
            $ie_res 	= mysqli_query($GLOBALS['con'], $sqlie);
            while ($row	= mysqli_fetch_object($ie_res)) {
            	$type 	= strtolower($row->type);
            	if (($company_id == $row->company_id && $row->totals > 0) || ($branch_id == $row->branch_id && $row->totals > 0 && $branch_id > 0) ) {
                	$row->totalamt 	= $row->totals;
                }
                $incomeexpense 		= 0; //28-01-2026
                if (($type == "income" || $type == "liability") && ($row->ledger_id == $ledger_id || $ledger_id == 0 || in_array($row->ledger_id, $plus_ledgers)) ) {
                	if ($row->location_id != $row->branch_id && $branch_id > 0) {
                		if ($row->is_same_side == 1) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		}else if($row->location_id == $branch_id) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		} else {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		}
                	} else if ($row->office_id != $row->company_id && $company_id > 0) {
                		if ($row->is_same_side == 1) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		}else if($row->office_id == $company_id) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		} else {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		}
                	} else {
                		$incomeexpense 			= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                	}
                } else if ($row->ledger_id == $ledger_id || $ledger_id == 0 || in_array($row->ledger_id, $less_ledgers)) {
                	if ($row->location_id != $row->branch_id && $branch_id > 0) {
                		if ($row->is_same_side == 1) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		}else if($row->location_id == $branch_id) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		} else {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		}
                	} else if ($row->office_id != $row->company_id && $company_id > 0) {
                		if ($row->is_same_side == 1) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		}else if($row->office_id == $company_id) {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                		} else {
                			$incomeexpense 		= ($row->ietp_ledger == $row->ie_type) ? 1 :2;
                		}
                	} else {
                		$incomeexpense 			= ($row->ietp_ledger == $row->ie_type) ? 2 :1;
                	}
                }
                if ($incomeexpense == 1) { //28-01-2026
                	$retTotals['total_income'] 	+= $row->totalamt;
                } else {
                	$retTotals['total_expense'] += $row->totalamt;
                }             	
            }
            //13-01-2026 ends

            /*
            // find the total income and expense amount based on the filters
            $seltfield  = 'SUM(amount) AS totalamt';// ($type == "income" || $type == "asset") ? 'SUM(amount) AS totalamt':'SUM(IF(converted > 0,converted,amount)) AS totalamt';
            $sqli = 'SELECT '.$seltfield.' FROM acc_journal WHERE ';
            if (!empty($plus_ledgers)) { 
	        	$sqli 	.= ' (ledger_id IN ('.implode(",",$plus_ledgers).') OR ledger_id = "'.$ledger_id.'")';
	        } else  {
	        	$sqli 	.= ' ledger_id = "'.$ledger_id.'"';
	        }
            $seltfield  = 'SUM(amount) AS totalamt'; //($type == "income" || $type == "asset") ? 'SUM(IF(converted > 0,converted,amount)) AS totalamt' :'SUM(amount) AS totalamt';	        
            $sqle = 'SELECT '.$seltfield.' FROM acc_journal WHERE ';
            if (!empty($less_ledgers)) { 
	        	$sqle 	.= ' (ledger_id IN ('.implode(",",$less_ledgers).') OR ledger_id = "'.$ledger_id.'")';
	        } else  {
	        	$sqle 	.= ' ledger_id = "'.$ledger_id.'"';
	        }
            if (isset($from_date) && $from_date != "") {
	        	$sqli 	.= ' AND date_entry >= "'.$from_date.'"';
	        	$sqle 	.= ' AND date_entry >= "'.$from_date.'"';
	        }
	        if (isset($to_date) && $to_date != "") {
	        	$sqli 	.= ' AND date_entry <= "'.$to_date.'"';
	        	$sqle 	.= ' AND date_entry <= "'.$to_date.'"';
	        }
	        switch($trans_type) {
	        	// external
	        	case '1' :  $sqli 	.= ' AND branch_id = location_id';
	        			    $sqle 	.= ' AND branch_id = location_id';
	        	break;
	        	// internal
	        	case '2' :  $sqli 	.= ' AND branch_id != location_id'; 
	        				$sqle 	.= ' AND branch_id != location_id';   
	        	break;
	        }
	        if ($type == "income") {
	        	$sqli 	.= ' AND (';
	        	$sqle 	.= ' AND (';
	        	if ($branch_id > 0) {
	        		$sqli 	.= ' (location_id != branch_id AND location_id="'.$branch_id.'") OR location_id="'.$branch_id.'"';
	        		$sqle 	.= ' location_id != branch_id AND branch_id="'.$branch_id.'"';
	        	} else if ($company_id > 0) {
	        		$sqli 	.= ' (office_id != company_id AND office_id="'.$company_id.'") OR office_id="'.$company_id.'"';
	        		$sqle 	.= ' office_id != company_id AND company_id="'.$company_id.'"';
	        	}
	        	$sqli 	.= ')';
	        	$sqle 	.= ')';
	        } else {
	        	$sqli 	.= ' AND (';
	        	$sqle 	.= ' AND (';
	        	if ($branch_id > 0) {
	        		$sqle 	.= ' (location_id != branch_id AND location_id="'.$branch_id.'") OR location_id="'.$branch_id.'"';
	        		$sqli 	.= ' location_id != branch_id AND branch_id="'.$branch_id.'"';
	        	} else if ($company_id > 0) {
	        		$sqle 	.= ' (office_id != company_id AND office_id="'.$company_id.'") OR office_id="'.$company_id.'"';
	        		$sqli 	.= ' office_id != company_id AND company_id="'.$company_id.'"';
	        	}
	        	$sqli 	.= ')';
	        	$sqle 	.= ')';
	        }
	        $sqli 	.= ' GROUP BY ledger_id';
	        $sqle 	.= ' GROUP BY ledger_id';
	        $inc_res 		= mysqli_query($GLOBALS['con'], $sqli);
			while($inc_row	= mysqli_fetch_array($inc_res,MYSQLI_NUM) ) {
				$retTotals['total_income'] += $inc_row[0];
			}	
			//$this->sql_query 		= $sqle;        
	        $exp_res 		= mysqli_query($GLOBALS['con'], $sqle);
			while($exp_row	= mysqli_fetch_array($exp_res,MYSQLI_NUM)) {
	        	$retTotals['total_expense'] += $exp_row[0];
			}*/
        }

		return $retTotals;
	}
	// add or delete thje balance sheet data into bank re consider table
	// created BY bilin AT 21-10-2025
	function saveordeleteBnkREc($bsid, $type, $user_id)
	{
		//delete the bsid based data dfrom table
		mysqli_query($GLOBALS['con'], "DELETE FROM acc_bank_consider WHERE bs_id=".$bsid);
		// insert the new data 
		mysqli_query($GLOBALS['con'], "INSERT INTO acc_bank_consider (bs_id, status, updated_at, updated_by) VALUES (".$bsid.", '".$type."','".date('Y-m-d H:i:s')."','".$user_id."')");
	}
	function openbalanceTypes() 
	{
		return ["1"=>"Captial", "2"=>"P and L Opening", "3"=>"Reserve", "4"=>"Loan Amount", "5"=>"Assets"];
	}
	/**
	 * List saved opening balance list
	 * common filter like company branch, title and year from and to
	 * Created By Bilin @ 24-11-25
	*/
	function listOpeningData($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';

		$tables 	= ' FROM acc_opening_balance AS ob '
				. ' LEFT JOIN locations AS bch ON (bch.LC_Id = ob.branch_id)';
		$where 		= ' WHERE ob.company_id > 0';
		$join 		= '  INNER JOIN offices AS ofs ON (ofs.OF_Id = ob.company_id)'
				.' LEFT JOIN acc_ledger AS ledr ON (ledr.id = ob.ledger_id)';
		$fields 	= '';
		if (isset($company) && $company > 0) {
			$where 	.= ' AND ob.company_id ="'.$company.'"';
		}
		if (isset($title) && $title != '') {
			$where 	.= ' AND ob.title LIKE "%'.$title.'%" ';
		}
		if (isset($branch) && $branch != '') {
			$where 	.= ' AND bch.LC_Name LIKE "%'.$branch.'%" ';
		}	
		if (isset($date_from) && $date_from != '') {
			$where 	.= ' AND ob.date_from ="'.$date_from.'"';
		}
		if (isset($date_to) && $date_to != '') {
			$where 	.= ' AND ob.date_to ="'.$date_to.'"';
		}
		// find the count from the query
		$sql_count 			= 'SELECT COUNT(DISTINCT ob.id) '.$tables.' '.$where;
		$res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total 	= $row_count[0];
        $this->sql_query 	= $sql_count;

        // get the list of data 
        if ($this->data_total > 0) {
        	$type_names		= $this->openbalanceTypes();
        	$updateids 		= [];
        	$predays 		= date('Y-m-d', strtotime('-30 days'));
        	$slno 			= 0;
			$data_list 		= [];
        	$sql 			= 'SELECT ob.id, ob.company_id, ob.branch_id, ob.type, ob.ledger_id, ob.title, ob.amount, ob.amt_type, ob.date_from, ob.date_to, ob.status, ob.created_at, ledr.title AS ledger_name, bch.LC_Name AS branch_Name, ofs.OF_Name AS company_name'
        			.' '.$tables.$join
        			.' '.$where
        			.' GROUP BY ob.id'
        			.' ORDER BY ';
			$sortby 	= (isset($sortby)) ? trim(strtolower($sortby)) :"id";
			$orderby	= (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
			switch ($sortby) {
				case "company"	: $sql 	.= 'ofs.OF_Name';	
				break;
				case "title"	: $sql 	.= 'ob.title';	
				break;
				case "branch"	: $sql 	.= 'bch.LC_Name';	
				break;
				case "amt"		: $sql 	.= 'ob.amount';	
				break;
				default 		: $sql 	.= 'ob.id';	
				break;
			}
			$sql 	.= ' '.$orderby;
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$this->sql_query 		= $sql;
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                if ($predays > date('Y-m-d', strtotime($row->created_at)) && $row->status ==2) {
                	$updateids[] 	= $row->id;
                	$row->status 	= 1;
                }
                $row->year_date 	= (date('Y', strtotime($row->date_from))).'-'.(date('Y', strtotime($row->date_to)));
                $row->type_name 	= $type_names[$row->type];
                $row->amttype_name 	= ($row->type == 1) ? "+ve or Profit" :"-ve or Loss";
                $this->data_list[]	= $row;
            }
            // set lock after the edit time period over.
            if (!empty($updateids)) {

            	$sql = "UPDATE acc_opening_balance SET status = 1 WHERE id IN (".implode(',',$updateids).")";
	        	mysqli_query( $GLOBALS['con'], $sql );
            }
        }
	}
	function saveOpeningData($inputs=[], $id=0) 
	{
		$ret_status = 0;
		$this->msg 	= '';
		if ($id > 0) { // update 
	   		// back up entry
	   		$this->backupTables( $id, 10 );

	   		$updstring 		= "";
			foreach ($inputs as $key => $value) { 
	            $updstring 	= $updstring .$key ."='".$value."', ";
	        }
	        $updstring = substr($updstring, 0, -2);
	        $sql = "UPDATE acc_opening_balance SET $updstring WHERE id=".$id;
	        mysqli_query( $GLOBALS['con'], $sql );
	        $ret_status = 1;
	        $this->msg 	= 'Opening Balance Updated Successfully';
	   	} else { // save entry;
	   		$sql 	= "INSERT INTO acc_opening_balance ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
			$res 	= mysqli_query( $GLOBALS['con'], $sql );
			if ( mysqli_affected_rows( $GLOBALS['con']) > 0 ) {
                $ret_status = 1;
            } else {
            	$ret_status = 0;
            }			
			$this->msg 	= ($ret_status == 1) ? 'Opening Balance Saved Successfully':'Failed to Save Opening Balance';
	   	}

		return $ret_status;
	}	
	/**
	 * Get open balance based on the start date of the financial year and company
	 * this data used for balance sheet 
	 * Type 1-capital, 2-pandl, 3-reserve, 4-loan 5-assets
	 * Created By Bilin 25-11-2025 Updated @ 10-12-2025
	*/
	function getOpenBalance($inputs) 
	{
		extract($inputs);
		$retData 	= [];
		$typeAry 	= ['1'=>'Captial Account', '2'=>'Profit And Loss Account', '3'=>'Reserve And Surplus', '4'=>'Loans', '5'=>'Fixed Assets'];
		$where 		= ' WHERE company_id > 0 ';
		if (isset($company_id) && $company_id > 0) {
			$where 	.= ' AND company_id ="'.$company_id.'"';
		}
		if (isset($branch_id) && $branch_id > 0) {
			$where 	.= ' AND branch_id ="'.$branch_id.'"';
		}
		if (isset($start_date) && $start_date != '') {
			$where 	.= ' AND date_from ="'.$start_date.'"';
		}
		$sql 		= 'SELECT id, type, ledger_id, title, amount, amt_type FROM acc_opening_balance '.$where.' ORDER BY type ASC, id ASC';
		$res 		= mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($res)) {
        	$type 	= ($row->type == 3)? 1:$row->type;
        	if (!isset($retData[$type])) {
        		$retData[$type] = ['0' =>(object)['amount'=>0, 'title'=>$typeAry[$type], 'showtype'=>1]];
        	}        	
        	$retData[$type][0]->amount = ($row->amt_type == 1) ? $retData[$type][0]->amount + $row->amount: $retData[$type][0]->amount - $row->amount;
        	$row->showtype = 2;
        	$row->amount = ($row->amt_type == 1) ? $row->amount: 0 - $row->amount;
        	$retData[$type][] = $row;
        }
        // P and L currant period amt added in the list
        $pandl 		= (isset($pandl) && $pandl) ? $pandl :0;
        $pandlbefore= (isset($pandlbefore) && $pandlbefore) ? $pandlbefore :0;
        if ($pandl != 0 || $pandlbefore != 0) {
        	$type 	= 2;
        	if (!isset($retData[$type])) {
        		$retData[$type] = ['0' =>(object)['amount'=>$pandl+$pandlbefore, 'title'=>$typeAry[$type], 'showtype'=>1]];
        	} else {
        		$retData[$type][0]->amount = ($retData[$type][0]->amount + $pandl) + $pandlbefore;
        	}
        	if ($pandlbefore != 0) {
        		$retData[$type][] = (object)['showtype'=>2, 'ledger_id'=>0, 'title'=>'Opening Balance 2', 'amt_type'=>($pandlbefore < 0)? 0:1, 'amount'=>$pandlbefore];
        	}
        	$retData[$type][] = (object)['showtype'=>2, 'ledger_id'=>0, 'title'=>'Current Period', 'amt_type'=>($pandl < 0)? 0:1, 'amount'=>$pandl];
        }

		return $retData;
	}
	/**
	 * Get the profit and loss account final amount based on the company, branch and data range.
	 * this is used for balance sheet of same date range/ current period section.
	 * Created by bilin @ 25-11-2025
	*/
	function profitAndLossTotal($inputs) 
	{
		extract($inputs);
		$ret_type 	= (isset($ret_type)) ? (int)$ret_type:0;
		$retData 	= [];
		$where 		= ' WHERE j.status != 3 AND grp.type IN ("Income","Expense") AND grp.status != 3 AND ledr.status != 3 AND (grp.parent_id = 0 OR pgr.status != 3)';
		if ( $company_id > 0 ) {
			$where  	.= ' AND j.company_id = "'.$company_id.'"';
		}
        if (isset($from_date) && $from_date != "") {
        	$where 		.= ' AND j.date_entry >= "'.$from_date.'"';
        }
        if (isset($to_date) && $to_date != "") {
        	$where 		.= ' AND j.date_entry <= "'.$to_date.'"';
        }
        if (isset($branch_id) && $branch_id > 0) {
    		$where 		.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
    	}
    	$sql 			= 'SELECT j.ledger_id, SUM(j.amount) AS ledger_amt, ledr.is_return, IF (grp.parent_id > 0, pgr.code, grp.code) As group_code'
        .' FROM acc_journal AS j '
		. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id) '
		. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id) '
		. ' LEFT JOIN acc_groups AS pgr ON (pgr.id = grp.parent_id) '
        .' '.$where
        .' GROUP BY j.ledger_id '
        .' ORDER BY ledr.is_return ASC, j.ledger_id ASC';
        $res 			= mysqli_query($GLOBALS['con'],$sql);
        $addamount 		= 0;
        $minusamount 	= 0;
		while ($row = mysqli_fetch_object($res)) {
			$group_code = trim($row->group_code);	
			if (in_array($group_code, ["purchase","direct_expense","indirect_expense"])) {
				if  ($row->is_return == 1) {
					$addamount 		= $addamount+$row->ledger_amt;
				}else {
					$minusamount 	= $minusamount+$row->ledger_amt;
				}
			} else if (in_array($group_code, ["sales","direct_income","indirect_income"])) {
				if  ($row->is_return == 1) {
					$minusamount 	= $minusamount+$row->ledger_amt;
				}else {
					$addamount 		= $addamount+$row->ledger_amt;
				}
			}
		}

		return ($ret_type == 1) ? $retData : ($addamount-$minusamount);
	}
	/**
	* List balance sheet data based on the user filter like date range, company and branch
	* Created By Bilin @ 4-10-2025 Updated @ 11-12-25
	* balance details getting steps below
	* a. find all assets and liabilities of selected date range
	* b. find the sundry debtors and creditor list and amount based on the entry mapped into the journal
	* c. fixed assets (opending values all+branch, fixed assets ledger based total after finance year date )
	*/
	function listBalanceSheet($inputs) 
	{
		extract($inputs);
		$basedate 			= "2025-04-01";
		$getBaseData 		= 0;
		$this->data_list 	= ['bank'=>[], 'branch'=>[], 'ledger'];
		$this->sql_query 	= '';
		$groupcodes 		= ["capital"=>"r0", "loans"=>"r1", "fixed_assets"=>"r2", "current_assets"=>"r3", "current_liabilities"=>"l0"];
		$resData 			= ['tax'=>['0'=>[],'inp'=>[], 'out'=>[]], 'creditor'=>[], 'debtor'=>[]];
		$ledger_ids 		= [];
		$where 				= ' WHERE j.status != 3 AND grp.type NOT IN ("Income","Expense") AND grp.status != 3 AND ledr.status != 3 AND (grp.parent_id = 0 OR pgr.status != 3) AND j.amount > 0 AND j.vendor_id <= 0 ';
		$whereitax 			= ' WHERE bl.tax_id > 0';
		$wherevndr 			= ' WHERE j.status != 3 AND grp.type NOT IN ("Income","Expense") AND grp.status != 3 AND ledr.status != 3 AND (grp.parent_id = 0 OR pgr.status != 3)';
		
		if ( $company_id > 0 ) {
			$where  	.= ' AND j.company_id = "'.$company_id.'"';
			$whereitax 	.= ' AND bl.company_id = "'.$company_id.'"';
			$wherevndr 	.= ' AND j.company_id = "'.$company_id.'"';
		}
        if (isset($from_date) && $from_date != "") {
        	//$where 		.= ' AND j.date_entry >= "'.$from_date.'"';
        	$whereitax 	.= ' AND bl.bill_date >= "'.$from_date.'"';
        	$wherevndr 	.= '  AND j.date_entry >= "'.$from_date.'"';
        }
        if ( isset($branch_id) && $branch_id > 0 ) {
	        $where 		.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
	        $whereitax 	.= ' AND bl.branch_id = "'.$branch_id.'"';
	        $wherevndr 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
	    }	 
	    $where1 		=  $where;   
        if (isset($fstart_date) && $fstart_date != "") {
        	$where 		.= ' AND j.date_entry >= "'.$fstart_date.'"';
        	if ($basedate < $fstart_date) {
        		$getBaseData = 1;
        		$where1 .= ' AND j.date_entry < "'.$fstart_date.'"';
        	}
        }    	
        if (isset($to_date) && $to_date != "") {
        	$where 		.= ' AND j.date_entry <= "'.$to_date.'"';
        	$whereitax 	.= ' AND bl.bill_date <= "'.$to_date.'"';
        	$wherevndr 	.= ' AND j.date_entry <= "'.$to_date.'"';
        }

	    // input tax list getting
	    $itaxsql 	= 'SELECT bl.tax_id, bl.bill_type, SUM(bl.tax_amount) AS tax1, SUM(bl.tax_amount2) As tax2, tx.title, tx.sub_title1, tx.sub_title2 '
	    .' FROM acc_bills AS bl '
	    .' INNER JOIN acc_tax_rules AS tx ON (tx.id = bl.tax_id) '
	    . $whereitax.' GROUP BY bl.tax_id, bl.bill_type ORDER BY tx.title ASC';
	    $itaxres 	= mysqli_query($GLOBALS['con'],$itaxsql);
	    $i 			= 0;
	    $taxkyary 	= [];
		while ($row = mysqli_fetch_object($itaxres)) {

			if (empty($resData['tax'][0])) {
				$resData['tax'][0] = (object)['amount'=>0, 'title'=>'Duties And Taxes', 'showtype'=>1];
			}
			$resData['tax'][0]->amount = ($row->bill_type == 1) ? $resData['tax'][0]->amount+$row->tax1+$row->tax2: $resData['tax'][0]->amount-($row->tax1+$row->tax2);
			$row->tax1 = ($row->bill_type == 1) ? $row->tax1: 0 - $row->tax1;
			if ($row->tax2 > 0) {
				$row->tax2  = ($row->bill_type == 1) ? $row->tax2: 0 - $row->tax2;
				$title 		= $row->sub_title1;
				$j 			= $i;
				if (isset($taxkyary[$row->tax_id][1])) {
					$j 			= $taxkyary[$row->tax_id][1];
					$row->tax2 	= $row->tax2+$resData['tax']['inp'][$j]->amount;
				} else {
					$taxkyary[$row->tax_id][1] = $i;
					$i++;
				}
				$resData['tax']['inp'][$j] = (object)['amount'=>$row->tax2, 'title'=>"Input ".$row->sub_title2, 'showtype'=>2];	
			} else {
				$title 		= $row->title;
			}
			$j 				= $i;
			if (isset($taxkyary[$row->tax_id][0])) {
				$j 			= $taxkyary[$row->tax_id][0];
				$row->tax1	= $row->tax1+$resData['tax']['inp'][$j]->amount;
			} else {
				$taxkyary[$row->tax_id][0] = $i;
				$i++;
			}
			$resData['tax']['inp'][$j] = (object)['amount'=>$row->tax1, 'title'=>"Input ".$title, 'showtype'=>2];
		}
		// input tax getting part completed and formated

		// vendors, dabtors, creditors list with amount start..
		$vndrSql = 'SELECT j.vendor_id, SUM(j.amount) AS amount, grp.type, grp.code, vnd.name AS vendor_title '
			. ' FROM acc_journal AS j '
			. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id) '
			. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id) '
			. ' INNER JOIN acc_vendors AS vnd ON (vnd.id = j.vendor_id) '
			. ' LEFT JOIN acc_groups AS pgr ON (pgr.id = grp.parent_id) '
			. $wherevndr
			. ' GROUP BY j.vendor_id, ledr.group_id ORDER BY j.vendor_id ASC ';
		$vndrres 	= mysqli_query($GLOBALS['con'],$vndrSql);
	    $ci  = $di	= 1;
	    $vndrary 	= ['ci'=>[], 'di'=>[]];
	    while ($row = mysqli_fetch_object($vndrres)) {
	    	//vendor_type - 2-Debtor 3-Credtor (hide from select , vnd.type AS vendor_type)
			
			if ($row->type == "Liability") { //$row->vendor_type == 3 || 
				if (empty($resData['creditor'][0])) {
					$resData['creditor'][0] = (object)['amount'=>0, 'title'=>'Sundry Creditors', 'showtype'=>1];
				}
				$j 				= $ci;
				if (isset($vndrary['ci'][$row->vendor_id])) {
					$j 			= $vndrary['ci'][$row->vendor_id];
					$row->amount= $row->amount+$resData['creditor'][$j]->amount;
				} else {
					$vndrary['ci'][$row->vendor_id] = $j;
					$ci++;
				}
				$resData['creditor'][$j] = (object)['amount'=>$row->amount, 'title'=>$row->vendor_title, 'showtype'=>2];
				$resData['creditor'][0]->amount = $resData['creditor'][0]->amount+$row->amount;				
			} else if ($row->type == "Asset") { //$row->vendor_type == 2 || 
				if (empty($resData['debtor'][0])) {
					$resData['debtor'][0] = (object)['amount'=>0, 'title'=>'Sundry Debtors', 'showtype'=>1];
				}
				$j 				= $di;
				if (isset($vndrary['di'][$row->vendor_id])) {
					$j 			= $vndrary['di'][$row->vendor_id];
					$row->amount= $row->amount+$resData['debtor'][$j]->amount;
				} else {
					$vndrary['di'][$row->vendor_id] = $j;
					$di++;
				}
				$resData['debtor'][$j] = (object)['amount'=>$row->amount, 'title'=>$row->vendor_title, 'showtype'=>2];
				$resData['debtor'][0]->amount = $resData['debtor'][0]->amount+$row->amount;
			}
	    }
		// vendor, debtors and creditors list end
	    // $resData['sql'] = $vndrSql;  
	    $ledgerDataL 	= [];
	    $ledgerDataR 	= [];
	    $capital 		= [];
	    $capitaltotal 	= 0;
	    $bankData 		= [];
	    //$bankids 		= [];
	    $branchData 	= [];
	    //$branchids 	= [];
	    //$ledgerids  	= [];
	    $otherledger 	= [];
	    // Bank Opening balance - first time
	    $bankData 		= $this->bankOpeningBal(['company_id'=>$company_id, 'date'=>$from_date]);

	    // Branch Opening balance - first time
	    $branchData 	= $this->branchOpeningBal(['company_id'=>$company_id, 'date'=>$from_date, 'branch_id'=>$branch_id]);
	    // get bank and branch data before the current financial year 
	    if ($getBaseData = 1) {
	    	list($resmain1, $sqlmain) = $this->periodicJournalEntry($where1);
	    	while ($rowmain = mysqli_fetch_object($resmain1)) {
	    		list($bankData, $branchData) = $this->subBankBranchBl($rowmain, $bankData, $branchData);
	    	}
	    }        		 

	    // get the details based on the finiancial year and mapped items / ledger entry
	    list($resmain, $sqlmain) = $this->periodicJournalEntry($where);	    
	    $resData['sql'] = $sqlmain;
	    $SubheadArray	= ['l'=>[], 'r'=>[], 'o'=>[], 'g'=>[]]; //12-02-2026
	    $spos 			= 0;
	    while ($rowmain = mysqli_fetch_object($resmain)) {
	    	// dual entry details
	    	if ($rowmain->is_contra == 1) {

	    		if (isset($ledgerData[$rowmain->less_id])) {
	    			$ledgerData[$rowmain->less_id]->amount += (0-$rowmain->amount);
	    			if ($rowmain->type == "Liability") {
		    			$ledgerDataR[$rowmain->less_id] = $ledgerData[$rowmain->less_id];
		    			$SubheadArray['r'][$rowmain->less_id] = $rowmain->group_code;
		    		} else {
						$ledgerDataL[$rowmain->less_id] = $ledgerData[$rowmain->less_id];
						$SubheadArray['l'][$rowmain->less_id] = $rowmain->group_code;
		    		}
	    		} else {
	    			//$ledgerids[] = $rowmain->less_id;
	    			if (isset($otherledger[$rowmain->less_id])) {
	    				$otherledger[$rowmain->less_id] = (object)['amount'=>0, 'title'=>"", 'showtype'=>2, 'code'=>$rowmain->group_code];
	    			} 
	    			$otherledger[$rowmain->less_id]->amount += (0-$rowmain->amount);
	    			$SubheadArray['o'][$rowmain->less_id] = $rowmain->group_code;
	    		}
	    		if (isset($ledgerData[$rowmain->plus_id])) {
	    			$ledgerData[$rowmain->plus_id]->amount += $rowmain->amount;
	    			if ($rowmain->type == "Liability") {
		    			$ledgerDataR[$rowmain->plus_id] = $ledgerData[$rowmain->plus_id];
		    			$SubheadArray['r'][$rowmain->plus_id] = $rowmain->group_code;
		    		} else {
						$ledgerDataL[$rowmain->plus_id] = $ledgerData[$rowmain->plus_id];
						$SubheadArray['l'][$rowmain->plus_id] = $rowmain->group_code;
		    		}
	    		} else {
	    			//$ledgerids[] = $rowmain->plus_id;
	    			if (isset($otherledger[$rowmain->plus_id])) {
	    				$otherledger[$rowmain->plus_id] = (object)['amount'=>0, 'title'=>"", 'showtype'=>2, 'code'=>$rowmain->group_code];
	    			} 
	    			$otherledger[$rowmain->plus_id]->amount += $rowmain->amount;
	    			$SubheadArray['o'][$rowmain->plus_id] = $rowmain->group_code;
	    		}
	    	} else { // single entry details

	    		if (!isset($ledgerData[$rowmain->ledger_id])) {
	    			$ledgerData[$rowmain->ledger_id] = (object)['amount'=>0, 'title'=>$rowmain->ledger_name, 'showtype'=>2, 'code'=>$rowmain->group_code];
	    		}
	    		$ledgerData[$rowmain->ledger_id]->amount += ($rowmain->is_return == 1) ? (0-$rowmain->amount): $rowmain->amount;
	    		if ($rowmain->group_code == "capital") {
	    			$capital[$rowmain->ledger_id] = $ledgerData[$rowmain->ledger_id];
	    			$capitaltotal += $ledgerData[$rowmain->ledger_id]->amount;
	    		} else if ($rowmain->type == "Liability") {
	    			$ledgerDataR[$rowmain->ledger_id] = $ledgerData[$rowmain->ledger_id];
	    			$SubheadArray['r'][$rowmain->ledger_id] = $rowmain->group_code;
	    		} else {
					$ledgerDataL[$rowmain->ledger_id] = $ledgerData[$rowmain->ledger_id];
					$SubheadArray['l'][$rowmain->ledger_id] = $rowmain->group_code;
	    		}	    		
	    	}
	    	if (!isset($SubheadArray['g'][$rowmain->group_code])) {	    		
	    		$SubheadArray['g'][$rowmain->group_code] = ['0'=>$rowmain->group_name, 'pos'=>$spos];
	    		$spos++;
	    	}
	    	list($bankData, $branchData) = $this->subBankBranchBl($rowmain, $bankData, $branchData);	    	
	    } 
	    // bank account details
	    if (!empty($bankData)) {
	    	$bankdet 	= $this->getIdNameAry(array_keys($bankData), 2); //bank accounts
	    	$banktotal 	= 0;
	    	//$bankdet = $this->getBankNames(array_keys($bankData));
	    	foreach ($bankData As $bkey => $bval) {
	    		$bankData[$bkey]->title = (isset($bankdet[$bkey])) ? $bankdet[$bkey] : 'Bank - ';
	    		$banktotal = $banktotal + $bankData[$bkey]->amount;
	    	}
	    	$bankData[0] = (object)['amount'=>$banktotal, 'title'=>'Bank Accounts', 'showtype'=>1];
	    }
	    // contra entry ledger details
	    if (!empty($otherledger)) {
	    	$ledgrdet = $this->getLedgerBase(array_keys($otherledger));
	    	foreach ($otherledger As $bkey => $bval) {

	    		$otherledger[$bkey]->title  = $ledgrdet[$bkey]->title;
	    		$otherledger[$bkey]->amount = ($ledgrdet[$bkey]->is_return == 1) ? (0-$otherledger[$bkey]->amount): $otherledger[$bkey]->amount;
	    		if ($ledgrdet[$bkey]->type == "Liability") {
	    			if (isset($ledgerDataR[$bkey])) {
	    				$ledgerDataR[$bkey]->amount += $otherledger[$bkey]->amount;
	    			} else {
						$ledgerDataR[$bkey] = $otherledger[$bkey];
					}
	    		} else {
	    			if (isset($ledgerDataL[$bkey])) {
	    				$ledgerDataL[$bkey]->amount += $otherledger[$bkey]->amount;
	    			} else {
						$ledgerDataL[$bkey] = $otherledger[$bkey];
					}
					//$ledgerDataL[$bkey] = $otherledger[$bkey];
	    		}	    		
	    	}
	    	unset($otherledger);
	    }
	    //Location/branch name details
	    if (!empty($branchData)) {
	    	$locdet = $this->getIdNameAry(array_keys($branchData), 3); //locations
	    	//$locdet = $this->getLocations(array_keys($branchData)); 
	    	$branchtotal = 0;
	    	foreach ($branchData As $bkey => $bval) {
	    		$branchData[$bkey]->title = (isset($locdet[$bkey])) ? $locdet[$bkey] : 'Branch - ';
	    		$branchtotal = $branchtotal + $branchData[$bkey]->amount;
	    	}
	    	$branchData[0] = (object)['amount'=>$branchtotal, 'title'=>'Cash in hand', 'showtype'=>1];
	    }
	    if ($capitaltotal > 0) {
	    	$capital[0] = (object)['amount'=>$capitaltotal, 'title'=>'Current Period (Captial)', 'showtype'=>1];
	    }
	    unset($ledgerData);
	    ksort($bankData);
	    ksort($branchData);
	    ksort($capital);
	    $leftDataNw = [];
	    // find the group head of asset
	    foreach ($ledgerDataL As $lid => $ldata) {
	    	if (isset($SubheadArray['l'][$lid])) {

	    		$pos 		= $SubheadArray['g'][$SubheadArray['l'][$lid]]['pos'];
	    		$groupnam 	= $SubheadArray['g'][$SubheadArray['l'][$lid]]['0'];
	    	} else {
	    		$pos 		= 500;
	    		$groupnam 	= "Common Group";
	    	}
	    	if (!isset($leftDataNw[$pos])) {
	    		$leftDataNw[$pos] = ['amount'=>0, 'title'=>$groupnam, 'showtype'=>1, 'list'=>[]];
	    	}
	    	$leftDataNw[$pos]['list'][] = $ldata;
	    	$leftDataNw[$pos]['amount'] += $ldata->amount;	    	
	    }
	    unset($ledgerDataL);
	    $rightDataNw = [];
	    // find the group head of liability
	    foreach ($ledgerDataR As $lid => $ldata) {
	    	if (isset($SubheadArray['r'][$lid])) {
	    		$pos 		= $SubheadArray['g'][$SubheadArray['r'][$lid]]['pos'];
	    		$groupnam 	= $SubheadArray['g'][$SubheadArray['r'][$lid]]['0'];
	    	} else {
	    		$pos 		= 500;
	    		$groupnam 	= "Common Group";
	    	}
	    	if (!isset($rightDataNw[$pos])) {
	    		$rightDataNw[$pos] = ['amount'=>0, 'title'=>$groupnam, 'showtype'=>1, 'list'=>[]];
	    	}
	    	$rightDataNw[$pos]['list'][] = $ldata;
	    	$rightDataNw[$pos]['amount'] += $ldata->amount;	    	
	    }
	    unset($ledgerDataR);
	    $this->data_list 	= ['bank'=>$bankData, 'branch'=>$branchData, 'asset'=>$leftDataNw, 'liability'=>$rightDataNw, 'capital'=>$capital];
	    // end the assets and liability sum amount based on the ledger.




        return $resData;
	}
	/**
	 * ledger, bank, branch deta fetching query creation based on the where filter provided 
	 * return sql and executed result set
	*/
	function periodicJournalEntry($where)
	{
		$tables 		= ' FROM acc_journal AS j '
			. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id) '
			. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id) '
			. ' LEFT JOIN acc_groups AS pgr ON (pgr.id = grp.parent_id) '
			. ' LEFT JOIN acc_ledger AS pldr ON (pldr.id = ledr.parent_id) ';
		//, IF (ledr.parent_id > 0, pldr.title, ledr.title) AS ledger_name
	    $sqlmain 		= 'SELECT j.ledger_id, SUM(j.amount) AS amount, grp.type, ledr.parent_id, ledr.title AS ledger_name, IF (grp.parent_id > 0, pgr.code, grp.code) As group_code, IF (grp.parent_id > 0, pgr.title, grp.title) As group_name, ledr.is_return, j.amt_type, j.ba_id, j.ba_id_debit, ledr.is_contra, ledr.less_id, ledr.plus_id, ledr.less_type, ledr.plus_type, j.location_id, j.branch_id, ledr.is_job_type '
	    . $tables.' '.$where
	    . ' GROUP BY j.ledger_id, j.ba_id, j.ba_id_debit, j.location_id, j.branch_id ORDER BY ledr.is_contra ASC, grp.type, j.ledger_id';
	    $resmain 		= mysqli_query($GLOBALS['con'],$sqlmain);

	    return ['0'=>$resmain, '1'=>$sqlmain];
	}
	/**
	 * Sub function for processing and storing the bank and branch entry details into the array provided from input
	 * Created By Bilin @ 02-01-2026
	*/
	function subBankBranchBl($rowmain, $bankData= [], $branchData= [])
	{

		if (($rowmain->ba_id > 0 || $rowmain->ba_id_debit > 0) && $rowmain->is_job_type == 0 ) { // bank related entry 
			if (!isset($bankData[$rowmain->ba_id])) {
    			$bankData[$rowmain->ba_id] = (object)['amount'=>0, 'title'=>'', 'showtype'=>2, 'ledger'=>$rowmain->ledger_id, 'code'=>$rowmain->group_code];
    			//$bankids[] = $rowmain->ba_id;
    		}
    		if (!isset($bankData[$rowmain->ba_id_debit])) {
    			$bankData[$rowmain->ba_id_debit] = (object)['amount'=>0, 'title'=>'', 'showtype'=>2, 'ledger'=>$rowmain->ledger_id, 'code'=>$rowmain->group_code];
    			//$bankids[] = $rowmain->ba_id_debit;
    		}
    		if ($rowmain->ba_id > 0 && $rowmain->ba_id_debit > 0) {
    			$bankData[$rowmain->ba_id]->amount 			+= $rowmain->amount;
    			$bankData[$rowmain->ba_id_debit]->amount 	+= (0-$rowmain->amount);
    		} else {
    			$baid = ($rowmain->ba_id > 0) ? $rowmain->ba_id: $rowmain->ba_id_debit;
	    		if ($rowmain->is_contra == 1 && $rowmain->plus_type == 2) {
	    			$bankData[$baid]->amount += $rowmain->amount;
	    		} else if ($rowmain->is_contra == 1 && $rowmain->less_type == 2) {
	    			$bankData[$baid]->amount += (0-$rowmain->amount);	    			
	    		} else if ($rowmain->type == "Liability" || $rowmain->type == "Income") {
	    			$bankData[$baid]->amount += ($rowmain->is_return == 0) ? (0-$rowmain->amount):$rowmain->amount;
	    		} else {
	    			$bankData[$baid]->amount += ($rowmain->is_return == 1) ? (0-$rowmain->amount):$rowmain->amount;
	    		}	
	    	}	    		
		} 
		if ((($rowmain->ba_id <= 0 && $rowmain->ba_id_debit <=0) || $rowmain->is_contra == 1)  && $rowmain->is_job_type == 0) { // cash transactions 
			if (!isset($branchData[$rowmain->location_id])) {
    			$branchData[$rowmain->location_id] = (object)['amount'=>0, 'title'=>'', 'showtype'=>2, 'ledger'=>$rowmain->ledger_id, 'code'=>$rowmain->group_code];
    			//$branchids[] = $rowmain->location_id;
    		} 
    		if (!isset($branchData[$rowmain->branch_id]) && $rowmain->branch_id != $rowmain->location_id) {
    			$branchData[$rowmain->branch_id] = (object)['amount'=>0, 'title'=>'', 'showtype'=>2, 'ledger'=>$rowmain->ledger_id, 'code'=>$rowmain->group_code];
    			//$branchids[] = $rowmain->branch_id;
    		}
    		if ($rowmain->is_contra == 1 && $rowmain->plus_type == 1) {
    			$branchData[$rowmain->location_id]->amount += $rowmain->amount;
    		} else if ($rowmain->is_contra == 1 && $rowmain->less_type == 1) {
    			$branchData[$rowmain->location_id]->amount += (0-$rowmain->amount);	    			
    		} else if ($rowmain->type == "Liability" || $rowmain->type == "Income") {
    			$branchData[$rowmain->location_id]->amount += ($rowmain->is_return == 0) ? (0-$rowmain->amount):$rowmain->amount;
    			if ($rowmain->branch_id != $rowmain->location_id)
    				$branchData[$rowmain->branch_id]->amount += ($rowmain->is_return == 1) ? (0-$rowmain->amount):$rowmain->amount;
    		} else {
    			$branchData[$rowmain->location_id]->amount += ($rowmain->is_return == 1) ? (0-$rowmain->amount):$rowmain->amount;
    			if ($rowmain->branch_id != $rowmain->location_id)
    				$branchData[$rowmain->branch_id]->amount += ($rowmain->is_return == 0) ? (0-$rowmain->amount):$rowmain->amount;
    		}
		}

		return ['0'=>$bankData, '1'=>$branchData];
	}
	/**
	 * Get all banks opening balance based on the company id provided
	 * Created By Bilin @ 02-01-2025
	*/
	function bankOpeningBal($inps)
	{
		extract($inps);
		$bankData	= [];
		$sql 		= 'SELECT BA_Id, BnkOB_OpenBal FROM bank_open_bals WHERE BnkOB_Status = 1 AND BnkOB_OpenBal > 0 ';
		if (isset($company_id) && $company_id > 0) {
			$sql 	.= ' AND OF_Id = '.$company_id;
		}
		if (isset($date) && $date != '') {
			$sql 	.= ' AND BnkOB_CDate <="'.$date.'"';
		}
		$sql 		.= ' ORDER BY BA_Id ASC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
		while ($row = mysqli_fetch_object($res)) {

			if (!isset($bankData[$row->BA_Id])) {
    			$bankData[$row->BA_Id] = (object)['amount'=>0, 'title'=>'', 'showtype'=>2, 'ledger'=>0, 'code'=>''];
    		}
    		$bankData[$row->BA_Id]->amount += $row->BnkOB_OpenBal;
		}
		return $bankData;
	}
	/**
	 * Get all branch or selected branch opening cash balance based on the company id provided
	 * Created By Bilin @ 02-01-2025
	*/
	function branchOpeningBal($inps)
	{
		extract($inps);
		$branchData	= [];
		$sql 		= 'SELECT LC_Id, OB_OpenBal FROM cash_open_bals WHERE OB_Status = 1 AND OB_OpenBal > 0 ';
		if (isset($company_id) && $company_id > 0) {
			$sql 	.= ' AND OF_Id = '.$company_id;
		}
		if (isset($branch_id) && $branch_id > 0) {
			$sql 	.= ' AND LC_Id = '.$branch_id;
		}
		if (isset($date) && $date != '') {
			$sql 	.= ' AND OB_Date <="'.$date.'"';
		}
		$sql 		.= ' ORDER BY LC_Id ASC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
		while ($row = mysqli_fetch_object($res)) {

			if (!isset($branchData[$row->LC_Id])) {
    			$branchData[$row->LC_Id] = (object)['amount'=>0, 'title'=>'', 'showtype'=>2, 'ledger'=>0, 'code'=>''];
    		}
    		$branchData[$row->LC_Id]->amount += $row->OB_OpenBal;
		}
		return $branchData;
	}
	/**
	 * list ledger name and group details based on the ledger id passed to the function
	 * created by bilin @ 23-12-2025
	*/
	function getLedgerBase($lids=[])
	{
		$retary = [];
		$sql 	= 'SELECT l.id, g.type, l.is_return, l.title, l.less_id, l.plus_id FROM acc_ledger AS l INNER JOIN acc_groups AS g ON (g.id = l.group_id) WHERE l.id IN  ('.implode(',',$lids).') ORDER BY l.id ASC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
		while ($row 	= mysqli_fetch_object($res)) {
			$retary[$row->id] = $row;
		}
		return $retary;
	}
	/**
	 * list all location name based on the location id array provided
	 * Created by Bilin @ 23-12-2025 
	
	function getLocations($locids=[])
	{
		$retary = [];
		$sql 	= 'SELECT LC_Id, LC_Name FROM locations WHERE LC_Id IN ('.implode(',', $locids).') ORDER BY LC_Id ASC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
		while ($row 	= mysqli_fetch_object($res)) {
			$retary[$row->LC_Id] = $row->LC_Name;
		}
		return $retary;
	}*/
	/**
	 * Cash Flow statement based on the date and company parameters send from the ui
	 * Created By Bilin @ 14-01-2026
	*/
	function listCashFlow($inputs) 
	{
		extract($inputs);
		$this->data_list 	= [];
		$this->sql_query 	= '';







		
		
	}
	/**
	 * Profit and Loss account in details (in this p and l we fetch the opening and calculate the close balance too)
	 * This statements based on the ceos suggestions and show the extra details uploaded by the account teams
	 * Created By Bilin @ 15-01-2026
	*/
	function listDetailProfitLoss($inputs) 
	{
		extract($inputs);
		$this->data_list= [];
		$this->sql_query= '';
		$branch_id 		= (isset($branch_id)) ? (int)$branch_id: 0;
		$company_id 	= (isset($company_id)) ? (int)$company_id: 0;
		$get_bal_tot 	= (isset($get_bal_tot)) ? (int)$get_bal_tot: 0;
		$groupcodes 	= ["purchase"=>"l0", "direct_expense"=>"l1", "sales"=>"r0", "direct_income"=>"r1", "indirect_expense"=>"l2", "indirect_income"=>"r2"];
		$resData 		= [];
		$ledger_ids 	= [];
		$where 			= ' WHERE j.status != 3 AND grp.type IN ("Income") AND grp.status != 3 AND ledr.status != 3 AND (grp.parent_id = 0 OR pgr.status != 3)';
		$whereExtra 	= ' WHERE ej.amount > 0 AND ej.type IN ("Income","Expense","IExpense")';
		if ( $company_id > 0 ) {
			$where  	.= ' AND j.company_id = "'.$company_id.'"';
			$whereExtra .= ' AND ej.company_id = "'.$company_id.'"';
		}
        switch($trans_type) {        	
        	case '1' : // external
        	$where 		.= ' AND j.branch_id = j.location_id';
        	if ($branch_id > 0) {
        		$where 	.= ' AND j.branch_id = "'.$branch_id.'"';
        	}
        	break;
        	case '2' : // internal
        	$where 		.= ' AND j.branch_id != j.location_id';        	
        	if ($branch_id > 0) {
        		$where 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
        	}
        	break;        	
        	default  : // both
        	if ($branch_id > 0) {
        		$where 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
        	}
        	break;
        }
        if ($branch_id > 0) {
        	$whereExtra .= ' AND ej.branch_id = "'.$branch_id.'"';
        }  
        if ($get_bal_tot == 1) {
        	$where1 	= $where.' AND j.date_entry >= "'.$strat_date.'"';
        	$where1 	.= ' AND j.date_entry <= "'.$from_date.'"';
        }
    	if ($from_date != "") {
        	$where 		.= ' AND j.date_entry >= "'.$from_date.'"';
        	$whereExtra .= ' AND ej.job_date >= "'.$from_date.'"';
        }
        if ($to_date != "") {
        	$where 		.= ' AND j.date_entry <= "'.$to_date.'"';
        	$whereExtra .= ' AND ej.job_date <= "'.$to_date.'"';
        }
          
        // Profile and loss main data fetching start
        $tables 	= ' FROM acc_journal AS j '
			. ' INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id) '
			. ' INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id) '
			. ' LEFT JOIN acc_groups AS pgr ON (pgr.id = grp.parent_id) '
			. ' LEFT JOIN acc_ledger AS pldr ON (pldr.id = ledr.parent_id) ';
		$sql 		= 'SELECT j.ledger_id, SUM(j.amount) AS ledger_amt, SUM(IF(j.converted > 0,j.converted,j.amount)) As ledger_convert, grp.type, ledr.is_return, IF (ledr.parent_id > 0, pldr.title, ledr.title) AS ledger_name, ledr.parent_id, ledr.group_id, IF (grp.parent_id > 0, pgr.code, grp.code) As group_code, IF (grp.parent_id > 0, pgr.title, grp.title) As group_name, j.company_id, j.ie_type, ledr.ie_type AS ietp_ledger'
        .' '.$tables
        .' '.$where
        .' GROUP BY j.ledger_id, j.company_id, j.ie_type '
        .' ORDER BY ledr.is_return ASC, j.ledger_id ASC';
        $this->sql_query= $sql;
		$res 		= mysqli_query($GLOBALS['con'],$sql);
		while ($row = mysqli_fetch_object($res)) {

			$group_code = trim($row->group_code);	
			$group_cid 	= (isset($groupcodes[$group_code])) ? $groupcodes[$group_code] : '0';
			// converted amount shows in p and l 13-01-2026
			if ( $company_id > 0 && $row->company_id == $company_id &&  $row->ledger_convert > 0 ) {
				$row->ledger_amt = $row->ledger_convert;
			}
			if ($group_cid == '0') {
				//profit and loss account only show the defined groups data no need other data	
			} else {
				if (!isset($resData[$group_cid])) {
					$resData[$group_cid] = ['total'=>0, 'title'=>$row->group_name, 'list'=>[]];
				} 
				$i 		= count($resData[$group_cid]['list']); // find the number of ledger present in the groups
				$ledid  = ($row->parent_id > 0) ? $row->parent_id : $row->ledger_id; // find the ledger id

				if ($row->ietp_ledger == $row->ie_type) { // 28-01-2026
					$amt 	= ($row->is_return == 1) ? (0-$row->ledger_amt):$row->ledger_amt; // check the amount was +ve / -ve
					$ledamt = $row->ledger_amt;
				} else {
					$amt 	= ($row->is_return != 1) ? (0-$row->ledger_amt):$row->ledger_amt; // check the amount was +ve / -ve
					$ledamt = (0-$row->ledger_amt);
				}
				// find the ledger id already in the list then update the amount
				if (isset($ledger_ids[$ledid])) { 
					$i 	= $ledger_ids[$ledid];
				} else { 
					// new then add into array
					$resData[$group_cid]['list'][$i] = ['title'=>($row->is_return == 1 ? "Less: ".$row->ledger_name:$row->ledger_name), 'amt'=>0];
				}
				$resData[$group_cid]['list'][$i]['amt'] += $ledamt;
				
				$resData[$group_cid]['total'] += $amt; // add the ledger amount into the group total
				$ledger_ids[$ledid] = $i;
			}
		}
		
         // Bank Opening balance - first time
	    /*$bankData 		= $this->bankOpeningBal(['company_id'=>$company_id, 'date'=>$from_date]);
	    // Branch Opening balance - first time
	    $branchData 	= $this->branchOpeningBal(['company_id'=>$company_id, 'date'=>$from_date, 'branch_id'=>$branch_id]);
	    if ($get_bal_tot == 1) { // find the old total of previous closing balance    
	    	list($resmain1, $sqlmain) = $this->periodicJournalEntry($where1);
	    	while ($rowmain = mysqli_fetch_object($resmain1)) {
	    		list($bankData, $branchData) = $this->subBankBranchBl($rowmain, $bankData, $branchData);
	    	}
	    }*/
	    // get the details based on the finiancial year and mapped items / ledger entry
	    //list($resmain, $sqlmain) = $this->periodicJournalEntry($where);	    
	    //$resData['sql'] = $sqlmain;



		/*

		
		
        $sql 			= 'SELECT j.ledger_id, SUM(j.amount) AS ledger_amt, SUM(IF(j.converted > 0,j.converted,j.amount)) As ledger_convert, grp.type, ledr.is_return, IF (ledr.parent_id > 0, pldr.title, ledr.title) AS ledger_name, ledr.parent_id, ledr.group_id, IF (grp.parent_id > 0, pgr.code, grp.code) As group_code, IF (grp.parent_id > 0, pgr.title, grp.title) As group_name, j.company_id'
        .' '.$tables
        .' '.$where
        .' GROUP BY j.ledger_id, j.company_id '
        .' ORDER BY ledr.is_return ASC, j.ledger_id ASC';
        $this->sql_query= $sql;
		$res 			= mysqli_query($GLOBALS['con'],$sql);
		while ($row = mysqli_fetch_object($res)) {
			$group_code = trim($row->group_code);	
			$group_cid = (isset($groupcodes[$group_code])) ? $groupcodes[$group_code] : '0';
			// converted amount shows in p and l 13-01-2026
			if ( $company_id > 0 && $row->company_id == $company_id &&  $row->ledger_convert > 0 ) {
				$row->ledger_amt = $row->ledger_convert;
			}
			
			if ($group_cid == '0') {
				//profit and loss account only show the defined groups data no need other data	
			} else {
				if (!isset($resData[$group_cid])) {
					$resData[$group_cid] = ['total'=>0, 'title'=>$row->group_name, 'list'=>[]];
				} 
				$i 		= count($resData[$group_cid]['list']); // find the number of ledger present in the groups
				$amt 	= ($row->is_return == 1) ? (0-$row->ledger_amt):$row->ledger_amt; // check the amount was +ve / -ve

				$ledid  = ($row->parent_id > 0) ? $row->parent_id : $row->ledger_id; // find the ledger id
				
				// find the ledger id already in the list then update the amount
				if (isset($ledger_ids[$ledid])) { 
					$i 	= $ledger_ids[$ledid];
					$resData[$group_cid]['list'][$i]['amt'] += $row->ledger_amt;
				} else { 
					// new then add into array
					$resData[$group_cid]['list'][$i] = ['title'=>($row->is_return == 1 ? "Less: ".$row->ledger_name:$row->ledger_name), 'amt'=>$row->ledger_amt];
				}
				
				$resData[$group_cid]['total'] += $amt; // add the ledger amount into the group total
				$ledger_ids[$ledid] = $i;
			}	
			$this->data_list 	= $resData;
		} */
		// extra details added and uploaded for detailed p and l
		$extPos 	 = ['Income'=>'r1', 'Expense'=>'l1', 'IExpense'=>'l2'];
		$headNames 	 = ['r1'=>'Job Income', 'l1'=>'Direct Expense', 'l2'=>'Office Running Expense'];
		$sqlexra 	 = 'SELECT ej.type, ej.ledger_id, IF (ej.ledger_id > 0, ledr.title , ej.title) AS show_title,  SUM(ej.amount) AS sum_amt  '
			.' FROM acc_extra_journal AS ej '
			.' LEFT JOIN acc_ledger AS ledr ON (ledr.id = ej.ledger_id) '
			.' '.$whereExtra
			.' GROUP BY ej.type, ej.title '
			.' ORDER BY ej.title ASC';
		$this->sql_query 	= $sqlexra;
		$resEx 		 = mysqli_query($GLOBALS['con'],$sqlexra);
		while ($exrw = mysqli_fetch_object($resEx)) {
			$typeky	 = trim($exrw->type);
			$typekid = (isset($extPos[$typeky])) ? $extPos[$typeky] : '0';
			$headnam = (isset($headNames[$typekid])) ? $headNames[$typekid] : '';
			if ($typekid == '0') {
				//profit and loss account only show the defined groups data no need other data	
			} else {
				if (!isset($resData[$typekid])) {
					$resData[$typekid] = ['total'=>0, 'title'=>$headnam, 'list'=>[]];
				}
				$i 		= count($resData[$typekid]['list']); 
				//list details uploaded   
				$resData[$typekid]['list'][$i] = ['title'=>$exrw->show_title, 'amt'=>$exrw->sum_amt];
				$resData[$typekid]['total'] += $exrw->sum_amt;
			}

		}
		$this->data_list 	= $resData; 
	}
	/**
	 * 
	 * 
	*/
	function listBankCashRpt($inputs) 
	{
		extract($inputs);
		$this->data_list	= [];
		$this->data_total 	= 0;
		$this->sql_query 	= '';
		$retTotals 			= ['total_income'=>0, 'total_expense'=> 0];
		$company_id 		= (isset($company_id)) ? $company_id :0;
		$branch_id 			= (isset($branch_id)) ? $branch_id :0;
		$bank_acc_id 		= (isset($bank_acc_id)) ? $bank_acc_id :0;
        $trans_type 		= (isset($cash_bank_type)) ? (int)$cash_bank_type:0;
        $limit				= (isset($limit) && $limit > 0) ? $limit : 0;
        $where 				= ' WHERE j.amount > 0 AND l.is_job_type=0  ';
		// AND grp.type IN ("Income","Expense")  //'Capital','Income','Expense','Asset','Liability'
		if ($company_id > 0) {
        	$where 	.= ' AND (j.company_id = "'.$company_id.'" OR j.office_id = "'.$company_id.'")';
        }
        if ($branch_id > 0) {
        	$where 	.= ' AND (j.branch_id = "'.$branch_id.'" OR j.location_id = "'.$branch_id.'")';
        }        
        if (isset($from_date) && $from_date != "") {
        	$where 	.= ' AND j.date_entry >= "'.$from_date.'"';
        }
        if (isset($to_date) && $to_date != "") {
        	$where 	.= ' AND j.date_entry <= "'.$to_date.'"';
        }
        if ($bank_acc_id > 0 ) { //&& $trans_type == "2"
        	$where 	.= ' AND (j.ba_id = "'.$bank_acc_id.'" OR j.ba_id_debit = "'.$bank_acc_id.'")';        	
        }
        if ($trans_type == "1") { // cash
        	//$where 	.= ' AND j.amt_type != 1';
        	$where 	.= ' AND (l.is_contra = 1 OR (j.ba_id <= 0 AND j.ba_id_debit <= 0 ))';
        	$idNameAry = $this->getIdNameAry([], 3, 0, $company_id);
        } else if ($trans_type == "2") { // bank
        	//$where 	.= ' AND j.amt_type != 2';
        	$where 	.= ' AND (j.ba_id > 0 OR j.ba_id_debit > 0 )';
        	$idNameAry = $this->getIdNameAry([], 2, 0, $company_id);
        }

        $tables 	= ' FROM acc_journal AS j'
        .' INNER JOIN acc_ledger AS l ON (l.id = j.ledger_id)'
        .' INNER JOIN acc_groups AS gp ON (gp.id = l.group_id)';
        //.' INNER JOIN acc_groups AS pgp ON (pgp.id = gp.parent_id)';
        if ($limit > 0) {
	        // find the count from the query
	        $sql_count 			= 'SELECT COUNT(DISTINCT(j.id)) '.$tables.' '.$where;
	        $res_count 			= mysqli_query($GLOBALS['con'], $sql_count);
			$row_count			= mysqli_fetch_array($res_count,MYSQLI_NUM);
	        $this->data_total 	= $row_count[0];
	        $this->sql_query 	= $sql_count;
	    } else {
	    	$this->data_total 	= 1;
	    }

        // get the list of data 
        if ($this->data_total > 0) {
        	$slno 		= 0;        
	        $sql 		= ' SELECT j.id, j.ledger_id, j.amount, j.converted, gp.code, gp.type, l.title, l.is_return, j.amt_type, j.ba_id, j.ba_id_debit, l.is_contra, l.less_id, l.plus_id, l.less_type, l.plus_type, j.location_id, j.branch_id, j.office_id, j.company_id, j.remarks, j.trackno, j.date_entry, j.ie_type, l.ie_type as lie_type, l.is_same_side '
	        	. $tables.' '.$where
	        	.' ORDER BY j.date_entry ASC, j.id ASC';
	        if ($limit > 0) {
	        	$sql 	.= ' LIMIT '.$start.','.$limit;
	        	$slno 	= $start;
	        }	
	        $this->sql_query 	= $sql;
	        $res 		= mysqli_query($GLOBALS['con'],$sql);	
	        while ($row = mysqli_fetch_object($res)) {
	        	// company is different so take the converted amount
				if ( $company_id > 0 && $row->company_id == $company_id &&  $row->converted > 0 ) {
					$row->amount = $row->converted;
				}
				
				$slno++;
				if ($trans_type == "1") { // cash

					list($income, $expense, $first_id, $second_id) =  $this->bankcashrptSub($row, $trans_type, $branch_id, 'branch_id', 'location_id');
				} else if ($trans_type == "2") { // bank
					list($income, $expense, $first_id, $second_id) =  $this->bankcashrptSub($row, $trans_type, $bank_acc_id, 'ba_id', 'ba_id_debit');					
				}	
		    	$subtitle   = '';			
		    	// branch name or bank name to display
		    	if ($first_id > 0 || $second_id > 0) {
		    		$subtitle 	.= ' - (';
		    		$subtitle 	.= (isset($idNameAry[$first_id])) ? $idNameAry[$first_id] :'';
		    		$subtitle 	.= ($first_id > 0 && $second_id > 0) ? ' - ':'';
		    		$subtitle 	.= (isset($idNameAry[$second_id])) ? $idNameAry[$second_id] :'';
		    		$subtitle 	.= ') ';
		    	}

		    	$row->title    .= $subtitle;
				$row->income 	= $income;
				$row->expense 	= $expense;
				$row->slno 		= $slno;
				if (isset($isexcel) && $isexcel == 1) {
					$this->data_list[] 	= ['slno'=>$slno, 'title'=>$row->title, 'trackno'=>$row->trackno, 'date_entry'=>date('d/m/Y', strtotime($row->date_entry)), 'income'=>$income, 'expense'=>$expense, 'remarks'=>$row->remarks];
				} else {
					$this->data_list[]	= $row;
				}				
				// excel export time no need extra query fetching
				$retTotals['total_income'] 	+= $income;	
				$retTotals['total_expense'] += $expense;
			}
			// total income and expense getting.....
			if ($limit > 0) {					
				$retTotals 			= ['total_income'=>0, 'total_expense'=> 0];
				$sql 	= ' SELECT j.ledger_id, SUM(j.amount) AS tot_amt, SUM(j.converted) AS convert_amt, gp.code, gp.type, l.is_return, j.ba_id, j.ba_id_debit, l.is_contra, l.less_id, l.plus_id, l.less_type, l.plus_type, j.location_id, j.branch_id, j.office_id, j.company_id,j.ie_type, l.ie_type as lie_type, l.is_same_side '. $tables.' '.$where;
		        $sql 	.= ($trans_type == "1") ? ' GROUP BY j.ledger_id, j.company_id, j.office_id, j.location_id, j.branch_id, j.ie_type ORDER BY l.is_contra ASC, gp.type, j.ledger_id' : 'GROUP BY j.ledger_id, j.company_id, j.office_id, j.ba_id, j.ba_id_debit, j.ie_type ORDER BY l.is_contra ASC, gp.type, j.ledger_id';
		        //$this->sql_query 	= $sql;
		        $res 		= mysqli_query($GLOBALS['con'],$sql);	
		        while ($row = mysqli_fetch_object($res)) {
		        	// company is different so take the converted amount
					if ( $company_id > 0 && $row->company_id == $company_id &&  $row->converted > 0 ) {
						$row->amount = $row->convert_amt;
					} else {
						$row->amount = $row->tot_amt;
					}
					if ($trans_type == "1") { // cash

						list($income, $expense, $first_id, $second_id) =  $this->bankcashrptSub($row, $trans_type, $branch_id, 'branch_id', 'location_id', $company_id);
					} else if ($trans_type == "2") { // bank

						list($income, $expense, $first_id, $second_id) =  $this->bankcashrptSub($row, $trans_type, $bank_acc_id, 'ba_id', 'ba_id_debit', $company_id);					
					}
					$retTotals['total_income'] 	+= $income;	
					$retTotals['total_expense'] += $expense;	
		        }
		    } else {
		    	$this->data_total = $slno;
		    } 
        }

		return $retTotals;
	}
	function bankcashrptSub($row, $type, $bbid=0, $field1='', $field2='', $company_id=0)
	{			
		$income 	= 0;
		$expense 	= 0;
		if ($bbid > 0) {

			if ($row->is_contra == 1 && $row->plus_type == $type && $row->$field1 == $bbid) {
    			$income 	= $row->amount;
    		} else if ($row->is_contra == 1 && $row->less_type == $type && $row->$field2 == $bbid) {
    			$expense 	= $row->amount;	    			
    		}else if ($row->$field1 > 0 && $row->$field2 > 0 && $row->$field2 != $bbid) {
    			$income 	= $row->amount;
    		}else if ($row->$field1 > 0 && $row->$field2 > 0 && $row->$field1 != $bbid) {
    			$expense 	= $row->amount;	    	
    		}
		} else {

			if ($row->is_contra == 1 && $row->plus_type == $type && $row->$field1 > 0) {
    			$income 	= $row->amount;
    		} else if ($row->is_contra == 1 && $row->less_type == $type && $row->$field2 > 0) {
    			$expense 	= $row->amount;
    		} else if ($row->is_contra == 1 && $row->plus_type != $type && $row->$field1 > 0) {
    			$expense 	= $row->amount;
    		}						
		}
		$first_id  	= ($row->$field1 > 0) ? $row->$field1 : 0;
		$second_id 	= ($row->$field2 > 0 && $row->$field2 != $row->$field1) ? $row->$field2 : 0;
		if ($income == 0 && $expense == 0) {
			// common function 
			if ($row->type == "Asset" || $row->type == "Income" || $row->code == "sundry_creditors") {
    			$income 	= ($row->is_return == 1) ? (0-$row->amount):$row->amount;    			
    		} else {
    			if ($row->is_same_side == 1 || $row->office_id == $company_id) {
    				$expense 	= ($row->is_return == 1) ? (0-$row->amount):$row->amount;
    			} else {
    				$income 	= ($row->is_return == 1) ? (0-$row->amount):$row->amount;
    			}    					
    		}
    	}
    	if ($row->ie_type != $row->lie_type) { // swap the income and expense
    		$inecp = $income;
    		$income = $expense;
    		$expense = $inecp;
    	}


    	return ['0'=>$income, '1'=>$expense, '2'=>$first_id, '3'=>$second_id];
	}





























	// check the sub table data before update the status
	function checkSubItemStauts($id, $type =1) 
	{		
		$where  	= ' WHERE status != 3 AND status != 0 ';
		$table  	= '';
		if ($type == 1) { // check the sub group
			$table  = ' acc_groups ';
			$where 	.= ' AND parent_id = '.$id;
		} else if ($type == 2) {
			$table  = ' acc_ledger ';
			$where 	.= ' AND group_id = '.$id;
		} else if ($type == 3) {
			$table  = ' acc_ledger ';
			$where 	.= ' AND parent_id = '.$id;
		} else if ($type == 4) {
			$table  = ' acc_map_ledger ';
			$where 	.= ' AND ledger_id = '.$id;
		}
		$sql 		= 'SELECT COUNT(id) FROM '.$table.$where;
		$res 		= mysqli_query($GLOBALS['con'], $sql);
	   	$row 		= mysqli_fetch_row($res);

	   	return (!empty($row) && $row[0] > 0) ? 1 : 0;
	}
	// check parent active status check
	function checkParentStatus($id, $type = 1)
	{	
		$where  	= ' WHERE status = 1 ';
		if ($type == 1) { // check the sub group
			$table  = ' acc_groups ';
			$where 	.= ' AND id = '.$id;
		} else if ($type == 2) {
			$table  = ' acc_ledger ';
			$where 	.= ' AND id = '.$id;
		} else if ($type == 3) {
			$table  = ' acc_map_ledger ';
			$where 	.= ' AND id = '.$id;
		}
		$sql 		= 'SELECT COUNT(id) FROM '.$table.$where;
		$res 		= mysqli_query($GLOBALS['con'], $sql);
	   	$row 		= mysqli_fetch_row($res);

	   	return (!empty($row) && $row[0] > 0) ? 0 : 1;
	}

	// backup tables 
	function backupTables($id= 0, $type=1) 
	{
		$bkupsql    	= "INSERT INTO ";
		if ($type == 1) {
			$bkupsql    .= "acc_back_groups (id, parent_id, type, title, description, status, created_by, verified_by, created_at, verified_at) SELECT id, parent_id, type, title, description, status, created_by, verified_by, created_at, verified_at FROM acc_groups";
		} else if ($type == 2) {
			$bkupsql    .= "acc_back_ledger (id, group_id, ie_type, parent_id, office_id, title, description, open_balance, open_bal_type, status, vendor_id, trans_type, is_return, is_contra, less_id, less_type, plus_id, plus_type, is_same_side, is_internal,is_job_type, created_by, verified_by, created_at, verified_at) SELECT id, group_id, ie_type, parent_id, office_id, title, description, open_balance, open_bal_type, status, vendor_id, trans_type, is_return, is_contra, less_id, less_type, plus_id, plus_type, is_same_side, is_internal,is_job_type, created_by, verified_by, created_at, verified_at FROM acc_ledger";
		} else if ($type == 3) {
			$bkupsql    .= "acc_back_map_ledger (id, ledger_id, item_id, office_id, status, trans_type, created_by, verified_by, created_at, verified_at) SELECT id, ledger_id, item_id, office_id, status, trans_type, created_by, verified_by, created_at, verified_at FROM acc_map_ledger";
		} else if ($type == 5) {
			$bkupsql 	.= " acc_back_vendors SELECT NULL, acc_vendors.* FROM acc_vendors ";
		} else if ($type == 7) {
			$bkupsql 	.= " acc_back_bills SELECT NULL, acc_bills.* FROM acc_bills ";
		} else if ($type == 9) {
			$bkupsql 	.= " acc_back_journal_entry SELECT NULL, acc_journal_entry.* FROM acc_journal_entry ";
		} else if ($type == 10) {
			$bkupsql 	.= " acc_back_opening_balance SELECT NULL, acc_opening_balance.* FROM acc_opening_balance ";
		} else if ($type == 11) {
			$bkupsql 	.= " acc_back_extra_journal SELECT NULL, acc_extra_journal.* FROM acc_extra_journal ";
		}
		   
		$bkupsql    	.= " WHERE id = ".$id;
		
	   	mysqli_query($GLOBALS['con'], $bkupsql);
	}
	// common sql return function based on the input fields and tables
	function getCustomField($table, $fields, $condition) 
	{
		$retary 	= [];
		$res 		= mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM ".$table."  ".$condition);
		while($row 	= mysqli_fetch_object($res)) {
			$retary[]	=$row;
		}
		return $retary;
	}
	// common sql to return the all data row based on the input id
	function getSRowData($id = 0, $type=0)
	{
		switch ($type) {
			case 5 	: $table = 'acc_vendors';
			break;
			case 6 	: $table = 'acc_tds_rules';
			break;
			case 7 	: $table = 'acc_bills';
			break;
			default : $table = 'acc_groups';
			break;
		}
		$sql 	= 'SELECT * FROM '.$table.' WHERE id = "'.$id.'" ORDER BY id DESC';
		$res 	= mysqli_query($GLOBALS['con'], $sql);
	   	$row 	= mysqli_fetch_array($res, MYSQLI_ASSOC);

	   	return (!empty($row)) ? $row : [];
	}	
	// common status updation 
	function updateStatusCustom($status, $id, $user_id, $type=0) 
	{
		if ($type != 8) {
			// back up entry
	   		$this->backupTables($id, $type);
		}		
		switch ($type) {
			case 5 	: $table = "acc_vendors";
			break;
			case 6 	: $table = "acc_tds_rules";
			break;
			case 8 	: $table = "acc_recurring_bills";
			break;
			default : $table = "acc_groups";
			break;
		}
		$sql = "UPDATE ".$table." SET status='".$status."', updated_by='".$user_id."', updated_at='".date('Y-m-d H:i:s')."' WHERE id=".$id;
		return mysqli_query($GLOBALS['con'],$sql);
	}

	// common Sql functoin for getting id, name array (id as kery and name as values)
	// Created BY Bilin @ 18-11-2025
	function getIdNameAry($ids = [], $type=0, $id=0, $offid=0)
	{
		$ids 		= (!empty($ids)) ? array_unique($ids) :[];		
		$retary 	= [];
		if ( !empty ($ids) || ($type > 0 && ($offid > 0 || $id > 0)) ) {
			switch ($type) {
				case 1 	: $table 	= "acc_vendors";
						  $id 		= "id";
						  $title 	= "name";
						  $condition= " WHERE id IN (".implode(',',$ids).")";
				break;
				case 2 	: $table 	= "bank_accounts";					  
						  $id 		= "BA_Id";
						  $title 	= "BA_DispName";
						  $condition= (!empty($ids)) ? " WHERE BA_Id IN (".implode(',',$ids).")": " WHERE 1";
				break;
				case 3 	: $table 	= "locations";
						  $id 		= "LC_Id";
						  $title 	= "LC_Name";
						  $condition=  (!empty($ids)) ? " WHERE LC_Id IN (".implode(',',$ids).")" : " WHERE LC_Status != 5";
						  $condition.= (!empty($offid)) ? " AND OF_Id =".$offid : ""; 
						  $condition.= (!empty($id)) ? " AND LC_Id =".$id : ""; 
				break;
				case 4 	: $table 	= "offices";
						  $id 		= "OF_Id";
						  $title 	= "OF_Name";
						  $condition=  (!empty($ids)) ? " WHERE OF_Id IN (".implode(',',$ids).")": " WHERE 1";
						  $condition.= (!empty($offid)) ? " AND OF_Id =".$offid : ""; 
				break;
				case 5 	: $table 	= "acc_ledger";
						  $id 		= "id";
						  $title 	= "trans_type";
						  $condition= " WHERE id IN (".implode(',',$ids).")";
				break;
				default : $table 	= "";
						  $id 		= "";
						  $title 	= "";						  
						  $condition= "";
				break;
			}
			$res 		= mysqli_query($GLOBALS['con'],"SELECT ".$id.",".$title." FROM ".$table."  ".$condition);
			while($row 	= mysqli_fetch_object($res)) {
				$retary[$row->$id]	= $row->$title;
			}
		}
		
		return $retary;
	}





}
?>