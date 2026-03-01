<?php
require_once($BASEPATH . "preTallyClass/CityClass.php");

$newCityObj = new CityClass();
$newCityObj->CT_Data = array(
    'US_Id' => $preTally_user_id,
    'CT_Name'     =>trim(htmlspecialchars($_REQUEST['CT_Name'], ENT_QUOTES)),
    'ST_Id'       =>htmlspecialchars($_REQUEST['ST_Id'], ENT_QUOTES),    
    'CT_Status'   =>htmlspecialchars($_REQUEST['CT_Status'], ENT_QUOTES),
    'CT_MDate' 	=> date('Y-m-d H:i:s')
);

if($newCityObj->verifyCity(htmlspecialchars($_REQUEST['CT_Id'], ENT_QUOTES))){
    if(htmlspecialchars($_REQUEST['CT_Id'], ENT_QUOTES) == 0){
        $newCityObj->CT_Data['CT_CDate'] = date('Y-m-d H:i:s');
        echo $newCityObj->newCity();
    }else {
        echo $newCityObj->updateCity(htmlspecialchars($_REQUEST['CT_Id'], ENT_QUOTES));
    }
    
} else {  echo'fail'; }
?>
