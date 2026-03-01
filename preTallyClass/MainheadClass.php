<?php
require_once("connection.php");

class MainheadClass
{
	var $MainheadArray;
	
	//----------------------------------------- All Mainheads -----------------------------------------//

	function viewMainheads($filt='')
	{			
		$count=0;
		$this->MainheadArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT * FROM main_heads ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->MainheadArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify Mainhead ----------------------------------------//
	function verifyMainhead($MHId){
		$sql = 'SELECT COUNT(MH_Name) FROM main_heads WHERE MH_Name = "'.$this->MH_Data['MH_Name'].'" AND MH_Id != '.$MHId;
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New Mainhead ----------------------------------------//
	function newMainhead(){
		
		$sql = "INSERT INTO main_heads ( " . implode(', ',array_keys($this->MH_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->MH_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
		return 'Mainhead Created Successfully';

	}
	//----------------------------------------- Update Mainhead ----------------------------------------//
	function updateMainhead($MHId){
		$MHData = '';
		foreach ($this->MH_Data as $key=>$value){ 
			$MHData = $MHData .$key ."='".$value."', ";
		}
		$MHData = substr($MHData, 0, -2);
		$sql = "UPDATE main_heads SET $MHData WHERE MH_Id=$MHId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Mainhead Updated Successfully';
	}
	//-----------------------------------------  Mainhead Id ----------------------------------------//
	function getMainHeads($fields='*',$tbls='',$filt='')
	{			
		$count=0;
		$this->MainheadArray = array();
                //return "SELECT IT_Id, IT_Name FROM items ".$filt;
		$result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM main_heads ".$tbls."  ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->MainheadArray[$count]=$row;
			$count++;
		}	
	}
}

?>