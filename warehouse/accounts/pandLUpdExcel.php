<?php
// some extra expense or liability list directly uploaded into database via excel export
// Created By Bilin @ 08-01-2026
require_once($BASEPATH . "preTallyClass/ImportExcelClass.php");
$ExcelObj = new ImportExcelClass();
$excelData = (array) json_decode($_REQUEST['accPL_ExcelArray']);
if($excelData != 0 && $excelData != null) {
   $ExcelObj->pandLUpdExcel($excelData,$preTally_user_id,$preTally_user_ofid);
   echo json_encode($ExcelObj->leaveErrorArray);
}
exit;