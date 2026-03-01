<?php
/** 
	* list yearly entered opening balance list with filter and paginations
	* Created By Bilin @ 24-11-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$inparams 			= [];
// pagination limit and start parameters 
$inparams['start'] 	= (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	= (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
$filterData 		= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$year				= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['title']	= (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";
$inparams['company']= (isset($filterData[2]) && $filterData[2] > 0) ? (int)$filterData[2] : 0;
$inparams['branch']	= (isset($filterData[3]) && $filterData[3] != "") ? trim($filterData[3]) : "";
// sort fields
$inparams['sortby']	= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";
if ($year != "") {
	$yearary 				= explode('-',$year);
	$inparams['date_from']	= $yearary[0]."-04-01";
	$inparams['date_to']	= $yearary[1]."-03-31";
}

//find the details based on the parameters provided
$accObj->listOpeningData($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;

//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';
    if (!empty($result_data)) {
    	foreach($result_data as $rw) {
    		$is_edit 	= ($rw->status == 2 && $UserACLObj->add_open_balance == 1) ? 1 : 0;
    		echo '<row id="'.$rw->id.'">';
    		echo '<userdata name="eid">'.$rw->id.'</userdata>'
			.'<userdata name="title">'.htmlspecialchars_decode($rw->title).'</userdata>'
			.'<userdata name="company_id">'.$rw->company_id.'</userdata>'
			.'<userdata name="branch_id">'.$rw->branch_id.'</userdata>'
			.'<userdata name="type">'.$rw->type.'</userdata>'
			.'<userdata name="ledger_id">'.$rw->ledger_id.'</userdata>'
			.'<userdata name="amount">'.round($rw->amount,4).'</userdata>'
			.'<userdata name="amt_type">'.$rw->amt_type.'</userdata>'
			.'<userdata name="year_date">'.$rw->year_date.'</userdata>';

			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.$rw->year_date.'</cell>';
			echo ' <cell>'.$rw->title.'</cell>';
			echo ' <cell>'.$rw->company_name.'</cell>';
			echo ' <cell>'.$rw->branch_Name.'</cell>';
			echo ' <cell>'.number_format($rw->amount,2).'</cell>';

			echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
				if ($is_edit == 1) {
	            	echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editOpenBalance('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
	            }
	            $viewdesc 	= '<div> Type : '.htmlspecialchars_decode($rw->type_name).'</div>';
	            $viewdesc  .= ($rw->ledger_id > 0) ? '<div> Ledger : '.htmlspecialchars_decode($rw->ledger_name).'</div>': '';	
	            $viewdesc  .= '<div> Amount Type : '.$rw->amttype_name.'</div>';
	            $viewdesc  .= '<div> Created At : '.date('M d, Y', strtotime($rw->created_at)).'</div>';

	            echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />';
			echo ']]></cell>';

			echo '</row>';
		}
    } else {
		echo '<row id="0"> <cell></cell> <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>