<?php
require_once("connection.php");
class BussRptClass {
    var $BusinessArray;
    
    
    //--------------------------------------Cash BalanceSheet Items ----------------------------------------//
    function reportBusinessData($filt,$stDate='',$enDate='',$filter,$filter_mask,$pos,$cnt) {		
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
        $this->BusinessArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date  , BS.BS_Amount, DS.DS_Description, TR.TR_Track, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name,US.US_FName, US.US_LName
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '1'
                                AND BS.BS_Status = 1
                                    AND MH.MH_Type IN ('1','2')
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND LC.".$filter."
                                ORDER BY BS.BS_Date   DESC
                                LIMIT ".$pos.",".$cnt
                                );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->BusinessArray[$count] = $row;
                $count++;
            }
        }
    }
    
    //-----------------------------------Cash BalanceSheet Items Count  ----------------------------------------//
    function reportBusinessDataCount($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '1'
                                AND BS.BS_Status = 1
                                    AND MH.MH_Type IN ('1','2')
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND LC.".$filter."
                                ORDER BY BS.BS_Date DESC
                                "
                                );
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        return $count=$row[0];
    }
    
     //--------------------------- BalanceSheet Cash Opening/Closing Balance PieChart ----------------------------//
    function reportBusinessSummary($filt,$stDate='',$enDate='',$filter='') {			
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
        $this->BusinessArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount ) AS IE , MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '1'
                                AND BS.BS_Status = 1
                                    AND MH.MH_Type IN ('1','2')
                                    ".$dateFilt."
                                    AND LC.".$filter."
                                GROUP BY MH.MH_Type
                                 ");
        
        $this->BusinessArray['BussRecv']     = 0;
        $this->BusinessArray['BussReturned'] = 0;
        
        while($row=mysqli_fetch_object($result)) {
//            $this->BusinessArray[$count] = $row;
            if($row->MH_Type=="1") {
                $this->BusinessArray['BussRecv'] += $row->IE;
            }elseif($row->MH_Type=="2") {
                $this->BusinessArray['BussReturned'] += $row->IE;
            }
            $count++;
        }
        
        $this->BusinessArray["CurrentPeriodOldStock"] = 0;
        $sql="SELECT SUM(OS_OpenBal) AS OS FROM stock_open_bals WHERE ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row = mysqli_fetch_object($result)) { 
            $this->BusinessArray['CurrentPeriodOldStock'] = $row->OS;
        } 
        
//        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount ) AS IE , MH.MH_Type
//                                FROM  `balance_sheets` AS BS
//                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
//                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
//                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
//                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
//                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
//                                WHERE IT.IT_Business = '1'
//                                    AND BS.BS_Status = 1
//                                    AND IT.IT_Id = 219
//                                    ".$dateFilt."
//                                    AND LC.".$filter."
//                                ");
//        
//        $this->BusinessArray["CurrentPeriodOldStock"] = 0;
//        
//        while($row=mysqli_fetch_object($result)) {
//            $this->BusinessArray["CurrentPeriodOldStock"] += $row->IE;
//            $count++;
//        }	
        
        if($stDate) {
            $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                    WHERE BS.BS_Status = 1 
                                        AND SH.SH_Track = 1
                                        AND IT.MH_Type IN ('1','2')
                                        AND BS.BS_Date < '".$stDate."'
                                        ".$filter_mask."
                                        AND LC.".$filter."
                                    GROUP BY IT.IT_Business, IT.MH_Type"
                                    );
            if($result){

                $this->BusinessArray['Old_Business'] = 0;
                $this->BusinessArray['Old_Income']   = 0;   
                $this->BusinessArray['Old_Expense']  = 0;   

                while($row = mysqli_fetch_object($result)) {
                    if($row->MH_Type=="1") {
                        if($row->IT_Business=="1") {
                            $this->BusinessArray['Old_Business'] += $row->BS_Amount;
                        }elseif($row->IT_Business=="0") {
                           $this->BusinessArray['Old_Income'] += $row->BS_Amount;
                        }
                    }else if($row->MH_Type=="2"){
                        if($row->IT_Business=="1"){
                            $this->BusinessArray['Old_Business'] = $this->BusinessArray['Old_Business'] - $row->BS_Amount;
                        }elseif($row->IT_Business=="0"){
                           $this->BusinessArray['Old_Expense'] += $row->BS_Amount;
                        }
                    }
                }
            }
        }
        
    }
    
    
    //--------------------------- BalanceSheet Cash Opening/Closing Balance Chart ---------------------------------//
    function ocBarChartCashData($filt,$stDate='',$enDate='',$filter='') {			
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
        $this->BusinessArray = array();        
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount )  AS IE,IT.SH_Id,SH.SH_Name, MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = 1 
                                AND BS.BS_Status = 1
                                    AND MH.MH_Type IN ('1','2')
                                    ".$dateFilt."
                                    AND LC.".$filter."
                                GROUP BY IT.SH_Id
                                ORDER BY MH.MH_Type DESC,IE DESC 
                                 ");
        while($row=mysqli_fetch_object($result)) {
            $this->BusinessArray[$count] = $row;
            $count++;
        }
    }
}
?>