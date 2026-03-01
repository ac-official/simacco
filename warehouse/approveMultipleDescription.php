<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");

$UserObj = new UserClass();
$OffObj  = new OfficeClass();
$BkupObj = new BackupClass();
$DescObj = new DescriptionClass();
$BSObj   = new BalanceSheetClass();

//echo $DescObj->approveMultipleItem($DS_Approval,$_REQUEST['DS_Id'],$preTally_user_id);

            $DescObj->DS_Data = array(
                'OF_Id'          => $preTally_user_ofid,
                'DS_Description' => trim(htmlspecialchars($_REQUEST['DS_Description'], ENT_QUOTES)),
                'DS_Approved'    => $preTally_user_id,
                'DS_MDate' 	 => date('Y-m-d H:i:s')
            );

            $offAdm = $OffObj->offzAdmin($preTally_user_ofid);
            if($offAdm == $preTally_user_id) {
                $DescObj->DS_Data["DS_Approval"]    = 0 ;
                $DescObj->DS_Data["DS_Status"]      = 1;
            } else if( $ACL_Obj->ACL_Item == 1 ){
                    $DS_Approval = $UserObj->myReportingPerson($preTally_user_id);
                    $DescObj->DS_Data["DS_Approval"]    = $DS_Approval;
                    $DescObj->DS_Data["DS_Status"]      = 1;
//            } else if($ACL_Obj->ACL_Item == 1) {
//                $DS_Approval = $UserObj->myReportingPerson($preTally_user_id);
//                $DescObj->DS_Data["DS_Approval"]     = $DS_Approval;
//                $DescObj->DS_Data["DS_Status"]       = 1;
            } else {
                $DS_Approval = $UserObj->myReportingPerson($preTally_user_id);
                $DescObj->DS_Data["DS_Approval"]     = $DS_Approval;
                $DescObj->DS_Data["DS_Status"]       = 3;
            }

            if(is_numeric($_REQUEST['DS_Id'])){

                $BkupObj->backupDetails('BS_Description = '.$_REQUEST['DS_Id_Old'],$preTally_user_id, 'balance_sheets_bkup','balance_sheets');
                        
                echo $BSObj->updateBalanceSheetDescriptions($_REQUEST['DS_Id'], $_REQUEST['DS_Id_Old']);
            }else{
                echo $DescObj->updateDescription($_REQUEST['DS_Id_Old']);
            }
      
?>