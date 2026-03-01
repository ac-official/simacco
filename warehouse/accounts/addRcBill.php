<?php
/**
* Save or update recurring Bills
* Created BY Bilin @ 24-09-2025
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
	// save or update start..
	$day_id 	= (isset($_REQUEST['day_id'])) ? (int)$_REQUEST['day_id']:0;
	$bill_id 	= (isset($_REQUEST['bill_id'])) ? (int)$_REQUEST['bill_id']:0; 
	$oldbill_id = (isset($_REQUEST['old_bill_id']) && $_REQUEST['old_bill_id'] > 0) ? (int)$_REQUEST['old_bill_id']:$bill_id; 
	$last_billid=(isset($_REQUEST['last_bill_id']) && $_REQUEST['last_bill_id'] > 0) ? (int)$_REQUEST['last_bill_id']:$bill_id;
	if ($bill_id != $oldbill_id && $oldbill_id > 0 ) {
		$last_billid= $bill_id;
	}
	// find the next billing date 
	if (date('d') > $day_id) {
		$daynext 	= ($day_id < 10 ) ? '0'.$day_id: $day_id;
		$nexttotime = strtotime("+1 month", strtotime(date('Y-m-').'01'));
		$next_date	= date("Y-m-t", $nexttotime);
		$next_dates	= date("Y-m-", $nexttotime).$daynext;
		$next_date  = ($next_date > $next_dates) ? $next_dates : $next_date;
	} else {
		$daynext 	=  (date('t') < $day_id) ? date('t'): $day_id;
		$daynext 	= ($daynext < 10 ) ? '0'.$daynext: $daynext;
		$next_date 	= date('Y-m-').$daynext;
	}
	$inputs 		= [
		'bill_id'		=> $bill_id, 
		'last_bill_id'	=> $last_billid,
		'status' 		=> (isset($_REQUEST['status'])) ? (int)$_REQUEST['status']:1, 
		'day_id'		=> $day_id,
		'next_date'		=> $next_date
	];
	$inputs['updated_by']	= $preTally_user_id;
	$inputs['updated_at']	= date('Y-m-d H:i:s');
	// save or update the Recurring bills
	$retstatus 	= $accObj->saveRcBills( $inputs, $edit_id );
	$msg 		= $accObj->msg;
} else if ( $flag == 2) {
	// delete recurring bills
	if ($accObj->updateStatusCustom(3, $edit_id, $preTally_user_id, 8)) {

		$msg 		='Successfully Removed Bill From Recurring List';
		$retstatus 	= 1;
	}
}
echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>