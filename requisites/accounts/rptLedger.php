<?php
/**
 * Re Created BY Bilin @ 15-10-2025
 * Selected ledger based transaction based on the company and period
 * 
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$inparams 					= [];
// pagination limit and start parameters 
$inparams['start'] 	=  (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	=  (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
// find the filter inputs from the user side
$filterData 				= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['from_date']		= (isset($filterData[0]) && $filterData[0] != "") ? date("Y-m-d", strtotime($filterData[0])) : NULL;
$inparams['to_date']		= (isset($filterData[1]) && $filterData[1] != "") ? date("Y-m-d", strtotime($filterData[1])) : NULL;
$inparams['company_id']		= (isset($filterData[2])) ? (int)$filterData[2] : 0;
$inparams['branch_id']		= (isset($filterData[3])) ? (int)$filterData[3] : 0;
$inparams['ledger_id']		= (isset($filterData[4])) ? (int)$filterData[4] : 0;
$inparams['trans_type']		= (isset($filterData[5])) ? (int)$filterData[5] : 0;

//find the details based on the parameters provided
$totaldata 			= $accObj->listRptLedgerDet($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;
$total_income 		= $totaldata['total_income'];
$total_expense 		= $totaldata['total_expense'];
//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';
    if (!empty($result_data)) {

		foreach($result_data as $rw) {
			$trackno 	   = '';
			if ($rw->trackno != "") {
				$trackno = ' Track: '.$rw->trackno;
			}else if (isset($rw->TR_Track) && $rw->TR_Track != '') {
				$trackno = ' Track: '.$rw->TR_Track;
			}
			echo '<row id="'.$rw->id.'">';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.htmlspecialchars_decode($rw->title).' @ '.date('M d, Y', strtotime($rw->date_entry)).$trackno.($rw->DS_Description != '' ? ' - '.$rw->DS_Description : '').'</cell>';
			echo ' <cell>'.number_format($rw->income,2,".","").'</cell>';
			echo ' <cell>'.number_format($rw->expense,2,".","").'</cell>';
			echo '</row>';
		}
	} else {
		echo '<row id="0"> <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
	}
	echo '<userdata name="total_income">'. number_format($total_income,2,".","").'</userdata>';
	echo '<userdata name="total_expense">'. number_format($total_expense,2,".","").'</userdata>';
echo '</rows>';
?>