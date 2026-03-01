<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
    }
echo '<?xml version="1.0" encoding="UTF-8"?>
<items>
    <item type="settings" position="label-left" labelWidth="130" inputWidth="150" noteWidth="150" offsetLeft="20" />
    <item type="combo" name="IU_Company" label="Company" readonly="true"></item>
    <item type="combo" name="IU_Branch" label="Branch" readonly="true" ></item>
    <item type="combo" name="IU_UserID" label="User ID" readonly="true"></item>
    <item type="combo" name="IU_UserPswd" label="User Password" readonly="true" ></item>
    <item type="combo" name="IU_ACL" label="User ACL Type" readonly="true" ></item>
    <item type="combo" name="IU_Email" label="Company Email" readonly="true" ></item>
    <item type="combo" name="IU_FName" label="First Name" readonly="true" ></item>
    <item type="combo" name="IU_LName" label="Last Name" readonly="true"></item>
    <item type="combo" name="IU_JoinDate" label="Date of Join" readonly="true" ></item>
    <item type="combo" name="IU_Department" label="Department" readonly="true" ></item>
    <item type="combo" name="IU_Designation" label="Designation" readonly="true" ></item>
    <item type="combo" name="IU_EmpStatus" label="Employee Status" readonly="true" ></item>
    <item type="combo" name="IU_ReportsTo" label="Reports To" readonly="true" ></item>
    <item type="hidden" name="IU_ExcelArray"></item>
    <item type="button" name="IU_Button" value="Save"/>
</items>';

?>