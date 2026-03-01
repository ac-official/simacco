<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
$salAdvObj = new AttendanceClass();
$DescObj= new DescriptionClass();
$SalItemId=$salAdvObj->getSalAdvItem($preTally_user_ofid);
    if($SalItemId==0 || is_null($SalItemId)){
        echo "Salary Item Error";
        return;
    }
    $us_id = trim(htmlspecialchars($_REQUEST['US_Id'], ENT_QUOTES));
    $monthArray = array('January'=>'01','February'=>'02', 'March'=>'03','April'=>'04','May'=>'05','June'=>'06','July'=>'07','August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12');
    $yearMonth=explode("-", $_REQUEST['BS_PaymentStartMonth']);
    $month = trim($yearMonth[0]);
    $year  = trim($yearMonth[1]);
    $salAdvObj->viewDetails("CONCAT(US_FName,' ',US_LName) AS Name,LAST_DAY(US_DOJ) AS LSTDATE ","users_auth"," WHERE US_Id=".$us_id);
    $userdetails=$salAdvObj->DetailsArray;     
    $mntyrString=strtotime($month." ".$year);
    $last_day  = date('Y-m-t', $mntyrString);
    if(strtotime($userdetails[0]->LSTDATE) > strtotime($last_day)){
        echo "Repayment Schedule month before Date of Join";
        return;
    }    
    $SalAdvArray=$salAdvObj->getDescriptions($userdetails[0]->Name,$SalItemId);
//    $SalAdvArray=$salAdvObj->BSSalAdvArray;
    $SalRptObj->BSSalRptArray=array();
    $counter=0;
    if(!$SalAdvArray){
        $DescObj->DS_Data = array(  
                    'US_Id'             => $preTally_user_id,
                    'OF_Id'             => $preTally_user_ofid,
                    'IT_Id'             => $SalItemId,
                    'DS_Description'	=> $userdetails[0]->Name,    
                    "DS_Notf"           => 'a:1:{i:0;s:1:"1";}',
                    'DS_Approved'	=> $preTally_user_id,
                    'DS_Status' 	=> 1,
                    'DS_MDate'          => date('Y-m-d')  
                     );
                    $desc_id=$DescObj->createDescription();
        
    }else{
        $desc_id=$SalAdvArray;
    }
    //$CHtemp = $BranchObj->verifyChequeNumber($_REQUEST['CHQ_Number'],$_REQUEST['BA_Id']); 
        $salAdvObj->BSSalRptArray["IT_Id"]          = $SalItemId;   
        $salAdvObj->BSSalRptArray["US_Id"]          = $preTally_user_id;  
        $salAdvObj->BSSalRptArray["BS_Description"] = $desc_id;    
        $salAdvObj->BSSalRptArray['LC_Id']          = $_REQUEST['LC_Id']!='' ? $_REQUEST['LC_Id']: $preTally_user_lcid;
        $salAdvObj->BSSalRptArray['BS_PaidBy']      = $_REQUEST['BS_PaidBy']!=''? $_REQUEST['BS_PaidBy']: $preTally_user_id ;
        $salAdvObj->BSSalRptArray['BS_PaidDate']    = $_REQUEST['BS_PaidDate']!=''? "'".$_REQUEST['BS_PaidDate']."'":"'".date("Y-m-d")."'";
        $salAdvObj->BSSalRptArray['BS_AprovdDate']    = $_REQUEST['BS_PaidDate']!=''? "'".$_REQUEST['BS_PaidDate']."'":"'".date("Y-m-d")."'";        
        $salAdvObj->BSSalRptArray["BS_Amount"]      =trim(htmlspecialchars($_REQUEST['SA_Amount'], ENT_QUOTES));
        $salAdvObj->BSSalRptArray['BS_VoucherNo']   = $_REQUEST['BS_VoucherNo'];   
        $salAdvObj->BSSalRptArray['PM_Id']          = $_REQUEST['PM_Id'] != 0     ?  $_REQUEST['PM_Id']      : 1 ;
        $salAdvObj->BSSalRptArray['BNK_Id']         = $_REQUEST['BNK_Id']!=''     ?  $_REQUEST['BNK_Id']     : 0 ;
        $salAdvObj->BSSalRptArray['BA_Id']          = $_REQUEST['BA_Id']!=''      ?  $_REQUEST['BA_Id']      : 0 ;
        $salAdvObj->BSSalRptArray['BB_Id']          = $_REQUEST['BB_Id']!=''      ?  $_REQUEST['BB_Id']      : 0 ;        
        $salAdvObj->BSSalRptArray['BS_User']        = $us_id;
        $salAdvObj->BSSalRptArray['BS_PayType']     = $_REQUEST['BS_PayType']!='' ?  $_REQUEST['BS_PayType'] : 0 ;           
        $salAdvObj->BSSalRptArray['BS_Transaction']     = $_REQUEST['BS_Transaction']!='' ?  $_REQUEST['BS_Transaction'] : 0 ;              
        $salAdvObj->BSSalRptArray['CHQ_Number']     = $_REQUEST['CHQ_Number']!='' ?  $_REQUEST['CHQ_Number'] : 0 ;              
        $salAdvObj->BSSalRptArray["BS_Status"]     =1;        
        $salAdvObj->BSSalRptArray["BS_Date"]       ="'".date("Y-m-d")."'";
        $salAdvObj->BSSalRptArray["BS_CDate"]      ="'".date("Y-m-d")."'";
        $salAdvObj->BSSalRptArray["BS_MDate"]      ="'".date("Y-m-d")."'";
        $salAdvObj->BSSalRptArray["BS_Complete"]   =1;     
        
        if($salAdvObj->BSSalRptArray['PM_Id'] == 2){
            $salAdvObj->BSSalRptArray['BS_Status'] = 2;
        } 
        else {                                                              // Setting Bank Details null while switching from bank to cash
           $salAdvObj->BSSalRptArray['BNK_Id']=0 ;
           $salAdvObj->BSSalRptArray['BB_Id']=0;
           $salAdvObj->BSSalRptArray['BA_Id']=0;
           $salAdvObj->BSSalRptArray['CHQ_Number']=0;
           $salAdvObj->BSSalRptArray['BS_Status'] = 1;
           $salAdvObj->BSSalRptArray['BS_PayType'] = 0;
        }           
           $salAdvObj->createSalaryAdvBSEntries();    
    $nextmonth=$monthArray[$month];
    //$SA_DeductAmt=round($_REQUEST['SA_Amount']/$_REQUEST['SA_PaymentDuration'],2);
    $salAdvObj->amt_split(trim(htmlspecialchars($_REQUEST['SA_Amount'], ENT_QUOTES)), htmlspecialchars($_REQUEST['SA_PaymentDuration'], ENT_QUOTES));
    $SA_DeductAmt=$salAdvObj->parts_array;     
    $salAdvObj->SA_data = array(
        'US_Id'                 =>trim(htmlspecialchars($_REQUEST['US_Id'], ENT_QUOTES)),
        'SA_Amount'             =>trim(htmlspecialchars($_REQUEST['SA_Amount'], ENT_QUOTES)),
        'SA_PaymentTime'        =>htmlspecialchars($_REQUEST['SA_PaymentDuration'], ENT_QUOTES),
        'SA_PaymentStartFrom'   =>$nextmonth."-".$year,
        'SA_DeductAmt'          =>$SA_DeductAmt[0] ,
        'SA_Cdate'         	    =>date('Y-m-d H:i:s'),
        'SA_USId' 	            =>$preTally_user_id
    );
    $SA_Id=$salAdvObj->saveSalaryAdvance();
    
    for($i=0;$i<$_REQUEST['SA_PaymentDuration'];$i++){
        $salAdvObj->SRS_data = array();
        $salAdvObj->SRS_data = array(
            'SA_Id'                         => $SA_Id,
            'US_Id'                         =>trim(htmlspecialchars($_REQUEST['US_Id'], ENT_QUOTES)),
            'SRS_RepaymentAmt'              => $SA_DeductAmt[$i],
            'SRS_Month'                     => $nextmonth."-".$year,
            'SRS_Status' 	            => 1
        );
    $salAdvObj->saveRepaymentShedule();
    $nextmonth  = $salAdvObj->getNextMonth($nextmonth,$year);
    $nextmonth=explode("-",$nextmonth);
    $year=$nextmonth[0];
    $nextmonth =$nextmonth[1];

    }
    echo "Salary Advance Details Added Successfully";
?>