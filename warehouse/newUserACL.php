<?php
/**
 * Save the Staff/user based special acl assignments
 * Created BY Bilin @ 07-07-2025 
*/
// declare the acl class objects
include_once($BASEPATH . "preTallyClass/ACLClass.php");
$ACLObj = new ACLClass();

// if save or uypdate case set the edit_id
$edit_id 	= (isset($_REQUEST['aclid'])) ? (int)$_REQUEST['aclid']:0;
$user_id 	= (isset($_REQUEST['ua_user_id'])) ? (int)$_REQUEST['ua_user_id']:0;
$inputs 	= [
	'us_id' 					=> $user_id,
	'add_break_time' 			=> (($_REQUEST['add_break_time'] == 1) ? 1:0),
	'view_break_time' 			=> (($_REQUEST['view_break_time'] == 1) ? 1:0),
	'approve_late_entry' 		=> (($_REQUEST['approve_late_entry'] == 1) ? 1:0),
	'view_late_entry' 			=> (($_REQUEST['view_late_entry'] == 1) ? 1:0),
	'add_clear_attendance' 		=> (($_REQUEST['add_clear_attendance'] == 1) ? 1:0),
	'view_clear_attencance' 	=> (($_REQUEST['view_clear_attencance'] == 1) ? 1:0),
	'view_attendance_detail' 	=> (($_REQUEST['view_attendance_detail'] == 1) ? 1:0),
	'view_accounts_report' 		=> (($_REQUEST['view_accounts_report'] == 1) ? 1:0),
	'view_master_report' 		=> (($_REQUEST['view_master_report'] == 1) ? 1:0),
	'view_account_settings' 	=> (($_REQUEST['view_account_settings'] == 1) ? 1:0),
	'add_account_settings' 		=> (($_REQUEST['add_account_settings'] == 1) ? 1:0),
	'approve_account_settings' 	=> (($_REQUEST['approve_account_settings'] == 1) ? 1:0),
	'approve_ledger_amount' 	=> (($_REQUEST['approve_ledger_amount'] == 1) ? 1:0),
	'manage_bills' 				=> (($_REQUEST['manage_bills'] == 1) ? 1:0),
	'manage_all_bills' 			=> (($_REQUEST['manage_all_bills'] == 1) ? 1:0),
	'approve_grace_time' 		=> (($_REQUEST['approve_grace_time'] == 1) ? 1:0),
	'close_others_tracks' 		=> (($_REQUEST['close_others_tracks'] == 1) ? 1:0),
	'view_all_company' 			=> (($_REQUEST['view_all_company'] == 1) ? 1:0),
	'bank_reconciliation' 		=> (($_REQUEST['bank_reconciliation'] == 1) ? 1:0),
	'add_open_balance' 			=> (($_REQUEST['add_open_balance'] == 1) ? 1:0),
	'updated_by'				=> $preTally_user_id,
	'updated_at'				=> date('Y-m-d H:i:s')
];
// find the existing data based on the user id inputed
$exid 		= $ACLObj->checkUserAcl($user_id);
if ($exid > 0 && $edit_id != $exid) {

	echo 'ACL already Exists!';
} else {
	if ($exid <= 0) {
		$inputs['created_by'] = $preTally_user_id;
		$inputs['created_at'] = date('Y-m-d H:i:s');
	}

	$id 	= $ACLObj->saveUserACL($exid, $inputs);
	if ($id > 0) {
		echo 'ACL Successfully Assigned to User.';
	} else {
		echo 'Sorry, Failed to Assign ACL to the User.';
	}
}
?>