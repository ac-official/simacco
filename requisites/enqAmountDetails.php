<!DOCTYPE html>
<html>
    <head>
        
        <?php
        $width  = $_REQUEST['Width']-100;
        $height = $_REQUEST['Height'] + 100;
        
        $APSUAmntList  = json_decode($_REQUEST['AA_Process']);
        
        if(empty($APSUAmntList)) 
             $height = $_REQUEST['Height'] - 150;
        
        
        ?>
               
        <style>
            .amntCols {
                text-align: right;
                width: 100px;
            }
            .amountData {
                height: <?php echo $height; ?>px !important;
                overflow: auto;
            }
        </style>
    </head>
    <body>
<div id="wapper">
    

                          
<!--content start here-->
    <div id="content" style="overflow-x: hidden; overflow-y: auto;">
        <div> 
            <img id="printbtn" src="images/icon/18/print.gif" style="cursor: pointer;height: 22px; width: 25px;" onclick="javascript:preTally.Track.printProcessDetails();" /> 
            <img id="closebtn" src="images/icon/button-close.gif" style="cursor: pointer; float: right;" onclick="javascript:preTally.Track.hideEnqAmtDetailsPop();" /></br><b>Print</b>
        </div>
        
        <div class="heading"> Muble Solutions Pvt. Ltd </div>
        <!--<div class="heading2"> XLI-725, C-2 Second floor ,C.P Ummer Road,Ernakulam 682035 </div>-->
        <div class="heading2"> Ph. 0484-3934839,3934838	</div>
        <div class="clr"></div>

        <div class="heading">PROCESS DETAILS</div>
        
        <div style="width: <?php echo $width ?>px;" class="subTitleDiv">

        <div class="invoice" style="width: 300px important;">Destination Country : <label id="CN_To"><?php echo $_REQUEST['VisitingCN'];?></label><br />
            Certificate/Degree/Service : <label id="ADOC_Name"><?php echo $_REQUEST['Certificate'];?></label></div>
        
        
        <div class="date" style="width: 300px important;">Visa Type :	<label id="Visa_Type"><?php echo $_REQUEST['VisaType'];?></label><br />	
            Certificate issued by : <label id="APS_Name"><?php echo $_REQUEST['CRIssuedBy'];?></label>		
        </div>   
            
        </div>
        <div class="clr"></div>


        <!--table grid start here-->

        <div class="datagrid amountData">
            <!--<table>
                <thead><tr><th style="border-left:none;">Narration </th><th>Qty</th><th>Amount</th></tr></thead>
                <tbody><tr><td>ATTESTATION ADVANCE </td><td>1</td><td id="pageReceiptAmount">0</td></tr></tbody>
            </table>-->
            
            <table class="trackPrintTable trackPrintTableFirst" border="1">
                <thead>
                    <tr><th>Item</th><th>Normal Amount</th><th>Urgent Amount</th></tr>
                </thead>
                <tbody>
            <?php 

            $APSNAmntList  = json_decode($_REQUEST['TAP_N_Amount']);
            $APSUAmntList  = json_decode($_REQUEST['TAP_U_Amount']);
            
            $normalSum = $urgentSum = 0 ;
            $letterCount = 'A';
            if(!empty($APSUAmntList))   {
                foreach ($APSNAmntList as $key=>$value) {

                    $normalSum += $value[1] + $value[2] + $value[3] + $value[4] + $value[5] + $value[6] ;
                    $urgentSum += $APSUAmntList[$key][1] + $APSUAmntList[$key][2] + $APSUAmntList[$key][3] + $APSUAmntList[$key][4] + $APSUAmntList[$key][5] + $APSUAmntList[$key][6] ;
                    //echo '<table border="1">';
                        //echo '<th>'.$value[0].'</th><th>Urgent Amount</th><th>Normal Amount</th>';
                        echo '<tr class = "tableBodyRows" style="height: 50px; font-family: serif;" ><td colspan = "3" ><b>'.$letterCount++.'. '.$value[0].'</b></td></tr>';
                        echo '<tr class = "tableBodyRows" ><td>1. Statutory Amount</td><td class = "amntCols" >'.$value[1].'</td><td class = "amntCols" >'.$APSUAmntList[$key][1].'</td></tr>';
                        echo '<tr class = "tableBodyRows" ><td>2. Extra Amount</td><td class = "amntCols" >'.$value[2].'</td><td class = "amntCols" >'.$APSUAmntList[$key][2].'</td></tr>';
                        echo '<tr class = "tableBodyRows" ><td>3. Courier Charges</td><td class = "amntCols" >'.$value[3].'</td><td class = "amntCols" >'.$APSUAmntList[$key][3].'</td></tr>';
                        echo '<tr class = "tableBodyRows" ><td>4. Travelling Expense</td><td class = "amntCols" >'.$value[4].'</td><td class = "amntCols" >'.$APSUAmntList[$key][4].'</td></tr>';
                        echo '<tr class = "tableBodyRows" ><td>5. ManPower Amount</td><td class = "amntCols" >'.$value[5].'</td><td class = "amntCols" >'.$APSUAmntList[$key][5].'</td></tr>';
                        echo '<tr class = "tableBodyRows" ><td>6. Service Charges</td><td class = "amntCols" >'.$value[6].'</td><td class = "amntCols" >'.$APSUAmntList[$key][6].'</td></tr>';
                    //echo '</table>';
                    //echo '</br></br></br></br>';
                }
            }else{
                echo '<tr class = "tableBodyRows" style="height: 50px; font-family: serif;" ><td colspan = "3" ><b>No Records Found</b></td></tr>';
            }
            
            ?>               
                </tbody>
                <tfoot><tr class = "tableBodyRows" style="height: 70px; font-family: serif;" ><td><b>TOTAL</b></td><td class = "amntCols" ><b><?php echo $normalSum; ?></b></td><td class = "amntCols" ><b><?php echo $urgentSum; ?></b></td></tr>
                </tfoot></table>
                   
        </div>

        <!--table grid end here-->
<!--        <div class="clr"></div>

        <div class="div3"> For Muble Solutions PVT LTD 	</div> 
        <div class="clr"></div>
        <div class="div4"> Authorised Signatory  </div>  
        <div class="clr" id="receiptData" style="display: none;"></div>  -->

        <div>
<!--            <button id="printbtn" onclick="javascript:preTally.Track.printProcessDetails()">Print this page</button></div>-->

    </div><!--content end here-->

</div>
           
    </body>
</html>