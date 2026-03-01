<?php

require_once("connection.php");

class TrackClass {

    var $TrackArray;
    var $instnArray;

    //----------------------------------------- All Subheads ----------------------------------------//
    function viewTracks($filt = '') {
        $count = 0;
        $this->TrackArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT * FROM tracks " . $filt);
        while ($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }
    }

    //----------------------------------------- Searched Tracks----------------------------------------//
    function viewSearchTracks($filt = '') {
        $count = 0;
        $this->TrackArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT TR.TR_Id, TR.US_Id, TR.OF_Id, TR.TR_Track, TR.TR_Status,UA.US_FName,UA.US_LName,LC.LC_Name,  
                                        BS.BS_Amount, BS.BS_Id, BS.BS_Date,IT.IT_Id,IT.MH_Type,IT.IT_Name,IT.IT_Business, IT.SH_Id, DS.DS_Description, BS.BS_Status 
                                            FROM `balance_sheets` AS BS 
                                            LEFT JOIN `items` IT ON BS.IT_Id = IT.IT_Id 
                                            LEFT JOIN `descriptions` AS DS ON BS.BS_Description = DS.DS_Id 
                                            LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id 
                                            LEFT JOIN `users_auth` AS UA ON BS.US_Id = UA.US_Id 
                                            LEFT JOIN `locations` AS LC ON LC.LC_Id = UA.LC_Id 
                                                WHERE (BS.BS_Status = 1 OR (BS.BS_Status = 2 AND BS.BS_Amount > 0))  " . $filt);
        while ($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }
    }

    function CalcTrackAmnt($filt = '') {

        $result = mysqli_query($GLOBALS['con'], "SELECT SUM(BS.BS_Amount) FROM tracks AS TR, balance_sheets AS BS, 
                    items AS IT WHERE BS.TR_Id = TR.TR_Id AND BS.IT_Id = IT.IT_Id " . $filt);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        return $row[0];
    }

    //----------------------------------------- Verify Track ----------------------------------------//
    function verifyTrack($OFId) {
        $sql = 'SELECT TR_Id, TR_Track FROM tracks WHERE TR_Track = "' . $this->TR_Data['TR_Track'] . '" AND OF_Id = ' . $OFId;
        $result = mysqli_query($GLOBALS['con'], $sql);
        if (mysqli_num_rows($result) == 0) {
            return 0;
        } else {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            return $row['TR_Id'];
        }
    }

    function verifyTrackStatus($OFId) {
        $sql = 'SELECT TR_Id FROM tracks WHERE TR_Track = "' . $this->TR_Data['TR_Track'] . '" AND OF_Id = ' . $OFId . ' AND TR_Status = 0';
        $result = mysqli_query($GLOBALS['con'], $sql);
        if (mysqli_num_rows($result) == 0) {
            return 0;
        } else {
            return 1;
        }
    }

    //----------------------------------------- New Track ----------------------------------------//
    function newTrack() {

        $sql = "INSERT INTO tracks ( " . implode(', ', array_keys($this->TR_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->TR_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'], $sql);
        return mysqli_insert_id($GLOBALS['con']);
    }

    //----------------------------------------- Update Subhead ----------------------------------------//
    function updateSubhead($SHId) {
        $SHData = '';
        foreach ($this->SH_Data as $key => $value) {
            $SHData = $SHData . $key . "='" . $value . "', ";
        }
        $SHData = substr($SHData, 0, -2);
        $sql = "UPDATE sub_heads SET $SHData WHERE SH_Id=$SHId";
        mysqli_query($GLOBALS['con'], $sql);
        return 'Subhead Updated Successfully';
    }

    //----------------------------------------- Subheads based on Main Head ----------------------------------------//
    function getSubHeadType($filt) {
        $count = 0;
        $this->TrackArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT SH.SH_Track FROM items as IT , sub_heads as SH WHERE IT.SH_Id = SH.SH_Id  AND IT.IT_Id =" . $filt);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row['SH_Track'];
    }

    function getSubHeadTypeBS($filt) {
        $count = 0;
        // IT_OtherUser added in the selection 28-05-2025 
        // account entry time branch show hide based on the above field value.
        $this->TrackArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT SH.SH_Track,IT.IT_Business, IT.IT_OtherUser FROM items as IT , sub_heads as SH WHERE IT.SH_Id = SH.SH_Id  AND IT.IT_Id =" . $filt);
        $row = mysqli_fetch_assoc($result);
        return json_encode($row);
    }

    function viewTrackList($preTally_user_ofid, $pos, $cnt, $filter) {
        $count = 0;
        $this->TrackArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT t.TR_Id,t.TR_Track,t.TR_Status,u.US_FName,
                                u.US_LName,l.LC_Name 
                                FROM tracks AS t LEFT JOIN users_auth AS u ON 
                                t.US_Id=u.US_Id LEFT JOIN locations AS l ON l.LC_Id=u.LC_Id 
                                WHERE t.OF_Id=" . $preTally_user_ofid . $filter .
                " ORDER BY t.TR_Track asc LIMIT " . $pos . "," . $cnt);
        while ($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }
    }

    function countViewTrackList($preTally_user_ofid, $filter) {
        $result = mysqli_query($GLOBALS['con'], "SELECT COUNT(t.TR_Id) 
                        FROM tracks AS t LEFT JOIN users_auth AS u ON 
                        t.US_Id=u.US_Id LEFT JOIN locations AS l ON l.LC_Id=u.LC_Id 
                        WHERE t.OF_Id=" . $preTally_user_ofid . $filter .
                " ORDER BY t.TR_Track");
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        return $row[0];
    }

    function checkTrack($OFId, $TR_Id) {
        $sql = 'SELECT TR_Id, TR_Track FROM tracks WHERE TR_Track = "' . $this->TR_Data['TR_Track'] . '" AND OF_Id = ' . $OFId . ' AND TR_Id != ' . $TR_Id;
        $result = mysqli_query($GLOBALS['con'], $sql);
        if (mysqli_num_rows($result) == 0) {
            return 0;
        } else {
            return 1;
        }
    }

    function updateTrack($filter) {
        $TRData = '';
        foreach ($this->TR_Data as $key => $value) {
            $TRData = $TRData . $key . "='" . $value . "', ";
        }
        $TRData = substr($TRData, 0, -2);
        $sql = "UPDATE tracks SET $TRData  $filter";
        mysqli_query($GLOBALS['con'], $sql);
        if (mysqli_affected_rows($GLOBALS['con']) > 0)
            return "success";
    }

    /* Function for removing unwanted characters or spaces */

    function cleanData($data) {
        $data = trim(strip_tags(htmlspecialchars($data, ENT_QUOTES)));
        $data = mysqli_real_escape_string($GLOBALS['con'], $data);
        return $data;
    }

    //----------------------------------------- List Instructions ----------------------------------------//
    function listInstruction() {
        $count = 0;
        $this->instnArray = array();
        $result = mysqli_query($GLOBALS['con'], "SELECT ATPI_Id, ATPI_Instruction  FROM atp_instruction LIMIT 0,1");
        while ($row = mysqli_fetch_object($result)) {
            $this->instnArray[$count] = $row;
            $count++;
        }
    }

    //----------------------------------------- Update Instructions ----------------------------------------//
    function updateInstruction() {
        $ATPIData = '';
        foreach ($this->ATPI_Data as $key => $value) {
            $ATPIData = $ATPIData . $key . "='" . $value . "', ";
        }
        $ATPIData = substr($ATPIData, 0, -2);
        $sql = "UPDATE atp_instruction SET $ATPIData WHERE ATPI_Id = 1";
        mysqli_query($GLOBALS['con'], $sql);
        return $sql;
        if (mysqli_affected_rows($GLOBALS['con']) > 0)
            return "success";
    }

    function oldTrackSubProcess($preTally_user_ofid, $pos, $cnt, $filter) {
        $count = 0;
        $this->TrackArray = array();

        $result = mysqli_query($GLOBALS['con'], "SELECT t.TR_Id,t.TR_Track,t.TR_Status,u.US_FName,
                                u.US_LName,l.LC_Name,US.US_FName AS FName,US.US_LName AS LName,
                                TOJ.APS_Id ,l.LC_Id,COUNT(TOJ.APS_Id ) AS TK_Count 
                                FROM tracks AS t LEFT JOIN users_auth AS u ON 
                                t.US_Id = u.US_Id LEFT JOIN locations AS l ON l.LC_Id = u.LC_Id 
                                LEFT JOIN tracks_old_jobs AS TOJ ON TOJ.TR_Id = t.TR_Id 
                                LEFT JOIN  users_auth AS US ON TOJ.TOJ_LastUpdated = US.US_Id 
                                WHERE t.OF_Id=" . $preTally_user_ofid . $filter .
                "  GROUP BY t.TR_Track ORDER BY t.TR_Track asc LIMIT " . $pos . "," . $cnt);
        while ($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }
    }

    function countOldTrackSubProcess($preTally_user_ofid, $filter) {
        $result = mysqli_query($GLOBALS['con'], "SELECT t.TR_Id,t.TR_Track,t.TR_Status,u.US_FName,
                                u.US_LName,l.LC_Name,US.US_FName AS FName,US.US_LName AS LName,
                                TOJ.APS_Id ,l.LC_Id,COUNT(TOJ.APS_Id ) AS TK_Count 
                                FROM tracks AS t LEFT JOIN users_auth AS u ON 
                                t.US_Id = u.US_Id LEFT JOIN locations AS l ON l.LC_Id = u.LC_Id 
                                LEFT JOIN tracks_old_jobs AS TOJ ON TOJ.TR_Id = t.TR_Id 
                                LEFT JOIN  users_auth AS US ON TOJ.TOJ_LastUpdated = US.US_Id 
                                WHERE t.OF_Id=" . $preTally_user_ofid . $filter .
                "  GROUP BY t.TR_Track ORDER BY t.TR_Track asc ");

        return mysqli_num_rows($result);
    }
    /**
    * List all tracks based on the office and job date
    * Created by Bilin @ 22-05-2025
    * list all job get in the filtered date 
    * take the total job count based on the filter
    * find the total income and expense get from the
    */
    function listAllTracks($inParms=[]) {
        // set the default return values
        $this->totalCount   = 0;
        $this->trackList    = [];
        $trackList          = [];
        extract($inParms);
        $where      = ' WHERE (BS.BS_Status = 1 OR (BS.BS_Status = 2 AND BS.BS_Amount > 0)) AND BS.TR_Id > 0 AND BS.TR_Id != "NULL"  ';
        //AND IT.IT_Business = 1 AND IT.MH_Type =1 
        $having     = '';
        $fields     = '';
        $table      = ' FROM `balance_sheets` AS BS '
               .' LEFT JOIN `items` IT ON (BS.IT_Id = IT.IT_Id) '
               .' LEFT JOIN `tracks` AS TR ON (BS.TR_Id = TR.TR_Id) ';
        if (isset($off_id) && $off_id > 0) {
            $where  .= ' AND TR.OF_Id= "'.$off_id.'"';
        }
        if (isset($track_no) && $track_no != '') { //21-01-2026
            $where  .= ' AND TR.TR_Track LIKE "%'.$track_no.'%"';
        }    
        if (isset($date_type) && $date_type == 1 && ((isset($from_date) && $from_date != '') || (isset($to_date) && $to_date != '')) ) {
            $having     = ' HAVING 1 ';
            if (isset($from_date) && $from_date != '') {
                $having  .= ' AND MIN(BS.BS_Date) >= "'.$from_date.'"';
            }
            if (isset($to_date) && $to_date != '') {
                $having  .= ' AND MIN(BS.BS_Date) <= "'.$to_date.'"';
            }
            // find the total
            $sqltotl    = 'SELECT COUNT(DISTINCT b.TR_Id) FROM balance_sheets AS b WHERE b.TR_Id IN (SELECT TR.TR_Id '.$table.' '.$where.' GROUP BY TR.TR_Id '.$having.' ORDER BY TR.TR_Id ASC)';
        } else {
            if (isset($from_date) && $from_date != '') {
                $where  .= ' AND BS.BS_Date >= "'.$from_date.'"';
            }
            if (isset($to_date) && $to_date != '') {
                $where  .= ' AND BS.BS_Date <= "'.$to_date.'"';
            }
            // find the total
            $sqltotl    = 'SELECT COUNT(DISTINCT TR.TR_Id) '.$table.' '.$where.$having;
        }            
        $restotl    = mysqli_query($GLOBALS['con'], $sqltotl);  
        $rowtotl    = mysqli_fetch_array($restotl, MYSQLI_NUM);
        $this->totalCount  = $rowtotl[0];
        $this->sql  = $sqltotl;
        if ($this->totalCount > 0) {
            $sql    = 'SELECT TR.TR_Id, TR.OF_Id, TR.TR_Track, TR.TR_Status, SUM(BS.BS_Amount) AS job_amount, BS.BS_Id, BS.BS_Date, IT.IT_Id, IT.IT_Name, IT.SH_Id, BS.BS_Status AS bs_status'
            .$table.' '
            .$where
            .' GROUP BY TR.TR_Id '.$having.' ORDER BY TR.TR_Id ASC, BS.BS_Date ASC';
            if (isset($limit) && $limit > 0) {
                $sql .=' LIMIT '.$start.', '.$limit;
            }
            $this->sql  = $sql;
            $res        = mysqli_query($GLOBALS['con'], $sql); 
            $slno       = $start;  
            $i          = 0;
            $tracksmap  = [];
            $tracks     = [];
            while ($row = mysqli_fetch_object($res)) {
                $slno++;
                $row->slno              = $slno;
                $row->after_income      = 0;
                $trackList[$i]          = $row;
                $tracksmap[$row->TR_Id] = $i;
                $tracks[]               = $row->TR_Id;
                $i++;
            }
            if (!empty($tracks)) {
                // get the total income expense and job expense calcualations
                $sqltot     = 'SELECT BS.TR_Id, SUM(IF(BS.BS_Status = 1 && IT.MH_Type = 1 && IT.IT_Business = 0, BS.BS_Amount, 0)) AS total_income, SUM(IF(IT.MH_Type = 2 && IT.IT_Business = 1, BS.BS_Amount, 0)) AS job_expense, SUM(IF(IT.MH_Type = 2 && IT.IT_Business = 0, BS.BS_Amount, 0)) AS total_expense, SUM(IF(BS.BS_Status != 1 && IT.MH_Type = 1 && IT.IT_Business = 0,BS.BS_Amount,0)) AS unapprove_amt, SUM(IF(IT.MH_Type = 1 && IT.IT_Business = 1, BS.BS_Amount, 0)) AS job_amount, MIN(BS.BS_Date) AS job_Date '
                . ' FROM `balance_sheets` AS BS '
                . ' LEFT JOIN `items` IT ON (BS.IT_Id = IT.IT_Id) ' 
                . ' WHERE (BS.BS_Status = 1 OR (BS.BS_Status = 2 AND BS.BS_Amount > 0)) AND BS.TR_Id IN ('.implode(",",$tracks).') '
                . ' GROUP BY BS.TR_Id ORDER BY BS.TR_Id ASC';
                $restot     = mysqli_query($GLOBALS['con'], $sqltot);     
                while ($rw  = mysqli_fetch_object($restot)) {

                    $i      = $tracksmap[$rw->TR_Id];
                    $trackList[$i]->job_amount      = $rw->job_amount;
                    $trackList[$i]->job_expense     = $rw->job_expense;
                    $trackList[$i]->total_income    = $rw->total_income;
                    $trackList[$i]->total_expense   = $rw->total_expense;
                    $balance_unpaid                 = $rw->job_amount-$rw->total_income-$rw->job_expense;
                    $trackList[$i]->balance_unpaid  = ($balance_unpaid > 0) ? $balance_unpaid:0; 
                    $trackList[$i]->unpaid_balance  = $balance_unpaid;
                    $trackList[$i]->unapprove_amt   = $rw->unapprove_amt;
                    $trackList[$i]->job_Date        = $rw->job_Date;
                }
                // amount after the searched date
                if (isset($to_date) && $to_date != '' && $to_date != date('Y-m-d')) {
                    $sqltot     = 'SELECT BS.TR_Id, SUM(BS.BS_Amount) AS after_income'
                    . ' FROM `balance_sheets` AS BS '
                    . ' LEFT JOIN `items` IT ON (BS.IT_Id = IT.IT_Id) ' 
                    . ' WHERE BS.TR_Id IN ('.implode(",",$tracks).') AND BS.BS_Date > "'.$to_date.'" AND IT.MH_Type = 1 AND IT.IT_Business = 0 '
                    . ' GROUP BY BS.TR_Id ORDER BY BS.TR_Id ASC';
                    $restot     = mysqli_query($GLOBALS['con'], $sqltot);     
                    while ($rw  = mysqli_fetch_object($restot)) {
                        $i      = $tracksmap[$rw->TR_Id];
                        $trackList[$i]->after_income     = $rw->after_income;
                    }
                }

                $this->trackList    = $trackList;
            }
            return $sql;
        }
    }
    /**
    * List all pending track 
    * pagination and trackno based fliters
    * Created by Bilin @ 31-07-2025
    */
    function listPendingTracks($inParms=[]) {
        // set the default return values
        $this->totalCount   = 0;
        $this->trackList    = [];
        extract($inParms);
        $where          = ' WHERE TR.is_closed = 0 ';
        $fields         = '';
        $table          = ' FROM `tracks` AS TR ';
        $join           = ' INNER JOIN `users_auth` AS UA ON (UA.US_Id = TR.US_Id) '
            . ' INNER JOIN `locations` AS LC ON (LC.LC_Id = UA.LC_Id)'
            . ' INNER JOIN balance_sheets AS bl On (bl.TR_Id = TR.TR_Id)'          
            . ' INNER JOIN descriptions DS ON (bl.BS_Description =DS.DS_Id)';
        if (isset($trackno) && $trackno != '') {
            $where  .= ' AND (TR.TR_Track LIKE "%'.$trackno.'%" OR DS.DS_Description LIKE "'.$trackno.'%")';
        }
        if (isset($user_id) && $user_id > 0) {
            $where  .= ' AND (TR.US_Id= "'.$user_id.'" OR bl.US_Id ="'.$user_id.'")';
        } else if (isset($staff) && $staff != '') {
            $where  .= ' AND (UA.US_FName LIKE "%'.$staff.'%" OR UA.US_LName LIKE "%'.$staff.'%" OR LC.LC_Name LIKE "%'.$staff.'%")';
        }
        if (isset($off_id) && $off_id > 0) {
            $where  .= ' AND TR.OF_Id= "'.$off_id.'"';
        }
         // find the total
        $sqltotl    = 'SELECT COUNT(DISTINCT TR.TR_Id) '.$table.$join.' '.$where;
        $restotl    = mysqli_query($GLOBALS['con'], $sqltotl);    
        $rowtotl    = mysqli_fetch_array($restotl, MYSQLI_NUM);
        $this->totalCount  = $rowtotl[0];
        $sql        = $sqltotl;
        if ($this->totalCount > 0) {

            $sql    = 'SELECT TR.TR_Id, TR.TR_Track, TR.TR_CDate, CONCAT(UA.US_FName," ",UA.US_LName," - ",LC.LC_Name) AS full_name, DS.DS_Description, bl.US_Id AS blUS_Id, TR.US_Id '
            .$table.' '. $join.'  '  
            .$where
            .' GROUP BY TR.TR_Id'
            .' ORDER BY ';
            $sortby     = (isset($sortby)) ? trim(strtolower($sortby)) :"id";
            $orderby    = (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
            switch ($sortby) {
                case "date"     : $sql  .= 'TR.TR_CDate';  
                break;
                case "track"    : $sql  .= 'TR.TR_Track';   
                break;
                case "name"     : $sql  .= 'UA.US_FName'; 
                break;
                default         : $sql  .= 'TR.TR_Id'; 
                break;
            }
            $sql    .= ' '.$orderby;
            if (isset($limit) && $limit > 0) {
                $sql .=' LIMIT '.$start.', '.$limit;
            }
            $res        = mysqli_query($GLOBALS['con'], $sql); 
            $slno       = $start; 
            while ($row = mysqli_fetch_object($res)) {
                $slno++;
                $row->slno              = $slno;
                $this->trackList[]      = $row;
            }
        }

        return $sql;
    }
    /**
    * Close track based on the track id and user id
    */
    function closeTracks($trid=0, $user_id=0) 
    {
        $sql = "UPDATE tracks SET is_closed='1', closed_by='".$user_id."', closed_at='".date('Y-m-d H:i:s')."'  WHERE TR_Id = ".$trid;
        if (mysqli_query($GLOBALS['con'], $sql) ) {

            return 1;
        } 

        return 0;
    }
    /**
     * Track based expense list based on the date and company 
     * Created By Bilin @ 06-01-2026
    */
    function trackExpenseList($inParms=[]) {
        // set the default return values
        $this->totalCount   = 0;
        $this->trackList    = [];
        extract($inParms);
        $where      = ' WHERE BS.BS_Status = 1 AND IT.MH_Type = 2 AND BS.TR_Id > 0 AND BS.TR_Id != "NULL" AND BS.BS_Amount > 0 ';
        $fields     = '';
        $table      = ' FROM `balance_sheets` AS BS '
            .' INNER JOIN `items` IT ON (BS.IT_Id = IT.IT_Id) '
            .' LEFT JOIN `tracks` AS TR ON (BS.TR_Id = TR.TR_Id) '
            .' LEFT JOIN `locations` AS LC ON (LC.LC_Id = BS.LC_Id) ';
        $join       = ' LEFT JOIN `descriptions` AS DS ON (BS.BS_Description = DS.DS_Id) ';
        if (isset($off_id) && $off_id > 0) {
            $where  .= ' AND LC.OF_Id= "'.$off_id.'"';
        }
        if (isset($from_date) && $from_date != '') {
            $where  .= ' AND BS.BS_Date >= "'.$from_date.'"';
        }
        if (isset($to_date) && $to_date != '') {
            $where  .= ' AND BS.BS_Date <= "'.$to_date.'"';
        }
        if (isset($track_no) && $track_no != '') {
            $where  .= ' AND TR.TR_Track LIKE "%'.$track_no.'%"';
        }
        if (isset($amount) && $amount > 0) {
            $where  .= ' AND BS.BS_Amount = "'.$amount.'"';
        }
        if (isset($search) && $search != '') {
            $where  .= ' AND (IT.IT_Name LIKE "%'.$search.'%")';
        }
        // OR DS.DS_Description LIKE "'.$trackno.'%"
        // find the total
        $sqltotl    = 'SELECT COUNT(DISTINCT BS.BS_Id), SUM(BS.BS_Amount) '.$table.' '.$where;
        $restotl    = mysqli_query($GLOBALS['con'], $sqltotl);    
        $rowtotl    = mysqli_fetch_array($restotl, MYSQLI_NUM);
        $this->totalCount  = $rowtotl[0];
        $this->totalAmt    = $rowtotl[1];
        $this->sql  = $sqltotl;
        if ($this->totalCount > 0) {
            $sql    = 'SELECT BS.BS_Id, BS.TR_Id, TR.TR_Track, TR.TR_Status, BS.BS_Amount, BS.BS_Date, IT.IT_Name, DS.DS_Description, LC.LC_Name '
            .$table.' '.$join
            .$where
            .' GROUP BY BS.BS_Id ORDER BY ';
            $sortby     = (isset($sortby)) ? trim(strtolower($sortby)) :"track";
            $orderby    = (isset($orderby) && trim($orderby) == "ASC") ? "ASC" :"DESC";
            switch ($sortby) {
                case "date"     : $sql  .= 'BS.BS_Date';  
                break;
                case "track"    : $sql  .= 'TR.TR_Track';   
                break;
                case "amount"   : $sql  .= 'BS.BS_Amount'; 
                break;
                case "id"       : $sql  .= 'BS.BS_Id'; 
                break;
                default         : $sql  .= 'TR.TR_Track'; 
                break;
            }
            $sql    .= ' '.$orderby;
            if (isset($limit) && $limit > 0) {
                $sql .=' LIMIT '.$start.', '.$limit;
            }
            $this->sql  = $sql;
            $res        = mysqli_query($GLOBALS['con'], $sql); 
            $slno       = $start; 
            while ($row = mysqli_fetch_object($res)) {
                $slno++;
                $row->slno              = $slno;
                $this->trackList[]      = $row;
            }
        }
    }
}

?>