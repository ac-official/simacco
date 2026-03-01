<?php
/*if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}*/

include_once($BASEPATH . "includes/functions.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");

$filter = $_REQUEST['USID'];
$UserObj = new UserClass();
$IMInfo_Obj = $UserObj->userDataIMInfo($filter);
echo json_encode($IMInfo_Obj);
?>