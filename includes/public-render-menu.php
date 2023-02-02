<?php

function rordbv2_render_menu_addhierarchical($elements, $catloc){
    $ret = "";

    // check for create permission, if not just return empty!
    if($catloc=="cat" && !current_user_can('rordbv2_edit_categories')){
        return $ret;
    }
    if($catloc=="loc" && !current_user_can('rordbv2_edit_locations')){
        return $ret;
    }

    // Check if clicked on add
    if(isset($_POST["rordb_create_".$catloc])){
        $name = $_POST["rordb_create_".$catloc];
        $parentid = $_POST["rordb_create_parent"];
        rordbv2_wpdb_add_hierarchical($name, $parentid, $catloc);
        // TODO view message it is added
    }

    $ret .= "<form action='' method='post'><input type='text' name='rordb_create_".$catloc."'/>";
    $ret .= "<select name='rordb_create_parent'>";
    $lvl = -1;
    foreach($elements as $el){
        $lvl = $el["level"];
        $lvlstr = str_repeat('--', $lvl-1).'+ ';
        $ret .= "<option value='".$el["element"]->id."'>".$lvlstr.$el["element"]->name."</option>";
    }
    $typename = $catloc=="cat" ? "category" : "location";
    $ret .= "</select><br><input type='submit' value='Add ".$typename."'></form>\n";

    return $ret;
}

function rordbv2_render_menu(){
    $ret="";

    $pageid = "";
    if(isset($_GET['page_id'])) $pageid = $_GET['page_id'];

    $cats = rordbv2_wpdb_get_hierarchical("cat");
    $locs = rordbv2_wpdb_get_hierarchical("loc");

    $ret .= "<hr><h5>Menu:</h5>";
    $ret .= "<a href='?page_id=".$pageid."'>Home</a><br>";
    $ret .= "<a href='?page_id=".$pageid."&rordb_action=rordb_help'>Help</a><br>";
    if(current_user_can('rordbv2_edit_items')) $ret .= "<a href='?page_id=".$pageid."&rordb_action=rordb_add#rordv2_main'>Create item</a><br>";
    $ret .= "<form action='' method='get'><input type='hidden' name='page_id' value='$pageid'><input type='text' name='rordb_search'><input type='submit' value='Search'>";
    if(isset($_GET['rordb_cat'])) $ret .= "<input type='hidden' name='rordb_cat' value='".$_GET['rordb_cat']."'>";
    if(isset($_GET['rordb_loc'])) $ret .= "<input type='hidden' name='rordb_loc' value='".$_GET['rordb_loc']."'>";
    if(isset($_GET['rordb_group'])) $ret .= "<input type='hidden' name='rordb_group' value='".$_GET['rordb_group']."'>";
    $ret .= "</form>";

    if(current_user_can('rordbv2_edit_item') or current_user_can('rordbv2_edit_categories') or current_user_can('rordbv2_edit_locations')) $ret .= "<hr><h5>Management:</h5>";
    // check for permissions for each menu item
    if(current_user_can('rordbv2_edit_categories')) $ret .= rordbv2_render_menu_addhierarchical($cats, "cat");
    if(current_user_can('rordbv2_edit_locations')) $ret .= rordbv2_render_menu_addhierarchical($locs, "loc");


    // Link to main part
    $ret .= "<a href='#rordbv2_main'>Go to content</a><br>";

    // List categories
    // First get full category table (roots and rest
    // this is done instead of querying each entry
    // on its own to not spam the database connection.
    // the table wont be incredibly big so this
    // should not give any problems
    $ret .= "<hr><h5>Categories:</h5>";
    // TODO if added (checked via error/msg sytem) reload elements
    foreach($cats as $el){
        $cat = $el["element"];
        $ret .= "<a href='?page_id=".$pageid."&rordb_cat=".$cat->id."'>".str_repeat("--", $el["level"]-1).'+ '.$cat->name."</a>";
        // check if permission
        if(current_user_can('rordbv2_edit_categories')) $ret .= "  (<a href='?page_id=".$pageid."&rordb_action=rordb_edit&rordb_edit=cat&rordb_id=".$el["element"]->id."'>edit</a>)";
        $ret .= "<br>";
    }

    // List locations
    // exact the same as for categories
    $ret .= "<h5>Locations:</h5>";
    // TODO if added (checked via error/msg sytem) reload elements
    foreach($locs as $el){
        $loc = $el["element"];
        $ret .= "<a href='?page_id=".$pageid."&rordb_loc=".$loc->id."'>".str_repeat("--", $el["level"]-1).'+ '.$loc->name."</a>";
        // check if permission
        if(current_user_can('rordbv2_edit_locations')) $ret .= "  (<a href='?page_id=".$pageid."&rordb_action=rordb_edit&rordb_edit=loc&rordb_id=".$el["element"]->id."'>edit</a>)";
        $ret .= "<br>";
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
