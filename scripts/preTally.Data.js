;(function ($, window, undefined) {
	preTally.Data = {
		mainLayout : function() {
			var dhxLayoutData = {
				parent		: document.body,
				pattern		: "2U",//3W, 4H
				cells: [{
					id: "a",
					text: "Main Block"
				}, {
					id: "b",
					text: "&nbsp;&nbsp;Simacco Menu",
					//header: false,
					width: "250"
				}]
			};
			return dhxLayoutData;
		},
		designationLayout : function() {
			var dhxLayoutData = {
				//parent		: document.body,
				pattern		: "2U",//3W, 4H
				cells: [{
					id: "new_designation",
					text: "New Designation",
					//header: false,
					width: 250
				}, {
					id: "list_designation",
					text:"List Designation"
				}]
			};
			return dhxLayoutData;
		},
		mainToolbar : function() {
			
		},
                reLoginLayout : function(empId) {
                    var reLoginLayoutData = [{
                            type            : "settings",
                            position	: "label-left",
                            labelWidth	: 150,
                            labelHeight	: 35,
                            inputHeight	: 25,
                            inputWidth	: 200
                        }, {
                            type: "fieldset",
                            label: " <img src='images/logo.png' width='100' />",
                            inputWidth: "auto",
                            offsetLeft : 10,
                            list: [{
                                        type		: "input",
                                        name		: "username",
                                        label		: "Login",
                                        value		: empId,
                                        disabled        : true
                                }, {
                                        type		: "password",
                                        name		: "password",
                                        label		: "Password",
                                        value		: "",
                                        required	: true,
                                        validate	: "NotEmpty"
                                        //validate	: "[0-9]+"
            //                    }, {
            //                            type		: "checkbox",
            //                            name		: "remember",
            //                            label		: "Remember me",
            //                            checked		: false
                                },{
                                        type		: "hidden",
                                        name		: "submt",
                                        value		: "1"
                                }, {
                                    type: "block", width: 350, list:[{
                                        type		: "button", 
                                        value		: "SignIn", 
                                        name		: "signIn",
                                        offsetLeft      : 120,
//                                },{
//                                    type:"newcolumn"
//                                },{
//                                    type		: "button", 
//                                    value		: "Cancel", 
//                                    name		: "cancelSignIn",
                                }]
                            }]
                        }];
                    
                    return reLoginLayoutData;
                },
                TrackRibbonLayout : function () {
                    var dhxTrackRibbonData = {
                            "tabs" : [
                                    {
                                            "id" : "tab_1",
                                            "text" : "Registration & Jobs",
                                            "active" : true,
                                            items: [
						{
							type:'block', text:'Registration', mode:'cols', list:[
								{type:'button', text:'New', isbig: true, img: "new_48.png"},
								{type:'button', text:'new' , img: "18/new.gif"},
								{type:'button', text:'cut', img: "18/cut.gif" }
							]
						},
						{
							type:'block', text:'Block 2', text_pos: 'top', list:[
								{type:'button', text:'copy' , img: "18/copy.gif"},
								{type:'button', text:'print' , img: "18/print.gif"},
								{type:'button', text:'paste', img: "48/paste.gif", isbig: true }
						]}
					]
                                    },
                                    {
                                            "id" : "tab_2",
                                            "text" : "Accounts & Reports",
                                            items: [
						{
							type:'block', text:'Block 1', mode:'rows', list:[
								{type:'button', text:'copy' , img: "18/copy.gif"},
								{type:'button', text:'cut', img: "18/cut.gif" },
								{type:'button', text:'new' , img: "18/new.gif"},
								{type: "newLevel"},
								{type:'button', text:'open' , img: "18/open.gif"},
								{type:'button', text:'paste', img: "18/paste.gif" },
								{type: "newLevel"},
								{type:'button', text:'print' , img: "18/print.gif"}
							]
						}
                                            ]
                                    }
                            ]
                    }
                    return dhxTrackRibbonData;
                },
                /*TrackToolbarLayout : function () {
                    var dhxTrackToolbarData =
                
                
                        [
                                // text, tooltip, action
                                { id: "track_dashboard", type: "button", img: "dashboards16.png", text: "Dashboard", title: "Track Dashboard",
                                        // userdata for item
                                        userdata: {
                                                my_data_name    : "my_data_value",
                                                more_info       : "new_value"
                                        }
                                },
                                { type: "separator" },
                                { id: "new_track_user_registration", type: "button", img: "department_20.png", text: "New Registration", title: "Register New Users Here", action: "myFunction",
                                        // userdata for item
                                        userdata: {
                                                my_data_name    : "my_data_value",
                                                more_info       : "new_value"
                                        }
                                },
                                { type: "separator" },
                                // icon only
                                { id: "save", type: "button", img: "save.gif" },
                                { type: "separator" },
                                // disabled
                                { id: "cut", type: "button", imgdis: "cut_dis.gif", text: "Cut", title: "Tooltip here", enabled: false },
                                { id: "copy", type: "button", imgdis: "copy_dis.gif", enabled: false, hidden: true },
                                {type: "spacer"},
                                
                                
                                
                                { type: "separator" },
                                {id: "login", type: "button", text: "Cancel", img: "cross.png"},
                                { type: "separator" },
                                {id: "login", type: "button", text: "Save", img: "save.gif"},
                                { type: "separator" },
                                {id: "login", type: "button", text: "Next", img: "arrow_r_16.gif"},
                                { type: "separator" },
                                {id: "login", type: "button", text: "Previous", img: "arrow_lr_16.gif"},
                                { type: "separator" },
                                {id: "newPhoneRegistration", type: "button", text: "Phone Registration", img: "phone_24.png"}


      
                        ]
                    return dhxTrackToolbarData;
                },*/
                TrackNewUserLayout : function () {
                    
                    var dhxTrackNewUserData = {items: [
                        {id: "candidate_details", text: "Candidate Details", icon: "recent.png", selected: true},
                        {type: "separator"},
                        {id: "desktop", text: "Documents", icon: "desktop.png"},
                        {type: "separator"},
                        {id: "downloads", text: "Billing / Invoice", icon: "downloads.png"},
                        {type: "separator"}
                    ]}
                    return dhxTrackNewUserData;
                },
                TAPManageTB : function() {
                    var TAPManageTBData = [
                            { id: "dummyButton", type: "button" },
                            { id: "dummySpacer", type: "spacer" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "closeProcess", type: "button", img: "images/icon/cross_16.png", text: "Close" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "saveProcess", type: "button", img: "images/icon/tick_16.png", text: "Save Process" }
                            
                        ];
			return TAPManageTBData;
		},
                TAPFromTB : function() {
                    var TAPFromTBData = [
                            { id: "dummyButton", type: "button" },
                            { id: "dummySpacer", type: "spacer" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "closeFromCountry", type: "button", img: "images/icon/cross_16.png", text: "Close" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "saveFromCountry", type: "button", img: "images/icon/tick_16.png", text: "Save From Country/State" }
                            
                        ];
			return TAPFromTBData;
		},
                TAPToTB : function() {
                    var TAPToTBData = [
                            { id: "dummyButton", type: "button" },
                            { id: "dummySpacer", type: "spacer" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "closeToCountry", type: "button", img: "images/icon/cross_16.png", text: "Close" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "saveToCountry", type: "button", img: "images/icon/tick_16.png", text: "Save To Country" }
                            
                        ];
			return TAPToTBData;
		},
                TAPCFTypeTB : function() {
                    var TAPCFTypeTBData = [
                            { id: "dummyButton", type: "button" },
                            { id: "dummySpacer", type: "spacer" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "closeCFType", type: "button", img: "images/icon/cross_16.png", text: "Close" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "saveCFType", type: "button", img: "images/icon/tick_16.png", text: "Save Certificate Type" }
                            
                        ];
			return TAPCFTypeTBData;
		},
                TAPListTB : function() {
                    var TAPListTBData = [
                            { id: "dummyButton", type: "button", text: "<b>Automate Track Procedures</b>" },
                            { id: "dummySpacer", type: "spacer" },
                            { type: "separator" },
                            { type: "separator" },
                            { id: "newProcedure", type: "button", img: "images/icon/arrow_animated.gif", text: "New Procedure" },
                            { type: "separator" },
                            { type: "separator" }
                            
                        ];
			return TAPListTBData;
		},
                
                TAPCFTypeGrid : function() {
                        
                    var TAPCFTypeGridData = {
                        head:[
                                {width:"30",  type:"ed", align:"center", sort:"na", value:"#"},
                                {width:"*", type:"ro", align:"left",   sort:"na", value:"Certificate Type"},
                                {width:"60", type:"ch", align:"center",   sort:"na", value:"Process"}
                            ],    
                        rows:[  
                                { id : 1, data:[ "1", "Educational Certificate", "0" ] },
                                { id : 2, data:[ "2", "Non-Educational Certificate", "0" ] },
                                { id : 3, data:[ "3", "Commercial Certificate", "0" ] },
                                { id : 4, data:[ "4", "Passport", "0" ] },
                                { id : 5, data:[ "5", "Registration certification for renewal", "0" ] },
                                { id : 6, data:[ "6", "PCC Certificate", "0" ] },
                                { id : 7, data:[ "7", "Courier", "0" ] },
                            ]
                    }

                    return TAPCFTypeGridData;
 
                }
//                NewPhoneRegistration : function () {
//                    var NewPhoneRegistrationData = [
//                                    {type: "block", blockOffset: 0, width: 500, list: [
//                                        {type: "settings", position: "label-left", labelWidth: 220, inputWidth: 220, offsetLeft: 5},
//                                        {type: "fieldset", label: "<img src=\'images/icon/close_button_icon.png\' style=\'cursor:pointer;\' onclick=\'preTally.Track.hideDetailData()\' />", width: 500, list: [
//                                            {type: "input", label: "Certificate Holder's Name ", name: "AE_Name",validate:"NotEmpty,^[a-zA-Z ]+$", required:true},
//                                            {type: "input", label: " Your Name (Caller's Name)", name: "AE_Cust_Name",validate:"NotEmpty,^[a-zA-Z ]+$", required:true},
//                                            {type: "input", label: "Mobile", name: "AE_Mobile",validate:"NotEmpty,ValidNumeric", required:true},
//                                            {type: "combo", label: "Calling from ", name: "AE_Cust_Loc",connector:"requisites/attestationPlaces.php",filtering:true,validate:"NotEmpty", required:true},
//                                            {type: "combo", label: "City ", name: "AE_City",connector:"requisites/attestationCities.php",filtering:true,validate:"ValidNumeric",value:0},
//                                            {type: "combo", label: " Certificate", name: "AE_Certificate",connector:"requisites/attestationDocuments.php",filtering:true,validate:"NotEmpty,ValidNumeric", required:true},
//                                            {type: "combo", label: " State/Country", name: "AE_State",connector:"requisites/attestationStates.php",filtering:true,validate:"NotEmpty,ValidNumeric", required:true},
//                                            {type: "combo", label: " University/Board/Council", name: "AE_Authorities",validate:"NotEmpty,ValidNumeric", required:true},
//                                            {type: "combo", label: " Year", name: "AE_Year",connector:"requisites/attestationDocYear.php",validate:"NotEmpty,ValidNumeric",readonly:true, required:true},
//                                            {type: "combo", name: "AE_Certi_Mode", label: "Is this a Regular Course",validate:"NotEmpty",readonly : true, required:true, options:[
//                                                {value: "", text: "Please select"},
//                                                {value: "1", text: "Yes"},
//                                                {value: "2", text: "No"}
//                                            ]},
//                                            {type: "input", label: "Where did you study ", name: "AE_Certi_StudyLoc",validate:"NotEmpty,^[a-zA-Z ]+$", required:true},
//                                            {type: "input", label: "Why you need this Attestation ", name: "AE_Att_For",validate:"NotEmpty,^[a-zA-Z ]+$", required:true},
//                                            {type: "input", label: "Where you want to go", name: "AE_Visiting_CNId",validate:"NotEmpty,^[a-zA-Z ]+$", required:true},
//
//
//                                            {type: "input", label: "E-mail ID", name: "AE_Email",validate:"NotEmpty,ValidEmail", required:true},
//                                            {type: "input", label: "Remark", name: "AE_Remarks",validate:"NotEmpty", required:true,rows:3},
//                                            {type: "block", inputWidth: "auto", list: [
//                                                {type: "settings", offsetTop: 10},
//                                                {type: "button", value: "Confirm", offsetLeft: 100,name:"PhRegConfirm"},
//                                                {type: "newcolumn"},
//                                                {type: "button", value: "Cancel",name:"PhRegCancel"}
//                                            ]}
//                                        ]}
//                                    ]}
//                                ]
//                    return NewPhoneRegistrationData;
//                }
	};
})(jQuery, this);