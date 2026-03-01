<?php

require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/TrackClass.php");

$DescObj = new DescriptionClass();
$BalSheetObj = new BalanceSheetClass();
$ItemObj = new ItemClass();
$UserObj = new UserClass();
$OffObj = new OfficeClass();
$TrackObj = new TrackClass();

$BS_DualEntry = 0;
$BS_PettyCashAmt = 0;
$BS_PettyCashRefId = $_REQUEST['BS_PettyCashRefId'] ? trim(htmlspecialchars($_REQUEST['BS_PettyCashRefId'], ENT_QUOTES)) : 0;
$itmoffid       = (isset($_REQUEST['IT_Off_Id'])) ? (int)$_REQUEST['IT_Off_Id']:$preTally_user_ofid; // 28-03-25

$BS_Date = date('Ymd', strtotime(str_replace('/', '-', $_REQUEST['BS_Date'])));
if ($BS_Date < 20250401) {
    echo "track_date";
    exit;
}

$rptPntTree = array();
$IT_Notf = $ItemObj->getReportingTree($preTally_user_id, $rptPntTree);
$BSMinAmt = $BSMaxAmt = 0;

//$IT_Approval = $UserObj->myReportingPerson($preTally_user_id);

$offAdm = $OffObj->offzAdmin($itmoffid);
if ($offAdm == $preTally_user_id) {
    $IT_Approval = $preTally_user_id;
    $DS_Approval = 0;
    $Status = 1;
} else {
    $DS_Approval = $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
    $Status = 2;
}

$SH_Id = $_REQUEST['MH_Type'] == 1 ? 59 : 58;

$TR_Id = $descriptionId = 0;
$ItemObj->IT_Data = array(
    'US_Id' => $preTally_user_id,
    'OF_Id' => $itmoffid,
    'IT_Name' => trim(htmlspecialchars($_REQUEST['IT_Name'], ENT_QUOTES)),
    'SH_Id' => $SH_Id,
    'MH_Type' => htmlspecialchars($_REQUEST['MH_Type']),
    'IT_Approval' => $IT_Approval,
    'IT_Approved' => $preTally_user_id,
    'IT_Notf' => $IT_Notf,
    'IT_Status' => $Status,
    'IT_CDate' => date('Y-m-d H:i:s'),
    'IT_MDate' => date('Y-m-d H:i:s')
);

$ItemObj->myMapItem($itmoffid);
$Map_Obj = $ItemObj->ItemMapArray;

//$old = array('"', "[", "]");
//$new   = array("", "", "");
//$itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
if ($itemMap == '')
    $itemMap = '""';
$IT_temp = $ItemObj->verifyItem('0', $itemMap);
$ITStatus = $ItemObj->ItemVArray['IT_Status'];
$ITMinAmt = $ItemObj->ItemVArray['IT_MinAmount'];
$ITMaxAmt = $ItemObj->ItemVArray['IT_MaxAmount'];

if ($IT_temp == 0 || $IT_temp == '') {
    if ($ACL_Obj->ACL_BSheet == '4') {
        $IT_Id = $ItemObj->newBalSheetItem();
    } else {
        echo "item_fail";
        exit;
    }
} else {
    $IT_Id = $IT_temp;

    if ($ItemObj->ItemVArray['IT_DualEntry'] == 1) {
        $BS_DualEntry = 1;
    }
    if ($ItemObj->ItemVArray['IT_PettyCash'] == 1) {
        $BS_PettyCashAmt = trim(htmlspecialchars($_REQUEST['BS_Amount'], ENT_QUOTES));
    }

//    $ItemObj->myMapItem($preTally_user_ofid);    
//    $Map_Obj = $ItemObj->ItemMapArray;
//    
//    $itemMap = str_replace("]", '', $Map_Obj[0]->IC_Map);
//    
//    $newItmMap=$itemMap.',"'.$IT_Id.'"]'; 
//    $ItemObj->IT_Data = array(
//        'IC_Map'		=> $newItmMap,
//        'IC_MDate' 		=> date('Y-m-d H:i:s')
//    );
//    $ItemObj->mapItemCompany($preTally_user_ofid);
}
$SH_Track = $TrackObj->getSubHeadType($IT_Id);
if ($SH_Track == 1) {
    if (preg_match('/^[a-z0-9 _\-]+$/i', $_REQUEST['TR_Track'])) {
        $TrackObj->TR_Data = array(
            'US_Id' => $preTally_user_id,
            'OF_Id' => $itmoffid,
            'TR_Track' => strtoupper(htmlspecialchars(trim($_REQUEST['TR_Track']), ENT_QUOTES)),
            'TR_Status' => 1,
            'TR_CDate' => date('Y-m-d H:i:s'),
            'TR_MDate' => date('Y-m-d H:i:s')
        );

        $temp = $TrackObj->verifyTrack($itmoffid);

        if ($temp == 0) {
            $TR_Id = $TrackObj->newTrack();
        } else {
            if ($TrackObj->verifyTrackStatus($itmoffid) == 0)
                $TR_Id = $temp;
            else {
                echo "track_fail";
                exit;
            }
        }
    } else {
        echo "invalid";
        exit;
    }
}

if ($ACL_Obj->ACL_Item == 1)
    $Status = 1;

$DescObj->DS_Data = array(
    'US_Id' => $preTally_user_id,
    'IT_Id' => $IT_Id,
    'OF_Id' => $itmoffid,
    'DS_Description' => trim(htmlspecialchars($_REQUEST['DS_Description'], ENT_QUOTES)),
    'DS_Approval' => $DS_Approval,
    'DS_Approved' => $preTally_user_id,
    'DS_Notf' => $IT_Notf,
    'DS_Status' => $Status,
    'DS_CDate' => date('Y-m-d H:i:s'),
    'DS_MDate' => date('Y-m-d H:i:s')
);

$temp = $DescObj->verifyDescription('0');

$DSMinAmt = $DescObj->DSStatus['DS_MinAmount'];
$DSMaxAmt = $DescObj->DSStatus['DS_MaxAmount'];

if ($temp == 0) {
    $descriptionId = $DescObj->newBalSheetDescription();
} else {
    $descriptionId = $temp;
}

$IT_MinAmnt = $ITMinAmt ? $ITMinAmt : 0;
$IT_MaxAmnt = $ITMaxAmt ? $ITMaxAmt : 0;
$DS_MinAmnt = $DSMinAmt ? $DSMinAmt : 0;
$DS_MaxAmnt = $DSMaxAmt ? $DSMaxAmt : 0;

$BSMinAmt = $DS_MinAmnt ? $DS_MinAmnt : $IT_MinAmnt;
$BSMaxAmt = $DS_MaxAmnt ? $DS_MaxAmnt : $IT_MaxAmnt;
$bsstatus=1;
$pmode = $REQUEST["BS_PayMode"];
if ($pmode == '2') {
    $bankDetails = explode("_", $REQUEST["BS_PayBankAC"]);
    $bnkid = $bankDetails[0];
    $bbid = $bankDetails[1];
    $baid = $bankDetails[2];
    $bsstatus=2;
} else {
    $bnkid = '0';
    $bbid = '0';
    $baid = '0';
    $bsstatus=1;
}
//29-05-25 start spent to which branch  start
$BS_BranchTo  = (isset($_REQUEST['BS_BranchTo'])) ? (int)$_REQUEST['BS_BranchTo']:0;
$BS_BranchTo  = ($BS_BranchTo <= 0) ? $preTally_user_lcid: $BS_BranchTo;
// End the new branch id saving data fetching and processing 29-05-2025 
$BalSheetObj->BL_Data = array(
    'US_Id' => $preTally_user_id,
    'IT_Id' => $IT_Id,
    'LC_Id' => $preTally_user_lcid,
    'BS_MinAmount' => $BSMinAmt,
    'BS_Amount' => trim(htmlspecialchars($_REQUEST['BS_Amount'], ENT_QUOTES)),
    'BS_PettyCashAmt' => $BS_PettyCashAmt,
    'BS_MaxAmount' => $BSMaxAmt,
    'BNK_Id'=>$bnkid,
    'BB_Id'=>$bbid,
    'BA_Id'=>$baid,
    'PM_Id'=>$pmode,
    'BS_PettyCashRefId' => $BS_PettyCashRefId,
    'BS_Description' => $descriptionId,
    'TR_Id' => $TR_Id,
    'BS_Complete' => 0,
    'BS_Status' => $bsstatus,
    'BS_DualEntry' => $BS_DualEntry,
    'BS_Date' => date('Y-m-d', strtotime(str_replace('/', '-', $_REQUEST['BS_Date']))),
    'BS_CDate' => date('Y-m-d H:i:s'),
    'BS_MDate' => date('Y-m-d H:i:s'),
    'BS_BranchTo'=>$BS_BranchTo, // 29-05-2025
);
echo $BalSheetObj->newBalanceSheetItem();
?>