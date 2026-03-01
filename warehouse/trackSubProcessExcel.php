<?php
/* File for adding sub processes through excel uploading */
require_once($BASEPATH . "preTallyClass/ImportExcelClass.php");
$ExcelObj = new ImportExcelClass();
$excelData = (array) json_decode($_REQUEST['TK_ExcelArray']);
if($excelData != 0 && $excelData != null) {
   $ExcelObj->trackSubProcessExcel($excelData,$preTally_user_ofid);
   echo json_encode($ExcelObj->subProcessErrorArray);
}
exit;