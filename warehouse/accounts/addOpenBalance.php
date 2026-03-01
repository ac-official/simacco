<?php
/**
 * Save or update Opening Balance of all accounts
 * Created By Bilin @ 24-11-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$edit_id 	= (isset($_REQUEST['eid'])) ? (int)$_REQUEST['eid']:0;

if ( $UserACLObj->add_open_balance == 0 ) { 
	
	$msg 		= 'You have no permission to do this action.';
} else if ( $flag == 0) {

	$year 		= (isset($_REQUEST['year_date'])) ? trim($_REQUEST['year_date']):"";
	$inputs 	= [
		'company_id'	=> (isset($_REQUEST['company_id'])) ? (int)$_REQUEST['company_id']:0, 
		'branch_id'		=> (isset($_REQUEST['branch'])) ? (int)$_REQUEST['branch']:0, 
		'type'			=> (isset($_REQUEST['type'])) ? (int)$_REQUEST['type']:0, 
		'ledger_id'		=> (isset($_REQUEST['ledger_id'])) ? (int)$_REQUEST['ledger_id']:0, 
		'amount'		=> (isset($_REQUEST['amount'])) ? (float)$_REQUEST['amount']:0,
		'amt_type'		=> (isset($_REQUEST['amt_type'])) ? (int)$_REQUEST['amt_type']:1,
		'title'			=> (isset($_REQUEST['title'])) ? trim(htmlspecialchars($_REQUEST['title'], ENT_QUOTES)) : '', 
		'updated_at'	=> date('Y-m-d H:i:s'),
		'updated_by'	=> $preTally_user_id
	];	
	if ($year != "") {
		$yearary 				= explode('-',$year);
		$inputs['date_from']	= $yearary[0]."-04-01";
		$inputs['date_to']		= $yearary[1]."-03-31";
	}
	if ($edit_id <= 0) {
		$inputs['status']		= 2;
		$inputs['created_by']	= $preTally_user_id;
		$inputs['created_at']	= date('Y-m-d H:i:s');
	}

	// save or update the Ledger Data
	$retstatus 	= $accObj->saveOpeningData( $inputs, $edit_id );
	$msg 		= $accObj->msg;
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>