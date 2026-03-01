<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="150" inputWidth="250" noteWidth="0" offsetLeft="15"/>
		<item type="hidden" name="ResUS_Id" value=""/>
		<item type="password" name="newPassword" label="Password" value="" offsetTop="10" required="true" validate="NotEmpty" >	
		<note width="315">Contain one digit, one small &amp; cap Letter &amp; a special character. Length (5-20).</note>		
		</item>
        <item type="password" name="confrmPassword" label="Confirm Password" value="" offsetTop="10" required="true" validate="NotEmpty" >
            <note width="315">Contain one digit, one small &amp; cap Letter &amp; a special character. Length (5-20).</note>			
		</item>
		<item type="block" width="300" offsetTop="0">
			<item type="button" value="Reset" name="admPassSave"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="admPassCancel"/>
		</item>
		
	</items>';
?>