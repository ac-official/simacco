<?php
/**
 * Export the break time based on the filter selected by the user
 * Created BY bilin @ 03-07-2025
*/
// include the break time related class files
require_once($BASEPATH . "preTallyClass/ExportExcelClass.php");
include_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
$btObj		= new BreakTimeClass();
$ExcelObj 	= new ExportExcelClass();

// process the inputs 
$inparams 				= ['off_id'=>$preTally_user_ofid, 'export'=>'yes'];
$filterData 			= (isset($_REQUEST['filter'])) ? $_REQUEST['filter'] : [];
$inparams['search']		= (isset($filterData[0]) && $filterData[0] != "") ? trim($filterData[0]) : "";
$inparams['from_date']	= (isset($filterData[1]) && $filterData[1] != "") ? date("Y-m-d", strtotime($filterData[1])) : NULL;
$inparams['to_date']	= (isset($filterData[2]) && $filterData[2] != "") ? date("Y-m-d", strtotime($filterData[2])) : NULL;
$inparams['branch_id']  = (isset($filterData[3]) && $filterData[3] > 0) ? (int)$filterData[3] : 0;
$inparams['bkstatus']   = (isset($filterData[4])) ? (int)$filterData[4] : 0;

// find the office based break time settings
$settingbt  			= $btObj->getBreakSettings($preTally_user_ofid);
if (!in_array($preTally_user_id, $settingbt['view_users'])) {
	// self users - no permissions to list others
	$inparams['user_id']= $preTally_user_id;	
}
$inparams['total_time']	= $settingbt['time'];

//find the details based on the parameters provided
$brktypeids 		= $btObj->listBreakTimeRpt($inparams);
//$total_records 		= $btObj->btUTotal;
$result_data 		= $btObj->btUsrList;

// excel export heading setting
$headerArray 		= ["slno"=>"Slno", "name"=>"Staff Name", "branch"=>"Branch Name", "break_date"=>"Date"];
foreach ($brktypeids AS $bky => $bkv) {
	$headerArray[$bkv->id] 	= $bkv->title;
}
$headerArray['time_total'] = "Total Time";

echo $ExcelObj->saveExcelRpt($result_data, $headerArray,['sheetTitle'=>'Break Time Report']);
exit;
?>