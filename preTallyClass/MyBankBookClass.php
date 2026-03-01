<?php
require_once("connection.php");
class MyBankBookClass {
    function reportBankBookBSData($stDate='',$enDate='',$filter,$filter_mask,$pos,$cnt) {		
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
        } elseif($stDate=='' && $enDate!=''){
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   =" AND BS.BS_Date   <=  '".$enDate."'";
        } elseif($stDate!='' && $enDate==''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $dateFilt   =" AND BS.BS_Date   >=  '".$stDate."'";
        }

        $count          = 0;
        $this->BankBSArray = array();
        
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date , BS.BS_Amount,BS.BS_PettyCashAmt,BS.CHQ_Number, DS.DS_Description, BS_LC.LC_Name AS BS_LCName,
                                TR.TR_Track, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name, BS.BS_CDate, BS.BS_Status , US.US_FName,US.US_LName , BA.BA_DispName AS bank_acc_name, acb.status AS bank_con_status 
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `bank_accounts` AS BA ON BS.BA_Id=BA.BA_Id 
                                LEFT JOIN `locations` AS LC ON BA.LC_Id = LC.LC_Id   
                                LEFT JOIN `locations` AS BS_LC ON BS.LC_Id = BS_LC.LC_Id  
                                LEFT JOIN `acc_bank_consider` AS acb ON acb.bs_id = BS.BS_Id  
                                WHERE IT.IT_Business = '0'
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 2 
                                    AND BS.BS_Status != 0 
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
    function reportBankBookDataCount($stDate='',$enDate='',$filter,$filter_mask) {		
        $dateFilt = '';
        
        if($stDate != '' && $enDate != ''){
            $stDate         = date("Y-m-d", strtotime($stDate));
            $enDate         = date("Y-m-d", strtotime($enDate));
            $dateFilt       = " AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
        } elseif($stDate == '' && $enDate != ''){
            $enDate         = date("Y-m-d", strtotime($enDate));
            $dateFilt       =   " AND BS.BS_Date <=  '".$enDate."'";
        } elseif($stDate != '' && $enDate == ''){
            $stDate         = date("Y-m-d", strtotime($stDate));
            $dateFilt       = " AND BS.BS_Date >=  '".$stDate."'";
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
                                LEFT JOIN `locations` AS LC ON BA.LC_Id = LC.LC_Id   
                                LEFT JOIN `locations` AS BS_LC ON BS.LC_Id = BS_LC.LC_Id                                
                                WHERE IT.IT_Business = '0'
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 2 
                                    AND BS.BS_Status != 0 
                                    AND BS.BS_PettyCashRefId = 0 
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND ".$filter."
                                ORDER BY BS.BS_Date DESC " );
        $row            = mysqli_fetch_array($result,MYSQLI_NUM);
        return $count   = $row[0];
    }
}
?>