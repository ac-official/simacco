$(document).ready(function(e) {

	var resetData; 
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
					type		: "password",
					name		: "new_pass",
					label		: "New Password",
					value		: "",
					required	: true,
					
				},{
					type		: "password",
					name		: "confirm_pass",
					label		: "Confirm Password",
					value		: "",
					required	: true,
					
				}, {
					type: "block", inputWidth: 240, list:[{
							type	: "button", 
							value	: "Save",
							name	: "ChangePass",
							position	: "absolute", 
							inputLeft	: 120,
							inputTop	: -20
						},{
							type:"newcolumn"
						},{
							type		: "button", 
							value		: "Cancel", 
							position	: "absolute", 
							inputLeft	: 205,
							inputTop	: -20,
							name		: "Cancel"
						},{
                                                    type    :"hidden",
                                                    name    :"pwd_reset",
                                                    value   :"1"
                                                }]
					
			}]
			
	}];
		
	resetPassForm = new dhtmlXForm("resetForm", resetData);
	resetPassForm.setItemFocus("new_pass");
	
	resetPassForm.attachEvent("onButtonClick", function(name) {		
		if(name == 'ChangePass') doResetPassword(); 
	});
	resetPassForm.attachEvent("onEnter", function() {
            doResetPassword();
        });
	function doResetPassword() {
		resetPassForm.validate();
		var doValidateReset = resetPassForm.validate();
		if(doValidateReset) {
                    $("#resetPassForm").submit();
		}
	}	         
});