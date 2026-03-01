<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="80" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="SS_Id" value="0"/>
                <item type="hidden" name="OF_Id" value="0"/>
                
		<item type="input" name="SS_Name" label="Title" value="" offsetTop="30" required="true" inputWidth="150">
			
		</item>
                <item type="fieldset" label="Salary" width="400">
                <item type="block" width="350" offsetLeft="5">
		<item type="input" name="SS_Basic" label="Basic + DA" value=""  required="true" validate="ValidNumeric">
			
		</item>
                <item type="newcolumn" />  
                   <item type="radio" name="SS_Basic_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
                   <item type="newcolumn" />  
                   <item type="radio" name="SS_Basic_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
               </item>';                
//                echo '<item type="block" width="350" offsetLeft="5">
//                    <item type="input" name="SS_DA" label="DA" value=""    required="true" validate="ValidNumeric">
//                            
//                    </item>
//                <item type="newcolumn" />  
//                    <item type="radio" name="SS_DA_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
//                    <item type="newcolumn" />  
//                    <item type="radio" name="SS_DA_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
//                </item>';                 
                echo '<item type="block" width="350" offsetLeft="5">
                    <item type="input" name="SS_HRA" label="HRA" value=""    required="true" validate="ValidNumeric">
                            
                    </item>
                <item type="newcolumn" />  
                    <item type="radio" name="SS_HRA_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
                    <item type="newcolumn" />  
                    <item type="radio" name="SS_HRA_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                </item>     
                <item type="block" width="350" offsetLeft="5">
                    <item type="input" name="SS_CCA" label="CCA" value=""    required="true" validate="ValidNumeric"></item>
                    <item type="newcolumn" />   <item type="radio" name="SS_CCA_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
                    <item type="newcolumn" />  
                    <item type="radio" name="SS_CCA_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                </item>
                <item type="block" width="350" offsetLeft="5">
                    <item type="input" name="SS_Convey" label="Conveyance" value=""    required="true" validate="ValidNumeric">
                            
                    </item>
                <item type="newcolumn" />  
                    <item type="radio" name="SS_Convey_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
                    <item type="newcolumn" />  
                    <item type="radio" name="SS_Convey_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                </item>                
                <item type="block" width="350" offsetLeft="5">
                    <item type="input" name="SS_Edu" label="Education" value=""    required="true" validate="ValidNumeric">
                            
                    </item>
                <item type="newcolumn" />  
                    <item type="radio" name="SS_Edu_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
                    <item type="newcolumn" />  
                    <item type="radio" name="SS_Edu_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                </item>                 
                <item type="block" width="350" offsetLeft="5">
                    <item type="input" name="SS_Medic" label="Medical" value=""    required="true" validate="ValidNumeric">
                            
                    </item>
                <item type="newcolumn" />  
                    <item type="radio" name="SS_Medic_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
                    <item type="newcolumn" />  
                    <item type="radio" name="SS_Medic_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                </item>';
                
              
                
                
//                echo'<item type="block" width="350" offsetLeft="5">
//                    <item type="input" name="SS_Misc" label="Miscellanious" value=""    required="true" validate="ValidNumeric"></item>
//                    <item type="newcolumn" />   <item type="radio" name="SS_Misc_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
//                    <item type="newcolumn" />  
//                    <item type="radio" name="SS_Misc_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
//                </item>                    


               echo '
                   </item>
                   <item type="block" width="300" offsetTop="10">
			<item type="button" value="Save" name="newSalStructValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newSalStructCancel"/>
		</item>
                <item type="newcolumn" />      
                
                <item type="combo" label="Status" name="SS_Status" readonly="true" offsetTop="30" inputWidth="150">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
                        <option value="3" label="Deleted" selected="false" />
			
		</item>
                <item type="fieldset" label="Deductions" width="380" offsetLeft="5">
                    <item type="block" width="350" offsetLeft="5">    
                        <item type="input" name="SS_DedEPF" label="EPF" value="" labelWidth="70" inputWidth="100" required="true" validate="ValidNumeric"  offsetLeft="5">
			
                        </item>
                    <item type="newcolumn" />  
                        <item type="radio" name="SS_DedEPF_Type" tooltip="Precentage of Gross-HRA" info="true" label="%" value="0" labelWidth="25" inputWidth="10" checked="true"  offsetLeft="5"></item>
                        <item type="newcolumn" />  
                        <item type="radio" name="SS_DedEPF_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                    </item>     
                    <item type="block" width="350" offsetLeft="5">    
                        <item type="input" name="SS_DedESI" label="ESI" value="" labelWidth="70" inputWidth="100" required="true" validate="ValidNumeric"  offsetLeft="5">
			
                        </item>
                    <item type="newcolumn" />  
                        <item type="radio" name="SS_DedESI_Type" tooltip="Precentage of Gross-HRA" info="true" label="%" value="0" labelWidth="25" inputWidth="10" checked="true"  offsetLeft="5"></item>
                        <item type="newcolumn" />  
                        <item type="radio" name="SS_DedESI_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                    </item>                     
                    <item type="block" width="350" offsetLeft="5">    
                        <item type="input" name="SS_DedLWF" label="LWF" value="" labelWidth="70" inputWidth="100" required="true" validate="ValidNumeric"  offsetLeft="5">
			
                        </item>
                    <item type="newcolumn" />  
                        <item type="radio" name="SS_DedLWF_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
                        <item type="newcolumn" />  
                        <item type="radio" name="SS_DedLWF_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                    </item>    
                    <item type="block" width="350" offsetLeft="5">    
                        <item type="input" name="SS_DedProfTDS" label="Prof TDS(%)" value="" labelWidth="70" inputWidth="100" required="true" validate="ValidNumeric"  offsetLeft="5">
			
                        </item>
                    <item type="newcolumn" />  
                        
                    </item>
                </item>
                <item type="fieldset" label="Employer Contributions" width="380" offsetLeft="5">
                    <item type="block" width="350" offsetLeft="5">    
                        <item type="input" name="SS_EmpConEPF" label="EPF" value="" labelWidth="70" inputWidth="100" required="true" validate="ValidNumeric"  offsetLeft="5">
			
                        </item>
                    <item type="newcolumn" />  
                        <item type="radio" name="SS_EmpConEPF_Type" tooltip="Precentage of Gross-HRA" info="true" label="%" value="0" labelWidth="25" inputWidth="10" checked="true"  offsetLeft="5"></item>
                        <item type="newcolumn" />  
                        <item type="radio" name="SS_EmpConEPF_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                    </item>     
                    <item type="block" width="350" offsetLeft="5">    
                        <item type="input" name="SS_EmpConESI" label="ESI" value="" labelWidth="70" inputWidth="100" required="true" validate="ValidNumeric"  offsetLeft="5">
			
                        </item>
                    <item type="newcolumn" />  
                        <item type="radio" name="SS_EmpConESI_Type" tooltip="Precentage of Gross-HRA" info="true" label="%" value="0" labelWidth="25" inputWidth="10" checked="true"  offsetLeft="5"></item>
                        <item type="newcolumn" />  
                        <item type="radio" name="SS_EmpConESI_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                    </item>     
                    <item type="block" width="350" offsetLeft="5">    
                        <item type="input" name="SS_EmpConLWF" label="LWF" value="" labelWidth="70" inputWidth="100" required="true" validate="ValidNumeric"  offsetLeft="5">
			
                        </item>
                    <item type="newcolumn" />  
                        <item type="radio" name="SS_EmpConLWF_Type" label="%"  value="0" labelWidth="20" inputWidth="10" checked="true"  offsetLeft="5"></item>
                        <item type="newcolumn" />  
                        <item type="radio" name="SS_EmpConLWF_Type" label="Rs" value="1" labelWidth="20" inputWidth="10"   offsetLeft="5"></item>
                    </item>     
                      

                </item>
		
		
	</items>';
?>