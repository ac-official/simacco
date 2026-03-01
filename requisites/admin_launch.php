<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
 <item type="settings" position="label-left" labelWidth="150"/>        
            <item type="fieldset" id="contact1" offsetTop="5" label="LOGS" width="750">
            <item type="template" className="admin_tile user" label="Users" value="245" ></item>                    
            <item type="template" className="admin_tile newuser" label="New Users" value="21" ></item>
            <item type="template" className="admin_tile company" label="Companies" value="21" ></item>            
        <item type="newcolumn"/>
           <item type="template" className="admin_tile subhead" label="Subheads" value="4850" ></item> 
           <item type="template" className="admin_tile items" label="Items" value="13485" ></item>           
           <item type="template" className="admin_tile license" label="License" value="21" ></item>           
        <item type="newcolumn"/>
            <item type="template" className="admin_tile mail" label="Mail" value="485" ></item>
            <item type="template" className="admin_tile bugs" label="Bug Reports" value="21" ></item>            
            <item type="template" className="admin_tile licensereq" label="License Request" value="5" ></item>       
        </item>
        </items>';
?>