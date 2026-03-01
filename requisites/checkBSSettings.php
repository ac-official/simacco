<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$balShtSettId  =  "1,2,3,4,5,6";
 $balShtSettId = str_replace(',', '', $balShtSettId);
 $balShtSettCount=mb_strlen( $balShtSettId );
$count = $AttObj->getValue('attestation_job_balsheet_settings', 'COUNT(AJBS_Type)', 'WHERE OF_Id= "'.$preTally_user_ofid.'"ORDER BY  AJBS_Type');
if(($ACL_Obj->ACL_Att_Master == 1) && ($count<$balShtSettCount) ) {
    echo "0";
}else if(($ACL_Obj->ACL_Att_Master == 0) && ($count<$balShtSettCount) ) {
    echo "2";
}else{
    echo "1";
}



//if($count < 1 )    echo "0";

//
//$balSheetArray = array(
//    1=>'While Adding new Job(s)',
//    2=>'While Receiving Advance Payment',
//    3=>'While Adding Additional Job(s)',
//    4=>'While Deleting added Job(s)',
//    5=>'While Receiving Balance Payment',
//    6=>'Expense aganist Courier Charges',
// );
//
//$balShtSettId = implode(",",array_keys($balSheetArray));
//$AttObj->getBalsheetsettings($balShtSettId,$preTally_user_ofid);
//$count=$AttObj->BSCountArray[0];
//if(count($balSheetArray) ==  $count )   {
//   echo "1"; 
//} else{
//   echo "0";
//}
?>
