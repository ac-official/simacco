<?php

require_once("connection.php");

class BankClass {

    var $BankArray;
    var $BankBranch;
    var $BankAccount;
    var $Cheque;
    var $BalArray;

    //----------------------------------------- All Banks ----------------------------------------//
    function viewBanks($filt = '') {
        $count = 0;
        $this->BankArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT * FROM banks " . $filt);
        while ($row = mysqli_fetch_object($result)) {
            $this->BankArray[$count] = $row;
            $count++;
        }
    }

    //----------------------------------------- Verify Bank ----------------------------------------//
    function verifyBank($BNKId) {
        $sql = 'SELECT COUNT(BNK_Id) FROM banks WHERE BNK_Name = "' . $this->Bank_Data['BNK_Name'] . '" AND BNK_Id!=' . $BNKId;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        $count = $row[0];
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    //----------------------------------------- New Bank ----------------------------------------//
    function newBank() {

        $sql = "INSERT INTO banks ( " . implode(', ', array_keys($this->Bank_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Bank_Data)) . "'" . ")";
        //return $sql;
        mysqli_query($GLOBALS['con'],$sql);
        return 'Bank Created Successfully';
    }

    //----------------------------------------- Update Bank ----------------------------------------//
    function updateBank($BNKId) {
        $BankData = '';
        foreach ($this->Bank_Data as $key => $value) {
            $BankData = $BankData . $key . "='" . $value . "', ";
        }
        $BankData = substr($BankData, 0, -2);
        $sql = "UPDATE banks SET $BankData WHERE BNK_Id=$BNKId";
        mysqli_query($GLOBALS['con'],$sql);
        return 'Bank Updated Successfully';
    }

    //----------------------------------------- All Bank_Branches ----------------------------------------//
    function viewBranch($cols, $filt = '') {
        $count = 0;
        $this->BranchArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT " . $cols . " FROM bank_branches " . $filt);
        while ($row = mysqli_fetch_object($result)) {
            $this->BranchArray[$count] = $row;
            $count++;
        }
    }

    function viewBranchGrid($filt = '') {
        $count = 0;
        $this->BranchArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BB.BB_Id,BB.BB_Name,BB.BB_Address,BB.BB_Comments,BB.BB_Status,BB.BNK_Id,BNK.BNK_Name FROM bank_branches AS BB,banks AS BNK " . $filt);
        while ($row = mysqli_fetch_object($result)) {
            $this->BranchArray[$count] = $row;
            $count++;
        }
    }

    //----------------------------------------- Verify Bank Branch ----------------------------------------//
    function verifyBranch($bbid,$ofid) {
        $sql = 'SELECT COUNT(BB_Id) FROM bank_branches WHERE BB_Name = "' . $this->Branch_Data['BB_Name'] . '" AND BNK_Id="' . $this->Branch_Data['BNK_Id'] . '"AND OF_Id='.$ofid.' AND BB_Id!=' . $bbid;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        $count = $row[0];
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    //----------------------------------------- New Bank ----------------------------------------//
    function newBranch() {

        $sql = "INSERT INTO bank_branches ( " . implode(', ', array_keys($this->Branch_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Branch_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        return 'Branch Created Successfully';
    }

    //----------------------------------------- Update Bank ----------------------------------------//
    function updateBranch($BBId) {
        $BranchData = '';
        foreach ($this->Branch_Data as $key => $value) {
            $BranchData = $BranchData . $key . "='" . $value . "', ";
        }
        $BranchData = substr($BranchData, 0, -2);
        $sql = "UPDATE bank_branches SET $BranchData WHERE BB_Id=$BBId";
        mysqli_query($GLOBALS['con'],$sql);
        return 'Branch Updated Successfully';
    }

    //----------------------------------------- New Account ----------------------------------------//
    function newAccount() {
       $sql = "INSERT INTO bank_accounts ( " . implode(', ', array_keys($this->Account_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Account_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        return 'Account Created Successfully';
    }

    //----------------------------------------- Update Account ----------------------------------------//
    function updateAccount($BA_Id) {
        $AccData = '';
        foreach ($this->Account_Data as $key => $value) {
            $AccData = $AccData . $key . "='" . $value . "', ";
        }
        $AccData = substr($AccData, 0, -2);
        $sql = "UPDATE bank_accounts SET $AccData WHERE BA_Id=$BA_Id";
        mysqli_query($GLOBALS['con'],$sql);
        return 'Account Updated Successfully';
    }

    //----------------------------------------- Verify Account----------------------------------------//
    function verifyAccount($OFId,$ba_id) {
       $sql = 'SELECT COUNT(BA_No) FROM bank_accounts WHERE BA_No = "' . $this->Account_Data['BA_No'] . '" AND OF_Id =' . $OFId ." AND BA_Id!=".$ba_id;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        $count = $row[0];
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }
    //--------------------------------------------Verify Disp Name-------------------------------------------//
    function verifyDispName($OFId,$ba_id) {
        $sql = 'SELECT COUNT(BA_DispName) FROM bank_accounts WHERE BA_DispName = "' . trim($this->Account_Data['BA_DispName']) . '" AND OF_Id =' . $OFId .' AND BA_Id!='.$ba_id;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        $count = $row[0];
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }
    //------------------------------------------View Account Details---------------------------------//
    function viewAccounts($filt = '') {
        $count = 0;
        $this->BankAccount = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BA.BA_Id,BA.BA_DispName,BA.BA_No,BA.BB_Id,BA.LC_Id,BA.BA_Status,BB.BB_Name,BNK.BNK_Id,BNK.BNK_Name,BNK.BNK_Abbr FROM bank_accounts AS BA,bank_branches AS BB,banks AS BNK " . $filt);
        while ($row = mysqli_fetch_object($result)) {
            $this->BankAccount[$count] = $row;
            $count++;
        }
    }

    function newCheque() {
       $sql = "INSERT INTO bank_cheque_books ( " . implode(', ', array_keys($this->Cheque_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Cheque_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        return mysqli_insert_id($GLOBALS['con']);
    }

    //----------------------------------------- Update Cheque ----------------------------------------//
    function updateCheque($BA_Id) {
        $ChqData = '';
        foreach ($this->Cheque_Data as $key => $value) {
            $ChqData = $ChqData . $key . "='" . $value . "', ";
        }
        $ChqData = substr($ChqData, 0, -2);
        $sql = "UPDATE bank_cheque_books SET $ChqData WHERE CHQ_Id=$BA_Id";
        mysqli_query($GLOBALS['con'],$sql);
        return 'Cheque Book Updated Successfully';
    }

    //----------------------------------------- Verify Cheque----------------------------------------//
    function verifyCheque($ChqId,$Ofid) {
        $sql = 'SELECT COUNT(CHQ_BookNo) FROM bank_cheque_books WHERE CHQ_BookNo = "' . $this->Cheque_Data['CHQ_BookNo'] . '" AND OF_Id='.$Ofid.' AND CHQ_Id!=' . $ChqId;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        $count = $row[0];
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }

    //------------------------------------------View Account Details---------------------------------//
    function viewCheque($filt = '') {
        $count = 0;
        $this->Cheque = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT CQ.CHQ_Id,CQ.CHQ_BookNo,CQ.CHQ_Firstleaf,CQ.CHQ_Lastleaf,CQ.CHQ_Status,BA.BA_Id,BA.BA_No,BA.BB_Id,BA.BA_Status,BB.BB_Name,BNK.BNK_Id,BNK.BNK_Name FROM bank_accounts AS BA,bank_branches AS BB,banks AS BNK,bank_cheque_books AS CQ " . $filt);

        while ($row = mysqli_fetch_object($result)) {
            $this->Cheque[$count] = $row;
            $count++;
        }
    }

    function newChqLeafs($bookid) {
        $start = $this->Cheque_Data['CHQ_Firstleaf'];
        $end = $this->Cheque_Data['CHQ_Lastleaf'];
        $len=strlen($end);
        $sql = "INSERT INTO bank_cheque_leafs (CL_Leaf,CHQ_Id,CL_Status,CL_Issued) VALUES";
        for ($i = $start; $i <= $end; $i++) {
            $sql.="('" . str_pad($i, $len, "0", STR_PAD_LEFT) . "'," . $bookid . ",1,0),";
        }
        $sql = substr($sql, 0, -1);        
        mysqli_query($GLOBALS['con'],$sql);
        return "Cheque Book Created";
    }
    function viewChqLeafs($filter){
        $count = 0;
        $this->Cheque_leaves = array();
        //$sql="SELECT * FROM bank_cheque_leafs WHERE ".$filter;
        $sql = "SELECT BCL.*,BS.IT_Id,BS.BS_PaidTo,BS.BS_Amount,IT.IT_Name"
                . " FROM bank_cheque_leafs AS BCL "
                . " LEFT JOIN balance_sheets AS BS ON BCL.CL_Id = BS.CHQ_Number "
                . " LEFT JOIN items AS IT ON BS.IT_Id = IT.IT_Id "
                . " WHERE ".$filter ;
        $result=  mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->Cheque_leaves[$count] = $row;
            $count++;
        }
    }
    function changeChqStatus($lvid,$stats){
        $sql="UPDATE bank_cheque_leafs SET CL_Status=".$stats." WHERE CL_Id=".$lvid;
        mysqli_query($GLOBALS['con'],$sql);
        echo "Cheque Leaf Status Updated";
    }
    function createBnkBalances() {      
       
      $sql = "INSERT INTO bank_open_bals ( " . implode(', ', array_keys($this->Bal_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Bal_Data)) . "'" . ")";
       mysqli_query($GLOBALS['con'],$sql);
    }    
    
    function getChqNumber($BA_Id, $filter){
        $count=0;
        $this->Cheque = array();                        
        $result=mysqli_query($GLOBALS['con'],"SELECT CL.CL_Id,CL.CL_Leaf FROM bank_cheque_leafs AS CL
                                    LEFT JOIN `bank_cheque_books` CB ON CB.CHQ_Id = CL.CHQ_Id
                                        WHERE CB.BA_Id = ".$BA_Id." AND CL.CL_Status = 1 AND CB.CHQ_Status = 1 ".$filter);
               //SELECT CL.CL_Id,CL.CL_Leaf FROM bank_cheque_leafs AS CL,bank_cheque_books AS CB  WHERE CB.BA_Id = ".$BA_Id." AND CL.CL_Status = 1 AND CB.CHQ_Id = CL.CHQ_Id ".$filter);
        while($row=mysqli_fetch_object($result)) {
                $this->Cheque[$count]=$row;
                $count++;
        }	
    }

    //----------------------------------------- Verify Cheque Number----------------------------------------//
    function verifyChequeNumber($ChqNum,$BA_Id){
        //$sql = 'SELECT COUNT(CL_Id) FROM bank_cheque_leafs AS CL,bank_cheque_books AS CB WHERE CL_Id = "'.$ChqNum.'" AND CB.BA_Id = "'.$BA_Id.'" AND CL.CL_Status = 1 AND CB.CHQ_Id = CL.CHQ_Id';
        $sql = 'SELECT COUNT(CL_Id) FROM bank_cheque_leafs AS CL
                    LEFT JOIN bank_cheque_books AS CB ON CB.CHQ_Id = CL.CHQ_Id
                        WHERE CL.CL_Id = "'.$ChqNum.'" AND CB.BA_Id = "'.$BA_Id.'" AND CL.CL_Status = 1';
        $result = mysqli_query($GLOBALS['con'],$sql);                     
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        $count=$row[0];
        if($count == 0){
                return 0;
        } else {
                return 1;	
        }
    }
    //----------------------------------------- Verify Cheque Number IN Accounts Entry----------------------------------------//
    function verifyBSChQ($ChqNum,$BA_Id,$BS_Id){
        $sql = 'SELECT COUNT(CL_Id) FROM bank_cheque_leafs AS CL
                    LEFT JOIN bank_cheque_books AS CB ON CB.CHQ_Id = CL.CHQ_Id
                    LEFT JOIN balance_sheets AS BS ON BS.CHQ_Number = CL.CL_Id
                        WHERE CL.CL_Id = "'.$ChqNum.'" AND CB.BA_Id = "'.$BA_Id.'" AND BS.BS_Id = "'.$BS_Id.'" ';
        $result = mysqli_query($GLOBALS['con'],$sql);                     
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        $count=$row[0];
        if($count == 0){
                return 0;
        } else {
                return 1;	
        }
    }
    
    //-----------------------------Show Cheque Details-----------------------------//
    function viewChequeDetails($filter) {
        $count = 0;
       /* $sql = "SELECT BB.BB_Name,BNK.BNK_Name,CONCAT(US.US_FName, ' ', US.US_LName) AS CreatedBy,CONCAT(UA.US_FName, ' ', UA.US_LName) AS PaidByUser,"
                . "BS.BS_PaidTo,BS.BS_PaidDate,BS_Remarks,BS.BS_Amount,MH.MH_Type "
                . " FROM `bank_cheque_leafs` AS BCL "
                . " LEFT JOIN `balance_sheets` AS BS ON BCL.CL_Id = BS.CHQ_Number "
                . " LEFT JOIN `items` AS IT ON BS.IT_Id = IT.IT_Id "
                . " LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id "
                . " LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id "
                . " LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id "
                . " LEFT JOIN `banks` AS BNK ON BS.BNK_Id = BNK.BNK_Id "
                . " LEFT JOIN `bank_branches` AS BB ON BS.BB_Id = BB.BB_Id "
                . " LEFT JOIN `users_auth` AS UA ON BS.BS_PaidBy = UA.US_Id "
                . " WHERE ".$filter;*/
        $sql = "SELECT BS.BS_VoucherNo,BS.BS_Amount,IT.IT_Name,BS.LC_Id,BS.BNK_Id,BS.BB_Id,"
                . "BS.BS_PaidTo,BS.BS_PaidBy,BS.BS_PayTime,BS.BS_Purpose,"
                . "BS.BS_AprovlGvnBy,BS.BS_AprovlTknBy,BS.BS_AprovdDate,"
                . "BS.BS_Remarks,BS.US_Id,DATE_FORMAT(BS.BS_Date, '%Y-%m-%d') AS BS_Date,MH.MH_Type "
                . " FROM `bank_cheque_leafs` AS BCL "
                . " LEFT JOIN `balance_sheets` AS BS ON BCL.CL_Id = BS.CHQ_Number "
                . " LEFT JOIN `items` AS IT ON BS.IT_Id = IT.IT_Id "
                . " LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id "
                . " LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id "
                . " WHERE ".$filter;
        $result=  mysqli_query($GLOBALS['con'],$sql);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }
    
    function getChequeDetails($key,$value) {
        
        switch($key) {
            case 'US_Id':
            case 'BS_PaidBy':
            case 'BS_AprovlGvnBy':
            case 'BS_AprovlTknBy':
                $field = "CONCAT(US_FName, ' ', US_LName)";
                $table = "users_auth";
                $filter = " US_Id = ".$value;
                break;
            
            case 'LC_Id':
                $field = "LC_Name";
                $table = "locations";
                $filter = " LC_Id = ".$value;
                break;
            
            case 'BNK_Id':
                $field = "BNK_Name";
                $table = "banks";
                $filter = " BNK_Id = ".$value;
                break;
            
            case 'BB_Id':
                $field = "BB_Name";
                $table = "bank_branches";
                $filter = " BB_Id = ".$value;
                break;
            
        }
        $sql = mysqli_query($GLOBALS['con'],"SELECT $field FROM $table WHERE $filter");
        $result = mysqli_fetch_array($sql,MYSQLI_ASSOC);
        return $result[$field];
    }
    function viewCompnySalBankName($ofid){
        $this->BankNames=array();
        $sql="SELECT Sal_BnkId,Sal_BnkName FROM salary_bank_names WHERE OF_Id=".$ofid;
        $result=mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
                $this->BankNames[$count]=$row;
                $count++;
        }
    }
    
}

?>