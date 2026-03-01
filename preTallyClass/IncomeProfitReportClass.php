<?php
require_once("connection.php");

class IncomeProfitReportClass {
    function reportBusinessData($lcfilt,$filt,$stDate='',$enDate='',$oldStockFrom,$oldStockTo) {		
        $dateFilt = '';
        
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }

        $count                      = 0;
        $this->StockArray           = array();  // used in reportBusinessSummary()
        
        $sql    = "SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status,SUM(SO.OS_OpenBal) AS OS FROM locations AS LC LEFT JOIN stock_open_bals AS SO ON LC.LC_Id = SO.LC_Id WHERE $lcfilt AND LC.LC_Status != 5 GROUP BY LC.LC_Id ORDER BY LC.LC_Name";
        $stockResult    = mysqli_query($GLOBALS['con'],$sql);
        while($stockRow = mysqli_fetch_object($stockResult)) {
            $this->StockArray[$stockRow->LC_Id]['CurrentPeriodOldStock'] = $stockRow->OS;
            $this->BranchArray[$stockRow->LC_Id] = $stockRow;
        } 

        if($oldStockFrom) {
            $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE BS.BS_Status = 1 AND SH.SH_Track = 1  
                                AND IT.MH_Type IN ('1','2')  
                                AND BS.BS_Date < '".$oldStockFrom."'    
                                GROUP BY IT.IT_Business, IT.MH_Type");
            if($result){
                
                while($row = mysqli_fetch_object($result)) {
                    if($row->MH_Type == "1") {
                        if($row->IT_Business == "1") {
                            $this->StockArray[$row->LC_Id]['Old_Business'] += $row->BS_Amount;
                        } elseif($row->IT_Business == "0") {
                           $this->StockArray[$row->LC_Id]['Old_Income'] += $row->BS_Amount;
                        }
                    } else if($row->MH_Type == "2"){
                        if($row->IT_Business == "1"){
                            $this->StockArray[$row->LC_Id]['Old_Business'] = $this->StockArray[$row->LC_Id]['Old_Business'] - $row->BS_Amount;
                        } elseif($row->IT_Business == "0"){
                           $this->StockArray[$row->LC_Id]['Old_Expense'] += $row->BS_Amount;
                        }
                    }
                }
            }
        }
        
        $sql = "SELECT BS.BS_Amount,IT.IT_Id, LC.LC_Name, 
            YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS MONTH,MH.MH_Type, LC.LC_Id 
            FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
            LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
            WHERE  ".$filt." ".$dateFilt."  ORDER BY LC.LC_Name";
           
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $this->BussRecvArray     = array();
        $this->BussReturnedArray = array();
        $this->BusinessArray     = array();
        if($result){
            while($row = mysqli_fetch_object($result)) {
                if($row->MH_Type == "1") {
                    $this->BussRecvArray[$row->LC_Id][$row->YEAR][$row->MONTH] += $row->BS_Amount;
                } elseif($row->MH_Type == "2") {
                    $this->BussReturnedArray[$row->LC_Id][$row->YEAR][$row->MONTH] += $row->BS_Amount;
                }
            }
        }
        
        /* Branch based monthwise data */
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount
                    FROM  `balance_sheets` AS BS
                    LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                    LEFT JOIN  `locations` AS LC ON BS.LC_Id = LC.LC_Id
                    WHERE ".$filt." ".$dateFilt." 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = ($this->BussRecvArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] - $this->BussReturnedArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]);
            }
        }
    }
    
    function reportStatutoryData($lcfilt,$filt,$stDate='',$enDate='',$preTally_user_ofid) {
        
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
            
        $this->StatMonthReportArray = array();
        
        $statItemsSql      = mysqli_query($GLOBALS['con'],"SELECT PRS_Stat_Items FROM profit_report_settings WHERE OF_Id = $preTally_user_ofid");
        $statExpenseResult = mysqli_fetch_array($statItemsSql,MYSQLI_ASSOC);
        $statExpenseItems  = $statExpenseResult['PRS_Stat_Items'];
        
        /* Branch based monthwise data */
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
                    WHERE $filt AND IT.IT_Id IN ($statExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->StatMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status FROM locations AS LC WHERE $lcfilt AND LC.LC_Status != 5 ORDER BY LC.LC_Name");
        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id] = $row;
            }
        }
    }
    
    function listExpenseItems($filter,$start,$end) {
        $count          = 0;
        $this->ItemArray= array();
        $sql            = "SELECT IT.IT_Name,SH.SH_Name,IT.IT_Id    
                    FROM  items as IT 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id  
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id  
                    WHERE IT.MH_Type = 2 AND IT.IT_Status != 4 $filter ORDER BY IT.IT_Name 
                    LIMIT $start,$end ";

        $result         =   mysqli_query($GLOBALS['con'],$sql);
        while($row  =   mysqli_fetch_object($result)) {
            $this->ItemArray[$count]    =   $row;
            $count++;
        }	
    }
    
    function listExpenseItemsCount($filter) {
        $sql    = "SELECT COUNT(IT.IT_Id) AS COUNT     
                    FROM  items as IT 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id  
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id  
                    WHERE IT.MH_Type = 2 AND IT.IT_Status != 4 $filter ORDER BY IT.IT_Name ";

        $result = mysqli_query($GLOBALS['con'],$sql);
        $row    = mysqli_fetch_array($result,MYSQLI_NUM);
        return $row[0];		
    }
    
    function fixedExpenseSettingsSave($preTally_user_ofid){
        $checkQuery     = mysqli_query($GLOBALS['con'],"SELECT PRS_Id FROM profit_report_settings WHERE OF_Id = ".$preTally_user_ofid);
        if(mysqli_num_rows($checkQuery)) {
            $row        = mysqli_fetch_array($checkQuery,MYSQLI_ASSOC);
            $PRS_Id     = $row['PRS_Id'];
            $sql    = "UPDATE profit_report_settings SET PRS_Stat_Items = '".$this->PRS_Data['PRS_Stat_Items']."',PRS_Fixed_Items = '".$this->PRS_Data['PRS_Fixed_Items']."',PRS_Variable_Items = '".$this->PRS_Data['PRS_Variable_Items']."',PRS_General_Items = '".$this->PRS_Data['PRS_General_Items']."',PRS_LastUpdated = '".$this->PRS_Data['PRS_LastUpdated']."',PRS_MDate = '".$this->PRS_Data['PRS_MDate']."' WHERE PRS_Id = $PRS_Id";
            mysqli_query($GLOBALS['con'],$sql);
        } else {
            $sql        = "INSERT INTO profit_report_settings ( " . implode(', ',array_keys($this->PRS_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->PRS_Data)) . "'" . ")";
            mysqli_query($GLOBALS['con'],$sql);
        }
        return 'Successfully updated expense settings';
    }
    
    function getfixedExpenseItems($preTally_user_ofid) {
        $checkQuery     = mysqli_query($GLOBALS['con'],"SELECT PRS_Stat_Items,PRS_Fixed_Items,PRS_Variable_Items,PRS_General_Items FROM profit_report_settings WHERE OF_Id = ".$preTally_user_ofid);
        $row            = mysqli_fetch_array($checkQuery,MYSQLI_ASSOC);
        return $row;
    }
    
    function reportFixedExpenseData($lcfilt,$filt,$stDate='',$enDate='',$preTally_user_ofid) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
            
        $this->FixedMonthReportArray = array();
        
        $fixedItemsSql      = mysqli_query($GLOBALS['con'],"SELECT PRS_Fixed_Items FROM profit_report_settings WHERE OF_Id = $preTally_user_ofid");
        $fixedExpenseResult = mysqli_fetch_array($fixedItemsSql,MYSQLI_ASSOC);
        $fixedExpenseItems  = $fixedExpenseResult['PRS_Fixed_Items'];
       
        /* Branch based monthwise data */
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE $filt AND IT.IT_Id IN ($fixedExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FixedMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status FROM locations AS LC WHERE $lcfilt AND LC.LC_Status != 5 ORDER BY LC.LC_Name");
        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id] = $row;
            }
        }
    }
    
    function reportReportOverview($startDate,$endDate,$OF_Id,$filter){        
        if($startDate != '' && $endDate != ''){
            $stDate     = date("Y-m-d", strtotime($startDate));
            $enDate     = date("Y-m-d", strtotime($endDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        /*  Get all branches Start */
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status FROM locations AS LC WHERE LC.OF_Id = $OF_Id AND LC.LC_Status != 5 $filter ORDER BY LC.LC_Name");
        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id] = $row;
                $this->BusinessOverviewArray[$row->LC_Id]    = 0;
                $this->StatutoryOverviewArray[$row->LC_Id]   = 0;
                $this->GeneralOverviewArray[$row->LC_Id]     = 0;
                $this->FixedExpOverviewArray[$row->LC_Id]    = 0;
                $this->VariableExpOverviewArray[$row->LC_Id] = 0;
            }
        }
        /*  Get all branches End */
        
        //////////////////////////////  Business Start //////////////////////////
        $sql = "SELECT BS.BS_Amount , 
            YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS MONTH,MH.MH_Type, LC.LC_Id 
            FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
            LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
            WHERE  IT.MH_Type =1 $filter AND IT.IT_Business = 1 AND BS.TR_Id != 'NULL' AND  BS.TR_Id != '0'  
            AND LC.OF_Id = $OF_Id AND BS.BS_Status = 1 $dateFilt ORDER BY LC.LC_Name";
           
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $this->BussRecvArray     = array();
        $this->BussReturnedArray = array();
     
        if($result){
            while($row = mysqli_fetch_object($result)) {
                if($row->MH_Type == "1") {
                    $this->BussRecvArray[$row->LC_Id] += $row->BS_Amount;
                }elseif($row->MH_Type == "2") {
                    $this->BussReturnedArray[$row->LC_Id] += $row->BS_Amount;
                }
            }
        }
        /////////////////////////////  Business End /////////////////////////////////
        
        ////////////////////////  Fixed items Start /////////////////////////////////

        $fixedExpenseResult = $this->getfixedExpenseItems($OF_Id);
        $fixedExpenseItems  = $fixedExpenseResult['PRS_Fixed_Items'];
        
        $fixedExpQuery = mysqli_query($GLOBALS['con']," SELECT LC.LC_Id, SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE  BS.BS_IEByLC != 'NULL' $filter AND LC.OF_Id = $OF_Id AND BS.BS_Status = 1  
                    AND IT.IT_Id IN ($fixedExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id ORDER BY LC.LC_Name ");
        
        if($fixedExpQuery){
            while($row = mysqli_fetch_object($fixedExpQuery)) {
                $this->FixedExpOverviewArray[$row->LC_Id] = $row->BS_Amount;
            }
        }
        /////////////////////////////////  Fixed items End ///////////////////////////
        
        //////////////////////////////// Statutory amount Start //////////////////////

        $statExpenseItems   = $fixedExpenseResult['PRS_Stat_Items'];
        $statExpQuery       = mysqli_query($GLOBALS['con']," SELECT LC.LC_Id, SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
                    WHERE  LC.OF_Id = $OF_Id $filter AND BS.BS_Status = 1  
                    AND IT.IT_Id IN ($statExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id ORDER BY LC.LC_Name ");
        
        if($statExpQuery){
            while($row = mysqli_fetch_object($statExpQuery)) {
                $this->StatutoryOverviewArray[$row->LC_Id] = $row->BS_Amount;
            }
        }
        ////////////////////////////////// Statutory amount End //////////////////////
        
        ////////////////////////////// Variable expense amount Start /////////////////
        
        $varExpenseItems    = $fixedExpenseResult['PRS_Variable_Items'];
        $varExpQuery        = mysqli_query($GLOBALS['con']," SELECT LC.LC_Id, SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE  BS.BS_IEByLC != 'NULL' AND LC.OF_Id = $OF_Id $filter AND BS.BS_Status = 1  
                    AND IT.IT_Id IN ($varExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id ORDER BY LC.LC_Name ");
        
        if($varExpQuery){
            while($row = mysqli_fetch_object($varExpQuery)) {
                $this->VariableExpOverviewArray[$row->LC_Id] = $row->BS_Amount;
            }
        }
        ////////////////////////////// Variable expense amount End /////////////////
        
        ////////////////////////////// General expense amount Start /////////////////
        
        $genExpenseItems    = $fixedExpenseResult['PRS_General_Items'];
        $genExpQuery        = mysqli_query($GLOBALS['con']," SELECT LC.LC_Id, SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE  BS.BS_IEByLC != 'NULL' AND LC.OF_Id = $OF_Id $filter AND BS.BS_Status = 1  
                    AND IT.IT_Id IN ($genExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id ORDER BY LC.LC_Name ");
        
        if($genExpQuery){
            while($row = mysqli_fetch_object($genExpQuery)) {
                $this->GeneralOverviewArray[$row->LC_Id] = $row->BS_Amount;
            }
        }
        ////////////////////////////// General expense amount End /////////////////
    }
    
    function reportVariableExpenseData($lcfilt,$filt,$stDate='',$enDate='',$preTally_user_ofid) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
            
        $this->VarMonthReportArray  = array();
        
        $variableItemsSql      = mysqli_query($GLOBALS['con'],"SELECT PRS_Variable_Items FROM profit_report_settings WHERE OF_Id = $preTally_user_ofid");
        $variableExpenseResult = mysqli_fetch_array($variableItemsSql,MYSQLI_ASSOC);
        $variableExpenseItems  = $variableExpenseResult['PRS_Variable_Items'];
        
        /* Branch based monthwise data */
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE $filt AND IT.IT_Id IN ($variableExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->VarMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status FROM locations AS LC WHERE $lcfilt AND LC.LC_Status != 5 ORDER BY LC.LC_Name");
        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id] = $row;
            }
        }
    }
    
    function reportGrossData($filt,$stDate='',$enDate='',$OF_Id) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        
        $filter     = ' IT.MH_Type =1 AND IT.IT_Business = 1 AND BS.TR_Id != "NULL" AND  BS.TR_Id != "0"  AND LC.OF_Id = '.$OF_Id.' AND BS.BS_Status = 1 '.$filt;
        $sql = "SELECT BS.BS_Amount , 
            YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS MONTH,MH.MH_Type, LC.LC_Id 
            FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
            LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
            WHERE $filter  $dateFilt ORDER BY LC.LC_Name";
        
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $this->BussRecvArray     = array();
        $this->BussReturnedArray = array();
     
        if($result){
            while($row = mysqli_fetch_object($result)) {
                if($row->MH_Type == "1") {
                    $this->BussRecvArray[$row->LC_Id][$row->YEAR][$row->MONTH] += $row->BS_Amount;
                } elseif($row->MH_Type == "2") {
                    $this->BussReturnedArray[$row->LC_Id][$row->YEAR][$row->MONTH] += $row->BS_Amount;
                }
            }
        }
        
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount
                    FROM  `balance_sheets` AS BS
                    LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                    LEFT JOIN  `locations` AS LC ON BS.LC_Id = LC.LC_Id
                    WHERE ".$filter." ".$dateFilt." 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = ($this->BussRecvArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] - $this->BussReturnedArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]);
                $this->TaxArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]      = ($this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] / 115) * 15;
            }
        }
        
        $filter     = " LC.OF_Id = $OF_Id AND BS.BS_Status = 1 ".$filt;
        
        $fixedExpenseResult = $this->getfixedExpenseItems($OF_Id);
        $statExpenseItems   = $fixedExpenseResult['PRS_Stat_Items'];
       
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
                    WHERE $filter AND IT.IT_Id IN ($statExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->StatMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status FROM locations AS LC WHERE LC.OF_Id = $OF_Id AND LC.LC_Status != 5 $filt ORDER BY LC.LC_Name");
        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id] = $row;
            }
        }
    }
    
    function reportGeneralExpenseData($lcfilt,$filt,$stDate='',$enDate='',$preTally_user_ofid) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
            
        $this->GenMonthReportArray  = array();
        
        $genItemsSql      = mysqli_query($GLOBALS['con'],"SELECT PRS_General_Items FROM profit_report_settings WHERE OF_Id = $preTally_user_ofid");
        $genExpenseResult = mysqli_fetch_array($genItemsSql,MYSQLI_ASSOC);
        $genExpenseItems  = $genExpenseResult['PRS_General_Items'];
        
        /* Branch based monthwise data */
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE $filt AND IT.IT_Id IN ($genExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->GenMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status FROM locations AS LC WHERE $lcfilt AND LC.LC_Status != 5 ORDER BY LC.LC_Name");
        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id] = $row;
            }
        }
    }
    
    function reportNetProfitData($filt,$stDate='',$enDate='',$OF_Id) {
        
        $this->BussRecvArray        = array();
        $this->BussReturnedArray    = array();
        $this->BusinessArray        = array();
        $this->TaxArray             = array();
        $this->VarMonthReportArray  = array();
        $this->FixedMonthReportArray= array();
        $this->GenMonthReportArray  = array();
        $this->StatMonthReportArray = array();
        $this->BranchArray          = array();
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        
        ////////////////////// Business Starts /////////////////////////////////
        $sql = "SELECT BS.BS_Amount , 
            YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS MONTH,MH.MH_Type, LC.LC_Id 
            FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
            LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
            WHERE IT.MH_Type =1 AND IT.IT_Business = 1 AND BS.TR_Id != 'NULL' AND  BS.TR_Id != '0'  
            AND LC.OF_Id = $OF_Id $filt AND BS.BS_Status = 1 $dateFilt ORDER BY LC.LC_Name";

        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                if($row->MH_Type == "1") {
                    $this->BussRecvArray[$row->LC_Id][$row->YEAR][$row->MONTH] += $row->BS_Amount;
                } elseif($row->MH_Type == "2") {
                    $this->BussReturnedArray[$row->LC_Id][$row->YEAR][$row->MONTH] += $row->BS_Amount;
                }
            }
        }
        
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount
                    FROM  `balance_sheets` AS BS
                    LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                    LEFT JOIN  `locations` AS LC ON BS.LC_Id = LC.LC_Id
                    WHERE  IT.MH_Type =1 AND IT.IT_Business = 1 AND BS.TR_Id != 'NULL' AND  BS.TR_Id != '0'  
                    AND LC.OF_Id = $OF_Id  $filt AND BS.BS_Status = 1 $dateFilt  
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]       = ($this->BussRecvArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] - $this->BussReturnedArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]);
                $this->TaxArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]            = ($this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] / 115 ) * 15;
            }
        }
        
        ////////////////////// Business Ends /////////////////////////////////
        
        $filter         = $filt." AND BS.BS_IEByLC != 'NULL'  AND LC.OF_Id = $OF_Id AND BS.BS_Status = 1 ";
        $expItemsResult = $this->getfixedExpenseItems($OF_Id);
        
        ////////////////////// Branch Flexi Expense Starts /////////////////////////////////
        
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE IT.IT_Id IN (".$expItemsResult['PRS_Variable_Items'].") $filter $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->VarMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        ////////////////////// Branch Flexi Expense Ends /////////////////////////////////

        
        ////////////////////// Branch Fixed Expense Starts /////////////////////////////////

        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE IT.IT_Id IN (".$expItemsResult['PRS_Fixed_Items'].") $filter $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FixedMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        ////////////////////// Branch Fixed Expense Ends /////////////////////////////////
        
        
        ////////////////////// Branch General Expense Starts /////////////////////////////////
        
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE IT.IT_Id IN (".$expItemsResult['PRS_General_Items'].") $filter $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->GenMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        ////////////////////// Branch General Expense Ends /////////////////////////////////
        
        
        ////////////////////// Statutory Expense Starts /////////////////////////////////
        $filter     = " LC.OF_Id = $OF_Id AND BS.BS_Status = 1 ".$filt;
        
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
                    WHERE $filter AND IT.IT_Id IN (".$expItemsResult['PRS_Stat_Items'].")  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->StatMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        ////////////////////// Statutory Expense Ends /////////////////////////////////
        
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status FROM locations AS LC WHERE LC.OF_Id = $OF_Id AND LC.LC_Status != 5 $filt ORDER BY LC.LC_Name");
        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id] = $row;
            }
        }
    }
}