<?php
require_once("connection.php");
class ExpenseControlClass {
    var $ExpenseControlArray;
    
    //----------------------------- Expense Control Report Count ---------------------------------//
    
    function expCntrlDataCount($stDate,$enDate,$ofid,$newFilt,$filter_key) {
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
        }
        $count=0;
        
        $result = mysqli_query($GLOBALS['con'],"SELECT COUNT(BS.BS_Id)
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `offices` AS OF1 ON LC.OF_Id = OF1.OF_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id = BS.US_Id
                                WHERE
                                BS.US_Id IN ( ".$newFilt." )
                                ".$filter_key."  
                                ".$dateFilt."    
                                AND MH.MH_Type IN ('1','2') 
                                AND ( BS.BS_Status = 1 OR BS.BS_Status = 2 ) ");
        if($result){
             $row = mysqli_fetch_array($result,MYSQLI_NUM);
             return $count=$row[0];
            
        }
    }
    
    //----------------------------- Expense Control Report  ---------------------------------//
    
    function expCntrlData($stDate,$enDate,$ofid,$newFilt,$filter_key,$pos,$cnt,$sort) {	
         $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
        }
        $count=0;
        $this->ExpenseControlArray = array();         
        $sql="SELECT BS.US_Id, BS.BS_Id, BS.BS_Date , BS.BS_MinAmount,BS.BS_Amount, BS.BS_MaxAmount, BS.BS_PettyCashAmt,
                    IT.IT_Id, IT.IT_Name,IT.IT_Business, IT.SH_Id, DS.DS_Id, DS.DS_Description,TR.TR_Id ,TR.TR_Track,
                    SH.SH_Id,SH.SH_Name,SH.SH_Track, MH.MH_Type, LC.LC_Name,US.US_FName,US.US_LName
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `offices` AS OF1 ON LC.OF_Id = OF1.OF_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                WHERE
                                BS.US_Id IN ( ".$newFilt." )
                                ".$filter_key." 
                                ".$dateFilt."    
                                AND MH.MH_Type IN ('1','2') 
                                AND ( BS.BS_Status = 1 OR BS.BS_Status = 2 )    
                                ORDER BY $sort LIMIT ".$pos.' , '.$cnt ;
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->ExpenseControlArray[$count] = $row;
                $count++;
            }
            
        }
    }
    
}
?>