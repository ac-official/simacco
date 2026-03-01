<?php 
require_once($BASEPATH . "preTallyClass/LeaveClass.php");
$lvObj          = new LeaveClass();

$filter_mask    = ' US.OF_Id = '.$preTally_user_ofid;
$filterData     = explode(",",$REQUEST['filter']);
$year           = $month = $flag = $cells = $toolTip = '';

if (!isset($_GET["posStart"]))
    $_GET["posStart"]   = 0;
if (!isset($_GET["count"]))
    $_GET["count"]      = 50;

if($filterData[0]) {
    $year  = $filterData[0];
//    $month = ($year == date('Y')) ? date('n') : 12;
}

if($filterData[1]) {
    $filter_mask .= " AND CONCAT(US.US_FName,' ',US.US_LName) LIKE '".$filterData[1]."%'" ;
}

if($filterData[2]) {
    $filter_mask .= " AND LC.LC_Name LIKE '".$filterData[2]."%'" ;
}

if($filterData[3]) {
    $flag         = 1;
}

$lvObj->getLeaveType($preTally_user_ofid);
$leaveTypes             = $lvObj->leaveTypeArray;

$lvObj->userLeaveTypeDays($filter_mask,$year);
$approvedLeaveDetails   = $lvObj->ApprovedLeaves;
$pendingLeaveDetails    = $lvObj->PendingLeaves;

$lvObj->leaveDetails($filter_mask,$year,$_GET["posStart"],$_GET["count"]);
$leaveDetails           = $lvObj->leaveDetailsArray;
$Count                  = $lvObj->leaveDetailsCount($filter_mask,$year);

$lvObj->userEligibleLeaves($filter_mask,$year,$preTally_user_ofid);
$eligibleLeaves         = $lvObj->userLeaveArray;
$EL_DateArray           = $lvObj->eligibleLeaveArray;  // doj/ES_Date
//print_r($eligibleLeaves);
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count = "'.$Count.'" pos = "'.$_GET["posStart"].'">
    <userdata name = "TL_Count">'.$Count.'</userdata>' ;
    if ($_GET["posStart"] == 0 && !$filterData[1] && !$filterData[2] && !$flag) {		
    echo '<head>
        <column width = "50" sort = "na" type = "ro" align = "center" >SlNo</column>	 			 	 	
        <column width = "*" sort = "na"  type = "ro" align = "left" ><![CDATA[<input type="text" id= "leave_user_name" class="leave_text_filter" style="width: 90%;" placeholder="User Name">]]></column>
        <column width = "150" sort = "na" type = "ro" align = "left" ><![CDATA[<input type="text" id = "leave_branch" class="leave_text_filter" style="width: 90%;" placeholder="Branch">]]></column>
        <column width = "70" sort = "na" type ="ro" align = "center" >Approved Leaves</column>';
        $cellFlag            =   0;
        if(count($leaveTypes) > 0) {
            foreach($leaveTypes as $lt) {
                $cells      .= $lt->LT_Name.",";
                $toolTip    .= ",false";

                if($cellFlag == 1)
                    echo '<column width="70" sort = "na"  type="ro" align="center" >#cspan</column>';
                $cellFlag    = 1;
            }
        } else 
            $cells          .= ",";
        
        $cellFlag            = 0;
        echo '<column width="70" sort = "na"  type="ro" align="center" >Total Leaves</column>';
        echo '<column width="70" sort = "na"  type="ro" align="center" >Eligible Leaves</column>';
        $toolTip            .= ",false";
        if(count($leaveTypes) > 0) {
            foreach($leaveTypes as $lt) {
                $toolTip    .= ",false";

                if($cellFlag == 1)
                    echo '<column sort = "na"  width="70" type="ro" align="center" >#cspan</column>';
                $cellFlag    = 1;
            }
        } else 
            $cells          .= ",";
       
        echo '<column width="70" sort = "na"  type="ro" align="center" >Total Eligible Leaves</column>
            <column width="70" sort = "na"  type="ro" align="center" >Pending Leaves</column>
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
                <call command="attachHeader"><param>,,,'.$cells.','.$cells.',</param></call>
                <call command="enableTooltips"><param>false,false,false'.$toolTip.',false,false</param></call>
            </afterInit>
    </head>';
    }
    
    if($leaveDetails) {
        $j = $_GET["posStart"]+1;
        foreach($leaveDetails as $rw) {
            $totalDays = $eligLeaves = $totalDays = 0;
            echo '<row id="'.$rw->US_Id.'">
            <cell title =" " name="from">'.$j.'</cell>
            <cell title =" "  name="from">'.$rw->Name.'</cell>
            <cell title =" "  name="to">'.$rw->LC_Name.'</cell>';
            $dpid=$rw->DP_Id;
            if(count($leaveTypes) > 0) {
                foreach($leaveTypes as $lt) { 
                    echo '<cell title = " " >';
                    echo $leaveDays = ($approvedLeaveDetails[$rw->US_Id][$lt->LT_Id]) ? $approvedLeaveDetails[$rw->US_Id][$lt->LT_Id] : 0 ;
                    $totalDays      += $leaveDays;
                    echo '</cell>';
                }
            }  else 
                echo '<cell title = " ">0</cell>';

            echo '<cell title = " " >'.$totalDays.'</cell>';
            if(count($leaveTypes) > 0) {
                foreach($leaveTypes as $lt) {
                    echo '<cell title =" " > ';
                    if(isset($eligibleLeaves[$rw->US_Id][$lt->LT_Id]) && $eligibleLeaves[$rw->US_Id][$lt->LT_Id] > 0) {
                        if(($dpid==123 || $dpid==122 || $dpid==121 || $dpid==120 || $dpid==98 || $dpid==66)&&($lt->LT_Id==6)){     
                            echo "0";
                        }
                        else{
                            $date       = explode('-',$EL_DateArray[$rw->US_Id]['Date']);
                            if($year == date('Y')) {
                                if($year == $date[1]) {
                                    
                                    if($date[0] != date('n')) 
                                        $month = date('n') - $date[0];
                                    else if($date[0] == date('n'))
                                            $month = 1;
                                    else
                                        $month = $date[0];
                                } else {
                                    $month = date('n');
                                }
                            } else {
                                $month = 12;
                            }
                            
                            
                            $leaves = (($eligibleLeaves[$rw->US_Id][$lt->LT_Id] * $month) - $approvedLeaveDetails[$rw->US_Id][$lt->LT_Id] - $pendingLeaveDetails[$rw->US_Id][$lt->LT_Id]);    
                            if($leaves > 0)
                                echo $leaves;
                            else {
                                echo $leaves = 0;
                            }
                            
                            $eligLeaves  += $leaves;
                        }                                              
                        
                    } else
                        echo '0';
                    echo '</cell>';
                }
            }  else 
                echo '<cell title = " ">0</cell>';

            echo '<cell title = " " >'.$eligLeaves.' </cell> 
            <cell title =" " >';
            echo ($pendingLeaveDetails[$rw->US_Id]) ? array_sum($pendingLeaveDetails[$rw->US_Id]) : '0';
            echo '</cell>';
            echo'</row>';
            $j++; 
        }
    }
    echo '</rows>';
?>