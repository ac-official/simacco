<?php
/**
* Accounts Ledger Listing with filter and pagination
* Created By Bilin @ 11-07-2025
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
$inparams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']	= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$accObj->listAllLedger($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;
//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>
      <userdata name="approve_acl">'.$UserACLObj->approve_account_settings.'</userdata>';

    if (!empty($result_data)) {
    		$transtypes = ['0'=>'Cash and Bank', '1'=>'Cash Only', '2'=>'Bank Only'];
		foreach($result_data as $rw) {
			$is_edit 	= ((($UserACLObj->approve_account_settings == 1 && $UserACLObj->add_account_settings ==1) || ($UserACLObj->add_account_settings == 1 && ($rw->status == 2 || $rw->status == 4) )) ? 1 : 0);
			echo '<row id="'.$rw->id.'">';
			echo '<userdata name="eid">'.$rw->id.'</userdata>'
			.'<userdata name="status">'.$rw->status.'</userdata>'
			.'<userdata name="type">'.$rw->type.'</userdata>'
			.'<userdata name="mgroup_id">'.$rw->mgroup_id.'</userdata>'
			.'<userdata name="office_id">'.$rw->office_id.'</userdata>'
			.'<userdata name="is_office">'.($rw->office_id > 0 ? 1 :0).'</userdata>'
			.'<userdata name="parent_id">'.$rw->parent_id.'</userdata>'
			.'<userdata name="vendor_id">'.$rw->vendor_id.'</userdata>'
			.'<userdata name="title">'.htmlspecialchars_decode($rw->title).'</userdata>'
			.'<userdata name="description">'.htmlspecialchars_decode($rw->description).'</userdata>'
			.'<userdata name="sgroup_id">'.$rw->group_id.'</userdata>'
			.'<userdata name="is_return">'.$rw->is_return.'</userdata>'
			.'<userdata name="trans_type">'.$rw->trans_type.'</userdata>'
			.'<userdata name="is_contra">'.$rw->is_contra.'</userdata>'
			.'<userdata name="less_id">'.$rw->less_id.'</userdata>'
			.'<userdata name="plus_id">'.$rw->plus_id.'</userdata>'
			.'<userdata name="is_same_side">'.$rw->is_same_side.'</userdata>'
			.'<userdata name="is_internal">'.$rw->is_internal.'</userdata>'
			.'<userdata name="is_job_type">'.$rw->is_job_type.'</userdata>'
			.'<userdata name="edit_status">'.$rw->edit_status.'</userdata>';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.$rw->type.'</cell>';
			echo ' <cell>'.$rw->title.'</cell>';
			echo ' <cell>'.$rw->parent_name.'</cell>';
			echo ' <cell>'.$rw->group_name.'</cell>';
			echo ' <cell>'.$rw->status_val.'</cell>';
           
            
            echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
            if ($is_edit == 1) {
            	echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editLedger('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
            }
            if ($rw->description != '') {
            	$viewdesc = '<div> Description: '.htmlspecialchars_decode($rw->description).'</div>';
            } else {
            	$viewdesc = '';
            }
            if ($rw->vendor_id > 0) {
            	$viewdesc .= '<div> Vendor, Debtor or Creditor Name : '.$rw->vendor_name.'</div>';
            } 
            $viewdesc .= '<div> Transaction Type : '.$transtypes[$rw->trans_type].'</div>';
            $viewdesc .= '<div> Last Updated By : '.$rw->created_user.'</div>'
            	.'<div> Last Updated At : '.date('M d, Y', strtotime($rw->created_at)).'</div>'
            	.'<div> Verified By : '.$rw->verified_user.'</div>'
            	.'<div> Verified At : '.date('M d, Y', strtotime($rw->verified_at)).'</div>';
            	echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />';
            
            echo ']]></cell>';

            echo '</row>';			
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>