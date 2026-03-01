<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
 $LeaveObj = new LeaveClass();
 $LeaveObj->getLeaveType($preTally_user_ofid );
 $aObj=$LeaveObj->leaveTypeArray; 
 // echo "<pre>";print_r($aObj);die;
 if($REQUEST['subordId'])
     $userid=$REQUEST['subordId'];
 else
     $userid=$preTally_user_id;
     $LeaveObj->getLeaveTypeParams($userid); 
     $dpid=$LeaveObj->LeaveParams["DP_Id"];
     $esid=$LeaveObj->LeaveParams["ES_Id"];    
 
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
echo '<option value=""  selected="true">Select Leave Type</option>'; 
 foreach($aObj as $rw) {
     $esStatusArray=array();
     $esarray=array();
     $esa=array();
     $esStatus=$rw->ES_Status;
     $esStatusArray=explode(",",$esStatus);     
     for($i=0;$i<5;$i++){         
         $element=$esStatusArray[$i];
         $esa=  explode(":", $element);
         $esarray[$esa[0]]=$esa[1];
     }          
     print_r($esarray);
     if(($dpid==123 || $dpid==122 || $dpid==121 || $dpid==120 || $dpid==98 || $dpid==66)&&($rw->LT_Id==6)){
         continue;
     }elseif($dpid!=66 && $rw->LT_Id!=38){
        continue;
     }if($esarray[$esid]==0)
         continue;
            echo '<option value="'.$rw->LT_Id.'"  '.$selected.'>'.str_replace("&","&amp;",$rw->LT_Name).'</option>';
     
}
echo '</complete>';

?>