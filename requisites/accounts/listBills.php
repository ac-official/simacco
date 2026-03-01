<?php
/**
* Accounts Bills Listing with filter and pagination
* Created By Bilin @ 10-09-2025
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
$inparams['name']			= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['vendor']		= (isset($filterData[1])) ? (string)$filterData[1] : "";
$inparams['amount']		= (isset($filterData[2])) ? (string)$filterData[2] : "";
$inparams['from_date']		= (isset($filterData[3]) && $filterData[3] != "") ? date("Y-m-d", strtotime($filterData[3])) : NULL;
$inparams['to_date']		= (isset($filterData[4]) && $filterData[4] != "") ? date("Y-m-d", strtotime($filterData[4])) : NULL;
$inparams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']		= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$accObj->listBills($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;
//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';

      if (!empty($result_data)) {

		foreach($result_data as $rw) {

			$is_edit 	= (date('Y-m-d', strtotime($rw->created_at . ' +30 days')) >= date('Y-m-d') && $rw->paid_status != 1) ? 1 : 0;
			echo '<row id="'.$rw->id.'">';
			echo '<userdata name="eid">'.$rw->id.'</userdata>'
			.'<userdata name="bill_name">'.htmlspecialchars_decode($rw->bill_name).'</userdata>'
			.'<userdata name="bill_date">'.$rw->bill_date.'</userdata>'
			.'<userdata name="due_date">'.$rw->due_date.'</userdata>'
			.'<userdata name="tds_percent">'.$rw->tds_percent.'</userdata>'
			.'<userdata name="tax_percent">'.$rw->tax_percent.'</userdata>'
			.'<userdata name="tds_id">'.$rw->tds_id.'</userdata>'
			.'<userdata name="tax_id">'.$rw->tax_id.'</userdata>'
			.'<userdata name="vendor_id">'.$rw->vendor_id.'</userdata>'
			.'<userdata name="total_amount">'.round($rw->total_amount,4).'</userdata>'
			.'<userdata name="bill_type">'.$rw->bill_type.'</userdata>'
			.'<userdata name="bill_amount">'.round($rw->bill_amount,4).'</userdata>'
			.'<userdata name="tds_amount">'.round($rw->tds_amount,4).'</userdata>'
			.'<userdata name="tax_amount">'.round($rw->tax_amount+$rw->tax_amount2,4).'</userdata>'
			.'<userdata name="company_id">'.$rw->company_id.'</userdata>'
			.'<userdata name="branch_id">'.$rw->branch_id.'</userdata>';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.date('d M Y',strtotime($rw->bill_date)).'</cell>';
			echo ' <cell>'.htmlspecialchars_decode($rw->bill_name).'</cell>';
			echo ' <cell>'.$rw->vendor_name.'</cell>';
			echo ' <cell>'.number_format($rw->bill_amount,2).'</cell>';
			echo ' <cell>'.number_format($rw->tds_amount,2).'</cell>';
			echo ' <cell>'.number_format($rw->total_amount,2).'</cell>';
			echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
			if ($is_edit == 1) {
	            	echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editBills('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
	            }
	            $viewdesc 	= '<div> Company : '.htmlspecialchars_decode($rw->OF_Name).'</div>';
	            $viewdesc  .= ($rw->branch_id > 0) ? '<div> Branch : '.htmlspecialchars_decode($rw->LC_Name).'</div>': '';
	            $viewdesc  .= '<div> Bill Type : '.$rw->type_val.'</div>';
	            $viewdesc  .= '<div> Bill Amount : '.number_format($rw->bill_amount,2).'</div>';
	            if ($rw->tds_id > 0) {
	            	$viewdesc .= '<div> TDS Percent : '.$rw->rule_name.'('.$rw->tds_percent.')</div>';
	            	$viewdesc .= '<div> TDS Amount : '.number_format($rw->tds_amount,2).'</div>';
	            }
	            $viewdesc  .= '<div> Total Amount : '.number_format($rw->total_amount,2).'</div>';
	            $viewdesc  .= '<div> Last Updated By : '.$rw->updated_user.'</div>'
	            	.'<div> Last Updated At : '.date('M d, Y', strtotime($rw->updated_at)).'</div>';

	            echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />'; 
			echo ']]></cell>';

           		echo '</row>';
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>