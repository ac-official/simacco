<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once("../preTallyClass/BalanceSheetClass.php");
if($_REQUEST['srch']==1)
{
    
$frm_date    = $_REQUEST["Srch_FrmDate"];
$to_date     = $_REQUEST["Srch_ToDate"];
$item_name   = $_REQUEST["Srch_Item"];
$paid_to     = $_REQUEST["Srch_Paidto"];
$paid_from   = $_REQUEST["Srch_Paidfrm"];
$cmpny_name  = $_REQUEST["Srch_Cmpny"];
$brnch_name  = $_REQUEST["Srch_Brnch"];
$dept_name   = $_REQUEST["Srch_Dept"];
$usr_name    = $_REQUEST["Srch_UserName"];
$bnk_name    = $_REQUEST["Srch_BnkName"];
$bnk_branch  = $_REQUEST["Srch_BnkBrnch"];
$acc_no      = $_REQUEST["Srch_Accno"];
$chq_no      = $_REQUEST["Srch_Cheque"];
$vch_no      = $_REQUEST["Srch_Voucher"];
if($ACL_Obj->ACL_HR==4)
    {
        $cmpny_name= $preTally_user_ofid;
    }
if($ACL_Obj->ACL_HR==3)
    {
       $cmpny_name= $preTally_user_ofid;
        
        $dept_name=$preTally_user_dpid;
        
    }
    if($ACL_Obj->ACL_HR==2)
    {
        $cmpny_name= $preTally_user_ofid;
        $brnch_name= $preTally_user_lcid;
        
        
    }
    if($ACL_Obj->ACL_HR==1)
    {
        $cmpny_name= $preTally_user_ofid;
        $brnch_name= $preTally_user_lcid;
        $dept_name=$preTally_user_dpid;
        
    }
    if($ACL_Obj->ACL_HR==0)
    {
        $cmpny_name= $preTally_user_ofid;
        $brnch_name= $preTally_user_lcid;
        $dept_name=$preTally_user_dpid;
        $usr_name=$preTally_user_id;        
    }
$filter="";
if($frm_date!="" && $to_date!="")
{
    $filter.=" AND BS.BS_Date BETWEEN '".$frm_date."' AND '".$to_date."'";
}

elseif($frm_date)   $filter.=" AND BS.BS_Date >'".$frm_date."'";

elseif($to_date)    $filter.=" AND BS.BS_Date <'".$to_date."'";

if($item_name)  $filter.=" AND IT.IT_Id  = '".$item_name."'";

if($paid_to)    $filter.=" AND BS.BS_PaidTo =".$paid_to;

if($paid_from)  $filter.=" AND BS.BS_PadiBy =".$paid_from;

if($cmpny_name) $filter.=" AND US.OF_Id ='".$cmpny_name."'";

if($brnch_name) $filter.=" AND US.LC_Id ='".$brnch_name."'";

if($dept_name)  $filter.=" AND US.DP_Id ='".$dept_name."'";

if($usr_name)   $filter.=" AND US.US_Id =".$usr_name;

if($bnk_name)   $filter.=" AND BNK.BNK_Id =".$bnk_name;

if($bnk_branch)   $filter.=" AND BB.BB_Id =".$bnk_branch;

if($acc_no)   $filter.=" AND BA.BA_Id ='".$acc_no."'";

if($chq_no)   $filter.=" AND CHQ.CHQ_Number ='".$chq_no."'";

if($vch_no)   $filter.=" AND BS.BS_VoucherNo ='".$vch_no."'";


$BalSheetObj = new BalanceSheetClass();
$BalSheetObj->searchBSData($filter);
$BS_Obj = $BalSheetObj->SearchArray;

}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <userdata name="db_table">balance_sheets</userdata>
    <userdata name="db_primary">BS_Id</userdata>
    <userdata name="db_date">BS_Date</userdata>
    <userdata name="db_status">BS_Status</userdata>
    <head>
        <column width="60" type="ro" align="center" >SL No.</column>
        <column width="*"  type="ro" align="left" ><![CDATA[<input type="text" id="text_filter" style="width: 90%;" placeholder="Enter a name of Income or Expense to search . . .">]]></column>
        <column width="100"  type="ro" align="center" >#select_filter_strict</column>
        <column width="0"  type="ro" align="left" >#select_filter_strict</column>
        <column width="100"  type="ro" align="right" >Amount</column>
        <column width="100" type="ro" align="right" >    Date</column>
        <column width="60" type="ro" align="center">Details</column>
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
                <param>true</param><param>20</param>
            </call> 
            <call command="enableAutoWidth">
                <param>true</param>
            </call>  
        </beforeInit> 
        <afterInit>
            
        </afterInit>
    </head>';
    /*<call command="attachHeader">
        <param>SL No.,#text_filter,#select_filter_strict,Amount,Date,Details</param>
    </call>
    <call command="attachFooter">
        <param>SL No.,#text_filter,#select_filter_strict,Amount,Date,Details</param>
    </call>     */
$IE_Type = array('','Income','Expense');
    if($BS_Obj) {
        $j = 1;
        foreach($BS_Obj as $rw) {
            $amount = number_format($rw->BS_Amount,2);
            echo '
                <row id="'.$rw->BS_Id.'">
                    <cell>'.$j.'</cell>';
                    if($rw->DS_Description != '')echo '<cell>'.$rw->IT_Name.' - '.$rw->DS_Description.'</cell>';
                    if($rw->TR_Track != '')echo '<cell>'.$rw->IT_Name.' - '.$rw->TR_Track.'</cell>';
                    echo '<cell>'.$IE_Type[$rw->MH_Type].'</cell>
                    <cell>'.$rw->SH_Name.'</cell>
                    <cell>'.$amount.'</cell>
                    <cell>'.$rw->BS_Date.'</cell>
                    <cell><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" class="target_'.$rw->BS_Id.'" onclick="preTally.Settings.showSrchDetailData(this,'.$rw->BS_Id.','.$rw->SH_Id.');" />]]></cell>
                    <cell></cell>
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>