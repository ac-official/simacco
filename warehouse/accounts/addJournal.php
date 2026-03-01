<?php
/**
* Save or update the journal single or multiple based on the extra input flag
* flag 1 - multiple journal saved here all items and ledger already mapped input was balance sheet ids
* flag 2 - single balance sheet entry with full details one to many journal entry possible and update the journal entry single or group 
* flag 4 - update the balance sheet entry into no need to add journal (so these items not shown in the next list)
* flag 5 - delete single journal entry based on id (change the status)
* flag 6 - delete group journal entry based on parent id (change the status)
* flag 7 - old bank details transfer into bank re consider table or delete from re consider table
* Created By Bilin @ 06-08-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$sql 		= '';
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;

if ($UserACLObj->approve_ledger_amount == 0) { 
	
	$msg 		= 'You have no permission to do this action.';

} else if ($flag == 1) {
	// fetch the other inputs need to save the journal 
	$date_entry = (isset($_REQUEST['newdate']) && $_REQUEST['newdate'] != "") ? date('Y-m-d', strtotime($_REQUEST['newdate'])):'';
	$bs_ids 	= (isset($_REQUEST['bs_ids'])) ? explode(",",$_REQUEST['bs_ids']) : [];
	if (!empty($bs_ids)) {
		$inputs 	= ['bs_ids'=>$bs_ids, 'office_id'=>$preTally_user_ofid, 'user_id'=>$preTally_user_id, 'date_entry'=>$date_entry];
		// call save function 
		list($retstatus,$msg, $sql)	= $accObj->saveMapItem($inputs);
	}

} else if($flag == 2) {
	// save or update the journal entry 
	$msg = "Save data successfully....";
	$parent_id 		= (isset($_REQUEST['parent_id'])) ? (int)$_REQUEST['parent_id']:0;
	$bs_id 			= (isset($_REQUEST['balance_sheet_id'])) ? (int)$_REQUEST['balance_sheet_id']:0;
	$office_id 		= (isset($_REQUEST['office_id'])) ? (int)$_REQUEST['office_id']:0;
	$location_id 	= (isset($_REQUEST['location_id'])) ? (int)$_REQUEST['location_id']:0;
	$details 		= (isset($_REQUEST['confirm_entry']) && $_REQUEST['confirm_entry'] != '') ? json_decode($_REQUEST['confirm_entry'],true):[];
	$track_id 		= (isset($_REQUEST['track_id'])) ? (int)$_REQUEST['track_id']:0;
	$date_entry 	= (isset($_REQUEST['date_entry'])) ? $_REQUEST['date_entry']:date('Y-m-d');
	$total_amount 	= (isset($_REQUEST['total_amount'])) ? (float)$_REQUEST['total_amount']:0;
	$amt_type 		= (isset($_REQUEST['amt_type'])) ? (int)$_REQUEST['amt_type']:0;
	//$amt_type 		= (isset($_REQUEST['amt_type'])) ? (int)$_REQUEST['amt_type']:0;
	$ba_id 			= (isset($_REQUEST['ba_id'])) ? (int)$_REQUEST['ba_id']:0;
	$ie_type 		= (isset($_REQUEST['ie_type'])) ? (int)$_REQUEST['ie_type']:0;
	$inputs 	= ['user_office'=>$preTally_user_ofid, 'user_id'=>$preTally_user_id, 'parent_id'=>$parent_id, 'bs_id'=>$bs_id, 'office_id'=>$office_id, 'location_id'=>$location_id, 'track_id'=>$track_id, 'date_entry'=>$date_entry, 'amt_type'=>$amt_type, 'details'=>$details, 'total_amount'=>$total_amount, 'ba_id'=>$ba_id, 'ie_type'=>$ie_type];
	// call save/update function 
	list($retstatus,$msg, $sql)	= $accObj->updateJournal($inputs);

} else if($flag == 5 || $flag == 6) {
	// delete single or miltiple entries 
	$id 		= (isset($_REQUEST['id'])) ? (int)$_REQUEST['id']:0;
	$parent_id 	= (isset($_REQUEST['parent_id'])) ? (int)$_REQUEST['parent_id']:0;
	$bs_id 		= (isset($_REQUEST['bs_id'])) ? (int)$_REQUEST['bs_id']:0;
	if ($parent_id > 0) {

		list($retstatus,$msg, $sql)	= $accObj->deleteJournal($id, $parent_id, $bs_id);
	}

} else if ($flag == 4 || $flag == 8) {
	// balance sheet itemk no need to save in journal group of balance sheet ids as inputs	
	$bs_id 		= (isset($_REQUEST['bs_id'])) ? (int)$_REQUEST['bs_id']:0;
	$bs_ids 	= (isset($_REQUEST['bs_ids'])) ? explode(",",$_REQUEST['bs_ids']) : [$bs_id];
	if (!empty($bs_ids)) {
		$inputs 	= ['bs_ids'=>$bs_ids, 'user_id'=>$preTally_user_id];
		// call save function 
		$upstate  = ($flag == 4) ? 2 :0;
		list($retstatus,$msg, $sql)	= $accObj->notMovedItem($inputs,$upstate);
	}
} else if ($flag == 7) { //
	$msg 		= "Update details successfully....";
	$retstatus  = 1;
	// balance sheet itemk into bank reconsider list
	$bs_id 		= (isset($_REQUEST['bs_id'])) ? (int)$_REQUEST['bs_id']:0;
	$type 		= (isset($_REQUEST['type'])) ? (int)$_REQUEST['type']:0;
	$accObj->saveordeleteBnkREc($bs_id, $type, $preTally_user_id);
} else if($flag == 11) {
	// delete single or miltiple entries 
	$id 		= (isset($_REQUEST['id'])) ? (int)$_REQUEST['id']:0;
	list($retstatus,$msg, $sql)	= $accObj->deleteExtraData($id);
}


$sql = '';
echo json_encode(['message'=>$msg, 'status'=>$retstatus, 'sql'=>$sql]);
exit();
?>