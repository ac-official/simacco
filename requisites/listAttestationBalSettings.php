<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$balSheetArray = array(
    1=>'While Adding new Job(s)',
    2=>'While Receiving Advance Payment',
    3=>'While Adding Additional Job(s)',
    4=>'While Deleting added Job(s)',
    5=>'While Receiving Balance Payment',
    6=>'Expense aganist Courier Charges',
//    7=>'Expense aganist Submitting Process',
//    8=>'Travelling Expense'
 );

$balShtSettId = implode(",",array_keys($balSheetArray));
//$AttObj->getDetails('attestation_job_balsheet_settings AS AJB','AJB.AJBS_Id,AJB.AJBS_Type,AJB.AJBS_Status,AJB.IT_Id,ITM.IT_Name,ITM.MH_Type','LEFT JOIN  items AS ITM ON ITM.IT_Id =  AJB.IT_Id WHERE AJB.OF_Id = '.$preTally_user_ofid .' AND AJB.AJBS_Type IN ('.$balShtSettId.') ORDER BY AJB.AJBS_Type ASC');
$AttObj->getBalsheetsettingsItems($balShtSettId,$preTally_user_ofid);
$BalObj = $AttObj->balshtArrayList;
//print_r($BalObj);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="80" type="ro" align="center" sort="na"> Type </column>
        <column width="0" type="ro" align="center" sort="na"></column>
        <column width="*" type="ro" align="left" sort="na"> Track Settings </column>
        <column width="*" type="ro" align="left" sort="na"> Items </column>
        <column width="0" type="ro" align="center" sort="na"></column>
        <column width="120" type="ro" align="center" sort="na">	Status </column>
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
            <call command="enableSmartRendering">
                <param>false</param>
            </call> 
            <call command="enableColSpan">
                <param>true</param>
            </call>   
        </beforeInit> 
    </head>';

    if($balSheetArray){
        $j = 1;
        foreach ($balSheetArray as $key => $value) {
            
            if($BalObj[$key]->MH_Type == '1')
                $entryType = "Income";
            else if($BalObj[$key]->MH_Type == '2')            
                $entryType = "Expense";
            else $entryType = "--";  
            
            if($BalObj[$key]->AJBS_Status == '1'){
                $AAUTH_Status = "Published";
                $statusImg    = "tick.png";
            }else if($BalObj[$key]->AJBS_Status == '0') {           
                $AAUTH_Status = "Blocked";
                $statusImg    = "cross_16.png";
            } else {
                $AAUTH_Status = "Not Set";
                $statusImg    = "alert_16.png";
            }
            
            $item = $BalObj[$key]->IT_Name != '' ? $BalObj[$key]->IT_Name : "--" ;
            echo '<row id="'.$j.'">
                
                    <userdata name="UData_AJBS_Id">'.$BalObj[$key]->AJBS_Id.'</userdata>
                    <userdata name="UData_AJS_Name">'.$balSheetArray[$key].'</userdata>
                    <userdata name="UData_MH_Type">'.$BalObj[$key]->MH_Type.'</userdata>
                    <userdata name="UData_AJS_ITId">'.$BalObj[$key]->IT_Id.'</userdata>
                    <userdata name="UData_Bal_Status">'.$BalObj[$key]->AJBS_Status.'</userdata>
                    <userdata name="UData_AJBS_Type">'.$key.'</userdata>
                        
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="MH_Type">'.$entryType.'</cell>
                    <cell>'.$entryType.'</cell>
                    <cell title=" " name="ABS_BalSettngs">'.$value.'</cell>
                    <cell title=" " name="ABS_IT_Name">'.$item.'</cell>
                    <cell >'.$AAUTH_Status.'</cell>
                    <cell title="'.$AAUTH_Status.'"><![CDATA[<img src="images/icon/'.$statusImg.'" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
            echo '</row>';

            $j++;
        }
    }
echo '</rows>';
?>