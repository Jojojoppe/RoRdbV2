<?php

define("RORDBV2_DBVERSION", "1");

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
