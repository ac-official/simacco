<?php 

require_once($BASEPATH . "preTallyClass/LateEntryClass.php");
$lvObj = new LateEntryClass();
$fromDate = "";
$toDate = "";
$filterCondition = "";

if (!empty($_REQUEST['appFromDate']) && !empty($_REQUEST['appToDate'])) {
    $fromDate = date("Y-m-d", strtotime($_REQUEST['appFromDate']));
    $toDate = date("Y-m-d", strtotime($_REQUEST['appToDate']));
    $filterCondition = " AND DATE(aa.Att_Lt_Created_At) BETWEEN '$fromDate' AND '$toDate' ";
} elseif (!empty($_REQUEST['appFromDate'])) {
    $fromDate = date("Y-m-d", strtotime($_REQUEST['appFromDate']));
    $filterCondition = " AND DATE(aa.Att_Lt_Created_At) = '$fromDate' ";
} elseif (!empty($_REQUEST['appToDate'])) {
    $toDate = date("Y-m-d", strtotime($_REQUEST['appToDate']));
    $filterCondition = " AND DATE(aa.Att_Lt_Created_At) = '$toDate' ";
} else {
    // If no date is selected, get records of the current month
    $firstDayOfMonth = date('Y-m-01');
    $lastDayOfMonth = date('Y-m-t');
    $filterCondition = " AND DATE(aa.Att_Lt_Created_At) BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth' ";
}

$userId = $preTally_user_id;
$lvObj->listManageLateEntries($userId, $filterCondition);

$lv_Obj = $lvObj->listLateEntryArray;
// echo "<pre>";print_r($lvObj->listLateEntryArray);die;
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
        header("Content-type: text/xml");
}
        echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
            echo '<rows>		
		<head>
            <column width="40" type="ro" align="center" >SlNo</column>	 			 	 	
            <column width="90" type="ro" align="left" >From Date</column>
            <column width="90" type="ro" align="left" >To Date</column>
            <column width="60" type="ro" align="left" >Total Days</column>
            <column width="60" type="ro" align="left" >Late Session</column>
            <column width="*" type="ro" align="left" >Late Duration</column>
            <column width="*" type="ro" align="left" >Status</column>
            <column width="110" type="ro" align="left" >Applied On</column>
            <column width="*" type="ro" align="left" >Reason</column>
            <column width="80" type="ro" align="left" >Verified By</column>
            <column width="110" type="ro" align="left" >Verified At</column>
            <column width="*" type="ro" align="left" >Remarks</column>
            <column width="70" type="ro" align="center" sort="na">Edit</column>
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
        $j=1;
        if($lv_Obj) {
			foreach ($lv_Obj as $rw) {
                $lateSession = ($rw->Att_Lt_Type == "p1") ? 'Morning' : 'Evening';
                $statusText = ucfirst($rw->Att_Lt_Status);
                if($rw->Att_Lt_Status=="pending"){
                    $statusText = "Pending Approval";
                }
                // $statusText = ucfirst($rw->Att_Lt_Status);
                $verifiedOn = ($rw->Att_Lt_Verified_At)?date('d/m/y h:i a', strtotime($rw->Att_Lt_Verified_At)):'';
                $remarks = $verifiedRemarks = '';

                $encodedReason = htmlspecialchars(json_encode($rw->Att_Lt_Reason), ENT_QUOTES);

                $encodedVerifiedReason = htmlspecialchars(json_encode($rw->Att_Lt_Verified_Remark), ENT_QUOTES);
                $timeTaken = explode(':', $rw->Att_Lt_Duration);
                $Att_Lt_Duration = $rw->Att_Lt_Duration.' Min';
                if($timeTaken[0]=="00"){
                    $Att_Lt_Duration = $timeTaken[1].' Min';
                }else{
                    $Att_Lt_Duration = $timeTaken[0].'Hr '.$timeTaken[1].' Min';
                }
                if (strlen($rw->Att_Lt_Reason) > 30) {
                    $remarks = '<cell name="AT_Min" style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[
                        <div style="width:85%; overflow:hidden;text-overflow: ellipsis;">
                            ' . htmlspecialchars_decode($rw->Att_Lt_Reason) . '
                        </div>
                        <div style="margin-right: 5%;">
                            <img 
                                src="images/icon/info_18.png" 
                                id="tp' . $rw->Att_Lt_Id . '-1" 
                                onmouseover="preTally.Settings.showLabel(this, ' . $encodedReason . ', \'custom\');" 
                                onmouseout="preTally.Settings.hideLabel(this);" 
                            />
                        </div>
                    ]]></cell>';
                } else {
                    $remarks = '<cell name="AT_Min"><![CDATA[
                        ' . htmlspecialchars_decode($rw->Att_Lt_Reason) . '
                    ]]></cell>';
                }

                if (strlen($rw->Att_Lt_Verified_Remark) > 30) {
                    $verifiedRemarks = '<cell name="AT_Min" style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[
                        <div style="width:85%; overflow:hidden;text-overflow: ellipsis;">
                            ' . htmlspecialchars_decode($rw->Att_Lt_Verified_Remark) . '
                        </div>
                        <div style="margin-right: 5%;">
                            <img 
                                src="images/icon/info_18.png" 
                                id="tp' . $rw->Att_Lt_Id . '-1" 
                                onmouseover="preTally.Settings.showLabel(this, ' . $encodedVerifiedReason . ', \'custom\');" 
                                onmouseout="preTally.Settings.hideLabel(this);" 
                            />
                        </div>
                    ]]></cell>';
                } else {
                    $verifiedRemarks = '<cell name="AT_Min"><![CDATA[
                        ' . htmlspecialchars_decode($rw->Att_Lt_Verified_Remark) . '
                    ]]></cell>';
                }
                if ($rw->Att_Lt_Status=="pending") {
                    $editBtn = '<cell name="AT_Min" style="display: flex;align-items: center;"><![CDATA[
                        <img src="images/icon/edit_icon.gif" style="cursor:pointer" id="tp-edit-' . $rw->Att_Lt_Id . '-1" onclick=\'preTally.UserProfile.editLateEntry(this, ' . json_encode($rw) . ');\'/>
                    ]]></cell>';
                } else {
                    $editBtn = '<cell name="AT_Min" style="display: flex;align-items: center;"><![CDATA[
                        <img src="images/icon/cross.png" id="tp-edit-' . $rw->Att_Lt_Id . '-2"/>
                    ]]></cell>';
                }
                echo '<row id="' . $rw->Att_Lt_Id . '">
                    <cell name="id">' . $j . '</cell>
                    <cell name="Att_Lt_From_Date">' . DateTime::createFromFormat('Y-m-d', $rw->Att_Lt_From_Date)->format('d/m/Y') . '</cell>
                    <cell name="Att_Lt_To_Date">' . DateTime::createFromFormat('Y-m-d', $rw->Att_Lt_To_Date)->format('d/m/Y') . '</cell>
                    <cell name="Att_Lt_NumOFDays">' . $rw->Att_Lt_NumOFDays . '</cell>
                    <cell name="Att_Lt_Type">' . $lateSession . '</cell>
                    <cell name="Att_Lt_Duration">' . $Att_Lt_Duration . '</cell>
                    <cell name="Att_Lt_US_Id">' . $statusText . '</cell>
                    <cell name="Att_Lt_Created_At">' . date('d/m/y h:i a', strtotime($rw->Att_Lt_Created_At)) . '</cell>
                    ' . $remarks . '
                    <cell name="Att_Lt_Verified_By">' . $rw->verifiedBy_first . ' ' . $rw->verifiedBy_last . '</cell>
                    <cell name="Att_Lt_Verified_At">' . $verifiedOn . '</cell>
                    ' . $verifiedRemarks . '';

                if ($rw->Att_Lt_Status == "pending") {
                    echo $editBtn;
                } else {
                    echo '<cell type="ro" title=" "></cell>';
                }

                echo '</row>';
                $j++;
            }
		}else {
            echo '<row id="0"> 
            <cell colspan="11"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            </row>';
        }
        echo '</rows>';
?>