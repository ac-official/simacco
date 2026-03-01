<?php
/**
* Accounts Ledger and Old Item mapping list with filter and pagination
* Created By Bilin @ 16-07-2025
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
$inparams['start'] 	=  (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	=  (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
// filter inputs processing
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['type']		= (isset($filterData[1])) ? $filterData[1] : "";
$inparams['status']		= (isset($filterData[2])) ? (string)$filterData[2] : "";
$inparams['group_id']   = (isset($filterData[3]) && $filterData[3] > 0) ? (int)$filterData[3] : 0;
$inparams['search_item']= (isset($filterData[4]) && $filterData[4] != "") ? trim($filterData[4]) : "";
$inparams['item_shead'] = (isset($filterData[5]) && $filterData[5] > 0) ? (int)$filterData[5] : 0;
$inparams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']	= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$accObj->listMapLedger($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;

//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>
      <userdata name="approve_acl">'.$UserACLObj->approve_account_settings.'</userdata>';
    if (!empty($result_data)) {
    	foreach($result_data as $rw) {
    		//check the edit permisssion.
			$is_edit 	= ((($UserACLObj->approve_account_settings == 1 && $UserACLObj->add_account_settings ==1) || ($UserACLObj->add_account_settings == 1 && ($rw->status == 2 || $rw->status == 4) )) ? 1 : 0);

			echo '<row id="'.$rw->id.'">';
			echo '<userdata name="eid">'.$rw->id.'</userdata>'
			.'<userdata name="status">'.$rw->status.'</userdata>'
			.'<userdata name="type">'.$rw->type.'</userdata>'
			.'<userdata name="mgroup_id">'.$rw->mgroup_id.'</userdata>'
			.'<userdata name="sgroup_id">'.$rw->group_id.'</userdata>'
			.'<userdata name="office_id">'.$rw->office_id.'</userdata>'
			.'<userdata name="mledger_id">'.$rw->parent_id.'</userdata>'
			.'<userdata name="sledger_id">'.$rw->ledger_id.'</userdata>'
			.'<userdata name="subhead_id">'.$rw->SH_Id.'</userdata>'
			.'<userdata name="item_id">'.$rw->item_id.'</userdata>'
			.'<userdata name="edit_status">'.$rw->edit_status.'</userdata>';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.$rw->type.'</cell>';
			echo ' <cell>'.$rw->group_name.'</cell>';
			echo ' <cell>'.$rw->ledger_name.'</cell>';
			echo ' <cell>'.$rw->item_name.'</cell>';
			echo ' <cell>'.$rw->subhead_name.'</cell>';
			echo ' <cell>'.$rw->status_val.'</cell>';           
            
            echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
            if ($is_edit == 1) {
            	echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editMapLedger('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
            }
        	$viewdesc = '<div> Last Updated By : '.$rw->created_user.'</div>'
        	.'<div> Last Updated At : '.date('M d, Y', strtotime($rw->created_at)).'</div>'
        	.'<div> Verified By : '.$rw->verified_user.'</div>'
        	.'<div> Verified At : '.date('M d, Y', strtotime($rw->verified_at)).'</div>';
        	echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />';

            echo ']]></cell>';

            echo '</row>';
		}
    } else {

		echo '<row id="0"> <cell></cell> <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>