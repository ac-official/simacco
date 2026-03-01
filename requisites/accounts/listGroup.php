<?php
/**
* Accounts Group Listing with filter (all data fetching)
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

$inparams 				= ['flag'=>1];
// filter inputs processing
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['type']		= (isset($filterData[1])) ? $filterData[1] : "";
$inparams['status']		= (isset($filterData[2])) ? (string)$filterData[2] : "";
$inparams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']	= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$result_data 		= $accObj->ListGroups($inparams);
$total_records 		= (!empty($result_data)) ? count($result_data) : 0;

// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="0">
      <userdata name="Data_Count">'.$total_records.'</userdata>
      <userdata name="approve_acl">'.$UserACLObj->approve_account_settings.'</userdata>';

	if (!empty($result_data)) {

		foreach($result_data as $rw) {
			$is_edit 	= ((($UserACLObj->approve_account_settings == 1 && $UserACLObj->add_account_settings ==1) || ($UserACLObj->add_account_settings == 1 && ($rw->status == 2 || $rw->status == 4) )) ? 1 : 0);
			echo '<row id="'.$rw->id.'">';
			echo '<userdata name="gid">'.$rw->id.'</userdata>'
			.'<userdata name="type">'.$rw->type.'</userdata>'
			.'<userdata name="parent_id">'.$rw->parent_id.'</userdata>'
			.'<userdata name="title">'.htmlspecialchars_decode($rw->title).'</userdata>'
			.'<userdata name="description">'.htmlspecialchars_decode($rw->description).'</userdata>'
			.'<userdata name="status">'.$rw->status.'</userdata>'
			.'<userdata name="edit_status">'.$rw->edit_status.'</userdata>'
			.'<userdata name="is_edit">'.$is_edit.'</userdata>';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.$rw->type.'</cell>';
			echo ' <cell>'.$rw->title.'</cell>';
			echo ' <cell>'.$rw->parent_name.'</cell>';
			echo ' <cell>'.$rw->status_val.'</cell>';
            echo ' <cell>'.$rw->created_user.'</cell>';
            echo ' <cell>'.(($rw->status != 2) ? $rw->verified_user : '-').'</cell>';
            echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
            if ($is_edit == 1) {
            	echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editGroup(this, '.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
            }
            if ($rw->description != '') {
            	echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.htmlspecialchars_decode($rw->description).'\');" />';
            }
            if ($rw->description == '' && $is_edit != 1)  {
            	echo '-';
            }
            echo ']]></cell>';            
            echo '</row>';           
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>