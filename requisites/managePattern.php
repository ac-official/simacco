<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/SubheadClass.php");

$SubheadObj = new SubheadClass();
$SubheadObj->viewSubheads(' WHERE SH_Status = 1 ORDER BY SH_Name');
$SH_Obj = $SubheadObj->SubheadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="400" offsetLeft="20" offsetTop="30"/>
            <item type="combo" label="Select Subhead" name="SH_Id" readonly="true">
                <option value="0" label="Select Subhead"></option>';
                if($SH_Obj){
                    foreach($SH_Obj as $rw)
                    {
                        echo '<option value="'.$rw->SH_Id.'" '.$selected.' label ="'.str_replace("&","&amp;",$rw->SH_Name).'"></option>';
                    }
                }
            echo '</item>
	</items>';
?>