<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$role = 'adm';
$ajax = 'false';
echo '<items>
	<item type="settings" position="label-left" labelWidth="150" inputWidth="250" noteWidth="180" offsetLeft="30"/>
		<item type="hidden" name="IT_Id" value="0"/>
		<item type="password" name="currPassword" label="Current Password" value="" offsetTop="35" required="true" validate="NotEmpty" >
			<note width="150">Current Password</note>
		</item>
        <item type="password" name="newPassword" label="New Password" value="" offsetTop="15" required="true" validate="NotEmpty" >			
			<note width="315">Contain one digit, one small &amp; cap Letter &amp; a special character. Length (5-20).</note>	
		</item>
        <item type="password" name="rePassword" label="Confirm Password" value="" offsetTop="15" required="true" validate="NotEmpty" >
			<note width="315">Contain one digit, one small &amp; cap Letter &amp; a special character. Length (5-20).</note>	
		</item>
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newPasswordSave"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newPasswordCancel"/>
		</item>
		
	</items>';
?>