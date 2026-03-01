<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
//28-03-2025 Start the work office allow list selection (multiple office allowed some users)
/*require_once($BASEPATH . "preTallyClass/UserOfficesClass.php"); 
$uoffObj = new UserOfficesClass(); 
$office_list = $uoffObj->getCompanyList($preTally_user_id,"1");*/
$office_list = [];
//end 28-03-2025

$w = $REQUEST['w'];
$w = floor(($w-570)/2.5);
$cudate = date("Y-m-d");

echo '<items>
        <item type="settings" position="label-left" labelWidth="0" offsetLeft="0" offsetTop="0"/>
            <item type="hidden" name="IT_Name" value=""/>
            <item type="hidden" name="DS_Description" value=""/>
            <item type="hidden" name="TR_Track" value=""/>
            
            <item type="block" width="100%" className="newBalSheet">';
            if (!empty($office_list)) { //28-03-2025

                echo '<item type="combo" label="" name="IT_Off_Id" readonly="true" validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="180">';
                        foreach ($office_list AS $offid=>$offnam) {
                            $selt = ($offid == $preTally_user_ofid) ? ' selected="true"': '';
                            echo '<option value="'.$offid.'" label="'.$offnam.'" '.$selt.' />';
                        }
                        echo '<note>OFFICE </note>
                </item> 
                <item type="newcolumn"/>';
            }
          echo '<item type="combo" label="" name="MH_Type" readonly="true" validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="80">
			<option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" selected="false" />
			<note>ENTRY TYPE</note>
		</item> 
                <item type="newcolumn"/>
               
                <item type="combo" name="IT_Id" className="itemcombo" label="" value="" required="true" offsetTop="20" position="label-left" inputWidth="'.$w.'" >
                <note>NAME OF THE EXPENSE</note>
                </item>
                <item type="newcolumn"/>
                
                <item type="combo" name="BS_Description" className="desccombo" label="" value=""  validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="'.$w.'" >
                        <note>PAID TO [ NAME AND DETAILS PARTIES ]</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="BS_Amount" label="" value="" inputWidth="50"  validate="NotEmpty,^([0-9]*|\d*\.\d{1}?\d*)$"  offsetTop="20" position="label-left">
                        <note>AMOUNT</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="BS_PayMode" label="" value=""  validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="80" >
                <option value="1" label="Cash" selected="true" />
                <option value="2" label="Bank" selected="false" />
                        <note>Payment Mode</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="BS_PayBankAC" label="" value=""  validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="100" >
                        <note>Account Number</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="BS_Track" className="trackCombo" label="" value=""  offsetTop="20" position="label-left" inputWidth="100" validate="^[a-zA-Z0-9 _\-]+$">
                        <note>Track ID</note>
                </item>
                <item type="newcolumn"/>
                <item type="calendar"  validate="NotEmpty" name="BS_Date" label="Date"  required="true" readonly="true" offsetTop="20" inputWidth="80" serverDateFormat="%Y-%m-%d" dateFormat="%d/%m/%Y" value="'.$cudate.'" ><note>Date</note>
                </item>';
                // start - branch list for amount spent 28-05-2025
                echo '<item type="newcolumn"/>'; 
                echo '<item type="combo" name="BS_BranchTo" className="branchcombo" label="" value="'.$preTally_user_lcid.'" required="true" readonly="true" offsetTop="20" position="label-left" inputWidth="130"  connector="requisites/locations.php&amp;ofid=' . $preTally_user_ofid . '&amp;filter=BMR">
                    <note>Branch</note>
                </item>';
                // End - branch list for amount spent 28-05-2025
                echo '<item type="newcolumn"/>
                <item type="button" value="SUBMIT" offsetTop="15" position="label-left" name="saveBalanceSheetItem"/>

            </item>
	</items>';
?>