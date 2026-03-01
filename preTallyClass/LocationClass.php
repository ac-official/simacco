<?php

require_once("connection.php");

class LocationClass {
    
    var $LocationArray;    
    
    //----------------------------------------- All Locations ----------------------------------------//
    function viewLocations($field="*",$filt='1')
    {			
        $count=0;
        $this->LocationArray = array();
        $sql = "SELECT ".$field." FROM locations ".$filt; 
        //die($sql);
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
            $this->LocationArray[$count]=$row;
            $count++;
        }	
    }
    
    // ------------------- Loction With Office ------ 03-09-25-------------- //
    function viewAllOfficeLocation($field="",$filt='1')
    {           
        $count=0;
        $this->LocationArray = array();
        $sql = "SELECT lc.LC_Id, lc.LC_Status, concat(lc.LC_Name,' - ', offc.OF_Name) AS LC_Name  ".$field." FROM locations as lc LEFT JOIN offices as offc ON (offc.OF_Id = lc.OF_Id)  ".$filt; 
        //die($sql);
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
            $this->LocationArray[$count]=$row;
            $count++;
        }   
    }
    // ----------------- List all countries -- 03-09-2025 ------------- //
    function listCountry() 
    {
        $datas  = [];
        $sql    = 'SELECT CN_Id AS id, CN_Name AS name FROM countries WHERE CN_Flag IS NOT NULL';
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
            $datas[] = $row;
        }
        
        return $datas;
    }
    
    function viewLocationGrid($filter){
        $count=0;   
        $this->LocationArray = array();
        $sql = "SELECT LC.LC_Id,LC.OF_Id,OF1.OF_Name,LC.OF_Id,LC.LC_Name,LC.LC_Phone,LC.LC_Pincode,LC.LC_Building,LC.LC_Status,AST.SR_Name,AP.PL_Name,AL.ALC_Name,CT.CT_Name,ST.ST_Name,CN.CN_Name FROM locations AS LC 
                 LEFT JOIN offices AS OF1 ON LC.OF_Id=OF1.OF_Id 
                 LEFT JOIN addr_streets AS AST ON AST.SR_Id=LC.SR_Id 
                 LEFT JOIN addr_places AS AP ON AP.PL_Id= AST.PL_Id
                 LEFT JOIN addr_locations AS AL ON AL.ALC_Id=AP.ALC_Id 
                 LEFT JOIN cities AS CT ON LC.CT_Id=CT.CT_Id 
                 LEFT JOIN states AS ST ON CT.ST_Id=ST.ST_Id AND LC.ST_Id=ST.ST_Id 
                 LEFT JOIN countries AS CN ON CN.CN_Id=ST.CN_Id AND LC.CN_Id=CN.CN_Id ".$filter; 
        //die($sql);
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
            $this->LocationArray[$count]=$row;
            $count++;
        }
    }

    //----------------------------------------- Verify Location ----------------------------------------//
    function verifyLocation($LCId){
            $sql = 'SELECT COUNT(LC_Name) FROM locations WHERE LC_Name = "'.$this->LC_Data['LC_Name'].'" AND OF_Id = "'.$this->LC_Data['OF_Id'].'" AND LC_Status != 5 AND LC_Id != '.$LCId; 
            $result = mysqli_query($GLOBALS['con'],$sql);                     
            $row = mysqli_fetch_array($result,MYSQLI_NUM);
            $count=$row[0];
            if($count == 0){
                    return true;
            } else {
                    return false;	
            }
    }

    //----------------------------------------- New Location ----------------------------------------//
    function newLocation(){

            $sql = "INSERT INTO locations ( " . implode(', ',array_keys($this->LC_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->LC_Data)) . "'" . ")";            
            mysqli_query($GLOBALS['con'],$sql);
            if(mysqli_affected_rows($GLOBALS['con'])>0)
            {
            $LCId=mysqli_insert_id($GLOBALS['con']);
            return $LCId;
            }

    }
    //----------------------------------------- Update Location ----------------------------------------//
    function updateLocation($LCId){
            $LCData = '';
            foreach ($this->LC_Data as $key=>$value){ 
                    $LCData = $LCData .$key ."='".$value."', ";
            }
            $LCData = substr($LCData, 0, -2);
            $sql = "UPDATE locations SET $LCData WHERE LC_Id=$LCId";
            mysqli_query($GLOBALS['con'],$sql);
            return 'Branch Updated Successfully';
    }
     //------------------------------Get All Office  Names -----------------------------------------------//
    function getOffices($filt='')
    {
        $count=0;
            $this->NewLocationArray = array();
            $result=mysqli_query($GLOBALS['con'],"SELECT OF_Id, OF_Name FROM offices ".$filt);
                while($row=mysqli_fetch_object($result)) {
                    $this->NewLocationArray[$count]=$row;
                    $count++;
            }	
    }
	
    function createOpeningBalances() {
       
       
       $sql = "INSERT INTO cash_open_bals ( " . implode(', ', array_keys($this->Bal_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Bal_Data)) . "'" . ")";
       mysqli_query($GLOBALS['con'],$sql);
    }
    
    //----------------------------------------- Creating Old Stock ----------------------------------------//
    function createStockOpeningBalances() {
             
        $sql = "INSERT INTO stock_open_bals ( " . implode(', ', array_keys($this->OS_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->OS_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
    }
    //----------------------------------------- All Currencies ----------------------------------------//
    function getCurrencies() {
        $count=0;
        $this->LocationArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT CR_Id, CR_Name, CR_Symbol FROM currencies ".$filt);
        while($row=mysqli_fetch_object($result)) {
                $this->LocationArray[$count]=$row;
                $count++;
        }	
    }
    //----------------------------------------- All Time Zones ----------------------------------------//
    function getTimeZones() {
        $count=0;
        $this->LocationArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT TZ_Id, TZ_Name FROM time_zones ".$filt);
        while($row=mysqli_fetch_object($result)) {
                $this->LocationArray[$count]=$row;
                $count++;
        }	
    }
    //----------------------------------------- Location name by ID ----------------------------------------//
        function getLocationName($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT LC_Name FROM locations WHERE LC_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['LC_Name'];            
        }
}
?>