<?php
require_once("connection.php");
require_once("UserClass.php");

class AccountSummaryClass {
    var $MasterReportArray;
    var $BranchStkRptArray;

    //-------------------------------------- Branch Based Reports ---------------------------------------------//
          
    function reportBranches($of_id,$stDate,$enDate,$indx)
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
        
        $sql = "SELECT LC.LC_Id,LC.LC_Name,MH.MH_Type,IT.IT_Business, IT.IT_Transfers, SUM(BS.BS_Amount) AS BS_IEAmt, BS.PM_Id,MONTH(BS.BS_Date) AS INDX_MNTH,YEAR(BS.BS_Date) AS INDX_YEAR 
                FROM balance_sheets AS BS 
                    LEFT JOIN locations AS LC ON LC.LC_Id = BS.LC_Id
                    LEFT JOIN items AS IT ON IT.IT_Id = BS.IT_Id
                    LEFT JOIN sub_heads AS SH ON SH.SH_Id = IT.SH_Id
                    LEFT JOIN main_heads AS MH ON MH.MH_Id = SH.MH_Id
                    WHERE LC.OF_Id= ".$of_id."
                        AND BS.BS_Status = 1
                        AND BS.PM_Id = 1 
                        AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                        ".$dateFilt." 
                            GROUP BY MONTH(BS.BS_Date),YEAR(BS.BS_Date),MH.MH_Type,IT.IT_Business,IT.IT_Transfers
                            ORDER BY LC.LC_Name";
        $result =  mysqli_query($GLOBALS['con'],$sql);
        
        $lcIDCsh = $templcIDCsh='';
        while($row=mysqli_fetch_object($result)) {
//            $this->BranchCshRptArray[$row->LC_Id] = $row;
            
            $indx=str_pad($row->INDX_MNTH,2,"0",STR_PAD_LEFT)."-".$row->INDX_YEAR;
            if($templcIDCsh != $lcIDCsh){
                $this->BranchCshRptArray[$indx]['index']        = $indx;
                $this->BranchCshRptArray[$indx]['CshInc']       = 0;
                $this->BranchCshRptArray[$indx]['CshExp']       = 0;
                $this->BranchCshRptArray[$indx]['CshBussInc']   = 0;
                $this->BranchCshRptArray[$indx]['CshBussExp']   = 0;
                $this->BranchCshRptArray[$indx]['CshTransRecv'] = 0;
                $this->BranchCshRptArray[$indx]['CshTransPaid'] = 0;
                $lcIDCsh = $templcIDCsh;
            }
            
            if($row->MH_Type == 1 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->BranchCshRptArray[$indx]['CshInc'] += $row->BS_IEAmt;
            }else if($row->MH_Type == 2 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->BranchCshRptArray[$indx]['CshExp'] += $row->BS_IEAmt;
            }else if($row->IT_Business == 1 && $row->MH_Type == 1 ) {
                    $this->BranchCshRptArray[$indx]['CshBussInc'] = $this->BranchCshRptArray[$indx]['CshBussInc'] + $row->BS_IEAmt;
            }else if($row->IT_Business == 1 && $row->MH_Type == 2 ) {
                    $this->BranchCshRptArray[$indx]['CshBussExp'] = $this->BranchCshRptArray[$indx]['CshBussExp'] + $row->BS_IEAmt;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 1 ) {
                    $this->BranchCshRptArray[$indx]['CshTransRecv']  += $row->BS_IEAmt;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 2 ) {
                    $this->BranchCshRptArray[$indx]['CshTransPaid']  += $row->BS_IEAmt;
            }
        
        }
        
        //----------- Current Bank Based Income/Expense Details ---------------------//
        $sql = "SELECT LC.LC_Id,LC.LC_Name,MH.MH_Type,IT.IT_Business, IT.IT_Transfers, SUM(BS.BS_Amount) AS BS_IEAmt, BS.PM_Id,MONTH(BS.BS_Date) AS INDX_MNTH,YEAR(BS.BS_Date) AS INDX_YEAR 
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
                            GROUP BY MONTH(BS.BS_Date),YEAR(BS.BS_Date),MH.MH_Type,IT.IT_Business,IT.IT_Transfers
                            ORDER BY LC.LC_Name";
        $result =  mysqli_query($GLOBALS['con'],$sql);
        
        $lcIDBnk = $templcIDBnk='';
        while($row=mysqli_fetch_object($result)) {
            $indx=str_pad($row->INDX_MNTH,2,"0",STR_PAD_LEFT)."-".$row->INDX_YEAR;
            if($templcIDBnk != $lcIDBnk){
                $this->BranchBnkRptArray[$indx]['LC_Id']        = $indx;
                $this->BranchBnkRptArray[$indx]['BnkInc']       = 0;
                $this->BranchBnkRptArray[$indx]['BnkExp']       = 0;
                $this->BranchBnkRptArray[$indx]['BnkBussInc']   = 0;
                $this->BranchBnkRptArray[$indx]['BnkBussExp']   = 0;
                $this->BranchBnkRptArray[$indx]['BnkTransRecv'] = 0;
                $this->BranchBnkRptArray[$indx]['BnkTransPaid'] = 0;
                $lcIDBnk = $templcIDBnk;
            }
            
            if($row->MH_Type == 1 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->BranchBnkRptArray[$indx]['BnkInc'] += $row->BS_IEAmt;
            }else if($row->MH_Type == 2 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->BranchBnkRptArray[$indx]['BnkExp'] += $row->BS_IEAmt;
            }else if($row->IT_Business == 1 && $row->MH_Type == 1 ) {
                    $this->BranchBnkRptArray[$indx]['BnkBussInc'] = $this->BranchBnkRptArray[$indx]['BnkBussInc'] + $row->BS_IEAmt;
            }else if($row->IT_Business == 1 && $row->MH_Type == 2 ) {
                    $this->BranchBnkRptArray[$indx]['BnkBussExp'] = $this->BranchBnkRptArray[$indx]['BnkBussExp'] + $row->BS_IEAmt;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 1 ) {
                    $this->BranchBnkRptArray[$indx]['BnkTransRecv']  += $row->BS_IEAmt;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 2 ) {
                    $this->BranchBnkRptArray[$indx]['BnkTransPaid']  += $row->BS_IEAmt;
            }
        }
        
        
        
        /*-----------------------------------------_Current Stock Details_-----------------------------------------*/
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
        $this->StockArray = array();
        $sql="SELECT SUM( BS.BS_Amount ) AS BS_Amount, IT.IT_Business, IT.MH_Type,YEAR(BS.BS_Date) AS T_YEAR,MONTH(BS.BS_Date) AS T_MONTH
                    FROM `balance_sheets` AS BS
                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                    LEFT JOIN `users_auth` AS US ON TR.US_Id = US.US_Id
                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                    WHERE BS.BS_Status =1 
                    AND SH.SH_Track = '1'                    
                    AND IF( BS.BS_PettyCashRefId != 0 , IT.MH_Type = 1 , IT.MH_Type IN ('1','2'))
                    AND LC.OF_Id =".$of_id." ".$dateFilt." 
                    GROUP BY IT.IT_Business, IT.MH_Type,YEAR(BS.BS_Date),MONTH(BS.BS_Date)
                    ORDER BY T_YEAR,T_MONTH";
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $lcIDStk = $templcIDStk='';
        if($result){
            while($row = mysqli_fetch_object($result)) {
                    $indx=str_pad($row->T_MONTH,2,"0",STR_PAD_LEFT)."-".$row->T_YEAR;
                    if($templcIDStk != $lcIDStk){
                    $this->StockArray[$indx]['Business']   = 0;
                    $this->StockArray[$indx]['Income']     = 0;
                    $this->StockArray[$indx]['Expense']    = 0;
                    $this->StockArray[$indx]['CurrStk']    = 0;
                    $lcIDStk = $templcIDStk;
                    }
                if($row->MH_Type=="1") {
                    if($row->IT_Business=="1") {
                        $this->StockArray[$indx]['Business'] += $row->BS_Amount;
                    }else {
                       $this->StockArray[$indx]['Income'] += $row->BS_Amount;
                    }
                }else if($row->MH_Type=="2"){
                    if($row->IT_Business=="1"){
                        $this->StockArray[$indx]['Business'] = $this->StockArray[$indx]['Business'] - $row->BS_Amount;
                    }else{
                       $this->StockArray[$indx]['Expense'] += $row->BS_Amount;
                    }
                }  
                
               $this->StockArray[$indx]['CurrStk']    =$this->StockArray[$indx]['Business']- $this->StockArray[$indx]['Income']; 
            }
        } 
        
    }
        
    //--------------------------------- Bank Opening Balance of each Branch  ----------------------------------------//
    function getBranchBankOB($stDate='',$enDate='', $OFId,$indx)
    {
        $dateFilt = '';
        
        if($stDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   <'".$stDate."'";
        }

        $count=0;
        $this->BankOBArray = array(); 
        $BankOB = array();
        $BankIncExp = array();
        
        //--------------- Starting Bank Opening Balance ---------------------//
        $sql = "SELECT SUM(OB.BnkOB_OpenBal) AS OB_Amount,MIN(BnkOB_CDate) AS StrtDate, BA.LC_Id, BA.BA_Id
                    FROM bank_open_bals AS OB
                        LEFT JOIN bank_accounts AS BA ON BA.BA_Id = OB.BA_Id
                            WHERE OB.BnkOB_Status =1 AND OB.OF_Id = ".$OFId;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($result)) {
            $BankOB = $row;
        } 
        
        //--------------- Current Date ( From Date ) Bank Opening Balance (Inc/Exp of Previous) ---------------------//
        $sql="SELECT SUM( BS.BS_Amount ) AS IE_Amount , MH.MH_Type, LC.LC_Id, BA.BA_Id 
                                FROM balance_sheets AS BS 
                                    LEFT JOIN bank_accounts AS BA ON BS.BA_Id = BA.BA_Id
                                    LEFT JOIN locations AS LC ON LC.LC_Id = BA.LC_Id
                                    LEFT JOIN items AS IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN sub_heads AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN main_heads AS MH ON SH.MH_Id = MH.MH_Id
                                     WHERE IT.IT_Business = '0'
                                        AND BS.BS_Status = 1
                                        AND BS.PM_Id = 2 
                                        AND LC.OF_Id =".$OFId." 
                                        AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                        ".$dateFilt."
                                            GROUP BY MH.MH_Type
                                                ORDER BY MONTH(BS.BS_Date),YEAR(BS.BS_Date)";
        $result = mysqli_query($GLOBALS['con'],$sql);

        while($row=mysqli_fetch_object($result)) {            
            if($row->MH_Type == "1") {
                   $BankIncExp[$indx]['Income'] += $row->IE_Amount;
            }else if($row->MH_Type == "2"){
                   $BankIncExp[$indx]['Expense'] += $row->IE_Amount;
            }
        }        
        /*foreach ($BankOB as $key=>$value) {                */
            $this->BankOBArray['Amt'] = $BankOB->OB_Amount + $BankIncExp[$indx]['Income'] - $BankIncExp[$indx]['Expense'];
            $this->BankOBArray['StrtDate'] = $BankOB->StrtDate; 
        /*}*/
        
        
    }
    
    //--------------------------------- Cash Opening Balance of each Branch  ----------------------------------------//
    function getBranchCashOB($stDate='',$enDate='', $OFId,$indx)
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
        $sql = "SELECT SUM(OB.OB_OpenBal) AS OB_Amount,MIN(OB.OB_Date) AS StrtDate, LC_Id
                    FROM cash_open_bals AS OB
                    WHERE OB.OB_Status=1 AND OF_Id = ".$OFId;
        $res = mysqli_query($GLOBALS['con'],$sql);
        
        while($row=mysqli_fetch_object($res)) {
            $CashOB = $row;            
        }

        //--------------- Current Date ( From Date ) Cash Opening Balance (Inc/Exp of Previous) ---------------------//
        $sql="SELECT SUM( BS.BS_Amount ) AS IE_Amount , MH.MH_Type, LC.LC_Id
                                FROM balance_sheets AS BS 
                                    LEFT JOIN `items` AS IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
                                        WHERE BS.BS_Status = 1
                                        AND IT.IT_Business = '0'
                                        AND BS.PM_Id = 1                                        
                                        AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                        AND LC.OF_Id = ".$OFId."
                                        ".$dateFilt."
                                            GROUP BY MH.MH_Type
                                                ORDER BY LC.LC_Name";
        $result = mysqli_query($GLOBALS['con'],$sql);
         
        while($row=mysqli_fetch_object($result)) {
            if($row->MH_Type == "1") {
                   $CashIncExp[$indx]['Income'] += $row->IE_Amount;
            }else if($row->MH_Type == "2"){
                   $CashIncExp[$indx]['Expense'] += $row->IE_Amount;
            }
        }
       
        /*foreach ($CashOB as $key=>$value) {*/
            $this->CashOBArray["Amt"] = $CashOB->OB_Amount + $CashIncExp[$indx]['Income'] - $CashIncExp[$indx]['Expense'];
            $this->CashOBArray["StrtDate"]=$CashOB->StrtDate;
        /*}*/
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
        
        $sql = "SELECT SUM(OS_OpenBal) AS OS_OpenBal,MIN(OS_Date) AS OS_Date,LC_Id FROM stock_open_bals WHERE OS_Status=1 AND OF_Id = ".$OFId ;
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $this->BranchStkRptArray = array();
        while($row = mysqli_fetch_object($result)) { 
            $this->BranchStkRptArray['CurrentPeriodOldStock'] = $row->OS_OpenBal;
            $this->BranchStkRptArray['StrtDate'] = $row->OS_Date;
        }                 
        $sql="SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type,LC.LC_Id
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
				LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE BS.BS_Status = 1 AND SH.SH_Track='1' 
                                    AND IT.IT_Id != 219
                                    AND IF( BS.BS_PettyCashRefId != 0 , IT.MH_Type = 1 , IT.MH_Type IN ('1','2'))
                                    AND BS.BS_Date < '".$stDate."'
                                    ".$filter_mask."
                                    AND LC.OF_Id = ".$OFId."
                                GROUP BY IT.IT_Business,IT.MH_Type";
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result){
            while($row = mysqli_fetch_object($result)) {
                
                if($row->MH_Type=="1") {
                    if($row->IT_Business=="1") {
                        $this->BranchStkRptArray['Business'] += $row->BS_Amount;
                    }else {
                       $this->BranchStkRptArray['Income'] += $row->BS_Amount;
                    }
                }else if($row->MH_Type=="2"){
                    if($row->IT_Business=="1"){
                        $this->BranchStkRptArray['Business'] = $this->BranchStkRptArray['Business'] - $row->BS_Amount;
                    }else{
                       $this->BranchStkRptArray['Expense'] += $row->BS_Amount;
                    }
                }
                
            }
        }	
        
        if($stDate){
            $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type,LC.LC_Id
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id                                    
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id                                    
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                    WHERE BS.BS_Status = 1 
                                        AND SH.SH_Track = 1
                                        AND IF( BS.BS_PettyCashRefId != 0 , IT.MH_Type = 1 , IT.MH_Type IN ('1','2'))
                                        AND BS.BS_Date < '".$stDate."'
                                        ".$filter_mask."
                                        AND LC.OF_Id = ".$OFId."
                                    GROUP BY IT.IT_Business,IT.MH_Type  ORDER BY LC.LC_Name"
                                    );
            if($result){

                while($row = mysqli_fetch_object($result)) {

                    if($row->MH_Type=="1") {
                        if($row->IT_Business=="1") {
                            $this->BranchStkRptArray['Old_Business'] += $row->BS_Amount;
                        }else {
                           $this->BranchStkRptArray['Old_Income'] += $row->BS_Amount;
                        }
                    }else if($row->MH_Type=="2"){
                        if($row->IT_Business=="1"){
                            $this->BranchStkRptArray['Old_Business'] = $this->BranchStkRptArray['Old_Business'] - $row->BS_Amount;
                        }
                    }
                }
            }
        }	
        
    }
    

    
}
?>