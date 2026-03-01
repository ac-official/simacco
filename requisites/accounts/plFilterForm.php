<?php
/** 
	* Date , company and branch based filter for profit and loss account
	* Created By Bilin @ 29-09-2025 updated 14-01-2026
*/
if ( stristr($_SERVER['HTTP_ACCEPT'],'application/xhtml+xml')) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj			= new AccountsClass();
$flag 			= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']: 0;
// get all active office list
$Sibling_id 	= 0;
$ofquery 		= ($UserACLObj->view_all_company == 0) ? ' AND OF_Id='.$preTally_user_ofid:'';
$list_office 	= $accObj->getCustomField('offices', 'OF_Id, OF_Name, Sibling_id', ' WHERE OF_Status = 1'.$ofquery);
foreach ($list_office as $value) {
	if ($value->OF_Id == $preTally_user_ofid) {
		$Sibling_id  = $value->Sibling_id;
		break;
	}
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo "<items>";

	echo "<item type='settings' position='label-top' labelWidth='200' inputWidth='250' noteWidth='150' offsetLeft='20' offsetTop='2'  />";
	echo "<item type='combo' name='company_id'  label='Company Name' readonly='true' required='true'  offsetTop='6'>";		
		foreach ($list_office as $rw) {
			if (($Sibling_id > 0 && $Sibling_id == $rw->Sibling_id && $UserACLObj->view_all_company == 1) || $rw->OF_Id == $preTally_user_ofid) {

				$selected 		= ($rw->OF_Id == $preTally_user_ofid) ? "selected='true'":"";
            	echo "<option value='".$rw->OF_Id."' ".$selected." text='".$rw->OF_Name."'/>";
			}        	
        }
    echo "</item>";

    echo "<item type='combo' name='branch_id'  label='Branch Name'>";		
		echo "<option value='0' selected='true' text='Select Branch'/>";
    echo "</item>";
    if ($flag == 0 || $flag == 2 || $flag == 4) {
    	echo "<item type='combo' name='trans_type'  label='Transaction Type'>";		
			echo "<option value='1' text='External'/>";
			echo "<option value='2' text='Internal Only'/>";
			echo "<option value='0' text='Both' selected='true'/>";
    	echo "</item>";
    }
    if ($flag == 0) {
    	echo "<item type='combo' name='viewtype'  label='View Type'>";		
			echo "<option value='1' text='Base' selected='true'/>";
			echo "<option value='2' text='Pending Internal'/>";
    	echo "</item>";
    }
    if ($flag == 5) {
    	echo "<item type='combo' name='cash_bank_type'  label='Cash Or Bank'>";		
			echo "<option value='1' text='Cash' selected='true'/>";
			echo "<option value='2' text='Bank'/>";
    	echo "</item>";
    }

    echo "<item type='calendar' name='from_date' label='Start Date' value='".date('Y-m-01')."' serverDateFormat='%Y-%m-%d' dateFormat='%d.%m.%Y' required='true'  readonly='true'></item>";
    echo "<item type='calendar' name='to_date' label='End Date' value='".date('Y-m-d')."' serverDateFormat='%Y-%m-%d' dateFormat='%d.%m.%Y' required='true'  readonly='true'></item>";

    if ($flag == 1) {
    	$list_vendor 	= $accObj->getCustomField("acc_vendors", "id,name,gst_no", "WHERE status=1");
		echo "<item type='combo' name='vendor_id'  label='Vendor or Debtor or Creditor' validate='^[0-9]+$'>";
        echo "<option value=''  selected='true' text='Select'/>";
        foreach ($list_vendor as $rw) {        	
            echo "<option value='".$rw->id."' text='".$rw->name." - (".$rw->gst_no.")'/>";
        }
    	echo "</item>";
    }

    if ($flag == 2) {
    	$list_ledger 	= $accObj->getCustomField("acc_ledger AS l LEFT JOIN acc_ledger AS p ON (p.id =l.parent_id)", "l.id,l.title,p.title AS parents,l.parent_id", "WHERE l.status=1");
    	echo "<item type='combo' name='ledger_id' required='true'   label='Ledger Name' validate='^[0-9]+$'>";
		echo "<option value=''  selected='true' text='Select'/>";
        foreach ($list_ledger as $rw) {        	
            echo "<option value='".$rw->id."' text='".$rw->title.($rw->parent_id > 0 ? " - (".$rw->parents.")":"")."'/>";
        }
    	echo "</item>";
    }

    /*if ($flag == 5) {
    	$list_bnkacc 	= $accObj->getCustomField("bank_accounts", "BA_Id,BA_DispName,OF_Id", "WHERE BA_Status=1");
		echo "<item type='combo' name='bank_acc_id'  label='Bank Accounts' validate='^[0-9]+$'>";
        echo "<option value=''  selected='true' text='Select'/>";
        foreach ($list_bnkacc as $rw) {        	
            echo "<option value='".$rw->BA_Id."' text='".$rw->BA_DispName."'/>";
        }
    	echo "</item>";
    }*/
    if ($flag == 5) {
		echo "<item type='combo' name='bank_acc_id'  label='Bank Accounts' validate='^[0-9]+$'>";       
    	echo "</item>";
    }

    echo " <item type='button' value='Search' name='searchPandL'/> ";
   /*echo "<item type='block' width='300' offsetTop='1' offsetLeft='113'>
		<item type='button' value='Search' name='searchPandL'/>
    </item>";*/
echo "</items>";
?>

