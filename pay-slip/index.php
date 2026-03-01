<?php
/*
  ** Created By Arun
*/
session_start();
$domain = "http://".$_SERVER['SERVER_NAME'];
//Only HR ACL can have to access this page
if(empty($_SESSION) ||  $_SESSION['preTally_user_acl']->ACL_SalStruct!=1 || $_SESSION['preTally_user_acl']->ACL_SalDetail!=1){
     header('Location:'.$domain);
}
?>
<!doctype html>
<html lang="en">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <head>
    <title>MUBLE::PAYSLIP GENERATOR</title>
    <meta name="robots" content="noindex">
    <meta name="author" content="ArunDev">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <style type="text/css">
      .hline{
            width: 100%;
            text-align: left;
            border-bottom: 1px solid #000;
            line-height: 0.1em;
            margin: 10px 0 20px;
          margin-bottom: 20px;
}.hline span {
  background: #fff;
  padding: 0 10px;
  font-size: 24px;
  color: #0088CC;
}
.active{
  border: 3px solid #007bff;
  color: #ffffff;
}
.btn-custom{min-width:130px ;}
.error{color: red}
</style>
<?php
$year   = date('Y');
$month  = date('m');
if($month == 1) {
  $year = $year-1;
}
$month  = $month-1;
?>
  </head>
  <body>
    <nav class="main-header navbar navbar-expand navbar-dark bg-info">
      <a class="navbar-brand" href="#">Pay Slip Generator</a>
    </nav><br>
    <div class="container">
      <div class="col-12 mb-2 pl-4 pr-4 hline"><span>Choose Template</span></div><br>
        <div class="row">
        <div class="col-md-3 offset-md-3">
          <div class="card card-primary">
            <img src="images/logo-urogulf.png" class="card-img-top" alt="Urogulf" height="100px">
            <div class="card-body">
              <button class="col-md-12 btn btn-lg btn-outline-primary choose-template" data-choose="1">SELECT</button>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-primary">
              <img src="images/muble-logo.png" class="card-img-top" alt="Muble" height="100px">
            <div class="card-body">
              <button class="col-md-12 btn btn-lg btn-outline-primary choose-template" data-choose="2">SELECT</button>
            </div>
          </div>
        </div>
      </div><!-- row -->
    <form action="print.php" target="_blank" method="post" id="paSlip" autocomplete="off">
    <input type="hidden" value="" name="template" id="template">
    <div class="col-12 mb-2 pl-4 pr-4 hline"><span>Personal Info</span></div><br>
    <section class="content">
      <div class="row">
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-body">
              <div class="form-group">
                <label for="inputName">Name</label>
                <input type="text" id="name" class="form-control" name="name" required>
              </div>
              <div class="form-group">
                <label for="inputName">Designation</label>
                <input type="text" id="designation" class="form-control" name="designation" required>
              </div>
              <div class="form-group">
                <label for="inputName">Employee Code</label>
                <input type="text" id="emp_code" class="form-control" name="emp_code" required>
              </div>
              <div class="form-group">
                <label for="inputName">PAN Number</label>
                <input type="text" id="pan_no" class="form-control" name="pan_no" required>
              </div>
            </div>
          </div>
        </div><!-- col-md-6 -->
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-body">
              <div class="form-group">
                <label for="inputName">Working Days</label>
                <input type="text" id="w_days" class="form-control number mon-lt" name="w_days" required MAXLENGTH="5">
              </div>
              <div class="form-group">
                <label for="inputName">Worked Days</label>
                <input type="text" id="wd_days" class="form-control number mon-lt" name="wd_days" required MAXLENGTH="5">
              </div>
              <div class="form-group">
                <label for="inputName">Sundays and Holidays</label>
                <input type="text" id="holiday" class="form-control number mon-lt" name="holiday" required MAXLENGTH="5">
              </div>
              <div class="form-group">
                <label for="inputName">LOP</label>
                <input type="text" id="lop" value="0" class="form-control number mon-lt" name="lop" required MAXLENGTH="5">
              </div>
            </div>
          </div>
        </div><!-- col-md-6 -->
      </div><!-- row -->
    <div class="col-12 mb-2 pl-4 pr-4 hline"><span>Salary Info</span></div><br>
    <section class="content">
      <div class="row">
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">EMOLUMENTS</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="inputName">Basic Salary ( BS )</label>
                <input type="text" id="basic" class="form-control number addition" name="basic" required>
              </div>
              <div class="form-group">
                <label for="inputDescription">House Rent Allowance ( HRA )</label>
                 <input type="text" id="hra" class="form-control number addition" name="hra" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputStatus">Medical Allowance</label>
                 <input type="text" id="m_allow" class="form-control number addition" name="m_allow" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputClientCompany">Conveyance Allowance</label>
                <input type="text" id="c_allow" class="form-control number addition" name="c_allow" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputProjectLeader">City Compensatory Allowance ( CCA )</label>
                <input type="text" id="cca" class="form-control number addition" name="cca" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputProjectLeader">Other Emoluments</label>
                <input type="text" id="o_emo" class="form-control number addition" name="o_emo" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputProjectLeader">Previous Adjustment</label>
                <input type="text" id="p_adj" class="form-control number addition" name="p_adj" placeholder="0">
              </div>
              
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <div class="col-md-6">
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">DEDUCTIONS</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="inputEstimatedBudget">Professional Tax ( PT )</label>
                <input type="text" id="pt" class="form-control number deduction" name="pt" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputSpentBudget">Tax Deducted at Source ( TDS )</label>
                <input type="text" id="tds" class="form-control number deduction" name="tds" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputEstimatedDuration">Other Deductions ( Loan, etc. )</label>
                <input type="text" id="loan" class="form-control number deduction" name="loan" placeholder="0"> 
              </div>
              <div class="form-group">
                <label for="inputEstimatedDuration">EPF</label>
                <input type="text" id="epf" class="form-control number deduction" name="epf" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputEstimatedDuration">ESI</label>
                <input type="text" id="esi" class="form-control number deduction" name="esi" placeholder="0">
              </div>
              <div class="form-group">
                <label for="inputEstimatedDuration">LWF</label>
                <input type="text" id="lwf" class="form-control number deduction" name="lwf" placeholder="0">
              </div>
               <div class="form-group">
                <label for="inputEstimatedDuration">LOP Amount</label>
                <input type="text" id="lop_amt" class="form-control number deduction" name="lop_amt" placeholder="0">
              </div>
              
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-body">
                <div class="form-group">
                <label for="inputProjectLeader">Gross Pay</label>
                <input type="text" id="g_pay" class="form-control number" name="g_pay" readonly>
              </div>

              </div>
            </div><!-- c seco -->
        </div><!-- col-6 -->
        <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-body">
                 <div class="form-group">
                <label for="inputProjectLeader">Net Pay</label>
                <input type="text" id="net_pay" class="form-control number" name="net_pay" readonly>
              </div>

              </div>
            </div><!-- c seco -->
        </div>
      </div><!-- row -->
      <div class="col-12 mb-2 pl-4 pr-4 hline"><span>Payment Details</span></div><br>
      <div class="row">
      <div class="col-md-6">
          <div class="card card-secondary">
            
            <div class="card-body">
              <div class="form-group">
                <label for="inputEstimatedDuration">Month</label>
                <select name="month" class="form-control" required id="month">
                  <option value="">Select</option>
                  <option value="January" <?php echo ($month == "1")? "selected":""; ?>>January</option>
                  <option value="February" <?php echo ($month == "2")? "selected":""; ?>>February</option>
                  <option value="March" <?php echo ($month == "3")? "selected":""; ?>>March</option>
                  <option value="April" <?php echo ($month == "4")? "selected":""; ?>>April</option>
                  <option value="May" <?php echo ($month == "5")? "selected":""; ?>>May</option>
                  <option value="June" <?php echo ($month == "6")? "selected":""; ?>>June</option>
                  <option value="July" <?php echo ($month == "7")? "selected":""; ?>>July</option>
                  <option value="August" <?php echo ($month == "8")? "selected":""; ?>>August</option>
                  <option value="September" <?php echo ($month == "9")? "selected":""; ?>>September</option>
                  <option value="October" <?php echo ($month == "10")? "selected":""; ?>>October</option>
                  <option value="November" <?php echo ($month == "11")? "selected":""; ?>>November</option>
                  <option value="December" <?php echo ($month == "12")? "selected":""; ?>>December</option>
                </select>
              </div>
                 <div class="form-group">
                <label for="inputEstimatedDuration">Bank</label>
                <input type="text" id="bank" class="form-control" name="bank">
              </div>
            </div>
            </div>
            </div><!-- col-md-6 -->
             <div class="col-md-6">
          <div class="card card-secondary">

            <div class="card-body">
                <div class="form-group">
                <label for="inputEstimatedDuration">Year</label>
                <input type="text" id="year" class="form-control number" name="year" value="<?php echo $year; ?>" maxlength="4" required>
              </div>
                <div class="form-group">
                <label for="inputEstimatedDuration">Account Number</label>
                <input type="text" id="inputEstimatedDuration" class="form-control number" name="ac_no">
              </div>
            </div>
            </div>
            </div><!-- col-md-6 -->
          </div><!-- row -->
          <br>
      <div class="row">
        <div class="col-12">
          <input type="reset" value="Reset" class="btn btn-outline-secondary float-right btn-custom">
          <input type="submit" value="Print" class="btn btn-outline-info  float-right btn-custom" style="margin-right: 10px;">
        </div>
      </div><br>
    </section>
  </form>
  </div><!-- container -->
<footer class="page-footer font-small blue">

  <!-- Copyright -->
  <div class="footer-copyright text-center py-3">© <?php echo date('Y');?> Copyright:
    <a href="https://mublesolutions.com" target="_blank"> mublesolutions.com</a>
  </div>
  <!-- Copyright -->
</footer>
<!-- Footer -->
  <script type="text/javascript" src="js/jquery-3.2.1.slim.min.js"></script>
   <script type="text/javascript" src="js/jquery.validate.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $('.choose-template').click(function(){
        var choose = $(this).data('choose');
        localStorage.setItem("template", choose);
        $('#template').val(choose);
        $('.card').removeClass('active');
        $(this).parents('.card:first').addClass('active');
      });
      $('.choose-template:first').trigger('click');
      $('#paSlip').validate();
      $('.addition').keyup(function(){
         var gross = 0;
         $('.addition').each(function(){
          if($(this).val()!="")
            gross=gross+parseInt($(this).val());
          });
         $('#g_pay').val(gross);
         getNPay();

      });
       $('.deduction').keyup(function(){
         getNPay();
      });
       function getNPay(){
          var net = 0;
         var total_dedu =0;
         var gross = $('#g_pay').val();
         $('.deduction').each(function(){
          if($(this).val()!="")
            total_dedu=total_dedu+parseInt($(this).val());
          });
         $('#net_pay').val(gross-total_dedu);
       }
       //month limit checking days calculation not exceed 31 set to 30 -- 11-6-22
        $(".mon-lt").keyup(function(){
            if(parseFloat($(this).val()) > 31) {
              $(this).val(30);
            }
        });
    });
  </script>
  </body>
</html>
