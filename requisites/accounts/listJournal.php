<?php
/**
* List saved journal (ledger based data from old to new)
* List with ledger based filter and paginations
* Created By Bilin @ 04-08-2025
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$inparams 				= ['office_id'=>$preTally_user_ofid];
// pagination limit and start parameters 
$inparams['start'] 		= (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 		= (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['branch_id']		= (isset($filterData[1])) ? (int)$filterData[1] : 0;
$inparams['track_no']		= (isset($filterData[2])) ? trim($filterData[2]) : "";
$inparams['date']			= (isset($filterData[3]) && $filterData[3] != "") ? date('Y-m-d',strtotime($filterData[3])) : "";
$inparams['date_from'] 		= "";
$inparams['date_to'] 		= "";
$month 				= (isset($filterData[4])) ? (int)$filterData[4] : 0;
$year 				= (isset($filterData[5])) ? (int)$filterData[5] : 0;
$day 					= (isset($filterData[6])) ? (int)$filterData[6] : 0;
if ($day > 0 || $month > 0 || $year > 0 ) {
	$year 	= ($year <= 0) ? date('Y'):$year;
	$month	= ($month < 10) ? "0".$month:$month;
	$month 	= ($month <= 0 && $day > 0) ? date('m'):$month;
	$day 		= ($day < 10) ? "0".$day:$day;
	if ($day > 0) {
		$inparams['date_from'] 	= $year."-".$month."-".$day;
		$inparams['date_to'] 	= $year."-".$month."-".$day;
	}else if ($month > 0) {
		$inparams['date_from'] 	= $year."-".$month."-01";
		$inparams['date_to'] 	= date("Y-m-t",strtotime($year."-".$month."-01"));
	} else {
		$inparams['date_from'] 	= $year."-01-01";
		$inparams['date_to'] 	= $year."-12-31";
	}
}
$inparams['amt']			= (isset($filterData[7]) && $filterData[7] != "") ? (float)$filterData[7] : 0;

// sort fields
$inparams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']		= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$parentsum 			= $accObj->listJournal($inparams);
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
    		echo '<userdata name="jid">'.$rw->id.'</userdata>';

    		echo ' <cell>'.$rw->slno.'</cell>';
		echo ' <cell>'.date('d/m/Y', strtotime($rw->date_entry)).'</cell>';
		echo ' <cell>'.($rw->trackno != '' ? $rw->trackno : '-').'</cell>';
		echo ' <cell>'.$rw->ledger_name.'</cell>';
		echo ' <cell>'.$rw->LC_Name.'</cell>';
		echo ' <cell>'.number_format($rw->amount,3).'</cell>';
            echo ' <cell>'.date('d/m/Y', strtotime($rw->created_at)).'</cell>';
    		echo ' <cell>'.$rw->name.'</cell>';
    		echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
    		  if ($UserACLObj->approve_ledger_amount == 1) {
	            echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.loadItemdataForm('.$rw->parent_id.', '.$rw->balance_sheet_id.', '.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:14px;"/> Edit</div>';

                echo '<div style="width:30px;display: flex;cursor:pointer;" onclick="preTally.AccountsTeam.deleteJournal(5,'.$rw->id.','.$rw->parent_id.', '.$rw->balance_sheet_id.')" ><img src="images/icon/trash.png" style="margin:2px 0;height:13px;margin-left:5px;"/> </div>';
	        } else {
	        	echo '-';
	        }
	        $viewdesc = '<div> <b>Total Amount :</b> '.$parentsum[$rw->parent_id]["base"].'</div>'; 
	        if ($parentsum[$rw->parent_id]["convrt"] > 0) {
	        	$viewdesc .= '<div> <b>Converted Amount :</b> '.$parentsum[$rw->parent_id]["convrt"].'</div>'; 
	        }       	
        	  echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />';
            echo ']]></cell>';
    		echo '</row>';
    	}
    } else {

		echo '<row id="0"> <cell></cell> <cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>