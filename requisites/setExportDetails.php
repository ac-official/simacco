<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
    <item type="settings" position="label-right" labelWidth="160" inputWidth="150" offsetLeft="20"   />
     
        <item type="fieldset" label="Select Details" offsetTop="10" inputWidth="500" >
            
            <item type="block" width="480">
                <item type="checkbox" value = "1" checked = "true" name="XL_Id" label="Id"></item>
                <item type="checkbox" value = "1" checked = "true" name="XL_Branch" label="Branch"></item>  
                <item type="checkbox" value = "1" checked = "true" name="XL_Amount" label="Job Amount"></item> 
                <item type="checkbox" value = "1" checked = "true" name="XL_Date" label="Job Date"></item>  <item type="newcolumn"/>
                <item type="checkbox" value = "1" checked = "true" name="XL_Advance" label="Advance Received"></item> 
                <item type="checkbox" value = "1" checked = "true" name="XL_Part" label="Part Payment Received"></item> 
                <item type="checkbox" value = "1" checked = "true" name="XL_Final" label="Final Payment Received"></item> 
             </item>
         
            <item type="block" width="300"  offsetLeft="100" >
                <item type="button" value="Submit" name="xlExportSubmit"/>
                <item type="newcolumn"/>
                <item type="button" value="Cancel" name="xlExportCancel"/>
            </item>

        </item>
   
</items>';