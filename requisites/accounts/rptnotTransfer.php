<?php 
/**
 * marked not need to transfer to new account system 
 * List that items and delete from the block list and added again to the pending to transfer list
 * Created BY Bilin @ 07-11-2025 
 * 
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// get all entries based on the offical id /company id of the logined user
$inparams 			= ['office_id'=>$preTally_user_ofid];
// pagination limit and start parameters 
$inparams['start'] 	= (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	= (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
$filterData             = (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['search']     = (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['date']		= (isset($filterData[1]) && $filterData[1] != "") ? date('Y-m-d',strtotime($filterData[1])) : "";
// sort fields
$inparams['sortby']     = (isset($REQUEST['sort']) && $REQUEST['sort'] != "") ? (string)$REQUEST['sort'] : "date";
$inparams['orderby']    = (isset($REQUEST['order']) && $REQUEST['order'] != "") ? (string)$REQUEST['order'] : "ASC";

//find the details based on the parameters provided
if ($UserACLObj->approve_ledger_amount == 1) {
	$accObj->listItemBlocked($inparams);
	$total_records 		= $accObj->data_total;
	$result_data 		= $accObj->data_list;
	//echo $accObj->sql_query;
} else { // no permission to save 
	$total_records 		= 0;
	$result_data 		= [];
}
//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';
    if (!empty($result_data)) {
    	foreach($result_data as $rw) {
    		echo '<row id="'.$rw->BS_Id.'">';
    		echo ' <cell>'.$rw->slno.'</cell>';
    		echo ' <cell>'.$rw->IT_Name.' - ('.($rw->BS_BranchTo > 0 ? $rw->branch_name: $rw->LC_Name).') '.($rw->TR_Id > 0 ? ' - '.$rw->TR_Track:'').'</cell>';
    		echo ' <cell>'.number_format($rw->BS_Amount,2).'</cell>';
			echo ' <cell>'.date('d/m/Y', strtotime($rw->BS_Date)).'</cell>';
			echo ' <cell><![CDATA[';
			echo '<a href="#" style="text-decoration:none;" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \'No need to Un Block and moved to pending transfer list\');" onclick="preTally.AccountsTeam.deleteJournal(8,0,0,'.$rw->BS_Id.');"><span class="confirmBtn" >UnBlock</span></a>';
			echo ']]></cell>';
    		echo '</row>';
    	}
    } else {

		echo '<row id="0"> <cell></cell> <cell colspan="3"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>