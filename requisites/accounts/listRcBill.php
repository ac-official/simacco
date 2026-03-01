<?php
/**
* Accounts Bills Recurring Listing with filter and pagination
* Created By Bilin @ 23-09-2025
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
$inparams['name']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['amount']		= (isset($filterData[1])) ? (string)$filterData[1] : "";
$inparams['status']		= (isset($filterData[2])) ? (string)$filterData[2] : "";
// sort and order
$inparams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']	= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$accObj->listRcBills($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;
//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';

      if (!empty($result_data)) {

		foreach($result_data as $rw) {
			
			echo '<row id="'.$rw->id.'">';
			echo '<userdata name="eid">'.$rw->id.'</userdata>'
			.'<userdata name="bill_id">'.$rw->bill_id.'</userdata>'
			.'<userdata name="day_id">'.$rw->day_id.'</userdata>'
			.'<userdata name="last_bill_id">'.$rw->last_bill_id.'</userdata>'
			.'<userdata name="next_date">'.$rw->next_date.'</userdata>'
			.'<userdata name="bill_name">'.htmlspecialchars_decode($rw->bill_name.' - ('.date('d/m/Y',strtotime($rw->last_date)).')  '.$rw->LC_Name).'</userdata>'
			.'<userdata name="total_amount">'.round($rw->total_amount,4).'</userdata>'
			.'<userdata name="status">'.$rw->status.'</userdata>';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.htmlspecialchars_decode($rw->bill_name).'</cell>';
			echo ' <cell>'.date('d M Y',strtotime($rw->last_date)).'</cell>';			
			echo ' <cell>'.date('d M Y',strtotime($rw->next_date)).'</cell>';
			echo ' <cell>'.number_format($rw->total_amount,2).'</cell>';
			echo ' <cell>'.$rw->status_val.'</cell>';
			echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
			echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editRcBills('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
			echo '<div style="width:30px;display: flex;cursor:pointer;" onclick="preTally.AccountsTeam.deleteRCBill('.$rw->id.')" ><img src="images/icon/trash.png" style="margin:2px 0;height:13px;margin-left:5px;"/> </div>';
			echo ']]></cell>';

           	echo '</row>';
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>