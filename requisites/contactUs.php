<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$role = 'adm';
$ajax = 'false';
echo '<items>
	<item type="settings" position="label-left" labelWidth="120"  offsetLeft="20" />
        <item type="fieldset" id="contact" offsetTop="5" offsetLeft="10" label="CONTACT US" width="500" noteWidth="180">
        <item type="input" name="Contact_Name" label="Name" value=""  required="true"  inputWidth="250">
            <note width="150">Name</note>
	</item> 
        <item type="input" name="Contact_Email" label="Email" value=""  required="true"  inputWidth="250">
            <note width="150">Email ID</note>
	</item> 
        <item type="input" name="Contact_Phone" label="Phone" value=""  required="true"  inputWidth="250">
            <note width="150">Landline / Mobile</note>
	</item> 
        <item type="input" name="Contact_Message" label="Message" value=""  required="true" rows="3" inputWidth="250">
            <note width="150">Message</note>
	</item> 
        <item type="block" width="300" offsetTop="29">
			<item type="button" value="Send" name="ContactSend"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="ContactCancel"/>
		</item>	
        </item>
        		<item type="newcolumn"/>
        <item type="newcolumn"/>
        <item type="fieldset" id="contact1" offsetTop="5"  label="OFFICES" width="500">
        <item type="fieldset" id="contact2" offsetTop="5"  label="Corporate Office" width="400" inputWidth="200">
         <item type="template" value="Muble Solutions" inputWidth="150"></item>
         <item type="template" value="Shreeje Towers"></item>
         <item type="template" value="Shenoys"></item>
         <item type="template" value="Ernakulam,682035"></item>         
         <item type="newcolumn" offsetLeft="10"/>
         <item type="template" name="MyContainer"  className="logo_field" >
         
        </item>
        </item>
        <item type="fieldset" id="contact3" offsetTop="5" label="Customer Care" width="400">
         <item type="template" label="Pretally Team" value=": contact@pretally.in"></item>
         <item type="template" label="Support Team" value=": support@pretally.in"></item>
         <item type="template"  label="Administrator" value=": admin@pretally.in"></item>         
        </item>
       <item type="fieldset" id="contact4" offsetTop="5" label="Documents" width="400" >
                <item type="template" label="Release Notes" value=": Download Here &#x25BE;"></item>
                <item type="template" label="Configuration" value=": Download Here &#x25BE;"></item>                
                <item type="template" label="Instruction Manual" value=": Download Here &#x25BE;"></item>        
        </item>
        </item>
        </items>';
?>