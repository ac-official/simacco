<?php
/** 
	* Add or edit Bill details names
	* Created By Bilin @ 23-09-2025
*/
if ( stristr($_SERVER['HTTP_ACCEPT'],'application/xhtml+xml')) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo "<items>";
	echo "<item type='hidden' name='eid' value='0'/>";
	echo "<item type='hidden' name='last_bill_id' value='0'/>";	
	echo "<item type='hidden' name='old_bill_id' value='0'/>";	
	echo "<item type='hidden' name='next_date' value=''/>";	
	echo "<item type='settings' position='label-left' labelWidth='132' inputWidth='245' noteWidth='150' offsetLeft='20' offsetTop='8'  />";


	echo "<item type='combo' offsetTop='30' name='bill_id' label='Bill Name' filterCache='true'  required='true'></item>";

	echo "<item type='combo' name='day_id' label='Bill Day' required='true' readonly='true' validate='^[0-9]+$'>";
		echo "<option value=''  selected='true' text='Select Day'/>";
        for ($i=1; $i<=31; $i++) {        	
            echo "<option value='".$i."' text='".$i."'/>";
        }
    echo "</item>";

    echo "<item type='combo' name='status' label='Status' readonly='true'>";
		echo "<option value='1' selected='true' text='Active'/>";
		echo "<option value='0' text='Blocked'/>";
    echo "</item>";

	echo "<item type='block' width='300' offsetTop='1' offsetLeft='113'>
		<item type='button' value='Save' name='saveAccBillr'/>
		<item type='newcolumn'/>
		<item type='button' value='Cancel' name='CancelAccBillr'/>
    </item>";
echo "</items>";



?>