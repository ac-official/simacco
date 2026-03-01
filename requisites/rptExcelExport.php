<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$role = 'adm';
$ajax = 'true';

echo '<items>
        <item type="settings" position="label-left" labelWidth="0" inputWidth="100" offsetLeft="10" />
        
        <item type="fieldset" labelWidth="0" position="label-right"  label="Report Details" inputWidth="750" offsetTop="10" >
            <item type="hidden" name="OF_Id" value="'.$preTally_user_ofid.'"></item>
            
            <item type="combo" name="rpt_Type"  labelWidth="0" inputWidth="250" required="true" validate="NotEmpty" >
                <option value="" label="Select" selected="true" />
                <option value="stk_Rpt" label="Stock Reports - Transaction Based" selected="false" />
                <option value="tallyEx_Rpt" label="Tally Reports - Transaction Based" selected="false" />
                <option value="tallyEx_RptCash" label="Tally Reports - Transaction Based Cash Only" selected="false" />
                <option value="tallyEx_RptBank" label="Tally Reports - Transaction Based Bank Only" selected="false" />
                <note width="150">Report Type</note>
            </item>
            
            <item type="newcolumn" ></item>
               
            <item type="input" name="rpt_From" value="'. date('01.m.Y') .'"> <note>From</note> </item> 
            
            <item type="newcolumn" ></item>
            
            <item type="input" name="rpt_To" value= "'.date("d.m.Y").'" > <note>Till</note> </item> 
        
            <item type="newcolumn" ></item>
            
            <item type="button" value="Export" name="rptXLExport" offsetTop="0" />
            <item type="newcolumn"/>
            <item type="button" value="Cancel" name="rptXLCancel" offsetTop="0" />
            
        </item> 
    </items>';
?>