<?php
require_once("connection.php");
class AttestationClass {
    
    // Start Of General Functions 
    
    function getDetails($table, $fields, $condition) {
        $count = 0;
        $this->DataArray = array();  
        //echo "SELECT $fields FROM $table " . $condition;
        $result = mysqli_query($GLOBALS['con'],"SELECT $fields FROM $table " . $condition);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
     function getAPMADetails($table, $fields, $condition) {
        $count = 0;
        $this->DataArray = array();  
        //echo "SELECT $fields FROM $table " . $condition;
        $result = mysqli_query($GLOBALS['con'],"SELECT $fields FROM $table " . $condition);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    function getMainProcessDetails($fields,$OFId) {
        $count = 0;
        $this->DataArray = array();  
        //echo "SELECT $fields FROM attestation_process_main AS APM JOIN attestation_process_main_authority AS APMA ON APMA.APMA_Id = APM.APMA_Id";
        $result = mysqli_query($GLOBALS['con'],"SELECT $fields FROM attestation_process_main AS APM
                               JOIN attestation_process_main_authority AS APMA ON APMA.APMA_Id = APM.APMA_Id
                               WHERE APM.OF_Id = '".$OFId."'
                                    ORDER BY APM_Title");
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    function getValue($table, $fields, $condition) {
        //echo "SELECT $fields FROM $table " . $condition;
        $result = mysqli_query($GLOBALS['con'],"SELECT $fields FROM $table " . $condition);
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        return $row[0];
    }
    function getRowDetails($table, $fields, $condition) {
//        echo "SELECT $fields FROM $table " . $condition;
        $result = mysqli_query($GLOBALS['con'],"SELECT $fields FROM $table " . $condition);
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
        return $row;
    }
    function getMainProcessValueInsert($table, $fields, $condition) {
       // echo "SELECT $fields FROM $table " . $condition;
        $result = mysqli_query($GLOBALS['con'],"SELECT $fields FROM $table " . $condition);
        $row = mysqli_fetch_array($result,MYSQLI_NUM);
        return $row[0];
    }
    function getIdWiseName($ID,$Name,$table,$condition){
        $this->DataArray = array();  
        $sql = "SELECT ".$ID." , ".$Name."
                    FROM ".$table." 
                        WHERE ".$condition."  ORDER BY ".$ID;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$row->$ID] = $row->$Name;
        }
    }
    function getIdsList($table, $fields, $condition) {
        $count = 0;//echo 'SELECT GROUP_CONCAT(CAST('. $fields .' AS CHAR)) AS IDS FROM '. $table .' '. $condition;
        $this->DataArray = array(); 
        $result = mysqli_query($GLOBALS['con'],'SELECT GROUP_CONCAT(CAST('. $fields .' AS CHAR)) AS IDS FROM '. $table .' '. $condition);
        return mysqli_result($result, 0,'IDS');
    }
    function getDistinctIdsList($table, $fields, $condition) {
        $count = 0;
        $this->DataArray = array();   
        $result = mysqli_query($GLOBALS['con'],'SELECT GROUP_CONCAT( DISTINCT CAST('. $fields .' AS CHAR)) AS IDS FROM '. $table .' '. $condition);
        return mysqli_result($result, 0,'IDS');
    }
    function insertReturnId($table) {  // insert to table and return last insert id
        $sql = "INSERT INTO $table ( " . implode(', ', array_keys($this->InsertData)) . ") VALUES (" . "'" . implode("','", array_values($this->InsertData)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        return mysqli_insert_id($GLOBALS['con']);
    }
    function insertRecords($table) {
        $sql = "INSERT INTO $table ( " . implode(', ', array_keys($this->Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        if($table != 'balance_sheets') {
            if(mysqli_affected_rows($GLOBALS['con']) > 0)
                return "success";
            else
                return "fail";
        }
    }
    function updateRecords($table,$condition)  {
        $Data = '';
        foreach ($this->Data as $key => $value) {
            $Data = $Data . $key . "='" . $value . "', ";
        }
        $Data = substr($Data, 0, -2);
        $sql = "UPDATE $table SET $Data WHERE $condition";  
        mysqli_query($GLOBALS['con'],$sql);
        //echo $sql;
        return "success";
    }
    function insertMultipleData($table) {
        $sql = "INSERT INTO ".$table." ( " . implode(', ', array_keys($this->Data[0])) . ") VALUES ";
        foreach ($this->Data as $key => $value) {
            $sql .= "( '" . implode("','", array_values($this->Data[$key])) . "'" . "),";
        }
        
        $sql=rtrim($sql, ",");
        mysqli_query($GLOBALS['con'],$sql);
//        if(($table != 'balance_sheets') && ($table !='attestation_job_expense_description')) {
        if(mysqli_affected_rows($GLOBALS['con']) > 0)
            return "success";
        else
            return "fail";
//    }
    }
    function updateMultipleRecords($table,$condition){
        $sql = "UPDATE $table SET ".$this->UpdateData." WHERE ".$condition; 
        mysqli_query($GLOBALS['con'],$sql);
        return "success";
    }
    function insertMultipleRecords($table,$feilds,$value) {
        $sql = "INSERT INTO $table ( " .$feilds. ") VALUES ".$value;
        mysqli_query($GLOBALS['con'],$sql);
            if(mysqli_affected_rows($GLOBALS['con']) > 0)
                return "success";
            else
                return "fail";
    } 
    function deleteRecords($table,$condition) {
        $sql = "DELETE FROM " .$table. $condition;
        mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_affected_rows($GLOBALS['con']) > 0)
            return "success";
        else
            return "fail";
    }
    
    // End of General Functions 
    
    
    
    // Start Of Master Data Functions
    
    /**
     * Function for Listing Authorities in Document Authorities
     */
    function getAuthorityList() {
        $count = 0;
        $this->DataArray = array(); 
        $query = mysqli_query($GLOBALS['con'],"SELECT AAUTH.*,AST.AST_Id,AST.AST_State,AST.AST_CS  
                                FROM attestation_authorities AS AAUTH 
                                    LEFT JOIN attestation_state AS AST ON AAUTH.AST_Id = AST.AST_Id 
                                        ORDER BY AST.AST_State");
        
        $oldASTId = $newASTId = 0 ;
//        $this->DataArray[$row->AST_Id]['AST_AUId'] = '';
        while ($row = mysqli_fetch_object($query)) {
            $newASTId = $row->AST_Id;
            if(($newASTId != $oldASTId) && (!empty($row->AAUTH_Id)) ) {
                $oldCount = $count ;
                $this->DataArray[$oldCount]['AST_AUId']  =  "0,".$row->AAUTH_Id;
                $oldASTId = $newASTId;
            }  else {
                $this->DataArray[$oldCount]['AST_AUId']  = $this->DataArray[$oldCount]['AST_AUId'].",".$row->AAUTH_Id ;
            }
            $this->DataArray[$count]['AAUTH_Id']        = $row->AAUTH_Id;
            $this->DataArray[$count]['AST_Id']          = $row->AST_Id;
            $this->DataArray[$count]['AST_State']       = $row->AST_State;
            $this->DataArray[$count]['AAUTH_Authority'] = $row->AAUTH_Authority;
            $count++;
        }
    }
    
    /**
     * Function for Listing Balance Sheet Settings in Track
     * @param type $Ids
     * @param type $OfId
     */
    function getBalsheetsettingsItems($Ids,$OfId) {
        $this->balshtArrayList = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT AJB.AJBS_Id,AJB.AJBS_Type,AJB.AJBS_Status,AJB.IT_Id,ITM.IT_Name,ITM.MH_Type 
                                FROM attestation_job_balsheet_settings AS AJB
                                    LEFT JOIN  items AS ITM ON ITM.IT_Id =  AJB.IT_Id 
                                        WHERE AJB.OF_Id = ".$OfId ." AND
                                            AJB.AJBS_Type IN (".$Ids.")
                                                ORDER BY AJB.AJBS_Type ASC");
        while ($row = mysqli_fetch_object($result)) {
            $this->balshtArrayList[$row->AJBS_Type] = $row;
        } 
    }
    
    // End of Master Data Functions 
    
    
    // Start Of New Registeration Functions 
    
    /**
     * Function for loading districts in combo while add/edit job
     * @param type $filter
     */
    
    function getDistrict($filter) {
        $count = 0;
        $this->DataArray = array(); 
        $sql = "SELECT DT.DT_Id,DT.DT_Name,ST.ST_Name,CN.CN_Name
                    FROM districts AS DT 
                        LEFT JOIN states AS ST ON DT.ST_Id = ST.ST_Id
                        LEFT JOIN countries AS CN ON ST.CN_Id = CN.CN_Id
                            WHERE DT.DT_Status = 1 ".$filter." ORDER BY DT_Name";
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    /**
     * Function for loading State/Country in combo while add/edit job
     * @param type $filter
     */
    
    function getStateCountry($filter) {
        $count = 0;
        $this->DataArray = array(); 
        $sql = "SELECT ST.ST_Id, ST.ST_Name,CN.CN_Name
                    FROM states AS ST 
                        LEFT JOIN countries AS CN ON ST.CN_Id = CN.CN_Id
                            WHERE ST.ST_Status = 1 ".$filter." ORDER BY ST_Name";
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    function getPlaceCityState($filter) {
        $count = 0;
        $this->DataArray = array(); 
        /*$sql = "SELECT AP.AP_Id, AP.AP_Name,CT.CT_Name, ST.ST_Name
                    FROM address_places AS AP 
                        LEFT JOIN cities AS CT ON CT.CT_Id = AP.CT_Id
                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                            WHERE ST.ST_Status = 1 AND CT.CT_Status = 1 AND AP.AP_Status = 1".$filter." ORDER BY AP.AP_Name";
        */
        $sql = "SELECT AL.ALC_Id, AL.ALC_Name,CT.CT_Name, ST.ST_Name
                    FROM addr_locations AS AL  
                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                            WHERE ST.ST_Status = 1 AND CT.CT_Status = 1 AND AL.ALC_Status = 1".$filter." ORDER BY AL.ALC_Name";
        
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    /**
     * Function for getting Balance sheet settings done.
     * Used for checking whether all items set aganist track operations at the time of new registeration.
     * Used for getting Item id's while adding new job and its payment
     * @param type $OFId
     */
    function getBalsheetsettings($OFId){
        
        $this->DataArray = array();  
        $sql = "SELECT IT_Id, AJBS_Type FROM attestation_job_balsheet_settings WHERE OF_Id=".$OFId;
        $result=mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)){
            $this->DataArray[$row->AJBS_Type] = $row->IT_Id;
        }
    }
    
    /**
     * Function for loading attestation states in combo while add/Edit Document for a job.
     * @param type $filter
     */
    function getAttestationStates($filter) {
        $count = 0;
        $this->DataArray = array();   
        $query = mysqli_query($GLOBALS['con'],"SELECT * FROM attestation_state WHERE AST_Status = 1 ".$filter." ORDER BY AST_State ASC");
        while ($row = mysqli_fetch_object($query)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    /**
     * Generating randon Track name while adding new Job.
     * @param type $id
     * @return type
     */
    
    function generateTrackID($id) {
        if($id) { 
           $AJ_Track_Id = $id;
        }
        else  { 
            $sql = "INSERT INTO tracks ( " . implode(', ', array_keys($this->Track_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Track_Data)) . "'" . ")";
            mysqli_query($GLOBALS['con'],$sql);
            $GLOBALS['lastId'] = mysqli_insert_id($GLOBALS['con']);
            $AJ_Track_Id = "UVR_".$GLOBALS['lastId'];
        }
        
        if($this->getValue('tracks', 'TR_Track', " WHERE TR_Track =  '$AJ_Track_Id'")) {
            $AJ_Track_Id = "UVR_".substr(preg_replace('/[^0-9]+/', '', uniqid().mt_rand(1000,999999)),2,4);
            return $this->generateTrackID($AJ_Track_Id);
        }
           
        mysqli_query($GLOBALS['con'],"UPDATE tracks SET TR_Track ='$AJ_Track_Id' WHERE TR_Id = ".$GLOBALS['lastId']);
        return $GLOBALS['lastId'];
    }
    /**
     * Function for New TRACK Procedures
     */
    function newTrackProcedures($USID,$OFID) {
        $sql = "INSERT INTO `track_procedure` (`US_Id`,`OF_Id`, `TP_CDate`, `TP_MDate`, `TP_Status`) VALUES ('".$USID."','".$OFID."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."', '1')";
        mysqli_query($GLOBALS['con'],$sql);
        //die($sql);
    }
    /**
     * Function for listing All TRACK Procedures
     */
    function listTrackProcedures($filter) {
        $count = 0;
        $this->TrackArray = array();  
        $result = mysqli_query($GLOBALS['con']," SELECT TP_Id, US_Id, TP_From, (
                                SELECT GROUP_CONCAT( DISTINCT CAST( CN_Id AS CHAR ) ) FROM states WHERE FIND_IN_SET( ST_Id, TP_From )) AS CN_Id,  
                                TP_To, TP_Certificate, TP_Procedures, TP_Comment, TP_CDate, TP_MDate, TP_Status
                                FROM track_procedure
                                WHERE ".$filter."
                                ORDER BY TP_Id");
//                                LIMIT 0 , 30
        while($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }	
    }
    /**
     * Function for listing All TRACK Process In A Procedures
     */
    function listProcedureProcess($TPID) {
        $count = 0;
        $this->TrackArray = array();  
        /*$result = mysqli_query($GLOBALS['con']," SELECT APM.APM_Id, APM.APM_Title, TP.TP_Procedures
                                FROM `attestation_process_main` AS APM
                                LEFT JOIN track_procedure AS TP ON FIND_IN_SET( APM.APM_Id, TP.TP_Procedures )
                                WHERE TP.TP_Id = ".$TPID."
                                ORDER BY FIND_IN_SET( APM.APM_Id, TP.TP_Procedures )");*/
        $result = mysqli_query($GLOBALS['con']," SELECT TP_Id, TP_Procedures
                                FROM track_procedure 
                                WHERE TP_Id = ".$TPID);
        while($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }	
    }
    /**
     * Function for listing All Main and Main Process while add/edit Job (Add Process button popup)
     */
    function viewTrackMainProcess($OFId = '') {
        $count = 0; 
        $this->TrackArray = array();  
        $result = mysqli_query($GLOBALS['con'],"SELECT APM_Id, APM_Title, APMA_Id, APM_Status 
                                FROM attestation_process_main  
                                    WHERE APM_Status = 1 
                                        AND OF_Id = ".$OFId."
                                        ORDER BY APM_Title");
        while($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }	
    }
    
    /**
     * Function for listing All Main and Sub Process while add/edit Job (Add Process button popup)
     */
    function viewTrackDocumentProcess($OFId) {
        $count = 0;
        $this->TrackArray = array();  
        $result = mysqli_query($GLOBALS['con'],"SELECT APM.APM_Id, APS.APS_Id, APM.APM_Title, APS.APS_Title 
                                FROM attestation_process_main AS APM 
                                    LEFT JOIN attestation_process_sub AS APS ON APM.APM_Id = APS.APM_Id
                                        WHERE APM.APM_Status = 1 
                                            AND APS.APS_Status = 1 
                                            AND APS.OF_Id = '".$OFId."'
                                                ORDER BY APM.APM_Title, APS.APS_Title");
        while($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }	
    }
    
    /**
     * Function for listing Document Details added while editing Job
     * @param type $AJ_Id
     */
    function jobDocumentDetails($AJ_Id){
        $count = 0;
        $this->DataArray = array();
        $this->SubArray  = array();
        $sqlMain = "SELECT AJD.AJ_Id,AJD.AJD_Id,AJD.AJD_Year,AJD.ADOC_Id,AJD.AJD_UniqueNo,AJD.ST_Id,AJD.APS_Id,AJD.AJD_Amount,AJD.AJD_Status,
                        AJD.AJD_VisitingCNId, AJD.AJD_IssuingCNId, AJD.AJD_VisaType,AJD.LC_Id,AJD.AJD_CDate,AJD.AJD_Comment,AJD.AJD_LastProcess,AJD.AJD_Remarks,
                        AJSD.ASD_Id,AD.ADOC_Document,ST.ST_Name,APS.APS_Title,LC.LC_Name
                        FROM attestation_job_documents AS AJD
                            LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id=AJD.ADOC_Id 
                            LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJD.APS_Id
                            LEFT JOIN attestation_job_supporting_documents AS AJSD ON AJSD.AJD_Id = AJD.AJD_Id
                            LEFT JOIN states AS ST ON ST.ST_Id = AJD.ST_Id
                            LEFT JOIN locations AS LC ON LC.LC_Id = AJD.LC_Id
                                WHERE AJD.AJ_Id = ".$AJ_Id ." AND AJD.AJD_Status != 0";
        /*
         * LEFT JOIN attestation_state AS AST ON AST.AST_Id=AJD.AST_Id 
                        LEFT JOIN attestation_authorities AS AUTH ON AJD.AAUTH_Id = AUTH.AAUTH_Id 
         */
        
        $result = mysqli_query($GLOBALS['con'],$sqlMain);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            if($count)  $ids = $ids.','.$row->AJD_Id;
            else $ids = $row->AJD_Id;
            $count++;
        }
        
        $sqlSub  = "SELECT AJS.*,APS.APS_Title,APM.APM_Title,APM.APM_Id
                        FROM attestation_job_subprocess AS AJS 
                            LEFT JOIN attestation_process_sub  AS APS ON APS.APS_Id = AJS.APS_Id
                            LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id
                                WHERE AJS.AJD_Id IN (".$ids.") ORDER BY AJS.AJS_Order ";
        $resultSub = mysqli_query($GLOBALS['con'],$sqlSub);
        while ($subRow = mysqli_fetch_object($resultSub)) {
            $this->SubArray[$subRow->AJD_Id][] = $subRow;
        }
    }
    
    /**
     * Function for listing All Supporting Documents while add/edit Job
     * @param type $filt
     */
    
    function viewSupportingDocuments($OFId) {
        $count = 0;
        $this->TrackArray = array();  
        $result = mysqli_query($GLOBALS['con'],"SELECT * FROM attestation_supporting_documents
                                WHERE OF_Id = $OFId AND ASD_Status = 1 ORDER BY ASD_Document");
        while($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }	
    }
    
    function getCandidateDetails($AJId) {
        $count = 0;
        $this->DataArray = array();  
        $sql = "SELECT AJ.*,ALC.ALC_Name,CT.CT_Name,ST.ST_Name,LC.LC_Name ,SR.SR_Name ,PL.PL_Name, CN.CN_Name
                    FROM attestation_job_details AS AJ
                        LEFT JOIN addr_locations AS ALC ON AJ.ALC_Id = ALC.ALC_Id 
                        LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id 
                        LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id 
                        LEFT JOIN cities AS CT ON AJ.CT_Id = CT.CT_Id 
                        LEFT JOIN states AS ST ON AJ.ST_Id = ST.ST_Id 
                        LEFT JOIN countries AS CN ON CN.CN_Id = ST.CN_Id 
                        LEFT JOIN locations AS LC ON AJ.AJ_JobDeliverdTo = LC.LC_Id 
                            WHERE AJ_Id = ".$AJId ;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    function getSubmitterDetails($AJId) {
        $count = 0;
        $this->DataArray = array();  
        $sql = "SELECT ASJ.*,ALC.ALC_Name,CT.CT_Name,ST.ST_Name,SR.SR_Name ,PL.PL_Name , CN.CN_Name
                    FROM attestation_job_submitter_details AS ASJ 
                        LEFT JOIN addr_locations AS ALC ON ASJ.ALC_Id = ALC.ALC_Id 
                        LEFT JOIN addr_places AS PL ON ASJ.PL_Id = PL.PL_Id 
                        LEFT JOIN addr_streets AS SR ON ASJ.SR_Id = SR.SR_Id 
                        LEFT JOIN cities AS CT ON ASJ.CT_Id = CT.CT_Id 
                        LEFT JOIN states AS ST ON ASJ.ST_Id = ST.ST_Id 
                        LEFT JOIN countries AS CN ON CN.CN_Id = ST.CN_Id 
                            WHERE AJ_Id = ".$AJId ;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    function getDeliverdToDetails($AJId) {
        $count = 0;
        $this->DataArray = array();  
        $sql = "SELECT AD.*,ALC.ALC_Name,CT.CT_Name,ST.ST_Name,SR.SR_Name ,PL.PL_Name , CN.CN_Name
                    FROM attestation_job_deliver_details AS AD
                        LEFT JOIN addr_locations AS ALC ON AD.ALC_Id = ALC.ALC_Id 
                        LEFT JOIN addr_places AS PL ON AD.PL_Id = PL.PL_Id 
                        LEFT JOIN addr_streets AS SR ON AD.SR_Id = SR.SR_Id 
                        LEFT JOIN cities AS CT ON AD.CT_Id = CT.CT_Id 
                        LEFT JOIN states AS ST ON AD.ST_Id = ST.ST_Id 
                        LEFT JOIN countries AS CN ON CN.CN_Id = ST.CN_Id 
                            WHERE AJ_Id = ".$AJId ;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    /**
     * Funtion for saving Candidate Details at the time of adding new Job.
     * @return type
     */
    
    function newJobDetails(){
        
$sql = "INSERT INTO attestation_job_details ( " . implode(', ', array_keys($this->Job_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Job_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        $AJ_Id = mysqli_insert_id($GLOBALS['con']);

        /* Add invoice and document receipts details */
//        $DocReceiptInsertQuery = mysqli_query($GLOBALS['con'],"INSERT INTO attestation_job_doc_receipts (AJ_Id) VALUES ($AJ_Id)");
//        $AJDR_Id = mysqli_insert_id($GLOBALS['con']);
//        $AJDR_SlNo = 'M- DR- '.str_pad($AJDR_Id, 6, '0', STR_PAD_LEFT);
//        mysqli_query($GLOBALS['con'],"UPDATE attestation_job_doc_receipts SET AJDR_SlNo = '$AJDR_SlNo' WHERE AJDR_Id = $AJDR_Id");

        $InvoiceReceiptInsertQuery = mysqli_query($GLOBALS['con'],"INSERT INTO attestation_job_invoice_receipts (AJ_Id) VALUES ($AJ_Id)");
        $AJIR_Id = mysqli_insert_id($GLOBALS['con']);
        $AJIR_InvoiceNo = 'M- IN- '.str_pad($AJIR_Id, 6, '0', STR_PAD_LEFT);
        mysqli_query($GLOBALS['con'],"UPDATE attestation_job_invoice_receipts SET AJIR_InvoiceNo = '$AJIR_InvoiceNo' WHERE AJIR_Id = $AJIR_Id");
       
        return $AJ_Id; 
    }
    
    /**
     * Funtion for updating Candidate Details at the time of editing new Job.
     * @param type $AJ_Id
     * @return string
     */
    function updateJobDetails($AJ_Id){
        $AJData = '';
        foreach ($this->Job_Data as $key => $value){ 
            $AJData = $AJData .$key ."='".$value."', ";
        }
        $AJData = substr($AJData, 0, -2);
        mysqli_query($GLOBALS['con'],"UPDATE attestation_job_details SET $AJData WHERE AJ_Id = $AJ_Id");
        
        return "Job Details Updates Successfully"; 
    }
    
    /**
     * Function for Showing Receipts(Cash, Document, Invoice) Details while add/Edit Job
     * @param type $AJ_Id
     * @param type $preTally_user_lcid
     * @return type
     */
    function getReceiptsData($AJ_Id) {  // receipt serial numbers and branch name 
       
        $AJIR_InvoiceNo  = $this->getValue('attestation_job_invoice_receipts', 'AJIR_InvoiceNo', ' WHERE AJ_Id = '.$AJ_Id);   // invoice number
        $AJIR_DocumentId = $this->getValue('attestation_job_receipts', 'AJR_DRId', ' WHERE AJ_Id = '.$AJ_Id);   // Document number
        $AJIR_DocumentNo = 'M- DR- '.str_pad($AJIR_DocumentId, 6, '0', STR_PAD_LEFT);
        return json_encode(array($AJIR_DocumentNo,$AJIR_InvoiceNo));
    }
    
    /**
     * Function for Updating Invoice Receipt Details - warehouse/newAttestationCertificate.php
     * @param type $amount
     * @param type $AJ_Id
     */
    function updateDocReceipt($amount,$AJ_Id) {
        $subTot         = sprintf ("%.2f", $amount/1.14);   // 14% of service tax + sub total = total
        $statutoryAmt   = sprintf ("%.2f", $amount*0.4);   // 40% of total
        $serviceTax     = sprintf ("%.2f", $subTot*0.14); 

        mysqli_query($GLOBALS['con'],"UPDATE attestation_job_invoice_receipts SET 
                        AJIR_StatutoryAmt = $statutoryAmt,
                        AJIR_SubTotal     = $subTot,
                        AJIR_ServiceTax   = $serviceTax,
                        AJIR_Total        = '".$amount."' 
                            WHERE AJ_Id = $AJ_Id");
        
    }
    
    /**
     * Function for inserting Cash Receipt Details - warehouse/trackPaymentBilling.php
     * @param type $AJ_Id
     * @param type $ABP_Id
     * @param type $AJDR_Id
     * @return type
     */
    function cashReceiptEntry($AJ_Id,$ABP_Id,$AJDR_Id){
        mysqli_query($GLOBALS['con'],"INSERT INTO attestation_job_cash_receipts (AJ_Id,ABP_Id,AJDR_Id) VALUES ($AJ_Id,$ABP_Id,$AJDR_Id) ");
        return mysqli_insert_id($GLOBALS['con']);
    }
    /**
     * Function for Updating Cash Receipt Details - warehouse/trackPaymentBilling.php
     * @param type $AJ_Id
     * @param type $ABP_Id
     * @param type $AJDR_Id
     * @return type
     */
    function cashReceiptUpdate($AJCR_SlNo,$AJCR_Id){
        mysqli_query($GLOBALS['con'],"UPDATE attestation_job_cash_receipts SET AJCR_SlNo = '$AJCR_SlNo' WHERE AJCR_Id = $AJCR_Id");
    }
    
    /**
     * Function for Inserting Billing Details - warehouse/trackPaymentBilling.php
     * @return type
     */
    function trackBill(){
        $sql    = "INSERT INTO attestation_job_billing ( " . implode(', ', array_keys($this->Billing_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Billing_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
        return $AB_Id = mysqli_insert_id($GLOBALS['con']);
    }
    /**
     * Function for Updating Billing Details - warehouse/trackPaymentBilling.php
     * @param type $AJ_Id
     */
    function updateTrackBill($AJ_Id){
        $BillingData = '';
        foreach ($this->Billing_Data as $key => $value){ 
            $BillingData = $BillingData .$key ."='".$value."', ";
        }
        $BillingData    = substr($BillingData, 0, -2);
        mysqli_query($GLOBALS['con'],"UPDATE attestation_job_billing SET $BillingData WHERE AJ_Id = $AJ_Id");
        
    }
    /**
     * Function for Inserting Billing Payment Details - warehouse/trackPaymentBilling.php
     * @return type
     */
    function trackPayment($AB_Id){
        $sql = "INSERT INTO attestation_job_billing_payment ( " . implode(', ', array_keys($this->Payment_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->Payment_Data)) . "'" . ")";
        mysqli_query($GLOBALS['con'],$sql);
    }
    /**
     * Function for Updating Billing Payment Details - warehouse/trackPaymentBilling.php
     * @param type $AJ_Id
     */
    function updateTrackPayment($ABP_Id){
        $PaymentData = '';
        foreach ($this->Payment_Data as $key => $value){ 
                $PaymentData = $PaymentData .$key ."='".$value."', ";
        }
        $PaymentData    = substr($PaymentData, 0, -2);
        mysqli_query($GLOBALS['con'],"UPDATE attestation_job_billing_payment SET $PaymentData WHERE ABP_Id = $ABP_Id");
        
    }
    
    /**
     * Function for Updating Invoice Receipt Details - warehouse/trackPaymentBilling.php
     * @param type $statutoryAmt
     * @param type $serviceTax
     * @param type $AJ_Id
     * @param type $subTot
     */
    function updateReceipt($statutoryAmt,$serviceTax,$AJ_Id,$subTot){
        $result =  mysqli_query($GLOBALS['con'],"UPDATE attestation_job_invoice_receipts SET
                                    AJIR_StatutoryAmt = '$statutoryAmt',
                                    AJIR_SubTotal     = '$subTot',
                                    AJIR_ServiceTax   = '$serviceTax',
                                    AJIR_Total        = '".$this->Billing_Data['AB_TotalAmount']."' 
                                        WHERE AJ_Id = $AJ_Id");
    }
    
    // End of New Registeration Functions 
    
    
    // Start of Jobs List Module 
    /**
     * Listing all Jobs
     * @param type $filter
     * @param type $Havefilter
     * @param type $pos
     * @param type $cnt
     */     
    function listTrackJob($filter, $Havefilter,$pos,$cnt){

        $this->DataArray = array();
        $sql = " SELECT AJ.AJ_Id, AJ.AJ_FName,TR.TR_Track,UA.US_FName, UA.US_LName, LC.LC_Name, AJ.AJ_Status,
                    COUNT( DISTINCT AJD.AJD_Id ) AS NO_DOC , IFNULL( SUM( AJD.AJD_Amount ), 0 ) AS Amount, 
                    (SELECT COUNT( AJS.AJS_Id ) AS NO_SubPr FROM attestation_job_subprocess AS AJS WHERE FIND_IN_SET( AJS.AJD_Id, GROUP_CONCAT(AJD.AJD_Id ) ) ) AS NO_SubPr
                    FROM attestation_job_details AS AJ
                        LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
                        LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
                        LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
                        LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id  ".$filter."   
                            GROUP BY AJ.AJ_Id ".$Havefilter."
                                ORDER BY AJ.AJ_ReceivedDate DESC LIMIT $pos , $cnt";
        
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$row->AJ_Id] = $row;
        }
    }
    
    /**
     * Get Number of Jobs added.
     * @param type $filter
     * @param type $Havefilter
     * @return type
     */
    function listJobCount($filter,$Havefilter){
        $sql = "SELECT COUNT( DISTINCT AJ.AJ_Id ) AS NO_DOC
                    FROM attestation_job_details AS AJ
                        LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
                        LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
                        LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
                        LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id  ".$filter."   
                            GROUP BY AJ.AJ_Id ".$Havefilter."
                                ORDER BY AJ.AJ_ReceivedDate DESC ";
        $query = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_num_rows($query);
        
    }
    
    /**
     * Showing details of Jobs in Jobs List (While clicking +)
     * @param type $AJ_Id
     */
    function getSubProcess($AJ_Id) {
        $count = 0;
        $sql = "SELECT AJD.AJD_Id, AD.ADOC_Document, AJS.AJS_Id, AJS.APS_Id, APS.APS_Title, APM.APM_Title, AJS.AJS_Status,
                    (SELECT group_concat( ASD.ASD_Document ) FROM attestation_supporting_documents AS ASD WHERE FIND_IN_SET( ASD.ASD_Id, AJSD.ASD_Id ) ) AS ASD_Document
                        FROM  attestation_job_documents AS AJD 
                            LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id=AJD.ADOC_Id 
                            LEFT JOIN attestation_job_subprocess AS AJS ON AJS.AJD_Id = AJD.AJD_Id
                            LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJS.APS_Id 
                            LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id
                            LEFT JOIN attestation_job_supporting_documents AS AJSD ON AJSD.AJD_Id = AJD.AJD_Id
                                WHERE  AJD_Status != 0  AND AJD.AJ_Id = ".$AJ_Id;
        $result =  mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$row->AJD_Id][] = $row;
            $count++;
        }
    }
    // End of Jobs List Module 
    
  
    // Start of Document Wise List Module
    /**
     * Creating Group Reference ID
     * @param type $table
     * @param type $fields
     * @param type $condition
     * @return string
     */
    function generateGroupNumber($table, $fields, $condition) {
        $lastGrupNo = $this->getValue($table, $fields, $condition);
        if(!$lastGrupNo){
            $Number = 1;
        }else{
            $Number = substr($lastGrupNo, 3);
            $Number++;
        }
        $AJG_Number = 'GP_'.str_pad($Number, 6, '0', STR_PAD_LEFT);
        return $AJG_Number;
    } 
    
    /**
     * Function to load Main Process of while updating process.
     * @param type $gpId
     * @param type $docId
     */
    function getSubMainProcess($DocId) {
        $count = 0;
        $this->DataArray = array();
        $sql = "SELECT DISTINCT APS.APM_Id,APM.APM_Title
                                FROM attestation_job_documents AS AJD
                                    LEFT JOIN attestation_job_subprocess AS AJS ON AJS.AJD_Id = AJD.AJD_Id AND AJS.AJS_Status IN (1,2)
                                    LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJS.APS_Id
                                    LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id
                                        WHERE AJD.AJD_Id IN (".$DocId.")  ORDER BY APM.APM_Title ASC";
        $query = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($query)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    /**
     * Function to load Sub Process of while updating process.
     * @param type $APM_Id
     * @param type $AJD_Id
     */
    function getSubProcessList($APM_Id,$AJD_Id) {
        $count = 0;
        $this->DataArray = array(); 
        $query = mysqli_query($GLOBALS['con'],"SELECT DISTINCT APS.APS_Id,APS.APS_Title
                                FROM attestation_job_documents AS AJD
                                    LEFT JOIN attestation_job_subprocess AS AJS ON AJS.AJD_Id = AJD.AJD_Id AND AJS.AJS_Status IN (1,2) 
                                    LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJS.APS_Id
                                    LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id
                                        WHERE AJD.AJD_Id IN (".$AJD_Id.") AND APS.APM_Id = ".$APM_Id
                        );
        while ($row = mysqli_fetch_object($query)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    /**
     * Updating Process from Document wise list
     * @return string
     */
    function updateGpProcess() { 
        $sql = "UPDATE attestation_job_subprocess AS AJS 
                    LEFT JOIN  attestation_job_documents AS AJD ON AJD.AJD_Id = AJS.AJD_Id 
                     SET AJS.AJS_Status = '".$this->DataArray['AJS_Status']."' 
                        WHERE  AJS.APS_Id = ".$this->DataArray['APS_Id']." 
                               AND AJD.AJD_Id IN (". $this->DataArray['AJD_Id'].")";
                
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result==1) {
            if(mysqli_affected_rows($GLOBALS['con']) > 0)
                return "success";
            else
                return "alredy";
        } else {
            return "fail";
        }
    }
    function  getSubProcessDocumentIds($APSId,$OFId,$stDate,$enDate){
        
        if($stDate!='' && $enDate!=''){
            $stDate  = date("Y-m-d", strtotime($stDate));
            $enDate  = date("Y-m-d", strtotime($enDate));
            $filter =" AND AJ.AJ_ReceivedDate   between '".$stDate."' AND '".$enDate."'";
        }elseif($stDate=='' && $enDate!=''){
            $enDate  = date("Y-m-d", strtotime($enDate));
            $filter =" AND AJ.AJ_ReceivedDate   <=  '".$enDate."'";
        }elseif($stDate!='' && $enDate==''){
            $stDate  = date("Y-m-d", strtotime($stDate));
            $filter =" AND AJ.AJ_ReceivedDate   >=  '".$stDate."'";
        }


       /* $sql = 'SELECT GROUP_CONCAT(CAST(AJS.AJD_Id AS CHAR)) AS IDS 
                    FROM attestation_job_subprocess AS AJS 
                        LEFT JOIN attestation_job_documents AS AJD ON AJD.AJD_Id = AJS.AJD_Id 
                        LEFT JOIN attestation_job_details AS AJ ON AJD.AJ_Id = AJ.AJ_Id
                            WHERE AJ.AJ_Status != 0
                                AND AJ.AJ_Status != 2
                                AND AJD.AJD_Status != 0 
                                AND AJS.APS_Id = '. $APSId.'
                                AND AJ.OF_Id = '.$OFId. $filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_result($result, 0,'IDS');
        */
        
       $sql = 'SELECT AJS.AJD_Id AS IDS 
                    FROM attestation_job_subprocess AS AJS 
                        LEFT JOIN attestation_job_documents AS AJD ON AJD.AJD_Id = AJS.AJD_Id 
                        LEFT JOIN attestation_job_details AS AJ ON AJD.AJ_Id = AJ.AJ_Id
                            WHERE AJ.AJ_Status != 0
                                AND AJ.AJ_Status != 2
                                AND AJD.AJD_Status != 0 
                                AND AJS.APS_Id = '. $APSId.'
                                AND AJ.OF_Id = '.$OFId. $filter;
        $result = mysqli_query($GLOBALS['con'],$sql);        
         while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row->IDS;
            $count++;
        }
        $string_ids=  implode($this->DataArray, ",");
        return $string_ids;
    } 

    function getNextProcessDocumentIds($subProcessDocIds,$APSId){
        $this->DocIdArray = array();
        
        $sql = "SELECT * FROM attestation_job_subprocess AS AJS WHERE AJD_Id IN (".$subProcessDocIds.") ORDER BY AJD_Id ASC, AJS_Order ASC";
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        while($row = mysqli_fetch_object($result)){
            $count = 0;
            $curDoc = $row->AJD_Id;
            if($oldDoc != $curDoc){
                $CompletedDocFlag[$row->AJD_Id] = 0 ;
                $oldDoc = $curDoc;
                $count++;
            }
            
//            echo $CompletedDocFlag[$row->AJD_Id]."---".$count.'--'.$row->AJD_Id."---".$row->APS_Id."---".$APSId."---".$row->APS_Status."<br>\n";
            if($count == 1 && $row->APS_Id == $APSId && ( $row->AJS_Status == 1 || $row->AJS_Status == 2 ))
                array_push($this->DocIdArray,$row->AJD_Id);
            if($CompletedDocFlag[$row->AJD_Id] == 1 && $row->APS_Id == $APSId && ( $row->AJS_Status == 1 || $row->AJS_Status == 2 )) {
                array_push($this->DocIdArray,$row->AJD_Id);
            }
            $CompletedDocFlag[$row->AJD_Id] = $row->AJS_Status == 3 || $row->AJS_Status == 4 ? 1 : 0;
        }
    }
    // End of Document Wise List Module
       
    /**
     * 
     * @return string
     */
    function listTrackDocumnet($filter, $orderBy='' , $pos,$cnt,$flag){
        $count = 0;
        
        $this->DataArray = array();
        $this->SubArray = array();
        if($flag){ //notification list
        $query = mysqli_query($GLOBALS['con'],"SELECT AJDRD.AJDRD_Id,AJD.AJD_Id,AJDRD.AJG_Id ,AJDRD.Receive_LC_Id ,AJDRD.AJNB_Id ,AD.ADOC_Document,AJDT.AJ_FName,TR.TR_Track,AJDRD.Send_LC_Id,AJD.AJD_Status,AJD.AJD_Status,AJDRD.AJDRD_Status,LC.LC_Name
                                FROM attestation_job_notifi_batch AS AJNB 
                                    LEFT JOIN  attestation_job_doc_reference_details AS AJDRD ON AJDRD.AJNB_Id = AJNB.AJNB_Id
                                    LEFT JOIN  locations AS LC ON LC.LC_Id = AJDRD.Send_LC_Id
                                    LEFT JOIN attestation_job_documents  AS AJD ON AJD.AJD_Id = AJDRD.AJD_Id
                                    LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                    LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                    LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id".$filter." 
                                        ORDER BY AD.ADOC_Document LIMIT ".$pos.",".$cnt
                            );
        }else{ // Documnet wise list
            $query = mysqli_query($GLOBALS['con'],"SELECT AJD.AJD_Id,AD.ADOC_Document,AJDT.AJ_FName,TR.TR_Track,UA.US_FName,UA.US_LName,LC.LC_Name,LC.LC_Name,AJD.AJD_Status 
                                    FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                            ".$filter." 
                                                ORDER BY ".$orderBy." AJDT.AJ_ReceivedDate DESC LIMIT ".$pos.",".$cnt
                            );
        }
  
        while ($row = mysqli_fetch_object($query)) {
            $this->DataArray[$row->AJD_Id] = $row;
            if($count) {
                $ids = $ids.','.$row->AJD_Id;
            }else {
              $ids = $row->AJD_Id;
            }
            $count++;
        }
        $subPrQuery = mysqli_query($GLOBALS['con'],"SELECT AJS.*,APS.APS_Title,APM.APM_Title FROM attestation_job_subprocess AS AJS 
                                LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id=AJS.APS_Id
                                LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id
                                WHERE AJS.AJD_Id IN (".$ids.")"
                            );
        while ($SubRow = mysqli_fetch_object($subPrQuery)) {
            $this->SubArray[$SubRow->AJD_Id][] = $SubRow;
        }
    }
    function listTrackDocumnetCount($filter){
        $query = mysqli_query($GLOBALS['con'],"SELECT AJD.AJD_Id
                                FROM attestation_job_documents AS AJD 
                                    LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                    LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                    LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                    LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                    LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                    ".$filter
                            );
        return mysqli_num_rows($query);
    }
    
      /**
     * Function to list the group of document.
     * @return string
     */
    function listTrackDocumnetGp($dateFiltter,$pos,$cnt){
        $count = 0;
        $this->DataArray = array(); 
      
        $query = mysqli_query($GLOBALS['con'],"SELECT AJG.*,COUNT(DISTINCT AJGD.AJGD_Id) AS docCount
                                FROM attestation_job_group AS AJG
                                    LEFT JOIN attestation_job_group_doc AS AJGD ON AJG.AJG_Id = AJGD.AJG_Id
                                    LEFT JOIN  attestation_job_documents AS AJD ON AJD.AJD_Id = AJGD.AJD_Id
                                    LEFT JOIN  attestation_job_details AS AJDT ON  AJDT.AJ_Id = AJD.AJ_Id 
                                    LEFT JOIN  tracks AS TR ON TR.TR_Id = AJDT.AJ_Track_Id 
                                        ".$dateFiltter." GROUP BY AJG.AJG_Id ORDER BY AJG.AJG_Cdate DESC LIMIT ".$pos.",".$cnt
                            );
        while ($row = mysqli_fetch_object($query)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
     /**
     * Function to get count of group .
     * @return string
     */
    function listTrackDocumnetGpCount($dateFiltter){
        $query = mysqli_query($GLOBALS['con'],"SELECT AJG.*,COUNT(DISTINCT AJGD.AJGD_Id) AS docCount
                                FROM attestation_job_group AS AJG
                                    LEFT JOIN attestation_job_group_doc AS AJGD ON AJG.AJG_Id = AJGD.AJG_Id
                                    LEFT JOIN  attestation_job_documents AS AJD ON AJD.AJD_Id = AJGD.AJD_Id
                                    LEFT JOIN  attestation_job_details AS AJDT ON  AJDT.AJ_Id = AJD.AJ_Id 
                                    LEFT JOIN  tracks AS TR ON TR.TR_Id = AJDT.AJ_Track_Id ".$dateFiltter." GROUP BY AJG.AJG_Id"
                            );
        return mysqli_num_rows($query);
        
    }
    
    // Start of Track- Dashboard Module
    function countEnquiries($ofid){
        $sql="SELECT COUNT(AE_Id) FROM attestation_job_enquiry AS AE LEFT JOIN users_auth AS UA ON UA.US_Id=AE.US_Id WHERE UA.OF_Id=".$ofid." AND AE.AE_Status=1";
        $result=mysqli_query($GLOBALS['con'],$sql);
        $row=  mysqli_fetch_row($result);
        return $row[0];
    }
    function getDashBoardData($ofid){       
        $this->DataArray=array();
        $sql="SELECT AJ.AJ_Status,COUNT(AJ.AJ_Id) AS NOS FROM attestation_job_details as AJ
            WHERE AJ.OF_Id=".$ofid." GROUP BY AJ.AJ_Status";
        $result=  mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_assoc($result)){            
            $this->DataArray[$row["AJ_Status"]]=$row["NOS"];
        }    
    }
    function getBussinessAmount($ofid,$start,$end){        
        $this->BussAmt=array(0,0,0,0,0,0,0,0,0,0,0,0);
        $sql="SELECT SUM(AJD.AJD_Amount) AS TOTAL_BUSS,MONTH(AJ.AJ_ReceivedDate) AS MNTH 
            FROM attestation_job_documents AS AJD
            LEFT JOIN attestation_job_details AS AJ ON AJD.AJ_Id=AJ.AJ_Id            
            WHERE AJ.OF_Id=".$ofid." AND AJ.AJ_Status !=0 AND AJ.AJ_Status !=2 AND AJD.AJD_Status!=0
                AND  AJ.AJ_ReceivedDate BETWEEN '".$start."' AND '".$end."'    
            GROUP BY MONTH(AJ.AJ_ReceivedDate)";
        $result=  mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_assoc($result)){            
            $this->BussAmt[$row["MNTH"]-1]=$row;
        }  
    }   
    function getPaymentAmount($ofid,$start,$end){
         $this->PayAmt=array(0,0,0,0,0,0,0,0,0,0,0,0);
       $sql="SELECT SUM(AJP.ABP_AmountRecieved) AS TOTAL_PAY,MONTH(AJ.AJ_ReceivedDate) AS MNTH 
                FROM attestation_job_billing AS AJB 
                LEFT JOIN attestation_job_billing_payment AS AJP ON AJB.AB_Id = AJP.AB_Id  
                LEFT JOIN attestation_job_details AS AJ ON AJ.AJ_Id=AJB.AJ_Id
                WHERE AJ.OF_Id=".$ofid." AND ABP_AmountRecieved != 0 AND AJ.AJ_Status !=0 AND AJ.AJ_Status !=2 
                 AND AJ.AJ_ReceivedDate BETWEEN '".$start."' AND '".$end."'    
                GROUP BY MONTH(AJ.AJ_ReceivedDate)";        
        $result=  mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_assoc($result)){            
            $this->PayAmt[$row["MNTH"]-1]=$row;
        }       
    }
    function getCancelAmount($OFId,$start,$end){
        $this->CncJobAmt=array(0,0,0,0,0,0,0,0,0,0,0,0);
        
//        $sql="SELECT SUM(AJB.AB_TotalAmount) AS TOTAL_CNC,MONTH(AJ.AJ_ReceivedDate) AS MNTH 
//                FROM attestation_job_billing AS AJB 
//                LEFT JOIN attestation_job_billing_payment AS AJP ON AJB.AB_Id = AJP.AB_Id  
//                LEFT JOIN attestation_job_details AS AJ ON AJ.AJ_Id=AJB.AJ_Id
//                WHERE AJ.OF_Id=".$ofid." AND ABP_AmountRecieved != 0 AND AJ.AJ_Status =2  
//                    AND AJ.AJ_ReceivedDate BETWEEN '".$start."' AND '".$end."'    
//                GROUP BY MONTH(AJ.AJ_ReceivedDate)";
        
        $sql = "SELECT SUM(AJD.AJD_Amount) AS TOTAL_CNC, MONTH(AJ.AJ_ReceivedDate) AS MNTH 
                    FROM attestation_job_details AS AJ
                        LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id
                            WHERE AJ.OF_Id=".$OFId." 
                                AND AJ.AJ_Status =2  
                                AND AJ.AJ_ReceivedDate BETWEEN '".$start."' AND '".$end."'    
                                    GROUP BY MONTH(AJ.AJ_ReceivedDate)";
        $result=  mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_assoc($result)){            
            $this->CncJobAmt[$row["MNTH"]-1]=$row;
        }       
    }
    function getTopCollectors($ofid,$start,$end){        
        $this->TopBuss=array();        
        $sql="SELECT LC.LC_Id,LC.LC_Name,SUM(AJD.AJD_Amount) AS TOTAL_BUSS
                FROM attestation_job_documents AS AJD
                LEFT JOIN attestation_job_details AS AJ ON AJD.AJ_Id=AJ.AJ_Id            
                LEFT JOIN locations AS LC ON AJ.LC_Id=LC.LC_Id            
                WHERE AJ.OF_Id=".$ofid." AND AJ.AJ_ReceivedDate BETWEEN '".$start."' AND '".$end."' AND AJD.AJD_Status!=0 AND AJ.AJ_Status !=0 AND AJ.AJ_Status !=2 GROUP BY LC.LC_Id ORDER BY TOTAL_BUSS DESC";
        
        $result=  mysqli_query($GLOBALS['con'],$sql);
        $count=0;
        while($row=mysqli_fetch_assoc($result)){            
            $this->TopBuss[$count]=$row;
            $count++;
        }  
    }
    function getJobCategories($ofid,$currmnth,$year){
         $strmnth=$currmnth-5;
       //Fetching Top 6 in bussiness collected in branchwise 
       $this->JobsAmt=array();
        $sql="SELECT LC.LC_Name,SUM(AJD.AJD_Amount) AS TOTAL_BUSS
            FROM attestation_job_documents AS AJD
            LEFT JOIN attestation_job_details AS AJ ON AJD.AJ_Id=AJ.AJ_Id 
            LEFT JOIN locations AS LC ON AJ.LC_Id=LC.LC_Id        
            WHERE AJ.OF_Id=".$ofid." AND  MONTH(AJ.AJ_ReceivedDate) = ".$currmnth." AND YEAR(AJ.AJ_ReceivedDate) = ".$year."  AND AJD.AJD_Status!=0 AND AJ.AJ_Status !=0 AND AJ.AJ_Status !=2
            GROUP BY AJ.LC_Id ORDER BY TOTAL_BUSS DESC";
        $result=  mysqli_query($GLOBALS['con'],$sql);
        $count=0;
        while($row=mysqli_fetch_assoc($result)){                        
            $this->JobsAmt[$count]=$row;            
            $count++;
        }  
      //Fetching Top 6 in income collected in branchwise 
        $this->IncAmt=array();
        $sql="SELECT LC.LC_Name,SUM(ABP_AmountRecieved) AS TOTAL_PAY 
                FROM attestation_job_billing AS AJB 
                LEFT JOIN attestation_job_billing_payment AS AJP ON AJB.AB_Id = AJP.AB_Id  
                LEFT JOIN attestation_job_details AS AJ ON AJ.AJ_Id=AJB.AJ_Id
                LEFT JOIN locations AS LC ON AJ.LC_Id=LC.LC_Id 
                WHERE AJ.OF_Id=".$ofid." AND MONTH(AJ.AJ_ReceivedDate) = ".$currmnth." AND YEAR(AJ.AJ_ReceivedDate) = ".$year." AND AJ.AJ_Status !=0 AND AJ.AJ_Status !=2 AND ABP_AmountRecieved != 0
                GROUP BY AJ.LC_Id ORDER BY TOTAL_PAY DESC";
        $result=  mysqli_query($GLOBALS['con'],$sql);
        $count=0;
        while($row=mysqli_fetch_assoc($result)){            
            $this->IncAmt[$count]=$row;
            $count++;
        }  
      //Fetching Top 6  Cancelled Jobs in branchwise  
        $this->CncAmt=array();
//        $sql="SELECT LC.LC_Name,SUM(AJB.AB_TotalAmount) AS TOTAL_CNC 
//                FROM attestation_job_billing AS AJB 
//                LEFT JOIN attestation_job_billing_payment AS AJP ON AJB.AB_Id = AJP.AB_Id  
//                LEFT JOIN attestation_job_details AS AJ ON AJ.AJ_Id=AJB.AJ_Id
//                LEFT JOIN locations AS LC ON AJ.LC_Id=LC.LC_Id 
//                WHERE AJ.OF_Id=".$ofid." AND MONTH(AJ.AJ_ReceivedDate) = ".$currmnth." AND YEAR(AJ.AJ_ReceivedDate) = ".$year." AND AJ.AJ_Status=2 AND ABP_AmountRecieved != 0
//                GROUP BY AJ.LC_Id ORDER BY TOTAL_CNC DESC";
        $sql = "SELECT LC.LC_Name,SUM(AJD.AJD_Amount) AS TOTAL_CNC 
                    FROM attestation_job_details AS AJ
                        LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id
                        LEFT JOIN locations AS LC ON AJ.LC_Id = LC.LC_Id
                            WHERE AJ.OF_Id=".$ofid." 
                                AND MONTH(AJ.AJ_ReceivedDate) = ".$currmnth." 
                                AND YEAR(AJ.AJ_ReceivedDate) = ".$year."
                                AND AJ.AJ_Status =2  
                                    GROUP BY AJ.LC_Id ORDER BY TOTAL_CNC DESC";
        $result=  mysqli_query($GLOBALS['con'],$sql);
        $count=0;
        while($row=mysqli_fetch_assoc($result)){            
            $this->CncAmt[$count]=$row;
            $count++;
        }  
        
    }
    function JobSummary($ofid,$start,$end){         
        $this->JobArray=array();
        $sql_inc="SELECT SUM(AJP.ABP_AmountRecieved) AS JOB_INC 
                FROM attestation_job_billing AS AJB 
                LEFT JOIN attestation_job_billing_payment AS AJP ON AJB.AB_Id = AJP.AB_Id  
                LEFT JOIN attestation_job_details AS AJ ON AJ.AJ_Id=AJB.AJ_Id                
                WHERE AJ.OF_Id=".$ofid." AND AJ.AJ_ReceivedDate BETWEEN '".$start."' AND '".$end."'  AND AJ.AJ_Status !=0 AND AJ.AJ_Status !=2 AND ABP_AmountRecieved != 0";
        $result_inc =  mysqli_query($GLOBALS['con'],$sql_inc);
        $row_inc    =  mysqli_fetch_assoc($result_inc);
        $this->JobArray["Total_Inc"]=$row_inc["JOB_INC"] ? $row_inc["JOB_INC"] : 0;
        $sql_exp="SELECT SUM(BS.BS_Amount) AS JOB_EXP FROM balance_sheets AS BS 
                LEFT JOIN items AS IT ON IT.IT_Id=BS.IT_Id
                LEFT JOIN sub_heads AS SH ON SH.SH_Id=IT.SH_Id
                LEFT JOIN main_heads AS MH ON MH.MH_Id=SH.MH_Id
                LEFT JOIN locations AS LC ON LC.LC_Id=BS.LC_Id
                WHERE MH.MH_Type=2 AND SH.SH_Track=1 AND LC.OF_Id=".$ofid." AND BS.BS_Date BETWEEN '".$start."' AND '".$end."'";        
        $result_exp =  mysqli_query($GLOBALS['con'],$sql_exp);
        $row_exp    =  mysqli_fetch_assoc($result_exp);
        $this->JobArray["Total_Exp"]    =$row_exp["JOB_EXP"] ? $row_exp["JOB_EXP"] : 0;
        $this->JobArray["Profit"]       =$row_inc["JOB_INC"] - $row_exp["JOB_EXP"];
        
        $sql_jobs = "SELECT SUM(AJD.AJD_Amount) AS TOTAL_CNC
                        FROM attestation_job_details AS AJ
                            LEFT JOIN attestation_job_documents AS AJD ON AJ.AJ_Id = AJD.AJ_Id
                                WHERE AJ.OF_Id=".$ofid." AND AJ.AJ_Status = 4 AND AJ.AJ_ReceivedDate BETWEEN '".$start."' AND '".$end."'";        
        $result_jobs =  mysqli_query($GLOBALS['con'],$sql_jobs);
        $row_jobs    =  mysqli_fetch_assoc($result_jobs);
        $this->JobArray["Total_CompJob"] = $row_jobs["TOTAL_CNC"] ? $row_jobs["TOTAL_CNC"] : 0;
        
    }
    
    // End of Track- Dashboard Module
    
    
    // Start of Track- Enquiry Module
    function listTrackEnquiry($filter,$pos,$cnt){
        $count = 0;
        $this->DataArray = array();

        $sql = "SELECT AE.AE_Id,AE.AE_Name,AE.AE_Mobile,AE.AE_Email,UA.US_FName,UA.US_LName,LC.LC_Name,AE.AE_Status, 
                    COUNT(EF1.AE_Id) AS NO_FollowUp,EF.EF_CDate,EF.US_Id,EF.LC_Id,
                    CONCAT(UA1.US_FName, ' ' ,UA1.US_LName) AS LastUser,LC1.LC_Name AS LastBranch , EF.EF_Chance, EF.EF_NextDate
                        FROM attestation_job_enquiry AS AE 
                        LEFT JOIN attestation_enquiry_followup AS EF ON EF.AE_Id = AE.AE_Id
                        LEFT JOIN attestation_enquiry_followup AS EF1 ON EF1.AE_Id = AE.AE_Id
                        LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                        LEFT JOIN users_auth AS UA1 ON UA1.US_Id = EF.US_Id
                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id
                        LEFT JOIN  locations AS LC1 ON LC1.LC_Id = EF.LC_Id
                        WHERE ".$filter." 
                            AND CASE WHEN EF.EF_CDate != '' THEN EF.EF_CDate = (SELECT MAX(EF_CDate) FROM attestation_enquiry_followup GROUP BY AE_Id HAVING AE_Id = EF.AE_Id) ELSE 1 END 
                                GROUP BY AE.AE_Id
                                    ORDER BY AE.AE_CDate DESC, EF.EF_CDate DESC  LIMIT ".$pos.",".$cnt;
        
        $query = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($query)) {           
            $this->DataArray[$count] = $row;
            $count++;
        }         
    }   
    
    function listTrackEnquiryCount($filter){
        $sql = "SELECT COUNT(DISTINCT AE.AE_Id)
                    FROM attestation_job_enquiry AS AE 
                        LEFT JOIN attestation_enquiry_followup AS EF ON EF.AE_Id = AE.AE_Id
                        LEFT JOIN attestation_enquiry_followup AS EF1 ON EF1.AE_Id = AE.AE_Id
                        LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                        LEFT JOIN users_auth AS UA1 ON UA1.US_Id = EF.US_Id
                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id
                        LEFT JOIN  locations AS LC1 ON LC1.LC_Id = EF.LC_Id
                        WHERE ".$filter." 
                            AND CASE WHEN EF.EF_CDate != '' THEN EF.EF_CDate = (SELECT MAX(EF_CDate) FROM attestation_enquiry_followup GROUP BY AE_Id HAVING AE_Id = EF.AE_Id) ELSE 1 END 
                                ORDER BY AE.AE_CDate DESC, EF.EF_CDate DESC";
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row    = mysqli_fetch_row($result);
        return $row[0];
    }
    
    function getEnquiryDetails($AEId){
        $this->DataArray = array(); 
        $count = 0;
        
        $sql = 'SELECT AE.* , CONCAT(US.US_FName," ", US.US_LName) AS US_Name ,AD.ADOC_Document,MP.APM_Title AS LastProcess,SP.APS_Title,
                        SR.SR_Name, PL.PL_Name, ALC.ALC_Name, CT.CT_Name,ST.ST_Name,CN.CN_Name
                            FROM attestation_job_enquiry AS AE 
                                LEFT JOIN users_auth AS US ON US.US_Id = AE.US_Id
                                LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AE.AE_Certificate
                                LEFT JOIN attestation_process_main AS MP ON MP.APM_Id = AE.AE_LastProcess
                                LEFT JOIN attestation_process_sub AS SP ON SP.APS_Id = AE.APS_Id
                                LEFT JOIN addr_streets AS SR ON AE.SR_Id = SR.SR_Id 
                                LEFT JOIN addr_places AS PL ON AE.PL_Id = PL.PL_Id 
                                LEFT JOIN addr_locations AS ALC ON AE.ALC_Id = ALC.ALC_Id 
                                LEFT JOIN cities AS CT ON AE.CT_Id = CT.CT_Id
                                LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id
                                LEFT JOIN countries AS CN ON ST.CN_Id = CN.CN_Id
                                    WHERE AE.AE_Id = '.$AEId;
        
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
        
        
    }
    // End of Track- Enquiry Module
    
    function getJobDetails($jobIds) {
        $this->DataArray = array(); 
        $result = mysqli_query($GLOBALS['con'],"SELECT AJ_Id,AJD_Id,AJD_Status FROM attestation_job_documents 
            WHERE AJD_Status!=0 AND AJ_Id IN (".$jobIds.")");
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$row->AJ_Id][] = $row;
        }
    }

    function reciveDocument($value,$docId) {
        if($this->updateRecords('attestation_job_documents', 'AJD_Id IN ('.$docId.')')!='success'){
            echo 'fail1';
        }else {
             $query = mysqli_query($GLOBALS['con'],"INSERT INTO attestation_job_doc_reference_details (`AJD_Id`,`AJG_Id`,`Send_LC_Id`,`Receive_LC_Id`, `AJNB_Id`, `AJDRD_Status`, `AJDRD_CDate`) VALUES ".$value);
            if(mysqli_affected_rows($GLOBALS['con']) > 0) {
                return 'success';
            } else {
                return 'fail2';
            }
        }
    }
        
    function getDocmentStatus($jobIds) {
        $this->StatusArray = array(); 
        $result = mysqli_query($GLOBALS['con'],"SELECT AJD_Id,AJD_Status FROM attestation_job_documents 
            WHERE AJD_Status!=0 AND AJ_Id IN (".$jobIds.")");
        while ($row = mysqli_fetch_object($result)) {
            $this->StatusArray[$row->AJD_Id] = $row->AJD_Status;
        }
    }
    
    function getSubprocessStatus($jobIds) {
       $this->SubProStusArray = array(); 
        $result = mysqli_query($GLOBALS['con'],"SELECT AJS_Id,AJD_Id,APS_Id,AJS_Status FROM attestation_job_subprocess 
                                WHERE AJD_Id IN (
                                    SELECT AJD_Id FROM attestation_job_documents WHERE AJ_Id = $jobIds
                                )");
        while ($row = mysqli_fetch_object($result)) {
            $this->SubProStusArray[$row->AJD_Id][$row->APS_Id] = $row->AJS_Status;
        } 
    }
    /**
     * Function used in update Process and Send Document
     * @param type $docIds
     */
    function getDocTrackData($docIds) {
        $this->docArrayList = array();
        $result = mysqli_query($GLOBALS['con'],"SELECT AJD.AJD_Id,AJ.AJ_ReceivedDate,AJ.AJ_Track_Id, AJ.AJ_Id, AJ.AJ_Status, AJD.AJD_Status
                                FROM attestation_job_documents AS AJD 
                                    LEFT JOIN attestation_job_details AS AJ ON AJ.AJ_Id = AJD.AJ_Id
                                        WHERE AJD.AJD_Id IN(".$docIds.")");
        while ($row = mysqli_fetch_object($result)) {
            $this->docArrayList[$row->AJD_Id] = $row;
        } 
    }
    
    
    
    // Starts Notification Module 
    
    /**
     * Function for showing Notification items.
     * @param type $filter
     * @return type
     */
    
    function getBatchDetails($filter,$start,$end) {
        $count = 0;
        $LIMIT = "";
        $this->DataArray = array();  
        if($start || $end)
                    $LIMIT = " LIMIT $start,$end";
        
        $sql = 'SELECT AJNB.AJNB_Id,AJNB.AJNB_Description,COUNT(DISTINCT AJDRD.AJDRD_Id) AS NO_DOC,AJNB.AJNB_Status
                    FROM attestation_job_notifi_batch AS AJNB
                        LEFT JOIN attestation_job_doc_reference_details AS AJDRD ON AJDRD.AJNB_Id = AJNB.AJNB_Id
                            WHERE '.$filter.'
                                GROUP BY AJNB.AJNB_Id ORDER BY AJNB.AJNB_Cdate DESC '.$LIMIT;
        
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $row ->img = ($row->AJNB_Status == 0)? "<img src='images/icon/mailCl.png' style='margin:2px 0; cursor:pointer;'/>" :"<img src='images/icon/mailOp.png' style='margin:2px 0; cursor:pointer;'/>";
            $row ->id  = $count+1 .')';
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    /**
     * Function for getting Count of Notification items.
     * @param type $filter
     * @return type
     */
    function getBatchDetailsCount($filter){
        $sql = "SELECT COUNT(DISTINCT AJNB.AJNB_Id) FROM attestation_job_notifi_batch AS AJNB
                    LEFT JOIN attestation_job_doc_reference_details AS AJDRD ON AJDRD.AJNB_Id = AJNB.AJNB_Id
                        WHERE ". $filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row    = mysqli_fetch_row($result);
        return $row[0];
    }
    // Ends Notification Module 
    
    function getJobTrackingDetails($AJId){
        $this->DataArray = array();
        $this->CandDetails = array();
        $count = 0;
        
        $sqlCanDetails = "SELECT AJ.AJ_Id, AJ.AJ_Track_Id, AJ.LC_Id, AJ.AJ_FName, AJ.AJ_DOB, AJ.AJ_HouseNo, AJ.AJ_HouseName,AJ.ALC_Id,AJ.CT_Id,
                            AJ.AJ_Email, AJ.AJ_TotalAmount,AJ.AJ_Mobile1, AJ.AJ_Mobile2, AJ.AJ_SubmittedType, AJ.AJ_DeliveredType, AJ.AJ_JobDeliverdTo,
                            AJ.AJ_TotalAmount, AJ.AJ_ReceivedDate, AJ.AJ_Status,
                            AJD.AJD_Id, AJD.ADOC_Id,AJD.AJD_Year,AJD.ST_Id,AJD.APS_Id,AJD.AJD_Amount,ASD.ASD_Id,
                            AD.ADOC_Document , AST.ST_Name AS AST_State, APS.APS_Title,
                            AJDD.AD_FName,AJSD.AS_FName, ALC.ALC_Name,CT.CT_Name,ST.ST_Name,CN.CN_Name,AJ.AJ_BuildingName,AJ.AJ_Society,AJ.AJ_Email2,
                            AJ.AJ_Landline,AJ.AJ_PoliceLoc,AJ.AJ_WorkExp,
                            AJ.AJ_DeliveryDate,AJ.AJ_ContactMob,SR.SR_Name,PL.PL_Name,AJ.AJ_Pincode
                                FROM attestation_job_details AS AJ
                                    LEFT JOIN attestation_job_documents AS AJD ON AJD.AJ_Id = AJ.AJ_Id
                                    LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id
                                    LEFT JOIN attestation_job_supporting_documents AS ASD ON ASD.AJD_Id = AJD.AJD_Id
                                    LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJD.APS_Id
                                    LEFT JOIN states AS AST ON AST.ST_Id = AJD.ST_Id
                                    LEFT JOIN attestation_job_deliver_details AS AJDD ON AJDD.AJ_Id = AJ.AJ_Id
                                    LEFT JOIN attestation_job_submitter_details AS AJSD ON AJSD.AJ_Id = AJ.AJ_Id
                                    LEFT JOIN addr_locations AS ALC ON AJ.ALC_Id = ALC.ALC_Id 
                                    LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id 
                                    LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id 
                                    LEFT JOIN cities AS CT ON AJ.CT_Id = CT.CT_Id
                                    LEFT JOIN states AS ST ON AJ.ST_Id = ST.ST_Id
                                    LEFT JOIN countries AS CN ON ST.CN_Id = CN.CN_Id
                                        WHERE AJ.AJ_Id = ".$AJId; // LEFT JOIN attestation_authorities AS AA ON AA.AAUTH_Id = AJD.AAUTH_Id
        $resultCanDetails = mysqli_query($GLOBALS['con'],$sqlCanDetails);
        while ($rowCanDetails = mysqli_fetch_object($resultCanDetails)) {
            $this->CandDetails[$count] = $rowCanDetails;
            $count++;
        }
        $count=0;
        $sqlBillingDetails ="SELECT SUM(ABP_AmountRecieved) AS amtReceived
                                FROM attestation_job_billing_payment
                                    WHERE AJ_Id = ".$AJId;
        $resultBillDetails = mysqli_query($GLOBALS['con'],$sqlBillingDetails);
        while ($rowBillDetails = mysqli_fetch_object($resultBillDetails)) {
            $this->BillDetails[$count] = $rowBillDetails;
            $count++;
        }
        
        $count = 0 ;
        $sql = "SELECT AJT.*, APS.APS_Id, APS.APS_Title
                        FROM attestation_job_tracking AS AJT 
                            LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJT.APS_Id
                                WHERE AJT.AJ_Id = ".$AJId." ORDER BY AJT.AJT_Id";
        $ssql="SELECT AJD.AJD_Id, AD.ADOC_Document, AJS.AJS_Id, AJS.APS_Id, APS.APS_Title, APM.APM_Title, AJS.AJS_Status
                        FROM  attestation_job_documents AS AJD 
                            LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id=AJD.ADOC_Id 
                            LEFT JOIN attestation_job_subprocess AS AJS ON AJS.AJD_Id = AJD.AJD_Id
                            LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJS.APS_Id 
                            LEFT JOIN attestation_process_main AS APM ON APM.APM_Id = APS.APM_Id
                            LEFT JOIN attestation_job_supporting_documents AS AJSD ON AJSD.AJD_Id = AJD.AJD_Id
                            WHERE  AJD_Status != 0  AND AJD.AJ_Id =".$AJId."";
       
         $result = mysqli_query($GLOBALS['con'],$sql);
         while ($row = mysqli_fetch_object($result)) {
                    $this->DataArray[$row->AJD_Id][$count] = $row;
                    $count++;
              }
         $result2 = mysqli_query($GLOBALS['con'],$ssql);
         while ($row = mysqli_fetch_object($result2)) {
                    $this->DataSPArray[$row->AJD_Id][$count] = $row;
                    $count++;
              }
        $numRows=  mysqli_num_rows($result);
        if($numRows == 0){
            $result = mysqli_query($GLOBALS['con'],$ssql);
            while ($row = mysqli_fetch_object($result)) {
                    $this->DataNTArray[$row->AJD_Id][$count] = $row;
                    $count++;
              }
        }

    }
    function getUserName($OFId){
        
        $sql = "SELECT US_Id,CONCAT(US_FName,' ', US_LName) AS US_Name
                        FROM users_auth AS US 
                                WHERE OF_Id = ".$OFId." AND US.US_Status != 5 ORDER BY US_Id";
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$row->US_Id] = $row->US_Name;
        }
        
    }
    
    function getLocationName($OFId){
        
        $sql = "SELECT LC_Id, LC_Name 
                        FROM locations AS LC 
                                WHERE OF_Id = ".$OFId." AND LC_Status != 5 ORDER BY LC_Id";
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$row->LC_Id] = $row->LC_Name;
        }
        
    }
    
    function getCountryName(){
        
        $sql = "SELECT CN_Id, CN_Name 
                        FROM countries AS CN 
                                WHERE 1 ORDER BY CN_Id";
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$row->CN_Id] = $row->CN_Name;
        }
        
    }
    
    // Start of List Track Automate Module 
    /**
     * Listing all Automated Details
     *
     */     
    function listTrackAutomate($filter, $pos,$cnt){

        $this->DataArray = array();
        $count = 0;
        $sql = " SELECT AA.AA_Id,AA.US_Id,AA.OF_Id,AA.CN_Id, AA.AA_VisaType,AA.AA_Issuing_CNId,AA.AA_FromYear,AA.AA_ToYear,AA.AA_Status,
                    AA.ADOC_Id, GROUP_CONCAT(DISTINCT AD.ADOC_Document) AS Document, AA.AAUTH_Id, GROUP_CONCAT(DISTINCT AU.AAUTH_Authority) AS Authority 
                        FROM `attestation_automate` AA
                            LEFT JOIN attestation_documents AS AD ON FIND_IN_SET( AD.ADOC_Id ,  AA.ADOC_Id )
                            LEFT JOIN attestation_authorities AS AU ON FIND_IN_SET( AU.AAUTH_Id ,  AA.AAUTH_Id)
                                WHERE ".$filter."
                                    GROUP BY AA.AA_Id
                                        ORDER BY AA.AA_CDate DESC LIMIT $pos , $cnt";
        
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    /**
     * Get Number of Jobs added.
     * @param type $filter
     */
    function listTrackAutomateCount($filter){
        $sql = "SELECT COUNT(AA.AA_Id)                    
                        FROM `attestation_automate` AA
                            LEFT JOIN attestation_documents AS AD ON FIND_IN_SET( AD.ADOC_Id ,  AA.ADOC_Id )
                            LEFT JOIN attestation_authorities AS AU ON FIND_IN_SET( AU.AAUTH_Id ,  AA.AAUTH_Id)
                                WHERE ".$filter." 
                                    GROUP BY AA.AA_Id";
        $query = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_num_rows($query);
        
    }
    
    function listATPProcessOptions($filter) {
        $count = 0;
        $this->TrackArray = array();  
        $sql = " SELECT TP_Id, TP_From, TP_To, TP_Certificate, TP_Procedures, TP_Comment 
                                FROM track_procedure
                                WHERE ".$filter."
                                ORDER BY TP_Id
                                LIMIT 0 , 30";
        //die($sql);
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }	
    }
    
    function listProcessOptions($filter){
        $this->DataArray = array();
        $count = 0;
        $sql = "SELECT AA_Id, AA_AutomateData 
                    FROM attestation_automate
                        WHERE ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    
    function listCandidateProcessOptions($filter){
        $this->DataArray = array();
        $count = 0;
        $sql = "SELECT AE.*,SR.SR_Name,PL.PL_Name, ALC.ALC_Name,CT.CT_Name,ST.ST_Name,CN.CN_Name
                    FROM attestation_job_enquiry AS AE
                        LEFT JOIN users_auth AS US ON US.US_Id = AE.US_Id
                        LEFT JOIN addr_locations AS ALC ON AE.ALC_Id = ALC.ALC_Id 
                        LEFT JOIN addr_places AS PL ON AE.PL_Id = PL.PL_Id 
                        LEFT JOIN addr_streets AS SR ON AE.SR_Id = SR.SR_Id 
                        LEFT JOIN cities AS CT ON AE.CT_Id = CT.CT_Id 
                        LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id 
                        LEFT JOIN countries AS CN ON CN.CN_Id = ST.CN_Id 
                            WHERE ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while ($row = mysqli_fetch_object($result)) {
            $this->DataArray[$count] = $row;
            $count++;
        }
    }
    /*function getATPSubProcessCertificateIssuer($filter) {
        $count = 0;
        $this->TrackArray = array();  
        //echo "SELECT $fields FROM $table " . $condition;
        $result = mysqli_query($GLOBALS['con'],"SELECT APS.APS_Id, APS.APM_Id, APM.APM_Title,APS.APS_Title, APS.CN_Id, CN.CN_Name, APS.ST_Id, ST.ST_Name, 
                                APS.CT_Id, CT.CT_Name, APS.PL_Id, PL.PL_Name, APS.ALC_Id, ALC.ALC_Name, APS.SR_Id, SR.SR_Name FROM `attestation_process_sub` AS APS
                                LEFT JOIN attestation_process_main AS APM ON APS.APM_Id = APM.APM_Id
                                LEFT JOIN countries AS CN ON APS.CN_Id = CN.CN_Id
                                LEFT JOIN states AS ST ON APS.ST_Id = ST.ST_Id
                                LEFT JOIN cities AS CT ON APS.CT_Id = CT.CT_Id
                                LEFT JOIN addr_places AS PL ON APS.PL_Id = PL.PL_Id
                                LEFT JOIN addr_locations AS ALC ON APS.ALC_Id = ALC.ALC_Id
                                LEFT JOIN addr_streets AS SR ON APS.SR_Id = SR.SR_Id" . $filter);
        while ($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }
    }*/
    function getATPSubProcess($filter) {
        $count = 0;
        $this->TrackArray = array();  
        //echo "SELECT $fields FROM $table " . $condition;
        $result = mysqli_query($GLOBALS['con'],"SELECT APS.APS_Id, APS.APM_Id, APM.APM_Title, APS.APS_Title, APS.ASD_Id, APS.CN_Id_Travelling, APS.CN_Id, CN.CN_Name, APS.ST_Id, ST.ST_Name, 
                                APS.APS_StatutoryNAmt, APS.APS_StatutoryUAmt, APS.APS_ExtraNAmt, APS.APS_ExtraUAmt, APS.APS_CourierNAmt, APS.APS_CourierUAmt, APS.APS_TravellingNAmt, APS.APS_TravellingUAmt, APS.APS_ManpowerNAmt, APS.APS_ManpowerUAmt, APS.APS_ServiceNAmt, APS.APS_ServiceUAmt, 
                                APS.CT_Id, CT.CT_Name, APS.PL_Id, PL.PL_Name, APS.ALC_Id, ALC.ALC_Name, APS.SR_Id, SR.SR_Name FROM `attestation_process_sub` AS APS
                                LEFT JOIN attestation_process_main AS APM ON APS.APM_Id = APM.APM_Id
                                LEFT JOIN countries AS CN ON APS.CN_Id = CN.CN_Id
                                LEFT JOIN states AS ST ON APS.ST_Id = ST.ST_Id
                                LEFT JOIN cities AS CT ON APS.CT_Id = CT.CT_Id
                                LEFT JOIN addr_places AS PL ON APS.PL_Id = PL.PL_Id
                                LEFT JOIN addr_locations AS ALC ON APS.ALC_Id = ALC.ALC_Id
                                LEFT JOIN addr_streets AS SR ON APS.SR_Id = SR.SR_Id" . $filter);
        while ($row = mysqli_fetch_object($result)) {
            $this->TrackArray[$count] = $row;
            $count++;
        }
    }
}