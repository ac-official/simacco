<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$minOptions = '';
for($i=0;$i<=60;$i++){
    $i = ($i<10)?'0'.$i:$i;
    $minOptions.='<option text="'.$i.'" value="'.$i.'" />';
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="150" inputWidth="150" noteWidth="150" offsetLeft="20" offsetTop="10" offsetBottom="10"/>
       <item type="calendar" name="Att_Lt_From_Date" label="From Date" readonly="true" required="true"/>
        <item type="calendar" name="Att_Lt_To_Date" label="To Date" readonly="true"/>
        <item type="input" name="Att_Lt_NumOFDays" label="Total Days" required="true" readonly="true" ></item>  
        <item type="combo" name="Att_Lt_Type" label="Time Adjustment In" readonly="true" required="true">
            <option text="Select Option" value="" selected="true"/>
            <option text="Morning" value="p1"/>
            <option text="Evening" value="p2"/>
        </item>

        <item type="block" width="100%" offsetTop="1" offsetLeft="0">
            <item type="combo" required="true" readonly="true" name="late_hr" label="Time Taken" inputWidth="95"  offsetLeft="0">
                <option value="0" text="0" />
                <option value="1" text="1" />
                <note>Hours</note>
            </item>
            <item type="newcolumn"/>
            <item type="combo" name="late_min" readonly="true" inputWidth="95" offsetLeft="0">
                '.$minOptions.'
                <note>Minutes</note>
            </item>
        </item>


        
        <item type="input" name="Att_Lt_Reason" label="Reason" height="500" rows="3" required="true">
        </item>  
        <item type="hidden" name="Att_Lt_Id" value="0"></item>
        <item type="block" width="300" offsetTop="5" style="justify-content: center;
    display: flex
;">
			<item type="button" value="Save" name="saveApplyLateEntry"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="CancelApplyLateEntry"/>
        </item>
    </items>';
?>