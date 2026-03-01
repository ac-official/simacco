<?php
/** 
	* Add or edit TDS names
	* Created By Bilin @ 03-09-2025
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// accounts settings related class
//include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
//$accObj			= new AccountsClass();

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>';
	echo '<item type="hidden" name="eid" value="0"/>';
	echo '<item type="hidden" name="allow_add" value="'.$UserACLObj->add_account_settings.'"/>';
	echo '<item type="settings" position="label-left" labelWidth="132" inputWidth="245" noteWidth="150" offsetLeft="20" offsetTop="8"  />';

	echo '<item type="input" offsetTop="30" name="rule_name" label="Rule Name" required="true" validate="^[-_0-9a-zA-Z ]+$" value=""></item>';

	echo '<item type="input" name="section" label="Section No" required="true" validate="^[-_0-9a-zA-Z ]+$" value=""></item>';

	echo '<item type="input" name="code" label="Section Code" required="true" validate="^[-_0-9a-zA-Z ]+$" value=""></item>';

	echo '<item type="input" name="percentage" label="Percentage" required="true" validate="^[.0-9 ]+$" value="0"></item>';

	echo '<item type="calendar" name="start_date" label="Start Date" value="'.date('Y-m-d').'" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" required="true"  readonly="true"></item>';

	echo '<item type="block" width="300" offsetTop="1" offsetLeft="113">
		<item type="button" value="Save" name="saveAccTds"/>
		<item type="newcolumn"/>
		<item type="button" value="Cancel" name="CancelAccTds"/>
    </item>';
echo '</items>';
?>