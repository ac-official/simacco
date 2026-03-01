<?php
require_once("connection.php");

class ItemClass
{
	var $ItemArray;
        var $ItemMapArray;
        var $ITStatus;
        var $ItemVArray;
		
	//----------------------------------------- All Items ----------------------------------------//
	function viewItems($fields="*",$filt='',$flag)
	{		
		$count=0;
		$this->ItemArray = array();
                //die("SELECT ".$fields." FROM items ".$filt);
                if($flag) {  // union query; Master Reports->Branch based item reports
                    $result=mysqli_query($GLOBALS['con'],"(SELECT ".$fields." FROM items ".$filt);
                } else {
                    $result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM items ".$filt);
                }
                
		while($row=mysqli_fetch_object($result)) {
			$this->ItemArray[$count]=$row;
			$count++;
		}	
	}
        
        //----------------------------------------- Pretally Defined Items ----------------------------------------//
	function viewPretallyItems($filt='')
	{	
		$count=0;
		$this->ItemArray = array();
        // IT.IT_OtherUser field added at 28-05-2025
		$result=mysqli_query($GLOBALS['con'],"SELECT IT.IT_Id,IT.IT_Name,IT.SH_Id,IT.IT_Comments, IT.IT_Business,IT.IT_DualEntry,IT.IT_DualItem, IT.OF_Id, IT.IT_Status, SH.SH_Name, MH.MH_Type, MH.MH_Name, IT.IT_OtherUser
                                        FROM items  AS IT
                                        LEFT JOIN sub_heads AS SH ON SH.SH_Id=IT.SH_Id
                                        LEFT JOIN main_heads AS MH ON SH.MH_Id=MH.MH_Id 
                                            WHERE ".$filt."
                                                ORDER BY IT.IT_Name");
		while($row=mysqli_fetch_object($result)) {
			$this->ItemArray[$count]=$row;
			$count++;
		}	
	}
        
        //----------------------------------------- Pretally Defined Items Count ----------------------------------------//
	function viewPretallyItemsCount($filt='')
	{	
		$count=0;
		$this->ItemArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT COUNT(IT.IT_Id)
                                        FROM items  AS IT
                                        LEFT JOIN sub_heads AS SH ON SH.SH_Id=IT.SH_Id
                                        LEFT JOIN main_heads AS MH ON SH.MH_Id=MH.MH_Id 
                                            WHERE ".$filt."
                                                ORDER BY IT.IT_Name");
		$row = mysqli_fetch_array($result,MYSQLI_NUM);
                return $count=$row[0];	
	}
        
        //----------------------------------------- Other Company Defined Items ----------------------------------------//
	function viewOtherItems($filter)
	{	
		$count=0;
		$this->ItemArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT IT.IT_Id,IT.IT_Name,IT.SH_Id,IT.IT_Comments, IT.OF_Id, IT.IT_Status, SH.SH_Name, MH.MH_Type, MH.MH_Name, OF.OF_Name
                                        FROM items  AS IT
                                        LEFT JOIN sub_heads AS SH ON SH.SH_Id=IT.SH_Id
                                        LEFT JOIN main_heads AS MH ON SH.MH_Id=MH.MH_Id 
                                        LEFT JOIN offices AS OF ON OF.OF_Id=IT.OF_Id 
                                            WHERE ".$filter."
                                                ORDER BY IT.IT_Name");
		while($row=mysqli_fetch_object($result)) {
			$this->ItemArray[$count]=$row;
			$count++;
		}	
	}
        //----------------------------------------- Other Company Defined Items Count----------------------------------------//
	function viewOtherItemsCount($filter)
	{	
		$count=0;
		$this->ItemArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT COUNT(IT.IT_Id)
                                        FROM items  AS IT
                                        LEFT JOIN sub_heads AS SH ON SH.SH_Id=IT.SH_Id
                                        LEFT JOIN main_heads AS MH ON SH.MH_Id=MH.MH_Id 
                                        LEFT JOIN offices AS OF ON OF.OF_Id=IT.OF_Id 
                                            WHERE ".$filter."
                                                ORDER BY IT.IT_Name");
		$row = mysqli_fetch_array($result,MYSQLI_NUM);
                return $count=$row[0];	
	}
        
	//----------------------------------------- All Items ----------------------------------------//
	function getItemList($fields='*',$tbls='',$filt='')
	{	
		$count=0;
		$this->ItemArray = array();     
               //print("SELECT ".$fields." FROM items ".$tbls."  ".$filt);
		$result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM items ".$tbls."  ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->ItemArray[$count]=$row;
			$count++;
		}	
	}
	//----------------------------------------- Verify Item ----------------------------------------//
    // GROUP BY IT_Id is added at 29-05-2025 bilin
	function verifyItem($ITId,$itemMap=''){
                $sql = 'SELECT COUNT(IT_Id), IT_Id, IT_Name, IT_DualEntry, SH_Id, MH_Type,IT_MinAmount,IT_MaxAmount,IT_PettyCash, IT_Status FROM items WHERE IT_Name = "'.$this->IT_Data['IT_Name'].'" AND IT_Id != '.$ITId.' AND IT_Status != 4 AND (OF_Id ='.$this->IT_Data['OF_Id'].' OR IT_Id IN ('.$itemMap.')) GROUP BY IT_Id';
		$result = mysqli_query($GLOBALS['con'],$sql);                
		$row= mysqli_fetch_assoc($result);
                if($row['COUNT(IT_Id)'] != 0){
                    $this->ITStatus = $row['IT_Status'];
                    $this->ItemVArray =  $row;
//                    $this->ItemVArray['IT_Status'] = $row['IT_Status'];
//                    $this->ItemVArray['SH_Id']     = $row['SH_Id'];
//                    $this->ItemVArray['MH_Type']   = $row['MH_Type'];
//                    $this->ItemVArray['IT_DualEntry'] = $row['IT_DualEntry'];
//                    $this->ItemVArray['IT_MinAmount'] = $row['IT_MinAmount'];
//                    $this->ItemVArray['IT_MaxAmount'] = $row['IT_MaxAmount'];
                    return $row['IT_Id'];
		} else {
                    return false;
		}
	}
	
	//----------------------------------------- New Item ----------------------------------------//
	function newItem(){
		
		$sql = "INSERT INTO items ( " . implode(', ',array_keys($this->IT_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->IT_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
		return 'Item Created Successfully';

	}
	//----------------------------------------- Update Item ----------------------------------------//
	function updateItem($ITId){
		$ITData = '';
		foreach ($this->IT_Data as $key=>$value){ 
			$ITData = $ITData .$key ."='".$value."', ";
		}
		$ITData = substr($ITData, 0, -2);
		$sql = "UPDATE items SET $ITData WHERE IT_Id=$ITId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Item Updated Successfully';
	}
        
        function newBalSheetItem(){
		
		$sql = "INSERT INTO items ( " . implode(', ',array_keys($this->IT_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->IT_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
		return mysqli_insert_id($GLOBALS['con']);

	}
        //----------------------------------------- Verify Item ----------------------------------------//
	function verifyMapItem($OFId){
		$sql = 'SELECT COUNT(IC_Id) FROM items_company WHERE OF_Id = "'.$this->IT_Data['OF_Id'].'"';
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
                        return false;	
		}
	}
        //------------------------------------- Map New Item to Company --------------------------------------------//
        function newMapItemCompany() {
            $sql = "INSERT INTO items_company ( " . implode(', ',array_keys($this->IT_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->IT_Data)) . "'" . ")"; 
            mysqli_query($GLOBALS['con'],$sql);
            return 'Map Items Inserted Successfully';
        }
        function mapItemCompany($OFId) {
            $ITData = '';
            foreach ($this->IT_Data as $key=>$value){ 
                $ITData = $ITData .$key ."='".$value."', ";
            }
            $ITData = substr($ITData, 0, -2);
            $sql = "UPDATE items_company SET $ITData WHERE OF_Id = ".$OFId; 
            //return $sql;
            mysqli_query($GLOBALS['con'],$sql);
            return 'Map Items Updated Successfully';
        }
        function myMapItem($OFId){
            $count=0; 
            $this->ItemMapArray = array();
            $sql = 'SELECT IC_Map FROM items_company WHERE OF_Id = '.$OFId;
            $result = mysqli_query($GLOBALS['con'],$sql);
            while($row=mysqli_fetch_object($result)) {
                $this->ItemMapArray[$count] = $row;
                $count++;
            }
	}
        function mapItemPreTally($ITId,$USId){
            
            $sql = 'SELECT OF_Id, IT_Name, IT_Comments, SH_Id, MH_Type FROM items WHERE IT_Id = '.$ITId;
            $result = mysqli_query($GLOBALS['con'],$sql);
            $OFId = mysqli_fetch_row($result);
            $sql = 'INSERT INTO items ( US_Id, OF_Id, OF_Id_Alias, SH_Id, MH_Type,  IT_Name, IT_Comments, IT_MDate, IT_CDate, IT_Status ) VALUES 
                   ( "' .$USId. '", "1", "' .$OFId[0]. '", "' .$OFId[3]. '", "' .$OFId[4]. '", "' .$OFId[1]. '", "' .$OFId[2]. '", "' .date('Y-m-d H:i:s'). '", "' .date('Y-m-d H:i:s'). '", 1)';
            //return $sql;
            mysqli_query($GLOBALS['con'],$sql);
            
            $sql = "UPDATE items SET OF_Id_Alias=1, IT_Approval=0 WHERE IT_Id = ".$ITId; 
            //return $sql;
            mysqli_query($GLOBALS['con'],$sql);
            
            return 'Item Mapped To PreTally Successfully';
            }
            
        function mapOtherCompanyItem($ITId){
            
            $sql = "INSERT INTO items ( " . implode(', ',array_keys($this->IT_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->IT_Data)) . "'" . ")";
            mysqli_query($GLOBALS['con'],$sql);
            
            $sql = "UPDATE items SET OF_Id_Alias=1, IT_Approval=0 WHERE IT_Id = ".$ITId; 
            //return $sql;
            mysqli_query($GLOBALS['con'],$sql);
            
            return 'Item Mapped To PreTally Successfully';
            }
	function getReportingTree($USId,$rptPntTree){
            $sql = 'SELECT US_Report FROM users_auth WHERE US_Id = '.$USId;
            $result = mysqli_fetch_array(mysqli_query($GLOBALS['con'],$sql),MYSQLI_NUM);
            
            array_push($rptPntTree, $result[0]);
            if( $result[0] != 1){
                return $this->getReportingTree($result[0],$rptPntTree);
            } else {
                return mysqli_real_escape_string($GLOBALS['con'],serialize($rptPntTree));
            }
        }
        function approveItem($USId,$ITId,$MyId) {
            $sql = "UPDATE items SET IT_Approval = ".$USId." , IT_Approved = ".$MyId." WHERE IT_Id = ".$ITId; 
            //return $sql;
            mysqli_query($GLOBALS['con'],$sql);
            return 'Item Approved Successfully';
        }
        function getItemCount($filter){
            $count=0; 
            $this->ItemArray = array();
            $sql = 'SELECT count(IT.IT_Id) FROM items as IT, sub_heads AS SH, main_heads as MH  WHERE '.$filter;
            $result = mysqli_query($GLOBALS['con'],$sql);
            $row = mysqli_fetch_array($result,MYSQLI_NUM);
            $count=$row[0];
            return $count;
	}
        	
	//----------------------------------------- Verify Subhead ----------------------------------------//
	function verifySubhead($SHId){
		$sql = 'SELECT COUNT(SH_Id) FROM sub_heads WHERE SH_Id = "'.$SHId.'"';
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return 0;
		} else {
			return true;	
		}
	}
        	//----------------------------------------- New Item ----------------------------------------//
	function newCompanyItem(){
		$sql = "INSERT INTO items ( " . implode(', ',array_keys($this->IT_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->IT_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
                $ITId=mysqli_insert_id($GLOBALS['con']);
                return $ITId;
	}
        
        //---------------------------------- Item / Description Custom Item list -------------------------------------//
	function viewCustomItems($filt='',$filter,$start,$end, $ITAdded='' , $DSAdded='')
	{			
		$count=0;
        // IT.IT_OtherUser added at the select section 28-05-2025
		$this->ItemArray = array();
                $sql = 'SELECT IT.IT_Id, IT.US_Id, IT.IT_Name, IT.IT_Status, IT.IT_Comments, IT.IT_Approved, IT.IT_Approval, IT.IT_Business, IT.IT_Transfers,IT.IT_PettyCash, IT.IT_CDate, IT.SH_Id, IT.MH_Type,IT_DualEntry,IT_DualItem,IT.IT_MinAmount,IT.IT_MaxAmount,
                            DS.DS_Id, DS.DS_Description,DS.DS_Status,SH.SH_Name, MH.MH_Id, MH.MH_Name, IT.IT_OtherUser 
                                FROM `items`  AS IT
                                    LEFT JOIN `descriptions` AS DS ON  DS.DS_Id = ( SELECT
      					DS_Id FROM descriptions WHERE descriptions.IT_Id  = IT.IT_Id  AND descriptions.DS_Status = 1 LIMIT 1 )
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                            WHERE '.$filt . $filter.' ORDER BY IT.IT_Name LIMIT ' .$start.', '.$end;
               	$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->ItemArray[$count]=$row;
			$count++;
		}	
	}
        //---------------------------------- Item / Description Custom Item list -------------------------------------//
	function viewCustomItemsCount($filt='',$filter, $ITAdded='' , $DSAdded='')
	{			
		$count=0;
		$this->ItemArray = array();
                $sql = 'SELECT COUNT(IT.IT_Id)
                            FROM `items`  AS IT
                                LEFT JOIN `descriptions` AS DS ON  DS.DS_Id = ( SELECT
      					DS_Id FROM descriptions WHERE descriptions.IT_Id  = IT.IT_Id  AND descriptions.DS_Status = 1 LIMIT 1 )
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                        WHERE '.$filt . $filter;
               	$result = mysqli_query($GLOBALS['con'],$sql);
		$row    = mysqli_fetch_array($result,MYSQLI_NUM);
                return $count=$row[0];		
	}
}

?>