<?php
/* File for adding leaves through excel uploading */
require_once($BASEPATH . "preTallyClass/ImportExcelClass.php");
$ExcelObj = new ImportExcelClass();
$excelData = (array) json_decode($_REQUEST['LT_ExcelArray']);
if($excelData != 0 && $excelData != null) {
   $ExcelObj->leaveExcel($excelData,$preTally_user_id,$preTally_user_ofid);
   echo json_encode($ExcelObj->leaveErrorArray);
}
exit;