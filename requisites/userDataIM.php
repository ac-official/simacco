<?php
/*if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}*/

include_once($BASEPATH . "includes/functions.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");

$filter = $_REQUEST['IMS'];
$myID = $preTally_user_id;
$OFID = $preTally_user_ofid;
//die($myID);
$UserObj = new UserClass();
$UserObj->userDataIM($filter, $myID, $OFID);
$IM_Obj = $UserObj->UserArray;
echo json_encode($IM_Obj);
//print_r($IM_Obj);
//echo 'Anoop';
?>