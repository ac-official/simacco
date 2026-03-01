<?php
/* File for adding items through excel uploading */
require_once($BASEPATH . "preTallyClass/ImportExcelClass.php");
$ExcelObj = new ImportExcelClass();
$excelData = (array) json_decode($_REQUEST['IU_ExcelArray']);

if($excelData != 0 && $excelData != null) {
   $ExcelObj->newItemExcel($excelData,$preTally_user_id,$preTally_user_ofid);
   echo json_encode($ExcelObj->itemErrorArray);
}
exit;