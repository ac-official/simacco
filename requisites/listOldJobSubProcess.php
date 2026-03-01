<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$ProcessDocumentObj = new AttestationClass();
$ProcessDocumentObj->viewTrackDocumentProcess($preTally_user_ofid);
$PDObj      = $ProcessDocumentObj->TrackArray;
$ids        = array();

$ProcessDocumentObj->getDetails('tracks_old_jobs', 'APS_Id', ' WHERE TR_Id = '.$REQUEST['TR_Id']); 
$processIDs = json_decode(json_encode($ProcessDocumentObj->DataArray), true);  // object array to normal array

foreach($processIDs as $Ids) {
    $ids[]  =   $Ids['APS_Id'];
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
        <head>
            <column width="50" type="ch" align="center" sort="na"> SlNo </column>
            <column width="120" type="ro" align="left" sort="na">#select_filter_strict</column>
            <column width="*" type="ro" align="left" sort="na">#text_filter_inc</column>
            <column width="120" type="ed" align = "center" >Sub Process Count</column>
            <settings>
                <colwidth>px</colwidth>
            </settings>
            <beforeInit> 
                <call command="setSkin">
                    <param>dhx_skyblue</param>
                </call> 
                <call command="setImagePath">
                    <param>assets/grid/codebase/imgs/</param>
                </call> 
                <call command="enableSmartRendering">
                    <param>false</param>
                </call> 
            </beforeInit> 
        </head>';
        if ($PDObj) {
            foreach ($PDObj as $rw) {
                $value  = (in_array($rw->APS_Id, $ids)) ? '1' : '0';
                $counts = array_count_values($ids);                
                echo '<row  id="'.$rw->APS_Id.'">
                    <cell name="APM_Id">'.$value.'</cell>
                    <cell name="APM_Title">'.htmlentities($rw->APM_Title).'</cell>
                    <cell name="APS_Title">'.htmlentities($rw->APS_Title).'</cell>
                    <cell>'.$counts[$rw->APS_Id].'</cell>
                </row>';
            }
        }
echo '</rows>';
?>