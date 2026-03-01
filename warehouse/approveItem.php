<?php
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

$ItemObj = new ItemClass();
$UserObj = new UserClass();

$IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
echo $ItemObj->approveItem($IT_Approval,$_REQUEST['IT_Id'],$preTally_user_id);
?>