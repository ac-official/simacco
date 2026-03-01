<?php
/**
 * list item entry once mapped but after mapping the old item data changed 
 * Created by Bilin 05-01-2025
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
// date filter data
$inparams['date_from']	= (isset($REQUEST['from']) && $REQUEST['from'] != "") ? date("Y-m-d", strtotime($REQUEST['from'])) : NULL;
$inparams['date_to']	= (isset($REQUEST['to']) && $REQUEST['to'] != "") ? date("Y-m-d", strtotime($REQUEST['to'])) : NULL;
// sort fields
$inparams['sortby']     = (isset($REQUEST['sort']) && $REQUEST['sort'] != "") ? (string)$REQUEST['sort'] : "date";
$inparams['orderby']    = (isset($REQUEST['order']) && $REQUEST['order'] != "") ? (string)$REQUEST['order'] : "ASC";
// other filters
$filterData 		= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['track_no']	= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['ledger']	= (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";

// get data from the database based on the parameters 
$accObj->listUpmJournal($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;


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
			echo ' <cell>'.($rw->TR_Track != '' ? $rw->TR_Track : '-').'</cell>';
			echo ' <cell>'.$rw->ledger_name.'</cell>';
			echo ' <cell>'.number_format($rw->amount,2).'</cell>';
            echo ' <cell>'.date('d/m/Y', strtotime($rw->created_at)).'</cell>';
            echo ' <cell>'.date('d/m/Y', strtotime($rw->BS_MDate)).'</cell>';
            echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
    		/*if ($UserACLObj->approve_ledger_amount == 1) {
	            echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.loadItemdataForm('.$rw->parent_id.', '.$rw->balance_sheet_id.', '.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:14px;"/> Edit</div>';

                echo '<div style="width:30px;display: flex;cursor:pointer;" onclick="preTally.AccountsTeam.deleteJournal(5,'.$rw->id.','.$rw->parent_id.', '.$rw->balance_sheet_id.')" ><img src="images/icon/trash.png" style="margin:2px 0;height:13px;margin-left:5px;"/> </div>';
	        } else {
	        	echo '-';
	        }*/
	        echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.loadItemdataForm('.$rw->parent_id.', '.$rw->balance_sheet_id.', '.$rw->id.',9);"><img src="images/icon/pencil.png" style="margin:2px 0;height:14px;"/> Edit</div>';
            echo ']]></cell>';
    		echo '</row>';
		}
	} else {
		echo '<row id="0"><cell></cell><cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';      
?>