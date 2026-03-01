<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
$fromDate   = $REQUEST['BForm_Inp_Frm'];
$toDate     = $REQUEST['BForm_Inp_Til'];

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
    <item type="settings" position="label-left" labelWidth="80" inputWidth="100" offsetLeft="10" />
        <item type="fieldset" name="fieldset1" labelWidth="10" position="label-right" label="Filters" inputWidth="630">    
            <item type="input" name="From_Date" label="From Date" value="'.$fromDate.'" dateFormat="%d.%m.%Y"> </item>\
            <item type="input" name="ItemNameFilter" label="Name of Income or Expense to search" > </item> 
            <item type="newcolumn" offset="0"></item>  
            <item type="input" name="To_Date" label="To Date" value="'.$toDate.'" dateFormat="%d.%m.%Y"> </item> 
            <item type="input" name="BranchFilter" label="Branch" > </item> 
            <item type="newcolumn" offset="0"></item>
            <item type="combo" name="TypeFilter" inputWidth="80" label="Type">
                <option value ="0" label="All" />
                <option value="1" label="Income" />
                <option value="2" label="Expense" />
            </item>
            <item type="input" name="AddedByFilter" label="Added By" > </item> 
            <item type="hidden" name="shCRF"></item>
        </item>
        <item type="fieldset" name="fieldset2" labelWidth="250" inputWidth="630" position="label-right" label="Export Columns">
            <item type="checkbox" checked="true" position="label-right" name="Type" label="Type" offsetRight="50"></item> 
            <item type="checkbox" checked="true" position="label-right" name="Name" offsetRight="50" label="Name of Income/Expense"></item> 
            <item type="newcolumn" offset="0"></item>

            <item type="checkbox" checked="true" position="label-right" name="Amount" offsetLeft="50" label="Amount"></item> 
            <item type="checkbox" checked="true" position="label-right" name="Branch" offsetLeft="50" label="Branch"></item> 
            <item type="newcolumn" offset="0" ></item>     

            <item type="checkbox" checked="true" position="label-right" offsetLeft="50" name="AddedBy" label="Added By"></item> 
            <item type="checkbox" checked="true" position="label-right" offsetLeft="50" name="BS_Date" label="Date"></item> 
            <item type="hidden" name="r"></item>
            <item type="hidden" name="report_type"></item>
        </item>
        <item type="block" width="250" offsetTop="7" offsetLeft="100">
            <item type="button" value="Submit" name="SendMsgBtn"/>
            <item type="newcolumn"/>
            <item type="button" value="Cancel" name="SendMsgCancel"/>
        </item>
</items>';