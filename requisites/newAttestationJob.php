<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); 
} else {
    header("Content-type: text/xml");       
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$LC_Name = $AttObj->getValue('locations', 'LC_Name', ' WHERE LC_Id = '.$preTally_user_lcid);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
<item type="settings" position="label-left" labelWidth="165" inputWidth="250" noteWidth="100" offsetLeft="15" offsetRight="0" />
<!--Start of block item set-->
        <!-- Start Candidate Details Fieldset-->
        <item type="hidden" name="AJ_Id"></item>
        <item type="hidden" name="AJ_CT_Id_Text"></item>
        <item type="hidden" name="AS_CT_Id_Text"></item>
        <item type="hidden" name="AD_CT_Id_Text"></item>
        <item type="fieldset" inputWidth="600" label="Candidate Details"  offsetLeft="25" width="750" position="label-left" offsetTop="15">   

            <item type="block" width="950">
                <item type="input" name="AJ_FName" label="Full Name" required="true" validate="^[a-zA-Z ]+$" >
                    <note width="150">Full Name</note>
                </item>
                <item type="newcolumn"/>
                <item type="calendar" name="AJ_DOB" label="Date Of Birth" readonly="true" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" offsetRight="5" >
                    <note width="150">Date Of Birth</note>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AJ_HouseNo" label="House/Flat Number" >
                    <note width="150">House/Flat Number</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_HouseName" label="House/Building Name" >
                    <note width="150">House/Building Name</note>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AJ_Society" label="Society/Area" validate="^[a-zA-Z ]+$" >
                    <note width="150">Society/Area</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_Street" label="Street/Sub Place" validate="^[a-zA-Z ]+$" >
                    <note width="150">Street/Sub Place</note>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AJ_Place" label="Place" required="true" validate="^[a-zA-Z ]+$"  value = "ABCDEF">
                    <note width="150">Place</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_Village" label="Village/Taluk"  validate="^[a-zA-Z ]+$" >
                    <note width="150">Village/Taluk</note>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AJ_Pincode" label="Pincode" required="true" validate="ValidNumeric" value = "123456789">
                    <note width="150">Pincode</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="AJ_CT_Id" label="City/Town" required="true" className="AJ_CityCombo" serverFiltering="requisites/attestationCities.php" filterCache="true" >
                    <note width="150">City/Town</note>
                </item>
            </item>    

            <item type="block" width="950">
                <item type="combo" label="District/State/Country" name="AJ_DT_Id" required="true" serverFiltering="requisites/attestationDistricts.php" className="AJ_DistCombo" filterCache="true">
                    <note width="150">District/State/Country</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_Email" label="Email ID" required="true" validate="ValidEmail" value="preeja@augmob.in">
                    <note width="150">Email ID</note>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AJ_Mobile1" label="Mobile 1" required="true" validate="ValidNumeric" value = "123456789">
                    <note width="150">Mobile Number 1</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AJ_Mobile2" label="Mobile 2" validate="ValidNumeric">
                    <note width="150">Mobile Number 2</note>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AJ_Landline" label="Landline" validate="ValidNumeric">
                    <note width="150">Landline</note>
                </item> 
                <item type="newcolumn"/>
                <item type="calendar" name="AJ_ReceivedDate" label="Job Received Date" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" required="true" readonly="true" value="'.date("Y-m-d").'">
                    <note width="150">Job Received Date</note>
                </item>
            </item>
        </item>  
        <!--end of Candidate Details Fieldset-->

        <!--start of Document Submitted By fieldset-->

        <item type="fieldset" inputWidth="100" label="Document Submitted By"  offsetLeft="25" width="950" offsetRight="5" offsetTop="15"> 
            <item type = "block" width="950">
                <item type="combo" label="Document Submitted By" name="AJ_SubmittedType" readonly="true" required="true">			
                    <option value="1" label="Self" />
                    <option value="2" label="Relative" />
                    <option value="3" label="Others" />
                    <note width="150">Document Submitted By</note>
                </item>
            </item>
            <item type="block" width="950">
                <item type="input" name="AS_FName" label="Full Name" validate="^[a-zA-Z ]+$" >
                    <note width="150">Full Name</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="combo"  readonly="true"  name="AS_Relation" label="Relation" validate="^[a-zA-Z ]+$" className = "AS_Relation" >
                    <note width="150">Relation</note>
                    <userdata>submittedBy</userdata>
                    <option value="" label="Select Relation" selected="true" />
                    <option value="Father" label="Father" />
                    <option value="Mother" label="Mother" />
                    <option value="Brother" label="Brother" />
                    <option value="Sister" label="Sister" />
                    <option value="Friend" label="Friend" />
                    <option value="Others" label="Others" />
                </item>
            </item>    
            <item type="block" width="950">
                <item type="input" name="AS_HouseNo" label="House/Flat Number" >
                    <note width="150">House/Flat Number</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_HouseName" label="House/Building Name"  >
                    <note width="150">House/Building Name</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>    
            <item type="block" width="950">    
                <item type="input" name="AS_Society" label="Society/Area" validate="^[a-zA-Z ]+$" >
                    <note width="150">Society/Area</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Street" label="Street/Sub Place"  validate="^[a-zA-Z ]+$" >
                    <note width="150">Street/Sub Place</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>    
            <item type="block" width="950">   
                <item type="input" name="AS_Place" label="Place"  validate="^[a-zA-Z ]+$">
                    <note width="150">Place</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Village" label="Village/Taluk"   validate="^[a-zA-Z ]+$">
                    <note width="150">Village/Taluk</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>    
            <item type="block" width="950">
                <item type="input" name="AS_Pincode" label="Pincode" validate="ValidNumeric" >
                    <note width="150">Pincode</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="AS_CT_Id" label="City/Town" className="AS_CityCombo" serverFiltering="requisites/attestationCities.php" filterCache="true" >
                    <note width="150">City/Town</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>    
            <item type="block" width="950">
                <item type="combo" label="District/State/Country"  name="AS_DT_Id" className="AS_DistCombo" serverFiltering="requisites/attestationDistricts.php" filterCache="true"  required="true">
                    <note width="150">District/State/Country</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Email" label="Email ID" validate="ValidEmail">
                    <note width="150">Email ID</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>   
            <item type="block" width="950">
                <item type="input" name="AS_Mobile1" label="Mobile 1" validate="ValidNumeric" >
                    <note width="150">Mobile Number 1</note>
                    <userdata>submittedBy</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AS_Mobile2" label="Mobile 2" validate="ValidNumeric" >
                    <note width="150">Mobile Number 2</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>
            <item type="block" width="950">
                <item type="input" name="AS_Landline" label="Landline" validate="ValidNumeric" >
                    <note width="150">Landline</note>
                    <userdata>submittedBy</userdata>
                </item>
            </item>    
        </item>
        <!--end of Document Submitted By Fieldset-->

        <!--start of Document Delivered To Fieldset-->
        <item type="newcolumn"/>
        <item type="fieldset" inputWidth="100" label="Document Delivered To"  offsetLeft="25" width="950" offsetTop="15">  
            <item type="block" width="950">
                <item type="combo" label="Document Delivered To" name="AJ_DeliveredType" readonly="true" required="true">		
                    <option value="1" label="Self" />
                    <option value="2" label="'.$LC_Name.'" />
                    <option value="3" label="Other Branch" />
                    <option value="4" label="Others" />
                    <note width="150">Document Delivered To</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="AD_Branch" label="Branch" hidden="true" className="AD_BranchCombo" serverFiltering="requisites/attestationBranch.php" filterCache="true" >
                    <note width="150">Branch</note>
                </item>
            </item>  

            <item type="block" width="950">
                <item type="input" name="AD_FName" label="Full Name" validate="^[a-zA-Z ]+$" >
                    <note width="150">Full Name</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="AD_Relation" readonly="true" label="Relation" validate="^[a-zA-Z ]+$" className = "AD_Relation"  >
                    <note width="150">Relation</note>
                    <userdata>deliveredTo</userdata>
                    <option value="" label="Select Relation" selected="true" />
                    <option value="Father" label="Father" />
                    <option value="Mother" label="Mother" />
                    <option value="Brother" label="Brother" />
                    <option value="Sister" label="Sister" />
                    <option value="Friend" label="Friend" />
                    <option value="Others" label="Others" />
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_HouseNo" label="House/Flat Number" >
                    <note width="150">House/Flat Number</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_HouseName" label="House/Building Name" >
                    <note width="150">House/Building Name</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_Society" label="Society/Area" validate="^[a-zA-Z ]+$">
                    <note width="150">Society/Area</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Street" label="Street/Sub Place" validate="^[a-zA-Z ]+$">
                    <note width="150">Street/Sub Place</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_Place" label="Place" validate="^[a-zA-Z ]+$" >
                    <note width="150">Place</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Village" label="Village/Taluk"  validate="^[a-zA-Z ]+$" >
                    <note width="150">Village/Taluk</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_Pincode" label="Pincode" validate="ValidNumeric">
                    <note width="150">Pincode</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="AD_CT_Id" label="City/Town" className="AD_CityCombo" serverFiltering="requisites/attestationCities.php" filterCache="true">
                    <note width="150">City/Town</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="combo" label="District/State/Country" name="AD_DT_Id" serverFiltering="requisites/attestationDistricts.php" required="true" className="AD_DistCombo" filterCache="true" >
                    <note width="150">District/State/Country</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Email" label="Email ID" validate="ValidEmail">
                    <note width="150">Email ID</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_Mobile1" label="Mobile 1"  validate="ValidNumeric">
                    <note width="150">Mobile Number 1</note>
                    <userdata>deliveredTo</userdata>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="AD_Mobile2" label="Mobile 2" validate="ValidNumeric">
                    <note width="150">Mobile Number 2</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

            <item type="block" width="950">
                <item type="input" name="AD_Landline" label="Landline" validate="ValidNumeric" >
                    <note width="150">Landline</note>
                    <userdata>deliveredTo</userdata>
                </item>
            </item>

        </item>
        <!--End of Document Submitted By fieldset-->
    <!--End of block item set-->';
echo'</items>';
?>