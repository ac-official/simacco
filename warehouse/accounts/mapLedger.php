<?php
/**
* Map Ledger and items based on the office id of logined user
* Update status (change status, approve/reject)
* all above are of based on the flag inputed
* Created BY Bilin @ 17-07-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$edit_id 	= (isset($_REQUEST['eid'])) ? (int)$_REQUEST['eid']:0;

// find the existing record in the case of edit or status update
$extingData = ( $edit_id > 0 ) ? $accObj->getMapLedger($edit_id) : [];
$edit_id 	= (!empty($extingData)) ? $extingData['id'] : 0;

if ($UserACLObj->add_account_settings == 0 && $UserACLObj->approve_account_settings == 0) {
	
	$msg 		= 'You have no permission to do this action.';
} else if ( $flag == 0) {
	// map ledger and item
	//$office_id		= (isset($_REQUEST['office_id'])) ? (int)$_REQUEST['office_id']:0;
	$ledger_id		= (isset($_REQUEST['ledger_id'])) ? (int)$_REQUEST['ledger_id']:0;
	$subledger_id	= (isset($_REQUEST['subledger_id'])) ? (int)$_REQUEST['subledger_id']:0;
	$item_id		= (isset($_REQUEST['item_id'])) ? (int)$_REQUEST['item_id']:0;
	if ($subledger_id > 0) {
		$ledger_id 	= $subledger_id;
	}
	$verified_by= ($edit_id > 0) ? $extingData['verified_by']:0;
	$created_by = ($edit_id > 0) ? $extingData['created_by']:$preTally_user_id;
	$status 	= ($edit_id > 0) ? $extingData['status']:2;
	$created_at = ($edit_id > 0) ? $extingData['created_at']:date('Y-m-d H:i:s');
	$verified_at= ($edit_id > 0) ? $extingData['verified_at']:NULL;
	if ( $status != 2 && $UserACLObj->approve_account_settings == 0 ) {
		$status 	= 2;
		$created_by = $preTally_user_id;
	} else if ( $status != 0 && $status != 4 && $UserACLObj->approve_account_settings == 1 ) {
		$status 	= 1;
		$verified_at= date('Y-m-d H:i:s');
		$verified_by= $preTally_user_id;
	}
	$office_id		= ($edit_id > 0) ? $extingData['office_id'] : $preTally_user_ofid;
	
	if ($status == 0) {
		$msg 		= 'Edit Not Possible For Blocked Items.';
	} else {
		$retstatus 	= $accObj->saveMapLedger([ 'ledger_id'=>$ledger_id, 'office_id'=>$office_id, 'item_id'=>$item_id, 'created_at'=>$created_at, 'created_by'=>$created_by, 'verified_by'=>$verified_by, 'verified_at'=>$verified_at, 'status'=>$status ], $edit_id);
		$msg 		= $accObj->msg;
	}

} else if ( $flag == 1 && $UserACLObj->approve_account_settings == 1 ) {

	// update the Map ledger status or approve / reject the mapping
	$status		= (isset($_REQUEST['status'])) ? (int)$_REQUEST['status']:0;
	if ($extingData['status'] == $status) {

		$msg 		= 'Already Updated.';
	} else {
		$haveChild	= 0;
		if ($status == 1) {

			$haveChild	= $accObj->checkParentStatus($extingData['ledger_id'],2);
			$msg 		= ' Failed! Please Activate Ledger Name';
		} 

		if ($haveChild	== 0) {
			$accObj->updateMapLedgerStatus($status, $edit_id, $preTally_user_id, $extingData['status']);
			if ($extingData['status'] == 2) {
				$msg 	= ($status == 1) ? 'Ledger & Item Mapping Approved Successfully':'Ledger & Item Mapping Rejected Successfully';
			} else {
				$msg 	= 'Status Updated Successfully';
			}
			$retstatus 	= 1;	
		}
	}
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>
