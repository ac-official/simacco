<?php
require_once("connection.php");

class BalanceSheetClass
{
	var $BalanceSheetArray;
        var $BalanceSheetUnitArray;
	var $SearchArray;	
	//----------------------------------------- BalanceSheet Items ----------------------------------------//
	function viewBalanceSheetItems($SHID='')
	{			
            $count=0;
            $this->BalanceSheetArray = array();// DATE_FORMAT(BS.BS_Date,'%d/%m/%Y') AS BS_Date
            $result=mysqli_query($GLOBALS['con'],"SELECT IT.IT_Name, BS.BS_Amount, BS.BS_Id, SH.SH_Id,BS.BS_Date FROM balance_sheets AS BS, items AS IT, sub_heads AS SH WHERE BS.IT_Id=IT.IT_Id AND IT.SH_Id=SH.SH_Id AND BS_Complete=0 AND BS.BS_Status = 1 AND SH.SH_Id = ".$SHID);
            while($row=mysqli_fetch_object($result)) {
                    $this->BalanceSheetArray[$count]=$row;
                    $count++;
            }	
	}
        
        //----------------------------------------- BalanceSheet Items Count ----------------------------------------//
	function viewBalanceSheetItemsCount($filt='')
	{			
            $count=0;
            $this->BalanceSheetArray = array();
            $result = mysqli_query($GLOBALS['con'],"SELECT SH.SH_Id, SH.SH_Name, COUNT(IT.IT_Id) AS IT_Count FROM balance_sheets AS BS, items AS IT, sub_heads AS SH WHERE BS.IT_Id=IT.IT_Id AND IT.SH_Id=SH.SH_Id AND BS_Complete=0 AND BS.BS_Status = 1 GROUP BY SH.SH_Id ");
            //$result = mysqli_query($GLOBALS['con'],"SELECT IT_Id AS SH_Name, BS_Amount AS IT_Count FROM balance_sheets");
            //SELECT SH.SH_Name, COUNT(IT.IT_Id) FROM balance_sheets AS BS, items AS IT, sub_heads AS SH WHERE BS.IT_Id=IT.IT_Id AND IT.SH_Id=SH.SH_Id GROUP BY SH.SH_Id
            while($row=mysqli_fetch_object($result)) {
                $this->BalanceSheetArray[$count] = $row;
                $count++;
            }	
	}
	//----------------------------------------- New Item ---------------------------------------------------------//
	function newBalanceSheetItem(){
		
		$sql = "INSERT INTO balance_sheets ( " . implode(', ',array_keys($this->BL_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->BL_Data)) . "'" . ")";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Balance Sheet Item Created Successfully';

	}
	//----------------------------------------- Update Item ----------------------------------------//
	function updateBalanceSheet($BSId){
		$BSData = '';
		foreach ($this->BS_Data as $key=>$value){ 
			$BSData = $BSData .$key ."='".$value."', ";
		}
		$BSData = substr($BSData, 0, -2);
		$sql = "UPDATE balance_sheets SET $BSData WHERE BS_Id=$BSId";
		mysqli_query($GLOBALS['con'],$sql);                
		return 'Accounts Entry Updated Successfully';
	}
        //----------------------------------------- Update Item ----------------------------------------//    
        function updateChequeStatus(){
            $sql = "UPDATE bank_cheque_leafs SET CL_Status = ".$this->CL_Data['CL_Status']." WHERE CL_Id =".$this->CL_Data['CL_Id'];
            mysqli_query($GLOBALS['con'],$sql);
        }
        //---------------------------------------Check for Already exist Item ---------------------------//
        function verifyBalSheet($BS_Id)
	{			
		$sql = 'SELECT COUNT(BS_Name) FROM balance_sheets WHERE BS_Name = "'.$this->BL_Update_Data['BS_Name'].'"  AND BS_Id != '.$BS_Id; 
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}	
	}
	//--------------------------------------- Updating Balance Sheet item entries ---------------------------//        
        function updateBalSheet($BS_Id){
            $BSData = '';
            
            if($this->BL_Update_Data['CHQ_Number']!= ''){
                $sql = "UPDATE bank_cheque_leafs SET CL_Status = 2 WHERE CL_Id=".$this->BL_Update_Data['CHQ_Number'];
                mysqli_query($GLOBALS['con'],$sql);
            } 
            foreach ($this->BL_Update_Data as $key=>$value){ 
                    $BSData = $BSData .$key ."='".$value."', ";
            }
            $BSData = substr($BSData, 0, -2);
           $sql = "UPDATE balance_sheets SET $BSData WHERE BS_Id=$BS_Id"; 		
            mysqli_query($GLOBALS['con'],$sql);
            
            if($this->BL_Update_Data['BS_PettyCashRefId']!= 0){
                $sql = "UPDATE balance_sheets BS LEFT JOIN (
                            SELECT  IFNULL( ( SELECT SUM(BS_Amount) AS Amount FROM balance_sheets WHERE BS_PettyCashRefId = ".$this->BL_Update_Data['BS_PettyCashRefId']."  AND BS_Status = 1 ), 0 ) AS Amount ) BS1 ON BS.BS_Id = ".$this->BL_Update_Data['BS_PettyCashRefId']."
                                SET BS.BS_PettyCashAmt = BS.BS_Amount - BS1.Amount 
                                    WHERE BS.BS_Id = ".$this->BL_Update_Data['BS_PettyCashRefId'];
                mysqli_query($GLOBALS['con'],$sql);
            } 
            
            return 'Accounts Entry Item Details Updated Successfully';
        }
        
        //------------------------ Updating Expense Control for Balance Sheet entries --------------------// 
        function updateExpCntl($filter){
                
            $sql = "UPDATE balance_sheets SET 
                        BS_MinAmount ='".$this->BL_Update_Data['BS_MinAmount']."' , 
                        BS_MaxAmount = '".$this->BL_Update_Data['BS_MaxAmount']."' ,
                        BS_MDate = NOW()
                            WHERE ".$filter; 		
            mysqli_query($GLOBALS['con'],$sql);
            
        }
        
        //---------------------------- Updating Expense Control for Descriptions -----------------------// 
        function updateDescExpCntl($DSId){
            
            $sql = "UPDATE descriptions SET 
                            DS_MinAmount = '".$this->BL_Update_Data['BS_MinAmount']."' , 
                            DS_MaxAmount = '".$this->BL_Update_Data['BS_MaxAmount']."' ,
                            DS_MDate = NOW()
                                WHERE DS_Id = ".$DSId;
            mysqli_query($GLOBALS['con'],$sql);
            
        }
        
        
        //--------------------------------------- Updating Petty Cash Amount on edit ---------------------------//     
        function updatePettyCashAmount($BS_Id){
            $sql = "UPDATE balance_sheets BS LEFT JOIN (
                        SELECT  IFNULL( ( SELECT SUM(BS_Amount) AS Amount FROM balance_sheets WHERE BS_PettyCashRefId = $BS_Id AND BS_Status = 1), 0 ) AS Amount ) BS1 ON BS.BS_Id = $BS_Id
                            SET BS.BS_PettyCashAmt = BS.BS_Amount - BS1.Amount 
                                WHERE BS.BS_Id = $BS_Id";

            //UPDATE balance_sheets SET BS_Amount = BS_PettyCashAmt - SUM(BS_Amount)  WHERE BS_Id=".$BS_Id;
            mysqli_query($GLOBALS['con'],$sql);
        }
        
        function updatePettyCashBSEntries($BS_Id){
            $sql = "UPDATE balance_sheets BS LEFT JOIN (
                        SELECT  IFNULL( ( SELECT SUM(BS_Amount) AS Amount FROM balance_sheets WHERE BS_PettyCashRefId = $BS_Id AND BS_Id != $BS_Id AND BS_Status = 1), 0 ) AS Amount ) BS1 ON BS.BS_Id = $BS_Id
                            SET BS.BS_PettyCashAmt = BS.BS_Amount - BS1.Amount 
                                WHERE BS.BS_Id = $BS_Id";

            mysqli_query($GLOBALS['con'],$sql);
        }
        function updatePettyCashRefrenceEntries($BS_Id){
            $sql = "UPDATE balance_sheets
                            SET BS_PettyCashRefId = 0
                                WHERE BS_PettyCashRefId = $BS_Id";

            mysqli_query($GLOBALS['con'],$sql);
        }
        //--------------------------------------- Edit Petty Cash Amount on edit ---------------------------//     
        function verifyPettyCash($BS_Amount,$BS_Id){
            $sql = "SELECT SUM(BS_Amount) AS Amount FROM balance_sheets WHERE BS_PettyCashRefId =". $BS_Id;
            $result = mysqli_query($GLOBALS['con'],$sql);                     
            $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['Amount'];
            
        }
        function viewUnits(){
            $count=0;
            $this->BalanceSheetUnitArray = array();
            $result=mysqli_query($GLOBALS['con'],"SELECT * FROM units");
            while($row=mysqli_fetch_object($result)) {
                    $this->BalanceSheetUnitArray[$count]=$row;
                    $count++;
            }	
        }
        function viewPaymentModes(){
            $count=0;
            $this->BalanceSheetArray = array();
            $result=  mysqli_query($GLOBALS['con'],"SELECT * FROM payment_modes");
            while($row=  mysqli_fetch_object($result)){
                $this->BalanceSheetArray[$count]=$row;
                $count++;
            }
        }
        function viewUser($fields, $tbls, $key, $filter){
            $count=0;
            $this->BalanceSheetArray = array();            
            $result=  mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM ".$tbls." Where CONCAT(US_FName, ' ', US_LName) like '".mysqli_real_escape_string($GLOBALS['con'],ucfirst($key))."%' AND ".$filter." Order By US_FName,US_LName");
            //$result=  mysqli_query($GLOBALS['con'],"SELECT US_Id,US_FName,US_LName FROM users_auth");
            while($row=  mysqli_fetch_object($result)){
                $this->BalanceSheetArray[$count]=$row;
                $count++;
            }
        }
        //------------------------------- View all Balance Sheet Items added -------------------------------------------//
        function  getBalanceSheetItems($US_Id){
            $count=0; 
            $this->BalanceSheetArray= array();
//            $result=mysqli_query($GLOBALS['con'],"SELECT IT.IT_Name,IT.SH_Id, BS.BS_Id, BS.BS_Amount, BS.BS_Description, DATE_FORMAT( BS.BS_Date, '%d/%m/%Y' ) AS BS_Date, DS_Description
//                                    FROM balance_sheets AS BS, items AS IT , descriptions as DS
//                                        WHERE BS.IT_Id = IT.IT_Id AND BS.BS_Description = DS.DS_Id AND BS.BS_Complete =0 AND BS.US_Id=".$US_Id." 
//                                            ORDER BY BS.BS_Id");
//            echo "SELECT IT.IT_Name, SH.SH_Id, BS.BS_Id, BS.BS_Amount, BS.BS_Description, DATE_FORMAT( BS.BS_Date, '%d/%m/%Y' ) AS BS_Date, DS_Description
//                                    FROM balance_sheets AS BS, items AS IT , descriptions as DS,sub_heads AS SH 
//                                        WHERE BS.IT_Id = IT.IT_Id AND BS.BS_Description = DS.DS_Id AND IT.SH_Id=SH.SH_Id AND BS.BS_Complete =0 AND BS.US_Id=".$US_Id." 
//                                            ORDER BY BS.BS_Id";
             $result=mysqli_query($GLOBALS['con'],"SELECT IT.IT_Name, IT.SH_Id, IT.MH_Type,IT.IT_Status,BS.BS_Id, BS.BS_Amount, BS.BS_Description, DATE_FORMAT( BS.BS_Date, '%d/%m/%Y' ) AS BS_Date, DATE_FORMAT( BS.BS_CDate, '%d/%m/%Y' ) AS BS_CDate, DS.DS_Description, TR.TR_Track
                                    FROM balance_sheets AS BS
                                        LEFT JOIN items IT ON BS.IT_Id = IT.IT_Id 
                                        LEFT JOIN descriptions DS ON BS.BS_Description =DS.DS_Id
                                        LEFT JOIN tracks TR ON BS.TR_Id = TR.TR_Id
                                        WHERE BS.BS_Status IN (1,2) AND  BS.BS_Complete =0 AND BS.US_Id=".$US_Id." 
                                        ORDER BY BS.BS_Id");
            
            while ($row=  mysqli_fetch_object($result)){
                $this->BalanceSheetArray[$count]=$row;
                $count++;
            }
           
        } 
        //------------------------------------ BS Item Details -------------------------//
        function getBSItemValue($BS_Id){
            // other branch related fields added in selects (IT.IT_OtherUser, BS.BS_BranchTo) At 30-05-2025 
            $count=0;     
            $this->BalanceSheetArray=array();
            $result= mysqli_query($GLOBALS['con'],"SELECT BS.BS_Amount,BS.BS_Description,BS.IT_Id, BS.BS_VoucherNo,BS.LC_Id,BS.PM_Id, BS.BS_Date,BS.BS_PayTime,BS.BS_PaidBy,BS.BS_PettyCashRefId,BS.BS_IEByLC,BS.BS_IEByUS ,
                                    BS.TR_Id,BS.BS_PaidDate,BS.BNK_Id,BS.BA_Id,BS.BS_PayType,BS.BB_Id,BS.CHQ_Number,BS.BS_Transaction,BS.BS_PayersBank,BS.BS_PayersChQ,BS.BS_PrchsdFor,
                                    DS.DS_Description , IT.IT_PettyCash, IT.IT_Status, IT.MH_Type, IT.IT_OtherUser, BS.BS_BranchTo 
                                        FROM balance_sheets AS BS 
                                        LEFT JOIN descriptions as DS ON BS.BS_Description  = DS.DS_Id 
                                        LEFT JOIN items as IT ON BS.IT_Id = IT.IT_Id
                                        LEFT JOIN  tracks as TR ON  BS.TR_Id = TR.TR_Id
                                            WHERE  BS.BS_Status IN (1,2) AND BS.BS_Id=".$BS_Id);
            //BS1.BS_Amount AS PettyCashAmount ,   LEFT JOIN balance_sheets AS BS1 ON BS.BS_PettyCashRefId  = BS1.BS_Id
            while($row=mysqli_fetch_object($result)) {
                    $this->BalanceSheetArray[$count]=$row;
                    $count++;
            }
        }
        
        //---------------------------------------- Saved Balance Sheet Item Details -----------------------//
        function  getSavedBSItemList($US_Id,$key){
            if($key=="week"){$sortkey="7 Day";}
            else if($key=="mnt"){$sortkey="1 Month";}
            else if($key=="year"){$sortkey="1 Year";}
            $count=0; 
            $this->BalanceSheetArray= array();
            $result=mysqli_query($GLOBALS['con'],"SELECT IT.IT_Name, SH.SH_Id, BS.BS_Id, BS.BS_Amount, BS.BS_Description, DATE_FORMAT( BS.BS_Date, '%d/%m/%Y' ) AS BS_Date, DS_Description
                                    FROM balance_sheets AS BS, items AS IT , descriptions as DS,sub_heads AS SH 
                                        WHERE BS.IT_Id = IT.IT_Id AND BS.BS_Description =DS.DS_Id AND IT.SH_Id=SH.SH_Id AND BS.BS_Complete =1 AND BS.US_Id=".$US_Id." AND BS.BS_CDate AND BS.BS_Status = 1 BETWEEN DATE_SUB( CURDATE( ) ,INTERVAL ".$sortkey. " ) AND CURDATE( )
                                            ORDER BY BS.BS_MDate Desc");
            while ($row=  mysqli_fetch_object($result)){
                $this->BalanceSheetArray[$count]=$row;
                $count++;
            }
           
        }
        
//---------------------------------------- List Saved Balance Sheet Items ----------------------------------------------//
        function  viewSavedBSItems($BSID=''){
            $count=0;
            $this->BalanceSheetArray= array();
            $result=mysqli_query($GLOBALS['con'],"SELECT * 
                                    FROM balance_sheets 
                                        WHERE BS_Complete =1 AND BS_Id=".$BSID."
                                            ORDER BY BS_Id");
            while ($row=  mysqli_fetch_object($result)){
                $this->BalanceSheetArray[$count]=$row;
                $count++;
            }
           
        }
//---------------------------------------- Balance Sheet Items Popup----------------------------------------------//
        function  viewBalSheetPopup($BSID=''){
            $count=0;
            $this->BalanceSheetArray= array();
            $result=mysqli_query($GLOBALS['con'],"SELECT * 
                                    FROM balance_sheets 
                                        WHERE BS_Id=".$BSID."
                                            ORDER BY BS_Id");
            while ($row=  mysqli_fetch_object($result)){
                $this->BalanceSheetArray[$count]=$row;
                $count++;
            }
        }
        //---------------------------------------- Balance Sheet Items Popup----------------------------------------------//
        function  viewBalSheetDetails($BSID=''){
            $count=0;
            $this->BalanceSheetArray= array();
            $sql = "SELECT BS.* ,DS.DS_Description,IT.IT_Name,IT.IT_PettyCash,IT.IT_Status, IT.MH_Type
                        FROM balance_sheets AS BS
                            LEFT JOIN descriptions as DS ON BS.BS_Description  = DS.DS_Id 
                            LEFT JOIN items as IT ON BS.IT_Id = IT.IT_Id
                            LEFT JOIN  tracks as TR ON  BS.TR_Id = TR.TR_Id 
                                WHERE ( BS.BS_Status = 1 OR BS.BS_Status = 2 ) AND BS.BS_Id=".$BSID;
            $result=mysqli_query($GLOBALS['con'],$sql);
            while ($row=  mysqli_fetch_object($result)){
                $this->BalanceSheetArray[$count]=$row;
                $count++;
            }
        }
        //----------------------------------------- Swap BalanceSheet Items ----------------------------------------//
	function updateBalanceSheetItems($ITId,$ITId_Old){
		
		$sql = "UPDATE balance_sheets SET IT_Id = ".$ITId."  WHERE IT_Id =".$ITId_Old;
		mysqli_query($GLOBALS['con'],$sql);
                
		mysqli_query($GLOBALS['con'],"UPDATE descriptions SET IT_Id = ".$ITId."  WHERE IT_Id =".$ITId_Old);
                
                mysqli_query($GLOBALS['con'],"UPDATE items SET IT_Status = 4  WHERE IT_Id =".$ITId_Old);
                
		return 'Accounts Entry Item Updated Successfully';
	}
        
        //----------------------------------------- Swap BalanceSheet Descriptions ----------------------------------------//
	function updateBalanceSheetDescriptions($ITId,$ITId_Old){
		
		$sql = "UPDATE balance_sheets SET BS_Description = ".$ITId."  WHERE BS_Description =".$ITId_Old;
		mysqli_query($GLOBALS['con'],$sql);
//                echo "UPDATE descriptions SET IT_Id = ".$ITId."  WHERE IT_Id =".$ITId_Old;
//		mysqli_query($GLOBALS['con'],"UPDATE descriptions SET IT_Id = ".$ITId."  WHERE IT_Id =".$ITId_Old);
                
		return 'Accounts Entry Updated Successfully';
	}
        
        //---------------------------------------- Get Name of Combo Value BSForm----------------------------------------------//
        function fetchValueById($con_id,$con_label,$field_name,$db_name){
           $sql="SELECT ".$field_name." FROM ".$db_name." WHERE ".$con_label."=".$con_id;
            $result=  mysqli_query($GLOBALS['con'],$sql); 
            if($result){
                $row= mysqli_fetch_array($result,MYSQLI_NUM);
                if($con_label=='US_Id'){
                    $rW=$row[0].' '.$row[1]; return $rW;
                }else
                    return $row[0];
            }
        }
        
        //---------------------------------------- Chk for of Combo Id ----------------------------------------------//
        function getColNamebyId($sw_id,$pass_id)
        {   $get_name="";
            switch ($sw_id)
            {
                case 'UT_Id':
                    $get_name= $this->fetchValueById($pass_id, 'UT_Id', 'UT_Name', 'units');
                    break;
                case 'PM_Id':
//                    $get_name= $this->fetchValueById($pass_id, 'PM_Id', 'PM_Name', 'payment_modes');
                    if($pass_id==1){
                        $get_name="Cash (Pay Cash)";
                    }elseif ($pass_id==2) {
                        $get_name="Bank (Pay thru Bank)";
                    }
                    break;
                case 'BS_PrchsdFor' :    
                case 'LC_Id':
                case 'BS_IEByLC':
                case 'BS_BranchTo': //02-03-2025
                    $get_name= $this->fetchValueById($pass_id, 'LC_Id', 'LC_Name', 'locations');
                    break;
                case 'CN_Id':
                    $get_name= $this->fetchValueById($pass_id, 'CN_Id', 'CN_Name', 'countries');
                    break;
                case 'ST_Id':
                    $get_name= $this->fetchValueById($pass_id, 'ST_Id', 'ST_Name', 'states');
                    break;
                case 'BS_PruchasedBy':
                case 'BS_Approved':
                case 'BS_Persons' :
                case 'BS_PaidBy' :
                case 'BS_User' : 
                case 'BS_AprovlGvnBy' :
                case 'BS_AprovlTknBy' : 
                case 'US_Id' :
                case 'BS_IEByUS':
                    $get_name= $this->fetchValueById($pass_id, 'US_Id', 'US_FName,US_LName', 'users_auth');
                    break;
                case 'BNK_Id' :
                    $get_name= $this->fetchValueById($pass_id, 'BNK_Id', 'BNK_Name', 'banks');
                    break;
                case 'BB_Id' :
                    $get_name= $this->fetchValueById($pass_id, 'BB_Id', 'BB_Name', 'bank_branches');
                    break;
                case 'BA_Id' :
                    $get_name= $this->fetchValueById($pass_id, 'BA_Id', 'BA_No', 'bank_accounts');
                    break;
                case 'CHQ_Number' :
                    $get_name= $this->fetchValueById($pass_id, 'CL_Id', 'CL_Leaf', 'bank_cheque_leafs');
                    break;
                case 'BS_PrchsdFor' :
                    $get_name= $this->fetchValueById($pass_id, 'LC_Id', 'LC_Name', 'locations');
                    break;
                case 'BS_StaffId' :    
                    $get_name= $this->fetchValueById($pass_id, 'US_Id', 'US_EMPID', 'users_auth');
                    break;
                case 'BS_Description' :    
                    $get_name= $this->fetchValueById($pass_id, 'DS_Id', 'DS_Description', 'descriptions');
                    break;
                case 'BS_PayTime' : 
                    if($pass_id==1){
                        $get_name="Pay Now - No Credit";
                    }elseif ($pass_id==2) {
                        $get_name="Pay Later - Credit";
                    }
                    break;
                case 'BS_PayType' :   
                    $get_name= $this->fetchValueById($pass_id, 'PM_Id', 'PM_Name', 'payment_modes');
//                    if($pass_id==1){
//                        $get_name="Cash (Pay Cash)";
//                    }elseif ($pass_id==2) {
//                        $get_name="Bank (Pay thru Bank)";
//                    }
                    break;
//                case 'BS_PettyCashRefId' :
//                    $get_name= $this->fetchValueById($pass_id, 'BS_Id', 'BS_Amount', 'balance_sheets');
                case 'TR_Id' :
                    $get_name= $this->fetchValueById($pass_id, 'TR_Id', 'TR_Track', 'tracks');
                    break;
                default :
                    $get_name=$pass_id;
                    break;
            }
            return $get_name;
        }
        function  searchBSItems($filter=""){
             $count=0;
            $this->SearchArray= array();
            $sql="SELECT IT.IT_Name, SH.SH_Id, BS.BS_Id, BS.BS_Amount, BS.BS_Description, DATE_FORMAT( BS.BS_Date, '%d/%m/%Y' ) 
                AS BS_Date, DS_Description FROM balance_sheets AS BS, items AS IT , descriptions as DS,sub_heads AS SH WHERE 
                BS.IT_Id = IT.IT_Id AND BS.BS_Description =DS.DS_Id AND IT.SH_Id=SH.SH_Id AND BS.BS_Complete =0 ".$filter." ORDER BY BS.BS_MDate Desc";
            $result=mysqli_query($GLOBALS['con'],$sql);
            while ($row=  mysqli_fetch_object($result)){
                $this->SearchArray[$count]=$row;
                $count++;
            }
           
        }
        function searchBSData($filt="") {           
        $count=0;
        $this->SearchArray = array();
        $sql="SELECT BS.US_Id, BS.BS_Id, BS.BS_CDate AS BS_Date, BS.BS_Amount, DS.DS_Description, TR.TR_Track, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `banks` AS BNK ON BNK.BNK_Id = BS.BNK_Id
                                LEFT JOIN `bank_branches` AS BB ON BB.BB_Id = BS.BB_Id
                                LEFT JOIN `bank_accounts` AS BA ON BA.BA_Id = BS.BA_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id = BS.US_Id
                                
                                WHERE 
                                IT.IT_Business = '0'
                                AND MH.MH_Type IN ('1','2')
                                AND BS.BS_Status = 1
                                ".$filt."
                                ORDER BY BS.BS_Date DESC
                                ";
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->SearchArray[$count] = $row;
                $count++;
            }
        }
        
    }
    function blockBSItems($BSId,$CLId){
        
        if($CLId!= ''){
            mysqli_query($GLOBALS['con'],"UPDATE bank_cheque_leafs SET CL_Status = 1 WHERE CL_Id = ".$CLId);
        } 
            
        $sql="UPDATE balance_sheets SET BS_Status = 0 WHERE BS_Id = ".$BSId;
        mysqli_query($GLOBALS['con'],$sql);
        return "Balance Sheet Item Deleted";
        
    }
    
    
    
    function getSubheadDetails ($filter) {
        $sql="SELECT BS.US_Id, BS.BS_Id, BS.BS_CDate AS BS_Date,BS.BS_PayTime, BS.BS_Amount, DS.DS_Id, DS.DS_Description,IT.IT_Id, IT.IT_Status,TR.TR_Track,BS.TR_Id, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type
                FROM `balance_sheets` AS BS
                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                WHERE 
                MH.MH_Type IN ('1','2') 
                AND (BS.BS_Status = 1 OR BS.BS_Status = 2 )
                ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_fetch_array($result,MYSQLI_ASSOC);
            }
     //-------------------------------- Cash Payment Notifications ----------------------------------------//
        function viewPettyCashPaid($filter) {		

            $count=0;
            $this->BalanceSheetArray = array();

            $result = mysqli_query($GLOBALS['con'],"SELECT BS.BS_Id, BS.BS_PettyCashAmt, BS.BS_Amount,BS.BS_Date, DS.DS_Description 
                                        FROM `balance_sheets` AS BS
                                            LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                            LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                            LEFT JOIN `users_auth` AS US ON BS.US_Id = US.US_Id
                                                WHERE IT.IT_Business = '0'  
                                                    AND  IT.IT_PettyCash = '1'  
                                                    AND MH.MH_Type IN ('2')
                                                    AND BS.BS_Status = 1
                                                    AND ".$filter."
                                                        ORDER BY BS.BS_Date DESC"
                                            );//AND BS.BS_Amount > 0 AND DS.DS_Description = CONCAT(US.US_FName,' ',US.US_LName) AND ".$filter."
            if($result){
                while($row = mysqli_fetch_object($result)) {
                    $this->BalanceSheetArray[$count] = $row;
                    $count++;
                }
            }
        } 
        
        //----------------------------------------- Amount for Petty Cash ----------------------------------------//
	function getAmount($BSId,$PCRefId)
	{			//echo "SELECT BS_Amount - IFNULL((Select BS_Amount FROM balance_sheets WHERE BS_PettyCashRefId= ".$PCRefId." AND BS_Id = ".$BSId." ), 0 ) AS Amount FROM `balance_sheets` WHERE BS_Id = ".$PCRefId;
            $result =   mysqli_query($GLOBALS['con'],"SELECT BS_PettyCashAmt + IFNULL((Select BS_Amount FROM balance_sheets WHERE BS_PettyCashRefId= ".$PCRefId." AND BS_Id = ".$BSId." ), 0 ) AS Amount FROM `balance_sheets` WHERE BS_Id = ".$PCRefId);
            //SELECT BS_Amount FROM balance_sheets WHERE BS_Id =".$BSId
            $row    =   mysqli_fetch_array($result,MYSQLI_ASSOC); 	
            return $row['Amount'];
	}
        
        //-------------------------------- Petty Cash Reference for Pop up ----------------------------------------//
        function viewPettyCashRefrence($filter) {		

            $result = mysqli_query($GLOBALS['con'],"SELECT BS.BS_Id, BS.BS_PettyCashAmt, BS.BS_Amount,BS.BS_Date, DS.DS_Description 
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `users_auth` AS US ON BS.US_Id = US.US_Id
                                    WHERE IT.IT_Business = '0'  
                                        AND MH.MH_Type IN ('2')
                                        AND BS.BS_Status != 0
                                        AND ".$filter."
                                    ORDER BY BS.BS_Date DESC"
                                    );//AND BS.BS_Amount > 0 AND DS.DS_Description = CONCAT(US.US_FName,' ',US.US_LName) AND ".$filter."
            $row    =   mysqli_fetch_array($result,MYSQLI_ASSOC); 	
            return $row;
        }  
        
        function bsEntryUpdateDate($OFId){
            $count = 0;
            $this->HistoryArray = array();

            $sql = 'SELECT BES_Date FROM bs_entry_update_settings WHERE OF_Id = '.$OFId;
            $result = mysqli_query($GLOBALS['con'],$sql);
            $row    = mysqli_fetch_array($result,MYSQLI_ASSOC); 	
            return $row['BES_Date'];
        }
        /**
        * Balance sheet table data fetched based on the parameters provided by the user
        * created by Bilin 9-05-2025
        */
        function getBalanceSheetEntry($inpParms= []) 
        {
            extract($inpParms);
            $retdata    = [];
            $this->ermsg= '(Track No Mis-match)';   
            $table      = ' FROM balance_sheets AS bs '
                .' INNER JOIN items AS itm ON (itm.IT_Id = bs.IT_Id)';
            $where      = ' WHERE bs.BS_Status != 0 ';
            if (isset( $track_id ) && $track_id > 0) {
                $where  .= ' AND bs.TR_Id ="'.$track_id.'"';
            }
            /*if (isset( $acc_id ) && $acc_id > 0) {
                $where  .= ' AND bs.BA_Id ="'.$acc_id.'"';
            }
            if (isset( $amount ) && $amount > 0) {
                $where  .= ' AND bs.BS_Amount ="'.$amount.'"';
            }            
            if (isset( $paid_date ) && $paid_date != '') {
                $where  .= ' AND bs.BS_Date ="'.$paid_date.'"';
            }  */                      
            if (isset( $type ) && $type > 0) {
                $where  .= ' AND itm.MH_Type ="'.$type.'"';
            }
            $sql = ' SELECT bs.BS_Id, bs.US_Id, bs.BS_Status, itm.MH_Type, bs.TR_Id, bs.BA_Id, bs.BS_Amount, bs.BS_Date '
            . $table
            . $where
            . ' ORDER BY bs.BS_Id DESC';
            $result = mysqli_query($GLOBALS['con'],$sql);
            while ( $row    = mysqli_fetch_array($result,MYSQLI_ASSOC) ) {
                if (isset( $amount ) && $row['BS_Amount'] == $amount) {

                    if (isset( $acc_id ) && $row['BA_Id'] == $acc_id) {

                        if (isset( $paid_date ) && $row['BS_Date'] == $paid_date) {

                            $retdata =  $row;
                            break;
                        } else {
                            $this->ermsg = '(Payment Date Mis-match)';
                        }
                    } else {
                        $this->ermsg = '(Bank Account Mis-match)';
                    }
                } else {
                    $this->ermsg = '(Amount Mis-match)';
                }
            }
            /*$row    = mysqli_fetch_array($result,MYSQLI_ASSOC); 
            if (!empty($row)) {
                $retdata = $row;
            }*/
            //$this->sql = $sql;
            return $retdata;
        }



    }
        
?>