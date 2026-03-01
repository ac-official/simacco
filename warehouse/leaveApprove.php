<?php
    require_once($BASEPATH . "includes/_define.php");
    require_once($BASEPATH . "smtp/smtpMail.php");
    require_once($BASEPATH . "preTallyClass/LeaveClass.php");
    $LvObj = new LeaveClass();
    $LvObj->getHrMailId($preTally_user_ofid );
    $hrMail=$LvObj->getHrMailIdArray[0];
    $today=date("Y-m-d");
    //$LR_ApprovedHR = $LR_FirstApproval = 0;
    
    $LvObj->Leave_Data = array(
        'LR_Id'             =>  $REQUEST['LR_Id'],
        'LR_Status'         =>  $REQUEST['status'],
        'LR_Comments'       =>  $REQUEST['comment'],        
        'LR_CreditedDate'   =>  $today    
    );
    
    if($REQUEST['status'] == '2' || $REQUEST['status'] == '4' || $REQUEST['status'] == '6') {  // HR Approved
        $LvObj->Leave_Data['LR_ApprovedHR'] = $preTally_user_id;
        $LvObj->Leave_Data['LR_FinalDt'] = date("Y-m-d");
    }
    else if($REQUEST['status'] == '1' || $REQUEST['status'] == '3'){
        $LvObj->Leave_Data['LR_FirstApproval'] = $preTally_user_id;
        $LvObj->Leave_Data['LR_FirstDt'] = date("Y-m-d");
    }
    if($REQUEST['status']==1){
        $result=$LvObj->changeLeaveStatus($REQUEST['LR_Id']);
        if($result=="Leave Updated Successfully"){
                $mail->addAddress($hrMail,'leave Mail');
                $mail->Subject 	= 'PreTally, Applied for leave Mail';
                $mailContent 	= file_get_contents($BASEPATH.'mailTemplate/userLeaveApproveRequest.html');
                $find	= array("{path}", "{user}", "{user_name}", "{base_path}");
                $replace= array(constant("BASE_PATH")."/mailTemplate/images","hr", $preTally_user_name, constant("BASE_PATH"));
                $mailContent 	= str_replace($find, $replace, $mailContent);
                $mail->msgHTML($mailContent, dirname(__FILE__));
                if (!$mail->send()) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                } else {
                    echo  $result;
                }
        }
        else{
            echo  $result;
        }
    }
    else{
     echo $result=$LvObj->changeLeaveStatus($REQUEST['LR_Id']); 
    }
?>