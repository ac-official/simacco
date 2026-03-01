<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
    }
echo '<?xml version="1.0" encoding="UTF-8"?>
<items>
    <item type="settings" position="label-left" labelWidth="130" inputWidth="270" noteWidth="150" offsetLeft="20" />
    <item type="combo" name="TK_Main" label="Main Process" readonly="true"></item>
    <item type="combo" name="TK_SubProcess" label="Sub Process" readonly="true"></item>
    <item type="combo" name="TK_Street" label="Street" readonly="true"></item>
    <item type="combo" name="TK_Place" label="Place" readonly="true"></item>
    <item type="combo" name="TK_Location" label="Location" readonly="true"></item>
    <item type="combo" name="TK_City" label="City" readonly="true"></item>
    <item type="combo" name="TK_State" label="State" readonly="true"></item>
    <item type="combo" name="TK_Country" label="Country" readonly="true"></item>
    <item type="combo" name="TK_Pincode" label="Pincode" readonly="true"></item>
    <item type="combo" name="TK_StatutoryAmt" label="Statutory Amount" readonly="true"></item>
    <item type="combo" name="TK_StatutoryUAmt" label="Statutory Amount - Urgent" readonly="true"></item>
    <item type="combo" name="TK_ExtraNAmt" label="Extra Amount - Normal" readonly="true"></item>
    <item type="combo" name="TK_ExtraUAmt" label="Extra Amount - Urgent" readonly="true"></item>
    <item type="combo" name="TK_CourierNAmt" label="Courier Charge - Normal" readonly="true"></item>
    <item type="combo" name="TK_CourierUAmt" label="Courier Charge - Urgent" readonly="true"></item>
    <item type="combo" name="TK_TravellingNAmt" label="Travelling Expense – Normal" readonly="true"></item>
    <item type="combo" name="TK_TravellingUAmt" label="Travelling Expense – Urgent" readonly="true"></item>
    <item type="combo" name="TK_ManpowerNAmt" label="Manpower Charge – Normal" readonly="true"></item>
    <item type="combo" name="TK_ManpowerUAmt" label="Manpower Charge – Urgent" readonly="true"></item>
    <item type="combo" name="TK_ServiceNAmt" label="Company Service – Normal" readonly="true"></item>
    <item type="combo" name="TK_ServiceUAmt" label="Company Service – Urgent" readonly="true"></item>
    <item type="combo" name="TK_FromYear" label="Company Service – Normal" readonly="true"></item>
    <item type="combo" name="TK_ToYear" label="Company Service – Urgent" readonly="true"></item>
    <item type="hidden" name="TK_ExcelArray"></item>
    <item type="button" name="TK_Button" value="Save"/>
</items>';