<?php
/* Class for excel export sections */
require_once("connection.php");
require './vendor/autoload.php';
use \PhpOffice\PhpSpreadsheet\Helper\Sample;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;
class ExportExcelClass
{
    var $UserArray;    
    /* Function for creating excel sheet */
    function createExcel($excelData, $headerArray) 
    {       
        // Create new PHPExcel object
        $objPHPExcel = new Spreadsheet();        
        // Fill worksheet from values in array
      
        $objPHPExcel->getActiveSheet()->fromArray($headerArray, null, 'A1');
        if($excelData)
            $objPHPExcel->getActiveSheet()->fromArray($excelData, null,'A2');
        else 
            $objPHPExcel->getActiveSheet()->setCellValue("A2", "No records found");
        
         $maxColumn=\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headerArray));        
       $styleArray = array(
         'alignment' => array(   
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ));
       
         $objPHPExcel->getActiveSheet()->getStyle('A1:' . 
            $objPHPExcel->getActiveSheet()->getHighestColumn() . 
            $objPHPExcel->getActiveSheet()->getHighestRow()
        )->applyFromArray($styleArray);
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Export Report');
        $objPHPExcel->getActiveSheet()->getStyle("A1:".$maxColumn."1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("A1:".$maxColumn."1")->applyFromArray(array("font" => array( "bold" => true)));
        // Set AutoSize
            
        for($i = 0; $i <= count($headerArray); $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
        
       
       // $totalRows = count($excelData)+2;
        
//        $objPHPExcel->getActiveSheet()->getStyle("A1:$maxColumn".$totalRows)->getNumberFormat()
//            ->setFormatCode(PHPExcel_Cell_DataType::TYPE_STRING );
        
        // Adding preceding zeros
        if($headerArray['Accountno'] || $headerArray['ChequeNo']){
            foreach ($excelData as $key => $value) {
                $rId = $key+2;
                if($headerArray['Accountno'])
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G".$rId, $value['Accountno'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                else if($headerArray['ChequeNo']) 
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rId, $value['ChequeNo'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);                
            }
        }        
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="01simple.xlsx"');
header('Cache-Control: max-age=0');

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
ob_clean();
$fileName = date("dFY",time()).'_'.substr(md5(rand()), 2, 7).".xlsx";             
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="file.xls"');        
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
$writer->save($BASEPATH."uploads/excelFile/".$fileName);
return "XL_".$fileName;
    }
    
    /* Export Payroll Report */
    
    function createPayrollExcel($excelData, $headerArray) 
    { 
        include $BASEPATH.'assets/grid/codebase/grid_export/lib/PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Fill worksheet from values in array
      
        $objPHPExcel->getActiveSheet()->fromArray($headerArray, null, 'A1');
        if($excelData)
            $objPHPExcel->getActiveSheet()->fromArray($excelData, null,'A2');
        else 
            $objPHPExcel->getActiveSheet()->setCellValue("A2", "No records found");
        
        $maxColumn =  PHPExcel_Cell::stringFromColumnIndex(count($headerArray) - 1); // get maximum column name with data
        
        //Set Header height
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        
//        $style = array(
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//            )
//        );
//        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 
//            $objPHPExcel->getActiveSheet()->getHighestColumn() . 
//            $objPHPExcel->getActiveSheet()->getHighestRow()
//        )->applyFromArray($style);
    
        $styleArray = array(
         'alignment' => array(   
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        ));
       
        $objPHPExcel->getActiveSheet()->getStyle('A2:' . 
            $objPHPExcel->getActiveSheet()->getHighestColumn() . 
            $objPHPExcel->getActiveSheet()->getHighestRow()
        )->applyFromArray($styleArray);
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Export Report');
        $objPHPExcel->getActiveSheet()->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("A1:".$maxColumn."1")->applyFromArray(array("font" => array( "bold" => true)));
        $objPHPExcel->getActiveSheet()->getStyle("A2:".$maxColumn."2")->applyFromArray(array("font" => array( "bold" => true)));
        
        // Set Width             
        for($i = 0; $i < count($headerArray); $i++) {
//            $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(20);
        }
        
        
        //Merge Columns
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B1:C1');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F1:L1');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N1:V1');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('W1:X1');
        
        //Merge Column Rows
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:A2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D1:D2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E1:E2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('M1:M2');
       // $totalRows = count($excelData)+2;
        
//        $objPHPExcel->getActiveSheet()->getStyle("A1:$maxColumn".$totalRows)->getNumberFormat()
//            ->setFormatCode(PHPExcel_Cell_DataType::TYPE_STRING );
        
        // Adding preceding zeros
        if($headerArray['Accountno'] || $headerArray['ChequeNo']){
            foreach ($excelData as $key => $value) {
                $rId = $key+2;
                if($headerArray['Accountno'])
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G".$rId, $value['Accountno'], PHPExcel_Cell_DataType::TYPE_STRING);
                else if($headerArray['ChequeNo']) 
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rId, $value['ChequeNo'], PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        // Save Excel 2007 file
 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_clean();
        $fileName = date("dFY",time()).'_'.substr(md5(rand()), 2, 7).".xlsx";
        $objWriter->save($BASEPATH.'uploads/excelFile/'.$fileName);  
        return "XL_".$fileName;
    }
    
    /* Export business summary reports */
    function exportreportBusinessData($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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
        $this->BusinessArray = array();
     
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date  , BS.BS_Amount, DS.DS_Description, TR.TR_Track, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name,US.US_FName,US.US_LName
                    FROM `balance_sheets` AS BS
                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                    LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                    WHERE IT.IT_Business = '1'
                    AND BS.BS_Status = 1
                        AND MH.MH_Type IN ('1','2')
                        ".$dateFilt."
                        ".$filter_mask."
                        AND LC.".$filter."
                    ORDER BY BS.BS_Date   DESC"
                    );
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->BusinessArray[$count] = $row;
                $count++;
            }
        }
    }
    
    /* Export transaction summary reports */
    function reportCashBSData($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date,BS.BS_Amount,DS.DS_Description,TR.TR_Track,IT.IT_Name,IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name,US.US_FName, US.US_LName
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
                    AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                    AND BS.PM_Id = 1
                    ".$dateFilt."  
                    ".$filter_mask."
                    AND LC.".$filter."
                    ORDER BY BS.BS_Date DESC"
                    );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->CashBSArray[$count] = $row;
                $count++;
            }
        }
    }
    
    /* Export bank balance sheet */
    function reportBankBSData($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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
        $this->BankBSArray = array();
        
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id,BS.BS_Id, BS.BS_Date,
            BS.BS_Amount, DS.DS_Description,TR.TR_Track,IT.IT_Name,
            IT.SH_Id, SH.SH_Name, MH.MH_Type, LOC.LC_Name,US.US_FName,US.US_LName,LC.LC_Name AS PaidBranch 
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                 LEFT JOIN `bank_accounts` AS BA on BA.BA_Id = BS.BA_Id 
                                LEFT JOIN `locations` AS LC ON BA.LC_Id = LC.LC_Id 
                                LEFT JOIN `locations` AS LOC ON BS.LC_Id = LOC.LC_Id 
                                WHERE IT.IT_Business = '0'
                                    AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                                    AND BS.PM_Id = 2 
                                    AND BS.BS_Status = 2
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND ".$filter."
                                ORDER BY BS.BS_Date DESC");
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->BankBSArray[$count] = $row;
                $count++;
            }
        }
    }
    
    function reportBranchBSData($filt,$stDate='',$enDate='',$filter,$filter_mask) {		
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
        $this->BranchBSArray = array();        
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date,BS.BS_Amount, DS.DS_Description, TR.TR_Track,IT.IT_Id, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Type, LOC.LC_Name,LC.LC_Name AS PaidBranch,US.US_FName, US.US_LName 
                    FROM `balance_sheets` AS BS
                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                    LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                    LEFT JOIN `bank_accounts` AS BA on BA.BA_Id = BS.BA_Id 
                    LEFT JOIN `locations` AS LC ON BA.LC_Id = LC.LC_Id  
                    LEFT JOIN `locations` AS LOC ON BS.LC_Id = LOC.LC_Id 
                    WHERE IT.IT_Business = '0' AND  BS.BA_Id != 0 
                        AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ('1','2'))
                        AND BS.PM_Id = 2 
                        AND BS.BS_Status = 1
                        ".$dateFilt."
                        ".$filter_mask."
                        AND ".$filter."
                    ORDER BY BS.BS_Date DESC"
                    );
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->BranchBSArray[$count] = $row;
                $count++;
            }
        }
    }
    
    /* Export stock summary report */
    function reportStockData($filt,$stDate='',$enDate='',$filter, $filter_mask) 
    {
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
                    ORDER BY TR.TR_Id DESC"
                );
        $trList = '';
        while($row = mysqli_fetch_object($result)) {
            $trList .= $row->TR_Id.',';
        }
        $trList = rtrim($trList, ',');
        $count=0;
        $this->StockArray = array();
       
        $result = mysqli_query($GLOBALS['con'],"SELECT TR.TR_Id, TR.US_Id, SUM(BS.BS_Amount) AS BS_Amount, DS.DS_Description, TR.TR_Track,TR.TR_CDate, IT.IT_Business,IT.MH_Type, IT.IT_Name, IT.SH_Id, SH.SH_Name,LC.LC_Name,US.US_FName, US.US_LName, BS.BS_Date 
                FROM `balance_sheets` AS BS
                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                LEFT JOIN  `users_auth` AS US ON TR.US_Id =US.US_Id
                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
                    AND TR.TR_Id IN (".$trList.")
                    AND IT.MH_Type IN ('1','2')
                    ".$dateFilt." 
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
                    $this->StockArray[$row->TR_Id]['IT_Business']= $row->IT_Business;
                    $this->StockArray[$row->TR_Id]['US_FName']   = $row->US_FName;
                    $this->StockArray[$row->TR_Id]['US_LName']   = $row->US_LName;
                    $this->StockArray[$row->TR_Id]['Business']   = 0;
                    $this->StockArray[$row->TR_Id]['Income']     = 0;
                    $this->StockArray[$row->TR_Id]['Expense']    = 0;
                    $this->StockArray[$row->TR_Id]['BS_Date']    = $row->BS_Date;
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
    
    //-------------------------------- Income Based Reports Export (Stock Reports)--------------------------------//
    function exportstkRpt($stDate='',$enDate='',$OFId) {		
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
      
        $result = mysqli_query($GLOBALS['con'],"SELECT TR.TR_Id, TR.TR_Track, IT.IT_Name , BS.BS_Amount
                    FROM `balance_sheets` AS BS 
                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                    WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
                        AND IT.MH_Type = 1
                        AND IT.IT_Business = 0
                        AND TR.OF_Id = ".$OFId."
                        AND IT.OF_Id = ".$OFId."
                        ".$dateFilt."
                        ORDER BY TR.TR_Id DESC"
                );
        $trList = '';
        $this->trArray = array();
        $this->trNameArray = array();
        $this->itArray = array();
        $this->amntArray = array();
        while ($row = mysqli_fetch_object($result)) {
            $this->trArray[] = $row->TR_Id;
            $this->trNameArray[$row->TR_Id] = $row->TR_Track;
            $this->itArray[] = $row->IT_Name;
            $this->amntArray[$row->TR_Id][$row->IT_Name] += $row->BS_Amount; 
        }
        $this->trArray = array_unique($this->trArray);
        $this->itArray = array_unique($this->itArray);
        $this->trNameArray = array_unique($this->trNameArray);
       
        $trTrack = implode(",", $this->trArray);

        $result = mysqli_query($GLOBALS['con'],"SELECT TR.TR_Id,IT.MH_Type, TR.TR_Track,TR.TR_CDate,SUM(BS.BS_Amount) AS BS_Amount, BS.BS_Date, LC.LC_Name
                    FROM `balance_sheets` AS BS
                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                    LEFT JOIN `users_auth` AS US ON TR.US_Id =US.US_Id
                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                    WHERE BS.BS_Status = 1 AND SH.SH_Track='1' AND BS.TR_Id !='0'
                    AND TR.TR_Id IN (".$trTrack.")
                    AND IT.MH_Type IN ('1','2')
                    AND IT.IT_Business = 1
                    AND TR.OF_Id = ".$OFId."
                    AND IT.OF_Id = ".$OFId."
                        GROUP BY BS.TR_Id, IT.MH_Type
                        ORDER BY TR.TR_Id DESC"
                );
        
        $this->jobAmtArray = array();
        while ($row = mysqli_fetch_object($result)) {
            if($row->MH_Type == 1){
                $this->jobAmtArray[$row->TR_Id]['JobAmount'] += $row->BS_Amount; 
            }elseif($row->MH_Type == 2) {
                $this->jobAmtArray[$row->TR_Id]['JobAmount'] -= $row->BS_Amount; 
            }
            $this->jobAmtArray[$row->TR_Id]['TR_CDate'] = $row->TR_CDate;
            $this->jobAmtArray[$row->TR_Id]['TR_Track'] = $row->TR_Track;
            $this->jobAmtArray[$row->TR_Id]['LC_Name']  = $row->LC_Name;
        }
    }
    //----------------------------------------- BalanceSheet Items ----------------------------------------//
    function reportBSDataFilter($filt,$stDate='',$enDate='',$newParm='',$filter_mask) {		
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
                                ");
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->ReportArray[$count] = $row;
                $count++;
            }
        }
    }
//---------------------------------Export users List-----------------------------------------------------------------------//
    function viewUserGrid($filterUSR,$usid,$likeFilter){			
        $count=0;
        $this->UserArray = array();                
        //if($OfId != 1 ){ $userfilter = "USAUTH.OF_Id =".$OfId."  AND"; } else $userfilter="";
                                   $query=  "SELECT 
                                    USAUTH.US_Id,
                                    CTY.CT_Name,
                                    CNY.CN_Name,
                                    ST.ST_Name,
                                    SPAY.SP_Name,
                                    DP.DP_Name,
                                    BG.BG_Name,
                                    USAUTH.US_EMPID,
                                    USAUTH.US_FName,
                                    USAUTH.US_LName,
                                    CONCAT(USAUTH1.US_FName,' ',USAUTH1.US_LName) AS 'ReportTo',
                                    USAUTH.OF_Id,
                                    USAUTH.LC_Id,
                                    USAUTH.US_Email,
                                    USAUTH.US_Report,
                                    USAUTH.US_Status,
                                    USAUTH.US_DOJ,
                                    USAUTH.US_Status,
                                    USAUTH.US_LoginTime,
                                    USAUTH.US_LogoutTime,
                                    ES.ES_Name,
                                    USPERS.US_Mobile1,
                                    USPERS.US_DOB,
                                    USPERS.US_Address,
                                    USPERS.US_Pemail1,
                                    USPERS.US_Passport,
                                    SS.SS_Name,
                                    SAL.US_GrossSal,
                                    SAL.US_DASal,
                                    SAL.US_HRASal,
                                    SAL.US_CcaSal,
                                    SAL.US_ConveySal,
                                    SAL.US_EduSal,
                                    SAL.US_MedSal,
                                    SAL.US_MiscSal,
                                    SAL.US_BasicSal,                                                                        
                                    UACC.US_PAN,
                                    UACC.US_Bankname,
                                    UACC.US_BankBranch,	
                                    UACC.US_PFNo,
                                    UACC.US_ESI,
                                    UACC.US_AccNo,
                                    DG.DG_Name,
                                    LC.LC_Name,
                                    OFF.OF_Name,
                                    USPERS.US_Relative,
                                    USPERS.US_Guardian,
                                    USPERS.US_Guardianphone,
                                    USPERS.US_Emergencyperson,
                                    USPERS.US_Emergencynumber,
                                    USPERS.ST_Id,
                                    USPERS.CT_Id,
                                    USPERS.US_Gender,
                                    USQUAL.US_Qualification,
                                    USQUAL.US_Specialization,
                                    USQUAL.US_Experience,
                                    USQUAL.US_LastEmployee,
                                    ACL_Name,
                                    USAUTH.US_WrkHours  
                                    FROM
                                    locations AS LC,
                                    designations AS DG,
                                    departments as DP,
                                    offices as OFF,
                                    acl as AC ,
                                    users_auth AS USAUTH
                                    LEFT JOIN  users_auth AS USAUTH1 ON USAUTH1.US_Id =  USAUTH.US_Report
                                    LEFT JOIN employee_status AS ES ON ES.ES_Id = USAUTH.ES_Id,
                                    users_salary SAL LEFT JOIN salary_structures AS SS ON SS.SS_Id =  SAL.SS_Id,
                                    users_personal USPERS
                                    LEFT JOIN cities AS CTY ON CTY.CT_Id = USPERS.CT_Id 
                                    LEFT JOIN countries AS CNY ON CNY.CN_Id = USPERS.CN_Id 
                                    LEFT JOIN states AS ST ON ST.ST_Id = USPERS.ST_Id 
                                    LEFT JOIN blood_groups AS BG ON BG.BG_Id = USPERS.BG_Id ,
                                    users_accounts UACC
                                    LEFT JOIN salary_paymodes AS SPAY ON SPAY.SP_Id = UACC.SP_Id,
                                    users_qualification USQUAL  
                                    WHERE
                                    USAUTH.LC_Id = LC.LC_Id AND 
                                    USAUTH.DP_Id = DP.DP_Id AND 
                                    USAUTH.DG_Id = DG.DG_Id AND 
                                    USAUTH.OF_Id = OFF.OF_Id AND 
                                    USAUTH.US_Id = UACC.US_Id AND 
                                    USAUTH.US_Id = USPERS.US_Id AND
                                    USAUTH.US_Id = USQUAL.US_Id AND
                                    USAUTH.US_Id = SAL.US_Id AND
                                    USAUTH.UT_Id = AC.ACL_Id  AND USAUTH.US_Status != 5 AND LC.LC_Status != 5 ".$filterUSR." ". $likeFilter." ORDER BY USAUTH.US_FName ASC";
        $result=mysqli_query($GLOBALS['con'],$query);
        while($row=mysqli_fetch_object($result)) {
                $this->UserArray[$count]=$row;
                $count++;
        }
    }
    //select employee status id of selected employee status name in users list header
    function getEmpStatus($ES_Name,$of_id){
        $query="SELECT ES_Id FROM employee_status WHERE OF_Id=".$of_id." AND ES_Name='".$ES_Name."'";
        $result=mysqli_query($GLOBALS['con'],$query);
        $this->getEmpStatusArray=mysqli_fetch_row($result);
        
    }
    function PaymodeWiseSalRptexcel($month,$paymode,$acl,$lcid,$year,$ofId,$BA_Id,$filt){
        if(is_numeric($BA_Id)){
            $filter=" AND USAL.Sal_BnkId=".$BA_Id;
        }
        $count=0;
        $this->excelPaymodeWiseSalRptArray = array(); 
       
         if($paymode==0 && $acl==0)
           $sel_modeOfPayment="(UA.SP_Id=1 OR UA.SP_Id=2 OR UA.SP_Id=3) AND UU.LC_Id=".$lcid;   
        elseif($paymode==0 && $acl==1)   
           $sel_modeOfPayment="(UA.SP_Id=1 OR UA.SP_Id=2 OR UA.SP_Id=3) ";
        else{
            if($paymode==2 && $acl==0){
            $sel_modeOfPayment=" UA.SP_Id=".$paymode." AND UU.LC_Id=".$lcid;    
            }else{
            $sel_modeOfPayment=" UA.SP_Id=".$paymode;
        }
        }
        $count=0;
        $this->excelPaymodeWiseSalRptArray=array();
        if (is_numeric($month)&&is_numeric($paymode)){
           
            $query="SELECT ESR.ESR_Id,UU.US_EMPID,ESR.US_Id,ESR.US_FName,ESR.US_LName,ESR.LC_Name,ESR.ESR_TakeHomeSalary,ESR.ESR_Year,ESR.ESR_Month,UA.SP_Id,UA.US_AccNo,UA.US_Bankname FROM employee_salary_report AS ESR
            LEFT JOIN users_accounts AS UA ON UA.US_Id = ESR.US_Id 
            LEFT JOIN users_salary AS USAL ON USAL.US_Id = ESR.US_Id 
            LEFT Join users_auth AS UU ON UU.US_Id=ESR.US_Id
            WHERE ESR.OF_Id=".$ofId." AND UU.US_Status != 5 AND ESR.ESR_Year=".$year." AND ESR_Month=".$month." AND ".$sel_modeOfPayment.$filter.$filt ;
            $result=mysqli_query($GLOBALS['con'],$query);
                while($row=mysqli_fetch_object($result)){
			$this->excelPaymodeWiseSalRptArray[$count]=$row;
			$count++;
                }
        }
    }
    function SalRptExcel($filter ){
        $count=0;
        $this->SalRptExcelArray = array(); 
//        if (is_numeric($month)){
            $sql= "SELECT ESR_Id,US_Id,US_FName,US_LName,LC_Name,US_GrossSal,US_DedEPF,US_DedESI,US_DedLWF,US_HRASal,US_ConveySal,US_EduSal,US_CcaSal,US_MedSal,US_BasicSal,
                        ESR_Year,ESR_Month,ESR_Lop,ESR_TakeHomeSalary,ESR_Otherallowance,ESR_Lop,ESR_Proftax,ESR_ProfTds,ESR_SalTds,ESR_MealCard,ESR_Salaryadvance,ESR_Loan,ESR_AdjstmntAddition,ESR_AdjstmntDeduction,ESR_Status 
                            FROM employee_salary_report 
                                WHERE ".$filter;
            $result=mysqli_query($GLOBALS['con'],$sql);
            while($row=mysqli_fetch_object($result)){
                    $this->SalRptExcelArray[$count]=$row;
                    $count++;
//            }
                
        }
        
    }
    
    function SalPFESIRptExcel($filter ){
        $count=0;
        $this->SalPFESIRptExcelArray = array(); 
           $sql= "SELECT ESR.ESR_Id,ESR.US_Id,ESR.US_FName,ESR.US_LName,ESR.LC_Name,ESR.ESR_PFESI_Sal,
                        UA.US_PFNo,UA.US_ESI,
                        EP.EP_SalDeductableLeave,EP.EP_WorkDays 	
                            FROM employee_salary_report AS ESR
                                LEFT JOIN users_accounts as UA ON UA.US_Id = ESR.US_Id
                                LEFT JOIN employee_payroll AS EP ON EP.US_Id = ESR.US_Id AND ESR.ESR_Month = EP.EP_Month AND ESR.ESR_Year = EP.EP_Year
                                    WHERE ".$filter;
            $result=mysqli_query($GLOBALS['con'],$sql);
            while($row=mysqli_fetch_object($result)){
                $this->SalPFESIRptExcelArray[$count]=$row;
                $count++;
            }
        
    }
    
    function reportBankBookBSData($stDate='',$enDate='',$filter,$filter_mask) {	 // my bank book	
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
        $this->BankBSArray = array();
      
        $result = mysqli_query($GLOBALS['con'],"SELECT BS.BS_CDate,BS.BS_Date,BS.BS_Amount,DS.DS_Description,TR.TR_Track,IT.IT_Name, MH.MH_Type, LC.LC_Name AS PaidBranch,BS.BS_Status,US.US_FName,US.US_LName,BS_LC.LC_Name, BA.BA_DispName AS bank_acc_name 
                                FROM `balance_sheets` AS BS
                                LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                LEFT JOIN `bank_accounts` AS BA ON BS.BA_Id=BA.BA_Id 
                                LEFT JOIN `locations` AS LC ON BA.LC_Id = LC.LC_Id   
                                LEFT JOIN `locations` AS BS_LC ON BS.LC_Id = BS_LC.LC_Id   
                                WHERE IT.IT_Business = '0'
                                    AND MH.MH_Type IN ('1','2')
                                    AND BS.PM_Id = 2 
                                    AND BS.BS_Status != 0 
                                    AND BS.BS_PettyCashRefId = 0 
                                    ".$dateFilt."
                                    ".$filter_mask."
                                    AND ".$filter."
                                ORDER BY BS.BS_Date DESC");
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->BankBSArray[$count] = $row;
                $count++;
            }
        }
    }
    /* Tally Report*/
    function incomeExportRpt($stDate='',$enDate='',$filter) {
        
        $dateFilt = '';
        $count=0;
        $this->paymentDateArray=array();
        if($stDate!='' && $enDate!=''){
            $stDate     =   date("Y-m-d", strtotime($stDate));
            $enDate     =   date("Y-m-d", strtotime($enDate));
            $dateFilt   =" AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt   =" AND BS.BS_Date   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $dateFilt   =" AND BS.BS_Date   >=  '".$stDate."'";
        }
        $query="SELECT BS.BS_Date AS PaymentDate,TR.TR_Track, MH.MH_Name AS TYPE ,SH.SH_Name AS Head, PM.PM_Name AS PaymentMode,BA.BA_DispName AS BankName,  BCL.CL_Leaf AS ChequeNo,BS_PaidDate AS ChequeDate, BS.BS_Amount AS Amount,LC.LC_Name AS BranchName,IT.IT_Name AS Item,DS.DS_Description AS Description
                        FROM payment_modes AS PM 
                        LEFT JOIN  balance_sheets AS BS ON PM.PM_Id = BS.PM_Id
                        LEFT JOIN  items AS IT ON BS.IT_Id = IT.IT_Id
                        LEFT JOIN  sub_heads AS SH ON IT.SH_Id = SH.SH_Id
                        LEFT JOIN  main_heads AS MH ON  SH.MH_Id = MH.MH_Id
                        LEFT JOIN  tracks AS TR ON BS.TR_Id=TR.TR_Id
                        LEFT JOIN  bank_accounts  AS BA ON BS.BA_Id=BA.BA_Id
                        LEFT JOIN  descriptions AS DS ON BS.BS_Description=DS.DS_Id
                        LEFT JOIN  bank_cheque_leafs AS BCL ON BS.CHQ_Number=BCL.CL_Id
                        LEFT JOIN  bank_branches AS BB ON BS.BB_Id=BB.BB_Id
                        LEFT JOIN  locations AS LC ON BS.LC_Id=LC.LC_Id
                        WHERE IT.IT_Business = 0
                             $filter  $dateFilt 
                            ORDER BY BS.BS_Date DESC";
          $result       =  mysqli_query($GLOBALS['con'],$query); 
          while($row    =  mysqli_fetch_object($result)){
               $this->paymentDateArray[$count]=$row;
               $count++;
          }
    
    }
    
    function payrollExport($Mnt,$Yr,$OFId){
        
        $this->payrollExportArray=array();
        $count = 0;
        $query="SELECT * FROM employee_payroll
                    WHERE EP_Month  = $Mnt
                        AND EP_Year = $Yr
                        AND OF_Id   = $OFId
                            ORDER BY US_FName ASC";
        $result       =  mysqli_query($GLOBALS['con'],$query); 
        while($row    =  mysqli_fetch_object($result)){
             $this->payrollExportArray[$count]=$row;
             $count++;
        }
        
    }
       /* END Tally Report*/
    function reportTrackData($stDate='',$enDate='',$OFId) {		
        $filter = '';
        
        if($stDate!='' && $enDate!=''){
            $stDate  = date("Y-m-d", strtotime($stDate));
            $enDate  = date("Y-m-d", strtotime($enDate));
            $filter .=" AND AJ.AJ_ReceivedDate   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate  = date("Y-m-d", strtotime($enDate));
            $filter .=" AND AJ.AJ_ReceivedDate   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate  = date("Y-m-d", strtotime($stDate));
            $filter .=" AND AJ.AJ_ReceivedDate   >=  '".$stDate."'";
        }

        $this->TrackDataArray = array();
        $sql = "SELECT AJ.AJ_Id, AJ.AJ_FName,TR.TR_Track,LC.LC_Name,IFNULL( ( AJD.AJD_Amount ), 0 ) AS JobAmount,AJ.AJ_ReceivedDate,
                    AJD.AJD_Id, AD.ADOC_Document, ST.ST_Name, APS1.APS_Title, SUM(APSED.APSE_StatutoryNAmt) AS StatutoryNAmt,
                    SUM(APSED.APSE_ExtraNAmt) AS ExtraNAmt, GROUP_CONCAT(APS.APS_Title) AS Process
                    FROM attestation_job_details AS AJ
			LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
                        LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id=AJD.ADOC_Id 
			LEFT JOIN states AS ST ON ST.ST_Id = AJD.ST_Id
                        LEFT JOIN attestation_job_subprocess AS AJS ON AJS.AJD_Id = AJD.AJD_Id
                        LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJS.APS_Id 
			LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id
			LEFT JOIN attestation_process_sub AS APS1 ON APS1.APS_Id = AJD.APS_Id 
                        LEFT JOIN attestation_process_sub_expense_details AS APSED ON ( APSED.APS_Id = APS.APS_Id AND  (AJ.AJ_ReceivedDate) BETWEEN (APSE_FDate)  AND (APSE_LDate) )
                        LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
                        LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id  
                            WHERE AJD.AJD_Id != 0 AND AJ.AJ_Status != 2
                                    AND AJ.OF_Id = '".$OFId."' $filter 
                                    GROUP BY AJD.AJD_Id 
                                        ORDER BY AJ.AJ_Id DESC"; 
        
       /*
        * 
SELECT AJ.AJ_Id, AJ.AJ_FName,TR.TR_Track,LC.LC_Name,IFNULL( ( AJD.AJD_Amount ), 0 ) AS JobAmount,
                    AJD.AJD_Id, AD.ADOC_Document, ST.ST_Name, APS1.APS_Title, APS1.APS_StatutoryNAmt, GROUP_CONCAT(APM.APM_Title)
                    FROM attestation_job_details AS AJ
			LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
                        LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id=AJD.ADOC_Id 
			LEFT JOIN states AS ST ON ST.ST_Id = AJD.ST_Id
                        LEFT JOIN attestation_job_subprocess AS AJS ON AJS.AJD_Id = AJD.AJD_Id
                        LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJS.APS_Id 
			LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id

			LEFT JOIN attestation_process_sub AS APS1 ON APS1.APS_Id = AJD.APS_Id 

                        LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
                        LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id  
                            WHERE  AJ.AJ_ReceivedDate   between '2017-01-01' AND '2017-01-27'   
                            GROUP BY AJD.AJD_Id 
                                ORDER BY AJ.AJ_Id DESC
        */
        
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->TrackDataArray[$row->AJD_Id] = $row;
        }
    
           
    }
    function reportReportOverview($startDate,$endDate,$OF_Id,$filter){
        if($startDate != '' && $endDate != ''){
            $stDate     = date("Y-m-d", strtotime($startDate));
            $enDate     = date("Y-m-d", strtotime($endDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        //  Get all branches Start 
        $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name,LC.LC_Id,LC.LC_Status,COUNT(UA.US_Id) AS StaffCount FROM locations AS LC LEFT JOIN users_auth AS UA ON LC.LC_Id = UA.LC_Id AND ((UA.US_ResignFlag  = 0) OR (UA.US_ResignFlag = 1 AND UA.US_ResignDate > '$enDate')) WHERE LC.OF_Id = $OF_Id AND UA.US_Status != 5 $filter GROUP BY LC.LC_Id ORDER BY LC.LC_Name");

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
                $this->TaxArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH]  = ($this->BussArray[$row->LC_Id][$row->YEAR.'-'.$row->MONTH] / 115 ) * 15; 
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
                } 
            }
        }
        //////////////////////////////// Statutory amount End ///////////////////////////
    }
    function getfixedExpenseItems($preTally_user_ofid) {
        $checkQuery     = mysqli_query($GLOBALS['con'],"SELECT BRS_Fixed_Items,BRS_Variable_Items,BRS_General_Items FROM bonus_report_settings WHERE OF_Id = ".$preTally_user_ofid);
        $row            = mysqli_fetch_array($checkQuery,MYSQLI_ASSOC);
        return $row;
    }
    function excelTrackEnquiry($filter){
       print $sql = "SELECT AE.AE_Id,AE.AE_Name,AE.AE_Mobile,AE.AE_Email,UA.US_FName,UA.US_LName,LC.LC_Name,AE.AE_Status, 
                    COUNT(EF1.AE_Id) AS NO_FollowUp,EF.EF_CDate,EF.US_Id,EF.LC_Id,
                    CONCAT(UA1.US_FName, ' ' ,UA1.US_LName) AS LastUser,LC1.LC_Name AS LastBranch , EF.EF_Chance, EF.EF_NextDate
                        FROM attestation_job_enquiry AS AE 
                        LEFT JOIN attestation_enquiry_followup AS EF ON EF.AE_Id = AE.AE_Id
                        LEFT JOIN attestation_enquiry_followup AS EF1 ON EF1.AE_Id = AE.AE_Id
                        LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                        LEFT JOIN users_auth AS UA1 ON UA1.US_Id = EF.US_Id
                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id
                        LEFT JOIN  locations AS LC1 ON LC1.LC_Id = EF.LC_Id
                        WHERE ".$filter." 
                            AND CASE WHEN EF.EF_CDate != '' THEN EF.EF_CDate = (SELECT MAX(EF_CDate) FROM attestation_enquiry_followup GROUP BY AE_Id HAVING AE_Id = EF.AE_Id) ELSE 1 END 
                                GROUP BY AE.AE_Id
                                    ORDER BY AE.AE_CDate DESC, EF.EF_CDate DESC"; 
        $query = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($query)) {           
            $this->EnquiryDataArray[$count] = $row;
            $count++;
        }         
    }
    function ExportTrackDocument($filter, $orderBy=''){
        $count = 0;
        
        $this->DataArray = array();
        $this->SubArray = array();
        $sql="SELECT AJD.AJD_Id,AD.ADOC_Document,AJDT.AJ_FName,TR.TR_Track,UA.US_FName,UA.US_LName,LC.LC_Name,LC.LC_Name,AJD.AJD_Status 
                                    FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                            ".$filter." 
                                                ORDER BY ".$orderBy." AJDT.AJ_ReceivedDate DESC ";
            $query = mysqli_query($GLOBALS['con'],$sql);
        
  
        while ($row = mysqli_fetch_object($query)) {
            $this->DataArray[$row->AJD_Id] = $row;
            if($count) {
                $ids = $ids.','.$row->AJD_Id;
            }else {
              $ids = $row->AJD_Id;
            }
            $count++;
        }
        $subPrQuery = mysqli_query($GLOBALS['con'],"SELECT AJS.*,APS.APS_Title,APM.APM_Title FROM attestation_job_subprocess AS AJS 
                                LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id=AJS.APS_Id
                                LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id
                                WHERE AJS.AJD_Id IN (".$ids.")"
                            );
        while ($SubRow = mysqli_fetch_object($subPrQuery)) {
            $this->SubArray[$SubRow->AJD_Id][] = $SubRow;
        }
    }
    /**
    * Create  Excel files from the header and data provided in to the functions
    * Title, filename, right align columns, left align columns are of extra inputs
    * Function created by Bilin @ 03-07-2025
    */
    function saveExcelRpt($excelData, $headerArray, $extraparms=[]) 
    {
        extract($extraparms);
        $sheetTitle     = (isset($sheetTitle)) ? $sheetTitle : 'Export Report';
        $fileName       = (isset($fileName)) ? trim($fileName).'.xlsx' : date("dFy", time()) . '_' . substr(md5(rand()), 2, 6) . ".xlsx";
        $rightAlignCol  = (isset($rightAlignCol)) ? $rightAlignCol : [];
        $leftAlignCol   = (isset($leftAlignCol)) ? $leftAlignCol : [];
        // Create new PHPExcel object
        $objPHPExcel = new Spreadsheet();
        // Fill worksheet from values in array
        $objPHPExcel->getActiveSheet()->fromArray($headerArray, null, 'A1');
         // get maximum column name with data
        $maxColumn      =\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headerArray)); 
        //check the data array was empty or not
        if ($excelData) {
            $objPHPExcel->getActiveSheet()->fromArray($excelData, null, 'A2');
        } else {
            $objPHPExcel->getActiveSheet()->getStyle("A2:" . $maxColumn . "2")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue("A2", "No records found");
        }
        
        //define left and right align styles
        $styleArray = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ));
        $styleArray2 = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:' .
                $objPHPExcel->getActiveSheet()->getHighestColumn().
                $objPHPExcel->getActiveSheet()->getHighestRow()
        )->applyFromArray($styleArray);

       

        // Rename worksheet       
        $objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
        $objPHPExcel->getActiveSheet()->getStyle("A1:".$maxColumn."1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("A1:".$maxColumn."1")->applyFromArray(array("font" => array( "bold" => true)));

        // right align
        if (!empty($rightAlignCol)) {
            foreach ($rightAlignCol as $rcol) {
                $objPHPExcel->getActiveSheet()->getStyle($rcol.'2:'.$rcol.$objPHPExcel->getActiveSheet()->getHighestRow())->applyFromArray($styleArray2);
            }
        }
        // left align
        if (!empty($leftAlignCol)) {
            foreach ($leftAlignCol as $lcol) {
                $objPHPExcel->getActiveSheet()->getStyle($lcol.'2:'.$lcol.$objPHPExcel->getActiveSheet()->getHighestRow())->applyFromArray($styleArray);
            }
        }
        
        // Set AutoSize
        for ($i = 0; $i <= count($headerArray); $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }        
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        ob_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="file.xls"');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
        $writer->save($BASEPATH . "uploads/excelFile/" . $fileName);
        return "XL_" . $fileName;
    }
    
}
?>