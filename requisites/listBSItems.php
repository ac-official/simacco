<?php
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");

$BalSheetObj = new BalanceSheetClass();
//$BalSheetObj->viewBalanceSheetItemsCount();
$BalSheetObj->getBalanceSheetItems($preTally_user_id);
$BS_Obj = $BalSheetObj->BalanceSheetArray;

echo ' {
    userdata: { "db_table": "balance_sheets", "db_primary": "BS_Id", "db_date": "BS_MDate", "db_status" : "BS_Status" },
    head:[
        { width:40,  type:"sub_row_ajax", align:"center", sort:"na", value:""},
        { width:60,  type:"ro", align:"center", sort:"na", value:"#"},
        { width:"*", type:"ro", align:"left",   sort:"na", value:"Balance Sheet Items"},
        { width:120, type:"ro", align:"right",  sort:"na", value:"Income"},
        { width:120, type:"ro", align:"right",  sort:"na", value:"Expense"},
        { width:85, type:"ro", align:"center", sort:"na", value:"Date"},
        { width:85, type:"ro", align:"center", sort:"na", value:"Created Date"},
        { width:16,  type:"ro", align:"center", sort:"na", value:""}
    ],
    rows:[';

        if($BS_Obj) {
            $j = 1;
            foreach($BS_Obj as $rw) {
                $amount  = number_format($rw->BS_Amount,2);
                $income  = ($rw->MH_Type == '1') ? $amount : "---";
                $expense = ($rw->MH_Type == '2') ? $amount : "---";
                
                echo ' {  id:'.$rw->BS_Id .',
                userdata : { "SH_Id": "'.$rw->SH_Id.'" },    
                data:[
                    "requisites/BSItemForm.php&SHID='.$rw->SH_Id.'&BSID='.$rw->BS_Id.'&Stats='.$rw->IT_Status.'&MH_Type='.$rw->MH_Type.'",
                    "'.$j.'",
                    "'.$rw->IT_Name;
                        if($rw->DS_Description != ''){echo '-'. $rw->DS_Description;}
                        if($rw->TR_Track != ''){echo '-Track : '.$rw->TR_Track;}
                    echo '",
                    "'.$income.'",
                    "'.$expense.'",
                    "'.$rw->BS_Date.'",
                    "'.$rw->BS_CDate.'"
                ] },';
                $j++;
            }
        } else {
            echo ' {  id:0,
                data:[
                "",
                "",
                "<div style=font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px; >No records found.</div>",
                "",
                "",
                "",
                ""] }';
        }
        
       
echo ']}';
    
?>