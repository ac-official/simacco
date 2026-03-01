<?php
require_once("connection.php");

class StateClass {
    var $StateArray;
    
    //--------------------------------------------------- All States ----------------------------------------------//
    
    //--------------------------------------------------- New State -----------------------------------------------//
    function newState(){
        $sql = "INSERT INTO states ( " . implode(', ',array_keys($this->ST_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->ST_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        return "State Added Successfully";
    }
    
    
    //--------------------------------------------------- View State --------------------------------------------- //
    function viewStates($filt=''){
        $count=0;
		$this->StateArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT * FROM states ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->StateArray[$count]=$row;
			$count++;
		}
        
    }
    //--------------------------------------------------- View State --------------------------------------------- //
    function viewStatesGrid(){
        $count=0;
		$this->StateArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT S.ST_Id,S.ST_Name,S.ST_Status, C.CN_Id,C.CN_Name FROM states S,countries as C WHERE S.CN_Id=C.CN_Id ORDER BY C.CN_Name,S.ST_Name ASC");
		while($row=mysqli_fetch_object($result)) {
			$this->StateArray[$count]=$row;
			$count++;
		}
        
    }
    //------------------------------------------  Verify State  ---------------------------------------------------//
    function verifyState($STId)
    {
        $sql = 'SELECT COUNT(ST_Name) FROM states WHERE ST_Name = "'. $this->ST_Data['ST_Name'].'" AND ST_Id != '. $STId;
        $result = mysqli_query($GLOBALS['con'],$sql);                     
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        $count=$row[0];
        if($count == 0){
            return true;
        } else {
            return false;
        }
        
    }
    function updateState($STId){
		$STData = '';
		foreach ($this->ST_Data as $key=>$value){ 
			$STData = $STData .$key ."='".$value."', ";
		}
		$STData = substr($STData, 0, -2);
		$sql = "UPDATE states SET $STData WHERE ST_Id=$STId";
		mysqli_query($GLOBALS['con'],$sql); 
		return 'State Updated Successfully';
	}
}
?>
