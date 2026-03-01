<?php
error_reporting(E_ALL ^ E_NOTICE);
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$gpAction = trim($REQUEST['gpAction']); 
$gid      = trim($REQUEST['gId']);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
    <item type="settings" position="label-left" labelWidth="150" inputWidth="380" noteWidth="180" />
    
    <item type="hidden" name="gid" value="'.$gid.'"></item>

    <item type="block" width="500" offsetTop="20">';
        if($gpAction == 'Send') {
            echo'<item type="combo" offsetLeft="20" name="LC_Id" label="Receiving Branch" required="true" width="250" className="GP_Brch_Combo">
                <note width="150">Receiving Branch</note>
            </item>
            
            <item type="newcolumn"/>
            
            <item type="input"  offsetLeft="20" name="AJED_Amount" inputWidth="250" label="Courier charge" offsetTop="" required="true" validate="^[0-9 ]+$">
                <note width="">Courier charge</note>
            </item>
            
            <item type="combo"  offsetLeft="20" name="BS_Description" inputWidth="250" label="Paid To" offsetTop="" className="AJ_descCombo" required="true" validate="^[a-zA-Z0-9 ]+$">
                <note width="">Paid To</note>
            </item>';
        }    
        if($gpAction=='Delivered') {
        echo'<item type="input" name="delvRemarks" label="Remark" value="" rows="5" offsetLeft="20"  width="300">
                <note width="150">Remark</note>
            </item>';
        }
    echo '</item>
    <item type="block" width="200" offsetTop="7" offsetLeft="280">
        <item type="button" value="Submit" name="GpSubmitBtn"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="GpCancel"/>
    </item>
</items>';