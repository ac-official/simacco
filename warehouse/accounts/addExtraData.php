<?php
/**
 * Save or update extra data uploaded and manual entry via form
 * Created By Bilin @ 12-01-2026 
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
		'company_id'	=> (isset($_REQUEST['company_id'])) ? (int)$_REQUEST['company_id']:0, 
		'branch_id'		=> (isset($_REQUEST['branch'])) ? (int)$_REQUEST['branch']:0, 
		'ledger_id'		=> (isset($_REQUEST['ledger_id'])) ? (int)$_REQUEST['ledger_id']:0, 
		'job_date'		=> (isset($_REQUEST['job_date'])) ? $_REQUEST['job_date']:date('Y-m-d'), 
		'amount'		=> (isset($_REQUEST['amount'])) ? (float)$_REQUEST['amount']:0,
		'type'			=> (isset($_REQUEST['type'])) ? trim($_REQUEST['type']):"",
		'addtype'		=> (isset($_REQUEST['addtype'])) ? (int)$_REQUEST['addtype']:0,
		'title'			=> (isset($_REQUEST['title'])) ? trim(htmlspecialchars($_REQUEST['title'], ENT_QUOTES)) : '',
		'branch_name' 	=> (isset($_REQUEST['branch_name'])) ? trim(htmlspecialchars($_REQUEST['branch_name'], ENT_QUOTES)):''
	];
	if ($edit_id <= 0) {
		$inputs['created_by']	= $preTally_user_id;
		$inputs['created_at']	= date('Y-m-d H:i:s');
	} else {
		$inputs['updated_by']	= $preTally_user_id;
		$inputs['updated_at']	= date('Y-m-d H:i:s');
	}

	// save or update Data
	$retstatus 	= $accObj->saveExtraData( $inputs, $edit_id );
	$msg 		= $accObj->msg;
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>