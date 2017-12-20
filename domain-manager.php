<?php
/*
Plugin Name: Domains Manager
Description: Make Me Registrar.
Version: 1.0
Author: Botossi Mawaba
*/
include_once(dirname(__FILE__).'/lib/utils.php');

include_once(dirname(__FILE__).'/app.controllers.php');
include_once(dirname(__FILE__).'/views/nav.php');
include_once(dirname(__FILE__).'/views/app.views.php');
include_once(dirname(__FILE__).'/admin-page.php');
include_once(dirname(__FILE__).'/woocommerce.php');

include_once(dirname(__FILE__).'/views/user_profile.php');
include_once(dirname(__FILE__).'/views/signup.php');
include_once(dirname(__FILE__).'/views/pricing.php');
include_once(dirname(__FILE__).'/views/topup.php');

/*SECURITY HACKS*/
/*define('WP_ADMIN_DIR', 'manage');
define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . WP_ADMIN_DIR);

add_filter('site_url',  'wpadmin_filter', 10, 3);
 function wpadmin_filter( $url, $path, $orig_scheme ) {
  $old  = array( "/(wp-admin)/");
  $admin_dir = WP_ADMIN_DIR;
  $new  = array($admin_dir);
  return preg_replace( $old, $new, $url, 1);
 }
*/
$AT_BASE = plugins_url( '' , __FILE__ );

/* Disable the Admin Bar. */
add_shortcode( 'domain-manager', 'manager_shortcode' );
add_filter( 'show_admin_bar', '__return_false' );
add_action( 'wp_head', 'at_add_header_menu' );
add_action('wp_head','at_add_scripts');


function at_add_scripts(){
	$siteurl = get_option('siteurl');
	$url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__));
	echo "<link rel='stylesheet' type='text/css' href='$url/css/woocommerce-custom.css' />";
        echo "<script type='text/javascript' src='$url/js/jquery.js'></script>";
	echo "<script type='text/javascript' src='$url/js/angular.js'></script>";
	echo "<script type='text/javascript' src='$url/js/angular-route.js'></script>";
        echo "<script type='text/javascript' src='$url/js/angular-resource.js'></script>";
        echo "<script type='text/javascript' src='$url/js/angular-sanitize.js'></script>";
	echo "<script type='text/javascript' src='$url/js/ui.js'></script>";
        echo "<script type='text/javascript' src='$url/js/app.js'></script>";
        echo "<script type='text/javascript' src='$url/js/bootstrap.js'></script>";
	
}

function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function at_force_user_login(){
	if((is_page('app-send-sms') || is_page('app') || is_page('profile')) && !is_user_logged_in()){
		if(!is_login_page()) auth_redirect();
	}
}

function at_set_user_lang(){
	if(is_user_logged_in()){
	$current_user = wp_get_current_user();
		
	$user_lang = esc_attr(get_the_author_meta('user_lang', $current_user->ID)); 	
	
	$_SESSION['WPLANG'] = $user_lang;
	$_GET['lang'] = $user_lang;
}
}

function at_add_header_menu(){
	$redirect = site_url().'/app' ;
	$logout_url = wp_logout_url( $redirect );
	$login_url = wp_login_url( $redirect );
	$context = '';
	if(is_user_logged_in())
	$context = '';
}


function at_hide_admin_bar_settings() {
	echo <<<STL
	<style type="text/css">
		.show-admin-bar {
			display: none;
		}
	</style>
STL;
}


function at_login_css_tweaks() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_bloginfo( 'template_directory' ) ?>/img/login-logo.png);
            padding-bottom: 30px;
        }
	#user_login {
	    font-size:16px;	
	}
	body.login div#login h1 a {
	    padding-bottom: 10px;
	}

	.login h1 a {
	    background-size: 274px;
	    height: 47px;
	}
    </style>
<?php }

/*add_filter(  'gettext',  'at_register_text'  );
add_filter(  'ngettext',  'at_register_text' );
function at_register_text( $translated ) {
     $translated = str_ireplace( 'Identifiant',  'Téléphone',  $translated );
     $translated = str_ireplace( 'Username',  'Phone',  $translated );
     return $translated;
}
*/

function at_login_logo_url() {
    return get_bloginfo( 'url' );
}


function at_login_logo_url_title() {
    return get_bloginfo( 'name' );;
}

function at_ads_widgets_init() {

	register_sidebar( array(
		'name' => 'at_sidebar',
		'id' => 'at_ads',
		'before_widget' => '<div>',
		'after_widget' => '</div>',
		'before_title' => '<h2>',
		'after_title' => '</h2>',
	) );
}

function at_disable_admin_bar() {
    add_filter( 'show_admin_bar', '__return_false' );
    add_action( 'admin_print_scripts-profile.php', 'at_hide_admin_bar_settings' );
}

function at_show_user_balance(){
	$_user_balance = get_user_meta($current_user->ID,'user_balance',true)." EUR";
	if(get_user_meta($current_user->ID,'user_currency',true)=='XOF')
	$_user_balance = (get_user_meta($current_user->ID,'user_balance',true)*655.9)." FCFA";
	echo $_user_balance;
}

function at_language_init() {
       load_plugin_textdomain( 'active-texto', false, basename( dirname( __FILE__ ) ) . '/lang' );
}



/**
 * If an email address is entered in the username box, then look up the matching username and authenticate as per normal, using that.
 *
 * @param string $user
 * @param string $username
 * @param string $password
 * @return Results of autheticating via wp_authenticate_username_password(), using the username found when looking up via email.
 */
function dr_email_login_authenticate( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) )
		return $user;

	if ( !empty( $username ) ) {
		if(strpos($username, '00')===0) $username = substr($username, 2);
		$username = str_replace( '+', '', stripslashes( $username ) );
		$username = str_replace( '&', '&amp;', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
		if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
			$username = $user->user_login;
	}

	return wp_authenticate_username_password( null, $username, $password );
}

remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'dr_email_login_authenticate', 20, 3 );

/**
 * Add compatibility for WPMU 2.9.1 and WPMU 2.9.2, props r-a-y
 */
if ( !function_exists( 'is_super_admin' ) ) :
	function get_super_admins() {
		global $super_admins;

		if ( isset( $super_admins ) )
			return $super_admins;
		else
			return get_site_option( 'site_admins', array( 'admin' ) );
	}

	function is_super_admin( $user_id = false ) {
		if ( ! $user_id ) {
			$current_user = wp_get_current_user();
			$user_id = ! empty( $current_user ) ? $current_user->id : 0;
		}

		if ( ! $user_id )
			return false;

		$user = new WP_User( $user_id );

		if ( is_multisite() ) {
			$super_admins = get_super_admins();
			if ( is_array( $super_admins ) && in_array( $user->user_login, $super_admins ) )
				return true;
		} else {
			if ( $user->has_cap( 'delete_users' ) )
				return true;
		}

		return false;
	}
endif;

/**
 * Modify the string on the login page to prompt for username or email address
 */
function username_or_email_login() {
	if ( 'wp-login.php' != basename( $_SERVER['SCRIPT_NAME'] ) )
		return;

	?><script type="text/javascript">
	// Form Label
	if ( document.getElementById('loginform') )
		document.getElementById('loginform').childNodes[1].childNodes[1].childNodes[0].nodeValue = '<?php echo esc_js( __( 'Téléphone ou Email', 'active-texto' ) ); ?>';

	// Error Messages
	if ( document.getElementById('login_error') )
		document.getElementById('login_error').innerHTML = document.getElementById('login_error').innerHTML.replace( '<?php echo esc_js( __( 'identifiant' ) ); ?>', '<?php echo esc_js( __( 'Téléphone ou Email' , 'active-texto' ) ); ?>' );
	</script><?php
}
add_action( 'login_form', 'username_or_email_login' );
        

function at_set_page_title(){
	@session_start();
	if(isset($_GET['register'])){$_SESSION['at_dashboard_page_title'] = __('Enrégistrer un domaine', 'active-texto'); }
	else if(isset($_GET['signup'])){$_SESSION['at_dashboard_page_title'] = __('Inscription', 'active-texto'); }
	else if(isset($_GET['contacts'])){$_SESSION['at_dashboard_page_title'] = __('Contacts', 'active_texto');}
	else if(isset($_GET['doamains'])){$_SESSION['at_dashboard_page_title'] = __('Historique', 'active-texto');}
	else if(isset($_GET['groups'])){$_SESSION['at_dashboard_page_title'] = __('Groupes', 'active-texto');}
	else if(isset($_GET['reports'])){$_SESSION['at_dashboard_page_title'] = __('Rapports', 'active-texto');}
	else if(isset($_GET['scheduled'])){$_SESSION['at_dashboard_page_title'] = __('Envois différés', 'active-texto');}
	else{$_SESSION['at_dashboard_page_title'] = __("Vos noms de domaines",'active-texto');}
}

function manager_shortcode() {
	if(isset($_GET['register'])){ return at_send_sms_display_widget();}
	else if(isset($_GET['signup'])){ return at_signup_form_display_widget();}
	else if(isset($_GET['contacts'])){return at_contacts_display_widget();}
	else if(isset($_GET['domains'])){return at_history_display_widget();}
	else if(isset($_GET['groups'])){return at_groups_display_widget();}
	else if(isset($_GET['group-details'])){return at_groups_details_display_widget();}
	else if(isset($_GET['reports'])){ return at_reports_display_widget();}
	else if(isset($_GET['scheduled'])){return at_scheduled_display_widget();}
	else{return at_dashboard_display_widget();}
        
}
/**/


add_filter( 'cron_schedules', 'at_add_cron_intervals' );
 
function at_add_cron_intervals( $schedules ) {
   $schedules['1minute'] = array(
      'interval' => 5,
      'display' => __('Every Minute') // Easy to read display name
   );
   return $schedules; // Do not forget to give back the list of schedules!
}
 
add_action( 'at_cron_hook', 'at_cron_exec' );
 
if( !wp_next_scheduled( 'at_cron_hook' ) ) {
   wp_schedule_event( time(), '1minute', 'at_cron_hook' );
}
 
function at_cron_exec() {
   at_send_scheduled_sms();
}



add_action( 'init', 'at_disable_admin_bar' , 9 );
add_action( 'init', 'at_set_user_lang');
add_action( 'get_header', 'at_force_user_login');
add_action( 'get_header', 'at_set_page_title');
add_action( 'login_enqueue_scripts', 'at_login_css_tweaks' );
add_action( 'init', 'at_language_init');
add_action( 'widgets_init', 'at_ads_widgets_init' );
add_filter( 'login_headerurl', 'at_login_logo_url' );
add_filter( 'login_headertitle', 'at_login_logo_url_title' );

//$nic_options = new NicOptions();


