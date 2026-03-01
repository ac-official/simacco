<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
?>
<menu>
	<item id="view" text="View">
            <item id="ficon" text="Small Icons" img="new.gif" imgdis="new_dis.gif"/>
            <item type="separator"/>
            <item id="ftiles" text="Medium Icons" img="print.gif" imgdis="print_dis.gif"/>
            <item type="separator"/>
            <item id="ftable" text="Tiles" img="close.gif" imgdis="close_dis.gif"/>
	</item>
	
        <!-- <item id="sortby" text="Sort by">
		<item id="new1" text="New Folder" img="new.gif" imgdis="new_dis.gif"/>
		<item id="file_sep_1" type="separator"/>
		<item id="open" text="Open" img="open.gif" imgdis="open_dis.gif"/>
		<item id="save" text="Save" img="save.gif" imgdis="save_dis.gif"/>
		<item id="saveAs" text="Save As..." img="save_as.gif" imgdis="save_as_dis.gif" enabled="false"/>
		<item id="file_sep_2" type="separator"/>
		<item id="print" text="Print" img="print.gif" imgdis="print_dis.gif"/>
		<item id="pageSetup" text="Page Setup" img="page_setup.gif" imgdis="page_setup_dis.gif" enabled="false"/>
		<item id="file_sep_3" type="separator"/>
		<item id="close" text="Close" img="close.gif" imgdis="close_dis.gif"/>
	</item>
	
        <item id="groupby" text="Group by">
		<item id="new1" text="New Folder" img="new.gif" imgdis="new_dis.gif"/>
		<item id="file_sep_1" type="separator"/>
		<item id="open" text="Open" img="open.gif" imgdis="open_dis.gif"/>
		<item id="save" text="Save" img="save.gif" imgdis="save_dis.gif"/>
		<item id="saveAs" text="Save As..." img="save_as.gif" imgdis="save_as_dis.gif" enabled="false"/>
		<item id="file_sep_2" type="separator"/>
		<item id="print" text="Print" img="print.gif" imgdis="print_dis.gif"/>
		<item id="pageSetup" text="Page Setup" img="page_setup.gif" imgdis="page_setup_dis.gif" enabled="false"/>
		<item id="file_sep_3" type="separator"/>
		<item id="close" text="Close" img="close.gif" imgdis="close_dis.gif"/>
	</item> -->
        <item type="separator"/>   
        <item id="open" text="Open" img="refresh.png" imgdis="refresh.png"/>
        <item id="rename" text="Rename" img="refresh.png" imgdis="refresh.png"/>
        <item type="separator"/>   
	<item id="delete" text="Delete" img="open.gif" imgdis="open_dis.gif"/>

        
	<item type="separator"/>
	<item id="refresh" text="Refresh" img="refresh.png" imgdis="refresh.png"/>
        <item type="separator"/>
	<item id="new" text="New">
            <item id="folder" text="Folder" img="open.gif" imgdis="open_dis.gif"/>
	</item>
        <item type="separator"/>
        <item id="property" text="Properties" img="properties.png" imgdis="properties.png"/>
</menu>