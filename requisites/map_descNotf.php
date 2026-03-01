<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$role = 'adm';
$ajax = 'false';

echo '<items>
	<item type="settings" position="label-left" labelWidth="90" inputWidth="250" noteWidth="180" offsetLeft="20"/>

		<item type="hidden" name="DS_Id" value="'.$REQUEST['DS_Id'].'"/>
                    <item type="input" label="Description Name" value="'.$REQUEST['DS_Name'].'" rows="3"  offsetTop="20" readonly="true">
                            <note width="150">Description Title</note>
		</item>
                
                <item type="combo" label="Description" name="DS_MapId"  required="true" validate="NotEmpty" >
                    <note width="150">Description Name</note>
                    <option width="150" value="" label="" selected="true" />
		</item>
                
                <item type="block" width="300" >
			<item type="button" value="Save" name="mapDescValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="mapDescCancel"/>
		</item>
      
	</items>';
?>