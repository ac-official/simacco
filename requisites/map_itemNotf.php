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
	<item type="settings" position="label-left" labelWidth="80" inputWidth="250" noteWidth="180" offsetLeft="20"/>

		<item type="hidden" name="IT_Id" value="'.$REQUEST['IT_Id'].'"/>
                    <item type="input" label="Item Name" value="'.$REQUEST['IT_Name'].'" offsetTop="20" readonly="true">
                            <note width="150">Item Title</note>
		</item>
                
                <item type="combo" label="Entry Type" name="MH_Type" readonly="true" validate="NotEmpty" required="true" >
                        <option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" />
			<note>Select Income / Expense</note>
		</item>' ;
                if($offAdm == $preTally_user_id) { $validate = ' required="true" '; }  
                echo '<item type="combo" label="Subhead" name="SH_Id" '.$validate.' className="subheadcombo">
                    <note width="150">Subhead Name</note>
		</item>
		
                
                <item type="combo" label="Item" name="IT_MapId"  required="true" validate="NotEmpty" filterCache="true" className="itmapcombo">
                    <note width="150">Item Name</note>
                    <option width="150" value="" label="" selected="true" />
		</item>
                
                <item type="block" width="300" >
			<item type="button" value="Save" name="mapItemValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="mapItemCancel"/>
		</item>
      
	</items>';
?>