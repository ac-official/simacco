<?php 
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/TrackClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
////$description        = $_REQUEST['descTxt'];
//$descriptionId      = $_REQUEST['BS_Description'];
//
////$itemName           = $_REQUEST['itemTxt'];
//$IT_Id              = $_REQUEST['IT_Id'];

$DescObj        = new DescriptionClass();
$BalSheetObj    = new BalanceSheetClass();
$ItemObj        = new ItemClass();
$UserObj        = new UserClass();
$OffObj         = new OfficeClass();
$BkupObj        = new BackupClass();

$rptPntTree = array();
$IT_Notf = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);

//$IT_Approval = $UserObj->myReportingPerson($preTally_user_id);

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
if($offAdm == $preTally_user_id) {
    $IT_Approval = $preTally_user_id ;
    $DS_Approval = 0;
    $Status      = 1;
} else {
    $DS_Approval = $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
    $Status      = 2;
}

$DescObj->DS_Data = array(  
        'US_Id'             => $preTally_user_id,
        'IT_Id'             => trim(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES)),
        'OF_Id'             => $preTally_user_ofid,
        'DS_Description'    => htmlspecialchars($_REQUEST['DS_Description'], ENT_QUOTES),
        'DS_Approval'       => $DS_Approval,
        'DS_Approved'       => $preTally_user_id,
        'DS_Notf'           => $IT_Notf,
//        'DS_Status'         => $Status,
        'DS_CDate'          => date('Y-m-d H:i:s'),
        'DS_MDate'          => date('Y-m-d H:i:s')
);

$temp = $DescObj->verifyDescription('0'); 

if($temp == 0) { 
    $DescObj->DS_Data['DS_Status'] = $Status;
    $descriptionId = $DescObj->newBalSheetDescription();  
}  else {
    $descriptionId = $temp;
}

$BalSheetObj->BL_Data = array(
        'US_Id' 	=> $preTally_user_id,
	//'IT_Id' 	=> trim(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES)),
        'LC_Id'         => $preTally_user_lcid,
	'BS_Amount'	=> trim(htmlspecialchars($_REQUEST['Amount'], ENT_QUOTES)),
        'BS_Description'=> $descriptionId,
        'TR_Id'         => 0,
	'BS_Complete' 	=> 1,
        'BS_Status' 	=> 1,
//        'BS_Date' 	=> date('Y-m-d'),
	'BS_CDate' 	=> date('Y-m-d H:i:s'),
        'BS_MDate' 	=> date('Y-m-d H:i:s')
);


$fields=str_replace("\\","",$_REQUEST['db_fields']);
$fields_decode  = htmlspecialchars_decode($fields) ;
$db_fields      = array_flip(unserialize($fields_decode) ); 
$fieldValues    = $_REQUEST;
unset($fieldValues['BS_Template']);
$patternValues  = array_intersect_key($fieldValues, ($db_fields));

$UserById                           = $_REQUEST['BS_PaidBy']!=''        ?       $_REQUEST['BS_PaidBy']      : $_REQUEST['BS_RcvdBy'];
$patternValues['ST_Id']             = $_REQUEST['ST_Id']!=''            ?       $_REQUEST['ST_Id']          : 0 ;
$patternValues['LC_Id']             = $_REQUEST['LC_Id']!=''            ?       $_REQUEST['LC_Id']          : $preTally_user_lcid ;
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
$patternValues['BS_Complete']       = 1;
$patternValues['BS_MDate']          = date('Y-m-d H:i:s');
$patternValues['PM_Id']             = $_REQUEST['PM_Id'] != 0           ?       $_REQUEST['PM_Id']    : 1 ;
$patternValues['BS_PettyCashRefId'] = $_REQUEST['BS_PettyCashRefId'] != '' ?    $_REQUEST['BS_PettyCashRefId'] : 0;
$patternValues['BS_DualEntry']      = 0;
//if($patternValues['PM_Id'] == 2) $patternValues['BS_Status'] = 2;

unset($patternValues['BS_RcvdBy']);
unset($patternValues['BS_RcvdFrm']);
unset($patternValues['LC_Id']);
unset($patternValues['BS_Description']);

$blockedDate = $BalSheetObj->bsEntryUpdateDate($preTally_user_ofid);
if($_REQUEST['BS_Date'] && ($blockedDate < $_REQUEST['BS_Date'])) {

    $BalSheetObj->BL_Data = array_merge($BalSheetObj->BL_Data, $patternValues);

    echo $BalSheetObj->newBalanceSheetItem();

    $BalSheetObj->BS_Data = array(                
        'BS_DualEntry' 	=> 2,
        'BS_MDate' 	=> date('Y-m-d H:i:s')               
    );

    $BkupObj->backupDetails('BS_Id = '.$_REQUEST['BS_Id'],$preTally_user_id, 'balance_sheets_bkup','balance_sheets');

    $BalSheetObj->updateBalanceSheet($_REQUEST['BS_Id']);
}else{
    echo 'fail';
}
           
?>