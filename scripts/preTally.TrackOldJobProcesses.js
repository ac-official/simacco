;(function($, window, undefined) {
    var rowIndex;
    preTally.TrackOldJobProcesses = {
        listOldJobs : function() {
            if (!dhxMiddleBlockTabs.cells("oldJobsTab")) {
                dhxMiddleBlockTabs.addTab("oldJobsTab", "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;Old Job Processes", 200);
                dhxMiddleBlockTabs.tabs("oldJobsTab").setActive();
                var manageOldjobsLayout = dhxMiddleBlockTabs.cells("oldJobsTab").attachLayout('1C');
              
                manageOldjobsLayout.cells("a").setText("Add Track Sub Process");
                manageOldJobsGrid = manageOldjobsLayout.cells("a").attachGrid();                               
                manageOldJobsGrid.setHeader("SlNo,<input type='text' class='track_jb_filter' id ='trackID' style='width: 90%;' placeholder='Track ID'>,<input type='text' class='track_jb_filter' id ='createdID' style='width: 90%;' placeholder='Job Created By'>,<input type='text' class='track_jb_filter' id ='createdLC' style='width: 90%;' placeholder='Branch'>,<input type='text' class='track_jb_filter' id ='updatedBy' style='width: 90%;' placeholder='Process Updated By'>,Process Count,<select id='processCnt'><option value = ''>All</option><option value='1'> Process Added</option><option value='0'> No Process </option></select>");
                manageOldJobsGrid.setInitWidths("60,*,180,180,180,180,150");
                manageOldJobsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                manageOldJobsGrid.setColAlign("center,left,left,left,left,left,center");
                manageOldJobsGrid.enableTooltips("false,false,false,false,false,false,false");
                manageOldJobsGrid.enableColSpan(true);
                manageOldJobsGrid.enableEditEvents(true,false,true);
                manageOldJobsGrid.init();
                manageOldJobsGrid.setImagePath("assets/grid/codebase/imgs/");
                manageOldJobsGrid.setSkin("dhx_skyblue");
                
                notfButtonBar = manageOldjobsLayout.cells("a").attachStatusBar({
                    text  : "<div class='st_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='tk_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='Track_paging'></span></div>",
                    height: 35
                });
                
                manageOldJobsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                manageOldJobsGrid.enablePaging(true,50,5,"Track_paging",true);
                manageOldJobsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                manageOldJobsGrid.loadXML(preTally.Initialize.encryptURL("requisites/listTracks.php"), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    $('.tk_cnt_tot').html("# : "+manageOldJobsGrid.getUserData("", "TL_Count")+" ");
                    var filtrInterval ;
                    
                    $(".track_jb_filter").keyup(function(value) {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            preTally.TrackOldJobProcesses.applyFilter(); 
                            clearInterval(filtrInterval); 
                        }, 500);
                    });
                    
                    $("#processCnt").change(function() {
                        preTally.TrackOldJobProcesses.applyFilter(); 
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("oldJobsTab").setActive();
            }
        },
        viewSubProcesses : function(TR_Id,inp) {
            
            subProcessWin = dhxWins.createWindow('chartOtherDetails', 400, 100, 600, 350);
            subProcessWin.button("minmax1").hide();
            subProcessWin.button("minmax2").hide();
            subProcessWin.button("park").hide();
            subProcessWin.center();
            subProcessWin.setModal(true);
            subProcessWin.setText("Track Sub Process");
            trackAddProcessGrid = dhxWins.window('chartOtherDetails').attachGrid();
               
            trackAddProcessGrid.enableEditEvents(true,true,true);
            trackAddProcessGrid.enableTooltips("false,false,false,false");
            preTally.Settings.progressOn(true, subProcessWin, null);
            trackAddProcessGrid.loadXML(preTally.Initialize.encryptURL("requisites/listOldJobSubProcess.php&TR_Id="+TR_Id), function() {
                preTally.Settings.progressOff(true, subProcessWin, null);
                
                checked         =   [];
                trackAddProcessGrid.forEachRow(function(id){
                    if (trackAddProcessGrid.cells(id,0).getValue() == 1 ) {
                        for(var i = 0; i < trackAddProcessGrid.cells(id,3).getValue(); i++)
                            checked.push(id);
                    }
                });
                initialArray        = checked;
                var initialArrayLen = initialArray.length;
                
                var counts          = {};
                for(var i = 0; i< checked.length; i++) {    // get count of each elements in array checked                    
                    var num         = checked[i];
                    counts[num]     = counts[num] ? counts[num]+1 : 1;
                }
                trackAddProcessGrid.attachEvent("onCheck", function(rId,cInd,state){
                    
                    var index       = checked.indexOf(rId);
                    if(state) {
                        if(index == -1)
                            checked.push(rId);
                    } else {
                        for(var i = 0 ;i < counts[rId]; i++) {
                            var index  = checked.indexOf(rId);  // get position for each occurence of rId
                            if (index > -1) 
                                checked.splice(index,1);
                        }
                    }
                });
                
                trackAddProcessGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
                    rowId       = rId;
                    rowIndex    = cInd;
                    if(stage == 2 && cInd == 3) {
                        if( trackAddProcessGrid.cells(rId,0).isChecked()) {
                            preTally.TrackOldJobProcesses.createCheckedArray(rId,nValue);
                        }
                    }
                    return true;
                });

                subProcessWin.attachEvent("onClose", function(win){
                    
                    if(rowIndex == 3) {
                        var cellVal = trackAddProcessGrid.cells(rowId,rowIndex).getValue();
                        preTally.TrackOldJobProcesses.createCheckedArray(rowId,cellVal);
                    }
                    
                    if(initialArrayLen > 0 || checked.length > 0) {
                        $.post(preTally.Initialize.encryptURL("warehouse/addSubProcessOldJobs.php"),
                        { checked : checked, TR_Id : TR_Id, LC_Id : manageOldJobsGrid.getUserData(TR_Id, "LC_Id")}, 
                        function( data ){ });
                    }
                    preTally.TrackOldJobProcesses.applyFilter(manageOldJobsGrid.currentPage); 
                    return true;
                });

            });
        },
        applyFilter : function(currentPage) {
            var filterValue = new Array($('#trackID').val().trim(), $('#createdID').val().trim(),$('#createdLC').val().trim(),$('#updatedBy').val().trim(),$('#processCnt').val().trim());
            manageOldJobsGrid.clearAll();
            
            preTally.Settings.progressOn(true, dhxLayout, null);
            manageOldJobsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listTracks.php&filter="+filterValue), function() {
                $('.tk_cnt_tot').html("# : "+manageOldJobsGrid.getUserData("", "TL_Count")+" ");
                if(currentPage)
                    manageOldJobsGrid.changePage(currentPage);
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        createCheckedArray : function(rId,Val) {
            if(!isNaN(Val) && Val != 0) {
                                
                var counts          = {};
                for(var i = 0; i< checked.length; i++) {    // get count of each elements in array checked                    
                    var num         = checked[i];
                    counts[num]     = counts[num] ? counts[num]+1 : 1;
                }

                for(var i = 0 ;i < counts[rId]; i++) {
                    var index   = checked.indexOf(rId);
                    if (index > -1) 
                        checked.splice(index,1);
                }

                for(var i = 0;i < Val; i++) {
                    checked.push(rId);
                }
            }
        }
    };
})(jQuery, this);