<?php
require_once("connection.php");

class OfficeClass
{
	var $OfficeArray;
	
	
	//----------------------------------------- All Offices ----------------------------------------//
	function viewOffices($fields,$filt='')
	{			
		$count=0; 
		$this->OfficeArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM offices ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->OfficeArray[$count]=$row;
			$count++;
		}	
	}
        
        
        function viewOfficeGrid($filt='')
	{			
		$count=0; 
		$this->OfficeArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT OF1.OF_Id, OF1.OF_Name, OF1.AP_Id,OF1.OF_Street,OF1.OF_Building, OF1.OF_Pincode, 
                         OF1.OF_Comments, OF1.TZ_Id,OF1.CR_Id, OF1.OF_Status, US.US_Id, US.US_Email,  US.US_FName, US.US_LName,
                         AST.SR_Name,AP.PL_Name,AL.ALC_Name,CT.CT_Name,ST.ST_Name,CN.CN_Name 
                         FROM offices as OF1 LEFT JOIN users_auth as US ON OF1.OF_Admin = US.US_Id
                        LEFT JOIN addr_streets AS AST ON AST.SR_Id=OF1.SR_Id 
                        LEFT JOIN addr_places AS AP ON AP.PL_Id= AST.PL_Id
                        LEFT JOIN addr_locations AS AL ON AL.ALC_Id=AP.ALC_Id 
                        LEFT JOIN cities AS CT ON OF1.CT_Id=CT.CT_Id 
                        LEFT JOIN states AS ST ON OF1.ST_Id=ST.ST_Id 
                        LEFT JOIN countries AS CN ON OF1.CN_Id=CN.CN_Id ".$filt."  
                        ORDER BY OF1.OF_Name");
		while($row=mysqli_fetch_object($result)) {
			$this->OfficeArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify Office ----------------------------------------//
	function verifyOffice($OFId){
		$sql = 'SELECT COUNT(OF_Name) FROM offices WHERE OF_Name = "'.$this->OF_Data['OF_Name'].'" AND OF_Id != '.$OFId;
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New Office -------------------------------------------//
	function newOffice(){
		
		$sql = "INSERT INTO offices ( " . implode(', ',array_keys($this->OF_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->OF_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
                if(mysqli_affected_rows($GLOBALS['con'])>0)
                {
                $OFId=mysqli_insert_id($GLOBALS['con']);
		return $OFId;
                }

	}
	//----------------------------------------- Update Office ----------------------------------------//
	function updateOffice($OFId){
		$OFData = '';
		foreach ($this->OF_Data as $key=>$value){ 
			$OFData = $OFData .$key ."='".$value."', ";
		}
		$OFData = substr($OFData, 0, -2);
		$sql = "UPDATE offices SET $OFData WHERE OF_Id=".$OFId;
		mysqli_query($GLOBALS['con'],$sql);                
		return 'Company Updated Successfully';
	}
        //------------------------- Create new Company Admin ----------------------------------------------//
        function newOfficeAdmin(){
                $sql = "INSERT INTO locations ( " . implode(', ',array_keys($this->OF_Details)) . ") VALUES (" . "'" . implode("','", array_values($this->OF_Details)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
            
        }
        //------------------------- Company Administrator ID ----------------------------------------------//
        function offzAdmin($OFId){          
            $sql = 'SELECT OF_Admin FROM  offices WHERE OF_Id = '.$OFId;         
            $result = mysqli_fetch_array(mysqli_query($GLOBALS['con'],$sql),MYSQLI_NUM);
            return $result[0];
        }
        
        //----------------------------------------- All Active Office Admins ----------------------------------------//
	function viewOfficeAdmins($filter='')
	{		
		$count=0; 
		$this->OfficeArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT OF1.OF_Id, OF1.OF_Name, US.US_Id, US.US_EMPID,  US.US_FName, US.US_LName, UAM.UAM_Map FROM offices  as OF1, users_auth as US 
                                        LEFT JOIN users_account_map AS UAM ON FIND_IN_SET( US.US_Id , UAM_Map )
                                        WHERE OF1.OF_Admin = US.US_Id
                                            AND OF1.OF_Id != 1 
                                            AND OF1.OF_Status = 1 
                                            AND US.US_Status = 1 
                                            ".$filter." ORDER BY US.US_FName" );
		while($row=mysqli_fetch_object($result)) {
			$this->OfficeArray[$count]=$row;
			$count++;
		}	
	}
        //----------------------------------------- Verify  ----------------------------------------//
	function verifyMapOfz($USId){
		$sql = 'SELECT COUNT(UAM_Id) FROM users_account_map WHERE FIND_IN_SET( '.$this->UAM_Data['US_Id'].' , UAM_Map )';
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
                        return false;	
		}
	}
        
        //------------------------------------- Map New Company to User --------------------------------------------//
        function newMapOffices() {
            $sql = "INSERT INTO users_account_map ( " . implode(', ',array_keys($this->UAM_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->UAM_Data)) . "'" . ")"; 
            mysqli_query($GLOBALS['con'],$sql);
            return 'Accounts Mapped Successfully';
        }
        
        function updateMapOffices($USId) {
            $UCData = '';
            foreach ($this->UAM_Data as $key=>$value){ 
                $UCData = $UCData .$key ."='".$value."', ";
            }
            $UCData = substr($UCData, 0, -2);
            $sql = "UPDATE users_account_map SET $UCData WHERE FIND_IN_SET( $USId , UAM_Map )"; 
            //return $sql;
            mysqli_query($GLOBALS['con'],$sql);
            return 'Accounts Mapped Successfully';
        }
        
        function myMapOffice($USId){
            $this->OfficeMapArray = array();
            $sql = 'SELECT UAM_Map FROM users_account_map WHERE FIND_IN_SET( '.$USId.' , UAM_Map )'; 
            $result = mysqli_query($GLOBALS['con'],$sql);                         
            while($row=mysqli_fetch_object($result)) {
                $this->OfficeMapArray = $row;
            }             
	}
        
        //----------------------------------------- All Offices ----------------------------------------//
	function viewMyOffices($filt='')
	{		
		$count=0; 
		$this->OfficeArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT OF1.OF_Id, OF1.OF_Name, US.US_Id, US.US_FName, US.US_LName, US.US_EMPID FROM offices as OF1
                                        LEFT JOIN users_auth as US ON US.OF_Id = OF1.OF_Id
                                            WHERE ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->OfficeArray[$count]=$row;
			$count++;
		}	
	}
        
}

?>