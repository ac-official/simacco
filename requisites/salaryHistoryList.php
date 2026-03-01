<?php

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$SalHistObj = new AttendanceClass();
$US_Id=$_REQUEST[US_Id];
$Count=$SalHistObj->listSalaryHistoryData($preTally_user_ofid,$US_Id);
$SalHistArr = $SalHistObj->salHistDetailsArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
                       <head>';
echo '<column width="130" type="ro" sort="na">Sl No</column>';
                        foreach($SalHistArr as $rw){
                            $ct=count($rw->Incrmnts);
                            if($ct!=NULL){
                                  for($i=$ct;$i>=1;$i--){
                                        echo '<column width="90" type="ro" sort="na">'.$i.'</column>';
                                  }
                            }
                        }

		echo '	<settings>
				<colwidth>px</colwidth>
			</settings>
			<beforeInit> 
            	<call command="setSkin">
					<param>dhx_skyblue</param>
				</call> 
				<call command="setImagePath">
					<param>assets/grid/codebase/imgs/</param>
				</call> 
				<call command="enableSmartRendering">
					<param>false</param>
				</call>
            </beforeInit> 
		  </head>';
 echo '<row style="padding-left:10px;">';
 echo '<cell>Date</cell>';
  foreach($SalHistArr as $rw){
                            $ct=count($rw->Incrmnts);
                            if($ct!=NULL){
                                for($i=0;$i<=$ct;$i++){
                                     $formattedDate[$i] =$rw->IncrmntsDates[$i];
                                     $fDate[$i]= str_replace("-", "/", $formattedDate[$i]);
                                     $ex = explode("/", $fDate[$i]);
                                     $year= $ex[0];
                                     $month=$ex[1];
                                     $date =$ex[2];
                                      
                                            if(($year!=0)&&($month!=0)&&($date!=0)){
                                                 echo '<cell>'.$date. '/'.$month. '/'.$year.'</cell>';

                                            }

                                  }
                            }
                        }
 echo '</row>';

 echo '<row style="padding-left:10px;">';
 echo '<cell>Salary Amount</cell>';
    foreach($SalHistArr as $rw)
    {
        $ct=count($rw->Incrmnts);
         for($i=0;$i<=$ct;$i++){

                    echo '<cell>'.$rw->Incrmnts[$i].'</cell>';


          }

    }	
echo '</row>';
  
echo '<row style="padding-left:10px;">';
echo '<cell>Recent upraisals</cell>';
    foreach($SalHistArr as $rw)
    {
        $ct=count($rw->Incrmnts);
         for($i=0;$i<=$ct;$i++){
              $diff[$i]=$rw->Incrmnts[$i]-$rw->Incrmnts[$i+1];
                    
                    if($diff[$i]==$diff[$ct-1]){
                      echo '<cell>--</cell>';
                 } else if($diff[$i]!=0){
                      echo '<cell>'.$diff[$i].'</cell>';
                 } 

          }

    }	
echo '</row>';

echo '</rows>';
?>