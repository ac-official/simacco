<?php
include_once($BASEPATH."preTallyClass/RuleClass.php");

// Safely fetch parameter
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

$rlObj = new RuleClass();
$rlObj->listManageRule($type);

// Send headers
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

// Start XML
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
?>
<rows>
    <head>
        <column width="50" type="ro" align="left">Sl No</column>                    
        <column width="*" type="ro" align="left">Rule Name</column>
        <column width="*" type="ro" align="left">Description</column>
        <?php if($type == 0){ ?>
            <column width="70" type="ro" align="left">Start Day</column>
            <column width="70" type="ro" align="left">End Day</column>
            <column width="120" type="ro" align="left">Leave Duration</column>
        <?php }else{ ?>
            <column width="*" type="ro" align="left">Leave Type</column>
        <?php }?>
        <column width="*" type="ro" align="left">Effective Date</column>
        <column width="*" type="ro" align="left">Is LOP</column>
        <column width="50" type="co" align="center">Status</column>
    <column width="50" type="co" align="center">Edit</column>
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
<?php
$j=1;
if ($rlObj->listManageRuleTypeArray) {
    foreach ($rlObj->listManageRuleTypeArray as $rw) {
        $RLStatus = ($rw->RL_Status == 1) ? 'Approved' : 'Blocked';
        $RLIsLop = ($rw->RL_Is_LOP == 1) ? 'Yes' : 'No';
?>
    <row id="<?php echo $rw->RL_Id; ?>">    
        <userdata name="RL_Id"><?php echo $rw->RL_Id; ?></userdata>
        <userdata name="RL_Name"><?php echo $rw->RL_Name; ?></userdata>
        <userdata name="RL_Description"><?php echo $rw->RL_Description; ?></userdata>
        <?php if($type == 0){ ?>
            <userdata name="RL_Start_Day"><?php echo $rw->RL_Start_Day; ?></userdata>
            <userdata name="RL_End_Day"><?php echo $rw->RL_End_Day; ?></userdata>
            <userdata name="RL_Leave_Duration"><?php echo $rw->RL_Leave_Duration; ?></userdata>
        <?php }else{ ?>
            <userdata name="RL_Sandwich_Type"><?php echo $rw->RL_Sandwich_Type; ?></userdata>
        <?php }?>
        <userdata name="RL_Is_LOP"><?php echo $rw->RL_Is_LOP; ?></userdata>
        <userdata name="RL_Status"><?php echo $rw->RL_Status; ?></userdata>
        <userdata name="RL_Except_Dept_office"><?php echo $rw->RL_Except_Dept_office; ?></userdata>

        <cell name="No"><?php echo $j; ?></cell>
        <cell name="RL_Name"><?php echo $rw->RL_Name; ?></cell>
        <cell name="RL_Description"><?php echo $rw->RL_Description; ?></cell>
        <?php if($type == 0){ ?>
            <cell name="RL_Start_Day"><?php echo $rlObj->daysArrayFun($rw->RL_Start_Day); ?></cell>
            <cell name="RL_End_Day"><?php echo $rlObj->daysArrayFun($rw->RL_End_Day); ?></cell>
            <cell name="RL_Leave_Duration">
                <?php
                    if($rw->RL_Leave_Duration=="single"){
                        echo "Any Day";
                    }else{
                        echo $rlObj->daysArrayFun($rw->RL_Start_Day). " to ". $rlObj->daysArrayFun($rw->RL_End_Day);
                    }
                ?>
            </cell>
        <?php }else{ ?>
            <cell name="RL_Sandwich_Type"><?php echo ucfirst($rw->RL_Sandwich_Type); ?></cell>
        <?php }?>
        <cell name="RL_Leave_Duration">
            <?php 
                echo DateTime::createFromFormat('Y-m-d', $rw->RL_Effective_Date)->format('d/m/Y');
            ?>
        </cell>
        <cell name="RL_Is_LOP"><?php echo $RLIsLop; ?></cell>
        <?php if($rw->RL_Status == 1){ ?>
            <cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,'<?php echo $RLStatus; ?>');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>
        <?php } else { ?>
            <cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,'<?php echo $RLStatus; ?>');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>
        <?php } ?>
        <cell><![CDATA[
            <img src="images/icon/edit_icon.gif" 
                 style="margin:2px 0; cursor:pointer;" 
                 onclick="preTally.Settings.editRule(this, '<?php echo $rw->RL_Id; ?>', '<?php echo $type; ?>');"
            />
        ]]></cell>
    </row>
<?php
    $j++;
    }
} else {
?>
    <row id="0">
        <cell colspan="4">
            <![CDATA[
            <div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;">
            No records found
            </div>]]>
        </cell>
    </row>
<?php
}
?>
</rows>