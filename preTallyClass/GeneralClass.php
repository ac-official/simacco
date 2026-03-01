<?php
require_once("connection.php");
class GeneralClass
{
	var $DataArray;

	function ViewDetails($field="*",$table,$filter='1',$order='',$limit='0, 2000000')
	{
		//die('SELECT '.$field.' FROM '.$table.' WHERE '.$filter.' ORDER BY '.$order.' LIMIT '.$limit);
		$detls = mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM '.$table.' WHERE '.$filter.' ORDER BY '.$order.' LIMIT '.$limit);
		$i=0;
		while($rw=mysqli_fetch_object($detls)) {
			$this->DataArray[$i]=$rw;
			$i++;
		}
		ob_clean();
	}
	
	function Count($field="*",$table,$filter='1')
	{
		$count = mysqli_query($GLOBALS['con'],'SELECT COUNT('.$field.') AS count FROM '.$table.' WHERE '.$filter);
		if($row=mysqli_fetch_array($count,MYSQLI_ASSOC))
		{
			return $row['count'];
		}
	}
	function Update($table,$field,$condn)
	{
		//die('UPDATE '.$table.' SET '.$field.' WHERE '.$condn);
		mysqli_query($GLOBALS['con'],'UPDATE '.$table.' SET '.$field.' WHERE '.$condn);
	}
	function Insert($table,$field,$values)
	{
		//die('INSERT INTO '.$table.' ( '.$field.' ) VALUES ( '.$values.' ) ');
		mysqli_query($GLOBALS['con'],'INSERT INTO '.$table.' ( '.$field.' ) VALUES ( '.$values.' ) ');
	}
	
	//------------------------- First Row CDB Details -----------------------------------
	function firstRow($field, $table, $status)
	{
		//die('SELECT MIN('.$field.') FROM `'.$table.'` WHERE '.$status.'=1');
		$result = mysqli_query($GLOBALS['con'],'SELECT MIN('.$field.') FROM `'.$table.'` WHERE '.$status.'=1');
		return mysqli_fetch_array($result,MYSQLI_ASSOC);
	}
	//------------------------- Previous Row CDB Details -----------------------------------
	function previousRow($field, $table, $status, $order, $current_id)
	{
		$result = mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM `'.$table.'` WHERE '.$status.'=1 AND '.$field.' < '.$current_id.' ORDER BY '.$field.' DESC LIMIT 1');
		return mysqli_fetch_array($result,MYSQLI_ASSOC);
	}
	//------------------------- Last Row CDB Details -----------------------------------
	function lastRow($field, $table, $status)
	{
		$result = mysqli_query($GLOBALS['con'],'SELECT MAX('.$field.') FROM `'.$table.'` WHERE '.$status.'=1');
		return mysqli_fetch_array($result,MYSQLI_ASSOC);
	}
	//------------------------- Next Row CDB Details -----------------------------------
	function nextRow($field, $table, $status, $order, $current_id)
	{
		$result = mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM `'.$table.'` WHERE '.$status.'=1 AND '.$field.' > '.$current_id.' ORDER BY '.$field.' ASC LIMIT 1');
		return mysqli_fetch_array($result,MYSQLI_ASSOC);
	}
        //----------------------------------------- Get States -----------------------------------------------------//
        function GetStates($field="*",$table,$filter="",$order='')
	{
		//die('SELECT '.$field.' FROM '.$table.' WHERE  CN_Id='.$filter.' ORDER BY '.$order);
		$detls = mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM '.$table.' WHERE CN_Id='.$filter.' ORDER BY '.$order);
		$i=0;
		while($rw=mysqli_fetch_object($detls))
		{
			$this->DataArray[$i]=$rw;
			$i++;
		}
        }
        //----------------------------------------- Get Cities  -----------------------------------------------------//
        function GetCities($field="*",$table,$filter="",$order='')
	{
		//die('SELECT '.$field.' FROM '.$table.' WHERE  ST_Id='.$filter.' ORDER BY '.$order);
		$detls = mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM '.$table.' WHERE ST_Id='.$filter.' ORDER BY '.$order);
		$i=0;
		while($rw=mysqli_fetch_object($detls))
		{
			$this->DataArray[$i]=$rw;
			$i++;
		}
        }
        
        function GetCityFiltr($field,$table,$stname,$order)
         {
		
		$detls = mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM '.$table.' WHERE CT_Name LIKE "'.$stname.'%" ORDER BY '.$order);
		$i=0;
		while($rw=mysqli_fetch_object($detls))
		{
			$this->DataArray[$i]=$rw;
			$i++;
		}
        }
        function GetValues($field="*",$table,$label="",$filter="",$order="")
	{
		
		$detls = mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM '.$table.' WHERE '.$label.'='.$filter.' ORDER BY '.$order);
		$i=0;
		while($rw=mysqli_fetch_object($detls))
		{
			$this->DataArray[$i]=$rw;
			$i++;
		}
        }
        function getComboDetails($fields,$table,$filter='1',$order='',$limit='0, 200'){
                $detls = mysqli_query($GLOBALS['con'],'SELECT '.$fields.' FROM '.$table.' WHERE '.$filter.' ORDER BY '.$order.' LIMIT '.$limit);
		$i=0;
		while($rw=mysqli_fetch_object($detls))
		{
			$this->DataArray[$i]=$rw;
			$i++;
		}
        }
        function unionValues($fields,$table,$filt) {
            //echo "(SELECT ".$fields." FROM $table WHERE ".$filt;exit;
            $detls = mysqli_query($GLOBALS['con'],"(SELECT ".$fields." FROM $table WHERE ".$filt);
            $i = 0;
            while($rw=mysqli_fetch_object($detls))
            {
                $this->DataArray[$i]=$rw;
                $i++;
            }
        }
        
        function getValue($table, $fields, $condition) {               
          $result = mysqli_query($GLOBALS['con'],"SELECT $fields FROM $table " . $condition);
            $row = mysqli_fetch_array($result,MYSQLI_NUM);            
            return $row[0];
        }       
        function insertReturnId($table) {  // insert to table and return last insert id
	       $sql = "INSERT INTO $table ( " . implode(', ', array_keys($this->InsertData)) . ") VALUES (" . "'" . implode("','", array_values($this->InsertData)) . "'" . ")";
	       mysqli_query($GLOBALS['con'],$sql);
	       return mysqli_insert_id($GLOBALS['con']);
	    }
    function clean($string) {   
   return preg_replace('/[^A-Za-z0-9\-\s]/', '', $string);
    }
}
?>