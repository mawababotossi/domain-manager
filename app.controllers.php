<?php

require_once(dirname(__FILE__).'/lib/whois.php');

add_action( 'wp_ajax_get_sidebar', 'at_get_sidebar' );
add_action( 'wp_ajax_get_groups', 'at_get_groups' );
add_action( 'wp_ajax_delete_groups', 'at_delete_groups' );
add_action( 'wp_ajax_get_history', 'at_get_history' );
add_action( 'wp_ajax_get_scheduled', 'at_get_scheduled' );
add_action( 'wp_ajax_delete_scheduled', 'at_delete_scheduled' );
add_action( 'wp_ajax_get_user_data', 'at_get_user_data');

add_action( 'wp_ajax_nopriv_add_domain_to_cart', 'at_add_domain_to_cart');
add_action( 'wp_ajax_add_domain_to_cart', 'at_add_domain_to_cart');

add_action( 'wp_ajax_delete_domain_from_cart', 'at_delete_domain_from_cart');
add_action( 'wp_ajax_nopriv_delete_domain_from_cart', 'at_delete_domain_from_cart');

add_action( 'wp_ajax_get_cart_data', 'at_get_cart_data');
add_action( 'wp_ajax_nopriv_get_cart_data', 'at_get_cart_data');

add_action( 'wp_ajax_save_command', 'at_save_command');

add_action( 'wp_ajax_nopriv_get_whois', 'at_get_whois');
add_action( 'wp_ajax_get_whois', 'at_get_whois');

add_action( 'wp_ajax_get_message_price', 'at_get_message_price');
add_action( 'wp_ajax_nopriv_get_country_price', 'at_get_country_price');
add_action( 'wp_ajax_get_country_price', 'at_get_country_price');
add_action( 'wp_ajax_submit_task', 'at_submit_task');
add_action( 'wp_ajax_get_month_sent_data', 'at_get_month_sent_data');
add_action( 'wp_ajax_nopriv_register_user', 'at_registration_form_exec' );


function at_wp_insert_user() {
	$user_login = $_POST['userPhone'];
	$_SESSION['user_login'] = $_POST['userPhone'];
	
	$user_data = array(
		'ID' => '',
		'user_pass' => $_SESSION['userPass'],
		'user_login' => $user_login,
		'user_nicename' => $_POST['userFname'],
		'user_url' => '',
		'user_email' => $_POST['userMail'],
		'display_name' => $_POST['userFname'],
		'atkname' => $_POST['userFname'],
		'first_name' => $_POST['userFname'],
		'last_name' => $_POST['userSname'],
		'user_registered' => date("Y-m-d H:i:s"),
		'role' => get_option('default_role')
	);
	$user_id = wp_insert_user( $user_data );
	return $user_id;
}

function at_add_user_metas($at_user_id){
	$at_initial_credit = 0.25;
	add_user_meta( $at_user_id, 'user_company', $_POST['company'], true );
	add_user_meta( $at_user_id, 'user_phone', $_POST['userPhone'], true );
	add_user_meta( $at_user_id, 'user_country', $_POST['Country'], true );
	add_user_meta( $at_user_id, 'user_lang', WPLANG, true );
	add_user_meta( $at_user_id, 'user_country_phone_prefix', $_POST['CountryPhonePrefix'], true );
	add_user_meta( $at_user_id, 'user_balance', $at_initial_credit, true );
}

function at_get_user_data(){
    global $wpdb;
    $current_user = wp_get_current_user();
    $userPhone = $current_user->user_login;
    $userFname = $current_user->user_firstname;
    $userSname = $current_user->user_lastname;
    $at_phone_prefix = get_user_meta($current_user->ID,'user_country_phone_prefix',true);
    $user_country_phone_prefix = is_numeric($at_phone_prefix)? $at_phone_prefix : '228';

    $userSenderIDs = array();
    array_push($userSenderIDs, $current_user->user_login); //userPhone for most users
    array_push($userSenderIDs, wd_remove_accents(get_user_meta($current_user->ID,'last_name',true)));
    array_push($userSenderIDs, wd_remove_accents(get_user_meta($current_user->ID,'first_name',true)));
    array_push($userSenderIDs, wd_remove_accents(get_user_meta($current_user->ID,'user_company',true)));
    $userSenderIDs = array_merge($userSenderIDs,
	explode(',',get_user_meta($current_user->ID,'senderIDs',true)));
    $senderID0ptions = array();
    if(is_array($userSenderIDs)) 
	foreach($userSenderIDs as $_senderID){ if(!empty($_senderID)) array_push($senderID0ptions, $_senderID); }
    
    $defaultDate = date("d/m/Y",strtotime("+1 day"));
    $defaultHour = date("H:i");

    $user = (array)$current_user;
    $user['senderIds'] = array_unique($senderID0ptions);
    $user['defaultPhonePrefix'] = $user_country_phone_prefix;
    $user['defaultDate'] = $defaultDate;
    $user['defaultHour'] = $defaultHour;
	echo json_encode( $user);
}

function at_get_whois(){

   $domain = $_REQUEST['domain'];
   $whois = lookupDomain($domain);
   $available = false;
   if(strpos($whois, "No match for") !== FALSE || strpos($whois, "NO OBJECT FOUND!") !== FALSE || strpos($whois, "NOT FOUND") !== FALSE ) $available = true;
   //elseif(strpos($whois, 'Socket Error 0') == 0 ) $available = 'error';
   echo json_encode(array('available' => $available, 'whois' => $whois));

}

function at_add_domain_to_cart(){

   @session_start();

   $domain = $_REQUEST['domain'];

   if(!isset($_SESSION['userCart'])) $_SESSION['userCart'] = array();
   array_push($_SESSION['userCart'], $domain);

   //Add product to woocommerce

   wc_add_product($domain);

   echo json_encode($_SESSION['userCart']);
   //$_SESSION['userCart'] = array();
}

function at_delete_domain_from_cart(){

   @session_start();

   $domain = $_REQUEST['domain'];
   
   $newCart = array();

   if(isset($_SESSION['userCart'])){
     foreach($_SESSION['userCart'] as $key => $_domain){ 
        if($_domain['name'] != $domain ) array_push($newCart, $_domain);
     }

     $_SESSION['userCart'] = $newCart;
   }

   wc_remove_product_from_cart($_domain['name']);


   echo json_encode($_SESSION['userCart']);
}

function at_get_cart_data(){

   @session_start();
   
   $cart = array();
   if(isset($_SESSION['userCart'])) $cart = $_SESSION['userCart']; 
   
   echo json_encode($cart); //$_SESSION['userCart'] = array();  //Reset
}

function at_get_history(){
	global $wpdb;
	$current_user = wp_get_current_user();
	$user_ID = $current_user->ID;
  	
	$M = $wpdb->get_results(sprintf("select * from nic_domains where domain_owner='%s'  order by domain_name desc",
$user_ID), ARRAY_A);

if(current_user_can( 'manage_options' ))

$M = $wpdb->get_results(sprintf("select * from nic_domains order by domain_name desc"), ARRAY_A); 

$N = array();

   foreach($M as $key => $domain){
     
   } /**/

   echo json_encode($M);
}


function wc_add_product($product){
   
$post = array(
    'post_author' => 1, //$user_id
    'post_content' => ' ',
    'post_status' => "publish",
    'post_title' => $product['name'], //$product->part_num
    'post_parent' => '',
    'post_type' => "product",
);

//Create post
$post_id = wp_insert_post( $post, $wp_error ); print_r($wp_error);
if($post_id){
    //$attach_id = get_post_meta($product->parent_id, "_thumbnail_id", true);
    //add_post_meta($post_id, '_thumbnail_id', $attach_id);
}

wp_set_object_terms( $post_id, 'Domains', 'product_cat' );
wp_set_object_terms($post_id, 'simple', 'product_type');

update_post_meta( $post_id, '_visibility', 'visible' );
update_post_meta( $post_id, '_stock_status', 'instock');
update_post_meta( $post_id, 'total_sales', '0');
update_post_meta( $post_id, '_downloadable', 'no');
update_post_meta( $post_id, '_virtual', 'yes');
update_post_meta( $post_id, '_regular_price', "20" );
update_post_meta( $post_id, '_sale_price', "15" );
update_post_meta( $post_id, '_purchase_note', "" );
update_post_meta( $post_id, '_featured', "no" );
update_post_meta( $post_id, '_weight', "" );
update_post_meta( $post_id, '_length', "" );
update_post_meta( $post_id, '_width', "" );
update_post_meta( $post_id, '_height', "" );
update_post_meta( $post_id, '_sku', "");
update_post_meta( $post_id, '_product_attributes', array());
update_post_meta( $post_id, '_sale_price_dates_from', "" );
update_post_meta( $post_id, '_sale_price_dates_to', "" );
update_post_meta( $post_id, '_price', "1" );
update_post_meta( $post_id, '_sold_individually', "" );
update_post_meta( $post_id, '_manage_stock', "no" );
update_post_meta( $post_id, '_backorders', "no" );
update_post_meta( $post_id, '_stock', "" );

WC()->cart->add_to_cart( $post_id );

}


function wc_remove_product_from_cart($name) {
    // Run only in the Cart or Checkout Page
     
    global $woocommerce; 

    foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) {
    $_product =  wc_get_product( $cart_item['data']->get_id());
        if($_product->get_title() == $name) {
          $woocommerce->cart->remove_cart_item($cart_item_key);
        }
    }
}

