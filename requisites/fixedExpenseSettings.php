<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/BusinessBonusReportClass.php");
$BSRptObj = new BusinessBonusReportClass();
if (!isset($_GET["posStart"]))
    $_GET["posStart"]   = 0;
if (!isset($_GET["count"]))
    $_GET["count"]      = 50;

$BRSItems       = $BSRptObj->getfixedExpenseItems($preTally_user_ofid);

$filterData     = explode(",",$REQUEST['filter']);
$filter         = ' AND IT.OF_Id = '.$preTally_user_ofid;
if($filterData[0] != "" && $filterData[0] != "All" ) {
    $filter     .=' AND IT.IT_Id = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    $filter     .=' AND SH.SH_Name like "'.$filterData[1].'%"' ;
}
if($filterData[2] != '') {
    if($filterData[2] == 1) {
        $filter .=' AND IT.IT_Id IN ('.$BRSItems['BRS_General_Items'].')';
    } elseif($filterData[2] == 2 && $BRSItems['BRS_General_Items']) {
        $filter .=' AND IT.IT_Id NOT IN ('.$BRSItems['BRS_General_Items'].')';
    }
}
if($filterData[3] != '') {
    if($filterData[3] == 1) {
        $filter .=' AND IT.IT_Id IN ('.$BRSItems['BRS_Fixed_Items'].')';
    } elseif($filterData[3] == 2 && $BRSItems['BRS_Fixed_Items']) {
        $filter .=' AND IT.IT_Id NOT IN ('.$BRSItems['BRS_Fixed_Items'].')';
    }
}
if($filterData[4] != '') {
    if($filterData[4] == 1) {
        $filter .=' AND IT.IT_Id IN ('.$BRSItems['BRS_Variable_Items'].')';
    } elseif($filterData[4] == 2 && $BRSItems['BRS_Variable_Items']) {
        $filter .=' AND IT.IT_Id NOT IN ('.$BRSItems['BRS_Variable_Items'].')';
    }
}
if($filterData[5] != '') {
    
    $flt = '(';
    $fltr = '';
    if($BRSItems['BRS_Variable_Items'])
        $fltr .= $BRSItems['BRS_Variable_Items'].',';
    if($BRSItems['BRS_Fixed_Items'])        
        $fltr .= $BRSItems['BRS_Fixed_Items'].',';
    if($BRSItems['BRS_General_Items'])
        $fltr .= $BRSItems['BRS_General_Items'].',';
    
    $flt .= trim($fltr,',').')';
    
    if($filterData[5] == 1) {
        if($fltr)
        $filter .=' AND IT.IT_Id NOT IN '.$flt;
    } elseif($filterData[5] == 2) {
        $filter .=' AND IT.IT_Id IN '.$flt;
    }
}

$BSRptObj->listExpenseItems($filter,$_GET["posStart"],$_GET["count"]);
$BSRptGridObj       = $BSRptObj->ItemArray;
$tot_count          = $BSRptObj->listExpenseItemsCount($filter);
$BRS_Fixed_Items    = explode(',',$BRSItems['BRS_Fixed_Items']);
$BRS_Variable_Items = explode(',',$BRSItems['BRS_Variable_Items']);
$BRS_General_Items  = explode(',',$BRSItems['BRS_General_Items']);

if($tot_count < $_GET["count"]) $_GET["count"] = $tot_count;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="'.$tot_count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$tot_count.'</userdata>
    <userdata name="BRS_Fixed_Items">'.$BRSItems['BRS_Fixed_Items'].'</userdata>  
    <userdata name="BRS_Variable_Items">'.$BRSItems['BRS_Variable_Items'].'</userdata>
    <userdata name="BRS_General_Items">'.$BRSItems['BRS_General_Items'].'</userdata>';
    if ($_GET["posStart"] == 0 && !isset($REQUEST['filter']) ) {   
        echo '<head>  
            <settings>
                <colwidth>px</colwidth>
            </settings>
            <beforeInit> 
                <call command="setSkin">
                    <param>dhx_skyblue</param>
                </call> 
                <call command="enableColSpan">
                    <param>true</param>
                </call>
                <call command="setImagePath">
                    <param>assets/grid/codebase/imgs/</param>
                </call> 
            </beforeInit> 
        </head>';
    }
    $j  =   $_GET["posStart"]+1;
    if($BSRptGridObj) {
        foreach($BSRptGridObj as $rw) {
            echo '<row id="'.$rw->IT_Id.'">
            <cell title=" ">'.$j.'</cell>
            <cell title=" " name="IT_Name" >'.$rw->IT_Name.'</cell>
            <cell title=" " name="SH_Name" >'.$rw->SH_Name.'</cell>
            <cell>';
                if (in_array($rw->IT_Id, $BRS_Fixed_Items))   echo '1';
            echo '</cell>
            <cell>';
                if (in_array($rw->IT_Id, $BRS_Variable_Items))   echo '1';
            echo '</cell>
            <cell>';
            if (in_array($rw->IT_Id, $BRS_General_Items))   echo '1';
            echo '</cell>
            <cell>';
            if (!in_array($rw->IT_Id, $BRS_Fixed_Items) && !in_array($rw->IT_Id, $BRS_Variable_Items) && !in_array($rw->IT_Id, $BRS_General_Items))
            echo '1';       
            echo '</cell>
            </row>';
            $j++;
        }
    } else {
        echo '<row id="no_records" ><cell></cell><cell></cell><cell title= " " colspan = "3"><![CDATA[<div style="font-size:16px;color:#0979B1;font-family: serif;padding-top: 10px;" >No Records Found</div>]]></cell>
        <cell></cell><cell></cell></row>';
    }
echo '</rows>';
?>