<?php
require_once("connection.php");

class HistoryClass
{
	var $HistoryArray;
        
        //---------------------------------- Items in a Company - List -------------------------------------//
	function viewAllOfzItems($filt='',$filter,$start,$end )
	{			
		$count=0;
		$this->HistoryArray = array();
               $sql = 'SELECT IT.IT_Id, IT.IT_Name, IT.SH_Id, MH.MH_Type,IT.IT_Status, 
                            SH.SH_Name, MH.MH_Id, MH.MH_Name
                                FROM `items`  AS IT
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                            WHERE '.$filt . $filter.' ORDER BY IT.IT_Name LIMIT ' .$start.', '.$end;
               	$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->HistoryArray[$count]=$row;
			$count++;
		}	
	}
        //---------------------------------- Items in a Company - Count  -------------------------------------//
	function viewAllOfzItemsCount($filt='',$filter)
	{			
		$count=0;
		$this->HistoryArray = array();
                $sql = 'SELECT COUNT(IT.IT_Id)
                            FROM `items`  AS IT
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                        WHERE '.$filt . $filter;
               	$result = mysqli_query($GLOBALS['con'],$sql);
		$row    = mysqli_fetch_array($result, MYSQLI_NUM);
                return $count=$row[0];		
	}
         
        //---------------------------------- Items in a Company - List -------------------------------------//
	function allOfzItems($filter)
	{			
		$count=0;
		$this->HistoryArray = array();
                $sql = 'SELECT IT.IT_Id, IT.IT_Name, IT.SH_Id, IT.MH_Type,IT.IT_Status, 
                            SH.SH_Name, MH.MH_Id, MH.MH_Name
                                FROM `items`  AS IT
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                            WHERE '. $filter.' ORDER BY IT.IT_Name ';
               	$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->HistoryArray[$count]=$row;
			$count++;
		}	
	}
        
        //----------------------------------------Custom Item Details ----------------------------------------------//
        function  getITDetails($ITID){
            $count=0;
            $this->HistoryArray= array();
            
            $result=mysqli_query($GLOBALS['con'],"SELECT IT.*, SH.SH_Name,
                                    US1.US_Id AS USId , CONCAT(US1.US_FName , ' ' , US1.US_LName ) AS USName ,
                                    US2.US_Id AS AprvlUSId , CONCAT(US2.US_FName , ' ' , US2.US_LName ) AS AprvlUSName ,
                                    US3.US_Id AS AprvdUSId , CONCAT(US3.US_FName , ' ' , US3.US_LName ) AS AprvdUSName ,
                                    IT2.IT_DualItem AS ITDualItem ,IT2.IT_Name AS DualItemName 
                                        FROM `items` AS IT
                                            LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                            LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                            LEFT JOIN users_auth AS US1 ON IT.US_Id = US1.US_Id 
                                            LEFT JOIN users_auth AS US2 ON IT.IT_Approval = US2.US_Id 
                                            LEFT JOIN users_auth AS US3 ON IT.IT_Approved = US3.US_Id 
                                            INNER JOIN items IT2 ON IT2.IT_DualItem = IT2.IT_Id
                                               WHERE IT.IT_Id = ".$ITID);
            while ($row =  mysqli_fetch_assoc($result)){
                $this->HistoryArray = $row;
                $count++;
            }
        }
            
    //---------------------------------------- Item Based History ----------------------------------------------//
        function  getIT_BkupDetails($ITID){
            $count=0;
            $this->HistoryArray= array();
            $sql = "SELECT IT.*, SH.SH_Name,
                        US1.US_Id AS USId , CONCAT(US1.US_FName , ' ' , US1.US_LName ) AS USName ,
                        US2.US_Id AS AprvlUSId , CONCAT(US2.US_FName , ' ' , US2.US_LName ) AS AprvlUSName ,
                        US3.US_Id AS AprvdUSId , CONCAT(US3.US_FName , ' ' , US3.US_LName ) AS AprvdUSName ,
                        US4.US_Id AS BKUSId , CONCAT(US4.US_FName , ' ' , US4.US_LName ) AS ITUSName ,
                        IT2.IT_DualItem AS ITDualItem ,IT2.IT_Name AS DualItemName 
                            FROM `items_bkup` AS IT
                                LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                LEFT JOIN users_auth AS US1 ON IT.US_Id = US1.US_Id 
                                LEFT JOIN users_auth AS US2 ON IT.IT_Approval = US2.US_Id 
                                LEFT JOIN users_auth AS US3 ON IT.IT_Approved = US3.US_Id 
                                LEFT JOIN users_auth AS US4 ON IT.BK_USID = US4.US_Id 
                                INNER JOIN items_bkup IT2 ON IT2.IT_DualItem = IT2.IT_Id
                                    WHERE IT.IT_Id = ".$ITID." ORDER BY BK_CDate DESC";
            $result=mysqli_query($GLOBALS['con'],$sql);
            while ($row = mysqli_fetch_assoc($result)){
                $this->HistoryArray[$row['BK_Id']] = $row;
                $count++;
            }
        }
        
    //------------------------------ Accounts Entry in a Company - List ---------------------------------//
        
        function viewAllBSEntries($stDate,$enDate,$ofid,$newFilt,$filter_key,$pos,$cnt) {	
            $dateFilt = '';
        
            if($stDate!='' && $enDate!=''){
                $stDate= date("Y-m-d", strtotime($stDate));
                $enDate= date("Y-m-d", strtotime($enDate));
                $dateFilt=" AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
            }elseif($stDate=='' && $enDate!=''){
                $enDate= date("Y-m-d", strtotime($enDate));
                $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
            }elseif($stDate!='' && $enDate==''){
                $stDate= date("Y-m-d", strtotime($stDate));
                $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
            }
            $count=0;
            $this->HistoryArray = array();         
            $sql="SELECT BS.US_Id, BS.BS_Id, BS.BS_Date, BS.BS_Amount, BS.BS_Status,
                         DS.DS_Description,BS.CHQ_Number, TR.TR_Id ,TR.TR_Track, 
                         IT.IT_Id, IT.IT_Name,IT.IT_Business, IT.SH_Id, 
                         SH.SH_Name,SH.SH_Track, MH.MH_Type, LC.LC_Name,US.US_FName,US.US_LName
                                    FROM `balance_sheets` AS BS
                                    LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                    LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                    LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                    LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                    LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                    LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                    LEFT JOIN `offices` AS OF ON LC.OF_Id = OF.OF_Id
                                    LEFT JOIN `users_auth` AS US ON US.US_Id=BS.US_Id
                                    WHERE
                                    BS.US_Id IN ( ".$newFilt." )
                                    ".$filter_key." 
                                    ".$dateFilt."    
                                    AND MH.MH_Type IN ('1','2') 
                                    ORDER BY BS.BS_CDate DESC LIMIT ".$pos.' , '.$cnt ;
             $result = mysqli_query($GLOBALS['con'],$sql);
            if($result){
                while($row = mysqli_fetch_object($result)) {
                    $this->HistoryArray[$count] = $row;
                    $count++;
                }

            }
        }
        
    //------------------------------ Accounts Entry in a Company - Count ---------------------------------//
        function countBSEntries($stDate,$enDate,$ofid,$newFilt,$filter_key) {
            $dateFilt = '';

            if($stDate!='' && $enDate!=''){
                $stDate= date("Y-m-d", strtotime($stDate));
                $enDate= date("Y-m-d", strtotime($enDate));
                $dateFilt=" AND BS.BS_Date   between '".$stDate."' AND '".$enDate."'";
            }elseif($stDate=='' && $enDate!=''){
                $enDate= date("Y-m-d", strtotime($enDate));
                $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
            }elseif($stDate!='' && $enDate==''){
                $stDate= date("Y-m-d", strtotime($stDate));
                $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
            }
            $count=0;
            $result = mysqli_query($GLOBALS['con'],"SELECT COUNT(BS.BS_Id)
                                    FROM `balance_sheets` AS BS
                                        LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id
                                        LEFT JOIN `sub_heads` AS SH ON IT.SH_Id = SH.SH_Id
                                        LEFT JOIN `main_heads` AS MH ON SH.MH_Id = MH.MH_Id
                                        LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id
                                        LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id
                                        LEFT JOIN `locations` AS LC ON BS.LC_Id = LC.LC_Id
                                        LEFT JOIN `offices` AS OF ON LC.OF_Id = OF.OF_Id
                                        LEFT JOIN `users_auth` AS US ON US.US_Id = BS.US_Id
                                            WHERE BS.US_Id IN ( ".$newFilt." )
                                                ".$filter_key."  
                                                ".$dateFilt."    
                                                AND MH.MH_Type IN ('1','2') 
                                                AND BS_Status = 1");
            if($result){
                 $row = mysqli_fetch_array($result,MYSQLI_NUM);
                 return $count=$row[0];

            }
        }
        
        //---------------------------------------- Accounts Entry Details----------------------------------------------//
        function  getBSEntryDetails($BSID){
            $count=0;
            $this->HistoryArray= array();
            
            $result=mysqli_query($GLOBALS['con'],"SELECT BS.*,IT.*,DS.*,US.*,LC.LC_Id,BA.BA_Id,BA.BA_No,
                                    BCL.CL_Id,BCL.CL_Leaf,
                                    TR.TR_Id , TR.TR_Track,
                                    US2.US_Id AS PaidUSId , CONCAT(US2.US_FName , ' ' , US2.US_LName ) AS PaidUSName ,
                                    US3.US_Id AS UserUSId , CONCAT(US3.US_FName , ' ' , US3.US_LName ) AS UserUSName ,
                                    US4.US_Id AS StaffUSId , US4.US_EMPID AS StaffId ,
                                    US5.US_Id AS AprlGvnUSId , CONCAT(US5.US_FName , ' ' , US5.US_LName ) AS AprlGvnUSName ,
                                    US6.US_Id AS AprlTknUSId , CONCAT(US6.US_FName , ' ' , US6.US_LName ) AS AprlTknUSName ,
                                    US7.US_Id AS BSUSId , CONCAT(US7.US_FName , ' ' , US7.US_LName ) AS BSUSName ,
                                    LC2.LC_Id AS PrchsdForLCId , LC2.LC_Name AS PrchsdForLCName 
                                    
                                    FROM `balance_sheets` AS BS
                                        LEFT JOIN items As IT ON BS.IT_Id = IT.IT_Id
                                        LEFT JOIN descriptions AS DS ON BS.BS_Description = DS.DS_Id
                                        LEFT JOIN users_auth AS US ON BS.US_Id = US.US_Id 
                                        LEFT JOIN users_auth AS US2 ON BS.BS_PaidBy = US2.US_Id 
                                        LEFT JOIN users_auth AS US3 ON BS.BS_User = US3.US_Id 
                                        LEFT JOIN users_auth AS US4 ON BS.BS_StaffId = US4.US_Id 
                                        LEFT JOIN users_auth AS US5 ON BS.BS_AprovlGvnBy = US5.US_Id
                                        LEFT JOIN users_auth AS US6 ON BS.BS_AprovlTknBy = US6.US_Id
                                        LEFT JOIN users_auth AS US7 ON BS.US_Id = US7.US_Id
                                        LEFT JOIN locations AS LC ON BS.LC_Id = LC.LC_Id
                                        LEFT JOIN locations AS LC2 ON BS.BS_PrchsdFor = LC2.LC_Id
                                        LEFT JOIN bank_accounts AS BA ON BS.BA_Id = BA.BA_Id
                                        LEFT JOIN bank_cheque_leafs AS BCL ON BS.CHQ_Number = BCL.CL_Id
                                        LEFT JOIN tracks AS TR ON BS.TR_Id = TR.TR_Id
                                           WHERE BS.BS_Id = ".$BSID);
            while ($row =  mysqli_fetch_assoc($result)){
                $this->HistoryArray = $row;
                $count++;
            }
        }
            
    //---------------------------------------- Accounts Entry History -----------------------------------------//
        function  getBS_BkupEntryDetails($BSID){
            $count=0;
            $this->HistoryArray= array();
            $sql = "SELECT BS.*,IT.*,DS.*,US.*,LC.LC_Id,BA.BA_Id,BA.BA_No,
                                    BCL.CL_Id,BCL.CL_Leaf,
                                    TR.TR_Id , TR.TR_Track,
                                    US2.US_Id AS PaidUSId , CONCAT(US2.US_FName , ' ' , US2.US_LName ) AS PaidUSName ,
                                    US3.US_Id AS UserUSId , CONCAT(US3.US_FName , ' ' , US3.US_LName ) AS UserUSName ,
                                    US4.US_Id AS StaffUSId , CONCAT(US4.US_FName , ' ' , US4.US_LName ) AS StaffUSName ,
                                    US5.US_Id AS AprlGvnUSId , CONCAT(US5.US_FName , ' ' , US5.US_LName ) AS AprlGvnUSName ,
                                    US6.US_Id AS AprlTknUSId , CONCAT(US6.US_FName , ' ' , US6.US_LName ) AS AprlTknUSId ,
                                    US7.US_Id AS BKUSId , CONCAT(US7.US_FName , ' ' , US7.US_LName ) AS BKUSName ,
                                    US8.US_Id AS BSUSId , CONCAT(US8.US_FName , ' ' , US8.US_LName ) AS BSUSName ,
                                    LC2.LC_Id AS PrchsdForLCId , LC2.LC_Name AS PrchsdForLCName 
                                    
                                    FROM `balance_sheets_bkup` AS BS
                                        LEFT JOIN items As IT ON BS.IT_Id = IT.IT_Id
                                        LEFT JOIN descriptions AS DS ON BS.BS_Description = DS.DS_Id
                                        LEFT JOIN users_auth AS US ON BS.US_Id = US.US_Id 
                                        LEFT JOIN users_auth AS US2 ON BS.BS_PaidBy = US2.US_Id 
                                        LEFT JOIN users_auth AS US3 ON BS.BS_User = US3.US_Id 
                                        LEFT JOIN users_auth AS US4 ON BS.BS_StaffId = US4.US_Id 
                                        LEFT JOIN users_auth AS US5 ON BS.BS_AprovlGvnBy = US5.US_Id
                                        LEFT JOIN users_auth AS US6 ON BS.BS_AprovlTknBy = US6.US_Id
                                        LEFT JOIN users_auth AS US7 ON BS.BK_USID = US7.US_Id
                                        LEFT JOIN users_auth AS US8 ON BS.US_Id = US8.US_Id
                                        LEFT JOIN locations AS LC ON BS.LC_Id = LC.LC_Id
                                        LEFT JOIN locations AS LC2 ON BS.BS_PrchsdFor = LC2.LC_Id
                                        LEFT JOIN bank_accounts AS BA ON BS.BA_Id = BA.BA_Id
                                        LEFT JOIN bank_cheque_leafs AS BCL ON BS.CHQ_Number = BCL.CL_Id
                                        LEFT JOIN tracks AS TR ON BS.TR_Id = TR.TR_Id
                                           WHERE BS.BS_Id = ".$BSID." ORDER BY BK_CDate DESC";
            $result=mysqli_query($GLOBALS['con'],$sql);
            while ($row = mysqli_fetch_assoc($result)){
                $this->HistoryArray[$row['BK_Id']] = $row;
                $count++;
            }
        }

        
        function bsEntryUpdateSettingsHistory($OF_Id){
            $count = 0;
            $this->HistoryArray = array();
            
            $sql  = 'SELECT BES.*, BES.BES_MDate AS UpdatedDate, CONCAT(US.US_FName," ",US.US_LName) AS AddedBy
                        FROM bs_entry_update_settings AS BES
                            LEFT JOIN users_auth AS US ON US.US_Id = BES.US_Id
                                WHERE BES.OF_Id = "'.$OF_Id.'" ';
            $result = mysqli_query($GLOBALS['con'],$sql);
            
            while($row = mysqli_fetch_object($result)){
                $this->HistoryArray[$count] = $row ;
                $count++;
            }
            
            $sql  = 'SELECT BK.*,BK.BES_BKCDate AS UpdatedDate, CONCAT(US1.US_FName," ",US1.US_LName) AS AddedBy, CONCAT(US2.US_FName," ",US2.US_LName)  AS UpdatedBy
                        FROM bs_entry_update_settings_bkup AS BK
                            LEFT JOIN users_auth AS US1 ON US1.US_Id = BK.US_Id
                            LEFT JOIN users_auth AS US2 ON US2.US_Id = BK.BES_BKUSId
                                WHERE BK.OF_Id = "'.$OF_Id.'" ORDER BY BK.BES_BKCDate DESC';
            $result = mysqli_query($GLOBALS['con'],$sql);
            
            while($row = mysqli_fetch_object($result)){
                $this->HistoryArray[$count] = $row ;
                $count++;
            }
        }
         function  getDSDetails($DSID){
            $count=0;
            $this->HistoryArray= array();
            $sql="SELECT DS.DS_Description,DS.DS_Status,
                                    US1.US_Id AS USId , CONCAT(US1.US_FName , ' ' , US1.US_LName ) AS USName                                     
                                        FROM `descriptions` AS DS                                            
                                            LEFT JOIN users_auth AS US1 ON DS.US_Id = US1.US_Id                                                                                      
                                               WHERE DS.DS_Id = ".$DSID;
            $result=mysqli_query($GLOBALS['con'],$sql);
            while ($row =  mysqli_fetch_assoc($result)){
                $this->HistoryArray = $row;
                $count++;
            }
        }
            
    //---------------------------------------- Item Based History ----------------------------------------------//
        function  getDS_BkupDetails($DSID){
            $count=0;
            $this->HistoryArray= array();
           $sql = "SELECT DS.BK_Id,DS.DS_Description,DS.DS_Status,DS.BK_CDate,US1.US_Id AS USId , CONCAT(US1.US_FName , ' ' , US1.US_LName ) AS BKUSName                                                                       
                                        FROM `descriptions_bkup` AS DS                               
                                LEFT JOIN users_auth AS US1 ON DS.BK_USID = US1.US_Id                                 
                                    WHERE DS.DS_Id = ".$DSID." ORDER BY BK_CDate DESC";
            $result=mysqli_query($GLOBALS['con'],$sql);
            while ($row = mysqli_fetch_assoc($result)){
                $this->HistoryArray[$row['BK_Id']] = $row;
                $count++;
            }
        }
}

?>