<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

//print_r($_REQUEST);

if(!$_REQUEST['APS_Id']){ 
    $filter = ' AE.AE_Status = 1 AND US.OF_Id = "'.$preTally_user_ofid.'" AND AE.AE_Mobile = "'.$_REQUEST['clientNo'].'" ';
    $AttObj->listCandidateProcessOptions($filter);
    $ProcessObj = $AttObj->DataArray;
}
//print_r($ProcessObj);

//echo "----------------------------------------------------------------".$ProcessObj[0]->AE_Certificate;
$_REQUEST['AE_LastProcess'] = $_REQUEST['AE_LastProcess']   != ''    ? $_REQUEST['AE_LastProcess']   : $ProcessObj[0]->AE_LastProcess ;
$_REQUEST['ADOC_Id']        = $_REQUEST['ADOC_Id']          != ''    ? $_REQUEST['ADOC_Id']          : $ProcessObj[0]->AE_Certificate ;
$_REQUEST['APS_Id']         = $_REQUEST['APS_Id']           != ''    ? $_REQUEST['APS_Id']           : $ProcessObj[0]->APS_Id ;
$_REQUEST['CN_Id']          = $_REQUEST['CN_Id']            != ''    ? $_REQUEST['CN_Id']            : $ProcessObj[0]->AE_Visiting_CNId ;
$_REQUEST['AA_IssuedYear']  = $_REQUEST['AA_IssuedYear']    != ''    ? $_REQUEST['AA_IssuedYear']    : $ProcessObj[0]->AE_Year ;

//print_r($_REQUEST);



function findForId($Skey, $id, $array) {
   foreach ($array as $key => $val) {
       if ($val[$Skey] === $id) {
           return $array[$key];
       }
   }
   return null;
}

function amountSum($Obj, $for) {
    if($for == 'U') return $Obj[0]->APS_StatutoryUAmt + $Obj[0]->APS_ExtraUAmt + $Obj[0]->APS_CourierUAmt + $Obj[0]->APS_TravellingUAmt + $Obj[0]->APS_ManpowerUAmt + $Obj[0]->APS_ServiceUAmt;
    if($for == 'N') return $Obj[0]->APS_StatutoryNAmt + $Obj[0]->APS_ExtraNAmt + $Obj[0]->APS_CourierNAmt + $Obj[0]->APS_TravellingNAmt + $Obj[0]->APS_ManpowerNAmt + $Obj[0]->APS_ServiceNAmt;
}
function listAmount($Obj, $for, $title) {
    if($for == 'U') return array($title, $Obj[0]->APS_StatutoryUAmt, $Obj[0]->APS_ExtraUAmt, $Obj[0]->APS_CourierUAmt, $Obj[0]->APS_TravellingUAmt, $Obj[0]->APS_ManpowerUAmt, $Obj[0]->APS_ServiceUAmt);
    if($for == 'N') return array($title, $Obj[0]->APS_StatutoryNAmt, $Obj[0]->APS_ExtraNAmt, $Obj[0]->APS_CourierNAmt, $Obj[0]->APS_TravellingNAmt, $Obj[0]->APS_ManpowerNAmt, $Obj[0]->APS_ServiceNAmt);
}

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$lastProcess = $_REQUEST['AE_LastProcess'];
$ADOC_Type   = $AttObj->getValue('attestation_documents', 'ADOC_Type ', " WHERE ADOC_Id = ".$_REQUEST['ADOC_Id']);

$filter  = ' TP_Status=1 ';
$filter .= ' AND FIND_IN_SET((SELECT ST_Id FROM attestation_process_sub WHERE APS_Id = '.$_REQUEST['APS_Id'].'),TP_From) ';
$filter .= ' AND FIND_IN_SET('.$_REQUEST['CN_Id'].',TP_To) ';
$filter .= ' AND FIND_IN_SET('.$ADOC_Type.',TP_Certificate) ';
$filter .= ' AND OF_Id = '.$preTally_user_ofid;
//$filter .= ' AND FIND_IN_SET('.$_REQUEST['AA_VisaType'].',TP_Certificate) ';




$AttObj->listATPProcessOptions($filter);
$ProcessObj = $AttObj->TrackArray;

$AttObj->viewTrackMainProcess($preTally_user_ofid);
$PDObj = $AttObj->TrackArray;

$AttObj->viewSupportingDocuments($preTally_user_ofid);
$SDObj = $AttObj->TrackArray;

//print_r($SDObj);

//echo $keys = array_keys(array_column($PDObj, 'APM_Id'), 7);
//echo $lastNames = array_column($PDObj, 'APM_Id', '7');
//$PDArray =  (array) $PDObj;
$PDArray = json_decode(json_encode($PDObj), true);
$SDArray = json_decode(json_encode($SDObj), true);
//$key = array_search(7, array_column($PDArray, 'APM_Id'));

//echo ("The key is: ".$key);
//echo '<pre>';
//print_r($ProcessObj);


//print_r(array_values($PDArray[array_search(7, $PDArray)]));
//print_r(array_search(7, $PDArray));

//$id = findForId('APM_Id', '7', $PDArray);
//print_r($id);
//echo '--------------------';
//print_r($ProcessObj);
//print_r($PDArray);

$AttObj->getATPSubProcess(' WHERE APS.APS_Id = '.$_REQUEST['APS_Id'].' AND APS.OF_Id = '. $preTally_user_ofid.' AND (APS.APS_ToYear>='.$_REQUEST['AA_IssuedYear'].' AND '.$_REQUEST['AA_IssuedYear'].' >=APS.APS_FromYear)');
$APSObj = $AttObj->TrackArray;
//echo '<pre>';
//print_r($APSObj);

//print_r($APSObj);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <userdata name="LC_Id">'.$preTally_user_lcid.'</userdata>
    <userdata name="LC_Name">'.$preTally_user_lcname.'</userdata>
    <userdata name="Date">'.date('d-m-Y').'</userdata>
    <userdata name="optionCount">'.count($ProcessObj).'</userdata>
    <head>
        <column width="40" type="ro" align="center" sort="na">SlNo</column>
        <column width="*"  type="ro" align="left" sort="na">Process To Be Completed</column>
        <column width="*"  type="ro" align="left" sort="na">Supporting Documents Required</column>
        <column width="0" type="ro" align="right" sort="na">Urgent Rate</column>
        <column width="80" type="ch" align="center" sort="na">#cspan</column>
        <column width="0" type="ro" align="right" sort="na">#cspan</column>
        <column width="0" type="ro" align="right" sort="na">Normal Rate</column>
        <column width="80" type="ch" align="center" sort="na">#cspan</column>
        <column width="0" type="ro" align="right" sort="na">#cspan</column>
        
        <column width="50" type="ro" align="center" sort="na">Select</column>
        <column width="50" type="ro" align="center" sort="na">Info</column>
        <settings>
            <colwidth>px</colwidth>
        </settings>
        <beforeInit> 
            <call command="setSkin">
                <param>dhx_skyblue</param>
            </call> 
            <call command="setImagePath">
                <param>assets/grid/codebase/imgs/</param>
            </call> 
            <call command="enableColSpan">
                <param>true</param>
            </call> 
        </beforeInit>
    </head>';
    if($ProcessObj) {
        $j = 1;
        foreach($ProcessObj as $key=>$rw) { 


            $processError = 0;
            $processData = array();
            $procedures = explode(",", $rw->TP_Procedures);
            $k = 1;
            $processData['process'] = '<div class="automateBlockListFiller">&nbsp;</div>';
            $processData['suppDoc'] = '<div class="automateBlockListFiller">&nbsp;</div>';
            
            $processData['APS_ID']    = array();
            $processData['APS_SUP']   = array();
            $processData['APS_UAMT']  = array();
            $processData['APS_NAMT']  = array();
            $processData['URG_AMT']   = 0;
            $processData['NRM_AMT']   = 0;
            

            $lastProcessKey = array_search($lastProcess, $procedures); 
            $lastProcessKey++;
            $procedureArray = array_splice($procedures, $lastProcessKey,count($procedures));
            
            if(sizeof($procedureArray) == 0){ 
                $processData['process'] = '<div class="automateBlockListNo">'.$k.'</div><div class="automateBlockListError"><img src="images/icon/warning_12.png" />&nbsp;&nbsp; No Process Left.</div>';
                $processError = 1;
            }
            foreach ($procedureArray as &$value) {
//                echo $value.'--';

                $processDetails = findForId('APM_Id', $value, $PDArray);
                //echo $processDetails['APM_Title'].'--'.$processDetails['APMA_Id'];
                //echo $processDetails['APMA_Id'];
                //echo '--'.$value.'--';



                if($processDetails['APMA_Id'] == 1) { //For University Verification
                
                    //$AttObj->getATPSubProcess(' WHERE APS.APS_Id = '.$_REQUEST['APS_Id']);
                    //$APSObj = $AttObj->TrackArray;
                    //print_r($APSObj);
                    //echo $APSObj[0]->APS_Title.' '.$processDetails['APM_Title'];
                    if($APSObj) {
                        array_push($processData['APS_ID'], $APSObj[0]->APS_Id);
                        array_push($processData['APS_SUP'], explode(",",$APSObj[0]->ASD_Id));
                        //$processData['process'] .= '<div class="automateBlockListNo">'.$k.'</div><div class="automateBlockList">'.$APSObj[0]->APS_Title.' '.$processDetails['APM_Title'].'</div>';
                        $processTitle = '<div class="automateBlockList">'.$APSObj[0]->APS_Title.' '.$processDetails['APM_Title'].'</div>';
                        //$processData['URG_AMT'] += $APSObj[0]->APS_StatutoryNAmt + $APSObj[0]->APS_StatutoryUAmt + $APSObj[0]->APS_ExtraNAmt + $APSObj[0]->APS_ExtraUAmt + $APSObj[0]->APS_CourierNAmt + $APSObj[0]->APS_CourierUAmt + $APSObj[0]->APS_TravellingNAmt + $APSObj[0]->APS_TravellingUAmt + $APSObj[0]->APS_ManpowerNAmt + $APSObj[0]->APS_ManpowerUAmt + $APSObj[0]->APS_ServiceNAmt + $APSObj[0]->APS_ServiceUAmt;

                        array_push($processData['APS_UAMT'], listAmount($APSObj, 'U', $APSObj[0]->APS_Title.' '.$processDetails['APM_Title']));
                        array_push($processData['APS_NAMT'], listAmount($APSObj, 'N', $APSObj[0]->APS_Title.' '.$processDetails['APM_Title']));

                        $processData['URG_AMT'] += amountSum($APSObj, 'U');
                        $processData['NRM_AMT'] += amountSum($APSObj, 'N');
                    } else {
                        $processTitle = '<div class="automateBlockListError"><img src="images/icon/warning_12.png" />&nbsp;&nbsp;Error !!! Not In Database.</div>';
                        $processError = 1;
                    }
                    $processData['process'] .= '<div class="automateBlockListNo">'.$k.'</div>'.$processTitle;
        
                            
                    //echo "\n";
                } elseif($processDetails['APMA_Id'] == 2) { //For HRD Process

                    $AttObj->getATPSubProcess(' WHERE APS.APM_Id = '.$value.' AND APS.ST_Id = '.$APSObj[0]->ST_Id.' AND APS.OF_Id = '. $preTally_user_ofid .' AND (APS.APS_ToYear>='.$_REQUEST['AA_IssuedYear'].' AND '.$_REQUEST['AA_IssuedYear'].' >=APS.APS_FromYear)');
                    $APS_ST_Obj = $AttObj->TrackArray;
                    //print_r($APS_ST_Obj);
                    //echo $APS_ST_Obj[0]->APS_Title;
                    if($APS_ST_Obj) {
                        array_push($processData['APS_ID'], $APS_ST_Obj[0]->APS_Id);
                        array_push($processData['APS_SUP'], explode(",",$APS_ST_Obj[0]->ASD_Id));
                        //$processData['APS_ID'] = $APS_ST_Obj[0]->APS_Id;
                        //$processData['process'] .= '<div class="automateBlockListNo">'.$k.'</div><div class="automateBlockList">'.$APS_ST_Obj[0]->APS_Title.'</div>';
                        $processTitle = '<div class="automateBlockList">'.$APS_ST_Obj[0]->APS_Title.'</div>';
                        
                        array_push($processData['APS_UAMT'], listAmount($APS_ST_Obj, 'U', $APS_ST_Obj[0]->APS_Title));
                        array_push($processData['APS_NAMT'], listAmount($APS_ST_Obj, 'N', $APS_ST_Obj[0]->APS_Title));

                        $processData['URG_AMT'] += amountSum($APS_ST_Obj, 'U');
                        $processData['NRM_AMT'] += amountSum($APS_ST_Obj, 'N');            
                        //echo "\n";
                    } else {
                        $processTitle = '<div class="automateBlockListError"><img src="images/icon/warning_12.png" />&nbsp;&nbsp;Error !!! Not In Database.</div>';
                        $processError = 1;
                    }
                    $processData['process'] .= '<div class="automateBlockListNo">'.$k.'</div>'.$processTitle;

                } elseif($processDetails['APMA_Id'] == 3 || $processDetails['APMA_Id'] == 5 || $processDetails['APMA_Id'] == 6) {

                    $AttObj->getATPSubProcess(' WHERE APS.APM_Id = '.$value.' AND APS.CN_Id = '.$APSObj[0]->CN_Id.' AND APS.OF_Id = '. $preTally_user_ofid .' AND (APS.APS_ToYear>='.$_REQUEST['AA_IssuedYear'].' AND '.$_REQUEST['AA_IssuedYear'].' >=APS.APS_FromYear)');
                    $APS_CN_Obj = $AttObj->TrackArray;
                    //print_r($APS_CN_Obj);
                    //echo $APS_CN_Obj[0]->APS_Title;
                    if($APS_CN_Obj) {
                        array_push($processData['APS_ID'], $APS_CN_Obj[0]->APS_Id);
                        array_push($processData['APS_SUP'], explode(",",$APS_CN_Obj[0]->ASD_Id));
                        //$processData['APS_ID'] = $APS_CN_Obj[0]->APS_Id;
                        $processTitle = '<div class="automateBlockList">'.$APS_CN_Obj[0]->APS_Title.'</div>';

                        array_push($processData['APS_UAMT'], listAmount($APS_CN_Obj, 'U', $APS_CN_Obj[0]->APS_Title));
                        array_push($processData['APS_NAMT'], listAmount($APS_CN_Obj, 'N', $APS_CN_Obj[0]->APS_Title));

                        $processData['URG_AMT'] += amountSum($APS_CN_Obj, 'U');
                        $processData['NRM_AMT'] += amountSum($APS_CN_Obj, 'N');
                    } else {
                        $processTitle = '<div class="automateBlockListError"><img src="images/icon/warning_12.png" />&nbsp;&nbsp;Error !!! Not In Database.</div>';
                        $processError = 1;
                    }
                    $processData['process'] .= '<div class="automateBlockListNo">'.$k.'</div>'.$processTitle;
                    //echo "\n";
                } elseif($processDetails['APMA_Id'] == 4) {

                    $AttObj->getATPSubProcess(' WHERE APS.APM_Id = '.$value.' AND APS.CN_Id_Travelling = '.$_REQUEST['CN_Id'].' AND APS.OF_Id = '. $preTally_user_ofid .' AND (APS.APS_ToYear>='.$_REQUEST['AA_IssuedYear'].' AND '.$_REQUEST['AA_IssuedYear'].' >=APS.APS_FromYear)');
                    $APS_CNT_Obj = $AttObj->TrackArray;
                    //print_r($APS_CN_Obj);
                    //echo $APS_CN_Obj[0]->APS_Title;
                    if($APS_CNT_Obj) {
                        array_push($processData['APS_ID'], $APS_CNT_Obj[0]->APS_Id);
                        array_push($processData['APS_SUP'], explode(",",$APS_CNT_Obj[0]->ASD_Id));
                        //$processData['APS_ID'] = $APS_CNT_Obj[0]->APS_Id;
                        //$processData['process'] .= '<div class="automateBlockListNo">'.$k.'</div><div class="automateBlockList">'.$APS_CNT_Obj[0]->APS_Title.'</div>';
                        $processTitle = '<div class="automateBlockList">'.$APS_CNT_Obj[0]->APS_Title.'</div>';

                        array_push($processData['APS_UAMT'], listAmount($APS_CNT_Obj, 'U', $APS_CNT_Obj[0]->APS_Title));
                        array_push($processData['APS_NAMT'], listAmount($APS_CNT_Obj, 'N', $APS_CNT_Obj[0]->APS_Title));

                        $processData['URG_AMT'] += amountSum($APS_CNT_Obj, 'U');
                        $processData['NRM_AMT'] += amountSum($APS_CNT_Obj, 'N');
                        //echo "\n";
                    } else {
                        $processTitle = '<div class="automateBlockListError"><img src="images/icon/warning_12.png" />&nbsp;&nbsp;Error !!! Not In Database.</div>';
                        $processError = 1;
                    }
                    $processData['process'] .= '<div class="automateBlockListNo">'.$k.'</div>'.$processTitle;
                } else {
                    $processData['process'] .= '<div class="automateBlockListNo">'.$k.'</div><div class="automateBlockListError"><img src="images/icon/warning_12.png" />&nbsp;&nbsp;Error !!! Not In Database.</div>';
                    $processError = 1;
                }
                    
        
                $k++;
            }
            $processData['process'] .= '<div class="automateBlockListFiller">&nbsp;</div>';
            
            $SUPDOCs = array_filter(array_values(array_unique(call_user_func_array('array_merge', $processData['APS_SUP']))));
           
            $k = 1;
            foreach ($SUPDOCs as &$value) {
                $docDetails = findForId('ASD_Id', $value, $SDArray);
                //$SUPDOCList .= $docDetails['ASD_Document'];
                $processData['suppDoc'] .= '<div class="automateBlockListNo">'.$k.'</div><div class="automateBlockList">'.$docDetails['ASD_Document'].'</div>';
                $k++;
            }
            $processData['suppDoc'] .= '<div class="automateBlockListFiller">&nbsp;</div>';
            //echo $SUPDOCList;
            
            echo '<row id="'.$j.'" class = "optionClass">
                    <userdata name="AA_Id">'.$rw->TP_Id.'</userdata>
                    <userdata name="ST_Id">'.$APSObj[0]->ST_Id.'</userdata>
                    <userdata name="AA_Comments">'.htmlspecialchars($rw->TP_Comment).'</userdata>
                    <userdata name="AA_Process"><![CDATA['.json_encode($processData['APS_ID']).']]></userdata>
                    <userdata name="AA_SupportingDocuments"><![CDATA['.json_encode($SUPDOCs).']]></userdata>
                    <userdata name="TAP_U_Amount"><![CDATA['.json_encode($processData['APS_UAMT']).']]></userdata>
                    <userdata name="TAP_N_Amount"><![CDATA['.json_encode($processData['APS_NAMT']).']]></userdata>


                    <cell><![CDATA[<div class = "_amntSplitUp'.$j.'" onclick="preTally.Track.showEnqAmtDetailsPop(this,'.$j.');">'.$j.'</div>]]></cell>
                    <cell ><![CDATA['.$processData['process'].']]></cell>
                    <cell ><![CDATA['.$processData['suppDoc'].']]></cell>
                    <cell >&#x20b9; '.$processData['URG_AMT'].'</cell>
                    <cell ></cell>
                    <cell title = "Urgent Rate chart"><![CDATA[<img src="images/icon/lens_18.png" height="18" style="cursor:pointer;" onclick="preTally.Track.ATPAmountSplit(this,'.$j.', \'U\');" />]]></cell>
                    <cell >&#x20b9; '.$processData['NRM_AMT'].'</cell>
                    <cell >1</cell>
                    <cell title = "Normal Rate chart"><![CDATA[<img src="images/icon/lens_18.png" height="18" style="cursor:pointer;" onclick="preTally.Track.ATPAmountSplit(this,'.$j.', \'N\');" />]]></cell>';
       
            if($processError == 0) {
                echo '<cell title = "Click to add option." class = "optionCellClass"><![CDATA[<input type="hidden" class="optionDivClass" value ="'.$j.'"/><img src="images/icon/add.png" height="18" style="cursor:pointer;" onclick="preTally.Track.selectATPOption('.$j.',\''.$REQUEST['forP'].'\');" />]]></cell>';
            } else {
                echo '<cell title = "Cannot Select.Invalid Options." class = "optionCellClass"><![CDATA[<input type="hidden" class="optionDivClass" value ="'.$j.'"/><img src="images/icon/error_18.png" />]]></cell>';
            }
            echo '  <cell title = "Click here to view comments" class = "optionCellClass"><![CDATA[<input type="hidden" class="optionDivClass" id="optionDivClass" value ="'.$j.'"/><img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" />]]></cell>';
            echo '</row>';
            $j++;

        }   

    }

    else {
        echo '<row id="0"> 
            <cell type="ro" colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';



die();

?>
