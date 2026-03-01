<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH."preTallyClass/CashBSClass.php");
include_once($BASEPATH."preTallyClass/BankBSClass.php");
include_once($BASEPATH."preTallyClass/StockRptClass.php");
include_once($BASEPATH."preTallyClass/MasterReportClass.php");
$CashOBObj  = new CashBSClass();
$BankOBObj  = new BankBSClass();
$MasterRptObj = new MasterReportClass();

$LCId = $REQUEST['LCId'] ? $REQUEST['LCId'] : $preTally_user_lcid ;

$filterOB = $filter = 'LC_Id IN ( '.$LCId.' ) ';
$cfields = 'SUM(OB_OpenBal) AS OB';
$bfields = 'SUM(OB.BnkOB_OpenBal) AS OB';
$sfields = 'SUM(OS_OpenBal) AS OS';

$MasterRptObj->reportBMRVisualDetails($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$BMRP_Obj = $MasterRptObj->MasterReportArray;
//print_r($BMRP_Obj);
$INC = $EXP = $CSH_INC = $CSH_EXP = $BNK_INC = $BNK_EXP = $CSH_TRANS_INC = $CSH_TRANS_EXP = $BNK_TRANS_INC = $BNK_TRANS_EXP = 0;

foreach ($BMRP_Obj as $key => $value) {

    if( $value->PM_Id == 1 && $value->MH_Type == 1 ){//echo $value->IE; echo "--";
        $CSH_INC += $value->IE;
        if( $value->IT_Transfers == 1){
            $CSH_TRANS_INC += $value->IE ;
        }
    }
    if( $value->PM_Id == 1 && $value->MH_Type == 2 ) {
        $CSH_EXP += $value->IE;
        if( $value->IT_Transfers == 1){
            $CSH_TRANS_EXP += $value->IE ;
        }
    }
    if( $value->PM_Id == 2 && $value->MH_Type == 1 ){
        $BNK_INC += $value->IE;
        if( $value->IT_Transfers == 1){
            $BNK_TRANS_INC += $value->IE ;
        }
    }
    if( $value->PM_Id == 2 && $value->MH_Type == 2 ) {
        $BNK_EXP += $value->IE;
        if( $value->IT_Transfers == 1){
            $BNK_TRANS_EXP += $value->IE ;
        }
    }
}

//-------------------------- Cash Opening Balance ----------------------//

$CashOBObj->getOpeningBalance($REQUEST['f'],$REQUEST['t'],$cfields, $filter);
$COB_Obj = $CashOBObj->CashBSArray;
//print_r($OBO_Obj);
$COB['1'] = $COB['2'] = $COB['3'] = $COB['4'] = 0;

$COB[$COB_Obj[1]->MH_Type] = $COB_Obj[1]->IE;
$COB[$COB_Obj[2]->MH_Type] = $COB_Obj[2]->IE;

$COB['3'] = ($COB_Obj[0]->OB + $COB['1']) - $COB['2'];
$COB['4'] = ($COB['3'] + $CSH_INC) - $CSH_EXP;


//-------------------------- Bank Opening Balance ----------------------//


$BankOBObj->getBranchBankOpeningBalance($REQUEST['f'],$REQUEST['t'],$bfields, $filterOB, $filterOB);
$BOB_Obj = $BankOBObj->BankBSArray;
//print_r($BOB_Obj);
$BOB['1'] = $BOB['2'] = $BOB['3'] = $BOB['4'] = 0;

$BOB[$BOB_Obj[1]->MH_Type] = $BOB_Obj[1]->IE;
$BOB[$BOB_Obj[2]->MH_Type] = $BOB_Obj[2]->IE;

$BOB['3'] = ($BOB_Obj[0]->OB + $BOB['1']) - $BOB['2'];
$BOB['4'] = ($BOB['3'] + $BNK_INC) - $BNK_EXP;


//---------------------------- Stock Opeining Balance ------------------//

//$JOB_Obj['CurrentPeriodOldStock'] Old Stock From Table

$CURRENTSTK = 0;
$MasterRptObj->reportStockDataSum($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$SRP_Obj = $MasterRptObj->MasterReportArray;

$MasterRptObj->reportStockDetails($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$sfields, $filter);
$JOB_Obj = $MasterRptObj->MasterReportArray;
$JOB     = $SRP_Obj['Business'];

$OLDSTK  = $JOB_Obj['CurrentPeriodOldStock'] + $JOB_Obj['Old_Business'] - $JOB_Obj['Old_Income'] ;
$CURRENTSTK = $OLDSTK + $JOB - $SRP_Obj['Income'];

$TOT_OB = $COB['3'] + $BOB['3'];
$TOT_CB = $COB['4'] + $BOB['4'];

$TRANSINC = $CSH_INC + $BNK_INC ;
$TRANSEXP = $CSH_EXP + $BNK_EXP;

$INC = $CSH_INC + $BNK_INC - $CSH_TRANS_INC - $BNK_TRANS_INC ;
$EXP = $CSH_EXP + $BNK_EXP - $CSH_TRANS_EXP - $BNK_TRANS_EXP ;


$timeF=strtotime($REQUEST['f']);
if($timeF){
    $dayF=date("d",$timeF);
    $monthF=date("F",$timeF);
    $yearF=date("Y",$timeF);
}else{
    $dayF='';
    $monthF='';
    $yearF='';
}

$timeT=strtotime($REQUEST['t']);
if($timeT){
    $dayT=date("d",$timeT);
    $monthT=date("F",$timeT);
    $yearT=date("Y",$timeT);
}else{
    $dayT='';
    $monthT='';
    $yearT='';
}
        
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
    
            <item type="settings" position="label-left" labelWidth="200" inputWidth="180" offsetLeft="20" offsetTop = "30"/>

            <item type="fieldset" width="900" label="TOTAL ( '.$dayF .'-'. $monthF .'-'. $yearF.'  To  '.$dayT .'-'. $monthT .'-'. $yearT.' )">
            
                <item type="input"  label="OPENING BALANCE (CASH+BANK)" value="'.number_format($TOT_OB, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="CLOSING BALANCE (CASH+BANK)" value="'.number_format($TOT_CB, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="INCOME (TRANSFERS INCLUDED)" value="'.number_format($TRANSINC, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="EXPENSE (TRANSFERS INCLUDED)" value="'.number_format($TRANSEXP, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="INCOME " value="'.number_format($INC, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="EXPENSE " value="'.number_format($EXP, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
     
            </item>
            
            <item type="fieldset" width="900" label="REPORTS  ( '.$dayF .'-'. $monthF .'-'. $yearF.'  To  '.$dayT .'-'. $monthT .'-'. $yearT.' )">
                
                <item type="input"  label="OPENING BALANCE - CASH" value="'.number_format($COB['3'], 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="CLOSING BALANCE - CASH" value="'.number_format($COB['4'], 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                    
                <item type="input"  label="INCOME - CASH" value="'.number_format($CSH_INC, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="EXPENSE - CASH" value="'.number_format($CSH_EXP, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 

                <item type="input"  label="OPENING BALANCE - BANK" value=" '.number_format($BOB['3'], 2).'  '.$currency.'" offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="CLOSING BALANCE - BANK" value="'.number_format($BOB['4'], 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                    
                <item type="input"  label="INCOME - BANK" value="'.number_format($BNK_INC, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="EXPENSE - BANK" value="'.number_format($BNK_EXP, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 

                <item type="input"  label="OPENING STOCK" value="'.number_format($OLDSTK, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="CLOSING STOCK" value="'.number_format($CURRENTSTK, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="BUSINESS" value="'.number_format($JOB, 2).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
            </item>                

    </items>';
?>