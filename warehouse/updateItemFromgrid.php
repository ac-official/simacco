<?php
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
$BkupObj = new BackupClass();
$ItmObj = new ItemClass();
$UserObj= new UserClass();
if($_REQUEST['ItmName'])
$ItmObj->IT_Data['IT_Name']=$_REQUEST['ItmName'];
if($_REQUEST['ItmSubhead'])
$ItmObj->IT_Data['SH_Id']=$_REQUEST['ItmSubhead'];    
if($_REQUEST['ItmStatus']!=""){
$ItmObj->IT_Data['IT_Status']=$_REQUEST['ItmStatus'];
if($_REQUEST['ItmStatus']=="3")
$ItmObj->IT_Data['IT_Approval']=$UserObj->myReportingPerson($preTally_user_id);
}
$BkupObj->backupDetails('IT_Id = '.$_REQUEST['ItmID'],$preTally_user_id,'items_bkup','items');
echo $ItmObj->updateItem($_REQUEST['ItmID']);
?>
