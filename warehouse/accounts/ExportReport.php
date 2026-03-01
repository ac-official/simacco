<?php
/**
* Cash report (cash transaction summery reports) for accounts teams
* 
* Created By Bilin At 10-06-2025  Last Updated At 20-11-2025
*/
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/ExportExcelClass.php");
require_once($BASEPATH . "preTallyClass/AccountRptClass.php");
// class object define
$AccRptObj 	= new AccountRptClass();
$ExcelObj 	= new ExportExcelClass();

$inparams 			= ['mh_type'=>0, 'export'=>'yes'];
$inpdata 			= $_REQUEST['inpdata'];
// find the other request parameters
$inparams['of_id'] 	= (isset($inpdata['OFID']) && $inpdata['OFID'] > 0) ? $inpdata['OFID']:0;
$inparams['lc_id'] 	= (isset($inpdata['LC_Id']) && $inpdata['LC_Id'] > 0) ? $inpdata['LC_Id']:0;
$inparams['branch']	= (isset($inpdata['Branch']) && $inpdata['Branch'] > 0) ? $inpdata['Branch']:0;
$inparams['bank_id']= (isset($inpdata['Bank']) && $inpdata['Bank'] > 0) ? $inpdata['Bank']:0;
$inparams['type']	= (isset($inpdata['type'])) ? $inpdata['type']:"cash";

// filter inputs processing
$filterData 		= (isset($inpdata['filter'])) ? explode(",",$inpdata['filter']) : [];
if (isset($filterData[0]) ) { // income, expense, internal 
	$mytypary 		= explode('-',$filterData[0]);
	$inparams['mh_type']		= $mytypary[0];
	if (isset($mytypary[1]) && $mytypary[1] > 0) {
		// internal only(2) expect internal - 1 
		$inparams['internal']	= ($mytypary[1] == 2)? "1":"0";
	}
}
$inparams['search']	= (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";
$inparams['amount']	= (isset($filterData[2])) ? (float)$filterData[2] : "";
// date filter data
$inparams['from_date']		= (isset($inpdata['from']) && $inpdata['from'] != "") ? date("Y-m-d", strtotime($inpdata['from'])) : NULL;
$inparams['to_date']		= (isset($inpdata['to']) && $inpdata['to'] != "") ? date("Y-m-d", strtotime($inpdata['to'])) : NULL;

//find the details based on the parameters provided
$AccRptObj->bsTransactionList($inparams);
$total_records 		= $AccRptObj->bsTotal;
$result_data 		= $AccRptObj->bsArray;

// excel export heading setting
$headerArray 		= ["Type"=>"Type", "Name"=>"Name of income/expense", "Desc"=>"Description", "T_Id"=>"Track ID", "Amount"=>"Amount", "LC_Name"=>"Entry Branch", "Branch"=>"For Branch", "Date"=>"Date"];
if ($inparams['type'] == "bank") {
	$headerArray['Bank_Acc'] 	= "Bank Account";
	$headerArray['Paid_Branch'] = "Branch";
}
$dataList 			= [];
if ($total_records > 0) {
	foreach ($result_data  as $rw) {

		$singdata 	= ['Type'=>$rw->item_type, 'Name'=>$rw->IT_Name, 'Desc'=>($rw->DS_Description ? htmlspecialchars_decode($rw->DS_Description, ENT_QUOTES): ""), 'T_Id'=>$rw->TR_Track, 'Amount'=>number_format($rw->BS_Amount, 2, '.', ''), 'LC_Name'=>$rw->LC_Name, 'Branch'=>$rw->Branch, 'Date'=>$rw->BS_Date];


		if ($inparams['type'] == "bank") {
			$singdata['Bank_Acc'] 	 = $rw->BA_DispName;
			$singdata['Paid_Branch'] = $rw->Paid_Branch;
		}

		$dataList[] = array_map('trim', $singdata);
	}
}

echo $ExcelObj->createExcel($dataList, $headerArray);
exit;
?>