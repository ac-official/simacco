<?php
require_once("connection.php");
class TrackDupRptClass {
    var $TrackDupArray;
    var $DataCount;
    function reportTrackDupData($filt,$stDate='',$enDate='',$filter,$filter_mask,$filter_having,$pos,$cnt,$sort) {		
        $dateFilt = '';
        $sortFilt = '';
        if($stDate!='' && $enDate!=''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   BETWEEN '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate= date("Y-m-d", strtotime($enDate));
            $dateFilt=" AND BS.BS_Date   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate= date("Y-m-d", strtotime($stDate));
            $dateFilt=" AND BS.BS_Date   >=  '".$stDate."'";
        }   
        if($sort=='ASC'){
            $sortFilt = " COUNT(BS.BS_Id) ASC,";
        }else if($sort=='DESC'){
            $sortFilt = " COUNT(BS.BS_Id) DESC,";
        }        
        $sql="SELECT SQL_CALC_FOUND_ROWS BS.BS_Id,GROUP_CONCAT(DISTINCT(LC.LC_Id)) AS LC_Id,GROUP_CONCAT(DISTINCT(LC.LC_Name)) AS LC_Name,IT.IT_Name,BS.TR_Id,IT.IT_Id,BS.BS_Date, COUNT(BS.BS_Id ) AS no_rows, 
        TR.TR_Track FROM  balance_sheets AS BS
        LEFT JOIN locations AS LC  ON BS.LC_Id = LC.LC_Id 
        LEFT JOIN items AS IT ON IT.IT_Id=BS.IT_Id 
        LEFT JOIN tracks AS TR ON TR.TR_Id = BS.TR_Id 
        LEFT JOIN sub_heads AS SH ON IT.SH_Id=SH.SH_Id 
        WHERE BS.BS_Status IN(1,2) AND BS.TR_Id<>0 AND BS.TR_Id<>'null' AND IT.IT_Business = '0' AND ".$filter." " .$dateFilt. " ".$filter_mask." AND SH.MH_Id IN (2,4,6) GROUP BY BS.TR_Id, IT.IT_Id HAVING COUNT(BS.BS_Id ) > 1 ".$filter_having." ORDER BY ".$sortFilt." BS.BS_Date ASC  LIMIT ".$pos.','.$cnt;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $resultc = mysqli_query($GLOBALS['con'],"SELECT found_rows() as COUNT" );
        $rowc=mysqli_fetch_array($resultc,MYSQLI_ASSOC);
        $this->DataCount=$rowc["COUNT"];
        if($result){
            
            if($this->DataCount>0){
                $count=0;
                while($row = mysqli_fetch_object($result)) {
                if($row->no_rows>1){
                $this->TrackDupArray[$count] = $row;
                $count++;   
                }
                }                 
            }
            
        } 
        
        
    }
    function viewSearchTracks($filt='')
	{	
		$count=0;
		$this->TrackArray = array();                
		$result=mysqli_query($GLOBALS['con'],"SELECT TR.TR_Id, TR.US_Id, TR.OF_Id, TR.TR_Track, TR.TR_Status,UA.US_FName,UA.US_LName,LC.LC_Name,  
                                        BS.BS_Amount, BS.BS_Id, BS.BS_Date,IT.IT_Id,IT.MH_Type,IT.IT_Name,IT.IT_Business, IT.SH_Id, DS.DS_Description
                                            FROM `balance_sheets` AS BS 
                                            LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                                            LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id 
                                            LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id 
                                            LEFT JOIN `users_auth` AS UA ON BS.US_Id = UA.US_Id 
                                            LEFT JOIN `locations` AS LC ON LC.LC_Id = UA.LC_Id 
                                                WHERE BS.BS_Status IN(1,2) ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->TrackArray[$count]=$row;
			$count++;
		}	
	}
}
?>