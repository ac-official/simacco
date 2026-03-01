<?php
require_once($BASEPATH . 'includes/functions.php');
/*if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}*/
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 0;
$year         = date("Y");
$currentMonth = date("m");
$mnthyrStr=str_pad($REQUEST['att_month'], 2, "0", STR_PAD_LEFT).'-'.$REQUEST['att_year'];
if($REQUEST['att_year'] && $REQUEST['att_month'] && $mnthyrStr != date('m-Y')){       
    $year = $REQUEST['att_year'];
    $month   = str_pad($REQUEST['att_month'], 2, "0", STR_PAD_LEFT);
    $g_date  = $year.'-'.$month.'-01';
    $lstdate = date("t", strtotime($g_date)); 
} else {    
    $month  = date("m");
    $g_date     =$year.'-'.$month.'-01';
    //$lstdate = date("t", strtotime($g_date));
    $lstdate = date("d")-1;
}
$filter = "";
$orderCol=" LC.LC_Name";
$orderBy=" ASC";
if($_REQUEST["Filters"]){
    $filters = explode(",",$_REQUEST["Filters"]);
    if($filters[0])
        $filter.=" AND CONCAT(US_FName, ' ', US_LName) LIKE '".mysqli_real_escape_string($GLOBALS['con'],ucfirst($filters[0]))."%'";
    if($filters[1]!=0 && $filters[1]!="")
        $filter.= " AND LC.LC_Id=".$filters[1];
    if($filters[2]==1){
        $orderCol=" CONCAT(US_FName, ' ', US_LName)";
    }
    else {
        $orderCol=" LC.LC_Name";
    }
    if($filters[3]=='des')$orderBy=" DESC";
}
    $a_date     = $year."-".$month."-".$lstdate;
    $UserObj    = new UserClass();
    $UserObj->selectCompanySettings($preTally_user_ofid); // Office ID & Details
    
    $CompSett    = $UserObj->CompanySettingsArray;
    $AttObj     = new AttendanceClass();
    $LeaveObj   = new LeaveClass();
    $AttObj->getHolidays($a_date,$preTally_user_ofid); // Company Holidays List
    $AttObj->getWeekendOffs($month,$year,$preTally_user_ofid); // Weekend Off List   
    $AttObj->getOfficeRH($preTally_user_ofid); // RH Off List
       
    $AttObj->getLeaveType($preTally_user_ofid); // Type Of Leaves
    $AttLv      = $AttObj->getLeaveTypeArray;
    
    if ( $_GET["count"] > 0) { // if limit passed by user then get find the total
        $att_count  = $AttObj->countReptAttendance($month, $year, $preTally_user_ofid, 0, $filter);
    } else {
        $att_count  = 0;
    }
    
    //echo $att_count;
    $userFilter = 0;
    if($REQUEST['userFilter']) $userFilter = $REQUEST['userFilter'];
    
    $AttObj->listReptAttendance($month, $year, $preTally_user_ofid, 0, $filter,$_GET["posStart"], $_GET["count"], $userFilter,$CompSett['CS_WrkHrGraceTime'],$orderCol.$orderBy);
    $holiday_count  = 0;
    $WeekendOffs    = array();
    $Att_Obj        = array();
    $Holidays       = array();
    $Holidays       = $AttObj->Holidays;    //var_dump($Holidays[0]['dates']);die();    
    $WeekendOffs    = $AttObj->WeekOffs;    
    $Att_Obj        = $AttObj->RptAttendance;
    $att_count      = ($att_count <= 0 && !empty($Att_Obj)) ? count($Att_Obj):$att_count;
    $daysInMonth    = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    /*if($currentMonth!=$month){*/
    $leave_headers="";
    $attendance_headers="";
    foreach($AttLv as $rows){
        $LT_Name = ucwords($rows->LT_Name); 
        //$leave_headers .= '<column width="110" type="ro" align="center" sort="na">Approved'." ".$LT_Name.'</column>';
        $leave_headers .= '{width:45,  type:"ro",   align:"center",  sort:"na", value:"'.$LT_Name[0].'L"},';        
        $LT_Id .= $rows->LT_Id;
        $LT_Id .= ",";
    }
    $LT_Ids = rtrim($LT_Id,","); 
   /* foreach($AttLv as $rows){
        $LT_Name = ucwords($rows->LT_Name);                 
        $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"'.$LT_Name[0].'L (Avail)"},';                
    }*/
    
    //$leave_headers .= '<column width="75" type="ro" align="center" sort="na">Salary Deductable Leaves</column>';
    $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"SDL"},';
    $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"LS"},';
    $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"ES"},';
    $leave_headers .= '{width:50,  type:"ro",   align:"center",  sort:"na", value:"IH"},';
    $attendance_headers .= '{width:60,  type:"ro",   align:"center",  sort:"na", value:"Details"}';                          
                /*}*/  
/*echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");*/
//echo '<rows total_count="'.$att_count.'" pos="'.$_GET["posStart"].'">';
    
    if($AttObj->verifyMonth("employee_payroll","EP_Month",$month,$preTally_user_ofid,"EP_Year",$year)){ 
        $editable = true;
    }else{
        $editable = false;
    }
    
echo '{
        "total_count"   :'.$att_count.',
        "pos"           :'.$_GET["posStart"].',';

    if($_GET["posStart"] == 0 && $_GET["calltype"] != "upd"){
        /*echo'<head>
            <column width="50" type="ed" align="center" sort="na">  # </column>                 
            <column width="200" type="ro" align="left" sort="na">Name</column>
            <column width="150" type="ro" align="left" sort="na">Branch</column>';*/
        echo '
            
            head:[
            {width:47,  type:"ed", align:"center", sort:"na", value:"SlNo"},
            {width:165, type:"ro", align:"left",   sort:"na", value:"Name"},
            {width:150, type:"ro", align:"left",   sort:"na", value:"Branch"},
            ';
            for ($j = 1; $j <= $lstdate; $j++) {
                if ($j < 10) {
                    $j = "0" . $j;
                }
                $date =  $j ;
                //echo '<column width="45" type="combo" align="center" sort="na">' . $date . '</column>';
                echo '{width:30, type:"combo",    align:"center",   sort:"na", value:"' . $date . '"}, 
                      ';
            }
        /*echo '<column width="75" type="ro" align="center" sort="na">FullDay</column>
              <column width="75" type="ro" align="center" sort="na">HalfDay</column>
              <column width="75" type="ro" align="center" sort="na">TotalLeaves</column>';*/
        echo '{width:40, type:"ro", align:"center", sort:"na", value:"FD"},
              {width:40, type:"ro", align:"center", sort:"na", value:"HD"},
              {width:45, type:"ro", align:"center", sort:"na", value:"TL"},
              ';
              $LT_Id = "";
        echo $leave_headers;  
        echo $attendance_headers;

        /*echo'<settings>
                <colwidth>px</colwidth>
            </settings>
            <beforeInit> 
                <call command="setSkin">
                    <param>dhx_skyblue</param>
                 </call> 
                <call command="setImagePath">
                    <param>assets/grid/codebase/imgs/</param>
                </call>                            
            </beforeInit>
        </head>';       */ 
    
        echo '],';
    }
    
    echo 'rows:[';
    //echo '<userdata name="leaveTypes">'.json_encode($AttLv).'</userdata>';
    $p = $_GET["posStart"]+1;
    if ($Att_Obj){
        $LTId = $LT_Ids;
        $LT_Id = explode(",",$LTId); 
        
        foreach ($Att_Obj as $rw){
            $array_user=array();
            $user_details   = $rw["USR_Details"];
            $att_details    = $rw["ATT_Dates"];  
            $array_user     = array_keys($att_details); 
            $att_attr       = array_values($att_details); 
            $allleaves      = "";
            $sumofAllLeaves = 0;
            $totalLeave     = 0;
            $lopdeduction   = 0;
            $takeHomeSalary = 0;
            $attendanceInfo = '<img src=\'images/icon/info_18.png\' style=\' margin:2px 0;cursor:pointer; \' class=\'img_Info\' id=\'img_Info\' title=\'Click here to view more details\'  onclick=\' preTally.UserProfile.reportShowAttendance('.$user_details['US_Id'].');\'  />';
            $lop=0;            
            $AttObj->tmp_rharray=array();                
            //$onedaySal=($user_details['US_GrossSal'])/$daysInMonth;              
            //print_r($att_details);die();

            $AttObj->ApprovedLeaveDays = array();  
            $AttObj->getApprovedLeaveDates($user_details['US_Id'],$month, $year,$LT_Ids);//Leave Type Dates                         
            for($k=0;$k<count($LT_Id);$k++){
                $AttObj->getApprovedLeave($user_details['US_Id'],$month, $year,$LT_Id[$k]);//Sum of Leave Types
                $approvedLeave     = $AttObj->getApprovedLeaveArray[0];
                $approvedLeaveName = $AttObj->getApprovedLeaveArray[1];
                if(($approvedLeave=="null")||($approvedLeave=="")){
                    $approvedLeave=0; 
                }
                $allleaves.=$approvedLeaveName.":".$approvedLeave.",";
            }  
            $half=0;$full=0;$absent=0;
            unset($AttObj->tmp_hol);
            $AttObj->tmp_hol    = array();   
            $allleaves          = rtrim($allleaves,","); 
            $individualLeaves   = explode(",",$allleaves);           
            for($j=0;$j<count($individualLeaves);$j++){  
                $indiLeavesCountAndName = explode(":",$individualLeaves[$j]);
                $sumofAllLeaves=$sumofAllLeaves+$indiLeavesCountAndName[1];
            }   
            $rh_count=0; 
            $stid=$user_details['ST_Id']; 
            $dpid=$user_details['DP_Id'];                            

            /*echo '<row id="' . $user_details['US_Id'] . '">    
                    
                    <cell>' . $p . '</cell>               
                    <cell>' . $user_details['US_FName'] .' '. $user_details['US_LName'].'</cell>
                    <cell>' . $user_details['LC_Name'] . '</cell>';*/
            
            //echo '{ id : 0,
                
//userdata: { "db_table": "balance_sheets", "db_primary": "BS_Id", "db_date": "BS_MDate", "db_status" : "BS_Status" },
//data:[]
 //               },';
            echo '{ id : ' . $user_details['US_Id'] . ',
                    data:[
                        "' . $p . '",
                        "' . $user_details['US_FName'] .' '. $user_details['US_LName'].'",
                        "' . $user_details['LC_Name'] . '",';
                    $half           = 0;
                    $full           = 0;
                    $absent         = 0;
                    $holiday_count  = 0;   
                    $late_sign_in=0;     
                    $duty_left=0;                            
                    unset($AttObj->tmp_hol);
                    $AttObj->tmp_hol = array(); 
                    $cellTypeValue = array();
                    $offTypeValue = array();                                        
                    $AttObj->getAdjMnthAttendance($user_details['US_Id'],$month, $year); 
                    $AttObj->getPunchTime($user_details['US_Id'],$month, $year); 
                    if($AttObj->AdjcntAttendance["last"]!=null)                              
                        array_push($array_user,$AttObj->AdjcntAttendance["last"]);
                    if($AttObj->AdjcntAttendance["next"]!=null)
                        array_push($array_user,$AttObj->AdjcntAttendance["next"]);                               
                                 
                    for ($j = 1; $j <= $lstdate; $j++) {                                              
                        $title      = "";
                        $cellValue  = "";
                        $Lbl        = "";
                        //$offTypeValue = array();
                        $AttObj->tmp_rharray = array_merge((array)$AttObj->RH_Holidays[0],(array)$AttObj->RH_Holidays[$stid]);                              
                        $AttObj->getTakenRHBatches($user_details['US_Id']);                            
                        $flp_array  = array_flip($AttObj->RH_Batches);                                        
                        $rh_holiday = array_diff_key($AttObj->tmp_rharray, $flp_array);                                            
                        if ($j < 10) $j = "0" . $j;   
                        
                        $date = $year . "-" . $month . "-" . $j; 
                        if(multi_array_search($date,$AttObj->RH_Holidays[0])||multi_array_search($date,$AttObj->RH_Holidays[$stid])) {
                            $cellType = "R"; //RH -- Reserved Holiday
                        } else {
                            $cellType = "N"; // NRM -- Normal Holiday
                        }
                        $approvedLeaveDates=array();
                            if(!empty($AttObj->ApprovedLeaveDays)) {
                                $approvedLeaveDates=array_column($AttObj->ApprovedLeaveDays, 'LRD_Date');    
                            }
                           $hrs = $att_details[$date]['AT_Hours'];                                                       
                           $array_intr=array();
                           $array_intr=array_intersect($approvedLeaveDates, $array_user);
                           //print_r($array_intr);
                           foreach($array_intr as $days){
                               $chrs = $att_details[$days]['AT_Hours'];
                           $loginTime   =$logoutTime="00:00:01";
                           $loginTime   =$att_details[$days]['AT_SignIn'];
                           $logoutTime  =$att_details[$days]['AT_SignOut'];    
                           
                           $meanDifAM   =abs(strtotime("12:00:00")-strtotime($loginTime));
                           $meanDifPM   =abs(strtotime("12:00:00")-strtotime($logoutTime));
                           $minWrkHrs   =($user_details['US_WrkHours']/2)-$CompSett['CS_WrkHrGraceTime'];
                           $attSession="FL";
                           if($meanDifAM > $meanDifPM)
                               $attSession="FN";
                           else 
                               $attSession="AN";    
                           $key_L      = array_search($days,$approvedLeaveDates);                                    
                           $session    =$AttObj->ApprovedLeaveDays[$key_L]['LRD_Session'];    
                           if (($key = array_search($days, $array_user)) !== false && $session=="FL") {
                                unset($array_user[$key]);
                           }
                           else if (($key = array_search($days, $array_user)) !== false && $chrs < $minWrkHrs && ($session=="FN" || $session=="AN")) {
                               if($attSession==$session){                                         
                                   unset($array_user[$key]);                                   
                               }
                           }
                           }
                        if (in_array($date, $array_user)&& (!multi_array_search($date, $approvedLeaveDates)) && $hrs >= (($user_details['US_WrkHours']/2)-$CompSett['CS_WrkHrGraceTime'])){                                          
                            unset($AttObj->tmp_hol);
                            $AttObj->tmp_hol = array();
                                                            
                            if($hrs >= ($user_details['US_WrkHours']-$CompSett['CS_WrkHrGraceTime'])){
                                $cellValue  = "P";
                                //$title="P";
                                $full       +=1;
                                //echo '<cell title="Present" usid="'.$user_details['US_Id'].'"  cellType="'.$cellType.'"  cellValue="'.$cellValue.'" attdate="'.$date.'" class="green_cell">P</cell>';
                                echo '"P",';
                                if($cellType === "R") $cellTypeValue[$j] = $cellType;
                                //$offTypeValue[$j] = $title;
                            }
                            elseif ($hrs >= (($user_details['US_WrkHours']/2)-$CompSett['CS_WrkHrGraceTime']) && $hrs<($user_details['US_WrkHours']-$CompSett['CS_WrkHrGraceTime'])){
                                $cellValue  = "P2";
                                //$title="H";
                                $half+=1;
                                //echo '<cell  title="Half Day" usid="'.$user_details['US_Id'].'" cellType="'.$cellType.'" cellValue="'.$cellValue.'" attdate="'.$date.'"  class="orange_cell">H</cell>';
                                echo '"P2",';
                                if($cellType === "R") $cellTypeValue[$j] = $cellType;
                                //$offTypeValue[$j] = $title;
                            }else
                            {
                                     $absent+=1;
                                    //$title="Absent";
                                    //$title="A";
                                    $cellValue="L";
                                     echo '"L",';
                                }
                        } else{ 
                                $AttObj->getRHTakenDays($user_details['US_Id']);
                                $RH_takendays=$AttObj->RH_TaknDays; 
                                $m_Hol = array();                                                               
                                $m_Hol=$WeekendOffs[$dpid];                                                               
                                $m_Hol_2 = array_merge((array)$Holidays[$stid]['dates'],(array)$Holidays[0]['dates']);                                                            
                                $m_Hol_3 = array_merge((array)$WeekendOffs[$dpid],(array)$RH_takendays);                                                          
                                $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);
                                sort($m_Hol);
                                $AttObj->chkadjDays($array_user,$m_Hol,$date);                                   
                            if((!in_array($date, $WeekendOffs[$dpid]))
                                &&(!in_array($date, $Holidays[0]['dates']))
                                &&(!in_array($date, $Holidays[$stid]['dates'])                                
                                &&(!multi_array_search($date, $approvedLeaveDates))
                                &&(!in_array($date, $AttObj->tmp_hol))        
                                &&(multi_array_search($date,$rh_holiday)))){
                                $RH_Status=$AttObj->checkRHDate($rh_holiday,$date,$user_details['US_Id']);
                                if($RH_Status=="RH_Apply"){
                                    $LeaveObj->deleteLeaveRecords($user_details['US_Id'], $date);
                                }
                            }
                            $AttObj->getRHTakenDays($user_details['US_Id']);
                            $RH_takendays=$AttObj->RH_TaknDays; 
                            if ((in_array($date, $WeekendOffs[$dpid])|| in_array($date, $Holidays[0]['dates']) || in_array($date, $Holidays[$stid]['dates']))
                                    &&(!in_array($date, $AttObj->tmp_hol) && !multi_array_search($date, $approvedLeaveDates)&& !in_array($date,$RH_takendays))){                                                                                                    
                                $holiday_count+=1;                                                        
                                
                                if(in_array($date, $WeekendOffs[$dpid])){                                                                                              
                                    $day = strtolower(date("l",strtotime($date)));
                                    if($day=='sunday'){
                                        $cellValue="S";
                                        $Lbl="S";
                                    }else{
                                        $cellValue="W";
                                        $Lbl="W";
                                    }
                                    
                                    
                                }
                                elseif(in_array($date, $Holidays[0]['dates']) || in_array($date, $Holidays[$stid]['dates'])){                                                            
                                    //$title="Holiday";
                                  $Ntitleindx= array_search($date, $Holidays[0]['dates']);
                                  $Stitleindx= array_search($date, $Holidays[$stid]['dates']); 
                                   if($Ntitleindx!==FALSE){$titleindx=$Ntitleindx;$indx=0;}
                                   else{ $titleindx=$Stitleindx; $indx=$stid;}
                                   
                                    $title=$Holidays[$indx]['title'][$titleindx];
                                    $cellValue="H";
                                    $Lbl= "H";
                                }

                                //echo '<cell  title="'.$title.'" usid="'.$user_details['US_Id'].'" cellType="'.$cellType.'" cellValue="'.$cellValue.'" attdate="'.$date.'" class="red_cell">'.$Lbl.'</cell>';
                                echo '"'.$Lbl.'",';
                                if($cellType === "R") $cellTypeValue[$j] = $cellType;
                                if($title) $offTypeValue[$j] = ucfirst($title);

                            } else {  //print_r($AttObj->tmp_hol);                                                                                                          
                                
                                unset($m_Hol);
                                $m_Hol = array();                                                               
                                $m_Hol=$WeekendOffs[$dpid];                                
                               // $m_Hol_1=  array_merge((array)$WeekendOffs[$dpid],(array)$approvedLeaveDates);
                                
                              /*  $m_Hol_2 = array_merge((array)$Holidays[$stid]['dates'],(array)$Holidays[0]['dates']);                                                            
                                $m_Hol_3 = array_merge((array)$WeekendOffs[$dpid],(array)$rhapplicableDays);                                                          
                                $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);*/

                                if(in_array($date,$RH_takendays)){
                                    $holiday_count  +=1;  
                                    $rh_count       +=1;
                                    $title          ="Restrited Holiday";
                                    //$title          ="R";
                                    $cellValue      ="H";
                                    $Lbl            ="H";
                                }
                                elseif(is_numeric(array_search($date, $approvedLeaveDates))){
                                    
                                    $Lbl        = "L";                                                            
                                    $key_H      = array_search($date,$approvedLeaveDates);
                                    $title      = $AttObj->ApprovedLeaveDays[$key_H]['LT_Name'];                                                                    
                                    $cellValue  = $AttObj->ApprovedLeaveDays[$key_H]['LT_Id']; 
                                    $session    =$AttObj->ApprovedLeaveDays[$key_H]['LRD_Session'];
                                    $halftooltip='';$halfTitle='';
                                    if($session!='FL'){
                                        $halftooltip="P2"; 
                                        $halfTitle="Half Day";            
                                        $absent+=0.5;    
                                        if (in_array($date, $array_user)){                                        
                                        $half+=1;
                                        unset($m_Hol);
                                        $m_Hol = array();     
                                        $Lbl="P2";    
                                        }
                                    }else{
                                        $absent+=1;
                                        $Lbl="L";    
                                    }
                                    $words      = explode(' ', $title); // array of word                                    
                                    //$Lbl=strtoupper($words[0][0].$words[1][0].$halftooltip);                                        
                                    $title.=' '.$halfTitle;
                                } else{
                                    $absent+=1;
                                    //$title="Absent";
                                    //$title="A";
                                    $cellValue="L";
                                    $Lbl="L";
                                }  
                                //sort($m_Hol);           
                                //print_r($array_user);
                                //$AttObj->chkNextDay($date,$m_Hol,$array_user);                                
                                //echo '<cell  title="'.$title.'" usid="'.$user_details['US_Id'].'" cellType="'.$cellType.'" cellValue="'.$cellValue.'" attdate="'.$date.'" class="red_cell">'.$Lbl.'</cell>';                                                        
                                echo '"'.$Lbl.'",';
                                if($cellType === "R") $cellTypeValue[$j] = $cellType;
                                
                                if($title) $offTypeValue[$j] = ucfirst($title);
                            }
                        }
                    }
                    
                    
                    $lop = $lstdate-(($half/2)+$full+$holiday_count);
                    $totalLeave = $lop - $sumofAllLeaves;
                    if($totalLeave<0) {
                        $totalLeave=0;
                    }
                    //$lopdeduction=round(($totalLeave*$onedaySal));
                    //$pfesiwfDeduction=$user_details['US_DedEPF']+$user_details['US_DedESI']+$user_details['US_DedLWF'];
                    //$deduction=$lopdeduction+$pfesiwfDeduction;
                    //$takeHomeSalary= round($user_details['US_GrossSal'] -$deduction);

                    /*echo '<cell><![CDATA[<font color="green"> '.$full.'</font>]]></cell>'
                        .'<cell><![CDATA[<font color="orange">'.$half.'</font>]]></cell>'
                        .'<cell><![CDATA[<font color="red">   '.$lop.' </font>]]></cell>';*/
                    
                    echo '"'.$full.'","'.$half.'","'.$lop.'",';

                    /*if($currentMonth!=$month){*/
                    for($l=0; $l<count($individualLeaves); $l++){  
                        $indiLeavesCountAndName = explode(":",$individualLeaves[$l]);
                        //echo'<cell><![CDATA[<font color="green">'.$indiLeavesCountAndName[1].'</font>]]></cell>';                        
                        echo '"'.$indiLeavesCountAndName[1].'",';                        
                    }
                   /* for($l=0; $l<count($individualLeaves); $l++){  
                        $indiLeavesCountAndName = explode(":",$individualLeaves[$l]);                     
                        echo '"'.$indiLeavesCountAndName[1].'",';                        
                    }*/
                    $late_sign_in = (empty($AttObj->lateSignIn)) ? 0: $AttObj->lateSignIn;
                    $early_left = (empty($AttObj->earlySignOut)) ? 0: $AttObj->earlySignOut;
                    $hour_pending = (empty($AttObj->dutyLeft)) ? 0: $AttObj->dutyLeft;
                    //echo '<cell><![CDATA[<font color="red">'.$totalLeave.'</font>]]></cell>';
                    echo '"'.$totalLeave.'",';
                    echo '"'.$late_sign_in.'",';
                    echo '"'.$early_left.'",';
                     echo '"'.$hour_pending.'",';
                    echo '"'.$attendanceInfo.'",';
                    /*}*/
                    //echo'</row>';
                    echo '], 
                        userdata:{
                            \'userID\'  :\''.$user_details['US_Id'].'\',
                            \'offType\' :\''.json_encode($offTypeValue).'\',
                            \'cellType\':\''.json_encode($cellTypeValue).'\'     
                        } 
                    },';
                    $p++;
                }
    }else {
/*        echo '{ id : "null_row",
                    data:["","<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>"]';*/
    }
//echo '</rows>';
    //warehouse/updateAttReport.php&date=2015-10-05&usid=648&updType=Pre&prevVal=L&cellType=NRM
    //warehouse/updateAttReport.php&date=2015-10-01&usid=138&updType=P&prevVal=L
echo '], 
    \'leaveTypes\':\''.json_encode($AttLv).'\',
    \'leaveCount\':\''.count($individualLeaves).'\',
    \'totalDays\':\''.$lstdate.'\',
    \'isEditable\':\''.$editable.'\',    
    \'ALCEdit\':\''.$ACL_Obj->ACL_AttendanceEdt.'\',        
    \'cellDate\':\''.$year.'-'.$month.'\' 
    }';
?>