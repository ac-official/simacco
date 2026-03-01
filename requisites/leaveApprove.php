<?php


    require_once("../preTallyClass/LeaveClass.php");
                    $lvObj = new LeaveClass();
                    
                    //$lvObj->listLeave();
                    //$lv_Obj = $lvObj->listLeaveArray;
                    $today=date("Y-m-d");

    if(isset($_REQUEST['LR_Id'])){
    $LR_Id=$_REQUEST['LR_Id'];
    $LR_ID=(explode("/",$LR_Id));
    $status=$LR_ID[1];
    $LR_Id=$LR_ID[0];
    $comment=$LR_ID[2];
    
    if($status==1){//approve
      $result=$lvObj->approveLeave($LR_Id,$preTally_user_id,$today,$comment);
     
      if($result==0){
          echo "approve failed...allready cancelled";
      }
      else{
          echo "approve successfully";
          
      }
      
        
    }
    if($status==2){//reject
       $result=$lvObj->rejectLeave($LR_Id,$preTally_user_id,$today,$comment);
       if($result==0){
           echo "can not reject ...allready cancelled";
           
       }
       else{
           echo "rejected successfully";
       }
    }
    if($status==3){//reject
       $result=$lvObj->cancelLeave($LR_Id,$preTally_user_id,$today,$comment);
       if($result>0){
           echo "cancelled successfully";
           
       }
    }
    
   

    
    
    }
?>
