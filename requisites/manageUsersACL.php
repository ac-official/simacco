<?php
/**
* Assign Acls into users (single user at a time)
* Created By Bilin @ 07-07-2025
*/
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
$newcolumn = '<item type="newcolumn" offset="0"></item>';
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

	echo '<items>
        <item type="settings" position="label-left" labelWidth="0" inputWidth="350" offsetLeft="10" />';
        echo '<item type="fieldset" name="useracl1" labelWidth="0" position="label-right"  label="Staff Name" inputWidth="590" className="manageACLFieldSet" offsetTop="10" >';
	        echo '<item type="hidden" name="aclid" value="0"/>';
	        echo '<item type="hidden" name="us_id" value="0"/>';
			echo '<item type="combo" name="ua_user_id" label="Staff Name" filterCache="true"  required="true" validate="NotEmpty,ValidNumeric" connector="requisites/personsWithBranch.php" value="0"  offsetLeft="20"><note width="100">Staff Name</note></item>';

			echo '<item type="newcolumn" />
	            <item type="button" value="Save" name="ACLBtnSave" offsetTop="0" />
	            <item type="newcolumn" />
	            <item type="button" value="Cancel" name="ACLBtnCancel" offsetTop="0" />';
	    echo '</item>'; 

        echo '<item type="fieldset" name="gen1" labelWidth="400" inputWidth="590" position="label-right"  label="General ACL List" className="manageACLFieldSet" >';
        	
        	$type = ($UserACLObj->add_break_time==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="add_break_time" labelWidth="auto"  label="Add Break Time" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->view_break_time==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="view_break_time" labelWidth="auto"  label="List Break Time" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->view_late_entry==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="view_late_entry" labelWidth="auto"  label="List Late Entry" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->approve_late_entry==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="approve_late_entry" labelWidth="auto"  label="Approve Late Entry" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->add_clear_attendance==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="add_clear_attendance" labelWidth="auto"  label="Add Clear Attendance" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->view_clear_attencance==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="view_clear_attencance" labelWidth="auto"  label="List Cleared Attendance" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->view_attendance_detail==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="view_attendance_detail" labelWidth="auto"  label="View Attencance Popup (Details)" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->view_master_report==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="view_master_report" labelWidth="auto"  label="View Master Reports" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->view_accounts_report==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="view_accounts_report" labelWidth="auto"  label="View Account Team Reports" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->add_account_settings==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="add_account_settings" labelWidth="auto"  label="Add Accounts Settings" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->view_account_settings==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="view_account_settings" labelWidth="auto"  label="View Accounts Settings" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->approve_account_settings==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="approve_account_settings" labelWidth="auto"  label="Approve Accounts Settings" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->approve_ledger_amount==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="approve_ledger_amount" labelWidth="auto"  label="Approve/Transfer Ledger Amount" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->manage_bills==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="manage_bills" labelWidth="auto"  label="Add/View Bills" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->manage_all_bills==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="manage_all_bills" labelWidth="auto"  label="Add Other Conmpany Bills" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';
	        
	        $type = ($UserACLObj->approve_grace_time==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="approve_grace_time" labelWidth="auto"  label="Approve More Grace Time" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->close_others_tracks==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="close_others_tracks" labelWidth="auto"  label="Close All Track" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->view_all_company==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="view_all_company" labelWidth="auto" label="View Accounts Of All Company " position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->bank_reconciliation==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="bank_reconciliation" labelWidth="auto"  label="Approve Bank Reconciliation" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	        $type = ($UserACLObj->add_open_balance==1) ? "checkbox":"hidden";
	        echo '<item type="'.$type.'" name="add_open_balance" labelWidth="auto"  label="Add Bank Opening Balance" position="label-right"></item>';
	        echo ($type == "checkbox") ? $newcolumn :'';

	    echo '</item>';


    echo '<item type="template" offsetTop="20" />
    </items>';
?>