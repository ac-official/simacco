<?php
require_once($BASEPATH ."preTallyClass/ExportExcelClass.php");
$ExcelObj   = new ExportExcelClass();

$filterData = $_REQUEST['filterParams'];

$filter = ' ESR.OF_Id = "'. $preTally_user_ofid .'" AND ESR_Year = "'. $filterData[1] .'" AND ESR_Month = "'. $filterData[0] .'" ';

if($filterData[2] != ''){
    $filter .=" AND ESR.US_FName LIKE '".$filterData[2]."%'";
}
if($filterData[3] != ''){
    $filter .=' AND ESR.LC_Name like "'.$filterData[3].'%"';
}
if($filterData[4] == "PF") {
    $filter .='AND ESR.US_DedEPF   != "0"';
}else if($filterData[4] == "ESI"){
    $filter .='AND ESR.US_DedESI   != "0"';
}

$ExcelObj->SalPFESIRptExcel($filter);
$SalPFRpt_Obj = $ExcelObj->SalPFESIRptExcelArray;

if($filterData[4] == "PF") {
    
    $headerArray = array();
    $headerArray['PF_Number']    = "PF NUMBER";
    $headerArray['Name']         = "EMPLLOYEE NAME";
    $headerArray['Branch']       = "BRANCH";
    $headerArray['LOP_Days']     = "LOP DAYS";
    $headerArray['PF_Salary']    = "PF SLARY";

    foreach($SalPFRpt_Obj as $rw) {

        $arrayStr = array();
        $arrayStr['PF_Number']   =   $rw->US_PFNo;
        $arrayStr['Name']        =   $rw->US_FName." ".$rw->US_LName;
        $arrayStr['Branch']      =   $rw->LC_Name;
        $arrayStr['LOP_Days']    =   $rw->EP_SalDeductableLeave;
        $arrayStr['PF_Salary']   =   $rw->ESR_PFESI_Sal;

        $data[] = array_map('trim',$arrayStr);
    }
}else if($filterData[4] == "ESI"){
    $headerArray = array();
    $headerArray['IP_Number']    = "IP NUMBER";
    $headerArray['Name']         = "EMPLLOYEE NAME";
    $headerArray['Branch']       = "BRANCH";
    $headerArray['Working_Days'] = "NO:OF WORKING DAYS";
    $headerArray['ESI_Salary']   = "ESI SLARY";

    foreach($SalPFRpt_Obj as $rw) {

        $arrayStr = array();
        $arrayStr['PF_Number']   =   $rw->US_ESI;
        $arrayStr['Name']        =   $rw->US_FName." ".$rw->US_LName;
        $arrayStr['Branch']      =   $rw->LC_Name;
        $arrayStr['Working_Days']=   $rw->EP_WorkDays;
        $arrayStr['ESI_Salary']  =   $rw->ESR_PFESI_Sal;

        $data[] = array_map('trim',$arrayStr);
    }
}
echo $ExcelObj->createExcel($data, $headerArray);

?>