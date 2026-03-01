<?php
/**
* list all pending tracks of selected user.
* Check the user based acl and permissioned user get all tracks
* Created By Bilin @ 31-07-2025
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// include the class files
require_once($BASEPATH . "preTallyClass/TrackClass.php");
// create object for needed class files
$TrackObj 	= new TrackClass();

// pagination and other filters
$inParams 	= [];
$inParams['start'] 		= (isset($_GET["posStart"])) ? (int)$_GET["posStart"]: 0;
$inParams['limit'] 		= (isset($_GET["count"])) ? (int)$_GET["count"]: 50;
$inParams['trackno'] 		= (isset($REQUEST['trackno'])) ? trim($REQUEST['trackno']): '';
$inParams['staff'] 		= (($UserACLObj->close_others_tracks == 1 || $UserACLObj->view_account_settings == 1) && isset($REQUEST['staff'])) ? trim($REQUEST['staff']): '';
$inParams['user_id'] 		= ($UserACLObj->close_others_tracks == 1 || $UserACLObj->view_account_settings == 1) ? 0: $preTally_user_id;
$inParams['off_id']		= $preTally_user_ofid;
$inParams['sortby']		= (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inParams['orderby']		= (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";


// call the function to retrive the data and total count
$sql  				= $TrackObj->listPendingTracks($inParams);
$total_records 			= $TrackObj->totalCount;
$result_data 			= $TrackObj->trackList;
//echo $sql;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inParams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>
      <userdata name="track_close_acl">'.$UserACLObj->close_others_tracks.'</userdata>
      <userdata name="track_view_acl">'.$UserACLObj->view_account_settings.'</userdata>';

    if (!empty($result_data)) {

		foreach($result_data as $rw) {

			echo '<row id="'.$rw->TR_Id.'">';
			echo '<cell>'.$rw->slno.'</cell>';
			echo '<cell>'.$rw->TR_Track.'  - '.$rw->DS_Description.'</cell>';
			echo '<cell>'.date('M d, Y',strtotime($rw->TR_CDate)).'</cell>';
			echo '<cell>'.$rw->full_name.'</cell>';
			if ($rw->US_Id == $preTally_user_id || $rw->blUS_Id == $preTally_user_id || $UserACLObj->close_others_tracks == 1) {
				echo '<cell><![CDATA[<div style="display:flex; cursor:pointer;" onclick="preTally.BalanceSheet.closeTrack('.$rw->TR_Id.');"><img src="images/icon/close.png" style="margin:4px 0;height:13px; padding-right:3px;"/> Close Track</div>]]></cell>';
			} else {
				echo '<cell>-</cell>';
			}			
			echo '</row>';
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell colspan="3"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>