<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$items=array();
include_once($BASEPATH . "preTallyClass/AttendanceAdjustmentClass.php");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
?>
<items>
   <item type="hidden" name="Att_Adj_Id" value="0"/>
   <item type="settings" position="label-left" labelWidth="150" inputWidth="200" noteWidth="150" offsetLeft="20" />
   <item type="combo" name="Att_Adj_US_Id" label="Users" filterCache="true" comboType="editable" required="true" validate="NotEmpty,ValidNumeric" className="RptCombo" offsetTop="30"/>
   <item type="calendar" validate="NotEmpty" serverDateFormat="%d/%m/%Y" dateFormat="%d/%m/%Y" name="Att_Adj_Date" label="Attendance Date" value="<?=$cudate?>"  required="true" readonly="true">
   </item>
   <item type="combo" name="Att_Adj_Type" label="Attendance Type" required="true" validate="NotEmpty">
        <option text="Select Option" value="" selected="true"/>
        <option text="Full Day" value="p"/>
        <option text="Morning Half Day" value="p1"/>
        <option text="Afternoon Half Day" value="p2"/>
   </item>
   <item type="input" name="Att_Adj_Remarks" required="true" label="Attendance Remark" rows="3" />
   <item type="block" width="300" offsetTop="5">
		<item type="button" value="Save" name="saveAttendanceAdj"/>
		<item type="newcolumn"/>
		<item type="button" value="Cancel" name="CancelAttendanceAdj"/>
   </item>
</items>