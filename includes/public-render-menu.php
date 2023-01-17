<?php

function rordbv2_render_menu(){
    $ret="";

    global $wpdb;
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $table_categories = $wpdb->prefix."rordbv2_cat";
    $table_locations = $wpdb->prefix."rordbv2_loc";

    $pageid = "";
    if(isset($_GET['page_id'])) $pageid = $_GET['page_id'];

    // TODO create search

    // List categories
    // First get full category table (roots and rest
    // this is done instead of querying each entry
    // on its own to not spam the database connection.
    // the table wont be incredibly big so this
    // should not give any problems
    $ret .= "<h5>Categories:</h5>";
    // TODO create add button
    $cat_roots = $wpdb->get_results("SELECT * from $table_categories WHERE parentid IS NULL");
    $cat_rest = $wpdb->get_results("SELECT * from $table_categories WHERE parentid IS NOT NULL");
    //$ret .= json_encode($cat_roots);
    //$ret .= json_encode($cat_rest);

    $catstack = $cat_roots;
    while(count($catstack)>0){
        $cat = array_pop($catstack);
        $ret .= "<a href='?page_id=".$pageid."&rordb_cat=".$cat->id."'>".$cat->name."</a><br>";
        // TODO create edit button

        // Search for child
        foreach($cat_rest as $c){
            if($c->parentid==$cat->id){
                array_push($catstack, $c);
            }
        }
    }

    // List locations
    // exact the same as for categories
    $ret .= "<h5>Locations:</h5>";
    // TODO create add button
    $loc_roots = $wpdb->get_results("SELECT * from $table_locations WHERE parentid IS NULL");
    $loc_rest = $wpdb->get_results("SELECT * from $table_locations WHERE parentid IS NOT NULL");

    $locstack = $loc_roots;
    while(count($locstack)>0){
        $loc = array_pop($locstack);
        $ret .= "<a href='?page_id=".$pageid."&rordb_loc=".$loc->id."'>".$loc->name."</a><br>";
        // TODO create edit button

        // Search for child
        foreach($loc_rest as $l){
            if($l->parentid==$loc->id){
                array_push($locstack, $l);
            }
        }
    }

    // List claim groups
    // Get the wp option and create an array from it
    $ret .= "<h5>Claimed by:</h5>";
    $groups = explode(',', get_option('rordbv2_claimgroups'));
    foreach($groups as $g){
        $ret .= "<a href='?page_id=".$pageid."&rordb_group=".$g."'>".$g."</a><br>";
    }

    return $ret;
}
