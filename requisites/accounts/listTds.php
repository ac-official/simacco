<?php
/**
* Accounts TDS Listing with filter and pagination
* Created By Bilin @ 02-09-2025
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
$inparams['start'] 		=  (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 		=  (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;
// filter inputs processing
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['name']			= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['status']		= (isset($filterData[1])) ? (string)$filterData[1] : "";
$inparams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']		= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

//find the details based on the parameters provided
$accObj->listTds($inparams);
$total_records 		= $accObj->data_total;
$result_data 		= $accObj->data_list;
//echo $accObj->sql_query;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>
      <userdata name="approve_acl">'.$UserACLObj->approve_account_settings.'</userdata>
      <userdata name="status_type">2</userdata>';

      if (!empty($result_data)) {

		foreach($result_data as $rw) {

			$is_edit 	= ($UserACLObj->approve_account_settings == 1 || $UserACLObj->add_account_settings ==1) ? 1 : 0;
			echo '<row id="'.$rw->id.'">';
			echo '<userdata name="eid">'.$rw->id.'</userdata>'
			.'<userdata name="status">'.$rw->status.'</userdata>'
			.'<userdata name="percentage">'.$rw->percentage.'</userdata>'
			.'<userdata name="section">'.$rw->section.'</userdata>'
			.'<userdata name="code">'.$rw->code.'</userdata>'
			.'<userdata name="start_date">'.$rw->start_date.'</userdata>'
			.'<userdata name="rule_name">'.htmlspecialchars_decode($rw->rule_name).'</userdata>';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.htmlentities($rw->rule_name).'</cell>';
			echo ' <cell>'.$rw->section.' - '.$rw->code.'</cell>';
			echo ' <cell>'.$rw->percentage.'%</cell>';
			echo ' <cell>'.date("d M Y",strtotime($rw->start_date)).'-'.($rw->end_date != '' ? date("d M Y",strtotime($rw->end_date)) : 'Till').'</cell>';
			echo ' <cell>'.$rw->status_val.'</cell>';           
            
            echo ' <cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[';
            if ($is_edit == 1) {
            	echo '<div style="width:55px; display:flex; cursor:pointer;" onclick="preTally.AccountsTeam.editTds('.$rw->id.');"><img src="images/icon/pencil.png" style="margin:2px 0;height:13px;"/> Edit</div>';
            }
            $viewdesc = '';
            if ($rw->updated_at != '') {
            	$viewdesc .= '<div> Last Updated By : '.$rw->updated.'</div>'
            	.'<div> Last Updated At : '.date('M d, Y', strtotime($rw->updated_at)).'</div>';
            }
            $viewdesc .= '<div> Created By : '.$rw->created.'</div>'
            	.'<div> Created At : '.date('M d, Y', strtotime($rw->created_at)).'</div>';
            	echo '<img src="images/icon/info_18.png" onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$viewdesc.'\');" />';
            
            echo ']]></cell>';

            echo '</row>';
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>