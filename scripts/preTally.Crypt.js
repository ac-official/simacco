document.write('<script type="text/javascript" src="crypt/scripts/pidcrypt.js"></script>');
document.write('<script type="text/javascript" src="crypt/scripts/pidcrypt_util.js"></script>');
document.write('<script type="text/javascript" src="crypt/scripts/asn1.js"></script>');
document.write('<script type="text/javascript" src="crypt/scripts/jsbn.js"></script>');
document.write('<script type="text/javascript" src="crypt/scripts/rng.js"></script>');
document.write('<script type="text/javascript" src="crypt/scripts/prng4.js"></script>');
document.write('<script type="text/javascript" src="crypt/scripts/rsa.js"></script>');

function certParser(cert){
    var lines = cert.split('\n');
    var read = false;
    var b64 = false;
    var flag = '';
    var retObj = {};
    retObj.info = '';
    retObj.salt = '';
    retObj.iv;
    retObj.b64 = '';
    retObj.aes = false;
    retObj.mode = '';
    retObj.bits = 0;
    for(var i=0; i< lines.length; i++){
        flag = lines[i].substr(0,9);
        if(i==1 && flag != 'Proc-Type' && flag.indexOf('M') == 0)//unencrypted cert?
            b64 = true;
        switch(flag){
            case '-----BEGI':
                read = true;
                break;
            case 'Proc-Type':
                if(read)
                    retObj.info = lines[i];
                break;
            case 'DEK-Info:':
                if(read){
                    var tmp = lines[i].split(',');
                    var dek = tmp[0].split(': ');
                    var aes = dek[1].split('-');
                    retObj.aes = (aes[0] == 'AES')?true:false;
                    retObj.mode = aes[2];
                    retObj.bits = parseInt(aes[1]);
                    retObj.salt = tmp[1].substr(0,16);
                    retObj.iv = tmp[1];
                }
                break;
            case '':
                if(read)
                    b64 = true;
                break;
            case '-----END ':
                if(read){
                  b64 = false;
                  read = false;
                }
                break;
            default:
                if(read && b64)
                    retObj.b64 += pidCryptUtil.stripLineFeeds(lines[i]);
        }
    }
    return retObj;
}

/*var public_key_1024  = '-----BEGIN PUBLIC KEY-----\n\
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDVd/gb2ORdLI7nTRHJR8C5EHs4\n\
RkRBcQuQdHkZ6eq0xnV2f0hkWC8h0mYH/bmelb5ribwulMwzFkuktXoufqzoft6Q\n\
6jLQRnkNJGRP6yA4bXqXfKYj1yeMusIPyIb3CTJT/gfZ40oli6szwu4DoFs66IZp\n\
JLv4qxU9hqu6NtJ+8QIDAQAB\n\
-----END PUBLIC KEY-----'; */     
                        
var public_key_4096  = '-----BEGIN PUBLIC KEY-----\n\
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAseqNvNv15syoKQl2/TxK\n\
/swS1zJEE7pGzvp9LWDaTsVon7j4pgPS6VYwOW8/Id+IRzFhe7Sgx74T+LBlCD9e\n\
GY2DcE+HssNePyASsWFHITumHxRJRGyT0pC8Qnr8qStHd9uNFKFYJrmsVm4kZt/Z\n\
YjzqxzZ3xl7sR95YyPaOJEBzA0bBwDd7IyASfIy8JZrs5AmNnnfn17WrEVh7RDUm\n\
1zU4Pwkgl6DwKb8iFqCseGBgoYJhYQXQ3Lg1GGIU162kKmlAd17OmaU5Eqw2Poxl\n\
sm3nORs5aVFUeqCVHsS9dFgbFVpNE/Nt8rFTFondjV2hN4Z+K3GHyDI3csFiHxlF\n\
arGQEVpgEQO0uro5IjS6j8j1gwm6rsMJ9x+UBQrPtZi1DJRk7ZqezKOQGSUHh2tj\n\
uOJTWn8kARoriv47qCIUHFYGphwhZgZSCWcA2hP5dCqIuGxtMFR1ZZ5G9SIl+8+Q\n\
Pnvi19oPZEuf9yOcPFiPMVdq5jQE+SLP8+cb4Dbz/KY+4T7m3LMRXL5UzzylkYhM\n\
sDJQIhQE/UcSiVXlJ3fszWAm59wrWJrFRh8VePqvZv/KfhlgR1UAUg2GZY4vN2/8\n\
mPquS4hC6muDL55JAqH/R9W1/Fu0tiynHnA80s1gsXsDlqdeugZD86mEBrP7sEm/\n\
gsjdlY74RZ/KvwupUy+7WukCAwEAAQ==\n\
-----END PUBLIC KEY-----';      

function encrypt(param){

  var input 		= param;
  var public_key 	= public_key_4096;
  var params 		= {};
  
    params = certParser(public_key);
    if(params.b64){
        var key = pidCryptUtil.decodeBase64(params.b64); //new RSA instance
        var rsa = new pidCrypt.RSA(); //RSA encryption
        var asn = pidCrypt.ASN1.decode(pidCryptUtil.toByteArray(key));//ASN1 parsing
        var tree = asn.toHexTree();
        rsa.setPublicKeyFromASN(tree);//setting the public key for encryption
        var t = new Date();  // timer
        crypted = rsa.encrypt(input);

        //pidCryptUtil.formatHex(crypted,63); //Text encrypted with public key as hex coded string
        //pidCryptUtil.fragment(pidCryptUtil.encodeBase64(pidCryptUtil.convertFromHex(crypted)),64); //Base64 encoded (RSA encrypted) text for testing of decryption with openssl:

        return pidCryptUtil.fragment(pidCryptUtil.encodeBase64(pidCryptUtil.convertFromHex(crypted)),64);

    } else alert('Could not find public key.');
} 

function decrypt(param){
    var input 		= param;
    var public_key 	= public_key_4096;
    var params 		= {};
  
    params = certParser(public_key);
    if(params.b64){

        var key = pidCryptUtil.decodeBase64(params.b64);   
        var rsa = new pidCrypt.RSA();  
        var asn = pidCrypt.ASN1.decode(pidCryptUtil.toByteArray(key));  
        var tree = asn.toHexTree();  
        rsa.setPrivateKeyFromASN(tree);   
        var ciphertext = pidCryptUtil.decodeBase64(pidCryptUtil.stripLineFeeds(input));  
        return rsa.decrypt(pidCryptUtil.convertToHex(ciphertext));  

    } else alert('Could not find public key.');
    
}