<?php
/** 
	* Add or Update the opening balance of accounts like (capital, assets, loans, p & L  ...)
	* Created By Bilin @ 21-11-2025
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
$listTypes  	= $accObj->openbalanceTypes();

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo "<items>";
	echo "<item type='hidden' name='eid' value='0'/>";
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

    echo "<item type='combo' name='year_date' label='Financial Year' readonly='true' required='true'>";
    	$cuyear  = date('Y');
    	if (date('m') >= 3) {
    		$nxyear = $cuyear+1;
    		echo "<option value='".$cuyear."-".$nxyear."' text='".$cuyear." - ".$nxyear."'/>";
    	}
    	if (date('m') < 4) {
    		$preyear = $cuyear-1;
    		echo "<option value='".$preyear."-".$cuyear."' text='".$preyear." - ".$cuyear."'/>";
    	}
    echo "</item>";

    echo "<item type='combo' name='type' label='Type/Group Name' readonly='true' required='true'>";
    	foreach ($listTypes as $rk => $rv) {
           	echo "<option value='".$rk."' text='".$rv."'/>";
        }
    echo "</item>";

    $list_ledger 	= $accObj->getCustomField("acc_ledger AS l LEFT JOIN acc_ledger AS p ON (p.id =l.parent_id)", "l.id,l.title,p.title AS parents,l.parent_id", "WHERE l.status=1");
	echo "<item type='combo' name='ledger_id' label='Ledger Name (Optional)' validate='^[0-9]+$'>";
	echo "<option value='0'  selected='true' text='Select'/>";
    foreach ($list_ledger as $rw) {
    	
        echo "<option value='".$rw->id."' text='".$rw->title.($rw->parent_id > 0 ? " - (".$rw->parents.")":"")."'/>";
    }
	echo "</item>";

	echo '<item type="input" name="title" label="Display Name" required="true" validate="^[-_0-9a-zA-Z ]+$" value=""></item>';

	echo "<item type='input' name='amount' label='Amount' required='true' validate='^[.0-9 ]+$' value='0'></item>";

	echo '<item type="combo" name="amt_type"  label="Amount Type" readonly="true" required="true">';		
		echo '<option value="1" selected="true" text="+ve (Profit)"/>';
		echo '<option value="0" text="-ve (Loss)"/>';        
    echo '</item>';

	echo "<item type='block' width='300' offsetTop='1' offsetLeft='113'>
		<item type='button' value='Save' name='saveOpenBal'/>
		<item type='newcolumn'/>
		<item type='button' value='Cancel' name='cancelOpenBal'/>
    </item>";

echo "</items>";
?>