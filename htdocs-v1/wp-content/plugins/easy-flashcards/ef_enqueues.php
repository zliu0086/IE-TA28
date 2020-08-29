<?php 
function ef_enqueues(){
wp_register_style( 'ef_style', plugin_dir_url(__FILE__).'ef_style.css');
wp_register_script( 'easy_flashcard', plugin_dir_url(__FILE__).'easy_flashcard.js');
wp_register_script('ef_validate',plugin_dir_url(__FILE__).'jquery.validate.min.js');
wp_enqueue_script('jquery');
wp_enqueue_script('easy_flashcard');
wp_enqueue_script('ef_validate');
wp_enqueue_style('ef_style');
}

add_action('wp_enqueue_scripts','ef_enqueues');
add_action('admin_menu','ef_enqueues');
?>