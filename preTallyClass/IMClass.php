<?php
require_once("connection.php");
class IMClass {
    var $IMArray;
    //----------------------------------------- All ACLs ----------------------------------------//
    function viewIM($CHTTO) {			
        $count = 0; 
        $this->IMArray = array(); 
        $sql = "SELECT CHT_Text FROM chats WHERE CHT_TO_US_Id = ".$CHTTO." AND CHT_Read = 0";
        //echo $sql;
        $result = mysql_query($sql);
        while($row = mysql_fetch_object($result)) {
            $this->IMArray[$count] = $row;
            $count++;
        }
        
        $sql = "UPDATE chats SET CHT_Read = 1 WHERE CHT_TO_US_Id=".$CHTTO;                
        mysql_query($sql);
    }
    //----------------------------------------- New ACL -------------------------------------------//
    function newIM() {		
        
        $sql = "INSERT INTO chats ( " . implode(', ',array_keys($this->IM_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->IM_Data)) . "'" . ")";		
        mysql_query($sql); 
        return $sql;
        if(mysql_affected_rows()>0) {
            //$UTId = mysql_insert_id();
            //return $UTId;
        }
    }

}

?>