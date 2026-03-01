<?php
require_once($BASEPATH . 'includes/sessions.php');

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="100" noteWidth="105"  />  
          <item type="fieldset" inputWidth="600" label="Track"  offsetLeft="100" >
        <item type="combo" name="Track_id" label="" inputWidth="200" offsetLeft="100" serverFiltering="requisites/tracks.php">
         <note>Track Id</note>
        </item>       
        <item type="newcolumn"/>
        <item type="button" value="Filter" name="Btn_Filter" inputWidth="150" offsetLeft="20" offsetTop="0"></item>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="Btn_Cancel" inputWidth="150" offsetLeft="10" offsetTop="0"></item>
        </item>
            <item type="block" width="840" offsetTop="20" offsetLeft="0">  
            
            <item type="fieldset" inputWidth="400" label="Job"   >
                <item type="input" name="job_inc" label="Job Amount Receivable" value="0" offsetTop="5" readonly="true" className="income"></item>
                <item type="input" name="job_exp" label="Job Amount Deductable" value="0" offsetTop="5" readonly="true" className="expense"></item> 
                <item type="block" width="10" offsetTop="43" >  </item>
            </item>
            <item type="newcolumn" /> 
            <item type="fieldset" inputWidth="400" label="Income/Expense"  offsetLeft="5">
                <item type="input" name="track_inc" label="Total Amount Received" value="0" offsetTop="5" readonly="true" className="income"></item> 
                <item type="input" name="track_exp" label="Total Amount Spent" value="0" offsetTop="5"  readonly="true" className="expense"></item> 
                <item type="input" name="track_bal" label="Balance Receivable" value="0" offsetTop="5" readonly="true" className="balance"></item> 
            </item>
        </item>
      
        </items>';