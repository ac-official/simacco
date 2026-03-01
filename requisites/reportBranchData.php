<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH."preTallyClass/LocationClass.php");
include_once($BASEPATH."preTallyClass/MasterReportClass.php");

$start_date = $REQUEST["f"];
$end_date   = $REQUEST["t"];
$LocationObj  = new LocationClass();
$BranchRptObj = new MasterReportClass();

$LocationObj->viewLocations("*", "WHERE OF_Id = ".$preTally_user_ofid." AND LC_Status != 5 ORDER BY LC_Name");//LC_Status=1 AND 
$LC_Obj = $LocationObj->LocationArray;

$BranchRptObj->reportBranches($preTally_user_ofid,$start_date,$end_date);
$LC_CshObj = $BranchRptObj->BranchCshRptArray;
$LC_BnkObj = $BranchRptObj->BranchBnkRptArray;
//print_r($LC_CshObj);

$BranchRptObj->getBranchBankOB($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid);
$BankOB_Obj = $BranchRptObj->BankOBArray;

$BranchRptObj->getBranchCashOB($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid);
$CashOB_Obj = $BranchRptObj->CashOBArray;

$BranchRptObj->reportStockDetailsBranch($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$preTally_user_ofid);
$JOB_Obj = $BranchRptObj->BranchStkRptArray;
//print_r($JOB_Obj);
$RptArray=array();
$cnt=0;
foreach($LC_Obj as  $rw){ 
        
    $RptArray[$rw->LC_Id]["Id"]=$rw->LC_Id;
    $RptArray[$rw->LC_Id]["Name"]=$rw->LC_Name;
    $RptArray[$rw->LC_Id]["Status"]=$rw->LC_Status;
    
    $RptArray[$rw->LC_Id]["CshInc"]        = $LC_CshObj[$rw->LC_Id]['CshInc'] ? $LC_CshObj[$rw->LC_Id]['CshInc'] : 0; 
    $RptArray[$rw->LC_Id]["CshExp"]        = $LC_CshObj[$rw->LC_Id]['CshExp'] ? $LC_CshObj[$rw->LC_Id]['CshExp'] : 0;
//    $RptArray[$rw->LC_Id]["Buss"]          = $LC_CshObj[$rw->LC_Id]['Buss']   ? $LC_CshObj[$rw->LC_Id]['Buss']   : 0;
    $RptArray[$rw->LC_Id]["CshTransRecv"]  = $LC_CshObj[$rw->LC_Id]['CshTransRecv'] ? $LC_CshObj[$rw->LC_Id]['CshTransRecv'] : 0;
    $RptArray[$rw->LC_Id]["CshTransPaid"]  = $LC_CshObj[$rw->LC_Id]['CshTransPaid'] ? $LC_CshObj[$rw->LC_Id]['CshTransPaid'] : 0;
    
    $RptArray[$rw->LC_Id]["BnkInc"]        = $LC_BnkObj[$rw->LC_Id]['BnkInc'] ? $LC_BnkObj[$rw->LC_Id]['BnkInc'] : 0; 
    $RptArray[$rw->LC_Id]["BnkExp"]        = $LC_BnkObj[$rw->LC_Id]['BnkExp'] ? $LC_BnkObj[$rw->LC_Id]['BnkExp'] : 0;
    $RptArray[$rw->LC_Id]["BnkTransRecv"]  = $LC_BnkObj[$rw->LC_Id]['BnkTransRecv'] ? $LC_BnkObj[$rw->LC_Id]['BnkTransRecv'] : 0;
    $RptArray[$rw->LC_Id]["BnkTransPaid"]  = $LC_BnkObj[$rw->LC_Id]['BnkTransPaid'] ? $LC_BnkObj[$rw->LC_Id]['BnkTransPaid'] : 0;
        
    $RptArray[$rw->LC_Id]["CshOB"]  =   $CashOB_Obj[$rw->LC_Id];
    $RptArray[$rw->LC_Id]["BnkOB"]  =   $BankOB_Obj[$rw->LC_Id];

    $RptArray[$rw->LC_Id]["CshCB"]  =   $RptArray[$rw->LC_Id]["CshOB"] + $RptArray[$rw->LC_Id]["CshInc"] - $RptArray[$rw->LC_Id]["CshExp"] + $RptArray[$rw->LC_Id]["CshTransRecv"] - $RptArray[$rw->LC_Id]["CshTransPaid"];
    $RptArray[$rw->LC_Id]["BnkCB"]  =   $RptArray[$rw->LC_Id]["BnkOB"] + $RptArray[$rw->LC_Id]["BnkInc"] - $RptArray[$rw->LC_Id]["BnkExp"] + $RptArray[$rw->LC_Id]["BnkTransRecv"] - $RptArray[$rw->LC_Id]["BnkTransPaid"];
    
    $RptArray[$rw->LC_Id]["Buss"]   =   $LC_CshObj[$rw->LC_Id]['Buss'] + $LC_BnkObj[$rw->LC_Id]['Buss']  ? $LC_CshObj[$rw->LC_Id]['Buss'] + $LC_BnkObj[$rw->LC_Id]['Buss']    : 0;
    
    $RptArray[$rw->LC_Id]["OLDSTK"] =   $JOB_Obj[$rw->LC_Id]['CurrentPeriodOldStock'] + $JOB_Obj[$rw->LC_Id]['Old_Business'] - $JOB_Obj[$rw->LC_Id]['Old_Income'] ;
    $RptArray[$rw->LC_Id]["CURSTK"] =   $RptArray[$rw->LC_Id]["OLDSTK"] + $JOB_Obj[$rw->LC_Id]['Business'] - $JOB_Obj[$rw->LC_Id]['Income'] ;
   }

//var_dump($LC_Obj);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		
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

	</head>';
        if($RptArray) {
              $j = 1;
              foreach($RptArray as $rws) {
                  $namecellcolor=''; 
            if($rws['Status']==0)
            $namecellcolor='style="color:#ff6600;font-weight:bold;"';
            elseif($rws['Status']==2)
            $namecellcolor='style="color:red;font-weight:bold;"';
            elseif($rws['Status']==1)
            $namecellcolor=''; 
                  
                  
                  echo '<row id="'.$rws['Id'].'">
                      <cell>'.$j.'</cell>
                      <cell name="LC_Name" '.$namecellcolor.'>'.$rws['Name'].'</cell>
                      <cell>'.round($rws['CshOB']).'</cell>
                      <cell>'.round($rws['BnkOB']).'</cell>
                      <cell>'.round($rws['OLDSTK']).'</cell>
                      <cell>'.round($rws['CshInc']).'</cell>
                      <cell>'.round($rws['BnkInc']).'</cell>
                      <cell>'.round($rws['CshExp']).'</cell>
                      <cell>'.round($rws['BnkExp']).'</cell>
                      <cell>'.round($rws['CshCB']).'</cell>
                      <cell>'.round($rws['BnkCB']).'</cell>
                      <cell>'.round($rws['CURSTK']).'</cell>
                      <cell>'.round($rws['Buss']).'</cell>
                      <cell>'.round($rws['CshTransRecv']).'</cell>
                      <cell>'.round($rws['BnkTransRecv']).'</cell>
                      <cell>'.round($rws['CshTransPaid']).'</cell>
                      <cell>'.round($rws['BnkTransPaid']).'</cell>
                  </row>';
                  $j++;
              }
          }
       else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}   
		  
echo '</rows>';
?>