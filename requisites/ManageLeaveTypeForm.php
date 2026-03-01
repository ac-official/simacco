<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$items=array();
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
$LvObj=new LeaveClass;
$LvObj->getEmployeeStatus($preTally_user_ofid);
$employees_status=$LvObj->getEmployeeStatusArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="150" inputWidth="150" noteWidth="150" offsetLeft="20" />
        
        <item type="hidden" name="LT_Id" value="0"/>
       
        <item type="input" name="LT_Name" label="Leave Type Name" required="true" validate="NotEmpty,^[a-zA-Z\s]+$"  offsetTop="30">
            <note>Leave Type Name</note>
        </item> 
        <item type="block" width = "500" offsetLeft ="0" >
            <item type="radio" name = "LT_LOP" value = "0" offsetLeft ="0" checked = "true" position = "label-right" label="Salary Not Deductable" > </item> 
            <item type="newcolumn" offset="0"></item>
            <item type="radio" name = "LT_LOP" value = "1" offsetLeft ="0" position = "label-right" label="Salary Deductable" ></item> 
        </item>

        <item type="input" name="LT_MaxCount" label="Leave per month" required="true" validate="NotEmpty,^\d+$">
        <note>Leave Per month</note>
        </item>';
        $count=0;
        echo'<item type="combo" name="emp_Status"  comboType="checkbox" label="Employee status" readonly="true">';
            foreach($employees_status as $rw){
                echo'<option value="'.$rw->ES_Id.'" text="'.$rw->ES_Name.'"/>';
                $items[$count] = $rw->ES_Id;
                $count++;
            }
        echo'</item>';
        $itemstus=implode(",", $items);
        echo'<item type="combo" name="LT_Status" label="Status" readonly="true">
             <option text="Published" value="1" selected="true"/>
             <option text="Blocked" value="0"/>
        </item>  
        <item type="block" width="300" offsetTop="5">
			<item type="button" value="Save" name="saveLeaveType"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="CancelLeaveType"/>
        </item>
        <item type="hidden" name="employee_Status" value="'.$itemstus.'">
        </item> 
        
       </items>';
?>