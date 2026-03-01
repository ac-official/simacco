<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
include_once($BASEPATH . 'includes/saveGoogleAddress.php' );
$AttObj = new AttestationClass();

$addressArray = array(
    'GOGL_Country'	=> trim(htmlspecialchars($_REQUEST['GOGL_Country'], ENT_QUOTES)),
    'GOGL_State'	=> trim(htmlspecialchars($_REQUEST['GOGL_State'], ENT_QUOTES)),
    'GOGL_City'         => trim(htmlspecialchars($_REQUEST['GOGL_City'], ENT_QUOTES)),
    'GOGL_Location'	=> trim(htmlspecialchars($_REQUEST['GOGL_Location'], ENT_QUOTES)),
    'GOGL_Place'	=> trim(htmlspecialchars($_REQUEST['GOGL_Place'], ENT_QUOTES)),
    'GOGL_Street'	=> trim(htmlspecialchars($_REQUEST['GOGL_Street'], ENT_QUOTES)),
);
$addressIds    = json_decode(saveGoglAddress($addressArray));

$APM_Title     = htmlspecialchars(trim($_REQUEST['APM_Title']), ENT_QUOTES);
$travelCNName  = htmlspecialchars(trim($_REQUEST['CN_Name_Travelling']), ENT_QUOTES);
//if(!is_numeric($APM_Title)){
//    $APM_Id = $AttObj->getValue('attestation_process_main','APM_Id','WHERE APM_Title = "'.$APM_Title.'"');
//    if(!$APM_Id){
//        $date = date('Y-m-d H:i:s');
//        $AttObj->InsertData = array(   
//        'APM_Title'               => $APM_Title, 
//        'APM_Description'         => '', 
//        'APM_CDate'               => $date,
//        'APM_MDate'               => $date,
//        'APM_Status'              => '1'
//        );
//        $APM_Id=$AttObj->insertReturnId('attestation_process_main');
//    }
//}else {
//    $APM_Id = $APM_Title;
//}

if($travelCNName) {
    $CN_Id_Travelling = $AttObj->getValue('countries','CN_Id','WHERE CN_Name = "'.$travelCNName.'"');
    if(!$CN_Id_Travelling){
        echo "country";        
        exit();
    }
}else $CN_Id_Travelling = 0;

$APM_Id = $AttObj->getValue('attestation_process_main','APM_Id','WHERE APM_Title = "'.$APM_Title.'" AND OF_Id = "'.$preTally_user_ofid.'"');
if(!$APM_Id){
    echo "invalid";
}else{
    $AttObj->Data = array(   
        'APM_Id'            => $APM_Id,
        'OF_Id'             => $preTally_user_ofid,
        'APS_Title'         => htmlspecialchars(trim($_REQUEST['APS_Title']), ENT_QUOTES), 
        'APS_Description'   => htmlspecialchars(trim($_REQUEST['APS_Description']), ENT_QUOTES),
        'ASD_Id'            => htmlspecialchars(trim($_REQUEST['ASDIds']), ENT_QUOTES),
        'SR_Id'             => $addressIds->SR_Id, 
        'PL_Id'             => $addressIds->PL_Id,
        'ALC_Id'            => $addressIds->ALC_Id,
        'CT_Id'             => $addressIds->CT_Id,        
        'ST_Id'             => $addressIds->ST_Id, 
        'CN_Id'             => $addressIds->CN_Id, 
        'CN_Id_Travelling'  => $CN_Id_Travelling,
        'APS_Pincode'       => htmlspecialchars(trim($_REQUEST['GOGL_Pincode']), ENT_QUOTES),
        'APS_StatutoryNAmt' => htmlspecialchars(trim($_REQUEST['APS_StatutoryNAmt']), ENT_QUOTES),
        'APS_StatutoryUAmt' => htmlspecialchars(trim($_REQUEST['APS_StatutoryUAmt']), ENT_QUOTES),
        'APS_ExtraNAmt'     => htmlspecialchars(trim($_REQUEST['APS_ExtraNAmt']), ENT_QUOTES),
        'APS_ExtraUAmt'     => htmlspecialchars(trim($_REQUEST['APS_ExtraUAmt']), ENT_QUOTES),
        'APS_CourierNAmt'   => htmlspecialchars(trim($_REQUEST['APS_CourierNAmt']), ENT_QUOTES), 
        'APS_CourierUAmt'   => htmlspecialchars(trim($_REQUEST['APS_CourierUAmt']), ENT_QUOTES),
        'APS_TravellingNAmt'=> htmlspecialchars(trim($_REQUEST['APS_TravellingNAmt']), ENT_QUOTES),
        'APS_TravellingUAmt'=> htmlspecialchars(trim($_REQUEST['APS_TravellingUAmt']), ENT_QUOTES),
        'APS_ManpowerNAmt'  => htmlspecialchars(trim($_REQUEST['APS_ManpowerNAmt']), ENT_QUOTES), 
        'APS_ManpowerUAmt'  => htmlspecialchars(trim($_REQUEST['APS_ManpowerUAmt']), ENT_QUOTES),
        'APS_ServiceNAmt'   => htmlspecialchars(trim($_REQUEST['APS_ServiceNAmt']), ENT_QUOTES),
        'APS_ServiceUAmt'   => htmlspecialchars(trim($_REQUEST['APS_ServiceUAmt']), ENT_QUOTES),
        'APS_FromYear'      => htmlspecialchars(trim($_REQUEST['APS_FromYear']), ENT_QUOTES),
        'APS_ToYear'        => htmlspecialchars(trim($_REQUEST['APS_ToYear']), ENT_QUOTES),
        'APS_CDate'         => date('Y-m-d H:i:s'),
        'APS_MDate'         => date('Y-m-d H:i:s'),
        'APS_Status'        => htmlspecialchars(trim($_REQUEST['APS_Status']), ENT_QUOTES)

    );
    
    $AttObj->ExpenseData = array(   
        'APSE_StatutoryNAmt' => htmlspecialchars(trim($_REQUEST['APS_StatutoryNAmt']), ENT_QUOTES),
        'APSE_StatutoryUAmt' => htmlspecialchars(trim($_REQUEST['APS_StatutoryUAmt']), ENT_QUOTES),
        'APSE_ExtraNAmt'     => htmlspecialchars(trim($_REQUEST['APS_ExtraNAmt']), ENT_QUOTES),
        'APSE_ExtraUAmt'     => htmlspecialchars(trim($_REQUEST['APS_ExtraUAmt']), ENT_QUOTES),
        'APSE_CourierNAmt'   => htmlspecialchars(trim($_REQUEST['APS_CourierNAmt']), ENT_QUOTES), 
        'APSE_CourierUAmt'   => htmlspecialchars(trim($_REQUEST['APS_CourierUAmt']), ENT_QUOTES),
        'APSE_TravellingNAmt'=> htmlspecialchars(trim($_REQUEST['APS_TravellingNAmt']), ENT_QUOTES),
        'APSE_TravellingUAmt'=> htmlspecialchars(trim($_REQUEST['APS_TravellingUAmt']), ENT_QUOTES),
        'APSE_ManpowerNAmt'  => htmlspecialchars(trim($_REQUEST['APS_ManpowerNAmt']), ENT_QUOTES), 
        'APSE_ManpowerUAmt'  => htmlspecialchars(trim($_REQUEST['APS_ManpowerUAmt']), ENT_QUOTES),
        'APSE_ServiceNAmt'   => htmlspecialchars(trim($_REQUEST['APS_ServiceNAmt']), ENT_QUOTES),
        'APSE_ServiceUAmt'   => htmlspecialchars(trim($_REQUEST['APS_ServiceUAmt']), ENT_QUOTES),
//        'APSE_FDate'         => htmlspecialchars(trim($_REQUEST['APSE_FDate']), ENT_QUOTES),
//        'APSE_LDate'         => htmlspecialchars(trim($_REQUEST['APSE_LDate']), ENT_QUOTES),//htmlspecialchars(trim(date('Y-m-d', strtotime('+5 years', strtotime($_REQUEST['APSE_FDate'])))), ENT_QUOTES),
        'APSE_CDate'         => date('Y-m-d H:i:s'),
    );
    
    if($_REQUEST['APS_Id'] != 0) { // update
        if($AttObj->getValue('attestation_process_sub', 'APS_Id', ' WHERE APS_Title = "'.$_REQUEST['APS_Title'].'" AND APM_Id = "'.$AttObj->Data['APM_Id'].'" AND APS_Id != "'.$_REQUEST['APS_Id'].'" AND OF_Id = "'.$preTally_user_ofid.'"' ) ) {
            echo "exists"; 
        } else {
            if( ($_REQUEST['APSE_FDate'] && !$_REQUEST['APSE_LDate'] ) || (!$_REQUEST['APSE_FDate'] && $_REQUEST['APSE_LDate']) ){
                echo "invaliddate";
            }else {
                if($AttObj->updateRecords('attestation_process_sub','APS_Id = '.$_REQUEST['APS_Id']) == "success"){
    //                echo "Sub Process successfully updated";
                    $AttObj->ExpenseData['APS_Id']     = $_REQUEST['APS_Id'] ;
                    $AttObj->Data = $AttObj->ExpenseData;

                    if($_REQUEST['APSE_FDate'] && $_REQUEST['APSE_LDate']) {

                        $AttObj->Data['APSE_FDate'] = htmlspecialchars(trim($_REQUEST['APSE_FDate']), ENT_QUOTES);
                        $AttObj->Data['APSE_LDate'] = htmlspecialchars(trim($_REQUEST['APSE_LDate']), ENT_QUOTES);

                        if($AttObj->getValue('attestation_process_sub_expense_details', 'APS_Id', 
                                ' WHERE (
                                            ( ("'.$_REQUEST['APSE_FDate'].'" BETWEEN APSE_FDate AND APSE_LDate ) || ( "'.$_REQUEST['APSE_LDate'].'" BETWEEN APSE_FDate AND APSE_LDate  ) ) 
                                            OR (("'.$_REQUEST['APSE_FDate'].'" <= APSE_FDate ) AND ( "'.$_REQUEST['APSE_LDate'].'" >= APSE_LDate  ))
                                        ) AND  APS_Id = "'.$_REQUEST['APS_Id'].'"') ) {
                            echo "date"; 
                        } else {
                            if($AttObj->insertRecords('attestation_process_sub_expense_details') == "success")
                                echo "Sub Process successfully updated";
                            else
                                echo "fail";
                        }
                    }else{
                        $APSEID = $AttObj->getValue('attestation_process_sub_expense_details', 'APSE_Id','WHERE APS_Id = "'.$_REQUEST['APS_Id'].'" ORDER BY APSE_CDate DESC LIMIT 1');
                        if($AttObj->updateRecords('attestation_process_sub_expense_details','APSE_Id = '.$APSEID) == "success"){
                            echo "Sub Process successfully updated";
                        } else
                            echo "fail";
                    }
                }
                else
                    echo "fail";
            }
        }
    } else {  // insert
        if($AttObj->getValue('attestation_process_sub', 'APS_Id', ' WHERE APS_Title = "'.$_REQUEST['APS_Title'].'" AND APM_Id = "'.$AttObj->Data['APM_Id'].'" AND OF_Id = "'.$preTally_user_ofid.'"' ) ) {
            echo "exists"; 
        } else {
            if($_REQUEST['APSE_FDate'] && $_REQUEST['APSE_LDate']){
                $AttObj->InsertData  =  $AttObj->Data ;
                $APSId = $AttObj->insertReturnId('attestation_process_sub') ;
                if($APSId > 0 ) {
                    $AttObj->ExpenseData['APS_Id']     = $APSId ;
                    $AttObj->ExpenseData['APSE_FDate'] = htmlspecialchars(trim($_REQUEST['APSE_FDate']), ENT_QUOTES);
                    $AttObj->ExpenseData['APSE_LDate'] = htmlspecialchars(trim($_REQUEST['APSE_LDate']), ENT_QUOTES);
                    $AttObj->Data = $AttObj->ExpenseData;
                    if($AttObj->insertRecords('attestation_process_sub_expense_details') == "success")
                        echo "Sub Process successfully created";
                    else
                        echo "invaliddate";
                }else{
                    echo 'fail';
                }
            }else{
                echo "invaliddate";
            }
        }
    }
}
