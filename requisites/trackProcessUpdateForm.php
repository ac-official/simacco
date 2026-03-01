<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$gid   = trim($REQUEST['gId']); 
$docId = trim($REQUEST['docId']);

$AttObj->getSubMainProcess($docId); 
$MProsArray = $AttObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
        <item type="settings" position="label-left" labelWidth="150" noteWidth="180" />
    
            <item type="hidden" name="GP_Id" value="'.$gid.'"></item>
            <item type="hidden" name="DOC_Id" value="'.$docId.'"></item>
            <item type="hidden" name="DS_PDescription" value=""></item>
            <item type="hidden" name="DS_TDescription" value=""></item>
            
            <item type="fieldset" offsetTop="20" label = "PROCESS DETAILS" width = "auto" offsetLeft="20" >
                <item type="block">
                    <item type="combo"  name="MP_Id" label="" required="true" readonly="true" width="300" >
                        <option value = "" label="Select Main Process" selected="selected"/>';
                            foreach($MProsArray as $rw) {
                                if(trim($rw->APM_Title)) 
                                    echo '<option value="'.$rw->APM_Id.'" label="'.$rw->APM_Title.'" selected="selected"/>';
                            }
                        echo '<note width="150">MAIN PROCESS</note>
                    </item> 
                    
                    <item type="newcolumn"></item>
                    
                    <item type="combo" name="APS_Id" offsetLeft="30" label="" required="true" readonly="true" width="340">
                        <option value = "" label="Select Sub Process" selected="selected"/>';
                        echo '<note width="150">SUB PROCESS</note>
                    </item> 
                </item>  
                
                <item type="block" width="580" offsetTop="10" offsetLeft="60" >
                    <item type="radio" name="AJS_Status" label="Submit" value="2" position="label-right" checked="true"></item>
                    <item type="newcolumn"/>
                    <item type="radio" name="AJS_Status" label="Complete" value="3" position="label-right"></item>
                    <item type="newcolumn"/>
                    <item type="radio" name="AJS_Status" label="Reject" value="4" position="label-right"></item>
                </item>
            </item>
            
            <item type="fieldset" name ="Process_Fee" label = "PROCESS FEES AND CHARGES DETAILS" width = "auto" offsetLeft="20" >
                <item type="combo"  name="IT_PId" label="" required="true" width="250" validate="ValidNumeric">
                    <note width="">ITEM</note>
                </item>

                <item type="newcolumn"/>

                <item type="combo"  name="DS_PId" label="" required="true" width="250" offsetLeft="20">
                    <note width="">DESCRIPTION</note>
                </item>

                <item type="newcolumn"/>

                <item type="input" name="BS_Amount_Process" inputWidth="150" required="true" validate="^[0-9 ]+$" offsetLeft="20">
                    <note width="">AMOUNT (PROCESS FEE)</note>
                </item>
            </item>
            
            <item type="fieldset" label = "TRAVELLING FEES AND CHARGES DETAILS" width = "auto" offsetLeft="20" >
                <item type="combo"  name="IT_TId" label="" required="true" width="250"  validate="ValidNumeric">
                    <note width="">ITEM</note>
                </item>

                <item type="newcolumn"/>

                <item type="combo"  name="DS_TId" label="" required="true" width="250" offsetLeft="20">
                    <note width="">DESCRIPTION</note>
                </item>

                <item type="newcolumn"/>

                <item type="input" name="BS_Amount_Travel" inputWidth="150" required="true" validate="^[0-9 ]+$" offsetLeft="20">
                    <note width="150">AMOUNT (TRAVELLING EXPENSE)</note>
                </item>
            </item>
            
            <item type="block" width="auto"  offsetLeft="200">
                <item type="button" value="Update Process" name="UpGpPros"/>
                <item type="newcolumn"/>
                <item type="button" value="Cancel" name="UpGpProsCanl" offsetLeft="10" />
            </item>
    </items>';