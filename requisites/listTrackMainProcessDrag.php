<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$processProcedureObj = new AttestationClass();
$processProcedureObj->listProcedureProcess($REQUEST['TPID']);
$PPObj = $processProcedureObj->TrackArray;
$processProcedureObj->viewTrackMainProcess($preTally_user_ofid);
$MPObj = $processProcedureObj->TrackArray;
if ($MPObj) {
    foreach ($MPObj as $rw) {
        $processObj[$rw->APM_Id] = $rw->APM_Title;
    }
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
        <head>
            
            <column width="50" type="ro" align="center" sort="na"> # </column>
            <column width="*" type="ro" align="left" sort="na">Process</column>
            <column width="70" type="ro" align="center" sort="na">Remove</column>
            
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
        if($PPObj[0]->TP_Procedures) {
            $i = 1;
            $count = 100000;
            $MPIDs = explode(",", $PPObj[0]->TP_Procedures);
            for($j = 0; $j < count($MPIDs); $j++){
                echo'<row id="'.$count.'">
                        <userdata name="processID">'.$MPIDs[$j].'</userdata>
                        <cell>'.$i.'</cell>
                        <cell>'.$processObj[$MPIDs[$j]].'</cell>
                        <cell></cell>
                    </row>';
                    $i++;
                    $count++;
            }
        }

    echo '</rows>';
?>
