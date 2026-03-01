<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$AttObj = new AttestationClass();
//$REQUEST['AEId']

$AttObj->getDetails('attestation_enquiry_followup AS EF 
                        LEFT JOIN users_auth AS US ON US.US_Id = EF.US_Id
                        LEFT JOIN locations AS LC ON LC.LC_Id = EF.LC_Id',
                        ' EF.* , CONCAT(US.US_FName," ", US.US_LName) AS US_Name , LC.LC_Name', 
                        ' WHERE AE_Id = '.$REQUEST['AEId'].' ORDER BY EF.EF_CDate DESC');
$EFObj  = $AttObj->DataArray;
//print_r($EFObj);

$efStatus   = array('','Low','Normal','High','Very High');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
            <head>
                <column width="50" type="ro" align="center" sort="na"> SlNo </column>
                <column width="160" type="ro" align="center" sort="na">Last Contacted Details</column>
                <column width="*"  type="ro" align="center" sort="na">Notes</column>
                <column width="120" type="ro" align="center" sort="na">Next Follow up Date</column>
                <column width="120" type="ro" align="center" sort="na">Chance for getting Job</column>
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
                </beforeInit> 
                <afterInit>
                   
                </afterInit>

            </head>';
            if($EFObj) {
                  $j = 1;
                  foreach($EFObj as $rw) {
                      
                    $lastDate = date('d-M-Y h:i A', strtotime($rw->EF_CDate));
                    $nextDate = $rw->EF_NextDate == '0000-00-00 00:00:00' ? "" : date('d-m-Y ', strtotime($rw->EF_NextDate));
                    
                    echo '<row id="'.$rw->EF_Id.'">
                          <cell title = " ">'.$j.'</cell>
                          <cell name="" title = " "><![CDATA['.$rw->US_Name.'</br>'.$rw->LC_Name.'</br>'.$lastDate.']]></cell>
                          <cell name="" title = " ">'.$rw->EF_Comments.'</cell>
                          <cell name="" title = " ">'.$nextDate.'</cell> 
                          <cell name="" title = " ">'.$efStatus[$rw->EF_Chance].'</cell> 
                    </row>';
                    $j++;
                  }
              }
		  
echo '</rows>';
?>