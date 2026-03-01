<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "includes/functions.php");
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
require_once($BASEPATH . "preTallyClass/LocationClass.php");
require_once($BASEPATH . "preTallyClass/DepartmentClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

$GeneralObj     = new GeneralClass();
$LocationObj    = new LocationClass();
$UserObj        = new UserClass();
$DepartmentObj  = new DepartmentClass();
$resign_stats=$UserObj->getResignStats($preTally_user_ofid);
$filterHR['OF'] = 1;
if($REQUEST['f'] == 'HR') {
    $ACLReq = $ACL_Obj->ACL_HR;
}
if($REQUEST['f'] == 'BS') {
    $ACLReq = $ACL_Obj->ACL_BSheet;
}

if($ACLReq != 5) 
    $filterHR = filterHR($ACLReq, $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);

$GeneralObj->ViewDetails(' OF_Id, OF_Name ', 'offices', $filterHR['OF'] ,' OF_Name ');
$OF_Obj = $GeneralObj->DataArray;
//print_r($OF_Obj);

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
function objectToArray ($object) {
    if(!is_object($object) && !is_array($object))
        return $object;

    return array_map('objectToArray', (array) $object);
}
function hierarchyTree($US_Ary,$usRpt = 1,$resign_stats) {
    $resAry = array();
    $resAry = keyVal($US_Ary, 'US_Report', $usRpt);
    if($resAry){
        
        foreach ($resAry as $key => $value) { 
            if($resign_stats==$value['ES_Id'])           
            $style_text='style="color: red;"';
            else $style_text='style="color: black;"';
            $tmp .= '<item '.$style_text.' text="'.$value['US_FName'].'" id="'.$value['US_Id'].'" >';
            if($value['US_Id'] != 1) 
                $tmp .= hierarchyTree($US_Ary,$value['US_Id'],$resign_stats); 
            $tmp .= '</item>';  
        }
    }
    return $tmp;
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<tree id="0">';
    if($OF_Obj) {
        $j = 1;
    
        if($REQUEST['r'] == 'OF_O') {
            foreach($OF_Obj as $rwOF) { 
                echo '<item text="'.$rwOF->OF_Name.'" id="OF_'.$rwOF->OF_Id.'" im1="../../../../../images/icon/company.png"  im2="../../../../../images/icon/company.png">';           
                    echo $LocationObj->viewLocations('*',' WHERE OF_Id='.$rwOF->OF_Id.' AND LC_Status != 5 '.$filterHR['LC'].' ORDER BY LC_Name');
                    $LC_Obj = $LocationObj->LocationArray;
                    //print_r($LC_Obj);
                    if($LC_Obj) {
                        $k = 1;
                        foreach($LC_Obj as $rwLC) {
                            echo '<item text="'.$rwLC->LC_Name.'" id="OF_'.$rwOF->OF_Id.'-LC_'.$rwLC->LC_Id.'" im1="../../../../../images/icon/office.gif"  im2="../../../../../images/icon/office.gif">';                      
                            $DepartmentObj->viewDepartments(' DP_Id, DP_Name ','',' WHERE OF_Id='.$rwOF->OF_Id.' '.$filterHR['DP'].' ORDER BY DP_Name');
                                $DP_Obj = $DepartmentObj->DepartmentArray;
                                if($DP_Obj) {
                                    $l = 1;
                                    foreach($DP_Obj as $rwDP) {
                                        $UserObj->viewUser(' WHERE LC_Id='.$rwLC->LC_Id.' AND US_Status != 5 AND DP_Id='.$rwDP->DP_Id.' '.$filterHR['US'].' ORDER BY US_FName');
                                        $US_Obj = $UserObj->UserArray;
                                        if($US_Obj) {
                                            $m = 1;
                                            $data = '';
                                            foreach($US_Obj as $rwUS) { $statImg    = ($rwUS->US_Status == '0') ? "blocked_user.gif" : "user.gif";
                                                $data .=  '<item text="'.$rwUS->US_FName.' '.$rwUS->US_LName.'" id="OF_'.$rwOF->OF_Id.'-LC_'.$rwLC->LC_Id.'-DP_'.$rwDP->DP_Id.'-US_'.$rwUS->US_Id.'" im0="../../../../../images/icon/'.$statImg.'"></item>';
                                            }
                                            if($data){
                                                echo '<item text="'.$rwDP->DP_Name.'" id="OF_'.$rwOF->OF_Id.'-LC_'.$rwLC->LC_Id.'-DP_'.$rwDP->DP_Id.'" im1="../../../../../images/icon/department.gif"  im2="../../../../../images/icon/department.gif">'.$data.'</item>';  
                                            }
                                        }
                                    }
                                }
                            echo '</item>';
                        }
                    }
                echo '</item>';
            }
        } else if($REQUEST['r'] == 'OF_D') {
            foreach($OF_Obj as $rwOF) { 
                echo '<item text="'.$rwOF->OF_Name.'" id="OF_'.$rwOF->OF_Id.'" im1="../../../../../images/icon/company.png"  im2="../../../../../images/icon/company.png">';
                $DepartmentObj->viewDepartments(' DP_Id, DP_Name ','',' WHERE OF_Id= '.$rwOF->OF_Id.' '.$filterHR['DP'].' ORDER BY DP_Name');
                $DP_Obj = $DepartmentObj->DepartmentArray;

                if($DP_Obj) {
                    $l = 1;
                    foreach($DP_Obj as $rwDP) {   
                        echo '<item text="'.$rwDP->DP_Name.'" id="OF_'.$rwOF->OF_Id.'-DP_'.$rwDP->DP_Id.'" im1="../../../../../images/icon/department.gif"  im2="../../../../../images/icon/department.gif">'; 
                        $LocationObj->viewLocations('*',' WHERE OF_Id= '.$rwOF->OF_Id.' '.$filterHR['LC'].' AND LC_Status=1 ORDER BY LC_Name');
                        $LC_Obj = $LocationObj->LocationArray;
                        if($LC_Obj) {
                            $k = 1;
                            foreach($LC_Obj as $rwLC) {
                                echo '<item text="'.$rwLC->LC_Name.'" id="OF_'.$rwOF->OF_Id.'-DP_'.$rwDP->DP_Id.'-LC_'.$rwLC->LC_Id.'" im1="../../../../../images/icon/office.gif"  im2="../../../../../images/icon/office.gif">';   
                                $UserObj->viewUser(' WHERE LC_Id='.$rwLC->LC_Id.' AND DP_Id='.$rwDP->DP_Id.' '.$filterHR['US'].' AND US_Status=1 ORDER BY US_FName');
                                $US_Obj = $UserObj->UserArray;
                                if($US_Obj) {
                                    $m = 1;
                                    foreach($US_Obj as $rwUS) {
                                        echo   '<item text="'.$rwUS->US_FName.' '.$rwUS->US_LName.'" id="OF_'.$rwOF->OF_Id.'-DP_'.$rwDP->DP_Id.'-LC_'.$rwLC->LC_Id.'-US_'.$rwUS->US_Id.'" im0="../../../../../images/icon/user.gif"></item>';
                                    }
                                }
                                echo '</item>';
                            }
                        }
                        echo '</item>';
                    }
                }
                echo '</item>'; 
            }
        } else if($REQUEST['r'] == 'OF_H') {
            $UserObj->userHierarchy(' US_Id, US_FName, US_LName, OF_Id, LC_Id, DP_Id, DG_Id, US_Report ,ES_Id ', ' WHERE US_Id!='.$preTally_user_id.' AND US_Status != 5 ORDER BY US_Report' );
            $US_Obj = $UserObj->UserArray;
            $US_Ary =  objectToArray($US_Obj);            
            //echo hierarchyTree($US_Ary,$preTally_user_id);  
            $tree='<item text="'.$preTally_user_name.'" id="'.$preTally_user_id.'">';
            $tree .= hierarchyTree($US_Ary,$preTally_user_id,$resign_stats);  
            $tree .= '</item>';
            echo $tree;
        }       
    }
echo '</tree>';

?>