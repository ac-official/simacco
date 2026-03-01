<?php
require_once("connection.php");

class CityClass {
    var $CityArray;
    
    //--------------------------------------------------- All Cities ----------------------------------------------//
    
    //--------------------------------------------------- New City -----------------------------------------------//
    function newCity(){
        $sql = "INSERT INTO cities ( " . implode(', ',array_keys($this->CT_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->CT_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        return "City Added Successfully";
    }
    
    
    //--------------------------------------------------- View City --------------------------------------------- //
    function viewCities($filt=''){
        $count=0;
		$this->CityArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT * FROM cities ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->CityArray[$count]=$row;
			$count++;
		}
        
    }
    //--------------------------------------------------- View City --------------------------------------------- //
    function viewCitiesGrid($pos,$cnt){
        $count=0;
		$this->CityArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT CT.CT_Id,CT.CT_Name,CT.CT_Status, ST.ST_Id,ST.ST_Name,CN.CN_Id,CN.CN_Name FROM cities AS CT,states AS ST,countries as CN WHERE ST.CN_Id=CN.CN_Id AND CT.ST_Id=ST.ST_Id ORDER BY CN.CN_Name,ST.ST_Name ASC,CT.CT_Name ASC LIMIT ".$pos.",".$cnt);
		while($row=mysqli_fetch_object($result)) {
			$this->CityArray[$count]=$row;
			$count++;
		}
        
    }
    //------------------------------------------  Verify City  ---------------------------------------------------//
    function verifyCity($CTId)
    {
        $sql = 'SELECT COUNT(CT_Name) FROM cities WHERE CT_Name = "'. $this->CT_Data['CT_Name'].'" AND ST_Id = "'. $this->CT_Data['ST_Id'].'" AND CT_Id != '. $CTId;
        $result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
            return true;
        } else {
            return false;
        }
        
    }
    function updateCity($STId){
		$STData = '';
		foreach ($this->CT_Data as $key=>$value){ 
			$STData = $STData .$key ."='".$value."', ";
		}
		$STData = substr($STData, 0, -2);
		$sql = "UPDATE cities SET $STData WHERE CT_Id=$STId";
		mysqli_query($GLOBALS['con'],$sql); 
		return 'City Updated Successfully';
	}
}
?>
