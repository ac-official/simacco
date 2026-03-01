<?php
require_once($BASEPATH . "preTallyClass/AddressClass.php");

$PlaceObj = new AddressClass();
$PlaceObj->AD_Data = array(        
	'AP_Name' 	=> trim(htmlspecialchars($_REQUEST['AP_Name'], ENT_QUOTES)),        
	'CT_Id'         => htmlspecialchars($_REQUEST['CT_Id'], ENT_QUOTES),
        'AP_Status' 	=> htmlspecialchars($_REQUEST['AP_Status'], ENT_QUOTES),
        'AP_MDate' 	=> date('Y-m-d H:i:s')
);
//echo $PlaceObj->verifyPlace(htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES)) ;
//die();
if( $PlaceObj->verifyPlace(htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES) == 0) {
           
                $PlaceObj->AD_Data["AP_CDate"] = date('Y-m-d H:i:s'); 
		echo $PlaceObj->newPlaces();                
	} else {
		echo $PlaceObj->updatePlace(htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES));
	}
} else { echo 'fail'; }
?>