<?php
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
$BkupObj = new BackupClass();
$ItmObj = new DescriptionClass();
$UserObj= new UserClass();
if($_REQUEST['DescName'])
$ItmObj->DS_Data['DS_Description']=$_REQUEST['DescName'];
if($_REQUEST['DescStatus']!=""){
$ItmObj->DS_Data['DS_Status']=$_REQUEST['DescStatus'];
if($_REQUEST['DescStatus']=="3")
$ItmObj->DS_Data['DS_Approval']=$UserObj->myReportingPerson($preTally_user_id);
$ItmObj->DS_Data['DS_Approved']=$preTally_user_id;
}
$BkupObj->backupDetails('DS_Id = '.$_REQUEST['DS_Id'],$preTally_user_id,'descriptions_bkup','descriptions');
echo $ItmObj->updateDescription($_REQUEST['DS_Id']);
?>
