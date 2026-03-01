<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
$mealCrdObj = new UserClass();
$DescObj= new DescriptionClass();
$MealCardItemId=$mealCrdObj->getMealCardItem($preTally_user_ofid);
    if($MealCardItemId==0 || is_null($MealCardItemId)){
        echo "Meal Allowance Item Error";
        return;
    }       
    $MealCrdArray=$mealCrdObj->getDescriptions($_REQUEST['ML_Month'],$MealCardItemId);
//    $MealCrdArray=$mealCrdObj->BSSalAdvArray;
    $SalRptObj->BSSalRptArray=array();
    $counter=0;    
    if(!$MealCrdArray){ 
        $DescObj->DS_Data = array(  
                    'US_Id'             => $preTally_user_id,
                    'OF_Id'             => $preTally_user_ofid,
                    'IT_Id'             => $MealCardItemId,
                    'DS_Description'	=> $_REQUEST['ML_Month'],    
                    "DS_Notf"           => 'a:1:{i:0;s:1:"1";}',
                    'DS_Approved'	=> $preTally_user_id,
                    'DS_Status' 	=> 1,
                    'DS_MDate'          => date('Y-m-d')  
                     );
                    $desc_id=$DescObj->createDescription();
        
    }else{
        $desc_id=$MealCrdArray;
    }
    //$CHtemp = $BranchObj->verifyChequeNumber($_REQUEST['CHQ_Number'],$_REQUEST['BA_Id']); 
        $mealCrdObj->BSSalRptArray["IT_Id"]          = $MealCardItemId;   
        $mealCrdObj->BSSalRptArray["US_Id"]          = $preTally_user_id;  
        $mealCrdObj->BSSalRptArray["BS_Description"] = $desc_id;    
        $mealCrdObj->BSSalRptArray['LC_Id']          = $_REQUEST['LC_Id']!='' ? $_REQUEST['LC_Id']: $preTally_user_lcid;
        $mealCrdObj->BSSalRptArray['BS_PaidBy']      = $_REQUEST['BS_PaidBy']!=''? $_REQUEST['BS_PaidBy']: $preTally_user_id ;
        $mealCrdObj->BSSalRptArray['BS_PaidDate']    = $_REQUEST['BS_PaidDate']!=''? "'".$_REQUEST['BS_PaidDate']."'":"'".date("Y-m-d")."'";
        $mealCrdObj->BSSalRptArray['BS_AprovdDate']  = $_REQUEST['BS_PaidDate']!=''? "'".$_REQUEST['BS_PaidDate']."'":"'".date("Y-m-d")."'";        
        $mealCrdObj->BSSalRptArray["BS_Amount"]      =trim(htmlspecialchars($_REQUEST['ML_Amount'], ENT_QUOTES));
        $mealCrdObj->BSSalRptArray['BS_VoucherNo']   = $_REQUEST['BS_VoucherNo'];   
        $mealCrdObj->BSSalRptArray['PM_Id']          = $_REQUEST['PM_Id'] != 0     ?  $_REQUEST['PM_Id']      : 1 ;
        $mealCrdObj->BSSalRptArray['BNK_Id']         = $_REQUEST['BNK_Id']!=''     ?  $_REQUEST['BNK_Id']     : 0 ;
        $mealCrdObj->BSSalRptArray['BA_Id']          = $_REQUEST['BA_Id']!=''      ?  $_REQUEST['BA_Id']      : 0 ;
        $mealCrdObj->BSSalRptArray['BB_Id']          = $_REQUEST['BB_Id']!=''      ?  $_REQUEST['BB_Id']      : 0 ;        
        $mealCrdObj->BSSalRptArray['BS_User']        = 0;
        $mealCrdObj->BSSalRptArray['BS_PayType']     = $_REQUEST['BS_PayType']!='' ?  $_REQUEST['BS_PayType'] : 0 ;           
        $mealCrdObj->BSSalRptArray['BS_Transaction'] = $_REQUEST['BS_Transaction']!='' ?  $_REQUEST['BS_Transaction'] : 0 ;              
        $mealCrdObj->BSSalRptArray['CHQ_Number']     = $_REQUEST['CHQ_Number']!='' ?  $_REQUEST['CHQ_Number'] : 0 ;              
        $mealCrdObj->BSSalRptArray["BS_Status"]     =1;        
        $mealCrdObj->BSSalRptArray["BS_Date"]       ="'".date("Y-m-d")."'";
        $mealCrdObj->BSSalRptArray["BS_CDate"]      ="'".date("Y-m-d")."'";
        $mealCrdObj->BSSalRptArray["BS_MDate"]      ="'".date("Y-m-d")."'";
        $mealCrdObj->BSSalRptArray["BS_Complete"]   =1;     
        
        if($mealCrdObj->BSSalRptArray['PM_Id'] == 2){
            $mealCrdObj->BSSalRptArray['BS_Status'] = 2;
        } 
        else {                                                              // Setting Bank Details null while switching from bank to cash
           $mealCrdObj->BSSalRptArray['BNK_Id']=0 ;
           $mealCrdObj->BSSalRptArray['BB_Id']=0;
           $mealCrdObj->BSSalRptArray['BA_Id']=0;
           $mealCrdObj->BSSalRptArray['CHQ_Number']=0;
           $mealCrdObj->BSSalRptArray['BS_Status'] = 1;
           $mealCrdObj->BSSalRptArray['BS_PayType'] = 0;
        }          
           $smids=$_REQUEST["SM_Id"];
           $mealCrdObj->createMealCardBSEntries();        
           $mealCrdObj->updateMealCardStats($smids);
    
    echo "Meal Card Details Added Successfully";
?>