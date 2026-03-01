<?php
error_reporting(E_ALL ^ E_NOTICE);
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/HistoryClass.php");

$HI_Obj = new HistoryClass();

$DSId = $REQUEST['DSId'];

$DS_Status = array('Suspended','Published','Just Created','Senior Approved','Deleted');
$DS_EntryStatus = array('Disabled','Enabled');


//----------------------- Get Item Details ------------------------//

$HI_Obj->getDSDetails($DSId);
$DSDetails = $HI_Obj->HistoryArray;
$DESDetCompare = $DSDetails;


//----------------------- Get Item Back Up Details ------------------------//

$HI_Obj->getDS_BkupDetails($DSId);
$DESBkupDetails = $HI_Obj->HistoryArray;
$DESBkupDetArray = $DESBkupDetails;
//print_r($DESBkupDetArray);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>';
echo '<item type="template" name="" label="DESCRIPTION HISTORY" value="" className="historyClass" offsetTop = "20" offsetLeft = "20"></item>';
echo '<item type="template" label="" value="'.$DSDetails['DS_Description'].'" className="historyClass"  offsetLeft = "40"></item>';

//array_push($DESBkupDetArray,$DESDetCompare);
//print_r($DESBkupDetArray);
$count = 0;
$DS_Items = array(           
        'DS_Description' => array('title' => 'Description','name' => 'DS_Description'),        
        'DS_Status'     => array('title' => 'Status', 'name' => 'DS_Status')
    );
foreach ($DESBkupDetArray as $key=>$value) { 
    
    if($count == 0)  $NewDSArray = $DSDetails;
    else  $NewDSArray = $OldDSArray;
        
    $OldDSArray = $value ;

    $CompareRes =  array_diff_assoc($DESDetCompare,$value);   
    $DESDetCompare = $value ; 
    itemHistory($CompareRes, $NewDSArray ,$OldDSArray, $DS_Desc, $DS_Status,$DS_Items);
    $count++;
}


function itemHistory($CompareRes, $NewDSArray, $OldDSArray, $DS_Desc, $DS_Status,$DS_Items){        
    $diff= abs(strtotime(date("Y-m-d H:i:s")) - strtotime($OldDSArray['BK_CDate']));
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
                <item type="fieldset"  label="Updated By '.$OldDSArray['BKUSName'].' '.$Date.' ago."  offsetTop="10"  >';
    
                    foreach ($CompareRes as $key => $value) {
                        
                        $oldValue = $OldDSArray[$DS_Items[$key]['name']] ? $OldDSArray[$DS_Items[$key]['name']] : '---';
                        $newValue = $NewDSArray[$DS_Items[$key]['name']] ? $NewDSArray[$DS_Items[$key]['name']] : '---';
                       
                        if($key == 'DS_Status'){
                            $oldValue = $DS_Status[$OldDSArray[$DS_Items[$key]['name']]];
                            $newValue = $DS_Status[$NewDSArray[$DS_Items[$key]['name']]];
                        }                        
                        if (array_key_exists($key,$DS_Items))
            		echo '<item type="template" name="'.$key.'" label="'.$DS_Items[$key]['title'].'" value="changed from '.$oldValue.' to '.$newValue.'" className = "titleClass"></item>';
                    }
                echo '</item>';
}
echo '</items>';
?>