<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AJ_Id  = $REQUEST['AJ_Id'];    // Job ID

$AttObj->getIdWiseName('CN_Id','CN_Name','countries','1');
$CNObj  = $AttObj->DataArray;

$AttObj->getIdWiseName('ASD_Id','ASD_Document','attestation_supporting_documents','ASD_Status = 1 AND OF_Id ="'.$preTally_user_ofid.'"');
$SupDocObj  = $AttObj->DataArray;

$AttObj->jobDocumentDetails($AJ_Id);
$DocObj      =  $AttObj->DataArray;
$ProcessObj  =  $AttObj->SubArray;
$VisaTypeNameArray = array('','Job Visa','Visiting Visa','Business Visa','Family Visa','Transit Visa','Tourist Visa');
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <head>
        <column width="40" type="ro" align="center" sort="na"> SlNo </column>
        <column width="*" type="ro" align="left" sort="na"> Certificate Details </column>
        <column width="*" type="ro" align="left" sort="na"> Options </column>
        <column width="*" type="ro" align="left" sort="na">Supporting Documents</column>
        <column width="150" type="ro" align="left" sort="na">Added Details</column>
        <column width="70" type="ed" align="right" sort="na">Amount</column>
        <column width="35" type="ro" align="center" sort="na">Process</column>
        <column width="35" type="ro" align="center" sort="na">#cspan</column>
        <column width="35" type="ro" align="center" sort="na">#cspan</column>
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
        </beforeInit> 
    </head>';
if($DocObj) {
    $j = 1;
    foreach($DocObj as $rw) {
        $subProcess     = "";
        $processID      = array();
        $processOrder   = array();
        foreach($ProcessObj[$rw->AJD_Id] as $proc) { 
           $subProcess  .= $proc->APS_Title."<br />";
           array_push($processID, $proc->APS_Id);
           array_push($processOrder,$proc->AJS_Order);
        }

        $supportingDocArray    = explode(',', $rw->ASD_Id);
        $supDocIds      = array();
        $sup_Docs       = '' ;
        foreach ($supportingDocArray as $key=>$value) {
            $sup_Docs   .= $SupDocObj[$value].'<br />';
            array_push($supDocIds, $value);
        }
        
        $certificateDetails    = $CNObj[$rw->AJD_VisitingCNId].', '.$VisaTypeNameArray[$rw->AJD_VisaType].'<br />'.
                                  $rw->ADOC_Document.', '.$rw->AJD_Year.'<br />'.
                                  $rw->APS_Title.', '.$CNObj[$rw->AJD_IssuingCNId].'<br />';
             
        echo '<row id="'.$j.'">
                <userdata name="AJD_Id">'.$rw->AJD_Id.'</userdata>
                <userdata name="ADOC_Id">'.$rw->ADOC_Id.'</userdata>
                <userdata name="ST_Id">'.$rw->ST_Id.'</userdata>
                <userdata name="APS_Id">'.$rw->APS_Id.'</userdata>
                <userdata name="AE_LastProcess">'.$rw->AJD_LastProcess.'</userdata>
                <userdata name="AJD_VisitingCNId">'.$rw->AJD_VisitingCNId.'</userdata>
                <userdata name="AJD_VisaType">'.$rw->AJD_VisaType.'</userdata>
                <userdata name="AJD_IssuingCNId">'.$rw->AJD_IssuingCNId.'</userdata>
                <userdata name="AJD_Amount">'.$rw->AJD_Amount.'</userdata>
                <userdata name="AJD_Year">'.$rw->AJD_Year.'</userdata>
                <userdata name="AJD_UniqueNo">'.$rw->AJD_UniqueNo.'</userdata>
                <userdata name="processIDs"><![CDATA['.json_encode($processID).']]></userdata>
                <userdata name="supDocIds"><![CDATA['.json_encode($supDocIds).']]></userdata>
                <userdata name="AA_Comments">'.$rw->AJD_Comment.'</userdata>
                <userdata name="AE_Comments">'.$rw->AJD_Remarks.'</userdata>
                    
                <cell title=" ">'.$j.'</cell>
                <cell title=" "><![CDATA['.$certificateDetails.']]></cell>
                <cell title=" " ><![CDATA['.$subProcess.']]></cell>
                <cell title=" "><![CDATA['.$sup_Docs.']]></cell>
                <cell title=" "><![CDATA['.$rw->LC_Name.'<br />'.date("d-m-Y",strtotime($rw->AJD_CDate)).']]></cell>
                <cell title=" ">&#x20b9; '.$rw->AJD_Amount.'</cell>'; 
                if($rw->AJD_Status== 1) { // 1: New  2: Underprocess  3: Transit
                    echo'<cell><![CDATA[<img src="images/icon/cross.png" style="cursor:pointer; margin:10px 0;" class="UIToolTip" UITitle="Delete Job Process" onclick="preTally.Track.removeATPOption('.$j.',\'U\');" />]]></cell>';
                }else { //4: Process Completed 5: Delivered
                    echo'<cell title=" "></cell>';
                }
                echo '<cell title=" " ><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" class="UIToolTip" UITitle="Information On Process" onclick="preTally.Track.showAutomateProcessComment(this,'.$j.',\'listDocs\');" />]]></cell>';
                echo '<cell title=" " ><![CDATA[<img src="images/icon/note_20.png" style="margin:2px 0; cursor:pointer;" class="UIToolTip" UITitle="Additional Comments On Process" onclick="preTally.Track.addOptionComment(this,'.$j.',\'listDocs\');" />]]></cell>';
            echo '</row>';
        $j++;
    }
}
 echo'</rows>';
 ?>