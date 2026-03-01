<?php
require_once($BASEPATH . "preTallyClass/ReportClass.php");
$sStartDate="2014-10-07";
$sEndDate="2014-10-20";
$sStartDate = date("Y-m-d", strtotime($sStartDate));  
$sEndDate = date("Y-m-d", strtotime($sEndDate));  
$aDays[] = $sStartDate;  
$sCurrentDate = $sStartDate;  
while($sCurrentDate < $sEndDate){  
CreateBalances($sCurrentDate);    
$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));  

}  
function CreateBalances($stDate)
{
$Rep_Obj=new ReportClass();
$Rep_Obj->CalcBalances($stDate,36);//currentdate & office ids
$RepObj=$Rep_Obj->ReportArray;
$expincarray[]=array();
$i=0;
$lcid=0;
foreach($RepObj as $rw)
{    
    if($lcid!=$rw->LC_Id && $lcid!=0)
    {
        $i++;
    }
    
 $expincarray[$i][1]=$rw->LC_Id; //location
  $expincarray[$i][4]=$rw->OB_OpenBal; //current day opening balance
  $expincarray[$i][5]=$rw->OB_Id; //current day opening balance id
  $expincarray[$i][6]=$rw->OF_Id; // office  id
 //echo $i."-".$rw->LC_Id."-".$rw->INC."<br/>"; 
    if($rw->MH_Type==1)
    {
       $expincarray[$i][2]=$rw->INC; //current day income
    }
    if($rw->MH_Type==2)
    {
       $expincarray[$i][3]=$rw->INC; //current day expense
    }
   
    $lcid=$rw->LC_Id;
}

foreach($expincarray as $arr)
{   $obid=$arr[5];
    $ofid=$arr[6];
    $lcid=$arr[1];
    $close=$arr[2]+$arr[4]-$arr[3];   
    
    //echo "Date:".$stDate."  LC_Id:".$arr[1]." OB_Id:".$arr[5]." INC:".$arr[2]." EXP:".$arr[3]." OPENING:".$arr[4]." Close:".$close."<br/>";
    
           $amt=$close;  
       
  //echo $lcid."-".$amt."<br/>";
    $Rep_Obj->createBalances($obid,$ofid,$lcid,$amt,$stDate);
 
}
}
?>



