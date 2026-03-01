<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/UserClass.php" );
require_once($BASEPATH . "includes/functions.php" );
$UsrObj  = new UserClass();
$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
generate_reptIDS($preTally_user_ofid,$preTally_user_id,$UsrObj->UserArray);
$Rprtid=$_SESSION['user_report_id'];   
$UsrObj->userReportsMe(' DD.US_Id, DD.US_FName AS DD_FName, DD.US_LName AS DD_LName ', ' AS DD WHERE US_Id IN ('.$Rprtid.') ORDER BY DD_FName' );
$US_List = $UsrObj->UserArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
   if($REQUEST["lVRpt"]=='All')echo "<option value='0' >All</option>";
   else if ($REQUEST["type"]=="filt") echo '<option value="All" selected="true">All</option>';
if($US_List){ 
    foreach($US_List as $rw){  
        if($_REQUEST['self'] && $preTally_user_id == $rw->US_Id) $selected = ' selected="1" ';
        echo "<option value='".$rw->US_Id."' ".$selected." >".$rw->DD_FName." ".$rw->DD_LName."</option>";
    }
}
echo '</complete>';
?>
