<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$filter = " AJDRD.Receive_LC_Id = $preTally_user_lcid ";
if( $REQUEST['filter'] == '' ){
    $filter .= " AND AJNB.AJNB_Status != 2 ";
}else{
    $filter .= " AND AJNB.AJNB_Status = ".$REQUEST['filter'];
}

$AttObj->getBatchDetails($filter,$_GET["posStart"],$_GET["count"]);
$NotfObj = $AttObj->DataArray;
$Count=$AttObj->getBatchDetailsCount($filter);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$Count.'</userdata>';
    if($_GET["posStart"]==0  && !isset($REQUEST['filter']) ) {
         echo '<head>
             <column width="60" type="ro" align="center" sort="na" >SlNo</column>
             <column width="*" type="ro" align="left" sort="na"><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Message</div>]]></column>
             <column width="200" type="ro" align="left" sort="na"  >Number of Documents</column>
             <column width="170" type="ro" align="center" sort="na"  >Status </column>
            <settings>
                <colwidth>px</colwidth>
            </settings>
            <beforeInit> 
            <call command="enableColSpan">
                    <param>true</param>
             </call> 
            </beforeInit>
            </head>';
    }
    if($NotfObj) {
         $j=$_GET["posStart"]+1;
        foreach($NotfObj as $rw) {
            if($rw->AJNB_Status == '0'){
                $editTitle="Not read"; 
                $img = 'mailCl.png';
            }else{
                $editTitle="Read";
                $img = 'mailOp.png';
            }
            echo '<row id="'.$rw->AJNB_Id.'">
                    <userdata name="UData_AJ_Id">'.$rw->AJNB_Id.'</userdata>
                    <cell title=" " name="">'.$j.'</cell>
                    <cell title=" " name="AJNB_Description">'.$rw->AJNB_Description.'</cell>
                    <cell title=" " name="NO_DOC">';echo $rw->NO_DOC ? $rw->NO_DOC : '0'; echo '</cell>
                    <cell title="'.$editTitle.'" name="AJNB_Status"><![CDATA[<img src="images/icon/'.$img.'" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
            echo '</row>';
            $j++;
        }
    }
    else {
        echo '<row id="0"> 
            <cell type="ro" colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>