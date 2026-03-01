<?php

require_once("connection.php");

class ZoneClass {

    var $ZoneArray;
    var $ZN_Data;
    var $UserArray;
    var $ZonArray;

    function newZone() {
        $sql = "INSERT INTO branch_zones ( " . implode(', ', array_keys($this->ZN_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->ZN_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
        return "Zone Added Successfully";
    }

    function viewZones($filt = '') {
        $count = 0;
        $this->ZoneArray = array();
        $sql="SELECT * FROM branch_zones " . $filt;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->ZoneArray[$count] = $row;
            $count++;
        }
    }

    function listZones($filt = '') {
        $count = 0;
        $this->ZoneArray = array();
        $sql = "SELECT BZ.*,CONCAT(UA.US_FName,' ',UA.US_LName) AS CreatedUser,
                 CONCAT(UA2.US_FName,' ',UA2.US_LName) AS ModifiedUser FROM branch_zones AS BZ 
                 LEFT JOIN users_auth AS UA ON UA.US_Id=BZ.ZN_Created 
                 LEFT JOIN users_auth AS UA2 ON UA2.US_Id=BZ.ZN_Modified " . $filt;
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->ZoneArray[$count] = $row;
            $count++;
        }
    }

    function viewLocs($filt = "") {
        $count = 0;
        $this->LocArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT LC_Id,LC_Name FROM locations " . $filt);
        while ($row = mysqli_fetch_assoc($result)) {
            $this->LocArray[$row['LC_Id']] = $row['LC_Name'];
            $count++;
        }
    }

    function updateZone($ZNId) {
        $ZNData = '';
        foreach ($this->ZN_Data as $key => $value) {
            $ZNData = $ZNData . $key . "='" . $value . "', ";
        }
        $ZNData = substr($ZNData, 0, -2);
        $sql = "UPDATE branch_zones SET $ZNData WHERE ZN_Id=$ZNId";
        mysqli_query($GLOBALS['con'], $sql);
        return 'Zone Updated Successfully';
    }

    function verifyZone($ZN_Id = 0) {
        $sql = 'SELECT COUNT(ZN_Name) FROM branch_zones WHERE ZN_Name = "' . $this->ZN_Data['ZN_Name'] . '" AND  ZN_Id != ' . $ZN_Id;
        $result = mysqli_query($GLOBALS['con'], $sql);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        $count = $row[0];
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    function getZonalManagers($filter, $having) {
        $sql = "SELECT US.US_Id,CONCAT(US.US_FName,' ',US.US_LName) AS NAME,US.US_Zones,LC.LC_Name,CONCAT(US2.US_FName,' ',US2.US_LName) AS UPDNAME 
                 FROM users_auth AS US LEFT JOIN locations AS LC ON US.LC_Id=LC.LC_Id 
                 LEFT JOIN users_auth AS US2 ON US2.US_Id=US.US_ZoneUpdBy
                LEFT JOIN acl AS ACL ON US.UT_Id=ACL.ACL_Id WHERE ACL.ACL_ZonalHead=1 AND US.US_Status=1 " . $filter;
        $count = 0;
        $this->UserArray = array();
        $result = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->UserArray[$count] = $row;
            $count++;
        }
    }

    function AssignZone($USId) {
        $ZNData = '';
        foreach ($this->ZN_Data as $key => $value) {
            $ZNData = $ZNData . $key . "='" . $value . "', ";
        }
        $ZNData = substr($ZNData, 0, -2);
        $sql = "UPDATE users_auth SET $ZNData WHERE US_Id=$USId";
        mysqli_query($GLOBALS['con'], $sql);
        return 'Zone Assigned Successfully';
    }

    function viewZoneArray($filt = "") {
        $count = 0;
        $this->ZonArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT ZN_Id,ZN_Name FROM branch_zones " . $filt);
        while ($row = mysqli_fetch_assoc($result)) {
            $this->ZonArray[$row['ZN_Id']] = $row['ZN_Name'];
            $count++;
        }
    }

    function getUserZone($usid) {
        $count = 0;
        $result = mysqli_query($GLOBALS['con'], "SELECT US_Zones FROM users_auth WHERE US_Id=" . $usid);
        $row = mysqli_fetch_assoc($result); 
        return  $row['US_Zones'];      
    }
    function getZoneLocations($lcid){
        $count = 0;
        $result = mysqli_query($GLOBALS['con'], "SELECT LC_Id FROM branch_zones WHERE ZN_Id=" . $lcid);
        $row = mysqli_fetch_assoc($result); 
        return  $row['LC_Id'];      
    }
    function getUserZoneLCId($usid) {
        $count = 0;
        $this->ZonArray=array();
		$sql="SELECT LC.LC_Id,LC.LC_Name,LC.LC_Status FROM users_auth UA 
LEFT JOIN branch_zones AS BZ ON FIND_IN_SET(BZ.ZN_Id,UA.US_Zones) 
LEFT JOIN locations AS LC ON FIND_IN_SET(LC.LC_Id,BZ.LC_Id) 
WHERE UA.US_Id=" . $usid;
        $result = mysqli_query($GLOBALS['con'], $sql);        
        $count=0;
         while ($row = mysqli_fetch_assoc($result)) {            
            $this->ZonArray[$count]=$row;
            $count++;
        } 
    }
    function viewLocations($field="*",$filt='1')
    {			
        $count=0;
        $this->LocationArray = array();
        $sql = "SELECT ".$field." FROM locations ".$filt;         
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_assoc($result)) {
            $this->LocationArray[$count]=$row;
            $count++;
        }	
    }

}
