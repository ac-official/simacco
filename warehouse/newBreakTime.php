<?php
/**
 * Save the employees break time 
 * Update break time also possible here based on the settings and conditions
 * Created BY Bilin @ 12-06-2025 
*/
// include the attendance related class files
include_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
$btObj		= new BreakTimeClass();
// return fields
$msg 		= 'Invalid Inputs Please Check.';
$status 	= 0;

// find the common inputs from or grid
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$flag 		= (isset($_GET['flag'])) ? (int)$_GET['flag']:$flag;
$editid 	= (isset($_REQUEST['btid'])) ? (int)$_REQUEST['btid']:0;
$us_id		= (isset($_REQUEST['us_id'])) ? (int)$_REQUEST['us_id']:0;
$break_date = date('Y-m-d');
$cur_time 	= strtotime(date("H:i:s"));
// find the existing record in the case of edit
$extingData = ( $editid > 0 ) ? $btObj->getBreakTimeEntry($editid) : [];
if ($flag == 1) { //update the in time only 

	if ($extingData['us_id'] == $us_id && $break_date == $extingData['break_date']) {

		$btObj->updateToTime($editid, $extingData['from_time'], $preTally_user_id);
		$msg 	= "Time updated successfully";
		$status = 1;
	} else {
		$msg 	= 'Not Possible save this entry. contact admin';
	}
} else { // save or update break time
	
	// find the other form inputs
	$break_id	= (isset($_REQUEST['break_id'])) ? (int)$_REQUEST['break_id']:0;
	$remarks 	= (isset($_REQUEST['remarks'])) ? htmlspecialchars($_REQUEST['remarks'], ENT_QUOTES) : '';
	$out_hour	= (isset($_REQUEST['out_hour'])) ? (int)$_REQUEST['out_hour']:0;
	$out_minute	= (isset($_REQUEST['out_minute'])) ? (int)$_REQUEST['out_minute']:0;
	$in_hour	= (isset($_REQUEST['in_hour'])) ? (int)$_REQUEST['in_hour']:0;
	$in_minute	= (isset($_REQUEST['in_minute'])) ? (int)$_REQUEST['in_minute']:0;
	// out time calculate
	$out_hour 	= ($out_hour < 10) ? "0".$out_hour:$out_hour;
	$out_minute = ($out_minute < 10) ? "0".$out_minute:$out_minute;
	$from_time 	= $out_hour.":".$out_minute;
	$start 		= strtotime($from_time.':00');
	$to_time 	= '';
	$time_taken = 0;
	if ($in_hour > 0) { 
		// in time calculate
		$in_hour 	= ($in_hour < 10) ? "0".$in_hour:$in_hour;
		$in_minute 	= ($in_minute < 10) ? "0".$in_minute:$in_minute;
		$to_time 	= $in_hour.":".$in_minute;		
		$end 		= strtotime($to_time.':00');
		// time taken calculate		
		$time_taken = ($end - $start) / 60;
	}
	if ($btObj->getBreakTypeisOfficial($break_id) == 1 && $remarks == '') {
		$msg 	= 'Please enter a reason. A reason is mandatory when the break type is official.';
	}else if ($start > $cur_time) { // out Time missmatch

		$msg 	= 'Out Time mismatch. Enter Correct Time';
	}else if ($editid > 0) { // update 
		if ($in_hour > 0 && ($end < $start || $end > $cur_time)) {

			$msg 	= 'In and Out Timing mismatch';
		}else if ($break_date == $extingData['break_date'] && $extingData['is_edited'] == 0) {
			unset($extingData['old_data']);
			$inputs  = ['break_id'=>$break_id, 'remarks'=>$remarks, 'us_id'=>$us_id, 'from_time'=>$from_time, 'updated_by'=>$preTally_user_id, 'updated_at'=>date('Y-m-d H:i:s'), 'old_data'=>json_encode($extingData)];
			if ($to_time != '') {
				$inputs['to_time'] 		= $to_time;
				$inputs['time_taken'] 	= $time_taken;
				$inputs['status'] 		= "1";
			}
			$inputs['is_edited'] 		= "1";

			if ($btObj->checkDupBreakTime(['us_id'=>$us_id, 'status'=>"1", 'break_date'=>$break_date, 'from_time'=>$from_time.':00', 'id'=>$editid])) {

				$msg 	= 'Failed to Save. Duplicate Entry!';
			} else {
				$btObj->updateBreakTime($inputs, $editid);
				$msg 	= "Break Time updated successfully";
				$status = 1;	
			}			
		} else {
			$msg 	= 'Not Possible Edit. contact admin';
		}
	} else { // save new entry
		if ($preTally_user_id == $us_id) {
			$from_time = date('H:i');
		}
		//check the user have any opened break informations in this day (same day)
		if ($btObj->checkDupBreakTime(['us_id'=>$us_id, 'status'=>"0", 'break_date'=>$break_date])) {

			$msg 	= 'Already employee was outside the office. Please mark the IN time.';
		} else if ($btObj->checkDupBreakTime(['us_id'=>$us_id, 'status'=>"1", 'break_date'=>$break_date, 'from_time'=>$from_time.':00'])) {
			$msg 	= 'Failed to Save. Duplicate Entry!';
		} else if ($flag == 2 && $btObj->checkDupBreakTime(['us_id'=>$us_id, 'status'=>"1", 'break_date'=>$break_date, 'break_id'=>$break_id])) {
			// save break type again entered by the same user 18-07-2025
			$status = 2;
			$msg 	= $btObj->getBreakTypeName($break_id)." break is already added. Do you want to add this as another entry?";
		} else {			
			$inputs  = ['break_id'=>$break_id, 'remarks'=>$remarks, 'us_id'=>$us_id, 'from_time'=>$from_time, 'created_by'=>$preTally_user_id, 'created_at'=>date('Y-m-d H:i:s'), 'break_date'=>$break_date];

			if ( $btObj->saveBreakTime($inputs) ) {

				$msg 	= 'Break Time saved successfully.';
				$status = 1;
			} else {
				$msg 	= 'Failed to save break time.';
			}
		}
	}
}

echo json_encode(['message'=>$msg, 'status'=>$status]);
exit();
?>