<?php
if(!$_SESSION) {session_start();}
$preTally_user_ofid 	= $_SESSION['preTally_user_ofid'];
/**
* Common Export page for different pages based on the flag id requested from the functions
* 1 - mapped ledger data list 
* 2 - mapped ledger and old item List
* 3 - item details with mapped ledger and item amount (not mapped or moved to new list)
* 4 - 
* 5 - 
* Created By Bilin At 26-11-2025
*/
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/ExportExcelClass.php");
// class object define
$ExcelObj 	= new ExportExcelClass();
//find the inputs from the request (get and post)
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$filterData = $_REQUEST['inpdata'];
// flag based class loading
if ($flag < 10) {
	// accounts settings related class
	include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
	$accObj	= new AccountsClass();
}
// common function for date range input generation from the inputed year, month and day params 27-11-2025
function daterangesetup($input, $day, $month, $year) {
	$input['date_from'] = "";
	$input['date_to'] 	= "";
	if ($day > 0 || $month > 0 || $year > 0 ) {
		$year 	= ($year <= 0) ? date('Y'):$year;
		$month	= ($month < 10) ? "0".$month:$month;
		$month 	= ($month <= 0 && $day > 0) ? date('m'):$month;
		$day 	= ($day < 10) ? "0".$day:$day;
		if ($day > 0) {
			$input['date_from'] = $year."-".$month."-".$day;
			$input['date_to'] 	= $year."-".$month."-".$day;
		}else if ($month > 0) {
			$input['date_from'] = $year."-".$month."-01";
			$input['date_to'] 	= date("Y-m-t",strtotime($year."-".$month."-01"));
		} else {
			$input['date_from'] = $year."-01-01";
			$input['date_to'] 	= $year."-12-31";
		}
	}

	return $input;
}


if ($flag == 1) { //mapped ledger based data or amount 
	$inparams 				= ['office_id'=>$preTally_user_ofid];
	$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
	$inparams['branch_id']	= (isset($filterData[1])) ? (int)$filterData[1] : 0;
	$inparams['track_no']	= (isset($filterData[2])) ? trim($filterData[2]) : "";
	$inparams['date']		= (isset($filterData[3]) && $filterData[3] != "") ? date('Y-m-d',strtotime($filterData[3])) : "";
	$month 					= (isset($filterData[4])) ? (int)$filterData[4] : 0;
	$year 					= (isset($filterData[5])) ? (int)$filterData[5] : 0;
	$day 					= (isset($filterData[6])) ? (int)$filterData[6] : 0;
	$inparams 				= daterangesetup($inparams, $day, $month, $year); // day , month, year based filter range setup	
	$inparams['amt']		= (isset($filterData[7])) ? (float)$filterData[7] : 0;
	$inparams['isexcel'] 	= 1;
	//find the details based on the parameters provided
	$accObj->listJournal($inparams);
	// excel export heading setting
	$headerArray 		= ["slno"=>"Slno", "date"=>"Date", "trackno"=>"Track No", "ledger"=>"Ledger Name", "branch"=>"Branch", "amount"=>"Amount"];
	$dataList 			= $accObj->data_list;
} else if ($flag == 2) { //mapped ledger and items 
	$inparams 				= ['office_id'=>$preTally_user_ofid];
	$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
	$inparams['type']		= (isset($filterData[1])) ? $filterData[1] : "";
	$inparams['status']		= (isset($filterData[2])) ? (string)$filterData[2] : "";
	$inparams['group_id']   = (isset($filterData[3]) && $filterData[3] > 0) ? (int)$filterData[3] : 0;
	$inparams['search_item']= (isset($filterData[4]) && $filterData[4] != "") ? trim($filterData[4]) : "";
	$inparams['item_shead'] = (isset($filterData[5]) && $filterData[5] > 0) ? (int)$filterData[5] : 0;
	$inparams['isexcel'] 	= 1;
	//find the details based on the parameters provided
	$accObj->listMapLedger($inparams);
	$dataList 				= $accObj->data_list;
	// excel export heading setting
	$headerArray 		= ["slno"=>"Slno", "type"=>"Group Type", "group"=>"Group Name", "ledger"=>"Ledger Name", "item"=>"Item Name", "headitem"=>"Item Head Name", "status"=>"Status"];
} else if ($flag == 3) { // items with map ledger and item amount list 27-11-2025
	$inparams 				= ['office_id'=>$preTally_user_ofid];
	$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
	$inparams['ledgers']	= (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";
	$inparams['date']		= (isset($filterData[2]) && $filterData[2] != "") ? date('Y-m-d',strtotime($filterData[2])) : "";
	$inparams['amt']		= (isset($filterData[3])) ? (float)$filterData[3] : 0;
	$month 					= (isset($filterData[4])) ? (int)$filterData[4] : 0;
	$year 					= (isset($filterData[5])) ? (int)$filterData[5] : 0;
	$day 					= (isset($filterData[6])) ? (int)$filterData[6] : 0;
	$inparams 				= daterangesetup($inparams, $day, $month, $year); // day , month, year based filter range setup	
	$inparams['isexcel'] 	= 1;
	//find the details based on the parameters provided
	$accObj->listItemJournal($inparams);
	$dataList 				= $accObj->data_list;
	// excel export heading setting
	$headerArray 		= ["slno"=>"Slno", "type"=>"Type", "item"=>"Item Name", "track"=>"Track No", "amount"=>"Amount", "amttype"=>"Amount Type", "date"=>"Date", "branchfrom"=>"Branch From", "branchto"=>"Branch To", "ledger"=>"Ledger Name"];
}else if ($flag == 4) { // bank or cash report list based on the search parameters 13-02-2026
	$inparams 					= [];
	$inparams['from_date']		= (isset($filterData[0]) && $filterData[0] != "") ? date("Y-m-d", strtotime($filterData[0])) : NULL;
	$inparams['to_date']		= (isset($filterData[1]) && $filterData[1] != "") ? date("Y-m-d", strtotime($filterData[1])) : NULL;
	$inparams['company_id']		= (isset($filterData[2])) ? (int)$filterData[2] : 0;
	$inparams['branch_id']		= (isset($filterData[3])) ? (int)$filterData[3] : 0;
	$inparams['bank_acc_id']	= (isset($filterData[4])) ? (int)$filterData[4] : 0;
	$inparams['cash_bank_type']	= (isset($filterData[5])) ? (int)$filterData[5] : 0;
	$inparams['isexcel'] 		= 1;

	$totaldata 			= $accObj->listBankCashRpt($inparams);
	$dataList 			= $accObj->data_list;
	$dataList[] 		= ['slno'=>($accObj->data_total+1), 'title'=>'Total', 'trackno'=>'', 'date_entry'=>'', 'income'=>$totaldata['total_income'], 'expense'=>$totaldata['total_expense'],'remarks'=>''];
	// excel export heading setting
	$headerArray 		= ["slno"=>"Slno", "title"=>"Description", "trackno"=>"Track No", "date_entry"=>"Date", "income"=>"Income", "expense"=>"Expense", "remarks"=>"Remarks"];
}else if ($flag == 5) { // profit and loss basic 19-02-2026
	$inparams 					= [];
	$inparams['from_date']		= (isset($filterData[0]) && $filterData[0] != "") ? date("Y-m-d", strtotime($filterData[0])) : NULL;
	$inparams['to_date']		= (isset($filterData[1]) && $filterData[1] != "") ? date("Y-m-d", strtotime($filterData[1])) : NULL;
	$inparams['company_id']		= (isset($filterData[2])) ? (int)$filterData[2] : 0;
	$inparams['branch_id']		= (isset($filterData[3])) ? (int)$filterData[3] : 0;
	$inparams['trans_type']		= (isset($filterData[4])) ? (int)$filterData[4] : 0;
	$inparams['isexcel'] 		= 1;


	
}



if (isset($headerArray))
echo $ExcelObj->createExcel($dataList, $headerArray);
exit;

?>