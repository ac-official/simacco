<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$AttObj = new AttendanceClass();
if($_REQUEST['HD_Id']==0 ){   
                if($_REQUEST['HD_Type']==3){
                //FOR A GROUP OF Restricted Holidays
                        $hd_array=  explode(",", $_REQUEST['HD_Count']);
                        $hdcount=sizeof($hd_array);        
                        $rh_batch=$AttObj->createRHBatch($preTally_user_ofid);
                        for($i=0;$i<$hdcount;$i++){
                            $datename   ='HD_Date_'.$hd_array[$i];
                            //$title      ='HD_Comments_'.$hds;     
                        if($_REQUEST[$datename]){    
                            $AttObj->AT_Data = array(    
                            'HD_Date' 		=> htmlspecialchars($_REQUEST[$datename], ENT_QUOTES),
                            'HD_Type'           => htmlspecialchars($_REQUEST['HD_Type'], ENT_QUOTES),
                            'HD_Comments' 	=> htmlspecialchars($_REQUEST['HD_Comments'], ENT_QUOTES),            
                            'OF_Id'             => $preTally_user_ofid,
                            'DP_Id'             => 0,
                            'ST_Id'             => htmlspecialchars($_REQUEST['StateList'], ENT_QUOTES),
                            'HD_Status' 	=> htmlspecialchars($_REQUEST['HD_Status'], ENT_QUOTES),
                            'HD_CDate' 		=> date('Y-m-d H:i:s')
                        );        
                        $AttObj->AT_Data['HD_Batch']=$rh_batch;                         
                        $AttObj->AddHolidays();
                        }
                    }
                    
                }  else {
                     $AttObj->AT_Data = array(    
                           'HD_Date' 		=> htmlspecialchars($_REQUEST['HD_Date_0'], ENT_QUOTES),
                           'HD_Type'           => htmlspecialchars($_REQUEST['HD_Type'], ENT_QUOTES),
                           'HD_Comments' 	=> htmlspecialchars($_REQUEST['HD_Comments'], ENT_QUOTES),
                           'HD_Batch'          => '0',
                           'OF_Id'             => $preTally_user_ofid,
                           'DP_Id'             => (int)$_REQUEST['DP_Id'],
                           'ST_Id'             => htmlspecialchars($_REQUEST['StateList'], ENT_QUOTES),
                           'HD_Status'          => htmlspecialchars($_REQUEST['HD_Status'], ENT_QUOTES),
                           'HD_CDate' 		=> date('Y-m-d H:i:s')
                       );
                     $AttObj->AddHolidays();
                }
        echo "Holiday Added Successfully";
}
else{ //FOR UPDATING HOLIDAY
    if($_REQUEST['HD_Type']==3){
        $hd_updarray=  explode(",", $_REQUEST['HD_Count']);
        $del_array=$_REQUEST['HD_Del'];
        $hdupdcount=sizeof($hd_updarray);     
        if($del_array!=0)
        {
            $AttObj->delRHDays($del_array);
        }  
        $btch=$_REQUEST['HD_Batch'];
        if($btch=="0"){          
            $batch_numb=$AttObj->createRHBatch($preTally_user_ofid);
        }
        else{
            $batch_numb=$_REQUEST['HD_Batch'];
            
        } //die($batch_numb);                   
                        for($i=0;$i<$hdupdcount;$i++){
                            $datename   ='HD_Date_'.$hd_updarray[$i];
                            $hid_id      ='HD_hid_'.$hd_updarray[$i];   
                            if($_REQUEST[$datename]){
                            $AttObj->AT_Data = array(    
                            'HD_Date' 		=> htmlspecialchars($_REQUEST[$datename], ENT_QUOTES),
                            'HD_Type'           => htmlspecialchars($_REQUEST['HD_Type'], ENT_QUOTES),
                            'HD_Comments' 	=> htmlspecialchars($_REQUEST['HD_Comments'], ENT_QUOTES),                                        
                            'ST_Id'             => htmlspecialchars($_REQUEST['StateList'], ENT_QUOTES),
                            'HD_Batch'          => $batch_numb,
                            'DP_Id'             => 0,
                            'HD_Status' 	=> htmlspecialchars($_REQUEST['HD_Status'], ENT_QUOTES),
                            'HD_CDate' 		=> date('Y-m-d H:i:s'));                      
                            
                            if($_REQUEST[$hid_id]!=0){
                            $AttObj->UpdateHoliday($_REQUEST[$hid_id]);                       
                            }  else {      
                                
                            $AttObj->AT_Data['OF_Id']=$preTally_user_ofid;                                
                            //var_dump($AttObj->AT_Data);
                            $AttObj->AddHolidays();                       
                            }
                            }
                            
                    }
    }else{
        $AttObj->AT_Data = array(    
           'HD_Date'           => htmlspecialchars($_REQUEST['HD_Date_0'], ENT_QUOTES),
           'HD_Type'           => htmlspecialchars($_REQUEST['HD_Type'], ENT_QUOTES),
           'HD_Comments'       => htmlspecialchars($_REQUEST['HD_Comments'], ENT_QUOTES), 
           'HD_Batch'          => '0',
           'OF_Id'             => $preTally_user_ofid,
           'DP_Id'             => (int)$_REQUEST['DP_Id'],
           'ST_Id'             => htmlspecialchars($_REQUEST['StateList'], ENT_QUOTES),
           'HD_Status'         => htmlspecialchars($_REQUEST['HD_Status'], ENT_QUOTES),
           'HD_CDate' 		=> date('Y-m-d H:i:s')
       );
    $AttObj->UpdateHoliday($_REQUEST['HD_hid_0']);
        
    }
     echo 'Holiday Updated Successfully';
}


?>