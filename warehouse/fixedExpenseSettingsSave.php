<?php
require_once($BASEPATH . "preTallyClass/BusinessBonusReportClass.php");
$BSRptObj           = new BusinessBonusReportClass();
$BRS_General_Items  = ltrim($REQUEST['BRS_General_Items'],',');
//$BRS_Stat_Items   = ltrim($REQUEST['BRS_Stat_Items'],',');
$BRS_Fixed_Items    = ltrim($REQUEST['BRS_Fixed_Items'],',');
$BRS_Variable_Items = ltrim($REQUEST['BRS_Variable_Items'],',');

$BSRptObj->BRS_Data = array(        
    'OF_Id'                  => $preTally_user_ofid, 
    'BRS_Fixed_Items'        => $BRS_Fixed_Items, 
    'BRS_Variable_Items'     => $BRS_Variable_Items, 
    'BRS_General_Items'      => $BRS_General_Items,
    'BRS_CreatedBy'          => $preTally_user_id,
    'BRS_LastUpdated'        => $preTally_user_id, 
    'BRS_MDate'              => date('Y-m-d H:i:s'), 
    'BRS_CDate'              => date('Y-m-d H:i:s')
); 
echo $BSRptObj->fixedExpenseSettingsSave($preTally_user_ofid);       