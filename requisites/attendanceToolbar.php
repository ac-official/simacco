<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$month = array("","January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");


?>
<toolbar>
	<item id="holidays" type="button" img="images/icon/holidays_16.png" text="Holidays &#x25BE;" />
	
	<item id="sep6" type="separator"/>
	<item id="attendanceCalendar" type="button" text="Calendar &#x25BE;"  img="images/icon/calendar_16.png"/>
    
    <item id="sep01" type="separator"/>
    <item id="sep02" type="separator"/>
    <item id="AttMonth" type="buttonSelect" openAll="true" renderSelect="true" mode="select" text="<?php echo date('F'); ?>" width="70">
        <?php
		for($j=1;$j<=12;$j++) {
			echo '<item type="button" id="'.$j.'" text="'.$month[$j].'"/>';
		}
		?>
    </item>
    <item id="sep03" type="separator"/>
    <item id="AttYear" type="buttonSelect" openAll="true" renderSelect="true" mode="select" text="<?php echo date('Y'); ?>" width="50">
        <?php
		for($j=2014;$j<=2014;$j++) {
			echo '<item type="button" id="'.$j.'" text="'.$j.'"/>';
		}
		?>
    </item>
    <item id="sep04" type="separator"/>
	<item id="filter" type="button" img="images/icon/magnifier_16.png" text="Search" title="Filter"/>

</toolbar>