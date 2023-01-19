<h4>Home</h4>

<!-- For now just list all items -->
<?php

$pageid = "";
if(isset($_GET['page_id'])) $pageid = $_GET['page_id'];

// Check if we must do an action (claim)
if(isset($_POST['rordb_claim'])){
    // TODO check permission
    rordbv2_wpdb_claim_item($_POST['rordb_id'], $_POST['rordb_claim']);
}

// search for items
$where = "1";
if(isset($_GET['rordb_cat'])) $where .= " AND (CA.parentid_list LIKE '%,".$_GET['rordb_cat'].",%' OR CA.id=".$_GET['rordb_cat'].")";
if(isset($_GET['rordb_loc'])) $where .= " AND (LO.parentid_list LIKE '%,".$_GET['rordb_loc'].",%' OR LO.id=".$_GET['rordb_loc'].")";
if(isset($_GET['rordb_group'])) $where .= " AND IT.claimedby='".$_GET['rordb_group']."'"; // FIXME somehow this does not work...
if(isset($_GET['rordb_search'])){
    // Apply search field as general search
    $where .= " AND LOWER(CONCAT(IT.name, IT.color, IT.amount, IT.size, IT.comments, CA.name, LO.name)) LIKE LOWER('%".$_GET['rordb_search']."%')";
}

$items = rordbv2_wpdb_get_items($where);
$claimgroups = rordbv2_wpdb_get_claimgroups();

//echo "WHERE clause: $where<br>";

foreach($items as $i){
    // TODO if item is hidden skip it
    ?>
    <div class='wp-block-columns' style='background-color:#d6c2d6'>
        <div class='wp-block-column' style='flex-basis:50%;' align='center'>
            <img width='80%' src='<?php echo $i->imgdata; ?>'>
        </div>
        <div class='wp-block-column' style='flex-basis:50%;'>
            <b><?php echo $i->name; ?></b>
            <?php 
                // TODO check for permission to edit file
                echo "<a href='?page_id=$pageid&rordb_action=rordb_edit&rordb_edit=item&rordb_id=$i->id'>edit</a>";
                // TODO check if permission to claim
                echo "<form action='' method='post'><select name='rordb_claim'>";
                foreach($claimgroups as $cg){
                    echo "<option value='$cg' ";
                    if($cg==$i->claimedby) echo "selected";
                    echo ">$cg</option>";
                }
                echo "</select><input type='hidden' name='rordb_id' value='$i->id'><input type='submit' value='claim' /></form>";
            ?>
            <br>
            Category: <?php echo $i->catname; ?><br>
            Location: <?php echo $i->locname; ?><br>
            Color: <?php echo $i->color; ?><br>
            Size: <?php echo $i->size; ?><br>
            Amount: <?php echo $i->amount; ?><br>
            Comments: <?php echo $i->comments; ?><br>
            Claimed: <?php echo $i->claimedby; ?><br>
            Hidden: <?php echo $i->hidden; ?>
        </div>
    </div>
    <?php
}
