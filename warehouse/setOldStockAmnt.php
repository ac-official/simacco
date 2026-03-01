<?php
require_once($BASEPATH . "preTallyClass/OldStockClass.php");
require_once($BASEPATH . "preTallyClass/TrackClass.php");

$OldStockObj  = new OldStockClass();
$TrackObj     = new TrackClass();

$OSId   =   trim(htmlspecialchars($_REQUEST['OS_Id'], ENT_QUOTES));
$OS_Status   =   trim(htmlspecialchars($_REQUEST['OS_Status'], ENT_QUOTES));
$LCNM   =   trim(htmlspecialchars($_REQUEST['LC_Name'], ENT_QUOTES));
$LCId   =   trim(htmlspecialchars($_REQUEST['LC_Id'], ENT_QUOTES));

$Rid = $LCId+222;
$TR_Track = 'OS_'.strtoupper(substr($LCNM,0,3)).$Rid ;

if($OS_Status == 0){
    
     $TrackObj->TR_Data = array(  
        'US_Id'             => $preTally_user_id,
        'OF_Id'             => $preTally_user_ofid,
        'TR_Track'          => $TR_Track,
        'TR_Status'         => 1,
        'TR_CDate'          => date('Y-m-d H:i:s'),
        'TR_MDate'          => date('Y-m-d H:i:s')
    );
    $TR_Id = $TrackObj->newTrack();
    
    if($TR_Id) {
    
        $OldStockObj->OS_Data = array(
            'TR_Id'       => $TR_Id,
            'OS_OpenBal'  => trim(htmlspecialchars($_REQUEST['OS_OpenBal'], ENT_QUOTES)),
//            'OS_Date'     => date('Y-m-d H:i:s'),
            'OS_Date'     => htmlspecialchars($_REQUEST['OS_Date'], ENT_QUOTES),
            'OS_Status'   => 1
        );

        $OldStockObj->updateOldStock($OSId);
        echo $TR_Track;

    }
    else "fail";
}else{
    $OldStockObj->OS_Data = array(
        'OS_OpenBal'  => trim(htmlspecialchars($_REQUEST['OS_OpenBal'], ENT_QUOTES)),
        'OS_Date'     => htmlspecialchars($_REQUEST['OS_Date'], ENT_QUOTES), 
    );

    $OldStockObj->updateOldStock($OSId);
    echo 'success';
}

/*


$LCId   =   trim(htmlspecialchars($_REQUEST['LC_Id'], ENT_QUOTES));
$BSId   =   trim(htmlspecialchars($_REQUEST['BS_Id'], ENT_QUOTES));
$USId   =   trim(htmlspecialchars($_REQUEST['US_Id'], ENT_QUOTES));
$LCNM   =   trim(htmlspecialchars($_REQUEST['LC_Name'], ENT_QUOTES));

$USObj->viewEmpId($USId);
$EMP_Obj = $USObj->UserArray;
$US_EMP = explode('-',$EMP_Obj[0]->US_EMPID);
$ArrLen = count($US_EMP);

$Rid = $LCId+333;
$TR_Track = 'OS_'.strtoupper(substr($LCNM,0,3)).$Rid ;
        
if($BSId != 0){
    
    $OldStkAmnt->OldStock_Data=array(
        'BS_Amount' => trim(htmlspecialchars($_REQUEST['BS_Amount'], ENT_QUOTES)),   
        'BS_Date'   => trim(htmlspecialchars($_REQUEST['BS_Date'], ENT_QUOTES)),  
    );   
    
    $BkupObj->backupDetails('BS_Id = '.$BSId,$preTally_user_id, 'balance_sheets_bkup','balance_sheets');

    $OldStkAmnt->updateOldStock($BSId);
    echo 'success';
}else {
    $TrackObj->TR_Data = array(  
        'US_Id'             => trim(htmlspecialchars($_REQUEST['US_Id'], ENT_QUOTES)),
        'OF_Id'             => $preTally_user_ofid,
        'TR_Track'          => $TR_Track,
        'TR_Status'         => 1,
        'TR_CDate'          => date('Y-m-d H:i:s'),
        'TR_MDate'          => date('Y-m-d H:i:s')
    );
    $TR_Id = $TrackObj->newTrack();
    
    if($TR_Id) {
        $BalSheetObj->BL_Data = array(
                'US_Id'         => trim(htmlspecialchars($_REQUEST['US_Id'], ENT_QUOTES)),
                'IT_Id'         => trim(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES)),
                'LC_Id'         => $LCId,
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

        echo $TR_Track;

    }else echo 'fail';

}

*/


?>