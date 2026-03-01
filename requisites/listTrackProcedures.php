<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$filter = 'TP_Status = 0 OR TP_Status = 1 AND OF_Id = '.$preTally_user_ofid;// TP_Status = 1 
$TrackProcedureObj = new AttestationClass();

if(isset($REQUEST['newProcedure']) && ($REQUEST['newProcedure'] == 'true')) {
    $TrackProcedureObj->newTrackProcedures($preTally_user_id,$preTally_user_ofid);
}
$TrackProcedureObj->listTrackProcedures($filter);
$TRKObj = $TrackProcedureObj->TrackArray;

$ProcessDocumentObj = new AttestationClass();
$ProcessDocumentObj->viewTrackMainProcess($preTally_user_ofid);
$PDObj = $ProcessDocumentObj->TrackArray;
$MPArray = array();
if ($PDObj) {
    foreach ($PDObj as $rw) {
        $MPArray[$rw->APM_Id] = $rw->APM_Title;
    }       
}
function formProcedureTree($process, $MPArray) {
    $processTree = explode(",", $process);
    $processData = '';
    $j = 0;
    foreach ($processTree as &$value) {
        $ATPProcess = $MPArray[$value];
        if($value) {
            if($j != 0) {
                $processData .= '<div class="automateBlockArrow">&nbsp;</div>';
            }
            $processData .= '<div class="automateBlock">'.$ATPProcess.'</div>';
            $j++;
        }
    }
    return $processData;
}
//echo '<pre>';
//print_r($MPArray);
    
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
        <head>
            <column width="40" type="ro" align="center" sort="int"> # </column>
            <column width="80" type="ro" align="left" sort="na">From</column>
            <column width="28" type="ro" align="center" sort="na">#cspan</column>
            <column width="80" type="ro" align="left" sort="na">To</column>
            <column width="28" type="ro" align="center" sort="na">#cspan</column>
            <column width="80" type="ro" align="left" sort="na">Type</column>
            <column width="28" type="ro" align="center" sort="na">#cspan</column>
            <column width="*" type="ro" align="left" sort="na">Attestation Procedures</column>
            <column width="30" type="ro" align="center" sort="na">Process</column>
            <column width="30" type="ro" align="center" sort="na">#cspan</column>
            <column width="30" type="ro" align="center" sort="na">#cspan</column>
            <column width="30" type="ro" align="center" sort="na">#cspan</column>
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
        if ($TRKObj) {
            $i = 1;
            foreach ($TRKObj as $rw) {
                $TP_From = $TP_To = $TP_Certificate = 0;
                if($rw->TP_From) $TP_From = $rw->TP_From.',';
                if($rw->TP_To) $TP_To = $rw->TP_To.',';
                if($rw->TP_Certificate) $TP_Certificate = $rw->TP_Certificate.',';
                
                if($rw->TP_Status == 1) { $statusIcon = 'tick_16.png'; $title = 'Click here to block this procedure'; } else { $statusIcon = 'cross_16.png'; $title = 'Click here to publish this procedure';} 
                echo'<row id="'.$rw->TP_Id.'">
                    <userdata name="TPStatus">'.$rw->TP_Status.'</userdata> 
                    <userdata name="CN_Id">'.$rw->CN_Id.'</userdata> 
                    <userdata name="TP_From">'.$rw->TP_From.'</userdata> 
                    <userdata name="TP_To">'.$rw->TP_To.'</userdata> 
                    <userdata name="TP_Certificate">'.$rw->TP_Certificate.'</userdata> 
                    <userdata name="TP_Comment">'.$rw->TP_Comment.'</userdata> 
                    <cell name="APM_Id">'.$i.'</cell>
                    <cell>'.substr_count($TP_From, ',').' Selected</cell>
                    <cell><![CDATA[<img src="images/icon/expand_16.png" class="pointer; UIToolTip" UITitle="Click here to select \' From country \'" />]]></cell>
                    <cell>'.substr_count($TP_To, ',').' Selected</cell>
                    <cell><![CDATA[<img src="images/icon/expand_16.png" class="pointer; UIToolTip" UITitle="Click here to select \' To country \'" />]]></cell>
                    <cell>'.substr_count($TP_Certificate, ',').' Selected</cell>
                    <cell><![CDATA[<img src="images/icon/expand_16.png" class="pointer; UIToolTip" UITitle="Click here to select \'Certificate type\'" />]]></cell>
                    <cell><![CDATA['.formProcedureTree($rw->TP_Procedures, $MPArray).']]></cell>
                    <cell><![CDATA[<img src="images/icon/arrow_20.png" style="cursor: pointer;" class="UIToolTip" UITitle="Click here to select your process" />]]></cell>
                    <cell><![CDATA[<img src="images/icon/'.$statusIcon.'" style="cursor: pointer;" class="UIToolTip" UITitle="'.$title.'" />]]></cell>
                    <cell><![CDATA[<img src="images/icon/trash.png" style="cursor: pointer;" class="UIToolTip" UITitle="Click here to remove this process" />]]></cell>
                    <cell><![CDATA[<img src="images/icon/note_20.png" style="cursor: pointer;" class="UIToolTip" UITitle="Click here to add comments" />]]></cell>
                </row>';
                $i++;
            }
        }

    echo '</rows>';
?>
