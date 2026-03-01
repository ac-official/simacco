<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/LateEntryClass.php");
$lvObj = new LateEntryClass();
$orderBy ='ORDER BY aa.Att_Lt_Created_At DESC';    
if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;
if ($_REQUEST['filter']) {
    $filterData = explode(",", $_REQUEST['filter']);
    $filter = '';
    $orderBy = 'Att_Lt_Created_At DESC'; // Default Order By

    // Applied By
    if (!empty($filterData[0])) {
        $filter .= " AND CONCAT(ua.US_FName, ' ', ua.US_LName) LIKE '" . mysqli_real_escape_string($GLOBALS['con'], $filterData[0]) . "%'";
    }

    // Branch
    if (!empty($filterData[1])) {
        $filter .= ' AND l.LC_Name LIKE "' . mysqli_real_escape_string($GLOBALS['con'], $filterData[1]) . '%"';
    }

    // Department
    if (!empty($filterData[2])) {
        $filter .= ' AND d.DP_Name = "' . mysqli_real_escape_string($GLOBALS['con'], $filterData[2]) . '"';
    }

    // Status
    if (!empty($filterData[3])) {
        $filter .= ' AND aa.Att_Lt_Status = "' . mysqli_real_escape_string($GLOBALS['con'], $filterData[3]) . '"';
    }

    // Sort Order
    if (!empty($filterData[5])) {
        if ($filterData[5] == 'asc') {
            $orderBy = 'Att_Lt_Created_At ASC';
        } else {
            $orderBy = 'Att_Lt_Created_At DESC';
        }
    }

    if($REQUEST['appFromDate'] && $_REQUEST['appToDate']){
        $startDate = DateTime::createFromFormat('d.m.Y', $_REQUEST['appFromDate'])->format('Y-m-d');
        $endDate = DateTime::createFromFormat('d.m.Y', $_REQUEST['appToDate'])->format('Y-m-d');
        $filter .= " AND Att_Lt_Created_At BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }

    // Call the function with filters
    $lvObj->listManageAllLateEntries($_GET["posStart"],$_GET["count"], $filter, $orderBy);
} else {
    // Call the function without filters
    $lvObj->listManageAllLateEntries($_GET["posStart"],$_GET["count"]);
}
$lv_Obj = $lvObj->listAllLateEntryArray;
$count  = $lvObj->lateEntryCount;
// echo $count;die;
// echo "<pre>";print_r($lvObj->listAllLateEntryArray);die;
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); 
} else {
    header("Content-type: text/xml");
}
        echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
            echo '<rows total_count="'.$count.'" pos="'.$_GET["posStart"].'">
            <userdata name="TL_Count">'.$count.'</userdata>
            <userdata name="LateEntryApprovePermission">'.$UserACLObj->approve_late_entry.'</userdata>';

        $j=1;
        if($lv_Obj) {
            foreach ($lv_Obj as $rw) {
                $lateSession = ($rw->Att_Lt_Type == "p1") ? 'Morning' : 'Evening';
                $statusText = ucfirst($rw->Att_Lt_Status);
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
                $Att_Lt_Status = ucfirst($rw->Att_Lt_Status);
                if($rw->Att_Lt_Status=="pending"){
                    $Att_Lt_Status = "Pending Approval";
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
                                onmouseover="preTally.Settings.showLabel(this, ' . $encodedReason . ', 
                                , \'custom\');" 
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
                        <img src="images/icon/warn_16.png" style="cursor:pointer" id="tp-edit-' . $rw->Att_Lt_Id . '-1" onclick=\'preTally.Settings.verifyLateEntry(this, ' . json_encode($rw) . ');\'/>
                    ]]></cell>';
                } elseif ($rw->Att_Lt_Status=="rejected") {
                    $editBtn = '<cell name="AT_Min" style="display: flex;align-items: center;"><![CDATA[
                        <img src="images/icon/cross.png" id="tp-edit-' . $rw->Att_Lt_Id . '-2"/>
                    ]]></cell>';
                } else{
                    $editBtn = '<cell name="AT_Min" style="display: flex;align-items: center;"><![CDATA[
                        <img src="images/icon/tick_16.png" id="tp-edit-' . $rw->Att_Lt_Id . '-2"/>
                    ]]></cell>';
                }
                echo '<row id="' . $rw->Att_Lt_Id . '">
                    <cell name="id">' . $j . '</cell>
                    <cell name="LC_Name">' . $rw->US_FName.' '.$rw->US_LName. '</cell>
                    <cell name="LC_Name">' . $rw->LC_Name . '</cell>
                    <cell name="Att_DP_Name">' . $rw->DP_Name . '</cell>
                    <cell name="Att_Lt_From_Date">' . DateTime::createFromFormat('Y-m-d', $rw->Att_Lt_From_Date)->format('d/m/Y') . '</cell>
                    <cell name="Att_Lt_To_Date">' . DateTime::createFromFormat('Y-m-d', $rw->Att_Lt_To_Date)->format('d/m/Y') . '</cell>
                    <cell name="Att_Lt_NumOFDays">' . $rw->Att_Lt_NumOFDays . '</cell>
                    <cell name="Att_Lt_Type">' . $lateSession . '</cell>
                    <cell name="Att_Lt_Duration">' . $Att_Lt_Duration . '</cell>
                    <cell name="Att_Lt_Created_At">' . date('d/m/y h:i a', strtotime($rw->Att_Lt_Created_At)) . '</cell>
                    ' . $remarks . '
                    <cell name="Att_Lt_Verified_By">' . $rw->verifiedBy_first . ' ' . $rw->verifiedBy_last . '</cell>
                    <cell name="Att_Lt_Verified_At">' . $verifiedOn . '</cell>
                    ' . $verifiedRemarks . '
                    <cell name="Att_Lt_Status">' . $Att_Lt_Status . '</cell>';
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