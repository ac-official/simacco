<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="100" inputWidth="400" offsetLeft="40" offsetTop="30"/>
           
                
                <item type="input" name="BR_Subject" label="Name of Problem" value="" inputWidth="500" offsetTop="20">
                    <note width="150">Name of Problem</note>
                </item> 
                <item type="combo" name="BR_Type" label="Type" value="" inputWidth="500" offsetTop="20" readonly="true">
                    <option value="1" label="Accounts" selected="true" />
                    <option value="2" label="Technical" selected="false" />
                    <option value="3" label="Others" selected="false" />
                    <note width="150">Type of Problem</note>
                </item> 
                <item type="input" name="BR_Desc" label="Details of Problem" value=""   rows="9" inputWidth="500">
                    <note width="150">Details of Problem</note>
                </item> 
                <item type="hidden" name="BR_Screen" label="Bug Screenshot" offsetTop="15" inputWidth="500">
                    <note width="150">Screenshot Image</note>
                </item> 
                               
                <item type="block" width="300" offsetTop="0" offsetLeft="400">
                    <item type="button" value="Submit" name="ReprtBugSend" offsetTop="0"/>
                    <item type="newcolumn"/>
                    <item type="button" value="Cancel" name="ReprtBugCancel" offsetLeft="0" offsetTop="0"/>
                </item>	
             
         
        </items>';
?>