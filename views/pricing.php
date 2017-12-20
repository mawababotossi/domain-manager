<?php 

add_shortcode( 'atpricing', 'at_pricing_shortcode' );

function at_pricing_display_widget(){
$site_url = site_url();
$AT_BASE = plugins_url( '' , __FILE__ );
//$country = PepakIpToCountry::IP_to_Country_Full('8.8.8.8');

ob_start();
	?>
<style>
.c-flag {
	background: url(<?php echo $AT_BASE; ?>/../images/flags2.gif) 0 -605px;
	width: 16px;
	height: 11px;
	overflow: hidden;
	margin-left: 4px;
	display:inline-block;
}
    
.autocomplete-suggestions {
    text-align: left; cursor: default; border: 1px solid #ccc; border-top: 0; background: #fff; box-shadow: -1px 1px 3px rgba(0,0,0,.1);
    /* core styles should not be changed */
    position: absolute; display: none; z-index: 9999; max-height: 254px; overflow: hidden; overflow-y: auto; box-sizing: border-box;
}
.autocomplete-suggestion { 
    position: relative; padding: 0 .6em; line-height: 23px; white-space: nowrap; overflow: hidden; font-size: 1.02em; color: #333; 
}
.autocomplete-suggestion b {
    font-weight: bold; color: #555; 
}
.autocomplete-suggestion.selected { background: #f0f0f0; }

</style>

<div class="row">
    <div class="col-xs-3 visible-desktop">&nbsp;<img src="<?php echo $AT_BASE; ?>/../images/signup3_XS.png"></div>
    
    <div class="col-xs-8" style="margin-top:40px">
        <form id="pricing-form" action="" class="form-horizontal">
            <fieldset id="personal">
                <div class="row form-group">
                    <label class="col-xs-12" for="country"><?php _e('Entrez le nom ou le code téléphonique du pays', 'active-texto'); ?></label>
                    <div class="_input-group col-xs-12">
                        <input class="form-control" 
                               style="padding-left:45px" 
                               id="country" 
                               name="country" 
                               size="30" 
                               type="text" 
                               minLength="8" 
                               maxLength="12" 
                               regContent="^[0-9\+]+$"
                               value="+228">
                        
                        <button id="c-flag-btn" 
                                style="position: absolute; top: 1px; width:40px; border:none; border-radius:0" 
                                class="btn dropdown-toggle" 
                                data-toggle="dropdown">
                            <div class="c-flag" id="c-flag_">&nbsp;</div>
                            <div class="caret hide"></div>
                        </button>
                        <ul class="dropdown-menu hide" id="countries-list" style="max-height:200px; overflow:auto; width:380px">
                            <li><a tabindex="-1" href="#" onclick="sCountry('tg')">
                                <span class="c-flag" style="background-position: 0 -605px">&nbsp;</span> Togo +228</a>
                            </li>
                            <li class="divider"></li>
                        </ul>
                    </div>
                </div>
            </fieldset>
            
            <div class="well" id="price_well" style="font-size:22px; font-weight:bold; text-align:center"></div>
            
            <br><br>
            <div class="buttons">
                <div class="signup-button" style="text-align:center">
                    <a href="../inscription" class="btn btn-lg btn-primary"><?php _e('Créer mon compte', 'active-texto'); ?></a>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="<?php echo $AT_BASE ?>/../js/g.countries.js"></script>
<script type="text/javascript" src="<?php echo $AT_BASE ?>/../js/jquery.auto-complete.js"></script>
<script type="text/javascript">
    
sCountry('tg');
requestPrice('228');
    
function sCountry(c){
	var s = Country[c]; 
	//document.getElementById('c-flag_').style.backgroud-position-y = ;
	//jQuery("#c-flag_").css({background-position:+"0 "+ s.imgPosition });
    jQuery("#c-flag_").attr("style", "background-position:0 "+ s.imgPosition);
	jQuery("#country").val(s.name);
    requestPrice(s.code);
}

function setCountryFromName(name){
var name = name;
  for(key in Country){
      var k = Country[key];
      if(name.toLowerCase() == k.name.toLowerCase() || name.replace("+","").indexOf(k.code) === 0){
           jQuery("#c-flag_").attr("style", "background-position:0 "+ k.imgPosition); console.log(k.name);
           requestPrice(k.code);
      }
  }
}

function requestPrice(prefix){
        jQuery.ajax({
        type: "POST",
        url: "<?php echo $site_url ?>/wp-admin/admin-ajax.php?action=get_country_price",
        data: 'prefix='+prefix,
        dataType: "json",
        success: function(data) {
        jQuery("#price_well").html(data.country+'<br><br>'+'EUR '+data.euro+'&nbsp; &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;'+data.xof+' XOF'+'<br><span style="font-size:11px; font-weight:normal">'+data.networks+'</span>');
           
        },
        error:function (xhr, ajaxOptions, thrownError){
            jQuery('#submit-button').text('Créer mon compte');
            alert(xhr.status);
        }  
        });
}
    
    
jQuery(function(){
	jQuery('#country').keyup(function(){
		return setCountryFromName(jQuery(this).val());
	}).change(function(){
		return setCountryFromName(jQuery(this).val());
	});
    
	for(key in Country){
		var k = Country[key];
		jQuery('#countries-list').append('<li><a tabindex="-1" href="#" onclick="sCountry('+"'"+key+"'"+')"><span class="c-flag" style="background-position: 0 '+k.imgPosition+'">&nbsp;</span> '+k.name+' '+k.code+'</a></li>'); 
       // console.log('sCountry('+"'"+key+"'"+')');
	}
    
    
    jQuery('#country').autoComplete({
                minChars: 1,
                /*menuClass: 'suggestions',*/
                source: function(term, suggest){
                    term = term.toLowerCase();
                    var choices = Country;
                    var suggestions = [];
                    for(key in Country){
		                var k = Country[key];
                        if (k.name && k.name.toLowerCase().indexOf(term)!=-1)  suggestions.push(k.name);
                    }
                    
                    suggest(suggestions);
                },
                
                renderItem: function (item, search){
                    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                    return '<div class="autocomplete-suggestion" data-val="' + item + '">' + item.replace(re, "<b>$1</b>") + '</div>';
                },
                
                onSelect: function(e, term, item){
                    setCountryFromName(item.data("val"));
                }
                         
            });
              
});

</script>
<?php
	$pricing_form = ob_get_contents();
	ob_end_clean();
	
	return $pricing_form;
    
}

function at_pricing_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'foo' => 'something',
	), $atts ) );

	return at_pricing_display_widget();
}
