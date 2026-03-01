<?php
error_reporting(E_ALL ^ E_NOTICE);
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/HistoryClass.php");

$HI_Obj = new HistoryClass();

$ITId = $REQUEST['ITId'];

$IT_Items = array(
    
        'US_Id'         => array('title' => 'Added by', 'name' => 'USName'),
        'IT_Name'       => array('title' => 'Item Name','name' => 'IT_Name'),
        'SH_Id'         => array('title' => 'Subhead' , 'name' => 'SH_Name'),
        'MH_Type'       => array('title' => 'Type Of Entry','name' => 'MH_Type'),
        'IT_Comments'   => array('title' => 'Remarks', 'name' => 'IT_Comments'),
        'IT_Approval'   => array('title' => 'Waiting For ',     'name' => 'IT_Approval'),
        'IT_Approved'   => array('title' => 'Approved User', 'name' => 'IT_Approved'),
        'IT_Business'   => array('title' => 'Business Entry Status', 'name' => 'IT_Business'),
        'IT_Transfers'  => array('title' => 'Internal Transfer Status', 'name' => 'IT_Transfers'),
        'IT_DualEntry'  => array('title' => 'Dual Entry Status', 'name' => 'IT_DualEntry'),
        'IT_DualItem'   => array('title' => 'Dual Item',     'name' => 'DualItemName'),
        'IT_OtherUser'  => array('title' => 'Other Branch Entry Status',     'name' => 'IT_OtherUser'), //28-05-2025
        'IT_Status'     => array('title' => 'Status', 'name' => 'IT_Status'),
);
$IT_Status = array('Suspended','Published','Just Created','Senior Approved','Deleted');
$IT_MHType = array('','Income','Expense');
$IT_EntryStatus = array('Disabled','Enabled');


//----------------------- Get Item Details ------------------------//

$HI_Obj->getITDetails($ITId);
$ITDetails = $HI_Obj->HistoryArray;
$ITDetCompare = $ITDetails;
array_splice($ITDetCompare,19);

//----------------------- Get Item Back Up Details ------------------------//

$HI_Obj->getIT_BkupDetails($ITId);
$ITBkupDetails = $HI_Obj->HistoryArray;
$ITBkupDetArray = $ITBkupDetails;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>';
echo '<item type="template" name="" label="ITEM HISTORY" value="" className="historyClass" offsetTop = "20" offsetLeft = "20"></item>';
echo '<item type="template" label="" value="'.$ITDetails['IT_Name'].'" className="historyClass"  offsetLeft = "40"></item>';

//array_push($ITBkupDetArray,$ITDetCompare);

$count = 0;
foreach ($ITBkupDetArray as $key=>$value) { 
    
    if($count == 0)  $NewItemArray = $ITDetails;
    else  $NewItemArray = $OldItemArray;
        
    $OldItemArray = $value ;
    
    
//    $BKUpArray = $value;  // Replaced by $OldItemArray & $ITDetails Replaced by $NewItemArray
    array_splice($value, 0,1);
    array_splice($value, 19);
    array_splice($value, 19,22);

    $CompareRes =  array_diff_assoc($ITDetCompare,$value);   
    $ITDetCompare = $value ;
   
    itemHistory($CompareRes, $NewItemArray ,$OldItemArray, $IT_Items, $IT_Status,$IT_MHType, $IT_EntryStatus);
    $count++;
}


function itemHistory($CompareRes, $NewItemArray, $OldItemArray, $IT_Items, $IT_Status, $IT_MHType, $IT_EntryStatus){
    
    $diff= abs(strtotime(date("Y-m-d H:i:s")) - strtotime($OldItemArray['BK_CDate']));
    $days = floor(($diff)/ (60*60*24));
    $Date = $days .' days';
    if($days <= 0){
        $hours = floor(($diff ) / (60*60));
        $Date  = $hours. ' hours';
    }else if($days > 31) {
        $months = floor(($diff ) / (30*60*60*24));
        $Date = $months. ' months';
    } 
    if($months > 12){
        $years = floor($diff / (365*60*60*24));
        $Date = $years. ' years';
    }
    
    echo '<item type="settings" position="label-left" offsetLeft="30" width = "90%" />
                <item type="fieldset"  label="Updated By '.$OldItemArray['ITUSName'].' '.$Date.' ago."  offsetTop="10"  >';
    
                    foreach ($CompareRes as $key => $value) {
                    
                        $oldValue = $OldItemArray[$IT_Items[$key]['name']] ? $OldItemArray[$IT_Items[$key]['name']] : '---';
                        $newValue = $NewItemArray[$IT_Items[$key]['name']] ? $NewItemArray[$IT_Items[$key]['name']] : '---';
                        
                        if($key == 'IT_Status'){
                            $oldValue = $IT_Status[$OldItemArray[$IT_Items[$key]['name']]];
                            $newValue = $IT_Status[$NewItemArray[$IT_Items[$key]['name']]];
                        }
                        
                        if($key == 'MH_Type'){
                            $oldValue = $IT_MHType[$OldItemArray[$IT_Items[$key]['name']]];
                            $newValue = $IT_MHType[$NewItemArray[$IT_Items[$key]['name']]];
                        }
                        
                        if($key == 'IT_Business' || $key == 'IT_Transfers' || $key == 'IT_DualEntry'){
                            $oldValue = $IT_EntryStatus[$OldItemArray[$IT_Items[$key]['name']]];
                            $newValue = $IT_EntryStatus[$NewItemArray[$IT_Items[$key]['name']]];
                        }
                        
                        
                        if (array_key_exists($key,$IT_Items))
            		echo '<item type="template" name="'.$key.'" label="'.$IT_Items[$key]['title'].'" value="changed from '.$oldValue.' to '.$newValue.'" className = "titleClass"></item>';
                    }
                echo '</item>';
}
echo '</items>';


?>