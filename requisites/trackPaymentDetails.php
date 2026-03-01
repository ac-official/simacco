<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj     = new AttestationClass();
$AJ_Id      = $REQUEST['AJ_Id'];    // Job ID
$AttObj->getDetails(" attestation_job_receipts " ,"AJR_Id,AJ_Id,AJR_CRAmount,AJR_CDate", " WHERE AJ_Id = ".$AJ_Id ." ORDER BY AJR_CDate DESC");
$DocObj     = $AttObj->DataArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <userdata name="AJR_Id">'.$DocObj[(count($DocObj)-1)]->AJR_Id.'</userdata>
    <userdata name="AJ_Id">'.$AJ_Id.'</userdata>
    <head>
        <column width="40" type="ro" align="center" sort="int"> SlNo </column>
        <column width="*" type="ro" align="left" sort="int"> Description </column>
        <column width="200" type="ro" align="left" sort="str">Amount</column>
        <column width="100" type="ro" align="center" sort="str">Date </column>
        <column width="100" type="ro" align="center" sort="str"></column>
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
    </head>';
if($DocObj) {
    $j = 1;
    foreach($DocObj as $rw) {
        echo '<row id="'.$j.'">
                <cell>'.$j.'</cell>
                <cell>Amount Received </cell>
                <cell>'.$rw->AJR_CRAmount.'</cell>
                <cell>'.date("d-m-Y",strtotime($rw->AJR_CDate)).'</cell>
                <cell><![CDATA[<a style="font-weight:bold;text-decoration:underline;cursor:pointer;color:#3366FF;" onclick="preTally.Track.downloadToPDF(\'trackCashReceiptPDF.php\','.$AJ_Id.','.$rw->AJR_Id.',1);">Cash Receipt</a>]]></cell>
              </row>';
        $j++;
    }
} else {
    echo '<row id="0"> 
        <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
    </row>';
}
echo'</rows>';