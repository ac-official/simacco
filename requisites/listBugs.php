<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/FeedbackClass.php");
$BugObj = new FeedbackClass();
if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;
$filter ='';
$filterData = explode("--",$REQUEST['filter']);
$StatusArray=array(1=>"New",2=>"Read",3=>"Under Process",4=>"Completed");
if($filterData){
    if($filterData[0]){
        $filter .=' AND Month(BR.BR_Date) = "'.$filterData[0].'" ' ;
    }
    if($filterData[1]) {
        $filter .=' AND BR.BR_Type = "'.$filterData[1].'" ' ;
    }
    if($filterData[2]){
        $filter .=' AND  BR.BR_Status = "'.$filterData[2].'" ' ;
    } 
}
if($preTally_user_id==1){
    $filter.=" ORDER BY BR.BR_Date DESC";
}
else{
    $filter.=" AND BR.OF_Id=".$preTally_user_ofid." ORDER BY BR.BR_Date DESC";
}
$BugObj->viewBugReports($filter,$_GET["posStart"],$_GET["count"] );
$Bug_Obj = $BugObj->ReportBugArray;
$Count=$BugObj->viewBugReportsCount($filter);
$Bug_Type = array("","Accounts","Technical","Others");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">';
echo '    <userdata name="TL_Count">'.$Count.'</userdata>';

$j=1+$_GET["posStart"];
		  if($Bug_Obj) {
				$l = 1;
				foreach($Bug_Obj as $rw) {					
					echo '<row id="'.$rw->BR_Id.'">						                                        
						<cell>'.$j.'</cell>
                                                <cell type="sub_row_ajax">requisites/bugReportDetails.php&amp;BR_Id='.$rw->BR_Id.'</cell>
                                                <cell name="BR_Subject">'.$rw->BR_Subject.'</cell>
                                                <cell name="BR_Type">'.$Bug_Type[$rw->BR_Type].'</cell>    
                                                <cell name="US_Name">'.$rw->US_FName. ' '.$rw->US_LName.'</cell>
                                                <cell name="US_Mobile1">'.$rw->US_Mobile1.'</cell>
                                                <cell name="US_Email">'.$StatusArray[$rw->BR_Status].'</cell>
                                                <cell name="BR_Date">'.date('d/m/Y h:i:s',strtotime($rw->BR_Date)).'</cell>    
                                                <cell type="ro"><![CDATA[<img src="images/icon/TextEditicon.png" style="margin:2px 0; cursor:pointer; "class="target_'.$rw->BR_Id.'" onclick="preTally.Settings.showCommentWin(this,'.$rw->BR_Id.');" onmouseover="preTally.Settings.showLabel(this,\'Add Updations\');" onmouseout="preTally.Settings.hideLabel(this);" />]]></cell></row>';	
                                        $j++;
				}
                    }else {
                        echo '<row id="0"> 
                        <cell colspan="8"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                        </row>';
                    }
                       		  
echo '</rows>';
?>