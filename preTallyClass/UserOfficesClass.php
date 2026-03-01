<?php
require_once("connection.php");
/**
 * some users have allow multiple company
 * they can add data on more than one company
 * created by  Bilin @ 28-03-25
*/
class UserOfficesClass
{ 
    var $OfficeArray;

    function getCompanyList($user_id = 0, $status="") 
    {
        $office_list = [];
        $sql    = 'SELECT o.OF_Id, o.OF_Name FROM offices AS o INNER JOIN user_office AS uo ON (uo.OF_Id= o.OF_Id) WHERE uo.US_Id ="'.$user_id.'" AND uo.status="1" ';
        if ($status == 1) {
           $sql .= ' AND o.OF_Status="1"';
        }
        $res    = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($res)) {
            $office_list[$row->OF_Id]=$row->OF_Name;
        } 
        
        return $office_list;
    }



}
?>