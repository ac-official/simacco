<?php
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");

$ItemObj = new ItemClass();
$UserObj = new UserClass();
$OffObj  = new OfficeClass();
$BkupObj = new BackupClass();
$DescriptionObj = new DescriptionClass();
//die($_REQUEST['IT_Id']);
$newItemFlag = 0;
$SHIdPending = $_REQUEST['MH_Type'] == 1 ? 59 : 58;
$SH_Id = $_REQUEST['SH_Id']!=''  ? $_REQUEST['SH_Id'] : $SHIdPending ;
$IT_Business = $_REQUEST['IT_Business']!=''  ? $_REQUEST['IT_Business'] : 0 ;
$IT_Transfers = $_REQUEST['IT_Transfers']!=''  ? $_REQUEST['IT_Transfers'] : 0 ;

$rptPntTree = array();
$IT_Notf = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);

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
        'IT_Notf'	=> $IT_Notf,
	'IT_CDate' 	=> date('Y-m-d H:i:s')
);
$DescriptionObj->DS_Data = array(  
    'OF_Id'             => $preTally_user_ofid,
    'IT_Id'             => htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES),
    'OF_Id'             => $preTally_user_ofid,
    'DS_Approved'	=> $preTally_user_id,
    'DS_Notf'           => $IT_Notf,
    'DS_CDate'          => date('Y-m-d H:i:s')  
);
//if($_REQUEST['SH_Id'] != '') {$SHChk = $ItemObj->verifySubHead($_REQUEST['SH_Id'], ENT_QUOTES);die($SHChk);}

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
if($offAdm == $preTally_user_id) {
    $ItemObj->IT_Data["IT_Approval"]           = 0 ;
    $ItemObj->IT_Data["IT_Status"]             = 1;
    $DescriptionObj->DS_Data["DS_Approval"]    = 0 ;
    $DescriptionObj->DS_Data["DS_Status"]      = 1;
} else {
    $IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
    $ItemObj->IT_Data["IT_Approval"]           = $IT_Approval;
    $ItemObj->IT_Data["IT_Status"]             = 2;
    $DescriptionObj->DS_Data["DS_Approval"]    = $IT_Approval;
    $DescriptionObj->DS_Data["DS_Status"]      = 2;
}

$ItemObj->myMapItem($preTally_user_ofid);    
$Map_Obj = $ItemObj->ItemMapArray;

//$old  = array('"', "[", "]");
//$new  = array("", "", "");
//$itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
if($itemMap == '') $itemMap = '""';
$temp=$ItemObj->verifyItem(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES),$itemMap);

$ITStatus = $ItemObj->ITStatus;
$cnt=0;
$arraykey='';

//echo count(array_unique($_REQUEST['DS_Description'])) ." ". count($_REQUEST['DS_Description']);
if($_REQUEST['DS_Description']){
    if(count(array_unique($_REQUEST['DS_Description'])) != count($_REQUEST['DS_Description'])) { 
        foreach ($_REQUEST['DS_Description'] as $key => $value) { 
            if($cnt != 0) $arraykey = in_array(htmlspecialchars($value, ENT_QUOTES), array_values($DescriptionObj->DS_Data_Description['DS_Description']));
            if ($arraykey != '' ) { echo $key ; return false; }
            $DescriptionObj->DS_Data_Description["DS_Description"][] = htmlspecialchars($value, ENT_QUOTES);
            $cnt++;
        }  
    }
}
//foreach ($_REQUEST['DS_Description'] as $key => $value) {
//    if($key != 1)$arraykey = in_array($value, array_values($DescriptionObj->DS_Data_Description['DS_Description']));
//    if ($arraykey != '' ) { echo $key ; return false; }
//    $DescriptionObj->DS_Data_Description["DS_Description"][] = htmlspecialchars($value, ENT_QUOTES);
//}
    
  //$patternValues  = (array_intersect_key($_REQUEST['DS_Description'], ($_REQUEST['DS_Id'])));
$DescriptionObj->DS_Data_Description["DS_Description"] = $_REQUEST['DS_Description']; 
if(!($IT_Business==1 && $IT_Transfers==1))
{
    if( $temp == 0 || $temp =='') { 
        if( is_numeric($_REQUEST['SH_Id']) || $_REQUEST['SH_Id'] == '') {
            if(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES) == 0) {
                $ItemObj->IT_Data["US_Id"] = $preTally_user_id; 
                $ItemObj->IT_Data["IT_MDate"] = date('Y-m-d H:i:s'); 
            $ITId = $ItemObj->newCompanyItem();
            $DescriptionObj->DS_Data["IT_Id"] = $ITId ;
            
            $DescriptionObj->DS_Data["US_Id"] = $preTally_user_id;
            $DescriptionObj->DS_Data["DS_MDate"] = date('Y-m-d H:i:s'); 
            if($_REQUEST['DS_Description']){
                $DescriptionObj->newMultipleDescription();
            }
                
            echo $ITMessage =  'Item Created Successfully';
            
//            foreach ($_REQUEST['DS_Description'] as $key => $value) {
//                $DescriptionObj->DS_Data["DS_Description"] = htmlspecialchars($value, ENT_QUOTES);
//                $temp = $DescriptionObj->verifyDescription(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES));
//                if( $temp != 0 || $temp != '') { echo $key ; return false; }
//                if($key != 1)$arraykey = in_array($value, array_values($DescriptionObj->DS_Data_Description['DS_Description']));
//                if ($arraykey != '' ) { echo $key ; return false; }
//                $DescriptionObj->DS_Data_Description["DS_Description"][] = htmlspecialchars($value, ENT_QUOTES);
//            }

//            if(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES) == 0) {
//                $DescriptionObj->DS_Data["US_Id"] = $preTally_user_id;
//                $DescriptionObj->DS_Data["DS_MDate"] = date('Y-m-d H:i:s'); 
//                $DescriptionObj->newMultipleDescription();
//                echo $ITMessage;
//            } else {
//                $DescriptionObj->DS_Data["DS_Description"] = htmlspecialchars($_REQUEST['DS_Description'][1], ENT_QUOTES);
//                $DescriptionObj->updateDescription(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES));
//                unset($DescriptionObj->DS_Data_Description["DS_Description"][0]); 
//                if(sizeof($DescriptionObj->DS_Data_Description['DS_Description']) > 0)
//                $DescriptionObj->newMultipleDescription();
//                echo $ITMessage;
//            }
            } else {
            if($_REQUEST['DS_Description']){
                foreach ($_REQUEST['DS_Description'] as $key => $value) {
                    if($value != ''){
                        $ID = explode("_", $key); 
                        //$ID = split("_", $key); 
                        if($ID[0] == 'O'){
                            $DescriptionObj->DS_Data["DS_Description"] = htmlspecialchars($value, ENT_QUOTES);
                            $DescriptionObj->updateCompanyDescription(htmlspecialchars($ID[1], ENT_QUOTES));
                        } else {
                            $newItemFlag = 1;
//                            $DescriptionObj->DS_Data["DS_Description"] = $_REQUEST['DS_Description'][$key];
//                            $DescriptionObj->DS_Data["US_Id"] = $preTally_user_id;
//                            $DescriptionObj->DS_Data["DS_MDate"] = date('Y-m-d H:i:s'); 
//                            $DescriptionObj->newCompanyDescription();
                        }
                    }
                }if($newItemFlag == '1'){
                    $DescriptionObj->DS_Data["DS_Description"] = $_REQUEST['DS_Description'][$key];
                    $DescriptionObj->DS_Data["US_Id"] = $preTally_user_id;
                    $DescriptionObj->DS_Data["DS_MDate"] = date('Y-m-d H:i:s'); 
                    $DescriptionObj->newCompanyDescription();
                }
            }
                $ItemObj->IT_Data["IT_Status"] = htmlspecialchars($_REQUEST['IT_Status'], ENT_QUOTES); 
                
                $BkupObj->backupDetails('IT_Id = '.$_REQUEST['IT_Id'],$preTally_user_id,'items_bkup','items');
                
                echo $ItemObj->updateItem(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES));
            }
        } else echo "Invalid";
    } else { 
        if($ITStatus == 2) { echo "Item Already Exists. Waiting for Admin Approval."; } 
        else { echo 'fail'; }
    }
}
 else {
    echo "flag";    
}
?>