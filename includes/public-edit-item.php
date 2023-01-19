<?php

// TODO check for permission to view this page

if(!isset($_GET['rordb_edit']) or !isset($_GET['rordb_id']) or $_GET['rordb_edit']!="item"){
    echo "ERROR: edit type or ID not specified or edit type not 'item'";
    return;
}

$pageid = "";
if(isset($_GET['page_id'])) $pageid = $_GET['page_id'];

// Check if we must do an action
if(isset($_POST['rordb_name'])){
    $name = $_POST['rordb_name'];
    $cat = $_POST['rordb_cat'];
    $loc = $_POST['rordb_loc'];
    $color = $_POST['rordb_color'];
    $amount = $_POST['rordb_amount'];
    $size = $_POST['rordb_size'];
    $comments = $_POST['rordb_comments'];
    $imgdata = $_POST['rordb_img'];
    $imgid = $_POST['rordb_imgid'];
    $id = $_GET['rordb_id'];

    rordbv2_wpdb_edit_item($id, $name, $cat, $loc, $color, $amount, $size, $comments, $imgdata, $imgid);
}

// Get info from the database
$item = rordbv2_wpdb_get_items("IT.id=".$_GET['rordb_id'])[0];

// Get categories and locations
$cats = rordbv2_wpdb_get_hierarchical("cat");
$locs = rordbv2_wpdb_get_hierarchical("loc");

// Load image compression and conversion script
wp_enqueue_script('rordbv2_public_items_js', plugin_dir_url(__FILE__)."../resources/js/settings_fields.js", array(), null, true);

?>

<h4>Edit item</h4>
<form action='' method='post'>
    Name: <input type='text' name='rordb_name' value='<?php echo $item->name; ?>'/> </br>

    Category: <select name='rordb_cat'>
    <?php
        $lvl = -1;
        foreach($cats as $el){
            $lvl = $el["level"];
            $lvlstr = str_repeat('|', $lvl-1).'+ ';
            echo "<option value='".$el["element"]->id."'";
            if($el["element"]->id==$item->catid) echo " selected";
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
            if($el["element"]->id==$item->locid) echo " selected";
            echo ">".$lvlstr.$el["element"]->name."</option>";
        }
    ?>
    </select><br>

    <!-- TODO add claimed by here? -->
    <!-- TODO add hidden here? -->

    Color: <input type='text' name='rordb_color'  value='<?php echo $item->color; ?>'/> </br>
    Amount: <input type='text' name='rordb_amount' value='<?php echo $item->amount; ?>'/> </br>
    Size: <input type='text' name='rordb_size' value='<?php echo $item->size; ?>'/> </br>
    Comments: <textarea name='rordb_comments'><?php echo $item->comments; ?></textarea> </br>

    <input type='hidden' name='rordb_img' id='rordb_img' value='<?php echo $item->imgdata; ?>'>
    <input type='hidden' name='rordb_imgid' value='<?php echo $item->imgid; ?>'>
    <img id='rordb_imgview' width='200' src='<?php echo $item->imgdata; ?>'><br>
    <input type='file' accept='image/*' id='rordb_imgfile' onchange='javascript:rordbv2_put_imgcontent_in_img("rordb_imgfile", "rordb_imgview", "rordb_img")'></br>

    <input type='submit' value='Edit item' />
</form>
