<?php
/**
 * List all temporary time adjusted users list with full details
 * Created By Bilin 08-12-2025
*/
// class define and declaration
include_once($BASEPATH . "preTallyClass/UserClass.php");
$UserObj = new UserClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$inparams 				= ['office_id'=>$preTally_user_ofid];
// pagination limit and start parameters 
$inparams['start'] 		= (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 		= (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 100;
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['status']		= (isset($filterData[1])) ? (string)$filterData[1] : '1';
// sort fields
$inparams['sortby']     = (isset($REQUEST['sort'])) ? (string)$REQUEST['sort'] : "";
$inparams['orderby']    = (isset($REQUEST['order'])) ? (string)$REQUEST['order'] : "";

$UserObj->selectCompanySettings($preTally_user_ofid);
$com_total_time	= round(abs(strtotime($cmpnySettings['CS_OfficeEnds']) - strtotime($cmpnySettings['CS_OfficeStart'])) / 60,2);
$inparams['worktime']	= $com_total_time;
// find the results from the data base based on the filters
$retDatas 		= $UserObj->listTimeAdjustedUser($inparams); 
$total_data 	= $retDatas['total'];
$list_data 		= $retDatas['list'];
$approvedlist	= $retDatas['approved'];
$sql 			= $retDatas['sql'];

// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_data.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_data.'</userdata>';
    if (!empty($list_data)) {
    	foreach($list_data as $rw) {
    		$cuhourss   = (int)($rw->US_WrkHours/60);
		$cuminutes  = $rw->US_WrkHours -  $cuhourss*60;
		$cuminutes  = ($cuminutes < 10) ? "0".$cuminutes:$cuminutes;
		$cuhourss   = ($cuhourss < 10) ? "0".$cuhourss:$cuhourss;

    		echo '<row id="adj'.$rw->slno.'">';
    		echo ' <cell>'.$rw->slno.'</cell>';
    		echo ' <cell>'.$rw->US_FName.' '.$rw->US_LName.' '.$rw->LC_Name.'</cell>';
    		echo ' <cell>'.$cuhourss.':'.$cuminutes.'</cell>';
    		echo ' <cell>'.$rw->start_date.'</cell>';
    		echo ' <cell>'.$rw->end_date.'</cell>';
    		echo ' <cell style="display: flex;align-items: center;justify-content: space-between;">';
    		if ($rw->reason != "") { echo '<![CDATA[
                <div style="width:85%; overflow:hidden;text-overflow: ellipsis;">
                    '. htmlspecialchars(html_entity_decode($rw->reason), ENT_QUOTES).'
                </div>
                <div style="margin-right: 5%;">
                    <img src="images/icon/info_18.png" onmouseover="preTally.Settings.showLabel(this,\''.htmlspecialchars(html_entity_decode($rw->reason), ENT_QUOTES).'\');" onmouseout="preTally.Settings.hideLabel(this);"/>
                </div>
            	]]>';
      	}
            echo '</cell>';
    		echo ' <cell>'.($rw->created_by > 0 ? $approvedlist[$rw->created_by] : "").'</cell>';
    		echo ' <cell>'.(($rw->is_active == 1 || $rw->is_active == "")  ? 'Active' : 'Completed').'</cell>';
    		echo '</row>';
    	}
    } else {
		echo '<row id="0"> <cell></cell> <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>