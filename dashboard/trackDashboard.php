<?php
session_start();
include_once("../_conf.php");
include_once("../preTallyClass/AttestationClass.php");
$AttestObj   = new AttestationClass();
$strDate     = date("Y-m-01", strtotime("-5 months", strtotime(date("Y-m-d"))));
$endDate     = date("Y-m-d");
//$histEndDate = date("Y-m-01", strtotime("-6 months", strtotime(date("Y-m-d"))));
//$histStrDate = date("Y-m-01", strtotime("-5 months", strtotime($histEndDate)));
$OFId        = $_SESSION['preTally_user_ofid'];

$AttestObj->getDashBoardData($OFId);  //For counters on the top
$AttestObj->getPaymentAmount($OFId,$strDate,$endDate);  //For graph plotting Payment amount
$AttestObj->getCancelAmount($OFId,$strDate,$endDate);   //For graph plotting Cancelled amount
$AttestObj->getBussinessAmount($OFId,$strDate,$endDate);//For graph plotting Business/Job    
$AttestObj->getTopCollectors($OFId,$strDate,$endDate); //For side progress bars
$AttestObj->getJobCategories($OFId,date("m"),date("Y")); //For pie charts
$AttestObj->JobSummary($OFId,$strDate,$endDate);//For middle tabs jobs summary
$job_array = $AttestObj->JobArray;

/*
$AttestObj->JobSummary($OFId,$histStrDate,$histEndDate);//For history upto last 6 months
$history_array = $AttestObj->JobArray;

$diff_inc   =   $job_array['Total_Inc']-$history_array['Total_Inc'];
$diff_exp   =   $job_array['Total_Exp']-$history_array['Total_Exp'];
$diff_prof  =   $job_array['Profit']-$history_array['Profit'];



$inc_per         =  $exp_per        = $prof_per         = $job_per         = 0;
$arrow_inc_dir   = $arrow_exp_dir   = $arrow_prof_dir   = $arrow_job_dir   ="left";
$arrow_inc_color = $arrow_exp_color = $arrow_prof_color = $arrow_job_color ="yellow";

if($history_array['Total_Inc'] != 0){
    if($diff_inc < 0){
        $inc_per = $history_array['Total_Inc'] / $job_array['Total_Inc'] * 100;
        $arrow_inc_dir   = "down"; 
        $arrow_inc_color = "red";       
    }elseif($diff_inc > 0){ //echo $history_array['Total_Inc'];
        $inc_per = $job_array['Total_Inc'] / $history_array['Total_Inc'] * 100;
        $arrow_inc_dir   = "up"; 
        $arrow_inc_color = "green";       
    }
}

if($history_array['Total_Exp'] != 0){
    if($diff_exp < 0){
        $exp_per = $history_array['Total_Exp'] / $job_array['Total_Exp'] * 100;
        $arrow_exp_dir   = "down"; 
        $arrow_exp_color = "red";       
    }elseif($diff_exp > 0){ echo $history_array['Total_Inc'];
        $exp_per = $job_array['Total_Exp'] / $history_array['Total_Exp'] * 100;
        $arrow_exp_dir   = "up"; 
        $arrow_exp_color = "green";       
    }
}

if($history_array['Total_Prof'] != 0){
    if($diff_prof < 0){
        $prof_per = $history_array['Total_Prof'] / $job_array['Total_Prof'] * 100;
        $arrow_prof_dir   = "down"; 
        $arrow_prof_color = "red";       
    }elseif($diff_prof > 0){
        $prof_per = $job_array['Total_Prof'] / $history_array['Total_Prof'] * 100;
        $arrow_prof_dir   = "up"; 
        $arrow_prof_color = "green";       
    }
}

if($history_array['Total_Job'] != 0){
    if($diff_job < 0){
        $exp_job = $history_array['Total_Job'] / $job_array['Total_Job'] * 100;
        $arrow_job_dir   = "down"; 
        $arrow_job_color = "red";       
    }elseif($diff_job > 0){
        $exp_job = $job_array['Total_Job'] / $history_array['Total_Job'] * 100;
        $arrow_job_dir   = "up"; 
        $arrow_job_color = "green";       
    }
}
*/

$job_enq    =$AttestObj->countEnquiries($OFId);
$new_jobs   =$AttestObj->DataArray[1] ? $AttestObj->DataArray[1] : 0;
$deleted    =$AttestObj->DataArray[2] ? $AttestObj->DataArray[2] : 0;
$processing =$AttestObj->DataArray[3] ? $AttestObj->DataArray[3] : 0;
$completed  =$AttestObj->DataArray[4] ? $AttestObj->DataArray[4] : 0;
$delivered  =$AttestObj->DataArray[5] ? $AttestObj->DataArray[5] : 0;
$buss_amt   =$AttestObj->BussAmt;
$pay_amt    =$AttestObj->PayAmt;
$cnc_amt    =$AttestObj->CncJobAmt;
$collection =$AttestObj->TopBuss;    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>PreTally | Dashboard</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- Bootstrap 3.3.4 -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="bootstrap/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
            .info-box {
                margin-bottom: 9px;
            }
        </style>
        
    </head>
    <body class="skin-blue sidebar-mini">
        <div class="wrapper">

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <!-- Main content -->
                <section class="content">
                    <!-- Info boxes -->
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-aqua"><i class="ion ion-images"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">New Jobs</span>
                                    <span class="info-box-number"><?= $new_jobs?></span>
                                    <span class="info-box-text">Job Enquiry(s) - <?= $job_enq?></span>
                                </div><!-- /.info-box-content -->
                            </div><!-- /.info-box -->
                        </div><!-- /.col -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa ion-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Job Processing</span>
                                    <span class="info-box-number"><?= $processing?></span>
                                </div><!-- /.info-box-content -->
                            </div><!-- /.info-box -->
                        </div><!-- /.col -->

                        <!-- fix for small devices only -->
                        <div class="clearfix visible-sm-block"></div>

                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="ion ion-log-out"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Job Completed</span>
                                    <span class="info-box-number"><?= $completed?></span>
                                    <span class="info-box-text">Job Delivered - <?= $delivered?></span>
                                </div><!-- /.info-box-content -->
                            </div><!-- /.info-box -->
                        </div><!-- /.col -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-red"><i class="ion ion-trash-a"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Job Cancelled</span>
                                    <span class="info-box-number"><?= $deleted?></span>
                                </div><!-- /.info-box-content -->
                            </div><!-- /.info-box -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Monthly Recap Report</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <p class="text-center">
                                                <strong>Jobs & Income</strong><i style="padding-left:20px;">:  1st-<?=date("M-Y", strtotime("-5 months"))?> - <?= date("d-M-Y")?></i>
                                            </p>
                                            <div class="chart">
                                                <div id="salesLegend" style="float: right;width:200px;"></div>
                                                <!-- Sales Chart Canvas -->
                                                <canvas id="salesChart" height="180"></canvas>
                                                
                                            </div><!-- /.chart-responsive -->
                                        </div><!-- /.col -->
                                        <div class="col-md-4">
                                            <p class="text-center">
                                                <strong>Top Job Collectors</strong><i style="padding-left:20px;">:  1st-<?=date("M-Y", strtotime("-5 months"))?> - <?= date("d-M-Y")?></i>
                                            </p>
                                            <?php 
                                            $color_array = array("aqua","red","green","yellow");
                                            $total_collection = array_sum(array_column($collection,"TOTAL_BUSS"));
                                            
                                            for($i = 0; $i < 5; $i++){
                                                if($collection[$i]["TOTAL_BUSS"] > 0) {
                                                    $collection_perc=$collection[$i]["TOTAL_BUSS"]/$total_collection*100;
                                            ?>
                                            <div class="progress-group">
                                                <span class="progress-text"><?=ucfirst($collection[$i]["LC_Name"])?></span>
                                                <span class="progress-number"><b><?=$collection[$i]["TOTAL_BUSS"]?></b>/<b><?=$total_collection?></b></span>
                                                <div class="progress sm">
                                                    <div class="progress-bar progress-bar-<?=$color_array[$i]?>" style="width:<?php echo round($collection_perc);?>%"></div>
                                                </div>
                                            </div><!-- /.progress-group -->
                                            <?php } } ?>
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->
                                </div><!-- ./box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block border-right">
                                                <!--<span class="description-percentage text-<?=$arrow_inc_color?>"><i class="fa fa-caret-<?=$arrow_inc_dir?>"></i><?=$inc_per?>%</span>-->
                                                <h5 class="description-header"><?= $job_array["Total_Inc"]?></h5>
                                                <span class="description-text">TOTAL INCOME</span>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block border-right">
                                                <!--<span class="description-percentage text-<?=$arrow_exp_color?>"><i class="fa fa-caret-<?=$arrow_exp_dir?>"></i><?=$exp_per?>%</span>-->
                                                <h5 class="description-header"><?= $job_array["Total_Exp"]?></h5>
                                                <span class="description-text">TOTAL EXPENSE</span>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block border-right">
                                                <!--<span class="description-percentage text-<?=$arrow_prof_color?>"><i class="fa fa-caret-<?=$arrow_prof_dir?>"></i><?=$prof_per?>%</span>-->
                                                <h5 class="description-header"><?= $job_array["Profit"]?></h5>
                                                <span class="description-text">TOTAL PROFIT / LOSS</span>
                                            </div><!-- /.description-block -->
                                        </div><!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block">
                                                <!--<span class="description-percentage text-<?=$arrow_job_color?>"><i class="fa fa-caret-<?=$arrow_job_dir?>"></i><?=$job_per?>%</span>-->
                                                <h5 class="description-header"><?= $job_array["Total_CompJob"]?></h5>
                                                <span class="description-text">JOB COMPLETIONS</span>
                                            </div><!-- /.description-block -->
                                        </div>
                                    </div><!-- /.row -->
                                </div><!-- /.box-footer -->
                            </div><!-- /.box -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <div class="col-md-8">
                        <!-- MAP & BOX PANE -->

                        <div class="row">

                            <div class="col-md-6">
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Top Branches By Job : <i><?php echo date("F - Y"); ?></i></h3>
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="chart-responsive">
                                                    <canvas id="pieChart-trackBD_ByJob" height="150"></canvas>
                                                </div><!-- ./chart-responsive -->
                                            </div><!-- /.col -->
                                            <div class="col-md-4">
                                                <ul class="chart-legend clearfix">
                                                    <?php 
                                                    $legend_jobs_colors = array("red","green","yellow","aqua","light-blue","gray"); 
                                                    $other_job_amt = 0;
                                                    $i = 0;
                                                    foreach($AttestObj->JobsAmt as $jobs){
                                                        if($i < 5) { ?>
                                                            <li><i class="fa fa-circle-o text-<?=$legend_jobs_colors[$i]?>"></i>&nbsp;<?= ucfirst($jobs["LC_Name"]) ?></li>
                                                        <?php } else if($i==6) { ?>
                                                                <li><i class="fa fa-circle-o text-<?=$legend_jobs_colors[$i]?>"></i>&nbsp;Others</li>
                                                        <?php }
                                                        if($i < 5) $jobs_list .= '<li><a href="#">'.ucfirst($jobs["LC_Name"]).'<span class="pull-right text-'.$legend_jobs_colors[$i].'"><i class="fa fa-angle-right"></i> '.$jobs["TOTAL_BUSS"].'</span></a></li>';
                                                        else $other_job_amt+=$jobs["TOTAL_BUSS"];
                                                        $i++;
                                                    } ?>
                                                </ul>
                                            </div><!-- /.col -->
                                        </div><!-- /.row -->
                                    </div><!-- /.box-body -->
                                    <div class="box-footer no-padding">
                                        <ul class="nav nav-pills nav-stacked">
                                            <?=$jobs_list?>                                                                                       
                                            <li><a href="#">Others <span class="pull-right text-red"><i class="fa fa-angle-right"></i><?= $other_job_amt?></span></a></li>
                                        </ul>
                                    </div><!-- /.footer -->
                                </div><!-- /.box -->
                            </div><!-- /.col -->

                            <div class="col-md-6">
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Top Branches By Income : <i><?php echo date("F - Y"); ?></i></h3>
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="chart-responsive">
                                                    <canvas id="pieChart-trackBD_ByIncome" height="150"></canvas>
                                                </div><!-- ./chart-responsive -->
                                            </div><!-- /.col -->
                                            <div class="col-md-4">
                                                <ul class="chart-legend clearfix">
                                                   <?php $legend_inc_colors=array("red","green","yellow","aqua","light-blue","gray"); 
                                                    $i=0;
                                                    $other_inc_amt=0;
                                                    foreach($AttestObj->IncAmt as $incomes){                                                        
                                                        if($i < 5) { ?>
                                                            <li><i class="fa fa-circle-o text-<?=$legend_inc_colors[$i]?>"></i>&nbsp;<?= ucfirst($incomes["LC_Name"]) ?></li>
                                                        <?php
                                                        }else if($i == 6){ ?>
                                                           <li><i class="fa fa-circle-o text-<?=$legend_inc_colors[$i]?>"></i>&nbsp;Others</li> 
                                                        <?php }
                                                        if($i < 5) $inc_list .= '<li><a href="#">'.ucfirst($incomes["LC_Name"]).'<span class="pull-right text-'.$legend_inc_colors[$i].'"><i class="fa fa-angle-right"></i> '.$incomes["TOTAL_PAY"].'</span></a></li>';
                                                        else $other_inc_amt+=$incomes["TOTAL_PAY"];
                                                        $i++; 
                                                    } ?>
                                                </ul>
                                            </div><!-- /.col -->
                                        </div><!-- /.row -->
                                    </div><!-- /.box-body -->
                                    <div class="box-footer no-padding">
                                        <ul class="nav nav-pills nav-stacked">
                                            <?=$inc_list?>                                                                                       
                                            <li><a href="#">Others <span class="pull-right text-red"><i class="fa fa-angle-right"></i><?= $other_inc_amt?></span></a></li>
                                        </ul>
                                    </div><!-- /.footer -->
                                </div><!-- /.box -->

                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.col -->

                    <!-- Main row -->
                    <div class="row">
                        <!-- Left col -->

                        <div class="col-md-4">

                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Top Branches By Cancellation : <i><?php echo date("F - Y"); ?></i></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="chart-responsive">
                                                <canvas id="pieChart-trackBD_ByCancel" height="150"></canvas>
                                            </div><!-- ./chart-responsive -->
                                        </div><!-- /.col -->
                                        <div class="col-md-4">
                                            <ul class="chart-legend clearfix">
                                                <?php $legend_cnc_colors=array("red","green","yellow","aqua","light-blue","gray"); 
                                                    $i=0;
                                                    $other_cnc_amt=0;
                                                    foreach($AttestObj->CncAmt as $cancellation){                                                        
                                                        if($i<5) { ?>
                                                            <li><i class="fa fa-circle-o text-<?=$legend_cnc_colors[$i]?>"></i>&nbsp;<?= ucfirst($cancellation["LC_Name"]) ?></li>
                                                        <?php 
                                                        }else if($i == 6){ ?>
                                                            <li><i class="fa fa-circle-o text-<?=$legend_cnc_colors[$i]?>"></i>&nbsp;Others</li>
                                                        <?php }
                                                        if($i<5) $cnc_list.='<li><a href="#">'.ucfirst($cancellation["LC_Name"]).'<span class="pull-right text-'.$legend_cnc_colors[$i].'"><i class="fa fa-angle-right"></i> '.$cancellation["TOTAL_CNC"].'</span></a></li>';
                                                        else $other_cnc_amt+=$cancellation["TOTAL_CNC"];
                                                        $i++; 
                                                    } ?>
                                            </ul>
                                        </div><!-- /.col -->
                                    </div><!-- /.row -->
                                </div><!-- /.box-body -->
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-pills nav-stacked">
                                        <?=$cnc_list?>                                                                                       
                                            <li><a href="#">Others <span class="pull-right text-red"><i class="fa fa-angle-right"></i><?= $other_cnc_amt?></span></a></li>
                                    </ul>
                                </div><!-- /.footer -->
                            </div><!-- /.box -->



                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->


        </div><!-- ./wrapper -->
        <?php 
        $Att_JOB = array_slice($AttestObj->JobsAmt, 0, 5); 
        $Att_INC = array_slice($AttestObj->IncAmt , 0, 5); 
        $Att_CNL = array_slice($AttestObj->CncAmt , 0, 5); 
        if($other_job_amt > 0) $Att_JOB[5] = array('LC_Name' => 'Others','TOTAL_BUSS' => $other_job_amt );
        if($other_inc_amt > 0) $Att_INC[5] = array('LC_Name' => 'Others','TOTAL_PAY'  => $other_inc_amt );
        if($other_cnc_amt > 0) $Att_CNL[5] = array('LC_Name' => 'Others','TOTAL_CNC'  => $other_cnc_amt );
        ?>
        <script type="text/javascript">    
            var bussamtArray  = <?php echo json_encode(array_values($buss_amt)); ?>;
            var payamtArray   = <?php echo json_encode(array_values($pay_amt)); ?>; 
            var cncamtArray   = <?php echo json_encode(array_values($cnc_amt)); ?>; 
            var pieDataJobs   = <?php echo json_encode($Att_JOB); ?>; 
            var pieDataIncome = <?php echo json_encode($Att_INC); ?>; 
            var pieDataCancel = <?php echo json_encode($Att_CNL); ?>;
        </script>

        <!-- jQuery 2.1.4 -->
        <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- FastClick -->
        <script src='plugins/fastclick/fastclick.min.js'></script>
        <!-- AdminLTE App -->
        <script src="dist/js/app.min.js" type="text/javascript"></script>
        <!-- Sparkline -->
        <script src="plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
        <!-- jvectormap -->
        <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
        <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
        <!-- SlimScroll 1.3.0 -->
        <script src="plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <!-- ChartJS 1.0.1 -->
        <script src="plugins/chartjs/Chart.min.js" type="text/javascript"></script>

        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="dist/js/pages/dashboard2.js?a='<?= date("His")?>'" type="text/javascript"></script>

        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js" type="text/javascript"></script>        
    </body>
</html>