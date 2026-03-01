<?php
require_once("connection.php");

class DepartmentClass
{
	var $DepartmentArray;
	
	
	//----------------------------------------- All Departments ----------------------------------------//
	function viewDepartments($fields='*',$tbls='',$filt='')
	{			
		$count=0; 
		$this->DepartmentArray = array();
                
		$result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM departments ".$tbls."  ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->DepartmentArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify Department ----------------------------------------//
	function verifyDepartment($DPId,$ofid){
		$sql = 'SELECT COUNT(DP_Name) FROM departments WHERE DP_Name = "'.$this->DP_Data['DP_Name'].'" AND DP_Id != '.$DPId.' AND OF_Id='.$ofid;
                $result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New Department ----------------------------------------//
	function newDepartment(){
		
		$sql = "INSERT INTO departments ( " . implode(', ',array_keys($this->DP_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->DP_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
                if(mysqli_affected_rows($GLOBALS['con'])>0)
                {
                        $DPId = mysqli_insert_id($GLOBALS['con']);
                        return $DPId;
                }
	}
	//----------------------------------------- Update Department ----------------------------------------//
	function updateDepartment($DPId){
		$DPData = '';
		foreach ($this->DP_Data as $key=>$value){ 
			$DPData = $DPData .$key ."='".$value."', ";
		}
		$DPData = substr($DPData, 0, -2);
		$sql = "UPDATE departments SET $DPData WHERE DP_Id=$DPId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Department Updated Successfully';
	}
	
}

?>
