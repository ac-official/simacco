<?php
require_once("connection.php");
class PettyCashClass {
    var $PettyCashArray;
    
    
    //--------------------------------------Cash BalanceSheet Items ----------------------------------------//
    function reportPettyCashData($filt,$stDate='',$enDate='',$filter,$filter_mask,$pos,$cnt) {		
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
        $this->PettyCashArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date, BS.BS_Amount,BS.BS_PettyCashAmt,BS.BS_PettyCashRefId,DS.DS_Id, DS.DS_Description,TR.TR_Id, TR.TR_Track, IT.IT_Id, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name,US.US_FName, US.US_LName
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_PettyCash = 1
                                AND BS.BS_Status = 1
                                AND MH.MH_Type IN ('1','2')
                                ".$dateFilt."
                                ".$filter_mask."
                                AND LC.".$filter."
                                ORDER BY BS.BS_Date   DESC
                                LIMIT ".$pos.",".$cnt
                                ); // AND BS.PM_Id = 1  Changed Condition on Feb 9th
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->PettyCashArray[$count] = $row;
                $count++;
            }
        }
    }
    
    //-----------------------------------Cash BalanceSheet Items Count  ----------------------------------------//
    function reportPettyCashDataCount($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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

        $result = mysqli_query($GLOBALS['con'],"SELECT COUNT(BS.BS_Id)
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_PettyCash = 1
                                AND BS.BS_Status = 1
                                AND MH.MH_Type IN ('1','2')
                                ".$dateFilt."
                                ".$filter_mask."
                                AND LC.".$filter."
                                ORDER BY BS.BS_Date DESC
                                "
                                );// AND BS.PM_Id = 1  Changed Condition on Feb 9th
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        return $count=$row[0];
    }
    //-----------------------------------Get current balance---------------------
    function reportPettyCashBalance($filter) {		
        $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_PettyCashAmt)
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_PettyCash = 1
                                AND BS.BS_Status = 1
                                AND MH.MH_Type IN ('1','2')
                                AND BS.PM_Id = 1                                
                                AND LC.".$filter." 
                                ORDER BY BS.BS_Date DESC");
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        return $count=$row[0];
    }
    //--------------------------------------Cash BalanceSheet Items ----------------------------------------//
    function reportPettyCashDetails($stDate='',$enDate='',$filter) {		
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
//echo "SELECT BS.US_Id, BS.BS_Id, BS.BS_Date, BS.BS_Amount,BS.BS_PettyCashAmt,BS.BS_PettyCashRefId,DS.DS_Id, DS.DS_Description,TR.TR_Id, TR.TR_Track, IT.IT_Id, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name,US.US_FName, US.US_LName
//                                FROM `balance_sheets` AS BS
//                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
//                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
//                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
//                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
//                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
//                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
//                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
//                                WHERE IT.IT_Business = 0
//                                AND BS.BS_Status = 1
//                                AND MH.MH_Type IN ('1','2')
//                                AND BS.PM_Id = 1 
//                                ".$dateFilt."
//                                AND ".$filter."
//                                ORDER BY BS.BS_Date   DESC";
                                
        $count=0;
        $this->PettyCashArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date, BS.BS_Amount,BS.BS_PettyCashAmt,BS.BS_PettyCashRefId,DS.DS_Id, DS.DS_Description,TR.TR_Id, TR.TR_Track, IT.IT_Id, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name,US.US_FName, US.US_LName
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = 0
                                AND BS.BS_Status = 1
                                AND MH.MH_Type IN ('1','2')
                                AND BS.PM_Id = 1 
                                ".$dateFilt."
                                AND ".$filter."
                                ORDER BY BS.BS_Date   DESC"
                                );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->PettyCashArray[$count] = $row;
                $count++;
            }
        }
    }
}
?>