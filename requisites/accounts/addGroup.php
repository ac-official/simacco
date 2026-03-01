<?php
/** 
	* Add or edit Group names
	* Created By Bilin @ 11-07-2025
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj			= new AccountsClass();
$parent_id		= 10;
// get all  type 
$list_types 	= $accObj->ListGroupTypes();

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>';
	echo '<item type="hidden" name="eid" value="0"/>';
	echo '<item type="hidden" name="mgroup_id" value="'.$parent_id.'"/>';
	echo '<item type="hidden" name="allow_add" value="'.$UserACLObj->add_account_settings.'"/>';
	echo '<item type="settings" position="label-left" labelWidth="132" inputWidth="245" noteWidth="150" offsetLeft="20" offsetTop="8"  />';

	echo '<item type="combo" name="type"  label="Accounts Type" readonly="true" required="true" offsetTop="40">';
		echo '<option value="" selected="true" text="Select"/>';
        foreach ($list_types as $rk=>$rw) {
            echo '<option value="'.$rk.'" text="'.$rw.'"/>';
        }
    echo '</item>';
    echo '<item type="combo" name="parent_id" label="Parent Group" value="'.$parent_id.'" readonly="true"><option value="0" selected="true" text="Select"/></item>';

    echo '<item type="input" name="title" label="Group Name" required="true" validate="^[-_0-9a-zA-Z ]+$"></item>';

    echo '<item type="input" name="description" label="Group Description" rows="3"></item>';

	echo '<item type="block" width="300" offsetTop="1" offsetLeft="113">
			<item type="button" value="Save" name="saveAccGrp"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="CancelAccGrp"/>
        </item>';

echo '</items>';
?>