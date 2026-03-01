<?php
/**
* Update or approve the bank entry or balance sheet entry from STS
* Track No, Bank Account No, Amount, date, approve/reject status are the inputs
* paid date, remark, reject reason and type (income/expense) are the extra parameters from the api call
* CHQ_Number, BS_BReff (reference remarks (mixed remarks with all details))
* Approve or reject the balance sheet entry based on the inputs
* Created By Bilin @ 08-05-2025
*/
header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");
//base config file loading
include_once("../_conf.php");
$BASEPATH = Settings::getPublic('site_root'); // simacco base path declaration

// find the Needed Class files includes
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
// object for class files
$BalSheetObj 	= new BalanceSheetClass();
$BkupObj     	= new BackupClass();
$GenObj     	= new GeneralClass();
$retMsg 		= '';
$retStatus 		= 0;
$retErrors 		= [];
// find the inputs from the api call
$allInput   	= file_get_contents('php://input'); 
$requestAll   	= ($allInput != "") ? json_decode($allInput, true) : [];
if( is_array($requestAll) && count($requestAll) <= 0) { //13-05-2025
   $allInput   	= $_POST;
   $requestAll  = json_decode(json_encode($allInput), true);
}

$track_no 		= (isset($requestAll['track_no'])) ? trim($requestAll['track_no']): '';
$emp_code		= (isset($requestAll['emp_code'])) ? trim($requestAll['emp_code']): '';
$acc_no			= (isset($requestAll['acc_no'])) ? trim($requestAll['acc_no']): '';
$amount 		= (isset($requestAll['amount'])) ? (float)$requestAll['amount']: 0;
$status 		= (isset($requestAll['status'])) ? (int)$requestAll['status']: 0; //3-reject 1-approve
$aprove_date 	= (isset($requestAll['payment_date'])) ? $requestAll['payment_date'] : date('Y-m-d');
$paid_date 		= (isset($requestAll['entry_date'])) ? $requestAll['entry_date']: '';
$remarks 		= (isset($requestAll['remarks'])) ? trim($requestAll['remarks']): '';
$reference 		= (isset($requestAll['reference'])) ? trim($requestAll['reference']): '';
$type			= (isset($requestAll['type'])) ? (int)$requestAll['type']: 0; //1-income 2-expense
$bs_id 			= 0; // balance sheet id
$track_id 		= 0; // Track table id
$emp_id 		= 0; // employee table id
$acc_id 		= 0; // bank account tabel id
$off_id 		= 0;
// check the required fields
if ($emp_code != "" && $amount != "" && $track_no != "" && $acc_no != "") {
	// find the api employee id from the user auth table - employee id
	$emp_id  	= $GenObj->getValue("users_auth", "US_Id", " WHERE US_EMPID='".$emp_code."' AND US_Status='1' ");
	// Find the track id from the track table - track number
	$track_id 	= $GenObj->getValue("tracks", "TR_Id", " WHERE TR_Track='".$track_no."'");
	if ($emp_id > 0) {
		// find the office id and office id based blocked date
		$off_id  		= $GenObj->getValue("users_auth", "OF_Id", " WHERE US_Id='".$emp_id."'");
		// find the bank account id - account number  : office id added 23-02-2026
		$acc_id 	= $GenObj->getValue("bank_accounts", "BA_Id", " WHERE BA_No='".$acc_no."' AND BA_Status='1' AND OF_Id='".$off_id."' ORDER BY BA_Id DESC");
		if ($acc_id <= 0) {
			$acc_id 	= $GenObj->getValue("bank_accounts", "BA_Id", " WHERE BA_No='".$acc_no."' AND BA_Status='1' ORDER BY BA_Id DESC");
		}
	} else {
		// find the bank account id - account number 
		$acc_id 	= $GenObj->getValue("bank_accounts", "BA_Id", " WHERE BA_No='".$acc_no."' AND BA_Status='1' ORDER BY BA_Id DESC");
	}	
	if ($emp_id > 0 && $track_id > 0 && $acc_id > 0) {
		
		$blockedDate 	= $BalSheetObj->bsEntryUpdateDate($off_id);
		if($blockedDate < $aprove_date) {

			// find the account entry based on the provided data.
			$extdata = $BalSheetObj->getBalanceSheetEntry(['track_id'=>$track_id, 'acc_id'=>$acc_id, 'amount'=>$amount, 'paid_date'=> $paid_date]);
			if ( !empty($extdata)) {

				$BalSheetObj->BS_Data = [
					'BS_Status'=>$status, 
					'BS_Date'=>$aprove_date, 
					'BS_MDate'=>date('Y-m-d H:i:s'), 
					'BS_BRemarks' => htmlspecialchars($remarks, ENT_QUOTES)
				];			
				if ($status == 1) { // approved

					if ($type == 1) {
						$BalSheetObj->BS_Data['BS_BReff'] =  htmlspecialchars($reference, ENT_QUOTES);
					}
				} else { // rejected
					$BalSheetObj->BS_Data['BS_RJReason'] =  htmlspecialchars($remarks, ENT_QUOTES);
				}
				// income or expense checking
				if ($type > 0 && $type != $extdata['MH_Type']) {

					$retErrors['type'] 	= 'Missmatch transaction type(Income/Expnse).';
					$retMsg 	= 'Missmatch Account Entry Type(Income/Expnse).';
				} else {
					// backup the existing data
					$BkupObj->backupDetails('BS_Id = '.$extdata['BS_Id'],$emp_id, 'balance_sheets_bkup','balance_sheets');

					// update the status and details
					$retMsg 	= $BalSheetObj->updateBalanceSheet($extdata['BS_Id']);
					$retErrors  = ['emp_id'=>$emp_id, 'track_id'=>$track_id, 'acc_id'=>$acc_id];
					$retStatus 	= 1;					
				}				
			} else {
				$retMsg 	= 'We dont find the account entry in Simacco '.$BalSheetObj->ermsg.'.';
			}
		} else {
			$retMsg 	= 'Account entry updation blocked.';
		}
	} else {// invalid inputs
		$retMsg 	= 'Invalid Inputs provided.';
		if ((int)$emp_id <= 0) {

			$retErrors['emp_code'] 	= 'Invalid Login.';
		}
		if ((int)$track_id <= 0) {
			$retErrors['track_no'] 	= 'Invalid Track Number.';
		}
		if ((int)$acc_id <= 0) {
			$retErrors['acc_no'] 	= 'Invalid Account Number.';
		}
	}	
} else { // missing required fields 
	$retMsg 	= 'Required Fields Missing.';
	if ($emp_code == "") {

		$retErrors['emp_code'] 	= 'Login Details required.';
	}
	if ($track_no == "") {
		$retErrors['track_no'] 	= 'Track Number required.';
	}
	if ($acc_no == '') {
		$retErrors['acc_no'] 	= 'Account Number required.';
	}
}

echo json_encode(['status'=>$retStatus, 'message'=>$retMsg, 'errors'=>$retErrors]);
?>