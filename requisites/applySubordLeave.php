<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="150" noteWidth="150" offsetLeft="20"/>
        
            <item type="calendar" name="LR_FromDate" inputWidth="150" label="From Date" readonly="true" required="true">        
                <note width="150">From Date</note>
            </item>
        
            <item type="calendar" name="LR_ToDate" inputWidth="150" label="To Date" readonly="true" required="true" >        
                <note width="150">To Date</note>
            </item>
       
            <item type="combo" name="LR_AppliedFor" label="Name"  connector="requisites/listSubordinates.php" validate="NotEmpty" readonly="true" required="true">			
                <note width="150">Select Subordinate</note>
            </item> 
            
            <item type="combo" name="LT_Id" label="Type Of Leave"  validate="NotEmpty" readonly="true" required="true">
		<note width="150">Choose Leave Type</note>
            </item>
            
            <item type="input" name="LR_eligibleLeave" label="Eligible Leave" readonly="true" >
                <note width="150">Eligible Leave</note>            
            </item> 
            
            <item type="combo" name="LR_duration" label="Duration" >
                <option text="Fullday" value="0" selected="true"/>
                 <option text="Halfday (Morn)" value="1"/>
                 <option text="Halfday (Eve)" value="2"/>
                <note width="150">Leave Duration</note>
            </item>
            
            <item type="input" name="LR_NumOFDays" label="Total Leave" readonly="true" >
                <note width="150">Total Leave</note>
            </item>   
            
            <item type="input" name="LR_Reason" label="Reason For Leave"   height="500" rows="3" required="true">
                <note width="150">Reason for Leave</note>
            </item>  
            
            <item type="block" width="300" offsetTop="5">
                <item type="button" value="Save" name="saveApplyLeave"/>
                <item type="newcolumn"/>
                <item type="button" value="Cancel" name="CancelApplyLeave"/>
            </item>
        
    </items>';























//echo '<items>
//	<item type="settings" position="label-left" labelWidth="120" inputWidth="150" noteWidth="150" offsetLeft="20"/>
//        
//            <item type="calendar" name="LR_FromDate" inputWidth="150" label="From Date" readonly="true" required="true">        
//                <note width="150">From Date</note>
//            </item>
//        
//            <item type="calendar" name="LR_ToDate" inputWidth="150" label="To Date" readonly="true" required="true" >        
//                <note width="150">To Date</note>
//            </item>
//       
//            <item type="combo" name="US_Id" label="Name"  connector="requisites/listSubordinates.php" validate="NotEmpty" readonly="true" required="true">			
//                <note width="150">Select Subordinate</note>
//            </item> 
//            
//            <item type="combo" name="LT_Id" label="Type Of Leave"  connector="requisites/leaveType.php" validate="NotEmpty" readonly="true" >
//		<note width="150">Choose Leave Type</note>
//            </item>
//            
//            <item type="input" name="LR_eligibleLeave" label="Eligible Leave" readonly="true" >
//                <note width="150">Eligible Leave</note>            
//            </item> 
//            
//            <item type="combo" name="LR_duration" label="Duration" readonly="true">
//                <option text="Fullday" value="0" selected="true"/>
//                <option text="Halfday" value="1"/>
//                <note width="150">Leave Duration</note>
//            </item>
//            
//            <item type="input" name="LR_NumOFDays" label="Total Leave" readonly="true" >
//                <note width="150">Total Leave</note>
//            </item>   
//            
//            <item type="input" name="LR_Reason" label="Reason For Leave"   height="500" rows="3" required="true">
//                <note width="150">Reason for Leave</note>
//            </item>  
//            
//            <item type="block" width="300" offsetTop="5">
//                <item type="button" value="Save" name="saveApplyLeave"/>
//                <item type="newcolumn"/>
//                <item type="button" value="Cancel" name="CancelApplyLeave"/>
//            </item>
//        
//    </items>';
?>