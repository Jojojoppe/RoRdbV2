<?php

// TODO check for permission to view this page

// check if we must do an action
if(isset($_POST['rordb_name'])){
    $name = $_POST['rordb_name'];
    $cat = $_POST['rordb_cat'];
    $loc = $_POST['rordb_loc'];
    $color = $_POST['rordb_color'];
    $amount = $_POST['rordb_amount'];
    $size = $_POST['rordb_size'];
    $comments = $_POST['rordb_comments'];
    $img = $_POST['rordb_img'];

    // Add image to database
    $imgid = rordbv2_wpdb_add_img($img, $name);
    // Add item to database
    rordbv2_wpdb_add_item($name, $cat, $loc, $color, $amount, $size, $comments, $imgid);
}

// Get categories and locations
$cats = rordbv2_wpdb_get_hierarchical("cat");
$locs = rordbv2_wpdb_get_hierarchical("loc");

// Load image compression and conversion script
wp_enqueue_script('rordbv2_public_items_js', plugin_dir_url(__FILE__)."../resources/js/settings_fields.js", array(), null, true);

?>

<h4>Create item</h4>
<form action='' method='post'>
    Name: <input type='text' name='rordb_name' /> </br>

    Category: <select name='rordb_cat'>
    <?php
        $lvl = -1;
        foreach($cats as $el){
            $lvl = $el["level"];
            $lvlstr = str_repeat('|', $lvl-1).'+ ';
            echo "<option value='".$el["element"]->id."'";
            echo ">".$lvlstr.$el["element"]->name."</option>";
        }
    ?>
    </select><br>

    Location: <select name='rordb_loc'>
    <?php
        $lvl = -1;
        foreach($locs as $el){
            $lvl = $el["level"];
            $lvlstr = str_repeat('|', $lvl-1).'+ ';
            echo "<option value='".$el["element"]->id."'";
            echo ">".$lvlstr.$el["element"]->name."</option>";
        }
    ?>
    </select><br>

    <!-- TODO add claimed by here? -->
    <!-- TODO add hidden here? -->

    Color: <input type='text' name='rordb_color' /> </br>
    Amount: <input type='text' name='rordb_amount' /> </br>
    Size: <input type='text' name='rordb_size' /> </br>
    Comments: <textarea name='rordb_comments'></textarea> </br>

    <input type='hidden' name='rordb_img' id='rordb_img'>
    <img id='rordb_imgview' width='200'><br>
    <input type='file' accept='image/*' id='rordb_imgfile' onchange='javascript:rordbv2_put_imgcontent_in_img("rordb_imgfile", "rordb_imgview", "rordb_img")'></br>

    <input type='submit' value='Create item' />
</form>
