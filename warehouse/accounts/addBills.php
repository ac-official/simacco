<?php
/**
* Save or update Bills
* Created BY Bilin @ 11-09-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$edit_id 	= (isset($_REQUEST['eid'])) ? (int)$_REQUEST['eid']:0;

if ($UserACLObj->manage_all_bills == 0 && $UserACLObj->manage_bills == 0) {
	
	$msg 		= 'You have no permission to do this action.';
} else if ( $flag == 0) {
	$total_amount 	= (isset($_REQUEST['total_amount'])) ? (float)$_REQUEST['total_amount']:0;
	$tax_amount 	= 0;
	$tax_amount2 	= 0;
	$bill_amount 	= 0; 
	$tds_percent 	= (isset($_REQUEST['tds_percent'])) ? (float)$_REQUEST['tds_percent']:0;
	$tax_percent 	= (isset($_REQUEST['tax_percent'])) ? (float)$_REQUEST['tax_percent']:0;
	$tax_no 		= (isset($_REQUEST['tax_no'])) ? (int)$_REQUEST['tax_no']:0;
	if ($tax_no > 0 && $tax_percent > 0 && $total_amount > 0) { // tax available
		$tbill_amount 	= $total_amount * (100/($tax_percent+100));
		$tax_amount 	= ($tax_no == 2) ? round(($total_amount - $tbill_amount)/2, 3) : round(($total_amount - $tbill_amount), 3);
		$tax_amount2 	= ($tax_no == 2) ? round(($total_amount - $tbill_amount)/2, 3) : 0;
	} else {
		$tbill_amount 	= $total_amount;
	}
	$bill_amount		= ($tds_percent > 0 && $tbill_amount > 0) ? ($tbill_amount * (100/($tds_percent+100))) : $tbill_amount;
	$tds_amount			= round(($tbill_amount - $bill_amount),3);
	$bill_amount		= round($bill_amount,3);
	$total_amount		= round($total_amount,3);

	$inputs 		= [
		'company_id'	=> (isset($_REQUEST['company_id'])) ? (int)$_REQUEST['company_id']:0, 
		'vendor_id'		=> (isset($_REQUEST['vendor_id'])) ? (int)$_REQUEST['vendor_id']:0, 
		'bill_name'		=> (isset($_REQUEST['bill_name'])) ? trim(htmlspecialchars($_REQUEST['bill_name'], ENT_QUOTES)) : '', 
		'bill_date'		=> (isset($_REQUEST['bill_date'])) ? $_REQUEST['bill_date']:date('Y-m-d'), 
		'total_amount'	=> $total_amount,
		'bill_amount'	=> $bill_amount,
		'bill_type'		=> (isset($_REQUEST['bill_type'])) ? $_REQUEST['bill_type']:1,
		'tds_amount' 	=> round($tds_amount, 3),
		'tax_amount' 	=> round($tax_amount, 3),
		'tax_amount2' 	=> round($tax_amount2, 3),
		'tds_percent'	=> $tds_percent,
		'tax_percent'	=> $tax_percent,
		'branch_id'		=> (isset($_REQUEST['branch'])) ? (int)$_REQUEST['branch']:0,
		'tds_id' 		=> (isset($_REQUEST['tds_id'])) ? (int)$_REQUEST['tds_id']:0,
		'tax_id' 		=> (isset($_REQUEST['tax_id'])) ? (int)$_REQUEST['tax_id']:0
	];
	if (isset($_REQUEST['due_date']) && $_REQUEST['due_date'] != "") {
		$inputs['due_date'] = ($_REQUEST['due_date'] > $inputs['bill_date']) ? $_REQUEST['due_date'] :$inputs['bill_date'];
	}
	if ($edit_id <= 0) {
		$inputs['created_by']	= $preTally_user_id;
		$inputs['created_at']	= date('Y-m-d H:i:s');
	}
	$inputs['updated_by']	= $preTally_user_id;
	$inputs['updated_at']	= date('Y-m-d H:i:s');
	// save or update the bills
	$retstatus 	= $accObj->saveBills( $inputs, $edit_id );
	$msg 		= $accObj->msg;
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>