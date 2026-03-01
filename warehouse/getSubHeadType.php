<?php
require_once($BASEPATH . "/preTallyClass/TrackClass.php");
$TrackObj = new TrackClass();
$IT_Id = $REQUEST['IT_Id'];
if($REQUEST['type']=="BS")
echo $SH_Track = $TrackObj->getSubHeadTypeBS($IT_Id);    
else    
echo $SH_Track = $TrackObj->getSubHeadType($IT_Id);

?>