<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$message = '' ;
foreach($_REQUEST as $key=>$value) {
    
    if(is_numeric($key)) { 
        
        $FDate = str_replace('/', '-', $value['APSE_FDate']);
        $LDate = str_replace('/', '-', $value['APSE_LDate']);

        $AttObj->Data = array(   
            'APSE_StatutoryNAmt' => $value['APSE_StatutoryNAmt']!= '' ? htmlspecialchars(trim($value['APSE_StatutoryNAmt']), ENT_QUOTES) : 0,
            'APSE_ExtraNAmt'     => $value['APSE_ExtraNAmt']!= '' ? htmlspecialchars(trim($value['APSE_ExtraNAmt']), ENT_QUOTES) : 0,
            'APSE_CourierNAmt'   => $value['APSE_CourierNAmt']!= '' ? htmlspecialchars(trim($value['APSE_CourierNAmt']), ENT_QUOTES) : 0, 
            'APSE_TravellingNAmt'=> $value['APSE_TravellingNAmt']!= '' ? htmlspecialchars(trim($value['APSE_TravellingNAmt']), ENT_QUOTES) : 0,
            'APSE_ManpowerNAmt'  => $value['APSE_ManpowerNAmt']!= '' ? htmlspecialchars(trim($value['APSE_ManpowerNAmt']), ENT_QUOTES) : 0, 
            'APSE_ServiceNAmt'   => $value['APSE_ServiceNAmt']!= '' ? htmlspecialchars(trim($value['APSE_ServiceNAmt']), ENT_QUOTES) : 0,
            'APSE_FDate'         => htmlspecialchars(trim(date("Y-m-d", strtotime($FDate) )),  ENT_QUOTES),
            'APSE_LDate'         => htmlspecialchars(trim(date("Y-m-d", strtotime($LDate) )),  ENT_QUOTES),
        );

        if($AttObj->updateRecords('attestation_process_sub_expense_details','APSE_Id = '.$value['APSE_Id']) == "success"){
            $message =  "Successfully Updated";
        }else{
            $message =  "Error Occured.Please Re-try";
        }
    }
}
echo $message;
?>
