<?php
error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
require_once('../includes/sessions.php');

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$role = 'adm';
$ajax = 'true';



echo '<items>
		<item type="settings" position="label-left" labelWidth="0" inputWidth="250" offsetLeft="10"/>
			
			<item type="block" width="100%">
			
				<item type="label" label="asdf" labelWidth="100"></item>
				<item type="newcolumn"/>
				
				<item type="label" label="asdf" labelWidth="80"></item>
				<item type="newcolumn"/>
				
				<item type="label" label="asdf" labelWidth="80"></item>
				<item type="newcolumn"/>
				
				<item type="label" label="asdf" labelWidth="80"></item>
				<item type="newcolumn"/>
				
				
			</item>
			
			<item type="block" width="100%">
				
				<item type="label" label="asdf" labelWidth="100"></item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Item Title</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Amount</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Item Description</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="button" value="Save" name="doSentWelcomeMail"/>
				
			</item>
			
			<item type="block" width="100%">
				
				<item type="label" label="QWERTY" labelWidth="100"></item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Item Title</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Amount</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Item Description</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="button" value="Save" name="doSentWelcomeMail"/>
				
			</item>
			
			<item type="block" width="100%">
				
				<item type="label" label="POIUYU" labelWidth="100"></item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Item Title</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Amount</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="input" name="asdf" label="" value="" inputWidth="80">
					<note>Item Description</note>
				</item>
				<item type="newcolumn"/>
				
				<item type="button" value="Save" name="doSentWelcomeMail"/>
				
			</item>

	</items>';
?>