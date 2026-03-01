<?php
/**
* Vendor or debtor data (income and expense) based on user filter (date, company and branch)
* Ledger list based on the selected vendor
* Created By Bilin @ 30-09-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

// find the inputs from the user side
$inparams 					= [];
$filterData 				= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['from_date']		= (isset($filterData[0]) && $filterData[0] != "") ? date("Y-m-d", strtotime($filterData[0])) : NULL;
$inparams['to_date']		= (isset($filterData[1]) && $filterData[1] != "") ? date("Y-m-d", strtotime($filterData[1])) : NULL;
$inparams['company_id']		= (isset($filterData[2])) ? (int)$filterData[2] : 0;
$inparams['branch_id']		= (isset($filterData[3])) ? (int)$filterData[3] : 0;
$inparams['vendor_id']		= (isset($filterData[4])) ? (int)$filterData[4] : 0;

// fetch data from database via model class 
if ( $inparams['vendor_id'] > 0) {
	$accObj->listRptVendorDet($inparams);
} else {
	$accObj->listRptVendor($inparams);
}
$result_data 		= $accObj->data_list;
$total_income 		= 0;
$total_expense 		= 0;

// return result in a selected xml grid format
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
	if (!empty($result_data)) {

		foreach($result_data as $rw) {

			echo '<row id="'.$rw['id'].'">';
			echo ' <cell>'.htmlspecialchars_decode($rw['title']).'</cell>';
			echo ' <cell>'.number_format($rw['income'],2,".","").'</cell>';
			echo ' <cell>'.number_format($rw['expense'],2,".","").'</cell>';
			echo '</row>';
			$total_income 	+= $rw['income'];
			$total_expense 	+= $rw['expense'];
		}
	} else {
		echo '<row id="0"> <cell colspan="3"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
	}
	echo '<userdata name="total_income">'. number_format($total_income,2,".","").'</userdata>';
	echo '<userdata name="total_expense">'. number_format($total_expense,2,".","").'</userdata>';
	echo '<userdata name="total_balance">'. number_format(($total_income-$total_expense),2,".","").'</userdata>';
echo '</rows>';
?>
