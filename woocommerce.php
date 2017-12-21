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

add_action( 'woocommerce_checkout_order_processed', 'action_woocommerce_new_order',  1, 1  ); //Or add_action( 'woocommerce_new_order', 'my_status_pending',  1, 1  );
function action_woocommerce_new_order($order_id){ 
    // Add domains to user account
    $order = wc_get_order($order_id);

   // Iterating through each WC_Order_Item_Product objects
   foreach ($order->get_items() as $item_key => $item_values){

       // Item ID is directly accessible from the $item_key in the foreach loop or
       $item_id = $item_values->get_id();

       ## Using WC_Order_Item_Product methods ##

       $item_name = $item_values->get_name(); // Name of the product
       $item_type = $item_values->get_type(); // Type of the order item ("line_item")

       $product_id = $item_values->get_product_id(); // the Product id
       $wc_product = $item_values->get_product(); // the WC_Product object
       ## Access Order Items data properties (in an array of values) ##
       $item_data = $item_values->get_data();

       $product_name = $item_data['name'];
       $product_id = $item_data['product_id'];
       $variation_id = $item_data['variation_id'];
       $quantity = $item_data['quantity'];
       $tax_class = $item_data['tax_class'];
       $line_subtotal = $item_data['subtotal'];
       $line_subtotal_tax = $item_data['subtotal_tax'];
       $line_total = $item_data['total'];
       $line_total_tax = $item_data['total_tax'];

       global $wpdb;
       $current_user = wp_get_current_user();

       $domain_validity = $quantity * 365;

      $R = $wpdb->insert('nic_domains', 
	array( 	'domain_owner' => $current_user->ID, 
		'domain_name' => $product_name,
		'domain_creation_date' => date("Y-m-d H:i:s"),
		//'domain_expiry_date' => date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + ".$domain_validity." day")), //expiry date should be added once payment is made
		'active' => 0
        )); 

   }
}


add_action( 'woocommerce_order_status_changed', 'action_woocommerce_order_status_changed', 99, 3 );
function action_woocommerce_order_status_changed( $order_id, $old_status, $new_status ){
    if( $new_status == "completed" ) {

     $order = wc_get_order($order_id);

     foreach ($order->get_items() as $item_key => $item_values){
	       
	       //Change domains staus

	       $item_id = $item_values->get_id();
	       $item_data = $item_values->get_data();

	       $product_name = $item_data['name'];
               $quantity = $item_data['quantity'];

               $domain_validity = $quantity * 365;
	 
	       global $wpdb;

               $expiry_date = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + ".$domain_validity." day"));

	       $R = $wpdb->get_results( "update nic_domains set active = 1, domain_expiry_date = '". $expiry_date ."' where domain_name = '".$product_name."'" ); 
               //exit( var_dump( $wpdb->last_query ) );
	}

    }
}

/**
 * @snippet       WooCommerce add text to the thank you page
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=382
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 2.6.7
 */
 
add_action( 'woocommerce_thankyou', 'action_add_content_thankyou' );
 
function action_add_content_thankyou() {
   echo '<p><a href="'.get_site_url().'/search" class="btn btn-default" >Ajouter un nom de domaine</a> <a href="'.get_site_url().'/app" class="btn btn-primary" >Voir mes domaines</a></p>';
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


