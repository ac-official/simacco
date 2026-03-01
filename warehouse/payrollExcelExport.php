<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/ExportExcelClass.php");
$ExcelObj       = new ExportExcelClass();

$data           = array();
$headerArray    = array();

$ExcelResult   = $ExcelObj->payrollExport($REQUEST['Month'],$REQUEST['Year'],$preTally_user_ofid);
$EP_Obj        = $ExcelObj->payrollExportArray;

/*
'US_DedEPF'=>$rw->US_DedEPF,
'US_DedESI '=>$rw->US_DedESI,
'US_DedLWF'=>$rw->US_DedLWF, 
 */

$headerArray['SL_No']               = "SL No";
$headerArray['US_Name']             = "User Details";
$headerArray['LC_Name']             = '';
$headerArray['US_GrossSal']         = "Gross Salary";
$headerArray['EP_TakehomeSal']      = "Take Home Salary";
$headerArray['US_BasicSal']         = "Salary Additions";
$headerArray['US_HRASal']           = "";
$headerArray['US_CcaSal']           = "";
$headerArray['US_ConveySal']        = "";
$headerArray['US_EduSal']           = "";
$headerArray['US_MedSal']           = "";
$headerArray['EP_Otherallowance']   = "";
$headerArray['EP_PFESI_Sal']        = "PF/ESI";
$headerArray['US_DedEPF']           = "Salary Deductions";
$headerArray['US_DedESI']           = "";
$headerArray['US_DedLWF']           = "";
$headerArray['EP_Lop']              = "";
$headerArray['EP_Proftax']          = "";
$headerArray['EP_ProfTds']          = "";
$headerArray['EP_SalTds']           = "";
$headerArray['EP_Salaryadvance']    = "";
$headerArray['EP_Loan']             = "";
$headerArray['EP_AdjstmntAddition'] = "Salary Adjustment";
$headerArray['EP_AdjstmntDeduction']= "";

$arrayStr       = array();

$arrayStr['SL_No']                  = "";
$arrayStr['US_Name']                = "User Name";
$arrayStr['LC_Name']                = "Location Name";
$arrayStr['US_GrossSal']            = "";
$arrayStr['EP_TakehomeSal']         = "";
$arrayStr['US_BasicSal']            = "Basic Salary";
$arrayStr['US_HRASal']              = "HRA Allowance";
$arrayStr['US_CcaSal']              = "CCA Allowance";
$arrayStr['US_ConveySal']           = "Conveyance Allowance";
$arrayStr['US_EduSal']              = "Educational Allowance";
$arrayStr['US_MedSal']              = "Medical Allowance";
$arrayStr['EP_Otherallowance']      = "Other Allowance";
$arrayStr['EP_PFESI_Sal']           = "";
$arrayStr['US_DedEPF']              = "EPF";
$arrayStr['US_DedESI']              = "ESI";
$arrayStr['US_DedLWF']              = "LWF";
$arrayStr['EP_Lop']                 = "LOP";
$arrayStr['EP_Proftax']             = "Professional Tax";
$arrayStr['EP_ProfTds']             = "Professional Tds";
$arrayStr['EP_SalTds']              = "Salary Tds";
$arrayStr['EP_MealCard']            = "Meal Card";
$arrayStr['EP_Salaryadvance']       = "Salary Advance";
$arrayStr['EP_Loan']                = "Loan";
$arrayStr['EP_AdjstmntAddition']    = "Addition";
$arrayStr['EP_AdjstmntDeduction']   = "Deduction";
$arrayStr['EP_WorkDays']            = "Days Worked";
//$arrayStr['EP_DeductDays']          = "Salary Deducted Days";
$data[]                             = array_map('trim',$arrayStr);

foreach($EP_Obj as $slNo => $EPData){ 
    $arrayStr       = array();
    
    $arrayStr['SL_No']               = $slNo+1;
    $arrayStr['US_Name']             = $EPData->US_FName." ".$EPData->US_LName;
    $arrayStr['LC_Name']             = $EPData->LC_Name;
    $arrayStr['US_GrossSal']         = $EPData->US_GrossSal;
    $arrayStr['EP_TakehomeSal']      = $EPData->EP_TakehomeSal;
    $arrayStr['US_BasicSal']         = $EPData->US_BasicSal;
    $arrayStr['US_HRASal']           = $EPData->US_HRASal;
    $arrayStr['US_CcaSal']           = $EPData->US_CcaSal;
    $arrayStr['US_ConveySal']        = $EPData->US_ConveySal;
    $arrayStr['US_EduSal']           = $EPData->US_EduSal;
    $arrayStr['US_MedSal']           = $EPData->US_MedSal;
    $arrayStr['EP_Otherallowance']   = $EPData->EP_Otherallowance;
    $arrayStr['EP_PFESI_Sal']        = $EPData->EP_PFESI_Sal;
    $arrayStr['US_DedEPF']           = $EPData->US_DedEPF;
    $arrayStr['US_DedESI']           = $EPData->US_DedESI;
    $arrayStr['US_DedLWF']           = $EPData->US_DedLWF;
    $arrayStr['EP_Lop']              = $EPData->EP_Lop;
    $arrayStr['EP_Proftax']          = $EPData->EP_Proftax;
    $arrayStr['EP_ProfTds']          = $EPData->EP_ProfTds;
    $arrayStr['EP_SalTds']           = $EPData->EP_SalTds;
    $arrayStr['EP_MealCard']         = $EPData->EP_MealCard;
    $arrayStr['EP_Salaryadvance']    = $EPData->EP_Salaryadvance;
    $arrayStr['EP_Loan']             = $EPData->EP_Loan;
    $arrayStr['EP_AdjstmntAddition'] = $EPData->EP_AdjstmntAddition;
    $arrayStr['EP_AdjstmntDeduction']= $EPData->EP_AdjstmntDeduction;
    $arrayStr['EP_WorkDays']         = $EPData->EP_WorkDays;
    //$arrayStr['EP_DeductDays']       = $EPData->EP_AdjstmntDeduction;
    
    $data[]                             = array_map('trim',$arrayStr);

}


echo $ExcelObj->createPayrollExcel($data, $headerArray);
?>