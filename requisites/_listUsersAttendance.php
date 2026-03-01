<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$UserObj    = new UserClass();
$userId = $REQUEST['US_Id'];
$UserObj->selectCompanySettings($preTally_user_ofid);
$UserObj->selectPunchingTimes($userId);
$CompObj=$UserObj->UserLogArray;
$CompSett=$UserObj->CompanySettingsArray;
$leastMins=$CompObj['US_WrkHours']-$CompSett['CS_WrkHrGraceTime'];
$stdate = $REQUEST['ST_Date'];
$lastdate = $REQUEST['LST_Date'];
$month = date('M');
if ($stdate != "" && $lastdate != "") {
    $filter = " WHERE AT_Date BETWEEN '" . $stdate . "' AND '" . $lastdate . "' AND US_Id=" . $userId ." ORDER BY AT_Date DESC";
    $month = date("M", strtotime($stdate));
} else {
    $cellDate=$REQUEST['celldate'];
    $yminfo = explode("-", $cellDate);
    $minfo = $yminfo[1];
    $monthInfo = ltrim($minfo, '0');
    $yearInfo = $yminfo[0];
    $startDate = 1;
    $endDate = cal_days_in_month(CAL_GREGORIAN, $monthInfo, $yearInfo);
    $stdate = $cellDate. '-'. '1';
    $lastdate = $cellDate. '-'. $endDate;
    $filter = " WHERE AT_Date BETWEEN '" . $stdate . "' AND '" . $lastdate . "' AND US_Id=" . $userId ." ORDER BY AT_Date DESC";
}
$AttObj = new AttendanceClass();
$att_res = array();
if ($month == date("M")) {
    $curDate = date("Y-m-d");
}
$att_res = $AttObj->calcAttendance($month, $curDate, $userId);
$AttObj->viewAttendance($filter);
$Att_Obj = $AttObj->AttendanceArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>		
<userdata name="full_days">' . $att_res['Full_days'] . '</userdata>
<userdata name="half_days">' . $att_res['Half_days'] . '</userdata>
<userdata name="off_days">' . $att_res['Off_days'] . '</userdata>
<userdata name="leave_days">' . $att_res['Leave_days'] . '</userdata>    
<head>
			<column width="50" type="ro" align="center" > SlNo </column>
			<column width="150" type="ro" align="left" > Date </column>
                        <column width="50" type="ro" align="left" > Day </column>
                        <column width="*" type="ro" align="left" > Sign In </column>
                        <column width="*" type="ro" align="left" > Sign Out </column>
                        <column width="180" type="ro" align="left" > Hours </column> 
                        <column width="120" type="ro" align="left" >Status</column>
			
			<settings>
				<colwidth>px</colwidth>
			</settings>
			<beforeInit> 
                                <call command="setSkin">
					<param>dhx_skyblue</param>
				</call> 
				<call command="setImagePath">
					<param>assets/grid/codebase/imgs/</param>
				</call> 
				<call command="enableSmartRendering">
					<param>false</param>
				</call> 
                        </beforeInit>
                        <afterInit>       
                            <call command="attachHeader">
                                <param>#rspan,#rspan,#rspan,#rspan,#rspan,#select_filter,#select_filter</param>
                            
                            </call>
                        </afterInit>  

		  </head>';
if ($Att_Obj) {
    $j = 1;
    foreach ($Att_Obj as $rw) {
        $date=$rw->AT_Date;
        $mins = ($rw->AT_Hours) % 60;
        $hr = ($rw->AT_Hours - $mins) / 60;
        $sign_in_time = "--:--";
        $sign_out_time = "--:--";
        $statLabel = "Unmarked";
        
          if (($date != date("Y-m-d")) && ($rw->AT_Status == 0 ) || ( ($rw->AT_Hours < $CompObj['US_WrkHours']/2 - $CompSett['CS_WrkHrGraceTime']) && ($rw->AT_Status==1)) ) {
                $statLabel = "Invalid";
                $statusImg = 'warn_16.png';
                $invalid_signs +=1;
            } else {
        $relaxation=$CompSett['CS_LoginGraceTime']+1;
         // earlier login relaxatioin was 30 min
            if( date('Y-m-d',strtotime($rw->AT_Date)) <= "2023-02-20" ){
                $relaxation = 31;
            }
        if (strtotime($rw->AT_SignIn) >= strtotime("+".$relaxation." minutes", strtotime($CompObj['US_LoginTime']))) {
            $statLabel = "Late Sign In";
            $statusImg = 'warn_16.png';
            $late_in +=1;
        } else if (($rw->AT_Hours < $leastMins) && ($rw->AT_SignOut != "00:00:00")) {
            $statLabel = "Early Sign out";
            $statusImg = 'warn_16.png';
            $early_out +=1;
        } else {
          
                $statLabel = "Marked";
                $statusImg = 'tick.png';
                $marked +=1;
            }
        }
        if ($rw->AT_SignIn != "" || $rw->AT_SignIn != null) {
            $sign_in_time = date('g:i a', strtotime($rw->AT_SignIn));
        }
        if ($rw->AT_SignOut != "" || $rw->AT_SignOut != null) {
            $sign_out_time = date('g:i a', strtotime($rw->AT_SignOut));
        }
        if (($Att_arr['AT_Status'] == 0) && ($rw->AT_SignOut == "00:00:00")) {
            $sign_out_time = "--:--";
        }
        if ($hr == 0 && $mins == 0) {
            $time = "--";
        } else {
            $time = (int) $hr . ' Hours ' . (int) $mins . "Minutes";
        }
        echo '<row id="' . $rw->AT_Id . '">						
						<cell>' . $j . '</cell>
						<cell name="AT_Date">'.date('d/m/Y', strtotime($date)).'</cell>
                                                <cell name="AT_Day">'.date("D", strtotime($date)).'</cell>
                                                <cell name="AT_SignIn">'. $sign_in_time.'</cell>
                                                <cell name="AT_SignOut">'.$sign_out_time.'</cell>
                                                <cell name="AT_Hours">'.$time.'</cell>
                                                <cell name="AT_Hours">'.$statLabel.'</cell>
                                                    </row>';
        $j++;
    }
}else{
    echo '<row id="0"> 
        <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
}

echo '</rows>';
?>