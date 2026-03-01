<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$AttObj = new AttestationClass();

$AttObj->getIdWiseName('LC_Id','LC_Name','locations',' LC_Status != 5');
$LCObj  = $AttObj->DataArray;
$AttObj->getIdWiseName('CN_Id','CN_Name','countries','1');
$CNObj  = $AttObj->DataArray;
$AttObj->getIdWiseName('ADOC_Id','ADOC_Document','attestation_documents','ADOC_Status = 1');
$AdocObj  = $AttObj->DataArray;
//$AttObj->getIdWiseName('AAUTH_Id','AAUTH_Authority','attestation_authorities','AAUTH_Status = 1');
//$AuthObj  = $AttObj->DataArray;
$AttObj->getIdWiseName('APS_Id','APS_Title','attestation_process_sub','APS_Status = 1 AND OF_Id ="'.$preTally_user_ofid.'"');
$SubProcObj = $AttObj->DataArray;
$AttObj->getIdWiseName('ASD_Id','ASD_Document','attestation_supporting_documents','ASD_Status = 1 AND OF_Id ="'.$preTally_user_ofid.'"');
$SupDocObj  = $AttObj->DataArray;
//print_r($SubProcObj);
$filterData = explode(",",$REQUEST['filter']);
$filter = ' AE.AE_Status != 3 AND US.OF_Id = "'.$preTally_user_ofid.'" ';
if($filterData[0] != '') {
    $filter .='AND AE.AE_Name = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    $filter .='AND AE.AE_Mobile = "'.$filterData[1].'"' ;
}
if($filterData[2] != ''){
    $filter .='AND AE.AE_Email = "'.$filterData[2].'"';
}

$AttObj->listCandidateProcessOptions($filter);
$ProcessObj = $AttObj->DataArray;
//print_r($ProcessObj);

$VisaTypeNameArray = array('','Job Visa','Visiting Visa','Business Visa','Family Visa','Transit Visa','Tourist Visa');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
       $j = 1;
    if($ProcessObj) {

        foreach($ProcessObj as $key=>$rw) { 
            echo '<userdata name="AE_Id">'.$rw->AE_Id.'</userdata>
                    <userdata name="AE_Name">'.$rw->AE_Name.'</userdata>
                    <userdata name="AE_Email">'.$rw->AE_Email.'</userdata>
                    <userdata name="AE_Cust_Name">'.$rw->AE_Cust_Name.'</userdata>
                    <userdata name="SR_Name">'.$rw->SR_Name.'</userdata>
                    <userdata name="PL_Name">'.$rw->PL_Name.'</userdata>
                    <userdata name="ALC_Name">'.$rw->ALC_Name.'</userdata>
                    <userdata name="CT_Name">'.$rw->CT_Name.'</userdata>
                    <userdata name="ST_Name">'.$rw->ST_Name.'</userdata>
                    <userdata name="CN_Name">'.$rw->CN_Name.'</userdata>
                    
                    <userdata name="CN_Id">'.$rw->AE_Visiting_CNId.'</userdata>
                    <userdata name="AA_VisaType">'.$rw->AE_Att_For.'</userdata>
                    <userdata name="ADOC_Id">'.$rw->AE_Certificate.'</userdata>
                    <userdata name="AA_Issuing_CNId">'.$rw->AE_Certi_StudyLoc.'</userdata>
                    <userdata name="APM_Id">'.$rw->APM_Id.'</userdata>
                    <userdata name="APS_Id">'.$rw->APS_Id.'</userdata>
                    <userdata name="AE_LastProcess">'.$rw->AE_LastProcess.'</userdata>
                    <userdata name="AA_CourseType">'.$rw->AE_Certi_Mode.'</userdata>
                    <userdata name="AA_IssuedYear">'.$rw->AE_Year.'</userdata>';
        
            $optionData = json_decode($rw->AE_AutomateData);

            foreach ($optionData as $optnId => $optionDetails) { 
                $sub_Process = $sup_Docs = '';
                
                $certificateDetails    = $CNObj[$optionDetails[0][0]].', '.$VisaTypeNameArray[$optionDetails[0][1]].'</br>'.
                                         $AdocObj[$optionDetails[0][2]].', '.$optionDetails[0][3].'</br>'.
                                         $SubProcObj[$optionDetails[0][4]].', '.$CNObj[$optionDetails[0][5]].'</br>';
                $main_sub_processArray = $optionDetails[1];
                $supportingDocArray    = $optionDetails[2];
                
                foreach ($certificateDetails as $key=>$value) {
                    $sup_Docs   .= $SupDocObj[$value].'</br>';
                }
                foreach ($main_sub_processArray as $key => $value) {
//                    $MS_Process   = explode("_", $value) ; 
                    $sub_Process .= $SubProcObj[$value].'</br>';
                }
                foreach ($supportingDocArray as $key=>$value) {
                    $sup_Docs   .= $SupDocObj[$value].'</br>';
                }
                          
                echo '<row id="'.$j.'">
                        <userdata name="AE_Comments">'.htmlspecialchars($optionDetails[7]).'</userdata>
                        <userdata name="AA_Comments">'.htmlspecialchars($optionDetails[4]).'</userdata>
                        <userdata name="AA_Process"><![CDATA['.json_encode($main_sub_processArray).']]></userdata>
                        <userdata name="AA_SupportingDocuments"><![CDATA['.json_encode($supportingDocArray).']]></userdata>
                        <userdata name="AA_CertificateDetails"><![CDATA['.json_encode($optionDetails[0]).']]></userdata>
                        <userdata name="LC_Id">'.$optionDetails[5].'</userdata>
                        <userdata name="LC_Name">'.$LCObj[$optionDetails[5]].'</userdata>
                        <userdata name="Date">'.$optionDetails[6].'</userdata>
                                        
                        <cell title=" ">'.$j.'</cell>
                        <cell title=" " ><![CDATA['.$certificateDetails.']]></cell>
                        <cell title=" " ><![CDATA['.$sub_Process.']]></cell>
                        <cell title=" " ><![CDATA['.$sup_Docs.']]></cell>
                        <cell title=" " ><![CDATA['.$LCObj[$optionDetails[5]].'</br>'.$optionDetails[6].']]></cell>
                        <cell title=" " >&#x20b9; '.$optionDetails[3].'</cell>
                        <cell title=" " ><![CDATA[<img src="images/icon/cross.png" height="18" style="cursor:pointer;" onclick="preTally.Track.removeATPOption('.$j.');" />]]></cell>
                        <cell title=" " ><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Track.showAutomateProcessComment(this,'.$j.',\'Enq\');" />]]></cell>
                            <cell title=" " ><![CDATA[<img src="images/icon/note_20.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Track.addOptionComment(this,'.$j.',\'Enq\');" />]]></cell>
                        <cell title=" "></cell>';
                echo '</row>';
                $j++;
            }
        }
    } else {
        echo '<row id="0"> 
            <cell type="ro" colspan="8"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
    $count = --$j;
    echo '<userdata name="TOT_Count">'.$count.'</userdata>';
    if($count == 0){
        echo '<row id="0"> 
            <cell type="ro" colspan="8"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>
