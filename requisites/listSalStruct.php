<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/SalStructClass.php");
$StructObj = new SalStructClass();
if($preTally_user_ofid!=1)
    { 
    $cmpnyfilter="WHERE OF1.OF_Id=".$preTally_user_ofid." AND SS.OF_Id=OF1.OF_Id AND SS.SS_Status!=3 ORDER BY SS.SS_Name"; 
    }
else
    {
    $cmpnyfilter="WHERE SS.OF_Id=OF1.OF_Id AND SS.SS_Status!=3 ORDER BY OF1.OF_Name";    
    }
$StructObj->viewSalStructs($cmpnyfilter);
$Struct_Obj = $StructObj->SalStructArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>	
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="na">Structure Name</column>';
                if($preTally_user_ofid == 1) 
                {
                 echo  '<column width="*" type="ro" align="left" sort="na">Company Name</column>';
                }
                echo '<column width="0" type="ro" align="center" sort="na">LSStatus </column>';
                echo '<column width="60" type="ro" align="center" sort="na">	Status </column>
			<settings>
				<colwidth>px</colwidth>
			</settings>
			<beforeInit> 
            	<call command="setSkin">
					<param>dhx_skyblue</param>
				</call> 
				<call command="setImagePath">
					<param>assets/grid/codebase/imgs/</param>
				</call> 
				<call command="enableSmartRendering">
					<param>false</param>
				</call> 
            </beforeInit> 
			

		  </head>';
		  if($Struct_Obj) {
				$j = 1;
				foreach($Struct_Obj as $rw) {
					
					echo '<row id="'.$rw->SS_Id.'">
						<userdata name="SS_Name">'.$rw->SS_Name.'</userdata>
                                                <userdata name="OF_Id">'.$rw->OF_Id.'</userdata>    
                                                <userdata name="SS_Basic">'.$rw->SS_Basic.'</userdata>
                                                <userdata name="SS_Basic_Type">'.$rw->SS_Basic_Type.'</userdata>    
                                                <userdata name="SS_DA">'.$rw->SS_DA.'</userdata>
                                                <userdata name="SS_DA_Type">'.$rw->SS_DA_Type.'</userdata>    
                                                <userdata name="SS_HRA">'.$rw->SS_HRA.'</userdata>
                                                <userdata name="SS_HRA_Type">'.$rw->SS_HRA_Type.'</userdata>    
                                                <userdata name="SS_CCA">'.$rw->SS_CCA.'</userdata>
                                                <userdata name="SS_CCA_Type">'.$rw->SS_CCA_Type.'</userdata>    
                                                <userdata name="SS_Convey">'.$rw->SS_Convey.'</userdata>
                                                <userdata name="SS_Convey_Type">'.$rw->SS_Convey_Type.'</userdata>
                                                <userdata name="SS_Edu">'.$rw->SS_Edu.'</userdata>
                                                <userdata name="SS_Edu_Type">'.$rw->SS_Edu_Type.'</userdata>    
                                                <userdata name="SS_Medic">'.$rw->SS_Medic.'</userdata>
                                                <userdata name="SS_Medic_Type">'.$rw->SS_Medic_Type.'</userdata>    
						<userdata name="SS_Misc">'.$rw->SS_Misc.'</userdata> 
                                                <userdata name="SS_Misc_Type">'.$rw->SS_Misc_Type.'</userdata>                                                
                                                <userdata name="SS_DedESI">'.$rw->SS_DedESI.'</userdata> 	
                                                <userdata name="SS_DedESI_Type">'.$rw->SS_DedESI_Type.'</userdata> 	
                                                <userdata name="SS_DedEPF">'.$rw->SS_DedEPF.'</userdata> 	
                                                <userdata name="SS_DedEPF_Type">'.$rw->SS_DedEPF_Type.'</userdata> 	
                                                <userdata name="SS_DedLWF">'.$rw->SS_DedLWF.'</userdata> 	
                                                <userdata name="SS_DedLWF_Type">'.$rw->SS_DedLWF_Type.'</userdata>
                                                <userdata name="SS_DedProfTDS">'.$rw->SS_DedProfTDS.'</userdata>
                                                <userdata name="SS_DedProfTDS_Type">'.$rw->SS_DedProfTDS_Type.'</userdata>    
                                                <userdata name="SS_EmpConEPF">'.$rw->SS_EmpConEPF.'</userdata> 	 	 	 	 	
                                                <userdata name="SS_EmpConEPF_Type">'.$rw->SS_EmpConEPF_Type.'</userdata>
                                                <userdata name="SS_EmpConESI">'.$rw->SS_EmpConESI.'</userdata>
                                                <userdata name="SS_EmpConESI_Type">'.$rw->SS_EmpConESI_Type.'</userdata>
                                                <userdata name="SS_EmpConLWF">'.$rw->SS_EmpConLWF.'</userdata>
                                                <userdata name="SS_EmpConLWF_Type">'.$rw->SS_EmpConLWF_Type.'</userdata>
                                                <userdata name="SS_Status">'.$rw->SS_Status.'</userdata>  
                                                <userdata name="SS_CFlag">'.$rw->SS_CFlag.'</userdata>      
						<cell>'.$j.'</cell>
						<cell name="Salstruct_Name">'.$rw->SS_Name.'</cell> '; 
                                                if($preTally_user_ofid == 1) 
                                                {
                                                 echo   '<cell name="Office_Name">'.$rw->OF_Name.'</cell> ';
                                                }
                                                if($rw->SS_Status == '0'){ 
                                                    $LSStatus="Blocked";
                                                }else{
                                                    $LSStatus="Approved";
                                                }
                                               echo' <cell>'.$LSStatus.'</cell>';
						if($rw->SS_Status == '0') { 
                                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Blocked\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                } else {
                                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Approved\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                }
                                        echo '</row>';
                                        $j++;
				}
			}
		  
echo '</rows>';
?>