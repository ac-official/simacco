<?php
/**
 * Track based expense list based on the date, company and track number
 * Created by Bilin @ 06-01-26
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
$inParams['from_date'] 	      = (isset($REQUEST['from'])) ? date("Y-m-d", strtotime($REQUEST['from'])): '';
$inParams['to_date'] 	      = (isset($REQUEST['to'])) ? date("Y-m-d", strtotime($REQUEST['to'])): date('Y-m-d');
$inParams['off_id']		= (isset($REQUEST['OFID'])) ? $REQUEST['OFID']:0;
// other filters
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inParams['track_no']	      = (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inParams['search']		= (isset($filterData[1]) && $filterData[1] != "") ? trim($filterData[1]) : "";
$inParams['amount']		= (isset($filterData[2])) ? (float)$filterData[2] : '';
// sort fields
$inParams['sortby']           = (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inParams['orderby']          = (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

// call the function to retrive the data and total count
$TrackObj->trackExpenseList($inParams);
$totalcount 			= $TrackObj->totalCount;
$allTracks 				= $TrackObj->trackList;
//echo $TrackObj->sql;
// process the data and display the grid
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$totalcount.'" pos="'.$inParams['start'].'">
      <userdata name="totalAmt">'.round($TrackObj->totalAmt, 2).'</userdata>
      <userdata name="totalCount">'.$totalcount.'</userdata>';
    if (!empty($allTracks)) {

    	foreach($allTracks as $rw) {
            echo '<row id="'.$rw->BS_Id.'">';
                  echo '<cell>'.$rw->slno.'</cell>';
                  echo '<cell>'.date('d-m-Y', strtotime($rw->BS_Date)).'</cell>';
                  echo '<cell><![CDATA[<a href="#" onmouseover="preTally.Settings.showLabel(this,\'Click here to show details\');" onmouseout="preTally.Settings.hideLabel(this);" onclick="preTally.ManageTracks.viewTrackEntries(\''.$rw->TR_Id.'\',this);"> '.$rw->TR_Track.'</a>]]></cell>';
                  echo '<cell>'.$rw->BS_Amount.'</cell>';
                  echo '<cell><![CDATA['.htmlentities($rw->IT_Name.' -  '.$rw->DS_Description).']]></cell>';
            echo '</row>';
	}
    } else { // no records found 
    	echo '<row id="0"> <cell colspan="5" style="font-size:16px; font-variant:small-caps;color:#0979B1; text-align:center !important; font-family: serif; padding-top: 10px;">No records found.</cell></row>';
    }
echo '</rows>';
?>