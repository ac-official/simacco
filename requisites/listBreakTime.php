<?php
/**
 * Created By Bilin At 13-06-2025
 * Break time of all employees listed with filter and paginations
 * Edit and in time update options present
*/
// include the attendance related class files
include_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
$btObj		= new BreakTimeClass();

// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$inparams 				= ['off_id'=>$preTally_user_ofid];
// pagination limit and start parameters 
$inparams['start'] 	=  (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	=  (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;

// filter inputs processing
$filterData 			= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['from_date']	= (isset($filterData[1]) && $filterData[1] != "") ? date("Y-m-d", strtotime($filterData[1])) : NULL;
$inparams['to_date']	= (isset($filterData[2]) && $filterData[2] != "") ? date("Y-m-d", strtotime($filterData[2])) : NULL;
$inparams['break_id']   = (isset($filterData[3]) && $filterData[3] > 0) ? (int)$filterData[3] : 0;
$inparams['status']     = (isset($filterData[4]) && $filterData[4] == 1) ? "0" : "";

// find the office based break time settings
$settingbt  = $btObj->getBreakSettings($preTally_user_ofid);
if (!in_array($preTally_user_id, $settingbt['view_users'])) {
	// self users - no permissions to list others
	$inparams['user_id'] = $preTally_user_id;	
}

//find the details based on the parameters provided
$btObj->listBreakTime($inparams);
$total_records 		= $btObj->btUTotal;
$result_data 		= $btObj->btUsrList;
$cu_date 			= date('Y-m-d');
// find the date wise total data
if ($inparams['from_date'] == $inparams['to_date']) {
      $inparams['is_entry'] = "1";
      $btObj->listDailyUserTotal($inparams);
      $dailytotal       = $btObj->btUsrList;
} else {
      $dailytotal       = [];
}


// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';

    if (!empty($result_data)) {

		foreach($result_data as $rw) {

			$rowstyle      = '';
            $timeexced     = $dailytotal[$rw->break_date][$rw->us_id]['total'];   
			if(isset($dailytotal[$rw->break_date]) && isset($dailytotal[$rw->break_date][$rw->us_id]) && $dailytotal[$rw->break_date][$rw->us_id][$rw->id] > $settingbt['time'] && $rw->not_calculate != 1) {
				$rowstyle = ' style="color:red;"';
			} else if ($rw->max_time < $rw->time_taken) {
                $rowstyle = ' style="color:rgb(255 77 1);"';
            }

			echo '<row id="'.$rw->id.'" '.$rowstyle.'>';
			echo '<userdata name="btid">'.$rw->id.'</userdata>'
			.'<userdata name="us_id">'.$rw->us_id.'</userdata>';
			echo ' <cell>'.$rw->slno.'</cell>';
			echo ' <cell>'.$rw->title.'</cell>';
			echo '<cell>'.$rw->employee_name.'</cell>';
            echo '<cell>'.date('d/m/Y',strtotime($rw->break_date)).'</cell>';
            echo ' <cell>'.date('h:i A', strtotime($rw->from_time)).'</cell>';
            echo ' <cell>'.(($rw->status == 1) ? date('h:i A', strtotime($rw->to_time)):'-').'</cell>';

            
            if ($timeexced != 0) {
                $totaltimemsg = " Total Break Time : $timeexced Min <br> Remaining. Time  : ";
                $totaltimemsg .= (($settingbt['time'] < $timeexced) ? 0 : ($settingbt['time']-$timeexced))." Min";
                echo ' <cell><![CDATA[<div style="width:100%">';
                echo '<div style="width:79%; float:left;">'.(($rw->status == 1) ? $rw->time_taken.' Min ' : '- ').'</div>';
                echo '  <div style="width:20%; float:right;"><img src="images/icon/info_18.png"  id="bttaken_'.$rw->id.'"  onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.$totaltimemsg.'\');" /></div>';
                echo '</div>]]> </cell>';  
            } else {
                echo ' <cell>'.(($rw->status == 1) ? $rw->time_taken.' Min' : '-').'</cell>';  
            }
            if ($rw->remarks != '' && strlen($rw->remarks) > 15 ) {
                echo '<cell style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[<div style="width:85%; overflow:hidden;text-overflow: ellipsis;">'.htmlspecialchars_decode($rw->remarks).'</div><div style="margin-right: 2%;"><img src="images/icon/info_18.png" id="btremrk_'.$rw->id.'"  onmouseout="preTally.Settings.hideLabel(this);"  onmouseover="preTally.Settings.showLabel(this, \''.htmlspecialchars_decode($rw->remarks).'\');" /></div> ]]></cell>';
            } else {
                echo '<cell>'.(($rw->remarks != '') ? htmlspecialchars_decode($rw->remarks):'-').'</cell>';
            }
            echo ' <cell><![CDATA[ ';
            if ($rw->break_date == $cu_date) {

            	$cuTime 	= strtotime(date("Y-m-d H:i:s"));
            	if ($rw->status == 0 &&  $UserACLObj->add_break_time == 1 ) {
            		echo '<a href="#" class="gridBtn" style="float:left;"  onclick="preTally.Attendance.updateInTime('.$rw->id.','.$rw->us_id.');">Mark In</a>';
            		$editTime = strtotime("+30 minutes", strtotime($rw->created_at));
            	} else {

            		$editTime = strtotime("+30 minutes", strtotime($rw->updated_at));
            	}
            	if ($rw->is_edited == 0 && in_array($preTally_user_id, $settingbt['users']) && $cuTime <= $editTime ) {
            		echo '<a href="#" class="gridBtn" style="float:right;"  onclick="preTally.Attendance.loadBreakTimeForm('.$rw->id.');">Edit</a>';
            	}
            }

            echo ']]></cell>';			 	
            echo '</row>';
		}
	} else {

		echo '<row id="0"> <cell></cell> <cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';
?>