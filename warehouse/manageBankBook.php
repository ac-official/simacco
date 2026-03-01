<?php
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");

$BalSheetObj = new BalanceSheetClass();
$BkupObj     = new BackupClass();

$blockedDate = $BalSheetObj->bsEntryUpdateDate($preTally_user_ofid);

if($blockedDate < $_REQUEST['BS_Date']) {

    $BalSheetObj->BS_Data=array();
    if($_REQUEST['BS_Status'] == 1){
        $BalSheetObj->BS_Data = array(
            'BS_Status'   => htmlspecialchars($_REQUEST['BS_Status'], ENT_QUOTES),
            'BS_Date'     => htmlspecialchars($_REQUEST['BS_Date'], ENT_QUOTES),
            'BS_BRemarks' => htmlspecialchars($_REQUEST['BS_BRemarks'], ENT_QUOTES),   
            'BS_MDate'    => date('Y-m-d H:i:s')
        );
        if($_REQUEST['MH_Type'] == 1) $BalSheetObj->BS_Data['BS_BReff'] =  htmlspecialchars($_REQUEST['BS_BReff'], ENT_QUOTES) ;

        $BalSheetObj->CL_Data = array(
            'CL_Id'       => htmlspecialchars($_REQUEST['CHQ_Number'], ENT_QUOTES),
            'CL_Status'   => 3
        );

    } else {
        $BalSheetObj->BS_Data = array(
            'BS_Status'   => htmlspecialchars($_REQUEST['BS_Status'], ENT_QUOTES),
            'BS_Date'     => htmlspecialchars($_REQUEST['BS_Date'], ENT_QUOTES),
            'BS_RJReason' => htmlspecialchars($_REQUEST['BS_RJReason'], ENT_QUOTES),   
            'BS_BRemarks' => htmlspecialchars($_REQUEST['BS_BRemarks'], ENT_QUOTES),   
            'BS_MDate'    => date('Y-m-d H:i:s')
        );    

        $BalSheetObj->CL_Data = array(
            'CL_Id'       => htmlspecialchars($_REQUEST['CHQ_Number'], ENT_QUOTES),
            'CL_Status'   => 4
        );
    }

    //$BalSheetObj->BS_Data = array(                
    //    'BS_Status' => htmlspecialchars($_REQUEST['BS_Status'], ENT_QUOTES),
    //    'BS_MDate'  => date('Y-m-d H:i:s')
    //);

    $BkupObj->backupDetails('BS_Id = '.$_REQUEST['BS_Id'],$preTally_user_id, 'balance_sheets_bkup','balance_sheets');

    if($_REQUEST['CHQ_Number']){
        $BalSheetObj->updateChequeStatus();
    }
    echo $BalSheetObj->updateBalanceSheet($_REQUEST['BS_Id']);
}else{
    echo 'fail';
}
?>