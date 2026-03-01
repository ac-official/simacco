<?php
/* File for adding descriptions through excel uploading */
require_once($BASEPATH . "preTallyClass/ImportExcelClass.php");
$ExcelObj = new ImportExcelClass();
$excelData = (array) json_decode($_REQUEST['DS_ExcelArray']);

if($excelData != 0 && $excelData != null) {
   $ExcelObj->newDescriptionExcel($excelData,$preTally_user_id,$preTally_user_ofid);
   echo json_encode($ExcelObj->descErrorArray);
}
exit;