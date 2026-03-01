<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	
}
require_once($BASEPATH . "preTallyClass/FeedbackClass.php");
$BugObj  = new FeedbackClass();
$BR_Id=$_REQUEST[BR_Id];


$BugObj ->getDetails("BR.BR_ModifiedUSId,BR.BR_Status,BR.BR_modifiedDate,BR.BR_AssignedUsId,BR.BR_Comment,CONCAT(US.US_FName,' ', US.US_LName) AS Name,CONCAT(UA.US_FName,' ', UA.US_LName) AS AssignedName ",
                     "bug_report_details AS BR LEFT JOIN users_auth as US ON US.US_Id=BR.BR_ModifiedUSId
                      LEFT JOIN users_auth as UA ON UA.US_Id=BR.BR_AssignedUsId",
                     " WHERE BR_Id=".$BR_Id ." ORDER BY BR.BRD_Id DESC");
$Bug_Obj   = $BugObj->getDetailsArray;
$BugObj->getBugreportsData($BR_Id);
$description=$BugObj->getBugreportsDataArray['BR_Desc' ];
$cdate=$BugObj->getBugreportsDataArray['BR_Date'];
$cUser=$BugObj->getBugreportsDataArray['CreatedUserName'];
?>

<link rel="stylesheet" type="text/css" href="assets/form/codebase/skins/dhtmlxform_dhx_skyblue.css">
<script src="assets/form/codebase/dhtmlxcommon.js"></script>
<script src="assets/form/codebase/dhtmlxform.js"></script>
<script language="javascript">
    
    var BS_Form_Data = [{
        type		: "settings",
        labelWidth	: 120,
        
    },    
    <?php
    $StatusArray=array(1=>"New",2=>"Read",3=>"Under Process",4=>"Completed");
    $j=0;
    $cdate=date_format(date_create($cdate), 'Y-m-d');
//    $TotCount=Count($Bug_Obj);
//    $TotCount=round(($TotCount)/2);
    foreach ($Bug_Obj as $rw) {
        
        
       // if($j==$TotCount){echo ' { type : "newcolumn", offset : "50"},';}
        echo '{type:"fieldset", name:"'.$j.'", label:"Updated Details", offsetLeft : "20",inputWidth:"auto", 
                 list:[
                    {type:"template", offsetLeft : "20",name:"Comment",label : "Comment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:", value : "'.str_replace( "\n", '<br />', wordwrap($rw->BR_Comment,100,"<br />",TRUE)).'"},
                    {type:"block" ,inputWidth:1150, offsetLeft:0,list:[{type:"template", offsetLeft:0,name:"Status",label : "Status",labelWidth:100, value : "   : &nbsp;&nbsp;&nbsp; '.$StatusArray[$rw->BR_Status].'  " },{ type : "newcolumn", offset : "50"},
                    {type:"template",inputWidth:120, name:"Assigned_To",label : "Assigned  To",labelWidth:100, value : "   : &nbsp;&nbsp;&nbsp; '.$rw->AssignedName.' " },{ type : "newcolumn", offset : "50"},
                    {type:"template",inputWidth:120, name:"modified_By",label : "Modified By",labelWidth:100, value : "   : &nbsp;&nbsp;&nbsp; '.$rw->Name.' " },{ type : "newcolumn", offset : "50"},
                    {type:"template",inputWidth:120, name:"modified_Date",label : "Modified Date",labelWidth:100, value : "   : &nbsp;&nbsp;&nbsp; '.date('d/m/Y',strtotime($rw->BR_modifiedDate)).'  " }]}    
                    
                ]},';
        $j++;
    }
        echo '{type:"fieldset", name:"Created Details",labelWidth:100,  label:"Created Details", offsetLeft : "20",inputWidth:"auto", width:100,
                 list:[
                    {type:"template", name:"Description",offsetLeft : "20",label : "Description&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:", value : "'.str_replace( "\n", '<br />', wordwrap($description,100,"<br />",TRUE)).'"},
                    {type:"block" ,inputWidth:1150, offsetLeft:0,list:[{type:"template", offsetLeft:0,name:"Created_By",label : "Created By",labelWidth:100, value : "   : &nbsp;&nbsp;&nbsp; '.$cUser.'  " },{ type : "newcolumn", offset : "50"},
                    {type:"template", name:"Created_Date",label : "Created Date", value : "   : &nbsp;&nbsp;&nbsp; '.date('d/m/Y',strtotime($cdate)).' " }]}
                    
                ]},';
   
    ?> 
 ];
    BSForm[<?php echo $BR_Id; ?>] = new dhtmlXForm("BS_Form_<?php echo $BR_Id; ?>", BS_Form_Data);  
</script>
<div id="BS_Form_<?php echo $BR_Id; ?>" style="margin: 10px 0;"></div>