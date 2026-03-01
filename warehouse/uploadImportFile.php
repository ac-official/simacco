<?php
require_once($BASEPATH . "preTallyClass/ImportExcelClass.php");
$ExcelObj = new ImportExcelClass();
$excelData = json_decode($REQUEST['data'],true);
if($excelData != 0 && $excelData != null)  {
   echo $ExcelObj->readExcel($excelData);
}
exit;