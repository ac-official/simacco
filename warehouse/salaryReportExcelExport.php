<?php
require_once($BASEPATH ."preTallyClass/ExportExcelClass.php");
$ExcelObj         = new ExportExcelClass();
$MonthData=array('1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=> "November",'12'=>"December");

/*
$month   = $REQUEST['month'];
$filt='';
$salApprovedBlockedFilter   = $REQUEST['salApprovedBlockedFilter'];
$nameFilter   = trim($REQUEST['nameFilter']);
$branchFilter   = trim($REQUEST['branchFilter']);

if($salApprovedBlockedFilter!=" "){
    if($salApprovedBlockedFilter=="Already Sent"){
        $salApprovedBlockedFilter=1;
        $filt=" AND ESR_Status=".$salApprovedBlockedFilter;
    }else if($salApprovedBlockedFilter=="Not Sent"){
        $salApprovedBlockedFilter=0;
        $filt=" AND ESR_Status=".$salApprovedBlockedFilter;
    }
}
if($nameFilter!=" "){
    $filt.=" AND US_FName like '$nameFilter%'";
}   
if($branchFilter!=" "){
    $filt.=" AND LC_Name like '$branchFilter%'";
    
}

*/
//$year    = date("Y");

$filterData = $_REQUEST['filterParams'];

$filter = ' OF_Id = "'. $preTally_user_ofid .'" AND ESR_Year = "'. $filterData[1] .'" AND ESR_Month = "'. $filterData[0] .'" ';

if($filterData[2] != ''){
    $filter .=" AND US_FName LIKE '".$filterData[2]."%'";
}
if($filterData[3] != ''){
    $filter .=' AND LC_Name like "'.$filterData[3].'%"';
}
if($filterData[4] != 'all'){
    $filter .='AND ESR_Status  = "'.$filterData[4].'"';
}

$ExcelObj->SalRptExcel($filter);
$SalRpt_Obj          = $ExcelObj->SalRptExcelArray;
$headerArray= array();
    $headerArray['Name']                            = "Name";
    $headerArray['Branch']                          = "Branch";
    $headerArray['Month']                           = "Month";
    
    $headerArray['Year']                            = "Year";
    $headerArray['BasicSalary']                     = "Basic Salary";
    $headerArray['HRAAllowance']                    = "HRA Allowance";
    
    $headerArray['CCAAllowance']                    = "CCA Allowance";
    $headerArray['ConveyanceAllowance']             = "Conveyance Allowance";
    $headerArray['EducationAllowance']              = "Education Allowance";
    
    $headerArray['MedicalAllowance']                = "Medical Allowance";
    $headerArray['OtherAllowance']                  = "Other Allowance";
    $headerArray['GrossSalary']                     = "Gross Salary";
    
    
    $headerArray['EPF']                             = "EPF";
    $headerArray['ESI']                             = "ESI";
    $headerArray['LWF']                             = "LWF";
    
    $headerArray['LOP']                             = "LOP";
    $headerArray['ProfessionalTax']                 = "Professional Tax";
    $headerArray['ProfTds']                         = "Professional TDS";
    $headerArray['SalTds']                          = "Salary TDS";
    $headerArray['MealCard']                        = "Meal Card";
    $headerArray['SalaryAdvance']                   = "Salary Advance";
    $headerArray['Loan']                            = "Loan";
    $headerArray['Addition']                        = "Addition";
    
    $headerArray['Deduction']                       = "Deduction";
    $headerArray['TakeHomeSalary']                  = "Take Home Salary";
    $headerArray['status']                          = "Status";
   
foreach($SalRpt_Obj as $rw) {
    if($rw->ESR_Status==1){$status="Already send";}else if($rw->ESR_Status==0){$status="Not send";}
    $arrayStr                    = array();
    $arrayStr['Name']                           =   $rw->US_FName." ".$rw->US_LName;
    $arrayStr['Branch']                         =   $rw->LC_Name;
    $arrayStr['Month']                          =   $MonthData[$rw->ESR_Month];
    
    $arrayStr['Year']                           =   $rw->ESR_Year;
    $arrayStr['BasicSalary']                    =   $rw->US_BasicSal;
    $arrayStr['HRAAllowance']                   =   $rw->US_HRASal;
    
    $arrayStr['CCAAllowance']                   =   $rw->US_CcaSal;
    $arrayStr['ConveyanceAllowance']            =   $rw->US_ConveySal;
    $arrayStr['EducationAllowance']             =   $rw->US_EduSal;
    
    $arrayStr['MedicalAllowance']               =   $rw->US_MedSal;
    $arrayStr['OtherAllowance']                 =   $rw->ESR_Otherallowance;
    $arrayStr['GrossSalary']                    =   $rw->US_GrossSal;
    
    $arrayStr['EPF']                            =   $rw->US_DedEPF;
    $arrayStr['ESI']                            =   $rw->US_DedESI ;
    $arrayStr['LWF']                            =   $rw-> US_DedLWF;
    
    $arrayStr['LOP']                            =   $rw->ESR_Lop;
    $arrayStr['ProfessionalTax']                =   $rw->ESR_Proftax;
    $arrayStr['ProfTds']                        =   $rw->ESR_ProfTds;
    $arrayStr['SalTds']                         =   $rw->ESR_SalTds;
    $arrayStr['MealCard']                       =   $rw->ESR_MealCard;
    $arrayStr['SalaryAdvance']                  =   $rw->ESR_Salaryadvance ;
    $arrayStr['Loan']                           =   $rw->ESR_Loan ;
    $arrayStr['Addition']                       =   $rw->ESR_AdjstmntAddition;
    
    $arrayStr['Deduction']                      =   $rw->ESR_AdjstmntDeduction;
    $arrayStr['TakeHomeSalary']                 =   $rw->ESR_TakeHomeSalary;
    $arrayStr['status']                         =   $status;
    
    $data[] = array_map('trim',$arrayStr);
}
echo $ExcelObj->createExcel($data, $headerArray);

?>