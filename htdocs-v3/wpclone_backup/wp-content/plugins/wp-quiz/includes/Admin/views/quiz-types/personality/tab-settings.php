<?php
/**
 * Personality quiz settings options
 *
 * @package WPQuiz
 * @var CMB2 $cmb
 */

use WPQuiz\Helper;
use WPQuiz\QuizTypeManager;

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
		'name'    => __( 'Randomize answers', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'id'      => 'wp_quiz_rand_answers',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'type'    => 'switch',
		'name'    => __( 'Restart questions', 'wp-quiz' ),
		'id'      => 'wp_quiz_restart_questions',
		'default' => Helper::get_option( 'restart_questions' ),
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
		'id'         => 'wp_quiz_refresh_step',
		'type'       => 'text',
		'name'       => __( 'Reload page after Xth questions', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'       => __( 'Applies to multiple pages layout.', 'wp-quiz' ),
		'attributes' => array(
			'type' => 'number',
			'min'  => 0,
			'step' => 1,
		),
		'default'    => Helper::get_option( 'refresh_step' ),
		'dep'        => array(
			array( 'wp_quiz_question_layout', 'multiple' ),
		),
		'classes'    => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'type'    => 'switch',
		'name'    => __( 'Auto scroll to next question', 'wp-quiz' ),
		'id'      => 'wp_quiz_auto_scroll',
		'default' => Helper::get_option( 'auto_scroll' ),
	)
);

\WPQuiz\ForceActions\Manager::register_force_actions_options( $cmb, 'meta_box', 'personality' );

$cmb->add_field(
	array(
		'type'             => 'select',
		'name'             => __( 'Result Delivery Method', 'wp-quiz' ),
		'id'               => 'wp_quiz_result_method',
		'desc'             => __( 'Applies to Trivia or Personality quiz.', 'wp-quiz' ),
		'options'          => array(
			'show'      => __( 'Show results', 'wp-quiz' ),
			'show_send' => __( 'Show results and send email', 'wp-quiz' ),
			'send'      => __( 'Email Results only', 'wp-quiz' ),
		),
		'show_option_none' => __( 'Use default value', 'wp-quiz' ),
		'dep'              => array(
			array( 'wp_quiz_force_action', '1' ),
		),
	)
);

$cmb->add_field(
	array(
		'type'             => 'radio_inline',
		'name'             => __( 'Show result in popup', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'id'               => 'wp_quiz_result_popup',
		'show_option_none' => __( 'Use default value', 'wp-quiz' ),
		'options'          => array(
			'yes' => __( 'Yes', 'wp-quiz' ),
			'no'  => __( 'No', 'wp-quiz' ),
		),
		'classes'          => 'wp-quiz-pro-feature',
	)
);

if ( in_array( 'personality', QuizTypeManager::get_pay_to_play_quiz_types(), true ) ) {
	$cmb->add_field(
		array(
			'id'      => 'wp_quiz_pay_to_play',
			'type'    => 'switch',
			'name'    => __( 'Pay to play', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
			'default' => 'off',
			'classes' => 'wp-quiz-pro-feature',
		)
	);
}

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
 * Fires after registering personality settings tab.
 *
 * @since 2.0.0
 *
 * @param CMB2 $cmb CMB2 object.
 */
do_action( 'wp_quiz_personality_settings_tab', $cmb );
