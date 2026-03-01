<?php
require_once("connection.php");

class SubheadPatternClass
{
	var $PatternArray;
	
	//----------------------------------------- All Patterns ----------------------------------------//
	function viewPatterns($filt='')
	{	
		$count=0;
		$this->PatternArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT PI_Id, PI_Title FROM sh_pattern_items ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->PatternArray[$count]=$row;
			$count++;
		}	
	}
	//----------------------------------------- Verify Item ----------------------------------------//
	function verifyPatternList($SHId){
		$sql = 'SELECT COUNT(PL_PatternMap) FROM sh_pattern_list WHERE SH_Id = "'.$this->PL_Data['SH_Id'].'"';
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
                        return false;	
		}
	}
        //------------------------------------- Map New Item to Company --------------------------------------------//
        function newPatternList() {
            $sql = "INSERT INTO sh_pattern_list ( " . implode(', ',array_keys($this->PL_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->PL_Data)) . "'" . ")"; 
            mysqli_query($GLOBALS['con'],$sql);
            return 'New Pattern Set Successfully';
        }
        function mapPatternList($SHId) {
            $PLData = '';
            foreach ($this->PL_Data as $key=>$value){ 
                $PLData = $PLData .$key ."='".$value."', ";
            }
            $PLData = substr($PLData, 0, -2);
            $sql = "UPDATE sh_pattern_list SET $PLData WHERE SH_Id = ".$SHId; 
            mysqli_query($GLOBALS['con'],$sql);
            return 'Pattern Updated Successfully';
        }
        
        function getPatternList($SHId){
            $count=0; 
            $this->PatternArray = array();
            $sql = 'SELECT PL_PatternMap FROM sh_pattern_list WHERE SH_Id = '.$SHId;
            $result = mysqli_query($GLOBALS['con'],$sql);
            while($row=mysqli_fetch_object($result)) {
                $this->PatternArray[$count] = $row;
                $count++;
            }
        }
        function listSHPatterns($PL_Pattern){
            $count=0; 
            $this->PatternArray = array();
            $sql = 'SELECT PI_Id, PI_Title FROM sh_pattern_items WHERE PI_Id IN('.$PL_Pattern.')';
            $result = mysqli_query($GLOBALS['con'],$sql);
            while($row=mysqli_fetch_object($result)) {
                $this->PatternArray[$count] = $row;
                $count++;
            }
        }
        function listSHPatternArray($PL_Pattern){
            $count=0; 
            $this->PatternArray = array();
            $sql = 'SELECT PI_Id,PI_Name FROM sh_pattern_items WHERE PI_Id IN('.$PL_Pattern.')';
            $result = mysqli_query($GLOBALS['con'],$sql);
            while($row=  mysqli_fetch_assoc($result)) {
                $this->PatternArray[$row['PI_Id']] = $row['PI_Name'];
                $count++;
            }
        }
	function listSHItemArray(){
            $count=0; 
            $this->PatternArray = array();
            $sql = 'SELECT * FROM sh_pattern_items';
            $result = mysqli_query($GLOBALS['con'],$sql);
            while($row=  mysqli_fetch_assoc($result)) {
                $this->PatternDetailsArray = array();
                if($row['PI_Title'] != '')  $this->PatternDetailsArray['title'] = $row['PI_Title'];
                if($row['PI_Type'] != '')   $this->PatternDetailsArray['type'] = $row['PI_Type'];
                if($row['PI_ClassName'] != '') $this->PatternDetailsArray['className'] = $row['PI_ClassName'];
                if($row['PI_Filter'] != '') $this->PatternDetailsArray['filter'] = $row['PI_Filter'];
                if($row['PI_Dateformat'] != '') $this->PatternDetailsArray['dateFormat'] = $row['PI_Dateformat'];
                if($row['PI_Block'] != '')  $this->PatternDetailsArray['block'] = $row['PI_Block'];
               
                $this->PatternArray[$row['PI_Name']] = $this->PatternDetailsArray;
                
            }
        }
}

?>