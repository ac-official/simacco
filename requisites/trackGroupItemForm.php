<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
    <item type="settings" position="label-left" noteWidth="10" />
        <item type="fieldset" inputWidth="" label="Create &amp; Manage Group"  width="332" offsetLeft="6">   
            <item type="hidden" name="AJG_Id" value="0"/>
            <item type="hidden" name="AJD_Id" value=""/>

            <item type="input" name="AJG_Name" inputWidth="285" label="" offsetTop="10" offsetLeft="" required="true" validate="^[a-zA-Z0-9 ]+$">
                <note width="">Group Name</note>
            </item>
            <!--item type="newcolumn"/-->
            
            <item type="block" width="" offsetTop="1" offsetLeft="2">
                <item type="button" value="Save" name="BtnSaveGroup"/>
                <item type="newcolumn"/>
                <item type="button" value="Clear" name="BtnClearGroup"/>
            </item>
        </item>
        <item type="block" width="" offsetTop="1" offsetLeft="0">
            <item type="button" value="Send Documents" name="sendDoc" width="95"/>
            <!--item type="newcolumn"/>
            <item type="button" value="Receive Documents" name="recvDoc" width="110"/-->
            <item type="newcolumn"/>
            <item type="button" value="Delivery Documents" name="devryDoc" width="110"/>
            <item type="newcolumn"/>
            <item type="button" value="Update Process" name="updtPros" width="95"/>
        </item>
</items>';