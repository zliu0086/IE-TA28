<?php
/*
    * Plugin Name: HD Quiz
    * Description: HD Quiz allows you to easily add an unlimited amount of Quizzes to your site.
    * Plugin URI: https://harmonicdesign.ca/hd-quiz/
    * Author: Harmonic Design
    * Author URI: https://harmonicdesign.ca
    * Version: 1.8.1
*/

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

if (!defined('HDQ_PLUGIN_VERSION')) {
    define('HDQ_PLUGIN_VERSION', '1.8.1');
}

// custom quiz image sizes
// add_image_size( 'hd_qu_size', 600, 400, true ); // featured image - removed since version 1.5.0
add_image_size('hd_qu_size2', 400, 400, true);
// image-as-answer

/* Include the basic required files
------------------------------------------------------- */
require dirname(__FILE__) . '/includes/post-type.php'; // custom post types
require dirname(__FILE__) . '/includes/meta.php'; // custom meta
require dirname(__FILE__) . '/includes/functions.php'; // general functions

// function to check if HD Quiz is active
function hdq_exists()
{
    return;
}

/* Add shortcode
------------------------------------------------------- */
function hdq_add_shortcode($atts)
{
    // Attributes
    extract(
        shortcode_atts(
            array(
                'quiz' => '',
            ),
            $atts
        )
    );

    // Code
    ob_start();
    include plugin_dir_path(__FILE__) . './includes/template.php';
    return ob_get_clean();
}
add_shortcode('HDquiz', 'hdq_add_shortcode');


/* Add Gutenberg block
------------------------------------------------------- */
function hdq_register_block_box()
{
    if (!function_exists('register_block_type')) {
        // Gutenberg is not active.
        return;
    }
    wp_register_script(
        'hdq-block-quiz',
        plugin_dir_url(__FILE__) . 'includes/js/hdq_block.js',
        array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'),
        HDQ_PLUGIN_VERSION
    );
    register_block_type('hdquiz/hdq-block-quiz', array(
        'style' => 'hdq-block-quiz',
        'editor_style' => 'hdq-block-quiz',
        'editor_script' => 'hdq-block-quiz',
    ));
}
add_action('init', 'hdq_register_block_box');

/* Get Quiz list
 * used for the gutenberg block
------------------------------------------------------- */
function hdq_get_quiz_list()
{
    $taxonomy = 'quiz';
    $term_args = array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    );
    $tax_terms = get_terms($taxonomy, $term_args);
    $quizzes = array();
    if (!empty($tax_terms) && !is_wp_error($tax_terms)) {
        foreach ($tax_terms as $tax_terms) {
            $quiz = new stdClass;
            $quiz->value = $tax_terms->term_id;
            $quiz->label = $tax_terms->name;
            array_push($quizzes, $quiz);
        }
    }
    echo json_encode($quizzes);
    die();
}
add_action('wp_ajax_hdq_get_quiz_list', 'hdq_get_quiz_list');

/* Disable Canonical redirection for paginated quizzes
------------------------------------------------------- */
function hdq_disable_redirect_canonical($redirect_url)
{
    global $post;
    if (has_shortcode($post->post_content, 'HDquiz')) {
        $redirect_url = false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'hdq_disable_redirect_canonical');

/* Create HD Quiz Settings page
------------------------------------------------------- */
function hdq_create_settings_page()
{
    if (hdq_user_permission()) {
        function hdq_register_quizzes_page()
        {
            add_menu_page('HD Quiz', 'HD Quiz', 'publish_posts', 'hdq_quizzes', 'hdq_register_quizzes_page_callback', 'dashicons-clipboard', 5);
            add_menu_page('HD Quiz Addons', 'HDQ Addons', 'edit_posts', 'hdq_addons', 'hdq_register_addons_page_callbak', '', 99);
            add_menu_page('HD Quiz Tools', 'HDQ Tools', 'edit_posts', 'hdq_tools', 'hdq_register_tools_page_callbak', '', 99);
            add_menu_page('HD Quiz Tools - CSV Importer', 'HDQ Tools CSV', 'edit_posts', 'hdq_tools_csv_importer', 'hdq_register_tools_csv_importer_page_callback', '', 99);
            add_menu_page('HD Quiz Tools - Data Upgrade', 'HDQ Tools DATA', 'edit_posts', 'hdq_tools_data_upgrade', 'hdq_register_tools__data_upgrade_page_callback', '', 99);
            remove_menu_page('hdq_addons');
            remove_menu_page('hdq_tools');
            remove_menu_page('hdq_tools_csv_importer');
            remove_menu_page('hdq_tools_data_upgrade');
        }
        add_action('admin_menu', 'hdq_register_quizzes_page');

        function hdq_register_settings_page()
        {
            add_submenu_page('hdq_quizzes', 'Quizzes', 'Quizzes', 'publish_posts', 'hdq_quizzes', 'hdq_register_quizzes_page_callback');
            add_submenu_page('hdq_quizzes', 'HD Quiz About', 'About / Options', 'publish_posts', 'hdq_options', 'hdq_register_settings_page_callback');
            add_submenu_page('hdq_quizzes', 'Addons', 'Addons', 'manage_options', 'admin.php?page=hdq_addons');
            add_submenu_page('hdq_quizzes', 'Tools', 'Tools', 'manage_options', 'admin.php?page=hdq_tools');
        }
        add_action('admin_menu', 'hdq_register_settings_page', 11);
    }

    $hdq_version = sanitize_text_field(get_option('HDQ_PLUGIN_VERSION'));
    $need_update_data = sanitize_text_field(get_option("hdq_data_upgrade"));
    
    if ($hdq_version != "" && $hdq_version != null && $hdq_version < "1.8") {
        update_option("hdq_remove_data_upgrade_notice", "yes");
        hdq_update_legacy_data();
    }
    
    if (HDQ_PLUGIN_VERSION != $hdq_version) {
        update_option('HDQ_PLUGIN_VERSION', HDQ_PLUGIN_VERSION);

        function hdq_show_upgrade_message()
        {
            ?>
            <div class = 'notice notice-success is-dismissible'>
            <p><strong>HD QUIZ</strong>. Thank you for upgrading or installing HD Quiz. If you experience any issues at all, please don't hesitate to <a href = "https://wordpress.org/support/plugin/hd-quiz" target = "_blank">reach out for support</a>! I'm always glad to help when I can.</p>
            </div>
            <?php
        }
        add_action('admin_notices', 'hdq_show_upgrade_message');
    }
}
add_action('init', 'hdq_create_settings_page');
