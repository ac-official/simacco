<?php
require_once("connection.php");

class NotificationClass {
	var $NotfArray;
	
	
	//----------------------------------------- All Descriptions ----------------------------------------//
	function viewNotifyDescriptions($filt='')
	{			
		$count=0;
		$this->NotfArray = array();                
		$result=mysqli_query($GLOBALS['con'],"SELECT DS.DS_Id, DS.IT_Id, DS.US_Id, DS.DS_Description, DS.DS_Approved,DS.DS_Approval, DS.DS_Status, DS.DS_CDate,DS.DS_MDate, US.US_FName, US.US_LName, IT.IT_Name, IT.IT_Id, IT.MH_Type, IT.SH_Id
                                           FROM descriptions as DS, items as IT, users_auth as US ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->NotfArray[$count]=$row;
			$count++;
		}	
	}
        
        //----------------------------------------- All Items ----------------------------------------//
	function viewNotifyItems($fields="*",$filt='')
	{			
		$count=0;
		$this->NotfArray = array();
		$result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM items ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->NotfArray[$count]=$row;
			$count++;
		}	
	}

        function countNotf($USID) {	
            $sql = "SELECT COUNT(IT_Id) AS CT FROM items WHERE IT_Approval = ".$USID;
            $result = mysqli_query($GLOBALS['con'],$sql);
            $itemCount = mysqli_fetch_assoc($result);
            
            $sql = "SELECT COUNT(DS_Id) AS CT FROM descriptions WHERE DS_Approval = ".$USID;
            $result = mysqli_query($GLOBALS['con'],$sql);
            $desnCount = mysqli_fetch_assoc($result);
           
            //$count = $itemCount['CT'] + $desnCount['CT'];
            return array($itemCount['CT'],$desnCount['CT']);
	}
	function swapItem($ITId){
                $SwpITData = '';
		foreach ($this->IT_MapData as $key=>$value){ 
			$SwpITData = $SwpITData .$key ."='".$value."', ";
		}
                $SwpITData = substr($SwpITData, 0, -2);
		$sql = "UPDATE balance_sheets SET $SwpITData WHERE IT_Id=$ITId";
		mysqli_query($GLOBALS['con'],$sql);
                
                mysqli_query($GLOBALS['con'],"UPDATE items SET IT_Status = 4, IT_Approval = 0 WHERE IT_Id=".$ITId);
                
                mysqli_query($GLOBALS['con'],"UPDATE descriptions SET $SwpITData WHERE IT_Id=$ITId");
		return 'Item Updated Successfully';
        }
        function swapDescription($DSId){
                $SwpDSData = '';
		foreach ($this->DS_MapData as $key=>$value){ 
			$SwpDSData = $SwpDSData .$key ."='".$value."', ";
		}
		$SwpDSData = substr($SwpDSData, 0, -2);
		$sql = "UPDATE balance_sheets SET $SwpDSData WHERE BS_Description=$DSId";
		mysqli_query($GLOBALS['con'],$sql);
                
                mysqli_query($GLOBALS['con'],"UPDATE descriptions SET DS_Status = 4, DS_Approval=0 WHERE DS_Id=".$DSId);
		return 'Description Updated Successfully';
        }
        //----------------------------------------- Verify MAP Description ----------------------------------------//
	function verifyMappedDesc($DSId){
		$sql = 'SELECT COUNT(DS_Id) FROM descriptions WHERE DS_Id = "'.$DSId.'" ';
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count != 0){
			return true;
		} else {
                        return false;
		}
	}
        //----------------------------------------- Verify MAP Item ----------------------------------------//
	function verifyMappedItem($ITId){
		$sql = 'SELECT COUNT(IT_Id) FROM items WHERE IT_Id = "'.$ITId.'" ';
                $result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count != 0){
			return true;
		} else {
                        return false;
		}
	}
        //------------------------------------- Item / Description Notification list ----------------------------------------//
	function viewNotificationItems($filt='',$filter,$start,$end, $ITAdded='' , $DSAdded='')
	{			
		$count=0;
		$this->NotfArray = array();
                $sql ='SELECT BS.BS_Id,BS_Amount,BS.US_Id AS BSUS_Id,BS.LC_Id,BS.BS_Date, IT.IT_Id, IT.US_Id AS ITUS_Id, IT.IT_Name, IT.IT_Status,  IT.IT_Approved, IT.IT_Approval, IT.IT_Business, IT.IT_Transfers, IT.IT_CDate, IT.SH_Id, IT.MH_Type,
                            DS.DS_Id, DS.DS_Description,DS.DS_Status,DS.DS_Approved,DS.DS_Approval,DS.US_Id AS DSUS_Id,
                            SH.SH_Name, MH.MH_Id, MH.MH_Name
                                FROM `balance_sheets`  AS BS
                                    RIGHT JOIN `descriptions` AS DS ON DS.DS_Id = BS.BS_Description
                                    RIGHT JOIN   items as IT ON  DS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                            WHERE ( ( IT.IT_Approval IN  ('.$filt.') AND ( IT_Status = 2 || IT_Status = 3 ) '.$ITAdded.') || ( DS.DS_Approval IN  ('.$filt.') AND ( DS_Status = 2 || DS_Status = 3 ) AND ( IT.IT_Status = 1 || IT_Status = 3 ) '.$DSAdded.' ) ) AND (IT_Status!=0) AND BS.BS_Id != ""  AND BS.BS_Status != 0 '.$filter.' ORDER BY BS.BS_Id LIMIT '.$start.', '.$end;
//                $sql = 'SELECT IT.IT_Id, IT.US_Id, IT.IT_Name,IT.IT_Approved, IT.IT_Comments,IT.IT_Status, IT.IT_Business, IT.IT_Transfers, IT.IT_CDate, IT.SH_Id, IT.MH_Type, US.US_FName, US.US_LName,DS_Description,DS_Status, SH.SH_Name, MH.MH_Name FROM items as IT
//                            LEFT JOIN `descriptions` AS DS ON DS.IT_Id = IT.IT_Id
//                            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
//                            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
//                            LEFT JOIN `users_auth`  AS US ON  IT.US_Id = US.US_Id
//                                WHERE ( IT.IT_Approval IN  ('.$filt.') || DS.DS_Approval IN  ('.$filt.') ) AND (DS_Status = 2 || IT_Status = 2 ) ORDER BY IT.IT_Name';
		$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->NotfArray[$count]=$row;
			$count++;
		}	
	}
        //------------------------------------- Count of Item / Description Notification list ---------------------------------//
        function viewNotificationItemsCount($filt='',$filter,$start,$end,  $ITAdded='' , $DSAdded='')
                {			
		$count=0;
		$this->NotfArray = array();
                $sql ='SELECT COUNT(BS.BS_Id)
                            FROM `balance_sheets`  AS BS
                                RIGHT JOIN `descriptions` AS DS ON DS.DS_Id = BS.BS_Description
                                RIGHT JOIN   items as IT ON  DS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                        WHERE ( ( IT.IT_Approval IN  ('.$filt.') AND ( IT_Status = 2 || IT_Status = 3 ) '.$ITAdded.') || ( DS.DS_Approval IN  ('.$filt.') AND  ( DS_Status = 2 || DS_Status = 3 ) AND ( IT.IT_Status = 1 || IT_Status = 3 ) '.$DSAdded.' ) ) AND (IT_Status!=0) AND BS.BS_Id != "" AND BS.BS_Status != 0 '.$filter;		
                $result=mysqli_query($GLOBALS['con'],$sql);                
		$row = mysqli_fetch_array($result,MYSQLI_NUM);
                return $count=$row[0];	
	}
        //------------------------------------- Item / Description Notification list ----------------------------------------//
	function viewItemsFilter($filt='')
	{			
		$count=0;
		$this->NotfArray = array();
                $sql ='SELECT DISTINCT(IT.IT_Id), IT.IT_Name
                                FROM `items`  AS IT
                                    RIGHT JOIN `descriptions` AS DS ON DS.IT_Id = IT.IT_Id
                                    WHERE ( ( IT.IT_Approval IN  ('.$filt.') AND ( IT_Status = 2 || IT_Status = 3 ) ) || ( DS.DS_Approval IN  ('.$filt.') AND ( DS_Status = 2 || DS_Status = 3 ) AND ( IT.IT_Status = 1 || IT_Status = 3 ) ) ) AND (IT_Status!=0) ORDER BY IT.IT_Name';
		$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->NotfArray[$count]=$row;
			$count++;
		}	
	}
        //------------------------------------- Item / Description Notification list ----------------------------------------//
	function viewPendingItemsFilter($OFId)
	{			
		$count=0;
		$this->NotfArray = array();
                $sql ='SELECT DISTINCT(IT.IT_Id), IT.IT_Name
                            FROM `items`  AS IT
                                RIGHT JOIN `descriptions` AS DS ON DS.IT_Id = IT.IT_Id
                                   WHERE ( IT.SH_Id = 58 || IT.SH_Id = 59 ) AND (IT.IT_Status = 1 AND DS.DS_Status = 1) AND IT.OF_Id = '.$OFId.' ORDER BY IT.IT_Name';
		$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->NotfArray[$count]=$row;
			$count++;
		}	
	}
        //------------------------------------- Item / Description Notification list ----------------------------------------//
	function viewItemsReportFilter($OFId)
	{			
		$count=0;
		$this->NotfArray = array();
                $sql ='SELECT DISTINCT(IT_Id), IT_Name
                            FROM `items`
                                WHERE OF_Id = '.$OFId.' AND IT_Transfers = 0 ORDER BY IT_Name';
		$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->NotfArray[$count]=$row;
			$count++;
		}	
	}
        function viewPendingItems($filter,$start,$end, $ITAdded='' , $DSAdded='')
	{			
		$count=0;
		$this->NotfArray = array();
                $sql ='SELECT BS.BS_Id,BS.LC_Id, BS_Amount,BS.US_Id AS BSUS_Id,BS.LC_Id,IT.IT_Id, IT.US_Id AS ITUS_Id, IT.IT_Name, IT.IT_Status,  IT.IT_Approved, IT.IT_Approval, IT.IT_Business, IT.IT_Transfers, IT.IT_CDate, IT.SH_Id, IT.MH_Type,
                            DS.DS_Id, DS.DS_Description,DS.DS_Status,DS.DS_Approved,DS.DS_Approval,DS.US_Id AS DSUS_Id,
                            SH.SH_Name, MH.MH_Id, MH.MH_Name, LC.LC_Name   
                                FROM `balance_sheets`  AS BS
                                    RIGHT JOIN `descriptions` AS DS ON DS.DS_Id = BS.BS_Description
                                    RIGHT JOIN   items as IT ON  DS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                        WHERE ( SH.SH_Id = 58 || SH.SH_Id = 59 ) AND (IT_Status!=0) AND BS.BS_Id != "" '.$filter.' AND ( ( (IT.IT_Status = 1 OR IT.IT_Status = 3)  '.$ITAdded.' ) AND ( (DS.DS_Status = 1 OR DS.DS_Status = 3 ) '.$DSAdded.') ) AND BS.BS_Status != 0 
                                            ORDER BY BS.BS_Id LIMIT '.$start.', '.$end;
                
		$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->NotfArray[$count]=$row;
			$count++;
		}	
	}
        
        //------------------------------------- Count of Item / Description Notification list ---------------------------------//
        function viewPendingItemsCount($filter,$start,$end, $ITAdded='' , $DSAdded='')
	{			
		$count=0;
		$this->NotfArray = array();
                $sql ='SELECT COUNT(BS.BS_Id)
                            FROM `balance_sheets`  AS BS
                                RIGHT JOIN `descriptions` AS DS ON DS.DS_Id = BS.BS_Description
                                RIGHT JOIN   items as IT ON  DS.IT_Id = IT.IT_Id
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                        WHERE ( SH.SH_Id = 58 || SH.SH_Id = 59 ) AND (IT_Status!=0) AND BS.BS_Id != "" '.$filter.' AND ( ( (IT.IT_Status = 1 OR IT.IT_Status = 3)  '.$ITAdded.' ) AND ( (DS.DS_Status = 1 OR DS.DS_Status = 3 ) '.$DSAdded.') ) AND BS.BS_Status != 0 
                                            ORDER BY BS.BS_Id';
		$result=mysqli_query($GLOBALS['con'],$sql);
		$row = mysqli_fetch_array($result,MYSQLI_NUM);
                return $count=$row[0];	
	}
        
        //-------------------------------- Bank Payment Notifications ----------------------------------------//
        function viewBankPaymentNoft($filter,$filter_mask,$pos,$cnt) {		

            $count=0;
            $this->NotfArray = array();
            $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date, BS.BS_Amount,BS.PM_Id,BS.BNK_Id,BS.BB_Id,BS.BA_Id, DS.DS_Description, TR.TR_Track, IT.IT_Name, IT.SH_Id, IT.IT_DualItem, SH.SH_Name, MH.MH_Type, LC.LC_Name
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN `users_auth` AS US ON BS.US_Id = US.US_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                    WHERE IT.IT_Business = '0' 
                                        AND IT.IT_DualEntry = 1
                                        AND MH.MH_Type IN ('1','2')
                                        AND BS.BS_Status = 1
                                        AND BS.BS_DualEntry = 1
                                        AND DS.DS_Description IN  ( SELECT BA_DispName FROM bank_accounts WHERE LC_Id =".$filter.")
                                        ".$dateFilt."
                                        ".$filter_mask."
                                    ORDER BY BS.BS_Date DESC
                                    LIMIT ".$pos.",".$cnt
                                    );
            
            if($result){
                while($row = mysqli_fetch_object($result)) {
                    $this->NotfArray[$count] = $row;
                    $count++;
                }
            }
        }
        //---------------------------------- Bank Payment Notifications Count ----------------------------------------//
        function viewBankPaymentNoftCount($filter,$filter_mask) {		
            
            $result = mysqli_query($GLOBALS['con'],"SELECT COUNT(BS.BS_Id)
                                    FROM `balance_sheets` AS BS
                                     LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN `users_auth` AS US ON BS.US_Id = US.US_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                    WHERE IT.IT_Business = '0' 
                                       AND IT.IT_DualEntry = 1
                                        AND MH.MH_Type IN ('1','2')
                                        AND BS.BS_Status = 1
                                        AND BS.BS_DualEntry = 1
                                        AND DS.DS_Description IN  ( SELECT BA_DispName FROM bank_accounts WHERE LC_Id =".$filter.")
                                        ".$dateFilt."
                                        ".$filter_mask."
                                        AND ".$filter."
                                    ORDER BY BS.BS_Date DESC
                                    "
                                    );
            $row = mysqli_fetch_array($result,MYSQLI_NUM);
            return $count=$row[0];
        }
        
        //-------------------------------- Cash Payment Notifications ----------------------------------------//
        function viewCashPaymentNoft($filter,$filter_mask,$pos,$cnt) {		

            $count=0;
            $this->NotfArray = array();

            $result = mysqli_query($GLOBALS['con'],"SELECT BS.US_Id, BS.BS_Id, BS.BS_Date , BS.BS_Amount, DS.DS_Description, TR.TR_Track, IT.IT_Name,IT.IT_DualItem, IT.SH_Id, SH.SH_Name, MH.MH_Type, LC.LC_Name
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN `users_auth` AS US ON BS.US_Id =US.US_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                    WHERE IT.IT_Business = '0' 
                                        AND IT.IT_DualEntry = 1
                                        AND MH.MH_Type IN ('1','2')
                                        AND BS.BS_Status = 1
                                        AND BS.BS_DualEntry = 1
                                        ".$dateFilt."
                                        ".$filter_mask."
                                        AND ".$filter."
                                    ORDER BY BS.BS_Date DESC
                                    LIMIT ".$pos.",".$cnt
                                    );
            if($result){
                while($row = mysqli_fetch_object($result)) {
                    $this->NotfArray[$count] = $row;
                    $count++;
                }
            }
        }
        //---------------------------------- Cash Payment Notifications Count ----------------------------------------//
        function viewCashPaymentNoftCount($filter,$filter_mask) {		
            
            $result = mysqli_query($GLOBALS['con'],"SELECT COUNT(BS.BS_Id)
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN  `users_auth` AS US ON BS.US_Id =US.US_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                    WHERE IT.IT_Business = '0'
                                        AND IT.IT_DualEntry = 1
                                        AND MH.MH_Type IN ('1','2')
                                        AND BS.BS_Status = 1
                                        AND BS.BS_DualEntry = 1
                                        ".$dateFilt."
                                        ".$filter_mask."
                                        AND ".$filter."
                                    ORDER BY BS.BS_Date DESC ");//AND BS.PM_Id = 1  AND DS.DS_Description =  LC.LC_Name
            $row = mysqli_fetch_array($result,MYSQLI_NUM);
            return $count=$row[0];
        }
        
        function getDualEntryItem($ITId){
            
            $count = 0;
            $this->NotfArray = array();
            
            $result = mysqli_query($GLOBALS['con'],"SELECT IT.IT_Id, IT.IT_Name, IT.SH_Id, SH.SH_Name, MH.MH_Id, MH.MH_Type
                                        FROM `items` AS IT
                                            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                                WHERE 
                                                    IT.IT_Id = ".$ITId
                                    );
            if($result){
                while($row = mysqli_fetch_assoc($result)) {
                    $this->NotfArray = $row;
                }
            }
            
        }
        
}

?>
