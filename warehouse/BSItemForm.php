<?php
include_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
include_once($BASEPATH . "preTallyClass/BankClass.php");
include_once($BASEPATH . "preTallyClass/TrackClass.php");
include_once($BASEPATH . "preTallyClass/BackupClass.php");
include_once($BASEPATH . "preTallyClass/DescriptionClass.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/ItemClass.php");
include_once($BASEPATH . "preTallyClass/OfficeClass.php");

$BalSheetUpdateObj = new BalanceSheetClass();
$BranchObj = new BankClass();
$TrackObj  = new TrackClass();
$BkupObj   = new BackupClass();
$DescObj   = new DescriptionClass();
$User_Obj  = new UserClass();
$IT_Obj    = new ItemClass();
$OffObj    = new OfficeClass();

$fields=str_replace("\\","",$_REQUEST['db_fields']);
$BS_Id          = $_REQUEST['BS_Id'];

$fields_decode  = htmlspecialchars_decode($fields) ;
$db_fields      = array_flip(unserialize($fields_decode) ); 
$fieldValues    = $_REQUEST;
unset($fieldValues['BS_Template']);
$patternValues  = array_intersect_key($fieldValues, ($db_fields));
//foreach ($db_fields as $value) { echo $patternValues[$value] = $fieldValues[$value]; }

$LC_Id                              = $_REQUEST['LC_Id_Old']            ?       $_REQUEST['LC_Id_Old']      : $preTally_user_lcid ;
$UserById                           = $_REQUEST['BS_PaidBy']!=''        ?       $_REQUEST['BS_PaidBy']      : $_REQUEST['BS_RcvdBy'];
$patternValues['ST_Id']             = $_REQUEST['ST_Id']!=''            ?       $_REQUEST['ST_Id']          : 0 ;
$patternValues['LC_Id']             = $_REQUEST['LC_Id']!=''            ?       $_REQUEST['LC_Id']          : $LC_Id ;
$patternValues['BNK_Id']            = $_REQUEST['BNK_Id']!=''           ?       $_REQUEST['BNK_Id']         : 0 ;
$patternValues['BA_Id']             = $_REQUEST['BA_Id']!=''            ?       $_REQUEST['BA_Id']          : 0 ;
$patternValues['BB_Id']             = $_REQUEST['BB_Id']!=''            ?       $_REQUEST['BB_Id']          : 0 ;
$patternValues['BS_PrchsdFor']      = $_REQUEST['BS_PrchsdFor']!=''     ?       $_REQUEST['BS_PrchsdFor']   : 0 ;
$patternValues['BS_User']           = $_REQUEST['BS_User']!=''          ?       $_REQUEST['BS_User']        : 0 ;
$patternValues['BS_AprovlGvnBy']    = $_REQUEST['BS_AprovlGvnBy']!=''   ?       $_REQUEST['BS_AprovlGvnBy'] : 0 ;
$patternValues['BS_AprovlTknBy']    = $_REQUEST['BS_AprovlTknBy']!=''   ?       $_REQUEST['BS_AprovlTknBy'] : 0 ;
$patternValues['BS_PrchsdNo']       = $_REQUEST['BS_PrchsdNo']!=''      ?       $_REQUEST['BS_PrchsdNo']    : 0 ;
$patternValues['BS_PayType']        = $_REQUEST['BS_PayType']!=''       ?       $_REQUEST['BS_PayType']     : 0 ;
$patternValues['BS_PaidBy']         = $UserById!=''                     ?       $UserById                   : 0 ;
$patternValues['BS_PaidTo']         = $_REQUEST['BS_PaidTo']!=''        ?       htmlspecialchars($_REQUEST['BS_PaidTo']  ,ENT_QUOTES)    : htmlspecialchars($_REQUEST['BS_RcvdFrm'], ENT_QUOTES) ;
$patternValues['BS_AprovdDate']     = $_REQUEST['BS_AprovdDate']!=''    ?       $_REQUEST['BS_AprovdDate']  : date('Y-m-d') ;
$patternValues['BS_PettyCashAmt']   = $_REQUEST['IT_PettyCash'] == 1    ?       $_REQUEST['PettyCashAmount']: 0;
$patternValues['BS_PettyCashRefId'] = $_REQUEST['BS_PettyCashRefId'] != '' ?    $_REQUEST['BS_PettyCashRefId'] : 0;

$blockedDate = $BalSheetUpdateObj->bsEntryUpdateDate($preTally_user_ofid);
if($blockedDate < $_REQUEST['BS_Date']) {

    if($_REQUEST["H_Stats"]!=1 || $_REQUEST['SH_Id']==59 || $_REQUEST['SH_Id']==58){
    $patternValues['BS_Complete']       = 0;
    }else{
        $patternValues['BS_Complete']   = 1;
    }
    $patternValues['BS_MDate']          = date('Y-m-d H:i:s');
    $patternValues['PM_Id']             = $_REQUEST['PM_Id'] != 0           ?       $_REQUEST['PM_Id']    : 1 ;
    if($_REQUEST['TR_Id']){
        $patternValues['TR_Id']         = $_REQUEST['TR_Id'] != ''          ?       $_REQUEST['TR_Id']    : 0 ;
    }

    if(!isset($REQUEST['type'])) {
        if($patternValues['PM_Id'] == 2) $patternValues['BS_Status'] = 2;
        else {                                                              // Setting Bank Details null while switching from bank to cash
           $patternValues['BNK_Id']=0 ;
           $patternValues['BB_Id']=0;
           $patternValues['BA_Id']=0;
           $patternValues['CHQ_Number']=0;
           $patternValues['BS_Status'] = 1;
           $patternValues['BS_PayType'] = 0;
        }
    }

    unset($patternValues['BS_RcvdBy']);
    unset($patternValues['BS_RcvdFrm']);

    if($_REQUEST['BS_Description'] && !is_numeric($_REQUEST['BS_Description'])) {    
        $rptPntTree = array();
        $IT_Notf = $IT_Obj->getReportingTree($preTally_user_id,$rptPntTree);
        $offAdm = $OffObj->offzAdmin($preTally_user_ofid);

        if($offAdm == $preTally_user_id) {
            $IT_Approval = $preTally_user_id ;
            $DS_Approval = 0;
            $Status      = 1;
        }else if( $ACL_Obj->ACL_Item == 1 ){
            $DS_Approval = $IT_Approval = $User_Obj->myReportingPerson($preTally_user_id);
            $Status = 1;
        } else {
            $DS_Approval = $IT_Approval = $User_Obj->myReportingPerson($preTally_user_id);
            $Status      = 2;
        }


        $DescObj->DS_Data = array(   
            'US_Id'          => $preTally_user_id, 
            'IT_Id'          => htmlspecialchars(trim($_REQUEST['IT_Id']), ENT_QUOTES), 
            'OF_Id'          => $preTally_user_ofid,
            'DS_Description' => htmlspecialchars(trim($_REQUEST['BS_Description']), ENT_QUOTES),
            'DS_Approval'    => $DS_Approval,
            'DS_Approved'    => $preTally_user_id,
            'DS_Notf'        => $IT_Notf,
            'DS_Status'      => $Status,
            'DS_CDate'       => date('Y-m-d H:i:s'),
            'DS_MDate'       => date('Y-m-d H:i:s')
        );   

        //$patternValues['BS_Description'] = $DescObj->verifyDescription($DescObj->DS_Data['DS_Description']);
        if(!$DescObj->verifyDescription('0')) {
            $patternValues['BS_Description'] = $DescObj->newBalSheetDescription();
        }
    }
    $BalSheetUpdateObj->BL_Update_Data=array();
    $BalSheetUpdateObj->BL_Update_Data = $patternValues;
    // other branch id saved into balance sheet table 2-06-25 
    if (isset($_REQUEST['BS_BranchTo']) && $_REQUEST['BS_BranchTo'] > 0) {
       $BalSheetUpdateObj->BL_Update_Data['BS_BranchTo'] = $_REQUEST['BS_BranchTo'];
    }
    // end

    if($_REQUEST['CHQ_Number']) {
        if($_REQUEST['updateType'] == 'rpt'){
            $CHtemp = $BranchObj->verifyBSChQ($_REQUEST['CHQ_Number'],$_REQUEST['BA_Id'],$BS_Id); 
        }else{
            $CHtemp = $BranchObj->verifyChequeNumber($_REQUEST['CHQ_Number'],$_REQUEST['BA_Id']); 
        }
    }else { $CHtemp = 1; }
    if($_REQUEST['TR_Id']) {
        $TrackObj->TR_Data = array(  
            'TR_Track'          => trim(htmlspecialchars($REQUEST['TR_Track'], ENT_QUOTES)),
        );
        $TRtemp = $TrackObj->verifyTrack($preTally_user_ofid); 
    }else { $TRtemp = 1; }

    $PCTemp = 1;
    if($_REQUEST['IT_PettyCash'] == 1){

        $PCRefTotal = $BalSheetUpdateObj->verifyPettyCash(htmlspecialchars($_REQUEST['BS_Amount'], ENT_QUOTES),$BS_Id);
        if($PCRefTotal > $_REQUEST['BS_Amount']){
            $PCTemp = 0;
        }else{
    //        $PCTemp = 1;
            $BalSheetUpdateObj->BL_Update_Data['BS_PettyCashAmt']   = $_REQUEST['BS_Amount'] - $PCRefTotal ;
    //        $BalSheetUpdateObj->BL_Update_Data['BS_Amount']         = $_REQUEST['BS_Amount'] ;
        }
    }

    if($_REQUEST['BS_ExpenseControl'] == 1){
        $BalSheetUpdateObj->BL_Update_Data['BS_MinAmount'] = $_REQUEST['BS_MinAmount']  ?  $_REQUEST['BS_MinAmount'] : 0;
        $BalSheetUpdateObj->BL_Update_Data['BS_MaxAmount'] = $_REQUEST['BS_MaxAmount']  ?  $_REQUEST['BS_MaxAmount'] : 0;
    }

    if($CHtemp == 1 && $TRtemp != 0 && $PCTemp == 1){

        if($_REQUEST['EditDS'] == 1)
            $BalSheetUpdateObj->updateDescExpCntl(htmlspecialchars($patternValues['BS_Description'], ENT_QUOTES));

        if($_REQUEST['FDate'] || $_REQUEST['TDate']){

            $stDate= date("Y-m-d", strtotime($_REQUEST['FDate']));
            $enDate= date("Y-m-d", strtotime($_REQUEST['TDate']));

            if($_REQUEST['FDate']!='' && $_REQUEST['TDate']!=''){
                $dateFilt=" AND BS_Date   between '".$stDate."' AND '".$enDate."'";
            }elseif($_REQUEST['FDate']=='' && $_REQUEST['TDate']!=''){
                $dateFilt=" AND BS_Date   <=  '".$enDate."'";
            }elseif($_REQUEST['FDate']!='' && $_REQUEST['TDate']==''){
                $dateFilt=" AND BS_Date   >=  '".$stDate."'";
            }

            $BkupObj->backupDetails('IT_Id = '.$_REQUEST['IT_Id'] .' AND BS.BS_Description = '.$patternValues['BS_Description'] .' AND BS.BS_Date between '.$stDate.' AND '.$enDate.' ',$preTally_user_id,'balance_sheets_bkup','balance_sheets');

            $BalSheetUpdateObj->updateExpCntl('IT_Id = '.$_REQUEST['IT_Id'] .' AND BS_Description =  '.$patternValues['BS_Description'] .' '.$dateFilt.' ');  

            unset($BalSheetUpdateObj->BL_Update_Data['BS_MinAmount']);
            unset($BalSheetUpdateObj->BL_Update_Data['BS_MaxAmount']);
        }

        $BkupObj->backupDetails('BS_Id = '.$BS_Id,$preTally_user_id,'balance_sheets_bkup','balance_sheets');

        echo $BalSheetUpdateObj->updateBalSheet(htmlspecialchars($BS_Id, ENT_QUOTES));  

        if( ($_REQUEST['BS_PettyCashRefId'] != $_REQUEST['PCRefId_Old']) && ($_REQUEST['PCRefId_Old'] != 0) ){
            $BkupObj->backupDetails('BS_Id = '.$_REQUEST['PCRefId_Old'],$preTally_user_id,'balance_sheets_bkup','balance_sheets');
            $BalSheetUpdateObj->updatePettyCashAmount(htmlspecialchars($_REQUEST['PCRefId_Old'], ENT_QUOTES));
        }
    }else if($PCTemp == 0){echo 'pettycash';}else{ echo '1'; } 
}else{
    echo '2';
}
















/*
if($CHtemp == 1 && $TRtemp != 0)
{

    $BkupObj->backupDetails('BS_Id = '.$BS_Id,$preTally_user_id,'balance_sheets_bkup','balance_sheets');

    echo $BalSheetUpdateObj->updateBalSheet(htmlspecialchars($BS_Id, ENT_QUOTES));  
    
    if( ($_REQUEST['BS_PettyCashRefId'] != $_REQUEST['PCRefId_Old']) && ($_REQUEST['PCRefId_Old'] != 0) ){
        $BkupObj->backupDetails('BS_Id = '.$_REQUEST['PCRefId_Old'],$preTally_user_id,'balance_sheets_bkup','balance_sheets');
        $BalSheetUpdateObj->updatePettyCashAmount(htmlspecialchars($_REQUEST['PCRefId_Old'], ENT_QUOTES));
    }
}else{ echo '1'; } 
*/







/*
if($_REQUEST['IT_PettyCash'] == 1){
    
    $PCRefTotal = $BalSheetUpdateObj->verifyPettyCash(htmlspecialchars($_REQUEST['BS_Amount'], ENT_QUOTES),$BS_Id);
    if($PCRefTotal > $_REQUEST['BS_Amount']){
        $PCTemp = 0;
    }else{
        $PCTemp = 1;
        $BalSheetUpdateObj->BL_Update_Data['BS_Amount']       = $_REQUEST['BS_Amount'] - $PCRefTotal ;
        $BalSheetUpdateObj->BL_Update_Data['BS_PettyCashAmt'] = $_REQUEST['BS_Amount'] ;
    }
}

if($CHtemp == 1 && $TRtemp != 0 && $PCTemp == 1){

    $BkupObj->backupDetails('BS_Id = '.$BS_Id,$preTally_user_id,'balance_sheets_bkup','balance_sheets');

    echo $BalSheetUpdateObj->updateBalSheet(htmlspecialchars($BS_Id, ENT_QUOTES));  
    
    if( ($_REQUEST['BS_PettyCashRefId'] != $_REQUEST['PCRefId_Old']) && ($_REQUEST['PCRefId_Old'] != 0) ){
        $BkupObj->backupDetails('BS_Id = '.$_REQUEST['PCRefId_Old'],$preTally_user_id,'balance_sheets_bkup','balance_sheets');
        $BalSheetUpdateObj->updatePettyCashAmount(htmlspecialchars($_REQUEST['PCRefId_Old'], ENT_QUOTES));
    }
}else if($PCTemp == 0){echo 'pettycash';}else{ echo '1'; } 
 * 
 */
?>