<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$APSId = $REQUEST['APS_ID'] != '' ? $REQUEST['APS_ID'] :  0;
if($REQUEST['APS_ID']) {
    include_once($BASEPATH . "preTallyClass/AttestationClass.php");
    $AttObj = new AttestationClass();
    $AttObj->getDetails('attestation_process_sub APS', 
                        'APS.*,APM.APM_Title,CN1.CN_Name AS CN_Name_Travelling,CN.CN_Name,ST.ST_Name,CT.CT_Name,ALC.ALC_Name,PL.PL_Name,SR.SR_Name',
                        'LEFT JOIN attestation_process_main APM ON APM.APM_Id=APS.APM_Id
                         LEFT JOIN addr_locations AS ALC ON APS.ALC_Id = ALC.ALC_Id 
                         LEFT JOIN addr_places AS PL ON APS.PL_Id = PL.PL_Id 
                         LEFT JOIN addr_streets AS SR ON APS.SR_Id = SR.SR_Id 
                         LEFT JOIN cities AS CT ON APS.CT_Id = CT.CT_Id 
                         LEFT JOIN states AS ST ON APS.ST_Id = ST.ST_Id 
                         LEFT JOIN countries AS CN ON CN.CN_Id = ST.CN_Id 
                         LEFT JOIN countries AS CN1 ON CN1.CN_Id = APS.CN_Id_Travelling 
                            WHERE APS.APS_Id ="'.$APSId.'" AND APS.OF_Id = "'.$preTally_user_ofid.'"'
                       );
    $APSObj = $AttObj->DataArray;
}
$curYear   = date("Y");
$lastyear  = $curYear + 5 ;

$CompleteDate=$APSObj['APSE_FDate'] != '0000-00-00' ? $APSObj['APSE_FDate'] : '';

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
    <item type="settings" position="label-left" labelWidth="120" inputWidth="221" noteWidth="180" offsetLeft="20"/>
    <item type="hidden" name="APS_Id" value="'.$APSId.'"/>
        <item type="hidden" name="APM_Title" value="'.$APSObj[0]->APM_Title.'"/>
        <item type="hidden" name="APM_Id" value="'.$APSObj[0]->APM_Id.'"/>
        <item type="hidden" name="CN_Name_Travelling" value="'.$APSObj[0]->CN_Name_Travelling.'"/>
        <item type="hidden" name="CNID_Travelling" value="'.$APSObj[0]->CN_Id_Travelling.'"/>
        <item type="hidden" name="ASDIds" value="'.$APSObj[0]->ASD_Id.'"/>    
    
    <item type="combo" name="APS_APM_Id" label="Process" required="true" offsetTop="30" className="APS_APM_Combo" filterCache="true" validate="^[0-9]+$">
        <note width="150">Main Process</note>
    </item>
    
    <item type="input" name="APS_Title" label="Sub Process" required="true" value="'.$APSObj[0]->APS_Title.'" > 
        <note width="150">Sub Process</note>
    </item>
    
    <item type="combo" name="CN_Id_Travelling" label="Travelling Country" value="'.$APSObj[0]->CN_Id_Travelling.'" connector="requisites/countries.php">
            <note width="150">Country</note>
    </item> 
    
    <item type="combo" label="Supporting Documents" comboType = "checkbox" name="ASD_Id" value = "'.$APSObj[0]->ASD_Id.'" position="label-left" required="true">
        <note>Supporting Documents</note>
    </item>
    
    <item type="input" name="GOGL_PlaceSearch" value="" label="Land Mark">
        <note width="150">Please Add Your Land Mark</note>
    </item>    
   
    <item type="input" name="GOGL_Street" value="'.$APSObj[0]->SR_Name.'" label="Street" labelWidth="120" offsetLeft="30" inputWidth="211" required="true">
        <note width="150">Please Add Your Street</note>
    </item>
    <item type="input" name="GOGL_Place" value="'.$APSObj[0]->PL_Name.'" label="Place" labelWidth="120" offsetLeft="30" inputWidth="211" required="true">
        <note width="150">Please Add Your Place</note>
    </item>
    <item type="input" name="GOGL_Location" value="'.$APSObj[0]->ALC_Name.'" label="Location" labelWidth="120" offsetLeft="30" inputWidth="211" required="true">
        <note width="150">Please Add Your Location</note>
    </item>
    <item type="input" name="GOGL_City" value="'.$APSObj[0]->CT_Name.'" label="City" labelWidth="120" offsetLeft="30" inputWidth="211" required="true">
        <note width="150">Please Add Your City</note>
    </item>
    <item type="input" name="GOGL_State" value="'.$APSObj[0]->ST_Name.'" label="State" labelWidth="120" offsetLeft="30" inputWidth="211" required="true">
        <note width="150">Please Add Your State</note>
    </item>
    <item type="input" name="GOGL_Country" value="'.$APSObj[0]->CN_Name.'" label="Country" labelWidth="120" offsetLeft="30" inputWidth="211" required="true">
        <note width="150">Please Add Your Country</note>
    </item>
    
    <item type="input" name="GOGL_Pincode" label="Pincode"  value="'.$APSObj[0]->APS_Pincode.'" labelWidth="120" offsetLeft="30" inputWidth="211" required="true" validate="ValidNumeric">
        <note width="150">Pincode</note>
    </item>
    
    <item type="input" name="APS_Description" label="Description" rows="3" value="'.$APSObj[0]->APS_Description.'">
        <note width="150">Description</note>
    </item>

    <item type="block" width="400" offsetLeft = "0">
        <item type="calendar" name="APSE_FDate" label="Back Date" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" readonly="true" value="" offsetLeft = "-30" inputWidth="100">
            <note width="100">From Date</note>
        </item>
        <item type="newcolumn"/>
        <item type="calendar" name="APSE_LDate" label="" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" readonly="true" value="" labelWidth="0" inputWidth="100">
            <note width="100">Till Date</note>
        </item>
    </item>

    <item type="block" width="400" offsetLeft = "0">
        <item type="input" name="APS_StatutoryNAmt" label="Statutory Amount" value="'.$APSObj[0]->APS_StatutoryNAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" offsetLeft = "-30" inputWidth="100">
            <note width="100">Normal Amount</note>
        </item>
        <item type="newcolumn"/>
        <item type="input" name="APS_StatutoryUAmt" label="" value="'.$APSObj[0]->APS_StatutoryUAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" labelWidth="0" inputWidth="100">
            <note width="100">Urgent Amount</note>
        </item>
    </item>
    <item type="block" width="400" offsetLeft = "0">
        <item type="input" name="APS_ExtraNAmt" label="Extra Amount" value="'.$APSObj[0]->APS_ExtraNAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" offsetLeft = "-30" inputWidth="100">
            <note width="100">Normal Amount</note>
        </item>
        <item type="newcolumn"/>
        <item type="input" name="APS_ExtraUAmt" label="" value="'.$APSObj[0]->APS_ExtraUAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" labelWidth="0" inputWidth="100">
            <note width="100">Urgent Amount</note>
        </item>
    </item>
    
    <item type="block" width="400" offsetLeft = "0">
        <item type="input" name="APS_CourierNAmt" label="Courier Charge" value="'.$APSObj[0]->APS_CourierNAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" offsetLeft = "-30" inputWidth="100">
            <note width="100">Normal Amount</note>
        </item>
        <item type="newcolumn"/>
        <item type="input" name="APS_CourierUAmt" label="" value="'.$APSObj[0]->APS_CourierUAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" labelWidth="0" inputWidth="100">
            <note width="100">Urgent Amount</note>
        </item>
    </item>
    
    <item type="block" width="400" offsetLeft = "0">
        <item type="input" name="APS_TravellingNAmt" label="Travelling Charge" value="'.$APSObj[0]->APS_TravellingNAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" offsetLeft = "-30" inputWidth="100">
            <note width="100">Normal Amount</note>
        </item>
        <item type="newcolumn"/>
        <item type="input" name="APS_TravellingUAmt" label="" value="'.$APSObj[0]->APS_TravellingUAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" labelWidth="0" inputWidth="100">
            <note width="100">Urgent Amount</note>
        </item>
    </item>
    
    <item type="block" width="400" offsetLeft = "0">
        <item type="input" name="APS_ManpowerNAmt" label="Manpower Charge" value="'.$APSObj[0]->APS_ManpowerNAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" offsetLeft = "-30" inputWidth="100">
            <note width="100">Normal Amount</note>
        </item>
        <item type="newcolumn"/>
        <item type="input" name="APS_ManpowerUAmt" label="" value="'.$APSObj[0]->APS_ManpowerUAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" labelWidth="0" inputWidth="100">
            <note width="100">Urgent Amount</note>
        </item>
    </item>
    
    <item type="block" width="400" offsetLeft = "0">
        <item type="input" name="APS_ServiceNAmt" label="Company Service Charge" value="'.$APSObj[0]->APS_ServiceNAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" offsetLeft = "-30" inputWidth="100">
            <note width="100">Normal Amount</note>
        </item>
        <item type="newcolumn"/>
        <item type="input" name="APS_ServiceUAmt" label="" value="'.$APSObj[0]->APS_ServiceUAmt.'" validate="^([0-9]*|\d*\.\d{1}?\d*)$" labelWidth="0" inputWidth="100">
            <note width="100">Urgent Amount</note>
        </item>
    </item>
    
    <item type="block" width="400" offsetLeft = "0">
        <item type="combo" label="Year" name="APS_FromYear" required="true" validate = "ValidNumeric" offsetLeft="-30" inputWidth="100">';
            echo '<option value=" " label="From Year" />';
            for($i= 1950; $i <= $lastyear; $i++){
                $selected = $i == $APSObj[0]->APS_FromYear ? true : false ;
                echo ' <option value="'.$i.'" label="'.$i.'" selected = "'.$selected.'" />';
            }
            echo'<note width="100">From Year</note>
        </item> 
        <item type="newcolumn"/>
        <item type="combo" label="" name="APS_ToYear" required="true" validate = "ValidNumeric" labelWidth="0" inputWidth="100">';
            echo '<option value=" " label="To Year" />';
            for($i=$lastyear; $i >= 1950; $i--){
                $selected = $i == $APSObj[0]->APS_ToYear ? true : false ;
                echo ' <option value="'.$i.'" label="'.$i.'" selected = "'.$selected.'" />';
            }
            echo'<note width="100">To Year</note>
        </item> 
    </item>
    
    <item type="combo" label="Status" name="APS_Status" readonly="true" required="true" >';
        if( ( $APSObj[0]->APS_Status && $APSObj[0]->APS_Status == 1 ) ) $selected = "true"; else $selected = "false";
            echo '<option value="1" label="Published" selected="'.$selected.'" />';
        if( $APSObj[0]->APS_Status == 0 ) $selected = "true"; else $selected = "false";
            echo '<option value="0" label="Blocked" selected="'.$selected.'" />
        <note width="150">Publish Status</note>
    </item>

    <item type="block" width="300" offsetTop="50">
        <item type="button" value="Save" name="newSubProcessValidate"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="newSubProcessCancel"/>
    </item>
</items>';