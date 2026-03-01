<?php
/**
* Created By Bilin At 27-06-2025
* This is used for return all json type data for using combo box or other filters
*/

// base  inputs for specify the combo box
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$results 	= [];
$retstatus 	= 0;
if ($flag == 1) { // break Time Types listing

	// break type base class declaration
	include_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
	$btObj		= new BreakTimeClass();

	// list break types
	$results 	= $btObj->getAllBreakTypes('1');
	$retstatus 	= 1;

} else if ($flag == 2 || $flag ==3 || $flag ==4 || $flag ==6 || $flag ==7) {
	// accounts settings related class
	include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
	$accObj		= new AccountsClass();
	$parent_id  = (isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != "") ? (string)$_REQUEST['parent_id']:"";
	$type  		= (isset($_REQUEST['type'])) ? trim($_REQUEST['type']):"";
	$status  	= (isset($_REQUEST['status'])) ? trim($_REQUEST['status']):"1";
	$group_id 	= (isset($_REQUEST['group_id'])) ? (int)$_REQUEST['group_id']:0;
	$ledger_id 	= (isset($_REQUEST['ledger_id'])) ? (int)$_REQUEST['ledger_id']:0;
	
	if ($flag == 2) {
		$results 	= $accObj->ListGroupTypes();
	} else if ($flag == 3) {
		$results 	= $accObj->ListGroups(['status'=>$status, 'type'=>$type, 'parent_id'=>$parent_id]);
	} else if ($flag == 4) {
		$results 	= $accObj->listLedger(['status'=>$status, 'parent_id'=>$parent_id, 'group_id'=>$group_id, 'office_id'=>$preTally_user_ofid, 'ledger_id'=>$ledger_id ]);
	} else if ($flag == 6) {
		$results 	= $accObj->listLedgerCbo(['status'=>$status, 'parent_id'=>$parent_id, 'group_id'=>$group_id, 'office_id'=>$preTally_user_ofid ]);
	}else if ($flag == 7) {
		$results 	= $accObj->listGroupsCbo(['status'=>$status, 'type'=>$type, 'parent_id'=>$parent_id ]);
	}

	$retstatus 	= (!empty($results)) ? 1: 0;
} else if ($flag == 8) {
	// accounts settings related class
	include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
	$accObj		= new AccountsClass();
	$trackno  	= (isset($_REQUEST['trackno'])) ? trim($_REQUEST['trackno']):"";

	$table 		= " balance_sheets as bl INNER JOIN items as itm ON (bl.IT_Id =itm.IT_Id) INNER JOIN tracks as trk ON (trk.TR_Id=bl.TR_Id)";
	$resultsdt 	= $accObj->getCustomField($table, " bl.LC_Id ", " WHERE itm.MH_Type = 1 AND itm.IT_Business = 1 AND trk.TR_Track = '".$trackno."' ORDER BY bl.LC_Id LIMIT 0, 1");

	$retstatus 	= (!empty($resultsdt[0])) ? 1: 0;
	$results 	= (!empty($resultsdt[0])) ? $resultsdt[0]->LC_Id:0;
} else {




}


echo json_encode(['status'=>$retstatus, 'data'=>$results]);
?>