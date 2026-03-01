<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

?>
<toolbar>
    <item type="separator"/>
    <item type="separator"/>
    
    <item id="authentication" type="button" img="images/icon/lock_16.png" text="Authentication Details" />
    <item type="separator"/>
    <item type="separator"/>
    
    <item id="personal" type="button" text="Personal Details"  img="images/icon/calendar_16.png" />
    <item type="separator"/>
    <item type="separator"/>

    <item id="educational" type="button" img="images/icon/magnifier_16.png" text="Qualification Details" />
    <item type="separator"/>
    <item type="separator"/>
    
    <item id="account" type="button" img="images/icon/account_16.png" text="Account Details" />
    <item type="separator"/>
    <item type="separator"/>
    
    <item id="salary" type="button" img="images/icon/salary_icon_16.png" text="Salary Details" />
    <item type="separator"/>
    <item type="separator"/>

</toolbar>