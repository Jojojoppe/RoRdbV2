<h4>Home</h4>

<!-- For now just list all items -->
<?php

$items = [];

// TODO do search for items

$items = rordbv2_wpdb_get_items();  // temp all items

foreach($items as $i){
    ?>
    <div class='wp-block-columns' style='background-color:#d6c2d6'>
        <div class='wp-block-column' style='flex-basis:50%;'>
            <img width='80%' src='<?php echo $i->imgdata; ?>'>
        </div>
        <div class='wp-block-column' style='flex-basis:50%;'>
            <b><?php echo $i->name; ?></b><br>
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
