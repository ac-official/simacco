<?php
/**
* map old item amount to new ledger 
* Mapped ledger and item based data shown in a list and the permissioned user confirm or edit 
* single or all data confirm at one click
* Created By bilin 04-08-2025
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
$inparams['ledgers']    = (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";
$inparams['date']		= (isset($filterData[2]) && $filterData[2] != "") ? date('Y-m-d',strtotime($filterData[2])) : "";
$inparams['amt']		= (isset($filterData[3]) && $filterData[3] != "") ? (float)$filterData[3] : 0;
// sort fields
$inparams['sortby']     = (isset($REQUEST['sort']) && $REQUEST['sort'] != "") ? (string)$REQUEST['sort'] : "date";
$inparams['orderby']    = (isset($REQUEST['order']) && $REQUEST['order'] != "") ? (string)$REQUEST['order'] : "ASC";

//find the details based on the parameters provided
if ($UserACLObj->approve_ledger_amount == 1) {
	$accObj->listItemJournal($inparams);
	$total_records 		= $accObj->data_total;
	$result_data 		= $accObj->data_list;
	//echo $accObj->sql_query;
} else { // no permission to save 
	$total_records 		= 0;
	$result_data 		= [];
}


// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';
    if (!empty($result_data)) {
    	foreach($result_data as $rw) {

    		echo '<row id="'.$rw->BS_Id.'">';
    		if ($rw->ledger_id > 0) { // mapped item have allow single click saving
    			echo '<userdata name="bsid">'.$rw->BS_Id.'</userdata>';
				echo ' <cell><![CDATA[ <input type="checkbox" name="itmchkbox" class="itmchkbox" value="'.$rw->BS_Id.'" id="chkitem'.$rw->BS_Id.'" /> ]]></cell>';
    		} else {
    			echo '<userdata name="bsid">0</userdata>';
				echo ' <cell>-</cell>';
    		}			
			echo ' <cell>'.$rw->IT_Name.' - ('.($rw->BS_BranchTo > 0 ? $rw->branch_name: $rw->LC_Name).') '.($rw->TR_Id > 0 ? ' - '.$rw->TR_Track:'').' '.$rw->itemdesc .'</cell>';
			echo ' <cell>'.$rw->ledger.'</cell>';
			echo ' <cell>'.number_format($rw->BS_Amount,2).'</cell>';
			echo ' <cell>'.date('d/m/Y', strtotime($rw->BS_Date)).'</cell>';

			echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
            echo '<div style="width:45px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.loadItemdataForm(0,'.$rw->BS_Id.',0);"><img src="images/icon/pencil.png" style="margin:2px 0;height:14px;"/> Edit</div>';

            echo '<img style="cursor:pointer;margin-left:1px;" src="images/icon/cross.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \'No need to Transfer / Block\');" onclick="preTally.AccountsTeam.deleteJournal(4,0,0,'.$rw->BS_Id.');" />';

        	$viewdesc = '<div> <b>Item Name :</b> '.$rw->IT_Name.'</div>'
        	.'<div> <b>Item Type :</b> '.($rw->MH_Type == 1 ? 'Income' : 'Expense').'</div>'
        	.'<div> <b>Branch From :</b> '.$rw->LC_Name.'</div>'
        	.'<div> <b>Description :</b> '.$rw->itemdesc.'</div>'
        	.'<div> <b>Branch To : </b>'.($rw->BS_BranchTo > 0 ? $rw->branch_name: $rw->LC_Name).'</div>'
        	.'<div> <b>Date : </b>'.date('M d, Y', strtotime($rw->BS_Date)).'</div>'
        	.'<div> <b>Amount : </b>'.number_format($rw->BS_Amount,2).'</div>'
        	.'<div> <b>Amount Type : </b>'.($rw->amt_type == 1 ? 'Bank' : 'Cash').'</div>'
        	.'<div> <b>Ledger Name : </b>'.($rw->ledger_id > 0 ? $rw->ledger : 'Item Not Mapped').'</div>';
        	
        	echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />';
            echo ']]></cell>';
    		
			echo '</row>';
    	}
    } else {

		echo '<row id="0"> <cell></cell> <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>