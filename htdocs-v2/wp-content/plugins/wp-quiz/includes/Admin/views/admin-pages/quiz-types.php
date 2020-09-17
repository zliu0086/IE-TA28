<?php
/**
 * Quiz types settings tab
 *
 * @package WPQuiz
 * @var CMB2 $cmb
 */

use WPQuiz\QuizTypeManager;

$quiz_types = QuizTypeManager::get_all( true );
foreach ( $quiz_types as $name => $quiz_type ) {
	$enable = in_array( $name, array( 'trivia', 'personality', 'flip' ), true );
	$cmb->add_field(
		array(
			'id'      => 'enable_' . $name,
			'name'    => $quiz_type->get_title() . ( $enable ? '' : '<span class="pro-text">' . esc_html__( 'Pro feature', 'wp-quiz' ) . '</span>' ),
			'type'    => 'switch',
			'desc'    => $quiz_type->get_desc(),
			'default' => $enable ? 'on' : 'off',
			'classes' => $enable ? '' : 'wp-quiz-pro-feature',
		)
	);
}
