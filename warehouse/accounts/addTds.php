<?php
/**
* Save TDS Rules
* Edit the TDS Rules name, percentages
* Update status or TDS Rules
* all above are of based on the flag inputed
* Created BY Bilin @ 03-09-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$edit_id 	= (isset($_REQUEST['eid'])) ? (int)$_REQUEST['eid']:0;
// get the existing data in the case of edit 
$extingData = ( $edit_id > 0 ) ? $accObj->getSRowData($edit_id,6) : [];
$edit_id 	= (!empty($extingData)) ? $extingData['id'] : 0;
$insert		= ($edit_id > 0) ? 0 : 1;

if ($UserACLObj->add_account_settings == 0 && $UserACLObj->approve_account_settings == 0) {	
	$msg 		= 'You have no permission to do this action.';
}else if ($edit_id > 0 && $flag == 0 && isset($extingData['start_date']) && $extingData['start_date'] > $_REQUEST['start_date']) {
	$msg 		= 'Not possible to update Rule into Old Date.';
} else if ( $flag == 0) {
	// save or update the TDS name
	$rule_name 	= (isset($_REQUEST['rule_name'])) ? trim(htmlspecialchars($_REQUEST['rule_name'], ENT_QUOTES)) : '';
	$percentage	= (isset($_REQUEST['percentage'])) ? (float)$_REQUEST['percentage']:0;
	$section	= (isset($_REQUEST['section'])) ? (string)$_REQUEST['section']:'';
	$code		= (isset($_REQUEST['code'])) ? (string)$_REQUEST['code']:'';
	$start_date	= (isset($_REQUEST['start_date'])) ? $_REQUEST['start_date']:date('Y-m-d');	
	$updated_by = ($edit_id > 0) ? $preTally_user_id:0;
	$created_by = ($edit_id > 0) ? $extingData['created_by']:$preTally_user_id;
	$status 	= ($edit_id > 0) ? $extingData['status']:1;
	$created_at = ($edit_id > 0) ? $extingData['created_at']:date('Y-m-d H:i:s');
	$updated_at = ($edit_id > 0) ? date('Y-m-d H:i:s'):NULL;
	if ($edit_id > 0 && isset($extingData['start_date']) && $start_date > $extingData['start_date']) {
		$insert = 1;
	}
	$inputs 	= [ 'rule_name'=>$rule_name, 'percentage'=>$percentage, 'section'=>$section,'code'=>$code, 'start_date'=>$start_date, 'created_at'=>$created_at, 'created_by'=>$created_by, 'updated_by'=>$updated_by, 'updated_at'=>$updated_at, 'status'=>$status ];
	if ($updated_at == NULL) {
		unset($inputs['updated_at']);
	}
	$retstatus 	= $accObj->saveTds($inputs, $edit_id,$insert);
	$msg 		= $accObj->msg;

} else if ( $flag == 1 && $UserACLObj->approve_account_settings == 1 && !empty($extingData)) {
	// update the TDS status or active or block
	$status		= (isset($_REQUEST['status'])) ? (int)$_REQUEST['status']:0;
	if ($extingData['status'] == $status) {

		$msg 		= 'Already Updated.';
	} else {		

		$accObj->updateStatusCustom($status, $edit_id, $preTally_user_id, 6);
		$msg 		= ($status == 1) ? 'TDS Rule Activate Successfully':'TDS Rule Blocked Successfully';
		$retstatus 	= 1;
	}
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>