<?php
/**
 * Save or update the Direct ledger entry 
 * Created By Bilin @ 14-11-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$edit_id 	= (isset($_REQUEST['eid'])) ? (int)$_REQUEST['eid']:0;

if ( $UserACLObj->approve_ledger_amount == 0 ) { 
	
	$msg 	= 'You have no permission to do this action.';
} else if ( $flag == 0) {

	$inputs 	= [
		'office_id'		=> (isset($_REQUEST['office_id'])) ? (int)$_REQUEST['office_id']:0, 
		'company_id'	=> (isset($_REQUEST['company_id'])) ? (int)$_REQUEST['company_id']:0, 
		'location_id'	=> (isset($_REQUEST['location'])) ? (int)$_REQUEST['location']:0,
		'branch_id'		=> (isset($_REQUEST['branch'])) ? (int)$_REQUEST['branch']:0, 
		'ledger_id'		=> (isset($_REQUEST['ledger_id'])) ? (int)$_REQUEST['ledger_id']:0, 
		'vendor_id'		=> (isset($_REQUEST['vendor_id'])) ? (int)$_REQUEST['vendor_id']:0, 
		'date_entry'	=> (isset($_REQUEST['date_entry'])) ? $_REQUEST['date_entry']:date('Y-m-d'), 
		'amount'		=> (isset($_REQUEST['amount'])) ? (float)$_REQUEST['amount']:0,
		'converted'		=> (isset($_REQUEST['converted'])) ? (float)$_REQUEST['converted']:0,
		'amt_type'		=> (isset($_REQUEST['amt_type'])) ? (int)$_REQUEST['amt_type']:0,
		'ba_id'			=> (isset($_REQUEST['bankaccount'])) ? (int)$_REQUEST['bankaccount']:0,
		'remarks'		=> (isset($_REQUEST['remarks'])) ? trim(htmlspecialchars($_REQUEST['remarks'], ENT_QUOTES)) : '', 
		'trackno'		=> (isset($_REQUEST['trackno'])) ? trim(htmlspecialchars($_REQUEST['trackno'], ENT_QUOTES)) : '', 
		'balance_sheet_id' => 0,
		'track_id'		=> 0,
		'add_type'		=> 1,
		'status'		=> 1,
		'parent_id'		=> (isset($_REQUEST['parent_id'])) ? (int)$_REQUEST['parent_id']:0, 
		'ba_id_debit'	=> (isset($_REQUEST['bankaccountdr'])) ? (int)$_REQUEST['bankaccountdr']:0,
		'ie_type'		=> (isset($_REQUEST['ie_type'])) ? trim($_REQUEST['ie_type']):"add",
	];
	if ($edit_id <= 0) {
		$inputs['created_by']	= $preTally_user_id;
		$inputs['created_at']	= date('Y-m-d H:i:s');
		$inputs['is_edited']	= 0;
	} else {
		$inputs['updated_by']	= $preTally_user_id;
		$inputs['updated_at']	= date('Y-m-d H:i:s');
	}

	// save or update the Ledger Data
	$retstatus 	= $accObj->saveLedgerData( $inputs, $edit_id );
	$msg 		= $accObj->msg;
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>