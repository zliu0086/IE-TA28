<?php
/**
 * Defaults settings tab
 *
 * @package WPQuiz
 * @var CMB2 $cmb
 */

use WPQuiz\Helper;

$default_options = Helper::get_default_options();

$cmb->add_field(
	array(
		'id'      => 'rand_questions',
		'type'    => 'switch',
		'name'    => __( 'Randomize Questions', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'rand_answers',
		'type'    => 'switch',
		'name'    => __( 'Randomize Answers', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'restart_questions',
		'type'    => 'switch',
		'name'    => __( 'Restart Questions', 'wp-quiz' ),
		'default' => $default_options['restart_questions'],
	)
);

$cmb->add_field(
	array(
		'id'      => 'promote_plugin',
		'type'    => 'switch',
		'name'    => __( 'Promote the plugin', 'wp-quiz' ),
		'default' => $default_options['promote_plugin'],
	)
);

$cmb->add_field(
	array(
		'id'      => 'embed_toggle',
		'type'    => 'switch',
		'name'    => __( 'Show embed code toggle', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'share_buttons',
		'type'    => 'pw_multiselect',
		'name'    => __( 'Share buttons', 'wp-quiz' ),
		'options' => array(
			'fb' => __( 'Facebook', 'wp-quiz' ),
			'tw' => __( 'Twitter', 'wp-quiz' ),
			'vk' => __( 'VK', 'wp-quiz' ),
		),
		'default' => $default_options['share_buttons'],
	)
);

$cmb->add_field(
	array(
		'id'         => 'countdown_timer',
		'type'       => 'text',
		'name'       => __( 'Countdown timer [Seconds/question]', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'       => __( 'Applies to Trivia quiz in multi page layout.', 'wp-quiz' ),
		'attributes' => array(
			'type' => 'number',
			'min'  => 0,
			'step' => 1,
		),
		'classes'    => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'         => 'refresh_step',
		'type'       => 'text',
		'name'       => __( 'Reload page after Xth questions', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'       => __( 'Applies to Trivia, Personality and Swiper quizzes in multi pages layout.', 'wp-quiz' ),
		'attributes' => array(
			'type' => 'number',
			'min'  => 0,
			'step' => 1,
		),
		'classes'    => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'auto_scroll',
		'type'    => 'switch',
		'name'    => __( 'Auto scroll to next question', 'wp-quiz' ),
		'desc'    => __( 'Applies to Trivia and Personality quiz in single page layout.', 'wp-quiz' ),
		'default' => $default_options['auto_scroll'],
	)
);

\WPQuiz\ForceActions\Manager::register_force_actions_options( $cmb, 'settings' );

$cmb->add_field(
	array(
		'id'      => 'result_method',
		'type'    => 'select',
		'name'    => __( 'Result Delivery Method', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'Applies to Trivia and Personality quiz.', 'wp-quiz' ),
		'options' => array(
			'show'      => __( 'Show results', 'wp-quiz' ),
			'show_send' => __( 'Show results and send email', 'wp-quiz' ),
			'send'      => __( 'Email Results only', 'wp-quiz' ),
		),
		'default' => '',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'continue_as_btn',
		'type'    => 'switch',
		'name'    => __( 'Use Continue As button', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'Replaces Facebook login button with Continue As button. Applies to FB Quiz only.', 'wp-quiz' ),
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'result_popup',
		'type'    => 'switch',
		'name'    => __( 'Show result in Popup', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'desc'    => __( 'Applies to Trivia and Personality quiz only.', 'wp-quiz' ),
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);

$cmb->add_field(
	array(
		'id'      => 'show_ads',
		'type'    => 'switch',
		'name'    => __( 'Show Ads', 'wp-quiz' ) . '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>',
		'default' => 'off',
		'classes' => 'wp-quiz-pro-feature',
	)
);
