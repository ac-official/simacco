<?php
include_once ($BASEPATH . 'preTallyClass/AddressClass.php');
$lcid=$REQUEST['loc'];
$ctid=$REQUEST['ctid'];
$AddressObj=new AddressClass();
if($lcid!=0 && $lcid!=null)
{
$AddressObj->getPlaceIdsLoc($lcid);
}
elseif ($ctid!=0 && $ctid!=null) 
{
$AddressObj->getPlaceIdsCty($ctid);
}
//print_r($AddressObj->IdArray);
echo json_encode($AddressObj->IdArray);
?>


