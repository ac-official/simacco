<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/UserClass.php");
$mealStats=array("Pending","Paid");
$UserObj = new UserClass();
$month=$REQUEST['meal_month'];
$year=$REQUEST['meal_year'];
$UserObj->mealAllowanceGrid($preTally_user_ofid,$year,ltrim($month, '0'));
$MealCardArray=$UserObj->UserArray;
echo '<rows>';
    if($MealCardArray) {
                $j = 1;
                foreach($MealCardArray as $rw) {

                    echo '<row id="'.$rw->SM_Id.'">

                        <cell>'.$j.'</cell>
                        <cell>'.$rw->US_EMPID.'</cell>
                        <cell>'.$rw->UNAME.'</cell>
                        <cell>'.$rw->LC_Name.'</cell>
                        <cell>'.date("F", mktime(0, 0, 0, $rw->SM_Month, 15)).'</cell>
                        <cell>'.$rw->SM_Year.'</cell>
                        <cell>'.$rw->SM_Amount.'</cell>                                                  
                        <cell>'.$mealStats[$rw->SM_Status].'</cell> 
                        <cell></cell>
                    </row>';
                    $j++;
                }
            }else {
                        echo '<row id="0"> 
                        <cell colspan="8"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                        </row>';
                    }
		  
echo '</rows>';