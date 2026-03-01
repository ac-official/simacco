<?php
/**
 * Created By Bilin At 11-06-2025
 * Break time of all employees add and edit form
 * Edit data loading based on the requested id
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$editid 	= (isset($_REQUEST['edit_id'])) ? (int)$_REQUEST['edit_id']:0;
// include the attendance related class files
include_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
$btObj		= new BreakTimeClass();

$btid 		= 0;
$emp_us_id 	= 0; // employee user id
$break_id 	= 0; // break type id
$break_date = date('Y-m-d');
$from_time 	= date('H:i');
$to_time 	= '';
$remarks 	= '';
$cu_time 	= strtotime(date('H:i'));
// get the break time settings
$settingbt  = $btObj->getBreakSettings($preTally_user_ofid);

// find the existing data based on the editid
if ($editid > 0 && in_array($preTally_user_id, $settingbt['users']) ) {
	$extingData = $btObj->getBreakTimeEntry($editid);
	// one time edit and created user can only edit option
	if (!empty($extingData) && $extingData['is_edited'] == 0 && $extingData['break_date'] == $break_date) {

		$btid 			= $extingData['id'];
		$emp_us_id 		= $extingData['us_id'];
		$break_id 		= $extingData['break_id'];
		$break_date 	= $extingData['break_date'];
		$from_time 		= $extingData['from_time'];
		$to_time 		= ($extingData['to_time'] != '') ? $extingData['to_time']: '';//date('H:i')
		$remarks 		= $extingData['remarks'];
	}
}
// get all break time type 
$break_types 			= $btObj->getAllBreakTypes('1');
$fromtimes 				= explode(':',$from_time);
$out_hour 				= $fromtimes[0];
$out_minute 			= $fromtimes[1];
$totimes 				= ($to_time != '') ? explode(':',$to_time):[];
$in_hour 				= (isset($totimes[0])) ? $totimes[0] : '';
$in_minute 				= (isset($totimes[1])) ? $totimes[1] : '';

// start to create the form
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>';
	echo '<item type="hidden" name="btid" value="'.$btid.'"/>';
	echo '<item type="hidden" name="flag" value="0"/>';
	echo '<item type="settings" position="label-left" labelWidth="150" inputWidth="200" noteWidth="150" />';

	echo '<item type="template" name="break_date" label="Break Entry Date" value="' . date('d-m-Y', strtotime($break_date)) . '"  offsetTop="30"  offsetLeft="20" />';

	echo '<item type="combo" name="break_id"  label="Break Type" readonly="true" required="true"  offsetLeft="20" >';
		$setsel 	= 0;
        foreach ($break_types as $rw) {
        	$selected 		= '';
        	if ($break_id > 0) {
        		$setsel 	= 1;
        		$selected 	= ($break_id == $rw->id) ? 'selected="true"':'';
        	} else if ($cu_time >= strtotime($rw->from_time) && $cu_time <= strtotime($rw->to_time)) {
        		$setsel 	= 1;
        		$selected 	= 'selected="true"';
        	} else if ($rw->from_time == "" && $setsel == 0) {
        		$selected 	= 'selected="true"';
        		$setsel 	= 1; 
        	}
            echo '<option value="'.$rw->id.'" '.$selected.' text="'.$rw->title.'"/>';
        }
    echo '</item>';

    if (in_array($preTally_user_id, $settingbt['users'])) {
    	// allow others break time enter
    	echo '<item type="hidden" name="allow_other" value="1"/>';
		echo '<item type="hidden" name="us_id" value="'.$emp_us_id.'"/>';
		echo '<item type="combo" name="bt_user_id" label="Staff" filterCache="true"  required="true" validate="NotEmpty,ValidNumeric" connector="requisites/personsWithBranch.php" value="'.$emp_us_id.'"  offsetLeft="20" />';
    } else { // our own break time
    	echo '<item type="hidden" name="us_id" value="'.$preTally_user_id.'"/>';
    	echo '<item type="hidden" name="allow_other" value="0"/>';
    }
    if (in_array($preTally_user_id, $settingbt['users'])) {
	    echo '<item type="block" width="400" offsetTop="1" offsetLeft="1">';
		    echo '<item type="combo" required="true" readonly="true" name="out_hour" label="Out Time"  value="'.$out_hour.'" inputWidth="95">';
		    for ($i = 6; $i < 22; $i++) {
		    	$val 		= ($i < 10) ? "0".$i:$i;
		    	$val 		= ($i > 12) ? $val."  (".($i-12)." PM)" : $val;
		    	$selected	= ($i == (int)$out_hour) ? 'selected="true"' :'';
		    	echo '<option value="'.$i.'" '.$selected.' text="'.$val.'"/>';
		    }
		    echo '</item>';		    
		    echo '<item type="newcolumn"/>';
		    echo '<item type="combo" name="out_minute" readonly="true" value="'.$out_minute.'" inputWidth="95" >';	        		
		    for ($i = 0; $i < 60; $i++) {
		    	$val 		= ($i < 10) ? "0".$i:$i;
		    	$selected	= ($val == $out_minute) ? 'selected="true"' :'';
		    	echo '<option value="'.$i.'" '.$selected.' text="'.$val.' Min"/>';
		    }
		    echo '</item>';
	    echo '</item>';
	    echo '<item type="hidden" name="from_time" value="'.$from_time.'"/>';
	} else {
		echo '<item type="template" offsetLeft="20" name="from_time" label="Out Time"  value="'.$from_time.'"  />';
		echo '<item type="hidden" name="out_hour" value="0"/>';
		echo '<item type="hidden" name="out_minute" value="0"/>';
	}
    if ($btid > 0 && $in_hour != '') {
    	echo '<item type="block" width="400" offsetTop="1" offsetLeft="1" id="bt_in_time_div">';
    		
    		echo '<item type="combo" required="true" readonly="true" name="in_hour" label="In Time"  value="'.$in_hour.'" inputWidth="95">';    		
		    for ($i = 6; $i < 22; $i++) {
		    	$val 		= ($i < 10) ? "0".$i:$i;
		    	$val 		= ($i > 12) ? $val."  (".($i-12)." PM)" : $val;
		    	$selected	= ($i == (int)$in_hour) ? 'selected="true"' :'';
		    	echo '<option value="'.$i.'" '.$selected.' text="'.$val.'"/>';
		    }
    		echo '</item>';
	    	echo '<item type="newcolumn"/>';
	    	echo '<item type="combo" name="in_minute" readonly="true"  value="'.$in_minute.'" inputWidth="95" >';	    	        		
		    for ($i = 0; $i < 60; $i++) {
		    	$val 		= ($i < 10) ? "0".$i:$i;
		    	$selected	= ($val == $in_minute) ? 'selected="true"' :'';
		    	echo '<option value="'.$i.'" '.$selected.' text="'.$val.' Min"/>';
		    }
	    	echo '</item>';
	    echo '</item>';
    } else {
    	echo '<item type="hidden" name="in_hour" value="0"/>';
    	echo '<item type="hidden" name="in_minute" value="0"/>';
    }   

    echo '<item type="input" name="remarks" label="Break Time Remark" rows="3" value="'.$remarks.'" offsetLeft="20" />';

    echo '<item type="block" width="300" offsetTop="35" offsetLeft="0">
			<item type="button" value="Save" name="saveBreakTime"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="CancelBreakTime"/>
        </item>';

echo '</items>';
?>