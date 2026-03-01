<?php
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
$vch_no       = $_REQUEST["Srch_Voucher"];
$filter="";
if($frm_date)   $filter.=" AND BS_Date >".$frm_date;

if($to_date)    $filter.=" AND BS_Date <".$to_date;

if($item_name)  $filter.=" AND IT_Name  =".$item_name;

if($paid_to)    $filter.=" AND BS_PaidTo =".$paid_to;

if($paid_from)  $filter.=" AND BS_PadiBy =".$paid_from;

if($cmpny_name) $filter.=" AND OF_Id =".$cmpny_name;

if($brnch_name) $filter.=" AND LC_Id =".$brnch_name;

if($dept_name)  $filter.=" AND Dept_Id =".$dept_name;

if($usr_id)   $filter.=" AND US_Id =".$usr_id;

if($bnk_name)   $filter.=" AND BNK_Id =".$bnk_name;

if($bnk_branch)   $filter.=" AND BB_Id =".$bnk_branch;

if($acc_no)   $filter.=" AND BA_Id =".$acc_no;

if($chq_no)   $filter.=" AND CHQ_Number =".$chq_no;

if($vch_no)   $filter.=" AND BS_VoucherNo =".$vch_no;
echo $filter;
//$BalSheetObj->searchBSItems($filter);
?>