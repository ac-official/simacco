<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
$OffObj = new OfficeClass();
$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
<item type="settings" position="label-left" labelWidth="0" offsetLeft="20" offsetTop="0" />
                <item type="hidden" name="DS_Description" value=""/>
                <item type="hidden" name="IT_Id" value=""/>
                <item type="hidden" name="DS_Id" value="0"/>
                
                <item type="button" value="Branch" offsetTop="15" position="label-left" width = "70" name="newLCDescription"/>
                <item type="newcolumn"/>
                <item type="button" value="Bank" offsetTop="15" position="label-left" width = "70" name="newBNKDescription"/>
                <item type="newcolumn"/>
                <item type="button" value="User" offsetTop="15" position="label-left" width = "70" name="newUSDescription"/>

	</items>';
?>