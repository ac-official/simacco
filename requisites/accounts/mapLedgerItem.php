<?php
/**
	* map ledger and items 
	* items listed based on the logined user office id
	* select type, group and ledger and map sub head and office based item
	* Created BY bilin @ 17-07-2025
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj			= new AccountsClass();

// get all  type 
//$list_types 	= $accObj->ListGroupTypes();
// get all ledger parent with type and group
$list_ledger 	= $accObj->listLedgerCbo(['status'=>1, 'parent_id'=>"0", 'office_id'=>$preTally_user_ofid]);
//sub item head
$item_heads 	= $accObj->getCustomField("sub_heads", "SH_Id,SH_Name", "WHERE SH_Status=1 ORDER BY SH_Name");

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>';
	echo '<item type="hidden" name="eid" value="0"/>';
	echo '<item type="hidden" name="mgroup_id" value="0"/>';
	echo '<item type="hidden" name="sgroup_id" value="0"/>';
	echo '<item type="hidden" name="mledger_id" value="0"/>';
	echo '<item type="hidden" name="sledger_id" value="0"/>';
	echo '<item type="hidden" name="office_id" value="'.$preTally_user_ofid.'"/>';
	echo '<item type="hidden" name="sitem_id" value="0"/>';    
    echo '<item type="hidden" name="allow_add" value="'.$UserACLObj->add_account_settings.'"/>';

	echo '<item type="fieldset" label="New Ledger" offsetLeft="10" offsetTop="20" width="380" >';
	echo '<item type="settings" position="label-left" labelWidth="110" inputWidth="215" noteWidth="150" offsetLeft="1" offsetTop="10"  />';
	/*echo '<item type="combo" name="type"  label="Accounts Type" readonly="true" required="true">';
		echo '<option value=""  selected="true" text="Select Type"/>';
        foreach ($list_types as $rk=>$rw) {
        	
            echo '<option value="'.$rk.'" text="'.$rw.'"/>';
        }
    echo '</item>';

    echo '<item type="combo" name="group_id" label="Parent Group" required="true" validate="^[0-9]+$" ><option value="" selected="true" text="Select"/></item>';

    echo '<item type="combo" name="subgroup_id" label="Sub Group" required="true"  validate="^[0-9]+$"><option value="" selected="true" text="Select"/></item>';

    echo '<item type="combo" name="ledger_id" label="Parent Ledger" required="true" validate="^[0-9]+$"><option value="" selected="true" text="Select"/></item>';
    */

    echo '<item type="combo" name="ledger_id"  label="Parent Ledger" required="true"  validate="^[0-9]+$">';
		echo '<option value=""  selected="true" text="Select"/>';
        foreach ($list_ledger as $rw) {
        	
            echo '<option value="'.$rw->id.'" text="'.$rw->title.'"/>';
        }
    echo '</item>';

    echo '<item type="combo" name="subledger_id" label="Sub Ledger"><option value="" selected="true" text="Select"/></item>';
    echo '</item>';
    

    echo '<item type="fieldset" label="Old Items" offsetLeft="10" offsetTop="10" width="380" >';
    echo '<item type="settings" position="label-left" labelWidth="110" inputWidth="215" noteWidth="150" offsetLeft="1" offsetTop="10"  />';
    echo '<item type="combo" name="subhead_id" label="Item Sub Head">';
		echo '<option value="" selected="true" text="Select"/>';
        foreach ($item_heads as $rw) {
        	
            echo '<option value="'.$rw->SH_Id.'" text="'.$rw->SH_Name.'"/>';
        }
    echo '</item>';

    echo '<item type="combo" name="item_id" label="Item Name" required="true" validate="^[0-9]+$"><option value="" selected="true" text="Select or Search"/></item>';
    echo '</item>';
    echo '<item type="block" width="300" offsetTop="1" offsetLeft="112">
			<item type="button" value="Save" name="saveMapLedger"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="CancelMapLedger"/>
        </item>';

echo '</items>';
?>