<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$SupDocumentObj = new AttestationClass();
$SupDocumentObj->viewSupportingDocuments($preTally_user_ofid);
$SDObj = $SupDocumentObj->TrackArray;
$SupDocArray = array();
if($REQUEST['id']) {   // on edit of jobs
    $ASD_Id = $SupDocumentObj->getValue('attestation_job_supporting_documents', 'ASD_Id ', " WHERE AJ_Id = ".$REQUEST['id']);
    $SupDocArray = explode(',', $ASD_Id);
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
        <head>
            <column width="50" type="ch" align="center" sort="na"> SlNo </column>
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
                <call command="enableColSpan">
                    <param>true</param>
                </call> 
            </beforeInit> 
            <afterInit>
            </afterInit>
        </head>';

        if ($SDObj) {
            $i = 1;
            foreach ($SDObj as $rw) {
                if(in_array($rw->ASD_Id,$SupDocArray)) {
                    $value = '1';
                    $style = ' style = "background-color: red; color:#FFF; font-weight:bold;" ';
                } else {
                    $value = '0';
                    $style = ' style = "background-color: none; color:#000; font-weight:normal;" ';
                }
                
                echo'<row '.$style .' id="'.$rw->ASD_Id.'">';
                    echo '<cell name="ASD_Id" >'.$value. '</cell>
                    <cell name="ASD_Document" >'.htmlentities($rw->ASD_Document).'</cell>
                </row>';
            }
        } else {
            echo '<row id="0"><cell colspan = "2"  type ="ro"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell></row>';
        }

echo '</rows>'
?>
