<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/ExportExcelClass.php");
require_once($BASEPATH . "preTallyClass/ZoneClass.php");
$ExcelObj = new ExportExcelClass();
$ZoneObj = new ZoneClass();
$filterValues = $_REQUEST['filter'];
$ACLReq = $ACL_Obj->ACL_BSheet;
if ( $filterValues['report_type'] == "stocks") {
    $temp = explode('-', $filterValues['r']);
    $OFkeyTemp = explode('_', $temp['0']);
    $LCkeyTemp = explode('_', $temp['1']);

    if (($temp['1'])) {
        $filter = 'LC_Id = ' . $LCkeyTemp[1] . ' ';
    } else if ($ACLReq == 2) {
        $filter = 'LC_Id = ' . $preTally_user_lcid . ' ';
    } else {
        $filter = 'OF_Id = ' . $OFkeyTemp[1] . ' ';
    }
} else if ($filterValues['report_type'] == "transaction" || $filterValues['report_type'] == "business") {
    $OFkeyTemp = $REQUEST['OFID'];
    $LCkeyTemp = $REQUEST['LCID'];
    $ZNkeyTemp = $REQUEST['ZNID'];
    if ($REQUEST['ZNID'] != 'null' && $REQUEST['ZNID'] != 'All' && $REQUEST['ZNID'] != 'null' && $REQUEST['ZNID'] != '' && $REQUEST['LCID'] == 'All') {
        $znlcid = $ZoneObj->getZoneLocations($REQUEST['ZNID']);
        $filter = 'LC_Id IN (' . $znlcid . ')';
    } else if ($REQUEST['LCID'] && $REQUEST['LCID'] != 'All') {
        $filter = 'LC_Id =' . $REQUEST['LCID'] . ' ';
    } else if ($ACLReq == 2) {
        $filter = 'LC_Id = ' . $preTally_user_lcid . ' ';
    } else {
        $filter = 'OF_Id = ' . $OFkeyTemp . ' ';
    }
}

$data = array();
$IE_Type = array('', 'Income', 'Expense');
$headerArray = array();
$BS_Status = array('', 'Approved', 'Waiting for Approval', 'Rejected');
if ($filterValues['report_type'] == "business" || $filterValues['report_type'] == "transaction" || $filterValues['report_type'] == "bankbalancesheet" || $filterValues['report_type'] == "branchbalancesheet" || $filterValues['report_type'] == "hierarchy" || $filterValues['report_type'] == "mybankbook") {
    $headerArray['Type'] = "Type";
    $headerArray['Name'] = "Name of income/expense";
    $headerArray['Desc'] = "Description";
    $headerArray['T_Id'] = "Track ID";
    $headerArray['Amount'] = "Amount";
    if ($filterValues['report_type'] == "branchbalancesheet" || $filterValues['report_type'] == "bankbalancesheet" || $filterValues['report_type'] == "mybankbook") {
        $headerArray['PaidBranch'] = "Paid Branch";
        $headerArray['CrtdBranch'] = "Created Branch";
    } else {
        $headerArray['Branch'] = "Branch";
    }
    if (($filterValues['report_type'] != "bankbalancesheet") && ($filterValues['report_type'] != "hierarchy" ))
        $headerArray['AddedBy'] = "Added By";
    $headerArray['Date'] = "Added On";

    if ($filterValues['report_type'] == "mybankbook") {
        $headerArray['ApprvdOn'] = "Approved/Rejected Date";
        $headerArray['Status'] = "Status";
        $headerArray['BankAccount'] = "Bank Account";
    }
} else if ($filterValues['report_type'] == "stocks") {
    $headerArray['Track'] = "Track ID";
    $headerArray['Business'] = "Business";
    $headerArray['Amount_Received'] = "Amount Received";
    if ($ACL_Obj->ACL_MasterReports == 1)
        $headerArray['Amount_Spend'] = "Amount Spend";
    $headerArray['Current_Stock'] = "Current Stock";
    $headerArray['Branch'] = "Branch";
    $headerArray['AddedBy'] = "Added By";
    $headerArray['Date'] = "Added On";
}

$filter_mask = 'AND 1 ';
if ($filterValues['TypeFilter']) {
    //$filter_mask .= ' AND IT.MH_Type = "' . $filterValues['TypeFilter'] . '" ';
    //change the old fileter and new added below 19-05-2025
    $mytypary = explode('-',$filterValues['TypeFilter']);
    if (isset($mytypary[1]) && $mytypary[0] != 0) {
        $filter_mask .= ' AND IT.MH_Type = "'.$mytypary[0].'" ';
        if ($mytypary[1] == 1) { // expecet internal 
            $filter_mask .= ' AND IT.IT_Transfers = "0"';
        } else if ($mytypary[1] == 2) { // internal only
            $filter_mask .= ' AND IT.IT_Transfers = "1"';
        } 
    } else if($mytypary[0] != 0) {
        $filter_mask .= ' AND IT.MH_Type = "'.$mytypary[0].'" ';
    }   
}

if ($filterValues['ItemNameFilter']) {
    //$filter_mask .= ' AND IT.IT_Name like "' . $filterValues['ItemNameFilter'] . '%"';
    // change the old filter and enter new below 16-05-2025
    $filter_mask .=' AND (IT.IT_Name like "%'.$filterValues['ItemNameFilter'].'%" OR DS.DS_Description like "%'.$filterValues['ItemNameFilter'].'%" OR TR.TR_Track like "%'.$filterValues['ItemNameFilter'].'%" )' ;
}

if ($filterValues['BranchFilter']) {
    // if($filterValues['report_type'] == "stocks") {
      //$filter_mask .=' AND ((LC.LC_Name like "'.$filterValues['BranchFilter'].'%"  AND IT.IT_Business = 1) '
      //. ' OR  (IT.IT_Business = 0))   ';
      //} else 
    if ($filterValues['report_type'] == "mybankbook")
        $filter_mask .= ' AND BS_LC.LC_Name like "' . $filterValues['BranchFilter'] . '%"';
    else
        $filter_mask .= ' AND LC.LC_Name like "' . $filterValues['BranchFilter'] . '%"';
}

if ($filterValues['AddedByFilter']) {
    $filter_mask .= ' AND CONCAT(US_FName," ",US_LName) like "' . $filterValues['AddedByFilter'] . '%" ';
}

if ($filterValues['TrackIDFilter']) {
    $filter_mask .= ' AND TR.TR_Track like "' . $filterValues['TrackIDFilter'] . '%" ';
}

if ($filterValues['amount'] != '') {
    $filter_mask .= "AND BS.BS_Amount = '" . $filterValues['amount'] . "'";
}

if ($filterValues['shCRF'] && ($filterValues['report_type'] == "bankbalancesheet" || $filterValues['report_type'] == "hierarchy")) {
    $filter_mask .= "AND SH.SH_Name like '" . $filterValues['shCRF'] . "%'";
} else if ($filterValues['shCRF']) {
    $filter_mask .= ' AND IT.SH_Id = "' . $filterValues['shCRF'] . '" ';
}

if ($filterValues['statusFilter']) {  // my bank book
    $filter_mask .= ' AND BS.BS_Status = ' . $filterValues['statusFilter'];
}

//echo $filter_mask;
if ($filterValues['report_type'] == "business") { 
    $ExcelResult = $ExcelObj->exportreportBusinessData($filterValues['r'], $filterValues['From_Date'], $filterValues['To_Date'], $filter, $filter_mask);
    $COC_Obj = $ExcelObj->BusinessArray;
} else if ($filterValues['report_type'] == "transaction") {
    $ExcelResult = $ExcelObj->reportCashBSData($filterValues['r'], $filterValues['From_Date'], $filterValues['To_Date'], $filter, $filter_mask);
    $COC_Obj = $ExcelObj->CashBSArray;
} else if ($filterValues['report_type'] == "bankbalancesheet") {

    $temp = explode('-', $filterValues['r']);
    $keyTemp = explode('_', $temp['0']);

    if (($keyTemp['0']) == 'BA') {
        $filter = 'BS.BA_Id = ' . $keyTemp[1] . ' ';
    } else if ($ACLReq == 2) {
        $filter = 'BS.BA_Id IN  ( SELECT BA_Id FROM bank_accounts WHERE LC_Id = ' . $preTally_user_lcid . ' ) ';
    } else {
        $filter = 'US.OF_Id = ' . $keyTemp[1] . ' ';
    }

    $ExcelResult = $ExcelObj->reportBankBSData($filterValues['r'], $filterValues['From_Date'], $filterValues['To_Date'], $filter, $filter_mask);
    $COC_Obj = $ExcelObj->BankBSArray;
} else if ($filterValues['report_type'] == "branchbalancesheet") {
    $temp = explode('-', $filterValues['r']);
    $OFkeyTemp = explode('_', $temp['0']);
    $LCkeyTemp = explode('_', $temp['1']);

    if (($temp['1'])) {
        $filter = ' BA.LC_Id = ' . $LCkeyTemp[1] . ' ';
    } else if ($ACLReq == 2) {
        $filter = 'BS.LC_Id = ' . $preTally_user_lcid . ' ';
    } else {
        $filter = 'LC.OF_Id = ' . $OFkeyTemp[1] . ' ';
    }
    //echo $filter;
    $filterInner = end(explode('-', $filterValues['r']));
    $innerFilter = explode('_', $filterInner);
    if ($innerFilter[0] == 'BA') {
        $filter_mask = ' AND BA.BA_ID = ' . $innerFilter[1];
    }
    //echo $filter_mask;
    $ExcelResult = $ExcelObj->reportBranchBSData($filterValues['r'], $filterValues['From_Date'], $filterValues['To_Date'], $filter, $filter_mask);
    $COC_Obj = $ExcelObj->BranchBSArray;
} else if ($filterValues['report_type'] == "stocks") {
    $ExcelResult = $ExcelObj->reportStockData($filterValues['r'], $filterValues['From_Date'], $filterValues['To_Date'], $filter, $filter_mask);
    $COC_Obj = $ExcelObj->StockArray;
} elseif ($filterValues['report_type'] == "hierarchy") {

    $newParm = '';
    $H_ID = base64_decode($_REQUEST['ID']);

    if ($H_ID) {
        $newParm = $H_ID;
    } else {
        $temp = explode('-', $REQUEST['r']);
        $keyTemp = explode('_', $temp['0']);

        foreach ($temp as $value) {
            $newTemp = explode('_', $value);
            $newFilt .= $newTemp['0'] . "_Id =  '" . $newTemp['1'] . "' AND ";
        }
        if ($ACL_Obj->ACL_BSheet != 5)
            $newFilt .= filterBS_User($ACL_Obj->ACL_BSheet, '', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
        $newFilt .= '1';
        $newParm = "SELECT US_Id FROM `users_auth` WHERE " . $newFilt." AND US_Status != 5";
    }
    $ExcelResult = $ExcelObj->reportBSDataFilter($filterValues['r'], $filterValues['From_Date'], $filterValues['To_Date'], $newParm, $filter_mask);
    $COC_Obj = $ExcelObj->ReportArray;
} else if ($filterValues['report_type'] == "mybankbook") {

    if ($ACLReq == 2) {     // Self Office
        $filter = ' BS.LC_Id = ' . $preTally_user_lcid . '  ';
    } else if ($ACLReq == 4) {  // Self Company
        $filter = ' BS_LC.OF_Id = ' . $preTally_user_ofid . '  ';
    } else {
        $filter = ' BS.US_Id = ' . $preTally_user_id;
    }
    $ExcelResult = $ExcelObj->reportBankBookBSData($filterValues['From_Date'], $filterValues['To_Date'], $filter, $filter_mask);
    $COC_Obj = $ExcelObj->BankBSArray;
}

foreach ($COC_Obj as $option) {
    $arrayStr = array();
    $nameStr = "";

    if ($filterValues['report_type'] == "business" || $filterValues['report_type'] == "transaction" || $filterValues['report_type'] == "bankbalancesheet" || $filterValues['report_type'] == "branchbalancesheet" || $filterValues['report_type'] == "hierarchy" || $filterValues['report_type'] == "mybankbook") {
        $arrayStr['Type'] = $IE_Type[$option->MH_Type];
        $arrayStr['Name'] = $option->IT_Name;
        if ($option->DS_Description)
            $arrayStr['Desc'] = htmlspecialchars_decode($option->DS_Description, ENT_QUOTES);
        else
            $arrayStr['Desc'] = "";

        if ($option->TR_Track)
            $arrayStr['T_Id'] = $option->TR_Track;
        else
            $arrayStr['T_Id'] = "";

        $arrayStr['Amount'] = number_format($option->BS_Amount, 2, '.', '');
        if ($filterValues['report_type'] == "branchbalancesheet" || $filterValues['report_type'] == "bankbalancesheet" || $filterValues['report_type'] == "mybankbook") {
            $arrayStr['PaidBranch'] = $option->PaidBranch;
            $arrayStr['CrtdBranch'] = $option->LC_Name;
        } else {
            //if($filterValues['report_type'] == "stocks")
            //    echo ($option->IT_Business == 1) ? $option->LC_Name : "--";
            //else 
            $arrayStr['Branch'] = $option->LC_Name;
        }
        if (($filterValues['report_type'] != "bankbalancesheet") && ($filterValues['report_type'] != "hierarchy"))
            $arrayStr['AddedBy'] = $option->US_FName . ' ' . substr($option->US_LName, 0, 1);

        if ($filterValues['report_type'] == "mybankbook") {
            $arrayStr['Date'] = $option->BS_CDate;
            $arrayStr['ApprvdOn'] = $option->BS_Date;
            $arrayStr['Status'] = $BS_Status[$option->BS_Status];
            $arrayStr['BankAccount'] = $option->bank_acc_name;
        } else
            $arrayStr['Date'] = $option->BS_Date;
    } else if ($filterValues['report_type'] == "stocks") {
        $arrayStr['Track'] = $option['TR_Track'];
        $arrayStr['Business'] = number_format($option['Business'], 2, '.', '');
        $arrayStr['Amount_Received'] = number_format($option['Income'], 2, '.', '');
        if ($ACL_Obj->ACL_MasterReports == 1)
            $arrayStr['Amount_Spend'] = number_format($option['Expense'], 2, '.', '');

        $arrayStr['Current_Stock'] = number_format(($option['Business'] - $option['Income']), 2, '.', '');
        $arrayStr['Branch'] = $option['LC_Name'];
        $arrayStr['AddedBy'] = $option['US_FName'] . ' ' . substr($option['US_LName'], 0, 1);
        $arrayStr['Date'] = $option['BS_Date'];
    }

    $data[] = array_map('trim', $arrayStr);
}

echo $ExcelObj->createExcel($data, $headerArray);
exit;
?>