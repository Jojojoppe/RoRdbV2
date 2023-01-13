<?php

function rordbv2_admin_menu(){
	add_menu_page(
		"RoRdb settings", 				// page title
		"RoRdb",      	 				// menu title
		"read", 					// capability
		"rordb2", 					// menu slug
		"rordb2_options_page_html",		        // callable
		file_get_contents(plugin_dir_path(__FILE__)."../resources/images/nest_icon.wpico") // icon url
		// position
	);
}
add_action('admin_menu', 'rordbv2_admin_menu');

function rordbv2_options_page_html(){

}
