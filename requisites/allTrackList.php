<?php
/**
* All track number based list with job amount, income, expense, balance amount etc
* Track listing based on the job date
* Created at 22-05-2025 By Bilin 
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
$inParams['from_date'] 	= (isset($REQUEST['fromdate']) && $REQUEST['fromdate'] != "") ? date("Y-m-d", strtotime($REQUEST['fromdate'])): '';
$inParams['to_date'] 	= (isset($REQUEST['todate']) && $REQUEST['todate'] != "") ? date("Y-m-d", strtotime($REQUEST['todate'])): date('Y-m-d');
$inParams['off_id']     = (isset($REQUEST['OFID'])) ? $REQUEST['OFID']:0;
$inParams['track_no']   = (isset($REQUEST['track_no']) && $REQUEST['track_no'] != '') ? trim($REQUEST['track_no']):''; //21-01-2026
$inParams['date_type']	= (isset($REQUEST['date_type'])) ? (int)$REQUEST['date_type']:0; //22-01-2026
// call the function to retrive the data and total count
$TrackObj->listAllTracks($inParams);
$totalcount 			= $TrackObj->totalCount;
$allTracks 				= $TrackObj->trackList;
//echo $TrackObj->sql;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$totalcount.'" pos="'.$inParams['start'].'">
      <userdata name="totalCount">'.$totalcount.'</userdata>';
    if (!empty($allTracks)) {

    	foreach($allTracks as $rw) {
            $bgcolor = ($rw->unpaid_balance >= 0) ? '' :'style="color:#ef8429;"';
    		echo '<row id="'.$rw->TR_Id.'" '.$bgcolor.'>';
    			echo '<cell>'.$rw->slno.'</cell>';
    			echo '<cell><![CDATA[<a href="#" onmouseover="preTally.Settings.showLabel(this,\'Click here to show details\');" onmouseout="preTally.Settings.hideLabel(this);" onclick="preTally.ManageTracks.viewTrackEntries(\''.$rw->TR_Id.'\',this);"> '.$rw->TR_Track.'</a>]]></cell>';
                echo '<cell>'.date('d-m-Y', strtotime($rw->BS_Date)).'</cell>';
    			echo '<cell>'.date('d-m-Y', strtotime($rw->job_Date)).'</cell>';
    			echo '<cell>'.$rw->job_amount.'</cell>';
    			echo '<cell>'.$rw->job_expense.'</cell>';
    			echo '<cell>'.$rw->total_income.($rw->unapprove_amt > 0 ? ' (+'.$rw->unapprove_amt.')' :'').'</cell>';
    			echo '<cell>'.$rw->total_expense.'</cell>';
    			echo '<cell>'.$rw->balance_unpaid.'</cell>';
    			echo '<cell>'.$rw->after_income.'</cell>';
    		echo '</row>';
    	}
    } else { // no records found 
    	echo '<row id="0"><cell></cell> <cell colspan="9" style="font-size:16px; font-variant:small-caps;color:#0979B1; text-align:center !important; font-family: serif; padding-top: 10px;">No records found.</cell></row>';
    }


echo '</rows>';
?>