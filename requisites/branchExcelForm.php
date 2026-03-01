<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
    }
echo '<?xml version="1.0" encoding="UTF-8"?>
<items>
    <item type="settings" position="label-left" labelWidth="130" inputWidth="150" noteWidth="150" offsetLeft="20" />
    <item type="combo" name="IBB_Title" label="Office Name" readonly="true"></item>
    <item type="combo" name="IBB_Office" label="Company" readonly="true"></item>
    <item type="combo" name="IBB_Phone" label="Phone Number" readonly="true"></item>
    <item type="combo" name="IBB_Building" label="Building Name/Number" readonly="true"></item>
    <item type="combo" name="IBB_Street" label="Street Name" readonly="true"></item>
    <item type="combo" name="IBB_Place" label="Place" readonly="true"></item>
    <item type="combo" name="IBB_Location" label="Location/Place" readonly="true"></item>
    <item type="combo" name="IBB_City" label="City" readonly="true"></item>
    <item type="combo" name="IBB_State" label="State" readonly="true"></item>
    <item type="combo" name="IBB_Country" label="Country" readonly="true"></item>
    <item type="combo" name="IBB_Pincode" label="Pincode" readonly="true"></item>
    <item type="combo" name="IBB_Remarks" label="Remarks" readonly="true"></item>
    <item type="hidden" name="IBB_ExcelArray"></item>
    <item type="button" name="IBB_Button" value="Save"/>
</items>'
?>