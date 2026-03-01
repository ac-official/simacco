<?php
require_once($BASEPATH . "/preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/TrackClass.php");
require_once($BASEPATH . "preTallyClass/CashBSClass.php");
require_once($BASEPATH . "preTallyClass/OldStockClass.php");

$CashBS       = new CashBSClass();
$BalSheetObj  = new BalanceSheetClass();
$TrackObj     = new TrackClass();
$OldStockObj  = new OldStockClass();

$BSId   =   trim(htmlspecialchars($_REQUEST['BS_Id'], ENT_QUOTES));
//
$CashBS->Bal_Data = array(
    'OF_Id'       => $preTally_user_ofid,
    'LC_Id'       => $preTally_user_lcid,
    'OB_OpenBal'  => trim(htmlspecialchars($_REQUEST['OB_OpenBal'], ENT_QUOTES)),
    'OB_Date'     => date('Y-m-d H:i:s'), 
    'OB_Status'   => 1
);

$OBId = $CashBS->getOpenBalId($preTally_user_lcid);

$CashBS->updateOpenBal($OBId);

$TrackObj->TR_Data = array(  
    'US_Id'             => $preTally_user_id,
    'OF_Id'             => $preTally_user_ofid,
    'TR_Track'          => trim(htmlspecialchars($_REQUEST['TR_Track'], ENT_QUOTES)),
    'TR_Status'         => 1,
    'TR_CDate'          => date('Y-m-d H:i:s'),
    'TR_MDate'          => date('Y-m-d H:i:s')
);

$TR_Id = $TrackObj->newTrack();  
    
if($TR_Id) {
    
    $OldStockObj->OS_Data = array(
//        'OF_Id'       => $preTally_user_ofid,
//        'LC_Id'       => $preTally_user_lcid,
        'TR_Id'       => $TR_Id,
        'OS_OpenBal'  => trim(htmlspecialchars($_REQUEST['OS_OpenBal'], ENT_QUOTES)),
        'OS_Date'     => date('Y-m-d H:i:s'), 
        'OS_Status'   => 1
    );

    $OSId = $OldStockObj->getOldStockId($preTally_user_lcid);

    $OldStockObj->updateOldStock($OSId);
    echo trim(htmlspecialchars($_REQUEST['TR_Track'], ENT_QUOTES));

}
else "fail";



//echo 'success';
//if($BSId != 0){
//    
//    $CashBS->OldStock_Data=array(
//        'BS_Amount' => trim(htmlspecialchars($_REQUEST['BS_Amount'], ENT_QUOTES),   
//        ''   => date('Y-m-d')  
//    );   
//
//    $CashBS->updateOldStock($BSId);
//    echo trim(htmlspecialchars($_REQUEST['TR_Track'], ENT_QUOTES);
//} else {
/*if($BSId == 0){
    
    $TrackObj->TR_Data = array(  
        'US_Id'             => $preTally_user_id,
        'OF_Id'             => $preTally_user_ofid,
        'TR_Track'          => trim(htmlspecialchars($_REQUEST['TR_Track'], ENT_QUOTES)),
        'TR_Status'         => 1,
        'TR_CDate'          => date('Y-m-d H:i:s'),
        'TR_MDate'          => date('Y-m-d H:i:s')
    );

    $TR_Id = $TrackObj->newTrack();  

    if($TR_Id) {
        $BalSheetObj->BL_Data = array(
                'US_Id'         => $preTally_user_id,
                'IT_Id'         => trim(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES)),
                'LC_Id'         => $preTally_user_lcid,
                'BS_Amount'     => trim(htmlspecialchars($_REQUEST['BS_Amount'], ENT_QUOTES)),
                'BS_Description'=> 0,
                'TR_Id'         => $TR_Id,
                'BS_Complete'   => 0,
                'BS_Status'     => 1,
                'BS_Date'       => date('Y-m-d H:i:s'),
                'BS_CDate'      => date('Y-m-d H:i:s'),
                'BS_MDate'      => date('Y-m-d H:i:s')
        );
        $BalSheetObj->newBalanceSheetItem();

        echo trim(htmlspecialchars($_REQUEST['TR_Track'], ENT_QUOTES));
    }else echo 'fail';
}else{
    echo 'success';
}*/
?>