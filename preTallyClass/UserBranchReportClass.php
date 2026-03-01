<?php

require_once("connection.php");

class UserBranchReportClass {

    var $MasterReportArray;
    var $BranchStkRptArray;
    var $MetaData;

    //----------------------------------------- Item Based Reports ----------------------------------------//
    function reportItemData($filt, $stDate = '', $enDate = '') {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date BETWEEN '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }

        $count = 0;
        $this->MasterReportArray = array();
        $sql = "SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS BS_Amount ,IT.IT_Id, IT.IT_Name,IT.IT_Transfers,IT.IT_Business,MH.MH_Type,MH.MH_Name,SH.SH_Id,SH.SH_Name, LC.LC_Name,LC.LC_Id
                    FROM  `balance_sheets` AS BS
                        LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                        LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                        LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                        LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                            WHERE  " . $filt . "
                                " . $dateFilt . "
                                    GROUP BY IT.IT_Id ORDER BY IT.IT_Name";
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result) {
            while ($row = mysqli_fetch_object($result)) {
                $this->MasterReportArray[$count] = $row;
                $count++;
            }
        }
    }

    //----------------------------------------- Item Based Branch Reports ----------------------------------------//
    function reportItemBranchData($filt, $stDate = '', $enDate = '') {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date BETWEEN '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }

        $count = 0;
        $this->MasterReportArray = array();

        $sql = "SELECT CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS BS_Amount ,IT.IT_Id, IT.IT_Name, SH.SH_Id, 
            SH.SH_Name, LC.LC_Name, LC.LC_Id ,LC.LC_Status,BS.BS_IEByLC 
            FROM `balance_sheets` AS BS LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
            LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
            WHERE  " . $filt . " " . $dateFilt . " GROUP BY BS.BS_IEByLC ORDER BY LC.LC_Name";

        $result = mysqli_query($GLOBALS['con'], $sql);

        if ($result) {
            while ($row = mysqli_fetch_object($result)) {
                $this->MasterReportArray[$count] = $row;
                $count++;
            }
        }
        $monthSQL = " SELECT BS.BS_IEByLC,IFNULL(LC.LC_Id,0) AS LC_Id,YEAR(BS.BS_Date) AS YEAR, MONTH( BS.BS_Date ) AS 
                    MONTH , CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS BS_Amount
                    FROM  `balance_sheets` AS BS
                    LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                    LEFT JOIN  `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id 
                    WHERE " . $filt . " " . $dateFilt . " 
                    GROUP BY BS.BS_IEByLC, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  
                    ORDER BY BS.BS_IEByLC, MONTH( BS.BS_Date ),YEAR(BS.BS_Date)  ";

        $monthResult = mysqli_query($GLOBALS['con'], $monthSQL);
        if ($monthResult) {
            while ($monthRow = mysqli_fetch_object($monthResult)) {
                $this->MonthReportArray[$monthRow->BS_IEByLC][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->BS_Amount;
            }
        }
    }

    function entryTypeSumItemRpt($filt, $stDate = '', $enDate = '') { // get total amount for entry based types in Item Based Reports(grid footer data)
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date BETWEEN '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
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
                WHERE  " . $filt . " " . $dateFilt;
        $result = mysqli_query($GLOBALS['con'], $sql);
        return mysqli_fetch_array($result, MYSQLI_ASSOC);
    }

    function reportItemBasedData($stDate, $enDate, $filter_key,$order,$pos,$limit) { //item based detail data
        $dateFilt = '';        
        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   between '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }
        $count = 0;
        $this->ReportArray = array();

       $sql = "SELECT BS.US_Id, BS.BS_Id,BS.BS_MinAmount,BS.BS_MaxAmount,BS.BS_Date,BS.BS_IEByLC,BS.BS_IEByUS,  
                    BS.BS_Amount, DS.DS_Description,BS.CHQ_Number, TR.TR_Id ,TR.TR_Track, 
                    IT.IT_Id, IT.IT_Name,IT.IT_Business, IT.SH_Id,LC2.LC_Name AS PaidLC_Name,  
                    SH.SH_Name,SH.SH_Track, MH.MH_Type, LC.LC_Name,US.US_FName,US.US_LName,CONCAT(US2.US_FName,' ',US2.US_LName) AS PaidUS_Name
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `locations` AS LC2 ON BS.BS_IEByLC = LC2.LC_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                LEFT JOIN `users_auth` AS US2 ON US2.US_Id=BS.BS_IEByUS
                                WHERE
                                " . $filter_key . " 
                                " . $dateFilt . "    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Status = 1 
                                ORDER BY ".$order." LIMIT ".$pos.','.$limit;
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result) {
            while ($row = mysqli_fetch_object($result)) {
                $this->MasterReportArray[$count] = $row;
                $count++;
            }
        }
    }

    function reportMRVisualDetails($filt, $stDate = '', $enDate = '', $filter = '') {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   between '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }

        $count = 0;
        $this->CashBSArray = array();
        $sql = "SELECT BS.US_Id, CAST( SUM( BS_Amount ) AS DECIMAL( 15, 2 ) ) AS IE , MH.MH_Type, IT.IT_Transfers,BS.BS_Complete,IT.IT_Business 
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE 1
                                " . $dateFilt . " AND BS.BS_Status = 1 
                                AND LC." . $filter . "
                                GROUP BY MH.MH_Type,IT.IT_Transfers,IT.IT_Business";
        $result = mysqli_query($GLOBALS['con'], $sql);

        while ($row = mysqli_fetch_object($result)) {
            $this->CashBSArray[$count] = $row;
            $count++;
        }
    }

    function reportUserBasedData($stDate, $enDate, $filter_key,$order_by, $pos, $limit) {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   between '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }
        $count = 0;
        $this->ReportArray = array();

        $sql = "SELECT BS.US_Id, BS.BS_Id,BS.BS_MinAmount,BS.BS_MaxAmount, BS.BS_Date  , 
                    SUM(BS.BS_Amount) AS BS_Amount, DS.DS_Description,BS.CHQ_Number, TR.TR_Id ,TR.TR_Track, 
                    IT.IT_Id, IT.IT_Name,IT.IT_Business, IT.SH_Id,LC2.LC_Name AS PaidLC_Name,BS.BS_IEByLC,BS.BS_IEByUS,  
                    SH.SH_Name,SH.SH_Track, MH.MH_Type, LC.LC_Name,US.US_FName,US.US_LName,CONCAT(US2.US_FName,' ',US2.US_LName) AS PaidUS_Name
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `locations` AS LC2 ON BS.BS_IEByLC = LC2.LC_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                LEFT JOIN `users_auth` AS US2 ON US2.US_Id=BS.BS_IEByUS
                                WHERE
                                " . $filter_key . " 
                                " . $dateFilt . "    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Status = 1 
                                GROUP BY IT.IT_Id,BS.BS_IEByLC,BS.BS_IEByUS ORDER BY ".$order_by."  LIMIT " . $pos . ',' . $limit;
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result) {
            while ($row = mysqli_fetch_object($result)) {
                $this->MasterReportArray[$count] = $row;
                $count++;
            }
        }
    }

    function reportUserBasedDataCount($stDate, $enDate, $filter_key) {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   between '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }
        $count = 0;
        $this->ReportArray = array();

        $sql = "SELECT COUNT(BS.BS_Id) AS COUNT,SUM(BS.BS_Amount) AS SUM
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `locations` AS LC2 ON BS.BS_IEByLC = LC2.LC_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                LEFT JOIN `users_auth` AS US2 ON US2.US_Id=BS.BS_IEByUS
                                WHERE
                                " . $filter_key . " 
                                " . $dateFilt . "    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Status = 1 GROUP BY IT.IT_Id,BS.BS_IEByLC,BS.BS_IEByUS";
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result) {
            $this->MetaData['Count'] = mysqli_num_rows($result);
            $count = 0;
            $this->MetaDataSum = array();
            $this->MetaData['Sum'] = 0;
            while ($row = mysqli_fetch_object($result)) {
                //print($row->SUM.'_');
                $this->MetaData['Sum'] += $row->SUM;
                $count++;
            }
        }
    }
function reportItemDetailedDataCount($stDate, $enDate, $filter_key) {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   between '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }
        $count = 0;
        $this->ReportArray = array();

        $sql = "SELECT COUNT(BS.BS_Id) AS COUNT,SUM(BS.BS_Amount) AS SUM
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `locations` AS LC2 ON BS.BS_IEByLC = LC2.LC_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                LEFT JOIN `users_auth` AS US2 ON US2.US_Id=BS.BS_IEByUS
                                WHERE
                                " . $filter_key . " 
                                " . $dateFilt . "    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Status = 1 GROUP BY IT.IT_Id,BS.BS_IEByLC,BS.BS_IEByUS";
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result) {            
            $count = 0;
            $this->MetaDataSum = array();
            $this->MetaData['Sum'] = 0;
            while ($row = mysqli_fetch_object($result)) {
                $this->MetaData['Count'] += $row->COUNT;
                $this->MetaData['Sum'] += $row->SUM;
                $count++;
            }
        }
    }
    function getUserFilterCombo($stDate, $enDate, $filter_key) {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   between '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }
        $sql = "SELECT DISTINCT(US.US_Id),CONCAT(US.US_FName,' ',US_LName) AS NAME
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id                                
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.BS_IEByUS
                                WHERE
                                " . $filter_key . " 
                                " . $dateFilt . "    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Status = 1 GROUP BY IT.IT_Id,BS.BS_IEByLC,BS.BS_IEByUS ORDER BY NAME ASC";
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result) {
            $count = 0;
            while ($row = mysqli_fetch_object($result)) {
                $this->UserFitlerData[$count] = $row;
                $count++;
            }
        }
    }
    function getaddedUserFilterCombo($stDate, $enDate, $filter_key) {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   between '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }
       $sql = "SELECT DISTINCT(US.US_Id),CONCAT(US.US_FName,' ',US_LName) AS NAME
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id                                
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                WHERE
                                " . $filter_key . " 
                                " . $dateFilt . "    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Status = 1 ORDER BY NAME ASC";
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result) {
            $count = 0;
            while ($row = mysqli_fetch_object($result)) {
                $this->UserFitlerData[$count] = $row;
                $count++;
            }
        }
    }

    function getBranchFilterCombo($stDate, $enDate, $filter_key) {
        $dateFilt = '';

        if ($stDate != '' && $enDate != '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   between '" . $stDate . "' AND '" . $enDate . "'";
        } elseif ($stDate == '' && $enDate != '') {
            $enDate = date("Y-m-d", strtotime($enDate));
            $dateFilt = " AND BS.BS_Date   <=  '" . $enDate . "'";
        } elseif ($stDate != '' && $enDate == '') {
            $stDate = date("Y-m-d", strtotime($stDate));
            $dateFilt = " AND BS.BS_Date   >=  '" . $stDate . "'";
        }
        $sql = "SELECT DISTINCT(LC.LC_Id),LC_Name
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `locations` AS LC ON BS.BS_IEByLC = LC.LC_Id                                
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.BS_IEByUS
                                WHERE
                                " . $filter_key . " 
                                " . $dateFilt . "    
                                AND MH.MH_Type IN ('1','2') 
                                AND BS.BS_Status = 1 GROUP BY IT.IT_Id,BS.BS_IEByLC,BS.BS_IEByUS ORDER BY LC_Name ASC";
        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result) {
            $count = 0;
            while ($row = mysqli_fetch_object($result)) {
                $this->BranchFitlerData[$count] = $row;
                $count++;
            }
        }
    }
    function getReportFilters($filt,$stDate='',$enDate='') {		
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
        $this->RptFilterItemArray    = array();
        $this->RptFilterSubheadArray    = array();
        $sql = "SELECT IT.IT_Id, IT.IT_Name
            FROM `balance_sheets` AS BS LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id    
            LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id 
            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
            LEFT JOIN `main_heads` AS MH ON MH.MH_Id = SH.MH_Id 
            WHERE  ".$filt." ".$dateFilt." GROUP BY IT.IT_Id ORDER BY IT.IT_Name";
           
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->RptFilterItemArray[$count] = $row;                
                $count++;
            }
        }
    }

}
