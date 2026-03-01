<?php
require_once("connection.php");
require_once("UserClass.php");

class BankBSClass {
    var $BankBSArray;
    
    
    //---------------------------------------- Bank BalanceSheet Items ----------------------------------------//
    function reportBankBSData($filt,$stDate='',$enDate='',$filter,$filter_mask,$pos,$cnt) {		
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
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id,CONCAT(US.US_FName,' ',US_LName) AS US_Name , BS.BS_Id, BS.BS_Date , BS.BS_Amount,BS.BS_PettyCashAmt,BS.CHQ_Number, DS.DS_Description, TR.TR_Track, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `bank_accounts` AS BA ON BS.BA_Id=BA.BA_Id 
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id                                
                                WHERE IT.IT_Business = '0'
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 2 
                                    AND BS.BS_Status = 2
                                    AND BS.BS_PettyCashRefId = 0
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND ".$filter."
                                ORDER BY BS.BS_Date DESC
                                LIMIT ".$pos.",".$cnt
                                );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->BankBSArray[$count] = $row;
                $count++;
            }
        }
    }
    //---------------------------------- Bank BalanceSheet Items Count ----------------------------------------//
    function reportBankBSDataCount($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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
                                LEFT JOIN `bank_accounts` AS BA ON BS.BA_Id=BA.BA_Id 
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id                                
                                WHERE IT.IT_Business = '0'
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 2 
                                    AND BS.BS_Status = 2
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
 
    //--------------------------------- Opening Balance of a Company/Branch  ----------------------------------------//
    function getBankOpeningBalance($stDate='',$enDate='', $fields, $filterOB, $filter)
    {
        $dateFilt = '';
        
        if($stDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   < '".$stDate."'";
        }

        $count=0;
        $this->BankBSArray = array();  
        $sql="SELECT ".$fields." FROM bank_open_bals WHERE ".$filterOB;
        $res = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($res)) {
            $this->BankBSArray[$count] = $row;
            $count++;       
        } 

        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND MH.MH_Type IN ('1','2')
                                AND BS.PM_Id = 2
                                ".$dateFilt."
                                AND ".$filter."
                                GROUP BY MH.MH_Type
                                ");
        
        while($row=mysqli_fetch_object($result)) {
            $this->BankBSArray[$count] = $row;
            $count++;       
        }    
    }
    
    //--------------------------------- Opening Balance of a Branch  ----------------------------------------//
    function getBranchBankOpeningBalance($stDate='',$enDate='', $fields, $filterOB, $filter)
    {
        $dateFilt = '';
        
        if($stDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   < '".$stDate."'";
        }

        $count=0;
        $this->BankBSArray = array();  
        
        //--------------- Starting Bank Opening Balance ---------------------//
        $sql = "SELECT ".$fields."
                    FROM bank_open_bals AS OB
                        LEFT JOIN bank_accounts AS BA ON BA.BA_Id = OB.BA_Id
                            WHERE BA.".$filterOB;
        
        $res = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($res)) {
            $this->BankBSArray[$count] = $row;
            $count++;       
        }  
        
        //--------------- Current Date ( From Date ) Bank Opening Balance (Inc/Exp of Previous) ---------------------//
        $result = mysqli_query($GLOBALS['con'],"SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type, LC.LC_Id, BA.BA_Id
                                FROM balance_sheets AS BS 
                                    LEFT JOIN bank_accounts AS BA ON BS.BA_Id = BA.BA_Id
                                    LEFT JOIN locations AS LC ON LC.LC_Id = BA.LC_Id
                                    LEFT JOIN items AS IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN sub_heads AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN main_heads AS MH ON SH.MH_Id = MH.MH_Id
                                     WHERE IT.IT_Business = '0'
                                        AND BS.BS_Status = 1
                                        AND MH.MH_Type IN ('1','2')
                                        AND BS.PM_Id = 2 
                                        AND BS.BS_PettyCashRefId = 0
                                        AND BA.".$filter."
                                        ".$dateFilt."
                                            GROUP BY MH.MH_Type");
        
        while($row=mysqli_fetch_object($result)) {
            $this->BankBSArray[$count] = $row;
            $count++;       
        }    
    }
    
    //---------------------------- BalanceSheet Bank Opening/Closing Balance PieChart -----------------------------//
    function ocPieChartBankData($filt,$stDate='',$enDate='',$filter='') {			
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
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
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
            $this->BankBSArray[$count] = $row;
            $count++;
        }	
    }
    
    //----------------------------------------- BalanceSheet IE Bar Chart ----------------------------------------//
    function ocBarChartBankData($filt,$stDate='',$enDate='',$filter='') {			
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
        $this->BankBSArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) )  AS IE,IT.SH_Id,SH.SH_Name, MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
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
            $this->BankBSArray[$count] = $row;
            $count++;
        }
    }
    //------------------------------------------View Account Details---------------------------------//
    function viewBankAccounts($OFId,$filter) {
        $count = 0;
        $this->BankBSArray = array();

        $sql = "SELECT BA.BA_Id, BA.BA_No, BB.BB_Name,BNK.BNK_Abbr,BA.BA_DispName, BA.BA_Status 
                        FROM  `bank_accounts` AS BA
                            LEFT JOIN  `bank_branches` BB ON BA.BB_Id = BB.BB_Id
                            LEFT JOIN  `banks` AS BNK ON BB.BNK_Id = BNK.BNK_Id
                            WHERE BA.OF_Id=".$OFId . $filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->BankBSArray[$count] = $row;
            $count++;
        }
    }
    function viewBnkOpeningBalances($ofid) {
        $sql = "SELECT BnkOB.BnkOB_Id,BA.BA_No,BB.BB_Name,BNK.BNK_Abbr,BnkOB.BnkOB_OpenBal,BnkOB.BnkOB_Status,BnkOB.BnkOB_CDate FROM bank_open_bals AS BnkOB,bank_accounts AS BA,bank_branches as BB,banks as BNK WHERE BNK.BNK_Id=BB.BNK_Id AND BB.BB_Id=BA.BB_Id AND BnkOB.BA_Id=BA.BA_Id AND BB.BB_Status=1 AND BA.BA_Status=1 AND BA.OF_Id=" . $ofid;
        $count = 0;
        $this->BalArray = array();
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->BalArray[$count] = $row;
            $count++;
        }
    }

    function updateBnkOpenBal($obid) {
        $BalData = '';
        foreach ($this->Bal_Data as $key => $value) {
            $BalData = $BalData . $key . "='" . $value . "', ";
        }
        $BalData = substr($BalData, 0, -2);
        $sql = "UPDATE bank_open_bals SET $BalData WHERE BnkOB_Id=" . $obid;
        mysqli_query($GLOBALS['con'],$sql);
        return 'Opening Balance Updated Successfully';
    }
}
?>