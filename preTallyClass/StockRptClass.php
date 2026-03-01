<?php
require_once("connection.php");
class StockRptClass {
    var $StockArray;
    
    
    //--------------------------------------Cash BalanceSheet Items ----------------------------------------//
    function reportStockData($filt,$stDate='',$enDate='',$filter,$filter_mask,$pos,$cnt) {		
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND TR.TR_CDate   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND TR.TR_CDate   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND TR.TR_CDate   >=  '".$stDate."'";
        }

        $result = mysqli_query($GLOBALS['con'],"SELECT DISTINCT TR.TR_Id
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
                                    AND IT.MH_Type IN ('1','2')
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND LC.".$filter."
                                    ORDER BY TR.TR_Id DESC
                                    LIMIT ".$pos.",".$cnt
                                        
                                );
        $trList = '';
        while($row = mysqli_fetch_object($result)) {
            $trList .= $row->TR_Id.',';
        }
        $trList = rtrim($trList, ',');
        $count=0;
        $this->StockArray = array();

        $result = mysqli_query($GLOBALS['con'],"SELECT TR.TR_Id, TR.US_Id, SUM(BS.BS_Amount) AS BS_Amount, SOB.OS_OpenBal, DS.DS_Description, TR.TR_Track,TR.TR_CDate, IT.IT_Business,IT.MH_Type, IT.IT_Name, IT.SH_Id, SH.SH_Name,  LC.LC_Name,US.US_FName, US.US_LName
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON TR.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `stock_open_bals` AS SOB ON SOB.TR_Id = BS.TR_Id
                                WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
                                    AND TR.TR_Id IN (".$trList.")
                                    AND IT.MH_Type IN ('1','2')
                                    ".$filter_mask."
                                    AND LC.".$filter."
                                GROUP BY BS.TR_Id, IT.IT_Business,IT.MH_Type
                                ORDER BY TR.TR_Id DESC"
                                );
        
        $oldTrack = '';
        $curTrack = '';
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $curTrack = $row->TR_Track;
                if($oldTrack != $curTrack){
                    $this->StockArray[$row->TR_Id]['TR_Id']      = $row->TR_Id;
                    $this->StockArray[$row->TR_Id]['TR_Track']   = $row->TR_Track;
                    $this->StockArray[$row->TR_Id]['TR_CDate']   = $row->TR_CDate;
                    $this->StockArray[$row->TR_Id]['SH_Name']    = $row->LC_Name;
                    $this->StockArray[$row->TR_Id]['LC_Name']    = $row->LC_Name;
                    $this->StockArray[$row->TR_Id]['US_FName']   = $row->US_FName;
                    $this->StockArray[$row->TR_Id]['US_LName']   = $row->US_LName;
                    $this->StockArray[$row->TR_Id]['Business']   = $row->OS_OpenBal ? $row->OS_OpenBal : 0;
                    $this->StockArray[$row->TR_Id]['Income']     = 0;
                    $this->StockArray[$row->TR_Id]['Expense']    = 0;
                    $oldTrack = $curTrack;
                }
                
                if($row->MH_Type=="1") {
                    if($row->IT_Business=="1") {
                        $this->StockArray[$row->TR_Id]['Business'] += $row->BS_Amount;
                    }else {
                       $this->StockArray[$row->TR_Id]['Income'] += $row->BS_Amount;
                    }
                }else if($row->MH_Type=="2"){
                    if($row->IT_Business=="1"){
                        $this->StockArray[$row->TR_Id]['Business'] = $this->StockArray[$row->TR_Id]['Business'] - $row->BS_Amount;
                    }else{
                       $this->StockArray[$row->TR_Id]['Expense'] += $row->BS_Amount;
                    }
                }
                $count++;
            }
        }
    }
    
    //-----------------------------------Cash BalanceSheet Items Count  ----------------------------------------//
    function reportStockDataCount($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND TR.TR_CDate   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND TR.TR_CDate   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND TR.TR_CDate   >=  '".$stDate."'";
        }

        $result = mysqli_query($GLOBALS['con'],"SELECT COUNT(DISTINCT TR.TR_Id)
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
                                    AND IT.MH_Type IN ('1','2')
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND LC.".$filter
                                );
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        return $count=$row[0];
    }
    
     //--------------------------- BalanceSheet Cash Opening/Closing Balance PieChart ----------------------------//
    function reportStockDataSum($filt,$stDate='',$enDate='',$filter='') {			
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND TR.TR_CDate   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND TR.TR_CDate   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND TR.TR_CDate   >=  '".$stDate."'";
        }

        $this->StockArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
                                    AND IT.MH_Type IN ('1','2')
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND LC.".$filter."
                                GROUP BY IT.IT_Business,IT.MH_Type"
                                );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->StockArray[$row->TR_Id]['Business']   = 0;
                $this->StockArray[$row->TR_Id]['Income']     = 0;
                $this->StockArray[$row->TR_Id]['Expense']    = 0;
                if($row->MH_Type=="1") {
                    if($row->IT_Business=="1") {
                        $this->StockArray['Business'] += $row->BS_Amount;
                    }else {
                       $this->StockArray['Income'] += $row->BS_Amount;
                    }
                }else if($row->MH_Type=="2"){
                    if($row->IT_Business=="1"){
                        $this->StockArray['Business'] = $this->StockArray['Business'] - $row->BS_Amount;
                    }else{
                       $this->StockArray['Expense'] += $row->BS_Amount;
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
        $this->StockArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount )  AS IE,IT.SH_Id,SH.SH_Name, MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE IT.IT_Business = '1'
                                AND BS.BS_Status = 1
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 1
                                    ".$dateFilt."
                                    AND LC.".$filter."
                                GROUP BY IT.SH_Id
                                ORDER BY MH.MH_Type DESC,IE DESC 
                                 ");
        while($row=mysqli_fetch_object($result)) {
            $this->StockArray[$count] = $row;
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

        $count=1;
        $this->StockArray = array();  
        /*$sql="SELECT ".$fields." FROM cash_open_bals WHERE ".$filter;
        $res = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($res)) {
            $this->StockArray[$count] = $row;
            $count++;       
        }*/ 

        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount ) AS IE , MH.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                WHERE IT.IT_Business = '1'
                                AND BS.BS_Status = 1
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 1 
                                    ".$dateFilt."
                                    AND US.".$filter."
                                GROUP BY MH.MH_Type
                                ");
        
        while($row=mysqli_fetch_object($result)) {
            $this->StockArray[$count] = $row;
            $count++;       
        }    
    }
        
    //------------------------------- Verify Cash Opening Balance Status of a Branch  -------------------------------//
    function VerifyCashOBStatus($filter)
    {
     
        $count=0;
        $this->StockArray = array();  
        $sql="SELECT OB_Status FROM cash_open_bals WHERE ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC); 	
        return $row['OB_Status'];
    }
    
function CalcBalances($curDate,$ofid) {			
        $dateFilt = '';      
        $count=0;
        $this->StockArray = array();        
        $result = mysqli_query($GLOBALS['con'],"SELECT SUM( BS.BS_Amount) AS INC,BS.LC_Id, MH.MH_Type,OB.OF_Id,OB.OB_Id,OB.OB_OpenBal
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                RIGHT JOIN  `cash_open_bals` AS OB ON OB.LC_Id = BS.LC_Id
                                WHERE OB.OF_Id=".$ofid." 
                                AND IT.IT_Business = '1'
                                AND BS.BS_Status = 1    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Date  ='".$curDate."' AND OB.OB_Date='".$curDate."' GROUP BY BS.LC_Id,MH.MH_Type");
        
        while($row=mysqli_fetch_object($result)) {
            $this->StockArray[$count] = $row;
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
        $this->StockArray = array();  
         $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($result)) {
            $this->StockArray[$count] = $row;
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
        $sql="SELECT BS.BS_Id, BS.US_Id, BS.BS_Amount, BS.BS_Date, TR.TR_Track
                    FROM  `balance_sheets`  AS BS
                        LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                             WHERE `IT_Id` =219
                                 AND `LC_Id` = ".$LCId;
        $count=0;
        $this->StockArray = array();  
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($result)) {
            $this->StockArray[$count] = $row;
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
        
    //---------------------------  Business - Income Stock Details  ----------------------------//
    function reportStockDetails($filt,$stDate='',$enDate='',$filter='') {			
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND TR.TR_CDate   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND TR.TR_CDate   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND TR.TR_CDate   >=  '".$stDate."'";
        }
        
        
        $sql="SELECT SUM(OS_OpenBal) AS OS FROM stock_open_bals WHERE ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row = mysqli_fetch_object($result)) { 
            $this->StockArray['Old_Stock'] = $row->OS;
        } 

//        $this->StockArray = array();
//        $sql = "SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type
//                                FROM `balance_sheets` AS BS
//                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
//				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
//                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
//                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
//                                LEFT JOIN `users_auth` AS US ON TR.US_Id =US.US_Id
//                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
//                                WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
//                                    AND IT.MH_Type IN ('1','2')
//                                    AND IT.IT_Id = 219
//                                    ".$dateFilt."
//                                    ".$filter_mask."
//                                    AND LC.".$filter."
//                                GROUP BY IT.IT_Business,IT.MH_Type";
//        $result = mysqli_query($GLOBALS['con'],$sql);
//        if($result){
//            while($row = mysqli_fetch_object($result)) {
//                $this->StockArray['Old_Stock'] = $row->BS_Amount;
//            }
//        }
        
        
        if($stDate){
            $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                    WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
                                        AND IT.MH_Type IN ('1','2')
                                        AND TR.TR_CDate < '".$stDate."'
                                        ".$filter_mask."
                                        AND LC.".$filter."
                                    GROUP BY IT.IT_Business,IT.MH_Type"
                                    );
            if($result){

                $this->StockArray['Old_Business'] = 0;
                $this->StockArray['Old_Income']   = 0;     

                while($row = mysqli_fetch_object($result)) {
                    if($row->MH_Type=="1") {
                        if($row->IT_Business=="1") {
                            $this->StockArray['Old_Business'] += $row->BS_Amount;
                        }else {
                           $this->StockArray['Old_Income'] += $row->BS_Amount;
                        }
                    }else if($row->MH_Type=="2"){
                        if($row->IT_Business=="1"){
                            $this->StockArray['Old_Business'] = $this->StockArray['Old_Business'] - $row->BS_Amount;
                        }
                    }
                }
            }
        }	
        
    }
}
?>