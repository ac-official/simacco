<?php
require_once('preTallyClass/ReportClass.php');
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
//var_dump($REQUEST);die();
if($_REQUEST["type"]&&$_REQUEST["filter"]){
            $filtr_val=$_REQUEST['filtrVal'];    
            $type   =$_REQUEST["type"];
            $filter =$_REQUEST["filter"];
            $sDate  =$_REQUEST["stDate"];
            $eDate  =$_REQUEST["enDate"];
}else{
$type=1;
$filter=1;    
$filtr_val="";
$sDate  =date('Y') . '-01-01';
$eDate  =date('Y') . '-12-31';
}
$type_arr=array(1=>"branch",2=>"user",3=>"subhead",4=>"item");
$MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate ,'OFId' => $preTally_user_ofid, 'LCId' => $LCId ,'FilterBy'=>$filter ,'FilterVal'=>$filtr_val, 'Request' => $type_arr[$type]);
echo showHeader($type,$filter);            
echo showData($MISArgArray);

function showHeader($type,$filter){
    if($type==1 ){
    //Branchwise report of company
    return '<rows parent="0">
	<head>
            <afterInit>
                <call command="attachHeader">
                    <param>#rspan,#rspan,Income,Expense,Gross Profit/Loss,Income,Expense,Income,Expense,Income,Expense</param>
                </call>
            </afterInit>
            <column width="50" type="ro" align="right"  sort="int">Slno</column>
            <column width="110" type="ro" align="left"  sort="str">Branches</column>
            <column width="110" type="ro" align="right" sort="int">Business Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="125" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Other Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Branch Fund Transfers</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Total</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>

            <settings>
                <colwidth>px</colwidth>
            </settings>
	</head>
            ';
    }else if($type==2){
     //Userwise report of company   
     return '<rows parent="0">
	<head>
            <afterInit>
                <call command="attachHeader">
                    <param>#rspan,#rspan,Income,Expense,Gross Profit/Loss,Income,Expense,Income,Expense,Income,Expense</param>
                </call>
            </afterInit>
            <column width="50" type="ro" align="right"  sort="int">Slno</column>
            <column width="110" type="ro" align="left"  sort="str">User</column>
            <column width="110" type="ro" align="right" sort="int">Business Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="125" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Other Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Branch Fund Transfers</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Total</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>

            <settings>
                <colwidth>px</colwidth>
            </settings>
	</head>';   
    }
    else if($type==3){
        //Subheadwise report of company
     return '<rows parent="0">
	<head>
            <afterInit>
                <call command="attachHeader">
                    <param>#rspan,#rspan,Income,Expense,Gross Profit/Loss,Income,Expense,Income,Expense,Income,Expense</param>
                </call>
            </afterInit>
            <column width="50" type="ro" align="right"  sort="int">Slno</column>
            <column width="110" type="ro" align="left"  sort="str">Subhead</column>
            <column width="110" type="ro" align="right" sort="int">Business Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="125" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Other Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Branch Fund Transfers</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Total</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>

            <settings>
                <colwidth>px</colwidth>
            </settings>
	</head>
            ';   
    }
    else if($type==4){
        //Itemwise report of company
     return '<rows parent="0">
            <head>
            <afterInit>
                <call command="attachHeader">
                    <param>#rspan,#rspan,Income,Expense,Gross Profit/Loss,Income,Expense,Income,Expense,Income,Expense</param>
                </call>
            </afterInit>
            <column width="50" type="ro" align="right"  sort="int">Slno</column>
            <column width="110" type="ro" align="left"  sort="str">Item</column>
            <column width="110" type="ro" align="right" sort="int">Business Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="125" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Other Income And Expenses</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Branch Fund Transfers</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <column width="110" type="ro" align="right" sort="int">Total</column>
            <column width="110" type="ro" align="right" sort="int">#cspan</column>
            <settings>
                <colwidth>px</colwidth>
            </settings>
	</head>';   
    }
}
function showData($MISArgArray)
{   
    global $currency, $nullValue;
    $nullValue="--";
    $ReportObj = new ReportClass();
  echo  $ReportObj->viewMISGridData($MISArgArray);
    $RptData = $ReportObj->ReportArray;   
    $i=1;
    foreach ($RptData as $key => $value){
        
        $BusinessPL  = $value['BusInc'] - $value['BusExp'];
        $TotalInc    = $value['BusInc'] + $value['OtrInc'] + $value['CshTransRecv'];
        $TotalExp    = $value['BusExp'] + $value['OtrExp'] + $value['CshTransPaid'];
        
        $value['BusInc']      = $value['BusInc'] !='' ? $value['BusInc'].$currency  : $nullValue ;
        $value['BusExp']      = $value['BusExp'] !='' ? $value['BusExp'].$currency  : $nullValue ;
        $value['OtrInc']      = $value['OtrInc'] !='' ? $value['OtrInc'].$currency  : $nullValue ;
        $value['OtrExp']      = $value['OtrExp'] !='' ? $value['OtrExp'].$currency  : $nullValue ;
        $value['CshTransRecv']  = $value['CshTransRecv'] !='' ? $value['CshTransRecv'].$currency  : $nullValue ;
        $value['CshTransPaid']  = $value['CshTransPaid'] !='' ? $value['CshTransPaid'].$currency  : $nullValue ;
        $BusinessPL = $BusinessPL !='' ? $BusinessPL.$currency  : $nullValue ;
        $TotalInc   = $TotalInc   !='' ? $TotalInc.$currency    : $nullValue ;
        $TotalExp   = $TotalExp   !='' ? $TotalExp.$currency    : $nullValue ;
    
        $result .= '<row id="'.$value['Id'].'">
                        <cell>'.$i.'</cell>
                        <cell>'.$value['Name'].'</cell>
                        <cell>'.$value['BusInc'].'</cell>
                        <cell>'.$value['BusExp'].'</cell>
                        <cell>'.$BusinessPL.'</cell>
                        <cell>'.$value['OtrInc'].'</cell>
                        <cell>'.$value['OtrExp'].'</cell>
                        <cell>'.$value['CshTransRecv'].'</cell>
                        <cell>'.$value['CshTransPaid'].'</cell>
                        <cell>'.$TotalInc.'</cell>
                        <cell>'.$TotalExp.'</cell>
                    </row>';    
        $i++;
    }
    return $result."</rows>";
}
?>

