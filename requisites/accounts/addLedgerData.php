<?php
/** 
	* Add or edit direct Ledger entries
	* Created By Bilin @ 14-11-2025
*/
if ( stristr($_SERVER['HTTP_ACCEPT'],'application/xhtml+xml')) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj			= new AccountsClass();

// get all active office list
$Sibling_id 	= 0;
$off_currency 	= [];
$list_office 	= $accObj->getCustomField('offices', 'OF_Id, OF_Name, Sibling_id, CR_Id', ' WHERE OF_Status = 1');
foreach ($list_office as $value) {
	$off_currency['"'.$value->OF_Id.'"'] = $value->CR_Id; // currency of offices
	if ($value->OF_Id == $preTally_user_ofid) {
		$Sibling_id  = $value->Sibling_id;
		break;
	}	
}
// find the combo listing array. of vendor..
$list_vendor 	= $accObj->getCustomField("acc_vendors", "id,name,gst_no", "WHERE status=1");

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo "<items>";
	echo "<item type='hidden' name='eid' value='0'/>";
	echo "<item type='hidden' name='branch_id' value='0'/>";
	echo "<item type='hidden' name='location_id' value='0'/>";
	echo "<item type='hidden' name='parent_id' value='0'/>";
	echo "<item type='hidden' name='ba_id' value='0'/>"; // bank account id
	echo "<item type='hidden' name='ba_id_debit' value='0'/>"; // bank account id debit	
	//echo "<item type='hidden' name='off_currency' value='".json_encode($off_currency)."'/>";
	echo "<item type='settings' position='label-left' labelWidth='132' inputWidth='245' noteWidth='150' offsetLeft='20' offsetTop='8'  />";

	echo "<item type='combo' name='office_id' offsetTop='30'  label='From Company Name' readonly='true' required='true' >";		
		foreach ($list_office as $rw) {
			if (($Sibling_id > 0 && $Sibling_id == $rw->Sibling_id) || $rw->OF_Id == $preTally_user_ofid) {

				$selected 		= ($rw->OF_Id == $preTally_user_ofid) ? "selected='true'":"";
            	echo "<option value='".$rw->OF_Id."' ".$selected." text='".$rw->OF_Name."'/>";
			}        	
        }
    echo "</item>";
    echo "<item type='combo' name='location'  label='Branch Name'>";		
		echo "<option value='0' selected='true' text='Select Branch'/>";
    echo "</item>";

	echo "<item type='combo' name='company_id'  label='To Company Name' readonly='true' required='true' >";		
		foreach ($list_office as $rw) {
			if (($Sibling_id > 0 && $Sibling_id == $rw->Sibling_id) || $rw->OF_Id == $preTally_user_ofid) {

				$selected 		= ($rw->OF_Id == $preTally_user_ofid) ? "selected='true'":"";
            	echo "<option value='".$rw->OF_Id."' ".$selected." text='".$rw->OF_Name."'/>";
			}        	
        }
    echo "</item>";
    echo "<item type='combo' name='branch'  label='Branch Name'>";		
		echo "<option value='0' selected='true' text='Select Branch'/>";
    echo "</item>";

	$list_ledger 	= $accObj->getCustomField("acc_ledger AS l LEFT JOIN acc_ledger AS p ON (p.id =l.parent_id)", "l.id,l.title,p.title AS parents,l.parent_id", "WHERE l.status=1");
	echo "<item type='combo' name='ledger_id' required='true'   label='Ledger Name' validate='^[0-9]+$'>";
	echo "<option value=''  selected='true' text='Select'/>";
    foreach ($list_ledger as $rw) {
    	
        echo "<option value='".$rw->id."' text='".$rw->title.($rw->parent_id > 0 ? " - (".$rw->parents.")":"")."'/>";
    }
	echo "</item>";

	echo "<item type='calendar' name='date_entry' label='Date' value='".date('Y-m-d')."' serverDateFormat='%Y-%m-%d' dateFormat='%d.%m.%Y' required='true'  readonly='true'></item>";

	echo "<item type='input' name='amount' label='Total Amount' required='true' validate='^[.0-9 ]+$' value='0'></item>";

	echo "<item type='input' name='amount_ratio' label='Converted Ratio' validate='^[.0-9 ]+$' value='0'></item>";
	echo "<item type='input' name='converted' label='Converted Amount' validate='^[.0-9 ]+$' value='0'></item>";

	echo "<item type='combo' name='amt_type' label='Cash or Bank'>";
		echo "<option value='1' text='Bank'/>";
		echo "<option value='2'  selected='true' text='Cash'/>";
    echo "</item>";    
    echo "<item type='combo' name='bankaccountdr'  label='Bank Account (Debit)'>";		
		echo "<option value='0' selected='true' text='Select Bank'/>";
    echo "</item>";
    echo "<item type='combo' name='bankaccount'  label='Bank Account (Credit)'>";		
		echo "<option value='0' selected='true' text='Select Bank'/>";
    echo "</item>";

	echo "<item type='combo' name='vendor_id' label='Vendor Name' validate='^[0-9]+$'>";
		echo "<option value=''  selected='true' text='Select Vendor'/>";
        foreach ($list_vendor as $rw) {        	
            echo "<option value='".$rw->id."' text='".$rw->name."'/>";
        }
    echo "</item>";

    echo "<item type='input' name='remarks' label='Remarks' rows='3' value='' validate='^[-_.0-9a-zA-Z ]+$' />";

    echo "<item type='input' name='trackno' label='Track No'  value=''/>";

    echo "<item type='combo' name='ie_type' label='Add/ Subtract'>";
		echo "<option value='sub' text='Subtract'/>";   
		echo "<option value='add' selected='true' text='Add'/>";     
    echo "</item>";

    echo "<item type='block' width='300' offsetTop='1' offsetLeft='113'>
		<item type='button' value='Save' name='saveLedgerData'/>
		<item type='newcolumn'/>
		<item type='button' value='Cancel' name='cancelLedgerData'/>
    </item>";

echo "</items>";
?>