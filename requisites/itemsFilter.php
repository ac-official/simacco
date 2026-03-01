<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . 'preTallyClass/NotificationClass.php' );
include_once($BASEPATH . 'preTallyClass/UserClass.php' );
include_once($BASEPATH . 'includes/functions.php' );
$NotfItmObj = new NotificationClass();
$UsrObj     = new UserClass();

$ITId = $REQUEST['IT_Id'];
$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
generate_reptIDS($preTally_user_ofid,$preTally_user_id,$UsrObj->UserArray);
$Rprtid=$_SESSION['user_report_id'];
if($REQUEST['filter'] == "Pending") {
    $NotfItmObj->viewPendingItemsFilter($preTally_user_ofid);
}elseif($REQUEST['filter'] == "Reports") {
    $NotfItmObj->viewItemsReportFilter($preTally_user_ofid);
} else {
  $NotfItmObj->viewItemsFilter($Rprtid);  
}

$IT_NotfObj = $NotfItmObj->NotfArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
if($IT_NotfObj){
    $count = 1;
    if ($REQUEST["type"]=="filt") echo '<option value="All" selected="true">All</option>';
    foreach($IT_NotfObj as $rw){ 
        if($ITId && $rw->IT_Id == $ITId) { $selected = 'selected = "'. 1 .'" ' ; } 
        //else if($count == 1 && $REQUEST['filter'] == "Reports") { $selected = 'selected = "'. 1 .'" ' ; } 
        else { $selected = 'selected = "'. 0 .'" ' ; }        
        echo '<option value="'.$rw->IT_Id.'"  '. $selected .'>'.str_replace("&","&amp;",$rw->IT_Name).'</option>';
        $count++;
    }
}
echo '</complete>';
?>