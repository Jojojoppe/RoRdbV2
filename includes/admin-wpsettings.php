<?php

// Field callbacks
// ---------------
function rordbv2_field_text($args){
	$option = get_option($args['label_for']);
	echo "<input type='text' id='".esc_attr($args['label_for'])."' ";
	echo "name='".esc_attr($args['label_for'])."' ";
	echo "value='".$option."'>";
	echo "<p class='description'>";
	echo esc_html_e($args['description']);
	echo "</p>";
}
function rordbv2_field_text_disabled($args){
	$option = get_option($args['label_for']);
	echo "<input readonly type='text' id='".esc_attr($args['label_for'])."' ";
	echo "name='".esc_attr($args['label_for'])."' ";
	echo "value='".$option."'>";
	echo "<p class='description'>";
	echo esc_html_e($args['description']);
	echo "</p>";
}
function rordbv2_field_textarea($args){
	$option = get_option($args['label_for']);
	echo "<textarea id='".esc_attr($args['label_for'])."' ";
	echo "name='".esc_attr($args['label_for'])."'>";
	echo $option."</textarea>";
	echo "<p class='description'>";
	echo esc_html_e($args['description']);
	echo "</p>";
}
function rordbv2_field_textarea_disabled($args){
	$option = get_option($args['label_for']);
	echo "<textarea id='".esc_attr($args['label_for'])."' ";
	echo "name='".esc_attr($args['label_for'])."'>";
	echo $option."</textarea>";
	echo "<p class='description'>";
	echo esc_html_e($args['description']);
	echo "</p>";
}
function rordbv2_field_select($args){
	$option = get_option($args['label_for']);
	$selectoptions = $args['options'];
	echo "<select id='".esc_attr($args['label_for'])."' ";
	echo "name='".esc_attr($args['label_for'])."'>";
	foreach($selectoptions as $o){
		echo "<option value='".$o."' ";
		if($option==$o) echo "selected";
		echo ">".$o;
		echo "</option>";
	}
	echo "</select>";
	echo "<p class='description'>";
	echo esc_html_e($args['description']);
	echo "</p>";
}
function rordbv2_field_checkbox($args){
	$option = get_option($args['label_for']);
	echo "<input type='checkbox' id='".esc_attr($args['label_for'])."' ";
	echo "name='".esc_attr($args['label_for'])."' ";
	if($option) echo "checked ";
	echo "value='".$option."'>";
	echo "<p class='description'>";
	echo esc_html_e($args['description']);
	echo "</p>";
}
function rordbv2_field_checkbox_disabled($args){
	$option = get_option($args['label_for']);
	echo "<input disabled type='checkbox' id='".esc_attr($args['label_for'])."' ";
	echo "name='".esc_attr($args['label_for'])."' ";
	if($option) echo "checked ";
	echo "value='".$option."'>";
	echo "<p class='description'>";
	echo esc_html_e($args['description']);
	echo "</p>";
}

function rordbv2_field_filecontent($args){
	$option = get_option($args['label_for']);
	$id = esc_attr($args['label_for']);
	echo "<textarea readonly id='".esc_attr($args['label_for'])."' ";
	echo "name='".esc_attr($args['label_for'])."'>";
	echo $option."</textarea><br>";
	echo "<input type='file' id='rordbv2_file_".$id."' ";
	echo "onchange='javascript:rordbv2_put_filecontent_in_div(\"rordbv2_file_".$id."\", \"".$id."\")'>";
	echo "<p class='description'>";
	echo esc_html_e($args['description']);
        echo "</p>";
}

// Initialize settings
function rordbv2_settings_init(){
    add_settings_section('rordbv2_section_main', __('Main settings of RoRdb', 'rordbv2'), 'rordbv2_section_header_callback', 'rordbv2');

    register_setting('rordbv2', 'rordbv2_claimgroups', ['default'=>'none']); //join(',', rordbv2_wpdb_get_claimgroups())]);
    add_settings_field('rordbv2_field_claimgroups', __('Claim groups', 'rordbv2'),
        'rordbv2_field_text', 'rordbv2', 'rordbv2_section_main', [
            'label_for' => 'rordbv2_claimgroups',
            'description' => 'List of all the groups which can clame (comma separated)',
        ]);
}
add_action('admin_init', 'rordbv2_settings_init');

function rordbv2_section_header_callback($args){
    echo "<p id=\"".esc_attr($args["id"])."\">";
}
