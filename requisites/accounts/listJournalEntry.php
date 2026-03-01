<?php
/**
* Accounts Journal Entries Listing with filter and pagination
* Created By Bilin @ 12-11-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$inparams 				= ['company_id'=>($UserACLObj->manage_all_bills == 0) ? $preTally_user_ofid:0];
// pagination limit and start parameters 
$inparams['start'] 		=  (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 		=  (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
// filter inputs processing
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['dr_ledger']	= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['cr_ledger']	= (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";
$inparams['date']		= (isset($filterData[2]) && $filterData[2] != "") ? date('Y-m-d',strtotime($filterData[2])) : "";
$inparams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']		= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$accObj->listJournalEntry($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;
//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';

      if (!empty($result_data)) {

		foreach($result_data as $rw) {

			$is_edit 	= (date('Y-m-d', strtotime($rw->created_at . ' +30 days')) >= date('Y-m-d')) ? 1 : 0;
			echo '<row id="'.$rw->id.'">';
			echo '<userdata name="eid">'.$rw->id.'</userdata>'
			.'<userdata name="ledger_dr">'.$rw->ledger_dr.'</userdata>'
			.'<userdata name="ledger_cr">'.$rw->ledger_cr.'</userdata>'
			.'<userdata name="bank_dr">'.$rw->bank_dr.'</userdata>'
			.'<userdata name="bank_cr">'.$rw->bank_cr.'</userdata>'
			.'<userdata name="amount">'.round($rw->amount,4).'</userdata>'
			.'<userdata name="date">'.$rw->date.'</userdata>'
			.'<userdata name="company_id">'.$rw->company_id.'</userdata>'
			.'<userdata name="branch_id">'.$rw->branch_id.'</userdata>';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.htmlspecialchars_decode($rw->dr_name).'</cell>';
			echo ' <cell>'.htmlspecialchars_decode($rw->cr_name).'</cell>';
			echo ' <cell>'.date('d M Y',strtotime($rw->date)).'</cell>';
			echo ' <cell>'.number_format($rw->amount,2).'</cell>';
			echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
				if ($is_edit == 1) {
	            	echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editJournalEntry('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
	            }
	            $viewdesc 	= '<div> Company : '.htmlspecialchars_decode($rw->OF_Name).'</div>';
	            $viewdesc  .= ($rw->branch_id > 0) ? '<div> Branch : '.htmlspecialchars_decode($rw->LC_Name).'</div>': '';
	            //$viewdesc  .= ($rw->bank_dr > 0) ? '<div> Debit Bank : '.htmlspecialchars_decode($rw->debit_bank).'</div>':'';
	            //$viewdesc  .= ($rw->bank_cr > 0)? '<div> Credit Bank : '.htmlspecialchars_decode($rw->credit_bank).'</div>':'';
	            $viewdesc  .= '<div> Last Updated By : '.$rw->updated_user.'</div>'
	            	.'<div> Last Updated At : '.date('M d, Y', strtotime($rw->updated_at)).'</div>';
	            echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />'; 
			echo ']]></cell>';

           	echo '</row>';
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>