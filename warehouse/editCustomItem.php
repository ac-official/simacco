<?php
require_once($BASEPATH . "preTallyClass/NotificationClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");


$NotfObj        = new NotificationClass();
$OffObj         = new OfficeClass();
$BkupObj        = new BackupClass();
$ItemObj        = new ItemClass();
$UserObj        = new UserClass();


$rptPntTree = array();
$IT_Notf   = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);

$IE_Type   = array('','Income'=>'1','Expense'=>'2');
//$MH_Values = array('','Income'=>'1','Expense'=>'2');
//print_r($_REQUEST); //die(); 

foreach($_REQUEST as $key=>$value) {
    
    if(is_numeric($key)) {

        $MHType = $value['MH_Type'];

        if( is_numeric($value['SH_Name']) && $value['SH_Name'] != 0 ) {
            
            if( is_numeric($value['IT_Name']) && $value['IT_Name'] != 0 ) {
                $NotfObj->IT_MapData = array(
                    'IT_Id' =>  trim(htmlspecialchars($value['IT_Name'], ENT_QUOTES)),    
                );
                echo $NotfObj->swapItem(trim(htmlspecialchars($value['IT_Id_Old'], ENT_QUOTES)));
            } else {

                    $ItemObj->IT_Data = array(
                        'OF_Id'         => $preTally_user_ofid,
                        'IT_Name' 	=> trim(htmlspecialchars($value['IT_Name'], ENT_QUOTES)),
                        'SH_Id' 	=> $value['SH_Name'],
                        'MH_Type' 	=> $MHType,
                        'IT_Approved'   => $preTally_user_id,
                        'IT_Notf'	=> $IT_Notf,
                        'IT_MDate' 	=> date('Y-m-d H:i:s')
                    );

                    $offAdm = $OffObj->offzAdmin($preTally_user_ofid);
                    if($offAdm == $preTally_user_id || $ACL_Obj->ACL_Item == 1) {
                        $ItemObj->IT_Data["IT_Approval"]    = 0 ;
                        $ItemObj->IT_Data["IT_Status"]      = 1;
                    } else {
                        $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
                        $ItemObj->IT_Data["IT_Approval"]     = $IT_Approval;
                        $ItemObj->IT_Data["IT_Status"]       = 2;
                    }


                    $ItemObj->myMapItem($preTally_user_ofid);    
                    $Map_Obj = $ItemObj->ItemMapArray;

//                    $old = array('"', "[", "]");
//                    $new   = array("", "", "");
//                    $itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
                    if($itemMap == '') $itemMap = '""';
                    $IT_temp  = $ItemObj->verifyItem($value['IT_Id_Old'],$itemMap); 
                    $ITStatus = $ItemObj->ITStatus;

                    if($IT_temp == 0 || $IT_temp == '') {  
//                        $ItemObj->IT_Data['US_Id']    = $preTally_user_id;
                        $ItemObj->IT_Data['IT_CDate'] = date('Y-m-d H:i:s');
                        
                        $BkupObj->backupDetails('IT_Id = '.$value['IT_Id_Old'],$preTally_user_id,'items_bkup','items');
                
                        $Msg = $ItemObj->updateItem($value['IT_Id_Old']);
                    } else { 
                        $Msg = "Item Already Exists. Please Re-try.";
//                        $IT_Id=$IT_temp;
//                            echo $ItemObj->updateItem($IT_Id);
                    }
            }

        } 
    }
}if($Msg) echo $Msg ;
?>
