<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$items=array();
include_once($BASEPATH . "preTallyClass/RuleClass.php");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
?>
    <items>
	   <item type="settings" position="label-left" labelWidth="150" inputWidth="150" noteWidth="150" offsetLeft="20" />
        
        <item type="hidden" name="RL_Id" value="0"/>
        <item type="hidden" name="RL_Except_Dept_office" value="0" />
        <item type="input" name="RL_Name" label="Rule Name" required="true" offsetTop="30" />
        <item type="input" name="RL_Description" label="Rule Description" rows="3" />
        <item type="combo" name="RL_Is_Sandwich" label="Is Sandwich Leave" required="true" validate="NotEmpty">
            <option text="Select Option" value="" selected="true"/>
            <option text="Yes" value="1"/>
            <option text="No" value="0"/>
        </item>  
        <item type="combo" name="RL_Sandwich_Type" label="Sandwich Type" hidden="true">
            <option text="Select Option" value="" selected="true"/>
            <option text="Holiday" value="holiday"/>
            <option text="Weekend" value="weekend"/>
        </item>
        <item type="combo" name="RL_Start_Day" label="Start Day" required="true" validate="NotEmpty">
            <option text="Select Option" value="" selected="true"/>
            <option text="Sunday" value="sun"/>
            <option text="Monday" value="mon"/>
            <option text="Tuesday" value="tue"/>
            <option text="Wednesday" value="wed"/>
            <option text="Thursday" value="thu"/>
            <option text="Friday" value="fri"/>
            <option text="Saturday" value="sat"/>
        </item>
        <item type="combo" name="RL_End_Day" label="End Day" required="true" validate="NotEmpty">
            <option text="Select Option" value="" selected="true"/>
            <option text="Sunday" value="sun"/>
            <option text="Monday" value="mon"/>
            <option text="Tuesday" value="tue"/>
            <option text="Wednesday" value="wed"/>
            <option text="Thursday" value="thu"/>
            <option text="Friday" value="fri"/>
            <option text="Saturday" value="sat"/>
        </item>
        <item type="combo" name="RL_Leave_Duration" label="Leave Duration" required="true" validate="NotEmpty">
            <option text="Select Option" value="" selected="true"/>
        </item>
        <item type="calendar" validate="NotEmpty" serverDateFormat="%d/%m/%Y" dateFormat="%d/%m/%Y" name="RL_Effective_Date" label="Effective Date" value="<?=$cudate?>"  required="true" readonly="true">
            <note width="150">Effective Date</note>
        </item>
        <item type="combo" name="RL_Is_LOP" label="Is Sunday LOP" required="true" validate="NotEmpty">
            <option text="Select Option" value="" selected="true"/>
            <option text="Yes" value="1"/>
            <option text="No" value="0"/>
        </item>
        <item type="combo" name="RL_Status" label="Status" required="true" validate="NotEmpty">
             <option text="Published" value="1" selected="true"/>
             <option text="Blocked" value="0"/>
        </item>  
        <item type="block" width="300" offsetTop="5">
			<item type="button" value="Save" name="saveRule"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="CancelRule"/>
        </item>
    </items>