<?php
require_once("connection.php");
class OldStockClass {
    var $OldStockArray;
    
    
    //----------------------------------- Get Old Stock Id----------------------------------------//  
    function getOldStockId($LCId){
        $sql = 'SELECT OS_Id FROM stock_open_bals WHERE LC_Id = '.$LCId;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
        return $row['OS_Id'];
    }   
    
    //----------------------------------- Update Old Stock ----------------------------------------//  
    function updateOldStock($OSId){
            $BalData = '';
            foreach ($this->OS_Data as $key=>$value){ 
                    $BalData = $BalData .$key ."='".$value."', ";
            }
            $BalData = substr($BalData, 0, -2);
            $sql = "UPDATE stock_open_bals SET $BalData WHERE OS_Id=".$OSId;
            mysqli_query($GLOBALS['con'],$sql);
            return 'Old Stock Updated Successfully';
    }
    
    function viewStockBalances($OFId)
    {
        $sql="SELECT OS.OS_Id,LC.LC_Name,OS.OS_OpenBal,OS.OS_Status,OS.OS_Date, OS.TR_Id, TR.TR_Track 
                FROM stock_open_bals AS OS 
                    LEFT JOIN locations AS LC ON LC.LC_Id= OS.LC_Id
                    LEFT JOIN  tracks as TR ON  TR.TR_Id = OS.TR_Id
                        WHERE LC.LC_Status=1 AND LC.OF_Id=".$OFId;
        $count=0;
        $this->OldStockArray = array();  
         $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($result)) {
            $this->OldStockArray[$count] = $row;
            $count++;       
        }    
    }
    
    //------------------------------- Verify Old Stock Balance Status of a Branch  -------------------------------//
    function verifyOldStockStatus($filter)
    {
        $count=0;
        $this->CashBSArray = array();  
        $sql="SELECT OS_Status FROM stock_open_bals WHERE ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC); 	
        return $row['OS_Status'];
    }
}
?>