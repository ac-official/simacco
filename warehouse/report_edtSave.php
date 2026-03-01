<?php
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$BalSheetObj    = new BalanceSheetClass();
$DescObj        = new DescriptionClass();
$OffObj         = new OfficeClass();
$BkupObj        = new BackupClass();
$ItemObj        = new ItemClass();
$UserObj        = new UserClass();


$rptPntTree = array();
$IT_Notf   = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);

$IE_Type   = array('','Income'=>'1','Expense'=>'2');
//$MH_Values = array('','Income'=>'1','Expense'=>'2');
//print_r($_REQUEST); die("haiiiiiiiiii"); 


foreach($_REQUEST as $key=>$value) {
    
    if(is_numeric($key)) {      
        $date = str_replace('/', '-', $value['BS_Date']);
        $amount=(float)str_replace(',', '', $value['BS_Amount']) ;
        if( is_numeric($value['SH_Track']) && $value['SH_Track'] == 1 ) {        
            $BalSheetObj->BS_Data = array(                
                'BS_Date' 	=> date("Y-m-d", strtotime($date) ),
                'BS_Amount' 	=> $amount,
                'TR_Id' 	=> $value['TR_Id']      
            );
        } 
        else {
            $BalSheetObj->BS_Data = array(                
                'BS_Date' 	=> date("Y-m-d", strtotime($date) ),
                'BS_Amount' 	=>$amount               
            );

        }

        $BkupObj->backupDetails('BS_Id = '.$key,$preTally_user_id, 'balance_sheets_bkup','balance_sheets');

        $BalSheetObj->updateBalanceSheet($key);
        
        if($value['IT_PettyCash'] == 1){
            $BalSheetObj->updatePettyCashBSEntries($key);
        }
        
        if(is_numeric($value['BS_PettyCashRefId']) && $value['BS_PettyCashRefId'] != 0){
            $BalSheetObj->updatePettyCashAmount($value['BS_PettyCashRefId']);
        }
    }
}
echo 'Accounts Entry Updated Successfully';
?>