<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH."preTallyClass/UserBranchReportClass.php");
include_once($BASEPATH . "preTallyClass/ZoneClass.php");
$CashObj = new UserBranchReportClass(); 
$ZoneObj = new ZoneClass();
$filter="OF_Id=".$preTally_user_ofid;
if($REQUEST['znid']!='null' && $REQUEST['znid']!='All' && $REQUEST['znid']!='null' && $REQUEST['znid']!='')
{
$znlcid=$ZoneObj->getZoneLocations($REQUEST['znid']);
$filter.= " AND BS.BS_IEByLC IN (".$znlcid.") ";
}else if($ACL_Obj->ACL_BSheet==2){
$filter.= " AND BS.BS_IEByLC = ".$preTally_user_lcid." ";    
}
$CashObj->reportMRVisualDetails($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter); 
$CRP_Obj = $CashObj->CashBSArray;
$ITrans_Received=0;
$ITrans_Paid=0;
$Business_Recvd=0;
$Business_Return=0;
$Income=0;
$Expense=0;
foreach ($CRP_Obj as $key => $value) {
    if( $value->IT_Transfers == 1 && $value->MH_Type == 1 ){        
    $ITrans_Received  += $value->IE ;
    }
    if( $value->IT_Transfers == 1 && $value->MH_Type == 2 ){        
    $ITrans_Paid  += $value->IE ;
    }
    if( $value->MH_Type == 1 && $value->IT_Business == 1 ){        
    $Business_Recvd  += $value->IE ;
    }
    if( $value->MH_Type == 2 && $value->IT_Business == 1 ){        
    $Business_Return  += $value->IE ;
    }
    if( $value->MH_Type == 1 && $value->IT_Business == 0 && $value->IT_Transfers ==0 ){        
    $Income  += $value->IE ;
    }
    if( $value->MH_Type == 2 && $value->IT_Business == 0 && $value->IT_Transfers ==0 ){        
    $Expense  += $value->IE ;
    }
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>		
		<head>
			<column width="50" type="ro" align="center" sort="na"> SlNo </column>
			<column width="*" type="ro" align="left" sort="na">Type</column>
                        <column width="0" type="ro" align="left" sort="na">Inc</column>
                        <column width="*" type="ro" align="right" sort="na"><![CDATA[<div style="text-align:left;"> Amount <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="2" class="mrr_Sort_Visual"/></div>]]></column>
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
                            echo '<row id="1">                                
                                <cell>1</cell>
                                <cell name="Type">Income</cell>
                                <cell name="Income_Amount">'.round($Income).'</cell>
                                <cell name="Income_Amount">'.number_format(round($Income),2).'</cell>';
                            echo '</row>';
                            echo '<row id="2">                                
                                <cell>2</cell>
                                <cell name="LC_Name">Expense</cell>
                                <cell name="Income_Amount">'.round($Expense).'</cell>
                                <cell name="Expense_Amount">'.number_format(round($Expense),2).'</cell>';
                            echo '</row>';
                            echo '<row id="3">                                
                                <cell>3</cell>
                                <cell name="BUSS_REC">Business Received</cell>
                                <cell name="Income_Amount">'.round($Business_Recvd).'</cell>
                                <cell name="BusinessRec_Amount">'.number_format(round($Business_Recvd),2).'</cell>';
                            echo '</row>';
                            echo '<row id="4">                                
                                <cell>4</cell>
                                <cell name="BUSS_RET">Business Returned</cell>
                                <cell name="Income_Amount">'.round($Business_Return).'</cell>
                                <cell name="BusinessRet_Amount">'.number_format(round($Business_Return),2).'</cell>';
                            echo '</row>';
                            echo '<row id="5">                                
                                <cell>5</cell>
                                <cell name="ITRec">Internal Transfer Received</cell>
                                <cell name="Income_Amount">'.round($ITrans_Received).'</cell>
                                <cell name="IT_recAmt">'.number_format(round($ITrans_Received),2).'</cell>';
                            echo '</row>';
                            echo '<row id="6">                                
                                <cell>6</cell>
                                <cell name="ITPaid">Internal Transfer Paid</cell>
                                <cell name="Income_Amount">'.round($ITrans_Paid).'</cell>
                                <cell name="IT_paidAmt">'.number_format(round($ITrans_Paid),2).'</cell>';
                            echo '</row>';
                        
                    
		  
echo '</rows>';
?>