<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();


$AttObj->getDetails('attestation_process_sub_expense_details APSE', 
                    'APSE.*', 
                        'WHERE APS_Id = "'.$REQUEST['APSID'].'" '
                   );
$ApseObj = $AttObj->DataArray;

//print_r($ApseObj);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows >';
        echo '<head>
            <column width="*" type="ro" align="center" sort="na"> SlNo </column>
            <column width="100" type="dhxCalendarA" align="left" sort="na"> First Date </column>
            <column width="100" type="dhxCalendarA" align="left" sort="na"> Last Date </column>
            <column width="80" type="ed" align="left" sort="na"> Statutory Amount </column>
            <column width="80" type="ed" align="left" sort="na"> Extra Amount </column>
            <column width="80" type="ed" align="left" sort="na"> Courier Amount </column>
            <column width="80" type="ed" align="left" sort="na"> Travelling Amount </column>
            <column width="80" type="ed" align="left" sort="na"> Manpower Amount </column>
            <column width="80" type="ed" align="left" sort="na"> Service Amount </column>
            <column width="40" type="ch" align="center" sort="na"></column>
                       
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
    if($ApseObj) {
        $j=1;
        foreach($ApseObj as $rw) {
            $FDate = new DateTime($rw->APSE_FDate); 
            $LDate = new DateTime($rw->APSE_LDate); 
            
            echo '<row id="'.$rw->APSE_Id.'">

                    <cell title=" " >'.$j.'</cell>
                    <cell title=" " >'.$FDate->format('d/m/Y').'</cell>
                    <cell title=" " >'.$LDate->format('d/m/Y').'</cell>
                    <cell title=" " >'.$rw->APSE_StatutoryNAmt.'</cell>
                    <cell title=" " >'.$rw->APSE_ExtraNAmt.'</cell>
                    <cell title=" " >'.$rw->APSE_CourierNAmt.'</cell>
                    <cell title=" " >'.$rw->APSE_TravellingNAmt.'</cell>
                    <cell title=" " >'.$rw->APSE_ManpowerNAmt.'</cell>
                    <cell title=" " >'.$rw->APSE_ServiceNAmt.'</cell>
                    <cell></cell>';
            
            echo '</row>';
            $j++;
        }
    } else {
        echo '<row id="0"> 
            <cell colspan="10"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>