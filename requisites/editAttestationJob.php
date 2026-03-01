<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); 
} else {
    header("Content-type: text/xml");       
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AJ_Id = $REQUEST['AJ_Id'];
$AttObj->getCandidateDetails($AJ_Id);
$User_Obj = (array) $AttObj->DataArray[0];
//print_r($User_Obj);
$AttObj->getSubmitterDetails($AJ_Id);
$Submitter_Obj = (array) $AttObj->DataArray[0];

$AttObj->getDeliverdToDetails($AJ_Id);
$Deliver_Obj = (array) $AttObj->DataArray[0];
$LC_Name = $AttObj->getValue('locations', 'LC_Name', ' WHERE LC_Id = '.$preTally_user_lcid);

$status = $User_Obj['AJ_Status'];
if($status == 0) {
    $cashReceiptStatus  = $AttObj->getValue('attestation_job_cash_receipts', 'COUNT(AJCR_Id) AS COUNT', " WHERE AJ_Id = $AJ_Id");
    if($cashReceiptStatus > 0) // if cash receipt is generated, dont show delete button
        $status = 1;
}
 
$Date=$User_Obj['AJ_ReceivedDate']?$User_Obj['AJ_ReceivedDate'] :date("Y-m-d");
$CompleteDate=$User_Obj['AJ_DeliveryDate'] != '0000-00-00' ? $User_Obj['AJ_DeliveryDate'] : '';

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
<item type="settings" position="label-left" labelWidth="165" inputWidth="250" noteWidth="100" offsetLeft="15" offsetRight="0" />
<!--Start of block item set-->
        <!-- Start Candidate Details Fieldset-->
       
        <item type="hidden" name="AJ_Id" value="'.$AJ_Id.'"></item>
        <item type="hidden" name="AJ_Status" value="'.$status.'"></item>
        <item type="hidden" name="AJ_SubmittedType_Hid" value="'.$User_Obj['AJ_SubmittedType'].'"></item>
        <item type="hidden" name="AS_Relation_Hid" value="'.$Submitter_Obj['AS_Relation'].'"></item>
        <item type="hidden" name="AJ_DeliveredType_Hid" value="'.$User_Obj['AJ_DeliveredType'].'"></item>       
        <item type="hidden" name="AD_Relation_Hid" value="'.$Deliver_Obj['AD_Relation'].'"></item>
        <item type="hidden" name="AJ_JobDeliverdTo_Hid" value="'.$User_Obj['AJ_JobDeliverdTo'].'"></item>
        <item type="hidden" name="AJ_JobDeliverdTo" value="'.$preTally_user_lcid.'"></item>
        <item type="hidden" name="AJ_Comments" value="'.($User_Obj['AJ_Comments']).'"></item>
            
        <item type="fieldset" inputWidth="600" label="Candidate Details"  offsetLeft="25" width="750" position="label-left" offsetTop="15">   

            <item type="block" width="950">
                <item type="input" name="AJ_Mobile1" label="Mobile 1" value="'.$User_Obj['AJ_Mobile1'].'"  required="true" validate="ValidNumeric">
                    <note width="150">Mobile Number 1</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_HouseName" label="House/Flat Name"  value="'.$User_Obj['AJ_HouseName'].'" >
                    <note width="150">House/Flat Name</note>
                </item>
            </item>
            
            <item type="block" width="950">
                <item type="input" name="AJ_FName" label="Full Name" value="'.$User_Obj['AJ_FName'].'" required="true" validate="^[a-zA-Z ]+$" >
                    <note width="150">Name as per the Certificate</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_HouseNo" label="House/Flat Number"  value="'.$User_Obj['AJ_HouseNo'].'" >
                    <note width="150">House/Flat Number</note>
                </item>
            </item>
            
            <item type="block" width="950">
                <item type="input" name="AJ_Email" label="Email ID" required="true" value="'.$User_Obj['AJ_Email'].'" validate="ValidEmail">
                    <note width="150">Email ID</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_BuildingName" label="Building Name/Number"  value="'.$User_Obj['AJ_BuildingName'].'" >
                    <note width="150">Building Name/Number</note>
                </item>
            </item>
            
            <item type="block" width="950">
                <item type="input" name="AJ_ContactMob" label="Contact Mobile" value="'.$User_Obj['AJ_ContactMob'].'" validate="ValidNumeric">
                    <note width="150">Contact Mobile</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_Society" label="Society/Area Name"  value="'.$User_Obj['AJ_Society'].'"  validate="^[a-zA-Z ]+$" >
                    <note width="150">Society/Area</note>
                </item>
            </item>
            
            <item type="block" width="950">
            
                <item type="input" name="GOGL_PlaceSearch_1" value="" label="Location" >
                    <note width="180">Please Add Your Address Location</note>
                </item>

                <item type="input" name="GOGL_Street_1"  label="Street" required="true" value="'.$User_Obj['SR_Name'].'">
                    <note width="150">Please Add Your Street</note>
                </item>
                <item type="input" name="GOGL_Place_1"  label="Place" required="true" value="'.$User_Obj['PL_Name'].'">
                    <note width="150">Please Add Your Place</note>
                </item>
                <item type="input" name="GOGL_Location_1"  label="Location" required="true" value="'.$User_Obj['ALC_Name'].'">
                    <note width="150">Please Add Your Location</note>
                </item>
                <item type="input" name="GOGL_City_1"  label="City" required="true" value="'.$User_Obj['CT_Name'].'">
                    <note width="150">Please Add Your City</note>
                </item>
                <item type="input" name="GOGL_State_1"  label="State" required="true" value="'.$User_Obj['ST_Name'].'">
                    <note width="150">Please Add Your State</note>
                </item>
                <item type="input" name="GOGL_Country_1"  label="Country" required="true" value="'.$User_Obj['CN_Name'].'">
                    <note width="150">Please Add Your Country</note>
                </item>
                
                <item type="input" name="GOGL_Pincode_1" label="Pincode"  value="'.$User_Obj['AJ_Pincode'].'"   validate="ValidNumeric">
                    <note width="150">Pincode</note>
                </item>
                
                <item type="calendar" name="AJ_ReceivedDate" label="Job Received Date" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" required="true" readonly="true" value="'.$Date.'">
                    <note width="150">Job Received Date</note>
                </item>
            
                
                <item type="newcolumn"/>
                <item type="input" name="AJ_Email2" label="Email 2" value="'.$User_Obj['AJ_Email2'].'" validate="ValidEmail">
                    <note width="150">Email 2</note>
                </item>
                
                <item type="input" name="AJ_Mobile2" label="Mobile 2" value="'.$User_Obj['AJ_Mobile2'].'" validate="ValidNumeric">
                    <note width="150">Mobile Number 2</note>
                </item>
                
                <item type="input" name="AJ_Landline" label="Land Line Number"  value="'.$User_Obj['AJ_Landline'].'" >
                    <note width="150">Land Line Number</note>
                </item>';

                if($User_Obj['AJ_DOB'] != '0000-00-00' && $User_Obj['AJ_DOB'] != 0 && $User_Obj['AJ_DOB'] != "")
                    $DOB = $User_Obj['AJ_DOB'];
                else
                    $DOB = '';
                echo '<item type="calendar" name="AJ_DOB" label="Date Of Birth" value="'.$DOB.'" readonly="true" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" offsetRight="5" >
                    <note width="150">Date Of Birth</note>
                </item>
                
                <item type="input" name="AJ_PoliceLoc" label="Nearest Police Location"  value="'.$User_Obj['AJ_PoliceLoc'].'"   validate="^[a-zA-Z ]+$">
                    <note width="150">Nearest Police Location</note>
                </item>
                
                <item type="input" name="AJ_WorkExp" label="Work Experience" value="'.$User_Obj['AJ_WorkExp'].'" validate="^([0-9]*|\d*\.\d{1}?\d*)$">
                    <note width="150">Work Experience</note>
                </item>
                
                <item type="calendar" name="AJ_DeliveryDate" label="Delivery Date" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" readonly="true" value="'.$CompleteDate.'" >
                    <note width="150">Delivery Date</note>
                </item>


            </item>
            
        </item>  
        <!--end of Candidate Details Fieldset-->

        <!--start of Document Submitted By fieldset-->

        <item type="fieldset" inputWidth="100" label="Document Submitted By"  offsetLeft="25" width="950" offsetRight="5" offsetTop="15"> 
            <item type = "block" width="950">
                <item type="combo" label="Document Submitted By" name="AJ_SubmittedType" readonly="true" required="true">			
                    <option value="1" label="Self" ';if($User_Obj['AJ_SubmittedType'] == 1 ) echo ' selected = "true" ';echo '/>
                    <option value="2" label="Relative"';if($User_Obj['AJ_SubmittedType'] == 2 ) echo ' selected = "true" ';echo ' />
                    <option value="3" label="Others"';if($User_Obj['AJ_SubmittedType'] == 3 ) echo ' selected = "true" ';echo ' />
                    <note width="150">Document Submitted By</note>
                </item>
            </item>
            <item type="block" width="950">
                <item type="input" name="AS_FName" label="Full Name" validate="^[a-zA-Z ]+$" value="'.$Submitter_Obj['AS_FName'].'" >
                    <note width="150">Full Name</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="combo"  readonly="true" name="AS_Relation" label="Relation"  validate="^[a-zA-Z ]+$" className = "AS_Relation" >
                    <note width="150">Relation</note>
                    <userdata>submittedBy</userdata>
                    <option value="" label="Select Relation" ';if(!$Submitter_Obj['AS_Relation'] ) echo 'selected="true"'; echo ' />
                    <option value="Father" label="Father" ';if($Submitter_Obj['AS_Relation'] == "Father" ) echo 'selected="true"'; echo ' />
                    <option value="Mother" label="Mother" ';if($Submitter_Obj['AS_Relation'] == "Mother" ) echo 'selected="true"'; echo ' />
                    <option value="Brother" label="Brother" ';if($Submitter_Obj['AS_Relation'] == "Brother" ) echo 'selected="true"'; echo ' />
                    <option value="Sister" label="Sister" ';if($Submitter_Obj['AS_Relation'] == "Sister" ) echo 'selected="true"'; echo ' />
                    <option value="Friend" label="Friend" ';if($Submitter_Obj['AS_Relation'] == "Friend" ) echo 'selected="true"'; echo ' />
                    <option value="Others" label="Others" ';if($Submitter_Obj['AS_Relation'] == "Others" ) echo 'selected="true"'; echo ' />
                </item>
            </item>    
            
            <item type="block" width="950">
            
                <item type="input" name="GOGL_PlaceSearch_2" value="" label="Location">
                    <note width="180">Please Add Your Address Location</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="GOGL_Street_2"  label="Street" required="true"  value="'.$Submitter_Obj['SR_Name'].'">
                    <note width="150">Please Add Your Street</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="GOGL_Place_2"  label="Place" required="true"  value="'.$Submitter_Obj['PL_Name'].'">
                    <note width="150">Please Add Your Place</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="GOGL_Location_2"  label="Location" required="true"  value="'.$Submitter_Obj['ALC_Name'].'">
                    <note width="150">Please Add Your Location</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="GOGL_City_2"  label="City" required="true"  value="'.$Submitter_Obj['CT_Name'].'">
                    <note width="150">Please Add Your City</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="GOGL_State_2"  label="State" required="true"  value="'.$Submitter_Obj['ST_Name'].'">
                    <note width="150">Please Add Your State</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="GOGL_Country_2"  label="Country" required="true"  value="'.$Submitter_Obj['CN_Name'].'">
                    <note width="150">Please Add Your Country</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="AS_Mobile1" label="Mobile 1" validate="ValidNumeric" value="'.$Submitter_Obj['AS_Mobile1'].'" >
                    <note width="150">Mobile Number 1</note>
                    <userdata>submittedBy</userdata>
                </item>
                
                <item type="newcolumn"/>
                                
                <item type="input" name="AS_HouseNo" label="House/Flat Number"  value="'.$Submitter_Obj['AS_HouseNo'].'"  >
                    <note width="150">House/Flat Number</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="AS_HouseName" label="House/Building Name" value="'.$Submitter_Obj['AS_HouseName'].'"    >
                    <note width="150">House/Building Name</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="AS_Society" label="Society/Area" validate="^[a-zA-Z ]+$" value="'.$Submitter_Obj['AS_Society'].'" >
                    <note width="150">Society/Area</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="AS_Village" label="Village/Taluk"  validate="^[a-zA-Z ]+$" value="'.$Submitter_Obj['AS_Village'].'">
                    <note width="150">Village/Taluk</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="GOGL_Pincode_2" label="Pincode"  value="'.$Submitter_Obj['AS_Pincode'].'"   validate="ValidNumeric">
                    <note width="150">Pincode</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="AS_Email" label="Email ID" validate="ValidEmail" value="'.$Submitter_Obj['AS_Email'].'">
                    <note width="150">Email ID</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="AS_Landline" label="Landline" validate="ValidNumeric" value="'.$Submitter_Obj['AS_Landline'].'" >
                    <note width="150">Landline</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="input" name="AS_Mobile2" label="Mobile 2" validate="ValidNumeric"  value="'.$Submitter_Obj['AS_Mobile2'].'">
                    <note width="150">Mobile Number 2</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>
        </item>
        <!--end of Document Submitted By Fieldset-->

        <!--start of Document Delivered To Fieldset-->
        <item type="newcolumn"/>
        <item type="fieldset" inputWidth="100" label="Document Delivered To"  offsetLeft="25" width="950" offsetTop="15">
            <item type="block" width="950">
                <item type="combo" label="Document Delivered To" name="AJ_DeliveredType" readonly="true" required="true">';
                //if($User_Obj['LC_Name'] && $preTally_user_lcid != $User_Obj['LC_Id'] && $User_Obj['AJ_DeliveredType'] == '2')
                //echo '<option value="5" label="'.$User_Obj['LC_Name'].'" />';
                echo '<option value="1" label="Self" />';
                if($User_Obj['LC_Name'] && $User_Obj['AJ_DeliveredType'] == '2')
                    $LC_Name = $User_Obj['LC_Name'];
                else
                    $LC_Name = $LC_Name;
                echo '<option value="2" label="'.$LC_Name.'" />
                    <option value="3" label="Other Branch" />
                    <option value="4" label="Others" />
                    <note width="150">Document Delivered To</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="AD_Branch" serverFiltering="requisites/attestationBranch.php" hidden="true" label="Branch" className="AD_BranchCombo" filterCache="true" >
                    <note width="150">Branch</note>
                </item>
            </item>  

            <item type="block" width="950">
                <item type="input" name="AD_FName" label="Full Name" value="'.$Deliver_Obj['AD_FName'].'" validate="^[a-zA-Z ]+$" >
                    <note width="150">Full Name</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="AD_Relation" readonly="true"  label="Relation" validate="^[a-zA-Z ]+$" className = "AD_Relation"  >
                    <note width="150">Relation</note>
                    <userdata>deliveredTo</userdata>
                    <option value="" label="Select Relation" ';if(!$Deliver_Obj['AD_Relation'] ) echo 'selected="true"'; echo ' />
                    <option value="Father" label="Father" ';if($Deliver_Obj['AD_Relation'] == "Father" ) echo 'selected="true"'; echo ' />
                    <option value="Mother" label="Mother" ';if($Deliver_Obj['AD_Relation'] == "Mother" ) echo 'selected="true"'; echo ' />
                    <option value="Brother" label="Brother" ';if($Deliver_Obj['AD_Relation'] == "Brother" ) echo 'selected="true"'; echo ' />
                    <option value="Sister" label="Sister" ';if($Deliver_Obj['AD_Relation'] == "Sister" ) echo 'selected="true"'; echo ' />
                    <option value="Friend" label="Friend" ';if($Deliver_Obj['AD_Relation'] == "Friend" ) echo 'selected="true"'; echo ' />
                    <option value="Others" label="Others" ';if($Deliver_Obj['AD_Relation'] == "Others" ) echo 'selected="true"'; echo ' />
                </item>
            </item>
            
            <item type="block" width="950">
            
                <item type="input" name="GOGL_PlaceSearch_3" value="" label="Location">
                    <note width="180">Please Add Your Address Location</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="GOGL_Street_3"  label="Street" required="true"  value="'.$Deliver_Obj['SR_Name'].'">
                    <note width="150">Please Add Your Street</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="GOGL_Place_3"  label="Place" required="true"  value="'.$Deliver_Obj['PL_Name'].'">
                    <note width="150">Please Add Your Place</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="GOGL_Location_3"  label="Location" required="true"  value="'.$Deliver_Obj['ALC_Name'].'">
                    <note width="150">Please Add Your Location</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="GOGL_City_3"  label="City" required="true"  value="'.$Deliver_Obj['CT_Name'].'">
                    <note width="150">Please Add Your City</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="GOGL_State_3"  label="State" required="true"  value="'.$Deliver_Obj['ST_Name'].'">
                    <note width="150">Please Add Your State</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="GOGL_Country_3"  label="Country" required="true"  value="'.$Deliver_Obj['CN_Name'].'">
                    <note width="150">Please Add Your Country</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="AD_Mobile1" label="Mobile 1" validate="ValidNumeric" value="'.$Deliver_Obj['AD_Mobile1'].'" >
                    <note width="150">Mobile Number 1</note>
                    <userdata>deliveredTo</userdata>
                </item>
                
                <item type="newcolumn"/>
                                
                <item type="input" name="AD_HouseNo" label="House/Flat Number"  value="'.$Deliver_Obj['AD_HouseNo'].'"  >
                    <note width="150">House/Flat Number</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="AD_HouseName" label="House/Building Name" value="'.$Deliver_Obj['AD_HouseName'].'"    >
                    <note width="150">House/Building Name</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="AD_Society" label="Society/Area" validate="^[a-zA-Z ]+$" value="'.$Deliver_Obj['AD_Society'].'" >
                    <note width="150">Society/Area</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="AD_Village" label="Village/Taluk"  validate="^[a-zA-Z ]+$" value="'.$Deliver_Obj['AD_Village'].'">
                    <note width="150">Village/Taluk</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="GOGL_Pincode_3" label="Pincode"  value="'.$Deliver_Obj['AD_Pincode'].'"   validate="ValidNumeric">
                    <note width="150">Pincode</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="AD_Email" label="Email ID" validate="ValidEmail" value="'.$Deliver_Obj['AD_Email'].'">
                    <note width="150">Email ID</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="AD_Landline" label="Landline" validate="ValidNumeric" value="'.$Deliver_Obj['AD_Landline'].'" >
                    <note width="150">Landline</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="input" name="AD_Mobile2" label="Mobile 2" validate="ValidNumeric"  value="'.$Deliver_Obj['AD_Mobile2'].'">
                    <note width="150">Mobile Number 2</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            

        </item>
        <!--End of Document Submitted By fieldset-->
    <!--End of block item set-->';
echo'</items>';

/*
 * 
 * 
 * Hidden Data
 * 
 *    <item type="hidden" name="AJ_CT_Id_Text"></item>
        <item type="hidden" name="AS_CT_Id_Text"></item>
        <item type="hidden" name="AD_CT_Id_Text"></item>
        
        <item type="hidden" name="AJ_AP_Id_Text"></item>
        <item type="hidden" name="AS_AP_Id_Text"></item>
        <item type="hidden" name="AD_AP_Id_Text"></item>
 * 
 *  <item type="hidden" name="AJ_AP_Id_Hid" value="'.$User_Obj['AP_Id'].'"></item>
        <item type="hidden" name="AJ_CT_Id_Hid" value="'.$User_Obj['CT_Id'].'"></item>
        <item type="hidden" name="AJ_ST_Id_Hid" value="'.$User_Obj['ST_Id'].'"></item>
        
 * 
 * 
 *         <item type="hidden" name="AS_AP_Id_Hid" value="'.$Submitter_Obj['AP_Id'].'"></item>
        <item type="hidden" name="AS_CT_Id_Hid" value="'.$Submitter_Obj['CT_Id'].'"></item>
        <item type="hidden" name="AS_ST_Id_Hid" value="'.$Submitter_Obj['ST_Id'].'"></item>
 * 
 * 
 * <item type="hidden" name="AD_AP_Id_Hid" value="'.$Deliver_Obj['AP_Id'].'"></item>
        <item type="hidden" name="AD_CT_Id_Hid" value="'.$Deliver_Obj['CT_Id'].'"></item>
        <item type="hidden" name="AD_ST_Id_Hid" value="'.$Deliver_Obj['ST_Id'].'"></item>
 * 
 * 
 * 
 * 
 * 
 *  <item type="block" width="950">
                <item type="combo" name="AJ_AP_Id" label="Place" serverFiltering="requisites/attestationPlaces.php" value="'.$User_Obj['AP_Id'].'"  required="true" offsetTop = "100">
                    <note width="150">Place</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_Landline" label="Land Line Number"  value="'.$User_Obj['AJ_Landline'].'" offsetTop = "100">
                    <note width="150">Land Line Number</note>
                </item>
            </item>

            <item type="block" width="950">
                <item type="combo" name="AJ_CT_Id" label="City/Town" serverFiltering="requisites/attestationCities.php"  value="'.$User_Obj['CT_Name'].'"  required="true" className="AJ_CityCombo" filterCache="true" >
                    <note width="150">City/Town</note>
                </item>
                <item type="newcolumn"/>';
                if($User_Obj['AJ_DOB'] != '0000-00-00' && $User_Obj['AJ_DOB'] != 0 && $User_Obj['AJ_DOB'] != "")
                    $DOB = $User_Obj['AJ_DOB'];
                else
                    $DOB = '';
                echo '<item type="calendar" name="AJ_DOB" label="Date Of Birth" value="'.$DOB.'" readonly="true" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" offsetRight="5" >
                    <note width="150">Date Of Birth</note>
                </item>
            </item>    

            <item type="block" width="950">
                <item type="combo" label="State/Country" serverFiltering="requisites/attestationStateCountries.php" name="AJ_ST_Id" value="'.$User_Obj['DT_Name'].'"  required="true" className="AJ_DistCombo" filterCache="true">
                    <note width="150">State/Country</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_PoliceLoc" label="Nearest Police Location"  value="'.$User_Obj['AJ_PoliceLoc'].'"   validate="ValidNumeric">
                    <note width="150">Nearest Police Location</note>
                </item>
            </item>    
           
            <item type="block" width="950">
                <item type="input" name="AJ_Pincode" label="Pincode"  value="'.$User_Obj['AJ_Pincode'].'"   validate="ValidNumeric">
                    <note width="150">Pincode</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_Mobile2" label="Mobile 2" value="'.$User_Obj['AJ_Mobile2'].'" validate="ValidNumeric">
                    <note width="150">Mobile Number 2</note>
                </item>
            </item>    
            
            <item type="block" width="950">
                <item type="calendar" name="AJ_ReceivedDate" label="Job Received Date" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" required="true" readonly="true" value="'.$Date.'">
                    <note width="150">Job Received Date</note>
                </item>
                <item type="newcolumn"/>
                <item type="calendar" name="AJ_DeliveryDate" label="Delivery Date" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" readonly="true" value="'.$CompleteDate.'" >
                    <note width="150">Delivery Date</note>
                </item>
            </item>  
            
            <item type="block" width="950">
                
                <item type="newcolumn"/>
                <item type="input" name="AJ_WorkExp" label="Work Experience" value="'.$User_Obj['AJ_WorkExp'].'" validate="ValidNumeric">
                    <note width="150">Work Experience</note>
                </item>
            </item>  
 * 
 * Submitter details
 * 
 * 
 * <item type="block" width="950">
                <item type="input" name="AS_HouseNo" label="House/Flat Number"  value="'.$Submitter_Obj['AS_HouseNo'].'"  >
                    <note width="150">House/Flat Number</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_HouseName" label="House/Building Name" value="'.$Submitter_Obj['AS_HouseName'].'"    >
                    <note width="150">House/Building Name</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>    
            <item type="block" width="950">    
                <item type="input" name="AS_Society" label="Society/Area" validate="^[a-zA-Z ]+$" value="'.$Submitter_Obj['AS_Society'].'" >
                    <note width="150">Society/Area</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Street" label="Street/Sub Place"  validate="^[a-zA-Z ]+$"  value="'.$Submitter_Obj['AS_Street'].'">
                    <note width="150">Street/Sub Place</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item> 
 * 
 * 
 * <item type="block" width="950">   
                <item type="combo" name="AS_AP_Id" label="Place"  serverFiltering="requisites/attestationPlaces.php" value="'.$Submitter_Obj['AS_AP_Id'].'" required="true">
                    <note width="150">Place</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Village" label="Village/Taluk"  validate="^[a-zA-Z ]+$" value="'.$Submitter_Obj['AS_Village'].'">
                    <note width="150">Village/Taluk</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>    
            <item type="block" width="950">
                <item type="combo" name="AS_CT_Id" serverFiltering="requisites/attestationCities.php" label="City/Town" className="AS_CityCombo" filterCache="true" >
                    <note width="150">City/Town</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Pincode" label="Pincode" validate="ValidNumeric" value="'.$Submitter_Obj['AS_Pincode'].'" >
                    <note width="150">Pincode</note>
                    <userdata>submittedBy</userdata>
                </item>
                
            </item>    
            <item type="block" width="950">
                <item type="combo" label="State/Country" serverFiltering="requisites/attestationStateCountries.php" name="AS_ST_Id" className="AS_DistCombo" filterCache="true"  required="true">
                    <note width="150">State/Country</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Email" label="Email ID" validate="ValidEmail" value="'.$Submitter_Obj['AS_Email'].'">
                    <note width="150">Email ID</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>   
            <item type="block" width="950">
                <item type="input" name="AS_Mobile1" label="Mobile 1" validate="ValidNumeric" value="'.$Submitter_Obj['AS_Mobile1'].'" >
                    <note width="150">Mobile Number 1</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Mobile2" label="Mobile 2" validate="ValidNumeric"  value="'.$Submitter_Obj['AS_Mobile2'].'">
                    <note width="150">Mobile Number 2</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>
            <item type="block" width="950">
                <item type="input" name="AS_Landline" label="Landline" validate="ValidNumeric" value="'.$Submitter_Obj['AS_Landline'].'" >
                    <note width="150">Landline</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item> 
 * 
 * 
 * Deliver To Details
 * 
 * 
 * <item type="block" width="950">
                <item type="input" name="AD_HouseNo" label="House/Flat Number"  value="'.$Deliver_Obj['AD_HouseNo'].'" >
                    <note width="150">House/Flat Number</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_HouseName" label="House/Building Name"  value="'.$Deliver_Obj['AD_HouseName'].'" >
                    <note width="150">House/Building Name</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_Society" label="Society/Area" validate="^[a-zA-Z ]+$" value="'.$Deliver_Obj['AD_Society'].'" >
                    <note width="150">Society/Area</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Street" label="Street/Sub Place" validate="^[a-zA-Z ]+$" value="'.$Deliver_Obj['AD_Street'].'" >
                    <note width="150">Street/Sub Place</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="combo" name="AD_AP_Id" label="Place" serverFiltering="requisites/attestationPlaces.php" value="'.$Deliver_Obj['AD_AP_Id'].'" required="true">
                    <note width="150">Place</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Village" label="Village/Taluk" validate="^[a-zA-Z ]+$" value="'.$Deliver_Obj['AD_Village'].'" >
                    <note width="150">Village/Taluk</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="combo" serverFiltering="requisites/attestationCities.php" name="AD_CT_Id" label="City/Town" className="AD_CityCombo" filterCache="true">
                    <note width="150">City/Town</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Pincode" label="Pincode" validate="ValidNumeric" value="'.$Deliver_Obj['AD_Pincode'].'" >
                    <note width="150">Pincode</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="combo" label="State/Country" serverFiltering="requisites/attestationStateCountries.php" name="AD_ST_Id" required="true" className="AD_DistCombo" filterCache="true" >
                    <note width="150">State/Country</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Email" label="Email ID" validate="ValidEmail" value="'.$Deliver_Obj['AD_Email'].'" >
                    <note width="150">Email ID</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_Mobile1" label="Mobile 1"  validate="ValidNumeric" value="'.$Deliver_Obj['AD_Mobile1'].'" >
                    <note width="150">Mobile Number 1</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Mobile2" label="Mobile 2" validate="ValidNumeric" value="'.$Deliver_Obj['AD_Mobile2'].'" >
                    <note width="150">Mobile Number 2</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_Landline" label="Landline" validate="ValidNumeric"  value="'.$Deliver_Obj['AD_Landline'].'" >
                    <note width="150">Landline</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>
 * 
 * 
 */
?>