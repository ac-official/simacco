<?php
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/NotificationClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/MainheadClass.php");
require_once($BASEPATH . "preTallyClass/SubheadClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$BalSheetObj    = new BalanceSheetClass();
$NotfObj        = new NotificationClass();
$DescObj        = new DescriptionClass();
$MHObj          = new MainheadClass();
$SHObj          = new SubheadClass();
$BkupObj        = new BackupClass();
$OffObj         = new OfficeClass();
$ItemObj        = new ItemClass();
$UserObj        = new UserClass();


$rptPntTree = array();
$IT_Notf   = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);

$IE_Type   = array('','Income'=>'1','Expense'=>'2');
//$MH_Values = array('','Income'=>'1','Expense'=>'2');
//print_r($_REQUEST); //die();  1`

foreach($_REQUEST as $key=>$value) {
    
    if(is_numeric($key)) {

        $MHType = $value['MH_Type'];

        if( is_numeric($value['SH_Name']) && $value['SH_Name'] != 0 ) {
            
            $IT_Approval = $DS_Approval = $UserObj->myReportingPerson($preTally_user_id);
            
            if(!is_numeric($value['IT_Id_New'])){

                $ItemObj->IT_Data = array(
                    'OF_Id'     => $preTally_user_ofid,
                    'IT_Name' 	=> trim(htmlspecialchars($value['IT_Name'], ENT_QUOTES)),
                    'SH_Id' 	=> $value['SH_Name'],
                    'MH_Type' 	=> $MHType,
                    'IT_Approved'   => $preTally_user_id,
                    'IT_Notf'	=> $IT_Notf,
                    'IT_MDate' 	=> date('Y-m-d H:i:s')
                );

                $offAdm = $OffObj->offzAdmin($preTally_user_ofid);
                if($offAdm == $preTally_user_id) {
                    $ItemObj->IT_Data["IT_Approval"]    = 0 ;
                    $ItemObj->IT_Data["IT_Status"]      = 1;
                } else {
//                    $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
                    $ItemObj->IT_Data["IT_Approval"]     = $IT_Approval;
                    $ItemObj->IT_Data["IT_Status"]       = 3;
                }


                $ItemObj->myMapItem($preTally_user_ofid);    
                $Map_Obj = $ItemObj->ItemMapArray;

//                $old = array('"', "[", "]");
//                $new   = array("", "", "");
//                $itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
                if($itemMap == '') $itemMap = '""';
                $IT_temp  = $ItemObj->verifyItem('0',$itemMap); 
                //$ITStatus = $ItemObj->ITStatus;
                
                $ITStatus = $ItemObj->ItemVArray['IT_Status'];
                $MH_Type  = $ItemObj->ItemVArray['MH_Type'];
                $SH_Id    = $ItemObj->ItemVArray['SH_Id']; 

                if($IT_temp == 0 || $IT_temp == '') {  
//                    if($ACL_Obj->ACL_Item != 1) {
                        $ItemObj->IT_Data['US_Id']    = $preTally_user_id;
                        $ItemObj->IT_Data['IT_CDate'] = date('Y-m-d H:i:s');
                        $IT_Id = $ItemObj->newBalSheetItem();  
//                    } else {
//                        $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
//                        $ItemObj->IT_Data["IT_Approval"] = $IT_Approval;
//                        $ItemObj->IT_Data["IT_Status"]   = 1;
//                        $ItemObj->updateItem($value['IT_Id_Old']);
//                        $IT_Id = $value['IT_Id_Old'];
//                    }
                } else {
                    $IT_Id=$IT_temp;
                    if( $ITStatus != 1 || ( $ACL_Obj->ACL_Item == 1 ) || ( $ACL_Obj->ACL_Item == 1 && $ITStatus == 1 ) ){
//                        if($offAdm == $preTally_user_id) 
//                            $ItemObj->updateItem($IT_Id);
//                        if($MH_Type != $MHType || $SH_Id != $value['SH_Name'])
                        if( $ACL_Obj->ACL_Item == 1 && $ITStatus == 1 ){
                            $ItemObj->IT_Data["IT_Approval"]     = $IT_Approval;
                            $ItemObj->IT_Data["IT_Status"]       = 1;
                        }
                      
                        $BkupObj->backupDetails('IT_Id = '.$IT_Id,$preTally_user_id,'items_bkup','items');
    
                        $ItemObj->updateItem($IT_Id);
                    }   
                }
            } else { 
                $IT_Id = $value['IT_Id_New'];
            }
            
            if(!is_numeric($value['DS_Id_New'])){
                $DescObj->DS_Data = array(  
                    'IT_Id'             => $IT_Id,
                    'OF_Id'             => $preTally_user_ofid,
                    'DS_Description'    => trim(htmlspecialchars($value['DS_Description'], ENT_QUOTES)),
                    'DS_Approved'       => $preTally_user_id,
                    'DS_Notf'           => $IT_Notf,
                    'DS_MDate'          => date('Y-m-d H:i:s')
                );

                $offAdm = $OffObj->offzAdmin($preTally_user_ofid);
                if($offAdm == $preTally_user_id) {
                    $DescObj->DS_Data["DS_Approval"]    = 0 ;
                    $DescObj->DS_Data["DS_Status"]      = 1;
                } else {
//                    $DS_Approval = $UserObj->myReportingPerson($preTally_user_id);
                    $DescObj->DS_Data["DS_Approval"]    = $DS_Approval;
                    $DescObj->DS_Data["DS_Status"]      = 3;
                }

                $temp = $DescObj->verifyDescription('0'); 
                $DSStatus = $DescObj->DSStatus['DS_Status'];

                if($temp == 0 || $temp == '') { 
                    $DescObj->DS_Data['US_Id']    = $preTally_user_id;
                    $DescObj->DS_Data["DS_CDate"] = date('Y-m-d H:i:s');
                    $descriptionId = $DescObj->newBalSheetDescription();  
                }  else {
                    $descriptionId = $temp;
                    $DescObj->DS_Data["DS_Approval"]     = $DS_Approval;
                    
                    if( $ACL_Obj->ACL_Item == 1 ){
                        $DescObj->DS_Data["DS_Status"]   = 1;
                    } else {
                        $DescObj->DS_Data["DS_Status"]   = $DSStatus;
                    }
                    $DescObj->updateDescription($descriptionId);
                }
            }else{
                $descriptionId = $value['DS_Id_New'];
            }

//            if( $IT_Id != $_REQUEST[$key.'_IT_Name'] || $descriptionId == $_REQUEST[$key.'_DS_Description'] ) {
                $BalSheetObj->BS_Data = array(
                        'IT_Id'         => $IT_Id,
                        'BS_Amount'     => trim(htmlspecialchars($value['BS_Amount'], ENT_QUOTES)),
                        'BS_Description'=> $descriptionId,
                        'BS_MDate'      => date('Y-m-d H:i:s')
                );
                
                $BkupObj->backupDetails('BS_Id = '.$key,$preTally_user_id,'balance_sheets_bkup','balance_sheets');

                $Msg = $BalSheetObj->updateBalanceSheet($key);
//            } //else {echo "Succsess";}

        //    } else {
        //        echo "Invalid Entry.Please Re-Try.";
        } 
    }
}if($Msg) echo $Msg ;
?>
