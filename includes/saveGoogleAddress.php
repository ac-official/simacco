<?php
//require_once ($_SERVER['DOCUMENT_ROOT'].'/_conf.php');                        
require_once ("preTallyClass/GeneralClass.php");    
function saveGoglAddress($addressArray){ //Passing Address values in a Single Array    
    $GeneralObj = new GeneralClass();        
    $CNName = $addressArray['GOGL_Country'];
    $STName = $addressArray['GOGL_State'];
    $CTName = $addressArray['GOGL_City'];
    $LCName = $addressArray['GOGL_Location'];
    $PLName = $addressArray['GOGL_Place'];
    $SRName = $addressArray['GOGL_Street'];

    $CN_Id  = $GeneralObj->getValue('countries', 'CN_Id', " WHERE CN_Name = '".$CNName."' "); 
    $ST_Id  = $GeneralObj->getValue('states', 'ST_Id', " WHERE ST_Name = '".$STName."' AND ST_Status = 1"); 
  
    //--------------------------------Adding New City if city dosen't exist--------------------------------------//
    $CT_Id  = $GeneralObj->getValue('cities', 'CT_Id', " WHERE CT_Name = '".$CTName."' AND ST_Id = '".$ST_Id."' AND CT_Status = 1"); 
    if(!$CT_Id) {
        $GeneralObj->InsertData = array(
            'CT_Name'   =>  $CTName,
            'ST_Id'     =>  $ST_Id,
            'US_Id'     =>  0,
            'CT_CDate'  =>  date('Y-m-d'),
            'CT_MDate'  =>  date('Y-m-d'),
            'CT_Status' =>  1
        );
        $CT_Id = $GeneralObj->insertReturnId('cities');
    }

    //--------------------------------Adding New Location if Location dosen't exist--------------------------------------//
    $ALC_Id  = $GeneralObj->getValue('addr_locations', 'ALC_Id', " WHERE ALC_Name = '".$LCName."' AND CT_Id = '".$CT_Id."' AND ALC_Status = 1"); 
    if(!$ALC_Id) {
        unset($GeneralObj->InsertData);
        $GeneralObj->InsertData = array(
            'ALC_Name'   => $LCName,
            'CT_Id'     => $CT_Id,
            'US_Id'     => 0,
            'ALC_Status' => 1,
            'ALC_CDate'  => date('Y-m-d H:i:s'),
            'ALC_MDate'  => date('Y-m-d H:i:s')
        );
        $ALC_Id = $GeneralObj->insertReturnId('addr_locations');
    }
    
    //--------------------------------Adding New Place if Place dosen't exist--------------------------------------//
    $PL_Id  = $GeneralObj->getValue('addr_places', 'PL_Id', " WHERE PL_Name = '".$PLName."' AND ALC_Id = '".$ALC_Id."' AND PL_Status = 1"); 
    if(!$PL_Id) {
        unset($GeneralObj->InsertData);
        $GeneralObj->InsertData = array(
            'PL_Name'   => $PLName,
            'ALC_Id'    => $ALC_Id,
            'US_Id'     => 0,
            'PL_Status' => 1,
            'PL_CDate'  => date('Y-m-d H:i:s'),
            'PL_MDate'  => date('Y-m-d H:i:s')
        );
        $PL_Id = $GeneralObj->insertReturnId('addr_places');
    }
    
    //--------------------------------Adding New Street if Street dosen't exist--------------------------------------//
    $SR_Id  = $GeneralObj->getValue('addr_streets', 'SR_Id', " WHERE SR_Name = '".$SRName."' AND PL_Id = '".$PL_Id."' AND SR_Status = 1"); 
    if(!$SR_Id) {
        unset($GeneralObj->InsertData);
        $GeneralObj->InsertData = array(
            'SR_Name'   => $SRName,
            'PL_Id'     => $PL_Id,
            'US_Id'     => 0,
            'SR_Status' => 1,
            'SR_CDate'  => date('Y-m-d H:i:s'),
            'SR_MDate'  => date('Y-m-d H:i:s')
        );
        $SR_Id = $GeneralObj->insertReturnId('addr_streets');
    }
    
    $addressData = array(
        'CN_Id'  => $CN_Id,
        'ST_Id'  => $ST_Id, 
        'CT_Id'  => $CT_Id, 
        'ALC_Id' => $ALC_Id, 
        'PL_Id'  => $PL_Id, 
        'SR_Id'  => $SR_Id
    );   
    return json_encode($addressData);
}    
?>
