<?php
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

$DescObj = new DescriptionClass();
$UserObj = new UserClass();

$IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
echo $DescObj->approveDescription($IT_Approval,$_REQUEST['DS_Id'],$preTally_user_id);
?>