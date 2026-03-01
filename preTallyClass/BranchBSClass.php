<?php
require_once("connection.php");

class BranchBSClass {
    
    var $BankBSArray;
    
    //---------------------------------------- Bank BalanceSheet Items ----------------------------------------//
    function reportBranchBSData($filt,$stDate='',$enDate='',$filter,$filter_mask,$pos,$cnt) {		
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
        $this->BankBSArray = array();       
        $sql="SELECT BS.US_Id, BS.BS_Id, BS.BS_Date  , BS.BS_Amount, BS.BS_PettyCashAmt,DS.DS_Id, DS.DS_Description, TR.TR_Track, IT.IT_Id,IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name,US.US_FName, US.US_LName 
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `bank_accounts` AS BA on BA.BA_Id = BS.BA_Id 
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
                                WHERE IT.IT_Business = '0' AND  BS.BA_Id != 0 
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 2                                          
                                    AND BS.BS_Status = 1
                                    AND BS.BS_PettyCashRefId = 0
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND ".$filter."
                                ORDER BY BS.BS_Date DESC
                                LIMIT ".$pos.",".$cnt;
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->BankBSArray[$count] = $row;
                $count++;
            }
        }
    }
    //---------------------------------- Bank BalanceSheet Items Count ----------------------------------------//
    function reportBranchBSDataCount($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `bank_accounts` AS BA on BA.BA_Id = BS.BA_Id 
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
                                WHERE IT.IT_Business = '0' AND  BS.BA_Id != 0 
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 2 
                                    AND BS.BS_Status = 1
                                    AND BS.BS_PettyCashRefId = 0
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND ".$filter."
                                ORDER BY BS.BS_Date DESC
                                "
                                );
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        return $count=$row[0];
    }
 
    
    //--------------------------------- Opening Balance of a Branch  ----------------------------------------//
    function getBranchOpeningBalance($stDate='',$enDate='', $fields, $filterOB, $filter)
    {
        $dateFilt = '';
        $getOldbalance = 1; //18-11-2025
        if ($stDate!='') {
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   < '".$stDate."'";
            // add if condition @ 18-11-2025
            if (strtotime($stDate) > strtotime("2025-04-01")) {
                $dateFilt=" AND BS.BS_Date   < '".$stDate."' AND BS.BS_Date >= '2025-04-01'";
            } else if (strtotime($stDate) == strtotime("2025-04-01")) {
                $getOldbalance = 0;
            } 
        }

        $count=0;
        $this->BranchBSArray = array();  
        $sql="SELECT ".$fields." FROM bank_open_bals as BO 
                INNER JOIN bank_accounts as BA ON BO.BA_Id = BA.BA_Id  WHERE ".$filterOB;
        $res = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($res)) {
            $this->BranchBSArray[$count] = $row;
            $count++;       
        } 
        if ($getOldbalance == 1) { // add if condition @ 18-11-2025
            // below was old just add above if condition
            $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount ) AS IE , MH.MH_Type
                                    FROM  `balance_sheets` AS BS
                                    LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id 
                                    LEFT JOIN `bank_accounts` AS BA on BA.BA_Id = BS.BA_Id 
                                    WHERE IT.IT_Business = '0' AND  BS.BA_Id != 0 
                                    AND BS.BS_Status = 1
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 2
                                    AND BS.BS_PettyCashRefId = 0
                                    ".$dateFilt."
                                    AND ".$filter."
                                    GROUP BY MH.MH_Type
                                    ");
            
            while($row=mysqli_fetch_object($result)) {
                $this->BranchBSArray[$count] = $row;
                $count++;       
            }  
        }  
    }
    
    //---------------------------- BalanceSheet Bank Opening/Closing Balance PieChart -----------------------------//
    function ocPieChartBranchData($filt,$stDate='',$enDate='',$filter='') {			
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
        $this->BranchBSArray = array();
                                
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount ) AS IE , MH.MH_Type,  SUM(IF(IT.IT_Transfers = '1', BS.BS_Amount, '0' )) AS INE
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `bank_accounts` AS BA on BA.BA_Id = BS.BA_Id 
                                LEFT JOIN `locations` AS LC ON BA.LC_Id = LC.LC_Id 
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND MH.MH_Type IN ('1','2')
                                AND BS.BS_PettyCashRefId = 0
                                ".$dateFilt."
                                AND ".$filter."
                                AND BS.PM_Id = 2
                                GROUP BY MH.MH_Type
                                 ");
        while($row=mysqli_fetch_object($result)) {
            $this->BranchBSArray[$count] = $row;
            $count++;
        }	
    }
    
    //----------------------------------------- BalanceSheet IE Bar Chart ----------------------------------------//
    function ocBarChartBranchData($filt,$stDate='',$enDate='',$filter='') {			
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date >=  '".$stDate."'";
        }
        $count=0;
        $this->BranchBSArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount )  AS IE,IT.SH_Id,SH.SH_Name, MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id 
                                LEFT JOIN `bank_accounts` AS BA on BA.BA_Id = BS.BA_Id 
                                LEFT JOIN `locations` AS LC ON BA.LC_Id = LC.LC_Id 
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND MH.MH_Type IN ('1','2')
                                AND BS.PM_Id = 2
                                AND BS.BS_PettyCashRefId = 0
                                ".$dateFilt."
                                AND ".$filter."
                                GROUP BY IT.SH_Id
                                ORDER BY MH.MH_Type DESC,IE DESC 
                                 ");
        while($row=mysqli_fetch_object($result)) {
            $this->BranchBSArray[$count] = $row;
            $count++;
        }
    }
}