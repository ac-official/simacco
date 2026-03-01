<?php
require_once('includes/sessiondetails.php');
require_once('includes/header.php');
?>
<div id="gridbox" style="background-color:white; width:100%;"></div>

<?php require_once("includes/plugin_declare.php"); ?>	 
    
<script type="text/javascript" language="javascript">

//------------- Grid Object ---------------------------------
mygrid.setHeader("#, Destination, State, Top List, Status, Edit");
mygrid.attachHeader("&nbsp;,#text_filter,#select_filter,#select_filter,#select_filter, &nbsp;");
mygrid.setInitWidths("50,*,200,80,50,50");
mygrid.setColAlign("center,left,left,center,center,center");
mygrid.setColTypes("ro,ed,ro,ch,ch,ro");
mygrid.init();
mygrid.loadXML("xml/destination.php");

//-------------- Reload XML After Adding New Row ----------------
function reLoadXML(table)
{
	mygrid.updateFromXML("xml/destination.php",true,true,mygrid.sortRows(0));
}
</script>
<?php require_once('includes/footer.php'); ?>



		