<?php
/**
 * Cash Flow Statement based on the parameters selected from the form
 * Same as p and l but its contain all cash related flows
 * Created BY Bilin @ 14-01-2026
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
$inparams['trans_type']		= (isset($filterData[4])) ? (int)$filterData[4] : 0;

// fetch data from database via model class 
$accObj->listCashFlow($inparams);
$result_data 		= $accObj->data_list;

// return result in a selected xml grid format
echo ("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
	if (!empty($result_data)) {



	} else {
		echo '<row id="0"> <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
	}
echo '</rows>';

?>