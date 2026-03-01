<?php
require_once("connection.php");
class AddressClass
{ var $PlaceArray;
  var $IdArray;
    function addPlaces()
    {
        $sql = "INSERT INTO address_places ( " . implode(', ',array_keys($this->AD_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->AD_Data)) . "'" . ")";
        $result=mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_affected_rows($GLOBALS['con'])>0)
        {
        $lcid=mysqli_insert_id($GLOBALS['con']);
        return $lcid;
        }
    }
    function newPlaces()
    {
       $sql = "INSERT INTO address_places ( " . implode(', ',array_keys($this->AD_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->AD_Data)) . "'" . ")";
        $result=mysqli_query($GLOBALS['con'],$sql);        
        return "New Place added";
    }
    function updatePlace($plid)
    {      
		$ADData = '';
		foreach ($this->AD_Data as $key=>$value){ 
			$ADData = $ADData .$key ."='".$value."', ";
		}
		$ADData = substr($ADData, 0, -2);
		$sql = "UPDATE address_places SET $ADData WHERE AP_Id=$plid";
                $sql2 = "UPDATE locations SET CT_Id WHERE AP_Id=$plid";
               // $sql3 = "UPDATE offices SET CT_Id WHERE AP_Id=$plid";                
		mysqli_query($GLOBALS['con'],$sql);mysqli_query($GLOBALS['con'],$sql2);//mysqli_query($GLOBALS['con'],$sql3);
		return 'Place Updated Successfully';
	
    }
    function verifyPlace($apid)
    {
        $sql = "SELECT COUNT(*) FROM address_places WHERE AP_Name='".$this->AD_Data['AP_Name']."' AND CT_Id=".$this->AD_Data['CT_Id']." AND AP_Id!=".$apid;
        $result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
    }
    function listPlaces()
    {
         $sql="SELECT AP_Id,AP_Name,CT_Id,AP_Status FROM address_places " ;
        $result=mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
			$this->PlaceArray[$count]=$row;
			$count++;
                    }
        
    }
    function viewPlaces($key,$filter="")
    {
       $sql="SELECT AP.AP_Id,AP.AP_Name,CT.CT_Name,ST.ST_Name,CNT.CN_Name FROM address_places AS AP,cities AS CT,states as ST,countries as CNT WHERE AP.AP_Name LIKE '".mysqli_real_escape_string($GLOBALS['con'],ucfirst($key))."%' ".$filter." AND CNT.CN_Id=ST.CN_Id AND ST.ST_Id=CT.ST_Id AND CT.CT_Id=AP.CT_Id AND AP.AP_Status=1 ORDER BY AP.AP_Name" ;
       $result=mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
			$this->PlaceArray[$count]=$row;
			$count++;
                    }
    }
    function getCityName($ctid)
    {
        $sql="SELECT CT_Name FROM cities WHERE CT_Id=".$ctid;
        $result=mysqli_query($GLOBALS['con'],$sql);
        $row=  mysqli_fetch_array($result,MYSQLI_NUM);
        return $row[0];
    }
    function getPlaceIdsLoc($lcid)
    {   $count=0;
       $sql="SELECT AD.AP_Name,CT.CT_Id,CT.CT_Name,ST.ST_Id,ST.ST_Name,CN.CN_Id,CN.CN_Name FROM address_places AS AD,cities AS CT,states AS ST,countries AS CN WHERE  CT.ST_Id=ST.ST_Id AND ST.CN_Id=CN.CN_Id AND AD.CT_Id = CT.CT_Id AND AD.AP_Id=".$lcid;
        $result=mysqli_query($GLOBALS['con'],$sql);        
        while($row=mysqli_fetch_assoc($result)) {
			$this->IdArray[$count]=$row;
			$count++;
                    }
    }
    function getPlaceIdsCty($lcid)
    {   
       $count=0;
       $sql="SELECT CT.CT_Id,CT.CT_Name,ST.ST_Id,ST.ST_Name,CN.CN_Id,CN.CN_Name FROM cities AS CT,states AS ST,countries AS CN WHERE  CT.ST_Id=ST.ST_Id AND ST.CN_Id=CN.CN_Id AND CT.CT_Id =".$lcid;
        $result=mysqli_query($GLOBALS['con'],$sql);        
        while($row=mysqli_fetch_assoc($result)) {
			$this->IdArray[$count]=$row;
			$count++;
                    }
    }
    function getPlaceName($lcid)
    {
        $sql="SELECT AP_Name from address_places WHERE AP_Id=".$lcid;
        $result=mysqli_query($GLOBALS['con'],$sql);
        $row=  mysqli_fetch_row($result);
        return $row[0];
    }
    function verifyCity($ctid){
        $sql="SELECT COUNT(CT_Id) WHERE CT_Id=".$ctid;        
        $result =  mysqli_query($GLOBALS['con'],$sql);
        $row    =  mysqli_fetch_row($result);
        if($row[0]==0){
            return false;
        }else{
            return true;
        }
    }
    
}

