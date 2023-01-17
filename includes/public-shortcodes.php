<?php

function rordbv2_shortcode_render_menu($atts=[], $content=null){
    return rordbv2_render_menu();
}
add_shortcode('rordbv2_render_menu', 'rordbv2_shortcode_render_menu');

function rordbv2_shortcode_render_main($atts=[], $content=null){
    return "main...";
}
add_shortcode('rordbv2_render_main', 'rordbv2_shortcode_render_main');
