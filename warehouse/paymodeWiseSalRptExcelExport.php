<?php
require_once($BASEPATH ."preTallyClass/ExportExcelClass.php");
$ExcelObj         = new ExportExcelClass();
$MonthData=array('1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=> "November",'12'=>"December");
$month   = $_REQUEST['month'];
$year    = $_REQUEST['year'];
$paymode =$_REQUEST['paymode'];
$useridFilter=$_REQUEST['useridFilter'];
$nameFilter=$_REQUEST['nameFilter'];
$branchFilter=$_REQUEST['branchFilter'];
$filt='';
if(isset($REQUEST['BA_Id'])){
$Ba_Id=ltrim($_REQUEST['BA_Id'],"B_");
}
if($useridFilter!=''){
   $filt.=" AND UU.US_EMPID like '".$useridFilter."%'";
}
if($nameFilter!=''){
    $filt.=" AND ESR.US_FName like '".$nameFilter."%'";
}
if($branchFilter!=''){
    $filt.=" AND ESR.LC_Name like '".$branchFilter."%'";
}
    

$ExcelObj->PaymodeWiseSalRptexcel($month,$paymode,$ACL_Obj->ACL_SalPMwiseAll,$preTally_user_lcid,$year,$preTally_user_ofid,$Ba_Id,$filt);
$PaySalRpt_Obj          = $ExcelObj->excelPaymodeWiseSalRptArray;
$headerArray= array();
    $headerArray['UserID']                   = "User ID";
    $headerArray['Name']                    = " Name";
    $headerArray['Branch']                   = "Branch";
    $headerArray['Month']                    = "Month";
    $headerArray['Year']                     = "Year";
    $headerArray['TakeHomeSalary']           = "Take Home Salary";
    $headerArray['Accountno']                = "Account no";
    $headerArray['BankName']                 = "Bank Name";
    
foreach($PaySalRpt_Obj as $rw) {
    $arrayStr                    = array();
    $arrayStr['UserID']             =   $rw->US_EMPID;
    $arrayStr['Name']               =   $rw->US_FName." ".$rw->US_LName;
    $arrayStr['Branch']             =   $rw->LC_Name;
    $arrayStr['Month']              =   $MonthData[$rw->ESR_Month];
    $arrayStr['Year']               =   $rw->ESR_Year;
    $arrayStr['TakeHomeSalary']     =   $rw->ESR_TakeHomeSalary;
    $arrayStr['Accountno']          =   $rw->US_AccNo;
    $arrayStr['BankName']           =   $rw->US_Bankname;
    $data[] = array_map('trim',$arrayStr);
}
echo $ExcelObj->createExcel($data, $headerArray);
   
?>