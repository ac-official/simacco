<?php
/* File for adding branches through excel uploading */
require_once($BASEPATH . "preTallyClass/ImportExcelClass.php");
$ExcelObj = new ImportExcelClass();
$excelData= (array) json_decode($_REQUEST['IBB_ExcelArray']);

if($excelData != 0 && $excelData != null) {
    $ExcelObj->newBranchExcel($excelData,$preTally_user_id,$preTally_user_ofid);
    echo json_encode($ExcelObj->errorArray);
}
exit;