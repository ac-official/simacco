<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$AttObj =  new AttendanceClass();
$EPH_ColLabel= $_REQUEST['EPH_ColLabel'];
$EP_Id= $_REQUEST['EP_Id'];
$US_Id= $_REQUEST['US_Id'];
$EPH_ColValue= $_REQUEST['EPH_ColValue'];
$EP_TakehomeSal= $_REQUEST['EP_TakehomeSal'];
$month=$REQUEST['EP_Month'];
$year=$REQUEST['EP_Year'];
$Month=$_REQUEST['Month'];
$Year=$_REQUEST['Year'];
$US_GrossSal=$_REQUEST['US_GrossSal'];
$lop=$_REQUEST['lop'];
$ESI=$_REQUEST['ESI'];
$EPF=$_REQUEST['EPF'];
$LWF=$_REQUEST['LWF'];
$empconESI=$_REQUEST['empconESI'];
$empconEPF=$_REQUEST['empconEPF'];
$empconLWF=$_REQUEST['empconLWF'];
$PFESISal=$_REQUEST['EP_PFESI_Sal'];
$ProfTDS=$_REQUEST['ProfTDS'];
    $AttObj->payroll_History_Data=array(
            'US_Id'=>$US_Id ,
            'EP_Id' => $EP_Id,
            'EPH_ColLabel' => $EPH_ColLabel,
            'EPH_ColValue' => $EPH_ColValue,
            'EPH_HRId'=>$preTally_user_id,
            'EPH_Date'=> date('Y-m-d h:i:s ', time())
    );

    $AttObj->payroll_Data=array(
        $EPH_ColLabel=>$EPH_ColValue,
        'EP_TakehomeSal'=>$EP_TakehomeSal,
        'US_GrossSal'=>$US_GrossSal,
        'EP_Lop'=>$lop,
        'US_DedESI'=>$ESI,
        'US_DedEPF'=>$EPF,
        'US_DedLWF'=>$LWF,
        'EP_PFESI_Sal' =>$PFESISal, 
        'EP_ProfTds'=> $ProfTDS,   
        'EP_EmpConEPF'=> $empconEPF,
        'EP_EmpConESI'=> $empconESI,
        'EP_EmpConLWF'=> $empconLWF,
    );
if(isset($REQUEST['EP_Month'])){
    if($AttObj->verifyMonth("employee_salary_report","ESR_Month",$month,$preTally_user_ofid,"ESR_Year",$year)){      
        echo "11111";
    }else{
     echo "00000";
    }
    return;
}

 $AttObj->createPayrollHistory();    
 $AttObj->changeEmployeePayrollData($EP_Id,$Month,$Year);
?>