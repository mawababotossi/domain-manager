<?php

add_filter( 'woocommerce_return_to_shop_redirect', 'dm_change_empty_cart_button_url' );
function dm_change_empty_cart_button_url() {
	return get_site_url()."/search";
	//Can use any page instead, like return '/sample-page/';
}

add_action( 'woocommerce_proceed_to_checkout', 'insert_search_domain_button' );
function insert_search_domain_button() {
    // Echo our Search domain button
    echo '<input type="submit" class="button" name="add_domain" value="Ajouter un domain" />';
}

add_filter('gettext', 'translate_reply');
add_filter('ngettext', 'translate_reply');

function translate_reply($translated) {
   $translated = str_ireplace('Quantity', 'Duration/years', $translated);
   return $translated;
}

//function my_custom_add_to_cart_redirect( $url ) {
//    $url = get_permalink( 1 ); // URL to redirect to (1 is the page ID here)
//    return $url;
//}
//add_filter( 'woocommerce_add_to_cart_redirect', 'my_custom_add_to_cart_redirect' );


