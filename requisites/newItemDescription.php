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
<item type="settings" position="label-left" labelWidth="120"  offsetTop="0" inputWidth="200" />
                
    <item type="block" width="100%" className="newItemDesc">   
        <item type="hidden" name="IT_Id" value=""/>
        <item type="hidden" name="DS_Id" value="0"/>

        <item type="input" name="DS_Description" label = "Description" value="" required="true" offsetLeft="23" offsetTop="20" inputWidth="415" position="label-left">
            <note> Description </note>
        </item>

        <item type="block" width="600" offsetLeft="20">
            <item type="template" label="&amp;nbsp;Expense Control" inputWidth="0"  ></item>
            <item type="newcolumn"></item>
            <item type="input" name="DS_MinAmount" offsetLeft="0" labelWidth="0" validate="^([0-9]*|\d*\.\d{1}?\d*)$" >
                <note width="150">Minimum Value</note>
            </item>
            <item type="newcolumn"></item>
            <item type="input" name="DS_MaxAmount" labelWidth="0" offsetLeft="10" validate="^([0-9]*|\d*\.\d{1}?\d*)$">
                <note width="150">Maximum Value</note>
            </item>
        </item> 



        <item type="newcolumn"/>';

        if($ACL_Obj->ACL_Item == 1) {
            echo '<item type="combo" label="Status" labelWidth="120" name="DS_Status" readonly="true" validate="NotEmpty"  offsetTop="11" offsetLeft="25">
                    <option value="1" label="Published" selected="true" />
                    <option value="0" label="Suspended" selected="false" />
                    <option value="4" label="Deleted" selected="false" />
                    <note width="70">Publish Status</note>
            </item>';
        } else {
            echo '<item type="hidden" name="DS_Status" value="0"/>';
        }

        echo '
        <item type="newcolumn"/>
        <item type="button" value="Save"  position="label-left" width = "80" name="newItemDescriptionValidate"  offsetLeft="15"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" position="label-left" width = "80" name="newItemDescriptionCancel" offsetLeft="10" />

    </item>

</items>';
?>