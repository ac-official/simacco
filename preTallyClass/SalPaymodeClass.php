<?php
require_once("connection.php");

class SalPaymodeClass
{
	var $SalPaymodeArray;
	
	
	//----------------------------------------- All SalPaymodes ----------------------------------------//
	function viewSalPaymodes($filt='')
	{			
		$count=0;
		$this->SalPaymodeArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT * FROM salary_paymodes ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->SalPaymodeArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify SalPaymode ----------------------------------------//
	function verifySalPaymode($MId){
		$sql = 'SELECT COUNT(SP_Name) FROM salary_paymodes WHERE SP_Name = "'.$this->SalPaymode_Data['SP_Name'].'" AND SP_Id != '.$MId;
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New SalPaymode ----------------------------------------//
	function newSalPaymode(){
		
		$sql = "INSERT INTO salary_paymodes ( " . implode(', ',array_keys($this->SalPaymode_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->SalPaymode_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
		return 'Salary Paymode Created Successfully';

	}
	//----------------------------------------- Update SalPaymode ----------------------------------------//
	function updateSalPaymode($MId){
		$SalPaymodeData = '';
		foreach ($this->SalPaymode_Data as $key=>$value){ 
			$SalPaymodeData = $SalPaymodeData .$key ."='".$value."', ";
		}
		$SalPaymodeData = substr($SalPaymodeData, 0, -2);
		$sql = "UPDATE salary_paymodes SET $SalPaymodeData WHERE SP_Id=$MId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Salary Paymode Updated Successfully';
	}
	
}

?>