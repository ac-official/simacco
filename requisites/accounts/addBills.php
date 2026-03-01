<?php
/** 
	* Add or edit Bill details names
	* Created By Bilin @ 10-09-2025
*/
if ( stristr($_SERVER['HTTP_ACCEPT'],'application/xhtml+xml')) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj			= new AccountsClass();
// find the combo listing array...
$list_vendor 	= $accObj->getCustomField("acc_vendors", "id,name,gst_no", "WHERE status=1");
$list_tds 		= $accObj->getCustomField("acc_tds_rules", "id,rule_name,percentage", "WHERE status=1");
$tdsdetails 	= '';
if (!empty($list_tds)) {
	$tttt 		= [];
	foreach ($list_tds as $tds) {
		$tttt[$tds->id] = $tds->percentage; 
	}
	$tdsdetails = json_encode($tttt);
}
// find the tax details combo list
$taxdetails 	= '';
$list_tax 		= $accObj->getCustomField("acc_tax_rules", "id,title,sub_title1,sub_title2", "WHERE status=1");
if (!empty($list_tax)) {
	$tttt 		= [];
	foreach ($list_tax as $tax) {
		$tttt[$tax->id] = ['percent'=>$tax->percentage, 'title1'=>($tax->sub_title1 != '') ? $tax->sub_title1 : $tax->title, 'title2'=>($tax->sub_title2 != '') ? $tax->sub_title2 : '']; 
	}
	$taxdetails = json_encode($tttt);
}

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

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo "<items>";
	echo "<item type='hidden' name='eid' value='0'/>";
	echo "<item type='hidden' name='tds_percent' value='0'/>";	
	echo "<item type='hidden' name='tdsdetails' value='".$tdsdetails."'/>";	
	echo "<item type='hidden' name='taxdetails' value='".$taxdetails."'/>";	
	echo "<item type='hidden' name='tax_no' value='0'/>";
	echo "<item type='hidden' name='tax_amount' value='0'/>";
	echo "<item type='hidden' name='branch_id' value='0'/>";
	echo "<item type='settings' position='label-left' labelWidth='132' inputWidth='245' noteWidth='150' offsetLeft='20' offsetTop='8'  />";

	echo "<item type='input' offsetTop='30' name='bill_name' label='Bill Name' required='true' validate='^[-:.;_0-9a-zA-Z ]+$' value=''></item>";

	echo "<item type='calendar' name='bill_date' label='Bill Date' value='".date('Y-m-d')."' serverDateFormat='%Y-%m-%d' dateFormat='%d.%m.%Y' required='true'  readonly='true'></item>";

	echo "<item type='calendar' name='due_date' label='Due Date' value='' serverDateFormat='%Y-%m-%d' dateFormat='%d.%m.%Y' readonly='true'></item>";

    echo "<item type='combo' name='vendor_id' label='Vendor Name' required='true' validate='^[0-9]+$'>";
		echo "<option value=''  selected='true' text='Select Vendor'/>";
        foreach ($list_vendor as $rw) {        	
            echo "<option value='".$rw->id."' text='".$rw->name."'/>";
        }
    echo "</item>";

	echo "<item type='combo' name='tds_id' label='TDS Rule' validate='^[0-9]+$'>";
		echo "<option value=''  selected='true' text='Select'/>";
        foreach ($list_tds as $rw) {        	
            echo "<option value='".$rw->id."' text='".htmlentities($rw->rule_name." - ".$rw->percentage.'%')."'/>";
        }
    echo "</item>";

    echo "<item type='combo' name='bill_type' label='Credit Or Debit'>";    
		echo "<option value='0'  selected='true' text='Debit'/>";
		echo "<option value='1' text='Credit'/>";
    echo "</item>";

    echo "<item type='input' name='total_amount' label='Total Bill Amount' required='true' validate='^[.0-9 ]+$' value='0'></item>";    
    echo "<item type='combo' name='tax_id' label='Tax' validate='^[0-9]+$' readonly='true'>";
		echo "<option value=''  selected='true' text='Select'/>";
        foreach ($list_tax as $rw) {        	
            echo "<option value='".$rw->id."' text='".htmlentities($rw->title)."'/>";
        }
    echo "</item>";

    echo "<item type='input' name='tax_percent' label='Tax Percentage' validate='^[.0-9 ]+$' value='0'></item>";

    echo "<item type='template' name='bill_amount' label='Bill Amount' validate='^[.0-9 ]+$' value='0.000'></item>";
    echo "<item type='template' name='sub_title1' label='Tax Amount' validate='^[.0-9 ]+$' value='0.000'></item>";
    echo "<item type='template' name='sub_title2' label='Tax Amount' validate='^[.0-9 ]+$' value='0.000'></item>";

    echo "<item type='template' name='tds_amount' label='TDS Amount' validate='^[.0-9 ]+$' value='0.000'></item>";    

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
		<item type='button' value='Save' name='saveAccBill'/>
		<item type='newcolumn'/>
		<item type='button' value='Cancel' name='CancelAccBill'/>
    </item>";
echo "</items>";
?>
