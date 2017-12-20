<?php

add_action( 'admin_menu', 'dm_admin_menu' );

function dm_admin_menu() {
	add_menu_page( 'Domain Manager', 'Domain Manager', 'manage_options', 'domain-manager/admin-page.php', 'show_dm_admin_page', 'dashicons-tickets', 6  );
}

function show_dm_admin_page(){
        echo "<h1>Hello World!</h1>";
}
