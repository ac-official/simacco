<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$AJId = $REQUEST['AJId'];
$AttObj->getDetails(" attestation_job_receipts " ,"AJR_Id,AJ_Id,AJR_CRAmount,AJR_CDate", " WHERE AJ_Id = ".$AJId."");
$paymentDetails    = $AttObj->DataArray;

$AttObj->getUserName($preTally_user_ofid);
$userDetails      = $AttObj->DataArray; 
$AttObj->getLocationName($preTally_user_ofid);
$locationDetails  = $AttObj->DataArray;
//$AttObj->getDetails(" attestation_job_supporting_documents ", "ASD_Id", " WHERE AJ_Id = ".$AJId);
//$supportingDocIds = $AttObj->DataArray[0];

//$AttObj->getDetails(" attestation_supporting_documents ", "ASD_Document", " WHERE ASD_Id IN (".$supportingDocIds->ASD_Id.")");
//$supportingDocs   = $AttObj->DataArray;

$AttObj->getIdWiseName('ASD_Id','ASD_Document','attestation_supporting_documents','1');
$supportingDocs  = $AttObj->DataArray;

$AttObj->getJobTrackingDetails($AJId);
$candidateDetails = $AttObj->CandDetails; 
$trackingDetails  = $AttObj->DataArray; 
$billDetails = $AttObj->BillDetails; 
$trackRelatedDetails  = $AttObj->DataSPArray;
$trackUnRelatedDetails  = $AttObj->DataNTArray; 

array_walk_recursive($candidateDetails[0], 'replacer');
array_walk_recursive($trackingDetails[0], 'replacer');
array_walk_recursive($billDetails[0], 'replacer');
array_walk_recursive($trackRelatedDetails[0], 'replacer');
array_walk_recursive($trackUnRelatedDetails[0], 'replacer');

function replacer(& $item, $key) {   
    if ($item == '' || $item == '0000-00-00') {
        $item = 'NIL';
    }
}

$submittedBy   = $candidateDetails[0]->AJ_SubmittedType == 1 ? 'Self' : $candidateDetails[0]->AS_FName;
if($candidateDetails[0]->AJ_DeliveredType == 1) {
    $deliveredTo   = 'Self' ;
}else if($candidateDetails[0]->AJ_DeliveredType == 2){
    $deliveredTo   = $locationDetails[$candidateDetails[0]->LC_Id];
}else if($candidateDetails[0]->AJ_DeliveredType == 3){
    $deliveredTo   = $locationDetails[$candidateDetails[0]->AJ_JobDeliverdTo];
}else{
    $deliveredTo   = $candidateDetails[0]->AD_FName;
}


$filedDocCount = ceil(count($trackingDetails)/2);
$count = 0;

$DocDetailsStatusArray  = array("","Document Accepted", "Document Deleted","Document Send","Document Received", "Document Under process","Documents Deliverd" );
$UserDocUpdatedArray    = array("","Document Accepted By", "Document Deleted By","Document Send By","Document Received By", "Document Under process","Documents Deliverd By" );
$SubProcessStatusArray  = array("","Pending","Submitted","Completed","Rejected");
$UserSubProUpdatedArray = array("","", "Process Submitted By","Process Completed By","Process Rejected By");
$JobStatusArray         = array("Incompleted Registration", "Completed Registration", "Deleted", "Job Under Process", "Job Completed", "Delivered");
$supportingDocArray     = array();


$formData = '[{type: "settings",position: "label-left", labelWidth: "180", inputWidth: "auto"},';   
//$formData .= '{type: "fieldset", name:"fieldsetname", class: "mydata",  label: "<img src=\'images/icon/close_button_icon.png\' style=\'cursor:pointer;\' onclick=\'preTally.Track.hideDetailData()\' />", width:"auto", list:[';        

$formData .= ' {type: "fieldset", label: "Candidate Details",className : "jobDetails",offsetLeft : "20", width:"auto", list:[
                    { type : "template", name : "", label : "<u><b><h3>'.$candidateDetails[0]->AJ_FName.'<h3></b></u>", value : "" },
                    { type : "template", name : "", label : "Phone/Mobile", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_Mobile1.' , '.$candidateDetails[0]->AJ_Mobile2.' " },
                    { type : "template", name : "", label : "Email", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_Email.'"},
                    { type : "template", name : "", label : "Date Of Birth", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_DOB .'" },
                    { type : "template", name : "", label : "Street", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->SR_Name .'" },                   
                    { type : "template", name : "", label : "Place/City", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->PL_Name.' / '.$candidateDetails[0]->CT_Name.'" },
                    { type : "template", name : "", label : "Location", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->ALC_Name .'" },  
                    { type : "template", name : "", label : "State/Country", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->ST_Name.' / '.$candidateDetails[0]->CN_Name.'" },    
                    { type : "template", name : "", label : "House/Flat Name", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_HouseName.'" },   
                    { type : "template", name : "", label : "House/Flat Number", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_HouseNo .'" },
                    { type : "template", name : "", label : "Building Name/Number", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_BuildingName .'" },
                    { type : "template", name : "", label : "Society/Area Name", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_Society .'" },
                    { type : "template", name : "", label : "Pincode", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_Pincode .'" },
                    { type : "newcolumn", offset : "50"},
                    { type : "template", name : "", label : "Email2", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_Email2 .'" , offsetTop : "65"},
                    { type : "template", name : "", label : "Land Line Number", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_Landline .'" },        
                    { type : "template", name : "", label : "Work Experience", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_WorkExp .'" },
                    { type : "template", name : "", label : "Nearest Police Location", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_PoliceLoc .'" },
                    { type : "template", name : "", label : "Delivery Date", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_DeliveryDate .'" },
                    { type : "template", name : "", label : "Contact Mobile", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_ContactMob .'" },
                    { type : "template", name : "", label : "Delivery Address", value : "   : &nbsp;&nbsp;&nbsp; '.$deliveredTo.'" },
                    { type : "template", name : "", label : "Submitted By", value : "   : &nbsp;&nbsp;&nbsp; '.$submittedBy.'" },
                    { type : "template", name : "", label : "Submitted Date", value : "   : &nbsp;&nbsp;&nbsp; '.date("d-m-Y", strtotime($candidateDetails[0]->AJ_ReceivedDate)).'" },
                    { type : "template", name : "", label : "Job Amount", value : "   : &nbsp;&nbsp;&nbsp; '.$candidateDetails[0]->AJ_TotalAmount.'" },  
                    { type : "template", name : "", label : "Job Status", value : "   : &nbsp;&nbsp;&nbsp; '.$JobStatusArray[$candidateDetails[0]->AJ_Status].'" },
                    { type : "template", name : "", label : "Job Amount Received", value : "   : &nbsp;&nbsp;&nbsp; '.$billDetails[0]->amtReceived .'" },
                    
                ]},
                {type: "fieldset", name:"fieldsetname", class: "mydata", label: "Document Details", offsetLeft : "20", width:"auto", list:[';

foreach ($candidateDetails as $key => $value) {
    if($value->AJD_Id){
        
//        $supportingDocArray    = explode(',', $value->ASD_Id);
        array_push($supportingDocArray,  explode(',', $value->ASD_Id) );
        
        $DocumentDetails = $trackingDetails[$value->AJD_Id]; 
        $appendProcessList='';
        $appendFormData= '';
        
        //    print_r($DocumentDetails);
        $formData .= '  { type : "block", list:[ ,
                            { type : "template", name : "", label : "<u><b>'.$value->ADOC_Document.'</b></u>", value : "" },
                            { type : "template", name : "", label : "State ", value : "   : &nbsp;&nbsp;&nbsp; '.$value->AST_State.'" },
                                { type : "template", name : "", label : "Year", value : "   : &nbsp;&nbsp;&nbsp; '.$value->AJD_Year.'" },
                            { type : "template", name : "",label : "University/Board/Council", value : "   : &nbsp;&nbsp;&nbsp; '.$value->APS_Title.'" },

                            ]},{ type : "block", width : "1000", list:[,
                            { type : "template", name : "", label : "<u><b>Process List</b></u>", value : "&nbsp;&nbsp;&nbsp; <u><b>Status</b></u>" },';

   
               // $trackRelatedDetails  = $AttObj->DataSPArray; 
                 //track related data 
                $SubprocessDetails = $trackRelatedDetails[$value->AJD_Id]; 
                  foreach ($SubprocessDetails as $AJDId => $DocDetails) {
                    $formData1 .= ' { type : "template", name : "", label :"'.$DocDetails->APS_Title.'",value :": &nbsp;&nbsp;&nbsp;'.$SubProcessStatusArray[$DocDetails->AJS_Status].'" },';
                 } 
                 $formData.=$formData1;
                 $formData .='{ type : "fieldset",offsetTop:"30", width:900,list:[' ;     
                 foreach ($DocumentDetails as $AJDId => $DocDetails) {
                    if($DocDetails->AJD_Status == 3){
                        $formData .= ' { type : "template", name : "", label :"'.date("dS M Y", strtotime($DocDetails->AJT_CDate)).'&nbsp;&nbsp;&nbsp;:",value :"Transit" },';
                    }else{
                        $formData .= ' { type : "template", name : "", label :"'.date("dS M Y", strtotime($DocDetails->AJT_CDate)).'&nbsp;&nbsp;&nbsp;:",value :"'.$DocDetails->APS_Title.' &nbsp;&nbsp;-&nbsp;&nbsp;'.$SubProcessStatusArray[$DocDetails->AJS_Status].'" },';
                        $formDetailsData .= ' { type : "template", name : "", label : "'.$UserSubProUpdatedArray[$DocDetails->AJS_Status].'", value    : "   : &nbsp;&nbsp;&nbsp; '.$userDetails[$DocDetails->US_Id].', '.$locationDetails[$DocDetails->LC_Id].'" },';
                    }
                 }   
               // $trackUnRelatedDetails  = $AttObj->DataNTArray; 
                  //track unrelated data    
                $NTSubprocessDetails = $trackUnRelatedDetails[$value->AJD_Id]; 
                  foreach ($NTSubprocessDetails as $AJDId => $DocDetails) {
                    $formData2 .= ' { type : "template", name : "", label :"'.$DocDetails->APS_Title.'",value :": &nbsp;&nbsp;&nbsp;'.$SubProcessStatusArray[$DocDetails->AJS_Status].'" },';
                 } 
                        $formData.= '{ type : "newcolumn", offset : "50"},';
                        $formData.= $formDetailsData;
                        $formData.= $formData2;
                 $formData.=']},'; 
        $formData .= ']},';
        }  else {
            $formData .= ' { type : "template", name : "", label : "No Documents Added.", value : "" }';
        }
}

$uniqueSupportingDocArray = '' ;
foreach ($supportingDocArray as $keySD => $valueSD) {    
    if($keySD == 0) $uniqueSupportingDocArray = $valueSD;
    else $uniqueSupportingDocArray = $uniqueSupportingDocArray + $valueSD;
}

    $formData .=   ']},
        {type: "fieldset", name:"paymentdetails", class: "mydata", label: "Payment Details", offsetLeft : "20", width:"auto", list:[  { type : "block", list:[' ;
            if($paymentDetails){
                $j=0;
                foreach($paymentDetails as $rw){
                    $formData .= '{ type : "template", name : "", label : "'.date("dS M Y", strtotime($rw->AJR_CDate)).'", value : "   : &nbsp;&nbsp;&nbsp;'.number_format($rw->AJR_CRAmount, 2).' '.$currency.'" },';
                $j++;
                }
            }
    $formData .= ']},';
    $formData .= ']},';
$formData .=' {type: "fieldset", label: "Supporting Document Details", offsetLeft : "20", width:"auto", list:[';
                foreach ($uniqueSupportingDocArray as $count => $ASDID) {
                    if($ASDID != 'NIL')
                        $formData .= ' { type : "template", name : "", label : "'.++$count.'. '.$supportingDocs[$ASDID].'",labelWidth: "300", value : "" },';
                }
                if($count < 1)$formData .= ' { type : "template",labelWidth: "250", name : "", label : "No Supporting Documents Added.", value : "" }';

$formData .=  ']},';
$formData .=  ']';
echo $formData;
?>