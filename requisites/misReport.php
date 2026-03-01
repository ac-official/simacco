<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/ReportClass.php");

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$ReportObj = new ReportClass();
$MISArgArray = array('OFId' => $preTally_user_ofid );
$ReportObj->viewMISReportData($MISArgArray);
$RP_Obj = $ReportObj->ReportArray;

$BusinessPL  = $RP_Obj['BusInc'] - $RP_Obj['BusExp'];
$TotalInc    = $RP_Obj['BusInc'] + $RP_Obj['OtrInc'] + $RP_Obj['CshTransRecv'];
$TotalExp    = $RP_Obj['BusExp'] + $RP_Obj['OtrExp'] + $RP_Obj['CshTransPaid'];

$allyear        = array('parent' => 'allYear', 'request' => 'allYear');
//$allitems       = array('parent' => 'allitems');
//$allbranches    = array('parent' => 'allbranches');
//$allusers       = array('parent' => 'allusers');
       
echo '<rows parent="0">
	<head>
            <beforeInit>
                <call command="attachHeader">
                    <param>#rspan,Income,Expense,Gross Profit/Loss,Income,Expense,Income,Expense,Income,Expense</param>
                </call>
            </beforeInit>
            <column width="*" type="tree" align="left"  sort="na">MIS Report Nodes</column>
            <column width="110" type="ro" align="right" sort="na">Business Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="na">#cspan</column>
            <column width="125" type="ro" align="right" sort="na">#cspan</column>
            <column width="110" type="ro" align="right" sort="na">Other Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="na">#cspan</column>
            <column width="110" type="ro" align="right" sort="na">Branch Fund Transfers</column>
            <column width="110" type="ro" align="right" sort="na">#cspan</column>
            <column width="110" type="ro" align="right" sort="na">Total</column>
            <column width="110" type="ro" align="right" sort="na">#cspan</column>

            <settings>
                <colwidth>px</colwidth>
            </settings>
	</head>
	<row id="main_parent" open = "1"  xmlkids="0">
            <userdata name="nodeName">'.$preTally_user_ofname.'</userdata> 
            <userdata name="nodeImage">images/icon/company.png</userdata> 
            <cell><![CDATA[<b style="color:#FC7B16;">'.$preTally_user_ofname.'</b>]]></cell>
            <cell>'.$RP_Obj['BusInc'].$currency.'</cell>
            <cell>'.$RP_Obj['BusExp'].$currency.'</cell>
            <cell>'.$BusinessPL.$currency.'</cell>
            <cell>'.$RP_Obj['OtrInc'].$currency.'</cell>
            <cell>'.$RP_Obj['OtrExp'].$currency.'</cell>
            <cell>'.$RP_Obj['CshTransRecv'].$currency.'</cell>
            <cell>'.$RP_Obj['CshTransPaid'].$currency.'</cell>
            <cell>'.$TotalInc.$currency.'</cell>
            <cell>'.$TotalExp.$currency.'</cell>
            
	</row>
</rows>';

/*
            <row id=\''.json_encode($allyear).'\' xmlkids="1">
                <cell >All Year Report</cell>
            </row>
            <row id=\''.json_encode($allitems).'\' xmlkids="1">
                <cell >All Items Report</cell>
            </row>
            <row id=\''.json_encode($allbranches).'\' xmlkids="1">
                <cell >All Branches Report</cell>
            </row>
            <row id=\''.json_encode($allusers).'\' xmlkids="1">
                <cell >All Users Report</cell>
            </row>
 */
?>