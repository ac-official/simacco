<?php
/** 
	* Add or Update Extra data uploaded and newly added
	* Created By Bilin @ 09-01-2026
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
$list_office 	= $accObj->getCustomField('offices', 'OF_Id, OF_Name, Sibling_id', ' WHERE OF_Status = 1');
foreach ($list_office as $value) {
	if ($value->OF_Id == $preTally_user_ofid) {
		$Sibling_id  = $value->Sibling_id;
		break;
	}
}
// get all  type 
$list_types 	= $accObj->ListGroupTypes(1);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo "<items>";
	echo "<item type='hidden' name='eid' value='0'/>";	
	echo "<item type='hidden' name='branch_name' value=''/>";
	echo "<item type='hidden' name='addtype' value='1'/>";

	echo "<item type='settings' position='label-left' labelWidth='132' inputWidth='245' noteWidth='150' offsetLeft='20' offsetTop='8'  />";

	echo "<item type='combo' name='company_id' offsetTop='30'  label='Company Name' readonly='true' required='true' >";		
		foreach ($list_office as $rw) {
			if (($Sibling_id > 0 && $Sibling_id == $rw->Sibling_id) || $rw->OF_Id == $preTally_user_ofid) {

				$selected 		= ($rw->OF_Id == $preTally_user_ofid) ? "selected='true'":"";
            	echo "<option value='".$rw->OF_Id."' ".$selected." text='".$rw->OF_Name."'/>";
			}        	
        }
    echo "</item>";

    echo "<item type='combo' name='branch'  label='Branch Name'>";	
    echo "</item>";

    echo "<item type='combo' name='type'  label='Accounts Type' readonly='true' required='true'>";
		echo "<option value='' selected='true' text='Select'/>";
        foreach ($list_types as $rk=>$rw) {
            echo "<option value='".$rk."' text='".$rw."'/>";
        }
    echo "</item>";

    $list_ledger 	= $accObj->getCustomField("acc_ledger AS l LEFT JOIN acc_ledger AS p ON (p.id =l.parent_id)", "l.id,l.title,p.title AS parents,l.parent_id", "WHERE l.status=1 ORDER BY l.title ASC");
	echo "<item type='combo' name='ledger_id' readonly='true'  label='Ledger Name' validate='^[0-9]+$'>";
	echo "<option value=''  selected='true' text='Select'/>";
    foreach ($list_ledger as $rw) {
    	
        echo "<option value='".$rw->id."' text='".$rw->title.($rw->parent_id > 0 ? " - (".$rw->parents.")":"")."'/>";
    }
	echo "</item>";
    
    echo "<item type='input' name='title' label='Display Title' required='true' validate='^[-_0-9a-zA-Z ]+$'></item>";    

	echo "<item type='calendar' name='job_date' label='Date' value='".date('Y-m-d')."' serverDateFormat='%Y-%m-%d' dateFormat='%d.%m.%Y' required='true'  readonly='true'></item>";

	echo "<item type='input' name='amount' label='Total Amount' required='true' validate='^[.0-9 ]+$' value='0'></item>";

	echo "<item type='block' width='300' offsetTop='1' offsetLeft='113'>
		<item type='button' value='Save' name='saveExtaData'/>
		<item type='newcolumn'/>
		<item type='button' value='Cancel' name='cancelExtaData'/>
    </item>";

echo "</items>";
?>