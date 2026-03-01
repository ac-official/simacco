<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
    }
echo '<?xml version="1.0" encoding="UTF-8"?>
<items>
    <item type="settings" position="label-left" labelWidth="130" inputWidth="150" noteWidth="150" offsetLeft="20" />
    <item type="combo" name="US_Id" label="User ID" readonly="true"></item>
    <item type="combo" name="LT_Name" label="Leave Type" readonly="true"></item>
    <item type="combo" name="Date" label="Date" readonly="true"></item>
    <item type="combo" name="Session" label="Session" readonly="true"></item>
    <item type="hidden" name="LT_ExcelArray"></item>
    <item type="button" name="LT_Button" value="Save"/>
</items>';
?>