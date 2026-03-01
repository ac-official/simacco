<?php
require_once("connection.php");
class BackupClass
{
	var $BackupArray;
	
	//-----------------------------------------Back up Details ----------------------------------------//
        function backupDetails($keyID,$USId,$toDB,$fromDB){
            
            $Now = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO ".$toDB." SELECT NULL , BK.*,'".$USId."','".$Now."' FROM ".$fromDB." as BK WHERE BK.".$keyID;
            mysqli_query($GLOBALS['con'],$sql);
            

//            $result = mysqli_query($GLOBALS['con'],"SELECT * FROM ".$fromDB." as BK WHERE BK.".$keyID);   
//            $BkupDetailsArray = mysqli_fetch_assoc($result);  
//            
//            foreach ($this->BK_Data as $key => $value) {
//                $BkupDetailsArray[$key] = $value;
//            }
//            
//            foreach ($BkupDetailsArray as $key => $value) {
//                if ($value == '' && ( $key == 'BS_PaidDate' || $key == 'BS_AprovdDate' ) ) {
//                   unset($BkupDetailsArray[$key]);
//                }elseif ($value == '') {
//                    $BkupDetailsArray[$key] = 0;
//                }
//            }
//            
//            array_walk_recursive($BkupDetailsArray, function(& $item, $key) {
//                if ($item == '' && $key == 'BS_PaidDate') {
//                   $item = "NULL"; echo "Pretally".$item;
//                }elseif ($item == '') {
//                    $item = 0; echo "Augmob";
//                }
//            });
//            print_r($BkupDetailsArray);die();
//            INSERT INTO `balance_sheets_bkup` SELECT NULL , BS.*,'27',CURRENT_DATE() FROM balance_sheets as BS WHERE BS.BS_Id = 3
//            $sql  = "INSERT INTO ".$toDB." ( " . implode(', ',array_keys($BkupDetailsArray)) . ") VALUES (" . "'" . implode("','", array_values($BkupDetailsArray)) . "'" . ")";
            
//            mysqli_query($GLOBALS['con'],$sql);
        }
}
?>
