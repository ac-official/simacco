<?php
require_once($BASEPATH . "preTallyClass/SalStructClass.php");

$SalStructObj = new SalStructClass();
$SalStructObj->SalStruct_Data = array(        
	'SS_Name'       => trim(htmlspecialchars($_REQUEST['SS_Name'], ENT_QUOTES)),                      
	'SS_Basic'	=> trim(htmlspecialchars($_REQUEST['SS_Basic'], ENT_QUOTES)),
        'SS_Basic_Type' => trim(htmlspecialchars($_REQUEST['SS_Basic_Type'], ENT_QUOTES)),
//        'SS_DA'         => trim(htmlspecialchars($_REQUEST['SS_DA'], ENT_QUOTES)),
//        'SS_DA_Type'    => trim(htmlspecialchars($_REQUEST['SS_DA_Type'], ENT_QUOTES)),
        'SS_HRA'	=> trim(htmlspecialchars($_REQUEST['SS_HRA'], ENT_QUOTES)),
        'SS_HRA_Type'	=> trim(htmlspecialchars($_REQUEST['SS_HRA_Type'], ENT_QUOTES)),
        'SS_CCA'	=> trim(htmlspecialchars($_REQUEST['SS_CCA'], ENT_QUOTES)),
        'SS_CCA_Type'	=> trim(htmlspecialchars($_REQUEST['SS_CCA_Type'], ENT_QUOTES)),
        'SS_Convey'	=> trim(htmlspecialchars($_REQUEST['SS_Convey'], ENT_QUOTES)),
        'SS_Convey_Type'=> trim(htmlspecialchars($_REQUEST['SS_Convey_Type'], ENT_QUOTES)),
        'SS_Edu'        => trim(htmlspecialchars($_REQUEST['SS_Edu'], ENT_QUOTES)),
        'SS_Edu_Type'   => trim(htmlspecialchars($_REQUEST['SS_Edu_Type'], ENT_QUOTES)),
        'SS_Medic'	=> trim(htmlspecialchars($_REQUEST['SS_Medic'], ENT_QUOTES)),
        'SS_Medic_Type'	=> trim(htmlspecialchars($_REQUEST['SS_Medic_Type'], ENT_QUOTES)),
//      'SS_Misc'	=> trim(htmlspecialchars($_REQUEST['SS_Misc'], ENT_QUOTES)),
//      'SS_Misc_Type'	=> trim(htmlspecialchars($_REQUEST['SS_Misc_Type'], ENT_QUOTES)),        
        'SS_DedESI'     => trim(htmlspecialchars($_REQUEST['SS_DedESI'], ENT_QUOTES)),
        'SS_DedESI_Type'=> trim(htmlspecialchars($_REQUEST['SS_DedESI_Type'], ENT_QUOTES)),
        'SS_DedEPF'     => trim(htmlspecialchars($_REQUEST['SS_DedEPF'], ENT_QUOTES)),
        'SS_DedEPF_Type'=> trim(htmlspecialchars($_REQUEST['SS_DedEPF_Type'], ENT_QUOTES)),
        'SS_DedLWF'     => trim(htmlspecialchars($_REQUEST['SS_DedLWF'], ENT_QUOTES)),
        'SS_DedLWF_Type'=> trim(htmlspecialchars($_REQUEST['SS_DedLWF_Type'], ENT_QUOTES)),
        'SS_DedProfTDS'     => trim(htmlspecialchars($_REQUEST['SS_DedProfTDS'], ENT_QUOTES)),
        'SS_DedProfTDS_Type'=> 0,
        'SS_EmpConEPF'      => trim(htmlspecialchars($_REQUEST['SS_EmpConEPF'], ENT_QUOTES)),
        'SS_EmpConEPF_Type' => trim(htmlspecialchars($_REQUEST['SS_EmpConEPF_Type'], ENT_QUOTES)),
        'SS_EmpConESI'      => trim(htmlspecialchars($_REQUEST['SS_EmpConESI'], ENT_QUOTES)),
        'SS_EmpConESI_Type' => trim(htmlspecialchars($_REQUEST['SS_EmpConESI_Type'], ENT_QUOTES)),
        'SS_EmpConLWF'      => trim(htmlspecialchars($_REQUEST['SS_EmpConLWF'], ENT_QUOTES)),
        'SS_EmpConLWF_Type' => trim(htmlspecialchars($_REQUEST['SS_EmpConLWF_Type'], ENT_QUOTES)),
        'SS_Status'         => trim(htmlspecialchars($_REQUEST['SS_Status'], ENT_QUOTES)),
        'SS_CFlag'          => trim(htmlspecialchars($_REQUEST['SS_CFlag'], ENT_QUOTES)),
	'SS_MDate'          => date('Y-m-d H:i:s')
);

 	 	 	 	 	
//var_dump($SalStructObj->SalStruct_Data);die();
array_walk_recursive($SalStructObj->SalStruct_Data, 'replacenulls');
function replacenulls(& $item, $key) {
    if ($item === '') {
        $item = 0;
    }
}
$office_id=htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES);
if(($office_id=="")||($office_id==0))
{
    $office_id=$preTally_user_ofid;
}
if( $SalStructObj->verifySalStruct(htmlspecialchars($_REQUEST['SS_Id'], ENT_QUOTES),$office_id) ) {
	if(htmlspecialchars($_REQUEST['SS_Id'], ENT_QUOTES) == 0) {
                $SalStructObj->SalStruct_Data["SS_CDate"] = date('Y-m-d H:i:s'); 
                $SalStructObj->SalStruct_Data["OF_Id"] = $office_id;
		$SalStructObj->newSalStruct(); 
                echo 'Salary Structure Created Successfully';
	} else {
		echo $SalStructObj->updateSalStruct(htmlspecialchars($_REQUEST['SS_Id'], ENT_QUOTES));
                echo $SalStructObj->updateUserSalary($_REQUEST['SS_Id']);
	}
} else { echo 'fail'; }
?>