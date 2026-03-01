<?php
require_once("connection.php");
//date_default_timezone_set('Asia/Kolkata');
class MessageClass
{
    function sendMessage($Ids)
    {
        $toIds = explode(',',$Ids);  
        $insertSql = "INSERT INTO user_messages ( " . implode(', ', array_keys($this->Message_Details)) . ") VALUES ";
        $valuesSQL = "";
        foreach($toIds as $Id) {
            $this->Message_Details['MSG_To'] = $Id;
            
            if (!empty($valuesSQL)) { 
                $valuesSQL .= " , ";
            }
            $valuesSQL .= "(" . "'" . implode("','", array_values($this->Message_Details)) . "'" . ")";
        }
        mysqli_query($GLOBALS['con'],$insertSql.$valuesSQL);
        if(mysqli_affected_rows($GLOBALS['con']) > 0) {
            return "success";
        }
        else {
            return "failed";
        }
    }
    function sendCorrectionMessage($Ids)
    {
        $toIds = explode(',',$Ids);  
        $insertSql = "INSERT INTO user_messages ( " . implode(', ', array_keys($this->Message_Details)) . ") VALUES ";
        $valuesSQL = "";
        foreach($toIds as $Id) {
            $this->Message_Details['MSG_To'] = $Id;
            
            if (!empty($valuesSQL)) { 
                $valuesSQL .= " , ";
            }
            $valuesSQL .= "(" . "'" . implode("','", array_values($this->Message_Details)) . "'" . ")";
        }
        mysqli_query($GLOBALS['con'],$insertSql.$valuesSQL);
        if(mysqli_affected_rows($GLOBALS['con']) > 0) {
            return mysqli_insert_id($GLOBALS['con']);
        }
        else {
            return "failed";
        }
    }
    function insert_CorrectionMsg(){
        $insertSql = "INSERT INTO entry_messages ( " . implode(', ', array_keys($this->Entry_MSG)) . ") VALUES (" . "'" . implode("','", array_values($this->Entry_MSG)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$insertSql);
    }
    function getBSMessages($id){
        $selectSQL="SELECT GROUP_CONCAT(Msg_Id) AS MSG_STRING FROM entry_messages WHERE BS_Id=".$id;
        $rows=mysqli_fetch_object(mysqli_query($GLOBALS['con'], $selectSQL));        
        $msgSQL="SELECT  MSG.MSG_Message,MSG.MSG_CreatedOn,US_FName FROM user_messages MSG LEFT JOIN users_auth AS UA ON UA.US_Id=MSG.MSG_From WHERE MSG.Msg_Id IN (".$rows->MSG_STRING.")";
        $query=mysqli_query($GLOBALS['con'], $msgSQL); 
        $count=1;        
        while($row = mysqli_fetch_object($query)) {
            $msgStr=explode('<br><br>',$row->MSG_Message);
            $string.= $count.".".$msgStr[1]."-".$row->US_FName."-".date("d.M.y",strtotime($row->MSG_CreatedOn))."<br>";
            $count++;
        }
        $this->MsgString=$string;
    }
    function getBSMessageCount($id){
        $selectSQL="SELECT COUNT(Msg_Id) AS MSG_COUNT FROM entry_messages WHERE BS_Id=".$id;
        $row=mysqli_fetch_object(mysqli_query($GLOBALS['con'], $selectSQL));  
        return $row->MSG_COUNT;
    }
    /* Function for getting value of a field based on a condition */
    function getFieldValue($field, $filter) {
        $query = mysqli_query($GLOBALS['con'],"SELECT $field FROM user_messages WHERE $filter");
        return mysqli_fetch_array($query,MYSQLI_ASSOC);
    }
    
    /* Function for sending reply to a message */
    function sendReply()
    {
        $insertSql = "INSERT INTO user_messages ( " . implode(', ', array_keys($this->messageReply)) . ") VALUES (" . "'" . implode("','", array_values($this->messageReply)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$insertSql);
        if(mysqli_affected_rows($GLOBALS['con']) > 0) {
            return "success";
        }
        else {
            return "fail";
        }
    }
    
    /* Function to view all messages received by currently logged in user */
    function viewReceivedMessages($UId)    // Messages send by logged in user
    {
        $count=0; 
        $this->ReceivedArray = array();
        $query = mysqli_query($GLOBALS['con'],"SELECT 
                m.*,u.US_FName,u.US_LName,t.MT_Type,d.DP_Name  
                FROM user_messages m LEFT JOIN users_auth u ON m.MSG_From=u.US_Id 
                LEFT JOIN user_message_types t ON m.MT_Id=t.MT_Id 
                LEFT JOIN departments d ON u.DP_Id=d.DP_Id 
                WHERE m.MSG_To=$UId and MSG_Status = 1  
                ORDER BY m.MSG_CreatedOn DESC");
        while($row = mysqli_fetch_object($query)) {
            $this->ReceivedArray[$count] = $row;
            $count++;
        }	
    }
    
    /* Function to view all messages sent by currently logged in user */
    function viewSentMessages($UId)
    {
        $count=0; 
        $this->SentArray = array();
        
        
        /*$query = mysqli_query($GLOBALS['con'],"SELECT 
                m.*,u.US_FName,u.US_LName,t.MT_Type,d.DP_Name  
                FROM user_messages m LEFT JOIN users_auth u ON m.MSG_To=u.US_Id 
                LEFT JOIN user_message_types t ON m.MT_Id=t.MT_Id 
                LEFT JOIN departments d ON u.DP_Id=d.DP_Id 
                WHERE m.MSG_From=$UId 
                ORDER BY m.MSG_CreatedOn DESC");*/
        $query = mysqli_query($GLOBALS['con'],"SELECT 
                m.*,u.US_FName,u.US_LName,t.MT_Type,d.DP_Name  
                FROM user_messages m LEFT JOIN users_auth u ON m.MSG_To=u.US_Id 
                LEFT JOIN user_message_types t ON m.MT_Id=t.MT_Id 
                LEFT JOIN departments d ON u.DP_Id=d.DP_Id 
                WHERE m.MSG_From=$UId and MSG_Status = 1 GROUP BY  `MSG_Code`  
                ORDER BY m.MSG_CreatedOn DESC");
        
        while($row = mysqli_fetch_object($query)) {
            $this->SentArray[$count] = $row;
            $count++;
        }
    }
    
    /* Function for viewing user deleted messages in trash */
    function viewTrashMessages($UId)
    {
        $count=0; 
        $this->TrashArray = array();
        $query = mysqli_query($GLOBALS['con'],"SELECT 
                m.*,u.US_FName,u.US_LName,t.MT_Type,d.DP_Name  
                FROM user_messages m LEFT JOIN users_auth u ON m.MSG_From=u.US_Id 
                LEFT JOIN user_message_types t ON m.MT_Id=t.MT_Id 
                LEFT JOIN departments d ON u.DP_Id=d.DP_Id 
                WHERE m.MSG_To=$UId AND MSG_Status=0  
                ORDER BY m.MSG_CreatedOn DESC");
        while($row = mysqli_fetch_object($query)) {
            $this->TrashArray[$count] = $row;
            $count++;
        }	
    }
    
    /* Function to get all message types */
    function getMessageTypes()
    {
        $count=0; 
        $this->TypeArray = array();
        $query = mysqli_query($GLOBALS['con'],"SELECT * FROM user_message_types ORDER BY MT_Type ASC");
        while($row = mysqli_fetch_object($query)) {
            $this->TypeArray[$count] = $row;
            $count++;
        }
    }
    
    function getRecipients($code, $type)
    {
        $count=0; 
        $this->RecpArray = array();
        if($type == "total") {
            $query = mysqli_query($GLOBALS['con'],"SELECT 
                u.US_FName,u.US_LName,d.DP_Name,l.LC_Name   
                FROM user_messages m LEFT JOIN users_auth u ON m.MSG_To=u.US_Id 
                LEFT JOIN departments d ON u.DP_Id=d.DP_Id 
                LEFT JOIN locations l ON u.LC_Id=l.LC_Id 
                WHERE m.MSG_Code=$code ORDER BY US_FName asc");
        }
        else if($type == "viewed") {
            $query = mysqli_query($GLOBALS['con'],"SELECT 
                u.US_FName,u.US_LName,d.DP_Name,l.LC_Name  
                FROM user_messages m LEFT JOIN users_auth u ON m.MSG_To=u.US_Id 
                LEFT JOIN departments d ON u.DP_Id=d.DP_Id 
                LEFT JOIN locations l ON u.LC_Id=l.LC_Id 
                WHERE m.MSG_Code=$code AND MSG_ReadStatus=1 ORDER BY US_FName asc");
        }
        $toArray = array();
        while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)) {
            $this->RecpArray[$count] = $row;
            $count++;
        }
        
        //return implode (", ", $toArray);
    }
    
    function formatDate($date)
    {
        return date('d-m-Y h:i a', strtotime($date)); 
    }
    
    function updateFieldValue($table,$filter,$value)
    {
        mysqli_query($GLOBALS['con'],"UPDATE $table SET $value WHERE $filter");
    }
    
    /* Function for removing unwanted characters or spaces */
    function cleanData($data)
    {
       $data    =   trim(strip_tags(htmlspecialchars($data, ENT_QUOTES)));
       $data    =   mysqli_real_escape_string($GLOBALS['con'],$data);
       return $data;
    }
}