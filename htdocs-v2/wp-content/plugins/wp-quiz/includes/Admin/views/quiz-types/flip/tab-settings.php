<?php
/**
 * Flip quiz settings options
 *
 * @package WPQuiz
 * @var CMB2 $cmb
 */

use WPQuiz\Helper;

$cmb->add_field(
	array(
		'type'    => 'switch',
		'name'    => __( 'Randomize questions', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'id'      => 'wp_quiz_rand_questions',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'type'    => 'switch',
		'name'    => __( 'Promote the plugin', 'wp-quiz' ),
		'id'      => 'wp_quiz_promote_plugin',
		'default' => Helper::get_option( 'promote_plugin' ),
	)
);

$cmb->add_field(
	array(
		'type'    => 'switch',
		'name'    => __( 'Show embed code toggle', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'id'      => 'wp_quiz_embed_toggle',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'type'    => 'pw_multiselect',
		'name'    => __( 'Share buttons', 'wp-quiz' ),
		'id'      => 'wp_quiz_share_buttons',
		'options' => array(
			'fb' => __( 'Facebook', 'wp-quiz' ),
			'tw' => __( 'Twitter', 'wp-quiz' ),
			'vk' => __( 'VK', 'wp-quiz' ),
		),
		'default' => Helper::get_option( 'share_buttons' ),
	)
);

$cmb->add_field(
	array(
		'type'    => 'switch',
		'name'    => __( 'Show ads', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'id'      => 'wp_quiz_show_ads',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

/**
 * Fires after registering flip settings tab.
 *
 * @since 2.0.0
 *
 * @param CMB2 $cmb CMB2 object.
 */
do_action( 'wp_quiz_flip_settings_tab', $cmb );
