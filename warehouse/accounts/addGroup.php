<?php
/**
* Save Group and sub Group name
* Edit the Group name and Sub group name
* Update status or verify the group / sub group
* all above are of based on the flag inputed
* Created BY Bilin @ 11-07-2025
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
$extingData = ( $edit_id > 0 ) ? $accObj->getGroup($edit_id) : [];
$edit_id 	= (!empty($extingData)) ? $extingData['id'] : 0;

if ($UserACLObj->add_account_settings == 0 && $UserACLObj->approve_account_settings == 0) {
	$msg 		= 'You have no permission to do this action.';

}else if ( $flag == 0) {
	// save or update the group name
	$type 		= (isset($_REQUEST['type'])) ? trim($_REQUEST['type']):'';
	$title 		= (isset($_REQUEST['title'])) ? trim(htmlspecialchars($_REQUEST['title'], ENT_QUOTES)) : '';
	$remarks 	= (isset($_REQUEST['description'])) ? trim(htmlspecialchars($_REQUEST['description'], ENT_QUOTES)) : '';
	$parent_id	= (isset($_REQUEST['parent_id'])) ? (int)$_REQUEST['parent_id']:0;
	$code 		= preg_replace('/\s+/', '_', strtolower($title));
	$code 		= preg_replace('/[^a-zA-Z0-9_]/', '', $code);
	if ($parent_id == $edit_id) {
		$parent_id = 0;
	}
	$verified_by= ($edit_id > 0) ? $extingData['verified_by']:0;
	$created_by = ($edit_id > 0) ? $extingData['created_by']:$preTally_user_id;
	$code 		= ($edit_id > 0) ? $extingData['code']:$code;
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
	if ($status == 0) {
		$msg 		= 'Edit Not Possible For Blocked Items.';
	} else {
		$retstatus 	= $accObj->saveGroup([ 'type'=>$type, 'title'=>$title,'description'=>$remarks, 'parent_id'=>$parent_id, 'created_at'=>$created_at, 'created_by'=>$created_by, 'verified_by'=>$verified_by, 'verified_at'=>$verified_at, 'status'=>$status, 'code'=>$code ], $edit_id);
		$msg 		= $accObj->msg;
	}
} else if ($flag == 1 && $UserACLObj->approve_account_settings == 1) {
	// update the group status or approve or reject the group
	$status		= (isset($_REQUEST['status'])) ? (int)$_REQUEST['status']:0;
	if ($extingData['status'] == $status) {

		$msg 		= 'Already Updated.';
	} else {

		$haveChild	= 0;
		if ($status == 0) {	
			if ($extingData['parent_id'] == 0) {
				$haveChild	= $accObj->checkSubItemStauts($edit_id,1);
				$msg 	= ' Failed! Please Block Sub Group';
			} else  {
				$haveChild	= $accObj->checkSubItemStauts($edit_id,2);
				$msg 	= ' Failed! Please Block Ledger Name';
			}
		} else if ($status == 1 && $extingData['parent_id'] == 0) {
			$haveChild	= $accObj->checkParentStatus($extingData['parent_id'],1);
			$msg 		= ' Failed! Please Activate Parent Group';
			
		}
		if ($haveChild	== 0) {
			$accObj->updateGroupStatus($status, $edit_id, $preTally_user_id, $extingData['status']);
			if ($extingData['status'] == 2) {
				$msg 	= ($status == 1) ? 'Group Approved Successfully':'Group Name Rejected Successfully';
			} else {
				$msg 	= 'Group Status Updated Successfully';
			}
			$retstatus 	= 1;			
		} 
	}

}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>