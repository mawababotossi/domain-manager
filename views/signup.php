<?php 

//add_action('init', 'at_signup_form_make_widget');
add_shortcode( 'atsf', 'at_signup_form_shortcode' );

function at_signup_form_display_widget(){
$site_url = site_url();
$redirect_to = site_url().'/dashboard';
$AT_BASE = plugins_url( '' , __FILE__ );
//$country = PepakIpToCountry::IP_to_Country_Full('8.8.8.8');

//print_r($_SESSION);
if(!is_user_logged_in()){
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
</style>

<div class="row">
    <div class="col-xs-3 visible-desktop">&nbsp;<img src="<?php echo $AT_BASE; ?>/../images/signup3_XS.png"></div>
    
    <div class="col-xs-8" style="margin-top:40px">
        <div id="alert2"  class="alert alert-danger hidden">
            <a class="close _close" data-dismiss="alert" href="#" style="float:right; color:#BBB">×</a>
            <p><?php _e('Vérifiez que tous les champs sont bien renseignés.', 'active-texto'); ?></p>
        </div>
        
        <div id="alert3"  class="alert allert-danger hidden">
            <a class="close _close" data-dismiss="alert" href="#" style="float:right; color:#BBB">×</a>
            <p><?php _e('Le code de vérification que vous avez renseigné est erroné.', 'active-texto'); ?></p>
        </div>
        
        <div id="alert4"  class="alert alert-danger hidden">
            <a class="close _close" data-dismiss="alert" href="#" style="float:right; color:#BBB">×</a>
            <p><?php _e('Ce numéro existe déjà dans notre base de données.', 'active-texto'); ?> </p>
        </div>
        <div id="alert6"  class="alert alert-info hidden">
            <a class="close _close" data-dismiss="alert" href="#" style="float:right; color:#BBB">×</a>
            <p><?php _e("Félicitations! Vous devez maintenant confirmer votre numéro de téléphone en fournissant le code de vérification que vous recevez à l'instant par sms.","active-texto"); ?><br/>
            </p>
        </div>
        
        <div id="alert5"  class="alert alert-success hidden">
            <p><?php _e('Félicitations votre compte a été créé avec succes. Vous pouvez dès à présent vous connecter à votre compte et envoyer des SMS.', 'active-texto'); ?><br/>
                <a href="<?php echo $site_url; ?>/app/#/dashboard" class="btn btn-primary"><?php _e('Se connecter', 'active-texto'); ?></a>
            </p>
        </div>
        
        
        <form id="signup-form" action="" class="form-horizontal">
            <fieldset id="personal">
                <div><?php wp_nonce_field('user-signup', '_wpnonce'); ?></div>
                <div class="row form-group">
                    <label class="col-xs-3" for="userPhone"><?php _e('Téléphone mobile', 'active-texto'); ?></label>
                    <div class="_input-group col-xs-8">
                        <input class="form-control" 
                               style="padding-left:45px" 
                               id="userPhone" 
                               name="userPhone" 
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
                            <div class="caret"></div>
                        </button>
                        <ul class="dropdown-menu" id="countries-list" style="max-height:200px; overflow:auto; width:380px">
                            <li><a tabindex="-1" href="#" onclick="sCountry('tg')">
                                <span class="c-flag" style="background-position: 0 -605px">&nbsp;</span> Togo +228</a>
                            </li>
                            <li class="divider"></li>
                        </ul>
                    </div>
                </div>
                
                <div class="row form-group">
                    <label class="col-xs-3" for="userFname"><?php _e('Prénom', 'active-texto'); ?></label>
                    <div class="col-xs-8">
                        <input class="form-control" 
                               id="userFname" 
                               name="userFname" 
                               size="30" 
                               type="text" 
                               minLength="3" 
                               maxLength="16" 
                               regContent="^[a-zA-Z]+$" >
                        <span class="help-inline hide"><?php _e('Ex. Timothy', 'active-texto'); ?></span>
                    </div>
                </div>
                
                <div class="row form-group">
                    <label class="col-xs-3" for="userSname"><?php _e('Nom', 'active-texto'); ?></label>
                    <div class="col-xs-8">
                        <input class="form-control" 
                               id="userSname" 
                               name="userSname" 
                               size="30" 
                               type="text" 
                               minLength="3" 
                               maxLength="16" 
                               regContent="^[a-zA-Z]+$" >
                        <span class="help-inline hide"><?php _e('Ex. Grant', 'active-texto'); ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-xs-3" for="company"><?php _e('Société', 'active-texto'); ?></label>
                    <div class="col-xs-8">
                        <input class="form-control" 
                               id="company" 
                               name="company" 
                               size="30" 
                               type="text" 
                               minLength="3" 
                               maxLength="16" 
                               regContent="^[a-zA-Z-09.-_ ]+$" >
                        <span class="help-inline hide"><?php _e('Ex. PWS S.A.R.L', 'active-texto'); ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-xs-3" for="userMail"><?php _e('E-mail', 'active-texto'); ?></label>
                    <div class="col-xs-8">
                        <input class="form-control" 
                               id="userMail" 
                               name="userMail" 
                               size="30" 
                               type="text" 
                               minLength="9" 
                               maxLength="64" 
                               regContent="^[a-zA-Z-09.-_@]+$" >
                        <span class="help-inline hide"><?php _e('Entrez une adresse mail valide', 'active-texto'); ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-xs-3" id="optionLegal" style="padding-top:0">
                        <?php _e('Dispositions légales', 'active-texto'); ?>
                    </label>
                    <div class="col-xs-8">
                        <input type="checkbox" 
                               id="legalC" 
                               name="legalC" 
                               value="legal" 
                               minLength="1">
                        <div style="position:absolute;top:0"></div>
                        <span ><?php _e("J'ai lu et j'accepte les ","active-texto"); ?><a href="./terms-of-use" target="_blank">
                            <?php _e("conditions d'utilisation","active-texto"); ?></a>
                        </span>
                    </div>
                </div>
                
                <div class="hide">
                    <input class="hide" id="Country" name="Country" type="hidden" value="Togo">
                    <input class="hide" id="CountryPhonePrefix" name="CountryPhonePrefix" type="hidden" value="228">
                </div>
            
            </fieldset>
            
            <fieldset>
                <div id="verification" class="control-group hidden">
                    <label class="control-label" for="userPass"><?php _e('Code de vérification', 'active-texto'); ?></label>
                    <div class="controls">
                        <input class="input-xlarge" 
                               id="userPass" 
                               name="userPass" 
                               size="30" 
                               type="text" 
                               rel="Activation" 
                               data-content="<?php _e('Vous allez recevoir un un code de verification par sms dans quelques secondes. Entrez ce code ici et cliquez sur créer mon compte', 'active-texto'); ?>">
                        <span class="help-inline"><?php _e('Entrez le code reçu par sms.', 'active-texto'); ?></span>
                    </div>
                </div><!-- /clearfix -->
                
                <div class="form-actions" style="background:transparent">
                    <a href="#" id="submit-button" 
                       class="btn btn-success btn-large" 
                       data-loading-text="<?php _e('Chargement...', 'active-texto'); ?>" 
                       onclick="getAccount()">
                        <?php _e('Créer mon compte', 'active-texto'); ?>
                    </a>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<script type="text/javascript" src="<?php echo $AT_BASE ?>/../js/g.countries.js"></script>
<script type="text/javascript" src="<?php echo $AT_BASE ?>/../js/app_signup.js"></script>
<script type="text/javascript">
function getAccount(){
/*-----------Checking----------------------*/
jQuery('#signup-form input:visible').each(function(index){
	validateForm(jQuery(this));
});

if(jQuery('#signup-form input.has-error').size()==0){
//jQuery('#submit-button').prepend('<i class="icon-spinner icon-spin"></i>&nbsp;');
   
//jQuery('#submit-button').button('loading').delay(5000).queue(function(){jQuery('#submit-button').button('reset')});

var formdata = jQuery('#signup-form').serialize();
jQuery.ajax({
type: "POST",
url: "<?php echo $site_url ?>/wp-admin/admin-ajax.php?action=register_user",
data: formdata,
dataType: "json",
success: function(data) {
	jQuery('#submit-button').text("<?php _e('Créer mon compte', 'active-texto'); ?>");
	//alert(data.response);
	switch(data.response){
		case 'phoneAdded':
		case 'accountToComplete': jQuery('#verification').show().removeClass('hidden');
			 jQuery('#personal').hide();jQuery('#alert6').show().removeClass('hidden');
			 jQuery('#submit-button').text("<?php _e('Confirmer', 'active-texto'); ?>");
			 break;
			
		case 'activationComplete': jQuery('#alert5').show().removeClass('hidden');jQuery('#verification').hide(); jQuery('.form-actions').hide();break;
		case 'activationError': getCFDiv(jQuery('#userPass')).show().addClass('error'); jQuery('#alert3').show().removeClass('hidden'); break;
		case 'accountAlreadyExist' : jQuery('#alert4').show().removeClass('hidden'); break;
	}
},
error:function (xhr, ajaxOptions, thrownError){
    jQuery('#submit-button').text('Créer mon compte');
    alert(xhr.status);
}  
});
}else{
	jQuery('#alert2').fadeIn();
     }
}

jQuery(function(){
	jQuery('#signup-form input').focus(function(){//alert(jQuery(this).parent().html());
		if(!jQuery(this).hasClass('success')) getILHSpan(jQuery(this)).fadeIn('slow');
	}).blur(function(){
		if(!jQuery(this).hasClass('error')) getILHSpan(jQuery(this)).fadeOut('slow');
	}).keyup(function(){
		jQuery('.block-message:visible').fadeOut();
		//validateForm(jQuery(this));
	}).change(function(){
		jQuery('.alert:visible').fadeOut();
		validateForm(jQuery(this));
	});
	jQuery('._close').click(function(){
		jQuery(this).parent().fadeOut();
	});
	//jQuery("input#userPass").popover({offset: 10}).click(function(){jQuery(this).popover('hide')});
	jQuery('#userPhone').keyup(function(){
		return setCountryFromPhone(jQuery(this).val());
	});
    
	for(key in Country){
		var k = Country[key];
		jQuery('#countries-list').append('<li><a tabindex="-1" href="#" onclick="sCountry('+"'"+key+"'"+')"><span class="c-flag" style="background-position: 0 '+k.imgPosition+'">&nbsp;</span> '+k.name+' '+"+"+k.code+'</a></li>'); 
       // console.log('sCountry('+"'"+key+"'"+')');
	}
              
});

</script>
<?php
	$signup_form = ob_get_contents();
	ob_end_clean();
	
	return $signup_form;
    }else{

echo 
'<p>&nbsp;</p>'.
'<div id="alert3"  class="alert">'.
  '<a class="close _close" data-dismiss="alert" href="#" style="float:right; color:#BBB">×</a>'.
  '<p>'.__('Veuillez vous déconnecter pour procéder').'</p>'.
'</div>';
    }
}

function at_signup_form_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'foo' => 'something',
	), $atts ) );

	//return "foo = {$foo}";
	return at_signup_form_display_widget();
}
