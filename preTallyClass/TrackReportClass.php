<?php
require_once("connection.php");
class TrackReportClass {
    function reportStateData($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->StateReportArray     = array();
        $this->StateMonthReportArray= array();
        
        $sql = "SELECT SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved, ST.ST_Name, ST.ST_Id  
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN `states` AS ST ON ST.ST_Id = AJ.ST_Id  
            WHERE  ".$filt." ".$dateFilt." GROUP BY ST.ST_Id  ORDER BY ST.ST_Name ASC ";

        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->StateReportArray[$count] = $row;
                $count++;
            }
        }
        
        /* State based monthwise data */
        $monthSQL = " SELECT ST.ST_Id,ST.ST_Name,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved  
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt."
                    GROUP BY ST.ST_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY ST.ST_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->StateMonthReportArray[$monthRow->ST_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ABP_AmountRecieved;
            }
        }
    }
    
    function reportStateDataCount($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }        
        /* City based monthwise data */
        
        $sql = "SELECT SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved, ST.ST_Name, ST.ST_Id  
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN `states` AS ST ON ST.ST_Id = AJ.ST_Id  
            WHERE  ".$filt." ".$dateFilt." GROUP BY ST.ST_Id ORDER BY ST.ST_Name ";
      
        $monthResult = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_num_rows($monthResult);
    }
    function getStateFooter($filt,$stDate='',$enDate='') {
        $monthSQL = " SELECT ST.ST_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved  
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt."
                    GROUP BY ST.ST_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY ST.ST_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ABP_AmountRecieved;
            }
        }
    }
  ///*
  ///////////////////////////*List document count Streetwise*///////////////////////////// 
    function reportDocumentCountStreetData($filter,$stDate='',$enDate='',$statusFilter,$flt,$orderFilter,$fields ){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->DStreetMonthReportArray= array();
         $sql="SELECT AJD.AJD_Id,AJDT.AJ_ReceivedDate,AL.ALC_Id,PL.PL_Id,SR.SR_Id,SR.SR_Name ,ST.ST_Id,CT.CT_Id,COUNT(AD.ADOC_Document) AS DOCUMENT ".$fields."
                 FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id 
                                        LEFT JOIN addr_streets AS SR ON AJDT.SR_Id = SR.SR_Id
                                        LEFT JOIN addr_places AS PL ON AJDT.PL_Id = PL.PL_Id 
                                        LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
        
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->DocumentCountStreetReportArray[$count] = $row;
                $count++;
        }
            
              /* street based monthwise data */
  
        $monthSQL = "SELECT SR.SR_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS MONTH,COUNT(AD.ADOC_Document) AS DOCUMENT
           FROM attestation_job_documents AS AJD 
                                   LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                   LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                   LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                   LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                   LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id
                                   LEFT JOIN addr_streets AS SR ON AJDT.SR_Id = SR.SR_Id 
                                   LEFT JOIN addr_places AS PL ON AJDT.PL_Id = PL.PL_Id 
                                   LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                   LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                   LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id 
           WHERE ".$filter." ".$statusFilter." ".$dateFilt."
           GROUP BY SR.SR_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)
           ORDER BY MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)" . $orderFilter;
        
        
        
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->DStreetMonthReportArray[$monthRow->SR_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->DOCUMENT;
            }
        }    
    }
    ///count
    function reportDocumentStreetDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
  $monthSQL = "SELECT AJD.AJD_Id,AJDT.AJ_ReceivedDate,AL.ALC_Id,PL.PL_Id,SR.SR_Id,SR.SR_Name ,ST.ST_Id,CT.CT_Id,COUNT(AD.ADOC_Document) AS DOCUMENT 
                      FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id 
                                        LEFT JOIN addr_streets AS SR ON AJDT.SR_Id = SR.SR_Id
                                        LEFT JOIN addr_places AS PL ON AJDT.PL_Id = PL.PL_Id 
                                        LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id  
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY SR.SR_Id  ORDER BY SR.SR_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
    }
    ///
    function getDocumentStreetFooter($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $monthSQL = "SELECT SR.SR_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS MONTH,COUNT(AD.ADOC_Document) AS DOCUMENT
                     FROM attestation_job_documents AS AJD 
                                   LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                   LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                   LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                   LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                   LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id
                                   LEFT JOIN addr_streets AS SR ON AJDT.SR_Id = SR.SR_Id 
                                   LEFT JOIN addr_places AS PL ON AJDT.PL_Id = PL.PL_Id 
                                   LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                   LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                   LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id 
                     WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                     GROUP BY SR.SR_Id, MONTH( AJDT.AJ_ReceivedDate ) , YEAR( AJDT.AJ_ReceivedDate )
                     ORDER BY SR.SR_Id, MONTH( AJDT.AJ_ReceivedDate ) , YEAR( AJDT.AJ_ReceivedDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->DOCUMENT;
            }
        }
    }
///
  ///*
  
     ////////////////////////////////*List document count Placewise*///////////////////////////// 
    function reportDocumentCountPlaceData($filter,$stDate='',$enDate='',$statusFilter,$flt,$orderFilter,$fields){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->DPlaceMonthReportArray= array();
        $sql="SELECT AJD.AJD_Id,AJDT.AJ_ReceivedDate,AL.ALC_Id,PL.PL_Id,PL.PL_Name,ST.ST_Id,CT.CT_Id,COUNT(AD.ADOC_Document) AS DOCUMENT ".$fields."
                 FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id 
                                        LEFT JOIN addr_places AS PL ON AJDT.PL_Id = PL.PL_Id 
                                        LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
       
        
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->DocumentCountPlaceReportArray[$count] = $row;
                $count++;
        }
            
              /* place based monthwise data */
  

       $monthSQL = "SELECT PL.PL_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS MONTH,COUNT(AD.ADOC_Document) AS DOCUMENT
           FROM attestation_job_documents AS AJD 
                                   LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                   LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                   LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                   LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                   LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id
                                   LEFT JOIN addr_places AS PL ON AJDT.PL_Id = PL.PL_Id 
                                   LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                   LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                   LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id 
           WHERE ".$filter." ".$statusFilter." ".$dateFilt."
           GROUP BY PL.PL_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)
           ORDER BY MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)" . $orderFilter;
        
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->DPlaceMonthReportArray[$monthRow->PL_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->DOCUMENT;
            }
        }      
    }
    ///count
    function reportDocumentPlaceDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
         $monthSQL = "SELECT AJD.AJD_Id,AJDT.AJ_ReceivedDate,AL.ALC_Id,PL.PL_Id,PL.PL_Name,ST.ST_Id,CT.CT_Id,COUNT(AD.ADOC_Document) AS DOCUMENT
                 FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id 
                                        LEFT JOIN addr_places AS PL ON AJDT.PL_Id = PL.PL_Id 
                                        LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY PL.PL_Id  ORDER BY PL.PL_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
    }
    ///
    function getDocumentPlaceFooter($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $monthSQL = " SELECT PL.PL_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS MONTH,COUNT(AD.ADOC_Document) AS DOCUMENT
           FROM attestation_job_documents AS AJD 
                                   LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                   LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                   LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                   LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                   LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id
                                   LEFT JOIN addr_places AS PL ON AJDT.PL_Id = PL.PL_Id 
                                   LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                   LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                   LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id  
                     WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                     GROUP BY PL.PL_Id, MONTH( AJDT.AJ_ReceivedDate ) , YEAR( AJDT.AJ_ReceivedDate )
                     ORDER BY PL.PL_Id, MONTH( AJDT.AJ_ReceivedDate ) , YEAR( AJDT.AJ_ReceivedDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->DOCUMENT;
            }
        }
    }

    
    
    
    
    ///*
    
     /////////////////////////*List enquiry count Locationwise*////////////////////////// 
   function reportDocumentCountLocationData($filter,$stDate='',$enDate='',$statusFilter,$flt,$orderFilter,$fields){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->DELocationMonthReportArray= array();
        $sql="SELECT AJD.AJD_Id,AJDT.AJ_ReceivedDate,AL.ALC_Id, AL.ALC_Name,ST.ST_Id,CT.CT_Id,COUNT(AD.ADOC_Document) AS DOCUMENT ".$fields."
                 FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id 
                                        LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
     
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->DocumentCountLocationReportArray[$count] = $row;
                $count++;
        }
              /* location based monthwise data */

        
         $monthSQL = "SELECT AL.ALC_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS MONTH,COUNT(AD.ADOC_Document) AS DOCUMENT
                FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id
                                        LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id 
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)
                ORDER BY MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)" . $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->DELocationMonthReportArray[$monthRow->ALC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->DOCUMENT;
            }
        }    
    }
    /// count
    function reportDocumentLocationDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
         $monthSQL = "SELECT AJD.AJD_Id,AJDT.AJ_ReceivedDate,AL.ALC_Id, AL.ALC_Name,ST.ST_Id,CT.CT_Id,COUNT(AD.ADOC_Document) AS DOCUMENT
                 FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id 
                                        LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id  ORDER BY AL.ALC_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
    }
    ///
    function getDocumentLocFooter($filter,$stDate='',$enDate='',$statusFilter) {
         $monthSQL = " SELECT AL.ALC_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS MONTH,COUNT(AD.ADOC_Document) AS DOCUMENT
                FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id 
                                        LEFT JOIN addr_locations AS AL ON AJDT.ALC_Id = AL.ALC_Id
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id 
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)
                ORDER BY AL.ALC_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->DOCUMENT;
            }
        }
    }
     
    
    ///*
    
     ///////////////////////////////*List document count Citywise*///////////////////////// 
    function reportDocumentCountCityData($filter,$stDate='',$enDate='',$statusFilter,$flt,$orderFilter,$fields){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->DCityMonthReportArray= array();
       
        $sql="SELECT AJD.AJD_Id,AJDT.AJ_ReceivedDate,ST.ST_Id,CT.CT_Id,CT.CT_Name,COUNT(AD.ADOC_Document) AS DOCUMENT ".$fields."
                 FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AJDT.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
        
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->DocumentCountCityReportArray[$count] = $row;
                $count++;
        }
            
            
              /* City based monthwise data */
      $monthSQL = "SELECT CT.ST_Id,CT.CT_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS MONTH,COUNT(AD.ADOC_Document) AS DOCUMENT
                FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AJDT.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id 
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)
                ORDER BY MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)" . $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->DCityMonthReportArray[$monthRow->CT_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->DOCUMENT;
            }
        }
    }
   
    ///count
    function reportDocumentCityDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
         $monthSQL = "SELECT  AJD.AJD_Id,AJDT.AJ_ReceivedDate,ST.ST_Id,CT.CT_Id,CT.CT_Name,COUNT(AD.ADOC_Document) AS DOCUMENT 
                 FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AJDT.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id  ORDER BY CT.CT_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
        }
    ///
    function getDocumentCityFooter($filter,$stDate='',$enDate='',$statusFilter) {
       if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
       $monthSQL = " SELECT CT.CT_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS MONTH,COUNT(AD.ADOC_Document) AS DOCUMENT
                FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                        LEFT JOIN cities AS CT ON CT.CT_Id = AJDT.CT_Id
                                        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id 
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)
                ORDER BY CT.CT_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->DOCUMENT;
            }
        }
      }
 
    
    
    
    
    
    
    
    
    
    
    
    ///*
    /////////////////////*List document count Statewise*//////////////////////////////////
    
    function reportDocumentCountStateData($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->DStateMonthReportArray= array();
        $sql="SELECT AJD.AJD_Id,AJDT.AJ_ReceivedDate,ST.ST_Id, ST.ST_Name ,COUNT(AD.ADOC_Document) AS DOCUMENT
                FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                        LEFT JOIN states AS ST ON ST.ST_Id = AJDT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id  ORDER BY ST.ST_Name ASC ";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
         while($row = mysqli_fetch_object($result)) {
                $this->DocumentCountReportArray[$count] = $row;
                $count++;
            }
    
        /* State based monthwise data */
        $monthSQL = "SELECT ST.ST_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS 
                MONTH , COUNT(AD.ADOC_Document) AS DOCUMENT
                FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                        LEFT JOIN states AS ST ON ST.ST_Id = AJDT.ST_Id 
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id,MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)
                ORDER BY ST.ST_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->DStateMonthReportArray[$monthRow->ST_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->DOCUMENT;
            }
        }
    }
///
    function getDocumentStateFooter($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJDT.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $monthSQL = "SELECT ST.ST_Id,YEAR(AJDT.AJ_ReceivedDate) AS YEAR, MONTH(AJDT.AJ_ReceivedDate ) AS 
                MONTH , COUNT(AD.ADOC_Document) AS DOCUMENT
                FROM attestation_job_documents AS AJD 
                                        LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id = AJD.ADOC_Id 
                                        LEFT JOIN attestation_job_details AS AJDT ON AJD.AJ_Id = AJDT.AJ_Id
                                        LEFT JOIN  tracks AS TR ON AJDT.AJ_Track_Id = TR.TR_Id
                                        LEFT JOIN  users_auth AS UA ON UA.US_Id = TR.US_Id 
                                        LEFT JOIN  locations AS LC ON LC.LC_Id = UA.LC_Id  
                                        LEFT JOIN states AS ST ON ST.ST_Id = AJDT.ST_Id 
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id,MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)
                ORDER BY ST.ST_Id, MONTH(AJDT.AJ_ReceivedDate) , YEAR(AJDT.AJ_ReceivedDate)";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->DOCUMENT;
            }
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    ///*
    
    
    
    
    
    
  /////////////////////*List enquiry count Statewise*//////////////////////////////////
    
    function reportEnquiryCountStateData($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->EStateMonthReportArray= array();
        $sql="SELECT AE.AE_Id, AE.AE_CDate,ST.ST_Id, ST.ST_Name,COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN states AS ST ON ST.ST_Id = AE.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id  ORDER BY ST.ST_Name ASC ";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
         while($row = mysqli_fetch_object($result)) {
                $this->EnquiryCountReportArray[$count] = $row;
                $count++;
            }
    
        /* State based monthwise data */
        $monthSQL = " SELECT ST.ST_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN states AS ST ON ST.ST_Id = AE.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                ORDER BY ST.ST_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->EStateMonthReportArray[$monthRow->ST_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ENQUIRY;
            }
        }
    }
///
    function getEnquiryStateFooter($filter,$stDate='',$enDate='',$statusFilter) {
        $monthSQL = " SELECT ST.ST_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN states AS ST ON ST.ST_Id = AE.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                ORDER BY ST.ST_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ENQUIRY;
            }
        }
    }
 

    
  ///////////////////////////////*List enquiry count Citywise*///////////////////////// 
    function reportEnquiryCountCityData($filter,$stDate='',$enDate='',$statusFilter,$flt,$orderFilter,$fields){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->ECityMonthReportArray= array();
        $sql="SELECT  AE.AE_Id, AE.AE_CDate,CT.ST_Id,CT.CT_Id,CT.CT_Name,COUNT( AE.AE_CDate ) AS ENQUIRY ".$fields."
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AE.CT_Id
                LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." AND CT.CT_Id != '' ".$statusFilter." ".$dateFilt."".$flt."";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->EnquiryCountCityReportArray[$count] = $row;
                $count++;
        }
            
            
              /* City based monthwise data */
       $monthSQL = " SELECT CT.ST_Id,CT.CT_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AE.CT_Id
                LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                ORDER BY MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )" . $orderFilter;

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->ECityMonthReportArray[$monthRow->CT_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ENQUIRY;
            }
        }
    }
   
    ///count
    function reportEnquiryCityDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
         $monthSQL = "SELECT AE.AE_Id, AE.AE_CDate,CT.ST_Id,CT.CT_Id,CT.CT_Name,COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AE.CT_Id
                LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id  ORDER BY CT.CT_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
        }
    ///
    function getEnquiryCityFooter($filter,$stDate='',$enDate='',$statusFilter) {
       $monthSQL = " SELECT CT.ST_Id,CT.CT_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AE.CT_Id
                LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                ORDER BY CT.CT_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ENQUIRY;
            }
        }
      }
 
    /////////////////////////*List enquiry count Locationwise*////////////////////////// 
   function reportEnquiryCountLocationData($filter,$stDate='',$enDate='',$statusFilter,$flt,$orderFilter,$fields){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->ELocationMonthReportArray= array();
        $sql="SELECT AE.AE_Id, AE.AE_CDate, AL.ALC_Id, AL.ALC_Name,CT.ST_Id, CT.CT_Id,COUNT( AE.AE_CDate ) AS ENQUIRY ".$fields."
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN addr_locations AS AL ON AE.ALC_Id = AL.ALC_Id
		LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->EnquiryCountLocationReportArray[$count] = $row;
                $count++;
        }
              /* location based monthwise data */
  
        $monthSQL = " SELECT  AL.ALC_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN addr_locations AS AL ON AE.ALC_Id = AL.ALC_Id
		LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                ORDER BY MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )". $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->ELocationMonthReportArray[$monthRow->ALC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ENQUIRY;
            }
        }    
    }
    /// count
    function reportEnquiryLocationDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
         $monthSQL = "SELECT AE.AE_Id, AE.AE_CDate, AL.ALC_Id, AL.ALC_Name,CT.ST_Id, CT.CT_Id,COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN addr_locations AS AL ON AE.ALC_Id = AL.ALC_Id
		LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id  ORDER BY AL.ALC_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
    }
    ///
    function getEnquiryLocFooter($filter,$stDate='',$enDate='',$statusFilter) {
         $monthSQL = " SELECT  AL.ALC_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN addr_locations AS AL ON AE.ALC_Id = AL.ALC_Id
		LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                ORDER BY AL.ALC_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ENQUIRY;
            }
        }
    }
   ////////////////////////////////*List enquiry count Placewise*///////////////////////////// 
    function reportEnquiryCountPlaceData($filter,$stDate='',$enDate='',$statusFilter,$flt,$orderFilter,$fields){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->EPlaceMonthReportArray= array();
        $sql="SELECT AE.AE_Id, AE.AE_CDate, AL.ALC_Id,AE.PL_Id,PL.PL_Name ,CT.ST_Id, CT.CT_Id,COUNT( AE.AE_CDate ) AS ENQUIRY ".$fields."
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN addr_places AS PL ON AE.PL_Id = PL.PL_Id 
                LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id
		LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id  
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->EnquiryCountPlaceReportArray[$count] = $row;
                $count++;
        }
            
              /* place based monthwise data */
  
        $monthSQL = " SELECT PL.PL_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                     MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                     FROM attestation_job_enquiry AS AE
                     LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                     LEFT JOIN addr_places AS PL ON AE.PL_Id = PL.PL_Id 
                     LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id
                     LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                     LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id 
                     WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                     GROUP BY PL.PL_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                     ORDER BY  MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )". $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->EPlaceMonthReportArray[$monthRow->PL_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ENQUIRY;
            }
        }      
    }
    ///count
    function reportEnquiryPlaceDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
         $monthSQL = "SELECT AE.AE_Id, AE.AE_CDate, AL.ALC_Id,AE.PL_Id,PL.PL_Name ,CT.ST_Id, CT.CT_Id,COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN addr_places AS PL ON AE.PL_Id = PL.PL_Id 
                LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id
		LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id  
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY PL.PL_Id  ORDER BY PL.PL_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
    }
    ///
    function getEnquiryPlaceFooter($filter,$stDate='',$enDate='',$statusFilter) {
        $monthSQL = " SELECT PL.PL_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                     MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                     FROM attestation_job_enquiry AS AE
                     LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                     LEFT JOIN addr_places AS PL ON AE.PL_Id = PL.PL_Id 
                     LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id
                     LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                     LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id 
                     WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                     GROUP BY PL.PL_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                     ORDER BY PL.PL_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ENQUIRY;
            }
        }
    }

    
    ///////////////////////////*List enquiry count Streetwise*///////////////////////////// 
    function reportEnquiryCountStreetData($filter,$stDate='',$enDate='',$statusFilter,$flt,$orderFilter,$fields ){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->EStreetMonthReportArray= array();
        $sql="SELECT AE.AE_Id, AE.AE_CDate, AL.ALC_Id,AE.PL_Id,AE.SR_Id,SR.SR_Name ,CT.ST_Id, CT.CT_Id,COUNT( AE.AE_CDate ) AS ENQUIRY ".$fields."
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN addr_streets AS SR ON AE.SR_Id = SR.SR_Id 
                LEFT JOIN addr_places AS PL ON SR.PL_Id = PL.PL_Id 
                LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id
		LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id  
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt ."";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->EnquiryCountStreetReportArray[$count] = $row;
                $count++;
        }
            
              /* street based monthwise data */
  
        $monthSQL = " SELECT SR.SR_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                     MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                     FROM attestation_job_enquiry AS AE
                     LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                     LEFT JOIN addr_streets AS SR ON AE.SR_Id = SR.SR_Id 
		     LEFT JOIN addr_places AS PL ON SR.PL_Id = PL.PL_Id 
		     LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id
	             LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
		     LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id 
                     WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                     GROUP BY SR.SR_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                     ORDER BY MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )". $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->EStreetMonthReportArray[$monthRow->SR_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ENQUIRY;
            }
        }    
    }
    ///count
    function reportEnquiryStreetDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AE.AE_CDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
         $monthSQL = "SELECT AE.AE_Id, AE.AE_CDate, AL.ALC_Id,AE.PL_Id,AE.SR_Id,SR.SR_Name ,CT.ST_Id, CT.CT_Id,COUNT( AE.AE_CDate ) AS ENQUIRY
                FROM attestation_job_enquiry AS AE
                LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                LEFT JOIN addr_streets AS SR ON AE.SR_Id = SR.SR_Id 
                LEFT JOIN addr_places AS PL ON SR.PL_Id = PL.PL_Id 
                LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id
		LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id  
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY SR.SR_Id  ORDER BY SR.SR_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
    }
    ///
    function getEnquiryStreetFooter($filter,$stDate='',$enDate='',$statusFilter) {
        $monthSQL = " SELECT PL.PL_Id, YEAR( AE.AE_CDate ) AS YEAR, MONTH( AE.AE_CDate ) AS
                     MONTH , COUNT( AE.AE_CDate ) AS ENQUIRY
                     FROM attestation_job_enquiry AS AE
                     LEFT JOIN users_auth AS UA ON UA.US_Id = AE.US_Id
                     LEFT JOIN addr_places AS PL ON AE.PL_Id = PL.PL_Id 
                     LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id
                     LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                     LEFT JOIN states AS ST ON AE.ST_Id = ST.ST_Id 
                     WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                     GROUP BY PL.PL_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )
                     ORDER BY PL.PL_Id, MONTH( AE.AE_CDate ) , YEAR( AE.AE_CDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ENQUIRY;
            }
        }
    }
///
    ///Job based pending///
        
  /////////////////////*List job count Statewise*//////////////////////////////////
    
    function reportJobCountStateData($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->JStateMonthReportArray= array();
        $sql="SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate,ST.ST_Id, ST.ST_Name, COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id  ORDER BY ST.ST_Name ASC ";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
         while($row = mysqli_fetch_object($result)) {
                $this->JobCountReportArray[$count] = $row;
                $count++;
            }
    
        /* State based monthwise data */
        $monthSQL = " SELECT ST.ST_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                FROM attestation_job_details AS AJ 
                LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR(AJ.AJ_ReceivedDate )
                ORDER BY ST.ST_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR( AJ.AJ_ReceivedDate )";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->JStateMonthReportArray[$monthRow->ST_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->JOB;
            }
        }
    }
///count
        function getJobStateFooter($filter,$stDate='',$enDate='',$statusFilter) {
         if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $monthSQL = " SELECT ST.ST_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                FROM attestation_job_details AS AJ 
                LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY ST.ST_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR(AJ.AJ_ReceivedDate )
                ORDER BY ST.ST_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR( AJ.AJ_ReceivedDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->JOB;
            }
        }
    }
    
    
     ///////////////////////////////*List job count Citywise*///////////////////////// 
    function reportJobCountCityData($filter,$stDate='',$enDate='',$flt,$orderFilter,$fields,$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $this->JCityMonthReportArray= array();
        $sql="SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate, ST.ST_Id, CT.CT_Id, CT.CT_Name, COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB ".$fields."
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
		LEFT JOIN cities AS CT ON CT.CT_Id = AJ.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
      
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
                $this->JobCountCityReportArray[$count] = $row;
                $count++;
        }
            
            
              /* City based monthwise data */
        $monthSQL = "SELECT CT.ST_Id,CT.CT_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                    FROM attestation_job_details AS AJ 
                    LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		        LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		        LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		        LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
                        LEFT JOIN cities AS CT ON CT.CT_Id = AJ.CT_Id
		        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                ORDER BY MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)". $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->JCityMonthReportArray[$monthRow->CT_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->JOB;
            }
        }
    }
   
    ///count
    function reportJobCityDataCount($filter,$stDate='',$enDate='',$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $monthSQL = "SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate, ST.ST_Id, CT.CT_Id, CT.CT_Name, COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
		LEFT JOIN cities AS CT ON CT.CT_Id = AJ.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id  ORDER BY CT.CT_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
        }
    ///
    function getJobCityFooter($filter,$stDate='',$enDate='',$statusFilter) {
      if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
      }
      $monthSQL = "SELECT CT.ST_Id,CT.CT_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH ,COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                    FROM attestation_job_details AS AJ 
                    LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
                    LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
                    LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
                    LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
                    LEFT JOIN cities AS CT ON CT.CT_Id = AJ.CT_Id
                    LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY CT.CT_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                ORDER BY CT.CT_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)";
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->JOB;
            }
        }
      }
 
     /////////////////////*List job count location wise*//////////////////////////////////
    
    function reportJobCountLocationData($filter,$stDate='',$enDate='',$flt,$orderFilter,$fields,$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->JLocMonthReportArray= array();
        $sql="SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate,AL.ALC_Id, AL.ALC_Name,ST.ST_Id,CT.CT_Id, COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB ".$fields."
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
         while($row = mysqli_fetch_object($result)) {
                $this->JobCountLocationReportArray[$count] = $row;
                $count++;
            }
    
        /* State based monthwise data */
        $monthSQL = " SELECT AL.ALC_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                FROM attestation_job_details AS AJ 
                LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR(AJ.AJ_ReceivedDate )
                ORDER BY  MONTH(AJ.AJ_ReceivedDate) , YEAR( AJ.AJ_ReceivedDate )". $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->JLocMonthReportArray[$monthRow->ALC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->JOB;
            }
        }
    }
    
    ///
      function getJobLocFooter($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }  
        $monthSQL = " SELECT AL.ALC_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                FROM attestation_job_details AS AJ 
                LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR(AJ.AJ_ReceivedDate )
                ORDER BY AL.ALC_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR( AJ.AJ_ReceivedDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->JOB;
            }
        }
    }
    ///count
     function reportJobLocationDataCount($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }        
        /* Location based monthwise data */
        
        $sql = "SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate,AL.ALC_Id, AL.ALC_Name,ST.ST_Id, ST.ST_Name, COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY AL.ALC_Id  ORDER BY AL.ALC_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_num_rows($monthResult);
    }
  //
       /////////////////////*List job count place wise*//////////////////////////////////
    
    function reportJobCountPlaceData($filter,$stDate='',$enDate='',$flt,$orderFilter,$fields,$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->JPlaceMonthReportArray= array();
        $sql="SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate,AL.ALC_Id, PL.PL_Id,PL.PL_Name,ST.ST_Id, CT.CT_Id,ST.ST_Name, COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB ".$fields."
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
         while($row = mysqli_fetch_object($result)) {
                $this->JobCountPlaceReportArray[$count] = $row;
                $count++;
            }
    
        /* State based monthwise data */
        $monthSQL = " SELECT  PL.PL_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                FROM attestation_job_details AS AJ 
                LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY PL.PL_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR(AJ.AJ_ReceivedDate )
                ORDER BY MONTH(AJ.AJ_ReceivedDate) , YEAR( AJ.AJ_ReceivedDate )". $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->JPlaceMonthReportArray[$monthRow->PL_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->JOB;
            }
        }
    }
    
    ///
      function getJobPlaceFooter($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }  
        $monthSQL = " SELECT PL.PL_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                FROM attestation_job_details AS AJ 
                LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY PL.PL_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR(AJ.AJ_ReceivedDate )
                ORDER BY PL.PL_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR( AJ.AJ_ReceivedDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->JOB;
            }
        }
    }
    ///count
     function reportJobPlaceDataCount($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }        
        /* Location based monthwise data */
        
        $sql = "SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate,AL.ALC_Id, PL.PL_Id,PL.PL_Name,ST.ST_Id, ST.ST_Name, COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY PL.PL_Id  ORDER BY PL.PL_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_num_rows($monthResult);
    }
    ////
      /////////////////////*List job count street wise*//////////////////////////////////
    
    function reportJobCountStreetData($filter,$stDate='',$enDate='',$flt,$orderFilter,$fields,$statusFilter){
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->JStreetMonthReportArray= array();
        $sql="SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate,AL.ALC_Id, PL.PL_Id,SR.SR_Id,SR.SR_Name,ST.ST_Id, COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB ".$fields."
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id 
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."".$flt."";
        $count=0;
        $result = mysqli_query($GLOBALS['con'],$sql);
         while($row = mysqli_fetch_object($result)) {
                $this->JobCountStreetReportArray[$count] = $row;
                $count++;
            }
    
        /* State based monthwise data */
        $monthSQL = " SELECT  SR.SR_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                FROM attestation_job_details AS AJ 
                LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
                LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id 
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY SR.SR_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR(AJ.AJ_ReceivedDate )
                ORDER BY MONTH(AJ.AJ_ReceivedDate) , YEAR( AJ.AJ_ReceivedDate )". $orderFilter;
        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->JStreetMonthReportArray[$monthRow->SR_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->JOB;
            }
        }
    }
    
    ///
      function getJobStreetFooter($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }  
        $monthSQL = " SELECT SR.SR_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                MONTH , COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB 
                FROM attestation_job_details AS AJ 
                LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id AND AJD.AJD_Status != 0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id 
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id 
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id 
                LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id 
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY SR.SR_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR(AJ.AJ_ReceivedDate )
                ORDER BY SR.SR_Id, MONTH(AJ.AJ_ReceivedDate) , YEAR( AJ.AJ_ReceivedDate )";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->JOB;
            }
        }
    }
    ///count
     function reportJobStreetDataCount($filter,$stDate='',$enDate='',$statusFilter) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }        
        /* Location based monthwise data */
        
        $sql = "SELECT AJ.AJ_Id, AJ.AJ_ReceivedDate,AL.ALC_Id, PL.PL_Id,SR.SR_Id,SR.SR_Name,ST.ST_Id,COUNT(DISTINCT AJ.AJ_Track_Id) AS JOB
                FROM attestation_job_details AS AJ
		LEFT JOIN attestation_job_documents AS AJD ON ( AJD.AJ_Id = AJ.AJ_Id
		AND AJD.AJD_Status !=0 )
		LEFT JOIN tracks AS TR ON TR.TR_Id = AJ.AJ_Track_Id
		LEFT JOIN users_auth AS UA ON UA.US_Id = TR.US_Id
		LEFT JOIN locations AS LC ON LC.LC_Id = UA.LC_Id
                LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id 
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id
                LEFT JOIN addr_locations AS AL ON AJ.ALC_Id = AL.ALC_Id
                LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
		LEFT JOIN states AS ST ON ST.ST_Id = AJ.ST_Id
                WHERE ".$filter." ".$statusFilter." ".$dateFilt."
                GROUP BY SR.SR_Id  ORDER BY SR.SR_Name ASC ";
        $monthResult = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_num_rows($monthResult);
    }
    
    function reportCityData($fields,$filt,$stDate='',$enDate='',$pos,$cnt,$flt) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->CityReportArray      = array();
        $this->CityMonthReportArray = array();
       
        $sql = "SELECT $fields  
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN `cities` AS CT ON AJ.CT_Id = CT.CT_Id     
            LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id 
            WHERE $filt AND CT.CT_Id != '' $dateFilt $flt ";
        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->CityReportArray[$count] = $row;
                $count++;
            }
        }
        
        /* City based monthwise data */
        $monthSQL = " SELECT CT.ST_Id,CT.CT_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved  
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN `cities` AS CT ON AJ.CT_Id = CT.CT_Id   
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt."
                    GROUP BY CT.CT_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->CityMonthReportArray[$monthRow->CT_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ABP_AmountRecieved;
            }
        }
    }
    
    function reportCityDataCount($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }        
        /* City based monthwise data */
        $monthSQL = "SELECT CT.ST_Id,SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved, CT.CT_Name, CT.CT_Id   
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN `cities` AS CT ON AJ.CT_Id = CT.CT_Id     
            LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
            WHERE  ".$filt." ".$dateFilt." GROUP BY CT.CT_Id ORDER BY CT.CT_Name ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        return mysqli_num_rows($monthResult);
    }
    
    function getCityFooter($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }  
        $monthSQL = " SELECT CT.ST_Id,CT.CT_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved  
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN `cities` AS CT ON AJ.CT_Id = CT.CT_Id   
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt."
                    GROUP BY CT.CT_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY CT.CT_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)" ;

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ABP_AmountRecieved;
            }
        }
    }
    
    function reportLocData($fields,$filt,$stDate='',$enDate='',$pos,$cnt,$flt) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                      = 0;
        $this->LocReportArray       = array();
        $this->LocMonthReportArray  = array();
        
        $sql = "SELECT $fields       
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN `addr_locations` AS AL ON AJ.ALC_Id = AL.ALC_Id     
            LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
            LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
            WHERE ".$filt." ".$dateFilt." $flt LIMIT ".$pos.",".$cnt;

        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->LocReportArray[$count] = $row;
                $count++;
            }
        }
        
        /* Location based monthwise data */
        $monthSQL = " SELECT AL.ALC_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved 
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN `addr_locations` AS AL ON AJ.ALC_Id = AL.ALC_Id   
                    LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt." 
                    GROUP BY AL.ALC_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY AL.ALC_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->LocMonthReportArray[$monthRow->ALC_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ABP_AmountRecieved;
            }
        }
    }
    
    function reportLocDataCount($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }        
        /* Location based monthwise data */
        
        $sql = "SELECT SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved,AL.ALC_Name, AL.ALC_Id      
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN `addr_locations` AS AL ON AJ.ALC_Id = AL.ALC_Id   
            LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id 
            LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
            WHERE  ".$filt." ".$dateFilt." GROUP BY AL.ALC_Id ORDER BY AL.ALC_Name";
        $monthResult = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_num_rows($monthResult);
    }
    
    function getLocFooter($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }  
        $monthSQL = " SELECT AL.ALC_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved 
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN `addr_locations` AS AL ON AJ.ALC_Id = AL.ALC_Id   
                    LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id  
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt." 
                    GROUP BY AL.ALC_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY AL.ALC_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ABP_AmountRecieved;
            }
        }
    }
    
    function manageBusinessReport($stDate,$enDate,$filter,$pos,$cnt,$havingFlt) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        } 
        $count              =   0;
        $this->DataArray    =   array();
        $sql = "SELECT BS.BS_Id,TR.TR_Id,LC.LC_Name,TR.TR_Track,US.US_FName,US.US_LName,SUM(BS.BS_Amount) AS BS_Amount,IT.IT_Name,BS.BS_Date 
                FROM balance_sheets AS BS 
                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id 
                LEFT JOIN items AS IT ON BS.IT_Id = IT.IT_Id 
                LEFT JOIN users_auth AS US ON BS.US_Id = US.US_Id 
                LEFT JOIN locations AS LC ON BS.LC_Id = LC.LC_Id   
                LEFT JOIN tracks_manageids AS TM ON TR.TR_Id = TM.TR_Id 
                WHERE IT.MH_Type = 1 AND IT.IT_Business = 1 AND IT.IT_Status = 1  $filter $dateFilt
                AND TR.TR_Status != 0 AND BS.BS_Status = 1 GROUP BY TR.TR_Id $havingFlt ORDER BY TR.TR_Track ASC 
                LIMIT $pos,$cnt ";
        $result = mysqli_query($GLOBALS['con'],$sql);
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->DataArray[$count] = $row;
                $count++;
            }
        }
    }
    
    function manageBusinessReportCount($stDate,$enDate,$filter,$havingFlt) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND BS.BS_Date BETWEEN '".$stDate."' AND '".$enDate."'";
        } 
        $count  =   0;
        
        $sql    =   mysqli_query($GLOBALS['con'],"SELECT TR.TR_Track 
                FROM balance_sheets AS BS 
                LEFT JOIN `tracks` AS TR ON BS.TR_Id = TR.TR_Id 
                LEFT JOIN items AS IT ON BS.IT_Id = IT.IT_Id 
                LEFT JOIN users_auth AS US ON BS.US_Id = US.US_Id 
                LEFT JOIN locations AS LC ON BS.LC_Id = LC.LC_Id  
                LEFT JOIN tracks_manageids AS TM ON TR.TR_Id = TM.TR_Id 
                WHERE IT.MH_Type = 1 AND IT.IT_Business = 1 AND IT.IT_Status = 1 $filter $dateFilt
                AND TR.TR_Status != 0 AND BS.BS_Status = 1 GROUP BY TR.TR_Track $havingFlt ORDER BY BS.BS_Date DESC");
        return mysqli_num_rows($sql);
    }
    
    function reportPlaceData($fields,$filt,$stDate='',$enDate='',$pos,$cnt,$flt) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                        = 0;
        $this->PlaceReportArray       = array();
        $this->PlaceMonthReportArray  = array();
        
        $sql = "SELECT $fields       
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id 
            LEFT JOIN `addr_locations` AS AL ON PL.ALC_Id = AL.ALC_Id     
            LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
            LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
            WHERE ".$filt." ".$dateFilt." $flt LIMIT ".$pos.",".$cnt;

        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->PlaceReportArray[$count] = $row;
                $count++;
            }
        }
        
        /* Location based monthwise data */
        $monthSQL = " SELECT PL.PL_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved 
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id 
                    LEFT JOIN `addr_locations` AS AL ON PL.ALC_Id = AL.ALC_Id     
                    LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt." 
                    GROUP BY PL.PL_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY PL.PL_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->PlaceMonthReportArray[$monthRow->PL_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ABP_AmountRecieved;
            }
        }
    }
    
    function reportPlaceDataCount($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }        
        /* Location based monthwise data */
        
        $sql = "SELECT SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved,PL.PL_Name,PL.PL_Id       
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id 
            LEFT JOIN `addr_locations` AS AL ON PL.ALC_Id = AL.ALC_Id   
            LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id 
            LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
            WHERE  ".$filt." ".$dateFilt." GROUP BY PL.PL_Id ORDER BY PL.PL_Name";
        $monthResult = mysqli_query($GLOBALS['con'],$sql);
        return mysqli_num_rows($monthResult);
    }
    
    function getPlaceFooter($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }  
        $monthSQL = " SELECT PL.PL_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved 
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id 
                    LEFT JOIN `addr_locations` AS AL ON PL.ALC_Id = AL.ALC_Id     
                    LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id  
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt." 
                    GROUP BY PL.PL_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY PL.PL_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  ";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ABP_AmountRecieved;
            }
        }
    }
    
    function reportStreetData($fields,$filt,$stDate='',$enDate='',$pos,$cnt,$flt) {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }
        $count                         = 0;
        $this->StreetReportArray       = array();
        $this->StreetMonthReportArray  = array();
        $srnames    =array();
       $sql = "SELECT $fields       
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id  
            LEFT JOIN addr_places AS PL ON SR.PL_Id = PL.PL_Id 
            LEFT JOIN addr_locations AS AL ON PL.ALC_Id = AL.ALC_Id     
            LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
            LEFT JOIN states AS ST ON AJ.ST_Id = ST.ST_Id   
            WHERE ".$filt." ".$dateFilt." $flt LIMIT ".$pos.",".$cnt;

        $result = mysqli_query($GLOBALS['con'],$sql);
        
        if($result){
            while($row = mysqli_fetch_object($result)) {
                $this->StreetReportArray[$count] = $row;       
                $count++;
            }
        }                   
        
        /* Location based monthwise data */
       $monthSQL = " SELECT SR.SR_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved 
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id  
                    LEFT JOIN addr_places AS PL ON SR.PL_Id = PL.PL_Id 
                    LEFT JOIN `addr_locations` AS AL ON PL.ALC_Id = AL.ALC_Id     
                    LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id   
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE ".$filt." ".$dateFilt." 
                    GROUP BY SR.SR_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY SR.SR_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  ";
       
       $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->StreetMonthReportArray[$monthRow->SR_Id][$monthRow->YEAR][$monthRow->MONTH] = $monthRow->ABP_AmountRecieved;
            }
        }
    }
    
    function reportStreetDataCount($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }        
        /* Location based monthwise data */
        
        $sql = "SELECT COUNT(*)         
            FROM `attestation_job_details` AS AJ 
            LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
            LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id  
            LEFT JOIN addr_places AS PL ON SR.PL_Id = PL.PL_Id 
            LEFT JOIN `addr_locations` AS AL ON PL.ALC_Id = AL.ALC_Id   
            LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id 
            LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
            WHERE  ".$filt." ".$dateFilt." GROUP BY SR.SR_Id ORDER BY SR.SR_Name";
       $monthResult = mysqli_query($GLOBALS['con'],$sql);
        //return mysqli_num_rows($monthResult);
        
    }
    
    function getStreetFooter($filt,$stDate='',$enDate='') {
        if($stDate  !='' && $enDate !=''){
            $stDate     = date("Y-m-d", strtotime($stDate));
            $enDate     = date("Y-m-d", strtotime($enDate));
            $dateFilt   = " AND AJ.AJ_ReceivedDate BETWEEN '".$stDate."' AND '".$enDate."'";
        }  
        $monthSQL = " SELECT SR.SR_Id,YEAR(AJ.AJ_ReceivedDate) AS YEAR, MONTH( AJ.AJ_ReceivedDate ) AS 
                    MONTH , SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved 
                    FROM `attestation_job_details` AS AJ 
                    LEFT JOIN attestation_job_billing_payment AS AB ON AJ.AJ_Id = AB.AJ_Id 
                    LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id  
                    LEFT JOIN addr_places AS PL ON SR.PL_Id = PL.PL_Id 
                    LEFT JOIN `addr_locations` AS AL ON PL.ALC_Id = AL.ALC_Id     
                    LEFT JOIN cities AS CT ON AL.CT_Id = CT.CT_Id  
                    LEFT JOIN `states` AS ST ON AJ.ST_Id = ST.ST_Id   
                    WHERE  ".$filt." ".$dateFilt." 
                    GROUP BY SR.SR_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)  
                    ORDER BY SR.SR_Id, MONTH( AJ.AJ_ReceivedDate ),YEAR(AJ.AJ_ReceivedDate)";

        $monthResult = mysqli_query($GLOBALS['con'],$monthSQL);
        if($monthResult){
            while($monthRow = mysqli_fetch_object($monthResult)) {
                $this->FooterArray[$monthRow->YEAR][$monthRow->MONTH] += $monthRow->ABP_AmountRecieved;
            }
        }
    }
}