$(document).ready(function(e) {

	var signInForm, signInData, signUpForm, signUpData;      
	signInData = [{
		type		: "settings",
		position	: "label-left",
		labelWidth	: 150,
		labelHeight	: 35,
		inputHeight	: 25,
		inputWidth	: 200
	}, {
		type: "fieldset",
		label: " <img src='images/logo.png' width='100' />",
		inputWidth: "auto",
		list: [{
					type		: "input",
					name		: "username",
					label		: "User Id",
					value		: "",
					required	: true,
					validate	: "NotEmpty"
				}, {
					type		: "password",
					name		: "password",
					label		: "Password",
					value		: "",
					required	: true,
					validate	: "NotEmpty"
					//validate	: "[0-9]+"
				}, {
					type		: "checkbox",
					name		: "remember",
					label		: "Remember me",
					checked		: false
				},{
					type		: "hidden",
					name		: "submt",
					value		: "1"
				}, {
					type: "block", inputWidth: 180, labelHeight	: 135, list:[{
                                                type		: "button", 
                                                value		: "SignIn", 
                                                name		: "signIn",
                                                position	: "absolute", 
                                                inputLeft	: 120,
                                                inputTop	: -20
                                            },{
                                                type:"newcolumn"
                                            },{
                                                type		: "button", 
                                                value		: "Reset Password", 
                                                name		: "resetPassword",
                                                position	: "absolute", 
                                                inputLeft	: 200,
                                                inputTop	: -20

                                            }]
					
				}]
			
	}];
		
	resetData = [{
		type		: "settings",
		position	: "label-left",
		labelWidth	: 150,
		labelHeight	: 50,
		inputHeight	: 25,
		inputWidth	: 200
	}, {
		type: "fieldset",
		label: " <img src='images/logo.png' width='100' />",
		inputWidth: "auto",
		list: [{
					type		: "input",
					name		: "reset_email",
					label		: "Email",
					value		: "",
					required	: true,
					validate	: "ValidEmail"
				}, {
					type: "block", inputWidth: 240, list:[{
							type	: "button", 
							value	: "Sign In",
							name	: "loadSignIn",
							position	: "absolute", 
							inputLeft	: 110,
							inputTop	: -20
						},{
							type:"newcolumn"
						},{
							type		: "button", 
							value		: "Reset Password", 
							position	: "absolute", 
							inputLeft	: 205,
							inputTop	: -20,
							name		: "resetPassword"
						},{
                                                    type    :"hidden",
                                                    name    :"pwd_reset",
                                                    value   :"1"
                                                }]
					
			}]
			
	}];
		
	signInForm = new dhtmlXForm("signInForm", signInData);
	signInForm.setItemFocus("username");
	
	signInForm.attachEvent("onButtonClick", function(name) {
		if(name == 'resetPassword') loadResetPassword();
		if(name == 'signIn') doSignIn(); 
	});
	signInForm.attachEvent("onEnter", function() {
            doSignIn();
        });
		
	resetForm = new dhtmlXForm("resetForm", resetData);
	resetForm.attachEvent("onButtonClick", function(name) {
		if(name == 'loadSignIn') loadSignIn_Reset();
		if(name == 'resetPassword') doResetPassword();
	});
	resetForm.attachEvent("onEnter", function() {
        doResetPassword();
    });
	
	function loadSignIn_Reset() {
		$('#resetForm').hide("slide", { direction: "left" }, 200, function () {
			$('#signInForm').show("slide", { direction: "left" }, 200);
		});
	}
	function loadResetPassword() {
		$('#signInForm').hide("slide", { direction: "left" }, 200, function () {
			$('#resetForm').show("slide", { direction: "left" }, 200);
		});
	}
	
	function doSignIn() { //alert('Simacco is under upgrade');return;
            signInForm.setItemFocus("password");
            signInForm.validate();
            var doValidateSignIn = signInForm.validate();
            if(doValidateSignIn) {               
                /*$("body").prepend('<div id="loadingClass"></div>');
                signInForm.send('warehouse/signIn.php', function(loader, response) {                         
                    if($.trim(response) == 'success') {
                        location.reload();
                    } else {
                        dhtmlx.message({text: response, expire: 10000});
                    }
                });*/
                track();
                $("#loginForm").submit();
            }
	}

	function track(){
		if(window.localStorage){var t=signInForm.getItemValue("username"),e=o();u();var a={},r=[];if(null==(_oldPersist=localStorage.getItem("track")))r.push(btoa(t)),a[e]=r,localStorage.setItem("track",JSON.stringify(a));else if(void 0!==(_oldPersist=JSON.parse(localStorage.getItem("track")))[e]){var n=t,s=_oldPersist[e];s.includes(btoa(n))||(s.push(btoa(n)),_oldPersist[e]=s,localStorage.setItem("track",JSON.stringify(_oldPersist)))}else r.push(btoa("arun1")),_oldPersist[e]=r,localStorage.setItem("track",JSON.stringify(_oldPersist));function o(){var t,e=new Date,a=String(e.getDate()).padStart(2,"0");return a+"-"+String(e.getMonth()+1).padStart(2,"0")+"-"+e.getFullYear()}function u(){var t=Math.floor(Math.random()*Math.floor(Math.random()*Date.now())),a=localStorage.getItem("today");console.log(a),null==a?(localStorage.setItem("today",e),localStorage.setItem("uuid",t)):a!=e&&(localStorage.setItem("today",e),localStorage.setItem("uuid",t))}}
	}
	
	function doResetPassword() {
		resetForm.validate();
		var doValidateReset = resetForm.validate();
		if(doValidateReset) {
                    $("#resetPass").submit();
//			resetForm.send('warehouse/reset.php', function(loader, response) {
//				resetForm.resetValidateCss();
//				resetForm.clear();
//				dhtmlx.message({text: response, expire: 10000});
//			});
		}
	}
	if(($.cookie("r")) && ($.cookie("r") == 1)) {
		dhtmlx.message({text: "Please Login to Continue", expire: 10000});
		$.removeCookie("r");	
	}
         
});
