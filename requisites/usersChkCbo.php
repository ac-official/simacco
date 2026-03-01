<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/UserClass.php" );
require_once($BASEPATH . "includes/functions.php" );
$existing	= (isset($_REQUEST['userides'])) ? explode(',',$_REQUEST['userides']): [];
$UsrObj  	= new UserClass();
$filter 	=" WHERE OF_Id= ".$preTally_user_ofid." AND US_Status != 5 ORDER BY US_FName,US_LName";
$UsrObj->viewOfficeStaffs($filter);
$US_List = $UsrObj->UserArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';   
   if ($REQUEST["type"]=="filt") echo '<option value="All" selected="true">All</option>';
   if ($REQUEST["type"]=="clear") echo '<option value="0">Clear All</option>';
if($US_List){ 
    foreach($US_List as $rw){          
    	if (!empty($existing) && in_array($rw->US_Id,$existing)) {
       		$sel=' checked="1" selected="true"';
       	} else { $sel=''; }

        echo "<option value='".$rw->US_Id."' ".$sel." >".str_replace("&","&amp;",$rw->US_FName." ".$rw->US_LName)."</option>";
    }
}
echo '</complete>';
?>
