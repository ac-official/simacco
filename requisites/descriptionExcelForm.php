<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
    }
echo '<?xml version="1.0" encoding="UTF-8"?>
<items>
    <item type="settings" position="label-left" labelWidth="130" inputWidth="150" noteWidth="150" offsetLeft="20" />
    <item type="combo" name="DS_Description" label="Description" readonly="true"></item>
    <item type="combo" name="IT_Name" label="Item Name" readonly="true"></item>
    <item type="hidden" name="DS_ExcelArray"></item>
    <item type="button" name="DS_Button" value="Save"/>
</items>'
?>