<?php
require_once($BASEPATH . "preTallyClass/IncomeProfitReportClass.php");
$BSRptObj           = new IncomeProfitReportClass();
$PRS_General_Items  = ltrim($REQUEST['PRS_General_Items'],',');
$PRS_Stat_Items     = ltrim($REQUEST['PRS_Stat_Items'],',');
$PRS_Fixed_Items    = ltrim($REQUEST['PRS_Fixed_Items'],',');
$PRS_Variable_Items = ltrim($REQUEST['PRS_Variable_Items'],',');

$BSRptObj->PRS_Data = array(        
    'OF_Id'                  => $preTally_user_ofid, 
    'PRS_Stat_Items'         => $PRS_Stat_Items,
    'PRS_Fixed_Items'        => $PRS_Fixed_Items, 
    'PRS_Variable_Items'     => $PRS_Variable_Items, 
    'PRS_General_Items'      => $PRS_General_Items,
    'PRS_CreatedBy'          => $preTally_user_id,
    'PRS_LastUpdated'        => $preTally_user_id, 
    'PRS_MDate'              => date('Y-m-d H:i:s'), 
    'PRS_CDate'              => date('Y-m-d H:i:s')
); 
echo $BSRptObj->fixedExpenseSettingsSave($preTally_user_ofid);       