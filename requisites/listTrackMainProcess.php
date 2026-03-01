<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$ProcessDocumentObj = new AttestationClass();
$ProcessDocumentObj->viewTrackMainProcess($preTally_user_ofid);
$PDObj = $ProcessDocumentObj->TrackArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
        <head>
            <column width="40" type="ro" align="center" sort="int"> # </column>
            <column width="*" type="ro" align="left" sort="na">Process</column>
            <column width="70" type="ro" align="center" sort="na">Add</column>
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
                echo'<row id="'.$rw->APM_Id.'">
                    <cell name="APM_Id">'.$i.'</cell>
                    <cell name="APM_Title">'.htmlentities($rw->APM_Title).'</cell>
                    <cell name="APS_Title"><![CDATA[<img src="images/icon/arrow_right.png" onclick="javascript:preTally.Track.TAPManagePopUpAddRow('.$rw->APM_Id.');" style="cursor: pointer;" />]]></cell>
                </row>';
                $i++;
            }
        }

    echo '</rows>';
?>
