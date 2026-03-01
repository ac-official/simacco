<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj         = new AccountsClass();

// get all active office list
$Sibling_id     = 0;
$list_office    = $accObj->getCustomField('offices', 'OF_Id, OF_Name, Sibling_id', ' WHERE OF_Status = 1');
foreach ($list_office as $value) {
    if ($value->OF_Id == $preTally_user_ofid) {
        $Sibling_id  = $value->Sibling_id;
        break;
    }
}
// get all  type 
$list_types     = $accObj->ListGroupTypes(1);
echo '<?xml version="1.0" encoding="UTF-8"?>
<items>
    <item type="settings" position="label-left" labelWidth="100" inputWidth="250" noteWidth="150" offsetLeft="20"/>';

    echo '<item type="combo" name="isNew"  label="Upload Type" readonly="true" required="true" offsetTop="40">';
        echo '<option value="1" selected="true" text="New"/>';
        echo '<option value="0" text="Replace"/>';
    echo '</item>';

    echo '<item type="combo" name="type"  label="Accounts Type" readonly="true" required="true">';
        echo '<option value="" selected="true" text="Select"/>';
        foreach ($list_types as $rk=>$rw) {
            echo "<option value='".$rk."' text='".$rw."'/>";
        }
    echo '</item>';

    echo "<item type='combo' name='company_id' label='Company Name' readonly='true' required='true'>";     
        foreach ($list_office as $rw) {
            if (($Sibling_id > 0 && $Sibling_id == $rw->Sibling_id) || $rw->OF_Id == $preTally_user_ofid) {

                $selected       = ($rw->OF_Id == $preTally_user_ofid) ? "selected='true'":"";
                echo "<option value='".$rw->OF_Id."' ".$selected." text='".$rw->OF_Name."'/>";
            }           
        }
    echo "</item>";

    echo '<item type="combo" name="Month"  label="Month Name" readonly="true" required="true">';
        echo '<option value="" selected="true" text="Select"/>';
        foreach (['1'=>'January', '2'=>'February', '3'=>'March', '4'=>'April', '5'=>'May', '6'=>'June', '7'=>'July', '8'=>'August', '9'=>'September', '10'=>'October', '11'=>'November', '12'=>'December'] as $rk=>$rw) {
            echo '<option value="'.$rk.'" text="'.$rw.'"/>';
        }
    echo '</item>';
    echo '<item type="combo" name="Year"  label="Year" readonly="true" required="true">';
        echo '<option value="" selected="true" text="Select"/>';
        $cuyear = date('Y');
        for ($y=$cuyear; $y >= 2025; $y--) {
            echo '<option value="'.$y.'" text="'.$y.'"/>';
        }
    echo '</item>';

    echo '<item type="combo" name="Date" label="Job Date" readonly="true"></item>
    <item type="combo" name="Process" label="Process Name" readonly="true"></item>
    <item type="combo" name="Amount" label="Amount" readonly="true"></item>
    <item type="combo" name="Branch" label="Branch Name" readonly="true"></item>';

    echo '<item type="hidden" name="accPL_ExcelArray"></item>
    <item offsetLeft="120" type="button" name="accPL_Button" value="Save"/>
</items>';
?>