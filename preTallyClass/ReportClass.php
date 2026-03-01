<?php
require_once("connection.php");
class ReportClass {
    var $ReportArray;
    //----------------------------------------- BalanceSheet Items ----------------------------------------//
    function reportBSData($filt,$stDate='',$enDate='',$newParm='',$pos,$cnt) {		
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date  <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date  >=  '".$stDate."'";
        }
            
        $count=0;
        $this->ReportArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Amount, DS.DS_Description, TR.TR_Track, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                WHERE BS.US_Id IN ( ".$newParm." )
                                    AND IT.IT_Business = '0'
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.BS_Status = 1 
                                    ".$dateFilt."
                                ORDER BY BS.BS_Date DESC
                                LIMIT ".$pos.",".$cnt
                                );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->ReportArray[$count] = $row;
                $count++;
            }
        }
    }
    //----------------------------------------- BalanceSheet Items ----------------------------------------//
    function reportBSDataFilter($filt,$stDate='',$enDate='',$newParm='',$filter_mask,$pos,$cnt) {		
        $dateFilt = '';        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date  <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date  >=  '".$stDate."'";
        }
                               
        $count=0;
        $this->ReportArray = array();       
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id,BS.BS_Date, BS.BS_Amount,BS.BS_PettyCashAmt, DS.DS_Description, TR.TR_Track, IT.IT_Name, IT.SH_Id, SH.SH_Name, IT.MH_Type, LC.LC_Name,US.US_FName, US.US_LName 
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id                                
                                WHERE BS.US_Id IN ( ".$newParm." )
                                    AND IT.IT_Business = '0'
                                    AND IT.MH_Type IN ('1','2')
                                    AND BS.BS_Status = 1 
                                    ".$dateFilt."
                                    ".$filter_mask."
                                ORDER BY BS.BS_Date DESC
                                LIMIT ".$pos.",".$cnt
                                );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->ReportArray[$count] = $row;
                $count++;
            }
        }
    }
    //----------------------------------------- BalanceSheet Items ----------------------------------------//
    function reportBSDataCount($filt,$stDate='',$enDate='',$newParm='',$filter_mask) {		
        $dateFilt = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date  <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date  >=  '".$stDate."'";
        }
      
                                
        $result = mysqli_query($GLOBALS['con'],"SELECT COUNT(BS.BS_Id)
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE BS.US_Id IN ( ".$newParm." )
                                    AND IT.IT_Business = '0'
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.BS_Status = 1 
                                    ".$dateFilt."
                                    ".$filter_mask."
                                ORDER BY BS.BS_Date DESC
                                "
                                );
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        return $count=$row[0];
        
//        if($result){
//            while($row = mysqli_fetch_object($result)) {
//                $this->ReportArray[$count] = $row;
//                $count++;
//            }
//        }
    }
    //----------------------------------------- BalanceSheet IE PieChart ----------------------------------------//
    function iePieChartData($filt,$stDate='',$enDate='',$newParm='') {			
        $dateFilt = '';
        /*$newFilt = '';
        $temp = explode('-', $filt);
        foreach ($temp as $value) {
            $newTemp = explode('_', $value);
            $newFilt.= $newTemp['0']."_Id =  '".$newTemp['1']."' AND ";
        }
        $newFilt.='1';*/
        
        
        
        
        
        
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date  <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date  >=  '".$stDate."'";
        }
        
        $count=0;
        $this->ReportArray = array();
        /*echo "SELECT BS.US_Id, SUM( BS.BS_Amount ) AS IE , MH.MH_Type,IT.IT_Transfers,IT.IT_Business
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                WHERE BS.US_Id IN (".$newParm.")
                                AND MH.MH_Type IN ('1','2')
                                AND BS.BS_Status = 1 
                                ".$dateFilt."
                                GROUP BY MH.MH_Type,IT_Transfers,IT.IT_Business
                                 ";*/
        
                                 
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount ) AS IE , MH.MH_Type,IT.IT_Transfers,IT.IT_Business
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                WHERE BS.US_Id IN (".$newParm.")
                                AND MH.MH_Type IN ('1','2')
                                AND BS.BS_Status = 1 
                                ".$dateFilt."
                                GROUP BY MH.MH_Type,IT_Transfers,IT.IT_Business
                                 ");
        
        while($row=mysqli_fetch_object($result)) {
            if($row->MH_Type == 1 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->ReportArray['CshInc'] += $row->IE;
            }else if($row->MH_Type == 2 && $row->IT_Business == 0 && $row->IT_Transfers == 0 ) {
                    $this->ReportArray['CshExp'] += $row->IE;
            }else if($row->IT_Business == 1 && $row->MH_Type == 1 ) {
                    $this->ReportArray['Buss'] =   $this->ReportArray['Buss'] + $row->IE;
            }else if($row->IT_Business == 1 && $row->MH_Type == 2 ) {
                    $this->ReportArray['Buss'] = $this->ReportArray['Buss'] - $row->IE;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 1 ) {
                    $this->ReportArray['CshTransRecv']  = $this->ReportArray['CshTransRecv'] +$row->IE;
            }else if($row->IT_Transfers == 1 && $row->MH_Type == 2 ) {
                    $this->ReportArray['CshTransPaid']  +=  $row->IE;
            }
            	
           
        }
    }    
    //----------------------------------------- BalanceSheet IE Bar Chart ----------------------------------------//
    function ieBarChartData($filt,$stDate='',$enDate='',$newParm='') {			
        $dateFilt = '';
        /*$newFilt = '';
        $temp = explode('-', $filt);
        foreach ($temp as $value) {
            $newTemp = explode('_', $value);
            $newFilt.= $newTemp['0']."_Id =  '".$newTemp['1']."' AND ";
        }
        $newFilt.='1';*/
        
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date  <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date  >=  '".$stDate."'";
        }
        $count=0;
        $this->ReportArray = array();       
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount )  AS IE,IT.SH_Id,SH.SH_Name, IT.MH_Type
                                FROM  `balance_sheets` AS BS
                                LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                WHERE BS.US_Id IN (".$newParm.")
                                    AND IT.IT_Business = '0'
                                    AND IT.MH_Type IN ('1','2')
                                    AND BS.BS_Status = 1 
                                    ".$dateFilt."
                                GROUP BY IT.SH_Id,IT.MH_Type
                                ORDER BY IT.MH_Type DESC,IE DESC 
                                 ");
        while($row=mysqli_fetch_object($result)) {
            $this->ReportArray[$count] = $row;
            $count++;
        }
    }
    function myIEReport($filt,$userId){
        if($filt=='i'){
            $MH_Type = '1';
        }elseif($filt=='e'){
            $MH_Type = '2';
        }
        $count=0;
        $this->ReportArray = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, SUM( BS.BS_Amount ) AS IE, MONTH( BS.BS_Date ) AS MN,  MONTHNAME( BS.BS_Date ) AS MName, MH.MH_Type
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                WHERE BS.US_Id = '".$userId."' 
                                    AND MH.MH_Type = '".$MH_Type."' 
                                    AND IT.IT_Business = '0' 
                                    AND BS.BS_Status = 1 
                                    AND BS.BS_Date BETWEEN (LAST_DAY(NOW()- INTERVAL 6 MONTH)+ INTERVAL 1 DAY)  AND NOW()
                                GROUP BY MN
                                ");
        while($row=mysqli_fetch_object($result)) {
            $this->ReportArray[$count] = $row;
            $count++;
        }
        
    }    
    function reportEdt_BSData($stDate,$enDate,$ofid,$newFilt,$filter_key,$pos,$cnt) {	
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
        $sql="SELECT BS.US_Id, BS.BS_Id, BS.BS_Date, BS.BS_PettyCashRefId,
                    BS.BS_Amount, DS.DS_Description,BS.CHQ_Number, TR.TR_Id ,TR.TR_Track, 
                    IT.IT_Id, IT.IT_Name,IT.IT_Business, IT.SH_Id, IT.IT_PettyCash,
                    SH.SH_Name,SH.SH_Track, MH.MH_Type, LC.LC_Name,US.US_FName,US.US_LName
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `offices` AS OF ON LC.OF_Id = OF.OF_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                WHERE
                                BS.US_Id IN ( ".$newFilt." )
                                ".$filter_key." 
                                ".$dateFilt."    
                                AND MH.MH_Type IN ('1','2') 
                                AND ( BS.BS_Status = 1 OR BS.BS_Status = 2 )
                                ORDER BY BS.BS_Date   DESC LIMIT ".$pos.' , '.$cnt ;
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->ReportArray[$count] = $row;
                $count++;
            }
            
        }
    }
    function CountEdt_BSData($stDate,$enDate,$ofid,$newFilt,$filter_key) {
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
        $result = mysqli_query($GLOBALS['con'],"SELECT COUNT(BS.BS_Id)
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                LEFT JOIN `offices` AS OF ON LC.OF_Id = OF.OF_Id
                                LEFT JOIN `users_auth` AS US ON US.US_Id = BS.US_Id
                                WHERE
                                BS.US_Id IN ( ".$newFilt." )
                                ".$filter_key."  
                                ".$dateFilt."    
                                AND MH.MH_Type IN ('1','2') 
                                AND ( BS.BS_Status = 1 OR BS.BS_Status = 2 )");
        if($result){
             $row = mysqli_fetch_array($result,MYSQLI_NUM);
             return $count=$row[0];
            
        }
    }
    
    //--------------------------------- MIS Report----------------------------------------//
    function viewMISReportData($argArray){
    
        $filter = '';
        
        if($argArray['stDate'] !='' && $argArray['enDate'] !=''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date between '".$argArray['stDate']."' AND '".$argArray['enDate']."'";
        }elseif($argArray['stDate']=='' && $argArray['enDate']!=''){
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['enDate']."'";
        }elseif($argArray['stDate']!='' && $argArray['enDate']==''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['stDate']."'";
        }
        
        if($argArray['OFId']) $filter .= " AND IT.OF_Id = '".$argArray['OFId']."' ";
               
        $count=0;
        $this->ReportArray = array();
        
        $sql  = "SELECT SUM( BS.BS_Amount ) AS BS_IEAmt ,MH.MH_Type, MH.MH_Id, IT.IT_Transfers, IT.IT_Business
                        FROM  `balance_sheets` AS BS
                            LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                            LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                            LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                WHERE BS.BS_Status = 1
                                    ".$filter."
                                        GROUP BY MH.MH_Id, IT.IT_Transfers, IT.IT_Business
                    ";
        $result = mysqli_query($GLOBALS['con'],$sql);       
        
        $this->ReportArray['BusInc'] = 0 ;
        $this->ReportArray['BusExp'] = 0 ;
        $this->ReportArray['OtrInc'] = 0 ;
        $this->ReportArray['OtrExp'] = 0 ;
        $this->ReportArray['CshTransRecv'] = 0 ;
        $this->ReportArray['CshTransPaid'] = 0 ;
        
        while($row = mysqli_fetch_object($result)) { 
            
            if($row->IT_Transfers == 0 && $row->IT_Business == 0){
                if($row->MH_Id == 1){
                    $this->ReportArray['BusInc'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 2){
                    $this->ReportArray['BusExp'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 3){
                    $this->ReportArray['OtrInc'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 4){
                    $this->ReportArray['OtrExp'] += $row->BS_IEAmt;
                }
            }else if($row->IT_Transfers == 1 && $row->IT_Business == 0){
                if($row->MH_Type == 1){
                    $this->ReportArray['CshTransRecv'] += $row->BS_IEAmt;
                }else if($row->MH_Type == 2){
                    $this->ReportArray['CshTransPaid'] += $row->BS_IEAmt;
                }
            }
    
        }    
    }
    
    
    //--------------------------------- MIS Report----------------------------------------//
    function viewMonthlyReportData($argArray){
    
        $filter = '';
        
        if($argArray['stDate'] !='' && $argArray['enDate'] !=''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date between '".$argArray['stDate']."' AND '".$argArray['enDate']."'";
        }elseif($argArray['stDate']=='' && $argArray['enDate']!=''){
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['enDate']."'";
        }elseif($argArray['stDate']!='' && $argArray['enDate']==''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['stDate']."'";
        }
        
        if($argArray['OFId']) $filter .= " AND IT.OF_Id = '".$argArray['OFId']."' ";
        if(is_numeric($argArray['ITId'])) $filter .= " AND BS.IT_Id = '".$argArray['ITId']."' ";
        if(is_numeric($argArray['LCId'])) $filter .= " AND BS.LC_Id = '".$argArray['LCId']."' ";
        
       
        $count=0;
        $this->ReportArray = array();
        
        $sql  = "SELECT BS.US_Id, SUM( BS.BS_Amount ) AS BS_IEAmt , MH.MH_Id, MH.MH_Type, YEAR(BS.BS_Date) AS Year , DATE_FORMAT(BS.BS_Date,'%m') AS Month, IT.IT_Transfers, IT.IT_Business
                        FROM  `balance_sheets` AS BS
                            LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                            LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                            LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                WHERE BS.BS_Status = 1
                                    ".$filter."
                                        GROUP BY MH.MH_Id, IT.IT_Transfers, MONTH(BS.BS_Date),IT.IT_Business
                    ";
        $result = mysqli_query($GLOBALS['con'],$sql);       
        
//        $this->ReportArray['BusInc'] = 0 ;
//        $this->ReportArray['BusExp'] = 0 ;
//        $this->ReportArray['OtrInc'] = 0 ;
//        $this->ReportArray['OtrExp'] = 0 ;
//        $this->ReportArray['CshTransRecv'] = 0 ;
//        $this->ReportArray['CshTransPaid'] = 0 ;
        
        while($row = mysqli_fetch_object($result)) { 
            
//            $this->ReportArray[$row->Month]['BusInc'] = 0 ;
//            $this->ReportArray[$row->Month]['BusExp'] = 0 ;
//            $this->ReportArray[$row->Month]['OtrInc'] = 0 ;
//            $this->ReportArray[$row->Month]['OtrExp'] = 0 ;
//            $this->ReportArray[$row->Month]['CshTransRecv'] = 0 ;
//            $this->ReportArray[$row->Month]['CshTransPaid'] = 0 ;
            
            
            if($row->IT_Transfers == 0 && $row->IT_Business == 0){
                if($row->MH_Id == 1){
                    $this->ReportArray[$row->Month]['BusInc'] = $row->BS_IEAmt;
                }else if($row->MH_Id == 2){
                    $this->ReportArray[$row->Month]['BusExp'] = $row->BS_IEAmt;
                }else if($row->MH_Id == 3){
                    $this->ReportArray[$row->Month]['OtrInc'] = $row->BS_IEAmt;
                }else if($row->MH_Id == 4){
                    $this->ReportArray[$row->Month]['OtrExp'] = $row->BS_IEAmt;
                }
            }else if($row->IT_Transfers == 1 && $row->IT_Business == 0){
                if($row->MH_Type == 1){
                    $this->ReportArray[$row->Month]['CshTransRecv'] = $row->BS_IEAmt;
                }else if($row->MH_Type == 2){
                    $this->ReportArray[$row->Month]['CshTransPaid'] = $row->BS_IEAmt;
                }
            }
    
            $count++;
        }    
    }
    
    //--------------------------------- MIS Report----------------------------------------//
    function viewDailyReportData($argArray){
    
        $filter = '';
        
        if($argArray['stDate'] !='' && $argArray['enDate'] !=''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date between '".$argArray['stDate']."' AND '".$argArray['enDate']."'";
        }elseif($argArray['stDate']=='' && $argArray['enDate']!=''){
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['enDate']."'";
        }elseif($argArray['stDate']!='' && $argArray['enDate']==''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['stDate']."'";
        }
        
        if($argArray['OFId']) $filter .= " AND IT.OF_Id = '".$argArray['OFId']."' ";
        if(is_numeric($argArray['LCId'])) $filter .= " AND BS.LC_Id = '".$argArray['LCId']."' ";
        
        
        $count=0;
        $this->ReportArray = array();
           
        $sql  = "SELECT BS.US_Id, SUM( BS.BS_Amount ) AS BS_IEAmt , MH.MH_Id, MH.MH_Type, DATE_FORMAT(BS.BS_Date,'%d') AS Date, IT.IT_Transfers, IT.IT_Business
                        FROM  `balance_sheets` AS BS
                            LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                            LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                            LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                WHERE BS.BS_Status = 1
                                    ".$filter."
                                        GROUP BY MH.MH_Id, IT.IT_Transfers, DATE(BS.BS_Date),IT.IT_Business
                    ";
        $result = mysqli_query($GLOBALS['con'],$sql);       
        
//        $this->ReportArray['BusInc'] = 0 ;
//        $this->ReportArray['BusExp'] = 0 ;
//        $this->ReportArray['OtrInc'] = 0 ;
//        $this->ReportArray['OtrExp'] = 0 ;
//        $this->ReportArray['CshTransRecv'] = 0 ;
//        $this->ReportArray['CshTransPaid'] = 0 ;
        
        while($row = mysqli_fetch_object($result)) { 
            
//            $this->ReportArray[$row->Date]['BusInc'] = 0 ;
//            $this->ReportArray[$row->Date]['BusExp'] = 0 ;
//            $this->ReportArray[$row->Date]['OtrInc'] = 0 ;
//            $this->ReportArray[$row->Date]['OtrExp'] = 0 ;
//            $this->ReportArray[$row->Date]['CshTransRecv'] = 0 ;
//            $this->ReportArray[$row->Date]['CshTransPaid'] = 0 ;
            
            if($row->IT_Transfers == 0 && $row->IT_Business == 0){
                if($row->MH_Id == 1){
                    $this->ReportArray[$row->Date]['BusInc'] = $row->BS_IEAmt;
                }else if($row->MH_Id == 2){
                    $this->ReportArray[$row->Date]['BusExp'] = $row->BS_IEAmt;
                }else if($row->MH_Id == 3){
                    $this->ReportArray[$row->Date]['OtrInc'] = $row->BS_IEAmt;
                }else if($row->MH_Id == 4){
                    $this->ReportArray[$row->Date]['OtrExp'] = $row->BS_IEAmt;
                }
            }else if($row->IT_Transfers == 1 && $row->IT_Business == 0){
                if($row->MH_Type == 1){
                    $this->ReportArray[$row->Date]['CshTransRecv'] = $row->BS_IEAmt;
                }else if($row->MH_Type == 2){
                    $this->ReportArray[$row->Date]['CshTransPaid'] = $row->BS_IEAmt;
                }
            }
    
            $count++;
        }    
    }
    //--------------------------------- MIS Report----------------------------------------//
    function viewItemReportData($argArray){
    
        $filter = '';
        
        if($argArray['stDate'] !='' && $argArray['enDate'] !=''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date between '".$argArray['stDate']."' AND '".$argArray['enDate']."'";
        }elseif($argArray['stDate']=='' && $argArray['enDate']!=''){
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['enDate']."'";
        }elseif($argArray['stDate']!='' && $argArray['enDate']==''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['stDate']."'";
        }
        
        if($argArray['OFId']) $filter .= " AND IT.OF_Id = '".$argArray['OFId']."' ";
        if(is_numeric($argArray['LCId'])) $filter .= " AND BS.LC_Id = '".$argArray['LCId']."' ";
        
        $count=0;
        $this->ReportArray = array();
        
        $sql  = "SELECT BS.US_Id, SUM( BS.BS_Amount ) AS BS_IEAmt , MH.MH_Id, MH.MH_Type,
                    IT.IT_Name, IT.IT_Id, IT.IT_Transfers, IT.IT_Business
                        FROM  `balance_sheets` AS BS
                            LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                            LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                            LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                WHERE BS.BS_Status = 1
                                    ".$filter."
                                        GROUP BY MH.MH_Id, IT.IT_Transfers, IT.IT_Id,IT.IT_Business
                                            ORDER BY IT.IT_Name
                    ";
        $result = mysqli_query($GLOBALS['con'],$sql);       
        
        while($row = mysqli_fetch_object($result)) { 
            
            $this->ReportArray[$count]['BusInc'] = 0 ;
            $this->ReportArray[$count]['BusExp'] = 0 ;
            $this->ReportArray[$count]['OtrInc'] = 0 ;
            $this->ReportArray[$count]['OtrExp'] = 0 ;
            $this->ReportArray[$count]['CshTransRecv'] = 0 ;
            $this->ReportArray[$count]['CshTransPaid'] = 0 ;
            
            if($row->IT_Business == 0) {
                $this->ReportArray[$count]['ITId']   = $row->IT_Id;
                $this->ReportArray[$count]['ITName'] = $row->IT_Name;

                if($row->IT_Transfers == 0){
                    if($row->MH_Id == 1){
                        $this->ReportArray[$count]['BusInc'] = $row->BS_IEAmt;
                    }else if($row->MH_Id == 2){
                        $this->ReportArray[$count]['BusExp'] = $row->BS_IEAmt;
                    }else if($row->MH_Id == 3){
                        $this->ReportArray[$count]['OtrInc'] = $row->BS_IEAmt;
                    }else if($row->MH_Id == 4){
                        $this->ReportArray[$count]['OtrExp'] = $row->BS_IEAmt;
                    }
                }else if($row->IT_Transfers == 1){
                    if($row->MH_Type == 1){
                        $this->ReportArray[$count]['CshTransRecv'] = $row->BS_IEAmt;
                    }else if($row->MH_Type == 2){
                        $this->ReportArray[$count]['CshTransPaid'] = $row->BS_IEAmt;
                    }
                }
                $count++;
            }
        }    
    }
    
    //--------------------------------- MIS Report----------------------------------------//
    function viewBranchReportData($argArray){
    
        $filter = '';
        
        if($argArray['stDate'] !='' && $argArray['enDate'] !=''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date between '".$argArray['stDate']."' AND '".$argArray['enDate']."'";
        }elseif($argArray['stDate']=='' && $argArray['enDate']!=''){
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['enDate']."'";
        }elseif($argArray['stDate']!='' && $argArray['enDate']==''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['stDate']."'";
        }
        
        if($argArray['OFId']) $filter .= " AND IT.OF_Id = '".$argArray['OFId']."' ";
        if(is_numeric($argArray['ITId'])) $filter .= " AND BS.IT_Id = '".$argArray['ITId']."' ";
        if(is_numeric($argArray['LCId'])) $filter .= " AND BS.LC_Id = '".$argArray['LCId']."' ";
        
        $count=0;
        $this->ReportArray = array();
        
        $sql  = "SELECT BS.US_Id, SUM( BS.BS_Amount ) AS BS_IEAmt , MH.MH_Id, MH.MH_Type ,
                    LC.LC_Name, LC.LC_Id ,
                    IT.IT_Business, IT.IT_Transfers, IT.IT_Business
                        FROM  `balance_sheets` AS BS
                            LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                            LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                            LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                            LEFT JOIN  `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE BS.BS_Status = 1
                                    ".$filter."
                                        GROUP BY MH.MH_Id, IT.IT_Transfers,LC.LC_Id,IT.IT_Business
                                            ORDER BY LC.LC_Name
                    ";
        $result = mysqli_query($GLOBALS['con'],$sql);       
        
        while($row = mysqli_fetch_object($result)) { 
            
//            $this->ReportArray[$count]['BusInc'] = 0 ;
//            $this->ReportArray[$count]['BusExp'] = 0 ;
//            $this->ReportArray[$count]['OtrInc'] = 0 ;
//            $this->ReportArray[$count]['OtrExp'] = 0 ;
//            $this->ReportArray[$count]['CshTransRecv'] = 0 ;
//            $this->ReportArray[$count]['CshTransPaid'] = 0 ;
            
            $this->ReportArray[$row->LC_Id]['LCId']   = $row->LC_Id;
            $this->ReportArray[$row->LC_Id]['LCName'] = $row->LC_Name;
            
            if($row->IT_Transfers == 0 && $row->IT_Business == 0){
                if($row->MH_Id == 1){
                    $this->ReportArray[$row->LC_Id]['BusInc'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 2){
                    $this->ReportArray[$row->LC_Id]['BusExp'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 3){
                    $this->ReportArray[$row->LC_Id]['OtrInc'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 4){
                    $this->ReportArray[$row->LC_Id]['OtrExp'] += $row->BS_IEAmt;
                }
            }else if($row->IT_Transfers == 1 && $row->IT_Business == 0){
                if($row->MH_Type == 1){
                    $this->ReportArray[$row->LC_Id]['CshTransRecv'] += $row->BS_IEAmt;
                }else if($row->MH_Type == 2){
                    $this->ReportArray[$row->LC_Id]['CshTransPaid'] += $row->BS_IEAmt;
                }
            }
    
            $count++;
        }    
    }
    
     //--------------------------------- MIS Report----------------------------------------//
    function viewUserReportData($argArray){
    
        $filter = '';
        
        if($argArray['stDate'] !='' && $argArray['enDate'] !=''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date between '".$argArray['stDate']."' AND '".$argArray['enDate']."'";
        }elseif($argArray['stDate']=='' && $argArray['enDate']!=''){
            $argArray['enDate']= date("Y-m-d", strtotime($argArray['enDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['enDate']."'";
        }elseif($argArray['stDate']!='' && $argArray['enDate']==''){
            $argArray['stDate']= date("Y-m-d", strtotime($argArray['stDate']));
            $filter = " AND BS.BS_Date   =  '".$argArray['stDate']."'";
        }
        
        if($argArray['OFId']) $filter .= " AND IT.OF_Id = '".$argArray['OFId']."' ";
        if(is_numeric($argArray['ITId'])) $filter .= " AND BS.IT_Id = '".$argArray['ITId']."' ";
        if(is_numeric($argArray['LCId'])) $filter .= " AND BS.LC_Id = '".$argArray['LCId']."' ";
        
        $count=0;
        $this->ReportArray = array();
        
        $sql  = "SELECT BS.US_Id, SUM( BS.BS_Amount ) AS BS_IEAmt ,
                    MH.MH_Id, MH.MH_Type ,
                    IT.IT_Transfers, IT.IT_Business,
                    US.US_Id, concat(US.US_FName ,' ',US.US_LName) AS USName
                        FROM  `balance_sheets` AS BS
                            LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                            LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                            LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                            LEFT JOIN  `users_auth` AS US ON BS.US_Id = US.US_Id
                                WHERE BS.BS_Status = 1
                                    ".$filter."
                                        GROUP BY MH.MH_Id, IT.IT_Transfers, US.US_Id,IT.IT_Business
                                            ORDER BY US.US_FName
                    ";
        $result = mysqli_query($GLOBALS['con'],$sql);       
        
        while($row = mysqli_fetch_object($result)) { 
            
//            $this->ReportArray[$row->US_Id]['BusInc'] = 0 ;
//            $this->ReportArray[$row->US_Id]['BusExp'] = 0 ;
//            $this->ReportArray[$row->US_Id]['OtrInc'] = 0 ;
//            $this->ReportArray[$row->US_Id]['OtrExp'] = 0 ;
//            $this->ReportArray[$row->US_Id]['CshTransRecv'] = 0 ;
//            $this->ReportArray[$row->US_Id]['CshTransPaid'] = 0 ;
            
            $this->ReportArray[$row->US_Id]['USId']   = $row->US_Id;
            $this->ReportArray[$row->US_Id]['USName'] = $row->USName;
            
            if($row->IT_Transfers == 0 && $row->IT_Business == 0){
                if($row->MH_Id == 1){
                    $this->ReportArray[$row->US_Id]['BusInc'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 2){
                    $this->ReportArray[$row->US_Id]['BusExp'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 3){
                    $this->ReportArray[$row->US_Id]['OtrInc'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 4){
                    $this->ReportArray[$row->US_Id]['OtrExp'] += $row->BS_IEAmt;
                }
            }else if($row->IT_Transfers == 1 && $row->IT_Business == 0){
                if($row->MH_Type == 1){
                    $this->ReportArray[$row->US_Id]['CshTransRecv'] += $row->BS_IEAmt;
                }else if($row->MH_Type == 2){
                    $this->ReportArray[$row->US_Id]['CshTransPaid'] += $row->BS_IEAmt;
                }
            }
    
            $count++;
        }    
    }
   function viewMISGridData($misArgArray){
       $filter = '';        
        if($misArgArray['stDate'] !='' && $misArgArray['enDate'] !=''){
            $misArgArray['stDate']= date("Y-m-d", strtotime($misArgArray['stDate']));
            $misArgArray['enDate']= date("Y-m-d", strtotime($misArgArray['enDate']));
            $filter = " AND BS.BS_Date between '".$misArgArray['stDate']."' AND '".$misArgArray['enDate']."'";
        }elseif($misArgArray['stDate']=='' && $misArgArray['enDate']!=''){
            $misArgArray['enDate']= date("Y-m-d", strtotime($misArgArray['enDate']));
            $filter = " AND BS.BS_Date   < '".$misArgArray['enDate']."'";
        }elseif($misArgArray['stDate']!='' && $misArgArray['enDate']==''){
            $misArgArray['stDate']= date("Y-m-d", strtotime($misArgArray['stDate']));
            $filter = " AND BS.BS_Date   > '".$misArgArray['stDate']."'";
        }
        
        if($misArgArray['OFId']) $filter .= " AND LC.OF_Id='".$misArgArray['OFId']."' AND IT.OF_Id = '".$misArgArray['OFId']."' ";
        if(is_numeric($misArgArray['ITId'])) $filter .= " AND BS.IT_Id = '".$misArgArray['ITId']."' ";
        if(is_numeric($misArgArray['LCId'])) $filter .= " AND BS.LC_Id = '".$misArgArray['LCId']."' ";
        $filterVal="";         
        if(($misArgArray['FilterVal']!=0 )&& ($misArgArray['FilterVal']!="")){
        if($misArgArray['FilterBy']==2)$filterVal=" AND BS.LC_Id  IN (".$misArgArray['FilterVal'].")";        
        if($misArgArray['FilterBy']==3)$filterVal=" AND BS.US_Id IN (".$misArgArray['FilterVal'].")";
        if($misArgArray['FilterBy']==4)$filterVal=" AND IT.SH_Id IN (".$misArgArray['FilterVal'].")";
        if($misArgArray['FilterBy']==5)$filterVal=" AND BS.IT_Id  IN (".$misArgArray['FilterVal'].")";
        }
        if($misArgArray['Request']=="item"){
        $cols="IT.IT_Id AS Id,IT.IT_Name";   
        $cols_arr="IT_Id";
        $group_by=",BS.IT_Id";
        }else if($misArgArray['Request']=="branch"){
        $cols="LC.LC_Id AS Id,LC.LC_Name";    
        $cols_arr="LC_Id";
        $group_by=",BS.LC_Id";
        }else if($misArgArray['Request']=="subhead"){
        $cols="SH.SH_Id AS Id,SH.SH_Name";    
        $cols_arr="SH_Id";
        $group_by=",IT.SH_Id";
        }else if($misArgArray['Request']=="user"){
        $cols="US.US_Id AS Id,concat(US.US_FName ,' ',US.US_LName)";    
        $cols_arr="US_Id";
        $group_by=",BS.US_Id";
        }
        
        $count=0;
        $this->ReportArray = array();
        
       $sql  = "SELECT ".$cols." AS NAME_X , SUM(BS.BS_Amount) AS BS_IEAmt ,
                    MH.MH_Id, MH.MH_Type ,
                    IT.IT_Transfers, IT.IT_Business,
                    US.US_Id
                        FROM  `balance_sheets` AS BS
                            LEFT JOIN  `items` IT ON BS.IT_Id = IT.IT_Id
                            LEFT JOIN  `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                            LEFT JOIN  `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                            LEFT JOIN  `users_auth` AS US ON BS.US_Id = US.US_Id
                            LEFT JOIN  `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                WHERE BS.BS_Status=1 AND IT.IT_Business=0 ".$filterVal."
                                    ".$filter."
                                        GROUP BY MH.MH_Id, IT.IT_Transfers,IT.IT_Business".$group_by."
                                            ORDER BY NAME_X";
        print_r($sql);
        $result = mysqli_query($GLOBALS['con'],$sql);  
        $count=1;        
        while($row = mysqli_fetch_object($result)) {
            $indx=$row->Id;
            $this->ReportArray[$indx]['Id']   = $row->Id;
            $this->ReportArray[$indx]['Name'] = $row->NAME_X;
            
            if($row->IT_Transfers == 0 && $row->IT_Business == 0){
                if($row->MH_Id == 1){
                    $this->ReportArray[$indx]['BusInc'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 2){
                    $this->ReportArray[$indx]['BusExp'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 3){
                    $this->ReportArray[$indx]['OtrInc'] += $row->BS_IEAmt;
                }else if($row->MH_Id == 4){
                    $this->ReportArray[$indx]['OtrExp'] += $row->BS_IEAmt;
                }
            }else if($row->IT_Transfers == 1 && $row->IT_Business == 0){
                if($row->MH_Type == 1){
                    $this->ReportArray[$indx]['CshTransRecv'] += $row->BS_IEAmt;
                }else if($row->MH_Type == 2){
                    $this->ReportArray[$indx]['CshTransPaid'] += $row->BS_IEAmt;
                }
            }
    
            $count++;
        }
   }
}
?>