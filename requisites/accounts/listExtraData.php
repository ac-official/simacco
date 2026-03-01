<?php
/**
 * List all extra data (add/excel upload) company, date and branch name filters enabled
 * Edit option for all entries
 * Created By Bilin 09-01-2026 
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$inparams 				= [];
// pagination limit and start parameters 
$inparams['start'] 		= (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 		= (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
// date filter data
$inparams['date_from']	= (isset($REQUEST['from']) && $REQUEST['from'] != "") ? date("Y-m-d", strtotime($REQUEST['from'])) : NULL;
$inparams['date_to']	= (isset($REQUEST['to']) && $REQUEST['to'] != "") ? date("Y-m-d", strtotime($REQUEST['to'])) : NULL;
// sort fields
$inparams['sortby']     = (isset($REQUEST['sort']) && $REQUEST['sort'] != "") ? (string)$REQUEST['sort'] : "date";
$inparams['orderby']    = (isset($REQUEST['order']) && $REQUEST['order'] != "") ? (string)$REQUEST['order'] : "ASC";
// other filters
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['company_id']	= (isset($filterData[0])) ? (int)$filterData[0] : 0;
$inparams['title']		= (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";
$inparams['branch']		= (isset($filterData[2]) && $filterData[2] != "") ? trim($filterData[2]) : "";

// get data from the database based on the parameters 
$accObj->listExtraData($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;

// get all  type 
$list_types 	= $accObj->ListGroupTypes(1);

//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';
    if (!empty($result_data)) {

		foreach($result_data as $rw) {

			$title  = htmlspecialchars_decode($rw->title);
			$title  = str_replace('&','and',$title);
			echo '<row id="'.$rw->id.'">';
			echo '<userdata name="title">'.$title.'</userdata>'
			.'<userdata name="type">'.htmlspecialchars_decode($rw->type).'</userdata>'
			.'<userdata name="branch_name">'.htmlspecialchars_decode($rw->branch_name).'</userdata>'
			.'<userdata name="company_id">'.$rw->company_id.'</userdata>'
			.'<userdata name="branch_id">'.$rw->branch_id.'</userdata>'
			.'<userdata name="ledger_id">'.$rw->ledger_id.'</userdata>'
			.'<userdata name="amount">'.round($rw->amount,3).'</userdata>'
			.'<userdata name="job_date">'.$rw->job_date.'</userdata>'
			.'<userdata name="addtype">'.$rw->addtype.'</userdata>';

			echo '<cell>'.$rw->slno.'</cell>';
			echo '<cell>'.$list_types[$rw->type].'</cell>';
			echo '<cell>'.date('d/m/Y', strtotime($rw->job_date)).'</cell>';
			echo '<cell>'.$title.(($rw->ledger_name != '' && $title != $rw->ledger_name)  ? ' ('.$rw->ledger_name.')' : '').'</cell>';
			echo '<cell>'.$rw->branch_name.($rw->branch_id > 0 ? '':' - (Update Branch)').'</cell>';
			echo '<cell>'.number_format($rw->amount,2).'</cell>';
			echo '<cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
			if ($UserACLObj->approve_ledger_amount == 1) {
		            echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editExtradataForm('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:14px;"/> Edit</div>';

	                echo '<div style="width:30px;display: flex;cursor:pointer;" onclick="preTally.AccountsTeam.deleteJournal(11,'.$rw->id.')" ><img src="images/icon/trash.png" style="margin:2px 0;height:13px;margin-left:5px;"/> </div>';
		      } else {
		        	echo '-';
		      }
			echo ']]></cell>';
			echo '</row>';
		}
	} else {
		echo '<row id="0"><cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
	}
echo '</rows>';      
?>