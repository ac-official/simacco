<?php
/**
* Cash / BAnk report (cash/bank transaction summery reports) for accounts teams
* 
* Created By Bilin At 04-06-2025  Last Updated At 06-06-2025
*/
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/AccountRptClass.php");
// class object define
$AccRptObj 	= new AccountRptClass();

// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$inparams 			= ['mh_type'=>0];

// pagination limit and start parameters 
$inparams['start'] 	=  (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	=  (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;

// find the other request parameters
$inparams['of_id'] 	= (isset($REQUEST['OFID']) && $REQUEST['OFID'] > 0) ? $REQUEST['OFID']:0;
$inparams['lc_id'] 	= (isset($REQUEST['LC_Id']) && $REQUEST['LC_Id'] > 0) ? $REQUEST['LC_Id']:0;
$inparams['branch']	= (isset($REQUEST['Branch']) && $REQUEST['Branch'] > 0) ? $REQUEST['Branch']:0;
$inparams['bank_id']	= (isset($REQUEST['Bank']) && $REQUEST['Bank'] > 0) ? $REQUEST['Bank']:0;
$inparams['type']		= (isset($REQUEST['type'])) ? $REQUEST['type']:"cash";

// filter inputs processing
$filterData 		= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
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
$inparams['from_date']		= (isset($REQUEST['from']) && $REQUEST['from'] != "") ? date("Y-m-d", strtotime($REQUEST['from'])) : NULL;
$inparams['to_date']		= (isset($REQUEST['to']) && $REQUEST['to'] != "") ? date("Y-m-d", strtotime($REQUEST['to'])) : NULL;

//find the details based on the parameters provided
$AccRptObj->bsTransactionList($inparams);
$total_records 		= $AccRptObj->bsTotal;
$result_data 		= $AccRptObj->bsArray;
$data_sum 			= $AccRptObj->bsAmtTotal;
$open_balance 		= 0;
if ($inparams['type'] == 'cash') {
	$open_balance 	= 0;
	$colnos 		= 4;
} else {
	$open_balance 	= $AccRptObj->BankOpenBalance($inparams);
	$colnos 		= 5;
}

// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';
echo '<userdata name="total_ic">'.$data_sum['income'].'</userdata>';
echo '<userdata name="total_ex">'.$data_sum['expense'].'</userdata>';
echo '<userdata name="total_inlrev">'.$data_sum['internal_rced'].'</userdata>';
echo '<userdata name="total_inlpaid">'.$data_sum['internal_paid'].'</userdata>';
echo '<userdata name="open_balance">'.$open_balance.'</userdata>';
	if (!empty($result_data)) {

		foreach($result_data as $rw) {

			$pettyCash 	= ($rw->BS_PettyCashAmt != 0) ? '('.number_format($rw->BS_PettyCashAmt,2).')': '';
			$amount 	= number_format($rw->BS_Amount,2); 
			echo '<row id="'.$rw->BS_Id.'">';
				echo '<userdata name="BS_Id">'.$rw->BS_Id.'</userdata>'
				.'<userdata name="SH_Id">'.$rw->SH_Id.'</userdata>';
				echo ' <cell>'.$rw->slno.'</cell>';
				echo ' <cell>'.$rw->item_type.'</cell>';
				echo '<cell>'.$rw->IT_Name;
                if($rw->DS_Description != '') { echo '-'. $rw->DS_Description; }
                if($rw->TR_Track != '') { echo '-Track : '.$rw->TR_Track; }
	            echo '</cell>';
	            echo '<cell>'.$amount.$pettyCash.'</cell>';
	            if ($inparams['type'] == 'bank') {
	            	echo ' <cell>'.$rw->bank_name.'</cell>';
	            }
	            echo ' <cell>'.$rw->Branch.'</cell>';
	            echo ' <cell>'.$rw->LC_Name.'</cell>';
	            echo ' <cell>'.$rw->BS_Date.'</cell>';			 	
            echo '</row>';
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell></cell> <cell colspan="'.$colnos.'"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>