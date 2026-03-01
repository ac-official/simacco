<?php 
require_once("connection.php");
class InventoryClass {
    var $InvItemReadArray;        
    var $PatternReadArray;  
     function AddInventoryItem()
        {
            $sql = "INSERT INTO inventory_items ( " . implode(', ',array_keys($this->InvItemArray)) . ") VALUES (" . "'" . implode("','", array_values($this->InvItemArray)) . "'" . ")";
	    mysqli_query($GLOBALS['con'],$sql);
            if(mysqli_insert_id($GLOBALS['con'])) {
                return "Item Added Successfully";
            } else {
                return "fail";
            }
		
        }
     function updateInventoryItem($Id){
		$InvData = '';
		foreach ($this->InvItemArray as $key=>$value){ 
			$InvData = $InvData .$key ."='".$value."', ";
		}
		$InvData = substr($InvData, 0, -2);
		$sql = "UPDATE inventory_items SET $InvData WHERE INV_ItemID=$Id";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Item Updated Successfully';
	}  
        
    function listInventoryItems($filter){
        $count=0; 
        $this->InvItemReadArray = array(); 
        $sql="SELECT IT.INV_ItemID,IT.INV_ItemName,IT.INV_ItemModel,IT.INV_TypeID,IT.INV_ItemQty,IType.INV_TypeName,IT.INV_ItemAssignee,IT.INV_ItemRemarks,CONCAT(US.US_FName,' ',US.US_LName) AS Assignee,IT.INV_ItemStatus,IT.INV_ItemRemarks,IT.INV_PurchaseDate,CONCAT(US2.US_FName,' ',US2.US_LName) AS AddedBy,LC.LC_Name,IT.LC_Id FROM inventory_items AS IT "
                . " LEFT JOIN inventory_types AS IType ON IType.INV_TypeID=IT.INV_TypeID"
                . " LEFT JOIN users_auth AS US ON IT.INV_ItemAssignee=US.US_Id"
                . " LEFT JOIN locations AS LC ON IT.LC_Id=LC.LC_Id"
                . " LEFT JOIN users_auth AS US2 ON IT.INV_AddedBy = US2.US_Id ".$filter;
        $result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->InvItemReadArray[$count]=$row;
			$count++;
		}
    }
    function AddInventoryType()
        {
            $sql = "INSERT INTO inventory_types ( " . implode(', ',array_keys($this->InvTypeArray)) . ") VALUES (" . "'" . implode("','", array_values($this->InvTypeArray)) . "'" . ")";
	    mysqli_query($GLOBALS['con'],$sql);
            if(mysqli_insert_id($GLOBALS['con'])) {
                return "Item Type Added Successfully";
            } else {
                return "fail";
            }
		
        }
    function listInventoryTypes(){
        $count=0; 
        $this->InvItemReadArray = array(); 
        $sql="SELECT INV_TypeID,INV_TypeName FROM inventory_types";
        $result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->InvItemReadArray[$count]=$row;
			$count++;
		}
    }
    function listUpdateInventoryItems($filter,$InvId){
        $count=0; 
        $this->InvItemEditReadArray = array(); 
        $sql="SELECT IT.INV_ItemID,IT.INV_ItemName,IT.INV_ItemModel,IT.INV_ItemQty,IType.INV_TypeName,CONCAT(US.US_FName,' ',US.US_LName) AS Assignee,IT.INV_ItemStatus,IT.INV_ItemRemarks,IT.INV_PurchaseDate,CONCAT(US2.US_FName,' ',US2.US_LName) AS AddedBy FROM inventory_items AS IT "
                . " LEFT JOIN inventory_types AS IType ON IType.INV_TypeID=IT.INV_TypeID"
                . " LEFT JOIN users_auth AS US ON IT.INV_ItemAssignee=US.US_Id"
                . " LEFT JOIN users_auth AS US2 ON IT.INV_AddedBy = US2.US_Id ".$filter ." AND INV_ItemID =".$InvId;
        $result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->InvItemEditReadArray[$count]=$row;
			$count++;
		}
    }
    function updateInventoryItems($InvId){

    }
    function listInventoryPatternItems($filter=""){
        $count=0; 
        $this->InvItemReadArray = array(); 
        $sql="SELECT INV_Id,INV_Title FROM inventory_pattern_items ".$filter." ORDER BY INV_Title ASC";
        $result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->InvItemReadArray[$count]=$row;
			$count++;
		}
    }
    function newPatternList() {
        $sql = "INSERT INTO inventory_pattern_list ( " . implode(', ',array_keys($this->PL_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->PL_Data)) . "'" . ")"; 
        mysqli_query($GLOBALS['con'],$sql);
        return 'New Pattern Set Successfully';
    }
    function mapPatternList($SHId) {
        $PLData = '';
        foreach ($this->PL_Data as $key=>$value){ 
            $PLData = $PLData .$key ."='".$value."', ";
        }
        $PLData = substr($PLData, 0, -2);
        $sql = "UPDATE inventory_pattern_list SET $PLData WHERE INVPType_Id = ".$SHId; 
        mysqli_query($GLOBALS['con'],$sql);
        return 'Pattern Updated Successfully';
    }
    function verifyPatternList($SHId){
		$sql = 'SELECT COUNT(INVP_PatternMap) FROM  inventory_pattern_list WHERE INVPType_Id = "'.$SHId.'"';
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
                        return false;	
		}
	}
    function getInvTypePattern($typeID){
        $this->PatternReadArray = array(); 
        $sql="SELECT INVP_PatternMap FROM  inventory_pattern_list WHERE INVPType_Id=".$typeID;
        $result=mysqli_query($GLOBALS['con'],$sql);
        $this->PatternReadArray=  mysqli_fetch_assoc($result);
    }    
}

?>