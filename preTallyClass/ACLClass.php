<?php
require_once("connection.php");

class ACLClass {
	var $ACLArray;
	//----------------------------------------- All ACLs ----------------------------------------//
	function viewACL($filt='') {			
            $count=0; 
            $this->ACLArray = array();             
            $result=mysqli_query($GLOBALS['con'],"SELECT * FROM acl as ACL,offices as OF1 ".$filt);
            while($row=mysqli_fetch_object($result)) {
                $this->ACLArray[$count]=$row;
                $count++;
            }	
	}
	//----------------------------------------- New ACL -------------------------------------------//
	function newACL()
        {		
		$sql = "INSERT INTO acl ( " . implode(', ',array_keys($this->ACL_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->ACL_Data)) . "'" . ")";		
		mysqli_query($GLOBALS['con'],$sql); 
                if(mysqli_affected_rows($GLOBALS['con'])>0)
                {
                        $UTId=mysqli_insert_id($GLOBALS['con']);
                        return $UTId;
                }
	}
	//----------------------------------------- Update ACL ----------------------------------------//
	function updateACL($ACLId){
		$ACLData = '';
		foreach ($this->ACL_Data as $key=>$value){ 
                        if($value===-1) continue;   
			else $ACLData = $ACLData .$key ."='".$value."', ";
		}
		$ACLData = substr($ACLData, 0, -2);
		$sql = "UPDATE acl SET $ACLData WHERE ACL_Id=$ACLId";                
		mysqli_query($GLOBALS['con'],$sql);
                return 'ACL Updated Successfully';
	}
	//----------------------------------------- Verify Office ----------------------------------------//
	function verifyACL($ACLId,$ofid){
            $sql = 'SELECT COUNT(ACL_Name) FROM acl WHERE ACL_Name = "'.$this->ACL_Data['ACL_Name'].'" AND ACL_Id != '.$ACLId.' AND OF_Id='.$ofid;
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	/**
	* List users based acls assigned
	*/
	function listUsersAcls($inparms=[])
	{
		extract($inparms);
		$this->data_total 	= 0;
		$this->data_list 	= [];
		$where 		= ' WHERE us.US_Status = "1" ';
		$tables 	= ' FROM `user_acl` AS ual'
					. ' INNER JOIN `users_auth` AS us ON (us.US_Id = ual.us_id)'
					. ' LEFT JOIN `locations` AS lc ON (lc.LC_Id = us.LC_Id)';		
		$fields 	= '';
		$joins		= '';			
		if (isset($off_id) && $off_id > 0) {
			$where 		.= ' AND us.OF_Id = "'.$off_id.'"';
			$fields 	.= ', concat(us.US_FName, " ", us.US_LName, ", ",lc.LC_Name) AS name';
		} else {
			$fields 	.= ', concat(us.US_FName, " ", us.US_LName, ", ",lc.LC_Name, ", ",of1.OF_Name) AS name';
			$joins 		.= ' LEFT JOIN offices AS of1 On (of1.OF_Id=us.OF_Id)';
		}
		if (isset($search) && $search != '') {
			$where 		.= ' AND (concat(us.US_FName, " ", us.US_LName)  like "%'.$search.'%" OR lc.LC_Name like "%'.$search.'%")';
		}
		// find the count from the query
		$sql_count 		= 'SELECT COUNT(DISTINCT ual.id) '.$tables.$joins.' '.$where;
		$res_count 		= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count		= mysqli_fetch_array($res_count,MYSQLI_NUM);
        $this->data_total = $row_count[0];
        if ($this->data_total > 0) {

        	$slno 		= 0;
        	$sql 		= 'SELECT ual.* '.$fields
        	.' '.$tables.$joins
        	.' '.$where
        	.' GROUP BY ual.us_id'
        	.' ORDER BY us.US_FName ASC, lc.LC_Name ASC';
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
                $slno++;
                $row->slno 			= $slno;
                $this->data_list[] 	= $row;
            }
        }
	}
	/**
	*  Assign or update the user based acls
	*/
	function saveUserACL($id=0, $inputs=[])
	{	
		if ($id > 0) {
			$ACLData = '';
			foreach ($inputs as $key=>$value){ 
	            if($value===-1) { continue;   }
				else {$ACLData = $ACLData .$key ."='".$value."', ";}
			}
			$ACLData = substr($ACLData, 0, -2);
			$sql = "UPDATE user_acl SET $ACLData WHERE id=$id";
		} else {
			$sql = "INSERT INTO user_acl ( " . implode(', ',array_keys($inputs)) . ") VALUES (" . "'" . implode("','", array_values($inputs)) . "'" . ")";
		}			
		mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_affected_rows($GLOBALS['con'])>0)
        {
            return ($id > 0) ? $id : mysqli_insert_id($GLOBALS['con']);
        }

        return 0;
	}
	function checkUserAcl($user_id=0) 
	{
		$sql 	= 'SELECT id FROM user_acl WHERE us_id = "'.$user_id.'"';
		$result = mysqli_query($GLOBALS['con'],$sql);                     
	    $row 	= mysqli_fetch_array($result,MYSQLI_NUM);
	    return (isset($row[0])) ? $row[0]:0;
	}
	
}

?>