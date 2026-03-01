<?php
/** 
	* Add or edit journal entries
	* Created By Bilin @ 11-11-2025
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
$ofquery 		= ($UserACLObj->manage_all_bills == 0) ? ' AND OF_Id='.$preTally_user_ofid:'';
$list_office 	= $accObj->getCustomField('offices', 'OF_Id, OF_Name, Sibling_id', ' WHERE OF_Status = 1'.$ofquery);
foreach ($list_office as $value) {
	if ($value->OF_Id == $preTally_user_ofid) {
		$Sibling_id  = $value->Sibling_id;
		break;
	}
}
// find all ledger 
$list_ledger 	= $accObj->getCustomField("acc_ledger AS l LEFT JOIN acc_ledger AS p ON (p.id =l.parent_id)", "l.id,l.title,p.title AS parents,l.parent_id", "WHERE l.status=1");

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo "<items>";
	echo "<item type='hidden' name='eid' value='0'/>";
	echo "<item type='hidden' name='branch_id' value='0'/>";
	echo "<item type='settings' position='label-left' labelWidth='132' inputWidth='245' noteWidth='150' offsetLeft='20' offsetTop='8'  />";

	echo "<item type='combo' offsetTop='30' name='ledger_cr' label='CR Account Name' required='true' validate='^[0-9]+$'>";
		echo "<option value=''  selected='true' text='Select CR Account'/>";
        foreach ($list_ledger as $rw) {        	
            echo "<option value='".$rw->id."' text='".$rw->title.($rw->parent_id > 0 ? " - (".$rw->parents.")":"")."'/>";
        }
    echo "</item>";

    echo "<item type='combo' name='ledger_dr' label='DR Account Name' required='true' validate='^[0-9]+$'>";
		echo "<option value=''  selected='true' text='Select DR Account'/>";
        foreach ($list_ledger as $rw) {        	
            echo "<option value='".$rw->id."' text='".$rw->title.($rw->parent_id > 0 ? " - (".$rw->parents.")":"")."'/>";
        }
    echo "</item>";

	echo "<item type='input' name='amount' label='Amount' required='true' validate='^[.0-9 ]+$' value='0'></item>"; 
	echo "<item type='calendar' name='date' label='Entry Date' value='' serverDateFormat='%Y-%m-%d' dateFormat='%d.%m.%Y' readonly='true' required='true'></item>";

	echo "<item type='combo' name='company_id'  label='Company Name' readonly='true' required='true' >";		
		foreach ($list_office as $rw) {
			if (($Sibling_id > 0 && $Sibling_id == $rw->Sibling_id) || $rw->OF_Id == $preTally_user_ofid) {

				$selected 		= ($rw->OF_Id == $preTally_user_ofid) ? "selected='true'":"";
            	echo "<option value='".$rw->OF_Id."' ".$selected." text='".$rw->OF_Name."'/>";
			}        	
        }
    echo "</item>";

    echo "<item type='combo' name='branch'  label='Branch Name:'>";		
		echo "<option value='0' selected='true' text='Select Branch'/>";
    echo "</item>";

	echo "<item type='block' width='300' offsetTop='1' offsetLeft='113'>
		<item type='button' value='Save' name='saveAccJEntry'/>
		<item type='newcolumn'/>
		<item type='button' value='Cancel' name='CancelAccJEntry'/>
    </item>";

echo "</items>";
?>