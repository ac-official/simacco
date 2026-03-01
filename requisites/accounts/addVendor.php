<?php
/** 
	* Add or edit Vendor names
	* Created By Bilin @ 03-09-2025
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// accounts settings related class
include_once($BASEPATH . "preTallyClass/LocationClass.php"); 
$locObj			= new LocationClass();
$listdata  		= $locObj->listCountry();
 
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>';
	echo '<item type="hidden" name="eid" value="0"/>';
	echo '<item type="hidden" name="allow_add" value="'.$UserACLObj->add_account_settings.'"/>';
	echo '<item type="settings" position="label-left" labelWidth="132" inputWidth="245" noteWidth="150" offsetLeft="20" offsetTop="8"  />';

	echo '<item type="combo" name="type" label="Type" required="true" validate="^[0-9]+$"  offsetTop="30">';
		echo '<option value="1"  selected="true" text="Vendor"/>';
		echo '<option value="2"  text="Debtor"/>';
		echo '<option value="3"  text="Creditor"/>';
    echo '</item>';

	echo '<item type="input" name="name" label="Display Name" required="true" validate="^[-_0-9a-zA-Z ]+$" value="" />';

	echo '<item type="input" name="address" label="Full Address" rows="3" value="" />';

	echo '<item type="combo" name="country_id" label="Country Name" required="true" validate="^[0-9]+$">';
		echo '<option value=""  selected="true" text="Select"/>';
        foreach ($listdata as $rw) {        	
            echo '<option value="'.$rw->id.'" text="'.htmlentities($rw->name).'"/>';
        }
    echo '</item>';

	echo '<item type="input" name="email" label="Email Address" validate="ValidEmail" value="" />';

	echo '<item type="input" name="phone" label="Contact Number" validate="^[-_+0-9 ]+$" value="" />';

	echo '<item type="input" name="gst_no" label="GST No" validate="^[-_0-9a-zA-Z ]+$" value=""></item>';

	echo '<item type="input" name="pan_no" label="PAN No" validate="^[-_0-9a-zA-Z ]+$" value=""></item>';
	
	echo '<item type="block" width="300" offsetTop="1" offsetLeft="113">
		<item type="button" value="Save" name="saveAccVendor"/>
		<item type="newcolumn"/>
		<item type="button" value="Cancel" name="CancelAccVendor"/>
    </item>';
echo '</items>';
?>

