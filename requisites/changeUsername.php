<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$role = 'adm';
$ajax = 'false';
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
$GeneralObj = new GeneralClass();
$where  	= " WHERE US_Id = '".$preTally_user_id."'";
$US_UName  	= $GeneralObj->getValue('users_auth','US_UName',$where);

echo '<items>
	<item type="settings" position="label-left" labelWidth="150" inputWidth="330" noteWidth="180" offsetLeft="30"/>

		<item type="input" name="US_UName" label="Username" value="'.$US_UName.'" placeholder="Update Username" maxLength="20" validate="NotEmpty" required="true" offsetTop="35">
            <note width="330">Allow Letters &amp; digits. First character must be a letter. Length between 5 - 20.</note>
        </item>
		<item type="block" width="330" offsetTop="15">
			<item type="button" value="Save" name="newUsernameSave"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newUsernameCancel"/>
		</item>
		
	</items>';
?>