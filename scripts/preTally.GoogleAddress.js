;(function($, window, undefined) { 
  preTally.GoogleAddress = {   
    googleAddress : function (formObject) {
         
    $('[name="GOGL_PlaceSearch"]')
        .geocomplete()
        .bind("geocode:result", function(event, result){

//            console.log(result);
            //console.log(Object.keys(result['address_components']).length);

            var postalCode = Object.keys(result['address_components']).length;
            //console.log(postalCode);
            console.log('--'+result.address_components[(postalCode-1)].types[0]+'--');
            //postal_code

            var GOGLData = {
                "country"                       : "GOGL_Country", 
                "administrative_area_level_1"   : "GOGL_State", 
                "administrative_area_level_2"   : "GOGL_City", 
                "locality"                      : "GOGL_Location", 
                "sublocality_level_1"           : "GOGL_Place", 
                "route"                         : "GOGL_Street",
                "postal_code"                   : "GOGL_Pincode"
            };
            var count = 0;
            var GOGLDataCount = 0;
            var GOGLResult = result['address_components'].reverse();

            formObject.setItemValue("GOGL_Pincode","");
            Object.keys(GOGLData).forEach(function (key) {
                formObject.setItemValue(GOGLData[key],"");
            });

            var GOGLStreet = '';
            var GOGLNeighbourhood = '';
            for (var i in GOGLResult) {
                if (result['address_components'].hasOwnProperty(i)) {
                    //console.log(result.address_components[count].types[0]);

                    if((result.address_components[count].types[0] == 'sublocality_level_2' || 
                        result.address_components[count].types[0] == 'neighborhood'   ||
                        !result.address_components[count].types[0]) && 
                        (formObject.getItemValue('GOGL_City') == formObject.getItemValue('GOGL_Location'))) {
                            formObject.setItemValue('GOGL_Location',formObject.getItemValue('GOGL_Place'));
                            formObject.setItemValue('GOGL_Place',result.address_components[count].long_name);
                            //formObject.setItemValue('GOGL_Place',result.address_components[(count-1)].long_name);
                    }
                    if((result.address_components[count].types[0] == 'natural_feature') || (result.address_components[count].types[0] == 'neighborhood')) {
                        GOGLNeighbourhood = result.address_components[count].long_name;
                    }
                    if(result.address_components[count].types[0] == 'route') {
                        GOGLStreet = result.address_components[count].long_name;
                    }
                    formObject.setItemValue(GOGLData[result.address_components[count].types[0]], result.address_components[count].long_name);
                    count++;
                }
            }
            if(GOGLStreet == '') {
                formObject.setItemValue('GOGL_Street',GOGLNeighbourhood);
            }
        });
    },
    multipleGoogleAddress : function (formObject, addressCount) {

//    console.clear();
    $('[name*="GOGL_PlaceSearch"]')
        .geocomplete()
        .bind("geocode:result", function(event, result){

            var j = $(this).attr('name') == "GOGL_PlaceSearch_1" ? 1 : $(this).attr('name') =="GOGL_PlaceSearch_2" ? 2 : $(this).attr('name') == "GOGL_PlaceSearch_3" ? 3 : 0 ;

            var postalCode = Object.keys(result['address_components']).length;

            var GOGLData = {
                "country"                       : "GOGL_Country_"+j, 
                "administrative_area_level_1"   : "GOGL_State_"+j, 
                "administrative_area_level_2"   : "GOGL_City_"+j, 
                "locality"                      : "GOGL_Location_"+j, 
                "sublocality_level_1"           : "GOGL_Place_"+j, 
                "route"                         : "GOGL_Street_"+j,
                "postal_code"                   : "GOGL_Pincode_"+j
            };
            var count = 0;
            var GOGLDataCount = 0;
            var GOGLResult = result['address_components'].reverse();

            formObject.setItemValue("GOGL_Pincode_"+j,"");
            Object.keys(GOGLData).forEach(function (key) {
                formObject.setItemValue(GOGLData[key],"");
            });

            var GOGLStreet = '';
            var GOGLNeighbourhood = '';
            for (var i in GOGLResult) {
                if (result['address_components'].hasOwnProperty(i)) {

                    if((result.address_components[count].types[0] == 'sublocality_level_2' || 
                        result.address_components[count].types[0] == 'neighborhood'   ||
                        !result.address_components[count].types[0]) && 
                        (formObject.getItemValue('GOGL_City_'+j) == formObject.getItemValue('GOGL_Location_'+j))) {
                            formObject.setItemValue('GOGL_Location_'+j,formObject.getItemValue('GOGL_Place_'+j));
                            formObject.setItemValue('GOGL_Place_'+j,result.address_components[count].long_name);
                            //formObject.setItemValue('GOGL_Place_'+j,result.address_components[(count-1)].long_name);
                    }
                    if((result.address_components[count].types[0] == 'natural_feature') || (result.address_components[count].types[0] == 'neighborhood')) {
                        GOGLNeighbourhood = result.address_components[count].long_name;
                    }
                    if(result.address_components[count].types[0] == 'route') {
                        GOGLStreet = result.address_components[count].long_name;
                    }
                    formObject.setItemValue(GOGLData[result.address_components[count].types[0]], result.address_components[count].long_name);
                    count++;
                }
            }
            if(GOGLStreet == '') {
                formObject.setItemValue('GOGL_Street_'+j,GOGLNeighbourhood);
            }
        });
    }
}
})(jQuery, this);
