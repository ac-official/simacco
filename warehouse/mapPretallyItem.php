<?php
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$OffObj         = new OfficeClass();
$ItemObj        = new ItemClass();
$UserObj        = new UserClass();

$rptPntTree  = array();
$IT_Notf     = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);
$IT_Approval =  $UserObj->myReportingPerson($preTally_user_id);

foreach($_REQUEST as $key=>$value) {
    
    if(is_numeric($key)) {
        
        $ItemObj->IT_Data = array(
            'OF_Id'         => $preTally_user_ofid,
            'US_Id'         => $preTally_user_id,
            'IT_Name'       => trim(htmlspecialchars($value['IT_Name'], ENT_QUOTES)),
            'SH_Id'         => trim(htmlspecialchars($value['SH_Id'], ENT_QUOTES)),
            'MH_Type'       => trim(htmlspecialchars($value['MH_Type'], ENT_QUOTES)),
            'IT_Approved'   => $preTally_user_id,
            'IT_Notf'       => $IT_Notf,
            'IT_CDate'      => date('Y-m-d H:i:s'),
            'IT_MDate'      => date('Y-m-d H:i:s')
        );
        
        $offAdm = $OffObj->offzAdmin($preTally_user_ofid);
        if($offAdm == $preTally_user_id) {
            $ItemObj->IT_Data["IT_Approval"]    = 0 ;
            $ItemObj->IT_Data["IT_Status"]      = 1;
        } else {
            $ItemObj->IT_Data["IT_Approval"]     = $IT_Approval;
            $ItemObj->IT_Data["IT_Status"]       = 3;
        }
        
        $old = array('"', "[", "]");
        $new   = array("", "", "");
        $itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
        if($itemMap == '') $itemMap = '""';
        $IT_temp  = $ItemObj->verifyItem('0',$itemMap); 
        
        if($IT_temp == 0 || $IT_temp == '') {  
            $ItemObj->newBalSheetItem();  
        }
    }
}

$ItemObj->IT_Data = array(
    'IC_Map'		=> $REQUEST['c'],
    'OF_Id'             => $preTally_user_ofid,
    'IC_MDate' 		=> date('Y-m-d H:i:s'),
    'IC_Status' 	=> 1
);

$temp = $ItemObj->verifyMapItem(trim(htmlspecialchars($preTally_user_ofid, ENT_QUOTES)));
if($temp == 1 ) {
    $ItemObj->IT_Data["IC_CDate"] = date('Y-m-d H:i:s'); 
    echo $ItemObj->newMapItemCompany();
} else {
    echo $ItemObj->mapItemCompany($preTally_user_ofid);
}  

?>