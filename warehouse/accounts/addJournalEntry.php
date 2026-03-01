<?php
/**
 * Save or update the Journal entry 
 * Created By Bilin @ 13-11-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$edit_id 	= (isset($_REQUEST['eid'])) ? (int)$_REQUEST['eid']:0;

if ( $flag == 0) {
	$inputs 	= [
		'company_id'	=> (isset($_REQUEST['company_id'])) ? (int)$_REQUEST['company_id']:0, 
		'ledger_dr'		=> (isset($_REQUEST['ledger_dr'])) ? (int)$_REQUEST['ledger_dr']:0, 
		'ledger_cr'		=> (isset($_REQUEST['ledger_cr'])) ? (int)$_REQUEST['ledger_cr']:0, 
		'date'			=> (isset($_REQUEST['date'])) ? $_REQUEST['date']:date('Y-m-d'), 
		'amount'		=> (isset($_REQUEST['amount'])) ? (float)$_REQUEST['amount']:0,
		'branch_id'		=> (isset($_REQUEST['branch'])) ? (int)$_REQUEST['branch']:0,
		'bank_dr'		=> (isset($_REQUEST['bank_dr'])) ? (int)$_REQUEST['bank_dr']:0, 
		'bank_cr'		=> (isset($_REQUEST['bank_cr'])) ? (int)$_REQUEST['bank_cr']:0, 
	];
	if ($edit_id <= 0) {
		$inputs['created_by']	= $preTally_user_id;
		$inputs['created_at']	= date('Y-m-d H:i:s');
	}
	$inputs['updated_by']	= $preTally_user_id;
	$inputs['updated_at']	= date('Y-m-d H:i:s');
	// save or update the Journal Entry
	$retstatus 	= $accObj->saveJournalEntry( $inputs, $edit_id );
	$msg 		= $accObj->msg;
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>