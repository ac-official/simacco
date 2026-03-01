<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH."preTallyClass/LocationClass.php");
include_once($BASEPATH."preTallyClass/AccountSummaryClass.php");
/*$REQUEST["f"]="2016-04-01";
$REQUEST["t"]="2017-03-31";*/
$start_date = $REQUEST["f"];
$end_date   = $REQUEST["t"];

$d1 = new DateTime($REQUEST["f"]);
$d2 = new DateTime($REQUEST["t"]);

$count =$d2->diff($d1)->m; // int(4)

$LocationObj  = new LocationClass();
$BranchRptObj = new AccountSummaryClass();

//$LocationObj->viewLocations("*", "WHERE OF_Id = ".$preTally_user_ofid." ORDER BY LC_Name");//LC_Status=1 AND 
//$LC_Obj = $LocationObj->LocationArray;
    $indx=date("m-Y",strtotime($start_date));
    $BranchRptObj->getBranchBankOB($start_date,$end_date,$preTally_user_ofid,$indx);
    $BankOB_Obj = $BranchRptObj->BankOBArray;
    
    $BranchRptObj->getBranchCashOB($start_date,$end_date,$preTally_user_ofid,$indx);
    $CashOB_Obj = $BranchRptObj->CashOBArray;
    
    $BranchRptObj->reportStockDetailsBranch($REQUEST['r'],$start_date,'',$preTally_user_ofid);
    $JOB_Obj = $BranchRptObj->BranchStkRptArray;

   $BranchRptObj->reportBranches($preTally_user_ofid,$start_date,$end_date,$indx);
    $LC_CshObj = $BranchRptObj->BranchCshRptArray;
    $LC_BnkObj = $BranchRptObj->BranchBnkRptArray;
    $LC_StkObj = $BranchRptObj->StockArray;
   //var_dump($LC_StkObj);

//print_r($JOB_Obj);
$RptArray=array();
$cnt=0;
$counter_date=$start_date;


$RptArray[0]["BnkOB"]  =   $BankOB_Obj["Amt"];
$RptArray[0]["CshOB"]  =   $CashOB_Obj["Amt"];
$RptArray[0]["StkOB"] =   $JOB_Obj['CurrentPeriodOldStock'] + $JOB_Obj['Old_Business'] - $JOB_Obj['Old_Income'] ;
$RptArray[0]["CURSTK"] =   $RptArray[$indx]["OLDSTK"] + $JOB_Obj[$indx]['Business'] - $JOB_Obj[$indx]['Income'] ;
for($i=0;$i<=$count;$i++){ 
     
    $RptArray[$i]["Id"]=$i;
    $RptArray[$i]["Month"]=  date("F",strtotime($counter_date));
    $RptArray[$i]["Year"]=  date("Y",strtotime($counter_date));
    if($i!=0){
        $RptArray[$i]["BnkOB"]  =   $RptArray[$i-1]["BnkCB"];
        $RptArray[$i]["CshOB"]  =   $RptArray[$i-1]["CshCB"];
        $RptArray[$i]["StkOB"] =   $RptArray[$i-1]["StkCB"];
    }
    $indx=date("m-Y",strtotime($counter_date));
    $from_date=date("Y-m-1",strtotime($counter_date));
    $to_date =date("Y-m-t",strtotime($counter_date));  

    
    $RptArray[$i]["CshInc"]        = $LC_CshObj[$indx]['CshInc'] ? $LC_CshObj[$indx]['CshInc'] : 0; 
    $RptArray[$i]["CshExp"]        = $LC_CshObj[$indx]['CshExp'] ? $LC_CshObj[$indx]['CshExp'] : 0;    
    $RptArray[$i]["CshTransRecv"]  = $LC_CshObj[$indx]['CshTransRecv'] ? $LC_CshObj[$indx]['CshTransRecv'] : 0;
    $RptArray[$i]["CshTransPaid"]  = $LC_CshObj[$indx]['CshTransPaid'] ? $LC_CshObj[$indx]['CshTransPaid'] : 0;
    
    $RptArray[$i]["BnkInc"]        = $LC_BnkObj[$indx]['BnkInc'] ? $LC_BnkObj[$indx]['BnkInc'] : 0; 
    $RptArray[$i]["BnkExp"]        = $LC_BnkObj[$indx]['BnkExp'] ? $LC_BnkObj[$indx]['BnkExp'] : 0;
    $RptArray[$i]["BnkTransRecv"]  = $LC_BnkObj[$indx]['BnkTransRecv'] ? $LC_BnkObj[$indx]['BnkTransRecv'] : 0;
    $RptArray[$i]["BnkTransPaid"]  = $LC_BnkObj[$indx]['BnkTransPaid'] ? $LC_BnkObj[$indx]['BnkTransPaid'] : 0;
        
    $RptArray[$i]["CrrStk"]  = $LC_StkObj[$indx]['CurrStk'] ? $LC_StkObj[$indx]['CurrStk'] : 0;
    
    

    $RptArray[$i]["CshCB"]  =   $RptArray[$i]["CshOB"] + $RptArray[$i]["CshInc"] - $RptArray[$i]["CshExp"] + $RptArray[$i]["CshTransRecv"] - $RptArray[$i]["CshTransPaid"];
    $RptArray[$i]["BnkCB"]  =   $RptArray[$i]["BnkOB"] + $RptArray[$i]["BnkInc"] - $RptArray[$i]["BnkExp"] + $RptArray[$i]["BnkTransRecv"] - $RptArray[$i]["BnkTransPaid"];
    $RptArray[$i]["StkCB"]  =   $RptArray[$i]["StkOB"] + $RptArray[$i]["CrrStk"];
    
    $RptArray[$i]["TotClose"]  =   $RptArray[$i]["CshCB"] + $RptArray[$i]["BnkCB"]+ $RptArray[$i]["StkCB"];
    
    $RptArray[$i]["Buss"]   =   $LC_CshObj[$indx]['Buss'] + $LC_BnkObj[$indx]['Buss']  ? $LC_CshObj[$indx]['Buss'] + $LC_BnkObj[$indx]['Buss']    : 0;
    
    $RptArray[$i]["TotOB"]          =$RptArray[$i]["CshOB"]+$RptArray[$i]["BnkOB"]+$RptArray[$i]["StkOB"] ;
    
    $RptArray[$i]["TotInc"]         = $RptArray[$i]["CshInc"]+$RptArray[$i]["BnkInc"]+$RptArray[$i]["CrrStk"];
    
    $RptArray[$i]["TotTransInc"]    = $RptArray[$i]["CshTransRecv"]+$RptArray[$i]["BnkTransRecv"];
    
    $RptArray[$i]["TotExp"]         = $RptArray[$i]["CshExp"]+$RptArray[$i]["BnkExp"];
    
    $RptArray[$i]["TotTransExp"]    = $RptArray[$i]["CshTransPaid"]+$RptArray[$i]["BnkTransPaid"];
    
   $RptArray[$i]["GrossInc"]    = $RptArray[$i]["TotTransInc"]+$RptArray[$i]["TotInc"];
   $RptArray[$i]["GrossExp"]    = $RptArray[$i]["TotTransExp"]+$RptArray[$i]["TotExp"];
    $counter_date=date("Y-m-01", strtotime("+1 months", strtotime($counter_date)));
   }
   
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
                  $color="";
                  $month=explode("-",$rws['Name']);
                  if($month[0]=="Apr" && $month[1]==date("Y"))
                      $color="bgColor='orange'";
                  echo '<row id="'.$rws['Id'].'" '.$color.'>
                    <cell>'.$j.'</cell>
                    <cell>'.$rws['Year'].'</cell>                     
                    <cell>'.$rws['Month'].'</cell>             
                    <cell>'.round($rws['TotOB']).'</cell>                                                          
                    <cell>'.round($rws['GrossInc']).'</cell>                            
                    <cell>'.round($rws['GrossExp']).'</cell>                                                                
                    <cell>'.round($rws['TotClose']).'</cell>
                                            
                  </row>';
                  $j++;
              }
          }
       else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}   
		  
echo '</rows>';
?>