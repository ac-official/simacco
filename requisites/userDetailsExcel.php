<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
    }
echo '<items>
    <item type="settings" position="label-left" labelWidth="150" inputWidth="300" noteWidth="180" offsetLeft="20"/>
    <item type="combo" name="username" label="User ID" readonly="true"></item>
    <item type="combo" name="US_DOB" label="Date Of Birth" readonly="true"></item>
    <item type="combo" label="Gender" name="US_Gender" readonly="true"> </item>
    <item type="combo" name="US_Address" label="Address" readonly="true"></item>
    <item type="combo" name="Country" label="Country" readonly="true"> </item>
    <item type="combo" name="State" label="State" readonly="true"> </item>
    <item type="combo" name="City" label="City" readonly="true"> </item>
    <item type="combo" name="US_Pemail" label="Personal Email" readonly="true"></item>
    <item type="combo" name="US_Altemail" label="Alternate Email" readonly="true"></item>
    <item type="combo" name="US_Mobile" label="Mobile No:" readonly="true"></item>
    <item type="combo" name="US_AltMobile" label="Alternate Mobile No:" readonly="true"></item>
    <item type="combo" name="US_Landline" label="Landline No:" readonly="true"> </item>
    <item type="combo" name="US_AltLandline" label="Alternate Landline No:" readonly="true"> </item>
    <item type="combo" name="US_Relative" label="Father/Spouse Name" readonly="true"></item>
    <item type="combo" name="US_Blood" label="Blood Group" readonly="true"></item>
    <item type="combo" name="US_Guardian" label="Hostel/Local Guardian Name" readonly="true"></item>
    <item type="combo" name="US_Guardianphone" label="Hostel/Local Guardian Phone" readonly="true"></item>
    <item type="combo" name="US_Emergencyperson" label="Emergency Contact Person" readonly="true"></item>
    <item type="combo" name="US_Emergencynumber" label="Emergency Contact No:" readonly="true"></item>
    <item type="combo" name="US_Emergencyrelation" label="Emergency Contact Relation" readonly="true"></item>
    <item type="combo" name="US_Passport" label="Passport No:" readonly="true"></item>
    <item type="combo" name="US_Qualification" label="Highest Qualification" readonly="true"></item>
    <item type="combo" name="US_Specialization" label="Specialization" readonly="true"></item>
    <item type="combo" name="US_Experience" label="Total Experience" readonly="true"></item>
    <item type="combo" name="US_LastEmployee" label="Last Company Details" readonly="true"> </item>
    <item type="combo" name="PAN" label="PAN No:" readonly="true"> </item>
    <item type="combo" name="Pay_Mode" label="Payable through:" readonly="true"></item>
    <item type="combo" name="BA_Id" label="Company Bank Account No:" readonly="true"></item>
    <item type="combo" name="US_AccNo" label="Employee Bank Account No:" validate="ValidNumeric" readonly="true"></item>
    <item type="combo" name="US_Bankname" label="Employee Bank Name:" readonly="true"></item>
    <item type="combo" name="US_BankBranch" label="Employee Bank Branch:" readonly="true" ></item>
    <item type="combo" name="US_PFNo" label="PF No:" readonly="true"></item>
    <item type="combo" name="US_ESI" label="ESI No:" readonly="true"></item>
    <item type="combo" name="Salary_Struct" label="Salary Structure" readonly="true" ></item>
    <item type="combo" name="US_GrossSal" label="Gross Salary" readonly="true"></item>
    <item type="hidden" name="IU_ExcelArray"></item>
    <item type="button" name="IU_Button" value="Save"/>
</items>';
?>