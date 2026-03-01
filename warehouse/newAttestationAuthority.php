<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$state = htmlspecialchars(trim($_REQUEST['ST_Id']), ENT_QUOTES);
if(is_numeric($_REQUEST['ST_Id'])) {
    $AST_Id = $state;
    $AttObj->Data = array(
        'AST_CS' => $_REQUEST['AST_CS']
    );
    $AttObj->updateRecords('attestation_state', 'AST_Id = '.$AST_Id);
} else {
    $AST_Id = $AttObj->getValue('attestation_state', 'AST_Id', " WHERE AST_State = '".$state."' AND AST_CS = '".$_REQUEST['AST_CS']."' AND AST_Status = 1");
    if(!$AST_Id) {
        $AttObj->InsertData = array(
            'AST_State'  =>  $state,
            'AST_CS'     =>  $_REQUEST['AST_CS'],
            'AST_Status' =>  1
        );
        $AST_Id = $AttObj->insertReturnId('attestation_state');
    }
}

$AttObj->Data = array(        
    'AAUTH_Authority'   => htmlspecialchars(trim($_REQUEST['AAUTH_Authority']), ENT_QUOTES),  
    'AST_Id'            => $AST_Id, 
    'AAUTH_Status'      => htmlspecialchars(trim($_REQUEST['AAUTH_Status']), ENT_QUOTES)
);

$AUTH_ID = $AttObj->getValue('attestation_authorities', 'AAUTH_Id', ' WHERE AAUTH_Authority = "'.$_REQUEST['AAUTH_Authority'].'" AND AST_Id = '.$AST_Id.' AND AAUTH_Id != '.$_REQUEST['AAUTH_Id']);

if(!$AUTH_ID){
    if(htmlspecialchars($_REQUEST['AAUTH_Id'], ENT_QUOTES) == 0) {
        if($AttObj->insertRecords('attestation_authorities') == "success")
            echo "Authority successfully created";
        else
            echo "fail";
    }else{
        if($AttObj->updateRecords('attestation_authorities','AAUTH_Id = '.$_REQUEST['AAUTH_Id']) == "success")
            echo "Authority successfully updated";
        else
            echo "fail";
    }
}else {
    echo "exists";
}
