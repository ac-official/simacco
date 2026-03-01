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

$filterOB = $filter = 'OF_Id = '.$preTally_user_ofid.' ';
$cfields = 'SUM(OB_OpenBal) AS OB';
$bfields = 'SUM(BnkOB_OpenBal) AS OB';
$sfields = 'SUM(OS_OpenBal) AS OS';

$CashOBObj->reportVisualDetails($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$CRP_Obj = $CashOBObj->CashBSArray;
//print_r($CRP_Obj);
$INC = $EXP = $CSH_INC = $CSH_EXP = $BNK_INC = $BNK_EXP = $CSH_TRANS_INC = $CSH_TRANS_EXP = $BNK_TRANS_INC = $BNK_TRANS_EXP = 0;

foreach ($CRP_Obj as $key => $value) {
//    if( $value->IT_Transfers == 0 && $value->MH_Type == 1 ){
//        $INC += $value->IE;
//    }
//    if( $value->IT_Transfers == 0 && $value->MH_Type == 2 ) {
//        $EXP += $value->IE;
//    }
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

$bfilter = 'US.OF_Id = '.$preTally_user_ofid.' ';

$BankOBObj->getBankOpeningBalance($REQUEST['f'],$REQUEST['t'],$bfields, $filterOB, $bfilter);
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
    $yearT=' Till now';
}
        
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
    
            <item type="settings" position="label-left" labelWidth="200" inputWidth="180" offsetLeft="20" offsetTop = "30"/>

            <item type="fieldset" width="900" label="TOTAL ( '.$dayF .'-'. $monthF .'-'. $yearF.'  To  '.$dayT .'-'. $monthT .'-'. $yearT.' )">
            
                <item type="input"  label="OPENING BALANCE (CASH+BANK)" value="'.round($TOT_OB).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="CLOSING BALANCE (CASH+BANK)" value="'.  round($TOT_CB).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="INCOME (TRANSFERS INCLUDED)" value="'.round($TRANSINC).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="EXPENSE (TRANSFERS INCLUDED)" value="'.round($TRANSEXP).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="INCOME " value="'.round($INC).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="EXPENSE " value="'.round($EXP).'  '.$currency.' " offsetTop="10" readonly = "true" ></item> 
     
            </item>
            
            <item type="fieldset" width="900" label="REPORTS  ( '.$dayF .'-'. $monthF .'-'. $yearF.'  To  '.$dayT .'-'. $monthT .'-'. $yearT.' )">
                
                <item type="input"  label="OPENING BALANCE - CASH" value="'.round($COB['3']).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="CLOSING BALANCE - CASH" value="'.round($COB['4']).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                    
                <item type="input"  label="INCOME - CASH" value="'.round($CSH_INC).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="EXPENSE - CASH" value="'.round($CSH_EXP).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 

                <item type="input"  label="OPENING BALANCE - BANK" value="'.round($BOB['3']).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="CLOSING BALANCE - BANK" value="'.round($BOB['4']).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                    
                <item type="input"  label="INCOME - BANK" value="'.round($BNK_INC).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="EXPENSE - BANK" value="'.round($BNK_EXP).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 

                <item type="input"  label="OPENING STOCK" value="'.round($OLDSTK).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="CLOSING STOCK" value="'.round($CURRENTSTK).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
                <item type="input"  label="BUSINESS" value="'.round($JOB).'  '.$currency.' " offsetTop="10" readonly = "true" ></item>
                    <item type="newcolumn" offsetLeft="15" /> 
            </item>                

    </items>';

//echo '<items>	
//        <item type="settings" position="label-left" labelWidth="0" inputWidth="180" offsetLeft="20"/>
//        
//        <item type="block" width="600"> 
//        
//            <item type="fieldset" inputWidth="auto" label="CASH OPENING BALANCE">
//                <item type="input" name="INCOME" label="" value=" '.$currency.' '.$COB['3'].'" offsetTop="5" required="true" ></item>
//            </item>
//
//            <item type="newcolumn" offsetLeft="5" /> 
//            
//            <item type="fieldset" inputWidth="auto" label="CASH CLOSING BALANCE">
//                <item type="input" name="INCOME" label="" value=" '.$currency.' '.$COB['4'].'" offsetTop="5" required="true" ></item>
//            </item>
//
//            <item type="newcolumn" /> 
//            
//            <item type="fieldset" inputWidth="auto" label="BANK OPENING BALANCE" labelWidth="0">
//                <item type="input" name="EXPENSE" label="" value=" '.$currency.' '.$BOB['3'].'" offsetTop="5" required="true" ></item> 
//            </item>
//            
//            <item type="newcolumn" /> 
//            
//            <item type="fieldset" inputWidth="auto" label="BANK CLOSING BALANCE" labelWidth="0">
//                <item type="input" name="EXPENSE" label="" value=" '.$currency.' '.$BOB['4'].'" offsetTop="5" required="true" ></item> 
//            </item>
//            
//            <item type="newcolumn" /> 
//
//            <item type="fieldset" inputWidth="auto" label="OLD STOCK">
//                <item type="input" name="INCOME" label="" value=" '.$currency.' '.$SRP_Obj['Business'].'" offsetTop="5" required="true" ></item>
//            </item>
//
//            <item type="newcolumn" /> 
//            
//            <item type="fieldset" inputWidth="auto" label="CURRENT STOCK">
//                <item type="input" name="INCOME" label="" value=" '.$currency.' '.$stock.'" offsetTop="5" required="true" ></item>
//            </item>
//
//            <item type="newcolumn" /> 
//            
//            <item type="fieldset" inputWidth="auto" label="INCOME" labelWidth="0">
//                <item type="input" name="EXPENSE" label="" value=" '.$currency.' '.$INC.'" offsetTop="5" required="true" ></item> 
//            </item>
//            
//            <item type="newcolumn" /> 
//            
//            <item type="fieldset" inputWidth="auto" label="EXPENSE" labelWidth="0">
//                <item type="input" name="EXPENSE" label="" value=" '.$currency.' '.$EXP.'" offsetTop="5" required="true" ></item> 
//            </item>
//            
//        </item>
//    </items>';
?>