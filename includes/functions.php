<?php
function filterHR($ACL_HR, $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id) {
    $filterHR = array();
    $filterHR['OF'] = $filterHR['DP'] = $filterHR['LC'] = $filterHR['US'] = '';
    
    if($ACL_HR == 4){
        $filterHR['OF'] = ' OF_Id = '.$preTally_user_ofid;
    }
    if($ACL_HR == 3){
        $filterHR['OF'] = ' OF_Id = '.$preTally_user_ofid;
        $filterHR['DP'] = ' AND DP_Id = '.$preTally_user_dpid;
    }
    if($ACL_HR == 2){
        $filterHR['OF'] = ' OF_Id = '.$preTally_user_ofid;
        $filterHR['LC'] = ' AND LC_Id = '.$preTally_user_lcid;
    }
    if($ACL_HR == 1){
        $filterHR['OF'] = ' OF_Id = '.$preTally_user_ofid;
        $filterHR['DP'] = ' AND DP_Id = '.$preTally_user_dpid;
        $filterHR['LC'] = ' AND LC_Id = '.$preTally_user_lcid;
    }
    if($ACL_HR == 0){
        $filterHR['OF'] = ' OF_Id = '.$preTally_user_ofid;
        $filterHR['DP'] = ' AND DP_Id = '.$preTally_user_dpid;
        $filterHR['LC'] = ' AND LC_Id = '.$preTally_user_lcid;
        $filterHR['US'] = ' AND US_Id = '.$preTally_user_id;
    }
    return $filterHR;
}
function filterHR_User($ACL_HR, $prefx, $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id) {
    $filterUSR = '';
    if($prefx) $p = $prefx.'.'; else $p = '';
    
    if($ACL_HR == 4) {
        $filterUSR = ' AND '.$p.'OF_Id = '.$preTally_user_ofid;
    } else if($ACL_HR == 3) {
        $filterUSR = ' AND '.$p.'OF_Id = '.$preTally_user_ofid.' AND '.$p.'DP_Id = '.$preTally_user_dpid;
    } else if($ACL_HR == 2) {
        $filterUSR = ' AND '.$p.'OF_Id = '.$preTally_user_ofid.' AND '.$p.'LC_Id = '.$preTally_user_lcid;
    } else if($ACL_HR == 1) {
        $filterUSR = ' AND '.$p.'OF_Id = '.$preTally_user_ofid.' AND '.$p.'LC_Id = '.$preTally_user_lcid.' AND '.$p.'DP_Id = '.$preTally_user_dpid;
    } else if($ACL_HR == 0) {
        $filterUSR = ' AND '.$p.'OF_Id = '.$preTally_user_ofid.' AND '.$p.'LC_Id = '.$preTally_user_lcid.' AND '.$p.'DP_Id = '.$preTally_user_dpid.' AND '.$p.'US_Id = '.$preTally_user_id;
    }
    return $filterUSR;
}
function filterBS_User($ACL_BS, $prefx, $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id) {
    $filterUSR = '';
    if($prefx) $p = $prefx.'.'; else $p = '';
    
    if($ACL_BS == 4) {
        $filterUSR = $p.'OF_Id = '.$preTally_user_ofid.' AND ' ;
    } else if($ACL_BS == 3) {
        $filterUSR = $p.'OF_Id = '.$preTally_user_ofid.' AND '.$p.'DP_Id = '.$preTally_user_dpid.' AND ';
    } else if($ACL_BS == 2) {
        $filterUSR = $p.'OF_Id = '.$preTally_user_ofid.' AND '.$p.'LC_Id = '.$preTally_user_lcid.' AND ';
    } else if($ACL_BS == 1) {
        $filterUSR = $p.'OF_Id = '.$preTally_user_ofid.' AND '.$p.'LC_Id = '.$preTally_user_lcid.' AND '.$p.'DP_Id = '.$preTally_user_dpid.' AND ';
    } else if($ACL_BS == 0) {
        $filterUSR = $p.'OF_Id = '.$preTally_user_ofid.' AND '.$p.'LC_Id = '.$preTally_user_lcid.' AND '.$p.'DP_Id = '.$preTally_user_dpid.' AND '.$p.'US_Id = '.$preTally_user_id.' AND ';
    }
    return $filterUSR;
}
function multi_array_search($search_for, $search_in) {
    foreach ($search_in as $element) {
        if ( ($element === $search_for) || (is_array($element) && multi_array_search($search_for, $element)) ){
            return true;
        }
    }
    return false;
}
if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}
function generate_reptIDS($preTally_user_ofid,$preTally_user_id,$usrArray)
{    
    if(!isset($_SESSION['user_report_id']) && empty($_SESSION['user_report_id'])) {             
    $US_Obj =$usrArray;   
    function objectToArray ($object) {
        if(!is_object($object) && !is_array($object))
            return $object;

        return array_map('objectToArray', (array) $object);
    }
    
    function keyVal($array, $key, $value) {
        $results = array();  
        if (is_array($array)) { 
            if (isset($array[$key]) && $array[$key] == $value) { 
                $results[] = $array; 
            }
            foreach ($array as $subarray) {
                $results = array_merge($results, keyVal($subarray, $key, $value));
            }
        }
        return $results;
    }
    
    function hierarchyTree($US_Ary,$usRpt = 1) {    
        $resAry = array();
        $resAry = keyVal($US_Ary, 'US_Report', $usRpt);
        if($resAry){
            foreach ($resAry as $key => $value) { 
                $tmp .= $value['US_Id'].',';
                if($value['US_Id'] != 1) 
                    $tmp .= hierarchyTree($US_Ary,$value['US_Id']); 

            }
        }
        return $tmp;
    }
    
    $US_Ary = objectToArray($US_Obj);
    $Rprtid .= hierarchyTree($US_Ary,$preTally_user_id);
    $Rprtid .=$preTally_user_id;
//    $Rprtid =10;
    $_SESSION['user_report_id']	= $Rprtid ;
    
}
}
function arrayFlatten($array) {
 $flattened_array = array();
    array_walk_recursive($array, function($a) use (&$flattened_array) { $flattened_array[] = $a; });
    return $flattened_array;
  } 
?>