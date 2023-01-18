<?php

function rordbv2_render_menu_addhierarchical($elements, $catloc){
    $ret = "";

    // TODO check for create permission, if not just return empty!

    // Check if clicked on add
    if(isset($_POST["rordb_create_".$catloc])){
        $name = $_POST["rordb_create_".$catloc];
        $parentid = $_POST["rordb_create_parent"];
        rordbv2_wpdb_add_hierarchical($name, $parentid, $catloc);
        // TODO view message it is added
    }

    $ret .= "\n<form action='' method='post'><input type='text' name='rordb_create_".$catloc."'/>";
    $ret .= "<select name='rordb_create_parent'>";
    $lvl = -1;
    foreach($elements as $el){
        $lvl = $el["level"];
        $lvlstr = str_repeat('|', $lvl-1).'+ ';
        $ret .= "<option value='".$el["element"]->id."'>".$lvlstr.$el["element"]->name."</option>";
    }
    $typename = $catloc=="cat" ? "category" : "location";
    $ret .= "</select><br><input type='submit' value='Add ".$typename."'></form><br>\n";

    return $ret;
}

function rordbv2_render_menu(){
    $ret="";

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
    $cats = rordbv2_wpdb_get_hierarchical("cat");
    $ret .= rordbv2_render_menu_addhierarchical($cats, "cat");
    // TODO if added (checked via error/msg sytem) reload elements
    foreach($cats as $el){
        $cat = $el["element"];
        $ret .= "<a href='?page_id=".$pageid."&rordb_cat=".$cat->id."'>".str_repeat("|", $el["level"]-1).'+ '.$cat->name."</a><br>";
        // TODO create edit button
    }

    // List locations
    // exact the same as for categories
    $ret .= "<h5>Locations:</h5>";
    $locs = rordbv2_wpdb_get_hierarchical("loc");
    $ret .= rordbv2_render_menu_addhierarchical($locs, "loc");
    // TODO if added (checked via error/msg sytem) reload elements
    foreach($locs as $el){
        $loc = $el["element"];
        $ret .= "<a href='?page_id=".$pageid."&rordb_loc=".$loc->id."'>".str_repeat("|", $el["level"]-1).'+ '.$loc->name."</a><br>";
        // TODO create edit button
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
