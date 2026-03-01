function doOnMenuCall(menuID) {
    requestedMenu = menuID;
    switch (menuID) {
        case 'new_payroll':
            preTally.Settings.menuPayroll();
            break;
        
        case 'menu_holidays':
            preTally.Settings.menuHolidays();
            break;

        case 'menuNewBalSheet':
            preTally.BalanceSheet.menuNewBalSheet();
            break;
            
        case 'menuNewBalSheetSample':
            preTally.BalanceSheet.menuNewBalSheetSample();
            break; 
        
        case 'view_master_reports':
            preTally.MasterReports.viewMasterReports();
            break;
            
        case 'view_mr_location':
            preTally.MasterReportsLocationBased.viewLocationBasedMasterReports();
            break;    
            
        case 'view_businessbonus_reports':
            preTally.BusinessBonusReport.viewBusinessBonusReports();
            break;
            
        case 'income_reports':
            preTally.IncomeProfitReport.viewIncomeProfitReports();
            break;
        
        case 'track_reports':
            preTally.TrackReports.viewStateWiseReports();
            break;
            
        case 'job_count_reports':
            preTally.JobCountReports.viewJobCountReports();
            break;
            
            
        case 'certificate_count_reports':
            preTally.CertificateCountReports.viewCertificateCountReports();
            break;
            
        case 'enquiry_count_reports':
            preTally.EnquiryCountReports.viewEnquiryCountReports();
            break;
            
            
        case 'view_branch_mstrRpt':
            preTally.BranchMasterReports.viewBranchMasterReports();
            break;
            
        case 'view_accsummary_reports':
            preTally.AccSummaryReports.viewAccSummaryReports();
            break;               
        
        case 'view_mstrRpt_export':
            preTally.ExportReports.menuExportReports();
            break;        
       
        case 'view_reports':
            preTally.Reports.viewReports();
            break; 
        
        case 'old_job_process':
            preTally.TrackOldJobProcesses.listOldJobs();
            break;
        
        case 'view_pettycashreports':
            preTally.PettyCash.view_pettycashreports();
            break;      
         
        case 'mywallet':
            preTally.MyWallet.view_mywallet();
            break;
            
        case 'view_edt_reports' :
            preTally.EntryEdit.view_editReports();
            break;
           
        case 'view_expCntrl' :
            preTally.ExpenseControl.view_ExpenseControl();
            break;
            
        case 'view_businessreports' :
            preTally.BusinessReport.view_BusinessReports();
            //preTally.BusinessReport.view_BusinessReports();
            break;
            
        case 'businessreport_manage':
            preTally.BusinessReportManage.viewBusinessReportManage();
            break;
         
        case 'view_stockreports' :
            preTally.StockReport.view_StockReports();
            break; 
            
        case 'view_trackdupreports' :
            preTally.TrackDupReports.view_TrackDupReports();
            break; 
            
        case 'view_cashbsreports':
            preTally.CashBalanceSheet.view_CashBSReports();
            break;
            
        case 'view_bankbsreports':
            preTally.BankBalanceSheet.view_BankBSReports();
            break;    
        
        case 'view_branchbsreports':
            preTally.BranchBSReports.view_BranchBSReports();
            break; 
        
        case 'view_mybankreports':
            preTally.MyBankBook.viewMyBankBook();
            break; 
        
        case 'view_branchreports':
            preTally.BranchReports.viewBranchReports();
            break;   
            
        case 'accounts_search':
            preTally.Settings.searchAccount();
            break;
            
         case 'admin_dashboard':
            preTally.Settings.adminDashboard();
            break;
            
        case 'admin_bugreport':
            preTally.Settings.adminBugReports();
            break;
               
        case 'send_message':
            preTally.Settings.sendMessage(this);
            break;            
            
        case 'tracks_search':
            preTally.Settings.searchTrack();
            break;            
            
        case 'tracks_all_list': //21-05-2025
            preTally.ManageTracks.searchAllTrack();
            break; 
        case 'tracks_expense': //05-01-2026
            preTally.ManageTracks.listTrackExpense();
            break;
        
        case 'manage_tracks' :
            preTally.ManageTracks.viewTracks();
            break; 

        
        case 'approval_queue':
            preTally.Settings.approvalQueue();
            break;
        case 'new_user':
            preTally.UserProfile.menuNewUser(0,null);
            break;
            
        case 'import_users':
            preTally.BulkUpload.importUsers();
            break;

        case 'import_users_details':
            preTally.BulkUpload.importUsersDetails();
            break;
        
        case 'list_user':
            preTally.UserProfile.menuListUser();
            break
            
        case 'attendance_user':
            preTally.UserProfile.listAttendance();
            break;
        case 'salary_advance':
            preTally.UserProfile.salaryAdvance();
            break;   
        case 'meal_allowance':
            preTally.UserProfile.mealAllowance();
            break;       
            
        case 'salary_report':
            preTally.UserProfile.salaryReports();
            break;
        case 'monthwisesal_report':
            preTally.UserProfile.monthwiseSalReports();
            break;
        
        
        case 'access_levels':
            preTally.Settings.menuAccessLevelUser();
            break;

        case 'new_designation':
            preTally.Settings.menuNewDesignation();
            break;
            
        case 'new_department':
            preTally.Settings.menuNewDepartment();
            break;    
            
        case 'new_location':
            preTally.Settings.menuNewLocation();
            break;

        case 'new_office':
            preTally.Settings.menuNewOffice();
            break;
              
        case 'listOfzAdmin':
            preTally.Office.menuListOfficeAdmin();
            break;
        
        case 'list_OldStockAmount':
            preTally.Settings.menuOldStockAmount();
            break; 
        case 'open_balance':
            preTally.Settings.menuOpenBalance();
            break;       
            
        case 'open_bnkbalance':
            preTally.Settings.menuBnkOpenBalance();
            break;

        case 'unused_ItemDesc':
            preTally.ManageUnused.menuManageUnused();
            break;
        
        case 'new_description':
            preTally.BalanceSheet.menuNewDescription(0);
            break;

        case 'new_mainhead':
            preTally.BalanceSheet.menuNewMainhead();
            break;

        case 'new_subhead':
            preTally.BalanceSheet.menuNewSubhead();
            break;
        case 'sh_pattern':
            preTally.BalanceSheet.menuManagePattern();
            break;
            
        case 'new_item':
            preTally.BalanceSheet.menuNewItem(0);
            break;
//        case 'new_company_item':
//            preTally.Item.menuNewCompanyItem(0);
//            break;
        case 'new_ofz_item':
            preTally.Item.menuNewOfzItem(0);
            break;
            
        case 'custom_item': 
            var OFName = dhxToolbar.getItemText(menuID);
            preTally.Item.menuListEditCustomItem(OFName);
            break;   
            
        case 'pretally_item':
            preTally.Item.menuListPretallyItem();
            break;
            
        case 'otherCompany_item':
            preTally.Item.menuListOtherCompanyItem();
            break;   
        case 'listOfzItem': 
            preTally.History.menuListAllOfzItems();
            break;   
            
        case 'import_items':
            preTally.BulkUpload.importItems();
            break;
            
        case 'import_descriptions':
            preTally.BulkUpload.importDescriptions();
            break;
            
        case 'import_salhistory':
            preTally.BulkUpload.salaryHistory();
            break; 
        case 'import_updsalary': //21-03-2025
            preTally.BulkUpload.salaryUpdated();
            break; 
        
        case 'listOfzActsEntry':
            preTally.History.menuListAllOfzActsEntries();
            break;
            
        case 'new_bank':
            preTally.Settings.menuNewBank();
            break;

        case 'new_bnkbranch':
            preTally.Settings.menuNewBnkBranch();
            break;

        case 'new_bnkacc':
            preTally.Settings.menuNewBnkAccount();
            break;

        case 'new_cheque':
            preTally.Settings.menuNewCheque();
            break;
            
        case 'manage_chqleaves':
            preTally.Settings.manageChqLeaves();
            break;
            
        case 'manage_entryEditDate':
            preTally.Settings.entryEditBlockDate();
            break;
        
        case 'import_branches':
            preTally.BulkUpload.menuImportBranches();
            break;
        
        case 'new_state':
            preTally.Settings.menuNewState();
            break;

        case 'new_city':
            preTally.Settings.menuNewCity();
            break;
        case 'new_place':
            preTally.Settings.menuNewPlace();
            break;
        case 'new_unit':
            preTally.Settings.menuNewUnit();
            break;

        case 'new_paymode':
            preTally.Settings.menuNewPaymode();
            break;

        case 'new_salstruct':
            preTally.Settings.menuNewSalStruct();
            break;

        case 'new_salpaymode':
            preTally.Settings.menuNewSalPaymode();
            break;      
            
        case 'user_map_tree':
            preTally.UserProfile.menuUserMapTree();
            break;

        case 'calculator':
            preTally.Settings.menuCalculator();
            break;

        case 'notifications':
          preTally.Notification.menuNotification(this);
          break;

        case 'my_profile':
          preTally.UserProfile.menuNewUser('self',null);                  
          break;
          
        case 'my_wallet':
          preTally.UserProfile.menuWallet();                  
          break;
          
        case 'my_username':
          preTally.UserProfile.menuMyUsername();
          break;

        case 'my_password':
          preTally.UserProfile.menuMyPassword();
          break;
          
        case 'my_attendance':
            preTally.UserProfile.menuMyAttendance();
            break;   
            
        case 'my_companies':
            preTally.Office.menuMyOffices();
            break; 
            
        case 'sign_out':
          preTally.UserProfile.menuSignOutProfile();
          break;

        case 'menuToolbarCalendar':
          preTally.Settings.menuCalendar();
          break;
       
            
        case 'about_pty':
            preTally.Settings.aboutPretally();
            break;
            
        case 'report_bug':
            preTally.Settings.reportBug(this);
            break;
            
        case 'contact_us':
            preTally.Settings.contactUs();
            break;
            
        case 'menu_ApproveLeave':            
            preTally.Settings.menu_ApproveLeave();
            break;

        case 'menu_LateEntriesList':            
            preTally.Settings.menu_LateEntriesList();
            break;    
            
        case 'leave_details':            
            preTally.Settings.leaveDetails();
            break;
            
        case 'import_leaves':
            preTally.BulkUpload.importLeaves();
            break;
            
        case 'menu_ManageLeaveType':
            preTally.Settings.menu_ManageLeaveType();
            break;
        case 'menu_ManageRule':
            preTally.Settings.menu_ManageRule();
            break;
        case 'menu_Adj_Attendance':
            preTally.Settings.menu_Adj_Attendance();
            break;
        case 'menu_Attendance_Compensation':
            preTally.Settings.menu_Attendance_Compensation();
            break;            
        case 'menu_Employee_Status':
            preTally.Settings.menu_Employee_Status();
            break;          
        case 'menu_Adj_Time':  //08-12-2025
            preTally.Settings.timeAdjustedUsers();
            break;
        
        case 'import_subprocess' :
            preTally.BulkUpload.importTrackSubProcess();
            break;
        
        
        case 'track':
            preTally.Track.Track();
            break; 
        case 'misReport':
            preTally.Reports.MISReport();
            break; 
        case 'misReport_2':
            preTally.Reports.MISGrid();
            break; 
            
        case 'im':
            //var person = prompt("Please enter your key", "");
            //if (person == 'anoop')
                preTally.IM.IMInitialize();
            //else return false;
            break; 
            
        case 'inventory'  :
            preTally.Inventory.addInventory();
            break;            
        case 'assign_zones':
            preTally.Settings.menuAssignZones();
            break;
        case 'manage_zones':
            preTally.Settings.menuManageZones();
            break;
        case 'view_userie_reports':  
            var RPTNAME = dhxToolbar.getItemText(menuID);
            preTally.UserIEReports.viewIEReports(RPTNAME);
            break;
        case 'listOfzDesc':
            preTally.Descriptions.viewAllDescriptions();
            break;
        case 'break_time_user':
            preTally.Attendance.menuBreakTimes();
            break;        
        case 'break_time_list': //27-06-2025
            preTally.Attendance.listBreakTimes();
            break; 
        case 'access_user_level': // 04-07-2025
            preTally.Settings.menuUserAccessLevel();
            break;
        case 'pendingTrack':
            preTally.BalanceSheet.pendingTrack();
            break;
        //account team related menu functions start 08-07-2025
        case 'account_cashbsreports': //03-06-2025
            preTally.AccountsTeam.acc_CashBSReports();
            break;             
        case 'account_branchbsreports': //03-06-2025
            preTally.AccountsTeam.acc_BankBSReports();
            break;              
        case 'account_vendor_rpt': //29-09-2025
            preTally.AccountsTeam.acc_VendorReport();
            break;                
        //case 'account_profit_loss': //29-09-2025
           // preTally.AccountsTeam.acc_ProfitLoss();
            //break;                 
        case 'account_pandl': //27-10-2025
            preTally.AccountsTeam.acc_PandL();
            break;                  
        case 'account_cpandl': //15-01-2026
            preTally.AccountsTeam.acc_CPandL();
            break;                    
        case 'account_blancesheet': //27-10-2025
            preTally.AccountsTeam.acc_BalanceSheet();
            break;                  
        case 'account_cashflow': //27-10-2025
            preTally.AccountsTeam.acc_CashFlow();
            break;                       
        case 'account_ledger_rpt': //07-10-2025
            preTally.AccountsTeam.acc_LedgerReport();
            break;                        
        case 'account_bnkcash_rpt': //29-01-2026
            preTally.AccountsTeam.acc_BnkCashRpt();
            break;                            
        case 'account_group': 
            preTally.AccountsTeam.acc_Groups();
            break;                       
        case 'account_ledger': 
            preTally.AccountsTeam.acc_Ledger();
            break;                       
        case 'account_map_item': 
            preTally.AccountsTeam.acc_MapItems();
            break;
        case 'account_journal': 
            preTally.AccountsTeam.acc_Journal();
            break;
        case 'account_tds': 
            preTally.AccountsTeam.acc_TdsRules();
            break;
        case 'account_vendor': 
            preTally.AccountsTeam.acc_Vendors();
            break;
        case 'account_recurbills': 
            preTally.AccountsTeam.acc_RecurringBill();
            break;
        case 'account_openbalance': 
            preTally.AccountsTeam.acc_OpenBalance();
            break;
        case 'account_extra_data': 
            preTally.AccountsTeam.acc_ExtraData();
            break;
        case 'account_bills': 
            preTally.AccountsTeam.acc_Bills();
            break;
        case 'account_not_transfer': //06-11-2025
            preTally.AccountsTeam.acc_NotTransfered();
            break;   
        case 'account_change_after': //31-12-2025
            preTally.AccountsTeam.acc_ChangeAfterMap();
            break; 
        case 'account_journal_entry': //11-11-2025
            preTally.AccountsTeam.acc_JournalEntry();
            break;  
        case 'account_journal_data': //13-11-2025
            preTally.AccountsTeam.acc_JournalData();
            break;          
        case 'account_importextra': //21-03-2025
            preTally.BulkUpload.acc_PandLData();
            break; 
            
        // End Accounts related functions 

        default:
            var res = menuID.match(/UCMap_/gi);
            if(res) {
                preTally.Office.menuSwitchUserAccount(menuID);
                break;
            }else    alert('fail');
//            preTally.Office.menuSwitchUserAccount(menuID);
    }
}
