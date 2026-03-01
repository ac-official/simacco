<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/GeneralClass.php");
$GeneralObj = new GeneralClass();
$sibling_id = (isset($_REQUEST['sibling_id'])) ? (int)$_REQUEST['sibling_id']: 0;
$user_id 	= (isset($_REQUEST['user_id'])) ? (int)$_REQUEST['user_id']: 0;
if ($sibling_id > 0 || $preTally_user_ofid == 1) {
	$extsilbing 	= [];
	if ($user_id > 0) {
		$offidss 	= $GeneralObj->getValue('user_office', 'GROUP_CONCAT(DISTINCT OF_Id) AS offids','WHERE US_Id = '.$user_id.' AND status = 1');
		$extsilbing = explode(',',$offidss);
	}
	$filter = ($preTally_user_ofid == 1) ? '1':'Sibling_id = '.$sibling_id;
	$GeneralObj->ViewDetails(' OF_Id, OF_Name', 'offices', $filter,' OF_Name ');
	$OF_Obj = $GeneralObj->DataArray;
	echo '<complete>';
	if (!empty($OF_Obj)) {
   
	    foreach ($OF_Obj as $rw) { $sel='';
	       	if (empty($extsilbing) && $rw->OF_Id == $preTally_user_ofid) {
	       		$sel='checked="1" selected="true"';
	       	} else if (in_array($rw->OF_Id,$extsilbing)) {
	       		$sel='checked="1" selected="true"';
	       	} else { $sel=''; }
	        echo '<option value="'.str_replace("&","&amp;",$rw->OF_Id).'"  '.$sel.' >'.$rw->OF_Name.'</option>';
	    }
	}
	echo '</complete>';
}
?>