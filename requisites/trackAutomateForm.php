<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

/*if($REQUEST['for'] != 'enq') {
    $comboType = ' comboType = "checkbox" ';
    $attDocFil = '&amp;form=automate';
}
else 
    $className = ' className = "TAClass"  ';
*/
$AA_Id = $REQUEST['AA_Id'] ? $REQUEST['AA_Id'] : 0 ;
$AttObj->getDetails('attestation_automate','*',' WHERE AA_Id = '.$AA_Id);
$AAObj = $AttObj->DataArray;
//print_r($AAObj);

$visaTypes = explode(",", $AAObj[0]->AA_VisaType);

$AttObj->getDetails('attestation_process_main', 'APM_Id, APM_Title, APM_Title_Alias, APM_Status', ' WHERE APM_Status = 1 AND OF_Id = "'.$preTally_user_ofid.'" ORDER BY APM_Title');
$MainProcess_Obj = $AttObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$curYear   = date("Y");
$offsetLeft = $REQUEST['for'] == 'enq' ? 5 : 35;
echo '<items>
        <item type="settings" position="label-left" labelWidth="210" inputWidth="200" offsetLeft="'.$offsetLeft.'" offsetTop="0"/>

    
        <item type="hidden" name="AA_Id" value = "'.$AAObj[0]->AA_Id.'"></item>
        <item type="hidden" name="CN_HId" value = "'.$AAObj[0]->CN_Id.'"></item>
        <item type="hidden" name="AA_Issuing_HCNId" value = "'.$AAObj[0]->AA_Issuing_CNId.'"></item>
        <item type="hidden" name="AA_AutomateData" value = "'.htmlentities($AAObj[0]->AA_AutomateData).'" offsetTop="30" ></item>
        <item type="hidden" name="AE_Id" value = "0"></item>
        <item type="hidden" name="AE_AutomateData" value = ""></item>
        <item type="hidden" name="AE_JobAutomateData" value = ""></item>
        <item type="hidden" name="AE_EnqAutomateData" value = ""></item>
        <item type="label"  value = "" offsetTop="2" ></item>';
        if($REQUEST['for'] == 'enq') {
            echo '  <item type="fieldset" label="CANDIDATE DETAILS" width="430">
                
                <item type="block" width="100%" offsetLeft="0" className="negativeLeft">   
                    <item type="input"  className = "TAClass" label="Mobile No:" name="clientNo" value = "" position="label-left" inputWidth="140" validate = "ValidInteger" offsetLeft="0">
                        <note width="142">Contact Mobile / Landline</note>
                    </item>
                    <item type="newcolumn"/>
                    <item type="button" className="TAButtonClass" offsetTop="12" value="Check" position="label-left" offsetLeft="0" name="listCandidateATPOptions" />
                </item>  

                <item type="input"  className = "TAClass" label="Caller Name" name="clientName" value = "" position="label-left" validate = "^[A-Za-z .]*$">
                    <note>What Is Your Name / Caller Name</note>
                </item>
                <!--
                <item type="input"  className = "TAClass" label="Certificate Holder Name" name="certificateHolderName" value = "" position="label-left" validate = "^[A-Za-z .]*$">
                    <note>Name Of Certificate Holder</note>
                </item>
                -->
                <item type="input"  className = "TAClass" label="Email ID" name="clientEmail" value = "" position="label-left" validate = "ValidEmail">
                    <note>Email ID</note>
                </item>
                
                <item type="input"  className = "TAClass" name="GOGL_PlaceSearch" value="" label="Location" >
                    <note width="180">Please Add Your Address Location</note>
                </item>

                <item type="input" hidden = "true" className = "TAClass" name="GOGL_Street"  label="Street" required="true" value="">
                    <note width="150">Please Add Your Street</note>
                </item>
                <item type="input" hidden = "true" className = "TAClass" name="GOGL_Place"  label="Place" required="true" value="">
                    <note width="150">Please Add Your Place</note>
                </item>
                <item type="input" hidden = "true" className = "TAClass" name="GOGL_Location"  label="Location" required="true" value="">
                    <note width="150">Please Add Your Location</note>
                </item>
                <item type="input" hidden = "true" className = "TAClass" name="GOGL_City"  label="City" required="true" value="">
                    <note width="150">Please Add Your City</note>
                </item>
                <item type="input" hidden = "true" className = "TAClass" name="GOGL_State"  label="State" required="true" value="" readonly="true">
                    <note width="150">Please Add Your State</note>
                </item>
                <item type="input" hidden = "true" className = "TAClass" name="GOGL_Country"  label="Country" required="true" value="" readonly="true">
                    <note width="150">Please Add Your Country</note>
                </item>
                <!--
                <item type="input" hidden = "true" className = "TAClass" name="GOGL_Pincode"  label="Pincode" required="true" value="">
                    <note width="150">Please Add Your Country</note>
                </item>
                -->
            </item>
            <item type="fieldset" label="CERTIFICATE DETAILS"  width="430">';
        }
        echo '
            <item type="combo"  className = "TAClass" label="Where do you want to go?" name="CN_Id" value = "'.$AAObj[0]->CN_Id.'" '.$comboType.' position="label-left" required="true" filtering = "true" >
                <note>Country</note>
            </item> 

            <item type="combo"  className = "TAClass" label="Why you need this attestation?" name="AA_VisaType" '.$comboType.' connector="requisites/attestationVisaTypes.php&amp;AA_VisaType='.$AAObj[0]->AA_VisaType.'" filterCache="false" position="label-left" required="true" filtering = "true" >
                <note>Document</note>
            </item> 

            <item type="combo"  className = "TAClass" label="Certificate/Degree/Service" name="ADOC_Id" '.$comboType.' connector="requisites/attestationDocuments.php&amp;ADOCId='.$AAObj[0]->ADOC_Id.''.$attDocFil.'" filterCache="false"  position="label-left" required="true" filtering = "true" >
                <note>Certificate</note>
            </item> 

            <item type="combo"  className = "TAClass" label="Where did you study?" name="AA_Issuing_CNId" value = "'.$AAObj[0]->AA_Issuing_CNId.'" '.$comboType.' position="label-left" required="true" filtering = "true" >
                <note>Issuing Country</note>
            </item> 

            <item type="combo"  className = "TAClass" label="Certificate issued by?" name="APS_Id" '.$comboType.' connector="requisites/attestationSubProcess.php&amp;APS_Id='.$AAObj[0]->APS_Id.''.$attDocFil.'" filtering = "true" position="label-left" required="true">
                <note>University/Board/Council</note>
            </item>
            <item type="combo"  className = "TAClass" label="Is this a regular course?" name="AA_CourseType" readonly="true" >
                <option value="1" label="Yes" />
                <option value="2" label="No" />
                    <note>Is this a Regular Course?</note>
            </item>
            <item type="combo"  className = "TAClass" label="Which is Last Process done?" name="AE_LastProcess" value = "'.$AAObj[0]->AE_LastProcess.'" '.$comboType.' position="label-left" required="true" filtering = "true" >
                <note>Last Process Done In Certificate</note>';
                echo '<option value="0" label="No Process Done" />';
                if($MainProcess_Obj){      
                    foreach($MainProcess_Obj as $rw) {
                        echo '<option value="'.$rw->APM_Id.'" label="'.$rw->APM_Title_Alias.'" />';
                    }
                }
            echo '</item> 
            <item type="block" width="410"  className = "TAClass" >   
                <item type="input"  className = "TAClass" label="Passed/Issuing Year" name="AA_IssuedYear" offsetLeft="3" value="" position="label-left" inputWidth="110" required="true" validate="ValidNumeric">
                    <note>Certificate Issued Year</note>
                </item>
                <item type="newcolumn"/>
                <item type="button" className="TAButtonClass" value="Submit" position="label-left" offsetTop="-10" offsetLeft="0" name="listATPOptions" />
            </item>
        ';
        if($REQUEST['for'] == 'enq') { echo '</item>'; }
    
    
    echo '</items>';
?>