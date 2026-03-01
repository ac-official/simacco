<?php
error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
require_once('../includes/sessions.php');

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
		<item type="settings" position="label-left" labelWidth="80" inputWidth="250" noteWidth="150" offsetLeft="20"/>
		
			<item type="combo" name="FL_Title" label="Title" value="" required="true" validate="NotEmpty" offsetTop="30">
				<note width="150">Filter Title</note>
			</item>
			
			<item type="input" name="FL_Description" label="Description" value="" rows="3">
				<note width="150">Filter Description</note>
			</item>
			
			<item type="input" name="asdf" label="Amount" value="">
				<note width="150">Amount</note>
			</item>
			
			<item type="combo" name="qwerty" label="User" value="" required="true" validate="NotEmpty">
				<note width="150">User</note>
			</item>
			
			<item type="block" offsetTop="50" width="350">
				<item type="button" value="Save &amp; Continue" name="doUserValidate"/>
				<item type="newcolumn"/>
				<item type="button" value="Exit" name="doSentWelcomeMail"/>
			</item>
	</items>';
?>