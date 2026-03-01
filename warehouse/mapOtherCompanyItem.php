<?php
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$ItemObj        = new ItemClass();

$rptPntTree  = array();
$IT_Notf     = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);

foreach($_REQUEST as $key=>$value) {
    
    if(is_numeric($key)) {
        
        $ItemObj->IT_Data = array(
            'US_Id'         => $preTally_user_id,
            'OF_Id'         => $preTally_user_ofid,
            'OF_Id_Alias'   => trim(htmlspecialchars($value['OF_Id_Alias'], ENT_QUOTES)),
            'IT_Name'       => trim(htmlspecialchars($value['IT_Name'], ENT_QUOTES)),
            'SH_Id'         => trim(htmlspecialchars($value['SH_Id'], ENT_QUOTES)),
            'MH_Type'       => trim(htmlspecialchars($value['MH_Type'], ENT_QUOTES)),
            'IT_Approval'   => 0,
            'IT_Approved'   => $preTally_user_id,
            'IT_Notf'       => $IT_Notf,
            'IT_CDate'      => date('Y-m-d H:i:s'),
            'IT_MDate'      => date('Y-m-d H:i:s'),
            'IT_Status'     => 1
        );
        
//        $old = array('"', "[", "]");
//        $new   = array("", "", "");
//        $itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
        if($itemMap == '') $itemMap = '""';
        $IT_temp  = $ItemObj->verifyItem('0',$itemMap); 
        
        if($IT_temp == 0 || $IT_temp == '') {  
            $Message = $ItemObj->mapOtherCompanyItem($value['IT_Id']);  
        }
    }
}
if($Message) echo $Message;
else echo "Item Already Exist in Pretally. Re-Try.";
/*
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$ItemObj        = new ItemClass();

foreach($_REQUEST as $key=>$value) {
    
    echo $ITObj->mapItemPreTally($value['IT_Id'],$preTally_user_id);
}*/
?>