<?php
/** 
	* list direct Ledger entries with filter and paginations
	* Created By Bilin @ 18-11-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$inparams 			= ['office_id'=>$preTally_user_ofid];
// pagination limit and start parameters 
$inparams['start'] 	= (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	= (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
$filterData 		= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['ledger']	= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['branch']	= (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";
$inparams['date']		= (isset($filterData[2]) && $filterData[2] != "") ? date('Y-m-d',strtotime($filterData[2])) : "";
// sort fields
$inparams['sortby']	= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']	= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$accObj->listLedgerData($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;

//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';
    if (!empty($result_data)) {
    	foreach($result_data as $rw) {

    		$is_edit 	= (date('Y-m-d', strtotime($rw->created_at . ' +30 days')) >= date('Y-m-d') && $UserACLObj->approve_ledger_amount == 1) ? 1 : 0;
    		echo '<row id="'.$rw->id.'">';
    		echo '<userdata name="eid">'.$rw->id.'</userdata>'
			.'<userdata name="remarks">'.htmlspecialchars_decode($rw->remarks).'</userdata>'
			.'<userdata name="date_entry">'.$rw->date_entry.'</userdata>'
			.'<userdata name="office_id">'.$rw->office_id.'</userdata>'
			.'<userdata name="location_id">'.$rw->location_id.'</userdata>'
			.'<userdata name="company_id">'.$rw->company_id.'</userdata>'
			.'<userdata name="branch_id">'.$rw->branch_id.'</userdata>'
			.'<userdata name="ledger_id">'.$rw->ledger_id.'</userdata>'
			.'<userdata name="vendor_id">'.$rw->vendor_id.'</userdata>'
			.'<userdata name="amount">'.round($rw->amount,4).'</userdata>'
			.'<userdata name="converted">'.round($rw->converted,4).'</userdata>'
			.'<userdata name="amount_ratio">'.round($rw->amount_ratio,4).'</userdata>'
			.'<userdata name="amt_type">'.$rw->amt_type.'</userdata>'
			.'<userdata name="ba_id">'.$rw->ba_id.'</userdata>'
			.'<userdata name="ba_id_debit">'.$rw->ba_id_debit.'</userdata>'
			.'<userdata name="trackno">'.$rw->trackno.'</userdata>'
			.'<userdata name="parent_id">'.$rw->parent_id.'</userdata>'
			.'<userdata name="ie_type">'.(($rw->ie_type == $rw->ietp_ledger) ? "add":"sub").'</userdata>';
			//12-01-2026 Start
			$amount_ratio = $rw->amount_ratio;
			$converted    = $rw->converted;
			$amount 	  = $rw->amount;
			if ($rw->company_id == $preTally_user_ofid && $rw->converted > 0) {
				$amount 	  = $rw->converted;
				$converted    = $rw->amount;
				$amount_ratio = $rw->amount/$rw->converted;
			}
			//12-01-2026 End
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.date('d/m/Y', strtotime($rw->date_entry)).'</cell>';
			echo ' <cell>'.$rw->ledger_name.'</cell>';
			echo ' <cell>'.($rw->branch_Name != '' ? $rw->branch_Name : '-').'</cell>';
			echo ' <cell>'.number_format($amount,4).'</cell>';

			echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
				if ($is_edit == 1) {
	            	echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editLedgerData('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
	            }
	            $viewdesc 	= '<div> Company (From) : '.htmlspecialchars_decode($rw->OF_Name).'</div>';
	            $viewdesc  .= ($rw->location_id > 0) ? '<div> Branch : '.htmlspecialchars_decode($rw->LC_Name).'</div>': '';
	            $viewdesc  .= '<div> Company (To) : '.htmlspecialchars_decode($rw->company_Name).'</div>';
	            $viewdesc  .= ($rw->branch_id > 0) ? '<div> Branch : '.htmlspecialchars_decode($rw->branch_Name).'</div>': '';
	            $viewdesc  .= ($rw->vendor_id > 0) ? '<div> Creditor/Debtor : '.htmlspecialchars_decode($rw->vendor_Name).'</div>': '';
	            $viewdesc  .= ($rw->converted > 0) ? '<div> Converted Amount : '.round($converted,4).'</div>': '';
	            $viewdesc  .= ($rw->converted > 0) ? '<div> Converted Ratio : '.round($amount_ratio,4).'</div>': '';
	            $viewdesc  .= '<div> Amount Type : '.$rw->type_val.'</div>';
	            $viewdesc  .= ($rw->ba_id > 0) ? '<div> Bank : '.htmlspecialchars_decode($rw->bank_Name).'</div>': '';
	            $viewdesc  .= ($rw->ba_id_debit > 0) ? '<div> Bank (Debit) : '.htmlspecialchars_decode($rw->bank_Dr_Name).'</div>': '';
	            $viewdesc  .= '<div> Last Updated At : '.($rw->updated_at != "" ? date('M d, Y', strtotime($rw->updated_at)): date('M d, Y', strtotime($rw->created_at))).'</div>';

	            echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />';
			echo ']]></cell>';

    		echo '</row>';
    	}
    } else {
		echo '<row id="0"> <cell></cell> <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>