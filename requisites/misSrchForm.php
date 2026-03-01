<?php
require_once($BASEPATH . 'includes/sessions.php');

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$currYear   = date("Y");
$currMonth  = ltrim(date("m"),0);

$mnth_name=array(1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec");

$currDate   = date("d");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="0" inputWidth="250" noteWidth="100" offsetLeft="20"/>
		<item type="block" name="dateBlock" width="300" offsetLeft="0" inputWidth="50">  
        <item type="combo" label="Duration" name="Srch_Year" inputWidth="80">'; 
                echo '<option value="0" label=" Year" />';
                for($i=$currYear;$i>=2012;$i--){
                    echo ' <option value="'.$i.'" label="'.$i.'" />';
                }
                echo'<note width="50">Year</note>
                </item>
                <item type="newcolumn" offsetLeft="0"/>
                <item type="combo" label="Duration" name="Srch_Month" inputWidth="50">';     
                echo '<option  value="0" label=" Month" />';
                for($j=$currMonth;$j>=1;$j--){
                    echo ' <option value="'.$j.'" label="'.$mnth_name[$j].'" />';
                }
                echo'<note width="50">Month</note>
                </item>
                <item type="newcolumn" offsetLeft="0"/>
                <item type="combo" label="Duration" name="Srch_Date" inputWidth="50">'; 
                echo '<option  value="0" label="Day" />';
                echo '<option value="'.$currDate.'" label="Today" />';
                for($k=$currDate-1;$k>=1;$k--){
                    echo ' <option value="'.$k.'" label="'.$k.'" />';
                }
                echo'<note width="50">Date</note>
                </item>
                </item>
		 <item type="block" name="dateBlock" width="300" offsetLeft="0" >         
                    <item type="calendar" name="Strt_Date" label="From" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" value="" offsetLeft="0" inputWidth="100">
                            <note width="75">From</note>
                    </item>
                <item type="newcolumn" offsetLeft="0"/>
                    <item type="calendar" name="End_Date" label="To" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" value="" offsetLeft="0" inputWidth="100">
                            <note width="75">To</note>
                    </item>
                </item>                
                <item type="combo" label="Filter By" name="Srch_FiltrBy" >
                    <option value="1" label="Whole Company" />
                    <option value="2" label="Branch" />
                    <option value="3" label="User" />
                    <option value="4" label="Subhead" />              
                    <option value="5" label="Item" />   
                    <note width="150">Filter By</note>
                </item>
                
                <item type="combo" name="LC_Id" label="Branch"  inputWidth="250"  comboType="checkbox">
                    <option value="0" label="All" />
                    <note width="250">Branch Name</note>
		</item>
                <item type="combo" name="SH_Id" label="Subhead"  inputWidth="250"  comboType="checkbox">
			<note width="250">Subhead</note>
		</item>
                <item type="combo" name="IT_Id" label="Item"  inputWidth="250"  comboType="checkbox">
			<note width="250">Item</note>
		</item> 
                <item type="combo" name="US_Id" label="User"  inputWidth="250"  comboType="checkbox">
			<note width="250">User</note>
		</item> 
                
                <item type="combo" label="Type" name="Srch_Type" required="true">                   
                      <option value="1" label="Branch" />
                    <option value="2" label="User" />
                    <option value="3" label="Subhead" />              
                    <option value="4" label="Item" /> 
                    <note width="150">Report of</note>
                </item>
                <item type="fieldset" label="Info" name="mis_info">
                    <item type="template" name="rpt_type" label="Report Type" value="Branch Wise" labelWidth="80" inputWidth="100" offsetLeft="0"/>                    
                    <item type="template" name="filt_lbl" label="Filter By" value="Branch" labelWidth="80" inputWidth="100" offsetLeft="0"/>
                    
                </item>
                <item type="hidden" name="srch_ids" value="0"/>
		<item type="block" width="250" offsetTop="10">
			<item type="button" value="Search" name="SrchButton"/>
			<item type="newcolumn"/>
			<item type="button" value="Clear" name="CancelSrchButton"/>
		</item>
		
	</items>';
?>