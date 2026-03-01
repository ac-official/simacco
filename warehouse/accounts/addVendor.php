<?php
/**
* Save Vendors
* Edit the Vendors name, address, contact info and unique number/gst no
* Update status or Vendors
* all above are of based on the flag inputed
* Created BY Bilin @ 03-09-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$retstatus 	= 0;
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$edit_id 	= (isset($_REQUEST['eid'])) ? (int)$_REQUEST['eid']:0;
// get the existing data in the case of edit 
$extingData = ( $edit_id > 0 ) ? $accObj->getSRowData($edit_id,5) : [];
$edit_id 	= (!empty($extingData)) ? $extingData['id'] : 0;

if ($UserACLObj->add_account_settings == 0 && $UserACLObj->approve_account_settings == 0) {	
	$msg 		= 'You have no permission to do this action.';
} else if ( $flag == 0) {
	// save or update the Vendor name
	$name 		= (isset($_REQUEST['name'])) ? trim(htmlspecialchars($_REQUEST['name'], ENT_QUOTES)) : '';
	$address 	= (isset($_REQUEST['address'])) ? trim(htmlspecialchars($_REQUEST['address'], ENT_QUOTES)) : '';
	$email 		= (isset($_REQUEST['email'])) ? trim(htmlspecialchars($_REQUEST['email'], ENT_QUOTES)) : '';
	$phone 		= (isset($_REQUEST['phone'])) ? trim(htmlspecialchars($_REQUEST['phone'], ENT_QUOTES)) : '';
	$country_id	= (isset($_REQUEST['country_id'])) ? (int)$_REQUEST['country_id']:0;	
	$type	= (isset($_REQUEST['type'])) ? (int)$_REQUEST['type']:0;	
	$gst_no 	= (isset($_REQUEST['gst_no'])) ? trim(htmlspecialchars($_REQUEST['gst_no'], ENT_QUOTES)) : '';
	$pan_no 	= (isset($_REQUEST['pan_no'])) ? trim(htmlspecialchars($_REQUEST['pan_no'], ENT_QUOTES)) : '';
	$updated_by = ($edit_id > 0) ? $preTally_user_id:0;
	$created_by = ($edit_id > 0) ? $extingData['created_by']:$preTally_user_id;
	$status 	= ($edit_id > 0) ? $extingData['status']:1;
	$created_at = ($edit_id > 0) ? $extingData['created_at']:date('Y-m-d H:i:s'); 
	$updated_at = ($edit_id > 0) ? date('Y-m-d H:i:s'):NULL;
	$inputs 	= [ 'name'=>$name, 'address'=>$address, 'email'=>$email,'phone'=>$phone, 'country_id'=>$country_id, 'gst_no'=>$gst_no, 'pan_no'=>$pan_no, 'created_at'=>$created_at, 'created_by'=>$created_by, 'updated_by'=>$updated_by, 'updated_at'=>$updated_at, 'status'=>$status, 'type'=>$type ];
	if ($updated_at == NULL) {
		unset($inputs['updated_at']);
	}
	$retstatus 	= $accObj->saveVendor($inputs, $edit_id);
	$msg 		= $accObj->msg;

} else if ( $flag == 1 && $UserACLObj->approve_account_settings == 1 && !empty($extingData)) {
	// update the TDS status or active or block
	$status		= (isset($_REQUEST['status'])) ? (int)$_REQUEST['status']:0;
	if ($extingData['status'] == $status) {

		$msg 		= 'Already Updated.';
	} else {		

		$accObj->updateStatusCustom($status, $edit_id, $preTally_user_id, 5);
		$msg 		= ($status == 1) ? 'Vendor Activate Successfully':'Vendor Blocked Successfully';
		$retstatus 	= 1;
	}
}

echo json_encode(['message'=>$msg, 'status'=>$retstatus]);
exit();
?>