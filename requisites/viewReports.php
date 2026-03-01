<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>	
        <item type="settings" position="label-left" labelWidth="0" inputWidth="180" offsetLeft="5"/>
        
        <item type="block" width="500"> 
            <item type="fieldset" inputWidth="auto" label="INCOME TOTAL">
                <item type="input" name="INCOME" label="" value=" '.$currency.' 0" offsetTop="5" required="true" ></item>
            </item>

            <item type="newcolumn" /> 
            
            <item type="fieldset" inputWidth="auto" label="EXPENSE TOTAL" labelWidth="0">
                <item type="input" name="EXPENSE" label="" value=" '.$currency.' 0" offsetTop="5" required="true" ></item> 
            </item>
        </item>
        
        <item type="fieldset" inputWidth="auto" label="PROFIT / LOSS" offsetLeft="30">
            <item type="input"  name="PL_Data" label="" value="Select Office / Branch / User"  inputWidth="420" />
        </item>
    </items>';
?>