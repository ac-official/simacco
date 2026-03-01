<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
//23-01-2026
//$latesign_nw = 0;
//$earlysign_nw = 0;
$hourincom_nw = 0;
//$invalid_nw   = 0;
// end 23-01-2026

require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/UserClass.php");
if ($REQUEST['ST_Date']!=""){
 $date= date("Y-m-d", strtotime($REQUEST['ST_Date']));
}
else {
 $date=date("Y-m-d");
}
$filterUSR = filterHR_User($ACL_Obj->ACL_HR, 'USAUTH', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
$filterUSR.= " GROUP BY USAUTH.US_Id";
$UserObj = new UserClass();
$UserObj->selectCompanySettings($preTally_user_ofid);
$CompSett=$UserObj->CompanySettingsArray;
$UserObj->viewAttendanceGrid($filterUSR,$preTally_user_ofid,$date);
$US_Obj = $UserObj->UserArray;
 
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <userdata name="db_table">users</userdata>
    <userdata name="db_primary">US_Id</userdata>
    <userdata name="db_date">US_MDate</userdata>
    <userdata name="db_status">US_Status</userdata>
    <head>
        <column width="50" type="ro" align="center" sort="int"> # &lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="btn_Sort_Datt"/&gt;  </column>        
        <column width="*" type="ro" align="left" sort="na">Name &lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="btn_Sort_Datt"/&gt; </column>               
        <column width="*" type="ro" align="left" sort="na"> Location&lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="2" class="btn_Sort_Datt"/&gt; </column>
        <column width="*" type="ro" align="left" sort="na"> Department&lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="3" class="btn_Sort_Datt"&gt; </column>
        <column width="*" type="ro" align="left" sort="na"> Status </column>
        <column width="0" type="ro" align="left" sort="na"> MnStatus</column>
        <column width="0" type="ro" align="left" sort="na"> SubStatus</column>
        <column width="0" type="ro" align="left" sort="na"> SubStatus2</column>
        <column width="100" type="ro" align="left" sort="na">Sign In</column>
        <column width="100" type="ro" align="left" sort="na">Sign Out</column>
        <column width="100" type="ro" align="left" sort="na">System IP</column>
        <column width="60" type="ro" align="center" sort="na">Info</column>
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
                <param>,#text_filter_inc,#text_filter_inc,#select_filter_strict,#select_filter_strict,#select_filter_strict,,,</param>
        </call>
        </afterInit>       
    </head>';

    if($US_Obj) {
        $j = 1;
        $signedin=0;$nt_marked=0;$late_in=0;$early_out=0;$invalid_signs=0;$lvapp=0;$lvrpt=0;  
        $signouts=0;$early_out=0;$prop_out=0;$late_out=0;
        foreach($US_Obj as $rw) {
            $leastMins=$rw->US_WrkHours-$CompSett['CS_WrkHrGraceTime'];
            $suspicious = false;
            $Att_arr=$rw;           
            $sign_in_time="--:--";
            $sign_out_time="--:--";
                $statLabel="Unmarked";   
                $mainstatLabel="Unmarked";
                $substatlabel="--";
                $substatlabel2="--";
                $attndFlag=0;
                $wrkHrFlag=0;
                $color      = "black"; //23-08-2024
            if($Att_arr->device > 1)$suspicious=true;
            if($Att_arr->US_AttndDate<=$date && $Att_arr->US_AttndFlag==1)$attndFlag=1;
            if($Att_arr->US_WrkHrDate<=$date && $Att_arr->US_WrkHrFlag==1 && $Att_arr->AT_SignIn!=null)$wrkHrFlag=1;
                if($Att_arr->AT_Status==null && $attndFlag==0 && $wrkHrFlag==0)
                { 
                    if($Att_arr->AppFor==$Att_arr->RptdBy && $Att_arr->AppFor!=null){
                        $popLabel=$statLabel="Leave Applied";
                        $mainstatLabel="Unmarked";
                        $lvapp += 1;
                         $statusImg = 'approved.png';
                    }
                    elseif($Att_arr->AppFor!=$Att_arr->RptdBy && $Att_arr->AppFor!=null){
                        $popLabel=$statLabel="Leave Reported";
                        $mainstatLabel="Unmarked";
                        $lvrpt += 1;
                         $statusImg = 'lvrep.png';
                    }
                    else{
                         $popLabel=$statLabel="Not Marked";
                         $mainstatLabel="Unmarked";
                         $nt_marked +=1;
                         $statusImg = 'cross.png';      
                    }
                                       
                    
                }
                else
                {   if(($date!=date("Y-m-d"))&& ($Att_arr->AT_Status==0 )   && $attndFlag==0 && $wrkHrFlag==0)
                    {
                         $suspicious=false;
                         $popLabel=$statLabel="Not Signed Out";
                         $mainstatLabel="Marked";
                         $statusImg = 'cross_16.png';
                         $invalid_signs +=1;
                    }
                    //not maintain half day hours (minimum)
                    else if( (($Att_arr->AT_Hours < ($rw->US_WrkHours/2 - $CompSett['CS_WrkHrGraceTime'])) && ($Att_arr->AT_Status==1))   && $attndFlag==0 && $wrkHrFlag==0)
                    {
                         $suspicious=false;
                         $popLabel=$statLabel="Invalid";
                         $mainstatLabel="Marked";
                         $statusImg = 'cross_16.png';
                         $invalid_signs +=1;
                    }
                    else
                    {        
                    $relaxation=$CompSett['CS_LoginGraceTime']+1;
                    // earlier login relaxatioin was 30 min
                    if( $date <= "2023-02-20" ){
                        $relaxation = 31;
                    }
                    // 23-08-2024
                    $logintime      = strtotime($Att_arr->US_LoginTime);
                    $logouttime     = strtotime($Att_arr->US_LogoutTime);
                    $newver         = 0;
                    $leastMins      = $Att_arr->US_WrkHours;
                     //No grace time for login & logout after 19 Aug 2024 
                    if( $date >= "2024-08-21"){
                        $relaxation = 1;                        
                        $newver     = 1;
                        if(!empty($Att_arr->AT_AllotTime)) {            
                            $assignedTime   = json_decode($Att_arr->AT_AllotTime);
                            $logintime      = strtotime($assignedTime->in); 
                            $leastMins      = $assignedTime->whour;
                            $logouttime     = strtotime($assignedTime->out);
                        }              
                    }
                    $logintime      = strtotime("+".$relaxation." minutes", $logintime);
                    $logouttime     = strtotime("-".$relaxation." minutes", $logouttime);
                    // if section added by bilin @ 23-08-2024
                    if(($Att_arr->AT_SignOut!="00:00:00") && ($Att_arr->AT_Hours < $leastMins) && $attndFlag==0 && $wrkHrFlag==0 && $newver == 1)
                    {
                        $color      = "red";
                        $popLabel="Early Sign out";
                        $statLabel="Hour Incomplete";
                        $mainstatLabel="Marked";
                         $substatlabel2="Signed In";
                         $statusImg = 'warn_red_16.png';//warn_red_16.png//warn_16.png
                        if ($rw->AT_SignOutEarly > 0) {
                            $early_out +=1;
                        }
                        if ($rw->AT_SignInDelay > 0) {
                            $late_in +=1;
                        }
                         $signedin +=1;
                    } else if(strtotime ($Att_arr->AT_SignIn)>= $logintime && $attndFlag==0 && $wrkHrFlag==0)
                    {
                        if ($newver == 1) {$color = '#ff4d01';} //23-08-2024
                    $popLabel=$statLabel="Late Sign In";
                    $mainstatLabel="Marked";
                    $statusImg = 'warn_16.png';
                    $late_in +=1;
                        if ($rw->AT_SignOutEarly > 0) {// 23-08-2024
                            $early_out +=1;
                        }
                    } // add below else if section @ 23-08-2024
                    else if(($Att_arr->AT_SignOut!="00:00:00") && (strtotime ($Att_arr->AT_SignOut) <= $logouttime) && $attndFlag==0 && $wrkHrFlag==0 && $newver == 1)
                    {   
                        $color = '#ff4d01';
                        $popLabel="Early Sign out";
                        $statLabel="Early Sign out";
                        $mainstatLabel="Marked";
                         $substatlabel2="Signed In";
                         $statusImg = 'warn_16.png';
                         $early_out +=1;
                         $signedin +=1;
                    }
                    else if(($Att_arr->AT_SignOut!="00:00:00")&& ($Att_arr->AT_Hours < $leastMins) && $attndFlag==0 && $wrkHrFlag==0)
                    {
                    $popLabel="Early Sign out";
                    $statLabel="Signed In";
                    $mainstatLabel="Marked";
                     $substatlabel2="Signed In";
                     $statusImg = 'warn_16.png';
                     $early_out +=1;
                     $signedin +=1;
                    }
                else{                     
                     if($attndFlag==1){
                     $statusImg = 'tick_or.png';
                     $popLabel="Skipped Attendance";
                     $statLabel="Signed In";
                     $mainstatLabel="Marked";
                     $substatlabel="SignOut";
                     $signedin +=1;
                     }else if($wrkHrFlag==1 ){
                     $statusImg = 'user_icon_3.png';
                     $popLabel="Skipped Working Hours";
                     $statLabel="Signed In";
                     $mainstatLabel="Marked";
                     $substatlabel="SignOut";
                     $signedin +=1;
                     }
                     else if($Att_arr->AT_Hours > $rw->US_WrkHours){
                     $popLabel="Marked";
                     $statusImg = 'tick.png';
                     $statLabel="Signed In";
                     $mainstatLabel="Marked";
                     $substatlabel="Sign Out";
                     $substatlabel2="Late Sign Out";
                     $late_out+=1;
                     $signedin +=1;
                     }
                     else if($Att_arr->AT_Hours == $rw->US_WrkHours){
                     $popLabel="Marked";
                     $statusImg = 'tick.png';
                     $statLabel="Signed In";
                     $mainstatLabel="Marked";
                     $substatlabel="Sign Out";
                     $substatlabel2="Proper Sign Out";
                     if ($rw->AT_SignOutEarly <= 0 || $rw->AT_SignInDelay <= 0) { // 23-08-2024
                        $prop_out+=1;
                     }
                     $signedin +=1;
                     }
                     else{
                     $popLabel="Marked";
                     $statusImg = 'tick.png';
                     $statLabel="Signed In";
                     $mainstatLabel="Marked";
                     $substatlabel="Sign Out";
                     $substatlabel2="Proper Sign Out";
                     //$prop_out+=1;
                     $signedin +=1;
                     }    
                     
                    }
                }
                if($suspicious){
                    $statusImg = 'lock_screen_open_16.png';
                    $popLabel="<b>Multiple Staff Logins from the Same Device Detected:</b> This staff member has either signed in from a system that is not their own or has allowed someone else to sign in from their system.";
                }
                if($Att_arr->AT_SignIn!="" || $Att_arr->AT_SignIn!=null)
                {
                    $sign_in_time=date('g:i a',strtotime($Att_arr->AT_SignIn));
                }
                 if($Att_arr->AT_SignOut!="" || $Att_arr->AT_SignOut!=null)
                {
                    $sign_out_time=date('g:i a',strtotime($Att_arr->AT_SignOut));
                }
                 if(($Att_arr->AT_Status==0) && ($Att_arr->AT_SignOut=="00:00:00"))
                {
                   $sign_out_time="--:--";
                }
                   
                }
               // echo strtotime ($Att_arr['AT_SignIn']).":".strtotime ("09:00:00");
            
//23-01-2026
if ($attndFlag!=1 && $Att_arr->AT_Status==1 ) {

    /*if ($Att_arr->AT_SignInDelay > 0 && $Att_arr->AT_Hours > 120) {
        $latesign_nw +=1;
    }
    if ($Att_arr->AT_SignOutEarly > 0 && $Att_arr->AT_Hours > 120) {
        $earlysign_nw +=1;
    }*/
    if ($Att_arr->AT_Hours > 120 && $Att_arr->AT_ExtraTime < 0) {
        $hourincom_nw +=1;
    }
    //$invalid_nw   = 0;  
}

// end 23-01-2026

            echo '<row id="'.$rw->US_Id.'">
                <userdata name="attndFlag">'.$attndFlag.'</userdata>
                <cell>'.$j.'</cell>
               
                <cell>'.ucfirst($rw->US_FName).' ' .ucfirst($rw->US_LName).'</cell>
                <cell>'.ucfirst($rw->LC_Name).'</cell>
                <cell>'.$rw->DP_Name.'</cell>';               
               echo '<cell style="color:'.$color.';">'.$statLabel.'</cell>'; 
               echo '<cell>'.$mainstatLabel.'</cell>';
               echo '<cell>'.$substatlabel.'</cell>';
               echo '<cell>'.$substatlabel2.'</cell>';
               echo'<cell>'.$sign_in_time.'</cell>
                <cell>'.$sign_out_time.'</cell><cell>'.$rw->AT_IPAddr.'</cell>';   
               
                echo '<cell><![CDATA[<img src="images/icon/'.$statusImg.'" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$popLabel.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
            echo '</row>';  
            $j++;
        }
    }
$marked=$signedin+$late_in+$invalid_signs;
$unmarked=$nt_marked+$lvapp+$lvrpt;
$signouts=$early_out+$prop_out+$late_out;
echo '<userdata name="att_marked">'.$marked.'</userdata>
    <userdata name="att_unmarked">'.$unmarked.'</userdata>
    <userdata name="att_signout">'.$signouts.'</userdata>    
    <userdata name="att_signedin">'.$signedin.'</userdata>    
    <userdata name="att_latein">'.$late_in.'</userdata>    
    <userdata name="att_lvapplied">'.$lvapp.'</userdata>
    <userdata name="att_lvrptd">'.$lvrpt.'</userdata>
    <userdata name="att_invalid">'.$invalid_signs.'</userdata>
    <userdata name="att_ntmarked">'.$nt_marked.'</userdata>    
    <userdata name="att_earlyout">'.$early_out.'</userdata>    
    <userdata name="att_propout">'.$prop_out.'</userdata>    
    <userdata name="att_lateout">'.$late_out.'</userdata>  
    <userdata name="att_incmhour">'.$hourincom_nw.'</userdata>     
    </rows>';
?>