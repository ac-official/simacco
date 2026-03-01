<?php
require_once("connection.php");
require_once("UserClass.php");

class MasterReportsLocationBasedClass {
    var $MasterReportArray;
    var $BranchStkRptArray;

    //----------------------------------------- Item Based Reports ----------------------------------------//
    function reportItemData($filt,$stDate='',$enDate='') {		
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
        }
            
        $count=0;
        $this->MasterReportArray = array();
        $sql = "SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS BS_Amount ,IT.IT_Id, IT.IT_Name,IT.IT_Transfers,IT.IT_Business,MH.MH_Type,MH.MH_Name,SH.SH_Id,SH.SH_Name, LC.LC_Name,LC.LC_Id
                    FROM  `balance_sheets` AS BS
                        LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                        LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                        LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                        LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
                            WHERE  ".$filt."
                                ".$dateFilt."
                                    GROUP BY IT.IT_Id ORDER BY IT.IT_Name";
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->MasterReportArray[$count] = $row;
                $count++;
            }
        }
    }
    //----------------------------------------- Item Based Branch Reports ----------------------------------------//
    function reportItemBranchData($filt,$stDate='',$enDate='') {		
        $dateFilt = '';
        
        if($stDate  !='' && $enDate !=''){
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt =" AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }elseif($stDate =='' && $enDate !=  ''){
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date   <=  '".$enDate."'";
        }elseif($stDate !='' && $enDate ==  ''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $dateFilt   =" AND BS.BS_Date   >=  '".$stDate."'";
        }
            
        $count                      = 0;
        $this->MasterReportArray    = array();
        
        $sql = "SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS BS_Amount ,IT.IT_Id, IT.IT_Name, SH.SH_Id, 
            SH.SH_Name, LC.LC_Name, LC.LC_Id ,LC.LC_Status 
            FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
            LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
            WHERE  ".$filt." ".$dateFilt." GROUP BY LC.LC_Id ORDER BY LC.LC_Name";
           
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->MasterReportArray[$count] = $row;
                $count++;
            }
        }
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS 
                    MONTH , CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS BS_Amount
                    FROM  `balance_sheets` AS BS
                    LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                    LEFT JOIN  `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
                    WHERE ".$filt." ".$dateFilt." 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->MonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
    }
    //----------------------------------------- Item Based User Reports ----------------------------------------//
    function reportItemUserData($filt,$stDate='',$enDate='') {		
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
        }
            
        $count=0;
        $this->MasterReportArray = array();
        $sql = "SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS BS_Amount ,IT.IT_Id, IT.IT_Name,SH.SH_Id,SH.SH_Name,US.US_Id,CONCAT(`US_FName`,' ',`US_LName`) AS US_Name
                    FROM  `balance_sheets` AS BS
                        LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                        LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                        LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                        LEFT JOIN `users_auth` AS US ON BS.US_Id = US.US_Id
                            WHERE  ".$filt."
                                ".$dateFilt."
                                    GROUP BY US.US_Id ORDER BY US.US_FName";
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->MasterReportArray[$count] = $row;
                $count++;
            }
        }
    }
    //-------------------------------------- Branch Based Reports ---------------------------------------------//
          
    function reportBranches($of_id,$stDate,$enDate)
    {   $count=0;
        $this->BranchCshRptArray = array();
        $this->BranchBnkRptArray = array();
        
         if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date >=  '".$stDate."'";
        }
        
        //----------- Current Cash Based Income/Expense Details ---------------------//
        
        $sql = "SELECT LC.LC_Id,LC.LC_Name,MH.MH_Type,IT.IT_Business, IT.IT_Transfers, SUM(BS.BS_Amount) AS BS_IEAmt, BS.PM_Id
                FROM balance_sheets AS BS 
                    LEFT JOIN locations AS LC ON LC.LC_Id = BS.BS_IEByLC
                    LEFT JOIN items AS IT ON IT.IT_Id = BS.IT_Id
                    LEFT JOIN sub_heads AS SH ON SH.SH_Id = IT.SH_Id
                    LEFT JOIN main_heads AS MH ON MH.MH_Id = SH.MH_Id
                    WHERE LC.OF_Id= ".$of_id."
                        AND BS.BS_Status = 1
                        AND BS.PM_Id = 1 
                        AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                        ".$dateFilt." 
                            GROUP BY LC.LC_Id,MH.MH_Type,IT.IT_Business,IT.IT_Transfers
                            ORDER BY LC.LC_Name";
        $result =  mysqli_query($GLOBALS['con'],$sql);
        
        $lcIDCsh = $templcIDCsh='';
        while($row=mysqli_fetch_object($result)) {
//            $this->BranchCshRptArray[$row->LC_Id] = $row;
            
            if($templcIDCsh != $lcIDCsh){
                $this->BranchCshRptArray[$row->LC_Id]['LC_Id']        = $row->LC_Id;
                $this->BranchCshRptArray[$row->LC_Id]['CshInc']       = 0;
                $this->BranchCshRptArray[$row->LC_Id]['CshExp']       = 0;
                $this->BranchCshRptArray[$row->LC_Id]['Buss']         = 0;
                $this->BranchCshRptArray[$row->LC_Id]['CshTransRecv'] = 0;
                $this->BranchCshRptArray[$row->LC_Id]['CshTransPaid'] = 0;
                $lcIDCsh = $templcIDCsh;
            }
            
            if($row->MH_Type == 1 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->BranchCshRptArray[$row->LC_Id]['CshInc'] += $row->BS_IEAmt;
            }else if($row->MH_Type == 2 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->BranchCshRptArray[$row->LC_Id]['CshExp'] += $row->BS_IEAmt;
            }else if($row->IT_Business == 1 && $row->MH_Type == 1 ) {
                    $this->BranchCshRptArray[$row->LC_Id]['Buss'] = $this->BranchCshRptArray[$row->LC_Id]['Buss'] + $row->BS_IEAmt;
            }else if($row->IT_Business == 1 && $row->MH_Type == 2 ) {
                    $this->BranchCshRptArray[$row->LC_Id]['Buss'] = $this->BranchCshRptArray[$row->LC_Id]['Buss'] - $row->BS_IEAmt;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 1 ) {
                    $this->BranchCshRptArray[$row->LC_Id]['CshTransRecv']  += $row->BS_IEAmt;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 2 ) {
                    $this->BranchCshRptArray[$row->LC_Id]['CshTransPaid']  += $row->BS_IEAmt;
            }
        
        }
        
        //----------- Current Bank Based Income/Expense Details ---------------------//
        $sql = "SELECT LC.LC_Id,LC.LC_Name,MH.MH_Type,IT.IT_Business, IT.IT_Transfers, SUM(BS.BS_Amount) AS BS_IEAmt, BS.PM_Id
                FROM balance_sheets AS BS 
                    LEFT JOIN bank_accounts AS BA ON BS.BA_Id = BA.BA_Id
                    LEFT JOIN locations AS LC ON LC.LC_Id = BA.LC_Id
                    LEFT JOIN items AS IT ON IT.IT_Id = BS.IT_Id
                    LEFT JOIN sub_heads AS SH ON SH.SH_Id = IT.SH_Id
                    LEFT JOIN main_heads AS MH ON MH.MH_Id = SH.MH_Id
                    WHERE LC.OF_Id= ".$of_id."
                        AND BS.BS_Status = 1
                        AND BS.PM_Id = 2
                        AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                        ".$dateFilt." 
                            GROUP BY LC.LC_Id,MH.MH_Type,IT.IT_Business,IT.IT_Transfers
                            ORDER BY LC.LC_Name";
        $result =  mysqli_query($GLOBALS['con'],$sql);
        
        $lcIDBnk = $templcIDBnk='';
        while($row=mysqli_fetch_object($result)) {
            
            if($templcIDBnk != $lcIDBnk){
                $this->BranchBnkRptArray[$row->LC_Id]['LC_Id']        = $row->LC_Id;
                $this->BranchBnkRptArray[$row->LC_Id]['BnkInc']       = 0;
                $this->BranchBnkRptArray[$row->LC_Id]['BnkExp']       = 0;
                $this->BranchBnkRptArray[$row->LC_Id]['Buss']         = 0;
                $this->BranchBnkRptArray[$row->LC_Id]['BnkTransRecv'] = 0;
                $this->BranchBnkRptArray[$row->LC_Id]['BnkTransPaid'] = 0;
                $lcIDBnk = $templcIDBnk;
            }
            
            if($row->MH_Type == 1 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->BranchBnkRptArray[$row->LC_Id]['BnkInc'] += $row->BS_IEAmt;
            }else if($row->MH_Type == 2 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->BranchBnkRptArray[$row->LC_Id]['BnkExp'] += $row->BS_IEAmt;
            }else if($row->IT_Business == 1 && $row->MH_Type == 1 ) {
                    $this->BranchBnkRptArray[$row->LC_Id]['Buss'] = $this->BranchBnkRptArray[$row->LC_Id]['Buss'] + $row->BS_IEAmt;
            }else if($row->IT_Business == 1 && $row->MH_Type == 2 ) {
                    $this->BranchBnkRptArray[$row->LC_Id]['Buss'] = $this->BranchBnkRptArray[$row->LC_Id]['Buss'] - $row->BS_IEAmt;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 1 ) {
                    $this->BranchBnkRptArray[$row->LC_Id]['BnkTransRecv']  += $row->BS_IEAmt;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 2 ) {
                    $this->BranchBnkRptArray[$row->LC_Id]['BnkTransPaid']  += $row->BS_IEAmt;
            }
        }
    }
    
    //--------------------------------- Bank Opening Balance of each Branch  ----------------------------------------//
    function getBranchBankOB($stDate='',$enDate='', $OFId)
    {
        $dateFilt = '';
        
        if($stDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   < '".$stDate."'";
        }

        $count=0;
        $this->BankOBArray = array(); 
        $BankOB = array();
        $BankIncExp = array();
        
        //--------------- Starting Bank Opening Balance ---------------------//
        $sql = "SELECT SUM(OB.BnkOB_OpenBal) AS OB_Amount, BA.LC_Id, BA.BA_Id
                    FROM bank_open_bals AS OB
                        LEFT JOIN bank_accounts AS BA ON BA.BA_Id = OB.BA_Id
                            WHERE OB.OF_Id = ".$OFId."
                                GROUP BY BA.LC_Id";
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($result)) {
            $BankOB[$row->LC_Id] = $row;
        } 
        
        //--------------- Current Date ( From Date ) Bank Opening Balance (Inc/Exp of Previous) ---------------------//
        $result = mysqli_query($GLOBALS['con'],"SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE_Amount , MH.MH_Type, LC.LC_Id, BA.BA_Id
                                FROM balance_sheets AS BS 
                                    LEFT JOIN bank_accounts AS BA ON BS.BA_Id = BA.BA_Id
                                    LEFT JOIN locations AS LC ON LC.LC_Id = BA.LC_Id
                                    LEFT JOIN items AS IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN sub_heads AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN main_heads AS MH ON SH.MH_Id = MH.MH_Id
                                     WHERE IT.IT_Business = '0'
                                        AND BS.BS_Status = 1
                                        AND BS.PM_Id = 2 
                                        AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                        ".$dateFilt."
                                            GROUP BY LC.LC_Id,MH.MH_Type
                                                ORDER BY LC.LC_Name");

        while($row=mysqli_fetch_object($result)) {
            if($row->MH_Type == "1") {
                   $BankIncExp[$row->LC_Id]['Income'] += $row->IE_Amount;
            }else if($row->MH_Type == "2"){
                   $BankIncExp[$row->LC_Id]['Expense'] += $row->IE_Amount;
            }
        }
        
        foreach ($BankOB as $key=>$value) {
            $this->BankOBArray[$value->LC_Id] = $BankOB[$key]->OB_Amount + $BankIncExp[$value->LC_Id]['Income'] - $BankIncExp[$value->LC_Id]['Expense'];
        }
    }
    
    //--------------------------------- Cash Opening Balance of each Branch  ----------------------------------------//
    function getBranchCashOB($stDate='',$enDate='', $OFId)
    {
        $dateFilt = '';
        
        if($stDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   < '".$stDate."'";
        }

        $count=0;
        $this->CashOBArray   = array();
        
        $CashOB = array();
        $CashIncExp = array();
        
        //--------------- Starting Cash Opening Balance ---------------------//
        $sql = "SELECT OB.OB_OpenBal AS OB_Amount, LC_Id
                    FROM cash_open_bals AS OB
                    WHERE OF_Id = ".$OFId."
                        GROUP BY LC_Id";
        $res = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($res)) {
            $CashOB[$row->LC_Id] = $row;
            $count++;       
        }

        //--------------- Current Date ( From Date ) Cash Opening Balance (Inc/Exp of Previous) ---------------------//
        $result = mysqli_query($GLOBALS['con'],"SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE_Amount , MH.MH_Type, LC.LC_Id
                                FROM balance_sheets AS BS 
                                    LEFT JOIN `items` AS IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                                        WHERE BS.BS_Status = 1
                                        AND IT.IT_Business = '0'
                                        AND BS.PM_Id = 1
                                        AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                        AND LC.OF_Id = ".$OFId."
                                        ".$dateFilt."
                                            GROUP BY LC.LC_Id,MH.MH_Type
                                                ORDER BY LC.LC_Name");
         
        while($row=mysqli_fetch_object($result)) {
            if($row->MH_Type == "1") {
                   $CashIncExp[$row->LC_Id]['Income'] += $row->IE_Amount;
            }else if($row->MH_Type == "2"){
                   $CashIncExp[$row->LC_Id]['Expense'] += $row->IE_Amount;
            }
        }
       
        foreach ($CashOB as $key=>$value) {
            $this->CashOBArray[$value->LC_Id] = $CashOB[$key]->OB_Amount + $CashIncExp[$value->LC_Id]['Income'] - $CashIncExp[$value->LC_Id]['Expense'];
        }
    }
    
     //--------------------------- BalanceSheet Cash Opening/Closing Balance PieChart ----------------------------//
    function reportStockDataSum($filt,$stDate='',$enDate='',$filter='') {			
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

        $this->MasterReportArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
                                WHERE BS.BS_Status = 1 
                                    AND SH.SH_Track = 1
                                    AND IF( BS.BS_PettyCashRefId != 0 , IT.MH_Type = 1 , IT.MH_Type IN ('1','2'))
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND LC.".$filter."
                                GROUP BY IT.IT_Business,IT.MH_Type"
                                );
        if($result){
            $this->MasterReportArray['Business']   = 0;
            $this->MasterReportArray['Income']     = 0;
            $this->MasterReportArray['Expense']    = 0;
                
            while($row = mysqli_fetch_object($result)) {
               
                if($row->MH_Type=="1") {
                    if($row->IT_Business=="1") {
                        $this->MasterReportArray['Business'] += $row->BS_Amount;
                    }else {
                       $this->MasterReportArray['Income'] += $row->BS_Amount;
                    }
                }else if($row->MH_Type=="2"){
                    if($row->IT_Business=="1"){
                        $this->MasterReportArray['Business'] -= $row->BS_Amount;
                    }else{
                       $this->MasterReportArray['Expense'] += $row->BS_Amount;
                    }
                }
                
            }
        }	
    }
    //---------------------------  Business - Income Stock Details  ----------------------------//
    function reportStockDetails($filt,$stDate='',$enDate='',$sfields, $filter='') {			
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

        $this->MasterReportArray = array();
//        $sql = "SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type
//                                FROM `balance_sheets` AS BS
//                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
//				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
//                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
//                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
//                                LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
//                                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
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
//                $this->MasterReportArray['CurrentPeriodOldStock'] = $row->BS_Amount;
//            }
//        }
        
        
        $sql="SELECT ".$sfields." FROM stock_open_bals WHERE ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row = mysqli_fetch_object($result)) { 
            $this->MasterReportArray['CurrentPeriodOldStock'] = $row->OS;
            $count++;       
        } 
        
        
        if($stDate){
            $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
                                    WHERE BS.BS_Status = 1
                                        AND SH.SH_Track = 1
                                        AND IF( BS.BS_PettyCashRefId != 0 , IT.MH_Type = 1 , IT.MH_Type IN ('1','2'))
                                        AND BS.BS_Date < '".$stDate."'
                                        ".$filter_mask."
                                        AND LC.".$filter."
                                    GROUP BY IT.IT_Business,IT.MH_Type"
                                    );
            if($result){

                $this->MasterReportArray['Old_Business'] = 0;
                $this->MasterReportArray['Old_Income']   = 0;     

                while($row = mysqli_fetch_object($result)) {
                    if($row->MH_Type=="1") {
                        if($row->IT_Business=="1") {
                            $this->MasterReportArray['Old_Business'] += $row->BS_Amount;
                        }else {
                           $this->MasterReportArray['Old_Income'] += $row->BS_Amount;
                        }
                    }else if($row->MH_Type=="2"){
                        if($row->IT_Business=="1"){
                            $this->MasterReportArray['Old_Business'] = $this->MasterReportArray['Old_Business'] - $row->BS_Amount;
                        }
                    }
                }
            }
        }
    }
    //---------------------------  Business - Income Stock Details Branch Based ----------------------------//
    function reportStockDetailsBranch($filt,$stDate='',$enDate='',$OFId) {			
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

//        $this->BranchStkRptArray = array();
//        $sql = "SELECT BS.US_Id, SUM( BS.BS_Amount ) AS BS_Amount , MH.MH_Type,LC.LC_Id
//                                FROM  `balance_sheets` AS BS
//                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
//                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
//                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
//                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
//                                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
//                                WHERE IT.IT_Business = '1' AND BS.TR_Id !='0'
//                                    AND BS.BS_Status = 1
//                                    AND IT.IT_Id = 219
//                                    ".$dateFilt."
//                                    ".$filter_mask."
//                                    AND LC.OF_Id = ".$OFId."
//                                GROUP BY LC.LC_Id, IT.IT_Business,IT.MH_Type ";
//        
//        $result = mysqli_query($GLOBALS['con'],$sql);
//        if($result){
//            while($row = mysqli_fetch_object($result)) {
//                $this->BranchStkRptArray[$row->LC_Id]['CurrentPeriodOldStock'] = $row->BS_Amount;
//            }
//        }
        
        $sql = "SELECT OS_OpenBal, LC_Id FROM stock_open_bals WHERE OF_Id = ".$OFId." GROUP BY LC_Id" ;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $this->BranchStkRptArray = array();
        while($row = mysqli_fetch_object($result)) { 
            $this->BranchStkRptArray[$row->LC_Id]['CurrentPeriodOldStock'] = $row->OS_OpenBal;
        } 
        
        $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type,LC.LC_Id
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
                                WHERE BS.BS_Status = 1 AND SH.SH_Track='1' 
                                    AND IT.IT_Id != 219
                                    AND IF( BS.BS_PettyCashRefId != 0 , IT.MH_Type = 1 , IT.MH_Type IN ('1','2'))
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND LC.OF_Id = ".$OFId."
                                GROUP BY LC.LC_Id,IT.IT_Business,IT.MH_Type"
                                );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                
                if($row->MH_Type=="1") {
                    if($row->IT_Business=="1") {
                        $this->BranchStkRptArray[$row->LC_Id]['Business'] += $row->BS_Amount;
                    }else {
                       $this->BranchStkRptArray[$row->LC_Id]['Income'] += $row->BS_Amount;
                    }
                }else if($row->MH_Type=="2"){
                    if($row->IT_Business=="1"){
                        $this->BranchStkRptArray[$row->LC_Id]['Business'] = $this->BranchStkRptArray[$row->LC_Id]['Business'] - $row->BS_Amount;
                    }else{
                       $this->BranchStkRptArray[$row->LC_Id]['Expense'] += $row->BS_Amount;
                    }
                }
                
            }
        }	
        
        if($stDate){
            $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type,LC.LC_Id
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
                                    WHERE BS.BS_Status = 1 
                                        AND SH.SH_Track = 1
                                        AND IF( BS.BS_PettyCashRefId != 0 , IT.MH_Type = 1 , IT.MH_Type IN ('1','2'))
                                        AND BS.BS_Date < '".$stDate."'
                                        ".$filter_mask."
                                        AND LC.OF_Id = ".$OFId."
                                    GROUP BY LC.LC_Id,IT.IT_Business,IT.MH_Type  ORDER BY LC.LC_Name"
                                    );
            if($result){

                while($row = mysqli_fetch_object($result)) {

                    if($row->MH_Type=="1") {
                        if($row->IT_Business=="1") {
                            $this->BranchStkRptArray[$row->LC_Id]['Old_Business'] += $row->BS_Amount;
                        }else {
                           $this->BranchStkRptArray[$row->LC_Id]['Old_Income'] += $row->BS_Amount;
                        }
                    }else if($row->MH_Type=="2"){
                        if($row->IT_Business=="1"){
                            $this->BranchStkRptArray[$row->LC_Id]['Old_Business'] = $this->BranchStkRptArray[$row->LC_Id]['Old_Business'] - $row->BS_Amount;
                        }
                    }
                }
            }
        }	
        
    }
    
    function reportItemBasedData($stDate,$enDate,$filter_key) {	
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
        $this->ReportArray = array();         
       
        $sql="SELECT BS.US_Id, BS.BS_Id,BS.BS_MinAmount,BS.BS_MaxAmount, BS.BS_Date  , 
                    BS.BS_Amount, DS.DS_Description,BS.CHQ_Number, TR.TR_Id ,TR.TR_Track, 
                    IT.IT_Id, IT.IT_Name,IT.IT_Business, IT.SH_Id, 
                    SH.SH_Name,SH.SH_Track, MH.MH_Type, LC.LC_Name,US.US_FName,US.US_LName
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                WHERE
                                ".$filter_key." 
                                ".$dateFilt."    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Status = 1 
                                ORDER BY BS.BS_Date DESC" ;
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->MasterReportArray[$count] = $row;
                $count++;
            }
        }
    }
    
    //--------------------------- BalanceSheet Cash Bank Income Expense Details ----------------------------//
    function reportBMRVisualDetails($filt,$stDate='',$enDate='',$filter='') {			
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
        $this->MasterReportArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type, BS.PM_Id ,IT.IT_Transfers,BS.BS_Complete
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                                WHERE IT.IT_Business = '0'
                                AND BS.BS_Status = 1
                                AND BS.PM_Id = 1
                                AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                ".$dateFilt."
                                AND LC.".$filter."
                                GROUP BY MH.MH_Type, IT.IT_Transfers
                                 ");
        
        while($row=mysqli_fetch_object($result)) {
            $this->MasterReportArray[$count] = $row;
            $count++;
        }
        
        
        //----------- Current Bank Based Income/Expense Details ---------------------//
        $sql = "SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type, BS.PM_Id ,IT.IT_Transfers,BS.BS_Complete
                    FROM balance_sheets AS BS 
                        LEFT JOIN bank_accounts AS BA ON BS.BA_Id = BA.BA_Id
                        LEFT JOIN locations AS LC ON LC.LC_Id = BA.LC_Id
                        LEFT JOIN items AS IT ON IT.IT_Id = BS.IT_Id
                        LEFT JOIN sub_heads AS SH ON SH.SH_Id = IT.SH_Id
                        LEFT JOIN main_heads AS MH ON MH.MH_Id = SH.MH_Id
                        WHERE IT.IT_Business = '0'
                            AND BS.BS_Status = 1
                            AND BS.PM_Id = 2
                            AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                            ".$dateFilt."
                            AND LC.".$filter."
                                GROUP BY MH.MH_Type, IT.IT_Transfers ";
        $result =  mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($result)) {
            $this->MasterReportArray[$count] = $row;
            $count++;
        }
        
    }
    
//    function reportBranchItemData($LCId,$filt,$stDate='',$enDate=''){ //Unused
//        $dateFilt = '';
//        
//        if($stDate!='' && $enDate!=''){
//            $stDate= date("Y-m-d", strtotime($stDate));
//            $enDate= date("Y-m-d", strtotime($enDate));
//            $dateFilt=" AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
//        }elseif($stDate=='' && $enDate!=''){
//            $enDate= date("Y-m-d", strtotime($enDate));
//            $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
//        }elseif($stDate!='' && $enDate==''){
//            $stDate= date("Y-m-d", strtotime($stDate));
//            $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
//        }
//        
//        $count=0;
//        $this->MasterReportArray = array();
//        $sql = "SELECT SUM( BS.BS_Amount ) AS BS_Amount ,BS.BS_IEByLC,IT.IT_Id, IT.IT_Name,IT.IT_Transfers,IT.IT_Business,
//                    MH.MH_Type,MH.MH_Name,SH.SH_Id,SH.SH_Name
//                    FROM `balance_sheets` AS BS
//                        LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
//                        LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
//                        LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
//                            WHERE  ((BS.BS_IEByLC = '".$LCId."' AND BS.PM_Id =1) OR (BS.BA_Id IN(11,12,15,23,27,28,59) AND (BS.PM_Id =2)))  
//                                    AND BS.BS_Status = 1 
//                                    AND ".$filt."
//                                    ".$dateFilt."
//                                        GROUP BY IT.IT_Id ORDER BY IT.IT_Name";
//        $result = mysqli_query($GLOBALS['con'],$sql);
//        if($result){
//            while($row = mysqli_fetch_object($result)) {
//                $this->MasterReportArray[$count] = $row;
//                $count++;
//            }
//        }
//    }

    function entryTypeSumItemRpt($filt,$stDate='',$enDate='') { // get total amount for entry based types in Item Based Reports(grid footer data)
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        } elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
        } elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
        }
        
        $sql = "SELECT
                SUM(IF(IT.MH_Type = 1 && IT.IT_Transfers = 1, BS.BS_Amount , 0)) AS InternalTransferReceived,
                SUM(IF(IT.MH_Type = 2 && IT.IT_Transfers = 1, BS.BS_Amount , 0)) AS InternalTransferPaid,
                SUM(IF(IT.IT_Business = 1 && IT.IT_Transfers = 0 && MH.MH_Type = 1 , BS.BS_Amount, 0)) AS BusinessReceived,
                SUM(IF(IT.IT_Business = 1 && IT.IT_Transfers = 0 && MH.MH_Type = 2 , BS.BS_Amount, 0)) AS BusinessReturned, 
                SUM(IF(IT.IT_Business = 0 && IT.IT_Transfers = 0 && MH.MH_Type = 1 , BS.BS_Amount, 0)) AS Income,
                SUM(IF(IT.IT_Business = 0 && IT.IT_Transfers = 0 && MH.MH_Type = 2 , BS.BS_Amount, 0)) AS Expense    
                FROM  `balance_sheets` AS BS   
                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id  
                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id   
                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id   
                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id   
                WHERE  ".$filt." ".$dateFilt;
        $result = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_fetch_array($result,MYSQLI_ASSOC);
        
    }
    
}
?>