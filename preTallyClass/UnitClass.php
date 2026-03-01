<?php
require_once("connection.php");

class UnitClass
{
	var $UnitArray;
	
	
	//----------------------------------------- All Units ----------------------------------------//
	function viewUnits($filt='')
	{			
		$count=0;
		$this->UnitArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT * FROM units ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->UnitArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify Unit ----------------------------------------//
	function verifyUnit($UTId){
		$sql = 'SELECT COUNT(UT_Name) FROM units WHERE UT_Name = "'.$this->Unit_Data['UT_Name'].'" AND UT_Id != '.$UTId; 
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New Unit ----------------------------------------//
	function newUnit(){
		
		$sql = "INSERT INTO units ( " . implode(', ',array_keys($this->Unit_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Unit_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
		return 'Unit Created Successfully';

	}
	//----------------------------------------- Update Unit ----------------------------------------//
	function updateUnit($SHId){
		$UnitData = '';
		foreach ($this->Unit_Data as $key=>$value){ 
			$UnitData = $UnitData .$key ."='".$value."', ";
		}
		$UnitData = substr($UnitData, 0, -2);
		$sql = "UPDATE units SET $UnitData WHERE UT_Id=$SHId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Unit Updated Successfully';
	}
	
}

?>