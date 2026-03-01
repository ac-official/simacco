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
                <item type="input" name="From_Date" label="From Date" value="'.$fromDate.'" offsetLeft="10" dateFormat="%d.%m.%Y"> </item> 
                <item type="input" name="AddedByFilter" label="Added By" > </item> 
                <item type="newcolumn" offset="0"></item>  
                <item type="input" name="To_Date" label="To Date" value="'.$toDate.'" offsetLeft="10" dateFormat="%d.%m.%Y"> </item> 
                <item type="input" name="BranchFilter" label="Branch" > </item> 
                <item type="newcolumn" offset="0"></item> 
                <item type="input" name="TrackIDFilter" label="Track ID" > </item> 
                <item type="hidden" name="shCRF"></item>
            </item>
            <item type="fieldset" name="fieldset2" labelWidth="400" inputWidth="630" position="label-right" label="Export Columns">
                <item type="checkbox" checked="true" position="label-right" name="Track" label="Track ID" offsetRight="50"></item> 
                <item type="checkbox" checked="true" position="label-right" name="Business" label="Business" offsetRight="50"></item> 
                <item type="checkbox" checked="true" position="label-right" name="BS_Date" label="Date" offsetRight="50"></item> 
                <item type="newcolumn" offset="0"></item>
                
                <item type="checkbox" checked="true" position="label-right"  labelWidth="100" offsetLeft="50" name="Amount_Received" label="Amount Received"></item> ';
                if($ACL_Obj->ACL_MasterReports == 1)
                    echo '<item type="checkbox" checked="true" position="label-right" offsetLeft="50"  name="Amount_Spend" label="Amount Spend"></item> ';
                echo '<item type="checkbox" checked="true" position="label-right" offsetLeft="50"  name="Branch" label="Branch"></item> 
                <item type="newcolumn" offset="0" ></item>     
                
                <item type="checkbox" checked="true" position="label-right" offsetLeft="50"  name="Current_Stock" label="Current Stock"></item> 
                <item type="checkbox" checked="true" position="label-right" offsetLeft="50"  name="AddedBy" label="Added By"></item> 
                <item type="newcolumn" offset="0" ></item>     
                
                <item type="hidden" name="r"></item>
                <item type="hidden" name="report_type"></item>
            </item>
            <item type="block" width="250" offsetTop="7" offsetLeft="100">
                <item type="button" value="Submit" name="SendMsgBtn"/>
                <item type="newcolumn"/>
                <item type="button" value="Cancel" name="SendMsgCancel"/>
            </item>
        </items>';