<?php
/** 
	* Add or edit Ledger and Sub ledger names
	* Created By Bilin @ 11-07-2025
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$editid 		= (isset($_REQUEST['eid'])) ? (int)$_REQUEST['eid']:0;
$group_id		= 0;
$parent_id		= 0;
$subgroup_id 	= 0;
$office_id 		= 0;
$type 			= '';
$title 			= '';
$description	= '';

// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj			= new AccountsClass();
// get all  type 
//$list_types 	= $accObj->ListGroupTypes();
// get all sub Group with type and group
$list_group 	= $accObj->listGroupsCbo(['status'=>1, 'parent_id'=>"-3"]);

$list_vendor 	= $accObj->getCustomField("acc_vendors", "id,name,gst_no", "WHERE status=1");

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>';
	echo '<item type="hidden" name="eid" value="'.$editid.'"/>';
	echo '<item type="hidden" name="mgroup_id" value="'.$group_id.'"/>';
	echo '<item type="hidden" name="sgroup_id" value="'.$subgroup_id.'"/>';
	echo '<item type="hidden" name="mledger_id" value="'.$parent_id.'"/>';
	echo '<item type="hidden" name="office_id" value="'.$office_id.'"/>';
	echo '<item type="hidden" name="allow_add" value="'.$UserACLObj->add_account_settings.'"/>';
	echo '<item type="settings" position="label-left" labelWidth="132" inputWidth="245" noteWidth="150" offsetLeft="20" offsetTop="8"  />';

	echo '<item type="combo" name="is_office"  label="Company" readonly="true" required="true" offsetTop="40">';		
		echo '<option value="0" selected="true" text="All Company"/>';
		echo '<option value="1" text="Only For This Company"/>';        
    echo '</item>';
    /*
	echo '<item type="combo" name="type"  label="Accounts Type" readonly="true" required="true" offsetTop="40">';
		$selected 		= ($type == '') ? 'selected="true"':'';
		echo '<option value=""  '.$selected.' text="Select Type"/>';
        foreach ($list_types as $rk=>$rw) {
        	$selected 	= ($type == $rk) ? 'selected="true"':'';
            echo '<option value="'.$rk.'" '.$selected.' text="'.$rw.'"/>';
        }
    echo '</item>';    

    echo '<item type="combo" name="group_id" label="Parent Group" value="'.$group_id.'"  required="true" validate="^[0-9]+$" ><option value="" selected="true" text="Select"/></item>';

    echo '<item type="combo" name="subgroup_id" label="Sub Group" value="'.$subgroup_id.'" required="true" validate="^[0-9]+$" ><option value="" selected="true" text="Select"/></item>';
    */
    echo '<item type="combo" name="subgroup_id"  label="Group / Sub Name" required="true"  validate="^[0-9]+$">';
		echo '<option value=""  selected="true" text="Select"/>';
        foreach ($list_group as $rw) {
        	
            echo '<option value="'.$rw->id.'" text="'.$rw->title.'"/>';
        }
    echo '</item>';

    echo '<item type="combo" name="parent_id" label="Parent Ledger Name" value="'.$parent_id.'"><option value="" selected="true" text="Select"/></item>';

    echo '<item type="combo" name="is_return"  label="Is Return Ledger" readonly="true" required="true">';		
		echo '<option value="0" selected="true" text="No"/>';
		echo '<option value="1" text="Yes"/>';        
    echo '</item>';

    echo '<item type="input" name="title" label="Ledger / Sub Name" required="true" validate="^[-_0-9a-zA-Z ]+$" value="'.$title.'"></item>';

    echo '<item type="combo" name="trans_type"  label="Transaction Type" readonly="true" required="true">';		
		echo '<option value="0" selected="true" text="Cash And Bank"/>';
		echo '<option value="1" text="Cash Only"/>';        
		echo '<option value="2" text="Bank Only"/>';        
    echo '</item>';

    echo '<item type="input" name="description" label="Ledger Description" rows="3" value="'.$description.'" />';

    echo '<item type="combo" name="vendor_id"  label="Vendor or Debtor/Creditor" validate="^[0-9]+$">';
		echo '<option value=""  selected="true" text="Select"/>';
        foreach ($list_vendor as $rw) {
        	
            echo '<option value="'.$rw->id.'" text="'.$rw->name.' - ('.$rw->gst_no.')"/>';
        }
    echo '</item>';

    echo '<item type="combo" name="is_contra"  label="Is Contra Ledger" readonly="true" required="true">';		
		echo '<option value="0" selected="true" text="No"/>';
		echo '<option value="1" text="Yes"/>';        
    echo '</item>';

    $list_ledger 	= $accObj->getCustomField("acc_ledger AS l LEFT JOIN acc_ledger AS p ON (p.id =l.parent_id)", "l.id,l.title,p.title AS parents,l.parent_id", "WHERE l.status=1");

    echo '<item type="combo" name="less_id"  label="Amount Less From" validate="^[0-9]+$">';
		echo '<option value="0"  selected="true" text="Select"/>';
        foreach ($list_ledger as $rw) {
        	
            echo '<option value="'.$rw->id.'" text="'.$rw->title.($rw->parent_id > 0 ? ' - ('.$rw->parents.')':'').'"/>';
        }
    echo '</item>';

    echo '<item type="combo" name="plus_id"  label="Amount Plus To"  validate="^[0-9]+$">';
		echo '<option value="0"  selected="true" text="Select"/>';
        foreach ($list_ledger as $rw) {
        	
            echo '<option value="'.$rw->id.'" text="'.$rw->title.($rw->parent_id > 0 ? ' - ('.$rw->parents.')':'').'"/>';
        }
    echo '</item>';

    echo '<item type="combo" name="is_same_side"  label="Shown In Same Side (Differnt Branch)" validate="^[0-9]+$">';
		echo '<option value="1"  selected="true"  text="Yes"/>';       
		echo '<option value="0"  text="No"/>';   
    echo '</item>';

    echo '<item type="combo" name="is_internal"  label="Transfer to (Branch/Company)" validate="^[0-9]+$">';		
		echo '<option value="0"  selected="true" text="No"/>';   
		echo '<option value="1"  text="Yes"/>';           
    echo '</item>';

    echo '<item type="combo" name="is_job_type"  label="is Job Ledger" validate="^[0-9]+$">';		
		echo '<option value="0"  selected="true" text="No"/>';   
		echo '<option value="1"  text="Yes"/>';           
    echo '</item>';

	echo '<item type="block" width="300" offsetTop="1" offsetLeft="113">
			<item type="button" value="Save" name="saveAccLedger"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="CancelAccLedger"/>
        </item>';

echo '</items>';
?>