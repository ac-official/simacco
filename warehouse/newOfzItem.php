<?php
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");

$ItemObj = new ItemClass();
$UserObj = new UserClass();
$OffObj  = new OfficeClass();
$BkupObj = new BackupClass();

//die($_REQUEST['IT_Id']);
$newItemFlag = 0;
$SHIdPending = $_REQUEST['MH_Type'] == 1 ? 59 : 58;
$SH_Id = $_REQUEST['SH_Id']!=''  ? $_REQUEST['SH_Id'] : $SHIdPending ;
$IT_Business = $_REQUEST['IT_Business']!=''  ? $_REQUEST['IT_Business'] : 0 ;
$IT_Transfers = $_REQUEST['IT_Transfers']!=''  ? $_REQUEST['IT_Transfers'] : 0 ;
$IT_PettyCash = $_REQUEST['IT_PettyCash']!=''  ? $_REQUEST['IT_PettyCash'] : 0 ;

$rptPntTree = array();
$IT_Notf = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);

$IT_DualItem      = $_REQUEST['IT_DualItem']!=''    ?       $_REQUEST['IT_DualItem']    : 0 ;
$IT_MinAmnt       = $_REQUEST['IT_MinAmount']       ?       $_REQUEST['IT_MinAmount']   : 0 ;
$IT_MaxAmnt       = $_REQUEST['IT_MaxAmount']       ?       $_REQUEST['IT_MaxAmount']   : 0 ;

$ItemObj->IT_Data = array(
//        'US_Id' 	=> $preTally_user_id,
        'OF_Id'         => $preTally_user_ofid,
	'IT_Name' 	=> trim(htmlspecialchars($_REQUEST['IT_Name'], ENT_QUOTES)),
        'SH_Id' 	=> $SH_Id,
        'MH_Type' 	=> htmlspecialchars($_REQUEST['MH_Type'], ENT_QUOTES),
	'IT_Comments'	=> htmlspecialchars($_REQUEST['IT_Comments'], ENT_QUOTES),
        'IT_Approved'   => $preTally_user_id,
        'IT_Business'   => htmlspecialchars($IT_Business, ENT_QUOTES),
        'IT_Transfers'  => htmlspecialchars($IT_Transfers, ENT_QUOTES),
        'IT_PettyCash'  => htmlspecialchars($IT_PettyCash, ENT_QUOTES),
        'IT_DualEntry'  => htmlspecialchars($_REQUEST['IT_DualEntry'], ENT_QUOTES),
        'IT_DualItem'   => $IT_DualItem,
        'IT_MinAmount'  => htmlspecialchars($_REQUEST['IT_MinAmount'], ENT_QUOTES) ? htmlspecialchars($_REQUEST['IT_MinAmount'], ENT_QUOTES) : htmlspecialchars($IT_MaxAmnt, ENT_QUOTES),
        'IT_MaxAmount'  => htmlspecialchars($_REQUEST['IT_MaxAmount'], ENT_QUOTES) ? htmlspecialchars($_REQUEST['IT_MaxAmount'], ENT_QUOTES) : htmlspecialchars($IT_MinAmnt, ENT_QUOTES),
        'IT_Notf'	=> $IT_Notf,
	'IT_MDate' 	=> date('Y-m-d H:i:s'),
    'IT_OtherUser'  => htmlspecialchars($_REQUEST['IT_OtherUser'], ENT_QUOTES), //28-05-2025
);

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
if($offAdm == $preTally_user_id) {
    $ItemObj->IT_Data["IT_Approval"]           = 0 ;
    $ItemObj->IT_Data["IT_Status"]             = 1;
} else {
    $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
    $ItemObj->IT_Data["IT_Approval"]           = $IT_Approval;
    $ItemObj->IT_Data["IT_Status"]             = 3;
}

$ItemObj->myMapItem($preTally_user_ofid);    
$Map_Obj = $ItemObj->ItemMapArray;

//$old  = array('"', "[", "]");
//$new  = array("", "", "");
//$itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
if($itemMap == '') $itemMap = '""';
$temp=$ItemObj->verifyItem(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES),$itemMap);

$ITStatus = $ItemObj->ITStatus;
if(!($IT_Business==1 && $IT_Transfers==1))
{
    if( $temp == 0 || $temp =='') { 
        if( is_numeric($_REQUEST['SH_Id']) || $_REQUEST['SH_Id'] == '') {
            if(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES) == 0) {
                $ItemObj->IT_Data["US_Id"] = $preTally_user_id; 
                $ItemObj->IT_Data["IT_CDate"] = date('Y-m-d H:i:s'); 
                echo $ITId = $ItemObj->newCompanyItem();
            } else {
                $ItemObj->IT_Data["IT_Status"] = htmlspecialchars($_REQUEST['IT_Status'], ENT_QUOTES); 
                
                $BkupObj->backupDetails('IT_Id = '.$_REQUEST['IT_Id'],$preTally_user_id,'items_bkup','items');
                
                echo $ItemObj->updateItem(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES));
            }
        } else echo "Invalid";
    } else { 
        if($ITStatus == 2) { echo "Item Already Exists. Waiting for Admin Approval."; } 
        else { echo 'fail'; }
    }
} else {
    echo "flag";    
}
?>