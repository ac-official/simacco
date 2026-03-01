<?php
// Updated salary amount saved in to database based on the excel uploaded by the user
// Created By Bilin @ 21-03-2025
require_once($BASEPATH . "preTallyClass/ImportExcelClass.php");
$ExcelObj = new ImportExcelClass();
$excelData = (array) json_decode($_REQUEST['SU_ExcelArray']);
if($excelData != 0 && $excelData != null) {
   $ExcelObj->salUpdtExcel($excelData,$preTally_user_id,$preTally_user_ofid);
   echo json_encode($ExcelObj->leaveErrorArray);
}
exit;