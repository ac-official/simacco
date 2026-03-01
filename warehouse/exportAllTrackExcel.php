<?php
/**
* Trackwise reports based on the job date filter and login user company id
* Created By Bilin @ 22-05-2025
*/
// class and object declaration
require_once($BASEPATH . "preTallyClass/ExportExcelClass.php");
require_once($BASEPATH . "preTallyClass/TrackClass.php");
$ExcelObj 	= new ExportExcelClass();
$TrackObj 	= new TrackClass();
//filter vcalues 
$flag 					= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$filters 				= $_REQUEST['filter'];
$inParams 				= ['start'=>0,'limit'=>0];
$inParams['from_date'] 	= (isset($filters['from_date']) && $filters['from_date']!= "") ? date("Y-m-d", strtotime($filters['from_date'])): '';
$inParams['to_date'] 	= (isset($filters['to_date']) && $filters['to_date'] != "") ? date("Y-m-d", strtotime($filters['to_date'])): date('Y-m-d');
$inParams['off_id']		= (isset($filters['off_id'])) ? $filters['off_id']:0;
if ($flag == 2) {
	$inParams['track_no']	= (isset($filters['track']) && $filters['track'] != "") ? trim($filters['track']) : "";
	$inParams['search']		= (isset($filters['search']) && $filters['search'] != "")? trim($filters['search']) :"";
	$inParams['amount']		= (isset($filters['amount'])) ? (float)$filters['amount'] : '';
	$TrackObj->trackExpenseList($inParams);
	$headerArray 	= array('slno'=>'SlNo', 'TR_Track'=>'Track No', 'BS_Date'=>'Payment Date', 'BS_Amount'=>'Amount Spent', 'IT_Name'=>'Item Name', 'DS_Description'=>'Details', 'LC_Name'=>'Location');
} else {	
	$inParams['track_no']	= (isset($filters['track_no']) && $filters['track_no'] != '') ? trim($filters['track_no']) : '';
	$inParams['date_type']	= (isset($filters['date_type'])) ? (int)$filters['date_type']:0; //22-01-2026
	$headerArray 	= array('slno'=>'SlNo', 'TR_Track'=>'Track No','bdate'=>'Entry Date', 'job_date'=>'Job Date', 'job_amount'=>'Job Amount', 'job_expense'=>'Amount Deductable', 'total_income'=>'Total Received (Verified)', 'unapprove_amt'=>'Total Received (Unverified)', 'total_expense'=>'Total Amount Spent', 'balance_unpaid'=>'Balance Receivable', 'after_income'=>'Amount Received After Search Date');
	$TrackObj->listAllTracks($inParams);
}
$totalcount 			= $TrackObj->totalCount;
$allTracks 				= $TrackObj->trackList;
// excel data and header preparation
$data 			= array();
if ($totalcount > 0 && !empty($allTracks)) {

	foreach ($allTracks as $rw) {
		if ($flag == 2) {
			$singleData 	= ['slno'=>$rw->slno, 'TR_Track'=>$rw->TR_Track, 'BS_Date'=>date('d-m-Y', strtotime($rw->BS_Date)), 'BS_Amount'=>$rw->BS_Amount, 'IT_Name'=>htmlentities($rw->IT_Name), 'DS_Description'=>htmlentities($rw->DS_Description), 'LC_Name'=>htmlentities($rw->LC_Name) ];
		} else {
			$singleData 	= ['slno'=>$rw->slno, 'TR_Track'=>$rw->TR_Track, 'bdate'=>date('d-m-Y', strtotime($rw->BS_Date)), 'job_date'=>date('d-m-Y', strtotime($rw->job_Date)), 'job_amount'=>$rw->job_amount, 'job_expense'=>$rw->job_expense, 'total_income'=>$rw->total_income, 'unapprove_amt'=>$rw->unapprove_amt, 'total_expense'=>$rw->total_expense, 'balance_unpaid'=>$rw->balance_unpaid, 'after_income'=>$rw->after_income ];
		}		
    	$data[] 		= $singleData; 
	}
}
echo $ExcelObj->createExcel($data, $headerArray);
exit;
?>