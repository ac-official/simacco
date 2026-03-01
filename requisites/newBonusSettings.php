<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
    <item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>		
    
    <item type="hidden" name="BPS_Id" value="0"/>
    
    <item type="combo" label="Year" name="BPS_Year"  offsetTop="30" readonly="true">';
        for($i = date("Y"); $i >= 2013 ; $i--)
            echo '<option value="'.$i.'" label="'.$i.'" />';

        echo '<note width="150">Year</note>
    </item>  
    
    <item type="combo" label="Month" name="BPS_Month" readonly="true" required="true">';
        echo '<option value = "" label = "Select" />';
        for ($m=1; $m <= 12; $m++) 
            echo '  <option value="'.$m.'" label = "'.date('F', mktime(0,0,0,$m)).'" />';

        echo '<note width="150">Month</note>
    </item>

    <item type="input" name="BPS_BranchShare" label="Branch Share" required="true" validate="ValidNumeric">
        <note width="150">Branch Share in Percentage</note>
    </item>
    
    <item type="input" name="BPS_GroupShare" label="Group Share" required="true" validate="ValidNumeric">
        <note width="150">Group Share in Percentage</note>
    </item>
   
    <item type="block" width="300" offsetTop="30">
        <item type="button" value="Save" name="newSettingsValidate"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="newSettingsCancel"/>
    </item>		
</items>';
?>
