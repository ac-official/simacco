<?php
require_once("connection.php");

class DescriptionClass
{
	var $DescriptionArray;
        var $DSStatus;
	var $DescCount;
	
	//----------------------------------------- All Descriptions ----------------------------------------//
	function viewDescriptions($filt='',$limit='')
	{	
		$count=0;
		$this->DescriptionArray = array();
                //die("SELECT * FROM descriptions ".$filt);
		$result=mysqli_query($GLOBALS['con'],"SELECT DS.DS_Id ,DS.US_Id,DS.IT_Id,DS.OF_Id,DS.DS_Description,DS.DS_Status, IT.IT_Id, IT.IT_Name, IT.MH_Type, SH.SH_Id FROM descriptions as DS , items as IT, sub_heads as SH ".$filt." ORDER BY DS.DS_Description ".$limit);		
                while($row=mysqli_fetch_object($result)) {
			$this->DescriptionArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify Description ----------------------------------------//
	function verifyDescription($DSId){
		$sql = 'SELECT DS_Id, DS_Description,DS_MinAmount,DS_MaxAmount, DS_Status FROM descriptions WHERE DS_Description = "'.$this->DS_Data['DS_Description'].'" AND IT_Id = "'.$this->DS_Data['IT_Id'].'" AND DS_Status != 4 AND DS_Id != '.$DSId;
                $result = mysqli_query($GLOBALS['con'],$sql);
		if(mysqli_num_rows($result) == 0){
			return false;
		} else {
                        $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
                        $this->DSStatus['DS_Status'] = $row['DS_Status'];
                        $this->DSStatus['DS_MinAmount'] = $row['DS_MinAmount'];
                        $this->DSStatus['DS_MaxAmount'] = $row['DS_MaxAmount'];
			return $row['DS_Id'];	
		}
	}
	
	//----------------------------------------- New Description ----------------------------------------//
	function newDescription(){
		
		$sql = "INSERT INTO descriptions ( " . implode(', ',array_keys($this->DS_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->DS_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
		return 'Description Created Successfully';

	}
             //----------------------------------------- Adding Multiple Description ----------------------------------------//
	function newMultipleDescription(){
		$this->DS_Data['DS_Description'] = '';
          	$sql = "INSERT INTO descriptions ( " . implode(', ',array_keys($this->DS_Data)) . ") VALUES ";
                foreach ($this->DS_Data_Description['DS_Description'] as $key=>$value){
                    if($value != '') { 
                            $this->DS_Data['DS_Description'] = trim(htmlspecialchars($value, ENT_QUOTES));
                            $sql .= " (" . "'" . implode("','", array_values($this->DS_Data)) . "'" . ")," ;
                        }
                    }
                $sql=rtrim($sql, ",");
		mysqli_query($GLOBALS['con'],$sql);
		return 'Description Created Successfully';

	}
        //------------------------------------- Adding Multiple Description for Company--------------------------------//
	function newCompanyDescription(){
		$this->DS_Data['DS_Description'] = '';
          	$sql = "INSERT INTO descriptions ( " . implode(', ',array_keys($this->DS_Data)) . ") VALUES ";
                foreach ($this->DS_Data_Description['DS_Description'] as $key=>$value){
                    if($value != '') { 
                        $ID = explode("_", $key); 
                        if($ID[0] == 'N'){
                            $this->DS_Data['DS_Description'] = htmlspecialchars($value, ENT_QUOTES);
                            $sql .= " (" . "'" . implode("','", array_values($this->DS_Data)) . "'" . ")," ;
                        }
                    }
                }
                $sql=rtrim($sql, ",");
		mysqli_query($GLOBALS['con'],$sql);
		return 'Description Created Successfully';

	}
	//----------------------------------------- Update Description ----------------------------------------//
	function updateDescription($DSId){
		$DSData = '';
		foreach ($this->DS_Data as $key=>$value){ 
			$DSData = $DSData .$key ."='".$value."', ";
		}
		$DSData = substr($DSData, 0, -2);
		$sql = "UPDATE descriptions SET $DSData WHERE DS_Id=$DSId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Description Updated Successfully<br>';
	}
	//----------------------------------------- New Description ----------------------------------------//
	function newBalSheetDescription(){
		
		$sql = "INSERT INTO descriptions ( " . implode(', ',array_keys($this->DS_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->DS_Data)) . "'" . ")";
		mysqli_query($GLOBALS['con'],$sql);
        	return mysqli_insert_id($GLOBALS['con']);

	}
        function approveDescription($USId,$DSId,$MyId) {
            $sql = "UPDATE descriptions SET DS_Approval = ".$USId." , DS_Approved = ".$MyId."  WHERE DS_Id = ".$DSId; 
            //return $sql;
            mysqli_query($GLOBALS['con'],$sql);
            return 'Description Approved Successfully';
        }
        //----------------------------------------- Verify Description ----------------------------------------//
	function getDescriptionName($ITId,$filter){
		
                $count=0;
		$this->DescriptionArray = array();
                $sql = 'SELECT DS_Id, DS_Description,DS_MinAmount,DS_MaxAmount,DS_Status FROM descriptions WHERE IT_Id = "'.$ITId.'" AND '.$filter.'  ORDER BY DS_Description ';
		$result=mysqli_query($GLOBALS['con'],$sql);		
                while($row=mysqli_fetch_object($result)) {
                        $row->DS_Description = htmlspecialchars_decode($row->DS_Description, ENT_QUOTES);
			$this->DescriptionArray[$count]=$row;
			$count++;
		}	
	}
        //-----------------------------------__Create Desc for Salary Payments and returns ID------------------------//
        function createDescription(){
		
		$sql = "INSERT INTO descriptions ( " . implode(', ',array_keys($this->DS_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->DS_Data)) . "'" . ")";		
		mysqli_query($GLOBALS['con'],$sql);
		return mysqli_insert_id($GLOBALS['con']);

	}
        //---------------------------------get description for salary reports----------------------------//
        	function getSalDescName($ITId,$filter){	
                $sql = 'SELECT DS_Id FROM descriptions WHERE IT_Id = "'.$ITId.'" AND '.$filter;
		$result=mysqli_query($GLOBALS['con'],$sql);
                if(mysqli_num_rows($result)>0){
                $row=mysqli_fetch_assoc($result);                
                return $row["DS_Id"];
                }else{
                    return false;
                }
	}
        //----------------------------------------- Update Company Description ----------------------------------------//
	function updateCompanyDescription($DSId){
		$DSData = '';
		foreach ($this->DS_Data as $key=>$value){ 
			$DSData = $DSData .$key ."='".$value."', ";
		}
		$DSData = substr($DSData, 0, -2);
		$sql = "UPDATE descriptions SET $DSData WHERE DS_Id=$DSId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Description Updated Successfully<br>';
	}
        function listAllDescriptions($filt='',$limit='')
	{	
		$count=0;
		$this->DescriptionArray = array();
                //die("SELECT * FROM descriptions ".$filt);
               $sql="SELECT SQL_CALC_FOUND_ROWS DS.DS_Id ,DS.US_Id,DS.IT_Id,DS.OF_Id,DS.DS_Description,DS.DS_Status, IT.IT_Id, IT.IT_Name,CONCAT(US_FName,' ',US_LName) AS USNAME FROM descriptions  DS 
                    LEFT JOIN items as IT ON IT.IT_Id=DS.IT_Id LEFT JOIN users_auth AS UA ON UA.US_Id=DS.US_Id ".$filt.$limit;               
		$result=mysqli_query($GLOBALS['con'],$sql);		
                $sqlfoundrows=mysqli_query($GLOBALS['con'],"SELECT FOUND_ROWS() AS TOTAL_DESC");
                $rowfound=mysqli_fetch_object($sqlfoundrows);                 
                $this->DescCount= $rowfound->TOTAL_DESC;
                while($row=mysqli_fetch_object($result)) {
			$this->DescriptionArray[$count]=$row;
			$count++;
		}	
	}
}

?>