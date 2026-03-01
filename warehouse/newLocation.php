<?php
require_once($BASEPATH . "preTallyClass/LocationClass.php");
require_once($BASEPATH . "preTallyClass/AddressClass.php");
require_once($BASEPATH . "includes/saveGoogleAddress.php");
$LocationObj = new LocationClass();
$AddressObj  = new AddressClass();
 $addressArray = array(
    'GOGL_Country'	=> trim(htmlspecialchars($_REQUEST['GOGL_Country'], ENT_QUOTES)),
    'GOGL_State'	=> trim(htmlspecialchars($_REQUEST['GOGL_State'], ENT_QUOTES)),
    'GOGL_City'         => trim(htmlspecialchars($_REQUEST['GOGL_City'], ENT_QUOTES)),
    'GOGL_Location'	=> trim(htmlspecialchars($_REQUEST['GOGL_Location'], ENT_QUOTES)),
    'GOGL_Place'	=> trim(htmlspecialchars($_REQUEST['GOGL_Place'], ENT_QUOTES)),
    'GOGL_Street'	=> trim(htmlspecialchars($_REQUEST['GOGL_Street'], ENT_QUOTES)),
);
$addressIds = json_decode(saveGoglAddress($addressArray));
/*if($_REQUEST['AP_Name']=='new'){      
    $AddressObj ->AD_Data = array(
            'AP_Name'   => trim(htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES)),
            'CT_Id'     => htmlspecialchars($_REQUEST['CT_Id'], ENT_QUOTES),
            'AP_Status' => 1,
            'AP_CDate'  => date('Y-m-d H:i:s'),
            'AP_MDate'  => date('Y-m-d H:i:s')
        );
       $LocId = $AddressObj->addPlaces();
}
else{
    $LocId=htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES);
}*/
$LocationObj->LC_Data = array(
        'US_Id' 	=> $preTally_user_id,
	'LC_Name' 	=> trim(htmlspecialchars($_REQUEST['LC_Name'], ENT_QUOTES)),
        'OF_Id' 	=> htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES),	
        'LC_Phone'	=> htmlspecialchars($_REQUEST['LC_Phone'], ENT_QUOTES),
        'LC_Pincode'	=> htmlspecialchars($_REQUEST['GOGL_Pincode'], ENT_QUOTES),
        'CT_Id'         => $addressIds->CT_Id,        
        'ST_Id'         => $addressIds->ST_Id,
        'CN_Id'         => $addressIds->CN_Id,
        'ALC_Id'        => $addressIds->ALC_Id,
        'PL_Id'         => $addressIds->PL_Id,        
        'SR_Id'         => $addressIds->SR_Id, 
        'LC_Street'     => '',
        'LC_Building'   => htmlspecialchars($_REQUEST['LC_Building'], ENT_QUOTES),               
	'LC_Comments'	=> htmlspecialchars($_REQUEST['LC_Comments'], ENT_QUOTES),
	'LC_Status' 	=> htmlspecialchars($_REQUEST['LC_Status'], ENT_QUOTES),
	'LC_MDate' 	=> date('Y-m-d H:i:s')
);

if( $LocationObj->verifyLocation(htmlspecialchars($_REQUEST['LC_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($_REQUEST['LC_Id'], ENT_QUOTES) == 0) {
		$LocationObj->LC_Data["LC_CDate"] = date('Y-m-d H:i:s'); 
		$lcid=$LocationObj->newLocation();                
                 $LocationObj->Bal_Data = array(        
                'OF_Id' 	=> $preTally_user_ofid,        	
                'LC_Id' 	=> $lcid,        
                'OB_OpenBal'   => 0,
                'OB_Status' 	=> 0,        
                'OB_Date' 	=> date('Y-m-d H:i:s')
        );	 	 	 	 	 	
              $LocationObj->createOpeningBalances();
              
                $LocationObj->OS_Data = array(        
                    'OF_Id'         => $preTally_user_ofid,        	
                    'LC_Id'         => $lcid,        
                    'OS_OpenBal'    => 0,
                    'OS_Status'     => 0,        
                    'OS_Date'       => date('Y-m-d H:i:s')
                );	 	 	 	 	 	
                $LocationObj->createStockOpeningBalances();
              
              
                echo 'Branch Created Successfully';
	} else {
		echo $LocationObj->updateLocation(htmlspecialchars($_REQUEST['LC_Id'], ENT_QUOTES));
	}
} else { echo 'fail'; }
?>