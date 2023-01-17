<?php

define("RORDBV2_DBVERSION", "1");

// Install RoRdb in the wordpress database
// This function is ran in the activation hook
// It checks if it is already installed and calls an update function
// if it is installed but does not have the right dababase version
// if it is not installed it creates the tables and fills them with
// initial data
function rordbv2_wpdb_install(){
    global $wpdb;
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');

    $table_categories = $wpdb->prefix."rordbv2_cat";
    $table_locations = $wpdb->prefix."rordbv2_loc";
    $table_items = $wpdb->prefix."rordbv2_items";
    $table_images = $wpdb->prefix."rordbv2_img";
    $table_settings = $wpdb->prefix."rordbv2";

    // Check if main table exists, if not run full install
    // if it is there check version and if no match run update
    // if it is there and version matches just return

    $live_version = $wpdb->get_var("SELECT text FROM $table_settings WHERE (name='version')");
    if($live_version!=null){
        if($live_version!=RORDBV2_DBVERSION){
            // TODO update DB to current version
        }
        return;
    }else{
        // Not yet in wpdb, add the tables
        $charset_collate = $wpdb->get_charset_collate();

        // settings table (ID, date, name, text)
        $sql = "CREATE TABLE $table_settings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            text text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);
        // Add basic fields
        $wpdb->insert($table_settings, ["name"=>"version", "text"=>RORDBV2_DBVERSION]);
        $wpdb->insert($table_settings, ["name"=>"groups", "text"=>"['none', 'Toneel', 'Musical', 'TheatreLab', 'InterNEST', 'Other']"]);

        // images table (ID, date, name, data)
        $sql = "CREATE TABLE $table_images (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            data text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // categories/locations table (ID, date, name, parentid, parentid_list, childid_list, searchtags)
        $sql = " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            parentid mediumint(9),
            parentid_list tinytext,
            childid_list tinytext,
            searchtags tinytext NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta("CREATE TABLE ".$table_categories.$sql);
        dbDelta("CREATE TABLE ".$table_locations.$sql);
        $wpdb->insert($table_categories, ["name"=>"All", "searchtags"=>"All"]);
        $wpdb->insert($table_categories, ["name"=>"None", "searchtags"=>"None"]);
        $wpdb->insert($table_locations, ["name"=>"All", "searchtags"=>"All"]);
        $wpdb->insert($table_locations, ["name"=>"None", "searchtags"=>"None"]);

        // items table (ID, date, name, category, location, )
        $sql = "CREATE TABLE $table_items (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            category mediumint(9) NOT NULL,
            location mediumint(9) NOT NULL,
            claimedby mediumint(9) NOT NULL,
            hidden mediumint(1) NOT NULL,
            img mediumint(9) NOT NULL,
            color tinytext,
            amount tinytext,
            size tinytext,
            comments text,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);
    }
}

// Fetch the claim groups from the database
// returns array with claim group names
function rordbv2_wpdb_get_claimgroups(){
    global $wpdb;
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $table_settings = $wpdb->prefix."rordbv2";

    $cgroups_string = $wpdb->get_var("SELECT text FROM $table_settings WHERE (name='groups')");
    return json_decode($cgroups_string);
}

// Update the claim groups in the database
// expects the claim group names to be in an array
function rordbv2_wpdb_set_claimgroups($cgroups){
    global $wpdb;
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $table_settings = $wpdb->prefix."rordbv2";
    $wpdb->update($table_settings, ['text'=>json_encode($cgroups)], ['name'=>'groups']);
}

// Add a location/category to the database
// expects a category name, the ID of the parent (null if top level) and the table name
// NOTE: table name IS NOT checked so must be in form of WPPREFIX_loc or WPPREFIX_cat
function rordbv2_wpdb_add_hierarchical($name, $parentID, $table){
    global $wpdb;
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');

    // Traverse full tree to get list of parents
    // (this is done to fill in the parentid_list field and add the new
    // loc/cat in the childid_list of one of the parents
    // note: using a counter with max 500 to break out of dependency loops
    // this means that the tree cannot be deeper than 500!
    // TODO create a global setting for this?
    $full_parent_tree = [];
    $tovisit = $parentID;
    $counter = 0;
    while($tovisit!=null and counter<500){
        $counter++;
        array_push($full_parent_tree, $tovisit);
        $tovisit = $wpdb->get_var("SELECT parentid FROM $table WHERE (id=$tovisit)");
    }

    // Add new loc/cat
    if($parentID!=null){
        $searchtags = join(', ', $full_parent_tree).", ".$name;
    }else{
        $searchtags = $name;
    }
    $wpdb->insert($table, ["name"=>$name, "parentid"=>$parentID, "parentid_list"=>join(',', $full_parent_tree)]);

    // update child lists of parent tree
    foreach($full_parent_tree as $p){
        $wpdb->query("UPDATE $table SET childid_list=childid_list+', '+$name WHERE id=$p");
    }
}
