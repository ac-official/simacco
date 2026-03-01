
<?php
/**
* close track (accept track id as input)
* Created by Bilin @ 01-08-2025
*/
// include the class files
require_once($BASEPATH . "preTallyClass/TrackClass.php");
// create object for needed class files
$TrackObj 	= new TrackClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$trid 		= (isset($_REQUEST['trid'])) ? (int)$_REQUEST['trid']:0;

if ($trid > 0) {
	$TrackObj->closeTracks($trid,$preTally_user_id);
	$msg 		= 'Track Successfully Closed';
	$retstatus 	= 1; 
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>