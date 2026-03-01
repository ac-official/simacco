<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/ReportClass.php");

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$mainNode;
$fromYear   = 2014;
if(date('m') > 3) $toYear     = date('Y') + 1;
else $toYear = date('Y');

$OFId       = $preTally_user_ofid;

$nullValue = '-';

$reportItems = array(

                    'year'       => array('0','All Years Report', 'calender_1_24.png'),
                    'month'      => array('0','All Months Report', 'calendar_2_24.png'),  
                    //'date'       => 'All Dates Report',  
                    'item'       => array('0','All Items Report','item_20.png'),  
                    'branch'     => array('0','All Branches Report','branch.png'),  
                    'user'       => array('0','All Users Report','user.gif'),
                    'entry'      => array('0','All Entries')
                );

$reqID      = $_REQUEST['id'];
$IDArray    = json_decode($reqID, true);

echo '<rows parent=\''.$reqID.'\'>';
    
    $request = $IDArray['request'];
    
    if($IDArray['request'] == 'allYear') {
        $mainNode = 'year';
        $return = yearWise();
    }
    
    if($IDArray['request'] == 'allMonth') {
        $mainNode = 'month';
        $return = monthWise();
    }
    
    if($IDArray['request'] == 'allDate') {
        $mainNode = 'date';
        $return = dateWise($reqArray);
    }
    
    if($IDArray['request'] == 'allItem') {
        $return = itemWise($reqArray);
    }
    
    if($IDArray['request'] == 'allBranch') {
        $return = branchWise($reqArray);
    }
    
    if($IDArray['request'] == 'allUser') {
        $return = userWise($reqArray);
    }
    
    if($IDArray['request'] == 'allEntry') {
        $return = entryWise($reqArray);
    }
    //die($IDArray['parent'] .'--'. $IDArray['request']);
    if($request != 'allYear') {
        $return .= otherNodes();
    }
    
    echo $return;
echo '</rows>';

function yearWise() {
    $nodeID = makeID();
    global $fromYear, $toYear;
    $result = '';
    for($year = $toYear; $year > $fromYear; $year--) {
        
        $nodeID['year']    = array(array(($year-1), $year),($year-1).' - '.$year,  'calender_1_24.png');
        $nodeID['request'] = 'allMonth';
        $nodeID['seed']    = makeSeed();
        
        $MISData = viewReportData($nodeID);

        //$IDArray['request'] = array('allMonth','allItem');
        $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
                        <cell style="background-color:#A6DDF7;">'.($year-1).' - '.($year).' -> Month</cell>
                            '.$MISData.'
                    </row>';
    }
    return $result;
}
function monthWise() {
    global $currency, $nullValue;
    
    $nodeID = makeID();
    $thisYear       = $nodeID['year'][0];
    if($thisYear[0] == date("Y")){
        $beginDate      = new DateTime(date("Y").'-'.date("m").'-01');
        $endDate        = new DateTime($thisYear[0].'-04-01');
        $firstDate      = $beginDate;
        $lastDate       = new DateTime($thisYear[0].'-03-01');
    } else {
        $beginDate      = new DateTime($thisYear[1].'-03-01');
        $endDate        = new DateTime($thisYear[0].'-04-01');
        $firstDate      = new DateTime($thisYear[0].'-04-01');
        $lastDate       = new DateTime($thisYear[1].'-04-01');
    }
//        $endDate        = new DateTime($thisYear[0].'-04-01');
    
    $MISData = viewReportData($nodeID,$beginDate,$endDate);
    
    while ($firstDate <> $lastDate) {
        $nodeID['year']    = array($firstDate->format('Y'),$firstDate->format('Y'), 'calender_1_24.png');
        $nodeID['month']   = array($firstDate->format('m'),$firstDate->format('F'), 'calendar_2_24.png');
        $nodeID['request'] = 'allDate';
        $nodeID['seed']    = makeSeed();
        
        $month = $firstDate->format('m');
    
        $BusinessPL  = $MISData[$month]['BusInc'] - $MISData[$month]['BusExp'];
        $TotalInc    = $MISData[$month]['BusInc'] + $MISData[$month]['OtrInc'] + $MISData[$month]['CshTransRecv'];
        $TotalExp    = $MISData[$month]['BusExp'] + $MISData[$month]['OtrExp'] + $MISData[$month]['CshTransPaid'];
        
        $MISData[$month]['BusInc']      = $MISData[$month]['BusInc'] ? $currency.$MISData[$month]['BusInc']  : $nullValue ;
        $MISData[$month]['BusExp']      = $MISData[$month]['BusExp'] ? $currency.$MISData[$month]['BusExp']  : $nullValue ;
        $MISData[$month]['OtrInc']      = $MISData[$month]['OtrInc'] ? $currency.$MISData[$month]['OtrInc']  : $nullValue ;
        $MISData[$month]['OtrExp']      = $MISData[$month]['OtrExp'] ? $currency.$MISData[$month]['OtrExp']  : $nullValue ;
        $MISData[$month]['CshTransRecv']  = $MISData[$month]['CshTransRecv'] ? $currency.$MISData[$month]['CshTransRecv']  : $nullValue ;
        $MISData[$month]['CshTransPaid']  = $MISData[$month]['CshTransPaid'] ? $currency.$MISData[$month]['CshTransPaid']  : $nullValue ;
        $BusinessPL = $BusinessPL ? $currency.$BusinessPL  : $nullValue ;
        $TotalInc   = $TotalInc   ? $currency.$TotalInc    : $nullValue ;
        $TotalExp   = $TotalExp   ? $currency.$TotalExp    : $nullValue ;
    
        $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
                        <cell style="background-color:#B5E5BC;">'.$firstDate->format('F - Y').' -> Date</cell>
                        <cell>'.$MISData[$month]['BusInc'].'</cell>
                        <cell>'.$MISData[$month]['BusExp'].'</cell>
                        <cell>'.$BusinessPL.'</cell>
                        <cell>'.$MISData[$month]['OtrInc'].'</cell>
                        <cell>'.$MISData[$month]['OtrExp'].'</cell>
                        <cell>'.$MISData[$month]['CshTransRecv'].'</cell>
                        <cell>'.$MISData[$month]['CshTransPaid'].'</cell>
                        <cell>'.$TotalInc.'</cell>
                        <cell>'.$TotalExp.'</cell>
                    </row>';
        if($thisYear[0] == date("Y")) $firstDate->modify('first day of previous month');
        else $firstDate->modify('first day of next month');
    }
    return $result;
}
function dateWise () {
    global $currency, $nullValue;
    
    $nodeID = makeID();
    $thisYear   = $nodeID['year'][0];
    $thisMonth  = $nodeID['month'][0];

    $MISData = viewReportData($nodeID); 
    
    if($thisMonth == date("m") && $thisYear == date("Y"))
        $tempDay = date("d");
    else
        $tempDay = 1;

//    for($tempDay = 31; $tempDay >= 1; $tempDay --) {
      while($tempDay >= 1 || $tempDay <= 31){  
        
        $time = mktime(12, 0, 0, $thisMonth, $tempDay, $thisYear);          
        if (date('m', $time) == $thisMonth) {     
            $nodeID['date']    = array(date('d', $time),date('dS', $time),'books.png');
            $nodeID['request'] = 'allItem';
            $nodeID['seed']    = makeSeed();
            
            $day = $nodeID['date'][0];
            
            $BusinessPL  = $MISData[$day]['BusInc'] - $MISData[$day]['BusExp'];
            $TotalInc    = $MISData[$day]['BusInc'] + $MISData[$day]['OtrInc'] + $MISData[$day]['CshTransRecv'];
            $TotalExp    = $MISData[$day]['BusExp'] + $MISData[$day]['OtrExp'] + $MISData[$day]['CshTransPaid'];
        
            $MISData[$day]['BusInc']      = $MISData[$day]['BusInc'] ? $currency.$MISData[$day]['BusInc']  : $nullValue ;
            $MISData[$day]['BusExp']      = $MISData[$day]['BusExp'] ? $currency.$MISData[$day]['BusExp']  : $nullValue ;
            $MISData[$day]['OtrInc']      = $MISData[$day]['OtrInc'] ? $currency.$MISData[$day]['OtrInc']  : $nullValue ;
            $MISData[$day]['OtrExp']      = $MISData[$day]['OtrExp'] ? $currency.$MISData[$day]['OtrExp']  : $nullValue ;
            $MISData[$day]['CshTransRecv']  = $MISData[$day]['CshTransRecv'] ? $currency.$MISData[$day]['CshTransRecv']  : $nullValue ;
            $MISData[$day]['CshTransPaid']  = $MISData[$day]['CshTransPaid'] ? $currency.$MISData[$day]['CshTransPaid']  : $nullValue ;
            $BusinessPL = $BusinessPL ? $currency.$BusinessPL  : $nullValue ;
            $TotalInc   = $TotalInc   ? $currency.$TotalInc    : $nullValue ;
            $TotalExp   = $TotalExp   ? $currency.$TotalExp    : $nullValue ;
        
            $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
                            <cell style="background-color:#F5F5CC;">'.date("dS M Y (l)", $time).' -> Items</cell>
                            <cell>'.$MISData[$day]['BusInc'].'</cell>
                            <cell>'.$MISData[$day]['BusExp'].'</cell>
                            <cell>'.$BusinessPL.'</cell>
                            <cell>'.$MISData[$day]['OtrInc'].'</cell>
                            <cell>'.$MISData[$day]['OtrExp'].'</cell>
                            <cell>'.$MISData[$day]['CshTransRecv'].'</cell>
                            <cell>'.$MISData[$day]['CshTransPaid'].'</cell>
                            <cell>'.$TotalInc.'</cell>
                            <cell>'.$TotalExp.'</cell>
                        </row>';
            
            if($thisMonth == date("m") && $thisYear == date("Y"))
                $tempDay --;
            else
                $tempDay++;
    
            }else{
                break;
            }
    }
    return $result; 
}
function branchWise ($reqArray) {
    global $currency, $nullValue;

    $nodeID = makeID();
    $MISData = viewReportData($nodeID);
    
    foreach ($MISData as $key => $value) {
        
        $nodeID['branch']  = array($value['LCId'],$value['LCName'],'branch.png');
        $nodeID['request'] = 'allUser';
        $nodeID['seed']    = makeSeed();
        
        $BusinessPL  = $value['BusInc'] - $value['BusExp'];
        $TotalInc    = $value['BusInc'] + $value['OtrInc'] + $value['CshTransRecv'];
        $TotalExp    = $value['BusExp'] + $value['OtrExp'] + $value['CshTransPaid'];
        
        $value['BusInc']      = $value['BusInc'] ? $currency.$value['BusInc']  : $nullValue ;
        $value['BusExp']      = $value['BusExp'] ? $currency.$value['BusExp']  : $nullValue ;
        $value['OtrInc']      = $value['OtrInc'] ? $currency.$value['OtrInc']  : $nullValue ;
        $value['OtrExp']      = $value['OtrExp'] ? $currency.$value['OtrExp']  : $nullValue ;
        $value['CshTransRecv']  = $value['CshTransRecv'] ? $currency.$value['CshTransRecv']  : $nullValue ;
        $value['CshTransPaid']  = $value['CshTransPaid'] ? $currency.$value['CshTransPaid']  : $nullValue ;
        $BusinessPL = $BusinessPL ? $currency.$BusinessPL  : $nullValue ;
        $TotalInc   = $TotalInc   ? $currency.$TotalInc    : $nullValue ;
        $TotalExp   = $TotalExp   ? $currency.$TotalExp    : $nullValue ;
    
        $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
                        <cell style="background-color:#FFEBE6;">'.$value['LCName'].' -> Users</cell>
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
        
    }
    
    /*for($k=1; $k<10; $k++) {

        $nodeID['branch']    = 'Branch '.$k;
        $nodeID['request'] = 'allUser';
        $nodeID['seed']    = makeSeed();
        
        $MISData = viewReportData($nodeID);
        
        $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
                        <cell>Branch '.$k.' -> Users</cell>
                    </row>';
    }*/
    //unset($IDArray['branch']);
    return $result;
}
function itemWise ($reqArray) {
    global $currency, $nullValue;
    
    $nodeID = makeID();
    $MISData = viewReportData($nodeID);
    
    foreach ($MISData as $key => $value) {
        
        $nodeID['item']    = array($value['ITId'], $value['ITName'],'item_20.png');
        $nodeID['request'] = 'allBranch';
        $nodeID['seed']    = makeSeed();
        
        $BusinessPL  = $value['BusInc'] - $value['BusExp'];
        $TotalInc    = $value['BusInc'] + $value['OtrInc'] + $value['CshTransRecv'];
        $TotalExp    = $value['BusExp'] + $value['OtrExp'] + $value['CshTransPaid'];
        
        $value['BusInc']      = $value['BusInc'] ? $currency.$value['BusInc']  : $nullValue ;
        $value['BusExp']      = $value['BusExp'] ? $currency.$value['BusExp']  : $nullValue ;
        $value['OtrInc']      = $value['OtrInc'] ? $currency.$value['OtrInc']  : $nullValue ;
        $value['OtrExp']      = $value['OtrExp'] ? $currency.$value['OtrExp']  : $nullValue ;
        $value['CshTransRecv']  = $value['CshTransRecv'] ? $currency.$value['CshTransRecv']  : $nullValue ;
        $value['CshTransPaid']  = $value['CshTransPaid'] ? $currency.$value['CshTransPaid']  : $nullValue ;
        $BusinessPL = $BusinessPL ? $currency.$BusinessPL  : $nullValue ;
        $TotalInc   = $TotalInc   ? $currency.$TotalInc    : $nullValue ;
        $TotalExp   = $TotalExp   ? $currency.$TotalExp    : $nullValue ;
    
        $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
                        <cell style="background-color:#F5E6FF;">'.$value['ITName'].' -> Branches</cell>
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
        
    }
    
//    for($k=1; $k<10; $k++) {
//
//        $nodeID['item']    = 'Item '.$k;
//        $nodeID['request'] = 'allBranch';
//        $nodeID['seed']    = makeSeed();
//        
//        $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
//                        <cell>Item '.$k.' -> Branches</cell>
//                    </row>';
//    }
    //unset($IDArray['item']);
    return $result;
}
function userWise ($reqArray) {
    global $currency, $nullValue;
    $nodeID = makeID();
    
    $MISData = viewReportData($nodeID);
    
    foreach ($MISData as $key => $value) {
        
        $nodeID['branch']  = array($value['USId'],$value['USName'],'user.gif');
        $nodeID['request'] = 'allUser';
        $nodeID['seed']    = makeSeed();
        
        $BusinessPL  = $value['BusInc'] - $value['BusExp'];
        $TotalInc    = $value['BusInc'] + $value['OtrInc'] + $value['CshTransRecv'];
        $TotalExp    = $value['BusExp'] + $value['OtrExp'] + $value['CshTransPaid'];
        
        $value['BusInc']      = $value['BusInc'] ? $currency.$value['BusInc']  : $nullValue ;
        $value['BusExp']      = $value['BusExp'] ? $currency.$value['BusExp']  : $nullValue ;
        $value['OtrInc']      = $value['OtrInc'] ? $currency.$value['OtrInc']  : $nullValue ;
        $value['OtrExp']      = $value['OtrExp'] ? $currency.$value['OtrExp']  : $nullValue ;
        $value['CshTransRecv']  = $value['CshTransRecv'] ? $currency.$value['CshTransRecv']  : $nullValue ;
        $value['CshTransPaid']  = $value['CshTransPaid'] ? $currency.$value['CshTransPaid']  : $nullValue ;
        $BusinessPL = $BusinessPL ? $currency.$BusinessPL  : $nullValue ;
        $TotalInc   = $TotalInc   ? $currency.$TotalInc    : $nullValue ;
        $TotalExp   = $TotalExp   ? $currency.$TotalExp    : $nullValue ;
    
        $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
                        <cell>'.$value['USName'].' -> Entries</cell>
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
        
    }
    
    /*for($k=1; $k<10; $k++) {

        $nodeID['user']    = 'User '.$k;
        $nodeID['request'] = 'allEntry';
        $nodeID['seed']    = makeSeed();
        $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1">
                        <cell>User '.$k.' -> Entries</cell>
                    </row>';
    }*/
    //unset($IDArray['user']);
    return $result;
}
function entryWise ($reqArray) {

    $nodeID = makeID();

    for($k=1; $k<1; $k++) {

        $nodeID['entry']    = 'Entry '.$k;
        $nodeID['request'] = 'noIdea';
        $nodeID['seed']    = makeSeed();
        
        $MISData = viewReportData($nodeID);
        
        $result .= '<row id=\''.json_encode($nodeID).'\'>
                        <cell> - </cell>
                    </row>';
    }
    //unset($IDArray['entry']);
    return $result;
}
function otherNodes() {
    
    $nodeID = makeID();
    global $mainNode;
    global $reportItems;
    $requestString    = $nodeID['request'];

    foreach ($reportItems as $key => $value) {
        //if (!array_key_exists($key, $IDArray) && strtolower(str_replace("all","",$requestString)) !== $key && strtolower(str_replace("all","",$requestStringID)) !== $key) { 
        if (!array_key_exists($key, $nodeID) && $mainNode !== $key && strtolower(str_replace("all","",$requestString)) !== $key) {    
            //if(strtolower(str_replace("all","",$requestString)) !== $key) {
            
            $continue = true;
            $nodeID[$key] = $value;
            if($key == 'month' && ! $nodeID['year']) {
                $continue = false;
            } 
            //<cell>'.json_encode($mainNode .'--'. $key).'--'.is_array($nodeID['year']).'--'.$continue.'--'.array_key_exists('year', $nodeID).'--'.'</cell>
            if($continue) {
                
                $nodeID['request'] = 'all'.ucwords($key);
                $nodeID['seed']    = makeSeed();
                $result .= '<row id=\''.json_encode($nodeID).'\' xmlkids="1"  style="background-color:#deeaf8 !important;">
                                <cell style="background-color:#EBFFFF;"><![CDATA[<b style="color:#FC7B16;">'.$value[1].'</b>]]></cell>
                            </row>';
            }
                
            unset($nodeID[$key]);
        }
    }
    return $result;
}
function makeID() {
    global $IDArray;
    //unset($IDArray['request']);
    unset($IDArray['seed']);
    return $IDArray;
}
function makeSeed() {
  list($usec, $sec) = explode(' ', microtime());
  return ((float) $sec + ((float) $usec * 100000)) * 1000;
}
function viewReportData($nodeID,$beginDate='',$endDate=''){

    $ReportObj = new ReportClass();
    global $OFId, $currency;
    
    if($nodeID['item'][0] != 0) $ITId = $nodeID['item'][0];
    if($nodeID['branch'][0] != 0) $LCId = $nodeID['branch'][0];
    
    if($nodeID['year'][0] && !is_numeric($nodeID['year'][0])){
        
        if($nodeID['date'][0]){
            $sDate    = date($nodeID['date'][0].'.'.$nodeID['month'][0].'.'.$nodeID['year'][0]);
        }else{
            $sDate    = date('01.4.'.$nodeID['year'][0][0]);
//            $lastDate = date('t',strtotime($sDate));
            $eDate    = date('31.3.'.$nodeID['year'][0][1]);
        }
        
    } else if($nodeID['year'][0] && is_numeric($nodeID['year'][0]) ){
        
        if($nodeID['date'][0]){
            $sDate    = date($nodeID['date'][0].'.'.$nodeID['month'][0].'.'.$nodeID['year'][0]);
        }else{
            $sDate    = date('01.'.$nodeID['month'][0].'.'.$nodeID['year'][0]);
            $lastDate = date('t',strtotime($sDate));
            $eDate    = date($lastDate.'.'.$nodeID['month'][0].'.'.$nodeID['year'][0]);
        }   
        
    }
        
        
    if($nodeID['request'] == 'allMonth') {
            
        if($beginDate && $endDate){

            $sYear  = $beginDate->format('Y');
            $sMonth = $beginDate->format('m');

            $eYear  = $endDate->format('Y');
            $eMonth = $endDate->format('m');

            $sDate    = date('01.'.$eMonth.'.'.$eYear);
            $lastDate = date('t',strtotime($sDate));
            if(!$eDate) $eDate    = date($lastDate.'.'.$sMonth.'.'.$sYear);

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId,'ITId' => $ITId ,'LCId' => $LCId , 'Request' => 'user');

            $ReportObj->viewMonthlyReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;

            $dataValue = $RptData;

        }else{

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId );

            $ReportObj->viewMISReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;

            $BusinessPL  = $RptData['BusInc'] - $RptData['BusExp'];
            $TotalInc    = $RptData['BusInc'] + $RptData['OtrInc'] + $RptData['CshTransRecv'];
            $TotalExp    = $RptData['BusExp'] + $RptData['OtrExp'] + $RptData['CshTransPaid'];

            $RptData['BusInc']      = $RptData['BusInc'] ? $currency.$RptData['BusInc']  : $nullValue ;
            $RptData['BusExp']      = $RptData['BusExp'] ? $currency.$RptData['BusExp']  : $nullValue ;
            $RptData['OtrInc']      = $RptData['OtrInc'] ? $currency.$RptData['OtrInc']  : $nullValue ;
            $RptData['OtrExp']      = $RptData['OtrExp'] ? $currency.$RptData['OtrExp']  : $nullValue ;
            $RptData['CshTransRecv']  = $RptData['CshTransRecv'] ? $currency.$RptData['CshTransRecv']  : $nullValue ;
            $RptData['CshTransPaid']  = $RptData['CshTransPaid'] ? $currency.$RptData['CshTransPaid']  : $nullValue ;
            $BusinessPL = $BusinessPL ? $currency.$BusinessPL  : $nullValue ;
            $TotalInc   = $TotalInc   ? $currency.$TotalInc    : $nullValue ;
            $TotalExp   = $TotalExp   ? $currency.$TotalExp    : $nullValue ;

            $dataValue   =  '<cell>'.$RptData['BusInc'].'</cell>
                            <cell>'.$RptData['BusExp'].'</cell>
                            <cell>'.$BusinessPL.'</cell>
                            <cell>'.$RptData['OtrInc'].'</cell>
                            <cell>'.$RptData['OtrExp'].'</cell>
                            <cell>'.$RptData['CshTransRecv'].'</cell>
                            <cell>'.$RptData['CshTransPaid'].'</cell>
                            <cell>'.$TotalInc.'</cell>
                            <cell>'.$TotalExp.'</cell>';
        }

    }
        
    if($nodeID['request'] == 'allDate') {

        $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId, 'LCId' => $LCId, 'Request' => 'item');

        $ReportObj->viewDailyReportData($MISArgArray);
        $RptData = $ReportObj->ReportArray;

        $dataValue = $RptData;

    }

    if($nodeID['request'] == 'allItem') {

        $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate ,'OFId' => $OFId, 'LCId' => $LCId , 'Request' => 'item');

        $ReportObj->viewItemReportData($MISArgArray);
        $RptData = $ReportObj->ReportArray;

        $dataValue = $RptData;

    }

    if($nodeID['request'] == 'allBranch') {

        $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate ,'OFId' => $OFId, 'LCId' => $LCId, 'ITId' => $ITId , 'Request' => 'branch');

        $ReportObj->viewBranchReportData($MISArgArray);
        $RptData = $ReportObj->ReportArray;

        $dataValue = $RptData;

    }

    if($nodeID['request'] == 'allUser') {

        $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId,'ITId' => $ITId ,'LCId' => $LCId , 'Request' => 'user');

        $ReportObj->viewUserReportData($MISArgArray);
        $RptData = $ReportObj->ReportArray;

        $dataValue = $RptData;

    }

    return $dataValue; 
}    
        
        
        
        
        














        
        
        
        
        
        
        
        
        
        
        
     /*   
        
        
        if($nodeID['request'] == 'allItem') {
            
            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId, 'Request' => 'item');

            $ReportObj->viewItemReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;
            
            $dataValue = $RptData;
    
        }
        if($nodeID['request'] == 'allBranch') {

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId,'ITId' => $ITId , 'Request' => 'branch');

            $ReportObj->viewBranchReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;
            
            $dataValue = $RptData; 

        }
        
        if($nodeID['request'] == 'allUser') {

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId,'ITId' => $ITId ,'LCId' => $LCId , 'Request' => 'user');
            
            $ReportObj->viewUserReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;
            
            $dataValue = $RptData;

        }

        if($nodeID['request'] == 'allMonth') {
            
            if($beginDate && $endDate){
                
                $sYear  = $beginDate->format('Y');
                $sMonth = $beginDate->format('m');
                
                $eYear  = $endDate->format('Y');
                $eMonth = $endDate->format('m');

                $sDate    = date('01.'.$eMonth.'.'.$eYear);
                $lastDate = date('t',strtotime($sDate));
                if(!$eDate) $eDate    = date($lastDate.'.'.$sMonth.'.'.$sYear);
        
                $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId,'ITId' => $ITId ,'LCId' => $LCId , 'Request' => 'user');
               
                $ReportObj->viewMonthlyReportData($MISArgArray);
                $RptData = $ReportObj->ReportArray;

                $dataValue = $RptData;
                
            }else{
                
                $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId );
            
                $ReportObj->viewMISReportData($MISArgArray);
                $RptData = $ReportObj->ReportArray;

                $BusinessPL  = $RptData['BusInc'] - $RptData['BusExp'];
                $TotalInc    = $RptData['BusInc'] + $RptData['OtrInc'] + $RptData['CshTransRecv'];
                $TotalExp    = $RptData['BusExp'] + $RptData['OtrExp'] + $RptData['CshTransPaid'];
                
                $RptData['BusInc']      = $RptData['BusInc'] ? $currency.$RptData['BusInc']  : $nullValue ;
                $RptData['BusExp']      = $RptData['BusExp'] ? $currency.$RptData['BusExp']  : $nullValue ;
                $RptData['OtrInc']      = $RptData['OtrInc'] ? $currency.$RptData['OtrInc']  : $nullValue ;
                $RptData['OtrExp']      = $RptData['OtrExp'] ? $currency.$RptData['OtrExp']  : $nullValue ;
                $RptData['CshTransRecv']  = $RptData['CshTransRecv'] ? $currency.$RptData['CshTransRecv']  : $nullValue ;
                $RptData['CshTransPaid']  = $RptData['CshTransPaid'] ? $currency.$RptData['CshTransPaid']  : $nullValue ;
                $BusinessPL = $BusinessPL ? $currency.$BusinessPL  : $nullValue ;
                $TotalInc   = $TotalInc   ? $currency.$TotalInc    : $nullValue ;
                $TotalExp   = $TotalExp   ? $currency.$TotalExp    : $nullValue ;

                $dataValue   =  '<cell>'.$RptData['BusInc'].'</cell>
                                <cell>'.$RptData['BusExp'].'</cell>
                                <cell>'.$BusinessPL.'</cell>
                                <cell>'.$RptData['OtrInc'].'</cell>
                                <cell>'.$RptData['OtrExp'].'</cell>
                                <cell>'.$RptData['CshTransRecv'].'</cell>
                                <cell>'.$RptData['CshTransPaid'].'</cell>
                                <cell>'.$TotalInc.'</cell>
                                <cell>'.$TotalExp.'</cell>';
            }
            
        }
        if($nodeID['request'] == 'allDate') {

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId,'ITId' => $ITId ,'LCId' => $LCId , 'Request' => 'user');
           
            $ReportObj->viewMonthlyReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;
            
            $dataValue = $RptData;
        }
       
    } else if($nodeID['year'][0] && is_numeric($nodeID['year'][0]) ){
        
        if($nodeID['date'][0]){
            $sDate    = date($nodeID['date'][0].'.'.$nodeID['month'][0].'.'.$nodeID['year'][0]);
        }else{
            $sDate    = date('01.'.$nodeID['month'][0].'.'.$nodeID['year'][0]);
            $lastDate = date('t',strtotime($sDate));
            $eDate    = date($lastDate.'.'.$nodeID['month'][0].'.'.$nodeID['year'][0]);
        }

        if($nodeID['request'] == 'allDate') {

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId, 'LCId' => $LCId, 'Request' => 'item');
         
            $ReportObj->viewDailyReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;
            
            $dataValue = $RptData;

        }
        
        if($nodeID['request'] == 'allItem') {

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate ,'OFId' => $OFId, 'LCId' => $LCId , 'Request' => 'item');
            
            $ReportObj->viewItemReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;
            
            $dataValue = $RptData;

        }
        
        if($nodeID['request'] == 'allBranch') {

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate ,'OFId' => $OFId, 'LCId' => $LCId, 'ITId' => $ITId , 'Request' => 'branch');

            $ReportObj->viewBranchReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;
            
            $dataValue = $RptData;

        }
        
        if($nodeID['request'] == 'allUser') {

            $MISArgArray = array('stDate'=> $sDate, 'enDate' => $eDate , 'OFId' => $OFId,'ITId' => $ITId ,'LCId' => $LCId , 'Request' => 'user');

            $ReportObj->viewUserReportData($MISArgArray);
            $RptData = $ReportObj->ReportArray;
            
            $dataValue = $RptData;

        }
             
    }
    return $dataValue; 
}*/
?>