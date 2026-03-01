<?php
/**
 * Created By Bilin At 27-06-2025 Updated @ 01-07-2025
 * Break time of all employees listed with filter and paginations
*/
// include the attendance related class files
include_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
$btObj		= new BreakTimeClass();

// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$inparams 			= ['off_id'=>$preTally_user_ofid];
// pagination limit and start parameters 
$inparams['start'] 	=  (isset($_GET["posStart"])) ? (int)$_GET["posStart"] : 0;
$inparams['limit'] 	=  (isset($_GET["count"]) && $_GET["count"] > 0) ? (int)$_GET["count"] : 50;

// filter inputs processing
$filterData 		= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['from_date']	= (isset($filterData[1]) && $filterData[1] != "") ? date("Y-m-d", strtotime($filterData[1])) : NULL;
$inparams['to_date']	= (isset($filterData[2]) && $filterData[2] != "") ? date("Y-m-d", strtotime($filterData[2])) : NULL;
$inparams['branch_id']  = (isset($filterData[3]) && $filterData[3] > 0) ? (int)$filterData[3] : 0;
$inparams['bkstatus']   = (isset($filterData[4])) ? (int)$filterData[4] : 0;

// find the office based break time settings
$settingbt  			= $btObj->getBreakSettings($preTally_user_ofid);
if (!in_array($preTally_user_id, $settingbt['view_users'])) {
	// self users - no permissions to list others
	$inparams['user_id'] = $preTally_user_id;	
}
$inparams['total_time']	= $settingbt['time'];


//find the details based on the parameters provided
$brktypeids = $btObj->listBreakTimeRpt($inparams);
$total_records 		= $btObj->btUTotal;
$result_data 		= $btObj->btUsrList;

//echo $btObj->sqlqry;
// return output processing
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="'.$total_records.'" pos="'.$inparams['start'].'">
      <userdata name="Data_Count">'.$total_records.'</userdata>';

    if (!empty($result_data)) {

    	foreach($result_data as $rw) {

    		$rowstyle      = ($rw['time_total'] > $settingbt['time'])? ' style="background:rgb(255 235 235);"': '';
    		
    		echo '<row id="bt'.$rw['slno'].'" '.$rowstyle.'>';
			echo ' <cell>'.$rw['slno'].'</cell>';
			echo ' <cell>'.$rw['name'].'</cell>';
			echo '<cell>'.$rw['branch'].'</cell>';
            echo '<cell>'.date('d/m/Y',strtotime($rw['break_date'])).'</cell>';
            foreach ($brktypeids AS $btk => $btyp) {
            	$cellstyle = '';
            	if ($rw['types'][$btk] > $settingbt['time'] && $rw['time_total'] > $settingbt['time'] ) {
            		$cellstyle = ' style="color:red;background:rgb(255 235 235);"';
            	}else if ($rw['types'][$btk] > $rw['max'][$btk] && $rw['time_total'] > $settingbt['time'])  {
    				$cellstyle = ' style="color:#ff6000;background:rgb(255 235 235);"';
    			}else if ($rw['types'][$btk] > $rw['max'][$btk])  {
    				$cellstyle = ' style="color:#ff6000;"';
    			}  
    			echo '<cell '.$cellstyle.'>'.(($rw['types'][$btk] > 0 ) ? $rw['types'][$btk].' Min':'-').'</cell>';          	 
            } 
    		$cellstyle =  ($rw['time_total'] > $settingbt['time']) ?' style="color:red;background:rgb(255 235 235);"':'';    		
            echo '<cell'.$cellstyle.'>'.$rw['time_total'].' Min</cell>';
            echo '</row>';
    	}
    } else {

    	echo '<row id="0"><cell colspan="9"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
    }

echo '</rows>';
?>