<?php
require_once("connection.php");

class SalStructClass
{
	var $SalStructArray;
	
	
	//----------------------------------------- All SalStructs ----------------------------------------//
	function viewSalStructs($filt='')
	{			
		$count=0;
		$this->SalStructArray = array();
                
		$result=mysqli_query($GLOBALS['con'],"SELECT * FROM salary_structures AS SS,offices AS OF1 ".$filt);
		while($row=mysqli_fetch_object($result)) {
			$this->SalStructArray[$count]=$row;
			$count++;
		}	
	}
	
	//----------------------------------------- Verify SalStruct ----------------------------------------//
	function verifySalStruct($MId,$ofid){
		$sql = 'SELECT COUNT(SS_Name) FROM salary_structures WHERE SS_Status!=3 AND SS_Name = "'.$this->SalStruct_Data['SS_Name'].'" AND SS_Id  != '.$MId.' AND OF_Id='.$ofid;
		$result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
			return true;
		} else {
			return false;	
		}
	}
	
	//----------------------------------------- New SalStruct ----------------------------------------//
	function newSalStruct(){
		
		$sql = "INSERT INTO salary_structures( " . implode(', ',array_keys($this->SalStruct_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->SalStruct_Data)) . "'" . ")";
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
                if(mysqli_affected_rows($GLOBALS['con'])>0)
                {
		$SSId = mysqli_insert_id($GLOBALS['con']);
                return $SSId;
                }
               
	}
	//----------------------------------------- Update SalStruct ----------------------------------------//
	function updateSalStruct($MId){
		$SalStructData = '';
		foreach ($this->SalStruct_Data as $key=>$value){ 
			$SalStructData = $SalStructData .$key ."='".$value."', ";
		}
		$SalStructData = substr($SalStructData, 0, -2);
		$sql = "UPDATE salary_structures SET $SalStructData WHERE SS_Id=$MId";
		mysqli_query($GLOBALS['con'],$sql);
		return 'Salary Structure Updated Successfully';
	}
        function getSalPerc($ssid)
        {
            $count=0;
            $this->SalStructArray = array();
            $sql="SELECT *  FROM salary_structures WHERE SS_Id=".$ssid;
            $result=  mysqli_query($GLOBALS['con'],$sql);
            while($row=mysqli_fetch_assoc($result)) {
			$this->SalStructArray[$count]=$row;
			$count++;
                    }
        }
        //Function for updating user's salary when salary attributes get updated
        function updateUserSalary($ssid){
           $this->getSalPerc($ssid);
            $struct_data=$this->SalStructArray[0];
            $sqluser_salary="SELECT US_Id,US_GrossSal FROM users_salary WHERE SS_Id=".$ssid;
            $result=mysqli_query($GLOBALS['con'],$sqluser_salary);
            while($row=mysqli_fetch_assoc($result)) {
			$this->SalArray[$count]=$row;
			$count++;
                    }
                   
            foreach($this->SalArray as $SalData){
                $subtotal=$basic_amt=$da_amt=$cca_amt=$hra_amt=$convey_amt=$edu_amt=$med_amt=$misc_amt=$gross_sal=0;
                
                $us_id=$SalData["US_Id"];
                $gross_sal=$SalData["US_GrossSal"];
                
                if($struct_data["SS_Basic_Type"]== 0){
                    $basic_amt=  $gross_sal * $struct_data["SS_Basic"]/100;   
                }
                else{
                    $basic_amt=  $struct_data["SS_Basic"];    
                }
                
                if($struct_data["SS_DA_Type"]== 0){
                    $da_amt=  $gross_sal * $struct_data["SS_DA"]/100;   
                }    
                else{
                    $da_amt=  $struct_data["SS_DA"];
                }
                
                if($struct_data["SS_CCA_Type"]== 0){
                    $cca_amt=  $gross_sal * $struct_data["SS_CCA"]/100;   
                }    
                else{
                    $cca_amt=  $struct_data["SS_CCA"];
                }
                
                if($struct_data["SS_HRA_Type"]== 0){
                    $hra_amt=  $gross_sal * $struct_data["SS_HRA"]/100;   
                }    
                else{
                    $hra_amt=  $struct_data["SS_HRA"];
                }
                
                if($struct_data["SS_Convey_Type"]== 0){
                    $convey_amt=  $gross_sal * $struct_data["SS_Convey"]/100;   
                }    
                else{
                    $convey_amt=  $struct_data["SS_Convey"];
                }
                
                if($struct_data["SS_Edu_Type"]== 0){
                    $edu_amt=  $gross_sal * $struct_data["SS_Edu"]/100;   
                }    
                else{
                    $edu_amt=  $struct_data["SS_Edu"];
                }
                
                if($struct_data["SS_Medic_Type"]== 0){
                    $med_amt=  $gross_sal * $struct_data["SS_Medic"]/100;   
                }    
                else{
                    $med_amt=  $struct_data["SS_Medic"];
                }
                $subtotal=$basic_amt+$da_amt+ $cca_amt+ $hra_amt+ $convey_amt+$edu_amt+$med_amt;
                $misc_amt= $gross_sal-$subtotal;
                
                //$gross_sal=$gross_sal+$misc_amt;
                //$misc_amt=0;                
                
                $deduct_total=$basic_amt+$da_amt+ $cca_amt+ $hra_amt+ $convey_amt+$edu_amt+$med_amt+$misc_amt;
                if($struct_data["SS_DedEPF_Type"]== 0){
                    $epf_amt=  ($deduct_total-$hra_amt) * $struct_data["SS_DedEPF"]/100;   
                }    
                else{
                    $epf_amt=  $struct_data["SS_DedEPF"];
                }
                
                if($struct_data["SS_DedESI_Type"]== 0){
                    $esi_amt=  ($deduct_total-$hra_amt) * $struct_data["SS_DedESI"]/100;   
                }    
                else{
                    $esi_amt=  $struct_data["SS_DedESI"];
                }
                
                if($struct_data["SS_DedProfTDS_Type"]== 0){
                    $ptds_amt=  $gross_sal * $struct_data["SS_DedProfTDS"]/100;   
                }    
                else{
                    $ptds_amt=  $struct_data["SS_DedProfTDS"];
                }
                
                if($struct_data["SS_DedLWF_Type"]== 0){
                    $lwf_amt=  $gross_sal * $struct_data["SS_DedLWF"]/100;   
                }    
                else{
                    $lwf_amt=  $struct_data["SS_DedLWF"];
                }                 
            
            $this->US_SalaryData    = array(               
            'US_BasicSal'           => $basic_amt,
            'US_DaSal'              => $da_amt,
            'US_CcaSal'             => $cca_amt,
            'US_HraSal'             => $hra_amt,
            'US_ConveySal'          => $convey_amt,
            'US_EduSal'             => $edu_amt,
            'US_MedSal'             => $med_amt,
            'US_MiscSal'            => $misc_amt,            
            'US_DedEPF'             => $epf_amt,
            'US_DedESI'             => $esi_amt,
            'US_DedProfTDS'         => $ptds_amt,
            'US_DedLWF'             => $lwf_amt); 
            $ITSalData="";
            if(count($this->US_SalaryData) > 0) {  
                /*salary details*/
                foreach ($this->US_SalaryData as $key=>$value){ 
                    $ITSalData = $ITSalData .$key ."='".$value."', ";
		}
		$ITSalData = substr($ITSalData, 0, -2);
		$sqlsal = "UPDATE users_salary SET $ITSalData WHERE US_Id=".$us_id;
		mysqli_query($GLOBALS['con'],$sqlsal);
                //print_r($sqlsal);
            } 
            }        
        }       
}

?>