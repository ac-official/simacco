<?php
/**
 * Created by Bilin At 07-07-2025
 * User based acls list with name or location based search (any word search)
 *   
*/
// class and object declaration
require_once($BASEPATH . "preTallyClass/ACLClass.php");
$ACLObj = new ACLClass();

// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$inparams 			= ( $preTally_user_ofid != 1 ) ? ['off_id'=>$preTally_user_ofid]:[];
// pagination limit and start parameters 
$inparams['start'] 	=  (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	=  (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
// filter inputs processing
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";

$ACLObj->listUsersAcls($inparams);
$total_records 		= $ACLObj->data_total;
$result_data 		= $ACLObj->data_list;

// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';

	if (!empty($result_data)) {
		// user data for edit time no need to get data from server
		foreach($result_data as $rw) {
			echo '<row id="'.$rw->id.'" '.$rowstyle.'>';
			echo '<userdata name="aclid">'.$rw->id.'</userdata>'
			.'<userdata name="us_id">'.$rw->us_id.'</userdata>'
			.'<userdata name="add_break_time">'.$rw->add_break_time.'</userdata>'
			.'<userdata name="view_break_time">'.$rw->view_break_time.'</userdata>'
			.'<userdata name="approve_late_entry">'.$rw->approve_late_entry.'</userdata>'
			.'<userdata name="view_late_entry">'.$rw->view_late_entry.'</userdata>'
			.'<userdata name="add_clear_attendance">'.$rw->add_clear_attendance.'</userdata>'
			.'<userdata name="view_clear_attencance">'.$rw->view_clear_attencance.'</userdata>'
			.'<userdata name="view_attendance_detail">'.$rw->view_attendance_detail.'</userdata>'
			.'<userdata name="view_accounts_report">'.$rw->view_accounts_report.'</userdata>'
			.'<userdata name="view_account_settings">'.$rw->view_account_settings.'</userdata>'
			.'<userdata name="add_account_settings">'.$rw->add_account_settings.'</userdata>'
			.'<userdata name="approve_account_settings">'.$rw->approve_account_settings.'</userdata>'
			.'<userdata name="approve_ledger_amount">'.$rw->approve_ledger_amount.'</userdata>'
			.'<userdata name="manage_bills">'.$rw->manage_bills.'</userdata>'
			.'<userdata name="manage_all_bills">'.$rw->manage_all_bills.'</userdata>'
			.'<userdata name="approve_grace_time">'.$rw->approve_grace_time.'</userdata>'
			.'<userdata name="close_others_tracks">'.$rw->close_others_tracks.'</userdata>'
			.'<userdata name="view_all_company">'.$rw->view_all_company.'</userdata>'
			.'<userdata name="bank_reconciliation">'.$rw->bank_reconciliation.'</userdata>'
			.'<userdata name="add_open_balance">'.$rw->add_open_balance.'</userdata>'
			.'<userdata name="view_master_report">'.$rw->view_master_report.'</userdata>';
			echo '<cell>'.$rw->slno.'</cell>';
			echo '<cell>'.$rw->name.'</cell>';
			echo '</row>';
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
	}
echo '</rows>';
?>