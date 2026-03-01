<?php
/* File for adding user details of registered users through excel uploading */
include_once ($BASEPATH.'preTallyClass/ImportExcelClass.php');
$ExcelObj = new ImportExcelClass();
$excelData= (array) json_decode($_REQUEST['IU_ExcelArray']);
if($excelData != 0 && $excelData != null) {
   $ExcelObj->userDetailsExcel($excelData,$preTally_user_id,$preTally_user_ofid);
   echo json_encode($ExcelObj->userDetailsErrorArray);
}
exit;