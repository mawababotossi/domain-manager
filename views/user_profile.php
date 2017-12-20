<?php


if(!(function_exists('get_user_to_edit'))){
	require_once(ABSPATH.'/wp-admin/includes/user.php');
}

if(!(function_exists('_wp_get_user_contactmethods'))){
	require_once(ABSPATH.'/wp-includes/registration.php');
}
/**/
	
function plugin_url(){
	$currentpath = dirname(__FILE__);
	$siteurl = get_option('siteurl').'/';
	$plugin_url = str_replace(ABSPATH,$siteurl,$currentpath);
	
	return $plugin_url;
}

add_shortcode( 'atprofile', 'at_profile_shortcode' );
add_action('personal_options_update', 'update_extra_profile_fields');

add_action( 'show_user_profile', 'at_add_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'at_add_custom_user_profile_fields' );

add_action( 'personal_options_update', 'update_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'update_extra_profile_fields' );
 
function update_extra_profile_fields($user_id) {
     if ( current_user_can('edit_user',$user_id) ){
            if(isset($_POST['user_currency']))  update_user_meta($user_id, 'user_currency', $_POST['user_currency']);
	    if(isset($_POST['user_lang'])) {
	       update_user_meta($user_id, 'user_lang', $_POST['user_lang']);/*$_SESSION['WPLANG'] = $_POST['user_lang']; $_GET['lang'] = $_POST['user_lang'];*/
	    }

	    if(isset($_POST['company'])) update_user_meta($user_id, 'user_company', $_POST['company']);
	    if(isset($_POST['senderIDs'])) update_user_meta($user_id, 'senderIDs', $_POST['senderIDs']);
	    if(isset($_POST['balance']) && current_user_can('edit_users') ) update_user_meta($user_id, 'user_balance', $_POST['balance']);
	    if(isset($_POST['country'])) update_user_meta($user_id, 'user_country', $_POST['country']);
	    if(isset($_POST['default-phone-prefix'])) update_user_meta($user_id, 'user_country_phone_prefix', $_POST['default-phone-prefix']);
	}
}


function at_profile_process_form(){
	
	global $wpdb;

	$wpdb->hide_errors();
	error_reporting(0);
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;

	if(!empty($_POST['action'])){   
		check_admin_referer('update-profile_' . $user_id); 
		$errors = edit_user($user_id);
 
		if ( is_wp_error( $errors ) ) { foreach( $errors->get_error_messages() as $message ) $errmsg = "$message"; }

		if($errmsg == '')
			{do_action('personal_options_update', $user_id);}
		else
			{$errmsg = '' . $errmsg . ''; return $errmsg;}
	} 
}
	
function at_profile_display_widget(){
	
	if(isset($_POST['action'])) $error = at_profile_process_form();	
	
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;		
	$profileuser = get_user_to_edit($user_id);		
	
	ob_start();
	?>

	<div class="row" style="margin-top:20px">
	   <div class="col-md-4 visible-md visible-lg">&nbsp;<img src="<?php echo WP_PLUGIN_URL; ?>/ActiveTexto/images/signup3_XS.png"></div>
	   <div class="col-md-6">
	      <?php if($error != '') 
	         echo '<div class="alert alert-error">'.
		'<a class="close _close" data-dismiss="alert" href="#" style="float:right; color:#BBB">×</a><p>'.$error.'</p></div>';
	      ?>
	      <form name="profile" action="<?php echo site_url(); ?>/profile/?profile" method="post" enctype="multipart/form-data" class="form-horizontal">
	         <?php wp_nonce_field('update-profile_' . $user_id) ?>
  	         <input type="hidden" name="from" value="profile" />
  	         <input type="hidden" name="action" value="update" />
  	         <input type="hidden" name="checkuser_id" value="<?php echo $user_id ?>" />
  	         <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />

	         <?php do_action('personal_options', $profileuser); ?>
	         <?php do_action('profile_personal_options', $profileuser); ?>

	         <div class="form-group">	
	           <label class="col-xs-3" for="user_login"><?php _e('Identifiant','active-texto'); ?></label>
	           <div class="col-xs-8">
	              <input type="text" name="user_login" id="user_login" value="<?php echo esc_attr($profileuser->user_login); ?>" disabled="disabled" class="form-control" />
	              </br><span class="help-inline hide"><?php __('Ne peut être changé.','active-texto'); ?></span>
	           </div>
	        </div>
		
	        <div class="form-group">			
	           <label class="col-xs-3" for="first_name"><?php _e('Prénom','active-texto') ?></label>
	           <div class="col-xs-8">
	              <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($profileuser->first_name) ?>" class="form-control" />
	           </div>
	        </div>
	
	        <div class="form-group">	
	           <label class="col-xs-3" for="last_name"><?php _e('Nom','active-texto') ?></label>
	           <div class="col-xs-8">
	              <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($profileuser->last_name) ?>" class="form-control" />
	           </div>
                </div>

	        <div class="form-group hide">
	           <label class="col-xs-3" for="nickname"><?php _e('Surnom','active-texto'); ?> 
	           <span class="help-inline"><?php _e('(requis)','active-texto'); ?></span></label>
	           <div class="col-xs-8">
	              <input type="hidden" name="nickname" id="nickname" value="<?php echo esc_attr($profileuser->nickname) ?>" class="form-control" />
	           </div>
                </div>
	
	        <div class="form-group">
	           <label class="col-xs-3" for="email"><?php _e('E-mail'); ?><?php _e('(requis)','active-texto'); ?></label>
	           <div class="col-xs-8">			
	              <input type="text" name="email" id="email" value="<?php echo esc_attr($profileuser->user_email) ?>" class="form-control" />
	              <span class="help-inline"></span>
	           </div>
                </div>
	
	        <div class="form-group">
	           <label class="col-xs-3" for="company"><?php _e('Société', 'active-texto'); ?></label>
	           <div class="col-xs-8">			
	              <input type="text" name="company" id="company" value="<?php echo esc_attr(get_the_author_meta('user_company', $user_id)); ?>" class="form-control" />
	              <span class="help-inline"></span>
	           </div>
                </div>

	        <div class="form-group">
	           <label class="col-xs-3" for="lang"><?php _e('Langue','active-texto') ?></label>
	           <div class="col-xs-8">
	              <select class="form-control" name="user_lang">
	              <?php $user_lang = esc_attr(get_the_author_meta('user_lang', $user_id)); ?>
	                 <option value="en_US" <?php if($user_lang == 'en_US') echo 'selected'; ?>>English</option>
	                 <option value="fr_FR" <?php if($user_lang == 'fr_FR') echo 'selected'; ?>>Français</option>
	              </select>
	           </div>
                </div>
	
	<div class="form-group">
	<label class="col-xs-3" for="currency"><?php _e('Devise','active-texto') ?></label>
	<div class="col-xs-8">
	<select class="form-control" name="user_currency">
	<?php $user_currency = esc_attr(get_the_author_meta('user_currency', $user_id)); ?>
	<option value="EUR" <?php if($user_currency == 'EUR') echo 'selected'; ?>>EUR</option>
	<option value="XOF" <?php if($user_currency =='XOF') echo 'selected'; ?>>FCFA</option>
	</select>
	</div></div>
	<hr />
	<span><?php __("Ignorez ces champs si vous ne souhaitez pas chager de mot de passe.",'active-texto'); ?></span>

	<div class="form-group">
	<label class="col-xs-3" for="pass1"><?php _e('Nouveau mot de passe','active-texto'); ?></label>
	<div class="col-xs-8">
	<input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" class="form-control" />
	</div></div>
	
	<div class="form-group">
	<label class="col-xs-3" for="pass1"><?php _e('Vérification','active-texto'); ?></label>
	<div class="col-xs-8">
	<input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" class="form-control" />
	<span class="help-inline"><?php _e('Saisir une deuxième fois le mot de passe.','active-texto'); ?></span>
	</div></div>
	
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($user_id); ?>" />
	<input type="submit" class="btn btn-primary" value="<?php _e('Mettre à jour','active-texto'); ?>" name="submit" />

	</form>
	</div>
	</div>
	
	<script type="text/javascript" charset="utf-8">
		if (window.location.hash == '#password') {
			document.getElementById('pass1').focus();
		}
	</script>
	<?php
	$form = ob_get_contents();
	ob_end_clean();
	
	echo $form;
}

function at_add_custom_user_profile_fields($user){
?>
	
	<?php if ( current_user_can('edit_users') ): ?>
	<div class="row">
	<label class="col-xs-3" for="balance"><?php _e('Balance', 'active-texto'); ?></label>
	<div class="col-xs-8">			
	<input type="text" name="balance" id="balance" value="<?php echo esc_attr(get_the_author_meta('user_balance', $user->ID)); ?>" class="form-control" />
	<span class="help-inline">EUR</span>
	</div></div>	
	<?php endif; ?>

	<div class="row">
	<label class="col-xs-3" for="company"><?php _e('Société', 'active-texto'); ?></label>
	<div class="col-xs-8">			
	<input type="text" name="company" id="company" value="<?php echo esc_attr(get_the_author_meta('user_company', $user->ID)); ?>" class="form-control" />
	<span class="help-inline"></span>
	</div></div>

	<div class="row">
	<label class="col-xs-3" for="lang"><?php _e('Langue','active-texto') ?></label>
	<div class="col-xs-8">
	<select class="form-control" name="user_lang">
	<?php $user_lang = esc_attr(get_the_author_meta('user_lang', $user->ID)); ?>
	<option value="en_US" <?php if($user_lang == 'en_US') echo 'selected'; ?>>English</option>
	<option value="fr_FR" <?php if($user_lang == 'fr_FR') echo 'selected'; ?>>Français</option>
	</select>
	</div></div>
	
	<div class="row">
	<label class="col-xs-3" for="currency"><?php _e('Devise','active-texto') ?></label>
	<div class="col-xs-8">
	<select class="form-control" name="user_currency">
	<?php $user_currency = esc_attr(get_the_author_meta('user_currency', $user->ID)); ?>
	<option value="EUR" <?php if($user_currency == 'EUR') echo 'selected'; ?>>EUR</option>
	<option value="XOF" <?php if($user_currency =='XOF') echo 'selected'; ?>>FCFA</option>
	</select>
	</div></div>
	
	<div class="row">
	<label class="col-xs-3" for="senderIDs"><?php _e('Sender IDs', 'active-texto'); ?></label>
	<div class="col-xs-8">			
	<input type="text" name="senderIDs" id="senderIDs" value="<?php echo esc_attr(get_the_author_meta('senderIDs', $user->ID)); ?>" class="form-control" />
	<span class="help-inline"><?php _e('Séparez par une virgule', 'active-texto'); ?></span>
	</div></div>

	<div class="row">
	<label class="col-xs-3" for="country"><?php _e('Pays', 'active-texto'); ?></label>
	<div class="col-xs-8">			
	<input type="text" name="country" id="country" value="<?php echo esc_attr(get_the_author_meta('user_country', $user->ID)); ?>" class="form-control" />
	<span class="help-inline"></span>
	</div></div>
	
	<div class="row">
	<label class="col-xs-3" for="country"><?php _e('Préfix par défaut', 'active-texto'); ?></label>
	<div class="col-xs-8">			
	<input type="text" name="default-phone-prefix" id="default-phone-prefix" value="<?php echo esc_attr(get_the_author_meta('user_country_phone_prefix', $user->ID)); ?>" class="form-control" />
	<span class="help-inline"></span>
	</div></div>
<?php
	$form = ob_get_contents();
	ob_end_clean();
	
	echo $form;

}

function at_profile_shortcode($atts){
	return at_profile_display_widget();
}


?>
