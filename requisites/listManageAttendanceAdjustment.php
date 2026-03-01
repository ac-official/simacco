<?php
include_once($BASEPATH."preTallyClass/AttendanceAdjustmentClass.php");

// Send headers
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

$filter_key = '';

// Default to current month
$startStr = date('Y-m-01');
$endStr = date('Y-m-d');

// If filters are set
if (isset($_REQUEST['rpt_month_filter']) || isset($_REQUEST['rpt_year_filter'])) {
    $year = (isset($_REQUEST['rpt_year_filter']) && $_REQUEST['rpt_year_filter'] != "Select Year")
        ? $_REQUEST['rpt_year_filter']
        : date('Y');

    if (isset($_REQUEST['rpt_month_filter']) && $_REQUEST['rpt_month_filter'] != "Select Month") {
        $monthName = $_REQUEST['rpt_month_filter'];
        $monthNumber = date('m', strtotime("1 $monthName"));

        $startOfMonth = new DateTime("$year-$monthNumber-01");
        $endOfMonth = clone $startOfMonth;
        $endOfMonth->modify('last day of this month');

        $startStr = $startOfMonth->format('Y-m-d');
        $endStr = $endOfMonth->format('Y-m-d');
    } else {
        if (isset($_REQUEST['rpt_year_filter']) && $_REQUEST['rpt_year_filter'] != "Select Year") {
            // Only year passed — use full year
            $startStr = "$year-01-01";
            $endStr = "$year-12-31";
        }
    }
}
// echo $startStr."=>".$endStr;die;
// Final filter condition
/*$filter_key .= " AND (
    attendance_adjustment.Att_Adj_Date BETWEEN '$startStr' AND '$endStr' 
    OR attendance_adjustment.Att_Adj_Created_At BETWEEN '$startStr 00:00:00' AND '$endStr 23:59:59'
)";*/
$filter_key .= " AND aa.Att_Adj_Created_At BETWEEN '$startStr 00:00:00' AND '$endStr 23:59:59'";

$sortFlter  =   " aa.Att_Adj_Created_At DESC ";
if(isset($REQUEST['filter']) && !empty($REQUEST['filter'])){
    $filterData = explode(",",$REQUEST['filter']);
    if($filterData[1] == 2){
        $sortFlter  =   " ua.US_FName DESC ";
        if($filterData[0] == 0) {
            $sortFlter  =   " ua.US_FName ASC ";
        }
    } 
    if($filterData[1] == 3){
        $sortFlter  =   " l.LC_Name DESC ";
        if($filterData[0] == 0) {
            $sortFlter  =   " l.LC_Name ASC ";
        }
    }  
    if($filterData[1] == 4){
        $sortFlter  =   " aa.Att_Adj_Date DESC ";
        if($filterData[0] == 0) {
            $sortFlter  =   " aa.Att_Adj_Date ASC ";
        }
    } 
    if($filterData[1] == 7){
        $sortFlter  =   " aa.Att_Adj_Created_At DESC ";
        if($filterData[0] == 0) {
            $sortFlter  =   " aa.Att_Adj_Created_At ASC ";
        }
    } 
} 
$rlObj = new AttendanceAdjustmentClass();
$rlObj->listManageAdjustments($sortFlter, $filter_key);
$TL_Count=count($rlObj->listManageAdjustmentArray);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
?>
<rows>
    <head>
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
    </head>
    <userdata name="TL_Count"><?=$TL_Count?></userdata>
<?php
$j=1;
if ($rlObj->listManageAdjustmentArray) {
    foreach ($rlObj->listManageAdjustmentArray as $rw) {?>
    <?php
        // $remarkText = preg_replace('/[^\x20-\x7E]/', '', htmlspecialchars_decode($rw->Att_Adj_Remarks));
        // // Step 2: Preserve matched quotes, temporarily replace them with placeholders
        // $remarkText = preg_replace_callback('/(["\'])(.*?)\1/', function ($m) {
        //     return '[__QUOTE__]' . $m[2] . '[__QUOTE__]'; // protect matched pairs
        // }, $remarkText);
        // // Step 3: Remove all remaining unmatched quotes
        // $remarkText = str_replace(['"', "'"], '', $remarkText);
        // // Step 4: Restore matched quote placeholders
        // $remarkText = str_replace('[__QUOTE__]', '"', $remarkText);
        // $remarkText = preg_replace('/[^a-zA-Z0-9 "\'\/\.\:\-]/', '', $remarkText);
        // // Step 5: Clean up spacing
        // $remarkText = preg_replace('/\s+/', ' ', $remarkText);
        // $remarkText = trim($remarkText);
        $remarkText = htmlspecialchars(html_entity_decode($rw->Att_Adj_Remarks), ENT_QUOTES);
        $createdAt = strtotime($rw->Att_Adj_Created_At); // timestamp of created time
        $now = time(); // current timestamp
        $diffInMinutes = ($now - $createdAt) / 60;
    ?>
    <row id="<?php echo $rw->Att_Adj_Id; ?>">    
        <userdata name="Att_Adj_Id"><?php echo $rw->Att_Adj_Id; ?></userdata>
        <userdata name="Att_Adj_Type"><?php echo $rw->Att_Adj_Type; ?></userdata>
        <userdata name="Att_Adj_US_Id"><?php echo $rw->Att_Adj_US_Id; ?></userdata>
        <userdata name="Att_Adj_Branch_Id"><?php echo $rw->Att_Adj_Branch_Id; ?></userdata>
        <userdata name="Att_Adj_Date"><?php echo DateTime::createFromFormat('Y-m-d', $rw->Att_Adj_Date)->format('d/m/Y'); ?></userdata>
        <userdata name="Att_Adj_Remarks"><?php echo $remarkText; ?></userdata>
        <cell name="No">
            <?php echo $j; ?>
        </cell>
        <cell name="Att_Adj_US_Id">
            <?php echo $rw->US_FName.' '.$rw->US_LName; ?>
        </cell> 
        <?php if(strlen($rw->LC_Name) > 20): ?>
            <cell name="Att_Adj_Branch_Id" style="display: flex;align-items: center;"><![CDATA[
                <?= htmlspecialchars(substr($rw->LC_Name, 0, 20), ENT_QUOTES, 'UTF-8') . '...' ?>
                <img 
                    src="images/icon/info_18.png" 
                    id="tp<?= $rw->Att_Adj_Id ?>-1" 
                    onmouseover="preTally.Settings.showLabel(this, '<?= htmlspecialchars($rw->LC_Name, ENT_QUOTES, 'UTF-8') ?>', "custom");" 
                    onmouseout="preTally.Settings.hideLabel(this);" 
                />
            ]]>
            </cell>
        <?php else: ?>
            <cell name="Att_Adj_Branch_Id"><![CDATA[
                <?= htmlspecialchars($rw->LC_Name, ENT_QUOTES, 'UTF-8') ?>
                <userdata name="Att_Adj_Branch_Id"><?php echo $rw->Att_Adj_Branch_Id; ?></userdata>
            ]]></cell>
        <?php endif; ?>      
        <cell name="Att_Adj_Date">
            <?php echo DateTime::createFromFormat('Y-m-d', $rw->Att_Adj_Date)->format('d/m/Y'); ?>
        </cell>
        <cell name="Att_Adj_Type">
            <?php
                if($rw->Att_Adj_Type=="p"){
                    echo "Full Day";
                }elseif($rw->Att_Adj_Type=="p1"){
                    echo "Morning Half Day";
                }else{
                    echo "Afternoon Half Day";
                }
            ?>
        </cell>
        <?php if(strlen($remarkText) > 50): ?>
            <cell name="AT_Min" style="display: flex;align-items: center;justify-content: space-between;"><![CDATA[
                <div style="width:85%; overflow:hidden;text-overflow: ellipsis;">
                    <?= $remarkText ?>
                </div>
                <div style="margin-right: 5%;">
                    <img 
                        src='images/icon/info_18.png' 
                        id='tp<?= $rw->Att_Adj_Id ?>-1' 
                        onmouseover='preTally.Settings.showLabel(this, <?= json_encode($remarkText) ?>, "custom");' 
                        onmouseout='preTally.Settings.hideLabel(this);' 
                    />
                </div>
            ]]>
            </cell>
        <?php else: ?>
            <cell name="AT_Min"><![CDATA[
                <?= $remarkText ?>
            ]]></cell>
        <?php endif; ?>
        <cell name="Att_Adj_Created_At">
            <?php echo DateTime::createFromFormat('Y-m-d H:i:s', $rw->Att_Adj_Created_At)->format('d/m/Y'); ?>
        </cell>
        <cell name="Att_Adj_Created_By">
            <?php echo $rw->createdBy_first.' '.$rw->createdBy_last; ?>
        </cell>
        <cell name="AT_Min" style="display: flex;align-items: center;"><![CDATA[
            <?php if ($diffInMinutes <= 30): ?>
                <img src='images/icon/edit_icon.gif' style='cursor:pointer' id='tp-edit-<?= $rw->Att_Adj_Id ?>-1' onclick="preTally.Settings.editAdjAttendance(this, '<?php echo $rw->Att_Adj_Id; ?>');"/>
            <?php else: ?>
                <img src='images/icon/cross.png' id='tp-edit-<?= $rw->Att_Adj_Id ?>-2'/>
            <?php endif; ?>
            ]]>
        </cell>
    </row>
<?php
    $j++;
    }
} else { ?>
    <row id="0">
        <cell colspan="8"><![CDATA[
            <div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;">
            No records found
            </div>
        ]]></cell>
    </row>
<?php
}
?>
</rows>