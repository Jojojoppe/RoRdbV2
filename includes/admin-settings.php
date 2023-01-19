<?php

function rordbv2_admin_menu(){
    add_menu_page(
        "RoRdb settings", 				// page title
        "RoRdb",      	 				// menu title
        "read", 					// capability
        "rordbv2", 					// menu slug
        "rordbv2_options_page_html",		        // callable
        file_get_contents(plugin_dir_path(__FILE__)."../resources/images/nest_icon.wpico") // icon url
        // position
    );
}
add_action('admin_menu', 'rordbv2_admin_menu');

function rordbv2_options_page_html(){

    // Do settings update actions
    if(isset($_GET['settings-updated'])){
        // Update claimgroups
        rordbv2_wpdb_set_claimgroups(explode(',', get_option('rordbv2_claimgroups'))); 
    }

    // Check if settings are updated
    if(isset($_GET['settings-updated'])){
        add_settings_error('rordbv2_messages', 'rordbv2_message',
            __('Settings saved', 'rordb'), 'updated');
    }

    // Show error/update messages
    settings_errors('rordbv2_messages');

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
                <?php
                settings_fields('rordbv2');
                do_settings_sections('rordbv2');
                submit_button('Save settings');
                ?>
        </form>
    </div>
    <?php
}
