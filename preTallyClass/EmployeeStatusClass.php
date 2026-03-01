<?php
require_once("connection.php");
class EmployeeStatusClass{
    var $EmployeeStatusArray;
    function newEmployeeStatus(){
        $sql = "INSERT INTO employee_status ( " . implode(', ',array_keys($this->ES_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->ES_Data)) . "'" . ")";
		//return $sql;
	mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_affected_rows($GLOBALS['con'])>0)
        {
	$ESId = mysqli_insert_id($GLOBALS['con']);
        return $ESId;
        }
    }
    function viewEmployeeStatus($fields='*', $filter=''){
        $count=0; 
		$this->EmployeeStatusArray = array();
                //return "SELECT ".$fields." FROM employee_status ".$filter;
		$result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM employee_status ".$filter);
		while($row=mysqli_fetch_object($result)) {
			$this->EmployeeStatusArray[$count]=$row;
			$count++;
		}
    }  
        
}