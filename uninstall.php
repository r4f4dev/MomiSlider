<?php

if(!defined('WP_UNINSTALL_PLUGIN')){
    die;
}

//Delete post type from db


$slides = get_posts(array('post_type'=>'momislider','numberposts'=>-1));
foreach($slides as $slide){
    wp_delete_post($slide->ID,true);
}