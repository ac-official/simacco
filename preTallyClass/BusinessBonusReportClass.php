<?php
require_once("connection.php");

class BusinessBonusReportClass {
    function reportBusinessData($lcfilt,$filt,$stDate='',$enDate='',$oldStockFrom,$OF_Id) {		
        $dateFilt = '';
        
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }

        $count                      = 0;
        $this->StockArray           = array();  
        
        $sql    = "SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status,SUM(SO.OS_OpenBal) AS OS FROM locations AS LC LEFT JOIN stock_open_bals AS SO ON LC.LC_Id = SO.LC_Id WHERE $lcfilt AND LC.LC_Status != 5 GROUP BY LC.LC_Id ORDER BY LC.LC_Name";
        $stockResult    = mysqli_query($GLOBALS['con'],$sql);
        while($stockRow = mysqli_fetch_object($stockResult)) {
            $this->StockArray[$stockRow->LC_Id]['CurrentPeriodOldStock'] = $stockRow->OS;
            $this->BranchArray[$stockRow->LC_Id] = $stockRow;
        } 
            
        if($oldStockFrom) {
            /*$result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type, LC.LC_Id 
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
                                GROUP BY IT.IT_Business, IT.MH_Type");*/
            $result = mysqli_query($GLOBALS['con'],"SELECT SUM(BS.BS_Amount) AS BS_Amount, IT.IT_Business,IT.MH_Type, LC.LC_Id 
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
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
            WHERE  ".$filt." ".$dateFilt." ORDER BY LC.LC_Name";
           
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
                $this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = round(round($this->BussRecvArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]) - round($this->BussReturnedArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]));
            }
        }
    }
    
    function reportStatutoryData($lcfilt,$filt,$stDate='',$enDate='') {
        
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->StatutoryReportArray = array();
        $this->StatMonthReportArray = array();
        
        $sql = "SELECT LC_Id,LC_Name , SUM(StatutoryNAmt) AS APS_StatutoryNAmt 
                FROM (
                    SELECT LC.LC_Id,LC.LC_Name,APS.APS_StatutoryNAmt AS StatutoryNAmt FROM `tracks` AS TR 
                        LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                        LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                        LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                        LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                        JOIN attestation_process_sub AS APS ON (APS.APS_Id = TJ.APS_Id)
                        JOIN locations AS LC ON TJ.LC_Id = LC.LC_Id  
                    WHERE ".$filt." ".$dateFilt." AND TR.TR_Status != 0 
                UNION ALL
                    SELECT LC.LC_Id,LC.LC_Name, APSED.APSE_StatutoryNAmt AS StatutoryNAmt FROM `tracks` AS TR 
                        LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                        LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                        LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                        LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                        JOIN attestation_process_sub AS APS ON AJS.APS_Id = APS.APS_Id
                        LEFT JOIN attestation_process_sub_expense_details AS APSED ON ( APSED.APS_Id = APS.APS_Id AND  (AJ.AJ_ReceivedDate) BETWEEN (APSE_FDate)  AND (APSE_LDate) )
                        JOIN locations AS LC ON AJ.LC_Id = LC.LC_Id
                    WHERE ".$filt." ".$dateFilt." AND TR.TR_Status != 0 
                ) AS T3 
                GROUP BY LC_Id
                ORDER BY LC_Name";
                
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->StatutoryReportArray[$row->LC_Id] = $row;
            }
        }
        
        /* Branch based monthwise data */
        
        $monthSQL = "SELECT LC_Id,YEAR(AJ_ReceivedDate) AS YEAR, MONTH(AJ_ReceivedDate) AS MONTH , SUM(StatutoryNAmt) AS APS_StatutoryNAmt 
                    FROM (
                        SELECT LC.LC_Id,AJ.AJ_ReceivedDate, APS.APS_StatutoryNAmt AS StatutoryNAmt FROM `tracks` AS TR 
                            LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                            LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                            LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                            LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                            JOIN attestation_process_sub AS APS ON (APS.APS_Id = TJ.APS_Id)
                            JOIN locations AS LC ON TJ.LC_Id = LC.LC_Id  
                        WHERE ".$filt." ".$dateFilt." AND TR.TR_Status != 0 
                    UNION ALL
                        SELECT LC.LC_Id,AJ.AJ_ReceivedDate, APSED.APSE_StatutoryNAmt AS StatutoryNAmt FROM `tracks` AS TR 
                            LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                            LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                            LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                            LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                            JOIN attestation_process_sub AS APS ON AJS.APS_Id = APS.APS_Id
                            LEFT JOIN attestation_process_sub_expense_details AS APSED ON ( APSED.APS_Id = APS.APS_Id AND  (AJ.AJ_ReceivedDate) BETWEEN (APSE_FDate)  AND (APSE_LDate) )
                            JOIN locations AS LC ON AJ.LC_Id = LC.LC_Id
                        WHERE ".$filt." ".$dateFilt." AND TR.TR_Status != 0 
                    ) AS T3 
                    GROUP BY LC_Id, MONTH( AJ_ReceivedDate ),YEAR(AJ_ReceivedDate)  
                    ORDER BY LC_Id, MONTH( AJ_ReceivedDate ),YEAR(AJ_ReceivedDate)";
        
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                if($monthRow->LC_Id != 'NULL') {
                    $LC_Id  = $monthRow->LC_Id;
                    $amt    = $monthRow->APS_StatutoryNAmt;
                    $this->StatMonthReportArray[$LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $amt;
                } 
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
        $checkQuery     = mysqli_query($GLOBALS['con'],"SELECT BRS_Id FROM bonus_report_settings WHERE OF_Id = ".$preTally_user_ofid);
        if(mysqli_num_rows($checkQuery)) {
            $row        = mysqli_fetch_array($checkQuery,MYSQLI_ASSOC);
            $BRS_Id     = $row['BRS_Id'];
            $sql        = "UPDATE bonus_report_settings SET BRS_Fixed_Items = '".$this->BRS_Data['BRS_Fixed_Items']."',BRS_Variable_Items = '".$this->BRS_Data['BRS_Variable_Items']."',BRS_General_Items = '".$this->BRS_Data['BRS_General_Items']."', BRS_LastUpdated = '".$this->BRS_Data['BRS_LastUpdated']."',BRS_MDate = '".$this->BRS_Data['BRS_MDate']."' WHERE BRS_Id = $BRS_Id";
            mysqli_query($GLOBALS['con'],$sql);
        } else {
            $sql        = "INSERT INTO bonus_report_settings ( " . implode(', ',array_keys($this->BRS_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->BRS_Data)) . "'" . ")";
            mysqli_query($GLOBALS['con'],$sql);
        }
        return 'Successfully updated expense settings';
    }
    
    function getfixedExpenseItems($preTally_user_ofid) {
        $checkQuery     = mysqli_query($GLOBALS['con'],"SELECT BRS_Fixed_Items,BRS_Variable_Items,BRS_General_Items FROM bonus_report_settings WHERE OF_Id = ".$preTally_user_ofid);
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
        
        $fixedItemsSql      = mysqli_query($GLOBALS['con'],"SELECT BRS_Fixed_Items FROM bonus_report_settings WHERE OF_Id = $preTally_user_ofid");
        $fixedExpenseResult = mysqli_fetch_array($fixedItemsSql,MYSQLI_ASSOC);
        $fixedExpenseItems  = $fixedExpenseResult['BRS_Fixed_Items'];

        /* Branch based monthwise data */
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE $filt AND IT.IT_Id IN ($fixedExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date) ";

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
        //  Get all branches Start 
        //print("SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status,COUNT(UA.US_Id) AS StaffCount FROM locations AS LC LEFT JOIN users_auth AS UA ON LC.LC_Id = UA.LC_Id AND ((UA.US_ResignFlag  = 0) OR (UA.US_ResignFlag = 1 AND UA.US_ResignDate > '$enDate')) WHERE LC.OF_Id = $OF_Id $filter GROUP BY LC.LC_Id ORDER BY LC.LC_Name");
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status,COUNT(UA.US_Id) AS StaffCount FROM locations AS LC LEFT JOIN users_auth AS UA ON LC.LC_Id = UA.LC_Id AND ((UA.US_ResignFlag  = 0) OR (UA.US_ResignFlag = 1 AND UA.US_ResignDate > '$enDate')) WHERE LC.OF_Id = $OF_Id AND UA.US_Status != 5 AND LC.LC_Status != 5 $filter GROUP BY LC.LC_Id ORDER BY LC.LC_Name");

        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id]     = $row;
                $this->StatutoryOverviewArray       = array();
                $this->FixedMonthReportArray        = array();
                $this->GeneralExpOverviewArray      = array();
                $this->VariableExpOverviewArray     = array();
            }
        }
        //  Get all branches End 
        
        ///////////////////////////////////  Business Start ////////////////////
        
         $sql = "SELECT BS.BS_Amount,IT.IT_Id, LC.LC_Name, 
            YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS MONTH,MH.MH_Type, LC.LC_Id 
            FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
            LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
            WHERE IT.IT_Business =1 $filter AND LC.OF_Id = $OF_Id AND BS.BS_Status = 1 $dateFilt ORDER BY LC.LC_Name";
           
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        $this->BussRecvArray        = array();
        $this->BussReturnedArray    = array();
        $this->BussArray            = array();
        $this->TaxArray             = array();
        if($result){
            while($row = mysqli_fetch_object($result)) {
                if($row->MH_Type == "1") {
                    $this->BussRecvArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH] += $row->BS_Amount;
                } elseif($row->MH_Type == "2") {
                    $this->BussReturnedArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH] += $row->BS_Amount;
                }
                $this->BussArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH] = round(round($this->BussRecvArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH])-round($this->BussReturnedArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH]));
                $this->TaxArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH] = ($this->BussArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH] / 115 ) * 15; 
            }
        }
        
        ///////////////////////////////  Business End //////////////////////////////
        
        //////////////////////////////  Fixed items Start /////////////////////////
        $fixedExpenseResult = $this->getfixedExpenseItems($OF_Id);
        $fixedExpenseItems  = $fixedExpenseResult['BRS_Fixed_Items'];
        
        $fixedExpQuery = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE BS.BS_IEByLC != 'NULL' $filter AND LC.OF_Id = $OF_Id AND BS.BS_Status = 1 
                    AND IT.IT_Id IN ($fixedExpenseItems) $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$fixedExpQuery);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FixedMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR.'-'.$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        ////////////////////////////////  Fixed items End ///////////////////////////
        
        //////////////////////////////// Variable expense amount Start ////////////////
       
        $varExpenseItems  = $fixedExpenseResult['BRS_Variable_Items'];
        $varExpQuery = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE  LC.OF_Id = $OF_Id $filter AND BS.BS_Status = 1  AND IT.IT_Id IN ($varExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $varExpResult = mysqli_query($GLOBALS['con'],$varExpQuery);
        if($varExpResult){
            while($monthRow = mysqli_fetch_object($varExpResult)) {
                $this->VariableExpOverviewArray[$monthRow->LC_Id][$monthRow->YEAR.'-'.$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        //////////////////////////////// Variable expense amount End ////////////////
        
        //////////////////////////////// General expense amount Start ////////////////
        
        $genExpenseItems  = $fixedExpenseResult['BRS_General_Items'];

        $genExpQuery = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE LC.OF_Id = $OF_Id $filter AND BS.BS_Status = 1 AND IT.IT_Id IN ($genExpenseItems)  $dateFilt 
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $genExpResult = mysqli_query($GLOBALS['con'],$genExpQuery);
        if($genExpResult){
            while($monthRow = mysqli_fetch_object($genExpResult)) {
                $this->GeneralExpOverviewArray[$monthRow->LC_Id][$monthRow->YEAR.'-'.$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
        
        //////////////////////////////// General expense amount End ////////////////
        
        //////////////////////////////// Statutory amount Start /////////////////////////
        if($startDate  !='' && $endDate !=''){
            $stDate     = date("Y-m-d", strtotime($startDate));
            $enDate     = date("Y-m-d", strtotime($endDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        
        $monthSQL = "SELECT LC_Id,YEAR(AJ_ReceivedDate) AS YEAR, MONTH(AJ_ReceivedDate) AS MONTH , SUM( APS_StatutoryNAmt) AS APS_StatutoryNAmt, SUM( APS_ExtraNAmt) AS APS_ExtraNAmt 
                    FROM (
                        SELECT LC.LC_Id,AJ.AJ_ReceivedDate, APS.APS_StatutoryNAmt, APS.APS_ExtraNAmt FROM `tracks` AS TR 
                            LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                            LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                            LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                            LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                            JOIN attestation_process_sub AS APS ON (APS.APS_Id = TJ.APS_Id)
                            JOIN locations AS LC ON TJ.LC_Id = LC.LC_Id  
                        WHERE LC.OF_Id = $OF_Id  $dateFilt AND TR.TR_Status != 0 
                    UNION ALL
                        SELECT LC.LC_Id,AJ.AJ_ReceivedDate, APSED.APSE_StatutoryNAmt AS APS_StatutoryNAmt, APSED.APSE_ExtraNAmt AS APS_ExtraNAmt FROM `tracks` AS TR 
                            LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                            LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                            LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                            LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                            JOIN attestation_process_sub AS APS ON AJS.APS_Id = APS.APS_Id
                            LEFT JOIN attestation_process_sub_expense_details AS APSED ON ( APSED.APS_Id = APS.APS_Id AND  (AJ.AJ_ReceivedDate) BETWEEN (APSE_FDate)  AND (APSE_LDate) )
                            JOIN locations AS LC ON AJ.LC_Id = LC.LC_Id
                        WHERE LC.OF_Id = $OF_Id  $dateFilt AND TR.TR_Status != 0 
                    ) AS T3 
                    GROUP BY LC_Id, MONTH( AJ_ReceivedDate ),YEAR(AJ_ReceivedDate)  
                    ORDER BY LC_Id, MONTH( AJ_ReceivedDate ),YEAR(AJ_ReceivedDate)";
        
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                if($monthRow->LC_Id != 'NULL') {
                    $LC_Id  = $monthRow->LC_Id;
                    $amt    = $monthRow->APS_StatutoryNAmt + $monthRow->APS_ExtraNAmt;
                    $this->StatutoryOverviewArray[$LC_Id][$monthRow->YEAR.'-'.$monthRow->MONTH] = $amt;
                    $this->StatutoryArray[$LC_Id][$monthRow->YEAR.'-'.$monthRow->MONTH] = $monthRow->APS_StatutoryNAmt;
                } 
            }
        }
        //////////////////////////////// Statutory amount End ///////////////////////////
    }
    
    function reportVariableExpenseData($lcfilt,$filt,$stDate='',$enDate='',$preTally_user_ofid) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
            
        $this->VarMonthReportArray  = array();
        
        $variableItemsSql      = mysqli_query($GLOBALS['con'],"SELECT BRS_Variable_Items FROM bonus_report_settings WHERE OF_Id = $preTally_user_ofid");
        $variableExpenseResult = mysqli_fetch_array($variableItemsSql,MYSQLI_ASSOC);
        $variableExpenseItems  = $variableExpenseResult['BRS_Variable_Items'];

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
        $filter  = $filt.' AND IT.IT_Business =1 AND LC.OF_Id = '.$OF_Id.' AND BS.BS_Status = 1 ';

        $sql = "SELECT BS.BS_Amount , 
            YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS MONTH,MH.MH_Type, LC.LC_Id 
            FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
            LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
            WHERE $filter $dateFilt ORDER BY LC.LC_Name";

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
                    WHERE $filter $dateFilt  
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]       = ($this->BussRecvArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] - $this->BussReturnedArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]);
                $this->TaxArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]            = ($this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] / 115 ) *15;
            }
        }

        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        
        $monthSQL = "SELECT LC_Id,YEAR(AJ_ReceivedDate) AS YEAR, MONTH(AJ_ReceivedDate) AS 
                MONTH , SUM( APS_StatutoryNAmt) AS APS_StatutoryNAmt 
            FROM (
                SELECT LC.LC_Id,AJ.AJ_ReceivedDate, APS.APS_StatutoryNAmt FROM `tracks` AS TR 
                LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                JOIN attestation_process_sub AS APS ON (APS.APS_Id = TJ.APS_Id)
                JOIN locations AS LC ON TJ.LC_Id = LC.LC_Id  
                WHERE ".$filt." ".$dateFilt." AND TR.TR_Status != 0 
            UNION ALL
                SELECT LC.LC_Id,AJ.AJ_ReceivedDate, APS.APS_StatutoryNAmt FROM `tracks` AS TR 
                LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                JOIN attestation_process_sub AS APS ON AJS.APS_Id = APS.APS_Id
                JOIN locations AS LC ON AJ.LC_Id = LC.LC_Id
                WHERE ".$filt." ".$dateFilt." AND TR.TR_Status != 0 
            ) AS T3 
                GROUP BY LC_Id, MONTH( AJ_ReceivedDate ),YEAR(AJ_ReceivedDate)  
                ORDER BY LC_Id, MONTH( AJ_ReceivedDate ),YEAR(AJ_ReceivedDate)";
        
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->StatMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->APS_StatutoryNAmt;
            }
        }
       
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status FROM locations AS LC WHERE $filt AND LC.LC_Status != 5 ORDER BY LC.LC_Name");
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
        
        $genItemsSql      = mysqli_query($GLOBALS['con'],"SELECT BRS_General_Items FROM bonus_report_settings WHERE OF_Id = $preTally_user_ofid");
        $genExpenseResult = mysqli_fetch_array($genItemsSql,MYSQLI_ASSOC);
        $genExpenseItems  = $genExpenseResult['BRS_General_Items'];
        
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
                WHERE IT.IT_Business =1 AND LC.OF_Id = $OF_Id $filt AND BS.BS_Status = 1 $dateFilt ORDER BY LC.LC_Name";
           
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
                    WHERE IT.IT_Business = 1 AND LC.OF_Id = $OF_Id $filt AND BS.BS_Status = 1 $dateFilt  
                    GROUP BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY LC.LC_Id, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->BSMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]  = $monthRow->BS_Amount;
                $this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]       = ($this->BussRecvArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] - $this->BussReturnedArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]);
                $this->TaxArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH]            = ($this->BusinessArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] / 115 ) * 15;
            }
        }
        
        ////////////////////// Business Ends /////////////////////////////////
        
        $filter         = $filt." AND BS.BS_IEByLC != 'NULL'  AND LC.OF_Id = $OF_Id AND BS.BS_Status = 1 ";
        $expItemsSql    = mysqli_query($GLOBALS['con'],"SELECT * FROM bonus_report_settings WHERE OF_Id = $OF_Id");
        $expItemsResult = mysqli_fetch_array($expItemsSql,MYSQLI_ASSOC);
        
        ////////////////////// Branch Flexi Expense Starts /////////////////////////////////
        
        $monthSQL = " SELECT LC.LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH(BS.BS_Date) AS 
                    MONTH , SUM( BS.BS_Amount ) AS BS_Amount  
                    FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                    LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE IT.IT_Id IN (".$expItemsResult['BRS_Variable_Items'].") $filter $dateFilt 
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
                    WHERE IT.IT_Id IN (".$expItemsResult['BRS_Fixed_Items'].") $filter $dateFilt 
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
                    WHERE IT.IT_Id IN (".$expItemsResult['BRS_General_Items'].") $filter $dateFilt 
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

        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        
        $monthSQL = "SELECT LC_Id,YEAR(AJ_ReceivedDate) AS YEAR, MONTH(AJ_ReceivedDate) AS 
                MONTH , SUM( APS_StatutoryNAmt) AS APS_StatutoryNAmt 
            FROM (
                SELECT LC.LC_Id,AJ.AJ_ReceivedDate, APS.APS_StatutoryNAmt FROM `tracks` AS TR 
                LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                JOIN attestation_process_sub AS APS ON (APS.APS_Id = TJ.APS_Id)
                JOIN locations AS LC ON TJ.LC_Id = LC.LC_Id  
                WHERE LC.OF_Id = $OF_Id ".$filt." ".$dateFilt." AND TR.TR_Status != 0 
            UNION ALL
                SELECT LC.LC_Id,AJ.AJ_ReceivedDate, APSED.APSE_StatutoryNAmt AS APS_StatutoryNAmt FROM `tracks` AS TR 
                LEFT JOIN attestation_job_details AS AJ ON (TR.TR_Id = AJ.AJ_Track_Id) AND (AJ.AJ_Status != 0 AND AJ.AJ_Status != 2)
                LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id 
                LEFT JOIN attestation_job_subprocess AS AJS ON (AJD.AJD_Id  = AJS.AJD_Id AND AJS.AJS_Status != 4) 
                LEFT JOIN tracks_old_jobs AS TJ ON TR.TR_Id = TJ.TR_Id 
                JOIN attestation_process_sub AS APS ON AJS.APS_Id = APS.APS_Id
                LEFT JOIN attestation_process_sub_expense_details AS APSED ON ( APSED.APS_Id = APS.APS_Id AND  (AJ.AJ_ReceivedDate) BETWEEN (APSE_FDate)  AND (APSE_LDate) )
                JOIN locations AS LC ON AJ.LC_Id = LC.LC_Id
                WHERE LC.OF_Id = $OF_Id ".$filt." ".$dateFilt." AND TR.TR_Status != 0 
            ) AS T3 
                GROUP BY LC_Id, MONTH( AJ_ReceivedDate ),YEAR(AJ_ReceivedDate)  
                ORDER BY LC_Id, MONTH( AJ_ReceivedDate ),YEAR(AJ_ReceivedDate)";
        
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->StatMonthReportArray[$monthRow->LC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->APS_StatutoryNAmt;
            }
        }
        
        ////////////////////// Statutory Expense Ends /////////////////////////////////
        
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Status,LC.LC_Id FROM locations AS LC WHERE LC.OF_Id = $OF_Id AND LC.LC_Status != 5 $filt ORDER BY LC.LC_Name");
        if($branchQuery){
            while($row = mysqli_fetch_object($branchQuery)) {
                $this->BranchArray[$row->LC_Id] = $row;
            }
        }
    }
    
    function reportBonusData($filt,$fromDate,$toDate,$OF_Id,$filter) {
       
        $SQL = "SELECT UA.LC_Id,UA.US_Id,LC.LC_Name,UA.US_FName,UA.US_LName  
                FROM users_auth AS UA 
                LEFT JOIN locations AS LC ON UA.LC_Id = LC.LC_Id  
                WHERE $filter  AND UA.US_Status != 5 AND ((UA.US_ResignFlag  = 0) 
                OR (UA.US_ResignFlag = 1 AND UA.US_ResignDate > '$toDate')) ORDER BY UA.US_FName ASC";

        $result = mysqli_query($GLOBALS['con'],$SQL);
        while($row = mysqli_fetch_object($result)) {
            $this->UserArray[$row->US_Id] = $row;
        }
               
        $SQL = "SELECT UA.LC_Id,UA.US_Id,SUM(EP.EP_WorkDays) AS EP_WorkDays,UA.US_FName,UA.US_LName,LC.LC_Name 
                FROM users_auth AS UA 
                LEFT JOIN employee_payroll AS EP ON UA.US_Id = EP.US_Id 
                LEFT JOIN locations AS LC ON UA.LC_Id = LC.LC_Id  
                WHERE $filter $filt  AND UA.US_Status != 5 GROUP BY UA.US_Id ORDER BY UA.US_FName ASC";

        $result = mysqli_query($GLOBALS['con'],$SQL);
        while($row = mysqli_fetch_object($result)) {
            $this->DataArray[$row->US_Id] = $row;
        }
    }
}