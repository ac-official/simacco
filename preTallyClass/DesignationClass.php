<?php
require_once("connection.php");

class DesignationClass
{
	var $DesignationArray;
        var $DesignationMapArray;
	
	
	//----------------------------------------- All Designations ----------------------------------------//
	function viewDesignations($fields='*', $tbls='', $filt='')
	{			
		$count=0; 
		$this->DesignationArray = array();
                //return "SELECT ".$fields." FROM designations ".$tbls."  ".$filt;
		$result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM designations ".$tbls."  ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->DesignationArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify Designation ----------------------------------------//
	function verifyDesignation($DGId, $OFID){
		$sql = 'SELECT COUNT(DG_Name) FROM designations WHERE DG_Name = "'.$this->DG_Data['DG_Name'].'" AND DG_Id != '.$DGId.' AND OF_Id ='.$OFID;
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New Designation ----------------------------------------//
	function newDesignation(){
		
		$sql = "INSERT INTO designations ( " . implode(', ',array_keys($this->DG_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->DG_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
                if(mysqli_affected_rows($GLOBALS['con'])>0)
                {
		$DGId = mysqli_insert_id($GLOBALS['con']);
                return $DGId;
                }

	}
	//----------------------------------------- Update Designation ----------------------------------------//
	function updateDesignation($DGId){
		$DGData = '';
		foreach ($this->DG_Data as $key=>$value){ 
			$DGData = $DGData .$key ."='".$value."', ";
		}
		$DGData = substr($DGData, 0, -2);
		$sql = "UPDATE designations SET $DGData WHERE DG_Id=$DGId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Designation Updated Successfully';
	}
	function myMapItem($OFId){
            $count=0;
            $this->DesignationMapArray = array();
            $sql = 'SELECT DC_Map FROM designations_company WHERE OF_Id = '.$OFId;
            $result = mysqli_query($GLOBALS['con'],$sql);
            while($row=mysqli_fetch_object($result)) {
                $this->DesignationMapArray[$count] = $row;
                $count++;
            }
	}
}

?>