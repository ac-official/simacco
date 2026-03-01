<?php
include_once("connection.php");
class ResetPwdClass {
    //------------------------------------------Checking Password Reset Token---------------------------//
        function checkPassToken($token){            
            $sql= "SELECT RQ_Id,US_Email,RQ_Token,TIMESTAMPDIFF(MINUTE,RQ_Time,NOW( )) AS DIFFER FROM user_resetpwd WHERE RQ_Token='".$token."' AND RQ_Status=0";
            $result=mysqli_query($GLOBALS['con'],$sql);            
            if(mysqli_num_rows($result)==1){
                $row=  mysqli_fetch_assoc($result);
                if($row['DIFFER']>60){
                    return "token_expired";
                }
                else{
                    $this->rec_Mail=$row['US_Email'];
                    $this->rec_ID=$row['RQ_Id'];
                }
            }
            else{
                return "token_error";
            }            
        }
    //------------------------------------------Checking Password Reset Token---------------------------//        
        function resetPassword(){
           $sql = "UPDATE users_auth SET US_Password='".md5($this->rec_Password)."' WHERE MD5(US_Email)='".$this->rec_Email."'  AND US_Status != 5"; 
           $sql_pwd = "UPDATE user_resetpwd SET RQ_Status=1 WHERE RQ_Id=".$this->rec_ID;           
           mysqli_query($GLOBALS['con'],$sql);
           mysqli_query($GLOBALS['con'],$sql_pwd);
           return "PwdChange_Success";
        }
}
