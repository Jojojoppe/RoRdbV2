<?php

if(!isset($_GET['rordb_edit']) or !isset($_GET['rordb_id']) or $_GET['rordb_edit']!="item"){
    echo "ERROR: edit type or ID not specified or edit type not 'item'";
    return;
}

// check for permission to view this page
if(!current_user_can('rordbv2_edit_items')){
    echo "You cannot edit or add items";
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

    //echo $imgid;
    //echo $imgdata;
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
<div role="form" class="wpcf7"><form action='' method='post' class="wpcf7">

    <p><label>Name:<br></label><span class='wpcf7-form-control-wrap'>
        <input type='text' name='rordb_name' class="wpcf7-form-control wpcf7-text" value='<?php echo $item->name; ?>'/></span></p>

    <p><label>Category:<br></label><span class='wpcf7-form-control-wrap'><select name='rordb_cat'>
    <?php
        $lvl = -1;
        foreach($cats as $el){
            $lvl = $el["level"];
            $lvlstr = str_repeat('--', $lvl-1).'+ ';
            echo "<option value='".$el["element"]->id."'";
            if($el["element"]->id==$item->catid) echo " selected";
            echo ">".$lvlstr.$el["element"]->name."</option>";
        }
    ?>
    </select></span></p>

    <p><label>Location:<br></label><span class='wpcf7-form-control-wrap'><select name='rordb_loc'>
    <?php
        $lvl = -1;
        foreach($locs as $el){
            $lvl = $el["level"];
            $lvlstr = str_repeat('--', $lvl-1).'+ ';
            echo "<option value='".$el["element"]->id."'";
            if($el["element"]->id==$item->locid) echo " selected";
            echo ">".$lvlstr.$el["element"]->name."</option>";
        }
    ?>
    </select></span></p>

    <!-- TODO add claimed by here? -->
    <!-- TODO add hidden here? -->

    <p><label>Color:<br></label><span class="wpcf7-form-control wpcf7-text"><input type='text' name='rordb_color' value='<?php echo $item->color; ?>'/></span></p>
    <p><label>Amount:<br></label><span class="wpcf7-form-control wpcf7-text"><input type='text' name='rordb_amount' value='<?php echo $item->amount; ?>'/></span></p>
    <p><label>Size:<br></label><span class="wpcf7-form-control wpcf7-text"><input type='text' name='rordb_size' value='<?php echo $item->size; ?>'/></span></p>
    <p><label>Comments:<br></label><span class="wpcf7-form-control wpcf7-text"><textarea name='rordb_comments'><?php echo $item->comments; ?></textarea></span></p>

    <input type='hidden' name='rordb_img' id='rordb_img' value='<?php echo $item->imgdata; ?>'>
    <input type='hidden' name='rordb_imgid' value='<?php echo $item->imgid; ?>'>
    <img id='rordb_imgview' width='200' src='<?php echo $item->imgdata; ?>'><br>
    <input type='file' accept='image/*' id='rordb_imgfile' oninput='javascript:rordbv2_put_imgcontent_in_img("rordb_imgfile", "rordb_imgview", "rordb_img")'>

    <p class="submit"><input type='submit' value='Edit item' class="button button-primary"/></p>

</form></div>
