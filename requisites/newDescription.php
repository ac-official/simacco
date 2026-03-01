<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
$ItemObj = new ItemClass();
$OffObj = new OfficeClass();

$ItemObj->myMapItem($preTally_user_ofid);    
$Map_Obj = $ItemObj->ItemMapArray;

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);

//$old = array('"', "[", "]");
//$new   = array("", "", "");
//$itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
if($itemMap == '') $itemMap = '""';

if($preTally_user_ofid == 1) {
    $flds   =   'IT.IT_Id, IT.IT_Name';
    $tbls   =   'as IT, main_heads as MH, sub_heads as SH';
    $filter =   'IT.IT_Status = 1 AND IT.SH_Id=SH.SH_Id AND SH.MH_Id=MH.MH_Id';
}else {
    $flds   =   'IT.IT_Id, IT_Name';
    $tbls   =   'as IT, main_heads as MH, sub_heads as SH';
    $filter =   "IT.IT_Status = 1 AND IT.SH_Id=SH.SH_Id AND SH.MH_Id=MH.MH_Id AND (IT.OF_Id=".$preTally_user_ofid." OR IT.IT_Id IN (".$itemMap."))";
}
$ItemObj->getItemList($flds,$tbls,'WHERE  '.$filter.' ORDER BY IT.IT_Name');
$IT_Obj = $ItemObj->ItemArray;

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="DS_Id" value="0"/>
		
                <item type="combo" label="Type of Entry" name="MH_Type" readonly="true" validate="NotEmpty" required="true" offsetTop="30">
                        <option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" />
			<note>Select Income / Expense</note>
		</item>' ;
                if($offAdm == $preTally_user_id) { $validate = ' required="true" '; }  
                echo '<item type="combo" label="Subhead" name="SH_Id" readonly = "true" '.$validate.'>
                    <note width="150">Subhead Name</note>
		</item>
                
                <item type="combo" label="Item" name="IT_Id" required="true" validate="NotEmpty" readonly="true" >';
//                    if($IT_Obj) {
//                        foreach($IT_Obj as $rw) {
//                            echo '<option value="'.$rw->IT_Id.'" label="'.$rw->IT_Name.'" selected="false" />';
//                        }
//                    }
                echo '<note width="150">Item Name</note>
		</item>

                <item type="input" name="DS_Description[1]" label="Description 1" required="true" value="" rows="3" >
			<note width="150">Description</note>
		</item>
                <item type="block" width="300">
                <item type="button" name="addDescription" value=" + " className="descClass" tooltip= "Click to Add new Description for Same Item." offsetLeft ="100"></item>
                <item type="newcolumn"/>
                <item type="button" name="removeDescription" value=" - " tooltip= "Click to Remove Last Description"></item>
                </item>';
                if($offAdm == $preTally_user_id) {
                    echo '<item type="combo" label="Status" name="DS_Status" readonly="true">
                            <option value="1" label="Published" selected="true" />
                            <option value="0" label="Suspended" selected="false" />
                            <option value="4" label="Deleted" selected="false" />
                            <note width="150">Publish Status</note>
                    </item>';
                } else {
                    echo '<item type="hidden" name="DS_Status" value="0"/>';
                }

		echo '
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newDescriptionValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newDescriptionCancel"/>
		</item>
                
	</items>';
?>