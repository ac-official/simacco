<?php
function encode($input) {
    $temp = '';
    $length = strlen($input);
    for ($i = 0; $i < $length; $i++)
        $temp .= '%' . bin2hex($input[$i]);
    return $temp;
}

//-------------  Verify Opening Balanace is Set ----------------------
require_once($BASEPATH . "preTallyClass/CashBSClass.php");

$BankBSObj = new CashBSClass();

$OB_Status = $BankBSObj->VerifyCashOBStatus('LC_Id = ' . $preTally_user_lcid);

//--------------------------------------------------------------------
//echo $ACL_Obj->ACL_BSheet;
//if($ACL_Obj->ACL_BSheet == 5)
//    $reptFltrID = '';
//if($ACL_Obj->ACL_BSheet == 4)
//    $reptFltrID = 'OF_'.$preTally_user_ofid;
//if($ACL_Obj->ACL_BSheet == 3)
//    $reptFltrID = 'OF_'.$preTally_user_ofid.'-DP_'.$preTally_user_dpid;
//if($ACL_Obj->ACL_BSheet == 2)
//    $reptFltrID = 'OF_'.$preTally_user_ofid.'-LC_'.$preTally_user_lcid;
//if($ACL_Obj->ACL_BSheet == 1)
//    $reptFltrID = 'OF_'.$preTally_user_ofid.'-LC_'.$preTally_user_lcid.'-DP_'.$preTally_user_dpid;
//if($ACL_Obj->ACL_BSheet == 0)
//    $reptFltrID = 'OF_'.$preTally_user_ofid.'-LC_'.$preTally_user_lcid.'-DP_'.$preTally_user_dpid.'-US_'.$preTally_user_id;
//echo $reptFltrID;

$reptFltrID = 'OF_' . $preTally_user_ofid;
//08-04-2025 hide mark in selected company
$markattence = ($preTally_user_ofid == 47) ? 0 :1;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
-->
<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class=""> <!--<![endif]-->
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZFREZ39YC4"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', 'G-ZFREZ39YC4');
              var RGV2aWNlVW5pcXVlSWQ="";
              // const MAX_RETRIES=3,RETRY_DELAY=2e3;let retryCount=0;function loadTrackerJS(){return import("./scripts/ZmluZ2VycHJpbnQ.js").then((e=>e.load())).then((e=>e.get())).then((e=>{RGV2aWNlVW5pcXVlSWQ=e.visitorId})).catch((e=>{if(e.message.includes("TIMEOUT")?console.error("Request timed out."):console.error("Error loading FJS:",e.message),retryCount<3)return retryCount++,console.log(`Retrying... Attempt #${retryCount} in 2 seconds.`),new Promise((e=>setTimeout(e,2e3))).then(loadTrackerJS);console.error("Maximum retries reached. Giving up.")}))}loadTrackerJS();
            </script>

            <meta charset="<?php echo Settings::getPublic('site_charset'); ?>" />
            <title><?php echo Settings::getPublic('site_title'); ?></title>

            <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
            <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
            <script language="javascript">
                var KEY = '<?php echo isset($_COOKIE["so-catch"]) ? $_COOKIE["so-catch"]:'';?>';
                var IP = '<?php echo  $_SERVER['REMOTE_ADDR'];?>';
                var NAME ='<?php echo $_SESSION['preTally_user_name']."(".$_SESSION['preTally_user_empid'].")";?>';                
                var JGG1P3bDnUSDL1USRIDJS = '<?php echo ($_SESSION['preTally_user_uname'] != "") ? encode($_SESSION['preTally_user_uname']) : encode($_SESSION['preTally_user_empid']) ; ?>';
                var JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($reptFltrID); ?>';
                var JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_BSheet); ?>';
			    var JGG1P3bDnUSDL2Mui7KzYjj29UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_BSheet_VM); ?>';
				var JGG1P3bDnUSDL2Mui7KzYjj27UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_HR); ?>';
                var JGG1P3bDnUSDL2Mui7KzYjj26UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_MnthlyAttendance); ?>';
                var JGG1P3bDnUSDL3Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($preTally_user_id); ?>';
                var JGG1P3bDnUSDL4Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($att_flag); ?>';
                var JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_Item); ?>';
                var JGG1P3bDnUSDL6Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_MasterReports); ?>';
                var JGG1P3bDnUSDL7Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_NotfLC); ?>';
                var JGG1P3bDnUSDL8Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_NotfBNK); ?>';
                var JGG1P3bDnUSDL9Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($preTally_user_empid); ?>';
                var JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($preTally_user_ofid); ?>';
                var JGG1P3bDnUSDL11Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($OB_Status); ?>';
                var JGG1P3bDnUSDL12Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($pretally_offzAdmin); ?>';
                var JGG1P3bDnUSDL13Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_ApproveLeave); ?>';
                var JGG1P3bDnUSDL14Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_AttendanceEdt); ?>';
                var JGG1P3bDnUSDL15Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_Payroll); ?>';
                var JGG1P3bDnUSDL16Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_PayrollEdt); ?>';
                var JGG1P3bDnUSDL17Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_SalPMwiseBranch); ?>';
                var JGG1P3bDnUSDL18Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_SalPMwiseAll); ?>';
                var JGG1P3bDnUSDL19Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_Attendance); ?>';
                var JGG1P3bDnUSDL20Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_DeleteEntries); ?>';                
                var JGG1P3bDnUSDL21Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($preTally_user_name); ?>';
                var JGG1P3bDnUSDL22Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($preTally_user_lcid); ?>';
                var JGG1P3bDnUSDL23Mui7KzYjj28UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_ZonalHead); ?>';
		var JGG1P3bDnUSDL24Mui7KzYjj30UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_EditAccEntries) ?>';
		var JGG1P3bDnUSDL25Mui7KzYjj30UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_MnthlyAttendanceSummary); ?>';    // 23-06-2025  By Achu
		var JGG1P3bDnUSDL26Mui7KzYjj30UjPdWxCCtGkJSHeuo = '<?php echo encode($ACL_Obj->ACL_ManageLateEntry); ?>';    // 01-07-2025  By Achu
		var JGG1P3bDnUSDL27Mui7KzYjj30UjPdWxCCtGkJSHeuo = '<?php echo encode($UserACLObj->add_break_time); ?>'; 
                var JGG1PMRKATNC = '<?php echo $markattence; ?>';
                var base_path 	= '<?php echo Settings::getPublic('base_path'); ?>';                
                var remote_path = '<?php echo Settings::getPublic('remote_address'); ?>'; // 21-04-2025
                var icon_path 	= '<?php echo Settings::getPublic('icon_path'); ?>';

                var currency = '<?php echo $currency; ?>';
                //var socket;
            </script>
            <link rel="stylesheet" type="text/css" href="css/preTally.style.css"></link>

            <!-- <script type="text/javascript" src="scripts/socket.io-1.2.0.js"></script> -->
            <script src = "scripts/socket.io-2.1.1.js" > </script> 
            <!-- <script type="text/javascript" src="scripts/analytics.js"></script> -->
            <script type="text/javascript" src="scripts/preTally.Assets.js?a=4"></script>
            <script type="text/javascript" src="scripts/nicEdit.js?a=4"></script>
            <script type="text/javascript" src="scripts/preTally.Main.js?a=13"></script>
            <script type="text/javascript" src="scripts/preTally.Initialize.js?a=16"></script>
            <script type="text/javascript" src="scripts/preTally.Settings.js?a=38"></script>
            <script type="text/javascript" src="scripts/preTally.UserProfile.js?a=38"></script>
            <script type="text/javascript" src="scripts/preTally.BalanceSheet.js?a=08"></script>
            <script type="text/javascript" src="scripts/preTally.Item.js?a=14"></script>
            <script type="text/javascript" src="scripts/preTally.Notification.js?a=15"></script>
            <script type="text/javascript" src="scripts/preTally.Salary.js"></script>
            <script type="text/javascript" src="scripts/preTally.MasterReports.js?a=29"></script>
            <script type="text/javascript" src="scripts/preTally.ExportReports.js?a=1"></script>
            <script type="text/javascript" src="scripts/preTally.Reports.js?a=16"></script>
            <script type="text/javascript" src="scripts/preTally.EntryEdit.js?a=8"></script>
            <script type="text/javascript" src="scripts/preTally.BusinessReport.js?a=12"></script>
            <script type="text/javascript" src="scripts/preTally.StockReport.js?a=15"></script>
            <script type="text/javascript" src="scripts/preTally.TrackDupReports.js?a=15"></script>
            <script type="text/javascript" src="scripts/preTally.CashBalanceSheet.js?a=23"></script>
            <script type="text/javascript" src="scripts/preTally.BankBalanceSheet.js?a=14"></script>
            <script type="text/javascript" src="scripts/preTally.BranchReports.js?a=8"></script>
            <script type="text/javascript" src="scripts/preTally.BranchBSReports.js?a=21"></script>
            <script type="text/javascript" src="scripts/preTally.Track.js?a=12"></script>
            <script type="text/javascript" src="scripts/preTally.Reload.js?a=13"></script>
            <script type="text/javascript" src="scripts/preTally.Functions.js"></script>
            <!-- <script type="text/javascript" src="scripts/preTally.Crypt.js"></script> -->
            <script type="text/javascript" src="scripts/preTally.Data.js?a=3"></script>
            <script type="text/javascript" src="scripts/preTally.Menu.js?a=29"></script>
            <script type="text/javascript" src="scripts/preTally.Validate.js?a=3"></script>
            <script type="text/javascript" src="scripts/preTally.Upload.js"></script>
            <script type="text/javascript" src="scripts/date.js"></script>
            <script type="text/javascript" src="scripts/formatCurrency.js"></script>
            <script type="text/javascript" src="scripts/idle.js"></script>
            <script type="text/javascript" src="scripts/lightbox.min.js"></script>
            <script type="text/javascript" src="scripts/preTally.BulkUpload.js?a=5"></script>
            <script type="text/javascript" src="scripts/preTally.ManageTracks.js?a=2"></script>
            <script type="text/javascript" src="scripts/preTally.ManageUnused.js?a=1"></script>
            <script type="text/javascript" src="scripts/preTally.History.js?a=1"></script>
            <script type="text/javascript" src="scripts/preTally.Office.js?a=1"></script>
            <script type="text/javascript" src="scripts/preTally.ExpenseControl.js?a=4"></script>
            <script type="text/javascript" src="scripts/preTally.PettyCash.js?a=4"></script>
            <script type="text/javascript" src="scripts/preTally.MyWallet.js?a=4"></script>
            <script type="text/javascript" src="scripts/preTally.BranchMasterReports.js"></script>
            <script type="text/javascript" src="scripts/preTally.AccSummaryReports.js"></script>
            <script type="text/javascript" src="scripts/preTally.MyBankBook.js?r=1"></script>
            <script type="text/javascript" src="scripts/preTally.BusinessBonusReport.js"></script>
            <script type="text/javascript" src="scripts/preTally.TrackReports.js"></script>
            <script type="text/javascript" src="scripts/preTally.JobCountReports.js"></script>
            <script type="text/javascript" src="scripts/preTally.CertificateCountReports.js"></script>
            <script type="text/javascript" src="scripts/preTally.EnquiryCountReports.js"></script>
            <script type="text/javascript" src="scripts/preTally.IncomeProfitReport.js"></script>
            <script type="text/javascript" src="scripts/preTally.TrackOldJobProcesses.js"></script>
            <script type="text/javascript" src="scripts/preTally.BusinessReportManage.js"></script>
            <script type="text/javascript" src="scripts/preTally.GoogleAddress.js"></script>
            <script type="text/javascript" src="scripts/preTally.FileUpload.js"></script>
            <script type="text/javascript" src="scripts/preTally.IM.js"></script>
            <script type="text/javascript" src="scripts/preTally.MasterReportsLocationBased.js"></script>
            <script type="text/javascript" src="scripts/preTally.UserIEReports.js"></script>
            <script type="text/javascript" src="scripts/preTally.Descriptions.js"></script>
            <script type="text/javascript" src="scripts/preTally.AccountsTeam.js?r=26"></script>
            <script type="text/javascript" src="scripts/preTally.Attendance.js?r=8"></script><!-- 11-06-2025 -->
             <!--<script type="text/javascript" src="scripts/preTally.Purchase.js"></script> -->
            <link rel="stylesheet" type="text/css" href="css/emojionearea.min.css" media="screen"/>
            <script type="text/javascript" src="scripts/emojionearea.js"></script>
            <script src="./scripts/ZmluZ2VycHJpbnQ.js?r=2" defer></script>
            
        </head>
        <body>
            <div class="preloader">
                <div class="overlay"></div>
                <div class="preloader_img"></div>
            </div>

            <div class="netConnectivity">
                <div class="overlay"></div>
                <div class="netConnectivity_msg"></div>
            </div>
            <img src="images/logo.png" class="logo_img" />
            <div id="gtipObj"></div>
            <div id="CalObj"></div>

            <div id="supportingDocsInnerGrid"></div>


            <div style="display:none;">
                <form method="post">
                    <input type="file" name="name" id="uptext" onChange="ajaxUpload(this.form, 'imageUpload.php?filename=name&amp;maxSize=9999999999&amp;maxW=1000&amp;relPath=uploads/profileImage/&amp;colorR=255&amp;colorG=255&amp;colorB=255&amp;maxH=400', document.getElementById('prog_bar').value, '&lt;br /&gt;&lt;img src=\'images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' /&gt;', '&lt;img src=\'images/error.gif\' width=\'16\' height=\'16\' border=\'0\' /&gt; Error in Upload.');
                    return false;" />
                    <input type="hidden" id="prog_bar" value="">
                </form>
            </div>

            <div style="display:none;">
                <form id="IMFileShareForm" action="IMFileShare.php" method="post" enctype="multipart/form-data">
                    <input type="file" size="60" name="IMFileShareFormFile" id="IMFileShareFormFile" onChange="preTally.IM.IMFileShare();">
                </form>
            </div>


            <!-- <div class="IMProgress" id="IMProgress">
                <canvas id="inactiveProgress" class="progress-inactive" height="100px" width="100px"></canvas>
                <canvas id="activeProgress" class="progress-active"  height="100px" width="100px"></canvas>
                <p>0%</p>
            </div> -->



            <div style="display:none; left: 0px; top: 0px; z-index: 999999;" class="dhx_popup_dhx_skyblue" id="UIToolTipContainer">
                <div class="dhx_popup_area">
                    <table cellspacing="0" cellpadding="0" border="0" class="dhx_popup_table">
                        <tbody>
                            <tr class="dhxnode">
                                <td class="dhx_popup_td">
                                    <div style="position:relative;" id="UIToolTipData">This is Test Data</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="dhx_popup_arrow dhx_popup_arrow_left" id="dhx_popup_arrow_left_C" style="right: 0px; top: 50%; margin-top: -10px; display:none;"></div>
                <div class="dhx_popup_arrow dhx_popup_arrow_right" id="dhx_popup_arrow_right_C" style="left: 0px; top: 50%; margin-top: -10px; display:none;"></div>
            </div>


            <!-- Piwik -->
            <script type="text/javascript">
                $(document).ready(function () {
                    setTimeout(function () {
                        $(".preloader").hide();
                    }, 2000);
                    $(':input').on('focus click keypress', function () {
                        $(this).attr('autocomplete', 'off');
                        $(this).val('');

                        var type = $(this).attr('type');
                        $(this).attr('type', '_' + type);
                        $(this).attr('type', type);
                    });

                    var timeoutID;
                    var timeoutStampP;
                    function toolTipInitialize() {
                        window.clearTimeout(timeoutID);
                        //if(timeoutStampP != timeoutStamp) {
                        $('.UIToolTip').unbind('mouseover');
                        $('.UIToolTip').unbind('mouseout');
                        $('.UIToolTip').mouseover(function () {
                            //console.log('Mouse Over -- '+$( document ).width()+' -- '+$(this).offset().left);

                            $('#UIToolTipContainer').show();
                            if ($(this).offset().left > ($(document).width() / 2)) {
                                $("#UIToolTipContainer").css({'left': $(this).offset().left - $('#UIToolTipContainer').width() - 5, 'top': $(this).offset().top - 24});
                                $('#dhx_popup_arrow_left_C').show();
                            } else {
                                $("#UIToolTipContainer").css({'left': $(this).offset().left + 22, 'top': $(this).offset().top - 24});
                                $('#dhx_popup_arrow_right_C').show();
                            }
                            $('#UIToolTipData').html($(this).attr('UITitle'));
                        });
                        $('.UIToolTip').mouseout(function () {
                            //console.log('Mouse Out');
                            $('#UIToolTipContainer').hide();
                            $('#dhx_popup_arrow_left_C').hide();
                            $('#dhx_popup_arrow_right_C').hide();
                        });
                        //console.log('Function Triggered');
                        //}
                        timeoutStampP = timeoutStamp;
                        timeoutID = window.setTimeout(toolTipInitialize, 2000);
                    }
                    //setInterval(transition, 10000);
                    timeoutID = window.setTimeout(toolTipInitialize, 2000);

                    //socket = io.connect('http://128.199.181.200:3000');
                    //preTally.IM.InitializeConnection();


                });

                $(document).load(function () {
                    $(".preloader").show();
                });
                off_name = '<?php echo $preTally_user_ofname; ?>';
                loc_name = '<?php echo $preTally_user_lcname; ?>';

                //   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                //  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                //  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                //  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
                //  ga('create', 'UA-57120895-1', 'auto');
                //  ga('send', 'pageview');
		<?php if ($_SESSION['preTally_user_uname'] == "") { // 9-12-2024 ?>
                var commWarningPopid = 'comm_wanr_info';
                commWarnInfoPopObj = new dhtmlXWindows();
                commWarnInfoPop = commWarnInfoPopObj.createWindow(commWarningPopid, 0, 0, 400, 250);
                commWarnInfoPopObj.window(commWarningPopid).setModal(true);
                commWarnInfoPop.button("minmax").hide();
                commWarnInfoPop.button("park").hide();
                commWarnInfoPop.button("close").hide();
                commWarnInfoPop.center();
                commWarnInfoPop.setText("User Id Expired");
                /*var cwpophtml = '<div style=" padding:20px; height: 95%; min-height:100px; overflow: auto; font-size:18px;">Your User Id will be expire today. Please change it before it expires</div>';
                commWarnInfoPop.attachHTMLString(cwpophtml);*/
                addUsrForm = commWarnInfoPop.attachForm();
                addUsrForm.loadStruct(preTally.Initialize.encryptURL("requisites/changeUsername.php"), function() {
                addUsrForm.attachEvent("onButtonClick", function(name) {                
                    if (name == 'newUsernameSave') {
                        var validinp    = addUsrForm.validate();
                        var values      = addUsrForm.getFormData();
                        var vaiduser    = preTally.Validate.Validate(values, 'US_UName', addUsrForm, 'username');
                        if (validinp && vaiduser) {
                            console.log("validation success");
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            addUsrForm.send(preTally.Initialize.encryptURL('warehouse/changeUsername.php'), function(loader, response) {
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                if(response == 'success') {
                                    dhtmlx.message({text: 'Your user id updated successfully. Please Login with new User Id'});
                                    JGG1P3bDnUSDL1USRIDJS = values.US_UName;
                                    addUsrForm.resetValidateCss(); 
                                    setTimeout(function () {
                                        commWarnInfoPop.close(); 
                                        $.ajax({
                                            type: "POST",
                                            url: preTally.Initialize.encryptURL("logout.php"),
                                        }
                                        ).done(function () {
                                            location.href = 'index.php';
                                        }); 
                                    }, 1200);
                                }else {
                                    dhtmlx.message({text: response});
                                }                                   
                            });
                        } else {
                            dhtmlx.message({text: "User Id First character must be a letter.  Allow letters and digits. Length between 5 - 20."});
                        }
                    } else {
                        addUsrForm.setItemValue('US_UName', "");
                    }
                });
            });
            	<?php } ?>
            </script>
            <!-- End Piwik Code -->
        </body>
    </html>
