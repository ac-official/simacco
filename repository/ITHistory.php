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
        'IT_Comments'   => array('title' => 'Comments', 'name' => 'IT_Comments'),
        'IT_Approval'   => array('title' => 'Waiting For ',     'name' => 'IT_Approval'),
        'IT_Approved'   => array('title' => 'Approved User', 'name' => 'IT_Approved'),
        'IT_Business'   => array('title' => 'Business Entry Status', 'name' => 'IT_Business'),
        'IT_Transfers'  => array('title' => 'Internal Transfer Status', 'name' => 'IT_Transfers'),
        'IT_DualEntry'  => array('title' => 'Sual Entry Status', 'name' => 'IT_DualEntry'),
        'IT_DualItem'   => array('title' => 'Dual Item',     'name' => 'IT_DualItem'),
        'IT_OtherUser'  => array('title' => 'Other Branch Entry Status',  'name' => 'IT_OtherUser'), //28-05-2025,
        'IT_Status'     => array('title' => 'Status', 'name' => 'IT_Status'),
);
$IT_Status = array('Suspended','Published','Notification','Semi Approval','Deleted');
$IT_MHType = array('','Income','Expense');


//----------------------- Get Item Details ------------------------//

$HI_Obj->getITDetails($ITId);
$ITDetails = $HI_Obj->HistoryArray;
$ITDetCompare = $ITDetails;
//array_splice($ITDetCompare,19);

//----------------------- Get Item Back Up Details ------------------------//

$HI_Obj->getIT_BkupDetails($ITId);
$ITBkupDetails = $HI_Obj->HistoryArray;
$ITBkupDetArray = $ITBkupDetails;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>';
echo '<item type="template" name="" label="ITEMS HISTORY" value="" className="historyClass" offsetTop = "20" offsetLeft = "20"></item>';

array_push($ITBkupDetArray,$ITDetCompare);

foreach ($ITBkupDetArray as $key=>$value) { 
    $BKUpArray = $value;
    
    if(key($value) == 'BK_Id')    array_splice($value, 0,1);
    array_splice($value, 19);
    $BKupDetArray = array_splice($ITBkupDetArray, 19,22);

    $CompareRes =  array_diff_assoc($ITDetCompare,$value);   
    $ITDetCompare = $value ;
   
    itemHistory($CompareRes, $ITDetails, $BKUpArray, $IT_Items, $IT_Status,$IT_MHType);
}


function itemHistory($CompareRes, $ITDetails, $BKUpArray, $IT_Items, $IT_Status, $IT_MHType){
    
    $diff= abs(strtotime(date("Y-m-d H:i:s")) - strtotime($BKUpArray['BK_CDate']));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    $Date = $days .' days';
    if($days > 31) {
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $Date = $months. ' months';
    }
    if($months > 12){
        $years = floor($diff / (365*60*60*24));
        $Date = $years. ' years';
    }
    
    echo '<item type="settings" position="label-left" offsetLeft="30" width = "90%" />
                <item type="fieldset"  label="Updated By '.$BKUpArray['ITUSName'].' '.$Date.' ago."  offsetTop="10"  >';
                    foreach ($CompareRes as $key => $value) { 
                    
                        $oldValue = $BKUpArray[$IT_Items[$key]['name']];
                        $newValue = $ITDetails[$IT_Items[$key]['name']];
                        
                        if($key == 'IT_Status'){
                            $oldValue = $IT_Status[$BKUpArray[$IT_Items[$key]['name']]];
                            $newValue = $IT_Status[$ITDetails[$IT_Items[$key]['name']]];
                        }
                        
                        if($key == 'MH_Type'){
                            $oldValue = $IT_MHType[$BKUpArray[$IT_Items[$key]['name']]];
                            $newValue = $IT_MHType[$ITDetails[$IT_Items[$key]['name']]];
                        }
                        
                        
                        if (array_key_exists($key,$IT_Items))
            		echo '<item type="template" name="'.$key.'" label="'.$IT_Items[$key]['title'].'" value="changed from '.$oldValue.' to '.$newValue.'" className = "titleClass"></item>';
                    }
                echo '</item>';
}
echo '</items>';


?>



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
        'IT_Comments'   => array('title' => 'Comments', 'name' => 'IT_Comments'),
        'IT_Approval'   => array('title' => 'Waiting For ',     'name' => 'IT_Approval'),
        'IT_Approved'   => array('title' => 'Approved User', 'name' => 'IT_Approved'),
        'IT_Business'   => array('title' => 'Business Entry Status', 'name' => 'IT_Business'),
        'IT_Transfers'  => array('title' => 'Internal Transfer Status', 'name' => 'IT_Transfers'),
        'IT_DualEntry'  => array('title' => 'Sual Entry Status', 'name' => 'IT_DualEntry'),
        'IT_DualItem'   => array('title' => 'Dual Item',     'name' => 'IT_DualItem'),
        'IT_OtherUser'  => array('title' => 'Other Branch Entry Status', 'name' => 'IT_OtherUser'), //28-05-2025,
        'IT_Status'     => array('title' => 'Status', 'name' => 'IT_Status'),
);

$IT_Status = array('Suspended','Published','Notification','Semi Approval','Deleted');
$IT_MHType = array('','Income','Expense');
//----------------------- Get Balance Sheet Entry Details ------------------------//

$HI_Obj->getITDetails($ITId);
$ITDetails = $HI_Obj->HistoryArray;
$ITDetCompare = $ITDetails;
array_splice($ITDetCompare,19);

//----------------------- Get Balance Sheet Back Up Entry Details ------------------------//

$HI_Obj->getIT_BkupDetails($ITId);
$ITBkupDetails = $HI_Obj->HistoryArray;
$ITBkupDetArray = $ITBkupDetails;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>';
echo '<item type="template" name="" label="ITEMS HISTORY" value="" className="historyClass" offsetTop = "20" offsetLeft = "20"></item>';


foreach ($ITBkupDetArray as $key=>$value) { 
    $BKUpArray = $value;
    array_splice($value, 0,1);
    array_splice($value, 19);
    $BKupDetArray = array_splice($ITBkupDetArray, 19,22);

    $CompareRes =  array_diff_assoc($ITDetCompare,$value);   
    $ITDetCompare = $value ;
   
    itemHistory($CompareRes, $ITDetails, $BKUpArray, $IT_Items, $IT_Status,$IT_MHType);
}


function itemHistory($CompareRes, $ITDetails, $BKUpArray, $IT_Items, $IT_Status, $IT_MHType){
    
    $diff= abs(strtotime(date("Y-m-d H:i:s")) - strtotime($BKUpArray['BK_CDate']));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    $Date = $days .' days';
    if($days > 31) {
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $Date = $months. ' months';
    }
    if($months > 12){
        $years = floor($diff / (365*60*60*24));
        $Date = $years. ' years';
    }
    
    echo '<item type="settings" position="label-left" offsetLeft="30" width = "90%" />
                <item type="fieldset"  label="Updated By '.$BKUpArray['ITUSName'].' '.$Date.' ago."  offsetTop="10"  >';
                    foreach ($CompareRes as $key => $value) { 
                    
                        $oldValue = $BKUpArray[$IT_Items[$key]['name']];
                        $newValue = $ITDetails[$IT_Items[$key]['name']];
                        
                        if($key == 'IT_Status'){
                            $oldValue = $IT_Status[$BKUpArray[$IT_Items[$key]['name']]];
                            $newValue = $IT_Status[$ITDetails[$IT_Items[$key]['name']]];
                        }
                        
                        if($key == 'MH_Type'){
                            $oldValue = $IT_MHType[$BKUpArray[$IT_Items[$key]['name']]];
                            $newValue = $IT_MHType[$ITDetails[$IT_Items[$key]['name']]];
                        }
                        
                        
                        if (array_key_exists($key,$IT_Items))
            		echo '<item type="template" name="'.$key.'" label="'.$IT_Items[$key]['title'].'" value="changed from '.$oldValue.' to '.$newValue.'" className = "titleClass"></item>';
                    }
                echo '</item>';
}
echo '</items>';









$bkupCnt = 0;
foreach ($ITBkupDetArray as $key=>$value) { 
    
    $BKUpArray[$bkupCnt] = $value;
    if(key($value) == 'BK_Id')    array_splice($value, 0,1);
    array_splice($value, 19);
    $BKupDetArray = array_splice($ITBkupDetArray, 19,22);
    
    if($bkupCnt != 0){
        $CompareRes =  array_diff_assoc($LastItemArray,$value);  
        itemHistory($CompareRes, $ITDetails, $BKUpArray[$bkupCnt-1], $IT_Items, $IT_Status,$IT_MHType);
    }
    $LastItemArray = $value ;
    $bkupCnt ++;
}


?>