;(function ($, window, undefined) { 
    preTally.Validate = {        
        Validate : function(data,label,form,ch){ 
            var checkValid = data[label].trim();
            var exp='';
            switch(ch)
            {
                case 'title':            
                     exp = /^[A-Za-z][A-Za-z0-9 & .]*$/;
                    break;
                case 'email': 
                     exp = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
                    break;
                case 'combo':            
                     exp = /^[A-Za-z0-9 & .]+$/;
                    break;
                case 'percent':
                     exp = /^[0-1]?[0-9]{1,2}$/;
                     break;  
                case 'decimal':
                     exp = /^([0-9]*|\d*\.\d{1}?\d*)$/;
                     break; 
                case 'password' :
                     exp = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[-_!@#$%^&*])[-_a-zA-Z0-9!@#$%^&*]{5,20}$/;
                     break;  
                case 'username' :
                     exp = /^[A-Za-z_]\w{4,20}$/;
                     break; 
                default:
  		  alert('Validation Failed');
                  break;
            }
            
            
            if (exp.test(checkValid)){
                form.setValidateCss(label, true ); 
                return true;
            }else { 
                form.setValidateCss(label, false );
                return false;
            }
        }
        
    };

})(jQuery, this);