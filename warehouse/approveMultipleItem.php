<?php
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");

$ItemObj = new ItemClass();
$UserObj = new UserClass();
$BkupObj = new BackupClass();
$OffObj  = new OfficeClass();
$BSObj   = new BalanceSheetClass();

//echo $ItemObj->approveMultipleItem($IT_Approval,$_REQUEST['IT_Id'],$preTally_user_id);

            $ItemObj->IT_Data = array(
                'OF_Id'         => $preTally_user_ofid,
                'IT_Name' 	=> trim(htmlspecialchars($_REQUEST['IT_Name'], ENT_QUOTES)),
                'IT_Approved'   => $preTally_user_id,
                'IT_MDate' 	=> date('Y-m-d H:i:s')
            );

            $offAdm = $OffObj->offzAdmin($preTally_user_ofid);
            if($offAdm == $preTally_user_id) {
                $ItemObj->IT_Data["IT_Approval"]    = 0 ;
                $ItemObj->IT_Data["IT_Status"]      = 1;
            } else if($ACL_Obj->ACL_Item == 1  && $_REQUEST['IT_Status'] == 1 ) {
                $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
                $ItemObj->IT_Data["IT_Approval"]     = $IT_Approval;
                $ItemObj->IT_Data["IT_Status"]       = 1;
            } else {
                $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
                $ItemObj->IT_Data["IT_Approval"]     = $IT_Approval;
                $ItemObj->IT_Data["IT_Status"]       = 3;
            }

            
            
            if(is_numeric($_REQUEST['IT_Id'])){
                
                $BkupObj->backupDetails('IT_Id = '.$_REQUEST['IT_Id_Old'],$preTally_user_id,'items_bkup','items');
                
                echo $BSObj->updateBalanceSheetItems($_REQUEST['IT_Id'], $_REQUEST['IT_Id_Old']);
            }else{
                
                $BkupObj->backupDetails('IT_Id = '.$_REQUEST['IT_Id_Old'],$preTally_user_id,'items_bkup','items');
                
                echo $ItemObj->updateItem($_REQUEST['IT_Id_Old']);
            }
      
 
            
?>