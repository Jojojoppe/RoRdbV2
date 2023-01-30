<?php

// Initialize user stuff
function rordbv2_users_init(){
    // Create the RoRdb maintainer role
    wp_roles()->add_role("rordbv2", "RoRdb maintainer", [
        "rordbv2_edit_items"=>true,
        "rordbv2_edit_categories"=>true,
        "rordbv2_edit_locations"=>true,
        "rordbv2_claim"=>true,
    ]);

    wp_roles()->add_role("rordbv2_viewer", "RoRdb user", [
        "rordbv2_edit_items"=>true,
        "rordbv2_edit_categories"=>false,
        "rordbv2_edit_locations"=>false,
        "rordbv2_claim"=>true,
    ]);

    // Add capabilities to administrator
    $admin = wp_roles()->get_role("administrator");

    $admin->add_cap("rordbv2_edit_items", true);
    $admin->add_cap("rordbv2_edit_categories", true);
    $admin->add_cap("rordbv2_edit_locations", true);
    $admin->add_cap("rordbv2_claim", true);
}

// Deinitialize user stuff
function rordbv2_users_deinit(){
    // Remove added capabilities
    $admin = wp_roles()->get_role("administrator");
    $admin->remove_cap("rordbv2_edit_items");
    $admin->remove_cap("rordbv2_edit_categories");
    $admin->remove_cap("rordbv2_edit_locations");
    $admin->remove_cap("rordbv2_claim");
    wp_roles()->remove_role("rordbv2");
    wp_roles()->remove_role("rordbv2_viewer");
}

// Register cap groups with the Members plugin (if used)
function rordbv2_register_cap_groups() {
	members_register_cap_group(
		'rordbv2',
		array(
			'label'    => __( 'RoRdb', 'rordbv2-textdomain' ),
			'caps'     => array(
                "rordbv2_edit_items",
                "rordbv2_edit_categories",
                "rordbv2_edit_locations",
                "rordbv2_claim",
            ),
			'icon'     => 'dashicons-database',
			'priority' => 10
		)
	);
}
add_action( 'members_register_cap_groups', 'rordbv2_register_cap_groups' );
