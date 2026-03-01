<?php
require_once($BASEPATH ."preTallyClass/AttendanceClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
echo "";
$SalRptObj = new AttendanceClass();
$DescObj= new DescriptionClass();
    $month  =$_REQUEST['attMonth'];
    $year   =$_REQUEST['attYear'];
    $SalItemId=$SalRptObj->getSalItem($preTally_user_ofid);
    if($SalItemId==0 || is_null($SalItemId)){
        echo "Item_err";
        return;
    }
    $payType=$_REQUEST['payType'];    
    $SalRptObj->getBankDetails($preTally_user_ofid);
    $BankAccDetails=$SalRptObj->BankDetails;       
    $month_arr =array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
    
    if($payType==1 || $payType==2){ //For Bank And Cash transaction
    $ESRIds=$_REQUEST['rowsId'];
    if($payType==1){
    $BaccId=$_REQUEST['BA_Id'];
    }else{
        $created_loc=$_REQUEST['Cr_Loc'];
        $paidto_loc=$_REQUEST['Paid_Loc'];
    }
    }else{ //For cheque transactions
        
        $IDArray=array();
        $CHQArray=array();
        $ESRIds="";
        $chqdetailArray=$_REQUEST['chqSalEntryDetails'];
        foreach($chqdetailArray AS $value){
            $IDArray[]=$value['ESR_Id'];
            $CHQArray[]=$value['CL_Id'];
        }
        $ESRIds=implode(",",$IDArray);
        $CHQIds=implode(",",$CHQArray);
    }    
    $SalRptObj->changeSalChequeStats($CHQIds);    
    $SalRptObj->getBSSalReport($month,$preTally_user_ofid,$year,$SalItemId,$ESRIds);    
    $SalrptArray=$SalRptObj->BSSalReportArray;
    $SalRptObj->BSSalRptArray=array();
    $counter=0;
    $acc_err=0;
    $err_ids=array();
    $error=array();
    $error["acc_err"]=$acc_err;
    $error["counter"]=$counter;
    if(count($SalrptArray)==0){
        echo "null_err";
        return;
    }
    if($SalrptArray){    
        foreach($SalrptArray as $RptData){    
            if($RptData["DS_Id"]==""){
                    $DescObj->DS_Data = array(  
                    'US_Id'             => $preTally_user_id,
                    'OF_Id'             => $preTally_user_ofid,
                    'IT_Id'             => $SalItemId,
                    'DS_Description'	=> htmlspecialchars(ucwords($RptData["US_FName"].' '.substr($RptData["US_LName"],0,1)), ENT_QUOTES),    
                    "DS_Notf"           => 'a:1:{i:0;s:1:"1";}',
                    'DS_Approved'	=> $preTally_user_id,
                    'DS_Status' 	=> 1,
                    'DS_MDate'          => date('Y-m-d')  
                     );
                    $desc_id=$DescObj->createDescription();
           }
           else{
               $desc_id=$RptData["DS_Id"];
           }           
           if($payType==1 && $_REQUEST['Ref_text']!="") $remarks    =$month_arr[$month-1] ." ".$year. "-". $_REQUEST['Ref_text'];
           else $remarks    =$month_arr[$month-1] ." ".$year;
           $SalRptObj->BSSalRptArray[$counter]["IT_Id"]      = $SalItemId;   
           $SalRptObj->BSSalRptArray[$counter]["US_Id"]      = $preTally_user_id;   
           $SalRptObj->BSSalRptArray[$counter]["BS_Description"]= $desc_id;    
           $SalRptObj->BSSalRptArray[$counter]["BS_Remarks"]    ="'".$remarks."'";
           $SalRptObj->BSSalRptArray[$counter]["BS_Amount"]     =$RptData["ESR_TakeHomeSalary"];
           $SalRptObj->BSSalRptArray[$counter]["BS_PaidBy"]     =$preTally_user_id;
           $SalRptObj->BSSalRptArray[$counter]["LC_Id"]        =$preTally_user_lcid;
           $SalRptObj->BSSalRptArray[$counter]["BS_User"]     =$RptData["US_Id"];
           $SalRptObj->BSSalRptArray[$counter]["BS_StaffId"]   =$RptData["US_Id"];
           $SalRptObj->BSSalRptArray[$counter]["BS_IEByLC"]   =$RptData["LC_Id"];
           $SalRptObj->BSSalRptArray[$counter]["BS_IEByUS"]   =$RptData["US_Id"];
           if(($payType==1 || $payType=="CHQ")){ //Through Bank Trans
               $SalRptObj->BSSalRptArray[$counter]["PM_Id"]      =2;    
               $SalRptObj->BSSalRptArray[$counter]["BS_Status"]  =2;
               if($payType==1){
               $SalRptObj->BSSalRptArray[$counter]["BNK_Id"] =$BankAccDetails[$BaccId]["BNK_Id"];
               $SalRptObj->BSSalRptArray[$counter]["BB_Id"]  =$BankAccDetails[$BaccId]["BB_Id"];
               $SalRptObj->BSSalRptArray[$counter]["BA_Id"]  =$BaccId;
               }else if($payType=='CHQ'){
               $chqBA_Id=$chqdetailArray[$RptData["ESR_Id"]]["BA_Id"];
               $SalRptObj->BSSalRptArray[$counter]["BNK_Id"] =$BankAccDetails[$chqBA_Id]["BNK_Id"];
               $SalRptObj->BSSalRptArray[$counter]["BB_Id"]  =$BankAccDetails[$chqBA_Id]["BB_Id"];
               $SalRptObj->BSSalRptArray[$counter]["BA_Id"]  =$chqBA_Id;
               $SalRptObj->BSSalRptArray[$counter]["CHQ_Number"]=$chqdetailArray[$RptData["ESR_Id"]]["CL_Id"];
               }
           }else{
               if($payType==1 || $payType=="CHQ"){ 
                   $acc_err++;    
                   $err_ids[]=$RptData["ESR_Id"];
               }
               $SalRptObj->BSSalRptArray[$counter]["PM_Id"]   =1;
               $SalRptObj->BSSalRptArray[$counter]["BS_Status"]  =1;
               $SalRptObj->BSSalRptArray[$counter]["BNK_Id"] =0;
               $SalRptObj->BSSalRptArray[$counter]["BB_Id"]  =0;
               $SalRptObj->BSSalRptArray[$counter]["BA_Id"]  =0;
           }
           $SalRptObj->BSSalRptArray[$counter]["BS_AprovdDate"]    ="'".date("Y-m-d")."'";
           $SalRptObj->BSSalRptArray[$counter]["BS_Date"]    ="'".date("Y-m-d")."'";
           $SalRptObj->BSSalRptArray[$counter]["BS_CDate"]   ="'".date("Y-m-d")."'";
           $SalRptObj->BSSalRptArray[$counter]["BS_MDate"]   ="'".date("Y-m-d")."'";
           $SalRptObj->BSSalRptArray[$counter]["BS_PaidDate"]   ="'".date("Y-m-d")."'";
           $SalRptObj->BSSalRptArray[$counter]["BS_Complete"]=1;
           $counter++;
        }            
        if($acc_err==0){              
            $SalRptObj->createSalaryBSEntries();
            $SalRptObj->updateBSSalaryRptStatus($ESRIds);           
        }
        $error["acc_err"]=$acc_err;
        $error["counter"]=$counter;
        $error["err_ids"]=$err_ids;
        echo json_encode($error);
    }
?>