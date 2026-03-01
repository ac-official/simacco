<?php
require_once("connection.php");
class UnusedItemDescClass
{
    function unusedDesc($filter_mask,$start,$end, $preTally_user_ofid) {
        $count=0;
        $this->DescArray = array();
        $sql = 'SELECT DISTINCT(DS.DS_Id) as DS_Id , DS.DS_Description, BS.BS_Id, IT.IT_Name 
                FROM `descriptions` AS DS 
                LEFT JOIN balance_sheets AS BS ON BS.BS_Description = DS.DS_Id AND BS.BS_Status != 0  
                LEFT JOIN items AS IT ON IT.IT_Id = DS.IT_Id 
                WHERE (DS_Status = 2 || DS_Status = 3) AND 
                DS.OF_Id = '.$preTally_user_ofid.' AND    
                BS.BS_Description IS NULL '. $filter_mask.' 
                ORDER BY DS.DS_Description LIMIT ' .$start.', '.$end;
        $result=mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
            $this->DescArray[$count]=$row;
            $count++;
        }	
    }
    
    function unusedDescCount($filter_mask,$preTally_user_ofid) {
        $count=0;
        $sql = 'SELECT COUNT(DISTINCT(DS.DS_Id)) AS count  
                FROM `descriptions` AS DS 
                LEFT JOIN balance_sheets AS BS ON BS.BS_Description = DS.DS_Id AND BS.BS_Status != 0  
                LEFT JOIN items AS IT ON IT.IT_Id = DS.IT_Id 
                WHERE  (DS_Status = 2 || DS_Status = 3) AND 
                DS.OF_Id = '.$preTally_user_ofid.' AND   
                BS.BS_Description IS NULL  '. $filter_mask;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row    = mysqli_fetch_array($result,MYSQLI_NUM);
        return $row[0];
    }
    
    function unusedItem($filter_mask,$start,$end,$preTally_user_ofid) {
        $count=0;
        $this->ItemArray = array();
        $sql = 'SELECT DISTINCT(IT.IT_Id) as IT_Id, IT.IT_Name, BS.BS_Id,SH.SH_Name, MH.MH_Name FROM `items` AS IT  
                LEFT JOIN `balance_sheets` AS BS ON BS.IT_Id = IT.IT_Id AND BS.BS_Status != 0 
                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                WHERE  (IT_Status = 2 || IT_Status = 3) AND  
                IT.OF_Id = '.$preTally_user_ofid.' AND  
                BS.IT_Id IS NULL '. $filter_mask
                .' ORDER BY IT.IT_Name ASC LIMIT '.$start.','.$end;
        $result=mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
            $this->ItemArray[$count]=$row;
            $count++;
        }
    }
    
    function unusedItemCount($filter_mask,$preTally_user_ofid) {
        $count=0;
        $sql = 'SELECT COUNT(DISTINCT(IT.IT_Id)) FROM `items` AS IT  
                LEFT JOIN `balance_sheets` AS BS ON BS.IT_Id = IT.IT_Id AND BS.BS_Status != 0  
                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id 
                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id 
                WHERE  (IT_Status = 2 || IT_Status = 3) AND 
                IT.OF_Id = '.$preTally_user_ofid.'  AND    
                BS.IT_Id IS NULL '.$filter_mask;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row    = mysqli_fetch_array($result,MYSQLI_NUM);
        return $row[0];
    }
    
    function updateItem($preTally_user_ofid, $condition, $IT_Id) {
        $ITData = '';
        foreach ($this->IT_Data as $key=>$value){ 
            $ITData = $ITData .$key ."='".$value."', ";
        }
        $ITData = substr($ITData, 0, -2);
        $checkQuery = mysqli_query($GLOBALS['con'],"SELECT COUNT(*) as count FROM items WHERE 
                    IT_Name = '".$this->IT_Data['IT_Name']."' AND 
                    OF_Id = ".$preTally_user_ofid." AND IT_Id != $IT_Id");
        $checkResult = mysqli_fetch_array($checkQuery,MYSQLI_ASSOC);
        if($checkResult['count'] > 0)  {
            return "exists";
        }
        else {
            $sql = "UPDATE items SET $ITData $condition";
            mysqli_query($GLOBALS['con'],$sql);
            if(mysqli_affected_rows($GLOBALS['con']) > 0)
                return 'success';
        }
    }
    
    function updateDesc($preTally_user_ofid, $condition, $DS_Id) {
        $DSData = '';
        foreach ($this->DS_Data as $key=>$value){ 
            $DSData = $DSData .$key ."='".$value."', ";
        }
        $DSData = substr($DSData, 0, -2);
        $checkQuery = mysqli_query($GLOBALS['con'],"SELECT COUNT(*) as count FROM descriptions WHERE 
                    DS_Description = '".$this->DS_Data['DS_Description']."' AND 
                    OF_Id = ".$preTally_user_ofid." AND DS_Id != $DS_Id");
        $checkResult = mysqli_fetch_array($checkQuery,MYSQLI_ASSOC);
        if($checkResult['count'] > 0)  {
            return "exists";
        }
        else {
            $sql = "UPDATE descriptions SET $DSData $condition";
            mysqli_query($GLOBALS['con'],$sql);
            if(mysqli_affected_rows($GLOBALS['con']) > 0)
                return 'success';
        }
    }
    
    function deleteUnused($table, $condition, $field) {
        $sql = "UPDATE $table SET $field = 4". $condition;
        mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_affected_rows($GLOBALS['con']) > 0)
            return 'success';
    }
    
    /* Function for removing unwanted characters or spaces */
    function cleanData($data)
    {
       $data    =   trim(strip_tags(htmlspecialchars($data, ENT_QUOTES)));
       $data    =   mysqli_real_escape_string($GLOBALS['con'],$data);
       return $data;
    }
}
