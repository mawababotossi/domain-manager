var msg = '';
jQuery(".dropdown-toggle").dropdown();
function validateForm(input){
rules = {
//"content":{"minLength":1, "maxLength":160, "regContent":/^./}, //que des chiffres
//"dests":{"minLength":11, "maxLength":100000, "regContent":/^[0-9 ,;\t\n]+jQuery/}, //que des lettres,
};
if(jQuery(input).attr("minLength") || jQuery(input).attr("maxLength") || jQuery(input).attr("regContent")){

_minLength  =  jQuery(input).attr("minLength");
_maxLength  =  jQuery(input).attr("maxLength");
_regContent =  new RegExp(jQuery(input).attr("regContent"));
_length     =  jQuery(input).val().length;
_val	    =  jQuery(input).val();

getCFDiv(jQuery(input)).removeClass('has-success').removeClass('has-error');;
jQuery(input).removeClass('has-error').removeClass('has-success');

if(jQuery(input).attr("type")=="checkbox" && jQuery(input).is(":checked")) {
//getCFDiv(jQuery(input)).addClass('success'); jQuery(input).addClass('success'); 
return true;}
if(jQuery(input).attr("type")=="checkbox" && !jQuery(input).is(":checked")) {getCFDiv(jQuery(input)).addClass('has-error'); jQuery(input).addClass('has-error'); return false;}

if((_minLength <= _length) && (_length <= _maxLength) && (_val !='') && (_regContent.test(_val))) {
	//getCFDiv(jQuery(input)).addClass('success'); jQuery(input).addClass('success');
	//getILHSpan(jQuery(input)).html('ok');
	return true;
}else{
	getCFDiv(jQuery(input)).addClass('has-error'); jQuery(input).addClass('has-error');
	//getILHSpan(jQuery(input)).html('');
	return false;
}
}
}

function getCFDiv(input){
if(jQuery(input).parent().hasClass('input-prepend')) return jQuery(input).parent().parent().parent(); 
else return jQuery(input).parent().parent(); 
}

function getILHSpan(input){
return jQuery(input).next('span');
}

function sCountry(c){
	var s = Country[c]; 
	//document.getElementById('c-flag_').style.backgroud-position-y = ;
	//jQuery("#c-flag_").css({background-position:+"0 "+ s.imgPosition });
    jQuery("#c-flag_").attr("style", "background-position:0 "+ s.imgPosition);
	jQuery("#userPhone").val("+"+s.code);
	jQuery("#Country").val(s.name);
	jQuery("#CountryPhonePrefix").val(s.code);
}

function setCountryFromPhone(phone){
  var phone = phone.replace("+","");
  for(key in Country){
      var k = Country[key];
      if(phone.indexOf(k.code) ===0)
           jQuery("#c-flag_").attr("style", "background-position:0 "+ k.imgPosition);
  }
}
