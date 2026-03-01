<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$ProcessDocumentObj = new AttestationClass();
$ProcessDocumentObj->viewTrackDocumentProcess($preTally_user_ofid);
$PDObj = $ProcessDocumentObj->TrackArray;
$processIDs = array();
$ids = trim(mysqli_real_escape_string($GLOBALS['con'],$REQUEST['ids'])); 
$processIDs = explode(',', $ids);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
        <head>
            <column width="50" type="ch" align="center" sort="na"> SlNo </column>
            <column width="120" type="ro" align="left" sort="na">#select_filter_strict</column>
            <column width="*" type="ro" align="left" sort="na">#text_filter_inc</column>
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
            <afterInit>

            </afterInit>

        </head>';
        if ($PDObj) {
            $i = 1;
            foreach ($PDObj as $rw) {
                if(in_array($rw->APM_Id.'_'.$rw->APS_Id, $processIDs)) {
                    $value = '1';
                    $style = ' style = "background-color: red; color:#FFF; font-weight:bold;" ';
                } else {
                    $value =  '0';
                    $style = ' style = "background-color: none; color:#000; font-weight:normal;" ';
                }
                echo'<row '.$style.' id="'.$rw->APM_Id.'_'.$rw->APS_Id.'">
                    <cell name="APM_Id">'.$value.'</cell>
                    <cell name="APM_Title">'.htmlentities($rw->APM_Title).'</cell>
                    <cell name="APS_Title">'.htmlentities($rw->APS_Title).'</cell>
                </row>';
            }
        }

echo '</rows>'
?>
