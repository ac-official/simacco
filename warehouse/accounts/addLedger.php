<?php
/**
* Save Ledger and sub ledger name
* Edit the Ledger name and Ledger group name
* Update status or verify the Ledger / sub Ledger
* all above are of based on the flag inputed
* Created BY Bilin @ 15-07-2025
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
$extingData = ( $edit_id > 0 ) ? $accObj->getLedger($edit_id) : [];
$edit_id 	= (!empty($extingData)) ? $extingData['id'] : 0;


if ($UserACLObj->add_account_settings == 0 && $UserACLObj->approve_account_settings == 0) {
	
	$msg 		= 'You have no permission to do this action.';
} else if ( $flag == 0) {
	// save or update the Ledger name
	$group_id	= (isset($_REQUEST['group_id'])) ? (int)$_REQUEST['group_id']:0;
	$group_id	= (isset($_REQUEST['subgroup_id']) && $_REQUEST['subgroup_id'] > 0) ? (int)$_REQUEST['subgroup_id']:$group_id;
	$office_id	= (isset($_REQUEST['office_id'])) ? (int)$_REQUEST['office_id']:0;
	$vendor_id	= (isset($_REQUEST['vendor_id'])) ? (int)$_REQUEST['vendor_id']:0;
	$is_office	= (isset($_REQUEST['is_office'])) ? (int)$_REQUEST['is_office']:0;	
	$is_return	= (isset($_REQUEST['is_return'])) ? (int)$_REQUEST['is_return']:0;	
	$trans_type	= (isset($_REQUEST['trans_type'])) ? (int)$_REQUEST['trans_type']:0;	
	$title 		= (isset($_REQUEST['title'])) ? trim(htmlspecialchars($_REQUEST['title'], ENT_QUOTES)) : '';
	$remarks 	= (isset($_REQUEST['description'])) ? trim(htmlspecialchars($_REQUEST['description'], ENT_QUOTES)) : '';
	$parent_id	= (isset($_REQUEST['parent_id'])) ? (int)$_REQUEST['parent_id']:0;
	$is_contra	= (isset($_REQUEST['is_contra'])) ? (int)$_REQUEST['is_contra']:0;	
	$less_id	= (isset($_REQUEST['less_id'])) ? (int)$_REQUEST['less_id']:0;	
	$plus_id	= (isset($_REQUEST['plus_id'])) ? (int)$_REQUEST['plus_id']:0;	
	$is_same	= (isset($_REQUEST['is_same_side'])) ? (int)$_REQUEST['is_same_side']:0;	
	$is_internal= (isset($_REQUEST['is_internal'])) ? (int)$_REQUEST['is_internal']:0;	
	$is_job_type= (isset($_REQUEST['is_job_type'])) ? (int)$_REQUEST['is_job_type']:0;	
	$less_type  = 0;
	$plus_type  = 0;
	if ($is_contra == 1 && $plus_id > 0) {
		$contratypes	= $accObj->getIdNameAry([$less_id,$plus_id], 5);
		$less_type  	= $contratypes[$less_id];
		$plus_type  	= $contratypes[$plus_id];
	}
	if ($parent_id == $edit_id) {
		$parent_id = 0;
	}
	$verified_by= ($edit_id > 0) ? $extingData['verified_by']:0;
	$created_by = ($edit_id > 0) ? $extingData['created_by']:$preTally_user_id;
	$status 	= ($edit_id > 0) ? $extingData['status']:2;
	$created_at = ($edit_id > 0) ? $extingData['created_at']:date('Y-m-d H:i:s');
	$verified_at= ($edit_id > 0) ? $extingData['verified_at']:NULL;
	$open_balance 	= 0;
	$open_bal_type 	= 0;
	if ( $status != 2 && $UserACLObj->approve_account_settings == 0 ) {
		$status 	= 2;
		$created_by = $preTally_user_id;
	} else if ( $status != 0 && $status != 4 && $UserACLObj->approve_account_settings == 1 ) {
		$status 	= 1;
		$verified_at= date('Y-m-d H:i:s');
		$verified_by= $preTally_user_id;
	}
	if ($edit_id > 0) {
		$office_id		= $extingData['office_id'];
		if ($is_office == 1 && $status == 2) {
			$office_id	= $preTally_user_ofid;
		}
	}else if ($is_office == 1) {
		$office_id		= $preTally_user_ofid;
	} 
	if ($status == 0) {
		$msg 		= 'Edit Not Possible For Blocked Items.';
	} else {
		$retstatus 	= $accObj->saveLedger([ 'group_id'=>$group_id, 'office_id'=>$office_id, 'title'=>$title,'description'=>$remarks, 'parent_id'=>$parent_id, 'created_at'=>$created_at, 'created_by'=>$created_by, 'verified_by'=>$verified_by, 'verified_at'=>$verified_at, 'status'=>$status, 'open_balance'=>$open_balance, 'open_bal_type'=>$open_bal_type, 'vendor_id'=>$vendor_id, 'is_return'=>$is_return, 'trans_type'=>$trans_type, 'is_contra'=>$is_contra, 'less_id'=>$less_id, 'plus_id'=>$plus_id, 'less_type'=>$less_type, 'plus_type'=>$plus_type, 'is_same_side'=>$is_same, 'is_internal'=>$is_internal, 'is_job_type'=>$is_job_type ], $edit_id);
		$msg 		= $accObj->msg;
	}

} else if ( $flag == 1 && $UserACLObj->approve_account_settings == 1) {

	// update the ledger status or approve or reject the Ledger
	$status		= (isset($_REQUEST['status'])) ? (int)$_REQUEST['status']:0;
	if ($extingData['status'] == $status) {

		$msg 		= 'Already Updated.';
	} else {
		$haveChild	= 0;
		if ($status == 0) {				
			if ($extingData['parent_id'] == 0) {
				$haveChild	= $accObj->checkSubItemStauts($edit_id,3);				
				$msg 		= ' Failed! Please Block Sub Ledger';
			} 
			if ($haveChild == 0) {
				$haveChild	= $accObj->checkSubItemStauts($edit_id,4);
				$msg 		= ' Failed! Please Block Mapped Items';
			}
		} else if ($status == 1) {
			if ($extingData['parent_id'] == 0) {
				$haveChild	= $accObj->checkParentStatus($extingData['group_id'],1);
				$msg 		= ' Failed! Please Activate Group Name';
			}else {
				$haveChild	= $accObj->checkParentStatus($extingData['parent_id'],2);
				$msg 		= ' Failed! Please Activate Parent Ledger';
			}
		}
		if ($haveChild	== 0) {
			$accObj->updateLedgerStatus($status, $edit_id, $preTally_user_id, $extingData['status']);
			if ($extingData['status'] == 2) {
				$msg 	= ($status == 1) ? 'Ledger Approved Successfully':'Ledger Rejected Successfully';
			} else {
				$msg 	= 'Ledger Status Updated Successfully';
			}
			$retstatus 	= 1;
		}	
	}
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>