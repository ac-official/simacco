<?php
require_once("connection.php");

class SubheadClass
{
	var $SubheadArray;
	
	
	//----------------------------------------- All Subheads ----------------------------------------//
	function viewSubheads($filt='')
	{			
		$count=0;
		$this->SubheadArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT * FROM sub_heads ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->SubheadArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify Subhead ----------------------------------------//
	function verifySubhead($SHId){
		$sql = 'SELECT COUNT(SH_Name) FROM sub_heads WHERE SH_Name = "'.$this->SH_Data['SH_Name'].'" AND SH_Id != '.$SHId;
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New Subhead ----------------------------------------//
	function newSubhead(){
		
		$sql = "INSERT INTO sub_heads ( " . implode(', ',array_keys($this->SH_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->SH_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
		return 'Subhead Created Successfully';

	}
	//----------------------------------------- Update Subhead ----------------------------------------//
	function updateSubhead($SHId){
		$SHData = '';
		foreach ($this->SH_Data as $key=>$value){ 
			$SHData = $SHData .$key ."='".$value."', ";
		}
		$SHData = substr($SHData, 0, -2);
		$sql = "UPDATE sub_heads SET $SHData WHERE SH_Id=$SHId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Subhead Updated Successfully';
	}
        //----------------------------------------- Subheads based on Main Head ----------------------------------------//
	function getSubHeads($fields='*',$tbls='',$filt='')
	{			
		$count=0;
		$this->SubheadArray = array();
                //return "SELECT IT_Id, IT_Name FROM items ".$filt;
		$result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM sub_heads ".$tbls."  ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->SubheadArray[$count]=$row;
			$count++;
		}	
	}
	
}

?>