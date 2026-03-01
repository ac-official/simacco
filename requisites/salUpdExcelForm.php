<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
    }
echo '<?xml version="1.0" encoding="UTF-8"?>
<items>
    <item type="settings" position="label-left" labelWidth="130" inputWidth="150" noteWidth="150" offsetLeft="20" />
    <item type="combo" name="US_Id" label="User ID" readonly="true"></item>
    <item type="combo" name="SUpd_Amt" label="Amount" readonly="true"></item>
    <item type="combo" name="Su_Name" label="Name" readonly="true"></item>
    <item type="combo" name="Su_Month" label="Month" readonly="true"></item>
    <item type="hidden" name="SU_ExcelArray"></item>
    <item type="button" name="SU_Button" value="Save"/>
</items>';
?>