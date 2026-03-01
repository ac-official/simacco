<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/LocationClass.php");
include_once($BASEPATH . "preTallyClass/AddressClass.php");
$LocationObj = new LocationClass();
$AddressObj = new AddressClass();
if($preTally_user_ofid == 1) {
    $flds     =   'OF1.OF_Name,LC.LC_Id,LC.US_Id,LC.LC_Name,LC.OF_Id,LC.AP_Id,LC.LC_Street,LC.LC_Building,LC.LC_Phone,LC.LC_Pincode,LC.CT_Id,LC.ST_Id ,LC.CN_Id,LC.LC_Comments,LC.LC_Status ';
    $filter   =   ' WHERE LC.LC_Status != 5 ORDER BY LC.LC_Name';
}else {
    $flds     =   '*';
    $filter   =   'WHERE LC.OF_Id = '.$preTally_user_ofid.' AND LC.LC_Status != 5 ORDER BY LC.LC_Name';
}
$LocationObj->viewLocationGrid($filter);
$LC_Obj = $LocationObj->LocationArray;



echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">locations</userdata>
		<userdata name="db_primary">LC_Id</userdata>
		<userdata name="db_date">LC_MDate</userdata>
		<userdata name="db_status">LC_Status</userdata>
		<head>
			<column width="50" type="ro" align="center" sort="na"> SlNo </column>
			<column width="*" type="ro" align="left" sort="na">Branch</column>';
                        if($preTally_user_ofid==1) { echo'<column width="*" type="ro" align="left" sort="na">Company</column>'; }
                        echo '<column width="0" type="ro" align="center" sort="na">	LLStatus </column>';
			echo '<column width="60" type="ro" align="center" sort="na">	Status </column>
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

		  </head>';
                    if($LC_Obj) {
                        $j = 1;
                        foreach($LC_Obj as $rw) {
                            $APName = htmlspecialchars($AddressObj->getPlaceName($rw->AP_Id));
                            echo '<row id="'.$rw->LC_Id.'">
                                <userdata name="LC_Name">'.$rw->LC_Name.'</userdata>
                                <userdata name="OF_Id">'.$rw->OF_Id.'</userdata>    
                                <userdata name="LC_Id">'.$rw->LC_Id.'</userdata>
                                <userdata name="LC_Building">'.$rw->LC_Building.'</userdata> 
                                <userdata name="LC_Name">'.$rw->LC_Name.'</userdata>
                                <userdata name="SR_Name">'.$rw->SR_Name.'</userdata>
                                <userdata name="PL_Name">'.$rw->PL_Name.'</userdata>
                                <userdata name="ALC_Name">'.$rw->ALC_Name.'</userdata>
                                <userdata name="CT_Name">'.$rw->CT_Name.'</userdata>
                                <userdata name="ST_Name">'.$rw->ST_Name.'</userdata>
                                <userdata name="CN_Name">'.$rw->CN_Name.'</userdata>
                                <userdata name="LC_Phone">'.$rw->LC_Phone.'</userdata>
                                <userdata name="LC_Pincode">'.$rw->LC_Pincode.'</userdata>    
                                <userdata name="LC_Comments">'.$rw->LC_Comments.'</userdata>
                                <userdata name="LC_Status">'.$rw->LC_Status.'</userdata>
                                <cell>'.$j.'</cell>
                                <cell name="LC_Name">'.$rw->LC_Name.'</cell>';
                                if($rw->LC_Status == '0') { 
                                    $LLStatus="Suspended";
                                }elseif($rw->LC_Status == '1') {
                                     $LLStatus="Published";
                                }elseif($rw->LC_Status == '2') {
                                     $LLStatus="Permanently Closed";
                                }
                                
                                if($preTally_user_ofid == 1) { echo '<cell name="OF_Name">'.$rw->OF_Name.'</cell>'; }
                                echo '<cell>'.$LLStatus.'</cell>';
                                if($rw->LC_Status == '0') { 
                                       echo '<cell><![CDATA[<img src="images/icon/warn_16.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Suspended\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                } else if($rw->LC_Status == '1') { 
                                        echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Approved\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                }else{
                                     echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Permanently Closed\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                }
                            echo '</row>';
                            $j++;
                        }
                    }
		  
echo '</rows>';
?>