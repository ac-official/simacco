<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$currYear   = date("Y");
echo '<items>
    <item type="settings" position="label-left" labelWidth="170" offsetLeft="20" offsetTop="0"/>
    <item type="block" width="100%" className="newBalSheet">   
        <item type="combo" label="Certificate/Document" name="ADOC_Id" serverFiltering="requisites/attestationDocuments.php" filterCache="false" required="true"  position="label-left" inputWidth="250" offsetTop="10" className="Doc_DocCombo" >
            <note>Certificate/Document</note>
        </item> 

        <item type="combo" label="State/Country" name="ST_Id" required="true" serverFiltering="requisites/attestationStates.php" filterCache="false"  position="label-left" inputWidth="250" className="Doc_StateCombo">
            <note>Certificate Issued State/Country</note>
        </item> 

        <item type="combo" label="University/Board/Council" name="AAUTH_Id" required="true" filterCache="false"  position="label-left" inputWidth="250" className="Doc_AuthCombo">
            <note>University/Board/Council</note>
        </item> 

        
        <item type="combo" label="Year" name="AJD_Year" inputWidth="250" required="true" readOnly="true" validate="NotEmpty">'; 
                echo '<option value=" " label="Select Year" />';
                for($i=$currYear;$i>=1970;$i--){
                    echo ' <option value="'.$i.'" label="'.$i.'" />';
                }
                echo'<note width="50">Year</note>
        </item>
        <item type="input" name="AJD_Amount" label="Amount" required="true" value="" inputWidth="250"  validate="NotEmpty,^([0-9]*|\d*\.\d{1}?\d*)$"  position="label-left">
            <note>Amount</note>
        </item>
        <item type="hidden" name="AJ_Id"> </item>
        <item type="hidden" name="ASD_Id"> </item>
        <item type="hidden" name="AJD_Id"> </item>
    </item>

    <item type="block" width="100%" className="newBalSheet">       
        <item type="button" value="SUBMIT" position="label-left" offsetLeft="30" name="saveNewTrackDocument"/>
        <item type="newcolumn"/>
        <item type="button" value="CANCEL" position="label-left" name="cancelNewTrackDocument"/>
        <item type="newcolumn"/>
        <item type="button" value="Add Process" position="label-left" width="130" offsetLeft="60" name="trackAddDocProcess"/>
    </item>
</items>';
?>