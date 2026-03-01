<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
include_once($BASEPATH . 'includes/saveGoogleAddress.php' );
$AttObj = new AttestationClass();


$validateAJItemsArray = array("GOGL_Street_1","GOGL_Place_1","GOGL_Location_1","GOGL_City_1","GOGL_State_1","GOGL_Country_1");
$validateAJMsgsArray  = array("AJ_StrtErr","AJ_PlaceErr","AJ_LocErr","AJ_CityErr","AJ_StateErr","AJ_CntryErr");

foreach ($validateAJItemsArray as $key => $value) {
    if(!preg_match("/^[a-zA-Z ()-]+$/", $_REQUEST[$value]))  {   // valid candidate address names
        echo $validateAJMsgsArray[$key];
        exit;
    }
}

$addressArray = array(
    'GOGL_Country'	=> trim(htmlspecialchars($_REQUEST['GOGL_Country_1'], ENT_QUOTES)),
    'GOGL_State'	=> trim(htmlspecialchars($_REQUEST['GOGL_State_1'], ENT_QUOTES)),
    'GOGL_City'         => trim(htmlspecialchars($_REQUEST['GOGL_City_1'], ENT_QUOTES)),
    'GOGL_Location'	=> trim(htmlspecialchars($_REQUEST['GOGL_Location_1'], ENT_QUOTES)),
    'GOGL_Place'	=> trim(htmlspecialchars($_REQUEST['GOGL_Place_1'], ENT_QUOTES)),
    'GOGL_Street'	=> trim(htmlspecialchars($_REQUEST['GOGL_Street_1'], ENT_QUOTES)),
);
$addressIds = json_decode(saveGoglAddress($addressArray));

if($_REQUEST['AJ_SubmittedType'] == 1)  // self submitted
    $AJ_SubmittedType = 1;
else 
    $AJ_SubmittedType = $_REQUEST['AJ_SubmittedType'];
   
$AJ_DeliveredType = $_REQUEST['AJ_DeliveredType'];

if($AJ_DeliveredType == 1)          // delivered to self
    $AJ_JobDeliverdTo = 0;
else if($AJ_DeliveredType == 2)     // this branch
    $AJ_JobDeliverdTo = $preTally_user_lcid;
else if($AJ_DeliveredType == 3)     // other branch
    $AJ_JobDeliverdTo = $_REQUEST['AD_Branch']; 
else                                // others
    $AJ_JobDeliverdTo = 0;

if(!$_REQUEST['AJ_DOB'])
    $DOB = "0000-00-00";
else
    $DOB = $_REQUEST['AJ_DOB'];

if(!$_REQUEST['AJ_Id']) {   // inserting new job
    $AttObj->Track_Data = array(
        'US_Id'     =>  $preTally_user_id,
        'OF_Id'     =>  $preTally_user_ofid,
        'TR_Track'  =>  '',
        'TR_Status' =>  1,
        'TR_CDate'  => date('Y-m-d H:i:s')
    );
    $AJ_Track_Id = $AttObj->generateTrackID('');   // generate track id
    $AttObj->Job_Data = array(     
        'AJ_Track_Id'       => $AJ_Track_Id, 
        'OF_Id'             => $preTally_user_ofid,
        'LC_Id'             => $preTally_user_lcid,
        'AJ_FName'          => htmlspecialchars(trim($_REQUEST['AJ_FName']), ENT_QUOTES),  
        'AJ_DOB'            => htmlspecialchars(trim($DOB), ENT_QUOTES),
        'AJ_HouseNo'        => htmlspecialchars(trim($_REQUEST['AJ_HouseNo']), ENT_QUOTES),        
        'AJ_HouseName'      => htmlspecialchars(trim($_REQUEST['AJ_HouseName']), ENT_QUOTES),
        'AJ_Society'        => htmlspecialchars(trim($_REQUEST['AJ_Society']), ENT_QUOTES),        
//        'AJ_Street'         => htmlspecialchars(trim($_REQUEST['AJ_Street']), ENT_QUOTES), 
        'SR_Id'             => $addressIds->SR_Id, 
        'PL_Id'             => $addressIds->PL_Id,
        'ALC_Id'            => $addressIds->ALC_Id,
        'CT_Id'             => $addressIds->CT_Id,        
        'ST_Id'             => $addressIds->ST_Id, 
//        'CN_Id'             => $addressIds->CN_Id, 
//        'AJ_Place'          => htmlspecialchars(trim($_REQUEST['AJ_Place']), ENT_QUOTES), 
        'AJ_Village'        => htmlspecialchars(trim($_REQUEST['AJ_Village']), ENT_QUOTES), 
//        'CT_Id'             => htmlspecialchars(trim($AJ_CT_Id), ENT_QUOTES), 
        'AJ_Pincode'        => htmlspecialchars(trim($_REQUEST['GOGL_Pincode_1']), ENT_QUOTES), 
//        'DT_Id'             => htmlspecialchars(trim($_REQUEST['AJ_DT_Id']), ENT_QUOTES), 
        'AJ_Email'          => htmlspecialchars(trim($_REQUEST['AJ_Email']), ENT_QUOTES), 
        'AJ_Mobile1'        => htmlspecialchars(trim($_REQUEST['AJ_Mobile1']), ENT_QUOTES), 
        'AJ_Mobile2'        => htmlspecialchars(trim($_REQUEST['AJ_Mobile2']), ENT_QUOTES), 
        'AJ_Landline'       => htmlspecialchars(trim($_REQUEST['AJ_Landline']), ENT_QUOTES), 
        'AJ_BuildingName'   => htmlspecialchars(trim($_REQUEST['AJ_BuildingName']), ENT_QUOTES), 
        'AJ_ContactMob'     => htmlspecialchars(trim($_REQUEST['AJ_ContactMob']), ENT_QUOTES), 
        'AJ_PoliceLoc'      => htmlspecialchars(trim($_REQUEST['AJ_PoliceLoc']), ENT_QUOTES), 
        'AJ_Email2'         => htmlspecialchars(trim($_REQUEST['AJ_Email2']), ENT_QUOTES), 
        'AJ_DeliveryDate'   => $_REQUEST['AJ_DeliveryDate'] ? $_REQUEST['AJ_DeliveryDate'] : '0000-00-00', 
        'AJ_WorkExp'        => htmlspecialchars(trim($_REQUEST['AJ_WorkExp']), ENT_QUOTES), 
        'AJ_SubmittedType'  => htmlspecialchars(trim($AJ_SubmittedType), ENT_QUOTES), 
        'AJ_DeliveredType'  => htmlspecialchars(trim($AJ_DeliveredType), ENT_QUOTES), 
        'AJ_JobDeliverdTo'  => htmlspecialchars(trim($AJ_JobDeliverdTo), ENT_QUOTES), 
        'AJ_TotalAmount'    => 0,
        'AJ_ReceivedDate'   => htmlspecialchars(trim($_REQUEST['AJ_ReceivedDate']), ENT_QUOTES), 
        'AJ_Cdate'          => date('Y-m-d H:i:s'),
        'AJ_MDate'          => date('Y-m-d H:i:s')
    );
} else {    // updating job
    $AttObj->Job_Data = array(     
        'AJ_FName'          => htmlspecialchars(trim($_REQUEST['AJ_FName']), ENT_QUOTES),  
        'AJ_DOB'            => htmlspecialchars(trim($DOB), ENT_QUOTES),
        'AJ_HouseNo'        => htmlspecialchars(trim($_REQUEST['AJ_HouseNo']), ENT_QUOTES),        
        'AJ_HouseName'      => htmlspecialchars(trim($_REQUEST['AJ_HouseName']), ENT_QUOTES),
        'AJ_Society'        => htmlspecialchars(trim($_REQUEST['AJ_Society']), ENT_QUOTES),        
        'SR_Id'             => $addressIds->SR_Id, 
        'PL_Id'             => $addressIds->PL_Id,
        'ALC_Id'            => $addressIds->ALC_Id,
        'CT_Id'             => $addressIds->CT_Id,        
        'ST_Id'             => $addressIds->ST_Id, 
//        'CN_Id'             => $addressIds->CN_Id, 
//        'AJ_Place'          => htmlspecialchars(trim($_REQUEST['AJ_Place']), ENT_QUOTES), 
        'AJ_Village'        => htmlspecialchars(trim($_REQUEST['AJ_Village']), ENT_QUOTES), 
//        'CT_Id'             => htmlspecialchars(trim($AJ_CT_Id), ENT_QUOTES), 
        'AJ_Pincode'        => htmlspecialchars(trim($_REQUEST['GOGL_Pincode_1']), ENT_QUOTES), 
//        'DT_Id'             => htmlspecialchars(trim($_REQUEST['AJ_DT_Id']), ENT_QUOTES), 
        'AJ_Email'          => htmlspecialchars(trim($_REQUEST['AJ_Email']), ENT_QUOTES), 
        'AJ_Mobile1'        => htmlspecialchars(trim($_REQUEST['AJ_Mobile1']), ENT_QUOTES), 
        'AJ_Mobile2'        => htmlspecialchars(trim($_REQUEST['AJ_Mobile2']), ENT_QUOTES), 
        'AJ_Landline'       => htmlspecialchars(trim($_REQUEST['AJ_Landline']), ENT_QUOTES), 
        'AJ_BuildingName'   => htmlspecialchars(trim($_REQUEST['AJ_BuildingName']), ENT_QUOTES), 
        'AJ_ContactMob'     => htmlspecialchars(trim($_REQUEST['AJ_ContactMob']), ENT_QUOTES), 
        'AJ_PoliceLoc'      => htmlspecialchars(trim($_REQUEST['AJ_PoliceLoc']), ENT_QUOTES), 
        'AJ_Email2'         => htmlspecialchars(trim($_REQUEST['AJ_Email2']), ENT_QUOTES), 
        'AJ_DeliveryDate'   => $_REQUEST['AJ_DeliveryDate'] ? $_REQUEST['AJ_DeliveryDate'] : '0000-00-00', 
        'AJ_WorkExp'        => htmlspecialchars(trim($_REQUEST['AJ_WorkExp']), ENT_QUOTES), 
        'AJ_SubmittedType'  => htmlspecialchars(trim($AJ_SubmittedType), ENT_QUOTES), 
        'AJ_DeliveredType'  => htmlspecialchars(trim($AJ_DeliveredType), ENT_QUOTES), 
        'AJ_JobDeliverdTo'  => htmlspecialchars(trim($AJ_JobDeliverdTo), ENT_QUOTES), 
        'AJ_TotalAmount'    => 0,
        'AJ_ReceivedDate'   => htmlspecialchars(trim($_REQUEST['AJ_ReceivedDate']), ENT_QUOTES),
        'AJ_MDate'          => date('Y-m-d H:i:s')
    );
}

/* Submitter Details */
if($AJ_SubmittedType != 1) {
    
    $validateASItemsArray = array("GOGL_Street_2","GOGL_Place_2","GOGL_Location_2","GOGL_City_2","GOGL_State_2","GOGL_Country_2");
    $validateASMsgsArray  = array("AS_StrtErr","AS_PlaceErr","AS_LocErr","AS_CityErr","AS_StateErr","AS_CntryErr");
    
    foreach ($validateASItemsArray as $key => $value) {
        if(!preg_match("/^[a-zA-Z ()-]+$/", $_REQUEST[$value]))  {   // valid submitter address names
            echo $validateASMsgsArray[$key];
            exit;
        }
    }
    unset($addressArray);
    $addressArray = array(
        'GOGL_Country'	=> trim(htmlspecialchars($_REQUEST['GOGL_Country_2'], ENT_QUOTES)),
        'GOGL_State'	=> trim(htmlspecialchars($_REQUEST['GOGL_State_2'], ENT_QUOTES)),
        'GOGL_City'     => trim(htmlspecialchars($_REQUEST['GOGL_City_2'], ENT_QUOTES)),
        'GOGL_Location'	=> trim(htmlspecialchars($_REQUEST['GOGL_Location_2'], ENT_QUOTES)),
        'GOGL_Place'	=> trim(htmlspecialchars($_REQUEST['GOGL_Place_2'], ENT_QUOTES)),
        'GOGL_Street'	=> trim(htmlspecialchars($_REQUEST['GOGL_Street_2'], ENT_QUOTES)),
    );
    $addressIds = json_decode(saveGoglAddress($addressArray));

    $AttObj->Submitter_Data = array(  
        'AS_FName'      => htmlspecialchars(trim($_REQUEST['AS_FName']), ENT_QUOTES),  
        'SR_Id'         => $addressIds->SR_Id, 
        'PL_Id'         => $addressIds->PL_Id,
        'ALC_Id'        => $addressIds->ALC_Id,
        'CT_Id'         => $addressIds->CT_Id,        
        'ST_Id'         => $addressIds->ST_Id, 
//        'CN_Id'         => $addressIds->CN_Id, 
        'AS_Relation'   => htmlspecialchars(trim($_REQUEST['AS_Relation']), ENT_QUOTES),
        'AS_HouseNo' 	=> htmlspecialchars(trim($_REQUEST['AS_HouseNo']), ENT_QUOTES),        
        'AS_HouseName'  => htmlspecialchars(trim($_REQUEST['AS_HouseName']), ENT_QUOTES),
        'AS_Society' 	=> htmlspecialchars(trim($_REQUEST['AS_Society']), ENT_QUOTES),        
//        'AS_Street' 	=> htmlspecialchars(trim($_REQUEST['AS_Street']), ENT_QUOTES), 
//        'AP_Id'         => $AS_AP_Id, 
        'AS_Village' 	=> htmlspecialchars(trim($_REQUEST['AS_Village']), ENT_QUOTES), 
//        'CT_Id' 	=> htmlspecialchars(trim($AS_CT_Id), ENT_QUOTES), 
        'AS_Pincode' 	=> htmlspecialchars(trim($_REQUEST['GOGL_Pincode_2']), ENT_QUOTES), 
//        'ST_Id' 	=> $AS_ST_Id, 
        'AS_Email'      => htmlspecialchars(trim($_REQUEST['AS_Email']), ENT_QUOTES), 
        'AS_Mobile1' 	=> htmlspecialchars(trim($_REQUEST['AS_Mobile1']), ENT_QUOTES), 
        'AS_Mobile2' 	=> htmlspecialchars(trim($_REQUEST['AS_Mobile2']), ENT_QUOTES), 
        'AS_Landline' 	=> htmlspecialchars(trim($_REQUEST['AS_Landline']), ENT_QUOTES) 
    );
}

/* Deliver details */
if($AJ_DeliveredType == 4) {

    $validateADItemsArray = array("GOGL_Street_3","GOGL_Place_3","GOGL_Location_3","GOGL_City_3","GOGL_State_3","GOGL_Country_3");
    $validateADMsgsArray  = array("AD_StrtErr","AD_PlaceErr","AD_LocErr","AD_CityErr","AD_StateErr","AD_CntryErr");
    
    foreach ($validateADItemsArray as $key => $value) {
        if(!preg_match("/^[a-zA-Z ()-]+$/", $_REQUEST[$value]))  {   // valid delivered to address names
            echo $validateADMsgsArray[$key];
            exit;
        }
    }
    unset($addressArray);
    $addressArray = array(
        'GOGL_Country'	=> trim(htmlspecialchars($_REQUEST['GOGL_Country_3'], ENT_QUOTES)),
        'GOGL_State'	=> trim(htmlspecialchars($_REQUEST['GOGL_State_3'], ENT_QUOTES)),
        'GOGL_City'     => trim(htmlspecialchars($_REQUEST['GOGL_City_3'], ENT_QUOTES)),
        'GOGL_Location'	=> trim(htmlspecialchars($_REQUEST['GOGL_Location_3'], ENT_QUOTES)),
        'GOGL_Place'	=> trim(htmlspecialchars($_REQUEST['GOGL_Place_3'], ENT_QUOTES)),
        'GOGL_Street'	=> trim(htmlspecialchars($_REQUEST['GOGL_Street_3'], ENT_QUOTES)),
    );
    $addressIds = json_decode(saveGoglAddress($addressArray));
    
    $AttObj->Deliver_Data = array(  
        'AD_FName'      => htmlspecialchars(trim($_REQUEST['AD_FName']), ENT_QUOTES), 
        'SR_Id'         => $addressIds->SR_Id, 
        'PL_Id'         => $addressIds->PL_Id,
        'ALC_Id'        => $addressIds->ALC_Id,
        'CT_Id'         => $addressIds->CT_Id,        
        'ST_Id'         => $addressIds->ST_Id, 
//        'CN_Id'         => $addressIds->CN_Id, 
        'AD_Relation'   => htmlspecialchars(trim($_REQUEST['AD_Relation']), ENT_QUOTES),
        'AD_HouseNo' 	=> htmlspecialchars(trim($_REQUEST['AD_HouseNo']), ENT_QUOTES),        
        'AD_HouseName'  => htmlspecialchars(trim($_REQUEST['AD_HouseName']), ENT_QUOTES),
        'AD_Society' 	=> htmlspecialchars(trim($_REQUEST['AD_Society']), ENT_QUOTES),        
//        'AD_Street' 	=> htmlspecialchars(trim($_REQUEST['AD_Street']), ENT_QUOTES), 
//        'AP_Id'         => $AD_AP_Id, 
        'AD_Village' 	=> htmlspecialchars(trim($_REQUEST['AD_Village']), ENT_QUOTES), 
//        'CT_Id' 	=> htmlspecialchars(trim($AD_CT_Id), ENT_QUOTES), 
        'AD_Pincode' 	=> htmlspecialchars(trim($_REQUEST['GOGL_Pincode_3']), ENT_QUOTES), 
//        'ST_Id' 	=> $AD_ST_Id, 
        'AD_Email'      => htmlspecialchars(trim($_REQUEST['AD_Email']), ENT_QUOTES), 
        'AD_Mobile1' 	=> htmlspecialchars(trim($_REQUEST['AD_Mobile1']), ENT_QUOTES), 
        'AD_Mobile2' 	=> htmlspecialchars(trim($_REQUEST['AD_Mobile2']), ENT_QUOTES), 
        'AD_Landline' 	=> htmlspecialchars(trim($_REQUEST['AD_Landline']), ENT_QUOTES) 
    );
}
if($_REQUEST['AJ_Id'] == 0 || !$_REQUEST['AJ_Id']){
    $AJ_Id = $AttObj->newJobDetails();
    $AttObj->Submitter_Data['AJ_Id'] = $AttObj->Deliver_Data['AJ_Id'] = $AJ_Id;
    if($AttObj->Job_Data['AJ_SubmittedType'] != 1) {
        $AttObj->Data = $AttObj->Submitter_Data;
        $AttObj->insertRecords('attestation_job_submitter_details');
    }
    if($AttObj->Job_Data['AJ_DeliveredType'] != 1 && $AttObj->Job_Data['AJ_JobDeliverdTo'] == 0){
        unset($AttObj->Data);
        $AttObj->Data = $AttObj->Deliver_Data;
        $AttObj->insertRecords('attestation_job_deliver_details');
    }
    echo $AJ_Id;
}else{
    $AttObj->Submitter_Data['AJ_Id'] = $AttObj->Deliver_Data['AJ_Id'] = $_REQUEST['AJ_Id'];
    $AttObj->updateJobDetails($_REQUEST['AJ_Id']);
    if($AttObj->Job_Data['AJ_SubmittedType'] != 1) {
        $AttObj->Data = $AttObj->Submitter_Data;
        $AS_Count = $AttObj->getValue('attestation_job_submitter_details', 'COUNT(*) as count', ' WHERE AJ_Id='.$_REQUEST['AJ_Id']) ; // check whether job entry already exist in table
        if( $AS_Count > 0 ) { 
            $AttObj->updateRecords('attestation_job_submitter_details', ' AJ_Id = '.$_REQUEST['AJ_Id']);
        }else
            $AttObj->insertRecords('attestation_job_submitter_details');
    }
    if($AttObj->Job_Data['AJ_DeliveredType'] != 1 && $AttObj->Job_Data['AJ_JobDeliverdTo'] == 0){
        unset($AttObj->Data);
        $AttObj->Data = $AttObj->Deliver_Data;
        $AD_Count = $AttObj->getValue('attestation_job_deliver_details', 'COUNT(*) as count', ' WHERE AJ_Id='.$_REQUEST['AJ_Id']) ; // check whether job entry already exist in table
        if( $AD_Count > 0 ) { // already exists    
            $AttObj->updateRecords('attestation_job_deliver_details', ' AJ_Id = '.$_REQUEST['AJ_Id']);
        }else
            $AttObj->insertRecords('attestation_job_deliver_details');
    }
    echo $_REQUEST['AJ_Id'] ;
}
