<?php
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$AttObj =  new AttendanceClass();
$year       = date("Y");
$curruentDate=date("Y-m-d H:i:s");
if($REQUEST['att_year'] && $REQUEST['att_month'] && $REQUEST['att_month'].'-'.$REQUEST['att_year'] != date('m-Y')){       
  $year = $REQUEST['att_year'];   
  $month  = $REQUEST['att_month'];
  $g_date     =$year.'-'.$month.'-1';
  $lstdate = date("t", strtotime($g_date));
}
 else {    
     echo "3";
     return;
}
$dataSalaryReport=array();
$count=0;
$ml_count=0;
$AttObj->getEmployeePayrollData($month,$year,$preTally_user_ofid);
$payrollObj=$AttObj->getEmployeePayrollDataArray;
if($payrollObj){
    foreach ($payrollObj as $rw){
        if($rw->EP_TakehomeSal<0){$THS=0;}else{$THS=$rw->EP_TakehomeSal;}        
            $final_arraySalReport=array();
            $final_arraySalReport= array(
                            'US_Id'=> $rw->US_Id,
                            'ESR_Month'=>$rw->EP_Month 	,
                            'ESR_Year'=>$rw->EP_Year,
                            'OF_Id'=>$rw->OF_Id,
                            'US_FName'=>"'".$rw->US_FName."'",
                            'US_LName'=>"'".$rw->US_LName ."'",
                            'LC_Name'=>"'".$rw->LC_Name."'",
                            'US_GrossSal'=>$rw->US_GrossSal,
                            'ESR_Lop'=>$rw->EP_Lop,
                            'ESR_Otherallowance'=>$rw->EP_Otherallowance,
                            'ESR_TakeHomeSalary'=>$THS,
                            'US_HRASal'=>$rw->US_HRASal,
                            'US_ConveySal'=>$rw->US_ConveySal,
                            'US_EduSal'=>$rw->US_EduSal,
                            'US_MedSal'=>$rw->US_MedSal,
                            'US_CcaSal'=>$rw->US_CcaSal,
                            'US_BasicSal '=>$rw->US_BasicSal,
                            'US_DedEPF'=>$rw->US_DedEPF,
                            'US_DedESI '=>$rw->US_DedESI,
                            'US_DedLWF'=>$rw->US_DedLWF,
                            'ESR_EmpConEPF'=>$rw->EP_EmpConEPF,
                            'ESR_EmpConESI'=>$rw->EP_EmpConESI,
                            'ESR_EmpConLWF'=>$rw->EP_EmpConLWF,
                            'ESR_Proftax'=>$rw->EP_Proftax,
                            'ESR_ProfTds'=>$rw->EP_ProfTds,
                            'ESR_SalTds '=>$rw->EP_SalTds,
                            'ESR_MealCard '=>$rw->EP_MealCard,
                            'ESR_Salaryadvance'=>$rw->EP_Salaryadvance,
                            'ESR_Loan'=>$rw->EP_Loan,
                            'ESR_AdjstmntAddition'=>$rw->EP_AdjstmntAddition,
                            'ESR_AdjstmntDeduction'=>$rw->EP_AdjstmntDeduction,
                            'ESR_PFESI_Sal'=>$rw->EP_PFESI_Sal,
                            'ESR_TotWrkDays'=>$rw->EP_TotWrkDays,
                            'ESR_PrsntDays'=>$rw->EP_PrsntDays,
                            'ESR_HlfDays'=>$rw->EP_HlfDays,
                            'ESR_LOPDays'=>$rw->EP_LOPDays,
                            'ESR_Status'=>0,
                            'ESR_GeneratedBy'=>$preTally_user_id,
                            'ESR_CDate'=>"'".$curruentDate."'",
                            'EP_HoursIncomplete'=>$rw->EP_HoursIncomplete
                            );
                        if($rw->EP_MealCard>0){
                            $mealCardData[$ml_count]=array('US_Id'=>$rw->US_Id,
                                'SM_Month'=>str_pad($rw->EP_Month, 2, "0", STR_PAD_LEFT),
                                'SM_Year'=>$rw->EP_Year,
                                'SM_Amount'=>$rw->EP_MealCard,
                                'SM_Generated'=>$preTally_user_id,
                                'SM_Status'=>0);
                            $ml_count++;
                        }
                        $dataSalaryReport[$count] = $final_arraySalReport;
                        $count++;
    }
    if($AttObj->verifyMonth("employee_salary_report","ESR_Month",$month,$preTally_user_ofid,"ESR_Year",$year)){  
        echo $result=$AttObj->generateEmployeePayroll("employee_salary_report",$dataSalaryReport);
        if($result==1){
        $Meal_result=$AttObj->generateEmployeePayroll("salary_meal_allowance",$mealCardData);
        }
    }
    else{
        
        echo "2";
    }
}else{
   
    echo "4";
    
}






?>