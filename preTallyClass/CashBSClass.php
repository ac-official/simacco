<?php
require_once("connection.php");
class CashBSClass {
    var $CashBSArray;
    
    
    //--------------------------------------Cash BalanceSheet Items ----------------------------------------//
    function reportCashBSData($filt,$stDate='',$enDate='',$filter,$filter_mask,$pos,$cnt) {		
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
        $this->CashBSArray = array(); 		
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date, BS.BS_Amount,BS.BS_PettyCashAmt,DS.DS_Id, DS.DS_Description,TR.TR_Id, TR.TR_Track, IT.IT_Id, IT.IT_Name, IT.SH_Id, IT.IT_Status, SH.SH_Name, MH.MH_Type, LC.LC_Name,US.US_FName, US.US_LName
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND BS.PM_Id = 1 
                                AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                ".$dateFilt."
                                ".$filter_mask."
                                AND LC.".$filter."
                                ORDER BY BS.BS_Date   DESC
                                LIMIT ".$pos.",".$cnt
                                );//IF(`email` != '', `email`, `email2`)
//         ( BS.BS_PettyCashRefId = 0 AND MH.MH_Type = 2 ) 
        //AND MH.MH_Type IN ('1','2')
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->CashBSArray[$count] = $row;
                $count++;
            }
        }
    }
    
    //-----------------------------------Cash BalanceSheet Items Count  ----------------------------------------//
    function reportCashBSDataCount($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND BS.PM_Id = 1
                                AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
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
    function ocPieChartCashData($filt,$stDate='',$enDate='',$filter='') {			
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
        $this->CashBSArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type,  SUM(IF(IT.IT_Transfers = '1',BS.BS_Amount, '0' )) AS INE 
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                ".$dateFilt."
                                AND LC.".$filter."
                                AND BS.PM_Id = 1 
                                GROUP BY MH.MH_Type
                                 ");
        while($row=mysqli_fetch_object($result)) {
            $this->CashBSArray[$count] = $row;
            $count++;
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
        $this->CashBSArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) )  AS IE,IT.SH_Id,SH.SH_Name, MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND BS.PM_Id = 1 
                                AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                ".$dateFilt."
                                AND LC.".$filter."
                                GROUP BY IT.SH_Id
                                ORDER BY MH.MH_Type DESC,IE DESC 
                                 ");
        while($row=mysqli_fetch_object($result)) {
            $this->CashBSArray[$count] = $row;
            $count++;
        }
    }
    
    //--------------------------------- Opening Balance of a Company/Branch  ---------------------------------------//
    function getOpeningBalance($stDate='',$enDate='', $fields, $filter)
    {
        $dateFilt = '';
        
        if($stDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   < '".$stDate."'";
        }

        $count=0;
        $this->CashBSArray = array();  
        $sql="SELECT ".$fields." FROM cash_open_bals WHERE ".$filter;
        $res = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($res)) {
            $this->CashBSArray[$count] = $row;
            $count++;       
        }         
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND BS.PM_Id = 1
                                AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                ".$dateFilt."
                                AND LC.".$filter."
                                GROUP BY MH.MH_Type
                                ");
        
        while($row=mysqli_fetch_object($result)) {
            $this->CashBSArray[$count] = $row;
            $count++;       
        }    
    }
        
    //------------------------------- Verify Cash Opening Balance Status of a Branch  -------------------------------//
    function VerifyCashOBStatus($filter)
    {
        $count=0;
        $this->CashBSArray = array();  
        $sql="SELECT OB_Status FROM cash_open_bals WHERE ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC); 	
        return $row['OB_Status'];
    }
    
    function CalcBalances($curDate,$ofid) {			
        $dateFilt = '';      
        $count=0;
        $this->CashBSArray = array();        
        $result = mysqli_query($GLOBALS['con'],"SELECT SUM( BS.BS_Amount) AS INC,BS.LC_Id, MH.MH_Type,OB.OF_Id,OB.OB_Id,OB.OB_OpenBal
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                RIGHT JOIN  `cash_open_bals` AS OB ON OB.LC_Id = BS.LC_Id
                                WHERE OB.OF_Id=".$ofid." AND IT.IT_Business = '0' AND BS.BS_Status = 1
                                AND MH.MH_Type IN ('1','2') AND BS.BS_Date  ='".$curDate."'AND OB.OB_Date='".$curDate."' GROUP BY BS.LC_Id,MH.MH_Type");
        
        while($row=mysqli_fetch_object($result)) {
            $this->CashBSArray[$count] = $row;
            $count++;
        }	
    }
    function createBalances($obid,$ofid,$lcid,$amt,$stDate)
    {
      // $newdate = date("Y-m-d", strtotime("+1 day", strtotime($stDate)));  
       $sqlupd="UPDATE cash_open_bals SET OB_CloseBal=".$amt." WHERE OB_Id=".$obid;
       mysqli_query($GLOBALS['con'],$sqlupd);       
       $sqlcreate="INSERT INTO cash_open_bals (OF_Id,LC_Id,OB_OpenBal,OB_Date) VALUES (".$ofid.",".$lcid.",".$amt.",'".$newdate."')";       
       mysqli_query($GLOBALS['con'],$sqlcreate);
    }
    function viewOpeningBalances($ofid)
    {
        $sql="SELECT OB.OB_Id,LC.LC_Name,OB.OB_OpenBal,OB.OB_CloseBal,OB.OB_Status,OB.OB_Date FROM cash_open_bals AS OB,locations AS LC WHERE LC.LC_Status=1 AND OB.LC_Id=LC.LC_Id AND LC.OF_Id=".$ofid;
        $count=0;
        $this->CashBSArray = array();  
         $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($result)) {
            $this->CashBSArray[$count] = $row;
            $count++;       
    }    
    }
   function updateOpenBal($obid){
		$BalData = '';
		foreach ($this->Bal_Data as $key=>$value){ 
			$BalData = $BalData .$key ."='".$value."', ";
		}
		$BalData = substr($BalData, 0, -2);
		$sql = "UPDATE cash_open_bals SET $BalData WHERE OB_Id=".$obid;
		mysqli_query($GLOBALS['con'],$sql);
		return 'Opening Balance Updated Successfully';
	}
        
    //----------------------------------- Get Opening Balance Id----------------------------------------//  
    function getOpenBalId($LCId){
        $sql = 'SELECT OB_Id FROM cash_open_bals WHERE LC_Id = '.$LCId;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
        return $row['OB_Id'];
    }   
    
    //----------------------------------- View Old Stock Amounts----------------------------------------//     
    function viewOldStockAmounts($LCId)
    {
        $sql="SELECT BS.BS_Id, BS.US_Id, BS.BS_Amount, BS.BS_Date   ,TR.TR_Track
                    FROM  `balance_sheets`  AS BS
                        LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                             WHERE `IT_Id` =219
                                 AND `LC_Id` = ".$LCId;
        $count=0;
        $this->CashBSArray = array();  
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($result)) {
            $this->CashBSArray[$count] = $row;
            $count++;       
        }    
    }
    
    function updateOldStock($BSId){
        $OldStockData = '';
        foreach ($this->OldStock_Data as $key=>$value){ 
                $OldStockData = $OldStockData .$key ."='".$value."', ";
        }
        $OldStockData = substr($OldStockData, 0, -2);
        $sql = "UPDATE balance_sheets SET $OldStockData WHERE BS_Id=".$BSId;
        mysqli_query($GLOBALS['con'],$sql);
        return 'Old Stock Amount Updated Successfully';
    }
    //--------------------------- BalanceSheet Cash Bank Income Expense Details ----------------------------//
    function reportVisualDetails($filt,$stDate='',$enDate='',$filter='') {			
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
        $this->CashBSArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type, BS.PM_Id ,IT.IT_Transfers,BS.BS_Complete
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                ".$dateFilt."
                                AND LC.".$filter."
                                GROUP BY MH.MH_Type, BS.PM_Id ,IT.IT_Transfers
                                 ");
        
        while($row=mysqli_fetch_object($result)) {
            $this->CashBSArray[$count] = $row;
            $count++;
        }	
    }
    
}
?>