<?php


if(!isset($_GET['rordb_edit']) or !isset($_GET['rordb_id']) or ($_GET['rordb_edit']!="cat" and $_GET['rordb_edit']!="loc")){
    echo "ERROR: edit type or ID not specified or edit type not 'cat' or 'loc'";
    return;
}

// check for permission to view this page
if($_GET['rordb_edit']=="cat" && !current_user_can('rordbv2_edit_categories')){
    echo "You cannot edit or add categories";
    return;
}
if($_GET['rordb_edit']=="loc" && !current_user_can('rordbv2_edit_locations')){
    echo "You cannot edit or add locations";
    return;
}

$header_type = $_GET['rordb_edit']=="cat" ? "category" : "location";
$pageid = "";
if(isset($_GET['page_id'])) $pageid = $_GET['page_id'];



// get info from database
$info = rordbv2_wpdb_get_hierarchical_single($_GET['rordb_edit'], $_GET["rordb_id"]);
if($info==[]){
    echo "Unknown $header_type specified...";
    return;
}
$info = $info[0];

// Check if we must do an action
if(isset($_POST['rordb_id'])){
    if(isset($_POST['rordb_delete'])){
        // EXECUTE DELETE
        rordbv2_wpdb_delete_hierarchical($_GET['rordb_edit'], $_POST['rordb_id'], $info->name);
    }else{
        // UPDATE
        rordbv2_wpdb_update_hierarchical($_GET['rordb_edit'], $_POST['rordb_id'], $info->name, $_POST['rordb_name'], $_POST['rordb_parent']);
    }

    echo "Updated $header_type";
    return;
}

// Get full list
$list = rordbv2_wpdb_get_hierarchical($_GET['rordb_edit']);

?>

<h4>Edit <?php echo $header_type; ?></h4>
<form action='' method='post'>
    <input type='hidden' name='rordb_id' value='<?php echo $info->id; ?>'/>
    Name: <input type='text' name='rordb_name' value='<?php echo $info->name; ?>' /><br>
    Parent: <select name='rordb_parent'>
    <?php
        $lvl = -1;
        foreach($list as $el){
            if($el["element"]->id==$info->id) continue;
            $lvl = $el["level"];
            $lvlstr = str_repeat('--', $lvl-1).'+ ';
            echo "<option value='".$el["element"]->id."'";
            if($el["element"]->id==$info->parentid) echo " selected";
            echo ">".$lvlstr.$el["element"]->name."</option>";
        }
    ?>
    </select><br>
    delete: <input type='checkbox' name='rordb_delete'><br>
    <input type='submit' value='Edit <?php echo $header_type; ?>'>
</form>
