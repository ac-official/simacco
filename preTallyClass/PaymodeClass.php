<?php
require_once("connection.php");

class PaymodeClass
{
	var $PaymodeArray;
	
	
	//----------------------------------------- All Paymodes ----------------------------------------//
	function viewPaymodes($filt='')
	{			
		$count=0;
		$this->PaymodeArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT * FROM payment_modes ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->PaymodeArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify Paymode ----------------------------------------//
	function verifyPaymode($MId){
		$sql = 'SELECT COUNT(PM_Name) FROM payment_modes WHERE PM_Name = "'.$this->Paymode_Data['PM_Name'].'" AND PM_Id != '.$MId;
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New Paymode ----------------------------------------//
	function newPaymode(){
		
		$sql = "INSERT INTO payment_modes ( " . implode(', ',array_keys($this->Paymode_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Paymode_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
		return 'Paymode Created Successfully';

	}
	//----------------------------------------- Update Paymode ----------------------------------------//
	function updatePaymode($MId){
		$PaymodeData = '';
		foreach ($this->Paymode_Data as $key=>$value){ 
			$PaymodeData = $PaymodeData .$key ."='".$value."', ";
		}
		$PaymodeData = substr($PaymodeData, 0, -2);
		$sql = "UPDATE payment_modes SET $PaymodeData WHERE PM_Id=$MId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Paymode Updated Successfully';
	}
	
}

?>