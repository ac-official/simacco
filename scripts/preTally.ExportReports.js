;(function($, window,undefined){
    preTally.ExportReports = {
        menuExportReports : function(){
            if(!dhxMiddleBlockTabs.cells("menuExportReports")){
                dhxMiddleBlockTabs.addTab("menuExportReports",  "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;Tally Reports &nbsp;&nbsp;<img src='images/icon/question.png' style='margin-top:2px;' class='popupExport' />", 170);
                $(".popupExport").click(function(){
                        $(this).data('clicked', true);
                        x = getAbsoluteLeft(this);
                        y = getAbsoluteTop(this); 
                        w = this.offsetWidth;
                        h = this.offsetHeight;
                        var myPop = new dhtmlXPopup();
                        myPop.attachHTML("Export Report");
                        myPop.show(x ,y,w,h);
                })
                dhxMiddleBlockTabs.tabs("menuExportReports").setActive();
                dhxMiddleBlockLayout = dhxMiddleBlockTabs.cells("menuExportReports").attachLayout("3L");
                dhxMiddleBlockLayout.cells("a").setWidth(200);
                dhxMiddleBlockLayout.cells("a").hideHeader();
                dhxMiddleBlockLayout.cells("b").setHeight(150);
                dhxMiddleBlockLayout.cells("b").fixSize(true, true);
                dhxMiddleBlockLayout.cells("b").hideHeader();
                dhxMiddleBlockLayout.cells("c").hideHeader();
                dhxXLExportForm = dhxMiddleBlockLayout.cells("b").attachForm();
                dhxXLExportForm.loadStruct(preTally.Initialize.encryptURL("requisites/rptExcelExport.php"),function(){
                    
                    rptTypeCombo = dhxXLExportForm.getCombo("rpt_Type");
                    rptTypeCombo.attachEvent("onChange",function(){
                        rptType = rptTypeCombo.getSelectedValue();
                        rptText = rptTypeCombo.getSelectedText();
                    });
                
                    var rpt_Inp_Frm = dhxXLExportForm.getInput("rpt_From");
                    rpt_Inp_Frm.setAttribute("readOnly", "true");
                    rpt_Inp_Frm.onclick = function() {
                        if(dhxXLExportForm.getItemValue("rpt_To",true)) preTally.ExportReports.setSens(rpt_Inp_Til, "max");
                    }
                    
                    var rpt_Inp_Til     = dhxXLExportForm.getInput("rpt_To");
                    rpt_Inp_Til.setAttribute("readOnly", "true");
                    rpt_Inp_Til.onclick = function() {
                        if(dhxXLExportForm.getItemValue("rpt_From",true)) preTally.ExportReports.setSens(rpt_Inp_Frm, "min");
                    }  
                        
                    rpt_Calendar        = new dhtmlXCalendarObject([rpt_Inp_Frm, rpt_Inp_Til]);
                    rpt_Calendar.setDateFormat("%d.%m.%Y");
                    dhxXLExportForm.attachEvent("onButtonClick", function(name){
                        if(name == 'rptXLExport'){
                            var rptXLValidate  = dhxXLExportForm.validate();
                            
                            if(rptXLValidate == false) {
                                dhxXLExportForm.setValidateCss('rpt_Type', rptXLValidate,  'validate_red');
                                return false;
                            }
                            else{
                                  /*Excel based on tally report*/
                                    if(rptType=='tallyEx_Rpt' || rptType=='tallyEx_RptBank' || rptType=='tallyEx_RptCash'){
                                         
                                         rpt_Inp_Frm    =   dhxXLExportForm.getItemValue("rpt_From");
                                         rpt_Inp_Til    =   dhxXLExportForm.getItemValue("rpt_To");
                                         var fdate      =   rpt_Inp_Frm.split('.');
                                         fday           =   fdate[0];
                                         fmonth         =   fdate[1]; 
                                         fyear          =   fdate[2];
                                         var fromDate   =   new Date(fyear+'-'+fmonth +'-'+fday);
                                         var tdate      =   rpt_Inp_Til.split('.');
                                         tday           =   tdate[0];
                                         tmonth         =   tdate[1];
                                         tyear          =   tdate[2];
                                         var toDate     =   new Date(tyear+'-'+tmonth +'-'+tday);
                                         var oneDay     =   24*60*60*1000;
                                         var diffDays   =   Math.round(Math.abs((fromDate.getTime() - toDate.getTime())/(oneDay)));
                                         if(fromDate<=toDate)
                                         {
                                            if(diffDays>16){
                                                dhtmlx.alert('Excel sheet upto 16 days only possible');
                                            }
                                            else{
                                                  preTally.Settings.progressOn(true, dhxLayout, null);
                                                        var params           = "report_type="+rptType+"&stDate="+dhxXLExportForm.getItemValue("rpt_From",true)+"&enDate="+dhxXLExportForm.getItemValue("rpt_To",true);
                                                        dhxXLExportForm.send(preTally.Initialize.encryptURL("warehouse/rptTallyExcelExport.php&"+params), function(loader, response){
                                                             var fileName    = response.split("XL_");
                                                             if(fileName[1]) {
                                                               location.href ="uploads/excelFile/"+fileName[1];
                                                             }
                                                      preTally.Settings.progressOff(true, dhxLayout, null);
                                                        });
                                            }
                                         }
                                         else{
                                            dhtmlx.alert('Invalid date');
                                         }
                                    }
                                    /*Excel based on tally report*/  
                                   else{ 
                                
                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                        var params       = "report_type="+rptType+"&stDate="+dhxXLExportForm.getItemValue("rpt_From",true)+"&enDate="+dhxXLExportForm.getItemValue("rpt_To",true);
                                        dhxXLExportForm.send(preTally.Initialize.encryptURL("warehouse/rptExcelExport.php&"+params), function(loader, response){
                                            var fileName = response.split("XL_");
                                            if(fileName[1]) {
                                                location.href ="uploads/excelFile/"+fileName[1];
                                            }
                                            preTally.Settings.progressOff(true, dhxLayout, null);
                                        });

                                         //preTally.ExportReports.setExportDetails(rptType,rptText);
                                   }
                           }
                        }
                        else{
                            dhxXLExportForm.resetValidateCss();
                            dhxXLExportForm.clear();
                        }
 
                    });
                });
            }else{
                dhxMiddleBlockTabs.tabs("menuExportReports").setActive();
            }
        },
        /*setExportDetails: function(id,text){
            xlWinObj = dhxWins.createWindow("setExportDetails",209,220, 600, 300);
            xlWinObj.setText('Export '+text);
            xlWinObj.button("minmax1").hide();
            xlWinObj.button("minmax2").hide();
            xlWinObj.button("park").hide();
            
            xlWinForm = xlWinObj.attachForm();
            
            preTally.Settings.progressOn(true,xlWinObj,null);
            
            xlWinForm.loadStruct("requisites/setExportDetails.php", function(){
                preTally.Settings.progressOff(true,xlWinObj,null);
                
                xlWinForm.attachEvent("onButtonClick", function(name){
                    preTally.Settings.progressOn(true,xlWinObj,null);
                    
                    if(name == 'xlExportSubmit'){
                        
                        if( xlWinForm.isItemChecked('XL_Id')      || 
                            xlWinForm.isItemChecked('XL_Branch')  || 
                            xlWinForm.isItemChecked('XL_Amount')  || 
                            xlWinForm.isItemChecked('XL_Date')    || 
                            xlWinForm.isItemChecked('XL_Advance') ||
                            xlWinForm.isItemChecked('XL_Part')    ||
                            xlWinForm.isItemChecked('XL_Final')
                        ) {
                            var params = "report_type="+id+"&stDate="+dhxXLExportForm.getItemValue("rpt_From",true)+"&enDate="+dhxXLExportForm.getItemValue("rpt_To",true);
                            xlWinForm.send("warehouse/rptExcelExport.php?"+encrypt(params), function(loader, response){
                                if(response){
                                    location.href = "excelUploads/"+response;
                                }
                                preTally.Settings.progressOff(true, xlWinObj, null);
                                xlWinObj.close();
                            });
                        }else{
                            preTally.Settings.progressOff(true,xlWinForm, null);
                            dhtmlx.message({text: "Please select any fields to Export."});
                        }
                        
                        
                    }else{
                        preTally.Settings.progressOff(true, xlWinObj, null);
                        xlWinObj.close();
                    }
                });
            });
        },*/
        setSens: function (inp, k) {
            if (k == "min") {
                rpt_Calendar.setSensitiveRange(inp.value, null);
            } else {
                rpt_Calendar.setSensitiveRange(null, inp.value);
            }
        }
        
    }
})(jQuery, this);
