<?php
/**
 * Plugin Name: WP Quiz
 * Plugin URI:  https://mythemeshop.com/plugins/wp-quiz/
 * Description: WP Quiz lets you easily add polished, responsive and modern quizzes to your site or blog! Increase engagement and shares while building your mailing list! WP Quiz makes it easy!
 * Version:     2.0.5
 * Author:      MyThemeShop
 * Author URI:  https://mythemeshop.com/
 *
 * Text Domain: wp-quiz
 * Domain Path: /languages/
 *
 * @package WPQuiz
 */

if ( defined( 'WP_QUIZ_FILE' ) ) {
	return;
}

define( 'WP_QUIZ_FILE', defined( 'WP_QUIZ_PRO_FILE' ) ? WP_QUIZ_PRO_FILE : __FILE__ );

/*
 * Main plugin files and dependencies.
 */
require_once dirname( WP_QUIZ_FILE ) . '/vendor/autoload.php';

if ( ! function_exists( 'wp_quiz' ) ) {
	/**
	 * Gets plugin instance.
	 *
	 * @return \WPQuiz\WPQuiz
	 */
	function wp_quiz() {
		return \WPQuiz\WPQuiz::get_instance();
	}
}

$wp_quiz = wp_quiz();

register_activation_hook( WP_QUIZ_FILE, array( $wp_quiz, 'activate' ) );
register_deactivation_hook( WP_QUIZ_FILE, array( $wp_quiz, 'deactivate' ) );
